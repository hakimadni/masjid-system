<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolunteerAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'volunteer_id',
        'available_date',
        'start_time',
        'end_time',
    ];

    protected function casts(): array
    {
        return [
            'available_date' => 'date',
        ];
    }

    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(Volunteer::class);
    }
}
