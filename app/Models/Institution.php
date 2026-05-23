<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'statut_juridique',
        'academic_year',
        'pays',
        'departement',
        'commune',
        'adresse',
        'email',
        'telephone',
        'site_web',
        'devise',
        'date_creation',
        'autorisation_etat',
        'logo',
        'status',
    ];

    public function filieres(): HasMany
    {
        return $this->hasMany(Filiere::class);
    }

    public function apprenants(): HasMany
    {
        return $this->hasMany(Apprenant::class);
    }

    public function administrativeUnits()
    {
        return $this->hasMany(AdministrativeUnit::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function administrativeActivities()
    {
        return $this->hasMany(AdministrativeActivity::class);
    }

    // app/Models/Institution.php
    public function libraryBooks(): HasMany
    {
        return $this->hasMany(LibraryBook::class);
    }

    // Dans Institution.php, ajouter :

    public function niveaux(): HasMany
    {
        return $this->hasMany(Niveau::class);
    }
}
