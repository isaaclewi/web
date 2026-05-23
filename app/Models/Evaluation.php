<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
        'subject_id',
        'title',
        'type',
        'type_evaluation', // 🔥 AJOUT
        'date',
        'max_score',
        'periode',         // 🔥 AJOUT
        'institution_id',   // 🔥 AJOUT (CRUCIAL)
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
