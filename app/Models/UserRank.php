<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRank extends Model
{
    protected $fillable = [
        'user_id',
        'rank_id',
        'achieved_at',
        'reward_paid',
    ];

    protected function casts(): array
    {
        return [
            'achieved_at' => 'datetime',
            'reward_paid' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }
}
