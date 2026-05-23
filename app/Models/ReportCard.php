<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportCard extends Model
{
    protected $fillable = [
        'apprenant_id',
        'class_id',
        'period',
        'average',
        'rank',
        'published'
    ];

    public function apprenant()
    {
        return $this->belongsTo(Apprenant::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }

    public function lines()
    {
        return $this->hasMany(ReportCardLine::class);
    }
}
