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
    'roi_max_months' => 25,

    // Weighting for rank qualification: [power leg, 2nd leg, every remaining leg combined].
    // The 3rd value applies to the *sum* of all legs beyond the top 2, not just the 3rd leg.
    'leg_weights' => [50, 30, 20],

    // Sentinel value stored in ranks.legs_open meaning "no limit".
    'unlimited_legs' => 255,

    // % fee deducted from a withdrawal request before it's sent to admin
    // for approval — applies only to the wallet types listed here.
    'withdrawal_fee_percent' => 5,
    'withdrawal_fee_wallet_types' => ['working', 'rank_reward'],

    // Smallest amount a member may request in a single withdrawal.
    'minimum_withdrawal' => 20,

    // Where public Contact Us form submissions are emailed to.
    'contact_email' => env('CONTACT_EMAIL', 'sopt.png2026@gmail.com'),
];
