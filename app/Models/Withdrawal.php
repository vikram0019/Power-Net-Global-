<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    protected $fillable = [
        'user_id',
        'wallet_type',
        'amount',
        'status',
        'otp_code',
        'otp_expires_at',
        'otp_verified_at',
        'admin_id',
        'admin_note',
        'processed_at',
    ];

    protected $hidden = [
        'otp_code',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'otp_expires_at' => 'datetime',
            'otp_verified_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
