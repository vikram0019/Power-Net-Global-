<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use App\Models\Rank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    public function home()
    {
        $ranks = Rank::ordered()->get();

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
        $ranks = Rank::ordered()->get()->groupBy('package_group');

        return view('site.plan', compact('ranks'));
    }

    public function contact()
    {
        return view('site.contact');
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        try {
            Mail::to(config('mlm.contact_email'))->send(new ContactMessageMail(
                $validated['name'],
                $validated['email'],
                $validated['message'],
            ));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('contact')->with('status', 'Thanks for reaching out! Our team will get back to you shortly.');
    }
}
