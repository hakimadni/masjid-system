<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Volunteer extends Model
{
    use HasFactory;

    protected $fillable = [
        'mosque_id',
        'user_id',
        'name',
        'phone',
        'role_type',
        'qr_token',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(VolunteerAvailability::class);
    }

    public function slaughterings(): BelongsToMany
    {
        return $this->belongsToMany(Slaughtering::class)
            ->withPivot('team_role')
            ->withTimestamps();
    }
}
