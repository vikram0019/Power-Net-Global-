<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $tree = $this->buildTree($user);

        $rows = $this->flattenTree($tree);
        usort($rows, fn ($a, $b) => $a->depth <=> $b->depth ?: $a->created_at <=> $b->created_at);

        $teamSize = count($rows);
        $totalTeamBusiness = array_sum(array_map(fn ($r) => (float) $r->invested, $rows));

        return view('dashboard.team', compact('rows', 'teamSize', 'totalTeamBusiness', 'tree'));
    }

    /**
     * Builds the tree via plain Eloquent recursion (portable to any
     * MySQL/MariaDB version — no recursive CTE support required).
     */
    private function buildTree(User $user, int $depth = 0, int $maxDepth = 20): array
    {
        $children = $depth >= $maxDepth
            ? collect()
            : $user->directReferrals()->with('currentRank')->orderBy('created_at')->get();

        $childNodes = $children->map(fn (User $child) => $this->buildTree($child, $depth + 1, $maxDepth))->all();

        return [
            'user' => $user,
            'invested' => $user->totalInvested(),
            'children' => $childNodes,
            'leg_counts' => $this->legMemberCounts($childNodes),
        ];
    }

    /**
     * Member-count version of the Power/2nd/rest-legs weighting used for $
     * team business (TeamBusinessCalculator::weightedTeamBusiness) — counts
     * people instead of dollars, per node, from the already-built subtree.
     */
    private function nodeSize(array $node): int
    {
        return 1 + array_sum(array_map(fn ($child) => $this->nodeSize($child), $node['children']));
    }

    private function legMemberCounts(array $childNodes): array
    {
        $sizes = collect($childNodes)
            ->map(fn ($child) => $this->nodeSize($child))
            ->sortDesc()
            ->values();

        return [
            'power' => $sizes->get(0, 0),
            'second' => $sizes->get(1, 0),
            'rest' => $sizes->slice(2)->sum(),
        ];
    }

    /**
     * Flattens buildTree()'s nested structure into the row shape the
     * Team Payment Detail table expects, mirroring the old recursive-CTE
     * query's columns (id, name, is_dummy, created_at, rank_name, invested,
     * depth) — sort order is applied by the caller.
     */
    private function flattenTree(array $node, int $depth = 1): array
    {
        $rows = [];

        foreach ($node['children'] as $child) {
            $childUser = $child['user'];

            $rows[] = (object) [
                'id' => $childUser->id,
                'name' => $childUser->name,
                'is_dummy' => $childUser->is_dummy,
                'created_at' => $childUser->created_at,
                'rank_name' => $childUser->currentRank->name ?? null,
                'invested' => $child['invested'],
                'depth' => $depth,
            ];

            $rows = array_merge($rows, $this->flattenTree($child, $depth + 1));
        }

        return $rows;
    }
}
