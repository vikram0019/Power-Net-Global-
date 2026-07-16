<?php

return [
    'minimum_investment' => 100,

    // % of a member's investment paid to their direct sponsor.
    'direct_reward_percent' => 4,

    // % of a member's investment paid to the sponsor's own sponsor (one level up).
    'direct_reward_upline_percent' => 2,

    // Total level-income pool as a % of the invested amount.
    'level_income_pool_percent' => 4.5,

    // Per-level share of the level-income pool (% of the 4.5% pool), levels 1-20.
    'level_percentages' => [
        1 => 15, 2 => 10, 3 => 5, 4 => 3, 5 => 2, 6 => 1,
        7 => 1, 8 => 1, 9 => 1, 10 => 1,
        11 => 0.5, 12 => 0.5, 13 => 0.5, 14 => 0.5, 15 => 0.5,
        16 => 0.5, 17 => 0.5, 18 => 0.5, 19 => 0.5, 20 => 0.5,
    ],

    // Monthly ROI as % of invested amount, paid for this many months.
    'monthly_roi_percent' => 8,
    'roi_max_months' => 24,

    // Weighting applied to a member's top-3 legs (by team business volume) for rank qualification.
    'leg_weights' => [50, 30, 20],

    // Sentinel value stored in ranks.legs_open meaning "no limit".
    'unlimited_legs' => 255,
];
