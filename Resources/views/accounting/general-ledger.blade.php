@extends('nexcore_client_manager::layouts.accounting')

@section('title', 'General Ledger - ' . $client->company_name)
@section('page_heading', 'GENERAL LEDGER')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(245,158,11,0.15), rgba(245,158,11,0.05)); border:1px solid rgba(245,158,11,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-book-open" style="color:#f59e0b; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">General Ledger | {{ $client->company_name }}</h1>
                <span class="sl-page-subtitle">{{ \Carbon\Carbon::parse($fromDate)->format('j M Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('j M Y') }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; align-items:center; gap:12px;">
            <span style="font-size:12px; color:var(--text-muted); font-family:var(--font-mono);">{{ now()->format('j M Y, H:i') }}</span>
        </div>
    </div>
</div>

@include('nexcore_client_manager::accounting.partials.period-filter')

{{-- Account Range Filter --}}
<div class="sl-card sl-animate d2" style="padding:16px 20px; margin-bottom:20px;">
    <form method="GET" action="{{ route('nexcore.clients.show.accounting.ledger', $client->id) }}" id="glFilterForm">
        <input type="hidden" name="from_date" value="{{ $fromDate }}">
        <input type="hidden" name="to_date" value="{{ $toDate }}">
        <input type="hidden" name="preset" value="{{ $preset }}">
        <div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
            <div style="display:flex; align-items:center; gap:8px;">
                <i class="fas fa-filter" style="color:#f59e0b; font-size:13px;"></i>
                <span style="font-size:13px; font-weight:600; color:var(--text-secondary);">Account Range:</span>
            </div>
            <div style="display:flex; align-items:center; gap:10px; flex:1; flex-wrap:wrap;">
                <select name="from_account" class="gl-select">
                    <option value="">First Account</option>
                    @foreach($allAccounts as $fa)
                        <option value="{{ $fa->id }}" {{ $fromAccountId == $fa->id ? 'selected' : '' }}>{{ rtrim($fa->account_code, '/') }} - {{ $fa->account_name }}</option>
                    @endforeach
                </select>
                <span style="font-size:14px; font-weight:700; color:var(--text-muted);">TO</span>
                <select name="to_account" class="gl-select">
                    <option value="">Last Account</option>
                    @foreach($allAccounts as $fa)
                        <option value="{{ $fa->id }}" {{ $toAccountId == $fa->id ? 'selected' : '' }}>{{ rtrim($fa->account_code, '/') }} - {{ $fa->account_name }}</option>
                    @endforeach
                </select>
                <button type="submit" style="padding:8px 20px; background:linear-gradient(135deg, #f59e0b, #d97706); border:none; border-radius:8px; color:#fff; font-size:13px; font-weight:700; cursor:pointer; white-space:nowrap;">
                    <i class="fas fa-search"></i> Run Report
                </button>
                @if($fromAccountId || $toAccountId)
                <a href="{{ route('nexcore.clients.show.accounting.ledger', [$client->id, 'from_date' => $fromDate, 'to_date' => $toDate, 'preset' => $preset]) }}" style="font-size:13px; color:var(--accent-red); text-decoration:none; font-weight:600; white-space:nowrap;">
                    <i class="fas fa-times"></i> Clear Range
                </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- Summary Cards --}}
<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">Total Debits</div>
        <div class="sl-stat-value" style="color:var(--accent-blue); font-size:20px;">R {{ number_format($totalDebits, 2, '.', ' ') }}</div>
        <div class="sl-stat-meta">Posted debit transactions</div>
    </div>
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Total Credits</div>
        <div class="sl-stat-value" style="color:var(--accent-green); font-size:20px;">R {{ number_format($totalCredits, 2, '.', ' ') }}</div>
        <div class="sl-stat-meta">Posted credit transactions</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(245,158,11,0.4);">
        <div class="sl-stat-label">Accounts</div>
        <div class="sl-stat-value" style="color:#f59e0b;">{{ $accountCount }}</div>
        <div class="sl-stat-meta">With transactions in period</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(167,139,250,0.4);">
        <div class="sl-stat-label">Transactions</div>
        <div class="sl-stat-value" style="color:#a78bfa;">{{ $transactionCount }}</div>
        <div class="sl-stat-meta">Posted journal lines</div>
    </div>
</div>

{{-- General Ledger Transaction Report --}}
@if($accountCount > 0)
<div class="sl-card sl-animate d3">
    <div class="sl-card-header" style="display:flex; align-items:center; justify-content:space-between;">
        <div class="sl-card-title" style="color:#f59e0b;"><i class="fas fa-book-open"></i> General Ledger Transaction Report</div>
        <span style="font-size:12px; color:var(--text-muted);">{{ now()->format('j M Y, H:i') }}</span>
    </div>

    @foreach($ledgerAccounts as $accData)
    @php $acc = $accData['account']; @endphp

    {{-- Account Header --}}
    <div class="gl-account-header gl-collapsible" data-target="gl-acc-{{ $acc->id }}" onclick="toggleGlAccount(this)">
        <div style="display:flex; align-items:center; gap:12px; flex:1;">
            <i class="fas fa-chevron-right gl-chevron"></i>
            <span class="mono" style="color:#f59e0b; font-weight:700; font-size:16px;">{{ rtrim($acc->account_code, '/') }}</span>
            <span style="color:var(--accent-blue); font-weight:600; font-size:16px; margin:0 4px;">|</span>
            <span style="font-weight:600; color:var(--accent-blue); font-size:16px; text-transform:uppercase;">{{ $acc->account_name }}</span>
            <span style="font-size:11px; color:var(--text-muted); font-family:var(--font-mono); margin-left:8px;">({{ count($accData['transactions']) }} txn{{ count($accData['transactions']) != 1 ? 's' : '' }})</span>
        </div>
        <div style="display:flex; align-items:center; gap:24px;">
            <span class="mono" style="font-size:14px; font-weight:600; color:var(--text-muted);">
                Dr: R {{ number_format($accData['total_debit'], 2, '.', ' ') }}
            </span>
            <span class="mono" style="font-size:14px; font-weight:600; color:var(--text-muted);">
                Cr: R {{ number_format($accData['total_credit'], 2, '.', ' ') }}
            </span>
            <span class="mono" style="font-size:15px; font-weight:700; color:#a78bfa;">
                Bal: R {{ number_format(abs($accData['closing_balance']), 2, '.', ' ') }}
                {{ $accData['closing_balance'] >= 0 ? ($acc->normal_balance === 'debit' ? 'Dr' : 'Cr') : ($acc->normal_balance === 'debit' ? 'Cr' : 'Dr') }}
            </span>
        </div>
    </div>

    {{-- Transaction Table (hidden by default) --}}
    <div class="gl-transactions gl-acc-{{ $acc->id }}" style="display:none;">
        <div class="sl-table-wrap">
            <table class="sl-table gl-table">
                <thead>
                    <tr>
                        <th style="width:100px;">Date</th>
                        <th style="width:100px;">Journal</th>
                        <th style="width:120px;">Reference</th>
                        <th>Description</th>
                        <th class="right" style="width:150px;">Debit (R)</th>
                        <th class="right" style="width:150px;">Credit (R)</th>
                        <th class="right" style="width:160px;">Balance (R)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accData['transactions'] as $txn)
                    <tr class="gl-txn-row{{ $loop->last ? ' gl-txn-last' : '' }}">
                        <td style="font-family:var(--font-mono); font-size:14px; color:var(--text-secondary);">{{ \Carbon\Carbon::parse($txn['date'])->format('j M Y') }}</td>
                        <td>
                            <a href="{{ route('nexcore.clients.show.accounting.journals.edit', [$client->id, $txn['journal_id']]) }}" style="font-family:var(--font-mono); font-size:14px; color:#f59e0b; font-weight:600; text-decoration:none;">{{ $txn['journal_number'] }}</a>
                        </td>
                        <td style="font-size:14px; color:var(--text-secondary);">{{ $txn['reference'] ?: '-' }}</td>
                        <td style="font-size:14px; color:var(--text-primary);">{{ $txn['description'] ?: '-' }}</td>
                        <td class="right mono" style="font-size:15px; color:{{ $txn['debit'] > 0 ? 'var(--accent-blue)' : 'var(--text-muted)' }};">
                            {{ $txn['debit'] > 0 ? 'R ' . number_format($txn['debit'], 2, '.', ' ') : '-' }}
                        </td>
                        <td class="right mono" style="font-size:15px; color:{{ $txn['credit'] > 0 ? 'var(--accent-green)' : 'var(--text-muted)' }};">
                            {{ $txn['credit'] > 0 ? 'R ' . number_format($txn['credit'], 2, '.', ' ') : '-' }}
                        </td>
                        <td class="right mono" style="font-size:15px; font-weight:600; color:#a78bfa;">
                            R {{ number_format(abs($txn['balance']), 2, '.', ' ') }}
                            {{ $txn['balance'] >= 0 ? ($acc->normal_balance === 'debit' ? 'Dr' : 'Cr') : ($acc->normal_balance === 'debit' ? 'Cr' : 'Dr') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="gl-account-total">
                        <td colspan="4" style="text-align:right; font-size:14px; font-weight:700; color:#a78bfa; text-transform:uppercase; letter-spacing:0.5px;">{{ $acc->account_name }} Total</td>
                        <td class="right mono" style="font-size:15px; font-weight:700; color:var(--accent-blue);">
                            {{ $accData['total_debit'] > 0 ? 'R ' . number_format($accData['total_debit'], 2, '.', ' ') : '-' }}
                        </td>
                        <td class="right mono" style="font-size:15px; font-weight:700; color:var(--accent-green);">
                            {{ $accData['total_credit'] > 0 ? 'R ' . number_format($accData['total_credit'], 2, '.', ' ') : '-' }}
                        </td>
                        <td class="right mono" style="font-size:15px; font-weight:700; color:#a78bfa;">
                            R {{ number_format(abs($accData['closing_balance']), 2, '.', ' ') }}
                            {{ $accData['closing_balance'] >= 0 ? ($acc->normal_balance === 'debit' ? 'Dr' : 'Cr') : ($acc->normal_balance === 'debit' ? 'Cr' : 'Dr') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endforeach

    {{-- Grand Total --}}
    <div class="gl-grand-total">
        <span style="font-size:15px; font-weight:800; color:var(--text-primary); text-transform:uppercase; letter-spacing:1px;">Grand Total ({{ $accountCount }} Account{{ $accountCount != 1 ? 's' : '' }})</span>
        <div style="display:flex; gap:32px;">
            <span class="mono" style="font-size:15px; font-weight:800; color:var(--accent-blue);">
                Dr: R {{ number_format($totalDebits, 2, '.', ' ') }}
            </span>
            <span class="mono" style="font-size:15px; font-weight:800; color:var(--accent-green);">
                Cr: R {{ number_format($totalCredits, 2, '.', ' ') }}
            </span>
        </div>
    </div>
</div>
@else
<div class="sl-card sl-animate d3">
    <div style="text-align:center; padding:80px 40px; color:var(--text-muted);">
        <i class="fas fa-book-open" style="font-size:48px; opacity:0.15; margin-bottom:20px; display:block;"></i>
        <div style="font-size:18px; font-weight:700; margin-bottom:8px; color:var(--text-secondary);">No Transactions Found</div>
        <div style="font-size:14px; max-width:400px; margin:0 auto; line-height:1.6;">
            @if($fromAccountId || $toAccountId)
                No posted transactions found for the selected account range in this period. Try adjusting your account range or period filter.
            @else
                No posted transactions found in this period. Create and post journal entries to see transaction details here.
            @endif
        </div>
        @if(!$fromAccountId && !$toAccountId)
        <a href="{{ route('nexcore.clients.show.accounting.journals.create', $client->id) }}" class="neon-btn neon-btn-amber" style="margin-top:24px; display:inline-flex;"><i class="fas fa-plus"></i> Create Journal Entry</a>
        @endif
    </div>
</div>
@endif
@endsection

@push('scripts')
<style>
    .gl-select { flex:1; min-width:200px; max-width:350px; padding:8px 12px; background:var(--bg-raised); border:1px solid var(--border-subtle); border-radius:8px; color:var(--text-primary); font-size:13px; font-family:var(--font-mono); }
    .gl-select:focus { outline:none; border-color:#f59e0b; box-shadow:0 0 0 2px rgba(245,158,11,0.15); }
    .gl-account-header { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; cursor:pointer; border-bottom:1px solid rgba(255,255,255,0.04); transition:background 0.15s ease; }
    .gl-account-header:hover { background:rgba(59,130,246,0.04); }
    .gl-chevron { color:var(--accent-blue); font-size:11px; transition:transform 0.25s ease; display:inline-block; }
    .gl-collapsible.gl-open .gl-chevron { transform:rotate(90deg); }
    .gl-transactions { padding:0 20px 20px 36px; }
    .gl-table { border-collapse:separate; border-spacing:0; }
    .gl-table thead th { font-size:12px; padding:10px 12px !important; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid var(--border-subtle) !important; }
    .gl-txn-row td { padding:8px 12px !important; border-bottom:1px solid rgba(255,255,255,0.03) !important; }
    .gl-txn-row td:first-child { border-left:3px solid #3b82f6 !important; }
    .gl-txn-row:hover td { background:rgba(59,130,246,0.04); }
    .gl-txn-last td:first-child { border-bottom-left-radius:10px; border-bottom:3px solid #3b82f6 !important; }
    .gl-txn-last td { padding-bottom:12px !important; }
    .gl-account-total td { padding:12px 12px !important; border-top:2px solid rgba(167,139,250,0.3) !important; background:rgba(167,139,250,0.03); }
    .gl-grand-total { display:flex; align-items:center; justify-content:space-between; padding:18px 20px; border-top:3px double rgba(245,158,11,0.4); background:rgba(245,158,11,0.03); }
</style>
<script>
function toggleGlAccount(el) {
    var target = el.getAttribute('data-target');
    var txnDiv = el.nextElementSibling;
    if (!txnDiv || !txnDiv.classList.contains(target)) {
        txnDiv = document.querySelector('.' + target);
    }
    if (!txnDiv) return;
    var isOpen = el.classList.contains('gl-open');
    if (isOpen) {
        el.classList.remove('gl-open');
        txnDiv.style.display = 'none';
    } else {
        el.classList.add('gl-open');
        txnDiv.style.display = 'block';
    }
}
</script>
@endpush
