<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuiviDisciplinaire extends Model
{
    protected $table = 'suivi_disciplinaire';

    protected $fillable = [
        'apprenant_id',
        'institution_id',
        'recorded_by',
        'annee_civile',
        'annee_academique',
        'date_incident',
        'type',
        'gravite',
        'description',
        'sanction',
        'sanction_detail',
        'sanction_executee',
        'sanction_date_execution',
        'parents_notifies',
        'date_notification',
        'observations',
        'statut',
    ];

    protected $casts = [
        'date_incident'            => 'date',
        'sanction_date_execution'  => 'date',
        'date_notification'        => 'date',
        'sanction_executee'        => 'boolean',
        'parents_notifies'         => 'boolean',
    ];

    /* ═══════════════════════════════════════════
     |  RELATIONS
     ═══════════════════════════════════════════ */

    public function apprenant(): BelongsTo
    {
        return $this->belongsTo(Apprenant::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /* ═══════════════════════════════════════════
     |  LABELS LISIBLES
     ═══════════════════════════════════════════ */

    public static function typeLabels(): array
    {
        return [
            'absence'       => 'Absence injustifiée',
            'retard'        => 'Retard',
            'insolence'     => 'Insolence / Irrespect',
            'violence'      => 'Violence',
            'triche'        => 'Triche / Fraude',
            'perturbation'  => 'Perturbation en classe',
            'tenue'         => 'Problème de tenue',
            'autre'         => 'Autre',
        ];
    }

    public static function sanctionLabels(): array
    {
        return [
            'aucune'                  => 'Aucune sanction',
            'avertissement'           => 'Avertissement',
            'blame'                   => 'Blâme',
            'exclusion_cours'         => 'Exclusion de cours',
            'exclusion_temp'          => 'Exclusion temporaire',
            'exclusion_def'           => 'Exclusion définitive',
            'convocation_parents'     => 'Convocation des parents',
            'travail_supplementaire'  => 'Travail supplémentaire',
            'autre'                   => 'Autre',
        ];
    }

    public static function graviteLabels(): array
    {
        return [
            1 => 'Mineur',
            2 => 'Modéré',
            3 => 'Grave',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeLabels()[$this->type] ?? $this->type;
    }

    public function getSanctionLabelAttribute(): string
    {
        return self::sanctionLabels()[$this->sanction] ?? $this->sanction;
    }

    public function getGraviteLabelAttribute(): string
    {
        return self::graviteLabels()[$this->gravite] ?? '—';
    }

    public function getGraviteColorAttribute(): string
    {
        return match((int) $this->gravite) {
            1 => 'amber',
            2 => 'orange',
            3 => 'red',
            default => 'gray',
        };
    }
}