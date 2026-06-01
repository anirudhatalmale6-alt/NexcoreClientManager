@extends('nexcore_client_manager::layouts.accounting')

@section('title', 'Journals - ' . $client->company_name)
@section('page_heading', 'JOURNAL ENTRIES')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(245,158,11,0.15), rgba(245,158,11,0.05)); border:1px solid rgba(245,158,11,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-book" style="color:#f59e0b; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Journal Entries</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; gap:8px;">
            <a href="{{ route('nexcore.clients.show.accounting.journals.create', $client->id) }}" class="neon-btn neon-btn-amber neon-pulse"><i class="fas fa-plus"></i> New Journal</a>
        </div>
    </div>
</div>

@php
    $totalCount   = $journals->count();
    $draftCount   = $journals->where('status', 'draft')->count();
    $postedCount  = $journals->where('status', 'posted')->count();
    $reversedCount = $journals->where('status', 'reversed')->count();
    $totalValue   = $journals->where('status', 'posted')->sum('total_debit');
    $batchRefs = $journals->where('source', 'bank_import')->pluck('reference')->unique()->filter()->values()->all();
    $stmtRefMap = \Modules\NexcoreClientManager\Models\NexcoreBankStatement::whereIn('batch_ref', $batchRefs)->pluck('statement_ref', 'batch_ref')->all();
@endphp

<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card" style="border-color:rgba(245,158,11,0.4);">
        <div class="sl-stat-label">Total Journals</div>
        <div class="sl-stat-value" style="color:#f59e0b;">{{ $totalCount }}</div>
    </div>
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Posted</div>
        <div class="sl-stat-value" style="color:var(--accent-green);">{{ $postedCount }}</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(245,158,11,0.4);">
        <div class="sl-stat-label">Draft</div>
        <div class="sl-stat-value" style="color:#f59e0b;">{{ $draftCount }}</div>
    </div>
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Posted Value</div>
        <div class="sl-stat-value" style="color:var(--accent-green); font-size:20px;">R {{ number_format($totalValue, 2, '.', ' ') }}</div>
    </div>
</div>

{{-- Tabs --}}
<div class="sl-card sl-animate d3" style="margin-bottom:0; border-bottom:none; border-radius:var(--radius-md) var(--radius-md) 0 0;">
    <div style="display:flex; gap:0; border-bottom:2px solid var(--border-subtle); padding:0 4px; flex-wrap:wrap;">
        <button class="jnl-tab active" data-filter="all" onclick="filterJournals('all', this)">
            <i class="fas fa-layer-group"></i> All <span class="jnl-tab-count">{{ $totalCount }}</span>
        </button>
        <button class="jnl-tab" data-filter="draft" onclick="filterJournals('draft', this)">
            <i class="fas fa-pencil-alt"></i> Draft <span class="jnl-tab-count">{{ $draftCount }}</span>
        </button>
        <button class="jnl-tab" data-filter="posted" onclick="filterJournals('posted', this)">
            <i class="fas fa-check-circle"></i> Posted <span class="jnl-tab-count">{{ $postedCount }}</span>
        </button>
        <button class="jnl-tab" data-filter="reversed" onclick="filterJournals('reversed', this)">
            <i class="fas fa-undo"></i> Reversed <span class="jnl-tab-count">{{ $reversedCount }}</span>
        </button>
    </div>
</div>

{{-- Journal Cards --}}
<div class="sl-card" style="border-radius:0 0 var(--radius-md) var(--radius-md); margin-top:0;">
    <div class="jnl-list">
        @forelse($journals as $idx => $j)
        <div class="jnl-card {{ $idx % 2 === 1 ? 'jnl-card-alt' : '' }}" data-journal-status="{{ $j->status }}">
            <div class="jnl-row1">
                <span class="jnl-num">{{ $idx + 1 }}</span>
                <span class="jnl-jnlno">
                    @if($j->source === 'bank_import' && isset($stmtRefMap[$j->reference]) && $stmtRefMap[$j->reference])
                        <div class="jnl-stmtref">{{ $stmtRefMap[$j->reference] }}</div>
                    @endif
                    <div>{{ $j->journal_number }}</div>
                </span>
                <span class="jnl-date"><i class="fas fa-calendar-alt"></i> {{ $j->journal_date->format('j M Y') }}</span>
                <span class="jnl-ref">{{ $j->reference ? \Illuminate\Support\Str::limit($j->reference, 30) : '-' }}</span>
                @if($j->status === 'posted')
                    <span class="jnl-status jnl-status-posted"><i class="fas fa-check-circle"></i> Posted</span>
                @elseif($j->status === 'reversed')
                    <span class="jnl-status jnl-status-reversed"><i class="fas fa-undo"></i> Reversed</span>
                @else
                    <span class="jnl-status jnl-status-draft"><i class="fas fa-pencil-alt"></i> Draft</span>
                @endif
                <div style="flex:1;"></div>
                <span class="jnl-amount">R {{ number_format($j->total_debit, 2, '.', ' ') }}</span>
            </div>
            <div class="jnl-row2">
                <span class="jnl-desc"><i class="fas fa-align-left"></i> {{ \Illuminate\Support\Str::limit($j->description, 60) }}</span>
                <span class="jnl-source">{{ ucfirst(str_replace('_', ' ', $j->source ?? 'manual')) }}</span>
                <span class="jnl-lines"><i class="fas fa-list"></i> {{ $j->lines_count ?? $j->lines->count() }} lines</span>
                <div style="flex:1;"></div>
                <div class="jnl-actions">
                    <a href="{{ route('nexcore.clients.show.accounting.journals.edit', [$client->id, $j->id]) }}" class="jnl-action-btn jnl-action-edit" title="Edit"><i class="fas fa-pen"></i></a>
                    <button type="button" class="jnl-action-btn jnl-action-delete" title="Delete" onclick="confirmDeleteJournal({{ $j->id }}, '{{ addslashes($j->journal_number) }}', '{{ $j->status }}')"><i class="fas fa-trash-alt"></i></button>
                    <form id="deleteJnlForm{{ $j->id }}" method="POST" action="{{ route('nexcore.clients.show.accounting.journals.destroy', [$client->id, $j->id]) }}" style="display:none;">
                        @csrf @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="jnl-empty">
            <i class="fas fa-book" style="font-size:40px; opacity:0.2; margin-bottom:16px; display:block;"></i>
            <div style="font-size:16px; font-weight:600; margin-bottom:6px;">No journal entries yet</div>
            <div style="font-size:13px;">Click "New Journal" to create your first entry.</div>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<style>
    .jnl-tab { background:none; border:none; color:var(--text-muted); font-size:13px; font-weight:600; padding:14px 20px; cursor:pointer; display:flex; align-items:center; gap:8px; border-bottom:2px solid transparent; margin-bottom:-2px; transition:all 0.2s ease; font-family:var(--font-body); }
    .jnl-tab:hover { color:var(--text-secondary); }
    .jnl-tab.active { color:#f59e0b; border-bottom-color:#f59e0b; }
    .jnl-tab-count { font-family:var(--font-mono); font-size:11px; background:rgba(245,158,11,0.1); color:#f59e0b; padding:2px 8px; border-radius:10px; }
    .jnl-tab.active .jnl-tab-count { background:rgba(245,158,11,0.2); }

    .jnl-list { display:flex; flex-direction:column; gap:14px; padding:18px; }

    .jnl-card {
        background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12);
        border-radius:16px; padding:20px 24px; transition:all 0.3s cubic-bezier(0.4,0,0.2,1);
    }
    .jnl-card:hover { border-color:rgba(245,158,11,0.4); background:rgba(255,255,255,0.08); box-shadow:0 4px 24px rgba(0,0,0,0.3), 0 0 0 1px rgba(245,158,11,0.15); }
    .jnl-card-alt { background:rgba(59,130,246,0.06); border-color:rgba(59,130,246,0.18); }
    .jnl-card-alt:hover { background:rgba(59,130,246,0.1); border-color:rgba(59,130,246,0.3); box-shadow:0 4px 24px rgba(0,0,0,0.3), 0 0 0 1px rgba(59,130,246,0.2); }

    .jnl-row1 { display:flex; align-items:center; gap:14px; margin-bottom:12px; flex-wrap:wrap; }
    .jnl-row2 { display:flex; align-items:center; gap:14px; flex-wrap:wrap; border-top:1px solid rgba(255,255,255,0.06); padding-top:12px; }

    .jnl-num {
        font-family:var(--font-mono,monospace); font-size:16px; font-weight:900; color:var(--text-secondary,#94a3b8);
        width:36px; height:36px; display:flex; align-items:center; justify-content:center; flex-shrink:0;
        background:rgba(255,255,255,0.08); border-radius:10px; border:1px solid rgba(255,255,255,0.12);
    }

    .jnl-jnlno {
        font-family:var(--font-mono,monospace); font-size:14px; font-weight:700; color:#f59e0b; min-width:110px;
    }
    .jnl-stmtref {
        font-size:11px; font-weight:800; color:var(--accent-cyan,#22d3ee); letter-spacing:0.3px; margin-bottom:2px;
    }

    .jnl-date {
        font-family:var(--font-mono,monospace); font-size:12px; font-weight:700; padding:6px 14px; border-radius:8px;
        background:rgba(245,158,11,0.12); color:#f59e0b; border:1px solid rgba(245,158,11,0.25);
        display:inline-flex; align-items:center; gap:6px;
    }

    .jnl-ref {
        font-size:12px; color:var(--text-muted); max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
    }

    .jnl-status {
        font-size:11px; font-weight:800; padding:5px 12px; border-radius:8px; text-transform:uppercase; letter-spacing:0.5px;
        display:inline-flex; align-items:center; gap:5px;
    }
    .jnl-status-posted { background:rgba(16,185,129,0.18); color:#6ee7b7; border:1px solid rgba(16,185,129,0.35); }
    .jnl-status-reversed { background:rgba(239,68,68,0.18); color:#fca5a5; border:1px solid rgba(239,68,68,0.35); }
    .jnl-status-draft { background:rgba(245,158,11,0.18); color:#fbbf24; border:1px solid rgba(245,158,11,0.35); }

    .jnl-amount {
        font-family:var(--font-mono,monospace); font-size:15px; font-weight:900; color:var(--accent-green,#10b981);
        padding:8px 18px; border-radius:10px; background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.25);
    }

    .jnl-desc {
        font-size:13px; font-weight:600; color:var(--text-primary); display:inline-flex; align-items:center; gap:6px;
        max-width:400px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
    }
    .jnl-desc i { color:var(--text-muted); font-size:11px; }

    .jnl-source {
        font-size:11px; font-weight:700; padding:4px 10px; border-radius:6px;
        background:rgba(255,255,255,0.06); color:var(--text-muted); border:1px solid rgba(255,255,255,0.08);
        text-transform:uppercase; letter-spacing:0.3px;
    }

    .jnl-lines {
        font-family:var(--font-mono,monospace); font-size:12px; color:var(--text-secondary);
        display:inline-flex; align-items:center; gap:5px;
    }
    .jnl-lines i { color:var(--text-muted); font-size:11px; }

    .jnl-actions { display:flex; gap:8px; }
    .jnl-action-btn {
        width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center;
        border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.04); cursor:pointer;
        font-size:14px; transition:all 0.2s ease; text-decoration:none;
    }
    .jnl-action-edit { color:var(--accent-blue,#3b82f6); }
    .jnl-action-edit:hover { background:rgba(59,130,246,0.15); border-color:rgba(59,130,246,0.4); }
    .jnl-action-delete { color:var(--accent-red,#ef4444); }
    .jnl-action-delete:hover { background:rgba(239,68,68,0.15); border-color:rgba(239,68,68,0.4); }

    .jnl-empty { text-align:center; padding:60px; color:var(--text-muted); }

    @@media (max-width:768px) {
        .jnl-row1, .jnl-row2 { gap:10px; }
        .jnl-desc { max-width:100%; }
        .jnl-ref { max-width:140px; }
    }
</style>
<style>
.nx-swal-popup { border-radius:16px !important; }
.nx-swal-popup .swal2-image { margin:0 auto 12px !important; }
.nx-swal-title { font-family:'Montserrat',sans-serif !important; font-weight:800 !important; font-size:18px !important; color:#0f172a !important; }
.nx-swal-html { font-size:14px !important; color:#475569 !important; line-height:1.6 !important; }
.nx-swal-actions .swal2-confirm, .nx-swal-actions .swal2-cancel { font-family:'Montserrat',sans-serif !important; font-weight:700 !important; font-size:13px !important; letter-spacing:1px !important; text-transform:uppercase !important; border-radius:8px !important; padding:12px 28px !important; }
.swal2-popup .swal2-icon { display:none !important; }
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
    success: function(title, message) { return this._fire('success', title, message); },
    warning: function(title, message) { return this._fire('warning', title, message); },
    info: function(title, message) { return this._fire('info', title, message); },
    error: function(title, message) { return this._fire('error', title, message); },
    confirm: function(title, message, confirmText) {
        return this._fire('confirm', title, message, { showCancel:true, confirmText:confirmText||'YES, CONFIRM', cancelText:'CANCEL' });
    }
};

function confirmDeleteJournal(journalId, journalNumber, status) {
    var msg = 'This will permanently delete journal <strong>' + journalNumber + '</strong> and all its lines.';
    if (status === 'posted') {
        msg += '<br><br><span style="color:#e11d48;font-weight:700;">WARNING:</span> This is a posted journal. Deleting it will reverse its effect on the General Ledger.';
    }
    NxAlert._fire('warning', 'Delete Journal?', msg, {
        showCancel: true,
        confirmText: 'YES, DELETE',
        cancelText: 'CANCEL'
    }).then(function(result) {
        if (result.isConfirmed) {
            document.getElementById('deleteJnlForm' + journalId).submit();
        }
    });
}

function filterJournals(status, btn) {
    document.querySelectorAll('.jnl-tab').forEach(function(t) { t.classList.remove('active'); });
    btn.classList.add('active');
    var cards = document.querySelectorAll('.jnl-card');
    var num = 0;
    cards.forEach(function(c) {
        if (status === 'all' || c.dataset.journalStatus === status) {
            c.style.display = '';
            num++;
            c.querySelector('.jnl-num').textContent = num;
        } else {
            c.style.display = 'none';
        }
    });
    var empty = document.querySelector('.jnl-empty');
    if (empty) empty.style.display = num === 0 ? '' : 'none';
}
</script>
@endpush
