@extends('nexcore_client_manager::layouts.accounting')

@section('title', 'Statement Register - ' . $client->company_name)
@section('page_heading', 'STATEMENT REGISTER')

@push('styles')
<style>
/* ═══ STATEMENT CARDS ═══ */
.sr-card-list { display:flex; flex-direction:column; gap:14px; padding:18px; }

.sr-stmt-card {
    background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12);
    border-radius:16px; padding:22px 26px; transition:all 0.3s cubic-bezier(0.4,0,0.2,1);
}
.sr-stmt-card:hover { border-color:rgba(245,158,11,0.4); background:rgba(255,255,255,0.08); box-shadow:0 4px 24px rgba(0,0,0,0.3), 0 0 0 1px rgba(245,158,11,0.15); }
.sr-stmt-card.card-alt { background:rgba(59,130,246,0.06); border-color:rgba(59,130,246,0.18); }
.sr-stmt-card.card-alt:hover { background:rgba(59,130,246,0.1); border-color:rgba(59,130,246,0.3); box-shadow:0 4px 24px rgba(0,0,0,0.3), 0 0 0 1px rgba(59,130,246,0.2); }
.sr-stmt-card.is-posted { border-left:4px solid #22c55e; }
.sr-stmt-card.is-reconciled { border-left:4px solid var(--accent-blue,#3b82f6); }

/* Row 1: Number, Period, Txn count, Status, Actions */
.sr-row1 { display:flex; align-items:center; gap:16px; margin-bottom:14px; flex-wrap:wrap; }

.sr-num {
    font-family:var(--font-mono,monospace); font-size:18px; font-weight:900; color:var(--text-secondary,#94a3b8);
    width:40px; height:40px; display:flex; align-items:center; justify-content:center; flex-shrink:0;
    background:rgba(255,255,255,0.08); border-radius:10px; border:1px solid rgba(255,255,255,0.12);
}

.sr-period {
    display:inline-flex; align-items:center; gap:8px;
    font-family:var(--font-mono,monospace); font-size:14px; font-weight:700;
    color:#fbbf24; background:rgba(245,158,11,0.15); padding:8px 16px; border-radius:10px;
    border:1px solid rgba(245,158,11,0.3);
}
.sr-period-to { font-size:12px; color:var(--text-muted,#64748b); font-weight:600; font-family:inherit; }

.sr-txn-count {
    display:inline-flex; align-items:center; gap:6px;
    font-family:var(--font-mono,monospace); font-size:16px; font-weight:800; color:#93c5fd;
    background:rgba(59,130,246,0.15); padding:6px 14px; border-radius:8px;
    border:1px solid rgba(59,130,246,0.3);
}
.sr-txn-split { font-size:12px; font-weight:600; color:var(--text-muted,#64748b); font-family:inherit; }
.sr-txn-split .c { color:#6ee7b7; }
.sr-txn-split .d { color:#fca5a5; }

.sr-status {
    font-size:11px; font-weight:800; padding:6px 14px; border-radius:8px;
    letter-spacing:0.5px; text-transform:uppercase;
    display:inline-flex; align-items:center; gap:5px;
}
.sr-status-imported { color:#fbbf24; background:rgba(245,158,11,0.15); border:1px solid rgba(245,158,11,0.3); }
.sr-status-posted { color:#6ee7b7; background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.3); }
.sr-status-reconciled { color:#93c5fd; background:rgba(59,130,246,0.15); border:1px solid rgba(59,130,246,0.3); }

.sr-actions { display:flex; align-items:center; gap:8px; }
.sr-action-btn {
    font-size:12px; font-weight:700; padding:6px 14px; border-radius:8px; cursor:pointer;
    display:inline-flex; align-items:center; gap:5px; transition:all 0.2s; text-decoration:none; border:1px solid;
    background:none;
}
.sr-action-view { color:#93c5fd; border-color:rgba(59,130,246,0.3); }
.sr-action-view:hover { background:rgba(59,130,246,0.15); border-color:rgba(59,130,246,0.5); color:#93c5fd; text-decoration:none; }
.sr-action-delete { color:#fca5a5; border-color:rgba(239,68,68,0.3); }
.sr-action-delete:hover { background:rgba(239,68,68,0.15); border-color:rgba(239,68,68,0.5); }

/* Row 2: Credits + Debits amounts */
.sr-row2 { display:flex; align-items:center; gap:16px; margin-bottom:14px; flex-wrap:wrap; }

.sr-amount-block {
    display:inline-flex; align-items:center; gap:8px;
    padding:8px 20px; border-radius:10px;
    font-family:var(--font-mono,monospace); font-size:18px; font-weight:900;
    white-space:nowrap; font-variant-numeric:tabular-nums;
}
.sr-amount-credit { color:#6ee7b7; background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.3); }
.sr-amount-debit { color:#fca5a5; background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); }
.sr-amount-label {
    font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.5px;
    padding:5px 12px; border-radius:6px;
}
.sr-amount-label-credit { color:#6ee7b7; background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.3); }
.sr-amount-label-debit { color:#fca5a5; background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); }

/* Row 3: Meta info */
.sr-row3 {
    padding-top:14px; border-top:1px solid rgba(255,255,255,0.1);
    display:flex; flex-wrap:wrap; gap:8px 22px; font-size:13px; color:var(--text-muted,#64748b);
}
.sr-meta-item { display:inline-flex; align-items:center; gap:6px; }
.sr-meta-icon { font-size:10px; color:rgba(255,255,255,0.2); }
.sr-meta-val { font-family:var(--font-mono,monospace); color:var(--text-secondary,#94a3b8); font-weight:600; }
.sr-meta-val-amber { color:#fbbf24; }

/* ═══ TOTALS CARD ═══ */
.sr-totals {
    background:rgba(245,158,11,0.06); border:2px solid rgba(245,158,11,0.2);
    border-radius:16px; padding:18px 26px; margin:0 18px 18px;
    display:flex; align-items:center; gap:20px; flex-wrap:wrap;
}
.sr-totals-label {
    font-size:14px; font-weight:800; color:var(--text-secondary,#94a3b8);
    text-transform:uppercase; letter-spacing:0.5px;
}

@@media (max-width:1200px) {
    .sr-stmt-card { padding:16px 18px; }
    .sr-row1, .sr-row2 { gap:10px; }
}
@@media (max-width:768px) {
    .sr-row2 { flex-direction:column; }
    .sr-amount-block { font-size:15px; padding:6px 14px; }
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
                <h1 class="sl-page-title" style="margin:0;">Statement Register | {{ $client->company_name }}</h1>
                <span class="sl-page-subtitle">{{ $bankAccount->bank_name }} - {{ $bankAccount->account_number }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; align-items:center; gap:12px;">
            <a href="{{ route('nexcore.clients.show.accounting.bank.accounts', $client->id) }}" style="font-size:13px; color:var(--text-muted); text-decoration:none; font-weight:600;">
                <i class="fas fa-arrow-left"></i> Back to Bank Accounts
            </a>
            <a href="{{ route('nexcore.clients.show.accounting.bank.import', [$client->id, $bankAccount->id]) }}" class="neon-btn neon-btn-amber" style="display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-file-import"></i> Import Statement
            </a>
        </div>
    </div>
</div>

{{-- Summary Cards --}}
<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card" style="border-color:rgba(245,158,11,0.4);">
        <div class="sl-stat-label">Total Statements</div>
        <div class="sl-stat-value" style="color:#f59e0b;">{{ $statements->count() }}</div>
        <div class="sl-stat-meta">Imported statement batches</div>
    </div>
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">Total Transactions</div>
        <div class="sl-stat-value" style="color:var(--accent-blue);">{{ number_format($statements->sum('transaction_count'), 0, '.', ' ') }}</div>
        <div class="sl-stat-meta">All imported transactions</div>
    </div>
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Total Credits</div>
        <div class="sl-stat-value" style="color:var(--accent-green); font-size:20px;">R {{ number_format($statements->sum('total_credits'), 2, '.', ' ') }}</div>
        <div class="sl-stat-meta">{{ number_format($statements->sum('credit_count'), 0, '.', ' ') }} credit transactions</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(239,68,68,0.4);">
        <div class="sl-stat-label">Total Debits</div>
        <div class="sl-stat-value" style="color:var(--accent-red); font-size:20px;">R {{ number_format($statements->sum('total_debits'), 2, '.', ' ') }}</div>
        <div class="sl-stat-meta">{{ number_format($statements->sum('debit_count'), 0, '.', ' ') }} debit transactions</div>
    </div>
</div>

@if(session('success'))
<div class="sl-animate d2" style="padding:12px 20px; background:rgba(16,185,129,0.12); border:1px solid rgba(16,185,129,0.3); border-radius:10px; color:#6ee7b7; font-size:14px; font-weight:600; margin-bottom:20px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

{{-- Statement Cards --}}
@if($statements->count() > 0)
<div class="sl-card sl-animate d3">
    <div class="sl-card-header" style="display:flex; align-items:center; justify-content:space-between;">
        <div class="sl-card-title" style="color:#f59e0b;"><i class="fas fa-file-alt"></i> Imported Statements</div>
        <span style="font-size:12px; color:var(--text-muted); font-family:var(--font-mono);">{{ $statements->count() }} {{ \Illuminate\Support\Str::plural('statement', $statements->count()) }}</span>
    </div>

    <div class="sr-card-list">
        @foreach($statements as $idx => $stmt)
        <div class="sr-stmt-card {{ $stmt->status === 'posted' ? 'is-posted' : ($stmt->status === 'reconciled' ? 'is-reconciled' : '') }} {{ $idx % 2 === 1 ? 'card-alt' : '' }}">
            <div class="sr-row1">
                <span class="sr-num">{{ $idx + 1 }}</span>
                @if($stmt->statement_ref)
                <span style="font-family:var(--font-mono,monospace); font-size:14px; font-weight:800; color:var(--accent-cyan,#22d3ee); background:rgba(6,182,212,0.15); padding:8px 16px; border-radius:10px; border:1px solid rgba(6,182,212,0.3); letter-spacing:0.5px;">{{ $stmt->statement_ref }}</span>
                @endif
                <span class="sr-period">
                    {{ \Carbon\Carbon::parse($stmt->period_from)->format('d M Y') }}
                    <span class="sr-period-to">to</span>
                    {{ \Carbon\Carbon::parse($stmt->period_to)->format('d M Y') }}
                </span>
                <span class="sr-txn-count">
                    {{ $stmt->transaction_count }}
                    <span class="sr-txn-split">(<span class="c">{{ $stmt->credit_count }}C</span> / <span class="d">{{ $stmt->debit_count }}D</span>)</span>
                </span>
                @if($stmt->status === 'reconciled')
                    <span class="sr-status sr-status-reconciled"><i class="fas fa-check-double"></i> Reconciled</span>
                @elseif($stmt->status === 'posted')
                    <span class="sr-status sr-status-posted"><i class="fas fa-check"></i> Posted</span>
                @else
                    <span class="sr-status sr-status-imported"><i class="fas fa-file-import"></i> Imported</span>
                @endif
                <div style="flex:1;"></div>
                <div class="sr-actions">
                    <a href="{{ route('nexcore.clients.show.accounting.bank.statements.view', [$client->id, $bankAccount->id, $stmt->id]) }}" class="sr-action-btn sr-action-view">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <form method="POST" action="{{ route('nexcore.clients.show.accounting.bank.statements.destroy', [$client->id, $bankAccount->id, $stmt->id]) }}" onsubmit="return ncDeleteStatement(event, this, {{ $stmt->transaction_count }});" style="margin:0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="sr-action-btn sr-action-delete">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
            <div class="sr-row2">
                <span class="sr-amount-label sr-amount-label-credit">Credits</span>
                <span class="sr-amount-block sr-amount-credit">R {{ number_format($stmt->total_credits, 2, '.', ' ') }}</span>
                <span class="sr-amount-label sr-amount-label-debit">Debits</span>
                <span class="sr-amount-block sr-amount-debit">R {{ number_format($stmt->total_debits, 2, '.', ' ') }}</span>
            </div>
            <div class="sr-row3">
                <span class="sr-meta-item"><i class="fas fa-arrow-right sr-meta-icon"></i> Opening: <span class="sr-meta-val">R {{ number_format($stmt->opening_balance, 2, '.', ' ') }}</span></span>
                <span class="sr-meta-item"><i class="fas fa-arrow-left sr-meta-icon"></i> Closing: <span class="sr-meta-val">R {{ number_format($stmt->closing_balance, 2, '.', ' ') }}</span></span>
                <span class="sr-meta-item"><i class="fas fa-hashtag sr-meta-icon"></i> Batch: <span class="sr-meta-val sr-meta-val-amber">{{ $stmt->batch_ref }}</span></span>
                <span class="sr-meta-item"><i class="fas fa-clock sr-meta-icon"></i> {{ $stmt->created_at->format('d M Y H:i') }}</span>
                @if($stmt->original_filename)
                <span class="sr-meta-item"><i class="fas fa-paperclip sr-meta-icon"></i> {{ $stmt->original_filename }}</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="sr-totals">
        <span class="sr-totals-label">Totals</span>
        <span class="sr-txn-count">{{ number_format($statements->sum('transaction_count'), 0, '.', ' ') }}</span>
        <span class="sr-amount-label sr-amount-label-credit">Credits</span>
        <span class="sr-amount-block sr-amount-credit">R {{ number_format($statements->sum('total_credits'), 2, '.', ' ') }}</span>
        <span class="sr-amount-label sr-amount-label-debit">Debits</span>
        <span class="sr-amount-block sr-amount-debit">R {{ number_format($statements->sum('total_debits'), 2, '.', ' ') }}</span>
    </div>
</div>
@else
<div class="sl-card sl-animate d3">
    <div style="text-align:center; padding:80px 40px; color:var(--text-muted);">
        <i class="fas fa-file-alt" style="font-size:48px; opacity:0.15; margin-bottom:20px; display:block;"></i>
        <div style="font-size:18px; font-weight:700; margin-bottom:8px; color:var(--text-secondary);">No Statements Imported</div>
        <div style="font-size:14px; max-width:400px; margin:0 auto; line-height:1.6;">
            Import your first bank statement to start tracking transactions for this account. Upload a PDF statement and the system will automatically extract all transactions.
        </div>
        <a href="{{ route('nexcore.clients.show.accounting.bank.import', [$client->id, $bankAccount->id]) }}" class="neon-btn neon-btn-amber" style="margin-top:24px; display:inline-flex; align-items:center; gap:8px;">
            <i class="fas fa-file-import"></i> Import Statement
        </a>
    </div>
</div>
@endif
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
        return Swal.fire({
            imageUrl: this._logo, imageWidth: 56, imageHeight: 56,
            title: title, html: message,
            confirmButtonText: (opts && opts.confirmText) || 'OK',
            confirmButtonColor: c.btn,
            background: '#ffffff',
            showCancelButton: !!(opts && opts.showCancel),
            cancelButtonText: (opts && opts.cancelText) || 'CANCEL',
            customClass: { popup: 'nx-swal-popup ' + c.cls, title: 'nx-swal-title', htmlContainer: 'nx-swal-html', actions: 'nx-swal-actions' }
        });
    },
    warning: function(title, message) { return this._fire('warning', title, message); }
};

function ncDeleteStatement(e, form, txnCount) {
    e.preventDefault();
    NxAlert._fire('warning', 'Delete Statement', 'Are you sure you want to delete this statement and all <strong style="color:#e11d48;">' + txnCount + ' linked transactions</strong>?<br><span style="font-size:12px;color:#94a3b8;">This action cannot be undone.</span>', {
        showCancel: true, confirmText: 'YES, DELETE', cancelText: 'CANCEL'
    }).then(function(result) {
        if (result.isConfirmed) form.submit();
    });
    return false;
}
</script>
@endpush
