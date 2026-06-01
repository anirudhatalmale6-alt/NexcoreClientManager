@extends('nexcore_client_manager::layouts.accounting')

@section('title', 'Bank Accounts - ' . $client->company_name)
@section('page_heading', 'BANK ACCOUNTS')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(245,158,11,0.15), rgba(245,158,11,0.05)); border:1px solid rgba(245,158,11,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-university" style="color:#f59e0b; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Bank Accounts</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.show.accounting.bank.accounts.create', $client->id) }}" class="neon-btn neon-btn-amber" style="display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-plus"></i> Link Bank Account
            </a>
        </div>
    </div>
</div>

{{-- Summary Cards --}}
<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card" style="border-color:rgba(245,158,11,0.4);">
        <div class="sl-stat-label">Linked Accounts</div>
        <div class="sl-stat-value" style="color:#f59e0b;">{{ $bankAccounts->count() }}</div>
    </div>
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">Total Transactions</div>
        <div class="sl-stat-value" style="color:var(--accent-blue);">{{ $bankAccounts->sum('total_transactions') }}</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(239,68,68,0.4);">
        <div class="sl-stat-label">Unallocated</div>
        <div class="sl-stat-value" style="color:var(--accent-red);">{{ $bankAccounts->sum('unallocated_count') }}</div>
    </div>
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Posted</div>
        <div class="sl-stat-value" style="color:var(--accent-green);">{{ $bankAccounts->sum('posted_count') }}</div>
    </div>
</div>

@if(session('success'))
<div class="sl-animate d2" style="padding:12px 20px; background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.25); border-radius:10px; color:var(--accent-green); font-size:14px; font-weight:600; margin-bottom:20px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

@if($bankAccounts->count() > 0)
<div class="ba-list sl-animate d3">
    @foreach($bankAccounts as $ba)
    @php
        $baLogo = ($ba->systemBank && $ba->systemBank->bank_logo) ? $ba->systemBank->bank_logo : null;
    @endphp
    <div class="ba-card {{ !$ba->is_active ? 'ba-inactive' : '' }}">
        {{-- LEFT: Bank Logo + Info --}}
        <div class="ba-card-main">
            <div class="ba-logo">
                @if($baLogo)
                    <img src="/{{ $baLogo }}" alt="{{ $ba->bank_name }}" class="ba-logo-img">
                @else
                    <span class="ba-logo-letter">{{ strtoupper(substr($ba->bank_name, 0, 1)) }}</span>
                @endif
            </div>
            <div class="ba-info">
                <div class="ba-info-header">
                    <h3 class="ba-bank-name">{{ $ba->bank_name }}</h3>
                    @if(!$ba->is_active)
                    <span class="ba-badge ba-badge-red">INACTIVE</span>
                    @endif
                </div>
                <div class="ba-account-num">{{ $ba->account_number }}</div>
                <div class="ba-meta-row">
                    <span class="ba-meta"><i class="fas fa-building"></i> {{ ucfirst($ba->account_type) }}</span>
                    @if($ba->branch_code)
                    <span class="ba-meta"><i class="fas fa-code-branch"></i> {{ $ba->branch_code }}</span>
                    @endif
                    @if($ba->glAccount)
                    <span class="ba-meta ba-meta-gl"><i class="fas fa-link"></i> {{ $ba->glAccount->account_code }} - {{ $ba->glAccount->account_name }}</span>
                    @endif
                </div>
                @if($ba->opening_balance_amount && floatval($ba->opening_balance_amount) != 0)
                <div class="ba-ob">
                    <i class="fas fa-balance-scale"></i>
                    OB: R {{ number_format(abs($ba->opening_balance_amount), 2, '.', ' ') }}{{ floatval($ba->opening_balance_amount) < 0 ? ' (OD)' : '' }}
                    @if($ba->opening_balance_date)
                        <span class="ba-ob-date">@ {{ $ba->opening_balance_date->format('j M Y') }}</span>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- RIGHT: Stats + Actions --}}
        <div class="ba-card-right">
            <div class="ba-stats-grid">
                <div class="ba-stat">
                    <div class="ba-stat-val" style="color:var(--accent-blue);">{{ $ba->total_transactions }}</div>
                    <div class="ba-stat-lbl">TRANSACTIONS</div>
                </div>
                <div class="ba-stat">
                    <div class="ba-stat-val" style="color:{{ $ba->unallocated_count > 0 ? 'var(--accent-red)' : 'var(--text-muted)' }};">{{ $ba->unallocated_count }}</div>
                    <div class="ba-stat-lbl">UNALLOCATED</div>
                </div>
                <div class="ba-stat">
                    <div class="ba-stat-val" style="color:var(--accent-green);">{{ $ba->posted_count }}</div>
                    <div class="ba-stat-lbl">POSTED</div>
                </div>
                <div class="ba-stat">
                    <div class="ba-stat-val" style="color:var(--accent-cyan);">{{ $ba->statements->count() }}</div>
                    <div class="ba-stat-lbl">STATEMENTS</div>
                </div>
            </div>
            <div class="ba-actions-row">
                <a href="{{ route('nexcore.clients.show.accounting.bank.statements', [$client->id, $ba->id]) }}" class="ba-action-btn ba-action-cyan" title="Statements"><i class="fas fa-file-alt"></i> Statements</a>
                <a href="{{ route('nexcore.clients.show.accounting.bank.import', [$client->id, $ba->id]) }}" class="ba-action-btn ba-action-amber" title="Import"><i class="fas fa-file-import"></i> Import</a>
                <a href="{{ route('nexcore.clients.show.accounting.bank.allocate', [$client->id, $ba->id]) }}" class="ba-action-btn ba-action-green" title="Allocate"><i class="fas fa-tags"></i> Allocate</a>
            </div>
            <div class="ba-tool-row">
                <a href="{{ route('nexcore.clients.show.accounting.bank.accounts.edit', [$client->id, $ba->id]) }}" class="ba-tool-btn ba-tool-blue" title="Edit"><i class="fas fa-pen"></i></a>
                <form method="POST" action="{{ route('nexcore.clients.show.accounting.bank.accounts.toggle', [$client->id, $ba->id]) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="ba-tool-btn {{ $ba->is_active ? 'ba-tool-amber' : 'ba-tool-green' }}" title="{{ $ba->is_active ? 'Deactivate' : 'Activate' }}">
                        <i class="fas fa-{{ $ba->is_active ? 'ban' : 'check' }}"></i>
                    </button>
                </form>
                <button type="button" class="ba-tool-btn ba-tool-red" title="Delete" onclick="confirmDeleteBank({{ $ba->id }}, '{{ addslashes($ba->bank_name) }}', {{ $ba->total_transactions }}, {{ $ba->statements->count() }})">
                    <i class="fas fa-trash-alt"></i>
                </button>
                <form id="deleteForm{{ $ba->id }}" method="POST" action="{{ route('nexcore.clients.show.accounting.bank.accounts.destroy', [$client->id, $ba->id]) }}" style="display:none;">
                    @csrf @method('DELETE')
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="sl-card sl-animate d3">
    <div style="text-align:center; padding:80px 40px; color:var(--text-muted);">
        <i class="fas fa-university" style="font-size:48px; opacity:0.15; margin-bottom:20px; display:block;"></i>
        <div style="font-size:18px; font-weight:700; margin-bottom:8px; color:var(--text-secondary);">No Bank Accounts Linked</div>
        <div style="font-size:14px; max-width:400px; margin:0 auto; line-height:1.6;">
            Link a bank account to a GL asset account to start importing statements.
        </div>
        <a href="{{ route('nexcore.clients.show.accounting.bank.accounts.create', $client->id) }}" class="neon-btn neon-btn-amber" style="margin-top:24px; display:inline-flex;"><i class="fas fa-plus"></i> Link Bank Account</a>
    </div>
</div>
@endif
@endsection

@push('scripts')
<style>
.ba-list { display:flex; flex-direction:column; gap:20px; }

.ba-card {
    background:rgba(255,255,255,0.04);
    border:1px solid rgba(255,255,255,0.1);
    border-radius:20px;
    padding:28px;
    transition:all 0.3s cubic-bezier(0.4,0,0.2,1);
    display:flex;
    gap:28px;
    align-items:stretch;
}
.ba-card:hover {
    border-color:rgba(255,255,255,0.2);
    background:rgba(255,255,255,0.06);
    box-shadow:0 8px 40px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,255,255,0.05);
    transform:translateY(-2px);
}
.ba-inactive { opacity:0.45; }

.ba-card-main { display:flex; gap:22px; flex:1; min-width:0; }

.ba-logo {
    width:72px; height:72px; border-radius:16px; flex-shrink:0;
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    box-shadow:0 4px 20px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.15);
    position:relative; overflow:hidden;
}
.ba-logo::after {
    content:''; position:absolute; top:0; left:0; right:0; bottom:0;
    background:linear-gradient(180deg, rgba(255,255,255,0.15) 0%, transparent 50%);
    border-radius:16px;
}
.ba-logo-letter {
    font-family:'Montserrat',sans-serif; font-size:28px; font-weight:900; color:#fff;
    line-height:1; position:relative; z-index:1; text-shadow:0 2px 4px rgba(0,0,0,0.3);
}
.ba-logo-label {
    font-family:'Montserrat',sans-serif; font-size:9px; font-weight:800; color:rgba(255,255,255,0.8);
    letter-spacing:2px; text-transform:uppercase; position:relative; z-index:1; margin-top:2px;
}
.ba-logo-img {
    width:100%; height:100%; object-fit:contain; border-radius:14px; position:relative; z-index:1;
    padding:6px; background:#fff;
}

.ba-info { flex:1; min-width:0; display:flex; flex-direction:column; justify-content:center; }

.ba-info-header { display:flex; align-items:center; gap:10px; margin-bottom:4px; }
.ba-bank-name { font-size:18px; font-weight:800; color:var(--text-primary); margin:0; letter-spacing:0.2px; }
.ba-badge { font-size:10px; padding:3px 8px; border-radius:6px; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; }
.ba-badge-red { background:rgba(239,68,68,0.15); color:#fca5a5; border:1px solid rgba(239,68,68,0.3); }

.ba-account-num {
    font-family:var(--font-mono,monospace); font-size:16px; font-weight:700; color:#f59e0b;
    margin-bottom:8px; letter-spacing:1px;
}

.ba-meta-row { display:flex; align-items:center; gap:16px; flex-wrap:wrap; margin-bottom:4px; }
.ba-meta {
    font-size:12px; color:var(--text-muted); display:inline-flex; align-items:center; gap:5px;
}
.ba-meta i { font-size:10px; opacity:0.7; }
.ba-meta-gl { color:var(--accent-blue,#3b82f6); }

.ba-ob {
    font-size:12px; color:var(--accent-cyan,#22d3ee); display:inline-flex; align-items:center; gap:5px;
    margin-top:4px; padding:4px 10px; background:rgba(34,211,238,0.08); border:1px solid rgba(34,211,238,0.2);
    border-radius:6px; width:fit-content;
}
.ba-ob i { font-size:10px; }
.ba-ob-date { color:var(--text-muted); }

.ba-card-right {
    display:flex; flex-direction:column; align-items:flex-end; justify-content:space-between; gap:12px;
    flex-shrink:0; min-width:260px;
}

.ba-stats-grid { display:grid; grid-template-columns:repeat(4, 1fr); gap:0; text-align:center; width:100%; }
.ba-stat { padding:6px 8px; }
.ba-stat-val { font-family:var(--font-mono,monospace); font-size:20px; font-weight:900; line-height:1.2; }
.ba-stat-lbl { font-size:9px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.8px; margin-top:2px; }

.ba-actions-row { display:flex; gap:8px; width:100%; justify-content:flex-end; }
.ba-action-btn {
    padding:8px 16px; border-radius:10px; font-size:12px; font-weight:700;
    text-decoration:none; display:inline-flex; align-items:center; gap:6px;
    transition:all 0.2s ease; letter-spacing:0.3px;
}
.ba-action-cyan { background:rgba(34,211,238,0.1); color:#22d3ee; border:1px solid rgba(34,211,238,0.25); }
.ba-action-cyan:hover { background:rgba(34,211,238,0.2); box-shadow:0 0 16px rgba(34,211,238,0.15); }
.ba-action-amber { background:rgba(245,158,11,0.1); color:#f59e0b; border:1px solid rgba(245,158,11,0.25); }
.ba-action-amber:hover { background:rgba(245,158,11,0.2); box-shadow:0 0 16px rgba(245,158,11,0.15); }
.ba-action-green { background:rgba(16,185,129,0.1); color:#10b981; border:1px solid rgba(16,185,129,0.25); }
.ba-action-green:hover { background:rgba(16,185,129,0.2); box-shadow:0 0 16px rgba(16,185,129,0.15); }

.ba-tool-row { display:flex; gap:6px; justify-content:flex-end; }
.ba-tool-btn {
    width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center;
    border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.04); cursor:pointer;
    font-size:13px; transition:all 0.2s ease; text-decoration:none;
}
.ba-tool-blue { color:var(--accent-blue,#3b82f6); }
.ba-tool-blue:hover { background:rgba(59,130,246,0.15); border-color:rgba(59,130,246,0.4); }
.ba-tool-amber { color:#f59e0b; }
.ba-tool-amber:hover { background:rgba(245,158,11,0.15); border-color:rgba(245,158,11,0.4); }
.ba-tool-green { color:var(--accent-green,#10b981); }
.ba-tool-green:hover { background:rgba(16,185,129,0.15); border-color:rgba(16,185,129,0.4); }
.ba-tool-red { color:var(--accent-red,#ef4444); }
.ba-tool-red:hover { background:rgba(239,68,68,0.15); border-color:rgba(239,68,68,0.4); }

.nx-swal-popup { border-radius:16px !important; }
.nx-swal-popup .swal2-image { margin:0 auto 12px !important; }
.nx-swal-title { font-family:'Montserrat',sans-serif !important; font-weight:800 !important; font-size:18px !important; color:#0f172a !important; }
.nx-swal-html { font-size:14px !important; color:#475569 !important; line-height:1.6 !important; }
.nx-swal-actions .swal2-confirm, .nx-swal-actions .swal2-cancel { font-family:'Montserrat',sans-serif !important; font-weight:700 !important; font-size:13px !important; letter-spacing:1px !important; text-transform:uppercase !important; border-radius:8px !important; padding:12px 28px !important; }
.swal2-popup .swal2-icon { display:none !important; }

@@media (max-width:900px) {
    .ba-card { flex-direction:column; }
    .ba-card-right { align-items:stretch; min-width:0; }
    .ba-actions-row { justify-content:stretch; }
    .ba-action-btn { flex:1; justify-content:center; }
    .ba-tool-row { justify-content:center; }
}
@@media (max-width:600px) {
    .ba-card { padding:20px; }
    .ba-card-main { flex-direction:column; align-items:center; text-align:center; }
    .ba-meta-row { justify-content:center; }
    .ba-ob { margin:4px auto 0; }
    .ba-stats-grid { grid-template-columns:repeat(2, 1fr); }
}
</style>
<script>
var NxAlert = {
    _logo: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB4PSIyIiB5PSIyIiB3aWR0aD0iMjgiIGhlaWdodD0iMjgiIHJ4PSI2IiBmaWxsPSIjMDU5NjY5Ii8+PHJlY3QgeD0iMzQiIHk9IjIiIHdpZHRoPSIyOCIgaGVpZ2h0PSIyOCIgcng9IjYiIGZpbGw9IiMyNTYzZWIiLz48cmVjdCB4PSIyIiB5PSIzNCIgd2lkdGg9IjI4IiBoZWlnaHQ9IjI4IiByeD0iNiIgZmlsbD0iI2Q5NzcwNiIvPjxyZWN0IHg9IjM0IiB5PSIzNCIgd2lkdGg9IjI4IiBoZWlnaHQ9IjI4IiByeD0iNiIgZmlsbD0iIzdjM2FlZCIvPjwvc3ZnPgo=',
    _colors: {
        success: { btn:'#059669', cls:'nx-swal-success' },
        warning: { btn:'#e11d48', cls:'nx-swal-warning' },
        info:    { btn:'#2563eb', cls:'nx-swal-info' },
        confirm: { btn:'#0891b2', cls:'nx-swal-confirm' },
        error:   { btn:'#d97706', cls:'nx-swal-error' }
    },
    _fire: function(type, title, message, opts) {
        var c = this._colors[type] || this._colors.info;
        var config = {
            imageUrl: this._logo, imageWidth:56, imageHeight:56,
            title: title, html: message,
            confirmButtonText: (opts && opts.confirmText) || 'OK',
            confirmButtonColor: c.btn,
            background: '#ffffff',
            showCancelButton: !!(opts && opts.showCancel),
            cancelButtonText: (opts && opts.cancelText) || 'CANCEL',
            customClass: { popup:'nx-swal-popup ' + c.cls, title:'nx-swal-title', htmlContainer:'nx-swal-html', actions:'nx-swal-actions' }
        };
        return Swal.fire(config);
    },
    warning: function(title, message) { return this._fire('warning', title, message); }
};

function confirmDeleteBank(bankId, bankName, txnCount, stmtCount) {
    var msg = 'This will permanently remove <strong>' + bankName + '</strong>.';
    if (txnCount > 0 || stmtCount > 0) {
        msg += '<br><br><span style="color:#e11d48;font-weight:700;">WARNING:</span> This will also delete <strong>' + stmtCount + '</strong> statement(s) and <strong>' + txnCount + '</strong> transaction(s).';
    }
    NxAlert._fire('warning', 'Delete Bank Account?', msg, {
        showCancel: true,
        confirmText: 'YES, DELETE',
        cancelText: 'CANCEL'
    }).then(function(result) {
        if (result.isConfirmed) {
            document.getElementById('deleteForm' + bankId).submit();
        }
    });
}
</script>
@endpush
