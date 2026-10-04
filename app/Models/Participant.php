<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'mosque_id',
        'animal_id',
        'qurban_saving_id',
        'name',
        'phone',
        'amount_due',
        'amount_paid',
        'payment_status',
        'slot_number',
    ];

    protected function casts(): array
    {
        return [
            'amount_due' => 'decimal:2',
            'amount_paid' => 'decimal:2',
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

    public function qurbanSaving(): BelongsTo
    {
        return $this->belongsTo(QurbanSaving::class, 'qurban_saving_id');
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }
}
