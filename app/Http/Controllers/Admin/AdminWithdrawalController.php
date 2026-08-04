<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Services\WalletService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use InvalidArgumentException;

class AdminWithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'otp_verified');

        $withdrawals = Withdrawal::with('user')
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->tap(fn ($q) => $this->applyDateRange($q, $request))
            ->tap(fn ($q) => $this->applySearch($q, $request))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.withdrawals.index', compact('withdrawals', 'status'));
    }

    private function applyDateRange(Builder $query, Request $request): void
    {
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }
    }

    private function applySearch(Builder $query, Request $request): void
    {
        $term = trim((string) $request->input('q', ''));

        if ($term === '') {
            return;
        }

        $query->where(function (Builder $q) use ($term) {
            $q->where('amount', 'like', "%{$term}%")
                ->orWhere('bep20_address', 'like', "%{$term}%")
                ->orWhereRaw("DATE_FORMAT(created_at, '%d %b %Y') LIKE ?", ["%{$term}%"])
                ->orWhereHas('user', function (Builder $uq) use ($term) {
                    $uq->where('name', 'like', "%{$term}%")
                        ->orWhere('referral_code', 'like', "%{$term}%");
                });
        });
    }

    public function approve(Request $request, Withdrawal $withdrawal, WalletService $walletService)
    {
        if ($withdrawal->status !== 'otp_verified') {
            return back()->withErrors(['withdrawal' => 'Only OTP-verified withdrawals can be approved.']);
        }

        try {
            $walletService->debit(
                $withdrawal->user,
                $withdrawal->wallet_type,
                (float) $withdrawal->amount,
                'Withdrawal approved and paid',
                Withdrawal::class,
                $withdrawal->id
            );
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['withdrawal' => $e->getMessage()]);
        }

        $withdrawal->update([
            'status' => 'paid',
            'admin_id' => $request->user()->id,
            'processed_at' => now(),
        ]);

        return back()->with('status', "Withdrawal #{$withdrawal->id} approved and paid.");
    }

    public function reject(Request $request, Withdrawal $withdrawal)
    {
        $request->validate(['admin_note' => ['nullable', 'string', 'max:255']]);

        if (! in_array($withdrawal->status, ['pending', 'otp_verified'], true)) {
            return back()->withErrors(['withdrawal' => 'This withdrawal cannot be rejected.']);
        }

        $withdrawal->update([
            'status' => 'rejected',
            'admin_id' => $request->user()->id,
            'admin_note' => $request->input('admin_note'),
            'processed_at' => now(),
        ]);

        return back()->with('status', "Withdrawal #{$withdrawal->id} rejected.");
    }
}
