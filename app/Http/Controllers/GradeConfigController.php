<?php

namespace App\Http\Controllers;

use App\Models\Apprenant;
use App\Models\Bulletin;
use App\Models\Classe;
use App\Models\Evaluation;
use App\Models\Grade;
use App\Models\GradeConfig;
use App\Models\Institution;
use App\Models\Subject;
use App\Services\BulletinCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GradeConfigController extends Controller
{
   private function getInstitution(): Institution
    {
        $inst = Auth::user()?->institution;
        if (! $inst) abort(403, 'Aucun établissement lié à votre compte.');
        return $inst;
    }

    private function getOrCreateConfig(Institution $institution): GradeConfig
    {
        return GradeConfig::firstOrCreate(
            [
                'institution_id'   => $institution->id,
                'annee_academique' => $institution->academic_year
                    ?? date('Y') . '-' . (date('Y') + 1),
            ],
            [
                'note_max'      => 20,
                'note_passage'  => 10,
                'pct_devoirs'   => 40,
                'pct_examen'    => 60,
                'decimales'     => 2,
                'mentions'      => GradeConfig::defaultMentions(),
                'type_periodes' => 'trimestres',
                'nb_periodes'   => 3,
            ]
        );
    }

    private function getEvaluationsQuery(Institution $institution, ?string $classeId = null)
    {
        // IDs de toutes les évaluations accessibles à cet établissement
        // — Voie 1 : institution_id direct (admin/staff)
        $directIds = Evaluation::where('institution_id', $institution->id)
            ->pluck('id');
 
        // — Voie 2 : via subjects.institution_id (enseignants — ancienne logique)
        $viaSubjectIds = Evaluation::whereHas(
            'subject',
            fn($q) => $q->where('institution_id', $institution->id)
        )->pluck('id');
 
        // Union des deux jeux d'IDs
        $allIds = $directIds->merge($viaSubjectIds)->unique()->values();
 
        $query = Evaluation::whereIn('id', $allIds)
            ->with(['subject.classe', 'grades'])
            ->orderByDesc('date');
 
        // Filtre optionnel par classe
        if ($classeId) {
            $query->whereHas('subject', fn($q) => $q->where('class_id', $classeId));
        }
 
        return $query;
    }

    private function listePeriodes(GradeConfig $config): array
    {
        $periodes = [];
        for ($i = 1; $i <= $config->nb_periodes; $i++) {
            $key   = $config->type_periodes === 'trimestres' ? "trimestre{$i}" : "semestre{$i}";
            $label = $config->type_periodes === 'trimestres'
                ? ($i === 1 ? '1er Trimestre' : "{$i}e Trimestre")
                : ($i === 1 ? '1er Semestre'  : "{$i}e Semestre");
            $periodes[] = ['key' => $key, 'label' => $label];
        }
        $periodes[] = ['key' => 'annuel', 'label' => 'Annuel'];
        return $periodes;
    }

    private function verifierInstitution(Bulletin $bulletin): void
    {
        if ((int) $bulletin->institution_id !== (int) Auth::user()?->institution_id) {
            abort(403, 'Accès non autorisé.');
        }
    }

    /* ══════════════════════════════════════════════════════════
     |  INDEX — Page principale notes & bulletins (admin)
     |  GET /admin/grade-config
     |
     |  NOUVELLE VERSION : charge aussi les évaluations,
     |  les apprenants de l'évaluation sélectionnée, et les
     |  matières, pour permettre la saisie de notes.
     ══════════════════════════════════════════════════════════ */
    public function index(Request $request)
    {
        $institution = $this->getInstitution();
        $config      = $this->getOrCreateConfig($institution);
        $periodes    = $this->listePeriodes($config);
 
        $classes = Classe::where('institution_id', $institution->id)
            ->withCount('apprenants')
            ->orderBy('name')
            ->get();
 
        $subjects = Subject::where('institution_id', $institution->id)
            ->with(['classe', 'teacher'])
            ->orderBy('name')
            ->get();
 
        // ✅ FIX BUG 2 : utiliser la requête robuste
        $evaluations = $this->getEvaluationsQuery(
            $institution,
            $request->get('classe_id')
        )->paginate(30)->withQueryString();
 
        // ── Évaluation sélectionnée pour la saisie de notes ──
        $selectedEval   = null;
        $evalApprenants = collect();
 
        if ($request->filled('evaluation_id')) {
            // ✅ FIX : chercher aussi via subject si institution_id absent
            $selectedEval = Evaluation::where(function ($q) use ($institution) {
                    $q->where('institution_id', $institution->id)
                      ->orWhereHas('subject', fn($sq) =>
                          $sq->where('institution_id', $institution->id)
                      );
                })
                ->with(['subject.classe', 'grades.apprenant'])
                ->find($request->evaluation_id);
 
            if (! $selectedEval) abort(404);
 
            $classeId       = $selectedEval->subject->class_id ?? null;
            $evalApprenants = $classeId
                ? Apprenant::where('class_id', $classeId)
                    ->where('institution_id', $institution->id)
                    ->orderBy('nom')->orderBy('prenom')
                    ->get()
                : collect();
        }
 
        // ── Stats bulletins ──
        $statsParPeriode = [];
        foreach ($periodes as $p) {
            $q = Bulletin::where('institution_id', $institution->id)
                ->where('annee_academique', $config->annee_academique)
                ->where('periode', $p['key']);
 
            $statsParPeriode[$p['key']] = [
                'total'   => (clone $q)->count(),
                'publie'  => (clone $q)->where('publie', true)->count(),
                'calcule' => (clone $q)->whereNotNull('calcule_at')->count(),
            ];
        }
 
        // ── Bulletins filtrés ──
        $periode  = $request->get('periode', $periodes[0]['key'] ?? 'trimestre1');
        $classeId = $request->get('classe_id');
        $publie   = $request->get('publie');
 
        $bulletinQuery = Bulletin::where('institution_id', $institution->id)
            ->where('annee_academique', $config->annee_academique)
            ->where('periode', $periode)
            ->with(['apprenant', 'classe']);
 
        if ($classeId)         $bulletinQuery->where('classe_id', $classeId);
        if ($publie !== null)  $bulletinQuery->where('publie', (bool) $publie);
 
        $bulletins = $bulletinQuery->orderBy('rang')->paginate(30)->withQueryString();
 
        // ── Notes récentes ──
        $notesRecentes = $this->getNotesRecentes($institution);
 
        return view('admin.grade_config', compact(
            'institution', 'config', 'classes', 'subjects', 'periodes',
            'evaluations', 'selectedEval', 'evalApprenants',
            'statsParPeriode', 'bulletins', 'periode', 'classeId', 'publie',
            'notesRecentes'
        ));
    }

    /* ══════════════════════════════════════════════════════════
     |  CRÉER UNE ÉVALUATION (admin / staff)
     |  POST /admin/grades/evaluation
     |  POST /staff/grades/evaluation
     ══════════════════════════════════════════════════════════ */
    public function evaluationStore(Request $request)
    {
        $institution = $this->getInstitution();
 
        $data = $request->validate([
            'subject_id'  => 'required|exists:subjects,id',
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:controle,examen,tp,projet,interro,devoir,composition',
            'periode'     => 'nullable|in:trimestre1,trimestre2,trimestre3,semestre1,semestre2,annuel',
            'date'        => 'required|date',
            'max_score'   => 'required|numeric|min:1|max:1000',
        ]);
 
        // Vérifier que la matière appartient à l'établissement
        $subject = Subject::where('id', $data['subject_id'])
            ->where('institution_id', $institution->id)
            ->firstOrFail();
 
        $typeLabels = [
            'controle'    => 'Contrôle',
            'examen'      => 'Examen',
            'tp'          => 'Travaux pratiques',
            'projet'      => 'Projet',
            'interro'     => 'Interrogation',
            'devoir'      => 'Devoir',
            'composition' => 'Composition',
        ];
 
        $eval = Evaluation::create([
            'subject_id'      => $data['subject_id'],
            'title'           => $data['title'],
            'type'            => $data['type'],
            'type_evaluation' => $typeLabels[$data['type']] ?? ucfirst($data['type']),
            'periode'         => $data['periode'] ?? null,
            'date'            => $data['date'],
            'max_score'       => $data['max_score'],
            'institution_id'  => $institution->id,   // ✅ toujours renseigné
        ]);
 
        return redirect()
            ->route('admin.grade_config', ['evaluation_id' => $eval->id])
            ->with('success', "Évaluation « {$eval->title} » créée. Vous pouvez maintenant saisir les notes.");
    }

    /* ══════════════════════════════════════════════════════════
     |  SUPPRIMER UNE ÉVALUATION (admin)
     |  DELETE /admin/grades/evaluation/{evaluation}
     ══════════════════════════════════════════════════════════ */
    public function evaluationDestroy(Evaluation $evaluation)
    {
        $institution = $this->getInstitution();
 
        // Vérifier appartenance (directe ou via subject)
        $appartient = (int) $evaluation->institution_id === $institution->id
            || Subject::where('id', $evaluation->subject_id)
                ->where('institution_id', $institution->id)
                ->exists();
 
        if (! $appartient) abort(403);
 
        DB::transaction(function () use ($evaluation) {
            $evaluation->grades()->delete();
            $evaluation->delete();
        });
 
        return redirect()->back()->with('success', 'Évaluation et ses notes supprimées.');
    }

    /* ══════════════════════════════════════════════════════════
     |  SAISIR / METTRE À JOUR LES NOTES D'UNE ÉVALUATION
     |  POST /admin/grades
     |  POST /staff/grades
     |
     |  Reçoit :
     |    evaluation_id  : int
     |    grades[{apprenant_id}] : float|null
     ══════════════════════════════════════════════════════════ */
   public function gradesStore(Request $request)
    {
        $institution = $this->getInstitution();
 
        $request->validate([
            'evaluation_id' => 'required|exists:evaluations,id',
            'grades'        => 'required|array',
        ]);
 
        // Vérifier accès à cette évaluation
        $evaluation = Evaluation::where(function ($q) use ($institution) {
                $q->where('institution_id', $institution->id)
                  ->orWhereHas('subject', fn($sq) =>
                      $sq->where('institution_id', $institution->id)
                  );
            })
            ->findOrFail($request->evaluation_id);
 
        $saved   = 0;
        $skipped = 0;
 
        // ✅ FIX BUG 3 : détecter si la colonne recorded_by existe
        $hasRecordedBy = Schema::hasColumn('grades', 'recorded_by');
 
        DB::transaction(function () use (
            $request, $evaluation, $institution, &$saved, &$skipped, $hasRecordedBy
        ) {
            foreach ($request->grades as $apprenantId => $score) {
                if ($score === null || $score === '') {
                    $skipped++;
                    continue;
                }
 
                // Vérifier que l'apprenant appartient à l'établissement
                $apprenant = Apprenant::where('id', $apprenantId)
                    ->where('institution_id', $institution->id)
                    ->first();
 
                if (! $apprenant) {
                    $skipped++;
                    continue;
                }
 
                $scoreVal = min(
                    max(0, (float) $score),
                    (float) $evaluation->max_score
                );
 
                $updateData = ['score' => $scoreVal];
                if ($hasRecordedBy) {
                    $updateData['recorded_by'] = Auth::id();
                }
 
                Grade::updateOrCreate(
                    [
                        'evaluation_id' => $evaluation->id,
                        'apprenant_id'  => (int) $apprenantId,
                    ],
                    $updateData
                );
 
                $saved++;
            }
        });
 
        $msg = "{$saved} note(s) enregistrée(s)";
        if ($skipped > 0) $msg .= ", {$skipped} ignorée(s)";
 
        // ✅ Rediriger vers la même évaluation pour rester en mode saisie
        return redirect()
            ->route('admin.grade_config', ['evaluation_id' => $evaluation->id])
            ->with('success', $msg . '.');
    }

    /* ══════════════════════════════════════════════════════════
     |  MODIFIER UNE NOTE INDIVIDUELLE
     |  PATCH /admin/grades/{grade}
     ══════════════════════════════════════════════════════════ */
   public function gradeUpdate(Request $request, Grade $grade)
    {
        $institution = $this->getInstitution();
 
        $evaluation = Evaluation::where(function ($q) use ($institution) {
                $q->where('institution_id', $institution->id)
                  ->orWhereHas('subject', fn($sq) =>
                      $sq->where('institution_id', $institution->id)
                  );
            })
            ->where('id', $grade->evaluation_id)
            ->firstOrFail();
 
        $request->validate(['score' => 'required|numeric|min:0']);
 
        $data = ['score' => min((float) $request->score, (float) $evaluation->max_score)];
        if (Schema::hasColumn('grades', 'recorded_by')) {
            $data['recorded_by'] = Auth::id();
        }
 
        $grade->update($data);
 
        return redirect()->back()->with('success', 'Note mise à jour.');
    }

    /* ══════════════════════════════════════════════════════════
     |  SUPPRIMER UNE NOTE INDIVIDUELLE
     |  DELETE /admin/grades/{grade}
     ══════════════════════════════════════════════════════════ */
     public function gradeDestroy(Grade $grade)
    {
        $institution = $this->getInstitution();
 
        Evaluation::where(function ($q) use ($institution) {
                $q->where('institution_id', $institution->id)
                  ->orWhereHas('subject', fn($sq) =>
                      $sq->where('institution_id', $institution->id)
                  );
            })
            ->where('id', $grade->evaluation_id)
            ->firstOrFail();
 
        $grade->delete();
 
        return redirect()->back()->with('success', 'Note supprimée.');
    }

    private function getNotesRecentes(Institution $institution): \Illuminate\Support\Collection
    {
        return Grade::join('evaluations', 'grades.evaluation_id', '=', 'evaluations.id')
            ->join('subjects', 'evaluations.subject_id', '=', 'subjects.id')
            ->join('apprenants', 'grades.apprenant_id', '=', 'apprenants.id')
            ->where(function ($q) use ($institution) {
                $q->where('evaluations.institution_id', $institution->id)
                  ->orWhere('subjects.institution_id', $institution->id);
            })
            ->select(
                'grades.id', 'grades.score', 'grades.created_at',
                'evaluations.title as eval_title', 'evaluations.max_score',
                'evaluations.type_evaluation', 'evaluations.periode',
                'subjects.name as matiere',
                'apprenants.nom', 'apprenants.prenom', 'apprenants.matricule'
            )
            ->orderByDesc('grades.created_at')
            ->limit(20)
            ->get();
    }

    public function updateConfig(Request $request)
    {
        $institution = $this->getInstitution();
        $config = $this->getOrCreateConfig($institution);

        $data = $request->validate([
            'note_max' => 'required|numeric|min:1|max:1000',
            'note_passage' => 'required|numeric|min:0',
            'pct_devoirs' => 'required|numeric|min:0|max:100',
            'pct_examen' => 'required|numeric|min:0|max:100',
            'decimales' => 'required|integer|min:0|max:4',
            'type_periodes' => 'required|in:trimestres,semestres',
            'nb_periodes' => 'required|integer|min:1|max:4',
            'compensation_active' => 'boolean',
            'seuil_compensation' => 'nullable|numeric|min:0',
            'mentions' => 'nullable|array',
            'mentions.*.libelle' => 'required|string|max:50',
            'mentions.*.min' => 'required|numeric|min:0',
        ]);

        $data['pct_devoirs'] = min(100, max(0, $data['pct_devoirs']));
        $data['pct_examen'] = 100 - $data['pct_devoirs'];
        $data['compensation_active'] = (bool) $request->input('compensation_active', false);

        $config->update($data);

        return redirect()->back()->with('success', 'Configuration de notation mise à jour.');
    }

    /* ══════════════════════════════════════════════════════════
     | CALCUL DES BULLETINS
     ══════════════════════════════════════════════════════════ */

    /** Calcule le bulletin d'UN apprenant */
    public function calculerBulletinApprenant(Request $request)
    {
        $institution = $this->getInstitution();
        $config = $this->getOrCreateConfig($institution);

        $data = $request->validate([
            'apprenant_id' => 'required|exists:apprenants,id',
            'periode' => 'required|string',
        ]);

        $apprenant = Apprenant::where('id', $data['apprenant_id'])
            ->where('institution_id', $institution->id)
            ->firstOrFail();

        $service = new BulletinCalculatorService($config);
        $bulletin = $service->calculerPourApprenant($apprenant, $data['periode']);

        return redirect()->back()->with('success',
            "Bulletin de {$apprenant->prenom} {$apprenant->nom} calculé. Moyenne : {$bulletin->moyenne_generale}/{$config->note_max}"
        );
    }

    /** Calcule tous les bulletins d'une classe */
    public function calculerBulletinsClasse(Request $request)
    {
        $institution = $this->getInstitution();
        $config = $this->getOrCreateConfig($institution);

        $data = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'periode' => 'required|string',
        ]);

        $classe = Classe::where('id', $data['classe_id'])
            ->where('institution_id', $institution->id)
            ->firstOrFail();

        $service = new BulletinCalculatorService($config);
        $bulletins = $service->calculerPourClasse($classe, $data['periode']);

        return redirect()->back()->with('success',
            count($bulletins)." bulletin(s) calculé(s) pour la classe {$classe->name}."
        );
    }

    /** Calcule TOUS les bulletins de l'établissement pour une période */
    public function calculerTousLesBulletins(Request $request)
    {
        $institution = $this->getInstitution();
        $config = $this->getOrCreateConfig($institution);

        $data = $request->validate(['periode' => 'required|string']);

        $classes = Classe::where('institution_id', $institution->id)->get();
        $service = new BulletinCalculatorService($config);
        $total = 0;

        foreach ($classes as $classe) {
            $bulletins = $service->calculerPourClasse($classe, $data['periode']);
            $total += count($bulletins);
        }

        return redirect()->back()->with('success', "{$total} bulletin(s) calculé(s) pour toutes les classes.");
    }

    /* ══════════════════════════════════════════════════════════
     | PUBLICATION
     ══════════════════════════════════════════════════════════ */

    /** Publie les bulletins d'une classe pour une période */
    public function publierBulletinsClasse(Request $request)
    {
        $institution = $this->getInstitution();
        $data = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'periode' => 'required|string',
        ]);

        Classe::where('id', $data['classe_id'])->where('institution_id', $institution->id)->firstOrFail();

        $count = Bulletin::where('institution_id', $institution->id)
            ->where('classe_id', $data['classe_id'])
            ->where('annee_academique', $institution->academic_year)
            ->where('periode', $data['periode'])
            ->whereNotNull('calcule_at')
            ->update([
                'publie' => true,
                'publie_at' => now(),
                'publie_par' => Auth::id(),
            ]);

        return redirect()->back()->with('success', "{$count} bulletin(s) publiés.");
    }

    /** Dépublie les bulletins d'une classe */
    public function depublierBulletinsClasse(Request $request)
    {
        $institution = $this->getInstitution();
        $data = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'periode' => 'required|string',
        ]);

        Classe::where('id', $data['classe_id'])->where('institution_id', $institution->id)->firstOrFail();

        $count = Bulletin::where('institution_id', $institution->id)
            ->where('classe_id', $data['classe_id'])
            ->where('annee_academique', $institution->academic_year)
            ->where('periode', $data['periode'])
            ->update(['publie' => false, 'publie_at' => null, 'publie_par' => null]);

        return redirect()->back()->with('success', "{$count} bulletin(s) dépubliés.");
    }

    /** Publie un bulletin individuel */
    public function publierBulletin(Bulletin $bulletin)
    {
        $this->verifierInstitution($bulletin);
        $bulletin->update(['publie' => true, 'publie_at' => now(), 'publie_par' => Auth::id()]);

        return redirect()->back()->with('success', 'Bulletin publié.');
    }

    public function depublierBulletin(Bulletin $bulletin)
    {
        $this->verifierInstitution($bulletin);
        $bulletin->update(['publie' => false, 'publie_at' => null, 'publie_par' => null]);

        return redirect()->back()->with('success', 'Bulletin dépublié.');
    }

    /* ══════════════════════════════════════════════════════════
     | AFFICHAGE BULLETINS — VUE ADMIN
     ══════════════════════════════════════════════════════════ */
    public function bulletinsIndex(Request $request)
    {
        $institution = $this->getInstitution();
        $config = $this->getOrCreateConfig($institution);
        $periodes = $this->listePeriodes($config);
        $periode = $request->get('periode', $periodes[0]['key'] ?? 'trimestre1');
        $classeId = $request->get('classe_id');
        $publie = $request->get('publie');

        $query = Bulletin::where('institution_id', $institution->id)
            ->where('annee_academique', $config->annee_academique)
            ->where('periode', $periode)
            ->with(['apprenant', 'classe', 'publiePar', 'calculePar']);

        if ($classeId) {
            $query->where('classe_id', $classeId);
        }
        if ($publie !== null) {
            $query->where('publie', (bool) $publie);
        }

        $bulletins = $query->orderBy('rang')->paginate(30)->withQueryString();

        $classes = Classe::where('institution_id', $institution->id)->orderBy('name')->get();

        return view('admin.Bulletins', compact(
            'institution', 'config', 'periodes', 'periode',
            'bulletins', 'classes', 'classeId', 'publie'
        ));
    }

    /** Bulletin détaillé d'un apprenant — vue admin */
    public function bulletinShow(Bulletin $bulletin)
    {
        $this->verifierInstitution($bulletin);
        $config = $this->getOrCreateConfig($bulletin->institution);
        $periodes = $this->listePeriodes($config);

        return view('admin.bulletin_detail', compact('bulletin', 'config', 'periodes'));
    }

    /** Mise à jour des appréciations manuelles */
    public function bulletinUpdateAppreciation(Request $request, Bulletin $bulletin)
    {
        $this->verifierInstitution($bulletin);
        $data = $request->validate([
            'appreciation_conseil' => 'nullable|string|max:500',
            'appreciation_directeur' => 'nullable|string|max:500',
        ]);
        $bulletin->update($data);

        return redirect()->back()->with('success', 'Appréciations mises à jour.');
    }

    /* ══════════════════════════════════════════════════════════
     | VUE APPRENANT — résultats (publiés uniquement)
     ══════════════════════════════════════════════════════════ */
    public function studentBulletins()
    {
        $user = Auth::user();
        $apprenant = $user?->apprenant;
        if (! $apprenant) {
            abort(403, 'Aucun profil apprenant lié à votre compte.');
        }

        $institution = $apprenant->institution;
        $config = GradeConfig::where('institution_id', $institution->id)
            ->where('annee_academique', $institution->academic_year)
            ->first();

        $bulletins = Bulletin::where('apprenant_id', $apprenant->id)
            ->where('annee_academique', $institution->academic_year)
            ->where('publie', true)
            ->orderBy('periode')
            ->get();

        return view('student.bulletins', compact('apprenant', 'institution', 'config', 'bulletins'));
    }

    /** Bulletin détaillé — vue apprenant (publiés uniquement) */
    public function studentBulletinShow(Bulletin $bulletin)
    {
        $user = Auth::user();
        $apprenant = $user?->apprenant;
        if (! $apprenant || $bulletin->apprenant_id !== $apprenant->id) {
            abort(403);
        }
        if (! $bulletin->publie) {
            abort(403, 'Ce bulletin n\'est pas encore publié.');
        }
        $config = GradeConfig::where('institution_id', $apprenant->institution_id)->first();

        return view('student.bulletin_detail', compact('bulletin', 'config', 'apprenant'));
    }

    /* ══════════════════════════════════════════════════════════
     | VUE PARENT
     ══════════════════════════════════════════════════════════ */
    public function parentBulletins(Apprenant $apprenant)
    {
        $user = Auth::user();
        $institution = $apprenant->institution;

        $roles = method_exists($user, 'getRoleNames') ? $user->getRoleNames()->toArray() : [];
        $isAdmin = in_array('admin', $roles) || in_array('directeur', $roles);

        if (! $isAdmin) {
            $schoolParent = \App\Models\SchoolParent::where('user_id', $user->id)->first();

            if (! $schoolParent) {
                abort(403, 'Profil parent introuvable.');
            }

            // Requête directe sur la table pivot — pas de boucle, pas d'ORM
            $linked = \Illuminate\Support\Facades\DB::table('apprenant_parent')
                ->where('parent_id', $schoolParent->id)
                ->where('apprenant_id', $apprenant->id)
                ->exists();

            if (! $linked) {
                abort(403, 'Cet élève n\'est pas lié à votre compte.');
            }
        }

        $config = GradeConfig::where('institution_id', $institution->id)
            ->where('annee_academique', $institution->academic_year)
            ->first();

        // with('classe') obligatoire pour éviter N+1
        $bulletins = Bulletin::where('apprenant_id', $apprenant->id)
            ->where('annee_academique', $institution->academic_year)
            ->where('publie', true)
            ->with('classe')
            ->orderBy('periode')
            ->get();

        return view('parent.bulletins', compact(
            'apprenant', 'institution', 'config', 'bulletins'
        ));
    }

    /* ══════════════════════════════════════════════════════════
     | VUE ENSEIGNANT — notes qu'il a saisies
     ══════════════════════════════════════════════════════════ */
    public function teacherNotesOverview()
    {
        $user = Auth::user();
        $teacher = $user?->teacher;
        if (! $teacher) {
            abort(403);
        }

        $institution = $teacher->institution;
        $config = GradeConfig::where('institution_id', $institution->id)
            ->where('annee_academique', $institution->academic_year)
            ->first();
        $periodes = $config ? $this->listePeriodes($config) : [];

        // Ses évaluations avec stats de complétion
        $evaluations = \App\Models\Evaluation::where('institution_id', $institution->id)
            ->whereHas('subject', fn ($q) => $q->whereHas('teacher', fn ($q2) => $q2->where('id', $teacher->id)))
            ->with(['subject.classe', 'grades'])
            ->orderByDesc('date')
            ->paginate(20);

        return view('teacher.notes_overview', compact('teacher', 'institution', 'config', 'periodes', 'evaluations'));
    }
}
