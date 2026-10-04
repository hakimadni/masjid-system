<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrayerSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'mosque_id',
        'schedule_date',
        'prayer_name',
        'prayer_time',
        'imam_name',
        'muadzin_name',
        'khatib_name',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'schedule_date' => 'date',
        ];
    }

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }
}
