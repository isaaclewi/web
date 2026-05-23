<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgrammePaiement extends Model
{
    protected $table = 'programmes_paiement';
    protected $fillable = [
        'institution_id','libelle','niveau_id','classe_id','montant','devise',
        'date_echeance','date_debut_rappel','jours_grace','type_frais',
        'annee_academique','periode','statut','description','obligatoire','ordre',
    ];
    protected $casts = [
        'date_echeance'      => 'date',
        'date_debut_rappel'  => 'date',
        'obligatoire'        => 'boolean',
        'montant'            => 'decimal:2',
    ];

    public function institution(): BelongsTo { return $this->belongsTo(Institution::class); }
    public function niveau(): BelongsTo      { return $this->belongsTo(Niveau::class); }
    public function classe(): BelongsTo      { return $this->belongsTo(Classe::class); }

    public static function typeLabels(): array
    {
        return [
            'inscription'=>'Inscription','scolarite'=>'Scolarité','examen'=>'Examen',
            'tenue'=>'Tenue','transport'=>'Transport','cantine'=>'Cantine',
            'activite'=>'Activité extra','autre'=>'Autre',
        ];
    }

    public static function periodeLabels(): array
    {
        return [
            'annuel'=>'Annuel','trimestre1'=>'Trimestre 1','trimestre2'=>'Trimestre 2',
            'trimestre3'=>'Trimestre 3','semestre1'=>'Semestre 1','semestre2'=>'Semestre 2',
            'mensuel'=>'Mensuel',
        ];
    }

    public function isEchue(): bool
    {
        return now()->gt($this->date_echeance->addDays($this->jours_grace));
    }

    public function getTypeLabelAttribute(): string
    { return self::typeLabels()[$this->type_frais] ?? $this->type_frais; }

    public function getJoursRestantsAttribute(): int
    {
        return now()->diffInDays($this->date_echeance->addDays($this->jours_grace), false);
    }
}