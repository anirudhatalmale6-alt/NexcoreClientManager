@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', 'Pay Periods - ' . $client->company_name)
@section('page_heading', 'PAY PERIODS')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(13,148,136,0.15), rgba(13,148,136,0.05)); border:1px solid rgba(13,148,136,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-calendar-week" style="color:#0d9488; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Pay Periods</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; gap:8px;">
            <a href="{{ route('nexcore.clients.show.payroll.periods.create', $client->id) }}" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-plus"></i> New Pay Period</a>
        </div>
    </div>
</div>

@php
    $totalCount     = $periods->count();
    $draftCount     = $periods->filter(fn($p) => $p->status === 'draft')->count();
    $processedCount = $periods->filter(fn($p) => $p->status === 'processed')->count();
    $finalisedCount = $periods->filter(fn($p) => $p->status === 'finalised')->count();
@endphp

<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card" style="border-color:rgba(13,148,136,0.4);">
        <div class="sl-stat-label">Total Periods</div>
        <div class="sl-stat-value" style="color:#0d9488;">{{ $totalCount }}</div>
        <div class="sl-stat-meta">All pay periods</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(251,191,36,0.4);">
        <div class="sl-stat-label">Draft</div>
        <div class="sl-stat-value" style="color:var(--accent-amber);">{{ $draftCount }}</div>
        <div class="sl-stat-meta">Not yet processed</div>
    </div>
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">Processed</div>
        <div class="sl-stat-value" style="color:var(--accent-blue);">{{ $processedCount }}</div>
        <div class="sl-stat-meta">Awaiting finalisation</div>
    </div>
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Finalised</div>
        <div class="sl-stat-value" style="color:var(--accent-green);">{{ $finalisedCount }}</div>
        <div class="sl-stat-meta">Completed periods</div>
    </div>
</div>

{{-- Tab Navigation --}}
<div class="sl-card sl-animate d3" style="margin-bottom:0; border-bottom:none; border-radius:var(--radius-md) var(--radius-md) 0 0;">
    <div style="display:flex; gap:0; border-bottom:2px solid var(--border-subtle); padding:0 4px; flex-wrap:wrap;">
        <button class="period-tab active" data-filter="all" onclick="filterPeriods('all', this)">
            <i class="fas fa-layer-group"></i> All Periods
            <span class="period-tab-count">{{ $totalCount }}</span>
        </button>
        <button class="period-tab" data-filter="draft" onclick="filterPeriods('draft', this)">
            <i class="fas fa-file-alt"></i> Draft
            <span class="period-tab-count">{{ $draftCount }}</span>
        </button>
        <button class="period-tab" data-filter="processed" onclick="filterPeriods('processed', this)">
            <i class="fas fa-cogs"></i> Processed
            <span class="period-tab-count">{{ $processedCount }}</span>
        </button>
        <button class="period-tab" data-filter="finalised" onclick="filterPeriods('finalised', this)">
            <i class="fas fa-check-circle"></i> Finalised
            <span class="period-tab-count">{{ $finalisedCount }}</span>
        </button>
    </div>
</div>

{{-- Table --}}
<div class="sl-card" style="border-radius:0 0 var(--radius-md) var(--radius-md); margin-top:0;">
    <div class="sl-table-wrap">
        <table class="sl-table" id="periodsTable">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Period Name</th>
                    <th>Frequency</th>
                    <th>Period</th>
                    <th>Payment Date</th>
                    <th class="right">Gross Pay</th>
                    <th class="right">Net Pay</th>
                    <th class="center">Status</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($periods as $idx => $period)
                <tr class="period-row" data-period-status="{{ $period->status }}">
                    <td style="color:var(--text-muted);" class="period-row-num">{{ $idx + 1 }}</td>
                    <td>
                        <div style="font-weight:600; color:var(--text-primary);">{{ $period->name }}</div>
                        @if($period->notes)
                            <div style="font-size:12px; color:var(--text-muted); margin-top:2px; line-height:1.4;">{{ \Illuminate\Support\Str::limit($period->notes, 60) }}</div>
                        @endif
                    </td>
                    <td style="font-size:13px; color:var(--text-secondary);">
                        {{ $payFrequencies[$period->pay_frequency] ?? ucfirst($period->pay_frequency) }}
                    </td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">
                        {{ $period->period_start->format('j M Y') }} &ndash; {{ $period->period_end->format('j M Y') }}
                    </td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">
                        {{ $period->payment_date->format('j M Y') }}
                    </td>
                    <td class="right" style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">
                        R {{ number_format($period->total_gross, 2) }}
                    </td>
                    <td class="right" style="font-family:var(--font-mono); font-size:13px; color:var(--accent-green); font-weight:600;">
                        R {{ number_format($period->total_net, 2) }}
                    </td>
                    <td class="center">
                        @switch($period->status)
                            @case('draft')
                                <span class="sl-tag sl-tag-amber">Draft</span>
                                @break
                            @case('processed')
                                <span class="sl-tag sl-tag-blue">Processed</span>
                                @break
                            @case('finalised')
                                <span class="sl-tag sl-tag-green">Finalised</span>
                                @break
                            @default
                                <span class="sl-tag">{{ ucfirst($period->status) }}</span>
                        @endswitch
                    </td>
                    <td class="center">
                        <div style="display:flex; gap:6px; justify-content:center;">
                            <a href="{{ route('nexcore.clients.show.payroll.periods.edit', [$client->id, $period->id]) }}" style="color:var(--accent-blue); font-size:15px;" title="Edit"><i class="fas fa-pen"></i></a>
                            <form method="POST" action="{{ route('nexcore.clients.show.payroll.periods.destroy', [$client->id, $period->id]) }}" style="display:inline;" onsubmit="return confirm('Delete this pay period?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:none; border:none; color:var(--accent-red); cursor:pointer; font-size:15px;" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="period-empty-row">
                    <td colspan="9" style="text-align:center; padding:60px; color:var(--text-muted);">
                        <i class="fas fa-calendar-week" style="font-size:40px; opacity:0.2; margin-bottom:16px; display:block;"></i>
                        <div style="font-size:16px; font-weight:600; margin-bottom:6px;">No pay periods yet</div>
                        <div style="font-size:13px;">Click "New Pay Period" to create the first pay period for this client</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div id="periodNoResults" style="display:none; text-align:center; padding:48px; color:var(--text-muted);">
        <i class="fas fa-search" style="font-size:32px; opacity:0.2; margin-bottom:12px; display:block;"></i>
        <div style="font-size:15px; font-weight:600;">No pay periods in this category</div>
        <div style="font-size:13px; margin-top:4px;">Try a different tab or add a new pay period</div>
    </div>
</div>
@endsection

@push('styles')
<style>
.period-tab {
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
.period-tab:hover {
    color:var(--text-primary);
    background:rgba(255,255,255,0.02);
}
.period-tab.active {
    color:#0d9488;
    border-bottom-color:#0d9488;
}
.period-tab.active .period-tab-count {
    background:rgba(13,148,136,0.2);
    color:#0d9488;
}
.period-tab-count {
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
.period-tab i {
    font-size:12px;
    opacity:0.7;
}
.period-tab.active i {
    opacity:1;
}
.period-row.hidden-row {
    display:none;
}
th.right, td.right {
    text-align:right;
}
</style>
@endpush

@push('scripts')
<script>
function filterPeriods(filter, btn) {
    document.querySelectorAll('.period-tab').forEach(function(t) { t.classList.remove('active'); });
    btn.classList.add('active');

    var rows = document.querySelectorAll('.period-row');
    var visibleCount = 0;
    var num = 0;

    rows.forEach(function(row) {
        var status = row.getAttribute('data-period-status') || '';
        var show = (filter === 'all') ? true : (status === filter);

        if (show) {
            row.classList.remove('hidden-row');
            num++;
            var numCell = row.querySelector('.period-row-num');
            if (numCell) numCell.textContent = num;
            visibleCount++;
        } else {
            row.classList.add('hidden-row');
        }
    });

    var noResults = document.getElementById('periodNoResults');
    var emptyRow  = document.querySelector('.period-empty-row');
    if (visibleCount === 0 && !emptyRow) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}
</script>
@endpush
