<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserRank;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AdminRankController extends Controller
{
    public function index(Request $request)
    {
        $rankLog = UserRank::with('user', 'rank')
            ->tap(fn ($q) => $this->applyDateRange($q, $request))
            ->tap(fn ($q) => $this->applySearch($q, $request))
            ->latest('achieved_at')
            ->paginate(30)
            ->withQueryString();

        return view('admin.ranks.index', compact('rankLog'));
    }

    private function applyDateRange(Builder $query, Request $request): void
    {
        if ($request->filled('from')) {
            $query->whereDate('achieved_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('achieved_at', '<=', $request->input('to'));
        }
    }

    private function applySearch(Builder $query, Request $request): void
    {
        $term = trim((string) $request->input('q', ''));

        if ($term === '') {
            return;
        }

        $query->where(function (Builder $q) use ($term) {
            $q->whereRaw("DATE_FORMAT(achieved_at, '%d %b %Y') LIKE ?", ["%{$term}%"])
                ->orWhereHas('user', function (Builder $uq) use ($term) {
                    $uq->where('name', 'like', "%{$term}%")
                        ->orWhere('referral_code', 'like', "%{$term}%");
                })
                ->orWhereHas('rank', function (Builder $rq) use ($term) {
                    $rq->where('name', 'like', "%{$term}%")
                        ->orWhere('package_group', 'like', "%{$term}%")
                        ->orWhere('reward_amount', 'like', "%{$term}%");
                });
        });
    }
}
