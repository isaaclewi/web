<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffTaskAssignment extends Model
{
    protected $fillable = [
        'staff_id', 'module_id', 'institution_id',
        'actif', 'notes', 'assigne_par', 'assigne_at', 'desactive_at',
    ];

    protected $casts = [
        'actif'        => 'boolean',
        'assigne_at'   => 'datetime',
        'desactive_at' => 'datetime',
    ];

    public function staff(): BelongsTo    { return $this->belongsTo(Staff::class); }
    public function module(): BelongsTo   { return $this->belongsTo(StaffTaskModule::class, 'module_id'); }
    public function assigne(): BelongsTo  { return $this->belongsTo(User::class, 'assigne_par'); }
    public function institution(): BelongsTo { return $this->belongsTo(Institution::class); }
}