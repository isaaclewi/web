<?php

namespace App\Http\Controllers;

use App\Models\Apprenant;
use App\Models\Evaluation;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TeacherDashboardController extends Controller
{
    /* ═══════════════════════════════════════════════════════════
     |  HELPER PRIVÉ — charge le Teacher authentifié
     ═══════════════════════════════════════════════════════════ */

    private function getTeacher(): Teacher
    {
        $teacher = Teacher::where('user_id', Auth::id())
            ->with(['classes.apprenants', 'niveaux', 'filieres'])
            ->first();

        abort_if(! $teacher, 403, 'Profil enseignant introuvable.');

        return $teacher;
    }

    /** Renvoie les subjectIds du teacher courant */
    private function subjectIds(Teacher $teacher)
    {
        return Subject::where('teacher_id', $teacher->id)->pluck('id');
    }

    /* ═══════════════════════════════════════════════════════════
     |  1. DASHBOARD  →  teacher.dashboard   GET /teacher/dashboard
     ═══════════════════════════════════════════════════════════ */
public function index()
{
    $user = Auth::user();
    $institution = $user->institution;
    $teacher = $this->getTeacher();

    if (! $teacher) {
        abort(403, 'Profil enseignant introuvable.');
    }

    // ── Données de base ──
    $subjects = Subject::where('teacher_id', $teacher->id)
        ->with('classe')
        ->get();

    $classes = $teacher->classes()->with('apprenants')->get();
    $classIds = $classes->pluck('id');
    $subjectIds = $subjects->pluck('id');

    $niveaux = $teacher->niveaux ?? collect();
    $filieres = $teacher->filieres ?? collect();

    $apprenants = Apprenant::whereIn('class_id', $classIds)->get();

    // ── Evaluations ──
    $evaluations = Evaluation::whereIn('subject_id', $subjectIds)
        ->with(['subject.classe', 'grades'])
        ->orderBy('date', 'desc')
        ->get();

    // ── Moyenne globale ──
    $avgGrade = Grade::whereHas('evaluation', function ($q) use ($subjectIds, $classIds) {
        $q->whereIn('subject_id', $subjectIds)
          ->whereHas('subject', function ($sq) use ($classIds) {
              $sq->whereIn('class_id', $classIds);
          });
    })->avg('score');

    // ── Heures hebdo ──
    $weeklyHours = $subjects->sum('coefficient') * 2;

    // ── Stats ──
    $stats = [
        'classes' => $classes->count(),
        'subjects' => $subjects->count(),
        'students' => $apprenants->count(),
        'levels' => $niveaux->count(),
        'average_grade' => $avgGrade ? round($avgGrade, 1) : '—',
        'hours' => $weeklyHours ?: 0,
    ];

    // ── GRAPHIQUES (IMPORTANT POSTGRES SAFE) ──
    $chartData = $this->buildChartDataPostgres($classes, $subjectIds);
    $gradeDistribution = $this->buildGradeDistributionPostgres($subjectIds);

    return view('teacher.dashboard', compact(
        'user',
        'institution',
        'teacher',
        'classes',
        'subjects',
        'apprenants',
        'niveaux',
        'filieres',
        'evaluations',
        'stats',
        'chartData',
        'gradeDistribution'
    ));
}

private function buildChartDataPostgres($classes, $subjectIds)
{
    $months = collect(range(1, 6))->map(function ($i) {
        return now()->subMonths(6 - $i)->format('Y-m');
    });

    $datasets = [];

    foreach ($classes as $class) {

        $data = [];

        foreach ($months as $month) {

            $avg = Grade::whereHas('evaluation', function ($q) use ($subjectIds, $class, $month) {
                $q->whereIn('subject_id', $subjectIds)
                  ->whereHas('subject', function ($sq) use ($class) {
                      $sq->where('class_id', $class->id);
                  })
                  ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$month]);
            })->avg('score');

            $data[] = $avg ? round($avg, 1) : null;
        }

        $datasets[] = [
            'label' => $class->name,
            'data' => $data,
        ];
    }

    return [
        'labels' => $months,
        'datasets' => $datasets
    ];
}

    /* ═══════════════════════════════════════════════════════════
     |  2. CLASSES  →  teacher.classes.index   GET /teacher/classes
     ═══════════════════════════════════════════════════════════ */

    public function classes(Request $request)
    {
        $user = Auth::user();
        $institution = $user->institution;
        $teacher = $this->getTeacher();
        $subjectIds = $this->subjectIds($teacher);

        // Classes avec filtre niveau / filière
        $query = $teacher->classes()->with(['apprenants', 'niveau', 'filiere']);

        if ($request->filled('niveau_id')) {
            $query->where('niveau_id', $request->niveau_id);
        }
        if ($request->filled('filiere_id')) {
            $query->where('filiere_id', $request->filiere_id);
        }

        $classes = $query->get();
        $classIds = $classes->pluck('id');
        $subjects = Subject::where('teacher_id', $teacher->id)->with('classe')->get();
        $niveaux = $teacher->niveaux;
        $filieres = $teacher->filieres;
        $apprenants = Apprenant::whereIn('class_id', $classIds)->get();

        $evaluations = Evaluation::whereIn('subject_id', $subjectIds)
            ->with(['subject.classe', 'grades'])
            ->get();

        $stats = [
            'classes' => $classes->count(),
            'students' => $apprenants->count(),
            'subjects' => $subjects->count(),
            'levels' => $niveaux->count(),
        ];

        return view('teacher.Classes', compact(
            'user', 'institution', 'teacher', 'classes', 'subjects',
            'apprenants', 'niveaux', 'filieres', 'evaluations', 'stats'
        ));
    }

    /* ═══════════════════════════════════════════════════════════
     |  3. APPRENANTS  →  teacher.apprenants.index
     |     GET /teacher/apprenants
     ═══════════════════════════════════════════════════════════ */

    public function apprenants(Request $request)
    {
        $user = Auth::user();
        $institution = $user->institution;
        $teacher = $this->getTeacher();
        $subjectIds = $this->subjectIds($teacher);

        $classes = $teacher->classes()->with('apprenants')->get();
        $classIds = $classes->pluck('id');
        $niveaux = $teacher->niveaux;
        $filieres = $teacher->filieres;

        // Requête élèves avec filtres
        $query = Apprenant::whereIn('class_id', $classIds)
            ->with('classe');

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('sexe')) {
            $query->where('sexe', $request->sexe);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('nom', 'like', "%{$s}%")
                ->orWhere('prenom', 'like', "%{$s}%")
                ->orWhere('matricule', 'like', "%{$s}%")
            );
        }

        // Tri
        match ($request->input('sort', 'nom')) {
            'prenom' => $query->orderBy('prenom'),
            'matricule' => $query->orderBy('matricule'),
            default => $query->orderBy('nom'),
        };

        $apprenants = $query->paginate(20)->withQueryString();

        // Notes groupées par apprenant (pour afficher la moyenne)
        $apIds = $apprenants->pluck('id');
        $gradesByApprenant = Grade::whereIn('apprenant_id', $apIds)
            ->whereHas('evaluation', fn ($q) => $q->whereIn('subject_id', $subjectIds))
            ->get()
            ->groupBy('apprenant_id');

        $subjects = Subject::where('teacher_id', $teacher->id)->with('classe')->get();

        $stats = [
            'classes' => $classes->count(),
            'students' => $apprenants->total(),
            'subjects' => $subjects->count(),
            'levels' => $niveaux->count(),
        ];

        return view('teacher.Apprenants', compact(
            'user', 'institution', 'teacher', 'classes', 'subjects',
            'apprenants', 'gradesByApprenant', 'niveaux', 'filieres', 'stats'
        ));
    }

    /* ═══════════════════════════════════════════════════════════
     |  3b. APPRENANT SHOW (JSON)  →  teacher.apprenants.show
     |     GET /teacher/apprenants/{apprenant}
     ═══════════════════════════════════════════════════════════ */

    public function apprenantShow(Apprenant $apprenant)
    {
        $teacher = $this->getTeacher();
        $classIds = $teacher->classes()->pluck('classes.id');

        abort_if(! $classIds->contains($apprenant->class_id), 403);

        $subjectIds = $this->subjectIds($teacher);

        $grades = Grade::where('apprenant_id', $apprenant->id)
            ->whereHas('evaluation', fn ($q) => $q->whereIn('subject_id', $subjectIds))
            ->with(['evaluation.subject'])
            ->get();

        return response()->json([
            'apprenant' => $apprenant->load('classe'),
            'grades' => $grades,
            'average' => $grades->isNotEmpty() ? round($grades->avg('score'), 2) : null,
        ]);
    }

    /* ═══════════════════════════════════════════════════════════
     |  4. ÉVALUATIONS  →  teacher.evaluations.index
     |     GET /teacher/evaluations
     ═══════════════════════════════════════════════════════════ */

    public function evaluations(Request $request)
    {
        $user = Auth::user();
        $institution = $user->institution;
        $teacher = $this->getTeacher();
        $subjectIds = $this->subjectIds($teacher);

        $classes = $teacher->classes()->with('apprenants')->get();
        $subjects = Subject::where('teacher_id', $teacher->id)->with('classe')->get();

        // Requête évaluations avec filtres
        $query = Evaluation::whereIn('subject_id', $subjectIds)
            ->with(['subject.classe', 'grades']);

        if ($request->filled('class_id')) {
            $query->whereHas('subject', fn ($q) => $q->where('class_id', $request->class_id));
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }
        if ($request->filled('status')) {
            if ($request->status === 'done') {
                $query->whereHas('grades');
            } elseif ($request->status === 'pending') {
                $query->whereDoesntHave('grades');
            }
        }

        // Tri
        match ($request->input('sort', 'date_desc')) {
            'date_asc' => $query->orderBy('date'),
            'title' => $query->orderBy('title'),
            default => $query->orderByDesc('date'),
        };

        $evaluations = $query->paginate(15)->withQueryString();

        return view('teacher.Evaluations', compact(
            'user', 'institution', 'teacher', 'classes', 'subjects', 'evaluations'
        ));
    }

    /* ═══════════════════════════════════════════════════════════
     |  4b. EVALUATION STORE  →  teacher.evaluations.store
     |     POST /teacher/evaluations
     ═══════════════════════════════════════════════════════════ */

     public function evaluationStore(Request $request)
    {
        $teacher = $this->getTeacher();

        $v = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'title'      => 'required|string|max:255',
            'type'       => 'required|in:controle,examen,tp,projet,interro,devoir,composition',
            'periode'    => 'nullable|in:trimestre1,trimestre2,trimestre3,semestre1,semestre2,annuel',
            'date'       => 'required|date',
            'max_score'  => 'required|numeric|min:1|max:1000',
            'description'=> 'nullable|string|max:500',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        // Vérification que la matière appartient bien à cet enseignant
        Subject::where('id', $request->subject_id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        // Label lisible pour type_evaluation
        $typeLabels = [
            'controle'    => 'Contrôle',
            'examen'      => 'Examen',
            'tp'          => 'Travaux pratiques',
            'projet'      => 'Projet',
            'interro'     => 'Interrogation',
            'devoir'      => 'Devoir',
            'composition' => 'Composition',
        ];

        $data = $v->validated();
        $data['institution_id']   = $teacher->institution_id;
        $data['type_evaluation']  = $typeLabels[$data['type']] ?? ucfirst($data['type']);

        $evaluation = Evaluation::create($data);

        return back()->with('success', "Évaluation « {$evaluation->title} » créée avec succès.");
    }
    /* ═══════════════════════════════════════════════════════════
     |  4c. EVALUATION DESTROY  →  teacher.evaluations.destroy
     |     DELETE /teacher/evaluations/{evaluation}
     ═══════════════════════════════════════════════════════════ */

    public function evaluationDestroy(Evaluation $evaluation)
    {
        $teacher = $this->getTeacher();
        $subjectIds = $this->subjectIds($teacher);

        abort_if(! $subjectIds->contains($evaluation->subject_id), 403);

        $evaluation->grades()->delete();
        $evaluation->delete();

        return back()->with('success', 'Évaluation supprimée.');
    }

    /* ═══════════════════════════════════════════════════════════
     |  4d. EXPORT CSV  →  teacher.evaluations.export
     |     GET /teacher/evaluations/{evaluation}/export
     ═══════════════════════════════════════════════════════════ */

    public function exportGrades(Evaluation $evaluation)
    {
        $teacher = $this->getTeacher();
        $subjectIds = $this->subjectIds($teacher);

        abort_if(! $subjectIds->contains($evaluation->subject_id), 403);

        $grades = Grade::where('evaluation_id', $evaluation->id)->with('apprenant')->get();
        $filename = 'notes_'.str()->slug($evaluation->title).'_'.now()->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($grades, $evaluation) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Matricule', 'Nom', 'Prénom', 'Note/'.$evaluation->max_score]);
            foreach ($grades as $g) {
                fputcsv($handle, [
                    $g->apprenant->matricule,
                    $g->apprenant->nom,
                    $g->apprenant->prenom,
                    $g->score,
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /* ═══════════════════════════════════════════════════════════
     |  5. NOTES  →  teacher.notes.index
     |     GET /teacher/notes
     ═══════════════════════════════════════════════════════════ */

    public function notes(Request $request)
    {
        $user = Auth::user();
        $institution = $user->institution;
        $teacher = $this->getTeacher();
        $subjectIds = $this->subjectIds($teacher);

        $classes = $teacher->classes()->with('apprenants')->get();
        $subjects = Subject::where('teacher_id', $teacher->id)->with('classe')->get();

        // ── Évaluations filtrées pour le sélecteur ──
        $evalQuery = Evaluation::whereIn('subject_id', $subjectIds)
            ->with(['subject.classe', 'grades']);

        if ($request->filled('class_id')) {
            $evalQuery->whereHas('subject', fn ($q) => $q->where('class_id', $request->class_id));
        }
        if ($request->filled('subject_id')) {
            $evalQuery->where('subject_id', $request->subject_id);
        }
        if ($request->filled('type')) {
            $evalQuery->where('type', $request->type);
        }

        $filteredEvaluations = $evalQuery->orderByDesc('date')->get();

        // ── Évaluation sélectionnée ──
        $selectedEval = null;
        $evalApprenants = collect();

        if ($request->filled('evaluation_id')) {
            $selectedEval = Evaluation::whereIn('subject_id', $subjectIds)
                ->with(['subject.classe', 'grades.apprenant'])
                ->findOrFail($request->evaluation_id);

            $evalApprenants = Apprenant::where('class_id', $selectedEval->subject->class_id ?? 0)
                ->orderBy('nom')
                ->get();
        }

        return view('teacher.Notes', compact(
            'user', 'institution', 'teacher', 'classes', 'subjects',
            'filteredEvaluations', 'selectedEval', 'evalApprenants'
        ));
    }

    /* ═══════════════════════════════════════════════════════════
     |  5b. GRADES STORE  →  teacher.grades.store
     |     POST /teacher/grades
     ═══════════════════════════════════════════════════════════ */

    public function gradesStore(Request $request)
    {
        $teacher = $this->getTeacher();
        $subjectIds = $this->subjectIds($teacher);

        $v = Validator::make($request->all(), [
            'evaluation_id' => 'required|exists:evaluations,id',
            'grades' => 'required|array|min:1',
            'grades.*.apprenant_id' => 'required|exists:apprenants,id',
            'grades.*.score' => 'required|numeric|min:0',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        $evaluation = Evaluation::findOrFail($request->evaluation_id);
        abort_if(! $subjectIds->contains($evaluation->subject_id), 403);

        DB::transaction(function () use ($request, $evaluation) {
            foreach ($request->grades as $g) {
                if ($g['score'] === '' || $g['score'] === null) {
                    continue;
                }
                Grade::updateOrCreate(
                    ['evaluation_id' => $evaluation->id, 'apprenant_id' => $g['apprenant_id']],
                    ['score' => min((float) $g['score'], $evaluation->max_score)]
                );
            }
        });

        return redirect()
            ->route('teacher.notes.index', ['evaluation_id' => $evaluation->id])
            ->with('success', 'Notes enregistrées avec succès.');
    }

    /* ═══════════════════════════════════════════════════════════
     |  5c. GRADE UPDATE  →  teacher.grades.update
     |     PATCH /teacher/grades/{grade}
     ═══════════════════════════════════════════════════════════ */

    public function gradeUpdate(Request $request, Grade $grade)
    {
        $teacher = $this->getTeacher();
        $subjectIds = $this->subjectIds($teacher);

        abort_if(! $subjectIds->contains($grade->evaluation->subject_id), 403);

        $v = Validator::make($request->all(), [
            'score' => 'required|numeric|min:0',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v);
        }

        $grade->update(['score' => $request->score]);

        return back()->with('success', 'Note mise à jour.');
    }

    /* ═══════════════════════════════════════════════════════════
     |  6. PROFIL  →  teacher.profil   GET /teacher/profil
     ═══════════════════════════════════════════════════════════ */

    public function profil()
    {
        $user = Auth::user();
        $institution = $user->institution;
        $teacher = $this->getTeacher();
        $subjectIds = $this->subjectIds($teacher);

        $subjects = Subject::where('teacher_id', $teacher->id)->with('classe')->get();
        $classes = $teacher->classes()->with(['apprenants', 'niveau', 'filiere'])->get();
        $niveaux = $teacher->niveaux;
        $filieres = $teacher->filieres;

        $evaluations = Evaluation::whereIn('subject_id', $subjectIds)
            ->with(['subject.classe', 'grades'])
            ->orderByDesc('date')
            ->get();

        $avgGrade = Grade::whereHas('evaluation', fn ($q) => $q->whereIn('subject_id', $subjectIds))->avg('score');
        $weeklyHours = $subjects->sum('coefficient') * 2;

        $stats = [
            'classes' => $classes->count(),
            'subjects' => $subjects->count(),
            'students' => Apprenant::whereIn('class_id', $classes->pluck('id'))->count(),
            'levels' => $niveaux->count(),
            'average_grade' => $avgGrade ? round($avgGrade, 1) : '—',
            'hours' => $weeklyHours ?: 0,
        ];

        return view('teacher.Profil', compact(
            'user', 'institution', 'teacher', 'classes', 'subjects',
            'niveaux', 'filieres', 'evaluations', 'stats'
        ));
    }

    /* ═══════════════════════════════════════════════════════════
     |  HELPERS PRIVÉS — graphiques
     ═══════════════════════════════════════════════════════════ */

    private function buildChartData($classes, $subjectIds): array
    {
        $months = [];
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $d = now()->subMonths($i);
            $months[] = $d->format('Y-m');
            $labels[] = $d->locale('fr')->isoFormat('MMM');
        }

        $colors = ['#0d9488', '#6366f1', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6'];
        $datasets = [];

        foreach ($classes as $idx => $classe) {
            $data = [];
            foreach ($months as $month) {
                $avg = Grade::whereHas('evaluation', function ($q) use ($subjectIds, $classe, $month) {
                    $q->whereIn('subject_id', $subjectIds)
                        ->whereHas('subject', fn ($sq) => $sq->where('class_id', $classe->id))
                        ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$month]);
                })->avg('score');
                $data[] = $avg ? round($avg, 1) : null;
            }
            $color = $colors[$idx % count($colors)];
            $datasets[] = [
                'label' => $classe->name,
                'data' => $data,
                'borderColor' => $color,
                'backgroundColor' => $color.'15',
                'fill' => $idx === 0,
                'tension' => 0.4,
                'pointRadius' => 4,
                'borderWidth' => 2,
            ];
        }

        return ['labels' => $labels, 'datasets' => $datasets];
    }

    private function buildGradeDistribution($subjectIds): array
    {
        $ranges = [[0, 5], [5, 8], [8, 10], [10, 12], [12, 14], [14, 16], [16, 18], [18, 20]];
        $labels = [];
        $counts = [];

        foreach ($ranges as [$min, $max]) {
            $labels[] = "{$min}–{$max}";
            $counts[] = Grade::whereHas('evaluation', fn ($q) => $q->whereIn('subject_id', $subjectIds))
                ->where('score', '>=', $min)
                ->where('score', $max === 20 ? '<=' : '<', $max)
                ->count();
        }

        return ['labels' => $labels, 'data' => $counts];
    }
}
