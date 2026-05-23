<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class SuperAdmin extends Authenticatable
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'password',
        'matricule',
    ];

    protected $hidden = [
        'password',
    ];

    // Relation avec le User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}