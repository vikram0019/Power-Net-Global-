<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    protected $fillable = [
        'code',
        'name',
        'package_group',
        'own_invest_required',
        'team_business_required',
        'reward_amount',
        'legs_open',
        'levels_unlocked',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'own_invest_required' => 'decimal:2',
            'team_business_required' => 'decimal:2',
            'reward_amount' => 'decimal:2',
        ];
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Each rank's own team_business_required amount is split into Power/2nd/
     * Rest buckets (config('mlm.leg_weights'), e.g. 50/30/20) and each
     * bucket accumulates its own running total across the ladder,
     * independently of the other two. Start is the one exception: it only
     * requires 2 legs open, so its own amount splits 50/50 across Power/2nd
     * only — it never contributes to the Rest bucket, which is why Rest
     * targets only start appearing from Super Star onward.
     *
     * Returns all ranks (ordered) annotated with power_target/second_target/
     * rest_target — the actual cumulative dollar amount each bucket must
     * independently clear for that rank.
     */
    public static function withCumulativeBucketTargets()
    {
        [$powerWeight, $secondWeight, $restWeight] = config('mlm.leg_weights');

        $cumPower = 0.0;
        $cumSecond = 0.0;
        $cumRest = 0.0;

        return self::ordered()->get()->map(function (self $rank) use (&$cumPower, &$cumSecond, &$cumRest, $powerWeight, $secondWeight, $restWeight) {
            $required = (float) $rank->team_business_required;

            if ($rank->code === 'start') {
                $cumPower += $required * 0.5;
                $cumSecond += $required * 0.5;
            } else {
                $cumPower += $required * $powerWeight / 100;
                $cumSecond += $required * $secondWeight / 100;
                $cumRest += $required * $restWeight / 100;
            }

            $rank->power_target = $cumPower;
            $rank->second_target = $cumSecond;
            $rank->rest_target = $cumRest;

            return $rank;
        });
    }

    public function getIconAttribute(): string
    {
        return match (strtolower($this->package_group ?? '')) {
            'star' => 'bi-star-fill',
            'eagle' => 'bi-award-fill',
            'diamond' => 'bi-gem',
            'crown' => 'bi-trophy-fill',
            'universal' => 'bi-globe-americas',
            default => 'bi-person-badge',
        };
    }
}
