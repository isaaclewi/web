<?php

namespace App\Http\Controllers;

use App\Models\Apprenant;
use App\Models\Classe;
use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\SchoolParent;
use App\Models\User;
use App\Services\WelcomeMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ParentController extends Controller
{
    /* ─────────────────────────────────────────
     | LISTE + STATS
     ───────────────────────────────────────── */
    public function index(Request $request)
    {
        $user        = Auth::user();
        $institution = $user->institution;
        $instId      = $institution->id;

        $query = SchoolParent::whereHas('apprenants', function ($q) use ($instId) {
            $q->where('institution_id', $instId);
        })
        ->orWhereHas('user', function ($q) use ($instId) {
            $q->where('institution_id', $instId);
        });

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nom',        'like', "%{$s}%")
                  ->orWhere('prenom',   'like', "%{$s}%")
                  ->orWhere('telephone','like', "%{$s}%")
                  ->orWhere('email',    'like', "%{$s}%");
            });
        }

        $parents = $query
            ->with(['apprenants.classe', 'user'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total'         => $query->toBase()->getCountForPagination(),
            'actifs'        => SchoolParent::where('status', 1)->count(),
            'total_enfants' => DB::table('apprenant_parent')
                                  ->join('apprenants', 'apprenant_parent.apprenant_id', '=', 'apprenants.id')
                                  ->where('apprenants.institution_id', $instId)
                                  ->count(),
            'avec_compte'   => SchoolParent::whereNotNull('user_id')->count(),
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

        $niveaux  = Niveau::orderBy('name')->get(['id', 'name']);
        $filieres = Filiere::where('institution_id', $instId)->orderBy('name')->get(['id', 'name']);
        $classes  = Classe::where('institution_id', $instId)->orderBy('name')->get(['id', 'name']);

        return view('admin.Parents', compact(
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
    public function store(Request $request)
    {
        $user        = Auth::user();
        $institution = $user->institution;

        $data = $request->validate([
            'prenom'                => 'required|string|max:100',
            'nom'                   => 'required|string|max:100',
            'sexe'                  => 'nullable|in:M,F',
            'telephone'             => 'nullable|string|max:30',
            'email'                 => 'nullable|email|max:255',
            'profession'            => 'nullable|string|max:100',
            'adresse'               => 'nullable|string|max:255',
            'apprenant_id'          => 'nullable|exists:apprenants,id',
            'lien'                  => 'nullable|string|max:50',
            'password'              => 'nullable|min:8|confirmed',
            'password_confirmation' => 'nullable',
        ]);

        // ✅ Mot de passe en clair AVANT la transaction (pour l'email)
        $passwordRaw = $data['password'] ?? 'password123';
        $emailDest   = ! empty($data['email']) ? $data['email'] : null;
        $parent      = null;

        DB::transaction(function () use ($data, $institution, $passwordRaw, &$parent) {

            $userId = null;

            if (! empty($data['email'])) {
                $existingUser = User::where('email', $data['email'])->first();

                if ($existingUser) {
                    $userId = $existingUser->id;
                } else {
                    $newUser = User::create([
                        'name'           => $data['prenom'].' '.$data['nom'],
                        'email'          => $data['email'],
                        // ✅ Hash::make($passwordRaw) — le clair est gardé dans $passwordRaw
                        'password'       => Hash::make($passwordRaw),
                        'institution_id' => $institution->id,
                        'status'         => 1,
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
                    'name'           => $data['prenom'].' '.$data['nom'],
                    'email'          => $techEmail,
                    'password'       => Hash::make(uniqid('', true)),
                    'institution_id' => $institution->id,
                    'status'         => 1,
                ]);
                \Spatie\Permission\Models\Role::firstOrCreate(
                    ['name' => 'parent', 'guard_name' => 'web']
                );
                $newUser->assignRole('parent');
                $userId = $newUser->id;
            }

            // Génération automatique du matricule
            $count     = SchoolParent::count() + 1;
            $matricule = 'PAR-'.str_pad($count, 5, '0', STR_PAD_LEFT);
            while (SchoolParent::where('matricule', $matricule)->exists()) {
                $count++;
                $matricule = 'PAR-'.str_pad($count, 5, '0', STR_PAD_LEFT);
            }

            // Créer le profil parent
            $parent = SchoolParent::create([
                'user_id'    => $userId,
                'nom'        => $data['nom'],
                'prenom'     => $data['prenom'],
                'sexe'       => $data['sexe']       ?? null,
                'matricule'  => $matricule,
                'telephone'  => $data['telephone']  ?? null,
                'email'      => $data['email']       ?? null,
                'profession' => $data['profession']  ?? null,
                'adresse'    => $data['adresse']     ?? null,
                'status'     => 1,
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
                email:       $emailDest,
                prenom:      $data['prenom'],
                nom:         $data['nom'],
                matricule:   $parent->matricule,
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
    public function update(Request $request, SchoolParent $parent)
    {
        $data = $request->validate([
            'prenom'     => 'required|string|max:100',
            'nom'        => 'required|string|max:100',
            'sexe'       => 'nullable|in:M,F',
            'telephone'  => 'nullable|string|max:30',
            'email'      => 'nullable|email|max:255',
            'profession' => 'nullable|string|max:100',
            'adresse'    => 'nullable|string|max:255',
            'status'     => 'nullable|in:0,1',
        ]);

        $parent->update([
            'nom'        => $data['nom'],
            'prenom'     => $data['prenom'],
            'sexe'       => $data['sexe']       ?? $parent->sexe,
            'telephone'  => $data['telephone']  ?? null,
            'email'      => $data['email']      ?? null,
            'profession' => $data['profession'] ?? null,
            'adresse'    => $data['adresse']    ?? null,
            'status'     => $data['status']     ?? $parent->status,
        ]);

        if ($parent->user_id && $parent->user) {
            $parent->user->update([
                'name'  => $data['prenom'].' '.$data['nom'],
                'email' => $data['email'] ?? $parent->user->email,
            ]);
        }

        return redirect()->back()
            ->with('success', "Parent « {$data['prenom']} {$data['nom']} » mis à jour.");
    }

    /* ─────────────────────────────────────────
     | SUPPRIMER UN PARENT
     ───────────────────────────────────────── */
    public function destroy(SchoolParent $parent)
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
        $user        = Auth::user();
        $institution = $user->institution;

        $data = $request->validate([
            'parent_id'    => 'required|exists:parents,id',
            'apprenant_id' => 'required|exists:apprenants,id',
            'lien'         => 'nullable|string|max:50',
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
                ->with('error', "Cette liaison parent-élève existe déjà.");
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
        $user        = Auth::user();
        $institution = $user->institution;

        $data = $request->validate([
            'parent_id'    => 'required|exists:parents,id',
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
    public function resetPassword(Request $request, SchoolParent $parent)
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
    public function show(SchoolParent $parent)
    {
        $user        = Auth::user();
        $institution = $user->institution;

        $parent->load([
            'apprenants' => function ($q) use ($institution) {
                $q->where('institution_id', $institution->id)
                  ->with([
                      'classe',
                      'niveau',
                      'filiere',
                      'financialRecords' => fn($q) => $q->where(
                          'annee_academique',
                          $institution->academic_year
                      ),
                  ]);
            },
            'user',
        ]);

        return view('admin.parent-show', compact('parent', 'institution', 'user'));
    }

    /* ─────────────────────────────────────────
     | HELPER : normalise le lien de parenté
     ───────────────────────────────────────── */
    private function normalizeLien(string $lien): string
    {
        $map = [
            'père'       => 'pere',
            'pere'       => 'pere',
            'mère'       => 'mere',
            'mere'       => 'mere',
            'tuteur'     => 'tuteur',
            'tutrice'    => 'tuteur',
            'grand-père' => 'tuteur',
            'grand-pere' => 'tuteur',
            'grand-mère' => 'tuteur',
            'grand-mere' => 'tuteur',
            'oncle'      => 'tuteur',
            'tante'      => 'tuteur',
            'autre'      => 'tuteur',
        ];

        return $map[mb_strtolower(trim($lien))] ?? 'tuteur';
    }

    /* ─────────────────────────────────────────
     | RECHERCHE AJAX D'APPRENANTS
     ───────────────────────────────────────── */
    public function searchApprenants(Request $request)
    {
        $user        = Auth::user();
        $institution = $user->institution;
        $instId      = $institution->id;

        $query = Apprenant::where('institution_id', $instId)
            ->with(['classe:id,name', 'niveau:id,name', 'filiere:id,name'])
            ->select('id', 'nom', 'prenom', 'matricule', 'class_id', 'niveau_id', 'filiere_id');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nom',        'like', "%{$q}%")
                    ->orWhere('prenom',   'like', "%{$q}%")
                    ->orWhere('matricule','like', "%{$q}%");
            });
        }

        if ($request->filled('niveau_id'))  $query->where('niveau_id',  $request->niveau_id);
        if ($request->filled('filiere_id')) $query->where('filiere_id', $request->filiere_id);
        if ($request->filled('classe_id'))  $query->where('class_id',   $request->classe_id);

        $total      = $query->count();
        $apprenants = $query->orderBy('nom')->orderBy('prenom')->limit(50)->get()
            ->map(function ($a) {
                return [
                    'id'        => $a->id,
                    'nom'       => $a->nom,
                    'prenom'    => $a->prenom,
                    'matricule' => $a->matricule,
                    'classe'    => optional($a->classe)->name,
                    'niveau'    => optional($a->niveau)->name,
                    'filiere'   => optional($a->filiere)->name,
                    'label'     => $a->prenom.' '.$a->nom
                                  .(optional($a->classe)->name ? ' — '.optional($a->classe)->name : '')
                                  .($a->matricule ? ' ('.$a->matricule.')' : ''),
                ];
            });

        return response()->json(['data' => $apprenants, 'total' => $total]);
    }
}