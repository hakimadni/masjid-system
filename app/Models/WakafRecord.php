<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WakafRecord extends Model
{
    protected $fillable = [
        'mosque_id',
        'wakaf_wakif_id',
        'wakaf_type',
        'amount',
        'asset_description',
        'purpose',
        'pledged_date',
        'received_date',
        'allocated_date',
        'completed_date',
        'status',
        'usage_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'pledged_date' => 'date',
        'received_date' => 'date',
        'allocated_date' => 'date',
        'completed_date' => 'date',
        'display_publicly' => 'boolean',
    ];

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }

    public function wakif(): BelongsTo
    {
        return $this->belongsTo(WakafWakif::class, 'wakaf_wakif_id');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(WakafUsage::class, 'wakaf_record_id');
    }
}