<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TransferRequest extends Model
{
    use SoftDeletes;

    protected $table = 'transfer_requests';

    protected $fillable = [
        'apprenant_id',
        'institution_source_id',
        'institution_dest_id',
        'requested_by',
        'processed_by',
        'statut',
        'scope',
        'motif',
        'motif_refus',
        'processed_at',
        'access_token',
        'token_expires_at',
    ];

    protected $casts = [
        'scope'            => 'array',
        'processed_at'     => 'datetime',
        'token_expires_at' => 'datetime',
    ];

    /* ══════════════════════════════════════════════
       RELATIONS
    ══════════════════════════════════════════════ */

    public function apprenant(): BelongsTo
    {
        return $this->belongsTo(Apprenant::class);
    }

    public function institutionSource(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_source_id');
    }

    public function institutionDest(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_dest_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /* ══════════════════════════════════════════════
       SCOPES
    ══════════════════════════════════════════════ */

    /** Demandes reçues par une institution (elle est la source). */
    public function scopeReceivedBy($query, int $institutionId)
    {
        return $query->where('institution_source_id', $institutionId);
    }

    /** Demandes envoyées par une institution (elle est le destinataire). */
    public function scopeSentBy($query, int $institutionId)
    {
        return $query->where('institution_dest_id', $institutionId);
    }

    public function scopePending($query)
    {
        return $query->where('statut', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('statut', 'approved');
    }

    /* ══════════════════════════════════════════════
       ACCESSORS
    ══════════════════════════════════════════════ */

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'pending'   => 'En attente',
            'approved'  => 'Approuvée',
            'rejected'  => 'Refusée',
            'completed' => 'Complétée',
            default     => ucfirst($this->statut),
        };
    }

    public function getStatutColorAttribute(): string
    {
        return match ($this->statut) {
            'pending'   => 'amber',
            'approved'  => 'green',
            'rejected'  => 'red',
            'completed' => 'blue',
            default     => 'gray',
        };
    }

    public function getIsTokenValidAttribute(): bool
    {
        return $this->access_token
            && $this->token_expires_at
            && $this->token_expires_at->isFuture();
    }

    /* ══════════════════════════════════════════════
       HELPERS
    ══════════════════════════════════════════════ */

    /** Génère un jeton d'accès sécurisé valable N heures. */
    public function generateToken(int $hoursValid = 72): string
    {
        $token = Str::random(64);
        $this->update([
            'access_token'     => $token,
            'token_expires_at' => now()->addHours($hoursValid),
        ]);
        return $token;
    }

    /** Révoque le jeton. */
    public function revokeToken(): void
    {
        $this->update([
            'access_token'     => null,
            'token_expires_at' => null,
        ]);
    }

    public static function scopeLabels(): array
    {
        return [
            'identity'   => 'Identité & Informations personnelles',
            'notes'      => 'Notes & Évaluations',
            'bulletins'  => 'Bulletins scolaires',
            'discipline' => 'Dossier disciplinaire',
            'finances'   => 'Situation financière',
            'classes'    => 'Historique des classes',
        ];
    }
}