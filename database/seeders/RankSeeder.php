<?php

namespace Database\Seeders;

use App\Models\Rank;
use Illuminate\Database\Seeder;

class RankSeeder extends Seeder
{
    public function run(): void
    {
        $unlimited = config('mlm.unlimited_legs');

        $ranks = [
            // Star Package
            ['code' => 'start', 'name' => 'Star', 'package_group' => 'Star', 'own_invest_required' => 100, 'team_business_required' => 2500, 'reward_amount' => 100, 'legs_open' => 2, 'levels_unlocked' => 1, 'sort_order' => 1],
            ['code' => 'super_star', 'name' => 'Super Star', 'package_group' => 'Star', 'own_invest_required' => 100, 'team_business_required' => 5000, 'reward_amount' => 200, 'legs_open' => 3, 'levels_unlocked' => 2, 'sort_order' => 2],
            ['code' => 'seven_star', 'name' => 'Seven Star', 'package_group' => 'Star', 'own_invest_required' => 100, 'team_business_required' => 10000, 'reward_amount' => 400, 'legs_open' => 4, 'levels_unlocked' => 3, 'sort_order' => 3],

            // Eagle Package
            ['code' => 'eagle', 'name' => 'Eagle', 'package_group' => 'Eagle', 'own_invest_required' => 500, 'team_business_required' => 25000, 'reward_amount' => 750, 'legs_open' => 10, 'levels_unlocked' => 20, 'sort_order' => 4],
            ['code' => 'emerald', 'name' => 'Emerald', 'package_group' => 'Eagle', 'own_invest_required' => 500, 'team_business_required' => 50000, 'reward_amount' => 1500, 'legs_open' => $unlimited, 'levels_unlocked' => 20, 'sort_order' => 5],
            ['code' => 'executive', 'name' => 'Executive', 'package_group' => 'Eagle', 'own_invest_required' => 500, 'team_business_required' => 100000, 'reward_amount' => 3000, 'legs_open' => $unlimited, 'levels_unlocked' => 20, 'sort_order' => 6],

            // Diamond Package
            ['code' => 'diamond', 'name' => 'Diamond', 'package_group' => 'Diamond', 'own_invest_required' => 2000, 'team_business_required' => 250000, 'reward_amount' => 5000, 'legs_open' => $unlimited, 'levels_unlocked' => 20, 'sort_order' => 7],
            ['code' => 'royal_diamond', 'name' => 'Royal Diamond', 'package_group' => 'Diamond', 'own_invest_required' => 2000, 'team_business_required' => 500000, 'reward_amount' => 10000, 'legs_open' => $unlimited, 'levels_unlocked' => 20, 'sort_order' => 8],
            ['code' => 'black_diamond', 'name' => 'Black Diamond', 'package_group' => 'Diamond', 'own_invest_required' => 2000, 'team_business_required' => 1000000, 'reward_amount' => 20000, 'legs_open' => $unlimited, 'levels_unlocked' => 20, 'sort_order' => 9],

            // Crown Club
            ['code' => 'crown', 'name' => 'Crown', 'package_group' => 'Crown', 'own_invest_required' => 5000, 'team_business_required' => 2500000, 'reward_amount' => 25000, 'legs_open' => $unlimited, 'levels_unlocked' => 20, 'sort_order' => 10],
            ['code' => 'royal_crown', 'name' => 'Royal Crown', 'package_group' => 'Crown', 'own_invest_required' => 5000, 'team_business_required' => 5000000, 'reward_amount' => 50000, 'legs_open' => $unlimited, 'levels_unlocked' => 20, 'sort_order' => 11],
            ['code' => 'crown_ambassador', 'name' => 'Crown Ambassador', 'package_group' => 'Crown', 'own_invest_required' => 5000, 'team_business_required' => 10000000, 'reward_amount' => 100000, 'legs_open' => $unlimited, 'levels_unlocked' => 20, 'sort_order' => 12],

            // Universal
            ['code' => 'crown_ambassador_universal', 'name' => 'Universal Crown', 'package_group' => 'Universal', 'own_invest_required' => 10000, 'team_business_required' => 30000000, 'reward_amount' => 250000, 'legs_open' => $unlimited, 'levels_unlocked' => 20, 'sort_order' => 13],
        ];

        foreach ($ranks as $rank) {
            Rank::updateOrCreate(['code' => $rank['code']], $rank);
        }
    }
}
