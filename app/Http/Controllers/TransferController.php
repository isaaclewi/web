<?php

namespace App\Http\Controllers;

use App\Models\Apprenant;
use App\Models\Grade;
use App\Models\Institution;
use App\Models\ReportCard;
use App\Models\Subject;
use App\Models\SuiviDisciplinaire;
use App\Models\TransferRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    /* ══════════════════════════════════════════════════════════
     |  HELPER
     ══════════════════════════════════════════════════════════ */

    private function getInstitution(): Institution
    {
        $institution = Auth::user()?->institution;
        abort_if(! $institution, 403, 'Aucun établissement lié à votre compte.');
        return $institution;
    }

    /**
     * Charge le dossier complet d'un apprenant selon le scope autorisé.
     */
    private function buildDossier(Apprenant $apprenant, array $scope): array
    {
        $dossier = [];

        // ── Identité (toujours incluse) ──────────────────────────────
        $dossier['apprenant'] = $apprenant->load([
            'institution', 'niveau', 'filiere', 'classe',
        ]);

        // ── Notes & Évaluations ──────────────────────────────────────
        if (in_array('notes', $scope)) {
            $subjects = Subject::where('institution_id', $apprenant->institution_id)->get();
            $dossier['notes'] = Grade::where('apprenant_id', $apprenant->id)
                ->with(['evaluation.subject'])
                ->orderByDesc('created_at')
                ->get();

            // Moyennes par matière
            $dossier['moyennes'] = $subjects->map(function ($sub) use ($apprenant) {
                $grades = Grade::where('apprenant_id', $apprenant->id)
                    ->whereHas('evaluation', fn($q) => $q->where('subject_id', $sub->id))
                    ->get();
                return [
                    'matiere'     => $sub->name,
                    'coefficient' => $sub->coefficient,
                    'moyenne'     => $grades->isNotEmpty() ? round($grades->avg('score'), 2) : null,
                    'nb_notes'    => $grades->count(),
                    'min'         => $grades->isNotEmpty() ? $grades->min('score') : null,
                    'max'         => $grades->isNotEmpty() ? $grades->max('score') : null,
                ];
            })->filter(fn($m) => $m['nb_notes'] > 0)->values();

            // Moyenne générale pondérée
            $num = $den = 0;
            foreach ($dossier['moyennes'] as $m) {
                if ($m['moyenne'] !== null) {
                    $num += $m['moyenne'] * $m['coefficient'];
                    $den += $m['coefficient'];
                }
            }
            $dossier['moyenne_generale'] = $den > 0 ? round($num / $den, 2) : null;
        }

        // ── Bulletins ────────────────────────────────────────────────
        if (in_array('bulletins', $scope)) {
            $dossier['bulletins'] = ReportCard::where('apprenant_id', $apprenant->id)
                ->where('published', true)
                ->with(['classe', 'lines'])
                ->orderByDesc('created_at')
                ->get();
        }

        // ── Dossier disciplinaire ────────────────────────────────────
        if (in_array('discipline', $scope)) {
            $dossier['incidents'] = SuiviDisciplinaire::where('apprenant_id', $apprenant->id)
                ->with('recordedBy:id,name')
                ->orderByDesc('date_incident')
                ->get();

            $dossier['stats_discipline'] = [
                'total'  => $dossier['incidents']->count(),
                'graves' => $dossier['incidents']->where('gravite', 3)->count(),
                'types'  => $dossier['incidents']->groupBy('type')
                    ->map(fn($g) => $g->count()),
            ];
        }

        // ── Situation financière ─────────────────────────────────────
        if (in_array('finances', $scope)) {
            $records = $apprenant->financialRecords()
                ->orderBy('annee_academique')
                ->orderBy('mois')
                ->get();

            $dossier['finances']        = $records;
            $dossier['finances_totaux'] = [
                'total_du'    => $records->sum('montant_du'),
                'total_paye'  => $records->sum('montant_paye'),
                'total_reste' => $records->sum('montant_reste'),
            ];
        }

        // ── Historique des classes ───────────────────────────────────
        if (in_array('classes', $scope)) {
            try {
                $dossier['historique_classes'] = DB::table('class_apprenant')
                    ->join('classes', 'class_apprenant.class_id', '=', 'classes.id')
                    ->leftJoin('niveaux',  'classes.niveau_id',  '=', 'niveaux.id')
                    ->leftJoin('filieres', 'classes.filiere_id', '=', 'filieres.id')
                    ->where('class_apprenant.apprenant_id', $apprenant->id)
                    ->select(
                        'classes.name as classe',
                        'niveaux.name as niveau',
                        'filieres.name as filiere',
                        'class_apprenant.annee_academique',
                        'class_apprenant.date_inscription',
                        'class_apprenant.statut'
                    )
                    ->orderBy('class_apprenant.annee_academique')
                    ->get();
            } catch (\Exception) {
                $dossier['historique_classes'] = collect();
            }
        }

        return $dossier;
    }

    /* ══════════════════════════════════════════════════════════
     |  1. PAGE PRINCIPALE — recherche d'un apprenant
     |  GET /admin/transfer
     ══════════════════════════════════════════════════════════ */

    public function index()
    {
        $user        = Auth::user();
        $institution = $this->getInstitution();

        // Demandes envoyées par mon institution
        $demandesEnvoyees = TransferRequest::sentBy($institution->id)
            ->with(['apprenant', 'institutionSource', 'requestedBy'])
            ->latest()
            ->paginate(10, ['*'], 'envoyees');

        // Demandes reçues par mon institution (je suis la source)
        $demandesRecues = TransferRequest::receivedBy($institution->id)
            ->with(['apprenant', 'institutionDest', 'requestedBy'])
            ->latest()
            ->paginate(10, ['*'], 'recues');

        // Toutes les institutions disponibles (pour la recherche)
        $institutions = Institution::where('id', '!=', $institution->id)
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $stats = [
            'envoyees_pending'  => TransferRequest::sentBy($institution->id)->pending()->count(),
            'recues_pending'    => TransferRequest::receivedBy($institution->id)->pending()->count(),
            'approuvees'        => TransferRequest::sentBy($institution->id)->approved()->count(),
        ];

        return view('admin.indexe', compact(
            'user', 'institution',
            'demandesEnvoyees', 'demandesRecues',
            'institutions', 'stats'
        ));
    }

    /* ══════════════════════════════════════════════════════════
     |  2. RECHERCHER UN APPRENANT (AJAX)
     |  GET /admin/transfer/search
     ══════════════════════════════════════════════════════════ */

    public function search(Request $request)
    {
        $request->validate([
            'matricule'          => 'required|string|min:2',
            'institution_source' => 'nullable|exists:institutions,id',
        ]);

        $query = Apprenant::with(['institution', 'classe', 'niveau', 'filiere'])
            ->where('matricule', $request->matricule);

        // Filtrer par institution source si précisée
        if ($request->filled('institution_source')) {
            $query->where('institution_id', $request->institution_source);
        }

        // Exclure les apprenants de NOTRE propre institution
        $myInstId = $this->getInstitution()->id;
        $query->where('institution_id', '!=', $myInstId);

        $apprenants = $query->get()->map(function ($a) {
            return [
                'id'          => $a->id,
                'matricule'   => $a->matricule,
                'nom'         => $a->nom,
                'prenom'      => $a->prenom,
                'sexe'        => $a->sexe,
                'date_naissance' => $a->date_naissance,
                'institution' => $a->institution?->name,
                'institution_id' => $a->institution_id,
                'classe'      => $a->classe?->name,
                'niveau'      => $a->niveau?->name,
                'filiere'     => $a->filiere?->name,
                'annee_academique' => $a->annee_academique,
                'status'      => $a->status,
            ];
        });

        return response()->json([
            'found'      => $apprenants->count(),
            'apprenants' => $apprenants,
        ]);
    }

    /* ══════════════════════════════════════════════════════════
     |  3. CRÉER UNE DEMANDE DE CONSULTATION
     |  POST /admin/transfer/request
     ══════════════════════════════════════════════════════════ */

    public function store(Request $request)
    {
        $institution = $this->getInstitution();

        $data = $request->validate([
            'apprenant_id'   => 'required|exists:apprenants,id',
            'motif'          => 'required|string|max:500',
            'scope'          => 'required|array|min:1',
            'scope.*'        => 'in:identity,notes,bulletins,discipline,finances,classes',
        ]);

        $apprenant = Apprenant::findOrFail($data['apprenant_id']);

        // Vérifier que l'apprenant n'appartient pas à mon institution
        abort_if(
            $apprenant->institution_id === $institution->id,
            422,
            'Cet apprenant appartient déjà à votre établissement.'
        );

        // Vérifier qu'une demande pending n'existe pas déjà
        $existing = TransferRequest::where('apprenant_id', $apprenant->id)
            ->where('institution_dest_id', $institution->id)
            ->whereIn('statut', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return redirect()->back()->with(
                'error',
                'Une demande est déjà en cours pour cet apprenant.'
            );
        }

        TransferRequest::create([
            'apprenant_id'         => $apprenant->id,
            'institution_source_id'=> $apprenant->institution_id,
            'institution_dest_id'  => $institution->id,
            'requested_by'         => Auth::id(),
            'statut'               => 'pending',
            'scope'                => $data['scope'],
            'motif'                => $data['motif'],
        ]);

        return redirect()->route('admin.transfer.index')
            ->with('success', 'Demande de consultation envoyée. L\'école source doit l\'approuver.');
    }

    /* ══════════════════════════════════════════════════════════
     |  4. APPROUVER UNE DEMANDE (école source)
     |  PATCH /admin/transfer/{transfer}/approve
     ══════════════════════════════════════════════════════════ */

    public function approve(TransferRequest $transfer)
{
    $institution = $this->getInstitution();

    abort_if(
        $transfer->institution_source_id !== $institution->id,
        403,
        'Action non autorisée.'
    );
    abort_if($transfer->statut !== 'pending', 422, 'Cette demande ne peut plus être traitée.');

    // Récupérer l'apprenant
    $apprenant = $transfer->apprenant;
    $annee = $apprenant->annee_academique ?? date('Y') . '-' . (date('Y') + 1);

    // ═══════════════════════════════════════════════════
    // NOUVEAU : Effectuer le vrai transfert
    // ═══════════════════════════════════════════════════
    DB::transaction(function () use ($transfer, $apprenant, $annee) {

        // 1. Marquer l'ancienne affectation comme "transferée"
        $apprenant->classes()
            ->wherePivot('annee_academique', $annee)
            ->wherePivot('statut', 'actif')
            ->each(fn ($c) => $apprenant->classes()->updateExistingPivot(
                $c->id, ['statut' => 'transfere']
            ));

        // 2. Changer l'institution de l'apprenant
        $apprenant->update([
            'institution_id' => $transfer->institution_dest_id,
            'class_id'       => null, // pas encore affecté à une classe dans la nouvelle école
        ]);

        // 3. Changer l'institution du User lié
        if ($apprenant->user_id && $apprenant->user) {
            $apprenant->user->update([
                'institution_id' => $transfer->institution_dest_id,
            ]);
        }

        // 4. Générer le token d'accès au dossier (72h)
        $transfer->generateToken(72);

        // 5. Mettre à jour le statut de la demande
        $transfer->update([
            'statut'       => 'approved',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);
    });

    return redirect()->back()->with(
        'success',
        "Transfert approuvé. {$apprenant->prenom} {$apprenant->nom} est maintenant membre de l'établissement destinataire. Le dossier complet est accessible pendant 72h."
    );
}

    /* ══════════════════════════════════════════════════════════
     |  5. REFUSER UNE DEMANDE (école source)
     |  PATCH /admin/transfer/{transfer}/reject
     ══════════════════════════════════════════════════════════ */

    public function reject(Request $request, TransferRequest $transfer)
    {
        $institution = $this->getInstitution();

        abort_if($transfer->institution_source_id !== $institution->id, 403);
        abort_if($transfer->statut !== 'pending', 422, 'Cette demande ne peut plus être traitée.');

        $request->validate(['motif_refus' => 'required|string|max:500']);

        $transfer->update([
            'statut'       => 'rejected',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
            'motif_refus'  => $request->motif_refus,
        ]);

        return redirect()->back()
            ->with('success', 'Demande refusée.');
    }

    /* ══════════════════════════════════════════════════════════
     |  6. CONSULTER LE DOSSIER (établissement destinataire)
     |  GET /admin/transfer/{transfer}/dossier
     ══════════════════════════════════════════════════════════ */

    public function dossier(TransferRequest $transfer)
    {
        $institution = $this->getInstitution();
        $user        = Auth::user();

        // Seul le destinataire peut voir le dossier
        abort_if($transfer->institution_dest_id !== $institution->id, 403);

        // La demande doit être approuvée
        abort_if(
            ! in_array($transfer->statut, ['approved', 'completed']),
            403,
            'Ce dossier n\'est pas encore accessible ou a expiré.'
        );

        // Vérifier l'expiration du token
        if ($transfer->token_expires_at && $transfer->token_expires_at->isPast()) {
            $transfer->update(['statut' => 'completed']);
            return redirect()->route('admin.transfer.index')
                ->with('error', 'Le délai d\'accès à ce dossier a expiré.');
        }

        $apprenant = $transfer->apprenant;
        $scope     = $transfer->scope ?? ['identity'];

        // Toujours inclure l'identité
        if (! in_array('identity', $scope)) {
            $scope[] = 'identity';
        }

        $dossier = $this->buildDossier($apprenant, $scope);

        // Marquer comme complété si premier accès
        if ($transfer->statut === 'approved') {
            $transfer->update(['statut' => 'completed']);
        }

        return view('admin.dossier', compact(
            'user', 'institution', 'transfer', 'apprenant', 'dossier', 'scope'
        ));
    }

    /* ══════════════════════════════════════════════════════════
     |  7. VOIR DÉTAIL D'UNE DEMANDE REÇUE (école source)
     |  GET /admin/transfer/{transfer}/show
     ══════════════════════════════════════════════════════════ */

    public function show(TransferRequest $transfer)
    {
        $institution = $this->getInstitution();
        $user        = Auth::user();

        // Soit je suis la source, soit le destinataire
        abort_if(
            $transfer->institution_source_id !== $institution->id &&
            $transfer->institution_dest_id   !== $institution->id,
            403
        );

        $apprenant = $transfer->apprenant->load([
            'institution', 'classe', 'niveau', 'filiere'
        ]);

        return view('admin.show', compact(
            'user', 'institution', 'transfer', 'apprenant'
        ));
    }

    /* ══════════════════════════════════════════════════════════
     |  8. ANNULER UNE DEMANDE (initiateur)
     |  DELETE /admin/transfer/{transfer}
     ══════════════════════════════════════════════════════════ */

    public function destroy(TransferRequest $transfer)
    {
        $institution = $this->getInstitution();

        abort_if($transfer->institution_dest_id !== $institution->id, 403);
        abort_if($transfer->statut === 'completed', 422, 'Impossible d\'annuler une demande complétée.');

        $transfer->delete();

        return redirect()->route('admin.transfer.index')
            ->with('success', 'Demande annulée.');
    }
}