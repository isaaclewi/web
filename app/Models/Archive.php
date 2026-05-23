<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Archive extends Model
{
    protected $fillable = [
        'institution_id',
        'nom',
        'annee_academique',
        'categorie',
        'type_export',
        'description',
        'fichier_path',
        'taille_octets',
        'cree_par',
    ];
 
    protected $casts = [
        'taille_octets' => 'integer',
    ];
 
    /* ── Relations ── */
 
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
 
    public function creePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cree_par');
    }
 
    /* ── Accesseurs ── */
 
    /**
     * Taille lisible en Ko / Mo.
     */
    public function getTailleFormateeAttribute(): string
    {
        $octets = $this->taille_octets ?? 0;
        if ($octets >= 1_048_576) {
            return number_format($octets / 1_048_576, 2) . ' Mo';
        }
        if ($octets >= 1_024) {
            return number_format($octets / 1_024, 1) . ' Ko';
        }
        return $octets . ' o';
    }
 
    /**
     * Label lisible de la catégorie.
     */
    public function getCategorieLibelleAttribute(): string
    {
        return match ($this->categorie) {
            'complet'       => 'Complet (tout)',
            'apprenants'    => 'Apprenants',
            'enseignants'   => 'Enseignants',
            'bulletins'     => 'Notes & Bulletins',
            'finances'      => 'Finances',
            'disciplinaire' => 'Disciplinaire',
            'classes'       => 'Classes',
            'planning'      => 'Planning / EDT',
            'staff'         => 'Staff administratif',
            default         => ucfirst($this->categorie),
        };
    }
 
    /**
     * Label lisible du type d'export.
     */
    public function getTypeExportLibelleAttribute(): string
    {
        return match ($this->type_export) {
            'annuel'      => 'Annuel',
            'trimestriel' => 'Trimestriel',
            'manuel'      => 'Manuel',
            default       => ucfirst($this->type_export),
        };
    }
 
    /* ── Scopes ── */
 
    public function scopeParAnnee($q, string $annee)
    {
        return $q->where('annee_academique', $annee);
    }
 
    public function scopeParCategorie($q, string $categorie)
    {
        return $q->where('categorie', $categorie);
    }
}
