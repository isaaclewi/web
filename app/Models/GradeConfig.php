<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeConfig extends Model
{
    protected $fillable = [
        'institution_id', 'annee_academique',
        'note_max', 'note_passage',
        'pct_devoirs', 'pct_examen',
        'decimales', 'mentions',
        'compensation_active', 'seuil_compensation',
        'type_periodes', 'nb_periodes',
    ];

    protected $casts = [
        'mentions'              => 'array',
        'compensation_active'   => 'boolean',
        'note_max'              => 'decimal:2',
        'note_passage'          => 'decimal:2',
        'pct_devoirs'           => 'decimal:2',
        'pct_examen'            => 'decimal:2',
        'seuil_compensation'    => 'decimal:2',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /** Retourne la mention selon la moyenne */
    public function getMention(float $moyenne): ?string
    {
        if (empty($this->mentions)) return null;
        $sorted = collect($this->mentions)->sortByDesc('min');
        foreach ($sorted as $m) {
            if ($moyenne >= (float) $m['min']) return $m['libelle'];
        }
        return null;
    }

    /** Mentions par défaut /20 */
    public static function defaultMentions(): array
    {
        return [
            ['libelle' => 'Excellent',       'min' => 18],
            ['libelle' => 'Très bien',        'min' => 16],
            ['libelle' => 'Bien',             'min' => 14],
            ['libelle' => 'Assez bien',       'min' => 12],
            ['libelle' => 'Passable',         'min' => 10],
            ['libelle' => 'Insuffisant',      'min' => 0],
        ];
    }
}