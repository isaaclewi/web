<?php

namespace App\Http\Controllers;

use App\Models\Apprenant;
use App\Models\Evaluation;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprenantDashboardController extends Controller
{
    /* ═══════════════════════════════════════════════════════════
     |  HELPER — récupère l'apprenant authentifié
     ═══════════════════════════════════════════════════════════ */

    private function getApprenant(): Apprenant
    {
        $user = Auth::user();
        $apprenant = Apprenant::where('user_id', $user->id)
            ->with([
                'classe.niveau',
                'classe.filiere',
                'classe.teachers.user',
                'niveau',
                'filiere',
                'institution',
                // NE PAS charger 'parents' ici — la table pivot varie selon
                // le nom réel en base. On la charge uniquement dans profil().
            ])
            ->first();

        abort_if(! $apprenant, 403, 'Profil apprenant introuvable.');

        return $apprenant;
    }

    /** Renvoie toutes les notes de l'apprenant chargées avec relations */
    private function loadGrades(Apprenant $apprenant)
    {
        return Grade::where('apprenant_id', $apprenant->id)
            ->with(['evaluation.subject.teacher', 'evaluation.subject.classe'])
            ->orderByDesc('created_at')
            ->get();
    }

    /* ═══════════════════════════════════════════════════════════
     |  1. DASHBOARD — GET /student/dashboard
     ═══════════════════════════════════════════════════════════ */

    public function index()
    {
        $user = Auth::user();
        $apprenant = $this->getApprenant();
        $institution = $apprenant->institution;
        $classe = $apprenant->classe;

        // Matières de sa classe
        $subjects = $classe
            ? Subject::where('class_id', $classe->id)
                ->with(['teacher', 'evaluations.grades'])
                ->get()
            : collect();

        // Toutes ses notes
        $grades = $this->loadGrades($apprenant);

        // Enseignants de sa classe
        $teachers = $classe ? $classe->teachers()->with('user')->get() : collect();

        // Moyennes par matière
        $moyennesParMatiere = $subjects->map(function ($sub) use ($apprenant) {
            $subGrades = Grade::where('apprenant_id', $apprenant->id)
                ->whereHas('evaluation', fn ($q) => $q->where('subject_id', $sub->id))
                ->get();

            return [
                'subject' => $sub,
                'avg' => $subGrades->isNotEmpty() ? round($subGrades->avg('score'), 2) : null,
                'count' => $subGrades->count(),
                'min' => $subGrades->isNotEmpty() ? $subGrades->min('score') : null,
                'max' => $subGrades->isNotEmpty() ? $subGrades->max('score') : null,
            ];
        });

        // Moyenne générale pondérée
        $moyenneGenerale = $this->calculerMoyenneGenerale($apprenant, $subjects);

        // Dernières évaluations
        $recentEvals = Evaluation::whereIn(
            'subject_id', $subjects->pluck('id')
        )
            ->with(['subject', 'grades' => fn ($q) => $q->where('apprenant_id', $apprenant->id)])
            ->orderByDesc('date')
            ->take(5)
            ->get();

        // Bulletins disponibles
        // Bulletins disponibles (publiés par l'admin)
        $reportCards = \App\Models\Bulletin::where('apprenant_id', $apprenant->id)
            ->where('publie', true)
            ->with('classe')
            ->orderByDesc('created_at')
            ->get();

        // Rang dans la classe (basé sur la moyenne générale)
        $rang = $this->calculerRang($apprenant, $classe, $subjects);

        // Données graphique radar
        $radarData = $this->buildRadarData($moyennesParMatiere);

        // Données graphique évolution (6 derniers mois)
        $evolutionData = $this->buildEvolutionData($apprenant, $subjects);

        $stats = [
            'moyenne' => $moyenneGenerale,
            'rang' => $rang['rang'],
            'total' => $rang['total'],
            'matieres' => $subjects->count(),
            'teachers' => $teachers->count(),
            'evals' => $grades->count(),
            'bulletins' => $reportCards->count(),
        ];

        return view('student.dashboard', compact(
            'user', 'apprenant', 'institution', 'classe',
            'subjects', 'grades', 'teachers', 'moyennesParMatiere',
            'moyenneGenerale', 'recentEvals', 'reportCards',
            'rang', 'radarData', 'evolutionData', 'stats'
        ));
    }

    /* ═══════════════════════════════════════════════════════════
     |  2. NOTES — GET /student/notes
     ═══════════════════════════════════════════════════════════ */

    public function notes(Request $request)
    {
        $user = Auth::user();
        $apprenant = $this->getApprenant();
        $institution = $apprenant->institution;
        $classe = $apprenant->classe;

        $subjects = $classe
            ? Subject::where('class_id', $classe->id)->with('teacher')->get()
            : collect();

        // Filtres
        $query = Grade::where('apprenant_id', $apprenant->id)
            ->with(['evaluation.subject.teacher']);

        if ($request->filled('subject_id')) {
            $query->whereHas('evaluation', fn ($q) => $q->where('subject_id', $request->subject_id));
        }
        if ($request->filled('type')) {
            $query->whereHas('evaluation', fn ($q) => $q->where('type', $request->type));
        }
        if ($request->filled('sort')) {
            match ($request->sort) {
                'score_asc' => $query->orderBy('score'),
                'score_desc' => $query->orderByDesc('score'),
                'date_asc' => $query->orderBy('created_at'),
                default => $query->orderByDesc('created_at'),
            };
        } else {
            $query->orderByDesc('created_at');
        }

        $grades = $query->paginate(15)->withQueryString();

        // Stats globales
        $allGrades = $this->loadGrades($apprenant);
        $moyennesParMatiere = $this->buildMoyennesParMatiere($apprenant, $subjects);
        $moyenneGenerale = $this->calculerMoyenneGenerale($apprenant, $subjects);
        $distributionData = $this->buildDistribution($apprenant, $subjects);

        return view('student.notes', compact(
            'user', 'apprenant', 'institution', 'classe',
            'subjects', 'grades', 'moyennesParMatiere',
            'moyenneGenerale', 'distributionData'
        ));
    }

    /* ═══════════════════════════════════════════════════════════
     |  3. CLASSES & MATIÈRES — GET /student/classes
     ═══════════════════════════════════════════════════════════ */

    public function classes(Request $request)
    {
        $user = Auth::user();
        $apprenant = $this->getApprenant();
        $institution = $apprenant->institution;
        $classe = $apprenant->classe;

        $subjects = $classe
            ? Subject::where('class_id', $classe->id)
                ->with(['teacher', 'evaluations' => fn ($q) => $q->with([
                    'grades' => fn ($g) => $g->where('apprenant_id', $apprenant->id),
                ])])
                ->get()
            : collect();

        $teachers = $classe ? $classe->teachers()->with('user')->get() : collect();

        // Par matière : moyenne de l'apprenant
        $subjectStats = $subjects->map(function ($sub) use ($apprenant) {
            $g = Grade::where('apprenant_id', $apprenant->id)
                ->whereHas('evaluation', fn ($q) => $q->where('subject_id', $sub->id))
                ->get();

            return [
                'subject' => $sub,
                'avg' => $g->isNotEmpty() ? round($g->avg('score'), 1) : null,
                'evals' => $sub->evaluations->count(),
                'graded' => $g->count(),
            ];
        });

        return view('student.classes', compact(
            'user', 'apprenant', 'institution', 'classe',
            'subjects', 'teachers', 'subjectStats'
        ));
    }

    /* ═══════════════════════════════════════════════════════════
     |  4. ENSEIGNANTS — GET /student/enseignants
     ═══════════════════════════════════════════════════════════ */

    public function enseignants(Request $request)
    {
        $user = Auth::user();
        $apprenant = $this->getApprenant();
        $institution = $apprenant->institution;
        $classe = $apprenant->classe;

        $teachers = $classe
            ? $classe->teachers()
                ->with(['user', 'niveaux', 'filieres'])
                ->get()
                ->map(function ($teacher) use ($apprenant, $classe) {
                    // Matières que ce teacher enseigne dans la classe de l'apprenant
                    $teacherSubjects = Subject::where('teacher_id', $teacher->id)
                        ->where('class_id', $classe->id)
                        ->get();

                    // Notes de l'apprenant dans ces matières
                    $subjectIds = $teacherSubjects->pluck('id');
                    $grades = Grade::where('apprenant_id', $apprenant->id)
                        ->whereHas('evaluation', fn ($q) => $q->whereIn('subject_id', $subjectIds))
                        ->get();

                    $teacher->my_subjects = $teacherSubjects;
                    $teacher->my_avg = $grades->isNotEmpty() ? round($grades->avg('score'), 1) : null;
                    $teacher->my_grade_cnt = $grades->count();

                    return $teacher;
                })
            : collect();

        // Filtre recherche
        if ($request->filled('search')) {
            $s = strtolower($request->search);
            $teachers = $teachers->filter(fn ($t) => str_contains(strtolower($t->nom.' '.$t->prenom), $s) ||
                str_contains(strtolower($t->specialite ?? ''), $s)
            );
        }

        // Sérialisation PHP → évite le bug Blade sur @json() multiligne
        $teachersJson = $teachers->values()->map(fn ($t) => [
            'id' => $t->id,
            'prenom' => $t->prenom,
            'nom' => $t->nom,
            'specialite' => $t->specialite,
            'email' => $t->email,
            'telephone' => $t->telephone,
            'type_contrat' => $t->type_contrat,
            'status' => $t->status,
            'matricule' => $t->matricule,
            'my_avg' => $t->my_avg,
            'my_grade_cnt' => $t->my_grade_cnt,
            'my_subjects' => $t->my_subjects->map(fn ($s) => [
                'name' => $s->name,
                'coefficient' => $s->coefficient,
            ])->values()->toArray(),
            'niveaux' => $t->niveaux->map(fn ($n) => $n->name)->values()->toArray(),
        ])->toJson(JSON_UNESCAPED_UNICODE);

        return view('student.enseignants', compact(
            'user', 'apprenant', 'institution', 'classe', 'teachers', 'teachersJson'
        ));
    }

    /* ═══════════════════════════════════════════════════════════
     |  5. BULLETINS — GET /student/bulletins
     ═══════════════════════════════════════════════════════════ */

    public function bulletins(Request $request)
    {
        $user = Auth::user();
        $apprenant = $this->getApprenant();
        $institution = $apprenant->institution;
        $classe = $apprenant->classe;

        // Uniquement les bulletins publiés par l'administration
        $bulletins = \App\Models\Bulletin::where('apprenant_id', $apprenant->id)
            ->where('publie', true)
            ->with('classe')
            ->orderBy('annee_academique', 'desc')
            ->orderBy('periode')
            ->get();

        $config = \App\Models\GradeConfig::where('institution_id', $apprenant->institution_id)
            ->where('annee_academique', $institution->academic_year)
            ->first();

        return view('student.bulletins', compact(
            'user', 'apprenant', 'institution', 'classe',
            'bulletins', 'config'
        ));
    }

    /* ═══════════════════════════════════════════════════════════
     |  6. PROFIL — GET /student/profil
     ═══════════════════════════════════════════════════════════ */

    public function profil()
    {
        $user = Auth::user();
        $apprenant = $this->getApprenant();
        $institution = $apprenant->institution;
        $classe = $apprenant->classe;

        // Parents — chargement sécurisé (nom de table pivot variable selon migrations)
        try {
            $parents = $apprenant->parents()->get();
        } catch (\Exception $e) {
            $parents = collect();
        }

        // Finances
        $finances = $apprenant->financialRecords()
            ->orderBy('annee_academique', 'desc')
            ->orderBy('mois', 'desc')
            ->get();

        $totalDu = $finances->sum('montant_du');
        $totalPaye = $finances->sum('montant_paye');
        $totalReste = $finances->sum('montant_reste');

        $subjects = $classe
            ? Subject::where('class_id', $classe->id)->with('teacher')->get()
            : collect();

        $moyenneGenerale = $this->calculerMoyenneGenerale($apprenant, $subjects);
        $rang = $this->calculerRang($apprenant, $classe, $subjects);

        return view('student.profil', compact(
            'user', 'apprenant', 'institution', 'classe',
            'parents', 'finances', 'totalDu', 'totalPaye', 'totalReste',
            'subjects', 'moyenneGenerale', 'rang'
        ));
    }

    /* ═══════════════════════════════════════════════════════════
     |  HELPERS PRIVÉS
     ═══════════════════════════════════════════════════════════ */

    private function calculerMoyenneGenerale(Apprenant $apprenant, $subjects): ?float
    {
        if ($subjects->isEmpty()) {
            return null;
        }

        $numerateur = 0;
        $denominateur = 0;

        foreach ($subjects as $sub) {
            $g = Grade::where('apprenant_id', $apprenant->id)
                ->whereHas('evaluation', fn ($q) => $q->where('subject_id', $sub->id))
                ->avg('score');

            if ($g !== null) {
                $coeff = $sub->coefficient ?? 1;
                $numerateur += $g * $coeff;
                $denominateur += $coeff;
            }
        }

        return $denominateur > 0 ? round($numerateur / $denominateur, 2) : null;
    }

    private function calculerRang(Apprenant $apprenant, $classe, $subjects): array
    {
        if (! $classe || $subjects->isEmpty()) {
            return ['rang' => '—', 'total' => '—'];
        }

        $apprenants = $classe->apprenants;
        $moyennes = [];

        foreach ($apprenants as $ap) {
            $moy = $this->calculerMoyenneGenerale($ap, $subjects);
            if ($moy !== null) {
                $moyennes[$ap->id] = $moy;
            }
        }

        arsort($moyennes);
        $rang = array_search($apprenant->id, array_keys($moyennes));

        return [
            'rang' => $rang !== false ? $rang + 1 : '—',
            'total' => count($moyennes),
        ];
    }

    private function buildMoyennesParMatiere(Apprenant $apprenant, $subjects): array
    {
        return $subjects->map(function ($sub) use ($apprenant) {
            $g = Grade::where('apprenant_id', $apprenant->id)
                ->whereHas('evaluation', fn ($q) => $q->where('subject_id', $sub->id))
                ->get();

            return [
                'subject' => $sub,
                'avg' => $g->isNotEmpty() ? round($g->avg('score'), 2) : null,
                'count' => $g->count(),
                'min' => $g->isNotEmpty() ? $g->min('score') : null,
                'max' => $g->isNotEmpty() ? $g->max('score') : null,
            ];
        })->toArray();
    }

    private function buildRadarData($moyennesParMatiere): array
    {
        $labels = [];
        $data = [];

        foreach ($moyennesParMatiere as $m) {
            $labels[] = $m['subject']->name;
            $data[] = $m['avg'] ?? 0;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function buildEvolutionData(Apprenant $apprenant, $subjects): array
    {
        $months = [];
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $d = now()->subMonths($i);
            $months[] = $d->format('Y-m');
            $labels[] = $d->locale('fr')->isoFormat('MMM');
        }

        $data = [];
        foreach ($months as $month) {
            $avg = Grade::where('apprenant_id', $apprenant->id)
                ->whereHas('evaluation', function ($q) use ($subjects, $month) {
                    $q->whereIn('subject_id', $subjects->pluck('id'))
                        ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$month]);
                })
                ->avg('score');
            $data[] = $avg ? round($avg, 1) : null;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function buildDistribution(Apprenant $apprenant, $subjects): array
    {
        $ranges = [[0, 5], [5, 8], [8, 10], [10, 12], [12, 14], [14, 16], [16, 18], [18, 20]];
        $labels = [];
        $counts = [];

        foreach ($ranges as [$min, $max]) {
            $labels[] = "{$min}–{$max}";
            $counts[] = Grade::where('apprenant_id', $apprenant->id)
                ->whereHas('evaluation', fn ($q) => $q->whereIn('subject_id', $subjects->pluck('id')))
                ->where('score', '>=', $min)
                ->where('score', $max === 20 ? '<=' : '<', $max)
                ->count();
        }

        return ['labels' => $labels, 'data' => $counts];
    }

    public function disciplinaire(\Illuminate\Http\Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user()
            ?? redirect()->route('login')->send();

        // Récupérer le profil apprenant lié à l'user connecté
        $apprenant = $user->apprenant;

        if (! $apprenant) {
            abort(403, 'Profil apprenant introuvable.');
        }

        $annee = $request->get('annee', date('Y'));

        $incidents = \App\Models\SuiviDisciplinaire::where('apprenant_id', $apprenant->id)
            ->where('annee_civile', $annee)
            ->with('recordedBy:id,name')
            ->orderByDesc('date_incident')
            ->get();

        $statsApprenant = [
            'total' => $incidents->count(),
            'graves' => $incidents->where('gravite', 3)->count(),
            'ouverts' => $incidents->where('statut', 'ouvert')->count(),
        ];

        // Toutes les années disponibles pour cet apprenant
        $anneesDispos = \App\Models\SuiviDisciplinaire::where('apprenant_id', $apprenant->id)
            ->distinct()->pluck('annee_civile')->sortDesc()->values();
        if (! $anneesDispos->contains($annee)) {
            $anneesDispos->prepend($annee);
        }

        // Charger la classe pour l'affichage
        $apprenant->load('classe:id,name');

        $typeLabels = \App\Models\SuiviDisciplinaire::typeLabels();
        $sanctionLabels = \App\Models\SuiviDisciplinaire::sanctionLabels();
        $graviteLabels = \App\Models\SuiviDisciplinaire::graviteLabels();

        return view('student.disciplinaire', compact(
            'user', 'apprenant',
            'incidents', 'statsApprenant',
            'annee', 'anneesDispos',
            'typeLabels', 'sanctionLabels', 'graviteLabels'
        ));
    }

    // public function aiChat(Request $request)
    // {
    //     $response = \Illuminate\Support\Facades\Http::withHeaders([
    //         'x-api-key' => config('services.anthropic.key'),
    //         'anthropic-version' => '2023-06-01',
    //     ])->post('https://api.anthropic.com/v1/messages', $request->only([
    //         'model', 'max_tokens', 'system', 'messages',
    //     ]));

    //     return $response->json();
    // }

    public function aiCoach()
    {
        $user = Auth::user();
        $apprenant = $this->getApprenant();
        $institution = $apprenant->institution;
        $classe = $apprenant->classe;

        $subjects = $classe
            ? Subject::where('class_id', $classe->id)->with('teacher')->get()
            : collect();

        $moyenneGenerale = $this->calculerMoyenneGenerale($apprenant, $subjects);
        $rang = $this->calculerRang($apprenant, $classe, $subjects);
        $moyennesParMatiere = $this->buildMoyennesParMatiere($apprenant, $subjects);

        $bulletinsCount = \App\Models\Bulletin::where('apprenant_id', $apprenant->id)
            ->where('publie', true)
            ->count();

        $stats = [
            'evals' => Grade::where('apprenant_id', $apprenant->id)->count(),
            'matieres' => $subjects->count(),
            'bulletins' => $bulletinsCount,
        ];

        return view('student.ai-coach', compact(
            'user', 'apprenant', 'institution', 'classe',
            'subjects', 'moyenneGenerale', 'rang',
            'moyennesParMatiere', 'stats'
        ));
    }

    /* ═══════════════════════════════════════════════════════════
     |  8. PROXY ANTHROPIC — POST /student/ai-chat
     |  Reçoit { system, messages } depuis le JS
     |  Transmet à l'API Anthropic avec la clé serveur
     |  Retourne la réponse JSON brute au client
     ═══════════════════════════════════════════════════════════ */

    public function aiChat(Request $request)
    {
        // Valider les entrées minimales
        $request->validate([
            'messages' => ['required', 'array', 'min:1'],
            'messages.*.role' => ['required', 'in:user,assistant'],
            'messages.*.content' => ['required', 'string', 'max:4000'],
            'system' => ['sometimes', 'string', 'max:8000'],
        ]);

        $anthropicKey = config('services.anthropic.key');

        if (! $anthropicKey || str_starts_with($anthropicKey, 'sk-ant-REMPLACE')) {
            return response()->json([
                'error' => ['message' => 'Clé API Anthropic non configurée. Ajoute ANTHROPIC_API_KEY dans ton .env.'],
            ], 503);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'x-api-key' => $anthropicKey,
                'anthropic-version' => config('services.anthropic.version', '2023-06-01'),
                'Content-Type' => 'application/json',
            ])
                ->timeout(30)
                ->post('https://api.anthropic.com/v1/messages', [
                    'model' => config('services.anthropic.model', 'claude-sonnet-4-20250514'),
                    'max_tokens' => 1000,
                    'system' => $request->input('system', ''),
                    'messages' => $request->input('messages'),
                ]);

            // Retourne la réponse Anthropic telle quelle au JS
            return response()->json($response->json(), $response->status());

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json([
                'error' => ['message' => 'Impossible de joindre le serveur IA. Vérifie ta connexion internet.'],
            ], 503);
        } catch (\Exception $e) {
            return response()->json([
                'error' => ['message' => 'Erreur interne : '.$e->getMessage()],
            ], 500);
        }
    }
}
