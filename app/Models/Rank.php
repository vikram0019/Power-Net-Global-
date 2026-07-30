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
