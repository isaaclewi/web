<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bulletin extends Model
{
    protected $fillable = [
        'apprenant_id', 'institution_id', 'classe_id',
        'annee_academique', 'periode',
        'moyenne_generale', 'moyenne_devoirs', 'moyenne_examens',
        'rang', 'effectif_classe', 'mention', 'admis',
        'detail_matieres',
        'appreciation_conseil', 'appreciation_directeur',
        'publie', 'publie_at', 'publie_par',
        'calcule_at', 'calcule_par',
    ];

    protected $casts = [
        'detail_matieres' => 'array',
        'publie'          => 'boolean',
        'admis'           => 'boolean',
        'publie_at'       => 'datetime',
        'calcule_at'      => 'datetime',
        'moyenne_generale'=> 'decimal:2',
        'moyenne_devoirs' => 'decimal:2',
        'moyenne_examens' => 'decimal:2',
    ];

    public function apprenant(): BelongsTo { return $this->belongsTo(Apprenant::class); }
    public function institution(): BelongsTo { return $this->belongsTo(Institution::class); }
    public function classe(): BelongsTo { return $this->belongsTo(Classe::class); }
    public function publiePar(): BelongsTo { return $this->belongsTo(User::class, 'publie_par'); }
    public function calculePar(): BelongsTo { return $this->belongsTo(User::class, 'calcule_par'); }

    public function periodeLabel(): string
    {
        return match($this->periode) {
            'trimestre1' => '1er Trimestre', 'trimestre2' => '2ème Trimestre',
            'trimestre3' => '3ème Trimestre', 'semestre1'  => '1er Semestre',
            'semestre2'  => '2ème Semestre',  'annuel'     => 'Annuel',
            default      => ucfirst($this->periode),
        };
    }

    /** Scopes */
    public function scopePublie($q) { return $q->where('publie', true); }
    public function scopeNonPublie($q) { return $q->where('publie', false); }
    
}