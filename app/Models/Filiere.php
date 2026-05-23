<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Filiere extends Model
{
    protected $fillable = [
        'institution_id',
        'name',
    ];

    /* ===============================
        RELATIONS
    =============================== */

    /**
     * Institution propriétaire
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Niveaux liés à cette filière
     */
    public function niveaux(): BelongsToMany
    {
        return $this->belongsToMany(Niveau::class, 'filiere_niveau');
    }

    /**
     * Classes appartenant à cette filière
     */
    public function classes(): HasMany
    {
        return $this->hasMany(Classe::class);
    }

    /**
     * Enseignants intervenant dans cette filière
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'filiere_teacher')
            ->withTimestamps();
    }

    /**
     * Étudiants inscrits dans cette filière
     */
    public function apprenants(): HasMany
    {
        return $this->hasMany(Apprenant::class);
    }
}