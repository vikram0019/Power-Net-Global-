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
        return bccomp((string) $this->roi_total_paid, (string) $this->roiCap(), 2) >= 0;
    }

    /**
     * Lifetime MPG ceiling for this investment — unchanged by the switch from
     * monthly to daily crediting, since the daily rate is just the same 8%
     * monthly total spread across each day of the month.
     */
    public function roiCap(): float
    {
        return round(((float) $this->amount) * (int) config('mlm.roi_max_months') * (float) config('mlm.monthly_roi_percent') / 100, 2);
    }

    /**
     * How many 8%-months' worth of MPG have been paid so far, as a float —
     * used for progress displays now that payouts land daily instead of in
     * whole-month increments.
     */
    public function roiMonthsEquivalent(): float
    {
        $perMonth = ((float) $this->amount) * (float) config('mlm.monthly_roi_percent') / 100;

        return $perMonth > 0 ? ((float) $this->roi_total_paid) / $perMonth : 0;
    }
}
