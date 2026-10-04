<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Distribution;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Slaughtering extends Model
{
    use HasFactory;

    protected $fillable = [
        'mosque_id',
        'animal_id',
        'date',
        'location',
        'cut_time',
        'meat_total_kg',
        'distribution_status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'cut_time' => 'datetime',
            'meat_total_kg' => 'decimal:2',
        ];
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }

    public function volunteers(): BelongsToMany
    {
        return $this->belongsToMany(Volunteer::class)
            ->withPivot('team_role')
            ->withTimestamps();
    }

    public function distributions(): HasMany
    {
        return $this->hasMany(Distribution::class);
    }
}
