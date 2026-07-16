<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Investment extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'roi_months_paid',
        'roi_total_paid',
        'last_roi_paid_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'roi_total_paid' => 'decimal:2',
            'last_roi_paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isRoiComplete(): bool
    {
        return $this->roi_months_paid >= 24;
    }
}
