<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $tree = $this->buildTree($user);
        $this->tagTeams($tree);

        $allRows = $this->flattenTree($tree);
        usort($allRows, fn ($a, $b) => $a->depth <=> $b->depth ?: $a->created_at <=> $b->created_at);

        $teamSize = count($allRows);
        $totalTeamBusiness = array_sum(array_map(fn ($r) => (float) $r->invested, $allRows));

        $search = trim((string) $request->query('q', ''));
        $filtered = new Collection($allRows);

        if ($search !== '') {
            $filtered = $filtered->filter(function ($row) use ($search) {
                $status = $row->is_dummy ? 'Dummy' : ((float) $row->invested > 0 ? 'Active' : 'Inactive');
                $haystacks = [
                    $row->name,
                    'Level ' . $row->depth,
                    $status,
                    $row->rank_name ?? 'Unranked',
                    number_format((float) $row->invested, 2),
                    $row->created_at->format('d M Y'),
                ];

                foreach ($haystacks as $haystack) {
                    if (stripos((string) $haystack, $search) !== false) {
                        return true;
                    }
                }

                return false;
            })->values();
        }

        $perPage = 20;
        $currentPage = (int) $request->query('page', 1);

        $rows = new LengthAwarePaginator(
            $filtered->forPage($currentPage, $perPage),
            $filtered->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('dashboard.team', compact('rows', 'teamSize', 'totalTeamBusiness', 'tree', 'search'));
    }

    public function print(Request $request)
    {
        $user = $request->user();

        $tree = $this->buildTree($user);
        $this->tagTeams($tree);

        $rows = $this->flattenTree($tree);
        $teamSize = count($rows);
        $totalTeamBusiness = array_sum(array_map(fn ($r) => (float) $r->invested, $rows));

        return view('dashboard.team-print', compact('user', 'tree', 'teamSize', 'totalTeamBusiness'));
    }

    /**
     * Builds the tree via plain Eloquent recursion (portable to any
     * MySQL/MariaDB version — no recursive CTE support required).
     */
    private function buildTree(User $user, int $depth = 0): array
    {
        $children = $user->directReferrals()->with('currentRank')->orderBy('created_at')->get();

        $childNodes = $children->map(fn (User $child) => $this->buildTree($child, $depth + 1))->all();

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
        $activeCount = $investment > 0 ? 1 : 0;
        $inactiveCount = $investment > 0 ? 0 : 1;

        foreach ($node['children'] as $child) {
            $childStats = $this->nodeStats($child);
            $count += $childStats['count'];
            $investment += $childStats['investment'];
            $activeCount += $childStats['active_count'];
            $inactiveCount += $childStats['inactive_count'];
        }

        return [
            'count' => $count,
            'investment' => $investment,
            'active_count' => $activeCount,
            'inactive_count' => $inactiveCount,
        ];
    }

    private function legStats(array $childNodes): array
    {
        $legs = collect($childNodes)
            ->map(fn ($child) => $this->nodeStats($child))
            ->sortByDesc('investment')
            ->values();

        $empty = ['count' => 0, 'investment' => 0.0, 'active_count' => 0, 'inactive_count' => 0];

        return [
            'power' => $legs->get(0, $empty),
            'second' => $legs->get(1, $empty),
            'rest' => [
                'count' => $legs->slice(2)->sum('count'),
                'investment' => $legs->slice(2)->sum('investment'),
                'active_count' => $legs->slice(2)->sum('active_count'),
                'inactive_count' => $legs->slice(2)->sum('inactive_count'),
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
