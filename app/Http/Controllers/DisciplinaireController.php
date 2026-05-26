<?php

namespace App\Http\Controllers;

use App\Models\Apprenant;
use App\Models\Classe;
use App\Models\Institution;
use App\Models\Niveau;
use App\Models\SuiviDisciplinaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DisciplinaireController extends Controller
{
    /* ── Helper institution ── */
    private function getInstitution(): Institution
    {
        $institution = Auth::user()?->institution;
        if (! $institution) abort(403, 'Aucun établissement lié à votre compte.');
        return $institution;
    }

    /* ════════════════════════════════════════════════════
     | LISTE GLOBALE — admin
     | GET /admin/disciplinaire
     ════════════════════════════════════════════════════ */
    public function index(Request $request)
    {
        $user        = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();
        $instId      = $institution->id;

        $annee    = $request->get('annee',    date('Y'));
        $type     = $request->get('type',     '');
        $gravite  = $request->get('gravite',  '');
        $statut   = $request->get('statut',   '');
        $classeId = $request->get('classe_id','');
        $search   = $request->get('search',   '');

        $query = SuiviDisciplinaire::where('institution_id', $instId)
            ->where('annee_civile', $annee)
            ->with(['apprenant.classe', 'recordedBy'])
            ->orderByDesc('date_incident');

        if ($type)     $query->where('type',    $type);
        if ($gravite)  $query->where('gravite', $gravite);
        if ($statut)   $query->where('statut',  $statut);
        if ($classeId) {
            $query->whereHas('apprenant', fn($q) => $q->where('class_id', $classeId));
        }
        if ($search) {
            $query->whereHas('apprenant', fn($q) => $q
                ->where('nom',    'like', "%{$search}%")
                ->orWhere('prenom','like', "%{$search}%")
            );
        }

        $incidents = $query->paginate(20)->withQueryString();

        // Stats
        $stats = SuiviDisciplinaire::where('institution_id', $instId)
    ->where('annee_civile', $annee)
    ->selectRaw("
        COUNT(*) as total,
        COUNT(CASE WHEN gravite = 1 THEN 1 END) as mineurs,
        COUNT(CASE WHEN gravite = 2 THEN 1 END) as moderes,
        COUNT(CASE WHEN gravite = 3 THEN 1 END) as graves,
        COUNT(CASE WHEN statut = 'ouvert' THEN 1 END) as ouverts,
        COUNT(CASE WHEN statut = 'en_suivi' THEN 1 END) as en_suivi,
        COUNT(CASE WHEN statut = 'clos' THEN 1 END) as clos,
        COUNT(CASE WHEN parents_notifies = true THEN 1 END) as notifies
    ")
    ->first();

        // Par type
        $parType = SuiviDisciplinaire::where('institution_id', $instId)
            ->where('annee_civile', $annee)
            ->select('type', DB::raw('COUNT(*) as total'))
            ->groupBy('type')->orderByDesc('total')->get();

        // Top apprenants concernés
        $topApprenants = SuiviDisciplinaire::where('institution_id', $instId)
            ->where('annee_civile', $annee)
            ->select('apprenant_id', DB::raw('COUNT(*) as nb_incidents'))
            ->groupBy('apprenant_id')->orderByDesc('nb_incidents')
            ->with('apprenant:id,nom,prenom,class_id')
            ->limit(5)->get();

        // Années disponibles
        $anneesDispos = SuiviDisciplinaire::where('institution_id', $instId)
            ->distinct()->pluck('annee_civile')->sortDesc()->values();
        if (! $anneesDispos->contains($annee)) $anneesDispos->prepend($annee);

        // Données formulaire
        $classes   = Classe::where('institution_id', $instId)->orderBy('name')->get();
        $apprenants = Apprenant::where('institution_id', $instId)
            ->with('classe:id,name')->orderBy('nom')->get();
        $typeLabels     = SuiviDisciplinaire::typeLabels();
        $sanctionLabels = SuiviDisciplinaire::sanctionLabels();
        $graviteLabels  = SuiviDisciplinaire::graviteLabels();

        return view('admin.Disciplinaire', compact(
            'user', 'institution',
            'incidents', 'stats', 'parType', 'topApprenants',
            'annee', 'type', 'gravite', 'statut', 'classeId', 'search',
            'anneesDispos', 'classes', 'apprenants',
            'typeLabels', 'sanctionLabels', 'graviteLabels'
        ));
    }

    /* ════════════════════════════════════════════════════
     | FICHE D'UN APPRENANT — admin + parent
     | GET /admin/disciplinaire/apprenant/{apprenant}
     ════════════════════════════════════════════════════ */
    public function apprenant(Apprenant $apprenant, Request $request)
    {
        $user        = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();

        // 🔒 Sécurité institution
        if ((int) $apprenant->institution_id !== $institution->id) abort(403);

        $annee = $request->get('annee', date('Y'));

        $incidents = SuiviDisciplinaire::where('apprenant_id', $apprenant->id)
            ->where('annee_civile', $annee)
            ->with('recordedBy')
            ->orderByDesc('date_incident')
            ->get();

        $statsApprenant = [
            'total'    => $incidents->count(),
            'graves'   => $incidents->where('gravite', 3)->count(),
            'ouverts'  => $incidents->where('statut', 'ouvert')->count(),
            'notifies' => $incidents->where('parents_notifies', true)->count(),
        ];

        // Toutes les années disponibles pour cet apprenant
        $anneesDispos = SuiviDisciplinaire::where('apprenant_id', $apprenant->id)
            ->distinct()->pluck('annee_civile')->sortDesc()->values();
        if (! $anneesDispos->contains($annee)) $anneesDispos->prepend($annee);

        $typeLabels     = SuiviDisciplinaire::typeLabels();
        $sanctionLabels = SuiviDisciplinaire::sanctionLabels();
        $graviteLabels  = SuiviDisciplinaire::graviteLabels();

        return view('admin.disciplinaire-apprenant', compact(
            'user', 'institution', 'apprenant',
            'incidents', 'statsApprenant',
            'annee', 'anneesDispos',
            'typeLabels', 'sanctionLabels', 'graviteLabels'
        ));
    }

    /* ════════════════════════════════════════════════════
     | ENREGISTRER UN INCIDENT
     | POST /admin/disciplinaire
     ════════════════════════════════════════════════════ */
    public function store(Request $request)
    {
        $institution = $this->getInstitution();
        $instId      = $institution->id;

        $data = $request->validate([
            'apprenant_id'         => 'required|exists:apprenants,id',
            'date_incident'        => 'required|date|before_or_equal:today',
            'type'                 => 'required|in:absence,retard,insolence,violence,triche,perturbation,tenue,autre',
            'gravite'              => 'required|integer|in:1,2,3',
            'description'          => 'nullable|string|max:1000',
            'sanction'             => 'required|in:aucune,avertissement,blame,exclusion_cours,exclusion_temp,exclusion_def,convocation_parents,travail_supplementaire,autre',
            'sanction_detail'      => 'nullable|string|max:500',
            'parents_notifies'     => 'nullable|boolean',
            'date_notification'    => 'nullable|date',
            'observations'         => 'nullable|string|max:1000',
            'statut'               => 'required|in:ouvert,en_suivi,clos',
        ]);

        // 🔒 L'apprenant doit appartenir à l'institution
        Apprenant::where('id', $data['apprenant_id'])
            ->where('institution_id', $instId)
            ->firstOrFail();

        $annee = date('Y', strtotime($data['date_incident']));

        SuiviDisciplinaire::create(array_merge($data, [
            'institution_id'   => $instId,
            'recorded_by'      => Auth::id(),
            'annee_civile'     => $annee,
            'annee_academique' => $institution->academic_year,
            'parents_notifies' => (bool) ($data['parents_notifies'] ?? false),
        ]));

        return redirect()->back()
            ->with('success', 'Incident disciplinaire enregistré avec succès.');
    }

    /* ════════════════════════════════════════════════════
     | METTRE À JOUR UN INCIDENT
     | PUT /admin/disciplinaire/{incident}
     ════════════════════════════════════════════════════ */
    public function update(Request $request, SuiviDisciplinaire $disciplinaire)
    {
        $institution = $this->getInstitution();

        // 🔒 Sécurité
        if ((int) $disciplinaire->institution_id !== $institution->id) abort(403);

        $data = $request->validate([
            'date_incident'           => 'required|date|before_or_equal:today',
            'type'                    => 'required|in:absence,retard,insolence,violence,triche,perturbation,tenue,autre',
            'gravite'                 => 'required|integer|in:1,2,3',
            'description'             => 'nullable|string|max:1000',
            'sanction'                => 'required|in:aucune,avertissement,blame,exclusion_cours,exclusion_temp,exclusion_def,convocation_parents,travail_supplementaire,autre',
            'sanction_detail'         => 'nullable|string|max:500',
            'sanction_executee'       => 'nullable|boolean',
            'sanction_date_execution' => 'nullable|date',
            'parents_notifies'        => 'nullable|boolean',
            'date_notification'       => 'nullable|date',
            'observations'            => 'nullable|string|max:1000',
            'statut'                  => 'required|in:ouvert,en_suivi,clos',
        ]);

        $disciplinaire->update(array_merge($data, [
            'sanction_executee' => (bool) ($data['sanction_executee'] ?? false),
            'parents_notifies'  => (bool) ($data['parents_notifies']  ?? false),
        ]));

        return redirect()->back()->with('success', 'Incident mis à jour.');
    }

    /* ════════════════════════════════════════════════════
     | SUPPRIMER UN INCIDENT
     | DELETE /admin/disciplinaire/{incident}
     ════════════════════════════════════════════════════ */
    public function destroy(SuiviDisciplinaire $disciplinaire)
    {
        $institution = $this->getInstitution();
        if ((int) $disciplinaire->institution_id !== $institution->id) abort(403);

        $disciplinaire->delete();

        return redirect()->back()->with('success', 'Incident supprimé.');
    }

    /* ════════════════════════════════════════════════════
     | VUE PARENT — lecture seule des incidents de l'enfant
     | GET /parent/disciplinaire/{apprenant}
     ════════════════════════════════════════════════════ */
    public function parentView(Apprenant $apprenant, Request $request)
    {
        $user = Auth::user() ?? redirect()->route('login')->send();

        // 🔒 Vérifier que ce user est bien parent de cet apprenant
        $isParent = $apprenant->parents()
            ->where('user_id', $user->id)
            ->exists();

        // Aussi accepter si le parent est lié via SchoolParent → user
        if (! $isParent) {
            $isParent = $apprenant->parents()
                ->whereHas('user', fn($q) => $q->where('id', $user->id))
                ->exists();
        }

        if (! $isParent) abort(403, 'Accès réservé aux parents de cet élève.');

        $annee = $request->get('annee', date('Y'));

        $incidents = SuiviDisciplinaire::where('apprenant_id', $apprenant->id)
            ->where('annee_civile', $annee)
            ->orderByDesc('date_incident')
            ->get();

        $statsApprenant = [
            'total'   => $incidents->count(),
            'graves'  => $incidents->where('gravite', 3)->count(),
            'ouverts' => $incidents->where('statut', 'ouvert')->count(),
        ];

        $anneesDispos = SuiviDisciplinaire::where('apprenant_id', $apprenant->id)
            ->distinct()->pluck('annee_civile')->sortDesc()->values();
        if (! $anneesDispos->contains($annee)) $anneesDispos->prepend($annee);

        $typeLabels     = SuiviDisciplinaire::typeLabels();
        $sanctionLabels = SuiviDisciplinaire::sanctionLabels();
        $graviteLabels  = SuiviDisciplinaire::graviteLabels();

        return view('parent.disciplinaire', compact(
            'user', 'apprenant',
            'incidents', 'statsApprenant',
            'annee', 'anneesDispos',
            'typeLabels', 'sanctionLabels', 'graviteLabels'
        ));
    }

    /* ════════════════════════════════════════════════════
     | EXPORT CSV
     | GET /admin/disciplinaire/export
     ════════════════════════════════════════════════════ */
    public function export(Request $request)
    {
        $instId = $this->getInstitution()->id;
        $annee  = $request->get('annee', date('Y'));

        $incidents = SuiviDisciplinaire::where('institution_id', $instId)
            ->where('annee_civile', $annee)
            ->with(['apprenant.classe', 'recordedBy'])
            ->orderByDesc('date_incident')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="disciplinaire_'.$annee.'_'.now()->format('Ymd').'.csv"',
        ];

        $typeLabels     = SuiviDisciplinaire::typeLabels();
        $sanctionLabels = SuiviDisciplinaire::sanctionLabels();
        $graviteLabels  = SuiviDisciplinaire::graviteLabels();

        return response()->stream(function () use ($incidents, $typeLabels, $sanctionLabels, $graviteLabels) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($h, [
                'Date', 'Apprenant', 'Classe', 'Type', 'Gravité',
                'Description', 'Sanction', 'Parents notifiés', 'Statut', 'Saisi par',
            ]);
            foreach ($incidents as $i) {
                fputcsv($h, [
                    $i->date_incident?->format('d/m/Y'),
                    ($i->apprenant?->prenom ?? '') . ' ' . ($i->apprenant?->nom ?? ''),
                    $i->apprenant?->classe?->name ?? '',
                    $typeLabels[$i->type]     ?? $i->type,
                    $graviteLabels[$i->gravite] ?? $i->gravite,
                    $i->description ?? '',
                    $sanctionLabels[$i->sanction] ?? $i->sanction,
                    $i->parents_notifies ? 'Oui' : 'Non',
                    $i->statut,
                    $i->recordedBy?->name ?? '',
                ]);
            }
            fclose($h);
        }, 200, $headers);
    }
}
