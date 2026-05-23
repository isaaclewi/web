<?php

namespace App\Http\Controllers;

use App\Models\Apprenant;
use App\Models\Bulletin;
use App\Models\Classe;
use App\Models\EmploiDuTemps;
use App\Models\FinancialRecord;
use App\Models\Institution;
use App\Models\Niveau;
use App\Models\Filiere;
use App\Models\Staff;
use App\Models\SuiviDisciplinaire;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * BLED — Export PDF
 *
 * Génère des PDF structurés et paginés pour toutes les catégories d'archives,
 * avec filtrage fin (général → particulier).
 */
class BledPdfController extends Controller
{
    /* ─────────────────────────────────────────────────────────────
     | Helper : institution courante
     ───────────────────────────────────────────────────────────── */
    private function getInstitution(): Institution
    {
        $inst = Auth::user()?->institution;
        if (! $inst) {
            abort(403, 'Aucun établissement lié à votre compte.');
        }
        return $inst;
    }

    /* ─────────────────────────────────────────────────────────────
     | PAGE DE FILTRES — formulaire avant génération PDF
     | GET /admin/bled/pdf/filtres
     ───────────────────────────────────────────────────────────── */
    public function filtres(Request $request)
    {
        $institution = $this->getInstitution();
        $instId      = $institution->id;

        $categorie = $request->get('cat', 'apprenants');
        $annee     = $request->get('annee', $institution->academic_year ?? date('Y') . '-' . (date('Y') + 1));

        // Données pour les selects du formulaire
        $anneesDispos = $this->getAnneesDispos($instId, $annee);
        $classes = Classe::where('institution_id', $instId)
    ->with(['niveau', 'filiere'])
    ->withCount('apprenants') // 🔥 IMPORTANT
    ->orderBy('name')
    ->get();
        $niveaux      = Niveau::where('institution_id', $instId)->orderBy('name')->get();
        $filieres     = Filiere::where('institution_id', $instId)->orderBy('name')->get();
        $apprenants = Apprenant::where('institution_id', $instId)
    ->with('classe') // 🔥 IMPORTANT
    ->select('id', 'nom', 'prenom', 'matricule', 'class_id')
    ->orderBy('nom')
    ->get();

    // Préparer les données JSON pour le JS (autocomplete)
$jsonFilieres = $filieres->map(function ($f) {
    return [
        'id' => $f->id,
        'name' => $f->name,
    ];
});

$jsonNiveaux = $niveaux->map(function ($n) {
    return [
        'id' => $n->id,
        'name' => $n->name,
    ];
});

$jsonClasses = $classes->map(function ($c) {
    return [
        'id' => $c->id,
        'name' => $c->name,
        'niveau_id' => $c->niveau_id,
        'filiere_id' => $c->filiere_id,
    ];
});

$jsonApprenants = $apprenants->map(function ($a) {
    return [
        'id' => $a->id,
        'nom' => $a->nom,
        'prenom' => $a->prenom,
        'matricule' => $a->matricule,
        'class_id' => $a->class_id,
    ];
});
        return view('admin.bled_pdf_filtres', compact(
            'institution', 'categorie', 'annee', 'anneesDispos',
            'classes', 'niveaux', 'filieres', 'apprenants',
            'jsonFilieres', 'jsonNiveaux', 'jsonClasses', 'jsonApprenants'
        ));
    }

    /* ─────────────────────────────────────────────────────────────
     | EXPORT PDF — génération et téléchargement
     | GET /admin/bled/pdf/export
     ───────────────────────────────────────────────────────────── */
    public function export(Request $request)
    {
        $institution = $this->getInstitution();
        $instId      = $institution->id;

        $categorie  = $request->get('cat', 'apprenants');
        $annee      = $request->get('annee', $institution->academic_year ?? date('Y') . '-' . (date('Y') + 1));
        $classeId   = $request->get('classe_id');
        $niveauId   = $request->get('niveau_id');
        $filiereId  = $request->get('filiere_id');
        $apprenantId= $request->get('apprenant_id');
        $periode    = $request->get('periode');
        $statut     = $request->get('statut');
        $search = (string) $request->input('q', '');

        // Collecter les données selon la catégorie
        $data = $this->collectData(
            $instId, $categorie, $annee,
            $classeId, $niveauId, $filiereId,
            $apprenantId, $periode, $statut, $search
        );

        // Métadonnées pour l'en-tête PDF
        $meta = $this->buildMeta(
            $institution, $categorie, $annee,
            $classeId, $niveauId, $filiereId,
            $apprenantId, $periode, $statut
        );

        // Rendu HTML → PDF via vue Blade dédiée
        $html = view('admin.bled_pdf_template', compact(
            'institution', 'categorie', 'annee', 'data', 'meta'
        ))->render();

        $fileName = 'bled_' . $categorie . '_' . $annee . '_' . now()->format('Ymd_His') . '.pdf';

        // Utilisation de wkhtmltopdf si disponible, sinon retour HTML imprimable
        if ($this->wkhtmlAvailable()) {
            return $this->generateWithWkhtml($html, $fileName);
        }

        // Fallback : HTML optimisé impression (page avec CSS @media print)
        return response($html, 200, [
            'Content-Type'        => 'text/html; charset=UTF-8',
            'X-PDF-Fallback'      => 'true',
        ]);
    }

    /* ─────────────────────────────────────────────────────────────
     | APERÇU HTML — page imprimable directement
     | GET /admin/bled/pdf/apercu
     ───────────────────────────────────────────────────────────── */
    public function apercu(Request $request)
    {
        $institution = $this->getInstitution();
        $instId      = $institution->id;

        $categorie   = $request->get('cat', 'apprenants');
        $annee       = $request->get('annee', $institution->academic_year ?? date('Y') . '-' . (date('Y') + 1));
        $classeId    = $request->get('classe_id');
        $niveauId    = $request->get('niveau_id');
        $filiereId   = $request->get('filiere_id');
        $apprenantId = $request->get('apprenant_id');
        $periode     = $request->get('periode');
        $statut      = $request->get('statut');
        $search = (string) $request->input('q', '');

        $data = $this->collectData(
            $instId, $categorie, $annee,
            $classeId, $niveauId, $filiereId,
            $apprenantId, $periode, $statut, $search
        );

        $meta = $this->buildMeta(
            $institution, $categorie, $annee,
            $classeId, $niveauId, $filiereId,
            $apprenantId, $periode, $statut
        );

        return view('admin.bled_pdf_template', compact(
            'institution', 'categorie', 'annee', 'data', 'meta'
        ));
    }

    /* ══════════════════════════════════════════════════════════════
     | COLLECTEURS DE DONNÉES
     ══════════════════════════════════════════════════════════════ */

    private function collectData(
        int $instId, string $categorie, string $annee,
        ?string $classeId, ?string $niveauId, ?string $filiereId,
        ?string $apprenantId, ?string $periode, ?string $statut, string $search
    ): array {
        return match ($categorie) {
            'apprenants'    => $this->dataApprenants($instId, $annee, $classeId, $niveauId, $filiereId, $apprenantId, $search),
            'enseignants'   => $this->dataEnseignants($instId, $classeId, $niveauId, $filiereId, $search),
            'bulletins'     => $this->dataBulletins($instId, $annee, $classeId, $niveauId, $filiereId, $apprenantId, $periode),
            'finances'      => $this->dataFinances($instId, $annee, $classeId, $niveauId, $filiereId, $apprenantId, $statut, $search),
            'disciplinaire' => $this->dataDisciplinaire($instId, $annee, $classeId, $apprenantId),
            'classes'       => $this->dataClasses($instId, $niveauId, $filiereId),
            'planning'      => $this->dataPlanning($instId, $annee, $classeId),
            'staff'         => $this->dataStaff($instId, $search),
            'complet'       => $this->dataComplet($instId, $annee, $classeId, $niveauId, $filiereId, $apprenantId, $periode, $statut, $search),
            default         => [],
        };
    }

    /* ── Apprenants ── */
    private function dataApprenants(
        int $instId, string $annee, ?string $classeId,
        ?string $niveauId, ?string $filiereId, ?string $apprenantId, string $search
    ): array {
        $q = Apprenant::where('institution_id', $instId)
            ->where('annee_academique', $annee)
            ->with(['niveau', 'filiere', 'classe']);

        if ($apprenantId) $q->where('id', $apprenantId);
        elseif ($classeId) $q->where('class_id', $classeId);
        elseif ($niveauId) $q->where('niveau_id', $niveauId);
        elseif ($filiereId) $q->where('filiere_id', $filiereId);

        if ($search) {
            $q->where(fn($s) => $s
                ->where('nom', 'like', "%{$search}%")
                ->orWhere('prenom', 'like', "%{$search}%")
                ->orWhere('matricule', 'like', "%{$search}%")
            );
        }

        $rows = $q->orderBy('nom')->get();

        return [
            'rows'    => $rows,
            'total'   => $rows->count(),
            'colonnes'=> ['Matricule', 'Nom complet', 'Sexe', 'Date naissance', 'Niveau', 'Filière', 'Classe', 'Statut'],
            'mapper'  => fn($a) => [
                $a->matricule,
                $a->prenom . ' ' . $a->nom,
                $a->sexe === 'M' ? 'Masculin' : ($a->sexe === 'F' ? 'Féminin' : '—'),
                $a->date_naissance ? \Carbon\Carbon::parse($a->date_naissance)->format('d/m/Y') : '—',
                optional($a->niveau)->name ?? '—',
                optional($a->filiere)->name ?? '—',
                optional($a->classe)->name ?? '—',
                $a->status ? 'Actif' : 'Inactif',
            ],
            'stats'   => [
                'Total'    => $rows->count(),
                'Actifs'   => $rows->where('status', 1)->count(),
                'Garçons'  => $rows->where('sexe', 'M')->count(),
                'Filles'   => $rows->where('sexe', 'F')->count(),
            ],
        ];
    }

    /* ── Enseignants ── */
    private function dataEnseignants(
        int $instId, ?string $classeId, ?string $niveauId,
        ?string $filiereId, string $search
    ): array {
        $q = Teacher::where('institution_id', $instId)->with(['niveaux', 'filieres', 'classes']);

        if ($classeId)  $q->whereHas('classes', fn($s) => $s->where('classes.id', $classeId));
        if ($niveauId)  $q->whereHas('niveaux', fn($s) => $s->where('niveaux.id', $niveauId));
        if ($filiereId) $q->whereHas('filieres', fn($s) => $s->where('filieres.id', $filiereId));
        if ($search)    $q->where(fn($s) => $s
            ->where('nom', 'like', "%{$search}%")
            ->orWhere('prenom', 'like', "%{$search}%")
            ->orWhere('specialite', 'like', "%{$search}%")
            ->orWhere('matricule', 'like', "%{$search}%")
        );

        $rows = $q->orderBy('nom')->get();

        return [
            'rows'    => $rows,
            'total'   => $rows->count(),
            'colonnes'=> ['Matricule', 'Nom complet', 'Sexe', 'Spécialité', 'Contrat', 'Date recrutement', 'Téléphone', 'Statut'],
            'mapper'  => fn($t) => [
                $t->matricule,
                $t->prenom . ' ' . $t->nom,
                $t->sexe === 'M' ? 'Masculin' : ($t->sexe === 'F' ? 'Féminin' : '—'),
                $t->specialite ?? '—',
                ucfirst($t->type_contrat ?? '—'),
                $t->date_recrutement ? \Carbon\Carbon::parse($t->date_recrutement)->format('d/m/Y') : '—',
                $t->telephone ?? '—',
                $t->status ? 'Actif' : 'Inactif',
            ],
            'stats'   => [
                'Total'     => $rows->count(),
                'Actifs'    => $rows->where('status', 1)->count(),
                'Hommes'    => $rows->where('sexe', 'M')->count(),
                'Femmes'    => $rows->where('sexe', 'F')->count(),
            ],
        ];
    }

    /* ── Bulletins ── */
    private function dataBulletins(
        int $instId, string $annee, ?string $classeId, ?string $niveauId,
        ?string $filiereId, ?string $apprenantId, ?string $periode
    ): array {
        $q = Bulletin::where('institution_id', $instId)
            ->where('annee_academique', $annee)
            ->with(['apprenant.niveau', 'apprenant.filiere', 'classe']);

        if ($apprenantId) $q->where('apprenant_id', $apprenantId);
        elseif ($classeId) $q->where('classe_id', $classeId);
        elseif ($niveauId) $q->whereHas('apprenant', fn($s) => $s->where('niveau_id', $niveauId));
        elseif ($filiereId) $q->whereHas('apprenant', fn($s) => $s->where('filiere_id', $filiereId));

        if ($periode) $q->where('periode', $periode);

        $rows = $q->orderBy('rang')->get();

        $periodeLabels = [
            'trimestre1' => '1er Trimestre', 'trimestre2' => '2ème Trimestre',
            'trimestre3' => '3ème Trimestre', 'semestre1'  => '1er Semestre',
            'semestre2'  => '2ème Semestre',  'annuel'     => 'Annuel',
        ];

        return [
            'rows'    => $rows,
            'total'   => $rows->count(),
            'colonnes'=> ['Apprenant', 'Classe', 'Période', 'Moy. Gén.', 'Rang', 'Effectif', 'Mention', 'Admis', 'Publié'],
            'mapper'  => fn($b) => [
                (optional($b->apprenant)->prenom ?? '') . ' ' . (optional($b->apprenant)->nom ?? ''),
                optional($b->classe)->name ?? '—',
                $periodeLabels[$b->periode] ?? $b->periode,
                number_format($b->moyenne_generale, 2),
                $b->rang ?? '—',
                $b->effectif_classe ?? '—',
                $b->mention ?? '—',
                $b->admis ? '✓ Admis' : '✗ Non admis',
                $b->publie ? 'Oui' : 'Non',
            ],
            'stats'   => [
                'Total bulletins' => $rows->count(),
                'Admis'           => $rows->where('admis', true)->count(),
                'Non admis'       => $rows->where('admis', false)->count(),
                'Moy. générale'   => $rows->count() ? number_format($rows->avg('moyenne_generale'), 2) . '/20' : '—',
            ],
            'detail_par_apprenant' => $apprenantId ? $this->bulletinsDetailApprenant($rows) : null,
        ];
    }

    /* ── Détail bulletins d'un seul apprenant (toutes périodes) ── */
    private function bulletinsDetailApprenant($bulletins): array
    {
        $detail = [];
        foreach ($bulletins as $b) {
            $detail[] = [
                'periode'  => $b->periode,
                'moyenne'  => number_format($b->moyenne_generale, 2),
                'rang'     => $b->rang,
                'effectif' => $b->effectif_classe,
                'mention'  => $b->mention,
                'admis'    => $b->admis,
                'notes'    => method_exists($b, 'notes') ? $b->notes()->with('subject')->get() : collect(),
            ];
        }
        return $detail;
    }

    /* ── Finances ── */
    private function dataFinances(
        int $instId, string $annee, ?string $classeId, ?string $niveauId,
        ?string $filiereId, ?string $apprenantId, ?string $statut, string $search
    ): array {
        $q = FinancialRecord::where('institution_id', $instId)
            ->where('annee_academique', $annee)
            ->with(['apprenant.classe', 'apprenant.niveau', 'apprenant.filiere']);

        if ($apprenantId) $q->where('apprenant_id', $apprenantId);
        elseif ($classeId) $q->whereHas('apprenant', fn($s) => $s->where('class_id', $classeId));
        elseif ($niveauId) $q->whereHas('apprenant', fn($s) => $s->where('niveau_id', $niveauId));
        elseif ($filiereId) $q->whereHas('apprenant', fn($s) => $s->where('filiere_id', $filiereId));

        if ($statut) $q->where('statut', $statut);
        if ($search)  $q->whereHas('apprenant', fn($s) => $s
            ->where('nom', 'like', "%{$search}%")
            ->orWhere('prenom', 'like', "%{$search}%")
            ->orWhere('matricule', 'like', "%{$search}%")
        );

        $rows = $q->orderBy('mois')->get();

        return [
            'rows'    => $rows,
            'total'   => $rows->count(),
            'colonnes'=> ['Apprenant', 'Classe', 'Mois', 'Dû (FCFA)', 'Payé (FCFA)', 'Reste (FCFA)', 'Statut', 'Mode', 'Date paiement'],
            'mapper'  => fn($f) => [
                (optional($f->apprenant)->prenom ?? '') . ' ' . (optional($f->apprenant)->nom ?? ''),
                optional($f->apprenant?->classe)->name ?? '—',
                $f->mois_label,
                number_format($f->montant_du, 0, ',', ' '),
                number_format($f->montant_paye, 0, ',', ' '),
                number_format($f->montant_reste, 0, ',', ' '),
                match($f->statut) { 'paye' => '✓ Payé', 'partiel' => '△ Partiel', default => '✗ Impayé' },
                $f->mode_paiement ? ucfirst(str_replace('_', ' ', $f->mode_paiement)) : '—',
                $f->date_paiement ? \Carbon\Carbon::parse($f->date_paiement)->format('d/m/Y') : '—',
            ],
            'stats'   => [
                'Total dû'   => number_format($rows->sum('montant_du'), 0, ',', ' ') . ' FCFA',
                'Total payé' => number_format($rows->sum('montant_paye'), 0, ',', ' ') . ' FCFA',
                'Total reste'=> number_format($rows->sum('montant_reste'), 0, ',', ' ') . ' FCFA',
                'Taux recouvrement' => $rows->sum('montant_du') > 0
                    ? round($rows->sum('montant_paye') / $rows->sum('montant_du') * 100, 1) . '%'
                    : '0%',
            ],
        ];
    }

    /* ── Disciplinaire ── */
    private function dataDisciplinaire(
        int $instId, string $annee, ?string $classeId, ?string $apprenantId
    ): array {
        $q = SuiviDisciplinaire::where('institution_id', $instId)
            ->where('annee_academique', $annee)
            ->with(['apprenant.classe']);

        if ($apprenantId) $q->where('apprenant_id', $apprenantId);
        elseif ($classeId) $q->whereHas('apprenant', fn($s) => $s->where('class_id', $classeId));

        $rows = $q->orderBy('date_incident', 'desc')->get();

        return [
            'rows'    => $rows,
            'total'   => $rows->count(),
            'colonnes'=> ['Apprenant', 'Classe', 'Date', 'Type', 'Gravité', 'Sanction', 'Parents notifiés', 'Statut'],
            'mapper'  => fn($d) => [
                (optional($d->apprenant)->prenom ?? '') . ' ' . (optional($d->apprenant)->nom ?? ''),
                optional($d->apprenant?->classe)->name ?? '—',
                $d->date_incident ? $d->date_incident->format('d/m/Y') : '—',
                $d->type_label ?? $d->type ?? '—',
                $d->gravite_label ?? $d->gravite ?? '—',
                $d->sanction_label ?? $d->sanction ?? '—',
                $d->parents_notifies ? 'Oui' : 'Non',
                ucfirst($d->statut ?? '—'),
            ],
            'stats'   => [
                'Total incidents'    => $rows->count(),
                'Parents notifiés'   => $rows->where('parents_notifies', true)->count(),
                'Parents non notifiés' => $rows->where('parents_notifies', false)->count(),
            ],
        ];
    }

    /* ── Classes ── */
    private function dataClasses(int $instId, ?string $niveauId, ?string $filiereId): array
    {
        $q = Classe::where('institution_id', $instId)
            ->withCount('apprenants')
            ->with(['niveau', 'filiere']);

        if ($niveauId)  $q->where('niveau_id', $niveauId);
        if ($filiereId) $q->where('filiere_id', $filiereId);

        $rows = $q->orderBy('name')->get();

        return [
            'rows'    => $rows,
            'total'   => $rows->count(),
            'colonnes'=> ['Nom', 'Code', 'Niveau', 'Filière', 'Effectif'],
            'mapper'  => fn($c) => [
                $c->name,
                $c->code ?? '—',
                optional($c->niveau)->name ?? '—',
                optional($c->filiere)->name ?? '—',
                $c->apprenants_count . ' élève(s)',
            ],
            'stats'   => [
                'Nombre de classes'     => $rows->count(),
                'Total apprenants'      => $rows->sum('apprenants_count'),
                'Moy. par classe'       => $rows->count() ? round($rows->avg('apprenants_count'), 1) : 0,
            ],
        ];
    }

    /* ── Planning ── */
    private function dataPlanning(int $instId, string $annee, ?string $classeId): array
    {
        $q = EmploiDuTemps::where('institution_id', $instId)
            ->where('annee_academique', $annee)
            ->with(['classe', 'subject', 'teacher']);

        if ($classeId) $q->where('class_id', $classeId);

        $rows = $q->orderBy('jour')->orderBy('heure_debut')->get();

        return [
            'rows'    => $rows,
            'total'   => $rows->count(),
            'colonnes'=> ['Classe', 'Matière', 'Enseignant', 'Jour', 'Début', 'Fin', 'Type', 'Salle'],
            'mapper'  => fn($e) => [
                optional($e->classe)->name ?? '—',
                optional($e->subject)->name ?? '—',
                $e->teacher ? $e->teacher->prenom . ' ' . $e->teacher->nom : '—',
                ucfirst($e->jour ?? '—'),
                isset($e->heure_debut) ? substr($e->heure_debut, 0, 5) : '—',
                isset($e->heure_fin) ? substr($e->heure_fin, 0, 5) : '—',
                $e->type_label ?? $e->type ?? '—',
                $e->salle ?? '—',
            ],
            'stats'   => [
                'Total séances' => $rows->count(),
                'Classes'       => $rows->pluck('class_id')->unique()->count(),
                'Enseignants'   => $rows->pluck('teacher_id')->unique()->count(),
            ],
        ];
    }

    /* ── Staff ── */
    private function dataStaff(int $instId, string $search): array
    {
        $q = Staff::where('institution_id', $instId)->with(['administrativeUnit']);

        if ($search) $q->where(fn($s) => $s
            ->where('nom', 'like', "%{$search}%")
            ->orWhere('prenom', 'like', "%{$search}%")
            ->orWhere('poste', 'like', "%{$search}%")
        );

        $rows = $q->orderBy('nom')->get();

        return [
            'rows'    => $rows,
            'total'   => $rows->count(),
            'colonnes'=> ['Matricule', 'Nom complet', 'Poste', 'Unité admin.', 'Téléphone', 'Email', 'Statut'],
            'mapper'  => fn($s) => [
                $s->matricule,
                $s->prenom . ' ' . $s->nom,
                $s->poste ?? '—',
                optional($s->administrativeUnit)->name ?? '—',
                $s->telephone ?? '—',
                $s->email ?? '—',
                $s->status ? 'Actif' : 'Inactif',
            ],
            'stats'   => [
                'Total membres' => $rows->count(),
                'Actifs'        => $rows->where('status', 1)->count(),
                'Inactifs'      => $rows->where('status', 0)->count(),
            ],
        ];
    }

    /* ── Complet ── */
    private function dataComplet(
        int $instId, string $annee, ?string $classeId, ?string $niveauId,
        ?string $filiereId, ?string $apprenantId, ?string $periode,
        ?string $statut, string $search
    ): array {
        return [
            'sections' => [
                'apprenants'    => $this->dataApprenants($instId, $annee, $classeId, $niveauId, $filiereId, $apprenantId, $search),
                'enseignants'   => $this->dataEnseignants($instId, $classeId, $niveauId, $filiereId, $search),
                'bulletins'     => $this->dataBulletins($instId, $annee, $classeId, $niveauId, $filiereId, $apprenantId, $periode),
                'finances'      => $this->dataFinances($instId, $annee, $classeId, $niveauId, $filiereId, $apprenantId, $statut, $search),
                'disciplinaire' => $this->dataDisciplinaire($instId, $annee, $classeId, $apprenantId),
                'classes'       => $this->dataClasses($instId, $niveauId, $filiereId),
                'staff'         => $this->dataStaff($instId, $search),
            ],
        ];
    }

    /* ══════════════════════════════════════════════════════════════
     | MÉTADONNÉES — en-tête du document PDF
     ══════════════════════════════════════════════════════════════ */

    private function buildMeta(
        Institution $institution, string $categorie, string $annee,
        ?string $classeId, ?string $niveauId, ?string $filiereId,
        ?string $apprenantId, ?string $periode, ?string $statut
    ): array {
        $instId = $institution->id;

        $catLabels = [
            'apprenants'    => 'Registre des Apprenants',
            'enseignants'   => 'Registre des Enseignants',
            'bulletins'     => 'Bulletins Scolaires',
            'finances'      => 'Registre Financier',
            'disciplinaire' => 'Suivi Disciplinaire',
            'classes'       => 'Registre des Classes',
            'planning'      => 'Emplois du Temps',
            'staff'         => 'Registre du Personnel Administratif',
            'complet'       => 'Archive Complète de l\'Établissement',
        ];

        $periodeLabels = [
            'trimestre1' => '1er Trimestre', 'trimestre2' => '2ème Trimestre',
            'trimestre3' => '3ème Trimestre', 'semestre1'  => '1er Semestre',
            'semestre2'  => '2ème Semestre',  'annuel'     => 'Annuel',
        ];

        $filtresActifs = [];

        if ($apprenantId) {
            $ap = Apprenant::find($apprenantId);
            if ($ap) $filtresActifs['Apprenant'] = $ap->prenom . ' ' . $ap->nom . ' (' . $ap->matricule . ')';
        }
        if ($classeId) {
            $cl = Classe::find($classeId);
            if ($cl) $filtresActifs['Classe'] = $cl->name;
        }
        if ($niveauId) {
            $nv = Niveau::find($niveauId);
            if ($nv) $filtresActifs['Niveau'] = $nv->name;
        }
        if ($filiereId) {
            $fi = Filiere::find($filiereId);
            if ($fi) $filtresActifs['Filière'] = $fi->name;
        }
        if ($periode) {
            $filtresActifs['Période'] = $periodeLabels[$periode] ?? $periode;
        }
        if ($statut) {
            $filtresActifs['Statut financier'] = match($statut) {
                'paye'    => 'Payé',
                'partiel' => 'Partiel',
                'impaye'  => 'Impayé',
                default   => $statut,
            };
        }

        return [
            'titre'         => $catLabels[$categorie] ?? ucfirst($categorie),
            'annee'         => $annee,
            'categorie'     => $categorie,
            'filtres'       => $filtresActifs,
            'date_edition'  => now()->format('d/m/Y à H:i'),
            'edite_par'     => Auth::user()?->name ?? 'Système',
            'institution'   => $institution->name,
            'logo'          => $institution->logo ?? null,
            'adresse'       => $institution->adresse ?? null,
            'telephone'     => $institution->telephone ?? null,
            'email'         => $institution->email ?? null,
        ];
    }

    /* ── Wkhtmltopdf ── */
    private function wkhtmlAvailable(): bool
    {
        return ! empty(shell_exec('which wkhtmltopdf 2>/dev/null'));
    }

    private function generateWithWkhtml(string $html, string $fileName)
    {
        $tmpHtml = tempnam(sys_get_temp_dir(), 'bled_') . '.html';
        $tmpPdf  = tempnam(sys_get_temp_dir(), 'bled_') . '.pdf';

        file_put_contents($tmpHtml, $html);

        shell_exec(sprintf(
            'wkhtmltopdf --page-size A4 --margin-top 15mm --margin-bottom 15mm '
            . '--margin-left 12mm --margin-right 12mm '
            . '--footer-center "Page [page] / [toPage]" --footer-font-size 8 '
            . '--header-right "%s" --header-font-size 8 '
            . '--enable-local-file-access %s %s 2>/dev/null',
            now()->format('d/m/Y H:i'),
            escapeshellarg($tmpHtml),
            escapeshellarg($tmpPdf)
        ));

        if (! file_exists($tmpPdf) || filesize($tmpPdf) === 0) {
            @unlink($tmpHtml);
            abort(500, 'Erreur de génération PDF. Veuillez utiliser l\'aperçu HTML.');
        }

        $content = file_get_contents($tmpPdf);
        @unlink($tmpHtml);
        @unlink($tmpPdf);

        return response($content, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            'Content-Length'      => strlen($content),
        ]);
    }

    /* ── Helper années disponibles ── */
    private function getAnneesDispos(int $instId, string $anneeActive): \Illuminate\Support\Collection
    {
        $a1 = Apprenant::where('institution_id', $instId)->distinct()->pluck('annee_academique');
        $a2 = FinancialRecord::where('institution_id', $instId)->distinct()->pluck('annee_academique');
        $a3 = Bulletin::where('institution_id', $instId)->distinct()->pluck('annee_academique');

        $all = $a1->merge($a2)->merge($a3)->filter()->unique()->sort()->values();
        if (! $all->contains($anneeActive)) {
            $all->push($anneeActive);
            $all = $all->sort()->values();
        }
        return $all;
    }
}
