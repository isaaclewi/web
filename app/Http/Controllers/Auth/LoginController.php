<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Apprenant;
use App\Models\SchoolParent;
use App\Models\Staff;
use App\Models\SuperAdmin;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    // Affiche le formulaire
    public function showLoginForm()
    {
        return view('login');
    }

    // Traite la connexion
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'matricule' => 'required|string',
        ]);

        $email = $request->email;
        $password = $request->password;
        $matricule = $request->matricule;

        // 1️⃣ Vérification SuperAdmin
        // Le SuperAdmin a son propre modèle/table, indépendant de users
        $superAdmin = SuperAdmin::where('email', $email)
            ->where('matricule', $matricule)
            ->first();

        if ($superAdmin && Hash::check($password, $superAdmin->password)) {
            Auth::guard('web')->login($superAdmin);
            $request->session()->regenerate();

            return redirect()->route('superadmin.dashboard');
        }

        // 2️⃣ Récupérer le User via email + vérifier le mot de passe
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return back()
                ->withErrors(['email' => 'Identifiants incorrects.'])
                ->withInput($request->only('email', 'matricule'));
        }

        // 3️⃣ Teacher
        // Vérifie dans la table teachers : user_id + matricule
        $teacher = Teacher::where('user_id', $user->id)
            ->where('matricule', $matricule)
            ->first();

        if ($teacher) {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('teacher.dashboard');
        }

        // 4️⃣ Admin / Directeur
        // Vérifie le User (déjà fait) + dans staff : user_id + matricule
        $staff = Staff::where('user_id', $user->id)
            ->where('matricule', $matricule)
            ->first();

        if ($staff) {
            Auth::login($user);
            $request->session()->regenerate();

            $poste = strtolower($staff->poste);

            // 🎯 Cas Directeur
            if (str_contains($poste, 'directeur')) {
                return redirect()->route('admin.dashboard');
            }

            // 🎯 Cas Comptable
            if (str_contains($poste, 'comptable')) {
                return redirect()->route('staff.dashboard'); // à créer si besoin
            }

            // 🎯 Cas Censeur / Discipline
            if (str_contains($poste, 'censeur') || str_contains($poste, 'discipline')) {
                return redirect()->route('staff.dashboard'); // optionnel
            }

            // 🎯 Cas Secrétaire / Scolarité
            if (str_contains($poste, 'secretaire') || str_contains($poste, 'scolarite')) {
                return redirect()->route('staff.dashboard');
            }

            // 🎯 Tous les autres staff
            return redirect()->route('staff.dashboard');
        }

        // 5️⃣ SchoolParent
        // Pas de matricule à vérifier pour les parents
        $parent = SchoolParent::where('user_id', $user->id)->first();

        if ($parent) {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('parent.dashboard');
        }

        // 6️⃣ Apprenant
        // Vérifie dans la table apprenants : user_id + matricule
        $apprenant = Apprenant::where('user_id', $user->id)
            ->where('matricule', $matricule)
            ->first();

        if ($apprenant) {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('student.dashboard');
        }

        // Aucun profil trouvé avec ce matricule
        return back()
            ->withErrors(['matricule' => 'Matricule invalide ou non associé à ce compte.'])
            ->withInput($request->only('email', 'matricule'));
    }

    // Déconnexion
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
