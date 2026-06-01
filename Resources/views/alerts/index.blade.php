@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', 'Alerts - ' . $client->company_name)
@section('page_heading', 'ALERTS')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(239,68,68,0.15), rgba(239,68,68,0.05)); border:1px solid rgba(239,68,68,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-bell" style="color:#ef4444; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Alerts</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; gap:8px;">
            <a href="{{ route('nexcore.clients.show.alerts.create', $client->id) }}" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-plus"></i> New Alert</a>
        </div>
    </div>
</div>

@php
    $today         = \Carbon\Carbon::today();
    $totalCount    = $alerts->count();
    $criticalCount = $alerts->filter(fn($a) => $a->severity === 'critical')->count();
    $warningCount  = $alerts->filter(fn($a) => $a->severity === 'warning')->count();
    $unreadCount   = $alerts->filter(fn($a) => !$a->is_read)->count();
    $dismissedCount = $alerts->filter(fn($a) => $a->is_dismissed)->count();
@endphp

<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card" style="border-color:rgba(239,68,68,0.4);">
        <div class="sl-stat-label">Total Alerts</div>
        <div class="sl-stat-value" style="color:#ef4444;">{{ $totalCount }}</div>
        <div class="sl-stat-meta">All alerts</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(239,68,68,0.4);">
        <div class="sl-stat-label">Critical</div>
        <div class="sl-stat-value" style="color:var(--accent-red);">{{ $criticalCount }}</div>
        <div class="sl-stat-meta">Requires immediate action</div>
    </div>
    <div class="sl-stat-card amber">
        <div class="sl-stat-label">Warnings</div>
        <div class="sl-stat-value" style="color:var(--accent-amber);">{{ $warningCount }}</div>
        <div class="sl-stat-meta">Needs attention</div>
    </div>
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">Unread</div>
        <div class="sl-stat-value" style="color:var(--accent-blue);">{{ $unreadCount }}</div>
        <div class="sl-stat-meta">Not yet read</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(100,116,139,0.3);">
        <div class="sl-stat-label">Dismissed</div>
        <div class="sl-stat-value" style="color:var(--text-muted);">{{ $dismissedCount }}</div>
        <div class="sl-stat-meta">Cleared alerts</div>
    </div>
</div>

{{-- Tab Navigation --}}
<div class="sl-card sl-animate d3" style="margin-bottom:0; border-bottom:none; border-radius:var(--radius-md) var(--radius-md) 0 0;">
    <div style="display:flex; gap:0; border-bottom:2px solid var(--border-subtle); padding:0 4px; flex-wrap:wrap;">
        <button class="alert-tab active" data-filter="all" onclick="filterAlerts('all', this)">
            <i class="fas fa-layer-group"></i> All Alerts
            <span class="alert-tab-count">{{ $totalCount }}</span>
        </button>
        <button class="alert-tab" data-filter="critical" onclick="filterAlerts('critical', this)">
            <i class="fas fa-exclamation-circle"></i> Critical
            <span class="alert-tab-count" style="background:rgba(239,68,68,0.15); color:var(--accent-red);">{{ $criticalCount }}</span>
        </button>
        <button class="alert-tab" data-filter="warning" onclick="filterAlerts('warning', this)">
            <i class="fas fa-exclamation-triangle"></i> Warnings
            <span class="alert-tab-count" style="background:rgba(217,119,6,0.15); color:var(--accent-amber);">{{ $warningCount }}</span>
        </button>
        <button class="alert-tab" data-filter="info" onclick="filterAlerts('info', this)">
            <i class="fas fa-info-circle"></i> Info
            <span class="alert-tab-count">{{ $alerts->filter(fn($a) => $a->severity === 'info')->count() }}</span>
        </button>
        <button class="alert-tab" data-filter="unread" onclick="filterAlerts('unread', this)">
            <i class="fas fa-envelope"></i> Unread
            <span class="alert-tab-count" style="background:rgba(59,130,246,0.15); color:var(--accent-blue);">{{ $unreadCount }}</span>
        </button>
        <button class="alert-tab" data-filter="dismissed" onclick="filterAlerts('dismissed', this)">
            <i class="fas fa-times-circle"></i> Dismissed
            <span class="alert-tab-count">{{ $dismissedCount }}</span>
        </button>
    </div>
</div>

{{-- Table --}}
<div class="sl-card" style="border-radius:0 0 var(--radius-md) var(--radius-md); margin-top:0;">
    <div class="sl-table-wrap">
        <table class="sl-table" id="alertsTable">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="width:110px;">Severity</th>
                    <th>Title</th>
                    <th style="width:130px;">Module</th>
                    <th style="width:120px;">Due Date</th>
                    <th class="center" style="width:70px;">Read</th>
                    <th class="center" style="width:160px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alerts as $idx => $alert)
                @php
                    $isPast = $alert->due_date && $alert->due_date->lt($today);
                @endphp
                <tr class="alert-row{{ $alert->is_dismissed ? ' alert-dismissed' : '' }}"
                    data-severity="{{ $alert->severity }}"
                    data-read="{{ $alert->is_read ? '1' : '0' }}"
                    data-dismissed="{{ $alert->is_dismissed ? '1' : '0' }}">
                    <td style="color:var(--text-muted);" class="alert-row-num">{{ $idx + 1 }}</td>
                    <td>
                        @switch($alert->severity)
                            @case('critical')
                                <span class="sl-tag sl-tag-red"><i class="fas fa-exclamation-circle" style="margin-right:4px;"></i>Critical</span>
                                @break
                            @case('warning')
                                <span class="sl-tag sl-tag-amber"><i class="fas fa-exclamation-triangle" style="margin-right:4px;"></i>Warning</span>
                                @break
                            @case('info')
                                <span class="sl-tag sl-tag-blue"><i class="fas fa-info-circle" style="margin-right:4px;"></i>Info</span>
                                @break
                            @default
                                <span class="sl-tag">{{ ucfirst($alert->severity) }}</span>
                        @endswitch
                    </td>
                    <td>
                        <div style="font-weight:600; color:var(--text-primary);">{{ $alert->title }}</div>
                        @if($alert->description)
                            <div style="font-size:12px; color:var(--text-muted); margin-top:2px; line-height:1.4;">{{ \Illuminate\Support\Str::limit($alert->description, 90) }}</div>
                        @endif
                    </td>
                    <td>
                        @if($alert->related_module)
                            <span class="sl-tag" style="background:rgba(100,116,139,0.12); color:var(--text-secondary); border:1px solid var(--border-subtle);">
                                {{ $modules[$alert->related_module] ?? ucfirst($alert->related_module) }}
                            </span>
                        @else
                            <span style="color:var(--text-muted);">-</span>
                        @endif
                    </td>
                    <td>
                        @if($alert->due_date)
                            <span style="font-family:var(--font-mono); font-size:13px; {{ $isPast ? 'color:var(--accent-red); font-weight:600;' : 'color:var(--text-secondary);' }}">
                                {{ $alert->due_date->format('j M Y') }}
                                @if($isPast)
                                    <i class="fas fa-exclamation-triangle" style="font-size:11px; margin-left:4px;"></i>
                                @endif
                            </span>
                        @else
                            <span style="color:var(--text-muted);">-</span>
                        @endif
                    </td>
                    <td class="center">
                        @if($alert->is_read)
                            <span title="Read" style="display:inline-block; width:10px; height:10px; border-radius:50%; background:var(--accent-green);"></span>
                        @else
                            <span title="Unread" style="display:inline-block; width:10px; height:10px; border-radius:50%; background:rgba(100,116,139,0.3); border:1px solid var(--border-subtle);"></span>
                        @endif
                    </td>
                    <td class="center">
                        <div style="display:flex; gap:5px; justify-content:center; align-items:center; flex-wrap:wrap;">
                            {{-- Toggle Read --}}
                            <form method="POST" action="{{ route('nexcore.clients.show.alerts.toggle-read', [$client->id, $alert->id]) }}" style="display:inline;">
                                @csrf
                                <button type="submit" style="background:none; border:none; cursor:pointer; font-size:14px; padding:2px 4px;" title="{{ $alert->is_read ? 'Mark Unread' : 'Mark Read' }}" class="{{ $alert->is_read ? 'action-icon-blue' : 'action-icon-muted' }}">
                                    <i class="fas {{ $alert->is_read ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                </button>
                            </form>
                            {{-- Dismiss --}}
                            @if(!$alert->is_dismissed)
                            <form method="POST" action="{{ route('nexcore.clients.show.alerts.dismiss', [$client->id, $alert->id]) }}" style="display:inline;" onsubmit="return confirm('Dismiss this alert?')">
                                @csrf
                                <button type="submit" style="background:none; border:none; cursor:pointer; font-size:14px; padding:2px 4px; color:var(--text-muted);" title="Dismiss Alert">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </form>
                            @endif
                            {{-- Edit --}}
                            <a href="{{ route('nexcore.clients.show.alerts.edit', [$client->id, $alert->id]) }}" style="color:var(--accent-blue); font-size:14px; padding:2px 4px;" title="Edit"><i class="fas fa-pen"></i></a>
                            {{-- Delete --}}
                            <form method="POST" action="{{ route('nexcore.clients.show.alerts.destroy', [$client->id, $alert->id]) }}" style="display:inline;" onsubmit="return confirm('Delete this alert?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:none; border:none; color:var(--accent-red); cursor:pointer; font-size:14px; padding:2px 4px;" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="alert-empty-row">
                    <td colspan="7" style="text-align:center; padding:60px; color:var(--text-muted);">
                        <i class="fas fa-bell" style="font-size:40px; opacity:0.2; margin-bottom:16px; display:block;"></i>
                        <div style="font-size:16px; font-weight:600; margin-bottom:6px;">No alerts yet</div>
                        <div style="font-size:13px;">Click "New Alert" to add the first alert for this client</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div id="alertNoResults" style="display:none; text-align:center; padding:48px; color:var(--text-muted);">
        <i class="fas fa-search" style="font-size:32px; opacity:0.2; margin-bottom:12px; display:block;"></i>
        <div style="font-size:15px; font-weight:600;">No alerts in this category</div>
        <div style="font-size:13px; margin-top:4px;">Try a different tab or add a new alert</div>
    </div>
</div>
@endsection

@push('styles')
<style>
.alert-tab {
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
.alert-tab:hover {
    color:var(--text-primary);
    background:rgba(255,255,255,0.02);
}
.alert-tab.active {
    color:#ef4444;
    border-bottom-color:#ef4444;
}
.alert-tab.active .alert-tab-count {
    background:rgba(239,68,68,0.2);
    color:#ef4444;
}
.alert-tab-count {
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
.alert-tab i {
    font-size:12px;
    opacity:0.7;
}
.alert-tab.active i {
    opacity:1;
}
.alert-row.hidden-row {
    display:none;
}
.alert-dismissed {
    opacity:0.6;
}
.action-icon-blue {
    color:var(--accent-blue);
}
.action-icon-muted {
    color:var(--text-muted);
}
</style>
@endpush

@push('scripts')
<script>
function filterAlerts(filter, btn) {
    document.querySelectorAll('.alert-tab').forEach(function(t) { t.classList.remove('active'); });
    btn.classList.add('active');

    var rows = document.querySelectorAll('.alert-row');
    var visibleCount = 0;
    var num = 0;

    rows.forEach(function(row) {
        var severity  = row.getAttribute('data-severity') || '';
        var isRead    = row.getAttribute('data-read') === '1';
        var isDismissed = row.getAttribute('data-dismissed') === '1';
        var show = false;

        if (filter === 'all') {
            show = true;
        } else if (filter === 'unread') {
            show = !isRead;
        } else if (filter === 'dismissed') {
            show = isDismissed;
        } else {
            show = severity === filter;
        }

        if (show) {
            row.classList.remove('hidden-row');
            num++;
            var numCell = row.querySelector('.alert-row-num');
            if (numCell) numCell.textContent = num;
            visibleCount++;
        } else {
            row.classList.add('hidden-row');
        }
    });

    var noResults = document.getElementById('alertNoResults');
    var emptyRow  = document.querySelector('.alert-empty-row');
    if (visibleCount === 0 && !emptyRow) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}
</script>
@endpush
