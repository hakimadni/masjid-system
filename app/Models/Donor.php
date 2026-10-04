<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donor extends Model
{
    use HasFactory;

    protected $fillable = [
        'mosque_id',
        'name',
        'phone',
        'email',
        'address',
        'notes',
    ];

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function maskedName(): string
    {
        return 'Hamba Allah';
    }

    public function maskedPhone(): ?string
    {
        if (! $this->phone) {
            return null;
        }

        return substr($this->phone, 0, 4).'****'.substr($this->phone, -2);
    }
}
