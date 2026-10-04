<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WakafWakif extends Model
{
    protected $fillable = [
        'mosque_id',
        'name',
        'phone',
        'email',
        'address',
        'display_publicly',
    ];

    protected $casts = [
        'display_publicly' => 'boolean',
    ];

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(WakafRecord::class, 'wakaf_wakif_id');
    }
}