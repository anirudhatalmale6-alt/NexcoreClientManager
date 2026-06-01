@extends('nexcore_client_manager::layouts.accounting')
@section('title', 'Allocate Bank Transactions - ' . $client->company_name)
@section('page_heading', 'ALLOCATE TRANSACTIONS')

@push('styles')
<style>
.al-wrap { min-height:calc(100vh - 120px); padding:28px 32px; }

.al-header {
    background:var(--bg-surface,rgba(15,19,32,0.95)); border-radius:18px; border:1px solid var(--border-subtle,rgba(255,255,255,0.06));
    padding:22px 28px; margin-bottom:24px;
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;
}
.al-header-left { display:flex; align-items:center; gap:16px; }
.al-header-icon {
    width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center;
    background:linear-gradient(135deg,rgba(245,158,11,0.15),rgba(245,158,11,0.05)); color:#f59e0b; font-size:22px; flex-shrink:0;
    border:1px solid rgba(245,158,11,0.3);
}
.al-header-title { font-size:22px; font-weight:900; color:var(--text-primary,#f1f5f9); margin:0; letter-spacing:-0.3px; }
.al-header-sub { font-size:14px; color:var(--text-muted,#64748b); font-weight:600; margin-top:2px; }
.al-header-btns { display:flex; gap:10px; flex-wrap:wrap; }

.al-btn {
    display:inline-flex; align-items:center; gap:8px; padding:10px 20px;
    border:none; border-radius:12px; font-size:13px; font-weight:800; cursor:pointer;
    text-decoration:none; transition:all 0.3s cubic-bezier(0.4,0,0.2,1); letter-spacing:0.3px; font-family:inherit;
    position:relative; overflow:hidden;
}
.al-btn:hover { transform:translateY(-2px); text-decoration:none; }
.al-btn-primary { background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; box-shadow:0 4px 14px rgba(245,158,11,0.3); }
.al-btn-primary:hover { box-shadow:0 6px 24px rgba(245,158,11,0.5), 0 0 40px rgba(245,158,11,0.15); color:#fff; }
.al-btn-green { background:linear-gradient(135deg,#22c55e,#16a34a); color:#fff; box-shadow:0 4px 14px rgba(34,197,94,0.3); }
.al-btn-green:hover { box-shadow:0 6px 24px rgba(34,197,94,0.5), 0 0 40px rgba(34,197,94,0.15); color:#fff; }
.al-btn-cyan { background:linear-gradient(135deg,#06b6d4,#0891b2); color:#fff; box-shadow:0 4px 14px rgba(6,182,212,0.3); }
.al-btn-cyan:hover { box-shadow:0 6px 24px rgba(6,182,212,0.5), 0 0 40px rgba(6,182,212,0.15); color:#fff; }
.al-btn-outline { background:transparent; color:var(--text-secondary,#94a3b8); border:2px solid var(--border-subtle,rgba(255,255,255,0.1)); }
.al-btn-outline:hover { border-color:#f59e0b; color:#f59e0b; }
.al-btn-sm { padding:8px 16px; font-size:12px; border-radius:10px; }

.al-alert { padding:14px 20px; border-radius:12px; margin-bottom:20px; font-size:14px; font-weight:700; display:flex; align-items:center; gap:10px; }
.al-alert-success { background:rgba(16,185,129,0.08); color:#10b981; border:1px solid rgba(16,185,129,0.25); }
.al-alert-error { background:rgba(239,68,68,0.08); color:#ef4444; border:1px solid rgba(239,68,68,0.25); }

.al-kpi-row { display:flex; gap:14px; margin-bottom:22px; flex-wrap:wrap; }
.al-kpi {
    background:var(--bg-surface,rgba(15,19,32,0.95)); border-radius:14px; border:1px solid var(--border-subtle,rgba(255,255,255,0.06)); padding:14px 22px;
    display:flex; align-items:center; gap:10px;
}
.al-kpi-icon { width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
.al-kpi-icon-warn { background:rgba(245,158,11,0.1); color:#f59e0b; }
.al-kpi-icon-ok { background:rgba(16,185,129,0.1); color:#22c55e; }
.al-kpi-icon-total { background:rgba(59,130,246,0.1); color:var(--accent-blue,#3b82f6); }
.al-kpi-icon-value { background:rgba(236,72,153,0.1); color:#ec4899; }
.al-kpi-label { font-size:12px; font-weight:700; color:var(--text-muted,#64748b); text-transform:uppercase; letter-spacing:0.5px; }
.al-kpi-val { font-size:20px; font-weight:900; color:var(--text-primary,#f1f5f9); }

.al-toolbar {
    background:var(--bg-surface,rgba(15,19,32,0.95)); border-radius:14px; border:1px solid var(--border-subtle,rgba(255,255,255,0.06)); padding:14px 22px;
    margin-bottom:18px; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
}
.al-toolbar-left { display:flex; gap:10px; align-items:center; }
.al-toolbar-right { display:flex; gap:10px; align-items:center; }

/* ═══ MAIN TABLE CARD ═══ */
.al-card {
    background:var(--bg-surface,rgba(15,19,32,0.95)); border-radius:14px; border:1px solid var(--border-subtle,rgba(255,255,255,0.06));
    overflow:hidden;
}
.al-card-header {
    padding:14px 20px; display:flex; align-items:center; justify-content:space-between;
    border-bottom:2px solid rgba(245,158,11,0.12);
}
.al-card-title { font-size:15px; font-weight:800; color:#f59e0b; display:flex; align-items:center; gap:8px; }
.al-card-count { font-size:12px; color:var(--text-muted,#64748b); font-family:var(--font-mono,monospace); }

/* ═══ TRANSACTION CARDS ═══ */
.al-txn-list { display:flex; flex-direction:column; gap:14px; padding:18px; }

.al-txn-card {
    background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12);
    border-radius:16px; padding:22px 26px; transition:all 0.3s cubic-bezier(0.4,0,0.2,1);
}
.al-txn-card:hover { border-color:rgba(245,158,11,0.4); background:rgba(255,255,255,0.08); box-shadow:0 4px 24px rgba(0,0,0,0.3), 0 0 0 1px rgba(245,158,11,0.15); }
.al-txn-card.card-alt { background:rgba(59,130,246,0.06); border-color:rgba(59,130,246,0.18); }
.al-txn-card.card-alt:hover { background:rgba(59,130,246,0.1); border-color:rgba(59,130,246,0.3); box-shadow:0 4px 24px rgba(0,0,0,0.3), 0 0 0 1px rgba(59,130,246,0.2); }
.al-txn-card.allocated { border-left:4px solid #22c55e; background:rgba(16,185,129,0.06); }
.al-txn-card.allocated:hover { background:rgba(16,185,129,0.1); border-color:rgba(16,185,129,0.35); border-left-color:#22c55e; }

/* Row 1: Number, Date, Type, Amount */
.al-txn-row1 { display:flex; align-items:center; gap:16px; margin-bottom:14px; }

.txn-num {
    font-family:var(--font-mono,monospace); font-size:18px; font-weight:900; color:var(--text-secondary,#94a3b8);
    width:40px; height:40px; display:flex; align-items:center; justify-content:center; flex-shrink:0;
    background:rgba(255,255,255,0.08); border-radius:10px; border:1px solid rgba(255,255,255,0.12);
}
.al-txn-card.allocated .txn-num { color:var(--accent-green,#10b981); border-color:rgba(16,185,129,0.35); background:rgba(16,185,129,0.12); }

.txn-date {
    display:inline-block; font-family:var(--font-mono,monospace); font-size:14px; font-weight:700;
    color:#fbbf24; white-space:nowrap; font-variant-numeric:tabular-nums;
    background:rgba(245,158,11,0.15); padding:8px 16px; border-radius:10px;
    border:1px solid rgba(245,158,11,0.3);
}

.txn-dir {
    font-size:11px; padding:6px 14px; border-radius:8px; font-weight:800;
    text-transform:uppercase; letter-spacing:0.5px;
    display:inline-flex; align-items:center; gap:5px;
}
.txn-dir-debit { background:rgba(239,68,68,0.18); color:#fca5a5; border:1px solid rgba(239,68,68,0.35); }
.txn-dir-credit { background:rgba(16,185,129,0.18); color:#6ee7b7; border:1px solid rgba(16,185,129,0.35); }

.txn-stmt-ref {
    font-size:11px; padding:6px 14px; border-radius:8px; font-weight:800;
    letter-spacing:0.3px; display:inline-flex; align-items:center; gap:5px;
    background:rgba(34,211,238,0.12); color:#22d3ee; border:1px solid rgba(34,211,238,0.3);
    font-family:var(--font-mono,monospace);
}

.txn-amount-pill {
    display:inline-block; padding:8px 20px; border-radius:10px;
    font-family:var(--font-mono,monospace); font-size:18px; font-weight:900;
    white-space:nowrap; font-variant-numeric:tabular-nums;
}
.txn-amount-debit { color:#fca5a5; background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); }
.txn-amount-credit { color:#6ee7b7; background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.3); }

/* Row 2: Description */
.al-txn-row2 { margin-bottom:18px; padding:4px 8px; display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
.txn-desc { font-size:15px; font-weight:600; color:var(--text-primary,#f1f5f9); line-height:1.6; word-break:break-word; }

.alloc-suggest {
    font-size:12px; color:#f59e0b; font-weight:700; cursor:pointer;
    display:inline-flex; align-items:center; gap:5px;
    padding:5px 14px; border-radius:8px; background:rgba(245,158,11,0.15);
    border:1px solid rgba(245,158,11,0.3); transition:all 0.15s; flex-shrink:0;
}
.alloc-suggest:hover { background:rgba(245,158,11,0.25); border-color:rgba(245,158,11,0.5); text-decoration:none; }

/* Row 3: Controls */
.al-txn-row3 {
    display:flex; align-items:center; gap:16px; flex-wrap:wrap;
    padding-top:18px; border-top:1px solid rgba(255,255,255,0.1);
}
.al-ctrl-item { display:flex; align-items:center; gap:8px; }
.al-ctrl-label {
    font-size:11px; font-weight:800; white-space:nowrap;
    text-transform:uppercase; letter-spacing:0.5px;
    color:#fbbf24; background:rgba(245,158,11,0.15); padding:5px 12px; border-radius:6px;
    border:1px solid rgba(245,158,11,0.3);
}

.al-txn-card input[type="text"], .al-txn-card select {
    padding:10px 14px; border:1px solid rgba(255,255,255,0.15); border-radius:10px;
    font-size:14px; font-weight:500; width:100%; font-family:inherit;
    background:rgba(255,255,255,0.07); color:var(--text-primary,#f1f5f9); transition:all 0.25s;
}
.al-txn-card input[type="text"]:focus, .al-txn-card select:focus { border-color:#f59e0b; outline:none; box-shadow:0 0 0 3px rgba(245,158,11,0.2); background:rgba(255,255,255,0.1); }
.al-txn-card select option { background:#1a1d29; color:#f1f5f9; }

.rule-check { display:flex; align-items:center; gap:6px; }
.rule-check input[type="checkbox"] { margin:0; width:17px; height:17px; accent-color:#f59e0b; cursor:pointer; }
.rule-check label { font-size:13px; font-weight:600; color:var(--text-secondary,#94a3b8); cursor:pointer; white-space:nowrap; }
.rule-keyword {
    padding:10px 14px; border:1px solid rgba(255,255,255,0.15); border-radius:10px;
    font-size:13px; font-weight:500; width:160px; font-family:inherit;
    background:rgba(255,255,255,0.07); color:var(--text-secondary,#94a3b8);
}
.rule-keyword:focus { border-color:#f59e0b; outline:none; box-shadow:0 0 0 3px rgba(245,158,11,0.15); color:var(--text-primary,#f1f5f9); }

/* ═══ SCROLL ═══ */
.al-scroll { max-height:calc(100vh - 340px); overflow-y:auto; }
.al-scroll::-webkit-scrollbar { width:6px; }
.al-scroll::-webkit-scrollbar-track { background:rgba(255,255,255,0.02); }
.al-scroll::-webkit-scrollbar-thumb { background:rgba(245,158,11,0.2); border-radius:3px; }
.al-scroll::-webkit-scrollbar-thumb:hover { background:rgba(245,158,11,0.35); }

/* ═══ FOOTER ═══ */
.al-footer {
    display:flex; gap:12px; padding:16px 20px; background:rgba(245,158,11,0.03);
    border-top:2px solid rgba(245,158,11,0.12); align-items:center;
}

/* ═══ EXCLUDE BUTTON ═══ */
.al-exclude-btn {
    width:34px; height:34px; border-radius:8px; border:1px solid rgba(239,68,68,0.2);
    background:rgba(239,68,68,0.05); color:rgba(239,68,68,0.5); cursor:pointer; transition:all 0.2s;
    display:inline-flex; align-items:center; justify-content:center; font-size:13px; flex-shrink:0;
}
.al-exclude-btn:hover { background:rgba(239,68,68,0.15); border-color:rgba(239,68,68,0.4); color:#f87171; transform:scale(1.05); }

/* ═══ EMPTY STATE ═══ */
.al-empty { text-align:center; padding:60px 20px; }
.al-empty i { font-size:48px; color:var(--text-muted,#64748b); opacity:0.3; margin-bottom:16px; display:block; }
.al-empty p { font-size:15px; color:var(--text-muted,#64748b); font-weight:500; margin-bottom:20px; }

/* ═══ ACCOUNT PICKER ═══ */
.acc-picker { position:relative; }
.acc-picker-input {
    padding:10px 30px 10px 14px; border:1px solid rgba(255,255,255,0.15); border-radius:10px;
    font-size:14px; font-weight:500; width:100%; background:rgba(255,255,255,0.07); cursor:text;
    font-family:inherit; color:var(--text-secondary,#94a3b8); transition:all 0.25s;
}
.acc-picker-input:focus { border-color:#f59e0b; outline:none; box-shadow:0 0 0 3px rgba(245,158,11,0.2); background:rgba(255,255,255,0.1); }
.acc-picker-input.has-value { color:#f59e0b; font-weight:700; }
.acc-picker-clear {
    position:absolute; right:8px; top:50%; transform:translateY(-50%);
    cursor:pointer; color:var(--text-muted,#64748b); font-size:14px; line-height:1; display:none;
    width:18px; height:18px; border-radius:50%; background:rgba(255,255,255,0.06); text-align:center;
}
.acc-picker-clear:hover { color:#f87171; background:rgba(239,68,68,0.1); }
.acc-picker.filled .acc-picker-clear { display:flex; align-items:center; justify-content:center; }
.acc-picker-dropdown {
    position:absolute; top:calc(100% + 4px); left:0; right:0;
    background:var(--bg-deepest,#0a0e1a); border:1px solid rgba(245,158,11,0.25);
    border-top:3px solid #f59e0b; border-radius:12px; max-height:240px;
    overflow-y:auto; z-index:100; display:none; box-shadow:0 16px 40px rgba(0,0,0,0.7);
}
.acc-picker.open .acc-picker-dropdown { display:block; }
.acc-picker-item {
    padding:11px 16px; font-size:14px; font-weight:500; cursor:pointer;
    border-bottom:1px solid rgba(255,255,255,0.04); color:var(--text-secondary,#94a3b8); transition:all 0.15s;
}
.acc-picker-item:hover, .acc-picker-item.highlighted { background:rgba(245,158,11,0.12); color:#f59e0b; font-weight:700; }
.acc-picker-item:last-child { border-bottom:none; }
.acc-picker-empty { padding:14px; font-size:14px; color:var(--text-muted,#64748b); text-align:center; font-weight:500; }

/* ═══ QUICK-ADD MODAL ═══ */
.qa-overlay { position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.6); backdrop-filter:blur(4px); z-index:9998; display:none; }
.qa-overlay.show { display:flex; align-items:center; justify-content:center; }
.qa-modal { background:var(--bg-surface,#0f1320); border:1px solid var(--border-subtle,rgba(255,255,255,0.08)); border-radius:18px; width:520px; max-width:95vw; box-shadow:0 20px 60px rgba(0,0,0,0.5); overflow:hidden; }
.qa-modal-header {
    background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff;
    padding:18px 24px; display:flex; justify-content:space-between; align-items:center;
}
.qa-modal-header h3 { margin:0; font-size:16px; font-weight:900; font-family:inherit; }
.qa-modal-close { color:rgba(255,255,255,0.7); cursor:pointer; font-size:22px; background:none; border:none; font-family:inherit; }
.qa-modal-close:hover { color:#fff; }
.qa-modal-body { padding:24px; }
.qa-row { margin-bottom:16px; }
.qa-row label { display:block; font-size:12px; font-weight:800; color:var(--text-muted,#64748b); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px; }
.qa-row select, .qa-row input[type="text"] {
    width:100%; padding:10px 14px; border:1px solid var(--border-subtle,rgba(255,255,255,0.1)); border-radius:10px;
    font-size:14px; font-weight:600; font-family:inherit; color:var(--text-primary,#f1f5f9); background:var(--bg-raised,rgba(255,255,255,0.04));
}
.qa-row select option { background:#1a1d29; color:#f1f5f9; }
.qa-row select:focus, .qa-row input:focus { border-color:#f59e0b; outline:none; background:rgba(255,255,255,0.06); }
.qa-row-half { display:flex; gap:16px; }
.qa-row-half .qa-row { flex:1; margin-bottom:0; }
.qa-modal-footer {
    padding:16px 24px; background:rgba(255,255,255,0.02); border-top:1px solid var(--border-subtle,rgba(255,255,255,0.06));
    display:flex; justify-content:flex-end; gap:10px;
}
.qa-error { color:#ef4444; font-size:13px; font-weight:700; margin-bottom:12px; display:none; padding:10px; background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2); border-radius:8px; }

@@media (max-width:1200px) {
    .al-wrap { padding:16px; }
    .al-kpi-row { gap:10px; }
    .al-kpi { padding:10px 16px; }
    .al-txn-card { padding:16px 18px; }
    .al-txn-row1 { flex-wrap:wrap; gap:10px; }
    .al-txn-row3 { gap:10px; }
    .al-ctrl-item { flex:1; min-width:140px; }
}
@@media (max-width:768px) {
    .al-txn-row1 { flex-wrap:wrap; }
    .al-txn-card { padding:14px 16px; }
    .txn-amount-pill { font-size:15px; padding:6px 14px; }
    .al-txn-row3 { flex-direction:column; align-items:stretch; }
    .al-ctrl-item { width:100% !important; max-width:none !important; }
}
</style>
@endpush

@section('content')
@php
    $batchRefs = $transactions->pluck('batch_ref')->unique()->filter()->values()->all();
    $stmtRefMap = \Modules\NexcoreClientManager\Models\NexcoreBankStatement::whereIn('batch_ref', $batchRefs)->pluck('statement_ref', 'batch_ref')->all();
    $bankLogo = ($bankAccount->systemBank && $bankAccount->systemBank->bank_logo) ? $bankAccount->systemBank->bank_logo : null;
@endphp
<div class="al-wrap">
    <div class="al-header">
        <div class="al-header-left">
            @if($bankLogo)
            <div class="al-header-icon" style="background:#fff; border:1px solid rgba(255,255,255,0.15); overflow:hidden; padding:0;">
                <img src="/{{ $bankLogo }}" alt="{{ $bankAccount->bank_name }}" style="width:100%; height:100%; object-fit:contain; padding:6px;">
            </div>
            @else
            <div class="al-header-icon"><i class="fas fa-tags"></i></div>
            @endif
            <div>
                <h1 class="al-header-title">Allocate Bank Transactions</h1>
                <div class="al-header-sub">{{ $bankAccount->bank_name }} - {{ $bankAccount->account_number }} | {{ $client->company_name }}{{ $client->trading_name ? ' t/a ' . $client->trading_name : '' }}</div>
            </div>
        </div>
        <div class="al-header-btns">
            <button type="button" class="al-btn al-btn-primary al-btn-sm" onclick="openQuickAdd()"><i class="fas fa-plus"></i> Add GL Account</button>
            <a href="{{ route('nexcore.clients.show.accounting.bank.accounts', $client->id) }}" class="al-btn al-btn-outline al-btn-sm"><i class="fas fa-arrow-left"></i> Banks</a>
        </div>
    </div>

    @if(session('success'))
    <div class="al-alert al-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="al-alert al-alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    @php
        $unallocCount = $transactions->where('status', 'unallocated')->count();
        $allocCount = $transactions->where('status', 'allocated')->count();
        $totalAmount = $transactions->sum('amount');
    @endphp

    @if($transactions->count() > 0)
    <div class="al-kpi-row">
        <div class="al-kpi">
            <div class="al-kpi-icon al-kpi-icon-warn"><i class="fas fa-clock"></i></div>
            <div>
                <div class="al-kpi-label">Unallocated</div>
                <div class="al-kpi-val" style="color:#f59e0b;">{{ $unallocCount }}</div>
            </div>
        </div>
        <div class="al-kpi">
            <div class="al-kpi-icon al-kpi-icon-ok"><i class="fas fa-check"></i></div>
            <div>
                <div class="al-kpi-label">Allocated</div>
                <div class="al-kpi-val" style="color:#22c55e;">{{ $allocCount }}</div>
            </div>
        </div>
        <div class="al-kpi">
            <div class="al-kpi-icon al-kpi-icon-total"><i class="fas fa-list"></i></div>
            <div>
                <div class="al-kpi-label">Total</div>
                <div class="al-kpi-val">{{ $transactions->count() }}</div>
            </div>
        </div>
        <div class="al-kpi">
            <div class="al-kpi-icon al-kpi-icon-value"><i class="fas fa-coins"></i></div>
            <div>
                <div class="al-kpi-label">Value</div>
                <div class="al-kpi-val">R {{ number_format($totalAmount, 2, '.', ' ') }}</div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('nexcore.clients.show.accounting.bank.allocate.save', [$client->id, $bankAccount->id]) }}" id="allocForm">
        @csrf
        <div class="al-toolbar">
            <div class="al-toolbar-left">
                <button type="submit" class="al-btn al-btn-green"><i class="fas fa-save"></i> Save Allocations</button>
                <button type="button" class="al-btn al-btn-primary" onclick="postAllocated()"><i class="fas fa-check-double"></i> Post to GL</button>
                <button type="button" class="al-btn al-btn-cyan" id="applyRulesBtn" onclick="applyRules()"><i class="fas fa-magic"></i> Apply Rules</button>
            </div>
            <div class="al-toolbar-right">
                <a href="{{ route('nexcore.clients.show.accounting.bank.accounts', $client->id) }}" class="al-btn al-btn-outline al-btn-sm"><i class="fas fa-university"></i> Bank Accounts</a>
            </div>
        </div>

        <div class="al-card">
            <div class="al-card-header">
                <div class="al-card-title"><i class="fas fa-tags"></i> Transaction Allocation</div>
                <span class="al-card-count">{{ $transactions->count() }} {{ \Illuminate\Support\Str::plural('transaction', $transactions->count()) }}</span>
            </div>
            <div class="al-scroll">
            <div class="al-txn-list">
                @foreach($transactions as $txn)
                <div class="al-txn-card {{ $txn->status === 'allocated' ? 'allocated' : '' }} {{ $loop->index % 2 === 1 ? 'card-alt' : '' }}" id="row-{{ $txn->id }}">
                    <div class="al-txn-row1">
                        <span class="txn-num">{{ $loop->iteration }}</span>
                        <span class="txn-date">{{ $txn->transaction_date->format('j M Y') }}</span>
                        <span class="txn-dir txn-dir-{{ $txn->direction }}">
                            <i class="fas {{ $txn->direction === 'debit' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                            {{ $txn->direction }}
                        </span>
                        @if(isset($stmtRefMap[$txn->batch_ref]) && $stmtRefMap[$txn->batch_ref])
                        <span class="txn-stmt-ref"><i class="fas fa-file-alt"></i> {{ $stmtRefMap[$txn->batch_ref] }}</span>
                        @endif
                        <div style="flex:1;"></div>
                        <span class="txn-amount-pill {{ $txn->direction === 'debit' ? 'txn-amount-debit' : 'txn-amount-credit' }}">R {{ number_format($txn->amount, 2, '.', ' ') }}</span>
                        <button type="button" class="al-exclude-btn" onclick="excludeTxn({{ $txn->id }})" title="Exclude">
                            <i class="fas fa-ban"></i>
                        </button>
                    </div>
                    <div class="al-txn-row2">
                        <span class="txn-desc">{{ $txn->description }}</span>
                        @if(!$txn->allocated_account_id && isset($txn->suggested_account_id) && $txn->suggested_account_id)
                        <div class="alloc-suggest" onclick="applySuggestion({{ $txn->id }}, {{ $txn->suggested_account_id }}, '{{ $txn->suggested_vat_type ?? 'standard' }}', '{{ addslashes($txn->suggested_account_name ?? '') }}')">
                            <i class="fas fa-magic"></i> {{ $txn->suggested_account_name }}
                        </div>
                        @endif
                    </div>
                    <div class="al-txn-row3">
                        <div class="al-ctrl-item" style="flex:1; max-width:400px;">
                            <span class="al-ctrl-label">GL</span>
                            <div class="acc-picker" id="picker-{{ $txn->id }}" style="flex:1;">
                                <input type="hidden" name="allocations[{{ $txn->id }}][account_id]" id="accval-{{ $txn->id }}" value="{{ $txn->allocated_account_id ?? '' }}">
                                <input type="text" class="acc-picker-input{{ $txn->allocated_account_id ? ' has-value' : '' }}" id="accinp-{{ $txn->id }}"
                                    placeholder="Search GL account..."
                                    value="{{ $txn->allocated_account_id ? ($accounts->firstWhere('id', $txn->allocated_account_id)->account_name ?? '') : '' }}"
                                    autocomplete="off">
                                <span class="acc-picker-clear" onclick="clearPicker({{ $txn->id }})">&times;</span>
                                <div class="acc-picker-dropdown" id="accdrop-{{ $txn->id }}"></div>
                            </div>
                            @if(!$txn->allocated_account_id && isset($txn->suggested_account_id) && $txn->suggested_account_id)
                            <div class="alloc-suggest" onclick="applySuggestion({{ $txn->id }}, {{ $txn->suggested_account_id }}, '{{ $txn->suggested_vat_type ?? 'standard' }}', '{{ addslashes($txn->suggested_account_name ?? '') }}')">
                                <i class="fas fa-magic"></i> {{ $txn->suggested_account_name }}
                            </div>
                            @endif
                        </div>
                        <div class="al-ctrl-item" style="width:170px;">
                            <span class="al-ctrl-label">VAT</span>
                            <select name="allocations[{{ $txn->id }}][vat_type]" id="vat-{{ $txn->id }}" style="flex:1;">
                                <option value="standard" {{ ($txn->vat_type ?? '') === 'standard' ? 'selected' : '' }}>15% VAT</option>
                                <option value="zero_rated" {{ ($txn->vat_type ?? '') === 'zero_rated' ? 'selected' : '' }}>Zero Rated</option>
                                <option value="exempt" {{ ($txn->vat_type ?? '') === 'exempt' ? 'selected' : '' }}>Exempt</option>
                                <option value="none" {{ ($txn->vat_type ?? 'none') === 'none' ? 'selected' : '' }}>None</option>
                            </select>
                        </div>
                        <div class="al-ctrl-item">
                            <div class="rule-check">
                                <input type="checkbox" name="allocations[{{ $txn->id }}][save_rule]" value="1" id="rule-{{ $txn->id }}">
                                <label for="rule-{{ $txn->id }}">Save Rule</label>
                            </div>
                            <input type="text" class="rule-keyword" name="allocations[{{ $txn->id }}][rule_keyword]" placeholder="keyword"
                                value="{{ strtolower(substr($txn->description, 0, 30)) }}">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            </div>

            <div class="al-footer">
                <button type="submit" class="al-btn al-btn-green"><i class="fas fa-save"></i> Save Allocations</button>
                <button type="button" class="al-btn al-btn-primary" onclick="postAllocated()"><i class="fas fa-check-double"></i> Post to GL</button>
                <div style="flex:1;"></div>
                <a href="{{ route('nexcore.clients.show.accounting.bank.accounts', $client->id) }}" class="al-btn al-btn-outline al-btn-sm"><i class="fas fa-university"></i> Bank Accounts</a>
            </div>
        </div>
    </form>

    <form method="POST" action="{{ route('nexcore.clients.show.accounting.bank.allocate.post', [$client->id, $bankAccount->id]) }}" id="postForm" style="display:none;">@csrf</form>

    @else
    <div class="al-card" style="padding:0;">
        <div class="al-empty">
            <i class="fas fa-check-circle"></i>
            <p>All transactions have been processed. Import more statements or view journals.</p>
            <div style="display:flex;gap:12px;justify-content:center;margin-top:16px;">
                <a href="{{ route('nexcore.clients.show.accounting.bank.accounts', $client->id) }}" class="al-btn al-btn-primary"><i class="fas fa-university"></i> Bank Accounts</a>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="qa-overlay" id="qaOverlay">
    <div class="qa-modal">
        <div class="qa-modal-header">
            <h3><i class="fas fa-plus-circle"></i> Quick Add GL Account</h3>
            <button class="qa-modal-close" onclick="closeQuickAdd()">&times;</button>
        </div>
        <div class="qa-modal-body">
            <div class="qa-error" id="qaError"></div>
            <div class="qa-row">
                <label>Parent Account (Level 2)</label>
                <select id="qaParent" onchange="onParentChange()">
                    <option value="">-- Select Parent --</option>
                    @foreach($parentAccounts as $pa)
                    <option value="{{ $pa->id }}" data-seg1="{{ $pa->segment1 }}" data-seg2="{{ $pa->segment2 }}" data-type="{{ $pa->account_type }}" data-bal="{{ $pa->normal_balance }}">
                        {{ $pa->account_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="qa-row-half">
                <div class="qa-row">
                    <label>Account Code (4 digits)</label>
                    <input type="text" id="qaCode" maxlength="4" placeholder="e.g. 0010" style="font-family:'Courier New',monospace;">
                </div>
                <div class="qa-row">
                    <label>Full Code Preview</label>
                    <input type="text" id="qaPreview" readonly style="background:rgba(245,158,11,0.06);font-family:'Courier New',monospace;color:#f59e0b;font-weight:800;">
                </div>
            </div>
            <div class="qa-row">
                <label>Account Name</label>
                <input type="text" id="qaName" placeholder="e.g. Bank Charges">
            </div>
            <div class="qa-row-half">
                <div class="qa-row">
                    <label>Account Type</label>
                    <select id="qaType">
                        <option value="asset">Asset</option>
                        <option value="liability">Liability</option>
                        <option value="equity">Equity</option>
                        <option value="revenue">Revenue</option>
                        <option value="cost_of_sales">Cost of Sales</option>
                        <option value="expense" selected>Expense</option>
                    </select>
                </div>
                <div class="qa-row">
                    <label>Normal Balance</label>
                    <select id="qaBal">
                        <option value="debit" selected>Debit</option>
                        <option value="credit">Credit</option>
                    </select>
                </div>
            </div>
            <div class="qa-row">
                <label>VAT Treatment</label>
                <select id="qaVat">
                    <option value="standard">15% VAT</option>
                    <option value="zero_rated">Zero Rated</option>
                    <option value="exempt">Exempt</option>
                    <option value="none" selected>None</option>
                </select>
            </div>
        </div>
        <div class="qa-modal-footer">
            <button type="button" class="al-btn al-btn-outline al-btn-sm" onclick="closeQuickAdd()">Cancel</button>
            <button type="button" class="al-btn al-btn-green al-btn-sm" id="qaSaveBtn" onclick="saveQuickAdd()"><i class="fas fa-save"></i> Save & Reload</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var NxAlert = {
    _logo: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB4PSIyIiB5PSIyIiB3aWR0aD0iMjgiIGhlaWdodD0iMjgiIHJ4PSI2IiBmaWxsPSIjMDU5NjY5Ii8+PHJlY3QgeD0iMzQiIHk9IjIiIHdpZHRoPSIyOCIgaGVpZ2h0PSIyOCIgcng9IjYiIGZpbGw9IiMyNTYzZWIiLz48cmVjdCB4PSIyIiB5PSIzNCIgd2lkdGg9IjI4IiBoZWlnaHQ9IjI4IiByeD0iNiIgZmlsbD0iI2Q5NzcwNiIvPjxyZWN0IHg9IjM0IiB5PSIzNCIgd2lkdGg9IjI4IiBoZWlnaHQ9IjI4IiByeD0iNiIgZmlsbD0iIzdjM2FlZCIvPjwvc3ZnPgo=',
    _colors: {
        success:  { btn: '#059669', pastel: '#d1fae5', cls: 'nx-swal-success' },
        warning:  { btn: '#e11d48', pastel: '#ffe4e6', cls: 'nx-swal-warning' },
        info:     { btn: '#2563eb', pastel: '#dbeafe', cls: 'nx-swal-info' },
        confirm:  { btn: '#0891b2', pastel: '#cffafe', cls: 'nx-swal-confirm' },
        error:    { btn: '#d97706', pastel: '#fef3c7', cls: 'nx-swal-error' }
    },
    _fire: function(type, title, message, opts) {
        var c = this._colors[type] || this._colors.info;
        var config = {
            imageUrl: this._logo, imageWidth: 56, imageHeight: 56,
            title: title, html: message,
            confirmButtonText: (opts && opts.confirmText) || 'OK',
            confirmButtonColor: c.btn,
            background: '#ffffff',
            showCancelButton: !!(opts && opts.showCancel),
            cancelButtonText: (opts && opts.cancelText) || 'CANCEL',
            customClass: { popup: 'nx-swal-popup ' + c.cls, title: 'nx-swal-title', htmlContainer: 'nx-swal-html', actions: 'nx-swal-actions' }
        };
        return Swal.fire(config);
    },
    success: function(title, message) { return this._fire('success', title, message); },
    warning: function(title, message) { return this._fire('warning', title, message); },
    info: function(title, message) { return this._fire('info', title, message); },
    error: function(title, message) { return this._fire('error', title, message); },
    confirm: function(title, message, confirmText) {
        return this._fire('confirm', title, message, { showCancel: true, confirmText: confirmText || 'YES, CONFIRM', cancelText: 'CANCEL' });
    }
};

var accList = [
@foreach($accounts as $acc)
    {id:{{ $acc->id }},name:"{{ addslashes($acc->account_name) }}",vat:"{{ $acc->vat_type ?? 'none' }}"},
@endforeach
];

function initPicker(txnId) {
    var inp = document.getElementById('accinp-' + txnId);
    var drop = document.getElementById('accdrop-' + txnId);
    var wrap = document.getElementById('picker-' + txnId);
    var hidden = document.getElementById('accval-' + txnId);
    var hlIdx = -1;

    inp.addEventListener('focus', function() {
        filterAndShow(inp.value);
        wrap.classList.add('open');
    });

    inp.addEventListener('input', function() {
        hidden.value = '';
        wrap.classList.remove('filled');
        inp.classList.remove('has-value');
        hlIdx = -1;
        filterAndShow(inp.value);
    });

    inp.addEventListener('keydown', function(e) {
        var items = drop.querySelectorAll('.acc-picker-item');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            hlIdx = Math.min(hlIdx + 1, items.length - 1);
            updateHighlight(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            hlIdx = Math.max(hlIdx - 1, 0);
            updateHighlight(items);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (hlIdx >= 0 && items[hlIdx]) items[hlIdx].click();
        } else if (e.key === 'Escape') {
            wrap.classList.remove('open');
            inp.blur();
        }
    });

    function updateHighlight(items) {
        for (var i = 0; i < items.length; i++) {
            items[i].classList.toggle('highlighted', i === hlIdx);
        }
        if (hlIdx >= 0 && items[hlIdx]) items[hlIdx].scrollIntoView({block:'nearest'});
    }

    function filterAndShow(q) {
        q = q.toLowerCase().trim();
        var filtered = accList.filter(function(a) {
            return !q || a.name.toLowerCase().indexOf(q) !== -1;
        });
        drop.innerHTML = '';
        if (filtered.length === 0) {
            drop.innerHTML = '<div class="acc-picker-empty">No accounts found</div>';
            return;
        }
        filtered.forEach(function(a) {
            var div = document.createElement('div');
            div.className = 'acc-picker-item';
            div.textContent = a.name;
            div.setAttribute('data-id', a.id);
            div.setAttribute('data-vat', a.vat);
            div.addEventListener('mousedown', function(ev) {
                ev.preventDefault();
                selectAccount(txnId, a.id, a.name, a.vat);
            });
            drop.appendChild(div);
        });
        hlIdx = -1;
    }
}

function selectAccount(txnId, accId, accName, vatType) {
    var inp = document.getElementById('accinp-' + txnId);
    var hidden = document.getElementById('accval-' + txnId);
    var wrap = document.getElementById('picker-' + txnId);

    hidden.value = accId;
    inp.value = accName;
    inp.classList.add('has-value');
    wrap.classList.add('filled');
    wrap.classList.remove('open');

    if (vatType) {
        document.getElementById('vat-' + txnId).value = vatType;
    }
}

function clearPicker(txnId) {
    var inp = document.getElementById('accinp-' + txnId);
    var hidden = document.getElementById('accval-' + txnId);
    var wrap = document.getElementById('picker-' + txnId);
    hidden.value = '';
    inp.value = '';
    inp.classList.remove('has-value');
    wrap.classList.remove('filled');
    inp.focus();
}

function applySuggestion(txnId, accountId, vatType, accountName) {
    selectAccount(txnId, accountId, accountName, vatType);
}

function postAllocated() {
    NxAlert.confirm('Post to General Ledger', 'Post all allocated transactions to the General Ledger?<br>This will create journal entries.', 'YES, POST').then(function(result) {
        if (result.isConfirmed) document.getElementById('postForm').submit();
    });
}

function excludeTxn(txnId) {
    NxAlert._fire('warning', 'Exclude Transaction', 'Exclude this transaction?<br><span style="font-size:12px;color:#94a3b8;">It will be hidden from allocation.</span>', {
        showCancel: true, confirmText: 'YES, EXCLUDE', cancelText: 'CANCEL'
    }).then(function(result) {
        if (!result.isConfirmed) return;
        fetch('{{ route("nexcore.clients.show.accounting.bank.allocate.exclude", [$client->id, $bankAccount->id]) }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ ids: [txnId] })
        }).then(function(r) { return r.json(); }).then(function(d) {
            if (d.success) {
                var row = document.getElementById('row-' + txnId);
                if (row) row.style.display = 'none';
            }
        });
    });
}

document.addEventListener('click', function(e) {
    var pickers = document.querySelectorAll('.acc-picker');
    pickers.forEach(function(p) {
        if (!p.contains(e.target)) p.classList.remove('open');
    });
});

@foreach($transactions as $txn)
initPicker({{ $txn->id }});
@endforeach

document.querySelectorAll('.acc-picker').forEach(function(p) {
    if (p.querySelector('input[type="hidden"]').value) p.classList.add('filled');
});

function openQuickAdd() {
    document.getElementById('qaOverlay').classList.add('show');
    document.getElementById('qaError').style.display = 'none';
    document.getElementById('qaParent').value = '';
    document.getElementById('qaCode').value = '';
    document.getElementById('qaPreview').value = '';
    document.getElementById('qaName').value = '';
    document.getElementById('qaType').value = 'expense';
    document.getElementById('qaBal').value = 'debit';
    document.getElementById('qaVat').value = 'none';
}

function closeQuickAdd() {
    document.getElementById('qaOverlay').classList.remove('show');
}

function onParentChange() {
    var sel = document.getElementById('qaParent');
    var opt = sel.options[sel.selectedIndex];
    if (opt.value) {
        document.getElementById('qaType').value = opt.dataset.type || 'expense';
        document.getElementById('qaBal').value = opt.dataset.bal || 'debit';
        updatePreview();
    } else {
        document.getElementById('qaPreview').value = '';
    }
}

document.getElementById('qaCode').addEventListener('input', updatePreview);

function updatePreview() {
    var sel = document.getElementById('qaParent');
    var opt = sel.options[sel.selectedIndex];
    var code = document.getElementById('qaCode').value;
    if (opt.value && code) {
        document.getElementById('qaPreview').value = opt.dataset.seg1 + '/' + opt.dataset.seg2 + '/' + code;
    } else {
        document.getElementById('qaPreview').value = '';
    }
}

function saveQuickAdd() {
    var errEl = document.getElementById('qaError');
    var parentId = document.getElementById('qaParent').value;
    var code = document.getElementById('qaCode').value.trim();
    var name = document.getElementById('qaName').value.trim();
    if (!parentId || !code || !name) {
        errEl.textContent = 'Please fill in Parent, Code and Name.';
        errEl.style.display = 'block';
        return;
    }
    if (code.length !== 4 || !/^\d{4}$/.test(code)) {
        errEl.textContent = 'Code must be exactly 4 digits.';
        errEl.style.display = 'block';
        return;
    }
    errEl.style.display = 'none';
    var btn = document.getElementById('qaSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    fetch('{{ route("nexcore.clients.show.accounting.bank.chart-quick-add", $client->id) }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({
            parent_id: parentId,
            segment3: code,
            account_name: name,
            account_type: document.getElementById('qaType').value,
            normal_balance: document.getElementById('qaBal').value,
            vat_type: document.getElementById('qaVat').value
        })
    }).then(function(r) { return r.json(); }).then(function(d) {
        if (d.success) {
            window.location.reload();
        } else {
            errEl.textContent = d.error || 'Failed to save.';
            errEl.style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Save & Reload';
        }
    }).catch(function() {
        errEl.textContent = 'Network error. Please try again.';
        errEl.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Save & Reload';
    });
}

document.getElementById('qaOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeQuickAdd();
});

function applyRules() {
    var btn = document.getElementById('applyRulesBtn');
    NxAlert.confirm('Apply Allocation Rules', 'Scan all unallocated transactions and auto-allocate using saved rules?', 'YES, APPLY').then(function(result) {
        if (!result.isConfirmed) return;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Applying...';
        fetch('{{ route("nexcore.clients.show.accounting.bank.allocate.auto", [$client->id, $bankAccount->id]) }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' }
        }).then(function(r) { return r.json(); }).then(function(d) {
            if (d.success) {
                NxAlert.success('Rules Applied', d.count + ' transaction' + (d.count !== 1 ? 's' : '') + ' allocated.').then(function() { window.location.reload(); });
            } else {
                NxAlert.error('Error', d.error || 'Failed to apply rules.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-magic"></i> Apply Rules';
            }
        }).catch(function() {
            NxAlert.error('Error', 'Network error. Please try again.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-magic"></i> Apply Rules';
        });
    });
}
</script>
@endpush
