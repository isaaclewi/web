<?php

namespace App\Http\Controllers;

use App\Models\Apprenant;
use App\Models\Classe;
use App\Models\Filiere;
use App\Models\FinancialRecord;
use App\Models\Grade;
use App\Models\Institution;
use App\Models\Niveau;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Services\WelcomeMailService;
use App\Traits\MatriculeGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminDashboardController extends Controller
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

    /* ══════════════════════════════════════════════════════════
     | DASHBOARD
     ══════════════════════════════════════════════════════════ */

    public function index()
    {
        $user = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();
        $instId = $institution->id;

        /* ================= KPI ================= */
        $totalStudents = Apprenant::where('institution_id', $instId)->count();
        $activeStudents = Apprenant::where('institution_id', $instId)->where('status', 1)->count();
        $totalTeachers = Teacher::where('institution_id', $instId)->count();
        $recentStudents = Apprenant::with('classe')
            ->where('institution_id', $instId)
            ->latest()
            ->take(5)
            ->get();

        /* ================= ACTIVITÉ MENSUELLE ================= */
        $monthlyActivity = Grade::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereHas('evaluation.subject', fn ($q) => $q->where('institution_id', $instId))
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $months = [];
        $vals   = [];

        for ($i = 5; $i >= 0; $i--) {
            $date     = now()->subMonths($i);
            $m        = $date->month;
            $months[] = $date->format('M');
            $vals[]   = $monthlyActivity[$m] ?? 0;
        }

        /* ================= RÉPARTITION ================= */
        $grades = Grade::whereHas('evaluation.subject', fn ($q) => $q->where('institution_id', $instId))->get();
        $total  = $grades->count();

        $resultats = [
            ['label' => 'Excellent (16–20)',    'pct' => $total ? round($grades->filter(fn ($g) => $g->score >= 16)->count() / $total * 100) : 0,                                      'color' => '#10b981'],
            ['label' => 'Bien (14–16)',          'pct' => $total ? round($grades->filter(fn ($g) => $g->score >= 14 && $g->score < 16)->count() / $total * 100) : 0,                    'color' => '#3b82f6'],
            ['label' => 'Assez bien (12–14)',    'pct' => $total ? round($grades->filter(fn ($g) => $g->score >= 12 && $g->score < 14)->count() / $total * 100) : 0,                    'color' => '#f59e0b'],
            ['label' => 'Passable (10–12)',      'pct' => $total ? round($grades->filter(fn ($g) => $g->score >= 10 && $g->score < 12)->count() / $total * 100) : 0,                    'color' => '#f97316'],
            ['label' => 'Insuffisant (<10)',     'pct' => $total ? round($grades->filter(fn ($g) => $g->score < 10)->count() / $total * 100) : 0,                                       'color' => '#ef4444'],
        ];

        /* ================= ÉVÉNEMENTS ================= */
        $events = \App\Models\Evaluation::with(['subject.teacher'])
            ->whereHas('subject', fn ($q) => $q->where('institution_id', $instId))
            ->whereDate('date', '>=', now())
            ->orderBy('date', 'asc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'user', 'institution', 'totalStudents', 'activeStudents', 'totalTeachers',
            'recentStudents', 'months', 'vals', 'resultats', 'events',
        ));
    }

    /* ══════════════════════════════════════════════════════════
     | GESTION DES UTILISATEURS
     ══════════════════════════════════════════════════════════ */
    public function userManagement()
    {
        $user        = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();
        $instId      = $institution->id;

        $allUsers = User::where('institution_id', $instId)
            ->where('id', '!=', $user->id)
            ->with(['roles', 'teacher', 'apprenant', 'staff.administrativeUnit'])
            ->latest()
            ->get();

        $roleNames = ['admin', 'teacher', 'apprenant', 'comptable', 'surveillant', 'directeur'];
        $byRole    = [];
        foreach ($roleNames as $r) {
            $byRole[$r] = $allUsers->filter(fn ($u) => $u->roles->pluck('name')->contains($r))->count();
        }

        $stats = ['total' => $allUsers->count(), 'by_role' => $byRole];

        $page      = request()->get('page', 1);
        $perPage   = 15;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $allUsers->forPage($page, $perPage)->values(),
            $allUsers->count(), $perPage, $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // ✅ Niveaux filtrés par institution
        $niveaux            = Niveau::where('institution_id', $instId)->orderBy('name')->get();
        $filieres           = Filiere::where('institution_id', $instId)->orderBy('name')->get();
        $administrativeUnits = \App\Models\AdministrativeUnit::where('institution_id', $instId)->orderBy('name')->get();

        return view('admin.users', compact(
            'user', 'institution', 'stats', 'niveaux', 'filieres', 'administrativeUnits',
        ) + ['users' => $paginated]);
    }

    public function userStore(Request $request)
    {
        $authUser    = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();
        $instId      = $institution->id;
        $role        = $request->input('role');

        $base = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:admin,teacher,apprenant,comptable,surveillant,directeur',
        ]);

        if ($role === 'teacher') {
            $profileData = $request->validate([
                'nom'             => 'required|string|max:100',
                'prenom'          => 'required|string|max:100',
                'matricule'       => 'nullable|string|max:50',
                'sexe'            => 'nullable|in:M,F',
                'telephone'       => 'nullable|string|max:30',
                'specialite'      => 'nullable|string|max:100',
                'type_contrat'    => 'nullable|in:CDI,CDD,vacataire,benevole',
                'date_recrutement'=> 'nullable|date',
            ]);
        } elseif ($role === 'apprenant') {
            $profileData = $request->validate([
                'nom'             => 'required|string|max:100',
                'prenom'          => 'required|string|max:100',
                'matricule'       => 'nullable|string|max:50',
                'date_naissance'  => 'nullable|date',
                'sexe'            => 'nullable|in:M,F',
                // ✅ niveau_id scopé à l'institution
                'niveau_id'       => ['nullable', Rule::exists('niveaux', 'id')->where('institution_id', $instId)],
                'filiere_id'      => ['nullable', Rule::exists('filieres', 'id')->where('institution_id', $instId)],
                'annee_academique'=> 'nullable|string|max:20',
            ]);
        } else {
            $profileData = $request->validate([
                'nom'                    => 'required|string|max:100',
                'prenom'                 => 'required|string|max:100',
                'matricule'              => 'nullable|string|max:50',
                'telephone'              => 'nullable|string|max:30',
                'poste'                  => 'nullable|string|max:100',
                'administrative_unit_id' => 'nullable|exists:administrative_units,id',
            ]);
        }

        $passwordRaw = $base['password'];
        $newUser     = null;

        DB::transaction(function () use ($base, $profileData, $institution, $instId, $role, $passwordRaw, &$newUser) {
            $newUser = User::create([
                'name'           => $base['name'],
                'email'          => $base['email'],
                'password'       => Hash::make($passwordRaw),
                'institution_id' => $instId,
                'status'         => 1,
            ]);

            \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
            $newUser->assignRole($role);

            if ($role === 'teacher') {
                if (empty($profileData['matricule'])) {
                    $profileData['matricule'] = $this->generateTeacherMatricule($institution, $instId);
                }
                \App\Models\Teacher::create(array_merge($profileData, [
                    'user_id' => $newUser->id, 'institution_id' => $instId, 'status' => 1,
                ]));
            } elseif ($role === 'apprenant') {
                if (empty($profileData['matricule'])) {
                    $profileData['matricule'] = $this->generateApprenantMatricule($institution, $instId);
                }
                \App\Models\Apprenant::create(array_merge($profileData, [
                    'user_id'          => $newUser->id,
                    'institution_id'   => $instId,
                    'annee_academique' => $profileData['annee_academique'] ?? $institution->academic_year,
                    'password'         => Hash::make($passwordRaw),
                ]));
            } else {
                if (empty($profileData['matricule'])) {
                    $profileData['matricule'] = $this->generateStaffMatricule($institution, $instId);
                }
                $unitId = $profileData['administrative_unit_id'] ?? null;
                if (! $unitId) {
                    $unitId = \App\Models\AdministrativeUnit::firstOrCreate(
                        ['institution_id' => $instId, 'type' => 'direction'],
                        ['name' => 'Direction', 'status' => 1]
                    )->id;
                }
                \App\Models\Staff::create([
                    'user_id'                => $newUser->id,
                    'institution_id'         => $instId,
                    'administrative_unit_id' => $unitId,
                    'poste'                  => $profileData['poste'] ?? null,
                    'matricule'              => $profileData['matricule'],
                    'nom'                    => $profileData['nom'],
                    'prenom'                 => $profileData['prenom'],
                    'telephone'              => $profileData['telephone'] ?? null,
                    'email'                  => $newUser->email,
                    'status'                 => 1,
                ]);
            }
        });

        $roleLabel = match ($role) {
            'teacher'     => 'Enseignant',
            'apprenant'   => 'Apprenant',
            'comptable'   => 'Comptable',
            'surveillant' => 'Surveillant',
            'directeur'   => 'Directeur',
            default       => ucfirst($role),
        };
        $matricule = match ($role) {
            'teacher'   => $newUser->teacher?->matricule,
            'apprenant' => $newUser->apprenant?->matricule,
            default     => $newUser->staff?->matricule,
        };

        WelcomeMailService::send(
            email:       $newUser->email,
            prenom:      $profileData['prenom'] ?? $newUser->name,
            nom:         $profileData['nom'] ?? '',
            matricule:   $matricule,
            role:        $roleLabel,
            passwordRaw: $passwordRaw,
            institution: $institution->name,
        );

        return redirect()->back()->with('success', "Utilisateur « {$newUser->name} » créé avec succès. Un e-mail de bienvenue a été envoyé.");
    }

    public function userUpdate(Request $request, User $user)
    {
        $authUser = Auth::user();
        $this->assertSameInstitution($authUser, $user);
        $instId = $user->institution_id;

        $role = $user->roles->pluck('name')->first() ?? 'admin';

        $base = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);
        $user->update(['name' => $base['name'], 'email' => $base['email']]);

        if ($role === 'teacher') {
            $profileData = $request->validate([
                'nom'             => 'required|string|max:100',
                'prenom'          => 'required|string|max:100',
                'matricule'       => 'nullable|string|max:50',
                'sexe'            => 'nullable|in:M,F',
                'telephone'       => 'nullable|string|max:30',
                'specialite'      => 'nullable|string|max:100',
                'type_contrat'    => 'nullable|in:CDI,CDD,vacataire,benevole',
                'date_recrutement'=> 'nullable|date',
            ]);
            $user->teacher
                ? $user->teacher->update($profileData)
                : \App\Models\Teacher::create(array_merge($profileData, ['user_id' => $user->id, 'institution_id' => $instId, 'status' => 1]));

        } elseif ($role === 'apprenant') {
            $profileData = $request->validate([
                'nom'             => 'required|string|max:100',
                'prenom'          => 'required|string|max:100',
                'matricule'       => 'nullable|string|max:50',
                'date_naissance'  => 'nullable|date',
                'sexe'            => 'nullable|in:M,F',
                // ✅ niveau_id et filiere_id scopés à l'institution
                'niveau_id'       => ['nullable', Rule::exists('niveaux', 'id')->where('institution_id', $instId)],
                'filiere_id'      => ['nullable', Rule::exists('filieres', 'id')->where('institution_id', $instId)],
                'annee_academique'=> 'nullable|string|max:20',
            ]);
            $user->apprenant
                ? $user->apprenant->update($profileData)
                : \App\Models\Apprenant::create(array_merge($profileData, ['user_id' => $user->id, 'institution_id' => $instId, 'password' => $user->password]));

        } else {
            $profileData = $request->validate([
                'nom'                    => 'required|string|max:100',
                'prenom'                 => 'required|string|max:100',
                'matricule'              => 'nullable|string|max:50',
                'telephone'              => 'nullable|string|max:30',
                'poste'                  => 'nullable|string|max:100',
                'administrative_unit_id' => 'nullable|exists:administrative_units,id',
            ]);
            if ($user->staff) {
                $user->staff->update($profileData);
            } else {
                $unitId = $profileData['administrative_unit_id']
                    ?? \App\Models\AdministrativeUnit::firstOrCreate(
                        ['institution_id' => $instId, 'type' => 'direction'],
                        ['name' => 'Direction', 'status' => 1]
                    )->id;
                \App\Models\Staff::create(array_merge($profileData, [
                    'user_id'                => $user->id,
                    'institution_id'         => $instId,
                    'administrative_unit_id' => $unitId,
                    'email'                  => $user->email,
                    'status'                 => 1,
                ]));
            }
        }

        return redirect()->back()->with('success', 'Utilisateur mis à jour.');
    }

    public function userResetPassword(Request $request, User $user)
    {
        $this->assertSameInstitution(Auth::user(), $user);
        $request->validate(['password' => 'required|min:8|confirmed']);
        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->back()->with('success', "Mot de passe de « {$user->name} » réinitialisé.");
    }

    public function userToggleStatus(Request $request, User $user)
    {
        $this->assertSameInstitution(Auth::user(), $user);
        $request->validate(['status' => 'required|in:active,inactive,blocked']);
        $user->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Statut mis à jour.');
    }

    public function userDestroy(User $user)
    {
        $this->assertSameInstitution(Auth::user(), $user);
        $user->delete();

        return redirect()->back()->with('success', 'Utilisateur supprimé.');
    }

    /* ══════════════════════════════════════════════════════════
     | ÉTABLISSEMENT
     ══════════════════════════════════════════════════════════ */
    public function institutionSettings()
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
            $teacherClasseAffectations = \Illuminate\Support\Facades\DB::table('class_teacher')
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

        return view('admin.academic', compact(
            'user', 'institution', 'sections', 'filieres', 'matieres', 'classes',
            'teachers', 'apprenants', 'teacherClasseAffectations', 'eleveClasseAffectations',
            'academicYear', 'stats', 'validations', 'schedules', 'bulletins', 'resultPublications'
        ));
    }

    public function institutionUpdate(Request $request)
{
    $institution = $this->getInstitution();

    $data = $request->validate([/* ... */]);

    if ($request->hasFile('logo')) {
        // Supprimer l'ancien logo
        if ($institution->logo) {
            Storage::disk('root_storage')->delete($institution->logo);
        }
        $data['logo'] = $request->file('logo')->store('logos/institutions', 'root_storage');
    }

    $data['autorisation_etat'] = (bool) $request->input('autorisation_etat', 0);
    unset($data['code']);
    $institution->update($data);

    return redirect()->back()->with('success', 'Établissement mis à jour.');
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

        return view('admin.academic', compact(
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
    public function searchApprenants(Request $request)
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

    /* ══════════════════════════════════════════════════════════
     | PAGES SIMPLES
     ══════════════════════════════════════════════════════════ */
    public function administrative()
    {
        $user        = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();
        $instId      = $institution->id;

        return view('admin.administrative', compact('user', 'institution') + [
            'totalStudents'  => Apprenant::where('institution_id', $instId)->count(),
            'activeStudents' => Apprenant::where('institution_id', $instId)->where('status', 1)->count(),
            'totalTeachers'  => Teacher::where('institution_id', $instId)->count(),
        ]);
    }

    /* ══════════════════════════════════════════════════════════
     | APPRENANTS
     ══════════════════════════════════════════════════════════ */
    public function apprenants()
    {
        $user        = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();
        $instId      = $institution->id;

        $apprenants = Apprenant::where('institution_id', $instId)
            ->with(['niveau', 'filiere', 'classe'])->latest()->paginate(20);

        $stats = [
            'total'   => Apprenant::where('institution_id', $instId)->count(),
            'active'  => Apprenant::where('institution_id', $instId)->where('status', 1)->count(),
            'garcons' => Apprenant::where('institution_id', $instId)->where('sexe', 'M')->count(),
            'filles'  => Apprenant::where('institution_id', $instId)->where('sexe', 'F')->count(),
        ];

        return view('admin.apprenants', compact('user', 'institution', 'apprenants', 'stats') + [
            // ✅ Niveaux filtrés par institution
            'niveaux'  => Niveau::where('institution_id', $instId)->orderBy('name')->get(),
            'filieres' => Filiere::where('institution_id', $instId)->orderBy('name')->get(),
            'classes'  => Classe::where('institution_id', $instId)->orderBy('name')->get(),
        ]);
    }

    public function apprenantStore(Request $request)
    {
        $user        = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();
        $instId      = $institution->id;

        $data = $request->validate([
            'nom'             => 'required|string|max:100',
            'prenom'          => 'required|string|max:100',
            'date_naissance'  => 'nullable|date',
            'sexe'            => 'nullable|in:M,F',
            'matricule'       => 'nullable|string|max:50',
            // ✅ Scopés à l'institution
            'niveau_id'       => ['nullable', Rule::exists('niveaux', 'id')->where('institution_id', $instId)],
            'filiere_id'      => ['nullable', Rule::exists('filieres', 'id')->where('institution_id', $instId)],
            'class_id'        => ['nullable', Rule::exists('classes', 'id')->where('institution_id', $instId)],
            'annee_academique'=> 'nullable|string|max:20',
            'email'           => 'nullable|email|unique:users,email',
            'password'        => 'nullable|min:8',
        ]);

        if (empty($data['matricule'])) {
            $data['matricule'] = $this->generateApprenantMatricule($institution, $instId);
        }

        $passwordRaw = $data['password'] ?? 'password123';
        $emailDest   = $data['email'] ?? null;

        DB::transaction(function () use ($data, $institution, $instId, $passwordRaw) {
            $userId = null;
            if (! empty($data['email'])) {
                $newUser = User::create([
                    'name'           => $data['prenom'].' '.$data['nom'],
                    'email'          => $data['email'],
                    'password'       => Hash::make($passwordRaw),
                    'institution_id' => $instId,
                    'status'         => 1,
                ]);
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'apprenant', 'guard_name' => 'web']);
                $newUser->assignRole('apprenant');
                $userId = $newUser->id;
            }
            Apprenant::create([
                'user_id'          => $userId,
                'institution_id'   => $instId,
                'niveau_id'        => $data['niveau_id'] ?? null,
                'filiere_id'       => $data['filiere_id'] ?? null,
                'class_id'         => $data['class_id'] ?? null,
                'matricule'        => $data['matricule'],
                'nom'              => $data['nom'],
                'prenom'           => $data['prenom'],
                'date_naissance'   => $data['date_naissance'] ?? null,
                'sexe'             => $data['sexe'] ?? null,
                'annee_academique' => $data['annee_academique'] ?? $institution->academic_year,
                'status'           => 1,
                'password'         => Hash::make($passwordRaw),
            ]);
        });

        if ($emailDest) {
            WelcomeMailService::sendToApprenant(
                email:       $emailDest,
                prenom:      $data['prenom'],
                nom:         $data['nom'],
                matricule:   $data['matricule'],
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
            'nom'             => 'required|string|max:100',
            'prenom'          => 'required|string|max:100',
            'date_naissance'  => 'nullable|date',
            'sexe'            => 'nullable|in:M,F',
            'matricule'       => 'nullable|string|max:50',
            'status'          => 'nullable|in:0,1',
            // ✅ Scopés à l'institution
            'niveau_id'       => ['nullable', Rule::exists('niveaux', 'id')->where('institution_id', $instId)],
            'filiere_id'      => ['nullable', Rule::exists('filieres', 'id')->where('institution_id', $instId)],
            'class_id'        => ['nullable', Rule::exists('classes', 'id')->where('institution_id', $instId)],
            'annee_academique'=> 'nullable|string|max:20',
        ]);

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
        $ids    = array_filter(explode(',', $request->input('ids', '')));
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
        $query  = Apprenant::where('institution_id', $instId)->with(['niveau', 'filiere', 'classe']);
        if ($request->filled('ids')) {
            $query->whereIn('id', array_filter(explode(',', $request->ids)));
        }
        $apprenants = $query->get();
        $headers    = ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="apprenants_export_'.now()->format('Ymd_His').'.csv"'];

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
        $instId      = $institution->id;
        $request->validate([
            'csv_file'        => 'required|file|mimes:csv,txt,xlsx|max:2048',
            'default_class_id'=> 'nullable|exists:classes,id',
        ]);

        if ($request->filled('default_class_id')) {
            Classe::where('id', $request->default_class_id)->where('institution_id', $instId)->firstOrFail();
        }

        $rows    = array_map('str_getcsv', file($request->file('csv_file')->getRealPath()));
        $header  = array_map('trim', array_shift($rows));
        $created = $skipped = 0;

        DB::transaction(function () use ($rows, $header, $institution, $instId, $request, &$created, &$skipped) {
            foreach ($rows as $row) {
                if (count($row) < 2) { $skipped++; continue; }
                $line = array_combine($header, array_map('trim', $row));
                if (! empty($line['matricule']) && Apprenant::where('institution_id', $instId)->where('matricule', $line['matricule'])->exists()) {
                    $skipped++; continue;
                }
                $matricule = $line['matricule'] ?? null;
                if (empty($matricule)) {
                    $matricule = $this->generateApprenantMatricule($institution, $instId);
                }
                Apprenant::create([
                    'institution_id'   => $instId,
                    'nom'              => $line['nom'] ?? '',
                    'prenom'           => $line['prenom'] ?? '',
                    'date_naissance'   => ! empty($line['date_naissance']) ? $line['date_naissance'] : null,
                    'sexe'             => $line['sexe'] ?? null,
                    'matricule'        => $matricule,
                    'niveau_id'        => $line['niveau_id'] ?? null,
                    'filiere_id'       => $line['filiere_id'] ?? null,
                    'class_id'         => $request->default_class_id ?? ($line['class_id'] ?? null),
                    'annee_academique' => $line['annee_academique'] ?? $institution->academic_year,
                    'status'           => 1,
                    'password'         => Hash::make('password123'),
                ]);
                $created++;
            }
        });

        return redirect()->back()->with('success', "{$created} apprenant(s) importé(s), {$skipped} ignoré(s).");
    }

    /* ══════════════════════════════════════════════════════════
     | STAFF
     ══════════════════════════════════════════════════════════ */
    public function staff()
    {
        $user        = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();
        $instId      = $institution->id;

        $staffMembers = \App\Models\Staff::where('institution_id', $instId)
            ->with(['user', 'administrativeUnit'])->latest()->paginate(20);
        $stats = [
            'total'    => \App\Models\Staff::where('institution_id', $instId)->count(),
            'actifs'   => \App\Models\Staff::where('institution_id', $instId)->where('status', 1)->count(),
            'inactifs' => \App\Models\Staff::where('institution_id', $instId)->where('status', 0)->count(),
        ];
        $administrativeUnits = \App\Models\AdministrativeUnit::where('institution_id', $instId)->orderBy('name')->get();

        return view('admin.Staff', compact('user', 'institution', 'staffMembers', 'stats', 'administrativeUnits'));
    }

    public function staffStore(Request $request)
    {
        $institution = $this->getInstitution();
        $instId      = $institution->id;

        $data = $request->validate([
            'nom'                    => 'required|string|max:100',
            'prenom'                 => 'required|string|max:100',
            'matricule'              => 'nullable|string|max:50|unique:staff,matricule',
            'telephone'              => 'nullable|string|max:30',
            'poste'                  => 'nullable|string|max:100',
            'administrative_unit_id' => 'nullable|exists:administrative_units,id',
            'email'                  => 'nullable|email|unique:users,email',
            'password'               => 'nullable|min:8|confirmed',
        ]);

        if (empty($data['matricule'])) {
            $data['matricule'] = $this->generateStaffMatricule($institution, $instId);
        }

        $passwordRaw = $data['password'] ?? 'password123';
        $emailDest   = $data['email'] ?? null;
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
                    'name'           => $data['prenom'].' '.$data['nom'],
                    'email'          => $data['email'],
                    'password'       => Hash::make($passwordRaw),
                    'institution_id' => $instId,
                    'status'         => 1,
                ]);
                $userId = $newUser->id;
            }

            $staffRecord = \App\Models\Staff::create([
                'user_id'                => $userId,
                'institution_id'         => $instId,
                'administrative_unit_id' => $unitId,
                'nom'                    => $data['nom'],
                'prenom'                 => $data['prenom'],
                'matricule'              => $data['matricule'],
                'telephone'              => $data['telephone'] ?? null,
                'poste'                  => $data['poste'] ?? null,
                'email'                  => $data['email'] ?? null,
                'status'                 => 1,
            ]);
        });

        if ($emailDest) {
            WelcomeMailService::sendToStaff(
                email:       $emailDest,
                prenom:      $data['prenom'],
                nom:         $data['nom'],
                matricule:   $data['matricule'],
                passwordRaw: $passwordRaw,
                institution: $institution->name,
                role:        $data['poste'] ?? 'Personnel administratif',
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
            'nom'                    => 'required|string|max:100',
            'prenom'                 => 'required|string|max:100',
            'matricule'              => 'nullable|string|max:50|unique:staff,matricule,'.$staff->id,
            'telephone'              => 'nullable|string|max:30',
            'poste'                  => 'nullable|string|max:100',
            'administrative_unit_id' => 'nullable|exists:administrative_units,id',
            'email'                  => 'nullable|email|unique:users,email,'.($staff->user_id ?? 'NULL'),
        ]);
        $staff->update([
            'nom'                    => $data['nom'],
            'prenom'                 => $data['prenom'],
            'matricule'              => $data['matricule'] ?? $staff->matricule,
            'telephone'              => $data['telephone'] ?? null,
            'poste'                  => $data['poste'] ?? null,
            'administrative_unit_id' => $data['administrative_unit_id'] ?? $staff->administrative_unit_id,
            'email'                  => $data['email'] ?? $staff->email,
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

    /* ══════════════════════════════════════════════════════════
     | ENSEIGNANTS
     ══════════════════════════════════════════════════════════ */
    public function teachers()
    {
        $user        = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();
        $instId      = $institution->id;

        $teachers = Teacher::where('institution_id', $instId)
            ->with(['user', 'classes', 'niveaux', 'filieres'])->latest()->paginate(20);
        $stats = [
            'total'     => Teacher::where('institution_id', $instId)->count(),
            'active'    => Teacher::where('institution_id', $instId)->where('status', 1)->count(),
            'hommes'    => Teacher::where('institution_id', $instId)->where('sexe', 'M')->count(),
            'femmes'    => Teacher::where('institution_id', $instId)->where('sexe', 'F')->count(),
            'cdi'       => Teacher::where('institution_id', $instId)->where('type_contrat', 'CDI')->count(),
            'vacataire' => Teacher::where('institution_id', $instId)->where('type_contrat', 'vacataire')->count(),
        ];

        return view('admin.teachers', compact('user', 'institution', 'teachers', 'stats') + [
            // ✅ Niveaux filtrés par institution
            'niveaux'  => Niveau::where('institution_id', $instId)->orderBy('name')->get(),
            'filieres' => Filiere::where('institution_id', $instId)->orderBy('name')->get(),
            'classes'  => Classe::where('institution_id', $instId)->orderBy('name')->get(),
        ]);
    }

    public function teacherStore(Request $request)
    {
        $institution = $this->getInstitution();
        $instId      = $institution->id;

        $data = $request->validate([
            'nom'             => 'required|string|max:100',
            'prenom'          => 'required|string|max:100',
            'matricule'       => 'nullable|string|max:50',
            'sexe'            => 'nullable|in:M,F',
            'telephone'       => 'nullable|string|max:30',
            'specialite'      => 'nullable|string|max:100',
            'type_contrat'    => 'nullable|in:CDI,CDD,vacataire,benevole',
            'date_recrutement'=> 'nullable|date',
            'niveaux'         => 'nullable|array',
            'niveaux.*'       => 'exists:niveaux,id',
            'filieres'        => 'nullable|array',
            'filieres.*'      => 'exists:filieres,id',
            'classes'         => 'nullable|array',
            'classes.*'       => 'exists:classes,id',
            'email'           => 'nullable|email|unique:users,email',
            'password'        => 'nullable|min:8|confirmed',
        ]);

        // ✅ Vérifier que les niveaux appartiennent à cette institution
        if (! empty($data['niveaux'])) {
            $count = Niveau::whereIn('id', $data['niveaux'])->where('institution_id', $instId)->count();
            if ($count !== count($data['niveaux'])) {
                abort(403, "Certains niveaux n'appartiennent pas à votre établissement.");
            }
        }
        if (! empty($data['classes'])) {
            $count = Classe::whereIn('id', $data['classes'])->where('institution_id', $instId)->count();
            if ($count !== count($data['classes'])) {
                abort(403, "Certaines classes n'appartiennent pas à votre établissement.");
            }
        }
        if (! empty($data['filieres'])) {
            $count = Filiere::whereIn('id', $data['filieres'])->where('institution_id', $instId)->count();
            if ($count !== count($data['filieres'])) {
                abort(403, "Certaines filières n'appartiennent pas à votre établissement.");
            }
        }

        if (empty($data['matricule'])) {
            $data['matricule'] = $this->generateTeacherMatricule($institution, $instId);
        }

        $passwordRaw = $data['password'] ?? 'password123';
        $emailDest   = $data['email'] ?? null;

        DB::transaction(function () use ($data, $instId, $passwordRaw) {
            $userId    = null;
            $userEmail = $data['email'] ?? null;
            if ($userEmail) {
                $newUser = User::create([
                    'name'           => $data['prenom'].' '.$data['nom'],
                    'email'          => $userEmail,
                    'password'       => Hash::make($passwordRaw),
                    'institution_id' => $instId,
                    'status'         => 1,
                ]);
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
                $newUser->assignRole('teacher');
                $userId = $newUser->id;
            }
            $teacher = Teacher::create([
                'user_id'          => $userId,
                'institution_id'   => $instId,
                'matricule'        => $data['matricule'],
                'nom'              => $data['nom'],
                'prenom'           => $data['prenom'],
                'sexe'             => $data['sexe'] ?? null,
                'telephone'        => $data['telephone'] ?? null,
                'email'            => $userEmail,
                'specialite'       => $data['specialite'] ?? null,
                'type_contrat'     => $data['type_contrat'] ?? null,
                'date_recrutement' => $data['date_recrutement'] ?? null,
                'status'           => 1,
            ]);
            if (! empty($data['niveaux'])) { $teacher->niveaux()->sync($data['niveaux']); }
            if (! empty($data['filieres'])) { $teacher->filieres()->sync($data['filieres']); }
            if (! empty($data['classes'])) { $teacher->classes()->sync($data['classes']); }
        });

        if ($emailDest) {
            WelcomeMailService::sendToTeacher(
                email:       $emailDest,
                prenom:      $data['prenom'],
                nom:         $data['nom'],
                matricule:   $data['matricule'],
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
            'nom'             => 'required|string|max:100',
            'prenom'          => 'required|string|max:100',
            'matricule'       => 'nullable|string|max:50',
            'sexe'            => 'nullable|in:M,F',
            'telephone'       => 'nullable|string|max:30',
            'specialite'      => 'nullable|string|max:100',
            'type_contrat'    => 'nullable|in:CDI,CDD,vacataire,benevole',
            'date_recrutement'=> 'nullable|date',
            'status'          => 'nullable|in:0,1',
            'niveaux'         => 'nullable|array',
            'niveaux.*'       => 'exists:niveaux,id',
            'filieres'        => 'nullable|array',
            'filieres.*'      => 'exists:filieres,id',
            'classes'         => 'nullable|array',
            'classes.*'       => 'exists:classes,id',
            'name'            => 'nullable|string|max:255',
            'email'           => 'nullable|email|unique:users,email,'.($teacher->user_id ?? 'NULL'),
        ]);

        // ✅ Vérifier que les niveaux appartiennent à cette institution
        if (! empty($data['niveaux'])) {
            $count = Niveau::whereIn('id', $data['niveaux'])->where('institution_id', $instId)->count();
            if ($count !== count($data['niveaux'])) {
                abort(403, "Certains niveaux n'appartiennent pas à votre établissement.");
            }
        }
        if (! empty($data['classes'])) {
            $count = Classe::whereIn('id', $data['classes'])->where('institution_id', $instId)->count();
            if ($count !== count($data['classes'])) {
                abort(403, "Certaines classes n'appartiennent pas à votre établissement.");
            }
        }
        if (! empty($data['filieres'])) {
            $count = Filiere::whereIn('id', $data['filieres'])->where('institution_id', $instId)->count();
            if ($count !== count($data['filieres'])) {
                abort(403, "Certaines filières n'appartiennent pas à votre établissement.");
            }
        }

        $teacher->update([
            'nom'              => $data['nom'],
            'prenom'           => $data['prenom'],
            'matricule'        => $data['matricule'] ?? $teacher->matricule,
            'sexe'             => $data['sexe'] ?? $teacher->sexe,
            'telephone'        => $data['telephone'] ?? null,
            'specialite'       => $data['specialite'] ?? null,
            'type_contrat'     => $data['type_contrat'] ?? null,
            'date_recrutement' => $data['date_recrutement'] ?? null,
            'status'           => $data['status'] ?? $teacher->status,
        ]);
        $teacher->niveaux()->sync($data['niveaux'] ?? []);
        $teacher->filieres()->sync($data['filieres'] ?? []);
        $teacher->classes()->sync($data['classes'] ?? []);

        if ($teacher->user_id && $teacher->user) {
            $update = [];
            if (! empty($data['name'])) { $update['name'] = $data['name']; }
            if (! empty($data['email'])) { $update['email'] = $data['email']; }
            if (! empty($update)) { $teacher->user->update($update); }
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
     | FINANCES
     ══════════════════════════════════════════════════════════ */
    public function financial()
    {
        $user        = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();
        $instId      = $institution->id;

        $annee    = request('annee', $institution->academic_year ?? date('Y').'-'.(date('Y') + 1));
        $search   = request('search', '');
        $statut   = request('statut', '');
        $classeId = request('classe_id', '');
        $niveauId = request('niveau_id', '');
        $filiereId= request('filiere_id', '');

        $query = Apprenant::where('institution_id', $instId)
            ->with(['classe', 'filiere', 'niveau', 'financialRecords' => fn ($q) => $q->where('annee_academique', $annee)]);

        if ($search)   { $query->where(fn ($q) => $q->where('nom', 'like', "%{$search}%")->orWhere('prenom', 'like', "%{$search}%")->orWhere('matricule', 'like', "%{$search}%")); }
        if ($classeId) { $query->where('class_id', $classeId); }
        if ($niveauId) { $query->where('niveau_id', $niveauId); }
        if ($filiereId){ $query->where('filiere_id', $filiereId); }
        if ($statut)   { $query->whereHas('financialRecords', fn ($q) => $q->where('annee_academique', $annee)->where('statut', $statut)); }

        $apprenants      = $query->orderBy('nom')->paginate(25)->withQueryString();
        $statsAnnee      = Financialrecord::where('institution_id', $instId)->where('annee_academique', $annee)->selectRaw('COALESCE(SUM(montant_du),0) as total_du, COALESCE(SUM(montant_paye),0) as total_paye, COALESCE(SUM(montant_reste),0) as total_reste, COUNT(CASE WHEN statut="paye" THEN 1 END) as nb_payes, COUNT(CASE WHEN statut="partiel" THEN 1 END) as nb_partiels, COUNT(CASE WHEN statut="impaye" THEN 1 END) as nb_impayes')->first();
        $statsMois       = Financialrecord::where('institution_id', $instId)->where('annee_academique', $annee)->selectRaw('mois, mois_label, SUM(montant_du) as du, SUM(montant_paye) as paye')->groupBy('mois', 'mois_label')->orderBy('mois')->get();
        $recentPaiements = Financialrecord::where('institution_id', $instId)->where('annee_academique', $annee)->whereNotNull('date_paiement')->with(['apprenant', 'recordedBy'])->orderByDesc('date_paiement')->limit(10)->get();
        $anneesDispos    = Financialrecord::where('institution_id', $instId)->distinct()->pluck('annee_academique')->sort()->values();
        if (! $anneesDispos->contains($annee)) { $anneesDispos->prepend($annee); }
        $moisLabels = Financialrecord::moisLabels();

        return view('admin.financial', compact(
            'user', 'institution', 'apprenants', 'annee', 'search', 'statut',
            'classeId', 'niveauId', 'filiereId',
            'statsAnnee', 'statsMois', 'recentPaiements', 'anneesDispos', 'moisLabels',
        ) + [
            'classes'  => Classe::where('institution_id', $instId)->orderBy('name')->get(),
            // ✅ Niveaux filtrés par institution
            'niveaux'  => Niveau::where('institution_id', $instId)->orderBy('name')->get(),
            'filieres' => Filiere::where('institution_id', $instId)->orderBy('name')->get(),
        ]);
    }

    public function financialApprenant(Apprenant $apprenant)
    {
        $institution = $this->getInstitution();
        $this->assertBelongsToInstitution($apprenant, $institution->id, 'Apprenant');
        $user         = Auth::user();
        $annee        = request('annee', $institution->academic_year ?? date('Y').'-'.(date('Y') + 1));
        $moisLabels   = Financialrecord::moisLabels();
        $anneesDispos = Financialrecord::where('apprenant_id', $apprenant->id)->distinct()->pluck('annee_academique')->sort()->values();
        if (! $anneesDispos->contains($annee)) { $anneesDispos->prepend($annee); }
        $allRecords = Financialrecord::where('apprenant_id', $apprenant->id)->with(['recordedBy', 'validatedBy'])->orderBy('annee_academique')->orderBy('mois')->get();
        $records    = $allRecords->where('annee_academique', $annee)->keyBy('mois');
        $totaux     = ['du' => $records->sum('montant_du'), 'paye' => $records->sum('montant_paye'), 'reste' => $records->sum('montant_reste')];

        return view('admin.FinancialApprenant', compact('user', 'institution', 'apprenant', 'annee', 'anneesDispos', 'allRecords', 'records', 'moisLabels', 'totaux'));
    }

    public function financialStore(Request $request)
    {
        $institution = $this->getInstitution();
        $data        = $request->validate([
            'apprenant_id'    => 'required|exists:apprenants,id',
            'annee_academique'=> 'required|string|max:20',
            'mois'            => 'required|integer|between:1,12',
            'montant_du'      => 'required|numeric|min:0',
            'montant_paye'    => 'required|numeric|min:0',
            'date_paiement'   => 'nullable|date',
            'mode_paiement'   => 'nullable|in:especes,virement,mobile_money,cheque,autre',
            'reference'       => 'nullable|string|max:100',
            'notes'           => 'nullable|string|max:500',
        ]);
        $apprenant  = Apprenant::where('id', $data['apprenant_id'])->where('institution_id', $institution->id)->firstOrFail();
        $moisLabels = Financialrecord::moisLabels();
        $reste      = max(0, $data['montant_du'] - $data['montant_paye']);
        $statut     = 'impaye';
        if ($data['montant_du'] > 0 && $data['montant_paye'] >= $data['montant_du']) { $statut = 'paye'; }
        elseif ($data['montant_paye'] > 0) { $statut = 'partiel'; }

        Financialrecord::updateOrCreate(
            ['apprenant_id' => $apprenant->id, 'annee_academique' => $data['annee_academique'], 'mois' => $data['mois']],
            [
                'institution_id'  => $institution->id,
                'mois_label'      => $moisLabels[$data['mois']],
                'montant_du'      => $data['montant_du'],
                'montant_paye'    => $data['montant_paye'],
                'montant_reste'   => $reste,
                'statut'          => $statut,
                'date_paiement'   => $data['date_paiement'] ?? null,
                'mode_paiement'   => $data['mode_paiement'] ?? null,
                'reference'       => $data['reference'] ?? null,
                'notes'           => $data['notes'] ?? null,
                'recorded_by'     => Auth::id(),
                'recorded_at'     => now(),
            ]
        );

        return redirect()->back()->with('success', "Paiement de {$apprenant->prenom} {$apprenant->nom} ({$moisLabels[$data['mois']]}) enregistré.");
    }

    public function financialValidate(Financialrecord $record)
    {
        $this->assertBelongsToInstitution($record, $this->getInstitution()->id, 'Enregistrement');
        $record->update(['validated_by' => Auth::id(), 'validated_at' => now()]);

        return redirect()->back()->with('success', 'Enregistrement validé et signé.');
    }

    public function financialDestroy(Financialrecord $record)
    {
        $this->assertBelongsToInstitution($record, $this->getInstitution()->id, 'Enregistrement');
        $record->delete();

        return redirect()->back()->with('success', 'Enregistrement supprimé.');
    }

    public function financialExport(Request $request)
    {
        $instId  = $this->getInstitution()->id;
        $annee   = $request->get('annee', Auth::user()->institution->academic_year);
        $records = Financialrecord::where('institution_id', $instId)->where('annee_academique', $annee)
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

    /* ══════════════════════════════════════════════════════════
     | RAPPORTS
     ══════════════════════════════════════════════════════════ */
    public function rapports()
    {
        $user        = Auth::user() ?? redirect()->route('login')->send();
        $institution = $this->getInstitution();
        $instId      = $institution->id;
        $annee       = $institution->academic_year ?? date('Y').'-'.(date('Y') + 1);

        $totalApprenants  = Apprenant::where('institution_id', $instId)->count();
        $actifsApprenants = Apprenant::where('institution_id', $instId)->where('status', 1)->count();
        $garcons          = Apprenant::where('institution_id', $instId)->where('sexe', 'M')->count();
        $filles           = Apprenant::where('institution_id', $instId)->where('sexe', 'F')->count();

        $apprenantsByClasse = DB::table('apprenants')
            ->join('classes', 'apprenants.class_id', '=', 'classes.id')
            ->where('apprenants.institution_id', $instId)
            ->select('classes.name as classe', DB::raw('COUNT(*) as total'))
            ->groupBy('classes.id', 'classes.name')->orderByDesc('total')->get();

        // ✅ Filtre sur niveaux.institution_id
        $apprenantsByNiveau = DB::table('apprenants')
            ->join('niveaux', 'apprenants.niveau_id', '=', 'niveaux.id')
            ->where('apprenants.institution_id', $instId)
            ->where('niveaux.institution_id', $instId)
            ->select('niveaux.name as niveau', DB::raw('COUNT(*) as total'))
            ->groupBy('niveaux.id', 'niveaux.name')->orderByDesc('total')->get();

        $totalTeachers    = Teacher::where('institution_id', $instId)->count();
        $actifsTeachers   = Teacher::where('institution_id', $instId)->where('status', 1)->count();
        $teachersBySexe   = DB::table('teachers')->where('institution_id', $instId)->select('sexe', DB::raw('COUNT(*) as total'))->groupBy('sexe')->get()->keyBy('sexe');
        $teachersByContrat= DB::table('teachers')->where('institution_id', $instId)->whereNotNull('type_contrat')->select('type_contrat', DB::raw('COUNT(*) as total'))->groupBy('type_contrat')->orderByDesc('total')->get();

        $totalClasses  = Classe::where('institution_id', $instId)->count();
        $totalMatieres = Subject::where('institution_id', $instId)->count();
        $totalFilieres = Filiere::where('institution_id', $instId)->count();

        // ✅ Niveaux filtrés par institution
        $totalNiveaux = Niveau::where('institution_id', $instId)
            ->withCount(['classes' => fn ($q) => $q->where('institution_id', $instId)])
            ->having('classes_count', '>', 0)
            ->count();

        $apprenantsSansClasse = Apprenant::where('institution_id', $instId)->whereNull('class_id')->count();
        $tauxAffectation      = $totalApprenants > 0 ? round(($totalApprenants - $apprenantsSansClasse) / $totalApprenants * 100, 1) : 0;

        $finStats = Financialrecord::where('institution_id', $instId)->where('annee_academique', $annee)
            ->selectRaw('COALESCE(SUM(montant_du),0) as total_du, COALESCE(SUM(montant_paye),0) as total_paye, COALESCE(SUM(montant_reste),0) as total_reste, COUNT(CASE WHEN statut="paye" THEN 1 END) as nb_payes, COUNT(CASE WHEN statut="partiel" THEN 1 END) as nb_partiels, COUNT(CASE WHEN statut="impaye" THEN 1 END) as nb_impayes, COUNT(*) as nb_total')
            ->first();

        $finMensuel = Financialrecord::where('institution_id', $instId)->where('annee_academique', $annee)
            ->selectRaw('mois, mois_label, SUM(montant_du) as du, SUM(montant_paye) as paye, SUM(montant_reste) as reste')
            ->groupBy('mois', 'mois_label')->orderBy('mois')->get();

        $topDebiteurs = Apprenant::where('apprenants.institution_id', $instId)
            ->join('financial_records', 'apprenants.id', '=', 'financial_records.apprenant_id')
            ->where('financial_records.annee_academique', $annee)
            ->where('financial_records.statut', '!=', 'paye')
            ->select('apprenants.id', 'apprenants.nom', 'apprenants.prenom', 'apprenants.class_id', DB::raw('SUM(financial_records.montant_reste) as total_reste'))
            ->groupBy('apprenants.id', 'apprenants.nom', 'apprenants.prenom', 'apprenants.class_id')
            ->orderByDesc('total_reste')->with('classe:id,name')->limit(5)->get();

        $totalStaff  = \App\Models\Staff::where('institution_id', $instId)->count();
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
        $apprenantsSansParent    = Apprenant::where('institution_id', $instId)->whereDoesntHave('parents')->count();
        $tauxCouvertureParents   = $totalApprenants > 0 ? round(($totalApprenants - $apprenantsSansParent) / $totalApprenants * 100, 1) : 0;

        return view('admin.Rapports', compact(
            'user', 'institution', 'annee',
            'totalApprenants', 'actifsApprenants', 'garcons', 'filles',
            'apprenantsByClasse', 'apprenantsByNiveau', 'apprenantsSansClasse', 'tauxAffectation',
            'totalTeachers', 'actifsTeachers', 'teachersBySexe', 'teachersByContrat',
            'totalClasses', 'totalMatieres', 'totalFilieres', 'totalNiveaux',
            'finStats', 'finMensuel', 'topDebiteurs',
            'totalStaff', 'actifsStaff', 'staffByUnit',
            'totalParents', 'apprenantsSansParent', 'tauxCouvertureParents',
        ));
    }
}