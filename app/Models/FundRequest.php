<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundRequest extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'screenshot_path',
        'status',
        'admin_id',
        'admin_note',
        'investment_id',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
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

    public function investment(): BelongsTo
    {
        return $this->belongsTo(Investment::class);
    }
}
