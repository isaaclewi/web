<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LibraryBook extends Model
{
    use SoftDeletes;

    protected $table = 'library_books';

    protected $fillable = [
        'institution_id',
        'uploaded_by',
        'uploader_role',
        'title',
        'author',
        'isbn',
        'description',
        'cover_path',
        'file_path',
        'file_type',
        'file_size',
        'category',
        'level',
        'language',
        'allow_download',
        'is_published',
        'views',
        'downloads',
    ];

    protected $casts = [
        'allow_download' => 'boolean',
        'is_published'   => 'boolean',
        'file_size'      => 'integer',
        'views'          => 'integer',
        'downloads'      => 'integer',
    ];

    /* ────────────────────────────────────────────
       RELATIONS
    ──────────────────────────────────────────── */

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /* ────────────────────────────────────────────
       SCOPES
    ──────────────────────────────────────────── */

    /**
     * Livres visibles pour un membre d'une institution donnée.
     * Règle : livres globaux (institution_id IS NULL) + livres de son institution.
     */
    public function scopeVisibleFor($query, ?int $institutionId)
    {
        return $query->where('is_published', true)
                     ->where(function ($q) use ($institutionId) {
                         $q->whereNull('institution_id');
                         if ($institutionId) {
                             $q->orWhere('institution_id', $institutionId);
                         }
                     });
    }

    /**
     * Livres appartenant à une institution précise (pour la gestion admin).
     */
    public function scopeForInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    /* ────────────────────────────────────────────
       ACCESSORS
    ──────────────────────────────────────────── */

public function getCoverUrlAttribute(): string
{
    if ($this->cover_path) {
        return 'https://xuyqlouytujiqhoqavcz.storage.supabase.co/storage/v1/object/public/syntriforg/'
            . ltrim($this->cover_path, '/');
    }
    return asset('images/book-placeholder.png');
}

public function getFileUrlAttribute(): string
{
    if ($this->file_path) {
        return 'https://xuyqlouytujiqhoqavcz.storage.supabase.co/storage/v1/object/public/syntriforg/'
            . ltrim($this->file_path, '/');
    }
    return '#';
}

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1_048_576) return round($bytes / 1_048_576, 1) . ' Mo';
        if ($bytes >= 1_024)    return round($bytes / 1_024, 0)     . ' Ko';
        return $bytes . ' o';
    }

    public function getFileIconAttribute(): string
    {
        return match ($this->file_type) {
            'pdf'   => '📄',
            'docx'  => '📝',
            'pptx'  => '📊',
            'xlsx'  => '📋',
            'epub'  => '📚',
            default => '📁',
        };
    }

    public function getIsGlobalAttribute(): bool
    {
        return is_null($this->institution_id);
    }

    /* ────────────────────────────────────────────
       HELPERS
    ──────────────────────────────────────────── */

    public static function fileTypes(): array
    {
        return ['pdf', 'docx', 'pptx', 'xlsx', 'epub', 'other'];
    }

    public static function categories(): array
    {
        return [
            'Mathématiques', 'Sciences', 'Histoire-Géographie', 'Français', 'Anglais',
            'Philosophie', 'Physique-Chimie', 'SVT', 'Économie', 'Informatique',
            'Droit', 'Arts', 'Sport', 'Cours', 'Ressources pédagogiques', 'Autre',
        ];
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function incrementDownloads(): void
    {
        $this->increment('downloads');
    }
}
