<?php

namespace App\Http\Controllers;

use App\Models\IncomeTransaction;
use App\Models\Investment;
use App\Models\Rank;
use App\Models\User;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        $ranks = Rank::ordered()->get();
        $memberCount = User::where('is_admin', false)->count();
        $totalInvested = Investment::sum('amount');
        $totalPaid = IncomeTransaction::sum('amount');

        return view('site.home', compact('ranks', 'memberCount', 'totalInvested', 'totalPaid'));
    }

    public function about()
    {
        return view('site.about');
    }

    public function service()
    {
        return view('site.service');
    }

    public function plan()
    {
        $ranks = Rank::ordered()->get()->groupBy('package_group');

        return view('site.plan', compact('ranks'));
    }

    public function contact()
    {
        return view('site.contact');
    }

    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // Contact form is stubbed — no outbound email is sent yet.
        return back()->with('status', 'Thanks for reaching out! Our team will get back to you shortly.');
    }
}
