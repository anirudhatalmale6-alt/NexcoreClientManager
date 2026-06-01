@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', 'CIPC Returns - ' . $client->company_name)
@section('page_heading', 'CIPC RETURNS')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(59,130,246,0.15), rgba(59,130,246,0.05)); border:1px solid rgba(59,130,246,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-clipboard-check" style="color:var(--accent-blue); font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">CIPC Returns</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; gap:8px;">
            <a href="{{ route('nexcore.clients.show.cipc.create', $client->id) }}" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-plus"></i> New Return</a>
        </div>
    </div>
</div>

<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Total Returns</div>
        <div class="sl-stat-value" style="color:var(--accent-green);">{{ $returns->count() }}</div>
        <div class="sl-stat-meta">All CIPC filings</div>
    </div>
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">Submitted</div>
        <div class="sl-stat-value" style="color:var(--accent-blue);">{{ $returns->filter(fn($r) => $r->submission_date)->count() }}</div>
        <div class="sl-stat-meta">Filed with CIPC</div>
    </div>
    <div class="sl-stat-card amber">
        <div class="sl-stat-label">Pending</div>
        <div class="sl-stat-value" style="color:var(--accent-amber);">{{ $returns->filter(fn($r) => !$r->submission_date)->count() }}</div>
        <div class="sl-stat-meta">Awaiting submission</div>
    </div>
    <div class="sl-stat-card" style="border-color:var(--accent-red);">
        <div class="sl-stat-label">Overdue</div>
        <div class="sl-stat-value" style="color:var(--accent-red);">{{ $returns->filter(fn($r) => $r->due_date && $r->due_date->isPast() && !$r->submission_date)->count() }}</div>
        <div class="sl-stat-meta">Past due date</div>
    </div>
</div>

{{-- Tab Navigation --}}
<div class="sl-card sl-animate d3" style="margin-bottom:0; border-bottom:none; border-radius:var(--radius-md) var(--radius-md) 0 0;">
    <div style="display:flex; gap:0; border-bottom:2px solid var(--border-subtle); padding:0 4px;">
        <button class="cipc-tab active" data-filter="all" onclick="filterCipc('all', this)">
            <i class="fas fa-layer-group"></i> All Returns
            <span class="cipc-tab-count">{{ $returns->count() }}</span>
        </button>
        <button class="cipc-tab" data-filter="AR" onclick="filterCipc('AR', this)">
            <i class="fas fa-calendar-check"></i> Annual Return
            <span class="cipc-tab-count">{{ $returns->filter(fn($r) => $r->returnType && $r->returnType->code === 'AR')->count() }}</span>
        </button>
        <button class="cipc-tab" data-filter="CoR" onclick="filterCipc('CoR', this)">
            <i class="fas fa-file-contract"></i> CoR Filings
            <span class="cipc-tab-count">{{ $returns->filter(fn($r) => $r->returnType && str_starts_with($r->returnType->code, 'CoR'))->count() }}</span>
        </button>
        <button class="cipc-tab" data-filter="BEE" onclick="filterCipc('BEE', this)">
            <i class="fas fa-certificate"></i> BEE Certificate
            <span class="cipc-tab-count">{{ $returns->filter(fn($r) => $r->returnType && $r->returnType->code === 'BEE')->count() }}</span>
        </button>
        <button class="cipc-tab" data-filter="OTHER" onclick="filterCipc('OTHER', this)">
            <i class="fas fa-ellipsis-h"></i> Other
            <span class="cipc-tab-count">{{ $returns->filter(fn($r) => $r->returnType && !in_array($r->returnType->code, ['AR','BEE']) && !str_starts_with($r->returnType->code, 'CoR'))->count() }}</span>
        </button>
    </div>
</div>

{{-- Table --}}
<div class="sl-card" style="border-radius:0 0 var(--radius-md) var(--radius-md); margin-top:0;">
    <div class="sl-table-wrap">
        <table class="sl-table" id="cipcTable">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Return Type</th>
                    <th>Filing Year</th>
                    <th>Due Date</th>
                    <th>Submitted</th>
                    <th>Approval Date</th>
                    <th class="center">Status</th>
                    <th class="right">Amount Due</th>
                    <th class="right">Amount Paid</th>
                    <th>Reference</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returns as $idx => $return)
                <tr class="cipc-row" data-type-code="{{ $return->returnType->code ?? '' }}">
                    <td style="color:var(--text-muted);" class="cipc-row-num">{{ $idx + 1 }}</td>
                    <td>
                        <div style="font-weight:600; color:var(--text-primary);">{{ $return->returnType->name ?? '-' }}</div>
                        <code style="font-size:11px; color:var(--accent-cyan); background:rgba(6,182,212,0.1); padding:2px 6px; border-radius:4px;">{{ $return->returnType->code ?? '' }}</code>
                    </td>
                    <td style="font-family:var(--font-mono); font-size:14px; font-weight:600; color:var(--accent-amber);">{{ $return->filing_year }}</td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">{{ $return->due_date ? $return->due_date->format('j M Y') : '-' }}</td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">{{ $return->submission_date ? $return->submission_date->format('j M Y') : '-' }}</td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">{{ $return->approval_date ? $return->approval_date->format('j M Y') : '-' }}</td>
                    <td class="center">
                        @if($return->status)
                            @php
                                $statusColor = $return->status->color ?? 'muted';
                                $tagClass = match($statusColor) {
                                    'green' => 'sl-tag sl-tag-green',
                                    'red' => 'sl-tag sl-tag-red',
                                    'amber' => 'sl-tag sl-tag-amber',
                                    'blue' => 'sl-tag sl-tag-blue',
                                    'cyan' => 'sl-tag',
                                    default => 'sl-tag',
                                };
                                $tagStyle = match($statusColor) {
                                    'cyan' => 'background:rgba(6,182,212,0.15); color:#22d3ee; border:1px solid rgba(6,182,212,0.3);',
                                    'muted' => 'background:rgba(148,163,184,0.1); color:var(--text-muted); border:1px solid rgba(148,163,184,0.2);',
                                    default => '',
                                };
                            @endphp
                            <span class="{{ $tagClass }}" @if($tagStyle) style="{{ $tagStyle }}" @endif>{{ $return->status->name }}</span>
                        @else
                            <span style="color:var(--text-muted);">-</span>
                        @endif
                    </td>
                    <td class="right">
                        @if($return->amount_due)
                            <span style="font-family:var(--font-mono); font-size:14px; font-weight:700; color:var(--accent-green);">R {{ number_format($return->amount_due, 2) }}</span>
                        @else
                            <span style="color:var(--text-muted);">-</span>
                        @endif
                    </td>
                    <td class="right">
                        @if($return->amount_paid)
                            <span style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">R {{ number_format($return->amount_paid, 2) }}</span>
                        @else
                            <span style="color:var(--text-muted);">-</span>
                        @endif
                    </td>
                    <td><span style="font-family:var(--font-mono); font-size:12px; color:var(--accent-cyan);">{{ $return->reference_number ?? '-' }}</span></td>
                    <td class="center">
                        <div style="display:flex; gap:6px; justify-content:center;">
                            <a href="{{ route('nexcore.clients.show.cipc.edit', [$client->id, $return->id]) }}" style="color:var(--accent-blue); font-size:15px;" title="Edit"><i class="fas fa-pen"></i></a>
                            <form method="POST" action="{{ route('nexcore.clients.show.cipc.destroy', [$client->id, $return->id]) }}" style="display:inline;" onsubmit="return confirm('Delete this CIPC return?')">@csrf @method('DELETE')
                                <button type="submit" style="background:none; border:none; color:var(--accent-red); cursor:pointer; font-size:15px;" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="cipc-empty-row"><td colspan="11" style="text-align:center; padding:60px; color:var(--text-muted);">
                    <i class="fas fa-clipboard-check" style="font-size:40px; opacity:0.2; margin-bottom:16px; display:block;"></i>
                    <div style="font-size:16px; font-weight:600; margin-bottom:6px;">No CIPC returns yet</div>
                    <div style="font-size:13px;">Click "New Return" to add the first CIPC return for this client</div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div id="cipcNoResults" style="display:none; text-align:center; padding:48px; color:var(--text-muted);">
        <i class="fas fa-search" style="font-size:32px; opacity:0.2; margin-bottom:12px; display:block;"></i>
        <div style="font-size:15px; font-weight:600;">No returns in this category</div>
        <div style="font-size:13px; margin-top:4px;">Try a different tab or add a new return</div>
    </div>
</div>
@endsection

@push('styles')
<style>
.cipc-tab {
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
.cipc-tab:hover {
    color:var(--text-primary);
    background:rgba(255,255,255,0.02);
}
.cipc-tab.active {
    color:var(--accent-cyan);
    border-bottom-color:var(--accent-cyan);
}
.cipc-tab.active .cipc-tab-count {
    background:rgba(6,182,212,0.2);
    color:var(--accent-cyan);
}
.cipc-tab-count {
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
.cipc-tab i {
    font-size:12px;
    opacity:0.7;
}
.cipc-tab.active i {
    opacity:1;
}
.cipc-row.hidden-row {
    display:none;
}
</style>
@endpush

@push('scripts')
<script>
function filterCipc(filter, btn) {
    document.querySelectorAll('.cipc-tab').forEach(function(t) { t.classList.remove('active'); });
    btn.classList.add('active');

    var rows = document.querySelectorAll('.cipc-row');
    var visibleCount = 0;
    var num = 0;

    rows.forEach(function(row) {
        var code = row.getAttribute('data-type-code') || '';
        var show = false;

        if (filter === 'all') {
            show = true;
        } else if (filter === 'CoR') {
            show = code.indexOf('CoR') === 0;
        } else if (filter === 'OTHER') {
            show = code !== 'AR' && code !== 'BEE' && code.indexOf('CoR') !== 0;
        } else {
            show = code === filter;
        }

        if (show) {
            row.classList.remove('hidden-row');
            num++;
            row.querySelector('.cipc-row-num').textContent = num;
            visibleCount++;
        } else {
            row.classList.add('hidden-row');
        }
    });

    var noResults = document.getElementById('cipcNoResults');
    var emptyRow = document.querySelector('.cipc-empty-row');
    if (visibleCount === 0 && !emptyRow) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}
</script>
@endpush
