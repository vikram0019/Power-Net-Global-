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
        $this->tagTeams($tree);

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
            'leg_stats' => $this->legStats($childNodes),
        ];
    }

    /**
     * Same Power/2nd/rest-legs ranking as $ team business
     * (TeamBusinessCalculator::weightedTeamBusiness) — legs are ranked by
     * $ business size (the canonical definition used everywhere else in the
     * app), computed per node from the already-built subtree. Each leg
     * reports both its member count and its total $ investment.
     */
    private function nodeStats(array $node): array
    {
        $count = 1;
        $investment = (float) $node['invested'];

        foreach ($node['children'] as $child) {
            $childStats = $this->nodeStats($child);
            $count += $childStats['count'];
            $investment += $childStats['investment'];
        }

        return ['count' => $count, 'investment' => $investment];
    }

    private function legStats(array $childNodes): array
    {
        $legs = collect($childNodes)
            ->map(fn ($child) => $this->nodeStats($child))
            ->sortByDesc('investment')
            ->values();

        $empty = ['count' => 0, 'investment' => 0.0];

        return [
            'power' => $legs->get(0, $empty),
            'second' => $legs->get(1, $empty),
            'rest' => [
                'count' => $legs->slice(2)->sum('count'),
                'investment' => $legs->slice(2)->sum('investment'),
            ],
        ];
    }

    /**
     * Tags every node with the team letter (A/B/C) of whichever top-level
     * direct leg it descends from — A is the root's largest ($) direct leg
     * (Power), B is the next largest (2nd), C is every leg beyond that
     * (Rest). The whole subtree under a given direct leg shares its letter,
     * so the tree can be colored consistently by leg regardless of depth.
     */
    private function tagTeams(array &$tree): void
    {
        $order = collect($tree['children'])
            ->map(fn ($child, $i) => ['i' => $i, 'investment' => $this->nodeStats($child)['investment']])
            ->sortByDesc('investment')
            ->values();

        foreach ($order as $rank => $entry) {
            $team = match (true) {
                $rank === 0 => 'A',
                $rank === 1 => 'B',
                default => 'C',
            };
            $this->propagateTeam($tree['children'][$entry['i']], $team);
        }
    }

    private function propagateTeam(array &$node, string $team): void
    {
        $node['team'] = $team;

        foreach ($node['children'] as &$child) {
            $this->propagateTeam($child, $team);
        }
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
