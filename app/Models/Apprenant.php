<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Apprenant extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'user_id',
        'institution_id',
        'niveau_id',
        'filiere_id',
        'matricule',
        'nom',
        'prenom',
        'date_naissance',
        'sexe',
        'annee_academique',
        'status',
        'password',
        // class_id gardé en cache lecture rapide (nullable)
        'class_id',
    ];

    protected $hidden = ['password'];

    /* ═══════════════════════════════════════════
     |  RELATIONS
     ═══════════════════════════════════════════ */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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

    /**
     * Classe actuelle (cache lecture rapide via class_id direct).
     * Utilisé pour les affichages rapides sans charger le pivot.
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }

    /**
     * Toutes les classes (historique) via pivot class_apprenant.
     * Même pattern que Teacher → classes via class_teacher.
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(
            Classe::class,
            'class_apprenant',  // table pivot
            'apprenant_id',     // FK vers Apprenant
            'class_id'          // FK vers Classe
        )
        ->withPivot(['annee_academique', 'date_inscription', 'statut'])
        ->withTimestamps();
    }

    /**
     * Classe active de l'année en cours.
     */
    public function classeActive(): BelongsToMany
    {
        return $this->classes()->wherePivot('statut', 'actif');
    }

    /**
     * Parents / tuteurs.
     */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolParent::class,
            'apprenant_parent',
            'apprenant_id',
            'parent_id'
        )
        ->withPivot('lien')
        ->withTimestamps();
    }

    public function reportCards(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ReportCard::class);
    }

    public function financialRecords(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FinancialRecord::class);
    }

    /**
     * Suivi disciplinaire de l'apprenant.
     */
    public function suiviDisciplinaire(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\SuiviDisciplinaire::class, 'apprenant_id');
    }
 
    /**
     * Incidents disciplinaires d'une année civile donnée.
     */
    public function incidentsAnnee(string $annee): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->suiviDisciplinaire()->where('annee_civile', $annee);
    }
}