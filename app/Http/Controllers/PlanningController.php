<?php

namespace App\Http\Controllers;

use App\Models\Apprenant;
use App\Models\Classe;
use App\Models\EmploiDuTemps;
use App\Models\Filiere;
use App\Models\Institution;
use App\Models\Niveau;
use App\Models\ProgrammePaiement;
use App\Models\SeanceCours;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanningController extends Controller
{
    private function getInstitution(): Institution
    {
        $inst = Auth::user()?->institution;
        if (! $inst) {
            abort(403, 'Aucun établissement lié.');
        }

        return $inst;
    }

    /* ═══════════════════════════════════════════════════════
     | MAIN PAGE — emploi du temps + séances + paiements
     | GET /admin/planning
     ═══════════════════════════════════════════════════════ */
    public function index(Request $request)
    {
        $user = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();
        $instId = $institution->id;
        $annee = $institution->academic_year ?? date('Y').'-'.(date('Y') + 1);

        // Filtres
        $classeId = $request->get('classe_id');
        $teacherId = $request->get('teacher_id');
        $jour = $request->get('jour');

        // ── Emploi du temps ──
        $edtQuery = EmploiDuTemps::where('institution_id', $instId)
            ->where('annee_academique', $annee)
            ->where('statut', 'actif')
            ->with(['classe', 'subject', 'teacher']);

        if ($classeId) {
            $edtQuery->where('classe_id', $classeId);
        }
        if ($teacherId) {
            $edtQuery->where('teacher_id', $teacherId);
        }
        if ($jour) {
            $edtQuery->where('jour', $jour);
        }

        $emploisDuTemps = $edtQuery
    ->orderByRaw("
        CASE jour
            WHEN 'lundi' THEN 1
            WHEN 'mardi' THEN 2
            WHEN 'mercredi' THEN 3
            WHEN 'jeudi' THEN 4
            WHEN 'vendredi' THEN 5
            WHEN 'samedi' THEN 6
            ELSE 7
        END
    ")
    ->orderBy('heure_debut')
    ->get();


        // Organiser par jour pour la grille
        $grille = [];
        foreach (EmploiDuTemps::jourLabels() as $j => $jLabel) {
            $grille[$j] = $emploisDuTemps->where('jour', $j)->values();
        }

        // ── Séances de la semaine courante ──
        $lundi = now()->startOfWeek()->toDateString();
        $samedi = now()->endOfWeek()->subDay()->toDateString(); // sam = 6e jour

        $seancesQuery = SeanceCours::where('institution_id', $instId)
            ->whereBetween('date_seance', [$lundi, $samedi])
            ->with(['classe', 'subject', 'teacher', 'evaluation']);
        if ($classeId) {
            $seancesQuery->where('classe_id', $classeId);
        }
        $seancesSemaine = $seancesQuery->orderBy('date_seance')->orderBy('heure_debut')->get();

        // ── Programmes de paiement ──
        $programmes = ProgrammePaiement::where('institution_id', $instId)
            ->where('annee_academique', $annee)
            ->where('statut', 'actif')
            ->with(['niveau', 'classe'])
            ->orderBy('date_echeance')
            ->orderBy('ordre')
            ->get();

        // Stats
        $stats = [
            'total_creneaux' => EmploiDuTemps::where('institution_id', $instId)->where('annee_academique', $annee)->where('statut', 'actif')->count(),
            'cours_semaine' => $seancesSemaine->count(),
            'realisees' => $seancesSemaine->where('statut', 'realisee')->count(),
            'echeances_proch' => ProgrammePaiement::where('institution_id', $instId)
                ->where('annee_academique', $annee)
                ->where('statut', 'actif')
                ->whereBetween('date_echeance', [now()->toDateString(), now()->addDays(30)->toDateString()])
                ->count(),
        ];

        // Données formulaire
        $classes = Classe::where('institution_id', $instId)->orderBy('name')->get();
        $teachers = Teacher::where('institution_id', $instId)->orderBy('nom')->get();
        $subjects = Subject::where('institution_id', $instId)->orderBy('name')->get();
        $niveaux = Niveau::where('institution_id', $instId)->orderBy('name')->get();
        $filieres = Filiere::where('institution_id', $instId)->orderBy('name')->get();

        $jourLabels = EmploiDuTemps::jourLabels();
        $typeLabels = EmploiDuTemps::typeLabels();
        $typeColors = EmploiDuTemps::typeColors();
        $periodeLabels = ProgrammePaiement::periodeLabels();
        $typeFraisLabels = ProgrammePaiement::typeLabels();

        return view('admin.Planning', compact(
            'user', 'institution', 'annee',
            'emploisDuTemps', 'grille', 'seancesSemaine',
            'programmes', 'stats',
            'classes', 'teachers', 'subjects', 'niveaux', 'filieres',
            'classeId', 'teacherId', 'jour',
            'jourLabels', 'typeLabels', 'typeColors', 'periodeLabels', 'typeFraisLabels',
        ));
    }

    /* ─── EMPLOI DU TEMPS — CRUD ─── */
    public function edtStore(Request $request)
    {
        $institution = $this->getInstitution();
        $data = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'jour' => 'required|in:lundi,mardi,mercredi,jeudi,vendredi,samedi',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'type' => 'required|in:cours,evaluation,examen,rattrapage,activite,pause',
            'salle' => 'nullable|string|max:60',
            'notes' => 'nullable|string|max:500',
            'couleur' => 'nullable|string|max:10',
            'periode' => 'nullable|in:trimestre1,trimestre2,trimestre3,semestre1,semestre2,annee',
        ]);

        // 🔒 classe doit appartenir à l'institution
        Classe::where('id', $data['classe_id'])->where('institution_id', $institution->id)->firstOrFail();

        EmploiDuTemps::create(array_merge($data, [
            'institution_id' => $institution->id,
            'annee_academique' => $institution->academic_year,
            'statut' => 'actif',
        ]));

        return redirect()->back()->with('success', 'Créneau ajouté à l\'emploi du temps.')->withFragment('edt');
    }

    public function edtUpdate(Request $request, EmploiDuTemps $emploiDuTemps)
    {
        $institution = $this->getInstitution();
        if ($emploiDuTemps->institution_id !== $institution->id) {
            abort(403);
        }

        $data = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'jour' => 'required|in:lundi,mardi,mercredi,jeudi,vendredi,samedi',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'type' => 'required|in:cours,evaluation,examen,rattrapage,activite,pause',
            'salle' => 'nullable|string|max:60',
            'notes' => 'nullable|string|max:500',
            'statut' => 'nullable|in:actif,suspendu,annule',
        ]);

        $emploiDuTemps->update($data);

        return redirect()->back()->with('success', 'Créneau mis à jour.')->withFragment('edt');
    }

    public function edtDestroy(EmploiDuTemps $emploiDuTemps)
    {
        $institution = $this->getInstitution();
        if ($emploiDuTemps->institution_id !== $institution->id) {
            abort(403);
        }
        $emploiDuTemps->delete();

        return redirect()->back()->with('success', 'Créneau supprimé.')->withFragment('edt');
    }

    /* ─── SÉANCES — CRUD ─── */
    public function seanceStore(Request $request)
    {
        $institution = $this->getInstitution();
        $data = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'date_seance' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'type' => 'required|in:cours,evaluation,examen,rattrapage,activite',
            'titre' => 'nullable|string|max:200',
            'description' => 'nullable|string|max:1000',
            'salle' => 'nullable|string|max:60',
            'statut' => 'required|in:planifiee,realisee,annulee,reportee',
            'motif_annulation' => 'nullable|string|max:500',
            'evaluation_id' => 'nullable|exists:evaluations,id',
        ]);

        Classe::where('id', $data['classe_id'])->where('institution_id', $institution->id)->firstOrFail();

        SeanceCours::create(array_merge($data, [
            'institution_id' => $institution->id,
            'annee_academique' => $institution->academic_year,
        ]));

        return redirect()->back()->with('success', 'Séance enregistrée.')->withFragment('seances');
    }

    public function seanceUpdate(Request $request, SeanceCours $seanceCours)
    {
        $institution = $this->getInstitution();
        if ($seanceCours->institution_id !== $institution->id) {
            abort(403);
        }

        $data = $request->validate([
            'statut' => 'required|in:planifiee,realisee,annulee,reportee',
            'titre' => 'nullable|string|max:200',
            'description' => 'nullable|string|max:1000',
            'salle' => 'nullable|string|max:60',
            'motif_annulation' => 'nullable|string|max:500',
            'date_report' => 'nullable|date',
        ]);

        $seanceCours->update($data);

        return redirect()->back()->with('success', 'Séance mise à jour.')->withFragment('seances');
    }

    public function seanceDestroy(SeanceCours $seanceCours)
    {
        $institution = $this->getInstitution();
        if ($seanceCours->institution_id !== $institution->id) {
            abort(403);
        }
        $seanceCours->delete();

        return redirect()->back()->with('success', 'Séance supprimée.')->withFragment('seances');
    }

    /* ─── PROGRAMMES PAIEMENT — CRUD ─── */
    public function paiementStore(Request $request)
    {
        $institution = $this->getInstitution();
        $data = $request->validate([
            'libelle' => 'required|string|max:200',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'classe_id' => 'nullable|exists:classes,id',
            'montant' => 'required|numeric|min:0',
            'devise' => 'nullable|string|max:20',
            'date_echeance' => 'required|date',
            'date_debut_rappel' => 'nullable|date',
            'jours_grace' => 'nullable|integer|min:0',
            'type_frais' => 'required|in:inscription,scolarite,examen,tenue,transport,cantine,activite,autre',
            'periode' => 'required|in:annuel,trimestre1,trimestre2,trimestre3,semestre1,semestre2,mensuel',
            'description' => 'nullable|string|max:500',
            'obligatoire' => 'nullable|boolean',
            'ordre' => 'nullable|integer|min:1',
        ]);

        if (! empty($data['classe_id'])) {
            Classe::where('id', $data['classe_id'])->where('institution_id', $institution->id)->firstOrFail();
        }

        ProgrammePaiement::create(array_merge($data, [
            'institution_id' => $institution->id,
            'annee_academique' => $institution->academic_year,
            'statut' => 'actif',
            'obligatoire' => (bool) ($data['obligatoire'] ?? true),
            'devise' => $data['devise'] ?? ($institution->devise ?? 'FCFA'),
            'jours_grace' => $data['jours_grace'] ?? 0,
            'ordre' => $data['ordre'] ?? ProgrammePaiement::where('institution_id', $institution->id)->max('ordre') + 1,
        ]));

        return redirect()->back()->with('success', "Échéance « {$data['libelle']} » créée.")->withFragment('paiements');
    }

    public function paiementUpdate(Request $request, ProgrammePaiement $programmePaiement)
    {
        $institution = $this->getInstitution();
        if ($programmePaiement->institution_id !== $institution->id) {
            abort(403);
        }

        $data = $request->validate([
            'libelle' => 'required|string|max:200',
            'montant' => 'required|numeric|min:0',
            'date_echeance' => 'required|date',
            'jours_grace' => 'nullable|integer|min:0',
            'type_frais' => 'required|in:inscription,scolarite,examen,tenue,transport,cantine,activite,autre',
            'statut' => 'required|in:actif,suspendu,archive',
            'description' => 'nullable|string|max:500',
            'obligatoire' => 'nullable|boolean',
            'ordre' => 'nullable|integer|min:1',
        ]);

        $programmePaiement->update(array_merge($data, [
            'obligatoire' => (bool) ($data['obligatoire'] ?? $programmePaiement->obligatoire),
        ]));

        return redirect()->back()->with('success', 'Échéance mise à jour.')->withFragment('paiements');
    }

    public function paiementDestroy(ProgrammePaiement $programmePaiement)
    {
        $institution = $this->getInstitution();
        if ($programmePaiement->institution_id !== $institution->id) {
            abort(403);
        }
        $programmePaiement->delete();

        return redirect()->back()->with('success', 'Échéance supprimée.')->withFragment('paiements');
    }

    /* ─── VUE PUBLIQUE PARENT / APPRENANT : emploi du temps d'une classe ─── */
    public function classeEdt(Classe $classe, Request $request)
    {
        $user = Auth::user() ?? redirect()->route('login')->send();
        $institution = $classe->institution;
        $annee = $institution?->academic_year ?? date('Y').'-'.(date('Y') + 1);

        $emplois = EmploiDuTemps::where('classe_id', $classe->id)
            ->where('annee_academique', $annee)
            ->where('statut', 'actif')
            ->with(['subject', 'teacher'])
            ->orderByRaw("FIELD(jour,'lundi','mardi','mercredi','jeudi','vendredi','samedi')")
            ->orderBy('heure_debut')
            ->get();

        $grille = [];
        foreach (EmploiDuTemps::jourLabels() as $j => $jLabel) {
            $grille[$j] = $emplois->where('jour', $j)->values();
        }

        $programmes = ProgrammePaiement::where('institution_id', $classe->institution_id)
            ->where('annee_academique', $annee)
            ->where('statut', 'actif')
            ->where(fn ($q) => $q->whereNull('classe_id')->orWhere('classe_id', $classe->id))
            ->orderBy('date_echeance')
            ->get();

        return view('student.Planning', compact(
            'user', 'institution', 'classe', 'emplois', 'grille', 'programmes', 'annee'
        ));
    }

    public function studentPlanning()
    {
        $user = Auth::user();
        $apprenant = $user->apprenant ?? null;

        if (! $apprenant || ! $apprenant->classe) {
            abort(403, 'Aucune classe associée.');
        }

        $classe = $apprenant->classe;
        $institution = $classe->institution;
        $annee = $institution->academic_year ?? date('Y').'-'.(date('Y') + 1);

        // ── Emploi du temps ──
        $emplois = EmploiDuTemps::where('classe_id', $classe->id)
            ->where('annee_academique', $annee)
            ->where('statut', 'actif')
            ->with(['subject', 'teacher'])
            ->orderByRaw("FIELD(jour,'lundi','mardi','mercredi','jeudi','vendredi','samedi')")
            ->orderBy('heure_debut')
            ->get();

        // ── Séances de la semaine ──
        $lundi = now()->startOfWeek()->toDateString();
        $samedi = now()->endOfWeek()->subDay()->toDateString();

        $seances = SeanceCours::where('classe_id', $classe->id)
            ->whereBetween('date_seance', [$lundi, $samedi])
            ->with(['subject', 'teacher'])
            ->orderBy('date_seance')
            ->orderBy('heure_debut')
            ->get();

        $grille = [];
        foreach (EmploiDuTemps::jourLabels() as $j => $label) {
            $grille[$j] = $emplois->where('jour', $j)->values();
        }

        // ── Programmes de paiement ──
        $programmes = ProgrammePaiement::where('institution_id', $classe->institution_id)
            ->where('annee_academique', $annee)
            ->where('statut', 'actif')
            ->where(fn ($q) => $q->whereNull('classe_id')->orWhere('classe_id', $classe->id))
            ->orderBy('date_echeance')
            ->get();

        return view('student.Planning', compact(
            'classe', 'institution', 'grille', 'programmes', 'annee', 'seances'
        ));
    }

     public function teacherPlanning(Request $request)
    {
        $user        = Auth::user() ?? redirect()->route('login')->send();
        $institution = $user->institution;
        $teacher     = $user->teacher;

        if (! $teacher) {
            abort(403, 'Aucun profil enseignant lié à votre compte.');
        }

        $instId = $institution->id;
        $annee  = $institution->academic_year ?? date('Y').'-'.(date('Y') + 1);

        // ── Emploi du temps de l'enseignant ──────────────────
        $emplois = EmploiDuTemps::where('institution_id', $instId)
            ->where('annee_academique', $annee)
            ->where('statut', 'actif')
            ->where('teacher_id', $teacher->id)       // ← uniquement SES cours
            ->with(['classe', 'subject', 'teacher'])
            ->get();

        // Organiser par jour pour la grille (même structure que admin)
        $grille = [];
        foreach (EmploiDuTemps::jourLabels() as $j => $jLabel) {
            $grille[$j] = $emplois->where('jour', $j)->values();
        }

        $jourLabels  = EmploiDuTemps::jourLabels();
        $typeLabels  = EmploiDuTemps::typeLabels();
        $typeColors  = EmploiDuTemps::typeColors();

        $teacher->load(['classes', 'niveaux', 'filieres']);

        return view('teacher.Planning', compact(
            'user', 'institution', 'teacher', 'annee',
            'emplois', 'grille',
            'jourLabels', 'typeLabels', 'typeColors',
        ));
    }

    /* ─── PLANNING PARENT (lecture seule, emplois des enfants) ─── */
    public function parentPlanning(Request $request)
    {
        $user = Auth::user() ?? redirect()->route('login')->send();

        $schoolParent = \App\Models\SchoolParent::where('user_id', $user->id)
            ->with([
                'apprenants',
                'apprenants.classe',
                'apprenants.niveau',
                'apprenants.filiere',
                'apprenants.institution',
            ])
            ->first();

        if (! $schoolParent) {
            abort(403, 'Aucun profil parent lié à votre compte.');
        }

        // L'institution vient du premier enfant (ou de l'user)
        $institution = $user->institution
            ?? $schoolParent->apprenants->first()?->institution;

        $typeLabels = EmploiDuTemps::typeLabels();
        $annee      = $institution?->academic_year ?? date('Y').'-'.(date('Y') + 1);

        return view('parent.Planning', compact(
            'user', 'institution', 'schoolParent',
            'typeLabels', 'annee',
        ));
    }
}
