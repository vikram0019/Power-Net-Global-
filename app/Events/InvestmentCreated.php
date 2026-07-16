<?php

namespace App\Events;

use App\Models\Investment;
use Illuminate\Foundation\Events\Dispatchable;

class InvestmentCreated
{
    use Dispatchable;

    public function __construct(public Investment $investment)
    {
    }
}
