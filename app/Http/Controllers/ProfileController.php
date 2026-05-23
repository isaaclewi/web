<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /* ══════════════════════════════════════════════════════════
     |  HELPER PRIVÉ
     ══════════════════════════════════════════════════════════ */

    private function getStaffProfile()
    {
        $user = Auth::user();
        return $user->staff()->with('administrativeUnit')->first();
    }

    /* ══════════════════════════════════════════════════════════
     |  AFFICHER LE PROFIL  —  GET /admin/profil
     ══════════════════════════════════════════════════════════ */

    public function show()
    {
        $user        = Auth::user();
        $institution = $user->institution;
        $staff       = $this->getStaffProfile();

        $stats = [];
        if ($institution) {
            $instId = $institution->id;
            $stats = [
                'apprenants' => \App\Models\Apprenant::where('institution_id', $instId)->count(),
                'teachers'   => \App\Models\Teacher::where('institution_id', $instId)->count(),
                'classes'    => \App\Models\Classe::where('institution_id', $instId)->count(),
                'staff'      => \App\Models\Staff::where('institution_id', $instId)->count(),
            ];
        }

        return view('admin.profil', compact('user', 'institution', 'staff', 'stats'));
    }

    /* ══════════════════════════════════════════════════════════
     |  METTRE À JOUR LES INFOS  —  PATCH /admin/profil/infos
     ══════════════════════════════════════════════════════════ */

    public function updateInfos(Request $request)
    {
        $user  = Auth::user();
        $staff = $this->getStaffProfile();

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'     => 'nullable|string|max:30',
            'nom'       => 'nullable|string|max:100',
            'prenom'    => 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:30',
            'poste'     => 'nullable|string|max:100',
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if ($staff) {
            $staff->update([
                'nom'       => $validated['nom']       ?? $staff->nom,
                'prenom'    => $validated['prenom']    ?? $staff->prenom,
                'telephone' => $validated['telephone'] ?? $validated['phone'] ?? null,
                'poste'     => $validated['poste']     ?? $staff->poste,
                'email'     => $validated['email'],
            ]);
        }

        return redirect()->route('admin.profil')
            ->with('success', 'Informations personnelles mises à jour avec succès.');
    }

    /* ══════════════════════════════════════════════════════════
     |  CHANGER LE MOT DE PASSE  —  PATCH /admin/profil/password
     ══════════════════════════════════════════════════════════ */

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password'      => 'required',
            'password'              => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'password_confirmation' => 'required',
        ], [
            'password.min'        => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.mixed_case' => 'Le mot de passe doit contenir des majuscules et des minuscules.',
            'password.numbers'    => 'Le mot de passe doit contenir au moins un chiffre.',
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.'])
                ->withInput();
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('admin.profil')
            ->with('success', 'Mot de passe changé avec succès.');
    }

    /* ══════════════════════════════════════════════════════════
     |  CHANGER L'AVATAR ADMIN  —  POST /admin/profil/avatar
     |  Stockage dans root_storage (racine InfinityFree)
     ══════════════════════════════════════════════════════════ */

    public function updateAvatar(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $user = Auth::user();

    // Supprimer ancien
    if ($user->avatar) {
        Storage::disk('root_storage')->delete($user->avatar);
    }

    // ✅ même logique que logo
    $path = $request->file('avatar')->store('avatars/users', 'root_storage');

    $user->update(['avatar' => $path]);

    return redirect()->route('admin.profil')
        ->with('success', 'Photo de profil mise à jour.');
}

    /* ══════════════════════════════════════════════════════════
     |  AVATAR ENSEIGNANT  —  POST /teacher/profil/avatar
     |  Stockage dans root_storage
     ══════════════════════════════════════════════════════════ */

    public function updateTeacherAvatar(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $user    = Auth::user();
    $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();

    if (! $teacher) {
        return redirect()->back()->with('error', 'Profil enseignant introuvable.');
    }

    // 🔥 Supprimer ancien fichier
    if ($teacher->photo) {
        Storage::disk('root_storage')->delete($teacher->photo);
    }

    // ✅ EXACTEMENT comme ton logo
    $path = $request->file('avatar')->store('avatars/teachers', 'root_storage');

    // ✅ Sauvegarde en base
    $teacher->update([
        'photo' => $path
    ]);

    return redirect()->back()->with('success', 'Photo de profil mise à jour.');
}

    /* ══════════════════════════════════════════════════════════
     |  AVATAR APPRENANT  —  POST /student/profil/avatar
     |  Stockage dans root_storage
     ══════════════════════════════════════════════════════════ */

    public function updateApprenantAvatar(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $user      = Auth::user();
    $apprenant = \App\Models\Apprenant::where('user_id', $user->id)->first();

    if (! $apprenant) {
        return redirect()->back()->with('error', 'Profil apprenant introuvable.');
    }

    // 🔥 Supprimer ancien
    if ($apprenant->photo) {
        Storage::disk('root_storage')->delete($apprenant->photo);
    }

    // ✅ Comme le logo
    $path = $request->file('avatar')->store('avatars/apprenants', 'root_storage');

    // ✅ Sauvegarde
    $apprenant->update([
        'photo' => $path
    ]);

    return redirect()->back()->with('success', 'Photo de profil mise à jour.');
}

    /* ══════════════════════════════════════════════════════════
     |  AVATAR PARENT  —  POST /parent/profil/avatar
     |  Stockage dans root_storage
     ══════════════════════════════════════════════════════════ */

    public function updateParentAvatar(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $user   = Auth::user();
    $parent = \App\Models\SchoolParent::where('user_id', $user->id)->first();

    if (! $parent) {
        return redirect()->back()->with('error', 'Profil parent introuvable.');
    }

    // 🔥 Supprimer ancien
    if ($parent->photo) {
        Storage::disk('root_storage')->delete($parent->photo);
    }

    // ✅ Comme le logo
    $path = $request->file('avatar')->store('avatars/parents', 'root_storage');

    // ✅ Sauvegarde
    $parent->update([
        'photo' => $path
    ]);

    return redirect()->back()->with('success', 'Photo de profil mise à jour.');
}

    /* ══════════════════════════════════════════════════════════
     |  PROFIL PARENT  —  GET /parent/profil
     ══════════════════════════════════════════════════════════ */

    public function parentShow()
    {
        $user   = Auth::user();
        $parent = \App\Models\SchoolParent::where('user_id', $user->id)
            ->with(['apprenants' => function ($q) {
                $q->with(['classe:id,name', 'niveau:id,name', 'institution:id,name']);
            }])
            ->first();

        $institution = $user->institution;

        return view('parent.profil', compact('user', 'parent', 'institution'));
    }

    /* ══════════════════════════════════════════════════════════
     |  INFOS PARENT  —  PATCH /parent/profil/infos
     ══════════════════════════════════════════════════════════ */

    public function updateParentInfos(Request $request)
    {
        $user   = Auth::user();
        $parent = \App\Models\SchoolParent::where('user_id', $user->id)->first();

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:users,email,' . $user->id,
            'telephone'  => 'nullable|string|max:30',
            'prenom'     => 'nullable|string|max:100',
            'nom'        => 'nullable|string|max:100',
            'profession' => 'nullable|string|max:100',
            'sexe'       => 'nullable|in:M,F',
            'adresse'    => 'nullable|string|max:255',
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($parent) {
            $parent->update([
                'prenom'     => $validated['prenom']     ?? $parent->prenom,
                'nom'        => $validated['nom']        ?? $parent->nom,
                'telephone'  => $validated['telephone']  ?? null,
                'profession' => $validated['profession'] ?? null,
                'sexe'       => $validated['sexe']       ?? $parent->sexe,
                'adresse'    => $validated['adresse']    ?? null,
                'email'      => $validated['email'],
            ]);
        }

        return redirect()->route('parent.profil')
            ->with('success', 'Informations mises à jour avec succès.');
    }

    /* ══════════════════════════════════════════════════════════
     |  MOT DE PASSE PARENT  —  PATCH /parent/profil/password
     ══════════════════════════════════════════════════════════ */

    public function updateParentPassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('parent.profil')
            ->with('success', 'Mot de passe changé avec succès.');
    }

    /* ══════════════════════════════════════════════════════════
     |  INFOS ENSEIGNANT  —  PATCH /teacher/profil/infos
     ══════════════════════════════════════════════════════════ */

    public function updateTeacherInfos(Request $request)
    {
        $user    = Auth::user();
        $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:30',
            'prenom'    => 'nullable|string|max:100',
            'nom'       => 'nullable|string|max:100',
            'specialite'=> 'nullable|string|max:100',
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($teacher) {
            $teacher->update([
                'prenom'     => $validated['prenom']     ?? $teacher->prenom,
                'nom'        => $validated['nom']        ?? $teacher->nom,
                'telephone'  => $validated['telephone']  ?? null,
                'specialite' => $validated['specialite'] ?? null,
                'email'      => $validated['email'],
            ]);
        }

        return redirect()->back()->with('success', 'Informations mises à jour.');
    }

    /* ══════════════════════════════════════════════════════════
     |  MOT DE PASSE ENSEIGNANT  —  PATCH /teacher/profil/password
     ══════════════════════════════════════════════════════════ */

    public function updateTeacherPassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->back()->with('success', 'Mot de passe changé avec succès.');
    }

    /* ══════════════════════════════════════════════════════════
     |  INFOS APPRENANT  —  PATCH /student/profil/infos
     ══════════════════════════════════════════════════════════ */

    public function updateApprenantInfos(Request $request)
    {
        $user      = Auth::user();
        $apprenant = \App\Models\Apprenant::where('user_id', $user->id)->first();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        return redirect()->back()->with('success', 'Informations mises à jour.');
    }
}