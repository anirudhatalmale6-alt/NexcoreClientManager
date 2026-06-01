@extends('nexcore_client_manager::layouts.accounting')

@section('title', $bankAccount->bank_name . ' Statement - ' . $client->company_name)
@section('page_heading', 'STATEMENT VIEW')

@push('styles')
<style>
/* ═══ TRANSACTION CARDS ═══ */
.sv-txn-list { display:flex; flex-direction:column; gap:14px; padding:18px; }

.sv-txn-card {
    background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12);
    border-radius:16px; padding:22px 26px; transition:all 0.3s cubic-bezier(0.4,0,0.2,1);
}
.sv-txn-card:hover { border-color:rgba(245,158,11,0.4); background:rgba(255,255,255,0.08); box-shadow:0 4px 24px rgba(0,0,0,0.3), 0 0 0 1px rgba(245,158,11,0.15); }
.sv-txn-card.card-alt { background:rgba(59,130,246,0.06); border-color:rgba(59,130,246,0.18); }
.sv-txn-card.card-alt:hover { background:rgba(59,130,246,0.1); border-color:rgba(59,130,246,0.3); box-shadow:0 4px 24px rgba(0,0,0,0.3), 0 0 0 1px rgba(59,130,246,0.2); }
.sv-txn-card.is-posted { border-left:4px solid #22c55e; background:rgba(16,185,129,0.06); }
.sv-txn-card.is-allocated { border-left:4px solid var(--accent-blue,#3b82f6); background:rgba(59,130,246,0.04); }
.sv-txn-card.is-excluded { opacity:0.5; }

/* Row 1: Number, Date, Direction, Status, Balance, Amount */
.sv-txn-row1 { display:flex; align-items:center; gap:16px; margin-bottom:14px; flex-wrap:wrap; }

.sv-txn-num {
    font-family:var(--font-mono,monospace); font-size:18px; font-weight:900; color:var(--text-secondary,#94a3b8);
    width:40px; height:40px; display:flex; align-items:center; justify-content:center; flex-shrink:0;
    background:rgba(255,255,255,0.08); border-radius:10px; border:1px solid rgba(255,255,255,0.12);
}

.sv-txn-date {
    display:inline-block; font-family:var(--font-mono,monospace); font-size:14px; font-weight:700;
    color:#fbbf24; white-space:nowrap; font-variant-numeric:tabular-nums;
    background:rgba(245,158,11,0.15); padding:8px 16px; border-radius:10px;
    border:1px solid rgba(245,158,11,0.3);
}

.sv-txn-dir {
    font-size:11px; padding:6px 14px; border-radius:8px; font-weight:800;
    text-transform:uppercase; letter-spacing:0.5px;
    display:inline-flex; align-items:center; gap:5px;
}
.sv-txn-dir-debit { background:rgba(239,68,68,0.18); color:#fca5a5; border:1px solid rgba(239,68,68,0.35); }
.sv-txn-dir-credit { background:rgba(16,185,129,0.18); color:#6ee7b7; border:1px solid rgba(16,185,129,0.35); }

.sv-txn-status {
    font-size:11px; font-weight:800; padding:5px 12px; border-radius:6px;
    letter-spacing:0.4px; text-transform:uppercase;
}
.sv-txn-status-posted { color:#6ee7b7; background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.3); }
.sv-txn-status-allocated { color:#93c5fd; background:rgba(59,130,246,0.15); border:1px solid rgba(59,130,246,0.3); }
.sv-txn-status-unallocated { color:#fbbf24; background:rgba(245,158,11,0.15); border:1px solid rgba(245,158,11,0.3); }
.sv-txn-status-excluded { color:var(--text-muted,#64748b); background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); }

.sv-txn-stmtref {
    font-size:11px; padding:6px 14px; border-radius:8px; font-weight:800;
    letter-spacing:0.3px; display:inline-flex; align-items:center; gap:5px;
    background:rgba(34,211,238,0.12); color:#22d3ee; border:1px solid rgba(34,211,238,0.3);
    font-family:var(--font-mono,monospace);
}

.sv-txn-balance {
    font-family:var(--font-mono,monospace); font-size:14px; font-weight:600; color:var(--text-secondary,#94a3b8);
    background:rgba(255,255,255,0.06); padding:6px 14px; border-radius:8px; border:1px solid rgba(255,255,255,0.1);
    white-space:nowrap;
}
.sv-txn-balance span { font-size:11px; font-weight:800; color:var(--text-muted,#64748b); text-transform:uppercase; letter-spacing:0.3px; margin-right:6px; }

.sv-txn-amount {
    display:inline-block; padding:8px 20px; border-radius:10px;
    font-family:var(--font-mono,monospace); font-size:18px; font-weight:900;
    white-space:nowrap; font-variant-numeric:tabular-nums;
}
.sv-txn-amount-debit { color:#fca5a5; background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); }
.sv-txn-amount-credit { color:#6ee7b7; background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.3); }

/* Row 2: Description */
.sv-txn-row2 { padding:4px 8px; }
.sv-txn-desc { font-size:15px; font-weight:600; color:var(--text-primary,#f1f5f9); line-height:1.6; word-break:break-word; }

/* Row 3: Allocated account */
.sv-txn-row3 {
    margin-top:14px; padding-top:14px; border-top:1px solid rgba(255,255,255,0.1);
    display:flex; align-items:center; gap:10px;
}
.sv-txn-acct-label {
    font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.5px;
    color:#93c5fd; background:rgba(59,130,246,0.15); padding:5px 12px; border-radius:6px;
    border:1px solid rgba(59,130,246,0.3);
}
.sv-txn-acct-code { font-family:var(--font-mono,monospace); font-size:14px; font-weight:700; color:var(--accent-blue,#3b82f6); }
.sv-txn-acct-name { font-size:14px; font-weight:600; color:var(--text-secondary,#94a3b8); }

/* ═══ SCROLL ═══ */
.sv-scroll { max-height:calc(100vh - 420px); overflow-y:auto; }
.sv-scroll::-webkit-scrollbar { width:6px; }
.sv-scroll::-webkit-scrollbar-track { background:rgba(255,255,255,0.02); }
.sv-scroll::-webkit-scrollbar-thumb { background:rgba(245,158,11,0.2); border-radius:3px; }
.sv-scroll::-webkit-scrollbar-thumb:hover { background:rgba(245,158,11,0.35); }

/* ═══ INFO CARDS ═══ */
.sv-info-grid { display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:20px; }
.sv-info-card { background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.1); border-radius:12px; padding:20px; }
.sv-info-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:var(--text-muted); margin-bottom:8px; }
.sv-info-value { font-size:16px; font-weight:700; color:var(--text-primary); font-family:var(--font-mono); }

.sv-badge { display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:700; padding:6px 14px; border-radius:8px; text-transform:uppercase; letter-spacing:0.5px; }
.sv-badge-amber { color:#fbbf24; background:rgba(245,158,11,0.15); border:1px solid rgba(245,158,11,0.3); }
.sv-badge-green { color:#6ee7b7; background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.3); }
.sv-badge-blue { color:#93c5fd; background:rgba(59,130,246,0.15); border:1px solid rgba(59,130,246,0.3); }

/* ═══ SEARCH ═══ */
.sv-search-input {
    background:rgba(255,255,255,0.07); border:1px solid rgba(255,255,255,0.15); border-radius:10px;
    padding:10px 14px 10px 38px; color:var(--text-primary); font-size:14px; width:280px; outline:none;
    font-family:inherit; transition:all 0.25s;
}
.sv-search-input:focus { border-color:#f59e0b; box-shadow:0 0 0 3px rgba(245,158,11,0.2); background:rgba(255,255,255,0.1); }

@@media (max-width:1200px) {
    .sv-txn-card { padding:16px 18px; }
    .sv-txn-row1 { gap:10px; }
}
@@media (max-width:1200px) {
    .sv-info-grid { grid-template-columns:repeat(3, 1fr) !important; }
}
@@media (max-width:768px) {
    .sv-info-grid { grid-template-columns:repeat(2, 1fr) !important; }
    .sl-stats-grid { grid-template-columns:repeat(3, 1fr) !important; }
    .sv-txn-row1 { flex-wrap:wrap; }
    .sv-txn-amount { font-size:15px; padding:6px 14px; }
    .sv-search-input { width:100%; }
}
</style>
@endpush

@section('content')
@php
    $bankLogo = ($bankAccount->systemBank && $bankAccount->systemBank->bank_logo) ? $bankAccount->systemBank->bank_logo : null;
@endphp
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            @if($bankLogo)
            <div style="width:40px; height:40px; border-radius:10px; overflow:hidden; flex-shrink:0; background:#fff; border:1px solid rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center;">
                <img src="/{{ $bankLogo }}" alt="{{ $bankAccount->bank_name }}" style="width:100%; height:100%; object-fit:contain; padding:3px;">
            </div>
            @else
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(245,158,11,0.15), rgba(245,158,11,0.05)); border:1px solid rgba(245,158,11,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-file-alt" style="color:#f59e0b; font-size:16px;"></i>
            </div>
            @endif
            <div>
                <h1 class="sl-page-title" style="margin:0;">{{ $statement->statement_name ?: $bankAccount->bank_name . ' Statement' }}</h1>
                <span class="sl-page-subtitle">{{ $bankAccount->bank_name }} - {{ $bankAccount->account_number }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; align-items:center; gap:12px;">
            @if($statement->status === 'reconciled')
                <span class="sv-badge sv-badge-blue"><i class="fas fa-check-double"></i> Reconciled</span>
            @elseif($statement->status === 'posted')
                <span class="sv-badge sv-badge-green"><i class="fas fa-check"></i> Posted</span>
            @else
                <span class="sv-badge sv-badge-amber"><i class="fas fa-file-import"></i> Imported</span>
            @endif
            <a href="{{ route('nexcore.clients.show.accounting.bank.statements', [$client->id, $bankAccount->id]) }}" style="font-size:13px; color:var(--text-muted); text-decoration:none; font-weight:600;">
                <i class="fas fa-arrow-left"></i> Back to Register
            </a>
        </div>
    </div>
</div>

{{-- Statement Info Cards --}}
<div class="sv-info-grid sl-animate d2" style="grid-template-columns:repeat(5, 1fr);">
    @if($statement->statement_ref)
    <div class="sv-info-card" style="border-color:rgba(6,182,212,0.3); background:rgba(6,182,212,0.06);">
        <div class="sv-info-label">Statement Ref</div>
        <div class="sv-info-value" style="color:var(--accent-cyan,#22d3ee); font-size:18px; font-weight:900; letter-spacing:0.5px;">{{ $statement->statement_ref }}</div>
    </div>
    @endif
    <div class="sv-info-card">
        <div class="sv-info-label">Statement Period</div>
        <div class="sv-info-value">{{ \Carbon\Carbon::parse($statement->period_from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($statement->period_to)->format('d M Y') }}</div>
    </div>
    <div class="sv-info-card">
        <div class="sv-info-label">File Uploaded</div>
        <div class="sv-info-value" style="font-size:14px;">{{ $statement->original_filename ?: 'N/A' }}</div>
        <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">{{ $statement->created_at->format('d M Y H:i') }}</div>
    </div>
    <div class="sv-info-card">
        <div class="sv-info-label">Opening Balance</div>
        <div class="sv-info-value" style="color:var(--accent-blue);">R {{ number_format($statement->opening_balance, 2, '.', ' ') }}</div>
    </div>
    <div class="sv-info-card">
        <div class="sv-info-label">Closing Balance</div>
        <div class="sv-info-value">R {{ number_format($statement->closing_balance, 2, '.', ' ') }}</div>
    </div>
</div>

{{-- Summary Stats --}}
<div class="sl-stats-grid sl-animate d2" style="grid-template-columns: 0.7fr 0.6fr 1.3fr 0.6fr 1.3fr 1fr;">
    <div class="sl-stat-card" style="border-color:rgba(245,158,11,0.4);">
        <div class="sl-stat-label">Transactions</div>
        <div class="sl-stat-value" style="color:#f59e0b;">{{ $statement->transaction_count }}</div>
    </div>
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Credits</div>
        <div class="sl-stat-value" style="color:var(--accent-green);">{{ $statement->credit_count }}</div>
    </div>
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Total Credits</div>
        <div class="sl-stat-value" style="color:var(--accent-green); font-size:18px; white-space:nowrap;">R {{ number_format($statement->total_credits, 2, '.', ' ') }}</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(239,68,68,0.4);">
        <div class="sl-stat-label">Debits</div>
        <div class="sl-stat-value" style="color:var(--accent-red);">{{ $statement->debit_count }}</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(239,68,68,0.4);">
        <div class="sl-stat-label">Total Debits</div>
        <div class="sl-stat-value" style="color:var(--accent-red); font-size:18px; white-space:nowrap;">R {{ number_format($statement->total_debits, 2, '.', ' ') }}</div>
    </div>
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">Batch Ref</div>
        <div class="sl-stat-value" style="color:var(--accent-blue); font-size:13px; word-break:break-all;">{{ $statement->batch_ref }}</div>
    </div>
</div>

{{-- Transaction Cards --}}
<div class="sl-card sl-animate d3">
    <div class="sl-card-header" style="display:flex; align-items:center; justify-content:space-between;">
        <div class="sl-card-title" style="color:#f59e0b;"><i class="fas fa-list"></i> Transactions ({{ $transactions->count() }})</div>
        <div style="position:relative;">
            <input type="text" id="svSearchBox" class="sv-search-input" placeholder="Search transactions...">
            <i class="fas fa-search" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:12px;"></i>
        </div>
    </div>

    <div class="sv-scroll">
        <div class="sv-txn-list" id="svTxnList">
            @foreach($transactions as $idx => $txn)
            <div class="sv-txn-card {{ $txn->status === 'posted' ? 'is-posted' : ($txn->status === 'allocated' ? 'is-allocated' : ($txn->status === 'excluded' ? 'is-excluded' : '')) }} {{ $idx % 2 === 1 ? 'card-alt' : '' }}" data-search="{{ strtolower($txn->description . ' ' . $txn->amount . ' ' . \Carbon\Carbon::parse($txn->transaction_date)->format('d M Y')) }}">
                <div class="sv-txn-row1">
                    <span class="sv-txn-num">{{ $idx + 1 }}</span>
                    <span class="sv-txn-date">{{ \Carbon\Carbon::parse($txn->transaction_date)->format('d M Y') }}</span>
                    <span class="sv-txn-dir sv-txn-dir-{{ $txn->direction }}">
                        <i class="fas {{ $txn->direction === 'debit' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                        {{ $txn->direction }}
                    </span>
                    @if($txn->status === 'posted')
                        <span class="sv-txn-status sv-txn-status-posted">POSTED</span>
                    @elseif($txn->status === 'allocated')
                        <span class="sv-txn-status sv-txn-status-allocated">ALLOCATED</span>
                    @elseif($txn->status === 'excluded')
                        <span class="sv-txn-status sv-txn-status-excluded">EXCLUDED</span>
                    @else
                        <span class="sv-txn-status sv-txn-status-unallocated">UNALLOCATED</span>
                    @endif
                    @if($statement->statement_ref)
                    <span class="sv-txn-stmtref"><i class="fas fa-file-alt"></i> {{ $statement->statement_ref }}</span>
                    @endif
                    <div style="flex:1;"></div>
                    <span class="sv-txn-balance"><span>Bal</span>R {{ number_format($txn->balance, 2, '.', ' ') }}</span>
                    <span class="sv-txn-amount {{ $txn->direction === 'debit' ? 'sv-txn-amount-debit' : 'sv-txn-amount-credit' }}">{{ $txn->direction === 'credit' ? '+' : '-' }}R {{ number_format(abs($txn->amount), 2, '.', ' ') }}</span>
                </div>
                <div class="sv-txn-row2">
                    <span class="sv-txn-desc">{{ $txn->description }}</span>
                </div>
                @if($txn->allocatedAccount)
                <div class="sv-txn-row3">
                    <span class="sv-txn-acct-label">GL</span>
                    <span class="sv-txn-acct-code">{{ $txn->allocatedAccount->account_code }}</span>
                    <span class="sv-txn-acct-name">{{ $txn->allocatedAccount->account_name }}</span>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('svSearchBox').addEventListener('input', function() {
    var q = this.value.toLowerCase();
    var cards = document.querySelectorAll('#svTxnList .sv-txn-card');
    cards.forEach(function(card) {
        card.style.display = card.getAttribute('data-search').indexOf(q) > -1 ? '' : 'none';
    });
});
</script>
@endpush
