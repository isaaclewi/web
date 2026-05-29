<?php

namespace App\Http\Controllers;

use App\Models\Apprenant;
use App\Models\Bulletin;
use App\Models\Classe;
use App\Models\EmploiDuTemps;
use App\Models\Evaluation;
use App\Models\Filiere;
use App\Models\FinancialRecord;
use App\Models\Grade;
use App\Models\GradeConfig;
use App\Models\Institution;
use App\Models\LibraryBook;
use App\Models\Module;
use App\Models\Niveau;
use App\Models\ProgrammePaiement;
use App\Models\ReportCard;
use App\Models\SchoolParent;
use App\Models\SeanceCours;
use App\Models\Staff;
use App\Models\StaffTaskAssignment;
use App\Models\Subject;
use App\Models\SuiviDisciplinaire;
use App\Models\Teacher;
use App\Models\TransferRequest;
use App\Models\User;
use App\Services\BulletinCalculatorService;
use App\Services\WelcomeMailService;
use App\Traits\MatriculeGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rule;

class StaffDashboardController extends Controller
{
    use MatriculeGenerator;

    /* ══════════════════════════════════════════════════════════
     | HELPER CENTRAL
     ══════════════════════════════════════════════════════════ */
    private function getInstitution(): Institution
    {
        $institution = Auth::user()?->institution;
        if (! $institution) {
            abort(403, 'Aucun établissement lié à votre compte.');
        }

        return $institution;
    }

    private function assertBelongsToInstitution(mixed $model, int $instId, string $label = 'Ressource'): void
    {
        if ((int) $model->institution_id !== $instId) {
            abort(403, "{$label} introuvable dans votre établissement.");
        }
    }

    private function assertSameInstitution(User $authUser, User $target): void
    {
        if ((int) $target->institution_id !== (int) $authUser->institution_id) {
            abort(403, "Cet utilisateur n'appartient pas à votre établissement.");
        }
    }

    private function getStaff()
    {
        $user = Auth::user();
        $staff = $user?->staff;
        if (! $staff) {
            abort(403, 'Aucun profil staff lié à votre compte.');
        }

        return $staff;
    }

    /* ══════════════════════════════════════════════════
     | DASHBOARD PRINCIPAL
     ══════════════════════════════════════════════════ */
    public function index()
    {
        $user = Auth::user() ?? redirect()->route('login')->send();
        $staff = $this->getStaff();
        $institution = $this->getInstitution();
        $instId = $institution->id;

        // Modules actifs de ce staff
        $modulesActifs = $staff->modulesActifs();

        // Stats de base (toujours disponibles)
        $stats = [
            'apprenants' => Apprenant::where('institution_id', $instId)->count(),
            'actifs' => Apprenant::where('institution_id', $instId)->where('status', 1)->count(),
            'classes' => Classe::where('institution_id', $instId)->count(),
        ];

        // Stats conditionnelles selon les modules actifs
        $moduleKeys = $modulesActifs->pluck('key')->toArray();

        if (in_array('paiements', $moduleKeys)) {
            $annee = $institution->academic_year ?? date('Y').'-'.(date('Y') + 1);
            $stats['impaye'] = FinancialRecord::where('institution_id', $instId)
                ->where('annee_academique', $annee)
                ->where('statut', 'impaye')
                ->sum('montant_reste');
        }

        if (in_array('disciplinaire', $moduleKeys)) {
            $stats['incidents_ouverts'] = SuiviDisciplinaire::where('institution_id', $instId)
                ->where('annee_civile', date('Y'))
                ->where('statut', 'ouvert')
                ->count();
        }

        // Activités récentes liées aux modules actifs
        $activitesRecentes = [];
        if (in_array('apprenants', $moduleKeys)) {
            $activitesRecentes['apprenants'] = Apprenant::where('institution_id', $instId)
                ->latest()->take(5)->get();
        }
        if (in_array('paiements', $moduleKeys)) {
            $annee = $institution->academic_year ?? date('Y').'-'.(date('Y') + 1);
            $activitesRecentes['paiements'] = FinancialRecord::where('institution_id', $instId)
                ->where('annee_academique', $annee)
                ->whereNotNull('date_paiement')
                ->with('apprenant')
                ->orderByDesc('date_paiement')
                ->take(5)
                ->get();
        }
        if (in_array('disciplinaire', $moduleKeys)) {
            $activitesRecentes['incidents'] = SuiviDisciplinaire::where('institution_id', $instId)
                ->where('annee_civile', date('Y'))
                ->with('apprenant')
                ->orderByDesc('date_incident')
                ->take(5)
                ->get();
        }

        return view('staff.dashboard', compact(
            'user', 'staff', 'institution',
            'modulesActifs', 'stats', 'activitesRecentes'
        ));
    }

    /* ══════════════════════════════════════════════════
     | MODULE : APPRENANTS
     ══════════════════════════════════════════════════ */

    /* ══════════════════════════════════════════════════
     | MODULE : ABSENCES
     ══════════════════════════════════════════════════ */
    public function absences(Request $request)
    {
        $staff = $this->getStaff();
        $institution = $this->getInstitution();
        $this->checkModule($staff, 'absences');

        // Réutilisation du module disciplinaire avec type='absence'
        $annee = $request->get('annee', date('Y'));
        $query = SuiviDisciplinaire::where('institution_id', $institution->id)
            ->where('type', 'absence')
            ->where('annee_civile', $annee)
            ->with(['apprenant.classe', 'recordedBy'])
            ->orderByDesc('date_incident');

        if ($request->filled('classe_id')) {
            $query->whereHas('apprenant', fn ($q) => $q->where('class_id', $request->classe_id));
        }

        $absences = $query->paginate(25)->withQueryString();
        $classes = Classe::where('institution_id', $institution->id)->orderBy('name')->get();
        $apprenants = Apprenant::where('institution_id', $institution->id)
            ->with('classe:id,name')->orderBy('nom')->get();

        return view('staff.absences', compact('staff', 'institution', 'absences', 'classes', 'apprenants', 'annee'));
    }

    public function absencesStore(Request $request)
    {
        $staff = $this->getStaff();
        $institution = $this->getInstitution();
        $this->checkModule($staff, 'absences');

        $data = $request->validate([
            'apprenant_id' => 'required|exists:apprenants,id',
            'date_incident' => 'required|date|before_or_equal:today',
            'description' => 'nullable|string|max:500',
            'gravite' => 'required|in:1,2,3',
            'statut' => 'required|in:ouvert,en_suivi,clos',
        ]);

        Apprenant::where('id', $data['apprenant_id'])
            ->where('institution_id', $institution->id)
            ->firstOrFail();

        SuiviDisciplinaire::create([
            'institution_id' => $institution->id,
            'apprenant_id' => $data['apprenant_id'],
            'type' => 'absence',
            'date_incident' => $data['date_incident'],
            'description' => $data['description'] ?? null,
            'gravite' => $data['gravite'],
            'sanction' => 'aucune',
            'statut' => $data['statut'],
            'annee_civile' => date('Y', strtotime($data['date_incident'])),
            'annee_academique' => $institution->academic_year,
            'recorded_by' => Auth::id(),
            'parents_notifies' => false,
        ]);

        return redirect()->back()->with('success', 'Absence enregistrée.');
    }

    /* ══════════════════════════════════════════════════
     | MODULE : PAIEMENTS (lecture + saisie)
     ══════════════════════════════════════════════════ */
    public function paiements(Request $request)
    {
        $staff = $this->getStaff();
        $institution = $this->getInstitution();
        $this->checkModule($staff, 'paiements');

        $annee = $request->get('annee', $institution->academic_year ?? date('Y').'-'.(date('Y') + 1));
        $search = $request->get('search', '');
        $statut = $request->get('statut', '');

        $query = Apprenant::where('institution_id', $institution->id)
            ->with(['classe', 'financialRecords' => fn ($q) => $q->where('annee_academique', $annee)]);

        if ($search) {
            $query->where(fn ($q) => $q->where('nom', 'like', "%{$search}%")
                ->orWhere('prenom', 'like', "%{$search}%")
                ->orWhere('matricule', 'like', "%{$search}%")
            );
        }
        if ($statut) {
            $query->whereHas('financialRecords', fn ($q) => $q->where('annee_academique', $annee)->where('statut', $statut)
            );
        }

        $apprenants = $query->orderBy('nom')->paginate(20)->withQueryString();
        $moisLabels = FinancialRecord::moisLabels();

        return view('staff.paiements', compact('staff', 'institution', 'apprenants', 'annee', 'search', 'statut', 'moisLabels'));
    }

    public function paiementsStore(Request $request)
    {
        $staff = $this->getStaff();
        $institution = $this->getInstitution();
        $this->checkModule($staff, 'paiements');

        $data = $request->validate([
            'apprenant_id' => 'required|exists:apprenants,id',
            'annee_academique' => 'required|string|max:20',
            'mois' => 'required|integer|between:1,12',
            'montant_du' => 'required|numeric|min:0',
            'montant_paye' => 'required|numeric|min:0',
            'date_paiement' => 'nullable|date',
            'mode_paiement' => 'nullable|in:especes,virement,mobile_money,cheque,autre',
            'reference' => 'nullable|string|max:100',
        ]);

        Apprenant::where('id', $data['apprenant_id'])
            ->where('institution_id', $institution->id)
            ->firstOrFail();

        $moisLabels = FinancialRecord::moisLabels();
        $reste = max(0, $data['montant_du'] - $data['montant_paye']);
        $statut = 'impaye';
        if ($data['montant_du'] > 0 && $data['montant_paye'] >= $data['montant_du']) {
            $statut = 'paye';
        } elseif ($data['montant_paye'] > 0) {
            $statut = 'partiel';
        }

        FinancialRecord::updateOrCreate(
            ['apprenant_id' => $data['apprenant_id'], 'annee_academique' => $data['annee_academique'], 'mois' => $data['mois']],
            [
                'institution_id' => $institution->id,
                'mois_label' => $moisLabels[$data['mois']],
                'montant_du' => $data['montant_du'],
                'montant_paye' => $data['montant_paye'],
                'montant_reste' => $reste,
                'statut' => $statut,
                'date_paiement' => $data['date_paiement'] ?? null,
                'mode_paiement' => $data['mode_paiement'] ?? null,
                'reference' => $data['reference'] ?? null,
                'recorded_by' => Auth::id(),
                'recorded_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Paiement enregistré.');
    }

    /* ══════════════════════════════════════════════════
     | MODULE : DISCIPLINAIRE
     ══════════════════════════════════════════════════ */
    public function disciplinaire(Request $request)
    {
        $staff = $this->getStaff();
        $institution = $this->getInstitution();
        $this->checkModule($staff, 'disciplinaire');

        $annee = $request->get('annee', date('Y'));

        $incidents = SuiviDisciplinaire::where('institution_id', $institution->id)
            ->where('annee_civile', $annee)
            ->with(['apprenant.classe', 'recordedBy'])
            ->orderByDesc('date_incident')
            ->paginate(20)->withQueryString();

        $apprenants = Apprenant::where('institution_id', $institution->id)->with('classe:id,name')->orderBy('nom')->get();
        $typeLabels = SuiviDisciplinaire::typeLabels();
        $sanctionLabels = SuiviDisciplinaire::sanctionLabels();
        $graviteLabels = SuiviDisciplinaire::graviteLabels();

        return view('staff.disciplinaire', compact(
            'staff', 'institution', 'incidents', 'apprenants',
            'annee', 'typeLabels', 'sanctionLabels', 'graviteLabels'
        ));
    }

    /* ══════════════════════════════════════════════════
     | MODULE : PLANNING (lecture seule)
     ══════════════════════════════════════════════════ */
    public function planning(Request $request)
    {
        $staff = $this->getStaff();
        $institution = $this->getInstitution();
        $this->checkModule($staff, 'planning');

        $annee = $institution->academic_year ?? date('Y').'-'.(date('Y') + 1);
        $classes = Classe::where('institution_id', $institution->id)->orderBy('name')->get();

        return view('staff.planning', compact('staff', 'institution', 'annee', 'classes'));
    }

    /* ══════════════════════════════════════════════════
     | PROFIL DU STAFF
     ══════════════════════════════════════════════════ */
    public function profil()
    {
        $user = Auth::user();
        $staff = $this->getStaff();
        $institution = $this->getInstitution();

        $staff->load(['taskAssignments.module', 'administrativeUnit']);

        return view('staff.profil', compact('user', 'staff', 'institution'));
    }

    /* ══════════════════════════════════════════════════
     | HELPER PRIVÉ : vérifier accès module
     ══════════════════════════════════════════════════ */
    private function checkModule($staff, string $moduleKey): void
    {
        $actif = StaffTaskAssignment::where('staff_id', $staff->id)
            ->where('actif', true)
            ->whereHas('module', fn ($q) => $q->where('key', $moduleKey))
            ->exists();

        if (! $actif) {
            abort(403, "Accès au module « {$moduleKey} » non autorisé. Contactez votre directeur.");
        }
    }

    /* ══════════════════════════════════════════════════════════
     | APPRENANTS
     ══════════════════════════════════════════════════════════ */
    public function apprenants()
    {
        $user = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();
        $instId = $institution->id;

        $apprenants = Apprenant::where('institution_id', $instId)
            ->with(['niveau', 'filiere', 'classe'])->latest()->paginate(20);

        $stats = [
            'total' => Apprenant::where('institution_id', $instId)->count(),
            'active' => Apprenant::where('institution_id', $instId)->where('status', 1)->count(),
            'garcons' => Apprenant::where('institution_id', $instId)->where('sexe', 'M')->count(),
            'filles' => Apprenant::where('institution_id', $instId)->where('sexe', 'F')->count(),
        ];

        return view('staff.apprenants', compact('user', 'institution', 'apprenants', 'stats') + [
            'niveaux' => Niveau::orderBy('name')->get(),
            'filieres' => Filiere::where('institution_id', $instId)->orderBy('name')->get(),
            'classes' => Classe::where('institution_id', $instId)->orderBy('name')->get(),
        ]);
    }

    public function apprenantStore(Request $request)
    {
        $user = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();
        $instId = $institution->id;

        $data = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_naissance' => 'nullable|date',
            'sexe' => 'nullable|in:M,F',
            'matricule' => 'nullable|string|max:50',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'filiere_id' => 'nullable|exists:filieres,id',
            'class_id' => 'nullable|exists:classes,id',
            'annee_academique' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'nullable|min:8',
        ]);

        if (! empty($data['class_id'])) {
            Classe::where('id', $data['class_id'])->where('institution_id', $instId)->firstOrFail();
        }
        if (! empty($data['filiere_id'])) {
            Filiere::where('id', $data['filiere_id'])->where('institution_id', $instId)->firstOrFail();
        }

        // ✅ Matricule auto
        if (empty($data['matricule'])) {
            $data['matricule'] = $this->generateApprenantMatricule($institution, $instId);
        }

        // ✅ Mot de passe en clair AVANT Hash::make()
        $passwordRaw = $data['password'] ?? 'password123';
        $emailDest = $data['email'] ?? null;

        DB::transaction(function () use ($data, $institution, $instId, $passwordRaw) {
            $userId = null;
            if (! empty($data['email'])) {
                $newUser = User::create([
                    'name' => $data['prenom'].' '.$data['nom'],
                    'email' => $data['email'],
                    'password' => Hash::make($passwordRaw),
                    'institution_id' => $instId,
                    'status' => 1,
                ]);
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'apprenant', 'guard_name' => 'web']);
                $newUser->assignRole('apprenant');
                $userId = $newUser->id;
            }
            Apprenant::create([
                'user_id' => $userId,
                'institution_id' => $instId,
                'niveau_id' => $data['niveau_id'] ?? null,
                'filiere_id' => $data['filiere_id'] ?? null,
                'class_id' => $data['class_id'] ?? null,
                'matricule' => $data['matricule'],
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'date_naissance' => $data['date_naissance'] ?? null,
                'sexe' => $data['sexe'] ?? null,
                'annee_academique' => $data['annee_academique'] ?? $institution->academic_year,
                'status' => 1,
                'password' => Hash::make($passwordRaw),
            ]);
        });

        // ✅ Envoi APRÈS la transaction
        if ($emailDest) {
            WelcomeMailService::sendToApprenant(
                email: $emailDest,
                prenom: $data['prenom'],
                nom: $data['nom'],
                matricule: $data['matricule'],
                passwordRaw: $passwordRaw,
                institution: $institution->name,
            );
        }

        return redirect()->back()->with(
            'success',
            "Apprenant « {$data['prenom']} {$data['nom']} » créé (matricule : {$data['matricule']})."
            .($emailDest ? ' Un e-mail de bienvenue a été envoyé.' : '')
        );
    }

    public function apprenantUpdate(Request $request, Apprenant $apprenant)
    {
        $instId = $this->getInstitution()->id;
        $this->assertBelongsToInstitution($apprenant, $instId, 'Apprenant');

        $data = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_naissance' => 'nullable|date',
            'sexe' => 'nullable|in:M,F',
            'matricule' => 'nullable|string|max:50',
            'status' => 'nullable|in:0,1',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'filiere_id' => 'nullable|exists:filieres,id',
            'class_id' => 'nullable|exists:classes,id',
            'annee_academique' => 'nullable|string|max:20',
        ]);

        if (! empty($data['class_id'])) {
            Classe::where('id', $data['class_id'])->where('institution_id', $instId)->firstOrFail();
        }
        if (! empty($data['filiere_id'])) {
            Filiere::where('id', $data['filiere_id'])->where('institution_id', $instId)->firstOrFail();
        }

        $apprenant->update($data);
        if ($apprenant->user_id && $apprenant->user) {
            $apprenant->user->update(['name' => $data['prenom'].' '.$data['nom']]);
        }

        return redirect()->back()->with('success', 'Apprenant mis à jour.');
    }

    public function apprenantResetPassword(Request $request, Apprenant $apprenant)
    {
        $this->assertBelongsToInstitution($apprenant, $this->getInstitution()->id, 'Apprenant');
        $request->validate(['password' => 'required|min:8|confirmed']);
        $hashed = Hash::make($request->password);
        $apprenant->update(['password' => $hashed]);
        if ($apprenant->user_id && $apprenant->user) {
            $apprenant->user->update(['password' => $hashed]);
        }

        return redirect()->back()->with('success', "Mot de passe de « {$apprenant->prenom} {$apprenant->nom} » réinitialisé.");
    }

    public function apprenantDestroy(Apprenant $apprenant)
    {
        $this->assertBelongsToInstitution($apprenant, $this->getInstitution()->id, 'Apprenant');
        $name = $apprenant->prenom.' '.$apprenant->nom;
        DB::transaction(function () use ($apprenant) {
            if ($apprenant->user_id && $apprenant->user) {
                $apprenant->user->delete();
            }
            $apprenant->delete();
        });

        return redirect()->back()->with('success', "Apprenant « {$name} » supprimé.");
    }

    public function apprenantBulkDestroy(Request $request)
    {
        $instId = $this->getInstitution()->id;
        $ids = array_filter(explode(',', $request->input('ids', '')));
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Aucun apprenant sélectionné.');
        }

        $apprenants = Apprenant::whereIn('id', $ids)->where('institution_id', $instId)->with('user')->get();
        DB::transaction(function () use ($apprenants) {
            foreach ($apprenants as $a) {
                if ($a->user_id && $a->user) {
                    $a->user->delete();
                }
                $a->delete();
            }
        });

        return redirect()->back()->with('success', "{$apprenants->count()} apprenant(s) supprimé(s).");
    }

    public function apprenantExport(Request $request)
    {
        $instId = $this->getInstitution()->id;
        $query = Apprenant::where('institution_id', $instId)->with(['niveau', 'filiere', 'classe']);
        if ($request->filled('ids')) {
            $query->whereIn('id', array_filter(explode(',', $request->ids)));
        }
        $apprenants = $query->get();
        $headers = ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="apprenants_export_'.now()->format('Ymd_His').'.csv"'];

        return response()->stream(function () use ($apprenants) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($h, ['Matricule', 'Nom', 'Prénom', 'Sexe', 'Date naissance', 'Niveau', 'Filière', 'Classe', 'Année', 'Statut']);
            foreach ($apprenants as $a) {
                fputcsv($h, [$a->matricule, $a->nom, $a->prenom, $a->sexe, $a->date_naissance, $a->niveau->name ?? '', $a->filiere->name ?? '', $a->classe->name ?? '', $a->annee_academique, $a->status ? 'Actif' : 'Inactif']);
            }
            fclose($h);
        }, 200, $headers);
    }

    public function apprenantImport(Request $request)
    {
        $institution = $this->getInstitution();
        $instId = $institution->id;
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt,xlsx|max:2048', 'default_class_id' => 'nullable|exists:classes,id']);

        if ($request->filled('default_class_id')) {
            Classe::where('id', $request->default_class_id)->where('institution_id', $instId)->firstOrFail();
        }

        $rows = array_map('str_getcsv', file($request->file('csv_file')->getRealPath()));
        $header = array_map('trim', array_shift($rows));
        $created = $skipped = 0;

        DB::transaction(function () use ($rows, $header, $institution, $instId, $request, &$created, &$skipped) {
            foreach ($rows as $row) {
                if (count($row) < 2) {
                    $skipped++;

                    continue;
                }
                $line = array_combine($header, array_map('trim', $row));
                if (! empty($line['matricule']) && Apprenant::where('institution_id', $instId)->where('matricule', $line['matricule'])->exists()) {
                    $skipped++;

                    continue;
                }
                $matricule = $line['matricule'] ?? null;
                if (empty($matricule)) {
                    $matricule = $this->generateApprenantMatricule($institution, $instId);
                }
                Apprenant::create([
                    'institution_id' => $instId,
                    'nom' => $line['nom'] ?? '',
                    'prenom' => $line['prenom'] ?? '',
                    'date_naissance' => ! empty($line['date_naissance']) ? $line['date_naissance'] : null,
                    'sexe' => $line['sexe'] ?? null,
                    'matricule' => $matricule,
                    'niveau_id' => $line['niveau_id'] ?? null,
                    'filiere_id' => $line['filiere_id'] ?? null,
                    'class_id' => $request->default_class_id ?? ($line['class_id'] ?? null),
                    'annee_academique' => $line['annee_academique'] ?? $institution->academic_year,
                    'status' => 1,
                    'password' => Hash::make('password123'),
                ]);
                $created++;
            }
        });

        return redirect()->back()->with('success', "{$created} apprenant(s) importé(s), {$skipped} ignoré(s).");
    }

    public function DisciplinaireIndex(Request $request)
    {
        $user = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();
        $instId = $institution->id;

        $annee = $request->get('annee', date('Y'));
        $type = $request->get('type', '');
        $gravite = $request->get('gravite', '');
        $statut = $request->get('statut', '');
        $classeId = $request->get('classe_id', '');
        $search = $request->get('search', '');

        $query = SuiviDisciplinaire::where('institution_id', $instId)
            ->where('annee_civile', $annee)
            ->with(['apprenant.classe', 'recordedBy'])
            ->orderByDesc('date_incident');

        if ($type) {
            $query->where('type', $type);
        }
        if ($gravite) {
            $query->where('gravite', $gravite);
        }
        if ($statut) {
            $query->where('statut', $statut);
        }
        if ($classeId) {
            $query->whereHas('apprenant', fn ($q) => $q->where('class_id', $classeId));
        }
        if ($search) {
            $query->whereHas('apprenant', fn ($q) => $q
                ->where('nom', 'like', "%{$search}%")
                ->orWhere('prenom', 'like', "%{$search}%")
            );
        }

        $incidents = $query->paginate(20)->withQueryString();

        // Stats
        $stats = SuiviDisciplinaire::where('institution_id', $instId)
            ->where('annee_civile', $annee)
            ->selectRaw('
                COUNT(*) as total,
                COUNT(CASE WHEN gravite = 1 THEN 1 END) as mineurs,
                COUNT(CASE WHEN gravite = 2 THEN 1 END) as moderes,
                COUNT(CASE WHEN gravite = 3 THEN 1 END) as graves,
                COUNT(CASE WHEN statut  = "ouvert"   THEN 1 END) as ouverts,
                COUNT(CASE WHEN statut  = "en_suivi" THEN 1 END) as en_suivi,
                COUNT(CASE WHEN statut  = "clos"     THEN 1 END) as clos,
                COUNT(CASE WHEN parents_notifies = 1 THEN 1 END) as notifies
            ')
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
        if (! $anneesDispos->contains($annee)) {
            $anneesDispos->prepend($annee);
        }

        // Données formulaire
        $classes = Classe::where('institution_id', $instId)->orderBy('name')->get();
        $apprenants = Apprenant::where('institution_id', $instId)
            ->with('classe:id,name')->orderBy('nom')->get();
        $typeLabels = SuiviDisciplinaire::typeLabels();
        $sanctionLabels = SuiviDisciplinaire::sanctionLabels();
        $graviteLabels = SuiviDisciplinaire::graviteLabels();

        return view('staff.disciplinaire', compact(
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
    public function DisciplinaireApprenant(Apprenant $apprenant, Request $request)
    {
        $user = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();

        // 🔒 Sécurité institution
        if ((int) $apprenant->institution_id !== $institution->id) {
            abort(403);
        }

        $annee = $request->get('annee', date('Y'));

        $incidents = SuiviDisciplinaire::where('apprenant_id', $apprenant->id)
            ->where('annee_civile', $annee)
            ->with('recordedBy')
            ->orderByDesc('date_incident')
            ->get();

        $statsApprenant = [
            'total' => $incidents->count(),
            'graves' => $incidents->where('gravite', 3)->count(),
            'ouverts' => $incidents->where('statut', 'ouvert')->count(),
            'notifies' => $incidents->where('parents_notifies', true)->count(),
        ];

        // Toutes les années disponibles pour cet apprenant
        $anneesDispos = SuiviDisciplinaire::where('apprenant_id', $apprenant->id)
            ->distinct()->pluck('annee_civile')->sortDesc()->values();
        if (! $anneesDispos->contains($annee)) {
            $anneesDispos->prepend($annee);
        }

        $typeLabels = SuiviDisciplinaire::typeLabels();
        $sanctionLabels = SuiviDisciplinaire::sanctionLabels();
        $graviteLabels = SuiviDisciplinaire::graviteLabels();

        return view('staff.disciplinaire', compact(
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
    public function DisciplinaireStore(Request $request)
    {
        $institution = $this->getInstitution();
        $instId = $institution->id;

        $data = $request->validate([
            'apprenant_id' => 'required|exists:apprenants,id',
            'date_incident' => 'required|date|before_or_equal:today',
            'type' => 'required|in:absence,retard,insolence,violence,triche,perturbation,tenue,autre',
            'gravite' => 'required|integer|in:1,2,3',
            'description' => 'nullable|string|max:1000',
            'sanction' => 'required|in:aucune,avertissement,blame,exclusion_cours,exclusion_temp,exclusion_def,convocation_parents,travail_supplementaire,autre',
            'sanction_detail' => 'nullable|string|max:500',
            'parents_notifies' => 'nullable|boolean',
            'date_notification' => 'nullable|date',
            'observations' => 'nullable|string|max:1000',
            'statut' => 'required|in:ouvert,en_suivi,clos',
        ]);

        // 🔒 L'apprenant doit appartenir à l'institution
        Apprenant::where('id', $data['apprenant_id'])
            ->where('institution_id', $instId)
            ->firstOrFail();

        $annee = date('Y', strtotime($data['date_incident']));

        SuiviDisciplinaire::create(array_merge($data, [
            'institution_id' => $instId,
            'recorded_by' => Auth::id(),
            'annee_civile' => $annee,
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
    public function DisciplinaireUpdate(Request $request, SuiviDisciplinaire $disciplinaire)
    {
        $institution = $this->getInstitution();

        // 🔒 Sécurité
        if ((int) $disciplinaire->institution_id !== $institution->id) {
            abort(403);
        }

        $data = $request->validate([
            'date_incident' => 'required|date|before_or_equal:today',
            'type' => 'required|in:absence,retard,insolence,violence,triche,perturbation,tenue,autre',
            'gravite' => 'required|integer|in:1,2,3',
            'description' => 'nullable|string|max:1000',
            'sanction' => 'required|in:aucune,avertissement,blame,exclusion_cours,exclusion_temp,exclusion_def,convocation_parents,travail_supplementaire,autre',
            'sanction_detail' => 'nullable|string|max:500',
            'sanction_executee' => 'nullable|boolean',
            'sanction_date_execution' => 'nullable|date',
            'parents_notifies' => 'nullable|boolean',
            'date_notification' => 'nullable|date',
            'observations' => 'nullable|string|max:1000',
            'statut' => 'required|in:ouvert,en_suivi,clos',
        ]);

        $disciplinaire->update(array_merge($data, [
            'sanction_executee' => (bool) ($data['sanction_executee'] ?? false),
            'parents_notifies' => (bool) ($data['parents_notifies'] ?? false),
        ]));

        return redirect()->back()->with('success', 'Incident mis à jour.');
    }

    /* ════════════════════════════════════════════════════
     | SUPPRIMER UN INCIDENT
     | DELETE /admin/disciplinaire/{incident}
     ════════════════════════════════════════════════════ */
    public function DisciplinaireDestroy(SuiviDisciplinaire $disciplinaire)
    {
        $institution = $this->getInstitution();
        if ((int) $disciplinaire->institution_id !== $institution->id) {
            abort(403);
        }

        $disciplinaire->delete();

        return redirect()->back()->with('success', 'Incident supprimé.');
    }

    public function export(Request $request)
    {
        $instId = $this->getInstitution()->id;
        $annee = $request->get('annee', date('Y'));

        $incidents = SuiviDisciplinaire::where('institution_id', $instId)
            ->where('annee_civile', $annee)
            ->with(['apprenant.classe', 'recordedBy'])
            ->orderByDesc('date_incident')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="disciplinaire_'.$annee.'_'.now()->format('Ymd').'.csv"',
        ];

        $typeLabels = SuiviDisciplinaire::typeLabels();
        $sanctionLabels = SuiviDisciplinaire::sanctionLabels();
        $graviteLabels = SuiviDisciplinaire::graviteLabels();

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
                    ($i->apprenant?->prenom ?? '').' '.($i->apprenant?->nom ?? ''),
                    $i->apprenant?->classe?->name ?? '',
                    $typeLabels[$i->type] ?? $i->type,
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

    /* ══════════════════════════════════════════════════════════
    | ENSEIGNANTS
    ══════════════════════════════════════════════════════════ */
    public function teachers()
    {
        $user = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();
        $instId = $institution->id;

        $teachers = Teacher::where('institution_id', $instId)
            ->with(['user', 'classes', 'niveaux', 'filieres'])->latest()->paginate(20);
        $stats = [
            'total' => Teacher::where('institution_id', $instId)->count(),
            'active' => Teacher::where('institution_id', $instId)->where('status', 1)->count(),
            'hommes' => Teacher::where('institution_id', $instId)->where('sexe', 'M')->count(),
            'femmes' => Teacher::where('institution_id', $instId)->where('sexe', 'F')->count(),
            'cdi' => Teacher::where('institution_id', $instId)->where('type_contrat', 'CDI')->count(),
            'vacataire' => Teacher::where('institution_id', $instId)->where('type_contrat', 'vacataire')->count(),
        ];

        return view('staff.enseignants', compact('user', 'institution', 'teachers', 'stats') + [
            'niveaux' => Niveau::orderBy('name')->get(),
            'filieres' => Filiere::where('institution_id', $instId)->orderBy('name')->get(),
            'classes' => Classe::where('institution_id', $instId)->orderBy('name')->get(),
        ]);
    }

    public function teacherStore(Request $request)
    {
        $institution = $this->getInstitution();
        $instId = $institution->id;

        $data = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'matricule' => 'nullable|string|max:50',
            'sexe' => 'nullable|in:M,F',
            'telephone' => 'nullable|string|max:30',
            'specialite' => 'nullable|string|max:100',
            'type_contrat' => 'nullable|in:CDI,CDD,vacataire,benevole',
            'date_recrutement' => 'nullable|date',
            'niveaux' => 'nullable|array', 'niveaux.*' => 'exists:niveaux,id',
            'filieres' => 'nullable|array', 'filieres.*' => 'exists:filieres,id',
            'classes' => 'nullable|array', 'classes.*' => 'exists:classes,id',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'nullable|min:8|confirmed',
        ]);

        if (! empty($data['classes'])) {
            $count = Classe::whereIn('id', $data['classes'])->where('institution_id', $instId)->count();
            if ($count !== count($data['classes'])) {
                abort(403, 'Certaines classes n\'appartiennent pas à votre établissement.');
            }
        }
        if (! empty($data['filieres'])) {
            $count = Filiere::whereIn('id', $data['filieres'])->where('institution_id', $instId)->count();
            if ($count !== count($data['filieres'])) {
                abort(403, 'Certaines filières n\'appartiennent pas à votre établissement.');
            }
        }

        // ✅ Matricule auto
        if (empty($data['matricule'])) {
            $data['matricule'] = $this->generateTeacherMatricule($institution, $instId);
        }

        // ✅ Mot de passe en clair AVANT Hash::make()
        $passwordRaw = $data['password'] ?? 'password123';
        $emailDest = $data['email'] ?? null;

        DB::transaction(function () use ($data, $instId, $passwordRaw) {
            $userId = null;
            $userEmail = $data['email'] ?? null;
            if ($userEmail) {
                $newUser = User::create([
                    'name' => $data['prenom'].' '.$data['nom'],
                    'email' => $userEmail,
                    'password' => Hash::make($passwordRaw),
                    'institution_id' => $instId,
                    'status' => 1,
                ]);
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
                $newUser->assignRole('teacher');
                $userId = $newUser->id;
            }
            $teacher = Teacher::create([
                'user_id' => $userId,
                'institution_id' => $instId,
                'matricule' => $data['matricule'],
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'sexe' => $data['sexe'] ?? null,
                'telephone' => $data['telephone'] ?? null,
                'email' => $userEmail,
                'specialite' => $data['specialite'] ?? null,
                'type_contrat' => $data['type_contrat'] ?? null,
                'date_recrutement' => $data['date_recrutement'] ?? null,
                'status' => 1,
            ]);
            if (! empty($data['niveaux'])) {
                $teacher->niveaux()->sync($data['niveaux']);
            }
            if (! empty($data['filieres'])) {
                $teacher->filieres()->sync($data['filieres']);
            }
            if (! empty($data['classes'])) {
                $teacher->classes()->sync($data['classes']);
            }
        });

        // ✅ Envoi APRÈS la transaction
        if ($emailDest) {
            WelcomeMailService::sendToTeacher(
                email: $emailDest,
                prenom: $data['prenom'],
                nom: $data['nom'],
                matricule: $data['matricule'],
                passwordRaw: $passwordRaw,
                institution: $institution->name,
            );
        }

        return redirect()->back()->with(
            'success',
            "Enseignant « {$data['prenom']} {$data['nom']} » créé (matricule : {$data['matricule']})."
            .($emailDest ? ' Un e-mail de bienvenue a été envoyé.' : '')
        );
    }

    public function teacherUpdate(Request $request, Teacher $teacher)
    {
        $instId = $this->getInstitution()->id;
        $this->assertBelongsToInstitution($teacher, $instId, 'Enseignant');

        $data = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'matricule' => 'nullable|string|max:50',
            'sexe' => 'nullable|in:M,F',
            'telephone' => 'nullable|string|max:30',
            'specialite' => 'nullable|string|max:100',
            'type_contrat' => 'nullable|in:CDI,CDD,vacataire,benevole',
            'date_recrutement' => 'nullable|date',
            'status' => 'nullable|in:0,1',
            'niveaux' => 'nullable|array', 'niveaux.*' => 'exists:niveaux,id',
            'filieres' => 'nullable|array', 'filieres.*' => 'exists:filieres,id',
            'classes' => 'nullable|array', 'classes.*' => 'exists:classes,id',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,'.($teacher->user_id ?? 'NULL'),
        ]);

        if (! empty($data['classes'])) {
            $count = Classe::whereIn('id', $data['classes'])->where('institution_id', $instId)->count();
            if ($count !== count($data['classes'])) {
                abort(403, 'Certaines classes n\'appartiennent pas à votre établissement.');
            }
        }

        $teacher->update([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'matricule' => $data['matricule'] ?? $teacher->matricule,
            'sexe' => $data['sexe'] ?? $teacher->sexe,
            'telephone' => $data['telephone'] ?? null,
            'specialite' => $data['specialite'] ?? null,
            'type_contrat' => $data['type_contrat'] ?? null,
            'date_recrutement' => $data['date_recrutement'] ?? null,
            'status' => $data['status'] ?? $teacher->status,
        ]);
        $teacher->niveaux()->sync($data['niveaux'] ?? []);
        $teacher->filieres()->sync($data['filieres'] ?? []);
        $teacher->classes()->sync($data['classes'] ?? []);

        if ($teacher->user_id && $teacher->user) {
            $update = [];
            if (! empty($data['name'])) {
                $update['name'] = $data['name'];
            }
            if (! empty($data['email'])) {
                $update['email'] = $data['email'];
            }
            if (! empty($update)) {
                $teacher->user->update($update);
            }
        }

        return redirect()->back()->with('success', "Enseignant « {$data['prenom']} {$data['nom']} » mis à jour.");
    }

    public function teacherResetPassword(Request $request, Teacher $teacher)
    {
        $this->assertBelongsToInstitution($teacher, $this->getInstitution()->id, 'Enseignant');
        if (! $teacher->user_id) {
            return redirect()->back()->with('error', "Cet enseignant n'a pas de compte utilisateur associé.");
        }
        $request->validate(['password' => 'required|min:8|confirmed']);
        $teacher->user->update(['password' => Hash::make($request->password)]);

        return redirect()->back()->with('success', "Mot de passe de « {$teacher->prenom} {$teacher->nom} » réinitialisé.");
    }

    public function teacherToggleStatus(Request $request, Teacher $teacher)
    {
        $this->assertBelongsToInstitution($teacher, $this->getInstitution()->id, 'Enseignant');
        if (! $teacher->user_id) {
            return redirect()->back()->with('error', "Cet enseignant n'a pas de compte utilisateur associé.");
        }
        $request->validate(['status' => 'required|in:active,inactive,blocked']);
        $teacher->user->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Statut du compte mis à jour.');
    }

    public function teacherDestroy(Teacher $teacher)
    {
        $this->assertBelongsToInstitution($teacher, $this->getInstitution()->id, 'Enseignant');
        $name = $teacher->prenom.' '.$teacher->nom;
        DB::transaction(function () use ($teacher) {
            $teacher->niveaux()->detach();
            $teacher->filieres()->detach();
            $teacher->classes()->detach();
            if ($teacher->user_id && $teacher->user) {
                $teacher->user->delete();
            }
            $teacher->delete();
        });

        return redirect()->back()->with('success', "Enseignant « {$name} » supprimé.");
    }

    /* ══════════════════════════════════════════════════════════
     | STAFF
     ══════════════════════════════════════════════════════════ */
    public function staff()
    {
        $user = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();
        $instId = $institution->id;

        $staffMembers = \App\Models\Staff::where('institution_id', $instId)
            ->with(['user', 'administrativeUnit'])->latest()->paginate(20);
        $stats = [
            'total' => \App\Models\Staff::where('institution_id', $instId)->count(),
            'actifs' => \App\Models\Staff::where('institution_id', $instId)->where('status', 1)->count(),
            'inactifs' => \App\Models\Staff::where('institution_id', $instId)->where('status', 0)->count(),
        ];
        $administrativeUnits = \App\Models\AdministrativeUnit::where('institution_id', $instId)->orderBy('name')->get();

        return view('staff.inscriptions', compact('user', 'institution', 'staffMembers', 'stats', 'administrativeUnits'));
    }

    public function staffStore(Request $request)
    {
        $institution = $this->getInstitution();
        $instId = $institution->id;

        $data = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'matricule' => 'nullable|string|max:50|unique:staff,matricule',
            'telephone' => 'nullable|string|max:30',
            'poste' => 'nullable|string|max:100',
            'administrative_unit_id' => 'nullable|exists:administrative_units,id',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'nullable|min:8|confirmed',
        ]);

        // ✅ Matricule auto
        if (empty($data['matricule'])) {
            $data['matricule'] = $this->generateStaffMatricule($institution, $instId);
        }

        // ✅ Mot de passe en clair AVANT Hash::make()
        $passwordRaw = $data['password'] ?? 'password123';
        $emailDest = $data['email'] ?? null;
        $staffRecord = null;

        DB::transaction(function () use ($data, $instId, $passwordRaw, &$staffRecord) {
            $unitId = $data['administrative_unit_id'] ?? null;
            if ($unitId) {
                \App\Models\AdministrativeUnit::where('id', $unitId)->where('institution_id', $instId)->firstOrFail();
            } else {
                $unitId = \App\Models\AdministrativeUnit::firstOrCreate(
                    ['institution_id' => $instId, 'type' => 'direction'],
                    ['name' => 'Direction', 'status' => 1]
                )->id;
            }

            $userId = null;
            if (! empty($data['email'])) {
                $newUser = User::create([
                    'name' => $data['prenom'].' '.$data['nom'],
                    'email' => $data['email'],
                    'password' => Hash::make($passwordRaw),
                    'institution_id' => $instId,
                    'status' => 1,
                ]);
                $userId = $newUser->id;
            }

            $staffRecord = \App\Models\Staff::create([
                'user_id' => $userId,
                'institution_id' => $instId,
                'administrative_unit_id' => $unitId,
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'matricule' => $data['matricule'],
                'telephone' => $data['telephone'] ?? null,
                'poste' => $data['poste'] ?? null,
                'email' => $data['email'] ?? null,
                'status' => 1,
            ]);
        });

        // ✅ Envoi APRÈS la transaction
        if ($emailDest) {
            WelcomeMailService::sendToStaff(
                email: $emailDest,
                prenom: $data['prenom'],
                nom: $data['nom'],
                matricule: $data['matricule'],
                passwordRaw: $passwordRaw,
                institution: $institution->name,
                role: $data['poste'] ?? 'Personnel administratif',
            );
        }

        return redirect()->back()->with(
            'success',
            "Membre « {$data['prenom']} {$data['nom']} » ajouté (matricule : {$data['matricule']})."
            .($emailDest ? ' Un e-mail de bienvenue a été envoyé.' : '')
        );
    }

    public function staffUpdate(Request $request, \App\Models\Staff $staff)
    {
        $this->assertBelongsToInstitution($staff, $this->getInstitution()->id, 'Staff');
        $data = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'matricule' => 'nullable|string|max:50|unique:staff,matricule,'.$staff->id,
            'telephone' => 'nullable|string|max:30',
            'poste' => 'nullable|string|max:100',
            'administrative_unit_id' => 'nullable|exists:administrative_units,id',
            'email' => 'nullable|email|unique:users,email,'.($staff->user_id ?? 'NULL'),
        ]);
        $staff->update([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'matricule' => $data['matricule'] ?? $staff->matricule,
            'telephone' => $data['telephone'] ?? null,
            'poste' => $data['poste'] ?? null,
            'administrative_unit_id' => $data['administrative_unit_id'] ?? $staff->administrative_unit_id,
            'email' => $data['email'] ?? $staff->email,
        ]);
        if ($staff->user_id && $staff->user) {
            $update = ['name' => $data['prenom'].' '.$data['nom']];
            if (! empty($data['email'])) {
                $update['email'] = $data['email'];
            }
            $staff->user->update($update);
        }

        return redirect()->back()->with('success', "Membre « {$data['prenom']} {$data['nom']} » mis à jour.");
    }

    public function staffResetPassword(Request $request, \App\Models\Staff $staff)
    {
        $this->assertBelongsToInstitution($staff, $this->getInstitution()->id, 'Staff');
        if (! $staff->user_id) {
            return redirect()->back()->with('error', "Ce membre n'a pas de compte utilisateur associé.");
        }
        $request->validate(['password' => 'required|min:8|confirmed']);
        $staff->user->update(['password' => Hash::make($request->password)]);

        return redirect()->back()->with('success', "Mot de passe de « {$staff->prenom} {$staff->nom} » réinitialisé.");
    }

    public function staffToggleStatus(Request $request, \App\Models\Staff $staff)
    {
        $this->assertBelongsToInstitution($staff, $this->getInstitution()->id, 'Staff');
        $request->validate(['status' => 'required|in:1,0']);
        $staff->update(['status' => (bool) $request->status]);
        if ($staff->user_id && $staff->user) {
            $staff->user->update(['status' => (int) $request->status]);
        }

        return redirect()->back()->with('success', 'Statut mis à jour.');
    }

    public function staffDestroy(\App\Models\Staff $staff)
    {
        $this->assertBelongsToInstitution($staff, $this->getInstitution()->id, 'Staff');
        $name = $staff->prenom.' '.$staff->nom;
        DB::transaction(function () use ($staff) {
            if ($staff->user_id && $staff->user) {
                $staff->user->delete();
            }
            $staff->delete();
        });

        return redirect()->back()->with('success', "Membre « {$name} » supprimé.");
    }

    /** Retourne l'institution_id de l'utilisateur courant (null = superadmin). */
    private function currentInstitutionId(): ?int
    {
        return Auth::user()->institution_id ?? null;
    }

    /** Extension → type normalisé */
    private function resolveFileType(string $extension): string
    {
        return match (strtolower($extension)) {
            'pdf' => 'pdf',
            'doc', 'docx' => 'docx',
            'ppt', 'pptx' => 'pptx',
            'xls', 'xlsx' => 'xlsx',
            'epub' => 'epub',
            default => 'other',
        };
    }

    /** Règles de validation communes pour le formulaire livre */
    private function bookRules(bool $isUpdate = false): array
    {
        $fileRule = $isUpdate
            ? ['nullable', File::types(['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'epub'])->max(50 * 1024)]
            : ['required', File::types(['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'epub'])->max(50 * 1024)];

        return [
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:2000',
            'category' => 'nullable|string|max:100',
            'level' => 'nullable|string|max:100',
            'language' => 'nullable|string|max:10',
            'allow_download' => 'boolean',
            'is_published' => 'boolean',
            'cover' => ['nullable', File::types(['jpg', 'jpeg', 'png', 'webp'])->max(2 * 1024)],
            'file' => $fileRule,
        ];
    }

    /** Upload du fichier et de la couverture. Retourne les paths. */
    private function handleUploads(Request $request, ?LibraryBook $existing = null): array
{
    $data = [];

    if ($request->hasFile('file')) {
        if ($existing && $existing->file_path) {
            Storage::disk('root_storage')->delete($existing->file_path);
        }
        $file = $request->file('file');
        $ext  = $file->getClientOriginalExtension();
        $path = $file->storeAs(
            'library/books',
            Str::slug($request->title).'_'.time().'.'.$ext,
            'root_storage'
        );
        $data['file_path'] = $path;
        $data['file_type'] = $this->resolveFileType($ext);
        $data['file_size'] = $file->getSize();
    }

    if ($request->hasFile('cover')) {
        if ($existing && $existing->cover_path) {
            Storage::disk('root_storage')->delete($existing->cover_path);
        }
        $cover = $request->file('cover');
        $data['cover_path'] = $cover->storeAs(
            'library/covers',
            Str::slug($request->title).'_cover_'.time().'.'.$cover->getClientOriginalExtension(),
            'root_storage'
        );
    }

    return $data;
}


    /** GET /admin/library */
    public function adminIndex(Request $request)
    {
        $institutionId = $this->currentInstitutionId();
        $institution = Auth::user()->institution;

        $myBooks = LibraryBook::with('uploader')
            ->forInstitution($institutionId);

        $this->applyFilters($myBooks, $request);

        $myBooks = $myBooks->latest()->paginate(12)->withQueryString();
        $categories = LibraryBook::categories();
        $stats = $this->institutionStats($institutionId);

        return view('staff.library', compact(
            'myBooks', 'categories', 'stats', 'institution'
        ));
    }

    /** POST /admin/library */
    public function adminStore(Request $request)
    {
        $validated = $request->validate($this->bookRules());
        $uploads = $this->handleUploads($request);

        LibraryBook::create(array_merge($validated, $uploads, [
            'institution_id' => $this->currentInstitutionId(),
            'uploaded_by' => Auth::id(),
            'uploader_role' => 'directeur',
            'allow_download' => $request->boolean('allow_download', true),
            'is_published' => $request->boolean('is_published', true),
        ]));

        return back()->with('success', 'Livre ajouté à la bibliothèque de votre établissement.');
    }

    /** PUT /admin/library/{book} */
    public function adminUpdate(Request $request, LibraryBook $book)
    {
        abort_if($book->institution_id !== $this->currentInstitutionId(), 403);

        $validated = $request->validate($this->bookRules(true));
        $uploads = $this->handleUploads($request, $book);

        $book->update(array_merge($validated, $uploads, [
            'allow_download' => $request->boolean('allow_download', true),
            'is_published' => $request->boolean('is_published', true),
        ]));

        return back()->with('success', 'Livre mis à jour.');
    }

    /** DELETE /admin/library/{book} */
    public function adminDestroy(LibraryBook $book)
{
    abort_if($book->institution_id !== $this->currentInstitutionId(), 403);
    Storage::disk('root_storage')->delete([$book->file_path, $book->cover_path]);
    $book->delete();

    return back()->with('success', 'Livre supprimé.');
}

    public function read(LibraryBook $book)
    {
        $institutionId = $this->currentInstitutionId();
        $this->authorizeView($book, $institutionId);

        $book->incrementViews();

        return view('read', compact('book'));
    }

    /**
     * Téléchargement du fichier.
     * GET /library/{book}/download
     */
    public function download(LibraryBook $book)
{
    $institutionId = $this->currentInstitutionId();
    $this->authorizeView($book, $institutionId);

    if (! $book->allow_download) {
        abort(403, 'Le téléchargement de ce document n\'est pas autorisé.');
    }

    abort_unless(Storage::disk('root_storage')->exists($book->file_path), 404);

    $book->incrementDownloads();

    return Storage::disk('root_storage')->download(
        $book->file_path,
        Str::slug($book->title).'.'.pathinfo($book->file_path, PATHINFO_EXTENSION)
    );
}

    /* ══════════════════════════════════════════════════════
     |  HELPERS INTERNES
    ══════════════════════════════════════════════════════ */

    /** Vérifie qu'un utilisateur peut voir ce livre. */
    private function authorizeView(LibraryBook $book, ?int $institutionId): void
    {
        $canSee = $book->is_published && (
            is_null($book->institution_id) ||
            $book->institution_id === $institutionId
        );
        abort_if(! $canSee, 403);
    }

    /** Applique les filtres communs (search, category, type, level). */
    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn ($sub) => $sub->where('title', 'like', "%{$q}%")
                ->orWhere('author', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%")
            );
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('file_type')) {
            $query->where('file_type', $request->file_type);
        }
        if ($request->filled('level')) {
            $query->where('level', 'like', "%{$request->level}%");
        }
        if ($request->filled('uploader_role')) {
            $query->where('uploader_role', $request->uploader_role);
        }
    }

    private function institutionStats(int $institutionId): array
    {
        $base = LibraryBook::forInstitution($institutionId);

        return [
            'total' => (clone $base)->count(),
            'published' => (clone $base)->where('is_published', true)->count(),
            'hidden' => (clone $base)->where('is_published', false)->count(),
            'views' => (clone $base)->sum('views'),
            'downloads' => (clone $base)->sum('downloads'),
        ];
    }

    private function getOrCreateConfig(Institution $institution): GradeConfig
    {
        return GradeConfig::firstOrCreate(
            [
                'institution_id' => $institution->id,
                'annee_academique' => $institution->academic_year ?? date('Y').'-'.(date('Y') + 1),
            ],
            [
                'note_max' => 20,
                'note_passage' => 10,
                'pct_devoirs' => 40,
                'pct_examen' => 60,
                'decimales' => 2,
                'mentions' => GradeConfig::defaultMentions(),
                'type_periodes' => 'trimestres',
                'nb_periodes' => 3,
            ]
        );
    }

    /* ══════════════════════════════════════════════════════════
     | CONFIG DE NOTATION — affichage + modification
     ══════════════════════════════════════════════════════════ */

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
    public function BulletinIndex(Request $request)
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

    // ✅ FIX BUG 2 : double source d'évaluations
    $directIds    = Evaluation::where('institution_id', $institution->id)->pluck('id');
    $viaSubjectIds = Evaluation::whereHas(
        'subject', fn($q) => $q->where('institution_id', $institution->id)
    )->pluck('id');
    $allEvalIds = $directIds->merge($viaSubjectIds)->unique()->values();

    $evaluations = Evaluation::whereIn('id', $allEvalIds)
        ->with(['subject.classe', 'grades'])
        ->orderByDesc('date')
        ->get();

    $selectedEval   = null;
    $evalApprenants = collect();

    if ($request->filled('evaluation_id')) {
        $selectedEval = Evaluation::whereIn('id', $allEvalIds)
            ->with(['subject.classe', 'grades.apprenant'])
            ->find($request->evaluation_id);

        if ($selectedEval) {
            $classeId = $selectedEval->subject->class_id ?? null;
            $evalApprenants = $classeId
                ? Apprenant::where('class_id', $classeId)
                    ->where('institution_id', $institution->id)
                    ->orderBy('nom')->orderBy('prenom')
                    ->get()
                : collect();
        }
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

    // ── Notes récentes (double source) ──
    $notesRecentes = Grade::join('evaluations', 'grades.evaluation_id', '=', 'evaluations.id')
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

    // ── Bulletins filtrés ──
    $periode  = $request->get('periode', $periodes[0]['key'] ?? 'trimestre1');
    $classeId = $request->get('classe_id');
    $publie   = $request->get('publie');

    $bulletinQuery = Bulletin::where('institution_id', $institution->id)
        ->where('annee_academique', $config->annee_academique)
        ->where('periode', $periode)
        ->with(['apprenant', 'classe']);

    if ($classeId)        $bulletinQuery->where('classe_id', $classeId);
    if ($publie !== null) $bulletinQuery->where('publie', (bool) $publie);

    $bulletins = $bulletinQuery->orderBy('rang')->paginate(30)->withQueryString();

    return view('staff.notes', compact(
        'institution', 'config', 'classes', 'subjects', 'periodes',
        'evaluations', 'selectedEval', 'evalApprenants',
        'statsParPeriode', 'notesRecentes', 'bulletins', 'periode', 'classeId', 'publie'
    ));
}

    /** Bulletin détaillé d'un apprenant — vue admin */
    public function bulletinShow(Bulletin $bulletin)
    {
        $this->verifierInstitution($bulletin);

        $config = $this->getOrCreateConfig($bulletin->institution);

        // 🔥 Récupérer toutes les évaluations de la classe + période
        $evaluations = Evaluation::with(['subject.teacher', 'grades'])
            ->whereHas('subject', function ($q) use ($bulletin) {
                $q->where('class_id', $bulletin->classe_id);
            })
            ->get();

        // 🔥 Regrouper par matière
        $matieres = [];

        foreach ($evaluations as $eval) {
            $subject = $eval->subject;
            if (! $subject) {
                continue;
            }

            $key = $subject->id;

            if (! isset($matieres[$key])) {
                $matieres[$key] = [
                    'matiere' => $subject->name,
                    'coef' => $subject->coefficient,
                    'teacher' => $subject->teacher
    ? $subject->teacher->prenom.' '.$subject->teacher->nom
    : '—',
                    'notes' => [],
                    'moyenne' => null,
                ];
            }

            // récupérer la note de cet apprenant
            $grade = $eval->grades->firstWhere('apprenant_id', $bulletin->apprenant_id);

            if ($grade) {
                $matieres[$key]['notes'][] = [
                    'evaluation' => $eval->title,
                    'score' => $grade->score,
                    'max' => $eval->max_score,
                    'type' => $eval->type,
                ];
            }
        }

        // 🔥 Calcul des moyennes par matière
        foreach ($matieres as &$m) {
            if (count($m['notes'])) {
                $total = collect($m['notes'])->avg(function ($n) {
                    return ($n['score'] / $n['max']) * 20;
                });

                $m['moyenne'] = round($total, 2);
            }
        }

        return view('staff.notes_detail', [
            'bulletin' => $bulletin,
            'config' => $config,
            'matieres' => $matieres,
        ]);
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

    private function listePeriodes(GradeConfig $config): array
    {
        $periodes = [];
        for ($i = 1; $i <= $config->nb_periodes; $i++) {
            $key = $config->type_periodes === 'trimestres' ? "trimestre{$i}" : "semestre{$i}";
            $label = $config->type_periodes === 'trimestres' ? "{$i}er Trimestre" : "{$i}er Semestre";
            if ($i === 1) {
                $label = $config->type_periodes === 'trimestres' ? '1er Trimestre' : '1er Semestre';
            }
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
     | FINANCES
     ══════════════════════════════════════════════════════════ */
    public function financial()
    {
        $user = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();
        $instId = $institution->id;

        $annee = request('annee', $institution->academic_year ?? date('Y').'-'.(date('Y') + 1));
        $search = request('search', '');
        $statut = request('statut', '');
        $classeId = request('classe_id', '');
        $niveauId = request('niveau_id', '');
        $filiereId = request('filiere_id', '');

        $query = Apprenant::where('institution_id', $instId)
            ->with(['classe', 'filiere', 'niveau', 'financialRecords' => fn ($q) => $q->where('annee_academique', $annee)]);

        if ($search) {
            $query->where(fn ($q) => $q->where('nom', 'like', "%{$search}%")->orWhere('prenom', 'like', "%{$search}%")->orWhere('matricule', 'like', "%{$search}%"));
        }
        if ($classeId) {
            $query->where('class_id', $classeId);
        }
        if ($niveauId) {
            $query->where('niveau_id', $niveauId);
        }
        if ($filiereId) {
            $query->where('filiere_id', $filiereId);
        }
        if ($statut) {
            $query->whereHas('financialRecords', fn ($q) => $q->where('annee_academique', $annee)->where('statut', $statut));
        }

        $apprenants = $query->orderBy('nom')->paginate(25)->withQueryString();
        $statsAnnee = FinancialRecord::where('institution_id', $instId)->where('annee_academique', $annee)
            ->selectRaw('COALESCE(SUM(montant_du),0) as total_du, COALESCE(SUM(montant_paye),0) as total_paye, COALESCE(SUM(montant_reste),0) as total_reste, COUNT(CASE WHEN statut="paye" THEN 1 END) as nb_payes, COUNT(CASE WHEN statut="partiel" THEN 1 END) as nb_partiels, COUNT(CASE WHEN statut="impaye" THEN 1 END) as nb_impayes')
            ->first();
        $statsMois = FinancialRecord::where('institution_id', $instId)->where('annee_academique', $annee)
            ->selectRaw('mois, mois_label, SUM(montant_du) as du, SUM(montant_paye) as paye')->groupBy('mois', 'mois_label')->orderBy('mois')->get();
        $recentPaiements = FinancialRecord::where('institution_id', $instId)->where('annee_academique', $annee)
            ->whereNotNull('date_paiement')->with(['apprenant', 'recordedBy'])->orderByDesc('date_paiement')->limit(10)->get();
        $anneesDispos = FinancialRecord::where('institution_id', $instId)->distinct()->pluck('annee_academique')->sort()->values();
        if (! $anneesDispos->contains($annee)) {
            $anneesDispos->prepend($annee);
        }
        $moisLabels = FinancialRecord::moisLabels();

        return view('staff.paiements', compact(
            'user', 'institution', 'apprenants', 'annee', 'search', 'statut',
            'classeId', 'niveauId', 'filiereId',
            'statsAnnee', 'statsMois', 'recentPaiements', 'anneesDispos', 'moisLabels',
        ) + [
            'classes' => Classe::where('institution_id', $instId)->orderBy('name')->get(),
            'niveaux' => Niveau::orderBy('name')->get(),
            'filieres' => Filiere::where('institution_id', $instId)->orderBy('name')->get(),
        ]);
    }

    public function financialApprenant(Apprenant $apprenant)
{
    $institution = $this->getInstitution();
    $this->assertBelongsToInstitution($apprenant, $institution->id, 'Apprenant');
    $instId = $institution->id;
    $user = Auth::user();
    $annee = request('annee', $institution->academic_year ?? date('Y').'-'.(date('Y') + 1));
    $moisLabels = FinancialRecord::moisLabels();
    $anneesDispos = FinancialRecord::where('apprenant_id', $apprenant->id)->distinct()->pluck('annee_academique')->sort()->values();
    if (! $anneesDispos->contains($annee)) {
        $anneesDispos->prepend($annee);
    }
    $allRecords = FinancialRecord::where('apprenant_id', $apprenant->id)->with(['recordedBy', 'validatedBy'])->orderBy('annee_academique')->orderBy('mois')->get();
    $records = $allRecords->where('annee_academique', $annee)->keyBy('mois');
    $totaux = ['du' => $records->sum('montant_du'), 'paye' => $records->sum('montant_paye'), 'reste' => $records->sum('montant_reste')];

    // ✅ Toutes les variables requises par la vue partagée
    $search    = '';
    $statut    = '';
    $classeId  = '';
    $niveauId  = '';
    $filiereId = '';
    $classes   = Classe::where('institution_id', $instId)->orderBy('name')->get();
    $niveaux   = Niveau::orderBy('name')->get();
    $filieres  = Filiere::where('institution_id', $instId)->orderBy('name')->get();

    // Faux paginator vide pour satisfaire $apprenants->total() et les boucles de la vue liste
    $apprenants = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 25);

    // Stats vides pour la vue liste
    $statsAnnee = (object)[
        'total_du' => 0, 'total_paye' => 0, 'total_reste' => 0,
        'nb_payes' => 0, 'nb_partiels' => 0, 'nb_impayes' => 0,
    ];
    $statsMois       = collect();
    $recentPaiements = collect();

    return view('staff.paiements', compact(
        'user', 'institution', 'apprenant', 'annee', 'anneesDispos',
        'allRecords', 'records', 'moisLabels', 'totaux',
        'search', 'statut', 'classeId', 'niveauId', 'filiereId',
        'classes', 'niveaux', 'filieres',
        'apprenants', 'statsAnnee', 'statsMois', 'recentPaiements'
    ));
}

    public function financialStore(Request $request)
    {
        $institution = $this->getInstitution();
        $data = $request->validate([
            'apprenant_id' => 'required|exists:apprenants,id',
            'annee_academique' => 'required|string|max:20',
            'mois' => 'required|integer|between:1,12',
            'montant_du' => 'required|numeric|min:0',
            'montant_paye' => 'required|numeric|min:0',
            'date_paiement' => 'nullable|date',
            'mode_paiement' => 'nullable|in:especes,virement,mobile_money,cheque,autre',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);
        $apprenant = Apprenant::where('id', $data['apprenant_id'])->where('institution_id', $institution->id)->firstOrFail();
        $moisLabels = FinancialRecord::moisLabels();
        $reste = max(0, $data['montant_du'] - $data['montant_paye']);
        $statut = 'impaye';
        if ($data['montant_du'] > 0 && $data['montant_paye'] >= $data['montant_du']) {
            $statut = 'paye';
        } elseif ($data['montant_paye'] > 0) {
            $statut = 'partiel';
        }

        FinancialRecord::updateOrCreate(
            ['apprenant_id' => $apprenant->id, 'annee_academique' => $data['annee_academique'], 'mois' => $data['mois']],
            [
                'institution_id' => $institution->id, 'mois_label' => $moisLabels[$data['mois']],
                'montant_du' => $data['montant_du'], 'montant_paye' => $data['montant_paye'], 'montant_reste' => $reste,
                'statut' => $statut, 'date_paiement' => $data['date_paiement'] ?? null,
                'mode_paiement' => $data['mode_paiement'] ?? null, 'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null, 'recorded_by' => Auth::id(), 'recorded_at' => now(),
            ]
        );

        return redirect()->back()->with('success', "Paiement de {$apprenant->prenom} {$apprenant->nom} ({$moisLabels[$data['mois']]}) enregistré.");
    }

    public function financialValidate(FinancialRecord $record)
    {
        $this->assertBelongsToInstitution($record, $this->getInstitution()->id, 'Enregistrement');
        $record->update(['validated_by' => Auth::id(), 'validated_at' => now()]);

        return redirect()->back()->with('success', 'Enregistrement validé et signé.');
    }

    public function financialDestroy(FinancialRecord $record)
    {
        $this->assertBelongsToInstitution($record, $this->getInstitution()->id, 'Enregistrement');
        $record->delete();

        return redirect()->back()->with('success', 'Enregistrement supprimé.');
    }

    public function financialExport(Request $request)
    {
        $instId = $this->getInstitution()->id;
        $annee = $request->get('annee', Auth::user()->institution->academic_year);
        $records = FinancialRecord::where('institution_id', $instId)->where('annee_academique', $annee)
            ->with(['apprenant.classe', 'recordedBy', 'validatedBy'])->orderBy('mois')->get();
        $headers = ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="finances_'.$annee.'_'.now()->format('Ymd').'.csv"'];

        return response()->stream(function () use ($records) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($h, ['Matricule', 'Nom', 'Prénom', 'Classe', 'Mois', 'Année', 'Dû (FCFA)', 'Payé (FCFA)', 'Reste (FCFA)', 'Statut', 'Date paiement', 'Mode', 'Référence', 'Enregistré par', 'Validé par']);
            foreach ($records as $r) {
                fputcsv($h, [$r->apprenant->matricule ?? '', $r->apprenant->nom ?? '', $r->apprenant->prenom ?? '', $r->apprenant->classe->name ?? '', $r->mois_label, $r->annee_academique, $r->montant_du, $r->montant_paye, $r->montant_reste, $r->statut_label, $r->date_paiement?->format('d/m/Y') ?? '', $r->mode_paiement ?? '', $r->reference ?? '', $r->recordedBy?->name ?? '', $r->validatedBy?->name ?? '']);
            }
            fclose($h);
        }, 200, $headers);
    }

    public function ParentIndex(Request $request)
    {
        $user = Auth::user();
        $institution = $user->institution;
        $instId = $institution->id;

        $query = SchoolParent::whereHas('apprenants', function ($q) use ($instId) {
            $q->where('institution_id', $instId);
        })
            ->orWhereHas('user', function ($q) use ($instId) {
                $q->where('institution_id', $instId);
            });

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nom', 'like', "%{$s}%")
                    ->orWhere('prenom', 'like', "%{$s}%")
                    ->orWhere('telephone', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $parents = $query
            ->with(['apprenants.classe', 'user'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => $query->toBase()->getCountForPagination(),
            'actifs' => SchoolParent::where('status', 1)->count(),
            'total_enfants' => DB::table('apprenant_parent')
                ->join('apprenants', 'apprenant_parent.apprenant_id', '=', 'apprenants.id')
                ->where('apprenants.institution_id', $instId)
                ->count(),
            'avec_compte' => SchoolParent::whereNotNull('user_id')->count(),
        ];

        $apprenantsSansParent = Apprenant::where('institution_id', $instId)
            ->whereDoesntHave('parents')
            ->with('classe')
            ->orderBy('nom')
            ->get();

        $apprenants = Apprenant::where('institution_id', $instId)
            ->with('classe')
            ->orderBy('nom')
            ->get();

        $recentAffectations = Apprenant::where('institution_id', $instId)
            ->has('parents')
            ->with(['parents', 'classe'])
            ->latest()
            ->take(6)
            ->get();

        $niveaux = Niveau::orderBy('name')->get(['id', 'name']);
        $filieres = Filiere::where('institution_id', $instId)->orderBy('name')->get(['id', 'name']);
        $classes = Classe::where('institution_id', $instId)->orderBy('name')->get(['id', 'name']);

        return view('staff.parents', compact(
            'user', 'institution',
            'parents', 'stats',
            'apprenants', 'apprenantsSansParent',
            'recentAffectations',
            'niveaux', 'filieres', 'classes'
        ));
    }

    /* ─────────────────────────────────────────
     | CRÉER UN PARENT
     | + compte User optionnel
     | + affectation immédiate si apprenant_id
     | + envoi e-mail de bienvenue automatique
     ───────────────────────────────────────── */
    public function ParentStore(Request $request)
    {
        $user = Auth::user();
        $institution = $user->institution;

        $data = $request->validate([
            'prenom' => 'required|string|max:100',
            'nom' => 'required|string|max:100',
            'sexe' => 'nullable|in:M,F',
            'telephone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'profession' => 'nullable|string|max:100',
            'adresse' => 'nullable|string|max:255',
            'apprenant_id' => 'nullable|exists:apprenants,id',
            'lien' => 'nullable|string|max:50',
            'password' => 'nullable|min:8|confirmed',
            'password_confirmation' => 'nullable',
        ]);

        // ✅ Mot de passe en clair AVANT la transaction (pour l'email)
        $passwordRaw = $data['password'] ?? 'password123';
        $emailDest = ! empty($data['email']) ? $data['email'] : null;
        $parent = null;

        DB::transaction(function () use ($data, $institution, $passwordRaw, &$parent) {

            $userId = null;

            if (! empty($data['email'])) {
                $existingUser = User::where('email', $data['email'])->first();

                if ($existingUser) {
                    $userId = $existingUser->id;
                } else {
                    $newUser = User::create([
                        'name' => $data['prenom'].' '.$data['nom'],
                        'email' => $data['email'],
                        // ✅ Hash::make($passwordRaw) — le clair est gardé dans $passwordRaw
                        'password' => Hash::make($passwordRaw),
                        'institution_id' => $institution->id,
                        'status' => 1,
                    ]);
                    \Spatie\Permission\Models\Role::firstOrCreate(
                        ['name' => 'parent', 'guard_name' => 'web']
                    );
                    $newUser->assignRole('parent');
                    $userId = $newUser->id;
                }
            } else {
                // Pas d'email → compte technique pour satisfaire user_id NOT NULL
                $techEmail = 'parent.'.uniqid().'@'.($institution->code ?? 'etab').'.local';
                $newUser = User::create([
                    'name' => $data['prenom'].' '.$data['nom'],
                    'email' => $techEmail,
                    'password' => Hash::make(uniqid('', true)),
                    'institution_id' => $institution->id,
                    'status' => 1,
                ]);
                \Spatie\Permission\Models\Role::firstOrCreate(
                    ['name' => 'parent', 'guard_name' => 'web']
                );
                $newUser->assignRole('parent');
                $userId = $newUser->id;
            }

            // Génération automatique du matricule
            $count = SchoolParent::count() + 1;
            $matricule = 'PAR-'.str_pad($count, 5, '0', STR_PAD_LEFT);
            while (SchoolParent::where('matricule', $matricule)->exists()) {
                $count++;
                $matricule = 'PAR-'.str_pad($count, 5, '0', STR_PAD_LEFT);
            }

            // Créer le profil parent
            $parent = SchoolParent::create([
                'user_id' => $userId,
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'sexe' => $data['sexe'] ?? null,
                'matricule' => $matricule,
                'telephone' => $data['telephone'] ?? null,
                'email' => $data['email'] ?? null,
                'profession' => $data['profession'] ?? null,
                'adresse' => $data['adresse'] ?? null,
                'status' => 1,
            ]);

            // Affectation immédiate si apprenant sélectionné
            if (! empty($data['apprenant_id'])) {
                $apprenant = Apprenant::where('id', $data['apprenant_id'])
                    ->where('institution_id', $institution->id)
                    ->first();

                if ($apprenant) {
                    $alreadyLinked = $parent->apprenants()
                        ->where('apprenant_parent.apprenant_id', $apprenant->id)
                        ->exists();

                    if (! $alreadyLinked) {
                        $parent->apprenants()->attach($apprenant->id, [
                            'lien' => $this->normalizeLien($data['lien'] ?? 'tuteur'),
                        ]);
                    }
                }
            }
        });

        // ✅ Envoi e-mail APRÈS la transaction
        // Uniquement si un vrai email a été fourni (pas le compte technique)
        if ($emailDest && $parent) {
            WelcomeMailService::sendToParent(
                email: $emailDest,
                prenom: $data['prenom'],
                nom: $data['nom'],
                matricule: $parent->matricule,
                passwordRaw: $passwordRaw,
                institution: $institution->name,
            );
        }

        $msg = "Parent « {$data['prenom']} {$data['nom']} » enregistré avec succès.";
        if ($emailDest) {
            $msg .= ' Un e-mail de bienvenue a été envoyé.';
        }

        return redirect()->back()->with('success', $msg);
    }

    /* ─────────────────────────────────────────
     | METTRE À JOUR UN PARENT
     ───────────────────────────────────────── */
    public function ParentUpdate(Request $request, SchoolParent $parent)
    {
        $data = $request->validate([
            'prenom' => 'required|string|max:100',
            'nom' => 'required|string|max:100',
            'sexe' => 'nullable|in:M,F',
            'telephone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'profession' => 'nullable|string|max:100',
            'adresse' => 'nullable|string|max:255',
            'status' => 'nullable|in:0,1',
        ]);

        $parent->update([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'sexe' => $data['sexe'] ?? $parent->sexe,
            'telephone' => $data['telephone'] ?? null,
            'email' => $data['email'] ?? null,
            'profession' => $data['profession'] ?? null,
            'adresse' => $data['adresse'] ?? null,
            'status' => $data['status'] ?? $parent->status,
        ]);

        if ($parent->user_id && $parent->user) {
            $parent->user->update([
                'name' => $data['prenom'].' '.$data['nom'],
                'email' => $data['email'] ?? $parent->user->email,
            ]);
        }

        return redirect()->back()
            ->with('success', "Parent « {$data['prenom']} {$data['nom']} » mis à jour.");
    }

    /* ─────────────────────────────────────────
     | SUPPRIMER UN PARENT
     ───────────────────────────────────────── */
    public function ParentDestroy(SchoolParent $parent)
    {
        $name = $parent->prenom.' '.$parent->nom;

        DB::transaction(function () use ($parent) {
            $parent->apprenants()->detach();
            if ($parent->user_id && $parent->user) {
                $parent->user->delete();
            }
            $parent->delete();
        });

        return redirect()->back()
            ->with('success', "Parent « {$name} » supprimé.");
    }

    /* ─────────────────────────────────────────
     | AFFECTER UN ENFANT À UN PARENT
     ───────────────────────────────────────── */
    public function affect(Request $request)
    {
        $user = Auth::user();
        $institution = $user->institution;

        $data = $request->validate([
            'parent_id' => 'required|exists:parents,id',
            'apprenant_id' => 'required|exists:apprenants,id',
            'lien' => 'nullable|string|max:50',
        ]);

        $apprenant = Apprenant::where('id', $data['apprenant_id'])
            ->where('institution_id', $institution->id)
            ->firstOrFail();

        $parent = SchoolParent::findOrFail($data['parent_id']);

        $exists = $parent->apprenants()
            ->where('apprenant_id', $apprenant->id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Cette liaison parent-élève existe déjà.');
        }

        $parent->apprenants()->attach($apprenant->id, [
            'lien' => $this->normalizeLien($data['lien'] ?? 'tuteur'),
        ]);

        return redirect()->back()
            ->with('success', "{$apprenant->prenom} {$apprenant->nom} affecté(e) à {$parent->prenom} {$parent->nom} ({$data['lien']}).");
    }

    /* ─────────────────────────────────────────
     | DÉTACHER UN ENFANT D'UN PARENT
     ───────────────────────────────────────── */
    public function detach(Request $request)
    {
        $user = Auth::user();
        $institution = $user->institution;

        $data = $request->validate([
            'parent_id' => 'required|exists:parents,id',
            'apprenant_id' => 'required|exists:apprenants,id',
        ]);

        $apprenant = Apprenant::where('id', $data['apprenant_id'])
            ->where('institution_id', $institution->id)
            ->firstOrFail();

        $parent = SchoolParent::findOrFail($data['parent_id']);

        $parent->apprenants()->detach($apprenant->id);

        return redirect()->back()
            ->with('success', "{$apprenant->prenom} {$apprenant->nom} retiré(e) du suivi de {$parent->prenom} {$parent->nom}.");
    }

    /* ─────────────────────────────────────────
     | RÉINITIALISER MOT DE PASSE
     ───────────────────────────────────────── */
    public function ParentResetPassword(Request $request, SchoolParent $parent)
    {
        if (! $parent->user_id) {
            return redirect()->back()
                ->with('error', "Ce parent n'a pas de compte utilisateur.");
        }

        $request->validate(['password' => 'required|min:8|confirmed']);

        $parent->user->update(['password' => Hash::make($request->password)]);

        return redirect()->back()
            ->with('success', "Mot de passe de {$parent->prenom} {$parent->nom} réinitialisé.");
    }

    /* ─────────────────────────────────────────
     | FICHE INDIVIDUELLE D'UN PARENT
     ───────────────────────────────────────── */
    public function ParentShow(SchoolParent $parent)
    {
        $user = Auth::user();
        $institution = $user->institution;

        $parent->load([
            'apprenants' => function ($q) use ($institution) {
                $q->where('institution_id', $institution->id)
                    ->with([
                        'classe',
                        'niveau',
                        'filiere',
                        'financialRecords' => fn ($q) => $q->where(
                            'annee_academique',
                            $institution->academic_year
                        ),
                    ]);
            },
            'user',
        ]);

        return view('staff.parent-show', compact('parent', 'institution', 'user'));
    }

    /* ─────────────────────────────────────────
     | HELPER : normalise le lien de parenté
     ───────────────────────────────────────── */
    private function normalizeLien(string $lien): string
    {
        $map = [
            'père' => 'pere',
            'pere' => 'pere',
            'mère' => 'mere',
            'mere' => 'mere',
            'tuteur' => 'tuteur',
            'tutrice' => 'tuteur',
            'grand-père' => 'tuteur',
            'grand-pere' => 'tuteur',
            'grand-mère' => 'tuteur',
            'grand-mere' => 'tuteur',
            'oncle' => 'tuteur',
            'tante' => 'tuteur',
            'autre' => 'tuteur',
        ];

        return $map[mb_strtolower(trim($lien))] ?? 'tuteur';
    }

    /* ─────────────────────────────────────────
     | RECHERCHE AJAX D'APPRENANTS
     ───────────────────────────────────────── */
    public function searchApprenants(Request $request)
    {
        $user = Auth::user();
        $institution = $user->institution;
        $instId = $institution->id;

        $query = Apprenant::where('institution_id', $instId)
            ->with(['classe:id,name', 'niveau:id,name', 'filiere:id,name'])
            ->select('id', 'nom', 'prenom', 'matricule', 'class_id', 'niveau_id', 'filiere_id');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nom', 'like', "%{$q}%")
                    ->orWhere('prenom', 'like', "%{$q}%")
                    ->orWhere('matricule', 'like', "%{$q}%");
            });
        }

        if ($request->filled('niveau_id')) {
            $query->where('niveau_id', $request->niveau_id);
        }
        if ($request->filled('filiere_id')) {
            $query->where('filiere_id', $request->filiere_id);
        }
        if ($request->filled('classe_id')) {
            $query->where('class_id', $request->classe_id);
        }

        $total = $query->count();
        $apprenants = $query->orderBy('nom')->orderBy('prenom')->limit(50)->get()
            ->map(function ($a) {
                return [
                    'id' => $a->id,
                    'nom' => $a->nom,
                    'prenom' => $a->prenom,
                    'matricule' => $a->matricule,
                    'classe' => optional($a->classe)->name,
                    'niveau' => optional($a->niveau)->name,
                    'filiere' => optional($a->filiere)->name,
                    'label' => $a->prenom.' '.$a->nom
                                  .(optional($a->classe)->name ? ' — '.optional($a->classe)->name : '')
                                  .($a->matricule ? ' ('.$a->matricule.')' : ''),
                ];
            });

        return response()->json(['data' => $apprenants, 'total' => $total]);
    }

    /* ═══════════════════════════════════════════════════════
     | MAIN PAGE — emploi du temps + séances + paiements
     | GET /admin/planning
     ═══════════════════════════════════════════════════════ */
    public function PlanningIndex(Request $request)
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

        $emploisDuTemps = $edtQuery->orderByRaw("FIELD(jour,'lundi','mardi','mercredi','jeudi','vendredi','samedi')")
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
        $niveaux = Niveau::orderBy('name')->get();
        $filieres = Filiere::where('institution_id', $instId)->orderBy('name')->get();

        $jourLabels = EmploiDuTemps::jourLabels();
        $typeLabels = EmploiDuTemps::typeLabels();
        $typeColors = EmploiDuTemps::typeColors();
        $periodeLabels = ProgrammePaiement::periodeLabels();
        $typeFraisLabels = ProgrammePaiement::typeLabels();

        return view('staff.planning', compact(
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

    /* ══════════════════════════════════════════════════════════
     | RAPPORTS
     ══════════════════════════════════════════════════════════ */
    public function rapports()
    {
        $user = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();
        $instId = $institution->id;
        $annee = $institution->academic_year ?? date('Y').'-'.(date('Y') + 1);

        $totalApprenants = Apprenant::where('institution_id', $instId)->count();
        $actifsApprenants = Apprenant::where('institution_id', $instId)->where('status', 1)->count();
        $garcons = Apprenant::where('institution_id', $instId)->where('sexe', 'M')->count();
        $filles = Apprenant::where('institution_id', $instId)->where('sexe', 'F')->count();

        $apprenantsByClasse = DB::table('apprenants')
            ->join('classes', 'apprenants.class_id', '=', 'classes.id')
            ->where('apprenants.institution_id', $instId)
            ->select('classes.name as classe', DB::raw('COUNT(*) as total'))
            ->groupBy('classes.id', 'classes.name')->orderByDesc('total')->get();

        $apprenantsByNiveau = DB::table('apprenants')
            ->join('niveaux', 'apprenants.niveau_id', '=', 'niveaux.id')
            ->where('apprenants.institution_id', $instId)
            ->select('niveaux.name as niveau', DB::raw('COUNT(*) as total'))
            ->groupBy('niveaux.id', 'niveaux.name')->orderByDesc('total')->get();

        $totalTeachers = Teacher::where('institution_id', $instId)->count();
        $actifsTeachers = Teacher::where('institution_id', $instId)->where('status', 1)->count();
        $teachersByGender = DB::table('teachers')->where('institution_id', $instId)
    ->select('sexe', DB::raw('COUNT(*) as total'))->groupBy('sexe')->get()->keyBy('sexe');

$teachersHommes = $teachersByGender->get('M')?->total ?? 0;
$teachersFemmes = $teachersByGender->get('F')?->total ?? 0;
        $teachersByContrat = DB::table('teachers')->where('institution_id', $instId)->whereNotNull('type_contrat')
            ->select('type_contrat', DB::raw('COUNT(*) as total'))->groupBy('type_contrat')->orderByDesc('total')->get();

        $totalClasses = Classe::where('institution_id', $instId)->count();
        $totalMatieres = Subject::where('institution_id', $instId)->count();
        $totalFilieres = Filiere::where('institution_id', $instId)->count();
        $totalNiveaux = Niveau::withCount(['classes' => fn ($q) => $q->where('institution_id', $instId)])->having('classes_count', '>', 0)->count();

        $apprenantsSansClasse = Apprenant::where('institution_id', $instId)->whereNull('class_id')->count();
        $tauxAffectation = $totalApprenants > 0
            ? round(($totalApprenants - $apprenantsSansClasse) / $totalApprenants * 100, 1) : 0;

        $finStats = FinancialRecord::where('institution_id', $instId)->where('annee_academique', $annee)
            ->selectRaw('COALESCE(SUM(montant_du),0) as total_du, COALESCE(SUM(montant_paye),0) as total_paye, COALESCE(SUM(montant_reste),0) as total_reste, COUNT(CASE WHEN statut="paye" THEN 1 END) as nb_payes, COUNT(CASE WHEN statut="partiel" THEN 1 END) as nb_partiels, COUNT(CASE WHEN statut="impaye" THEN 1 END) as nb_impayes, COUNT(*) as nb_total')
            ->first();

        $finMensuel = FinancialRecord::where('institution_id', $instId)->where('annee_academique', $annee)
            ->selectRaw('mois, mois_label, SUM(montant_du) as du, SUM(montant_paye) as paye, SUM(montant_reste) as reste')
            ->groupBy('mois', 'mois_label')->orderBy('mois')->get();

        $topDebiteurs = Apprenant::where('apprenants.institution_id', $instId)
            ->join('financial_records', 'apprenants.id', '=', 'financial_records.apprenant_id')
            ->where('financial_records.annee_academique', $annee)
            ->where('financial_records.statut', '!=', 'paye')
            ->select('apprenants.id', 'apprenants.nom', 'apprenants.prenom', 'apprenants.class_id', DB::raw('SUM(financial_records.montant_reste) as total_reste'))
            ->groupBy('apprenants.id', 'apprenants.nom', 'apprenants.prenom', 'apprenants.class_id')
            ->orderByDesc('total_reste')->with('classe:id,name')->limit(5)->get();

        $totalStaff = \App\Models\Staff::where('institution_id', $instId)->count();
        $actifsStaff = \App\Models\Staff::where('institution_id', $instId)->where('status', 1)->count();
        $staffByUnit = DB::table('staff')
            ->join('administrative_units', 'staff.administrative_unit_id', '=', 'administrative_units.id')
            ->where('staff.institution_id', $instId)
            ->select('administrative_units.name as unite', DB::raw('COUNT(*) as total'))
            ->groupBy('administrative_units.id', 'administrative_units.name')->orderByDesc('total')->get();

        $totalParents = DB::table('apprenant_parent')
            ->join('apprenants', 'apprenant_parent.apprenant_id', '=', 'apprenants.id')
            ->where('apprenants.institution_id', $instId)
            ->distinct('apprenant_parent.parent_id')->count('apprenant_parent.parent_id');
        $apprenantsSansParent = Apprenant::where('institution_id', $instId)->whereDoesntHave('parents')->count();
        $tauxCouvertureParents = $totalApprenants > 0
            ? round(($totalApprenants - $apprenantsSansParent) / $totalApprenants * 100, 1) : 0;

        return view('staff.rapports', compact(
            'user', 'institution', 'annee',
            'totalApprenants', 'actifsApprenants', 'garcons', 'filles',
            'apprenantsByClasse', 'apprenantsByNiveau', 'apprenantsSansClasse', 'tauxAffectation',
            'totalTeachers', 'actifsTeachers', 'teachersBySexe', 'teachersByContrat',
            'totalClasses', 'totalMatieres', 'totalFilieres', 'totalNiveaux',
            'finStats', 'finMensuel', 'topDebiteurs',
            'totalStaff', 'actifsStaff', 'staffByUnit',
            'totalParents', 'apprenantsSansParent', 'tauxCouvertureParents',
            'teachersByGender', 'teachersHommes', 'teachersFemmes'
        ));
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
                    ->whereHas('evaluation', fn ($q) => $q->where('subject_id', $sub->id))
                    ->get();

                return [
                    'matiere' => $sub->name,
                    'coefficient' => $sub->coefficient,
                    'moyenne' => $grades->isNotEmpty() ? round($grades->avg('score'), 2) : null,
                    'nb_notes' => $grades->count(),
                    'min' => $grades->isNotEmpty() ? $grades->min('score') : null,
                    'max' => $grades->isNotEmpty() ? $grades->max('score') : null,
                ];
            })->filter(fn ($m) => $m['nb_notes'] > 0)->values();

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
                'total' => $dossier['incidents']->count(),
                'graves' => $dossier['incidents']->where('gravite', 3)->count(),
                'types' => $dossier['incidents']->groupBy('type')
                    ->map(fn ($g) => $g->count()),
            ];
        }

        // ── Situation financière ─────────────────────────────────────
        if (in_array('finances', $scope)) {
            $records = $apprenant->financialRecords()
                ->orderBy('annee_academique')
                ->orderBy('mois')
                ->get();

            $dossier['finances'] = $records;
            $dossier['finances_totaux'] = [
                'total_du' => $records->sum('montant_du'),
                'total_paye' => $records->sum('montant_paye'),
                'total_reste' => $records->sum('montant_reste'),
            ];
        }

        // ── Historique des classes ───────────────────────────────────
        if (in_array('classes', $scope)) {
            try {
                $dossier['historique_classes'] = DB::table('class_apprenant')
                    ->join('classes', 'class_apprenant.class_id', '=', 'classes.id')
                    ->leftJoin('niveaux', 'classes.niveau_id', '=', 'niveaux.id')
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

    public function TransfertIndex()
    {
        $user = Auth::user();
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
            'envoyees_pending' => TransferRequest::sentBy($institution->id)->pending()->count(),
            'recues_pending' => TransferRequest::receivedBy($institution->id)->pending()->count(),
            'approuvees' => TransferRequest::sentBy($institution->id)->approved()->count(),
        ];

        return view('staff.transferts', compact(
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
            'matricule' => 'required|string|min:2',
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
                'id' => $a->id,
                'matricule' => $a->matricule,
                'nom' => $a->nom,
                'prenom' => $a->prenom,
                'sexe' => $a->sexe,
                'date_naissance' => $a->date_naissance,
                'institution' => $a->institution?->name,
                'institution_id' => $a->institution_id,
                'classe' => $a->classe?->name,
                'niveau' => $a->niveau?->name,
                'filiere' => $a->filiere?->name,
                'annee_academique' => $a->annee_academique,
                'status' => $a->status,
            ];
        });

        return response()->json([
            'found' => $apprenants->count(),
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
            'apprenant_id' => 'required|exists:apprenants,id',
            'motif' => 'required|string|max:500',
            'scope' => 'required|array|min:1',
            'scope.*' => 'in:identity,notes,bulletins,discipline,finances,classes',
        ]);

        $apprenant = Apprenant::findOrFail($data['apprenant_id']);

        // Vérifier que l'apprenant n'appartient pas à mon institution
        abort_if(
            $apprenant->institution_id === $institution->id,
            422,
            'Vous ne pouvez pas demander un transfert pour un élève déjà dans votre établissement.'
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
            'apprenant_id' => $apprenant->id,
            'institution_source_id' => $apprenant->institution_id,
            'institution_dest_id' => $institution->id,
            'requested_by' => Auth::id(),
            'statut' => 'pending',
            'scope' => $data['scope'],
            'motif' => $data['motif'],
        ]);

        return redirect()->route('staff.transfer.index')
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
            'statut' => 'rejected',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
            'motif_refus' => $request->motif_refus,
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
        $user = Auth::user();

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

            return redirect()->route('staff.transfer.index')
                ->with('error', 'Le délai d\'accès à ce dossier a expiré.');
        }

        $apprenant = $transfer->apprenant;
        $scope = $transfer->scope ?? ['identity'];

        // Toujours inclure l'identité
        if (! in_array('identity', $scope)) {
            $scope[] = 'identity';
        }

        $dossier = $this->buildDossier($apprenant, $scope);

        // Marquer comme complété si premier accès
        if ($transfer->statut === 'approved') {
            $transfer->update(['statut' => 'completed']);
        }

        return view('staff.transferts', compact(
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
        $user = Auth::user();

        // Soit je suis la source, soit le destinataire
        abort_if(
            $transfer->institution_source_id !== $institution->id &&
            $transfer->institution_dest_id !== $institution->id,
            403
        );

        $apprenant = $transfer->apprenant->load([
            'institution', 'classe', 'niveau', 'filiere',
        ]);

        return view('staff.transferts', compact(
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

        return redirect()->route('staff.transfer.index')
            ->with('success', 'Demande annulée.');
    }

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

    Subject::where('id', $data['subject_id'])
        ->where('institution_id', $institution->id)
        ->firstOrFail();

    $typeLabels = [
        'controle'    => 'Contrôle',    'examen'      => 'Examen',
        'tp'          => 'Travaux pratiques', 'projet' => 'Projet',
        'interro'     => 'Interrogation', 'devoir'    => 'Devoir',
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
        'institution_id'  => $institution->id,
    ]);

    return redirect()
        ->route('staff.bulletins', ['evaluation_id' => $eval->id])
        ->with('success', "Évaluation « {$eval->title} » créée. Saisissez maintenant les notes.");
}

    public function evaluationDestroy(Evaluation $evaluation)
{
    $institution = $this->getInstitution();

    $appartient = (int) $evaluation->institution_id === $institution->id
        || Subject::where('id', $evaluation->subject_id)
            ->where('institution_id', $institution->id)->exists();

    if (! $appartient) abort(403);

    DB::transaction(function () use ($evaluation) {
        $evaluation->grades()->delete();
        $evaluation->delete();
    });

    return redirect()->back()->with('success', 'Évaluation supprimée.');
}

    public function gradesStore(Request $request)
{
    $institution = $this->getInstitution();

    $request->validate([
        'evaluation_id' => 'required|exists:evaluations,id',
        'grades'        => 'required|array',
    ]);

    // ✅ Double source
    $directIds     = Evaluation::where('institution_id', $institution->id)->pluck('id');
    $viaSubjectIds = Evaluation::whereHas(
        'subject', fn($q) => $q->where('institution_id', $institution->id)
    )->pluck('id');
    $allIds = $directIds->merge($viaSubjectIds)->unique()->values();

    $evaluation = Evaluation::whereIn('id', $allIds)
        ->findOrFail($request->evaluation_id);

    $saved = $skipped = 0;
    $hasRecordedBy = \Illuminate\Support\Facades\Schema::hasColumn('grades', 'recorded_by');

    DB::transaction(function () use (
        $request, $evaluation, $institution, &$saved, &$skipped, $hasRecordedBy
    ) {
        foreach ($request->grades as $apprenantId => $score) {
            if ($score === null || $score === '') { $skipped++; continue; }

            $apprenant = Apprenant::where('id', $apprenantId)
                ->where('institution_id', $institution->id)->first();
            if (! $apprenant) { $skipped++; continue; }

            $data = ['score' => min(max(0, (float) $score), (float) $evaluation->max_score)];
            if ($hasRecordedBy) $data['recorded_by'] = \Illuminate\Support\Facades\Auth::id();

            Grade::updateOrCreate(
                ['evaluation_id' => $evaluation->id, 'apprenant_id' => (int) $apprenantId],
                $data
            );
            $saved++;
        }
    });

    return redirect()
        ->route('staff.bulletins', ['evaluation_id' => $evaluation->id])
        ->with('success', "{$saved} note(s) enregistrée(s), {$skipped} ignorée(s).");
}


    public function gradeUpdate(Request $request, Grade $grade)
{
    $institution = $this->getInstitution();

    $directIds     = Evaluation::where('institution_id', $institution->id)->pluck('id');
    $viaSubjectIds = Evaluation::whereHas(
        'subject', fn($q) => $q->where('institution_id', $institution->id)
    )->pluck('id');

    $evaluation = Evaluation::whereIn(
        'id', $directIds->merge($viaSubjectIds)->unique()
    )->where('id', $grade->evaluation_id)->firstOrFail();

    $request->validate(['score' => 'required|numeric|min:0']);

    $data = ['score' => min((float) $request->score, (float) $evaluation->max_score)];
    if (\Illuminate\Support\Facades\Schema::hasColumn('grades', 'recorded_by')) {
        $data['recorded_by'] = \Illuminate\Support\Facades\Auth::id();
    }

    $grade->update($data);
    return redirect()->back()->with('success', 'Note mise à jour.');
}

    public function gradeDestroy(Grade $grade)
{
    $institution = $this->getInstitution();

    $directIds     = Evaluation::where('institution_id', $institution->id)->pluck('id');
    $viaSubjectIds = Evaluation::whereHas(
        'subject', fn($q) => $q->where('institution_id', $institution->id)
    )->pluck('id');

    Evaluation::whereIn(
        'id', $directIds->merge($viaSubjectIds)->unique()
    )->where('id', $grade->evaluation_id)->firstOrFail();

    $grade->delete();
    return redirect()->back()->with('success', 'Note supprimée.');
}








/* ══════════════════════════════════════════════════════════
     | ACADÉMIQUE
     ══════════════════════════════════════════════════════════ */
    public function academic()
    {
        $user        = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();
        $instId      = $institution->id;

        // ✅ Niveaux filtrés par institution
        $sections = Niveau::where('institution_id', $instId)
            ->withCount(['classes' => fn ($q) => $q->where('institution_id', $instId)])
            ->orderBy('name')->get();

        $filieres  = Filiere::where('institution_id', $instId)->withCount('classes')->orderBy('name')->get();
        $matieres  = Subject::where('institution_id', $instId)->with(['classe', 'teacher'])->orderBy('name')->get();
        $classes   = Classe::where('institution_id', $instId)->withCount('apprenants')->with(['niveau', 'filiere'])->orderBy('name')->get();
        $teachers  = Teacher::where('institution_id', $instId)->with(['niveaux', 'classes', 'filieres'])->orderBy('nom')->get();
        $apprenants = Apprenant::where('institution_id', $instId)
            ->select('id', 'nom', 'prenom', 'matricule', 'class_id')
            ->with('classe:id,name')->orderBy('nom')->get();

        $teacherClasseAffectations = collect();
        try {
            $teacherClasseAffectations = DB::table('class_teacher')
                ->join('teachers', 'class_teacher.teacher_id', '=', 'teachers.id')
                ->join('classes', 'class_teacher.class_id', '=', 'classes.id')
                ->where('classes.institution_id', $instId)
                ->select('class_teacher.teacher_id', 'class_teacher.class_id',
                    'teachers.nom as t_nom', 'teachers.prenom as t_prenom', 'classes.name as c_name')
                ->get()
                ->map(fn ($r) => (object) [
                    'id'      => $r->teacher_id.'_'.$r->class_id,
                    'teacher' => (object) ['nom' => $r->t_nom, 'prenom' => $r->t_prenom],
                    'classe'  => (object) ['name' => $r->c_name],
                ]);
        } catch (\Exception) {
        }

        $eleveClasseAffectations = Apprenant::where('institution_id', $instId)
            ->whereNotNull('class_id')->with(['classe'])->select('id', 'nom', 'prenom', 'class_id')->get()
            ->map(fn ($a) => (object) ['apprenant' => $a, 'classe' => $a->classe]);

        $academicYear = (object) ['label' => $institution->academic_year ?? date('Y').'-'.(date('Y') + 1)];

        $stats = [
            'classes'  => Classe::where('institution_id', $instId)->count(),
            'sections' => $sections->count(),
            'filieres' => $filieres->count(),
            'matieres' => Subject::where('institution_id', $instId)->count(),
            'teachers' => Teacher::where('institution_id', $instId)->count(),
        ];

        $validations        = ['schedules_pending' => 0, 'schedules_total' => 0, 'bulletins_pending' => 0, 'bulletins_total' => 0, 'results_pending' => 0, 'results_total' => 0, 'pending_validations' => 0];
        $schedules          = collect();
        $bulletins          = collect();
        $resultPublications = collect();

        return view('staff.academic', compact(
            'user', 'institution', 'sections', 'filieres', 'matieres', 'classes',
            'teachers', 'apprenants', 'teacherClasseAffectations', 'eleveClasseAffectations',
            'academicYear', 'stats', 'validations', 'schedules', 'bulletins', 'resultPublications'
        ));
    }

    /* ── CLASSES ── */
    public function classeStore(Request $request)
    {
        $institution = $this->getInstitution();
        $instId      = $institution->id;

        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'code'      => 'nullable|string|max:20',
            // ✅ niveau_id et filiere_id scopés à l'institution
            'niveau_id' => ['nullable', Rule::exists('niveaux', 'id')->where('institution_id', $instId)],
            'filiere_id'=> ['nullable', Rule::exists('filieres', 'id')->where('institution_id', $instId)],
        ]);
        Classe::create(array_merge($data, ['institution_id' => $instId]));

        return redirect()->back()->with('success', "Classe « {$data['name']} » créée.")->withFragment('classes');
    }

    public function classeUpdate(Request $request, Classe $classe)
    {
        $institution = $this->getInstitution();
        $instId      = $institution->id;
        $this->assertBelongsToInstitution($classe, $instId, 'Classe');

        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'code'      => 'nullable|string|max:20',
            // ✅ niveau_id et filiere_id scopés à l'institution
            'niveau_id' => ['nullable', Rule::exists('niveaux', 'id')->where('institution_id', $instId)],
            'filiere_id'=> ['nullable', Rule::exists('filieres', 'id')->where('institution_id', $instId)],
        ]);
        $classe->update($data);

        return redirect()->back()->with('success', 'Classe mise à jour.')->withFragment('classes');
    }

    public function classeDestroy(Classe $classe)
    {
        $this->assertBelongsToInstitution($classe, $this->getInstitution()->id, 'Classe');
        $name = $classe->name;
        $classe->delete();

        return redirect()->back()->with('success', "Classe « {$name} » supprimée.")->withFragment('classes');
    }

    /* ── NIVEAUX ── */
    public function niveauStore(Request $request)
    {
        $institution = $this->getInstitution();

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:100', Rule::unique('niveaux', 'name')->where('institution_id', $institution->id)],
            'cycle' => 'nullable|in:primaire,secondaire,universite',
        ]);

        $data['institution_id'] = $institution->id;
        Niveau::create($data);

        return redirect()->back()->with('success', "Niveau « {$data['name']} » créé.")->withFragment('niveaux');
    }

    public function niveauUpdate(Request $request, Niveau $niveau)
    {
        $institution = $this->getInstitution();
        // ✅ Vérifier que le niveau appartient à cette institution
        abort_if((int) $niveau->institution_id !== $institution->id, 403, 'Niveau introuvable dans votre établissement.');

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:100', Rule::unique('niveaux', 'name')->where('institution_id', $institution->id)->ignore($niveau->id)],
            'cycle' => 'nullable|in:primaire,secondaire,universite',
        ]);

        $niveau->update($data);

        return redirect()->back()->with('success', 'Niveau mis à jour.')->withFragment('niveaux');
    }

    public function niveauDestroy(Niveau $niveau)
    {
        $institution = $this->getInstitution();
        // ✅ Vérifier que le niveau appartient à cette institution
        abort_if((int) $niveau->institution_id !== $institution->id, 403, 'Niveau introuvable dans votre établissement.');

        $name = $niveau->name;
        $niveau->delete();

        return redirect()->back()->with('success', "Niveau « {$name} » supprimé.")->withFragment('niveaux');
    }

    /* ── FILIÈRES ── */
    public function filiereStore(Request $request)
    {
        $institution = $this->getInstitution();
        $data        = $request->validate(['name' => 'required|string|max:100']);
        Filiere::create(array_merge($data, ['institution_id' => $institution->id]));

        return redirect()->back()->with('success', "Filière « {$data['name']} » créée.")->withFragment('filieres');
    }

    public function filiereUpdate(Request $request, Filiere $filiere)
    {
        $this->assertBelongsToInstitution($filiere, $this->getInstitution()->id, 'Filière');
        $data = $request->validate(['name' => 'required|string|max:100']);
        $filiere->update($data);

        return redirect()->back()->with('success', 'Filière mise à jour.')->withFragment('filieres');
    }

    public function filiereDestroy(Filiere $filiere)
    {
        $this->assertBelongsToInstitution($filiere, $this->getInstitution()->id, 'Filière');
        $name = $filiere->name;
        $filiere->delete();

        return redirect()->back()->with('success', "Filière « {$name} » supprimée.")->withFragment('filieres');
    }

    /* ── MATIÈRES ── */
    public function matiereStore(Request $request)
    {
        $institution = $this->getInstitution();
        $instId      = $institution->id;

        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'coefficient' => 'required|numeric|min:0.5',
            'class_id'    => 'nullable|exists:classes,id',
            'teacher_id'  => 'nullable|exists:teachers,id',
        ]);
        if (! empty($data['class_id'])) {
            Classe::where('id', $data['class_id'])->where('institution_id', $instId)->firstOrFail();
        }
        if (! empty($data['teacher_id'])) {
            Teacher::where('id', $data['teacher_id'])->where('institution_id', $instId)->firstOrFail();
        }
        Subject::create(array_merge($data, ['institution_id' => $instId]));

        return redirect()->back()->with('success', "Matière « {$data['name']} » créée.")->withFragment('matieres');
    }

    public function matiereUpdate(Request $request, Subject $subject)
    {
        $institution = $this->getInstitution();
        $instId      = $institution->id;
        $this->assertBelongsToInstitution($subject, $instId, 'Matière');

        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'coefficient' => 'required|numeric|min:0.5',
            'class_id'    => 'nullable|exists:classes,id',
            'teacher_id'  => 'nullable|exists:teachers,id',
        ]);
        if (! empty($data['class_id'])) {
            Classe::where('id', $data['class_id'])->where('institution_id', $instId)->firstOrFail();
        }
        if (! empty($data['teacher_id'])) {
            Teacher::where('id', $data['teacher_id'])->where('institution_id', $instId)->firstOrFail();
        }
        $subject->update($data);

        return redirect()->back()->with('success', 'Matière mise à jour.')->withFragment('matieres');
    }

    public function matiereDestroy(Subject $subject)
    {
        $this->assertBelongsToInstitution($subject, $this->getInstitution()->id, 'Matière');
        $name = $subject->name;
        $subject->delete();

        return redirect()->back()->with('success', "Matière « {$name} » supprimée.")->withFragment('matieres');
    }

    /* ── AFFECTATIONS ── */
    public function affectationTeacherClasse(Request $request)
    {
        $institution = $this->getInstitution();
        $instId      = $institution->id;

        $data = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'classe_id'  => 'required|exists:classes,id',
        ]);
        $classe  = Classe::where('id', $data['classe_id'])->where('institution_id', $instId)->firstOrFail();
        $teacher = Teacher::where('id', $data['teacher_id'])->where('institution_id', $instId)->firstOrFail();
        $teacher->classes()->syncWithoutDetaching([$classe->id]);

        return redirect()->back()->with('success', 'Affectation enregistrée.')->withFragment('affectations');
    }

    public function affectationTeacherClasseDestroy(Teacher $teacher, Classe $classe)
    {
        $instId = $this->getInstitution()->id;
        $this->assertBelongsToInstitution($teacher, $instId, 'Enseignant');
        $this->assertBelongsToInstitution($classe, $instId, 'Classe');
        $teacher->classes()->detach($classe->id);

        return redirect()->back()->with('success', 'Affectation supprimée.')->withFragment('affectations');
    }

    public function affectationTeacherNiveau(Request $request)
    {
        $institution = $this->getInstitution();
        $instId      = $institution->id;

        $data = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'niveau_id'  => 'required|exists:niveaux,id',
        ]);

        $teacher = Teacher::where('id', $data['teacher_id'])->where('institution_id', $instId)->firstOrFail();

        // ✅ Vérifier que le niveau appartient à cette institution
        $niveau = Niveau::where('id', $data['niveau_id'])->where('institution_id', $instId)->firstOrFail();

        $teacher->niveaux()->syncWithoutDetaching([$niveau->id]);

        return redirect()->back()->with('success', 'Affectation niveau enregistrée.')->withFragment('affectations');
    }

    public function affectationTeacherNiveauDestroy(Teacher $teacher, Niveau $niveau)
    {
        $instId = $this->getInstitution()->id;
        $this->assertBelongsToInstitution($teacher, $instId, 'Enseignant');
        // ✅ Vérifier que le niveau appartient à cette institution
        $this->assertBelongsToInstitution($niveau, $instId, 'Niveau');
        $teacher->niveaux()->detach($niveau->id);

        return redirect()->back()->with('success', 'Affectation niveau supprimée.')->withFragment('affectations');
    }

    public function affectationEleveClasse(Request $request)
    {
        $instId = $this->getInstitution()->id;

        $data = $request->validate([
            'apprenant_id'    => 'required|integer|exists:apprenants,id',
            'classe_id'       => 'required|integer|exists:classes,id',
            'annee_academique'=> 'nullable|string|max:20',
        ]);

        $apprenant = Apprenant::where('id', $data['apprenant_id'])->where('institution_id', $instId)->firstOrFail();
        $classe    = Classe::where('id', $data['classe_id'])->where('institution_id', $instId)->firstOrFail();
        $annee     = $data['annee_academique'] ?? $this->getInstitution()->academic_year ?? date('Y').'-'.(date('Y') + 1);

        $dejaAffecte = $apprenant->classes()
            ->wherePivot('class_id', $classe->id)
            ->wherePivot('annee_academique', $annee)
            ->exists();

        if ($dejaAffecte) {
            return redirect()->back()->with('error', "{$apprenant->prenom} {$apprenant->nom} est déjà inscrit(e) en {$classe->name}.");
        }

        DB::transaction(function () use ($apprenant, $classe, $annee) {
            $apprenant->classes()
                ->wherePivot('annee_academique', $annee)
                ->wherePivot('statut', 'actif')
                ->each(fn ($c) => $apprenant->classes()->updateExistingPivot($c->id, ['statut' => 'transfere']));
            $apprenant->classes()->attach($classe->id, [
                'annee_academique' => $annee,
                'date_inscription' => now()->toDateString(),
                'statut'           => 'actif',
            ]);
            $apprenant->update(['class_id' => $classe->id]);
        });

        return redirect()->back()->with('success', "{$apprenant->prenom} {$apprenant->nom} inscrit(e) en {$classe->name}.");
    }

    public function affectationEleveClasseDestroy(Apprenant $apprenant)
    {
        $institution = $this->getInstitution();
        $this->assertBelongsToInstitution($apprenant, $institution->id, 'Apprenant');
        $name  = "{$apprenant->prenom} {$apprenant->nom}";
        $annee = $institution->academic_year ?? date('Y').'-'.(date('Y') + 1);

        DB::transaction(function () use ($apprenant, $annee) {
            $apprenant->classes()
                ->wherePivot('annee_academique', $annee)
                ->wherePivot('statut', 'actif')
                ->each(fn ($c) => $apprenant->classes()->updateExistingPivot($c->id, ['statut' => 'transfere']));
            $apprenant->update(['class_id' => null]);
        });

        return redirect()->back()->with('success', "{$name} retiré(e) de sa classe.");
    }

    /* ── AJAX SEARCH ── */
    public function searchsApprenants(Request $request)
    {
        $instId = $this->getInstitution()->id;
        $query  = Apprenant::where('institution_id', $instId)
            ->select('id', 'nom', 'prenom', 'matricule', 'class_id', 'niveau_id', 'filiere_id')
            ->with(['classe:id,name', 'niveau:id,name']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn ($s) => $s->where('nom', 'like', "%{$q}%")->orWhere('prenom', 'like', "%{$q}%")->orWhere('matricule', 'like', "%{$q}%"));
        }
        if ($request->filled('niveau_id')) {
            $query->where('niveau_id', $request->niveau_id);
        }
        if ($request->filled('filiere_id')) {
            $query->where('filiere_id', $request->filiere_id);
        }
        if ($request->filled('classe_id')) {
            $query->where('class_id', $request->classe_id);
        }

        $total = $query->count();
        $data  = $query->orderBy('nom')->orderBy('prenom')->limit(50)->get()
            ->map(fn ($a) => [
                'id'       => $a->id,
                'nom'      => $a->nom,
                'prenom'   => $a->prenom,
                'matricule'=> $a->matricule,
                'classe'   => optional($a->classe)->name,
                'niveau'   => optional($a->niveau)->name,
                'label'    => $a->prenom.' '.$a->nom
                              .(optional($a->classe)->name ? ' — '.optional($a->classe)->name : '')
                              .($a->matricule ? ' ('.$a->matricule.')' : ''),
            ]);

        return response()->json(['data' => $data, 'total' => $total]);
    }

    public function searchTeachers(Request $request)
    {
        $instId = $this->getInstitution()->id;
        $query  = Teacher::where('institution_id', $instId)->select('id', 'nom', 'prenom', 'specialite', 'matricule');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn ($s) => $s->where('nom', 'like', "%{$q}%")->orWhere('prenom', 'like', "%{$q}%")->orWhere('specialite', 'like', "%{$q}%")->orWhere('matricule', 'like', "%{$q}%"));
        }

        $total = $query->count();
        $data  = $query->orderBy('nom')->orderBy('prenom')->limit(50)->get()
            ->map(fn ($t) => [
                'id'        => $t->id,
                'nom'       => $t->nom,
                'prenom'    => $t->prenom,
                'specialite'=> $t->specialite,
                'matricule' => $t->matricule,
                'label'     => $t->prenom.' '.$t->nom.($t->specialite ? ' ('.$t->specialite.')' : ''),
            ]);

        return response()->json(['data' => $data, 'total' => $total]);
    }

    public function searchClasses(Request $request)
    {
        $instId = $this->getInstitution()->id;
        $query  = Classe::where('institution_id', $instId)->withCount('apprenants')->with(['niveau:id,name', 'filiere:id,name']);

        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%');
        }
        if ($request->filled('niveau_id')) {
            $query->where('niveau_id', $request->niveau_id);
        }
        if ($request->filled('filiere_id')) {
            $query->where('filiere_id', $request->filiere_id);
        }

        $total = $query->count();
        $data  = $query->orderBy('name')->limit(50)->get()
            ->map(fn ($c) => [
                'id'     => $c->id,
                'name'   => $c->name,
                'niveau' => optional($c->niveau)->name,
                'filiere'=> optional($c->filiere)->name,
                'count'  => $c->apprenants_count,
                'label'  => $c->name
                             .(optional($c->niveau)->name ? ' · '.optional($c->niveau)->name : '')
                             .(optional($c->filiere)->name ? ' · '.optional($c->filiere)->name : '')
                             .' ('.$c->apprenants_count.' élèves)',
            ]);

        return response()->json(['data' => $data, 'total' => $total]);
    }

}
