<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ZakatMustahik extends Model
{
    use HasFactory;

    protected $table = 'zakat_mustahik';

    protected $fillable = [
        'mosque_id',
        'name',
        'phone',
        'address',
        'category',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }

    public function distributions(): HasMany
    {
        return $this->hasMany(ZakatDistribution::class, 'mustahik_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}