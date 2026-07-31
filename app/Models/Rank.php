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
     * Each rank's team_business_required is the amount added on top of every
     * earlier rank in the ladder, not an absolute total — e.g. Super Star's
     * stored $5,000 means $2,500 (Start) + $5,000 = $7,500 total needed.
     * This returns all ranks (ordered) annotated with that running total.
     */
    public static function withCumulativeTeamBusiness()
    {
        $running = 0;

        return self::ordered()->get()->map(function (self $rank) use (&$running) {
            $running += (float) $rank->team_business_required;
            $rank->cumulative_team_business_required = $running;

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
