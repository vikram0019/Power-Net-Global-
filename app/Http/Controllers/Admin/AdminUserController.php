<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TeamBusinessCalculator;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $users = User::with('currentRank', 'sponsor')
            ->where('is_admin', false)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                        ->orWhere('referral_code', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function show(User $user, TeamBusinessCalculator $calculator)
    {
        $user->load('currentRank', 'sponsor', 'wallet', 'directReferrals.currentRank');

        $totalInvested = $user->totalInvested();
        $teamSize = $calculator->totalTeamSize($user);
        $teamBusiness = $calculator->weightedTeamBusiness($user);
        $rankHistory = $user->rankHistory()->with('rank')->latest('achieved_at')->get();
        $investments = $user->investments()->latest()->get();

        return view('admin.users.show', compact('user', 'totalInvested', 'teamSize', 'teamBusiness', 'rankHistory', 'investments'));
    }
}
