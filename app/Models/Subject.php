<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'institution_id',
        'class_id',
        'teacher_id',
        'name',
        'coefficient'
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}
