<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class QurbanSaving extends Model
{
    use HasFactory;

    protected $fillable = [
        'mosque_id',
        'user_id',
        'target_amount',
        'current_balance',
        'status',
        'eligible_kambing',
        'eligible_sapi_share',
    ];

    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'current_balance' => 'decimal:2',
            'eligible_kambing' => 'boolean',
            'eligible_sapi_share' => 'boolean',
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

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }
}
