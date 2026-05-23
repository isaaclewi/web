<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialRecord extends Model
{
    protected $fillable = [
        'institution_id',
        'apprenant_id',
        'annee_academique',
        'mois',
        'mois_label',
        'montant_du',
        'montant_paye',
        'montant_reste',
        'statut',
        'date_paiement',
        'mode_paiement',
        'reference',
        'notes',
        'recorded_by',
        'recorded_at',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'date_paiement' => 'date',
        'recorded_at'   => 'datetime',
        'validated_at'  => 'datetime',
        'montant_du'    => 'decimal:2',
        'montant_paye'  => 'decimal:2',
        'montant_reste' => 'decimal:2',
    ];

    /* ── Relations ── */

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function apprenant(): BelongsTo
    {
        return $this->belongsTo(Apprenant::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /* ── Helpers ── */

    public static function moisLabels(): array
    {
        return [
            1  => 'Janvier',   2  => 'Février',   3  => 'Mars',
            4  => 'Avril',     5  => 'Mai',        6  => 'Juin',
            7  => 'Juillet',   8  => 'Août',       9  => 'Septembre',
            10 => 'Octobre',   11 => 'Novembre',   12 => 'Décembre',
        ];
    }

    public function getBadgeClassAttribute(): string
    {
        return match($this->statut) {
            'paye'    => 'badge-green',
            'partiel' => 'badge-amber',
            default   => 'badge-red',
        };
    }

    public function getStatutLabelAttribute(): string
    {
        return match($this->statut) {
            'paye'    => 'Payé',
            'partiel' => 'Partiel',
            default   => 'Impayé',
        };
    }
}