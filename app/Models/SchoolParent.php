<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolParent extends Model
{
    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        'nom',
        'prenom',
        'sexe',
        'matricule',
        'telephone',
        'email',
        'profession',
        'adresse',
        'status',
    ];

    /* ═══════════════════════════════════════════
     |  RELATIONS
     ═══════════════════════════════════════════ */

    /**
     * Compte utilisateur associé.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Enfants (apprenants) de ce parent.
     *
     * Même table pivot forcée que du côté Apprenant :
     * "apprenant_parent", avec parent_id → id dans la table "parents".
     */
    public function apprenants(): BelongsToMany
    {
        return $this->belongsToMany(
            Apprenant::class,
            'apprenant_parent',  // table pivot réelle
            'parent_id',         // FK vers ce modèle (SchoolParent)
            'apprenant_id'       // FK vers Apprenant
        )
        ->withPivot('lien')
        ->withTimestamps();
    }
}