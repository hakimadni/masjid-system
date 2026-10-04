<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Animal extends Model
{
    use HasFactory;

    protected $fillable = [
        'mosque_id',
        'type',
        'weight',
        'price',
        'supplier',
        'location',
        'slaughter_type',
        'vendor',
        'vendor_cost',
        'pickup_schedule',
        'external_status',
        'status',
        'qr_token',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'price' => 'decimal:2',
            'vendor_cost' => 'decimal:2',
            'pickup_schedule' => 'datetime',
        ];
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }

    public function slaughtering(): HasOne
    {
        return $this->hasOne(Slaughtering::class);
    }

    public function distributions(): HasMany
    {
        return $this->hasMany(Distribution::class);
    }

    public function isCow(): bool
    {
        return $this->type === 'sapi';
    }
}
