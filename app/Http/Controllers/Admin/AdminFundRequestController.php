<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundRequest;
use App\Services\InvestmentService;
use App\Services\WalletService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AdminFundRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $fundRequests = FundRequest::with('user')
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->tap(fn ($q) => $this->applyDateRange($q, $request))
            ->tap(fn ($q) => $this->applySearch($q, $request))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.fund-requests.index', compact('fundRequests', 'status'));
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
                ->orWhereRaw("DATE_FORMAT(created_at, '%d %b %Y') LIKE ?", ["%{$term}%"])
                ->orWhereHas('user', function (Builder $uq) use ($term) {
                    $uq->where('name', 'like', "%{$term}%")
                        ->orWhere('referral_code', 'like', "%{$term}%");
                });
        });
    }

    public function approve(Request $request, FundRequest $fundRequest, WalletService $walletService, InvestmentService $investmentService)
    {
        if ($fundRequest->status !== 'pending') {
            return back()->withErrors(['fund_request' => 'Only pending fund requests can be approved.']);
        }

        try {
            DB::transaction(function () use ($fundRequest, $walletService, $investmentService, $request) {
                $walletService->credit(
                    $fundRequest->user,
                    'deposit',
                    (float) $fundRequest->amount,
                    'Fund deposit approved (BEP20 payment)',
                    FundRequest::class,
                    $fundRequest->id
                );

                $investment = $investmentService->invest($fundRequest->user, (float) $fundRequest->amount);

                $fundRequest->update([
                    'status' => 'approved',
                    'admin_id' => $request->user()->id,
                    'investment_id' => $investment->id,
                    'processed_at' => now(),
                ]);
            });
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['fund_request' => $e->getMessage()]);
        }

        return back()->with('status', "Fund request #{$fundRequest->id} approved — investment created for {$fundRequest->user->name}.");
    }

    public function reject(Request $request, FundRequest $fundRequest)
    {
        $request->validate(['admin_note' => ['nullable', 'string', 'max:255']]);

        if ($fundRequest->status !== 'pending') {
            return back()->withErrors(['fund_request' => 'Only pending fund requests can be rejected.']);
        }

        $fundRequest->update([
            'status' => 'rejected',
            'admin_id' => $request->user()->id,
            'admin_note' => $request->input('admin_note'),
            'processed_at' => now(),
        ]);

        return back()->with('status', "Fund request #{$fundRequest->id} rejected.");
    }
}
