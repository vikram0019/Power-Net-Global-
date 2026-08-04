<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('dashboard.profile', ['user' => $request->user()]);
    }

    public function uploadImage(Request $request)
    {
        $validated = $request->validate([
            'profile_image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $user = $request->user();

        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $user->update([
            'profile_image' => $request->file('profile_image')->store('profile-images', 'public'),
        ]);

        return back()->with('status', 'Profile image updated.');
    }

    public function removeImage(Request $request)
    {
        $user = $request->user();

        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
            $user->update(['profile_image' => null]);
        }

        return back()->with('status', 'Profile image removed.');
    }
}
