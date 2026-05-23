<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeanceCours extends Model
{
    protected $table = 'seances_cours';
    protected $fillable = [
        'institution_id','emploi_du_temps_id','classe_id','subject_id','teacher_id',
        'date_seance','heure_debut','heure_fin','type','salle',
        'titre','description','objectifs','statut','motif_annulation',
        'date_report','evaluation_id','annee_academique',
    ];
    protected $casts = [
        'date_seance' => 'date',
        'date_report' => 'date',
    ];

    public function institution(): BelongsTo    { return $this->belongsTo(Institution::class); }
    public function emploiDuTemps(): BelongsTo  { return $this->belongsTo(EmploiDuTemps::class, 'emploi_du_temps_id'); }
    public function classe(): BelongsTo         { return $this->belongsTo(Classe::class); }
    public function subject(): BelongsTo        { return $this->belongsTo(Subject::class); }
    public function teacher(): BelongsTo        { return $this->belongsTo(Teacher::class); }
    public function evaluation(): BelongsTo     { return $this->belongsTo(Evaluation::class); }

    public static function statutLabels(): array
    {
        return ['planifiee'=>'Planifiée','realisee'=>'Réalisée',
                'annulee'=>'Annulée','reportee'=>'Reportée'];
    }
    public function getStatutLabelAttribute(): string
    { return self::statutLabels()[$this->statut] ?? $this->statut; }
}