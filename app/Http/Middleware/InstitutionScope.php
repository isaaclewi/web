<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstitutionScope
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Accès plateforme total
        if ($user && $user->hasRole('super_admin')) {
            return $next($request);
        }

        if (!$user || !$user->institution_id) {
            abort(403, 'Aucun établissement associé.');
        }

        // Institution courante accessible globalement
        app()->instance('current_institution_id', $user->institution_id);

        return $next($request);
    }
}
