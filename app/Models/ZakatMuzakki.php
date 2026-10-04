<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZakatMuzakki extends Model
{
    use HasFactory;

    protected $table = 'zakat_muzakki';

    protected $fillable = [
        'mosque_id',
        'name',
        'phone',
        'zakat_type',
        'payment_form',
        'money_amount',
        'rice_amount',
        'payment_date',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'money_amount' => 'decimal:2',
            'rice_amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('zakat_type', $type);
    }
}