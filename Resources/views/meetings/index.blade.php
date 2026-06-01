@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', 'Meetings - ' . $client->company_name)
@section('page_heading', 'MEETINGS')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(37,99,235,0.15), rgba(37,99,235,0.05)); border:1px solid rgba(37,99,235,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-calendar-alt" style="color:#2563eb; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Meetings</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; gap:8px;">
            <a href="{{ route('nexcore.clients.show.meetings.create', $client->id) }}" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-plus"></i> New Meeting</a>
        </div>
    </div>
</div>

@php
    $today = now()->startOfDay();
    $totalMeetings  = $meetings->count();
    $scheduled      = $meetings->where('meeting_status', 'scheduled')->count();
    $completed      = $meetings->where('meeting_status', 'completed')->count();
    $followUpsDue   = $meetings->filter(fn($m) => $m->follow_up_date && $m->follow_up_date->lt($today) && $m->meeting_status === 'completed')->count();
@endphp

<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">Total Meetings</div>
        <div class="sl-stat-value" style="color:#2563eb;">{{ $totalMeetings }}</div>
        <div class="sl-stat-meta">All recorded meetings</div>
    </div>
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Scheduled</div>
        <div class="sl-stat-value" style="color:var(--accent-green);">{{ $scheduled }}</div>
        <div class="sl-stat-meta">Upcoming meetings</div>
    </div>
    <div class="sl-stat-card" style="border-color:var(--accent-cyan);">
        <div class="sl-stat-label">Completed</div>
        <div class="sl-stat-value" style="color:var(--accent-cyan);">{{ $completed }}</div>
        <div class="sl-stat-meta">Held meetings</div>
    </div>
    <div class="sl-stat-card amber">
        <div class="sl-stat-label">Follow-ups Due</div>
        <div class="sl-stat-value" style="color:var(--accent-amber);">{{ $followUpsDue }}</div>
        <div class="sl-stat-meta">Overdue follow-ups</div>
    </div>
</div>

{{-- Tab Navigation --}}
<div class="sl-card sl-animate d3" style="margin-bottom:0; border-bottom:none; border-radius:var(--radius-md) var(--radius-md) 0 0;">
    <div style="display:flex; gap:0; border-bottom:2px solid var(--border-subtle); padding:0 4px;">
        <button class="meeting-tab active" data-filter="all" onclick="filterMeetings('all', this)">
            <i class="fas fa-layer-group"></i> All Meetings
            <span class="meeting-tab-count">{{ $meetings->count() }}</span>
        </button>
        <button class="meeting-tab" data-filter="scheduled" onclick="filterMeetings('scheduled', this)">
            <i class="fas fa-calendar-check"></i> Scheduled
            <span class="meeting-tab-count">{{ $meetings->where('meeting_status', 'scheduled')->count() }}</span>
        </button>
        <button class="meeting-tab" data-filter="completed" onclick="filterMeetings('completed', this)">
            <i class="fas fa-check-circle"></i> Completed
            <span class="meeting-tab-count">{{ $meetings->where('meeting_status', 'completed')->count() }}</span>
        </button>
        <button class="meeting-tab" data-filter="cancelled" onclick="filterMeetings('cancelled', this)">
            <i class="fas fa-times-circle"></i> Cancelled
            <span class="meeting-tab-count">{{ $meetings->where('meeting_status', 'cancelled')->count() }}</span>
        </button>
        <button class="meeting-tab" data-filter="postponed" onclick="filterMeetings('postponed', this)">
            <i class="fas fa-pause-circle"></i> Postponed
            <span class="meeting-tab-count">{{ $meetings->where('meeting_status', 'postponed')->count() }}</span>
        </button>
    </div>
</div>

{{-- Table --}}
<div class="sl-card" style="border-radius:0 0 var(--radius-md) var(--radius-md); margin-top:0;">
    <div class="sl-table-wrap">
        <table class="sl-table" id="meetingsTable">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Title</th>
                    <th>Date &amp; Time</th>
                    <th>Duration</th>
                    <th>Location</th>
                    <th>Attendees</th>
                    <th class="center">Status</th>
                    <th>Follow-up</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($meetings as $idx => $meeting)
                @php
                    $typeTags = [
                        'in_person'  => ['label' => 'In Person',        'style' => 'background:rgba(34,197,94,0.12); color:#4ade80; border:1px solid rgba(34,197,94,0.3);'],
                        'virtual'    => ['label' => 'Virtual / Online',  'style' => 'background:rgba(37,99,235,0.12); color:#60a5fa; border:1px solid rgba(37,99,235,0.3);'],
                        'phone'      => ['label' => 'Phone Call',        'style' => 'background:rgba(245,158,11,0.12); color:#fbbf24; border:1px solid rgba(245,158,11,0.3);'],
                        'site_visit' => ['label' => 'Site Visit',        'style' => 'background:rgba(139,92,246,0.12); color:#c084fc; border:1px solid rgba(139,92,246,0.3);'],
                    ];
                    $statusTags = [
                        'scheduled' => ['label' => 'Scheduled', 'style' => 'background:rgba(37,99,235,0.15); color:#60a5fa; border:1px solid rgba(37,99,235,0.3);'],
                        'completed' => ['label' => 'Completed', 'style' => 'background:rgba(34,197,94,0.15); color:#4ade80; border:1px solid rgba(34,197,94,0.3);'],
                        'cancelled' => ['label' => 'Cancelled', 'style' => 'background:rgba(239,68,68,0.15); color:#f87171; border:1px solid rgba(239,68,68,0.3);'],
                        'postponed' => ['label' => 'Postponed', 'style' => 'background:rgba(245,158,11,0.15); color:#fbbf24; border:1px solid rgba(245,158,11,0.3);'],
                    ];
                    $typeTag   = $typeTags[$meeting->meeting_type] ?? ['label' => $meeting->meeting_type, 'style' => ''];
                    $statusTag = $statusTags[$meeting->meeting_status] ?? ['label' => $meeting->meeting_status, 'style' => ''];
                    $durationHr  = $meeting->duration_minutes ? intdiv($meeting->duration_minutes, 60) : null;
                    $durationMin = $meeting->duration_minutes ? ($meeting->duration_minutes % 60) : null;
                    $followUpPast = $meeting->follow_up_date && $meeting->follow_up_date->lt($today);
                @endphp
                <tr class="meeting-row" data-meeting-status="{{ $meeting->meeting_status }}">
                    <td style="color:var(--text-muted);" class="meeting-row-num">{{ $idx + 1 }}</td>
                    <td>
                        <div style="font-weight:600; color:var(--text-primary); margin-bottom:4px;">{{ $meeting->title }}</div>
                        <span style="font-size:11px; padding:2px 8px; border-radius:10px; {{ $typeTag['style'] }}">{{ $typeTag['label'] }}</span>
                    </td>
                    <td>
                        <div style="font-family:var(--font-mono); font-size:13px; font-weight:600; color:var(--text-primary);">{{ $meeting->meeting_date->format('j M Y') }}</div>
                        @if($meeting->meeting_time)
                            <div style="font-family:var(--font-mono); font-size:12px; color:var(--text-muted); margin-top:2px;">{{ \Carbon\Carbon::createFromFormat('H:i', $meeting->meeting_time)->format('g:i A') }}</div>
                        @endif
                    </td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">
                        @if($meeting->duration_minutes)
                            @if($durationHr > 0 && $durationMin > 0)
                                {{ $durationHr }}hr {{ $durationMin }}min
                            @elseif($durationHr > 0)
                                {{ $durationHr }}hr
                            @else
                                {{ $durationMin }}min
                            @endif
                        @else
                            <span style="color:var(--text-muted);">-</span>
                        @endif
                    </td>
                    <td style="font-size:13px; color:var(--text-secondary); max-width:140px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                        {{ $meeting->location ?: '-' }}
                    </td>
                    <td style="font-size:12px; color:var(--text-muted); max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                        {{ $meeting->attendees ?: '-' }}
                    </td>
                    <td class="center">
                        <span style="font-size:12px; font-weight:600; padding:3px 10px; border-radius:10px; {{ $statusTag['style'] }}">{{ $statusTag['label'] }}</span>
                    </td>
                    <td>
                        @if($meeting->follow_up_date)
                            <span style="font-family:var(--font-mono); font-size:12px; {{ $followUpPast ? 'color:var(--accent-amber); font-weight:700;' : 'color:var(--text-secondary);' }}">
                                @if($followUpPast)<i class="fas fa-exclamation-circle" style="margin-right:4px;"></i>@endif
                                {{ $meeting->follow_up_date->format('j M Y') }}
                            </span>
                        @else
                            <span style="color:var(--text-muted);">-</span>
                        @endif
                    </td>
                    <td class="center">
                        <div style="display:flex; gap:6px; justify-content:center;">
                            <a href="{{ route('nexcore.clients.show.meetings.edit', [$client->id, $meeting->id]) }}" style="color:var(--accent-blue); font-size:15px;" title="Edit"><i class="fas fa-pen"></i></a>
                            <form method="POST" action="{{ route('nexcore.clients.show.meetings.destroy', [$client->id, $meeting->id]) }}" style="display:inline;" onsubmit="return confirm('Delete this meeting?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:none; border:none; color:var(--accent-red); cursor:pointer; font-size:15px;" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="meeting-empty-row">
                    <td colspan="9" style="text-align:center; padding:60px; color:var(--text-muted);">
                        <i class="fas fa-calendar-alt" style="font-size:40px; opacity:0.2; margin-bottom:16px; display:block;"></i>
                        <div style="font-size:16px; font-weight:600; margin-bottom:6px;">No meetings yet</div>
                        <div style="font-size:13px;">Click "New Meeting" to schedule the first meeting for this client</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div id="meetingNoResults" style="display:none; text-align:center; padding:48px; color:var(--text-muted);">
        <i class="fas fa-search" style="font-size:32px; opacity:0.2; margin-bottom:12px; display:block;"></i>
        <div style="font-size:15px; font-weight:600;">No meetings in this category</div>
        <div style="font-size:13px; margin-top:4px;">Try a different tab or add a new meeting</div>
    </div>
</div>
@endsection

@push('styles')
<style>
.meeting-tab {
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
.meeting-tab:hover {
    color:var(--text-primary);
    background:rgba(255,255,255,0.02);
}
.meeting-tab.active {
    color:#2563eb;
    border-bottom-color:#2563eb;
}
.meeting-tab.active .meeting-tab-count {
    background:rgba(37,99,235,0.2);
    color:#60a5fa;
}
.meeting-tab-count {
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
.meeting-tab i {
    font-size:12px;
    opacity:0.7;
}
.meeting-tab.active i {
    opacity:1;
}
.meeting-row.hidden-row {
    display:none;
}
</style>
@endpush

@push('scripts')
<script>
function filterMeetings(filter, btn) {
    document.querySelectorAll('.meeting-tab').forEach(function(t) { t.classList.remove('active'); });
    btn.classList.add('active');

    var rows = document.querySelectorAll('.meeting-row');
    var visibleCount = 0;
    var num = 0;

    rows.forEach(function(row) {
        var status = row.getAttribute('data-meeting-status') || '';
        var show = (filter === 'all') ? true : (status === filter);

        if (show) {
            row.classList.remove('hidden-row');
            num++;
            row.querySelector('.meeting-row-num').textContent = num;
            visibleCount++;
        } else {
            row.classList.add('hidden-row');
        }
    });

    var noResults = document.getElementById('meetingNoResults');
    var emptyRow  = document.querySelector('.meeting-empty-row');
    if (visibleCount === 0 && !emptyRow) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}
</script>
@endpush
