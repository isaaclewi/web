<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SujetExamen extends Model
{
     protected $fillable = [
         'teacher_id', 'institution_id', 'subject_id', 'classe_id',
         'titre', 'type', 'date_evaluation', 'duree_minutes',
         'instructions', 'statut', 'feedback_admin', 'traite_par', 'traite_at',
     ];
 
     protected $casts = [
         'date_evaluation' => 'date',
        'traite_at'       => 'datetime',
     ];
 
     public function teacher(): BelongsTo { return $this->belongsTo(Teacher::class); }
     public function institution(): BelongsTo { return $this->belongsTo(Institution::class); }
     public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
     public function classe(): BelongsTo { return $this->belongsTo(Classe::class); }
     public function traitePar(): BelongsTo { return $this->belongsTo(User::class, 'traite_par'); }
     public function fichiers(): HasMany { return $this->hasMany(SujetFichier::class); }
 
     public function getStatutLabelAttribute(): string {
         return match($this->statut) {
             'en_attente' => 'En attente', 'recu'   => 'Reçu',
             'valide'     => 'Validé',     'rejete' => 'Rejeté',
             'archive'    => 'Archivé',    default  => ucfirst($this->statut),
         };
     }
}
