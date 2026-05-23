<?php

namespace App\Traits;

use App\Models\Apprenant;
use App\Models\Teacher;

/**
 * Trait MatriculeGenerator
 *
 * Centralise la logique de génération automatique de matricule
 * pour tous les types d'utilisateurs (enseignant, apprenant, staff).
 *
 * Préfixes par défaut :
 *   - Apprenant : ETB-YYYY-0001
 *   - Enseignant : ENS-0001
 *   - Staff      : STF-0001
 */
trait MatriculeGenerator
{
    /**
     * Génère un matricule unique pour un apprenant.
     *
     * Format : {CODE_ETABLISSEMENT}-{ANNÉE}-{NUMÉRO_SÉQUENTIEL}
     * Ex    : ETB-2025-0042
     */
    protected function generateApprenantMatricule(
        \App\Models\Institution $institution,
        int $instId,
    ): string {
        $prefix = strtoupper(substr($institution->code ?? 'ETB', 0, 3));
        $year   = $institution->academic_year_short ?? date('Y');

        $count = Apprenant::where('institution_id', $instId)->count() + 1;
        $candidate = "{$prefix}-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);

        // Évite les collisions (rare mais possible en cas d'import massif)
        while (Apprenant::where('institution_id', $instId)->where('matricule', $candidate)->exists()) {
            $count++;
            $candidate = "{$prefix}-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }

    /**
     * Génère un matricule unique pour un enseignant.
     *
     * Format : ENS-{NUMÉRO_SÉQUENTIEL}
     * Ex    : ENS-0007
     */
    protected function generateTeacherMatricule(
        \App\Models\Institution $institution,
        int $instId,
    ): string {
        $prefix = strtoupper(substr($institution->code ?? 'ENS', 0, 3)) . 'P'; // ex: ETBP ou ENSP

        // On préfère un préfixe lisible
        $prefix = 'ENS';

        $count     = Teacher::where('institution_id', $instId)->count() + 1;
        $candidate = "{$prefix}-" . str_pad($count, 4, '0', STR_PAD_LEFT);

        while (Teacher::where('institution_id', $instId)->where('matricule', $candidate)->exists()) {
            $count++;
            $candidate = "{$prefix}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }

    /**
     * Génère un matricule unique pour un membre du staff.
     *
     * Format : STF-{NUMÉRO_SÉQUENTIEL}
     * Ex    : STF-0003
     */
    protected function generateStaffMatricule(
        \App\Models\Institution $institution,
        int $instId,
    ): string {
        $prefix = strtoupper(substr($institution->code ?? 'STF', 0, 3));

        $count     = \App\Models\Staff::where('institution_id', $instId)->count() + 1;
        $candidate = "{$prefix}-" . str_pad($count, 4, '0', STR_PAD_LEFT);

        while (\App\Models\Staff::where('matricule', $candidate)->exists()) {
            $count++;
            $candidate = "{$prefix}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }
}