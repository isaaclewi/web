<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffTaskModule extends Model
{
    protected $fillable = [
        'key', 'label', 'description', 'icone',
        'roles_autorises', 'route_prefix', 'actif', 'ordre',
    ];

    protected $casts = [
        'roles_autorises' => 'array',
        'actif'           => 'boolean',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(StaffTaskAssignment::class, 'module_id');
    }

    /** Modules compatibles avec un rôle donné */
    public static function pourRole(string $role)
    {
        return static::where('actif', true)
            ->whereJsonContains('roles_autorises', $role)
            ->orderBy('ordre')
            ->get();
    }
}