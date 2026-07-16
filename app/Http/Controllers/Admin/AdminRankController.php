<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserRank;

class AdminRankController extends Controller
{
    public function index()
    {
        $rankLog = UserRank::with('user', 'rank')->latest('achieved_at')->paginate(30);

        return view('admin.ranks.index', compact('rankLog'));
    }
}
