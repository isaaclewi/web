<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AdministrativeActivity extends Model
{
    protected $fillable = [
        'institution_id',
        'administrative_unit_id',
        'title',
        'description',
        'category',
        'status',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function administrativeUnit(): BelongsTo
    {
        return $this->belongsTo(AdministrativeUnit::class);
    }

    /**
     * Activité liée aux élèves / étudiants
     */
    public function apprenants(): BelongsToMany
    {
        return $this->belongsToMany(Apprenant::class, 'activity_apprenant')
            ->withTimestamps();
    }

    /**
     * Activité liée aux enseignants
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'activity_teacher')
            ->withTimestamps();
    }

    /**
     * Activité liée aux parents
     */
    public function parents()
    {
        return $this->belongsToMany(SchoolParent::class, 'activity_parent')
            ->withTimestamps();
    }
}
