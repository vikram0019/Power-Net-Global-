@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'Users')

@section('content')
    <div class="card-png p-4 mb-4 d-flex flex-wrap flex-row justify-content-between align-items-center gap-3">
        <form method="GET" class="d-flex gap-2 flex-fill" style="max-width: 480px;">
            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search by name, email, mobile, or referral code">
            <button type="submit" class="btn btn-navy">Search</button>
        </form>
        <a href="{{ route('admin.users.create-dummy') }}" class="btn btn-gold fw-bold"><i class="bi bi-person-plus me-1"></i>Create Dummy User</a>
    </div>

    <div class="card-png p-4">
        <div class="table-responsive">
            <table class="table table-png align-middle">
                <thead>
                    <tr><th>Name</th><th>Contact</th><th>Referral Code</th><th>Sponsor</th><th>Rank</th><th>Investor Status</th><th>MPG</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                        <tr>
                            <td class="fw-semibold">{{ $u->name }}</td>
                            <td class="small">{{ $u->email }}<br><span class="text-muted">{{ $u->mobile }}</span></td>
                            <td><code>{{ $u->referral_code }}</code></td>
                            <td>{{ $u->sponsor->name ?? '—' }}</td>
                            <td>{{ $u->currentRank->name ?? 'Unranked' }}</td>
                            <td>@include('partials.investor-status', ['user' => $u])</td>
                            <td>
                                <form method="POST" action="{{ route('admin.users.toggle-roi', $u) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $u->roi_enabled ? 'btn-gold' : 'btn-outline-secondary' }}">
                                        {{ $u->roi_enabled ? 'On' : 'Off' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-navy dropdown-toggle" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                        Action
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('admin.users.show', $u) }}">View</a></li>
                                        <li>
                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#addFundModal" data-user-id="{{ $u->id }}" data-user-name="{{ $u->name }}">Add Fund</button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#withdrawFundModal"
                                                data-user-id="{{ $u->id }}"
                                                data-user-name="{{ $u->name }}"
                                                data-balance-roi="{{ number_format($u->wallet->roi_balance ?? 0, 2, '.', '') }}"
                                                data-balance-working="{{ number_format($u->wallet->working_balance ?? 0, 2, '.', '') }}"
                                                data-balance-rank_reward="{{ number_format($u->wallet->rank_reward_balance ?? 0, 2, '.', '') }}"
                                                data-balance-deposit="{{ number_format($u->investments()->where('status', 'active')->sum('amount'), 2, '.', '') }}">Withdrawal</button>
                                        </li>
                                        <li><a class="dropdown-item" href="{{ route('admin.users.edit', $u) }}">Edit</a></li>
                                        @if ($u->status === 'approval_pending')
                                            <li>
                                                <form method="POST" action="{{ route('admin.users.approve', $u) }}">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">Approve</button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $users->links() }}
        </div>
    </div>

    <div class="modal fade" id="addFundModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" id="addFundForm" data-confirm-title="Confirm Add Fund" data-confirm="Add fund to this member's wallet? This cannot be undone.">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add Fund — <span id="addFundUserName"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label small fw-semibold">Amount ($)</label>
                        <input type="number" name="amount" id="addFundAmount" step="0.01" min="{{ config('mlm.minimum_investment') }}" class="form-control" required>
                        <p class="text-muted small mt-2 mb-0">This credits the amount and creates an investment for the member immediately — no approval step. Triggers direct reward, level income, and rank checks just like a real investment. Minimum ${{ number_format(config('mlm.minimum_investment'), 0) }}.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gold fw-bold">Add Fund</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="withdrawFundModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" id="withdrawFundForm" data-confirm-title="Confirm Withdrawal" data-confirm="Withdraw funds from this member's wallet? This cannot be undone.">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Withdrawal — <span id="withdrawFundUserName"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label small fw-semibold">Wallet Type</label>
                        <select name="wallet_type" id="withdrawFundWalletType" class="form-select mb-3" required>
                            <option value="roi" data-label="MPG Income">MPG Income</option>
                            <option value="working" data-label="Working Income">Working Income</option>
                            <option value="rank_reward" data-label="Rank &amp; Rewards">Rank &amp; Rewards</option>
                            <option value="deposit" data-label="Investment">Investment</option>
                        </select>
                        <label class="form-label small fw-semibold">Amount ($)</label>
                        <input type="number" name="amount" id="withdrawFundAmount" step="0.01" min="0.01" class="form-control" required>
                        <p class="text-muted small mt-2 mb-0">This debits the amount directly from the selected wallet — no OTP or approval step, takes effect immediately. "Investment" draws down the member's active invested principal directly (oldest investment first), not a wallet balance.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger fw-bold">Withdraw</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var addFundModal = document.getElementById('addFundModal');
    if (!addFundModal) {
        return;
    }

    // Action dropdowns live inside .table-responsive, which clips overflow
    // on both axes (a browser quirk: overflow-x:auto forces the other axis
    // to auto too, so it can't be reopened with CSS alone). Moving the menu
    // to <body> with fixed positioning while open sidesteps the clipping
    // entirely, then it's moved back on close so each row's own button
    // still owns its own menu element.
    document.querySelectorAll('.dropdown').forEach(function (dropdown) {
        var toggle = dropdown.querySelector('.dropdown-toggle');
        var menu = dropdown.querySelector('.dropdown-menu');
        if (!toggle || !menu) {
            return;
        }

        toggle.addEventListener('show.bs.dropdown', function () {
            var rect = toggle.getBoundingClientRect();
            document.body.appendChild(menu);
            menu.style.position = 'fixed';
            menu.style.top = rect.bottom + 'px';
            menu.style.right = (window.innerWidth - rect.right) + 'px';
            menu.style.left = 'auto';
            menu.style.zIndex = 3000;
        });

        toggle.addEventListener('hidden.bs.dropdown', function () {
            dropdown.appendChild(menu);
            menu.style.position = '';
            menu.style.top = '';
            menu.style.right = '';
            menu.style.left = '';
            menu.style.zIndex = '';
        });
    });

    var addFundForm = document.getElementById('addFundForm');
    var addFundAmount = document.getElementById('addFundAmount');
    var currentUserName = '';

    function updateAddFundConfirmText() {
        var amount = parseFloat(addFundAmount.value);
        var amountText = isNaN(amount) ? 'this amount' : ('$' + amount.toFixed(2));
        addFundForm.dataset.confirm = 'Add ' + amountText + ' as an investment for ' + currentUserName + "? This triggers direct reward, level income, and rank checks. This cannot be undone.";
    }

    addFundModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var userId = button.getAttribute('data-user-id');
        currentUserName = button.getAttribute('data-user-name');

        document.getElementById('addFundUserName').textContent = currentUserName;
        addFundForm.action = '/admin/users/' + userId + '/add-fund';
        addFundAmount.value = '';
        updateAddFundConfirmText();
    });

    addFundAmount.addEventListener('input', updateAddFundConfirmText);

    addFundForm.addEventListener('submit', function () {
        var modalInstance = bootstrap.Modal.getInstance(addFundModal);
        if (modalInstance) {
            modalInstance.hide();
        }
    });

    var withdrawFundModal = document.getElementById('withdrawFundModal');
    var withdrawFundForm = document.getElementById('withdrawFundForm');
    var withdrawFundAmount = document.getElementById('withdrawFundAmount');
    var withdrawFundWalletType = document.getElementById('withdrawFundWalletType');
    var withdrawCurrentUserName = '';

    function updateWithdrawFundConfirmText() {
        var amount = parseFloat(withdrawFundAmount.value);
        var amountText = isNaN(amount) ? 'this amount' : ('$' + amount.toFixed(2));
        var selectedOption = withdrawFundWalletType.options[withdrawFundWalletType.selectedIndex];
        var walletLabel = selectedOption.getAttribute('data-label');
        withdrawFundForm.dataset.confirm = 'Withdraw ' + amountText + ' from ' + withdrawCurrentUserName + "'s " + walletLabel + ' wallet? This cannot be undone.';
    }

    function updateWithdrawFundBalances(button) {
        var balances = {
            roi: button.getAttribute('data-balance-roi'),
            working: button.getAttribute('data-balance-working'),
            rank_reward: button.getAttribute('data-balance-rank_reward'),
            deposit: button.getAttribute('data-balance-deposit'),
        };

        Array.from(withdrawFundWalletType.options).forEach(function (option) {
            var label = option.getAttribute('data-label');
            var balance = balances[option.value];
            option.text = balance !== null ? label + ' ($' + parseFloat(balance).toFixed(2) + ')' : label;
        });
    }

    withdrawFundModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var userId = button.getAttribute('data-user-id');
        withdrawCurrentUserName = button.getAttribute('data-user-name');

        document.getElementById('withdrawFundUserName').textContent = withdrawCurrentUserName;
        withdrawFundForm.action = '/admin/users/' + userId + '/withdraw-fund';
        withdrawFundAmount.value = '';
        withdrawFundWalletType.selectedIndex = 0;
        updateWithdrawFundBalances(button);
        updateWithdrawFundConfirmText();
    });

    withdrawFundAmount.addEventListener('input', updateWithdrawFundConfirmText);
    withdrawFundWalletType.addEventListener('change', updateWithdrawFundConfirmText);

    withdrawFundForm.addEventListener('submit', function () {
        var modalInstance = bootstrap.Modal.getInstance(withdrawFundModal);
        if (modalInstance) {
            modalInstance.hide();
        }
    });
});
</script>
@endpush
