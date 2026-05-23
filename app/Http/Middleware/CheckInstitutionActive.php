<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckInstitutionActive
{
    /**
     * Bloque toute requête si l'institution de l'utilisateur connecté
     * est désactivée (status = 0).
     *
     * S'applique sur toutes les routes admin/teacher/apprenant/parent.
     * Laisse passer : login, logout, les assets, et le superadmin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Pas connecté → laisser passer (le middleware auth s'en chargera)
        if (! $user) {
            return $next($request);
        }

        // Superadmin → toujours autorisé
        if ($user->hasRole('superadmin')) {
            return $next($request);
        }

        $institution = $user->institution;

        // Pas d'institution liée → bloquer
        if (! $institution) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Votre compte n\'est lié à aucun établissement. Contactez l\'administrateur.');
        }

        // Institution désactivée (status = 0) → bloquer
        if ((int) $institution->status !== 1) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', "L'établissement « {$institution->name} » est actuellement désactivé. Veuillez contacter l'administrateur.");
        }

        return $next($request);
    }
}