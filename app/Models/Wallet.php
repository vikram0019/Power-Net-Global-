<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'deposit_balance',
        'roi_balance',
        'working_balance',
    ];

    protected function casts(): array
    {
        return [
            'deposit_balance' => 'decimal:2',
            'roi_balance' => 'decimal:2',
            'working_balance' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
