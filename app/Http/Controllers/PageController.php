<?php

namespace App\Http\Controllers;

use App\Models\Rank;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        $ranks = Rank::withCumulativeTeamBusiness();

        return view('site.home', compact('ranks'));
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
        $ranks = Rank::withCumulativeTeamBusiness()->groupBy('package_group');

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
