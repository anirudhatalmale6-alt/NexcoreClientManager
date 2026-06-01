@extends('nexcore_client_manager::layouts.accounting')

@section('title', 'Chart of Accounts - ' . $client->company_name)
@section('page_heading', 'CHART OF ACCOUNTS')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(245,158,11,0.15), rgba(245,158,11,0.05)); border:1px solid rgba(245,158,11,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-sitemap" style="color:#f59e0b; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Chart of Accounts</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; gap:8px;">
            <a href="{{ route('nexcore.clients.show.accounting.accounts.create', $client->id) }}" class="neon-btn neon-btn-amber neon-pulse"><i class="fas fa-plus"></i> New Account</a>
        </div>
    </div>
</div>

@php
    $totalCount = $accounts->count();
    $subCount = $accounts->where('account_level', 2)->count();
    $detailCount = $accounts->where('account_level', 3)->count();
    $assetCount = $accounts->where('account_type', 'asset')->count();
    $liabilityCount = $accounts->where('account_type', 'liability')->count();
    $equityCount = $accounts->where('account_type', 'equity')->count();
    $revenueCount = $accounts->where('account_type', 'revenue')->count();
    $cosCount = $accounts->where('account_type', 'cost_of_sales')->count();
    $expenseCount = $accounts->where('account_type', 'expense')->count();
    $otherCount = $accounts->where('account_type', 'other')->count();
@endphp

<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card" style="border-color:rgba(245,158,11,0.4);">
        <div class="sl-stat-label">Total Accounts</div>
        <div class="sl-stat-value" style="color:#f59e0b;">{{ $totalCount }}</div>
        <div class="sl-stat-meta">Sub: {{ $subCount }} | Detail: {{ $detailCount }}</div>
    </div>
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">Assets</div>
        <div class="sl-stat-value" style="color:var(--accent-blue);">{{ $assetCount }}</div>
    </div>
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Revenue</div>
        <div class="sl-stat-value" style="color:var(--accent-green);">{{ $revenueCount }}</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(239,68,68,0.4);">
        <div class="sl-stat-label">Expenses</div>
        <div class="sl-stat-value" style="color:var(--accent-red);">{{ $expenseCount + $cosCount }}</div>
    </div>
</div>

{{-- Tabs --}}
<div class="sl-card sl-animate d3" style="margin-bottom:0; border-bottom:none; border-radius:var(--radius-md) var(--radius-md) 0 0;">
    <div style="display:flex; gap:0; border-bottom:2px solid var(--border-subtle); padding:0 4px;">
        <button class="acc-tab active" data-filter="all" onclick="filterAccounts('all', this)">
            <i class="fas fa-layer-group"></i> All <span class="acc-tab-count">{{ $totalCount }}</span>
        </button>
        <button class="acc-tab" data-filter="revenue" onclick="filterAccounts('revenue', this)">
            <i class="fas fa-arrow-trend-up"></i> Revenue <span class="acc-tab-count">{{ $revenueCount }}</span>
        </button>
        <button class="acc-tab" data-filter="cost_of_sales" onclick="filterAccounts('cost_of_sales', this)">
            <i class="fas fa-receipt"></i> Cost of Sales <span class="acc-tab-count">{{ $cosCount }}</span>
        </button>
        <button class="acc-tab" data-filter="expense" onclick="filterAccounts('expense', this)">
            <i class="fas fa-arrow-down"></i> Expenses <span class="acc-tab-count">{{ $expenseCount }}</span>
        </button>
        <button class="acc-tab" data-filter="asset" onclick="filterAccounts('asset', this)">
            <i class="fas fa-coins"></i> Assets <span class="acc-tab-count">{{ $assetCount }}</span>
        </button>
        <button class="acc-tab" data-filter="liability" onclick="filterAccounts('liability', this)">
            <i class="fas fa-hand-holding-usd"></i> Liabilities <span class="acc-tab-count">{{ $liabilityCount }}</span>
        </button>
        <button class="acc-tab" data-filter="equity" onclick="filterAccounts('equity', this)">
            <i class="fas fa-balance-scale-right"></i> Equity <span class="acc-tab-count">{{ $equityCount }}</span>
        </button>
        <button class="acc-tab" data-filter="other" onclick="filterAccounts('other', this)">
            <i class="fas fa-ellipsis-h"></i> Other <span class="acc-tab-count">{{ $otherCount }}</span>
        </button>
    </div>
</div>

{{-- Table --}}
<div class="sl-card" style="border-radius:0 0 var(--radius-md) var(--radius-md); margin-top:0;">
    <div class="sl-table-wrap">
        <table class="sl-table" id="accountsTable" style="table-layout:fixed; width:100%;">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="width:120px;">Code</th>
                    <th style="width:280px;">Account Name</th>
                    <th style="width:100px;">Type</th>
                    <th style="width:60px;">Level</th>
                    <th style="width:80px;">Normal Bal.</th>
                    <th style="width:60px;">VAT</th>
                    <th style="width:70px;" class="center">Status</th>
                    <th style="width:70px;" class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $idx => $account)
                <tr class="acc-row" data-account-type="{{ $account->account_type }}">
                    <td style="color:var(--text-muted);" class="acc-row-num">{{ $idx + 1 }}</td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:#f59e0b; font-weight:600;">{{ $account->account_code }}</td>
                    <td style="max-width:280px; overflow:hidden;">
                        <div style="font-weight:600; color:var(--text-primary); white-space:normal; word-wrap:break-word; overflow-wrap:break-word; {{ $account->account_level == 2 ? 'padding-left:16px;' : ($account->account_level == 3 ? 'padding-left:32px; font-weight:400;' : 'font-size:15px;') }}">
                            {{ $account->account_name }}
                        </div>
                        @if($account->description)
                            <div style="font-size:12px; color:var(--text-muted); margin-top:2px; {{ $account->account_level >= 2 ? 'padding-left:32px;' : '' }}">{{ \Illuminate\Support\Str::limit($account->description, 50) }}</div>
                        @endif
                    </td>
                    <td>
                        @switch($account->account_type)
                            @case('asset') <span class="sl-tag sl-tag-blue">Asset</span> @break
                            @case('liability') <span class="sl-tag" style="background:rgba(168,85,247,0.1); color:#a855f7; border:1px solid rgba(168,85,247,0.3);">Liability</span> @break
                            @case('equity') <span class="sl-tag sl-tag-cyan">Equity</span> @break
                            @case('revenue') <span class="sl-tag sl-tag-green">Revenue</span> @break
                            @case('cost_of_sales') <span class="sl-tag sl-tag-amber">Cost of Sales</span> @break
                            @case('expense') <span class="sl-tag sl-tag-red">Expense</span> @break
                            @case('other') <span class="sl-tag" style="background:rgba(148,163,184,0.1); color:#94a3b8; border:1px solid rgba(148,163,184,0.3);">Other</span> @break
                        @endswitch
                    </td>
                    <td>
                        @if($account->account_level == 1)
                            <span style="font-size:11px; font-weight:700; color:#f59e0b; text-transform:uppercase;">Main</span>
                        @elseif($account->account_level == 2)
                            <span style="font-size:11px; font-weight:600; color:var(--text-secondary);">Sub</span>
                        @else
                            <span style="font-size:11px; color:var(--text-muted);">Detail</span>
                        @endif
                    </td>
                    <td style="font-family:var(--font-mono); font-size:12px; color:var(--text-secondary);">
                        {{ ucfirst($account->normal_balance ?? '-') }}
                    </td>
                    <td style="font-size:12px; color:var(--text-muted);">{{ ucfirst($account->vat_type ?? 'None') }}</td>
                    <td class="center">
                        @if($account->is_active)
                            <span class="sl-tag sl-tag-green">Active</span>
                        @else
                            <span class="sl-tag sl-tag-red">Inactive</span>
                        @endif
                    </td>
                    <td class="center">
                        <div style="display:flex; gap:6px; justify-content:center;">
                            <a href="{{ route('nexcore.clients.show.accounting.accounts.edit', [$client->id, $account->id]) }}" style="color:var(--accent-blue); font-size:15px;" title="Edit"><i class="fas fa-pen"></i></a>
                            @if(!$account->is_system)
                            <form method="POST" action="{{ route('nexcore.clients.show.accounting.accounts.destroy', [$client->id, $account->id]) }}" style="display:inline;" onsubmit="return confirm('Delete this account? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:none; border:none; color:var(--accent-red); cursor:pointer; font-size:15px;" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="acc-empty-row">
                    <td colspan="9" style="text-align:center; padding:60px; color:var(--text-muted);">
                        <i class="fas fa-sitemap" style="font-size:40px; opacity:0.2; margin-bottom:16px; display:block;"></i>
                        <div style="font-size:16px; font-weight:600; margin-bottom:6px;">No accounts yet</div>
                        <div style="font-size:13px;">Click "New Account" to set up your chart of accounts.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .acc-tab { background:none; border:none; color:var(--text-muted); font-size:13px; font-weight:600; padding:12px 13px; cursor:pointer; display:flex; align-items:center; gap:6px; border-bottom:2px solid transparent; margin-bottom:-2px; transition:all 0.2s ease; font-family:var(--font-body); white-space:nowrap; }
    .acc-tab:hover { color:var(--text-secondary); }
    .acc-tab.active { color:#f59e0b; border-bottom-color:#f59e0b; }
    .acc-tab-count { font-family:var(--font-mono); font-size:11px; background:rgba(245,158,11,0.1); color:#f59e0b; padding:2px 6px; border-radius:10px; }
    .acc-tab.active .acc-tab-count { background:rgba(245,158,11,0.2); }
</style>
<script>
function filterAccounts(type, btn) {
    document.querySelectorAll('.acc-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    let rows = document.querySelectorAll('.acc-row');
    let num = 0;
    rows.forEach(r => {
        if (type === 'all' || r.dataset.accountType === type) {
            r.style.display = '';
            num++;
            r.querySelector('.acc-row-num').textContent = num;
        } else {
            r.style.display = 'none';
        }
    });
    let empty = document.querySelector('.acc-empty-row');
    if (empty) empty.style.display = num === 0 ? '' : 'none';
}
</script>
@endpush
