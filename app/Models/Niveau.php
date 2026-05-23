<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Niveau extends Model
{
    protected $fillable = [
        'institution_id',
        'name',
        'cycle',
    ];

    /* ── Relations ── */

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(Classe::class);
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'niveau_teacher')
                    ->withTimestamps();
    }

    public function filieres(): BelongsToMany
    {
        return $this->belongsToMany(Filiere::class, 'filiere_niveau');
    }

    public function apprenants(): HasMany
    {
        return $this->hasMany(Apprenant::class);
    }
}