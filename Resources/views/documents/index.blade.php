@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', 'Documents - ' . $client->company_name)
@section('page_heading', 'DOCUMENTS')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(124,58,237,0.15), rgba(124,58,237,0.05)); border:1px solid rgba(124,58,237,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-folder-open" style="color:#7c3aed; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Documents</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; gap:8px;">
            <a href="{{ route('nexcore.clients.show.documents.create', $client->id) }}" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-plus"></i> Upload Document</a>
        </div>
    </div>
</div>

@php
    $now = \Carbon\Carbon::now();
    $totalDocs     = $documents->count();
    $activeDocs    = $documents->filter(fn($d) => $d->is_active)->count();
    $expiringSoon  = $documents->filter(fn($d) => $d->expiry_date && $d->expiry_date->isFuture() && $d->expiry_date->diffInDays($now) <= 30)->count();
    $expired       = $documents->filter(fn($d) => $d->expiry_date && $d->expiry_date->isPast())->count();
@endphp

<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card" style="border-color:#7c3aed;">
        <div class="sl-stat-label">Total Documents</div>
        <div class="sl-stat-value" style="color:#7c3aed;">{{ $totalDocs }}</div>
        <div class="sl-stat-meta">All uploaded files</div>
    </div>
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Active</div>
        <div class="sl-stat-value" style="color:var(--accent-green);">{{ $activeDocs }}</div>
        <div class="sl-stat-meta">Currently active</div>
    </div>
    <div class="sl-stat-card amber">
        <div class="sl-stat-label">Expiring Soon</div>
        <div class="sl-stat-value" style="color:var(--accent-amber);">{{ $expiringSoon }}</div>
        <div class="sl-stat-meta">Within 30 days</div>
    </div>
    <div class="sl-stat-card" style="border-color:var(--accent-red);">
        <div class="sl-stat-label">Expired</div>
        <div class="sl-stat-value" style="color:var(--accent-red);">{{ $expired }}</div>
        <div class="sl-stat-meta">Past expiry date</div>
    </div>
</div>

{{-- Tab Navigation --}}
<div class="sl-card sl-animate d3" style="margin-bottom:0; border-bottom:none; border-radius:var(--radius-md) var(--radius-md) 0 0;">
    <div style="display:flex; gap:0; border-bottom:2px solid var(--border-subtle); padding:0 4px; flex-wrap:wrap;">
        <button class="doc-tab active" data-filter="all" onclick="filterDocs('all', this)">
            <i class="fas fa-layer-group"></i> All Documents
            <span class="doc-tab-count">{{ $totalDocs }}</span>
        </button>
        <button class="doc-tab" data-filter="COMPANY" onclick="filterDocs('COMPANY', this)">
            <i class="fas fa-building"></i> Company
            <span class="doc-tab-count">{{ $documents->filter(fn($d) => $d->documentType && in_array($d->documentType->code, ['REG','MOI']))->count() }}</span>
        </button>
        <button class="doc-tab" data-filter="TAX" onclick="filterDocs('TAX', this)">
            <i class="fas fa-file-invoice-dollar"></i> Tax & SARS
            <span class="doc-tab-count">{{ $documents->filter(fn($d) => $d->documentType && in_array($d->documentType->code, ['TCC','SARS','AFS']))->count() }}</span>
        </button>
        <button class="doc-tab" data-filter="CERTS" onclick="filterDocs('CERTS', this)">
            <i class="fas fa-certificate"></i> Certificates
            <span class="doc-tab-count">{{ $documents->filter(fn($d) => $d->documentType && in_array($d->documentType->code, ['BEE','ID','BANK']))->count() }}</span>
        </button>
        <button class="doc-tab" data-filter="LEGAL" onclick="filterDocs('LEGAL', this)">
            <i class="fas fa-gavel"></i> Legal
            <span class="doc-tab-count">{{ $documents->filter(fn($d) => $d->documentType && in_array($d->documentType->code, ['POA']))->count() }}</span>
        </button>
        <button class="doc-tab" data-filter="OTHER" onclick="filterDocs('OTHER', this)">
            <i class="fas fa-ellipsis-h"></i> Other
            <span class="doc-tab-count">{{ $documents->filter(fn($d) => $d->documentType && !in_array($d->documentType->code, ['REG','MOI','TCC','SARS','AFS','BEE','ID','BANK','POA']))->count() }}</span>
        </button>
    </div>
</div>

{{-- Table --}}
<div class="sl-card" style="border-radius:0 0 var(--radius-md) var(--radius-md); margin-top:0;">
    <div class="sl-table-wrap">
        <table class="sl-table" id="docsTable">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Document Type</th>
                    <th>Title</th>
                    <th>File</th>
                    <th>Uploaded</th>
                    <th>Expiry Date</th>
                    <th class="center">Status</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $idx => $doc)
                @php
                    $isExpired      = $doc->expiry_date && $doc->expiry_date->isPast();
                    $isExpiringSoon = $doc->expiry_date && $doc->expiry_date->isFuture() && $doc->expiry_date->diffInDays($now) <= 30;
                    $expiryColor    = $isExpired ? 'var(--accent-red)' : ($isExpiringSoon ? 'var(--accent-amber)' : 'var(--text-secondary)');
                @endphp
                <tr class="doc-row" data-type-code="{{ $doc->documentType->code ?? '' }}">
                    <td style="color:var(--text-muted);" class="doc-row-num">{{ $idx + 1 }}</td>
                    <td>
                        <div style="font-weight:600; color:var(--text-primary);">{{ $doc->documentType->name ?? '-' }}</div>
                        @if($doc->documentType && $doc->documentType->code)
                            <code style="font-size:11px; color:#a78bfa; background:rgba(124,58,237,0.1); padding:2px 6px; border-radius:4px;">{{ $doc->documentType->code }}</code>
                        @endif
                    </td>
                    <td>
                        <span style="font-size:14px; font-weight:600; color:var(--text-primary);">{{ $doc->title }}</span>
                        @if($doc->description)
                            <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">{{ \Illuminate\Support\Str::limit($doc->description, 60) }}</div>
                        @endif
                    </td>
                    <td>
                        @if($doc->file_name)
                            <div style="font-size:13px; color:var(--text-secondary); font-family:var(--font-mono);">{{ $doc->file_name }}</div>
                            @if($doc->file_size)
                                <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">{{ number_format($doc->file_size / 1024, 1) }} KB</div>
                            @endif
                        @else
                            <span style="color:var(--text-muted);">-</span>
                        @endif
                    </td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">{{ $doc->created_at->format('j M Y') }}</td>
                    <td>
                        @if($doc->expiry_date)
                            <span style="font-family:var(--font-mono); font-size:13px; color:{{ $expiryColor }}; font-weight:{{ $isExpired || $isExpiringSoon ? '700' : '400' }};">
                                {{ $doc->expiry_date->format('j M Y') }}
                            </span>
                            @if($isExpired)
                                <div style="font-size:11px; color:var(--accent-red); margin-top:2px;">Expired</div>
                            @elseif($isExpiringSoon)
                                <div style="font-size:11px; color:var(--accent-amber); margin-top:2px;">Expires in {{ $doc->expiry_date->diffInDays($now) }}d</div>
                            @endif
                        @else
                            <span style="color:var(--text-muted);">No expiry</span>
                        @endif
                    </td>
                    <td class="center">
                        @if($doc->status)
                            @php
                                $statusColor = $doc->status->color ?? 'muted';
                                $tagClass = match($statusColor) {
                                    'green' => 'sl-tag sl-tag-green',
                                    'red'   => 'sl-tag sl-tag-red',
                                    'amber' => 'sl-tag sl-tag-amber',
                                    'blue'  => 'sl-tag sl-tag-blue',
                                    'cyan'  => 'sl-tag',
                                    default => 'sl-tag',
                                };
                                $tagStyle = match($statusColor) {
                                    'cyan'  => 'background:rgba(6,182,212,0.15); color:#22d3ee; border:1px solid rgba(6,182,212,0.3);',
                                    'muted' => 'background:rgba(148,163,184,0.1); color:var(--text-muted); border:1px solid rgba(148,163,184,0.2);',
                                    default => '',
                                };
                            @endphp
                            <span class="{{ $tagClass }}" @if($tagStyle) style="{{ $tagStyle }}" @endif>{{ $doc->status->name }}</span>
                        @else
                            <span style="color:var(--text-muted);">-</span>
                        @endif
                    </td>
                    <td class="center">
                        <div style="display:flex; gap:6px; justify-content:center; align-items:center;">
                            @if($doc->file_path)
                                <a href="{{ asset('uploads/documents/' . $doc->file_path) }}" target="_blank" style="color:#7c3aed; font-size:15px;" title="Download / View"><i class="fas fa-download"></i></a>
                            @endif
                            <a href="{{ route('nexcore.clients.show.documents.edit', [$client->id, $doc->id]) }}" style="color:var(--accent-blue); font-size:15px;" title="Edit"><i class="fas fa-pen"></i></a>
                            <form method="POST" action="{{ route('nexcore.clients.show.documents.destroy', [$client->id, $doc->id]) }}" style="display:inline;" onsubmit="return confirm('Delete this document? This action cannot be undone.')">@csrf @method('DELETE')
                                <button type="submit" style="background:none; border:none; color:var(--accent-red); cursor:pointer; font-size:15px;" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="doc-empty-row"><td colspan="8" style="text-align:center; padding:60px; color:var(--text-muted);">
                    <i class="fas fa-folder-open" style="font-size:40px; opacity:0.2; margin-bottom:16px; display:block;"></i>
                    <div style="font-size:16px; font-weight:600; margin-bottom:6px;">No documents yet</div>
                    <div style="font-size:13px;">Click "Upload Document" to add the first document for this client</div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div id="docNoResults" style="display:none; text-align:center; padding:48px; color:var(--text-muted);">
        <i class="fas fa-search" style="font-size:32px; opacity:0.2; margin-bottom:12px; display:block;"></i>
        <div style="font-size:15px; font-weight:600;">No documents in this category</div>
        <div style="font-size:13px; margin-top:4px;">Try a different tab or upload a new document</div>
    </div>
</div>
@endsection

@push('styles')
<style>
.doc-tab {
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:14px 20px;
    font-size:13px;
    font-weight:600;
    font-family:var(--font-body);
    color:var(--text-muted);
    background:none;
    border:none;
    border-bottom:3px solid transparent;
    cursor:pointer;
    transition:all 0.25s ease;
    position:relative;
    top:2px;
    letter-spacing:0.3px;
}
.doc-tab:hover {
    color:var(--text-primary);
    background:rgba(255,255,255,0.02);
}
.doc-tab.active {
    color:#a78bfa;
    border-bottom-color:#7c3aed;
}
.doc-tab.active .doc-tab-count {
    background:rgba(124,58,237,0.2);
    color:#a78bfa;
}
.doc-tab-count {
    font-size:11px;
    font-weight:700;
    font-family:var(--font-mono);
    padding:2px 7px;
    border-radius:10px;
    background:rgba(148,163,184,0.1);
    color:var(--text-muted);
    transition:all 0.25s ease;
    min-width:20px;
    text-align:center;
}
.doc-tab i {
    font-size:12px;
    opacity:0.7;
}
.doc-tab.active i {
    opacity:1;
}
.doc-row.hidden-row {
    display:none;
}
</style>
@endpush

@push('scripts')
<script>
function filterDocs(filter, btn) {
    document.querySelectorAll('.doc-tab').forEach(function(t) { t.classList.remove('active'); });
    btn.classList.add('active');

    var companyCodes = ['REG', 'MOI'];
    var taxCodes     = ['TCC', 'SARS', 'AFS'];
    var certCodes    = ['BEE', 'ID', 'BANK'];
    var legalCodes   = ['POA'];
    var knownCodes   = companyCodes.concat(taxCodes, certCodes, legalCodes);

    var rows = document.querySelectorAll('.doc-row');
    var visibleCount = 0;
    var num = 0;

    rows.forEach(function(row) {
        var code = row.getAttribute('data-type-code') || '';
        var show = false;

        if (filter === 'all') {
            show = true;
        } else if (filter === 'COMPANY') {
            show = companyCodes.indexOf(code) !== -1;
        } else if (filter === 'TAX') {
            show = taxCodes.indexOf(code) !== -1;
        } else if (filter === 'CERTS') {
            show = certCodes.indexOf(code) !== -1;
        } else if (filter === 'LEGAL') {
            show = legalCodes.indexOf(code) !== -1;
        } else if (filter === 'OTHER') {
            show = knownCodes.indexOf(code) === -1;
        }

        if (show) {
            row.classList.remove('hidden-row');
            num++;
            row.querySelector('.doc-row-num').textContent = num;
            visibleCount++;
        } else {
            row.classList.add('hidden-row');
        }
    });

    var noResults = document.getElementById('docNoResults');
    var emptyRow = document.querySelector('.doc-empty-row');
    if (visibleCount === 0 && !emptyRow) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}
</script>
@endpush
