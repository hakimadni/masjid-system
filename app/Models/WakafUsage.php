<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WakafUsage extends Model
{
    protected $fillable = [
        'mosque_id',
        'wakaf_record_id',
        'amount',
        'description',
        'usage_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'usage_date' => 'date',
    ];

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }

    public function record(): BelongsTo
    {
        return $this->belongsTo(WakafRecord::class, 'wakaf_record_id');
    }
}