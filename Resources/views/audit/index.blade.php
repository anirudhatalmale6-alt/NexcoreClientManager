@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', 'Audit Trail - ' . $client->company_name)
@section('page_heading', 'AUDIT TRAIL')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(6,182,212,0.15), rgba(6,182,212,0.05)); border:1px solid rgba(6,182,212,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-history" style="color:#06b6d4; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Audit Trail</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        {{-- READ-ONLY module — no New button --}}
    </div>
</div>

@php
    $total   = $auditTrail->count();
    $created = $auditTrail->where('action', 'created')->count();
    $updated = $auditTrail->where('action', 'updated')->count();
    $deleted = $auditTrail->where('action', 'deleted')->count();
@endphp

<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card" style="border-color:rgba(6,182,212,0.4);">
        <div class="sl-stat-label">Total Events</div>
        <div class="sl-stat-value" style="color:#06b6d4;">{{ $total }}</div>
        <div class="sl-stat-meta">All recorded changes</div>
    </div>
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Created</div>
        <div class="sl-stat-value" style="color:var(--accent-green);">{{ $created }}</div>
        <div class="sl-stat-meta">Records added</div>
    </div>
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">Updated</div>
        <div class="sl-stat-value" style="color:#60a5fa;">{{ $updated }}</div>
        <div class="sl-stat-meta">Records modified</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(239,68,68,0.4);">
        <div class="sl-stat-label">Deleted</div>
        <div class="sl-stat-value" style="color:var(--accent-red);">{{ $deleted }}</div>
        <div class="sl-stat-meta">Records removed</div>
    </div>
</div>

{{-- Tab Navigation --}}
<div class="sl-card sl-animate d3" style="margin-bottom:0; border-bottom:none; border-radius:var(--radius-md) var(--radius-md) 0 0;">
    <div style="display:flex; flex-wrap:wrap; gap:0; border-bottom:2px solid var(--border-subtle); padding:0 4px;">
        @foreach($modules as $key => $label)
        @php
            $count = ($key === 'all') ? $total : $auditTrail->where('module', $key)->count();
        @endphp
        <button class="audit-tab {{ $key === 'all' ? 'active' : '' }}"
                data-filter="{{ $key }}"
                onclick="filterAudit('{{ $key }}', this)">
            @if($key === 'all')<i class="fas fa-layer-group"></i>
            @elseif($key === 'clients')<i class="fas fa-building"></i>
            @elseif($key === 'addresses')<i class="fas fa-map-marker-alt"></i>
            @elseif($key === 'contacts')<i class="fas fa-address-book"></i>
            @elseif($key === 'banking')<i class="fas fa-university"></i>
            @elseif($key === 'directors')<i class="fas fa-user-tie"></i>
            @elseif($key === 'sars')<i class="fas fa-file-invoice"></i>
            @elseif($key === 'cipc')<i class="fas fa-stamp"></i>
            @elseif($key === 'financials')<i class="fas fa-chart-line"></i>
            @elseif($key === 'documents')<i class="fas fa-folder-open"></i>
            @elseif($key === 'tasks')<i class="fas fa-tasks"></i>
            @elseif($key === 'meetings')<i class="fas fa-calendar-alt"></i>
            @elseif($key === 'alerts')<i class="fas fa-bell"></i>
            @endif
            {{ $label }}
            <span class="audit-tab-count">{{ $count }}</span>
        </button>
        @endforeach
    </div>
</div>

{{-- Table --}}
<div class="sl-card" style="border-radius:0 0 var(--radius-md) var(--radius-md); margin-top:0;">
    <div class="sl-table-wrap">
        <table class="sl-table" id="auditTable">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="width:160px;">Timestamp</th>
                    <th style="width:140px;">User</th>
                    <th style="width:90px;" class="center">Action</th>
                    <th style="width:110px;" class="center">Module</th>
                    <th>Description</th>
                    <th style="width:60px;" class="center">Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auditTrail as $idx => $entry)
                <tr class="audit-row" data-module="{{ $entry->module }}">
                    <td style="color:var(--text-muted);" class="audit-row-num">{{ $idx + 1 }}</td>

                    {{-- Timestamp --}}
                    <td>
                        <span style="font-family:var(--font-mono); font-size:12px; color:var(--text-secondary);">
                            {{ $entry->created_at->format('j M Y') }}<br>
                            <span style="color:var(--text-muted);">{{ $entry->created_at->format('H:i') }}</span>
                        </span>
                    </td>

                    {{-- User --}}
                    <td>
                        <span style="font-size:13px; color:var(--text-primary); font-weight:500;">
                            {{ $entry->user_name ?: 'System' }}
                        </span>
                    </td>

                    {{-- Action tag --}}
                    <td class="center">
                        @if($entry->action === 'created')
                            <span class="audit-action-tag audit-action-created">Created</span>
                        @elseif($entry->action === 'updated')
                            <span class="audit-action-tag audit-action-updated">Updated</span>
                        @elseif($entry->action === 'deleted')
                            <span class="audit-action-tag audit-action-deleted">Deleted</span>
                        @else
                            <span class="audit-action-tag" style="background:rgba(148,163,184,0.1); color:var(--text-muted); border:1px solid rgba(148,163,184,0.2);">{{ ucfirst($entry->action) }}</span>
                        @endif
                    </td>

                    {{-- Module tag --}}
                    <td class="center">
                        <span class="audit-module-tag">{{ ucfirst($entry->module) }}</span>
                    </td>

                    {{-- Description --}}
                    <td style="font-size:13px; color:var(--text-secondary);">
                        {{ \Illuminate\Support\Str::limit($entry->description, 100) }}
                    </td>

                    {{-- Details toggle --}}
                    <td class="center">
                        @if($entry->old_values || $entry->new_values)
                            <button class="audit-details-btn" onclick="toggleAuditDetails(this)" title="View changed fields">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        @else
                            <span style="color:var(--text-muted); font-size:12px;">—</span>
                        @endif
                    </td>
                </tr>

                {{-- Hidden details row --}}
                @if($entry->old_values || $entry->new_values)
                <tr class="audit-details-row" style="display:none;">
                    <td colspan="7" style="padding:0;">
                        <div class="audit-details-panel">
                            @if($entry->old_values && $entry->new_values)
                                <div class="audit-details-columns">
                                    <div class="audit-details-col">
                                        <div class="audit-details-col-heading"><i class="fas fa-arrow-left"></i> Before</div>
                                        @foreach($entry->old_values as $field => $val)
                                        <div class="audit-details-field">
                                            <span class="audit-field-key">{{ str_replace('_', ' ', $field) }}</span>
                                            <span class="audit-field-val old">{{ is_null($val) ? '(empty)' : (is_array($val) ? json_encode($val) : $val) }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="audit-details-divider"></div>
                                    <div class="audit-details-col">
                                        <div class="audit-details-col-heading" style="color:#4ade80;"><i class="fas fa-arrow-right"></i> After</div>
                                        @foreach($entry->new_values as $field => $val)
                                        <div class="audit-details-field">
                                            <span class="audit-field-key">{{ str_replace('_', ' ', $field) }}</span>
                                            <span class="audit-field-val new">{{ is_null($val) ? '(empty)' : (is_array($val) ? json_encode($val) : $val) }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif($entry->new_values)
                                <div class="audit-details-col-heading" style="color:#4ade80; margin-bottom:10px;"><i class="fas fa-plus-circle"></i> Created with values</div>
                                @foreach($entry->new_values as $field => $val)
                                <div class="audit-details-field">
                                    <span class="audit-field-key">{{ str_replace('_', ' ', $field) }}</span>
                                    <span class="audit-field-val new">{{ is_null($val) ? '(empty)' : (is_array($val) ? json_encode($val) : $val) }}</span>
                                </div>
                                @endforeach
                            @elseif($entry->old_values)
                                <div class="audit-details-col-heading" style="color:var(--accent-red); margin-bottom:10px;"><i class="fas fa-trash-alt"></i> Deleted values</div>
                                @foreach($entry->old_values as $field => $val)
                                <div class="audit-details-field">
                                    <span class="audit-field-key">{{ str_replace('_', ' ', $field) }}</span>
                                    <span class="audit-field-val old">{{ is_null($val) ? '(empty)' : (is_array($val) ? json_encode($val) : $val) }}</span>
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </td>
                </tr>
                @endif

                @empty
                <tr class="audit-empty-row">
                    <td colspan="7" style="text-align:center; padding:72px 40px; color:var(--text-muted);">
                        <i class="fas fa-history" style="font-size:44px; opacity:0.15; margin-bottom:18px; display:block; color:#06b6d4;"></i>
                        <div style="font-size:16px; font-weight:600; margin-bottom:8px; color:var(--text-secondary);">No audit trail entries yet</div>
                        <div style="font-size:13px; max-width:380px; margin:0 auto; line-height:1.7;">Changes to client records will be logged here automatically.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="auditNoResults" style="display:none; text-align:center; padding:56px 40px; color:var(--text-muted);">
        <i class="fas fa-search" style="font-size:34px; opacity:0.2; margin-bottom:14px; display:block;"></i>
        <div style="font-size:15px; font-weight:600;">No entries in this category</div>
        <div style="font-size:13px; margin-top:4px;">Try selecting a different tab</div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* ── Tab styles ─────────────────────────────────────────────── */
.audit-tab {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 13px 16px;
    font-size: 12px;
    font-weight: 600;
    font-family: var(--font-body);
    color: var(--text-muted);
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    transition: all 0.25s ease;
    position: relative;
    top: 2px;
    letter-spacing: 0.3px;
    white-space: nowrap;
}
.audit-tab:hover {
    color: var(--text-primary);
    background: rgba(255,255,255,0.02);
}
.audit-tab.active {
    color: #06b6d4;
    border-bottom-color: #06b6d4;
}
.audit-tab.active .audit-tab-count {
    background: rgba(6,182,212,0.18);
    color: #67e8f9;
}
.audit-tab-count {
    font-size: 11px;
    font-weight: 700;
    font-family: var(--font-mono);
    padding: 2px 7px;
    border-radius: 10px;
    background: rgba(148,163,184,0.1);
    color: var(--text-muted);
    transition: all 0.25s ease;
    min-width: 20px;
    text-align: center;
}
.audit-tab i {
    font-size: 11px;
    opacity: 0.7;
}
.audit-tab.active i {
    opacity: 1;
}
.audit-row.hidden-row,
.audit-details-row.hidden-row {
    display: none !important;
}

/* ── Action tags ────────────────────────────────────────────── */
.audit-action-tag {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 10px;
    letter-spacing: 0.3px;
}
.audit-action-created {
    background: rgba(34,197,94,0.13);
    color: #4ade80;
    border: 1px solid rgba(34,197,94,0.3);
}
.audit-action-updated {
    background: rgba(37,99,235,0.13);
    color: #60a5fa;
    border: 1px solid rgba(37,99,235,0.3);
}
.audit-action-deleted {
    background: rgba(239,68,68,0.13);
    color: #f87171;
    border: 1px solid rgba(239,68,68,0.3);
}

/* ── Module tag ─────────────────────────────────────────────── */
.audit-module-tag {
    display: inline-block;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 9px;
    border-radius: 10px;
    background: rgba(6,182,212,0.1);
    color: #67e8f9;
    border: 1px solid rgba(6,182,212,0.25);
    letter-spacing: 0.3px;
}

/* ── Details button ─────────────────────────────────────────── */
.audit-details-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: #06b6d4;
    font-size: 15px;
    opacity: 0.6;
    transition: opacity 0.2s ease, transform 0.2s ease;
    padding: 2px 4px;
}
.audit-details-btn:hover {
    opacity: 1;
    transform: scale(1.15);
}
.audit-details-btn.active {
    opacity: 1;
    color: #67e8f9;
}

/* ── Details panel ──────────────────────────────────────────── */
.audit-details-panel {
    background: rgba(6,182,212,0.04);
    border-top: 1px solid rgba(6,182,212,0.12);
    border-bottom: 1px solid rgba(6,182,212,0.12);
    padding: 16px 24px;
    font-size: 12px;
}
.audit-details-columns {
    display: flex;
    gap: 0;
    align-items: flex-start;
}
.audit-details-col {
    flex: 1;
    min-width: 0;
}
.audit-details-divider {
    width: 1px;
    background: rgba(6,182,212,0.2);
    align-self: stretch;
    margin: 0 24px;
    flex-shrink: 0;
}
.audit-details-col-heading {
    font-size: 11px;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.audit-details-field {
    display: flex;
    align-items: baseline;
    gap: 10px;
    padding: 5px 0;
    border-bottom: 1px solid rgba(255,255,255,0.03);
}
.audit-details-field:last-child {
    border-bottom: none;
}
.audit-field-key {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: capitalize;
    min-width: 120px;
    flex-shrink: 0;
}
.audit-field-val {
    font-family: var(--font-mono);
    font-size: 12px;
    word-break: break-word;
    flex: 1;
}
.audit-field-val.old {
    color: #fca5a5;
}
.audit-field-val.new {
    color: #86efac;
}
</style>
@endpush

@push('scripts')
<script>
function filterAudit(filter, btn) {
    document.querySelectorAll('.audit-tab').forEach(function(t) { t.classList.remove('active'); });
    btn.classList.add('active');

    var rows = document.querySelectorAll('.audit-row');
    var visibleCount = 0;
    var num = 0;

    rows.forEach(function(row) {
        var module = row.getAttribute('data-module') || '';
        var show = (filter === 'all') ? true : (module === filter);

        // The details row immediately follows each data row
        var detailsRow = row.nextElementSibling;
        var hasDetails = detailsRow && detailsRow.classList.contains('audit-details-row');

        if (show) {
            row.classList.remove('hidden-row');
            num++;
            var numCell = row.querySelector('.audit-row-num');
            if (numCell) numCell.textContent = num;
            visibleCount++;
            // Keep details row visibility as-is (user may have toggled it)
        } else {
            row.classList.add('hidden-row');
            if (hasDetails) {
                detailsRow.classList.add('hidden-row');
                detailsRow.style.display = 'none';
                // Reset toggle button state
                var btn2 = row.querySelector('.audit-details-btn');
                if (btn2) btn2.classList.remove('active');
            }
        }
    });

    var noResults = document.getElementById('auditNoResults');
    var emptyRow  = document.querySelector('.audit-empty-row');
    if (visibleCount === 0 && !emptyRow) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}

function toggleAuditDetails(btn) {
    var dataRow = btn.closest('tr.audit-row');
    var detailsRow = dataRow ? dataRow.nextElementSibling : null;

    if (!detailsRow || !detailsRow.classList.contains('audit-details-row')) return;

    var isVisible = detailsRow.style.display !== 'none' && !detailsRow.classList.contains('hidden-row');

    if (isVisible) {
        detailsRow.style.display = 'none';
        btn.classList.remove('active');
    } else {
        detailsRow.style.display = 'table-row';
        detailsRow.classList.remove('hidden-row');
        btn.classList.add('active');
    }
}
</script>
@endpush
