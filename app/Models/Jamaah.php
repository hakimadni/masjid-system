<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jamaah extends Model
{
    use HasFactory;

    protected $fillable = [
        'mosque_id',
        'name',
        'gender',
        'category',
        'phone',
        'email',
        'address',
        'birth_date',
        'family_role',
        'status',
        'notes',
    ];

    public function maskedPhone(): ?string
    {
        if (! $this->phone) return null;
        return substr($this->phone, 0, 4) . '****' . substr($this->phone, -2);
    }

    public function maskedEmail(): ?string
    {
        if (! $this->email) return null;
        $parts = explode('@', $this->email);
        if (count($parts) !== 2) return '***@***';
        return substr($parts[0], 0, 2) . '***@' . $parts[1];
    }

    public function maskedAddress(): ?string
    {
        if (! $this->address) return null;
        return '*** (Data disembunyikan)';
    }

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }
}
