<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'institution_id',
        'phone',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // ✅ SUPPRIMÉ : 'password' => 'hashed'
            // Le controller utilise Hash::make() manuellement
            // Le cast 'hashed' + Hash::make() = double hash = connexion impossible
        ];
    }

    /* ══════════════════════════════════════════════════════════
     | RELATIONS
     ══════════════════════════════════════════════════════════ */

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function parentProfile()
    {
        return $this->hasOne(SchoolParent::class);
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function apprenant()
    {
        return $this->hasOne(Apprenant::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }
}