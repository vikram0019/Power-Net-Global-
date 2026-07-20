<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Services\InvestmentService;
use App\Services\TeamBusinessCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

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

    public function approve(User $user)
    {
        if ($user->status !== 'approval_pending') {
            return back()->withErrors(['user' => 'Only accounts pending approval can be approved.']);
        }

        $user->update(['status' => 'active']);

        return back()->with('status', "{$user->name}'s account has been approved and is now active.");
    }

    public function toggleRoi(User $user)
    {
        $user->update(['roi_enabled' => ! $user->roi_enabled]);

        $state = $user->roi_enabled ? 'enabled' : 'disabled';

        return back()->with('status', "Monthly ROI benefit {$state} for {$user->name}.");
    }

    public function createDummy()
    {
        return view('admin.users.create-dummy');
    }

    public function storeDummy(Request $request, InvestmentService $investmentService)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'mobile' => ['required', 'string', 'max:20', 'unique:users,mobile'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'sponsor_referral_code' => ['required', 'string', 'exists:users,referral_code'],
            'starting_investment' => ['nullable', 'numeric', 'min:' . config('mlm.minimum_investment')],
            'roi_enabled' => ['nullable', 'boolean'],
        ]);

        $sponsor = User::where('referral_code', $validated['sponsor_referral_code'])->firstOrFail();

        $user = User::create([
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'referral_code' => User::generateReferralCode(),
            'sponsor_id' => $sponsor->id,
            'status' => 'active',
            'email_verified_at' => now(),
            'is_dummy' => true,
            'roi_enabled' => $request->boolean('roi_enabled'),
        ]);

        Wallet::firstOrCreate(['user_id' => $user->id]);

        if (! empty($validated['starting_investment'])) {
            $walletService = app(\App\Services\WalletService::class);
            $walletService->credit($user, 'deposit', (float) $validated['starting_investment'], 'Dummy user starting balance (admin)');

            try {
                $investmentService->invest($user, (float) $validated['starting_investment']);
            } catch (InvalidArgumentException $e) {
                return back()->withErrors(['starting_investment' => $e->getMessage()]);
            }
        }

        return redirect()->route('admin.users.show', $user)->with('status', "Dummy user {$user->name} created successfully.");
    }
}
