<?php

namespace App\Http\Controllers;

use App\Models\IncomeTransaction;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $type = $request->query('type', 'all');

        $query = IncomeTransaction::with('sourceUser')->where('user_id', $user->id);

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $transactions = $query->latest()->paginate(20);

        $totals = IncomeTransaction::where('user_id', $user->id)
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $levelBreakdown = IncomeTransaction::where('user_id', $user->id)
            ->where('type', 'level_income')
            ->selectRaw('level, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('level')
            ->orderBy('level')
            ->get();

        return view('dashboard.income', compact('transactions', 'totals', 'levelBreakdown', 'type'));
    }
}
