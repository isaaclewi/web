<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'apprenant_id',
        'evaluation_id',
        'score'
    ];

    public function apprenant()
    {
        return $this->belongsTo(Apprenant::class);
    }

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }
}