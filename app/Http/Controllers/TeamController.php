<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $rows = DB::select(
            'WITH RECURSIVE subtree AS (
                SELECT id, name, current_rank_id, is_dummy, created_at, 1 AS depth
                FROM users WHERE sponsor_id = ?
                UNION ALL
                SELECT u.id, u.name, u.current_rank_id, u.is_dummy, u.created_at, s.depth + 1
                FROM users u
                INNER JOIN subtree s ON u.sponsor_id = s.id
                WHERE s.depth < 20
            )
            SELECT subtree.*, r.name AS rank_name,
                   COALESCE((SELECT SUM(amount) FROM investments WHERE user_id = subtree.id), 0) AS invested
            FROM subtree
            LEFT JOIN ranks r ON r.id = subtree.current_rank_id
            ORDER BY depth, created_at',
            [$user->id]
        );

        $teamSize = count($rows);
        $totalTeamBusiness = array_sum(array_map(fn ($r) => (float) $r->invested, $rows));

        $tree = $this->buildTree($user);

        return view('dashboard.team', compact('rows', 'teamSize', 'totalTeamBusiness', 'tree'));
    }

    private function buildTree(User $user, int $depth = 0, int $maxDepth = 20): array
    {
        $children = $depth >= $maxDepth
            ? collect()
            : $user->directReferrals()->with('currentRank')->orderBy('created_at')->get();

        return [
            'user' => $user,
            'invested' => $user->totalInvested(),
            'children' => $children->map(fn (User $child) => $this->buildTree($child, $depth + 1, $maxDepth))->all(),
        ];
    }
}
