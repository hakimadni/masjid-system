<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZakatDistribution extends Model
{
    use HasFactory;

    protected $table = 'zakat_distributions';

    protected $fillable = [
        'mosque_id',
        'mustahik_id',
        'muzakki_id',
        'zakat_type',
        'money_amount',
        'rice_amount',
        'distribution_date',
        'received',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'money_amount' => 'decimal:2',
            'rice_amount' => 'decimal:2',
            'distribution_date' => 'date',
            'received' => 'boolean',
        ];
    }

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }

    public function mustahik(): BelongsTo
    {
        return $this->belongsTo(ZakatMustahik::class, 'mustahik_id');
    }

    public function muzakki(): BelongsTo
    {
        return $this->belongsTo(ZakatMuzakki::class, 'muzakki_id');
    }
}