@php $__status = $user->investorStatus(); @endphp
<span class="d-inline-flex align-items-center gap-1" title="{{ $user->investorStatusLabel() }}">
    <span class="status-dot {{ $__status }}"></span>
    <span class="small">{{ $user->investorStatusLabel() }}</span>
</span>
