<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classe extends Model
{
    //
    protected $table = 'classes';

    protected $fillable = [
        'institution_id',
        'niveau_id',
        'filiere_id',
        'name',
        'code',
    ];

    /* ===============================
        RELATIONS
    =============================== */

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }

    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    public function apprenants(): HasMany
    {
        return $this->hasMany(Apprenant::class, 'class_id');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(
            Teacher::class,
            'class_teacher',
            'class_id',   // clé dans pivot
            'teacher_id'  // clé teacher
        );
    }
}
