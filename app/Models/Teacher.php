<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Teacher extends Model
{
    protected $fillable = [
        'user_id',
        'institution_id',
        'matricule',
        'nom',
        'prenom',
        'sexe',
        'telephone',
        'email',
        'specialite',
        'type_contrat',
        'date_recrutement',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Niveaux enseignés
     * Pivot : niveau_teacher (teacher_id, niveau_id) — sans timestamps
     */
    public function niveaux(): BelongsToMany
    {
        return $this->belongsToMany(Niveau::class, 'niveau_teacher', 'teacher_id', 'niveau_id');
        // Si ta table pivot HAS created_at/updated_at, remplace par :
        // ->withTimestamps();
    }

    /**
     * Filières enseignées
     * Pivot : filiere_teacher (teacher_id, filiere_id) — sans timestamps
     */
    public function filieres(): BelongsToMany
    {
        return $this->belongsToMany(Filiere::class, 'filiere_teacher', 'teacher_id', 'filiere_id');
    }

    /**
     * Classes assignées
     * Pivot : class_teacher (teacher_id, class_id) — sans timestamps
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(
            Classe::class,
            'class_teacher',
            'teacher_id',
            'class_id'
        );
    }
}
