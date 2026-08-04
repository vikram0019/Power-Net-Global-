<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Services\InvestmentService;
use App\Services\TeamBusinessCalculator;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $users = User::with('currentRank', 'sponsor', 'wallet')
            ->where('is_admin', false)
            ->where('status', '!=', 'pending')
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

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email,' . $user->id],
            'mobile' => ['required', 'string', 'max:20', 'unique:users,mobile,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->mobile = $validated['mobile'];
        $user->roi_enabled = $request->boolean('roi_enabled');

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.show', $user)->with('status', "{$user->name}'s account has been updated.");
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

    public function addFund(Request $request, User $user, WalletService $walletService, InvestmentService $investmentService)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $amount = (float) $validated['amount'];

        try {
            DB::transaction(function () use ($user, $amount, $walletService, $investmentService) {
                $walletService->credit($user, 'deposit', $amount, 'Manual fund addition by admin');
                $investmentService->invest($user, $amount);
            });
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['amount' => $e->getMessage()]);
        }

        return back()->with('status', '$' . number_format($amount, 2) . " added and invested for {$user->name}.");
    }

    public function withdrawFund(Request $request, User $user, WalletService $walletService, InvestmentService $investmentService)
    {
        $validated = $request->validate([
            'wallet_type' => ['required', 'in:roi,working,rank_reward,deposit'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $amount = (float) $validated['amount'];

        try {
            DB::transaction(function () use ($request, $user, $validated, $amount, $walletService, $investmentService) {
                $withdrawal = Withdrawal::create([
                    'user_id' => $user->id,
                    'wallet_type' => $validated['wallet_type'],
                    'amount' => $amount,
                    'fee_amount' => 0,
                    'net_amount' => $amount,
                    'status' => 'paid',
                    'admin_id' => $request->user()->id,
                    'admin_note' => 'Manual withdrawal by admin',
                    'processed_at' => now(),
                ]);

                if ($validated['wallet_type'] === 'deposit') {
                    // "Investment" — draws down invested principal directly,
                    // not a wallet balance column.
                    $investmentService->withdrawPrincipal($user, $amount);
                } else {
                    $walletService->debit(
                        $user,
                        $validated['wallet_type'],
                        $amount,
                        'Manual withdrawal by admin',
                        Withdrawal::class,
                        $withdrawal->id
                    );
                }
            });
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['amount' => $e->getMessage()]);
        }

        return back()->with('status', '$' . number_format($amount, 2) . " withdrawn from {$user->name}'s wallet.");
    }

    public function uploadProfileImage(Request $request, User $user)
    {
        $validated = $request->validate([
            'profile_image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $user->update([
            'profile_image' => $request->file('profile_image')->store('profile-images', 'public'),
        ]);

        return back()->with('status', "Profile image updated for {$user->name}.");
    }

    public function removeProfileImage(User $user)
    {
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
            $user->update(['profile_image' => null]);
        }

        return back()->with('status', "Profile image removed for {$user->name}.");
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
