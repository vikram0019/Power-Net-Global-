<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Team Network — {{ $user->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css') }}?v={{ filemtime(public_path('assets/css/app.css')) }}" rel="stylesheet">
    <style>
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        body { font-family: Arial, Helvetica, sans-serif; color: #1a1a1a; margin: 0; padding: 24px; background: #fff; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #0a1428; padding-bottom: 16px; margin-bottom: 20px; }
        .header h1 { font-size: 20px; margin: 0 0 4px; color: #0a1428; }
        .header .meta { font-size: 12px; color: #666; }
        .summary { display: flex; gap: 24px; margin-bottom: 24px; font-size: 13px; }
        .summary div { border: 1px solid #ddd; border-radius: 6px; padding: 8px 14px; }
        .summary strong { display: block; font-size: 16px; color: #0a1428; }
        .legend { display: flex; gap: 16px; margin-bottom: 20px; font-size: 12px; color: #555; }
        .print-bar { margin-bottom: 20px; }
        .print-bar button { background: #e6c26b; border: none; padding: 8px 18px; border-radius: 6px; font-weight: bold; cursor: pointer; }
        .org-node-avatar { font-size: 1.1rem; }
        @media print {
            .print-bar { display: none; }
            body { padding: 0; }
            @page { size: landscape; margin: 12mm; }
        }
    </style>
</head>
<body>
    <div class="print-bar">
        <button onclick="window.print()">Print</button>
    </div>

    <div class="header">
        <div>
            <h1>Team Network — {{ $user->name }}</h1>
            <div class="meta">Referral Code: {{ $user->referral_code }}</div>
        </div>
        <div class="meta">Generated {{ now()->format('d M Y, H:i') }}</div>
    </div>

    <div class="summary">
        <div>Total Team Members<strong>{{ $teamSize }}</strong></div>
        <div>Total Team Business<strong>${{ number_format($totalTeamBusiness, 2) }}</strong></div>
    </div>

    <div class="legend">
        <span><span class="tree-legend-swatch team-a"></span> Team A (Power leg)</span>
        <span><span class="tree-legend-swatch team-b"></span> Team B (2nd leg)</span>
        <span><span class="tree-legend-swatch team-c"></span> Team C (Rest legs)</span>
        <span><span class="status-dot green" style="display:inline-block;"></span> Active</span>
        <span><span class="status-dot red" style="display:inline-block;"></span> Inactive</span>
    </div>

    <div class="org-tree-wrap">
        <ul class="org-tree">
            <li>
                <div class="org-node org-node-root org-node-compact">
                    <div class="org-node-avatar">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <div class="org-node-name">{{ $user->name }} (You)</div>
                    <div class="org-node-invested">${{ number_format($tree['invested'], 2) }}</div>
                    <div class="org-node-referral">
                        <span class="org-node-referral-chip"><i class="bi bi-link-45deg"></i>{{ $user->referral_code }}</span>
                    </div>
                </div>
                @if (count($tree['children']))
                    <ul>
                        @foreach ($tree['children'] as $child)
                            @include('partials.team-tree-node-print', ['node' => $child])
                        @endforeach
                    </ul>
                @endif
            </li>
        </ul>
    </div>
    @unless (count($tree['children']))
        <p style="color:#888; margin-top: 16px;">You haven't referred anyone yet.</p>
    @endunless
</body>
</html>
