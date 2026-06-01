@extends('nexcore_client_manager::layouts.app')

@section('title', 'Manage Chart of Accounts')
@section('page_heading', 'MANAGE COA')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(245,158,11,0.15), rgba(245,158,11,0.05)); border:1px solid rgba(245,158,11,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-sitemap" style="color:#f59e0b; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Manage Chart of Accounts</h1>
                <span class="sl-page-subtitle">View and reset accounting data across all clients</span>
            </div>
        </div>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.index') }}" class="neon-btn" style="border:1px solid var(--border-subtle); color:var(--text-secondary);"><i class="fas fa-arrow-left"></i> All Clients</a>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card" style="border-color:rgba(245,158,11,0.4);">
        <div class="sl-stat-label">Total Clients</div>
        <div class="sl-stat-value" style="color:#f59e0b;">{{ $clients->count() }}</div>
        <div class="sl-stat-meta">All registered companies</div>
    </div>
    <div class="sl-stat-card green">
        <div class="sl-stat-label">With COA</div>
        <div class="sl-stat-value" style="color:var(--accent-green);">{{ $clients->where('account_count', '>', 0)->count() }}</div>
        <div class="sl-stat-meta">Chart of accounts set up</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(239,68,68,0.4);">
        <div class="sl-stat-label">No COA</div>
        <div class="sl-stat-value" style="color:var(--accent-red);">{{ $clients->where('account_count', 0)->count() }}</div>
        <div class="sl-stat-meta">Needs setup</div>
    </div>
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">Total Accounts</div>
        <div class="sl-stat-value" style="color:var(--accent-blue);">{{ number_format($clients->sum('account_count'), 0, '.', ' ') }}</div>
        <div class="sl-stat-meta">Across all clients</div>
    </div>
</div>

{{-- Warning --}}
<div class="sl-animate d2" style="padding:14px 20px; background:rgba(239,68,68,0.06); border:1px solid rgba(239,68,68,0.25); border-radius:10px; color:var(--accent-red); font-size:13px; font-weight:600; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
    <i class="fas fa-exclamation-triangle" style="font-size:16px;"></i>
    <span>Danger Zone: Resetting a client will permanently delete ALL accounting data (COA, journals, bank accounts, statements, transactions, reconciliations, allocation rules). This cannot be undone.</span>
</div>

{{-- Client Table --}}
<div class="sl-card sl-animate d3">
    <div class="sl-card-header" style="display:flex; align-items:center; justify-content:space-between;">
        <div class="sl-card-title" style="color:#f59e0b;"><i class="fas fa-building"></i> Client Accounting Overview</div>
        <span style="font-size:12px; color:var(--text-muted);">{{ $clients->count() }} {{ \Illuminate\Support\Str::plural('client', $clients->count()) }}</span>
    </div>

    <div class="sl-table-wrap">
        <table class="sl-table">
            <thead>
                <tr>
                    <th style="width:36px;">#</th>
                    <th>Company Name</th>
                    <th style="width:100px;">Client Code</th>
                    <th style="width:100px; text-align:center;">COA</th>
                    <th style="width:100px; text-align:center;">Journals</th>
                    <th style="width:100px; text-align:center;">Bank Acc</th>
                    <th style="width:100px; text-align:center;">Transactions</th>
                    <th style="width:110px; text-align:center;">Status</th>
                    <th style="width:120px; text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $idx => $c)
                <tr class="mc-row" id="mc-row-{{ $c->id }}">
                    <td style="font-family:var(--font-mono); font-size:13px; color:var(--text-muted);">{{ $idx + 1 }}</td>
                    <td>
                        <div style="font-weight:700; color:var(--text-primary); font-size:14px;">{{ $c->company_name }}</div>
                        @if($c->trading_name)
                        <div style="font-size:12px; color:var(--text-muted);">t/a {{ $c->trading_name }}</div>
                        @endif
                    </td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:#f59e0b; font-weight:600;">{{ $c->client_code }}</td>
                    <td style="text-align:center;">
                        <span style="font-family:var(--font-mono); font-size:15px; font-weight:700; color:{{ $c->account_count > 0 ? 'var(--accent-green)' : 'var(--text-muted)' }};">{{ $c->account_count }}</span>
                    </td>
                    <td style="text-align:center;">
                        <span style="font-family:var(--font-mono); font-size:15px; font-weight:700; color:{{ $c->journal_count > 0 ? 'var(--accent-blue)' : 'var(--text-muted)' }};">{{ $c->journal_count }}</span>
                    </td>
                    <td style="text-align:center;">
                        <span style="font-family:var(--font-mono); font-size:15px; font-weight:700; color:{{ $c->bank_account_count > 0 ? 'var(--accent-blue)' : 'var(--text-muted)' }};">{{ $c->bank_account_count }}</span>
                    </td>
                    <td style="text-align:center;">
                        <span style="font-family:var(--font-mono); font-size:15px; font-weight:700; color:{{ $c->transaction_count > 0 ? 'var(--accent-blue)' : 'var(--text-muted)' }};">{{ $c->transaction_count }}</span>
                    </td>
                    <td style="text-align:center;">
                        @if($c->account_count > 0)
                            <span class="mc-badge mc-badge-green"><i class="fas fa-check-circle"></i> Active</span>
                        @else
                            <span class="mc-badge mc-badge-amber"><i class="fas fa-exclamation-circle"></i> No COA</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        @if($c->account_count > 0 || $c->journal_count > 0 || $c->bank_account_count > 0 || $c->transaction_count > 0)
                        <button type="button" class="mc-reset-btn" onclick="confirmReset({{ $c->id }}, '{{ addslashes($c->company_name) }}', {{ $c->account_count }}, {{ $c->journal_count }}, {{ $c->bank_account_count }}, {{ $c->transaction_count }})">
                            <i class="fas fa-trash-alt"></i> Reset
                        </button>
                        @else
                        <span style="font-size:12px; color:var(--text-muted); font-weight:600;">Clean</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<style>
    .swal2-container { z-index: 99999 !important; }
    .nc-swal-popup { border: 1px solid rgba(245,158,11,0.2) !important; border-radius: 16px !important; box-shadow: 0 20px 60px rgba(0,0,0,0.6), 0 0 40px rgba(245,158,11,0.05) !important; }
    .nc-swal-title { color: #f1f5f9 !important; font-weight: 800 !important; font-size: 20px !important; letter-spacing: 0.5px !important; font-family: 'Montserrat', sans-serif !important; }
    .nc-swal-html { color: #94a3b8 !important; font-size: 14px !important; line-height: 1.6 !important; }
    .nc-swal-actions .swal2-confirm, .nc-swal-actions .swal2-cancel { font-family: 'Montserrat', sans-serif !important; font-weight: 700 !important; font-size: 13px !important; letter-spacing: 1px !important; text-transform: uppercase !important; border-radius: 8px !important; padding: 12px 28px !important; }
    .swal2-popup .swal2-icon { border: none !important; }
    .mc-row td { padding:12px 16px !important; border-bottom:1px solid rgba(255,255,255,0.03) !important; }
    .mc-row:hover td { background:rgba(245,158,11,0.03); }

    .mc-badge { display:inline-flex; align-items:center; gap:5px; font-size:12px; font-weight:700; padding:4px 10px; border-radius:6px; text-transform:uppercase; letter-spacing:0.3px; white-space:nowrap; }
    .mc-badge-green { color:var(--accent-green); background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.25); }
    .mc-badge-amber { color:#f59e0b; background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.25); }

    .mc-reset-btn {
        display:inline-flex; align-items:center; gap:5px; padding:6px 14px; border-radius:8px;
        font-size:12px; font-weight:700; cursor:pointer; transition:all 0.2s;
        background:rgba(239,68,68,0.08); color:var(--accent-red); border:1px solid rgba(239,68,68,0.25);
        font-family:inherit;
    }
    .mc-reset-btn:hover { background:rgba(239,68,68,0.15); border-color:rgba(239,68,68,0.4); }
</style>
<style>
.nx-swal-popup { background:#ffffff !important; border-radius:20px !important; box-shadow:0 25px 80px rgba(0,0,0,0.12),0 8px 24px rgba(0,0,0,0.06) !important; padding:32px 28px 24px !important; border:none !important; }
.nx-swal-popup.nx-swal-success { border-top:4px solid #059669 !important; }
.nx-swal-popup.nx-swal-warning { border-top:4px solid #e11d48 !important; }
.nx-swal-popup.nx-swal-info { border-top:4px solid #2563eb !important; }
.nx-swal-popup.nx-swal-confirm { border-top:4px solid #0891b2 !important; }
.nx-swal-popup.nx-swal-error { border-top:4px solid #d97706 !important; }
.nx-swal-title { color:#1e293b !important; font-weight:800 !important; font-size:20px !important; letter-spacing:0.3px !important; font-family:'Poppins','Montserrat',sans-serif !important; margin-top:8px !important; }
.nx-swal-html { color:#64748b !important; font-size:14px !important; line-height:1.7 !important; font-family:'Poppins',sans-serif !important; }
.nx-swal-actions .swal2-confirm { font-family:'Poppins',sans-serif !important; font-weight:700 !important; font-size:13px !important; letter-spacing:1px !important; text-transform:uppercase !important; border-radius:10px !important; padding:12px 32px !important; border:none !important; box-shadow:0 4px 14px rgba(0,0,0,0.1) !important; }
.nx-swal-actions .swal2-cancel { font-family:'Poppins',sans-serif !important; font-weight:700 !important; font-size:13px !important; letter-spacing:1px !important; text-transform:uppercase !important; border-radius:10px !important; padding:12px 32px !important; background:#f1f5f9 !important; color:#64748b !important; border:1px solid #e2e8f0 !important; box-shadow:none !important; }
.nx-swal-actions .swal2-cancel:hover { background:#e2e8f0 !important; color:#475569 !important; }
.swal2-popup .swal2-icon { display:none !important; }
.nx-swal-popup .swal2-image { margin:0 auto 12px !important; }
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
        if (opts && opts.extra) Object.assign(config, opts.extra);
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

function confirmReset(clientId, clientName, coaCount, journalCount, bankCount, txnCount) {
    var details = [];
    if (coaCount > 0) details.push('<strong>' + coaCount + '</strong> chart of accounts entries');
    if (journalCount > 0) details.push('<strong>' + journalCount + '</strong> journals (and all lines)');
    if (bankCount > 0) details.push('<strong>' + bankCount + '</strong> bank accounts');
    if (txnCount > 0) details.push('<strong>' + txnCount + '</strong> bank transactions');

    var htmlContent = '<div style="color:#64748b;font-size:14px;line-height:1.8;text-align:left;">' +
        'You are about to <strong style="color:#e11d48;">permanently delete ALL accounting data</strong> for:<br>' +
        '<div style="margin:12px 0;padding:10px 14px;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.25);border-radius:8px;color:#d97706;font-weight:700;font-size:15px;">' + clientName + '</div>' +
        '<div style="font-size:13px;color:#94a3b8;">This will delete:</div>' +
        '<ul style="margin:6px 0 12px 16px;font-size:13px;color:#64748b;line-height:1.8;">' + details.map(function(d) { return '<li>' + d + '</li>'; }).join('') +
        '<li>All bank statements &amp; reconciliations</li><li>All allocation rules</li></ul>' +
        '<div style="font-size:13px;color:#e11d48;font-weight:600;">This action CANNOT be undone.</div>' +
        '<div style="margin-top:14px;"><label style="font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Type DELETE to confirm:</label>' +
        '<input type="text" id="swalConfirmInput" style="width:100%;margin-top:6px;padding:10px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;color:#1e293b;font-size:14px;font-weight:700;font-family:inherit;letter-spacing:2px;" placeholder="DELETE" autocomplete="off"></div>' +
        '</div>';

    NxAlert._fire('warning', 'Reset Accounting Data', htmlContent, {
        showCancel: true,
        confirmText: 'RESET ALL DATA',
        cancelText: 'CANCEL',
        extra: {
            preConfirm: function() {
                var val = document.getElementById('swalConfirmInput').value.trim();
                if (val !== 'DELETE') {
                    Swal.showValidationMessage('You must type DELETE to confirm');
                    return false;
                }
                return true;
            }
        }
    }).then(function(result) {
        if (!result.isConfirmed) return;

        Swal.fire({
            imageUrl: NxAlert._logo, imageWidth:56, imageHeight:56,
            title: 'Resetting...',
            html: '<div style="color:#64748b;font-size:14px;">Deleting all accounting data for ' + clientName + '...</div>',
            allowOutsideClick: false,
            showConfirmButton: false,
            background: '#ffffff',
            customClass: { popup:'nx-swal-popup nx-swal-info', title:'nx-swal-title', htmlContainer:'nx-swal-html' },
            didOpen: function() { Swal.showLoading(); }
        });

        fetch('{{ url("nexcore/clients/manage-coa") }}/' + clientId + '/reset', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ confirm_text: 'DELETE' })
        }).then(function(r) { return r.json(); }).then(function(d) {
            if (d.success) {
                NxAlert.success('Reset Complete', '<div style="color:#64748b;font-size:14px;">' + d.message + '</div>').then(function() { window.location.reload(); });
            } else {
                NxAlert.error('Error', '<div style="color:#64748b;font-size:14px;">' + (d.error || 'Something went wrong.') + '</div>');
            }
        }).catch(function() {
            NxAlert.error('Network Error', '<div style="color:#64748b;font-size:14px;">Could not connect to the server. Please try again.</div>');
        });
    });
}
</script>
@endpush
