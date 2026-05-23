<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportCardLine extends Model
{
    protected $table = 'report_card_lines';

    protected $fillable = [
        'report_card_id',
        'subject_id',
        'average',
        'coefficient',
        'total',        // average * coefficient — calculé avant insertion
    ];

    protected $casts = [
        'average'     => 'decimal:2',
        'total'       => 'decimal:2',
        'coefficient' => 'integer',
    ];

    /* ═══════════════════════════════════════════
     |  RELATIONS
     ═══════════════════════════════════════════ */

    /**
     * Bulletin de notes auquel appartient cette ligne.
     */
    public function reportCard(): BelongsTo
    {
        return $this->belongsTo(ReportCard::class);
    }

    /**
     * Matière concernée.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /* ═══════════════════════════════════════════
     |  HELPERS
     ═══════════════════════════════════════════ */

    /**
     * Calcule et stocke automatiquement le total avant sauvegarde.
     */
    protected static function booted(): void
    {
        static::saving(function (self $line) {
            $line->total = round($line->average * $line->coefficient, 2);
        });
    }

    /**
     * Retourne l'appréciation textuelle selon la moyenne.
     */
    public function getAppreciationAttribute(): string
    {
        return match (true) {
            $this->average >= 16 => 'Excellent',
            $this->average >= 14 => 'Très bien',
            $this->average >= 12 => 'Bien',
            $this->average >= 10 => 'Assez bien',
            $this->average >= 8  => 'Passable',
            default              => 'Insuffisant',
        };
    }
}