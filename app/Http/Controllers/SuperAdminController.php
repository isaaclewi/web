<?php

namespace App\Http\Controllers;

use App\Models\Apprenant;
use App\Models\Institution;
use App\Models\Teacher;
use App\Models\User;
use App\Services\WelcomeMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    /**
     * ──────────────────────────────────────────────
     * IMPORTANT : Le Super Admin n'appartient à aucune
     * institution. Ne jamais utiliser Auth::user()->institution_id
     * dans ce controller.
     * ──────────────────────────────────────────────
     */

    /* ══════════════════════════════════════════════════
     | DASHBOARD
     ══════════════════════════════════════════════════ */
    public function dashboard()
    {
        // KPIs globaux — toutes institutions confondues
        $totalInstitutions = Institution::count();
        $activeUsers       = User::where('status', 1)->count();
        $totalUsers        = User::count();
        $totalStudents     = Apprenant::count();
        $totalTeachers     = Teacher::count();
        $totalParents      = DB::table('parents')->count();
        $totalAdmins       = User::whereHas('roles', fn($r) => $r->where('name', 'admin'))->count();

        // Top 5 institutions par nombre d'élèves
        $institutions = Institution::withCount(['apprenants'])
            ->orderByDesc('apprenants_count')
            ->take(5)
            ->get();

        // Toutes institutions pour les selects
        $schools = Institution::withCount(['apprenants as students_count'])
            ->orderBy('name')
            ->paginate(15);

        // Utilisateurs récents (tous rôles, toutes institutions)
        $users = User::with(['institution', 'roles'])
            ->latest()
            ->paginate(20);

        return view('superadmin.dashboard', compact(
            'totalInstitutions', 'activeUsers', 'totalUsers',
            'totalStudents', 'totalTeachers', 'totalParents', 'totalAdmins',
            'institutions', 'schools', 'users',
        ));
    }

    /* ══════════════════════════════════════════════════
     | INSTITUTIONS
     ══════════════════════════════════════════════════ */
    public function institutions()
    {
        $institutions = Institution::orderBy('name')->paginate(20);
        return view('superadmin.institutions', compact('institutions'));
    }

    public function institutionStore(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'code'             => 'required|string|max:50|unique:institutions,code',
            'type'             => 'nullable|string|max:100',
            'academic_year'    => 'required|string|max:20',
            'statut_juridique' => 'nullable|string|max:100',
            'pays'             => 'nullable|string|max:100',
            'departement'      => 'nullable|string|max:100',
            'commune'          => 'nullable|string|max:100',
            'adresse'          => 'nullable|string|max:255',
            'email'            => 'nullable|email|max:255',
            'telephone'        => 'nullable|string|max:30',
            'site_web'         => 'nullable|string|max:255',
            'devise'           => 'nullable|string|max:10',
            'description'      => 'nullable|string',
            'historique'       => 'nullable|string',
            'mission'          => 'nullable|string',
            'vision'           => 'nullable|string',
            'valeurs'          => 'nullable|string',
            'date_creation'    => 'nullable|date',
            'autorisation_etat'=> 'nullable|boolean',
        ]);

        $data['status'] = 1;
        Institution::create($data);

        return redirect()->route('superadmin.institutions')->with('success', "Institution « {$data['name']} » créée.");
    }

    public function institutionUpdate(Request $request, Institution $institution)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'type'             => 'nullable|string|max:100',
            'academic_year'    => 'required|string|max:20',
            'statut_juridique' => 'nullable|string|max:100',
            'departement'      => 'nullable|string|max:100',
            'commune'          => 'nullable|string|max:100',
            'adresse'          => 'nullable|string|max:255',
        ]);

        $institution->update($data);
        return redirect()->route('superadmin.institutions')->with('success', 'Institution mise à jour.');
    }

    public function institutionToggleStatus(Institution $institution)
    {
        $institution->update(['status' => !$institution->status]);
        $label = $institution->status ? 'activée' : 'désactivée';
        return redirect()->route('superadmin.institutions')->with('success', "Institution $label.");
    }

    public function institutionDestroy(Institution $institution)
    {
        $name = $institution->name;
        $institution->delete();
        return redirect()->route('superadmin.institutions')->with('success', "Institution « $name » supprimée.");
    }

    /* ══════════════════════════════════════════════════
     | UTILISATEURS (directeurs)
     ══════════════════════════════════════════════════ */
    public function users()
    {
        // Tous les directeurs — pas de filtre institution_id car superadmin voit tout
        $users = User::whereHas('roles', fn($r) => $r->where('name', 'directeur'))
            ->with(['institution', 'staff', 'roles'])
            ->latest()
            ->paginate(10);

        $institutions = Institution::orderBy('name')->get();

        return view('superadmin.users', compact('users', 'institutions'));
    }

    public function userStore(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|min:8',
            'status'         => 'nullable|in:0,1',
            'institution_id' => 'required|exists:institutions,id',
            'matricule'      => 'required|string|max:50|unique:staff,matricule',
            'telephone'      => 'nullable|string|max:30',
            'prenom'         => 'nullable|string|max:100',
            'nom'            => 'nullable|string|max:100',
        ]);

        // Conserver le mot de passe brut avant hashage pour l'e-mail
        $passwordRaw = $data['password'];
        $newUser     = null;

        DB::transaction(function () use ($data, $passwordRaw, &$newUser) {
            $newUser = User::create([
                'name'           => $data['name'],
                'email'          => $data['email'],
                'password'       => Hash::make($passwordRaw),
                'institution_id' => $data['institution_id'],
                'status'         => $data['status'] ?? 1,
            ]);

            \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'directeur', 'guard_name' => 'web']);
            $newUser->assignRole('directeur');

            // Trouver ou créer l'unité administrative Direction
            $unitId = \App\Models\AdministrativeUnit::firstOrCreate(
                ['institution_id' => $data['institution_id'], 'type' => 'direction'],
                ['name' => 'Direction', 'status' => 1]
            )->id;

            \App\Models\Staff::create([
                'user_id'                => $newUser->id,
                'institution_id'         => $data['institution_id'],
                'administrative_unit_id' => $unitId,
                'nom'                    => $data['nom'] ?? $data['name'],
                'prenom'                 => $data['prenom'] ?? '',
                'matricule'              => $data['matricule'],
                'telephone'              => $data['telephone'] ?? null,
                'email'                  => $data['email'],
                'poste'                  => 'Directeur',
                'status'                 => 1,
            ]);
        });

        // Récupérer le nom de l'institution pour le mail
        $institution = Institution::find($data['institution_id']);

        // Envoi de l'e-mail de bienvenue au directeur
        WelcomeMailService::send(
            email:       $data['email'],
            prenom:      $data['prenom'] ?? $data['name'],
            nom:         $data['nom'] ?? '',
            matricule:   $data['matricule'],
            role:        'Directeur d\'établissement',
            passwordRaw: $passwordRaw,
            institution: $institution?->name ?? '',
        );

        return redirect()->route('superadmin.users')->with(
            'success',
            "Directeur « {$data['name']} » créé avec succès. Un e-mail de bienvenue a été envoyé à {$data['email']}."
        );
    }

    public function userDestroy(User $user)
    {
        $name = $user->name;
        DB::transaction(function () use ($user) {
            $user->staff?->delete();
            $user->delete();
        });
        return redirect()->route('superadmin.users')->with('success', "Utilisateur « $name » supprimé.");
    }
}