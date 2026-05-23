<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SujetFichier extends Model
{
    protected $fillable = [
         'sujet_examen_id', 'nom_original', 'chemin',
         'mime_type', 'taille_octets', 'extension',
     ];
 
     public function sujetExamen(): BelongsTo { return $this->belongsTo(SujetExamen::class); }
 
     public function getTailleFormateeAttribute(): string {
         $o = $this->taille_octets ?? 0;
         if ($o >= 1048576) return number_format($o/1048576, 2).' Mo';
         if ($o >= 1024)    return number_format($o/1024, 1).' Ko';
         return $o.' o';
     }
}
