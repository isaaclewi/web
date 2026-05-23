<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware qui s'assure qu'un Super Admin ne se retrouve jamais
 * avec une institution_id héritée d'une session précédente.
 */
class SuperAdminGuard
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->hasRole('superadmin')) {
            // Forcer institution_id à null en session pour le superadmin
            // Cela empêche toute contamination entre sessions
            session()->forget('impersonate_institution_id');
        }

        return $next($request);
    }
}
