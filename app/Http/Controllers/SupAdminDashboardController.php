<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Staff;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SupAdminDashboardController extends Controller
{
    public function index()
    {
        $authUser = Auth::user();

        if (! $authUser) {
            return redirect()->route('login')
                ->with('error', 'Veuillez vous connecter.');
        }

        $institution = $authUser->institution;

        /* ===============================
            STATISTIQUES GLOBALES
        =============================== */

        $totalInstitutions = Institution::count();
        $activeInstitutions = Institution::where('status', 1)->count();

        $totalUsers = User::count();
        $activeUsers = User::where('status', 1)->count();

        $totalStudents = \App\Models\Apprenant::count();
        $activeStudents = \App\Models\Apprenant::where('status', 1)->count();

        $totalParents = \App\Models\SchoolParent::count();
        $totalTeachers = Teacher::count();
        $totalAdmins = Staff::count();

        //  AJOUT IMPORTANT
        $institutions = Institution::latest()->get();

        $users = User::with('institution')->latest()->paginate(10); // 10 utilisateurs par page

        /* ===============================
            TABLEAU ETABLISSEMENTS
        =============================== */

        $schools = Institution::withCount([
            'apprenants',
            'staff as admins_count',
            'administrativeUnits',
        ])
            ->withCount([
                'apprenants as students_count',
            ])
            ->latest()
            ->paginate(5);

        return view('superadmin.dashboard', compact(
            'authUser',
            'institution',
            'totalInstitutions',
            'activeInstitutions',
            'totalUsers',
            'activeUsers',
            'totalStudents',
            'activeStudents',
            'totalParents',
            'totalTeachers',
            'totalAdmins',
            'schools',
            'users',
            'institutions' // <- ajouté ici
        ));
    }

    public function users()
    {
        $authUser = Auth::user();

        if (! $authUser) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
        }

        // On affiche uniquement les utilisateurs ayant le rôle directeur
        $users = User::with('institution', 'staff')
    ->latest()
    ->paginate(10); // ✅ pagination activée

        $institutions = Institution::latest()->get();

        return view('superadmin.users', [
            'authUser' => $authUser,
            'users' => $users,
            'institution' => $authUser->institution,
            'institutions' => $institutions,
        ]);
    }

    /**
     * STORE NEW USER
     */
      public function store(Request $request)
    {
        $authUser = Auth::user();
 
        if (! $authUser) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
        }
 
        // Validation — on garde le password nullable ici car
        // le HTML peut envoyer une chaîne vide si l'utilisateur oublie
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|string|min:8',
            'institution_id' => 'required|exists:institutions,id',
            'matricule'      => 'required|string|max:50|unique:staff,matricule',
            'prenom'         => 'nullable|string|max:100',
            'nom'            => 'nullable|string|max:100',
            'telephone'      => 'nullable|string|max:30',
            'status'         => 'nullable|in:0,1',
        ]);
 
        DB::transaction(function () use ($validated, $request) {
 
            // 1. Créer le User
            $user = User::create([
                'name'           => $validated['name'],
                'email'          => $validated['email'],
                'password'       => Hash::make($validated['password']),
                'institution_id' => $validated['institution_id'],
                'status'         => $validated['status'] ?? 1,
            ]);
 
            // 2. Assigner le rôle directeur
            \Spatie\Permission\Models\Role::firstOrCreate([
                'name'       => 'directeur',
                'guard_name' => 'web',
            ]);
            $user->assignRole('directeur');
 
            // 3. Créer ou récupérer l'unité administrative "Direction"
            $unit = \App\Models\AdministrativeUnit::firstOrCreate(
                [
                    'institution_id' => $validated['institution_id'],
                    'type'           => 'direction',
                ],
                [
                    'name'   => 'Direction',
                    'status' => 1,
                ]
            );
 
            // 4. Créer le profil Staff lié au User
            //    C'est ce profil que le LoginController utilise pour authentifier l'admin
            \App\Models\Staff::create([
                'user_id'                => $user->id,
                'institution_id'         => $validated['institution_id'],
                'administrative_unit_id' => $unit->id,
                'matricule'              => $validated['matricule'],
                'nom'                    => $validated['nom']       ?? '',
                'prenom'                 => $validated['prenom']    ?? '',
                'telephone'              => $validated['telephone'] ?? null,
                'email'                  => $validated['email'],
                'poste'                  => 'Directeur',
                'status'                 => 1,
            ]);
        });
 
        return redirect()->back()->with('success', 'Directeur créé avec succès. Il peut maintenant se connecter avec son email et son matricule.');
    }
    
    public function destroy(User $user)
    {
        // Le Staff associé sera supprimé en cascade (cascadeOnDelete sur user_id)
        $user->delete();

        return redirect()->back()->with('success', 'Directeur supprimé.');
    }

    public function institutions()
    {
        $authUser = Auth::user();

        if (! $authUser) {
            return redirect()->route('login')
                ->with('error', 'Veuillez vous connecter.');
        }

        $institution = $authUser->institution;

        // Récupérer toutes les institutions
        $institutions = Institution::latest()->get();

        $totalInstitutions = Institution::count();
        $activeInstitutions = Institution::where('status', '1')->count();
        $inactiveInstitutions = Institution::where('status', '0')->count();

        return view('superadmin.institutions', compact(
            'authUser',
            'institution',
            'institutions', // <- ajouté ici
            'totalInstitutions',
            'activeInstitutions',
            'inactiveInstitutions'
        ));
    }

    public function destroyInstitution(Institution $institution)
    {
        $institution->delete();

        return redirect()->back()
            ->with('success', 'Institution supprimée.');
    }

    public function InstitutionStore(Request $request)
    {

        $data = $request->validate([
            'name' => 'required',
            'code' => 'required|unique:institutions,code',
            'type' => 'required',
            'statut_juridique' => 'required',
            'academic_year' => 'required',
            'pays' => 'required',
            'departement' => 'required',
            'commune' => 'required',
            'adresse' => 'required',

            'email' => 'nullable',
            'telephone' => 'nullable',
            'site_web' => 'nullable',
            'devise' => 'nullable',
            'date_creation' => 'nullable',
            'autorisation_etat' => 'nullable',
        ]);

        $data['status'] = 1;

        Institution::create($data);

        return redirect()->back()->with('success', 'Institution créée');
    }

    public function updateInstitution(Request $request, Institution $institution)
    {

        $data = $request->validate([
            'name' => 'required',
            'code' => 'required',
            'type' => 'required',
            'statut_juridique' => 'required',
            'academic_year' => 'required',
            'pays' => 'required',
            'departement' => 'required',
            'commune' => 'required',
            'adresse' => 'required',

            'email' => 'nullable',
            'telephone' => 'nullable',
            'site_web' => 'nullable',
            'devise' => 'nullable',
            'date_creation' => 'nullable',
            'autorisation_etat' => 'nullable',
        ]);

        $institution->update($data);

        return redirect()->back()->with('success', 'Institution modifiée');
    }

    public function toggleStatus(Institution $institution)
    {
        // Changer le statut
        $institution->status = $institution->status ? 0 : 1;
        $institution->save();

        return redirect()->back()
            ->with('success', "Statut de l'institution mis à jour.");
    }
}
