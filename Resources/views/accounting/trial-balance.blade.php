@extends('nexcore_client_manager::layouts.accounting')

@section('title', 'Trial Balance - ' . $client->company_name)
@section('page_heading', 'TRIAL BALANCE')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(59,130,246,0.15), rgba(59,130,246,0.05)); border:1px solid rgba(59,130,246,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-balance-scale" style="color:var(--accent-blue); font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Trial Balance | {{ $client->company_name }}</h1>
                <span class="sl-page-subtitle">{{ \Carbon\Carbon::parse($fromDate)->format('j M Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('j M Y') }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; align-items:center; gap:12px;">
            @if($isBalanced)
                <span class="tb-badge-balanced"><i class="fas fa-check-circle"></i> Balanced</span>
            @else
                <span class="tb-badge-unbalanced"><i class="fas fa-exclamation-circle"></i> Out of Balance (R {{ number_format(abs($totalDebits - $totalCredits), 2, '.', ' ') }})</span>
            @endif
        </div>
    </div>
</div>

@include('nexcore_client_manager::accounting.partials.period-filter')

{{-- Summary Cards --}}
<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">Total Debits</div>
        <div class="sl-stat-value" style="color:var(--accent-blue); font-size:20px;">R {{ number_format($totalDebits, 2, '.', ' ') }}</div>
        <div class="sl-stat-meta">Closing debit balances</div>
    </div>
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Total Credits</div>
        <div class="sl-stat-value" style="color:var(--accent-green); font-size:20px;">R {{ number_format($totalCredits, 2, '.', ' ') }}</div>
        <div class="sl-stat-meta">Closing credit balances</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba({{ $isBalanced ? '16,185,129' : '239,68,68' }},0.4);">
        <div class="sl-stat-label">Difference</div>
        <div class="sl-stat-value" style="color:{{ $isBalanced ? 'var(--accent-green)' : 'var(--accent-red)' }}; font-size:20px;">R {{ number_format(abs($totalDebits - $totalCredits), 2, '.', ' ') }}</div>
        <div class="sl-stat-meta">{{ $isBalanced ? 'Books are balanced' : 'Requires investigation' }}</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(245,158,11,0.4);">
        <div class="sl-stat-label">Active Accounts</div>
        <div class="sl-stat-value" style="color:#f59e0b;">{{ $accountCount }}</div>
        <div class="sl-stat-meta">Accounts with activity</div>
    </div>
</div>

{{-- Trial Balance Table --}}
@if($accountCount > 0)
<div class="sl-card sl-animate d3">
    <div class="sl-card-header" style="display:flex; align-items:center; justify-content:space-between;">
        <div class="sl-card-title" style="color:#f59e0b;"><i class="fas fa-balance-scale"></i> Trial Balance Report</div>
        <span style="font-size:12px; color:var(--text-muted);">{{ now()->format('j M Y, H:i') }}</span>
    </div>

    {{-- Table Header --}}
    <div class="sl-table-wrap">
        <table class="sl-table tb-table">
            <thead>
                <tr>
                    <th style="width:140px;">Account Code</th>
                    <th>Account Name</th>
                    <th class="right" style="width:180px;">Debit (R)</th>
                    <th class="right" style="width:180px;">Credit (R)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sortedGroups as $type => $group)
                {{-- Group Header --}}
                <tr class="tb-group-header">
                    <td colspan="4">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div class="tb-group-icon" style="color:{{ $typeColors[$type] ?? '#f59e0b' }}; background:{{ $typeColors[$type] ?? '#f59e0b' }}15;">
                                <i class="fas {{ $typeIcons[$type] ?? 'fa-folder' }}"></i>
                            </div>
                            <span style="font-weight:700; font-size:16px; color:var(--text-primary);">{{ $typeLabels[$type] ?? ucfirst($type) }}</span>
                            <span style="font-size:11px; color:var(--text-muted); font-family:var(--font-mono);">({{ count($group['accounts']) }} accounts)</span>
                        </div>
                    </td>
                </tr>

                {{-- Account Rows --}}
                @foreach($group['accounts'] as $row)
                <tr class="tb-account-row">
                    <td style="font-family:var(--font-mono); font-size:15px; color:#f59e0b; font-weight:600; padding-left:42px;">{{ $row['account']->account_code }}</td>
                    <td style="color:var(--text-primary); font-size:15px; {{ $row['account']->account_level == 2 ? 'font-weight:600;' : 'padding-left:20px;' }}">{{ $row['account']->account_name }}</td>
                    <td class="right mono" style="font-size:15px; text-align:right; color:{{ $row['debit'] > 0 ? 'var(--accent-blue)' : 'var(--text-muted)' }};">
                        {{ $row['debit'] > 0 ? 'R ' . number_format($row['debit'], 2, '.', ' ') : '-' }}
                    </td>
                    <td class="right mono" style="font-size:15px; text-align:right; color:{{ $row['credit'] > 0 ? 'var(--accent-green)' : 'var(--text-muted)' }};">
                        {{ $row['credit'] > 0 ? 'R ' . number_format($row['credit'], 2, '.', ' ') : '-' }}
                    </td>
                </tr>
                @endforeach

                {{-- Group Subtotal --}}
                <tr class="tb-subtotal-row">
                    <td></td>
                    <td style="text-align:right; font-size:14px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.5px;">{{ $typeLabels[$type] ?? ucfirst($type) }} Total</td>
                    <td class="right mono" style="font-size:15px; font-weight:700; color:var(--accent-blue); text-align:right;">
                        {{ $group['subtotal_debit'] > 0 ? 'R ' . number_format($group['subtotal_debit'], 2, '.', ' ') : '-' }}
                    </td>
                    <td class="right mono" style="font-size:15px; font-weight:700; color:var(--accent-green); text-align:right;">
                        {{ $group['subtotal_credit'] > 0 ? 'R ' . number_format($group['subtotal_credit'], 2, '.', ' ') : '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="tb-grand-total">
                    <td></td>
                    <td class="right" style="font-size:15px; font-weight:800; color:var(--text-primary); text-transform:uppercase; letter-spacing:1px;">Grand Total</td>
                    <td class="right mono" style="font-size:15px; font-weight:800; color:var(--accent-blue); text-align:right; white-space:nowrap;">R {{ number_format($totalDebits, 2, '.', ' ') }}</td>
                    <td class="right mono" style="font-size:15px; font-weight:800; color:var(--accent-green); text-align:right; white-space:nowrap;">R {{ number_format($totalCredits, 2, '.', ' ') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@else
<div class="sl-card sl-animate d3">
    <div style="text-align:center; padding:80px 40px; color:var(--text-muted);">
        <i class="fas fa-balance-scale" style="font-size:48px; opacity:0.15; margin-bottom:20px; display:block;"></i>
        <div style="font-size:18px; font-weight:700; margin-bottom:8px; color:var(--text-secondary);">No Posted Journals</div>
        <div style="font-size:14px; max-width:400px; margin:0 auto; line-height:1.6;">
            The trial balance will populate automatically once journal entries are posted. Create and post journal entries to see account balances here.
        </div>
        <a href="{{ route('nexcore.clients.show.accounting.journals.create', $client->id) }}" class="neon-btn neon-btn-amber" style="margin-top:24px; display:inline-flex;"><i class="fas fa-plus"></i> Create Journal Entry</a>
    </div>
</div>
@endif
@endsection

@push('scripts')
<style>
    .tb-badge-balanced { display:flex; align-items:center; gap:6px; font-size:13px; font-weight:700; color:var(--accent-green); background:rgba(16,185,129,0.08); padding:8px 16px; border-radius:8px; border:1px solid rgba(16,185,129,0.25); }
    .tb-badge-unbalanced { display:flex; align-items:center; gap:6px; font-size:13px; font-weight:700; color:var(--accent-red); background:rgba(239,68,68,0.08); padding:8px 16px; border-radius:8px; border:1px solid rgba(239,68,68,0.25); }
    .tb-table { border-collapse:separate; border-spacing:0; }
    .tb-group-header td { padding:16px 16px 8px !important; border-bottom:none !important; }
    .tb-group-icon { width:28px; height:28px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:12px; }
    .tb-account-row td { padding:8px 16px !important; border-bottom:1px solid rgba(255,255,255,0.03) !important; }
    .tb-account-row:hover td { background:rgba(245,158,11,0.03); }
    .tb-subtotal-row td { padding:10px 16px !important; border-bottom:2px solid var(--border-subtle) !important; }
    .tb-grand-total td { padding:18px 16px !important; border-top:3px double rgba(245,158,11,0.4) !important; background:rgba(245,158,11,0.03); }
</style>
@endpush
