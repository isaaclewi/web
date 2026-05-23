<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    protected $fillable = [
        'user_id',
        'institution_id',
        'administrative_unit_id',
        'poste',
        'matricule',
        'nom',
        'prenom',
        'telephone',
        'email',
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

    public function administrativeUnit(): BelongsTo
    {
        return $this->belongsTo(AdministrativeUnit::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(AdministrativeActivity::class);
    }

    public function taskAssignments(): HasMany
{
    return $this->hasMany(StaffTaskAssignment::class);
}

public function modulesActifs()
{
    return $this->taskAssignments()
        ->where('actif', true)
        ->with('module')
        ->get()
        ->pluck('module')
        ->filter();
}
}
