<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmploiDuTemps extends Model
{
    protected $table = 'emplois_du_temps';

    protected $fillable = [
        'institution_id','classe_id','subject_id','teacher_id',
        'jour','heure_debut','heure_fin','type','salle',
        'annee_academique','periode','couleur','notes','statut',
    ];

    public function institution(): BelongsTo { return $this->belongsTo(Institution::class); }
    public function classe(): BelongsTo      { return $this->belongsTo(Classe::class); }
    public function subject(): BelongsTo     { return $this->belongsTo(Subject::class); }
    public function teacher(): BelongsTo     { return $this->belongsTo(Teacher::class); }
    public function seances(): HasMany       { return $this->hasMany(SeanceCours::class, 'emploi_du_temps_id'); }

    public static function jourLabels(): array
    {
        return ['lundi'=>'Lundi','mardi'=>'Mardi','mercredi'=>'Mercredi',
                'jeudi'=>'Jeudi','vendredi'=>'Vendredi','samedi'=>'Samedi'];
    }

    public static function typeLabels(): array
    {
        return ['cours'=>'Cours','evaluation'=>'Évaluation','examen'=>'Examen',
                'rattrapage'=>'Rattrapage','activite'=>'Activité','pause'=>'Pause'];
    }

    public static function typeColors(): array
    {
        return ['cours'=>'#3b82f6','evaluation'=>'#f59e0b','examen'=>'#ef4444',
                'rattrapage'=>'#8b5cf6','activite'=>'#10b981','pause'=>'#9ca3af'];
    }

    public function getTypeLabelAttribute(): string
    { return self::typeLabels()[$this->type] ?? $this->type; }

    public function getDureeAttribute(): int
    {
        [$h1,$m1] = explode(':', $this->heure_debut);
        [$h2,$m2] = explode(':', $this->heure_fin);
        return ($h2*60+$m2) - ($h1*60+$m1);
    }
}