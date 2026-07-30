<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AdminAnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->paginate(20);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:2000'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        Announcement::create($validated + ['created_by' => $request->user()->id]);

        return redirect()->route('admin.announcements.index')->with('status', 'Announcement published.');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:2000'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $announcement->update($validated);

        return redirect()->route('admin.announcements.index')->with('status', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('status', 'Announcement deleted.');
    }
}
