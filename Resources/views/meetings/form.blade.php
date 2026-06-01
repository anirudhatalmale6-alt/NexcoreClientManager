@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', (isset($meeting) ? 'Edit' : 'New') . ' Meeting - ' . $client->company_name)
@section('page_heading', isset($meeting) ? 'EDIT MEETING' : 'NEW MEETING')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(37,99,235,0.15), rgba(37,99,235,0.05)); border:1px solid rgba(37,99,235,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-calendar-alt" style="color:#2563eb; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">{{ isset($meeting) ? 'Edit Meeting' : 'New Meeting' }}</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.show.meetings', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-arrow-left"></i> Back to Meetings</a>
        </div>
    </div>
</div>

@if($errors->any())
<div class="sl-verdict reject sl-mb-md sl-animate d2" style="padding:14px 20px;">
    <div class="sl-verdict-icon" style="width:32px;height:32px;font-size:16px;"><i class="fas fa-exclamation-triangle"></i></div>
    <div>
        <div class="sl-verdict-text" style="font-size:15px;">Please correct the following errors:</div>
        <ul style="margin:6px 0 0; padding-left:20px; font-size:13px; color:var(--text-secondary);">
            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
</div>
@endif

<form method="POST"
      action="{{ isset($meeting) ? route('nexcore.clients.show.meetings.update', [$client->id, $meeting->id]) : route('nexcore.clients.show.meetings.store', $client->id) }}">
    @csrf
    @if(isset($meeting)) @method('PUT') @endif

    {{-- Card 1: Meeting Details --}}
    <div class="sl-card sl-animate d2">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#2563eb;"><i class="fas fa-calendar-alt"></i> Meeting Details</div>
        </div>
        <div style="padding:24px;">
            <div class="sl-field" style="margin-bottom:20px;">
                <label>Title <span style="color:var(--accent-red);">*</span></label>
                <input type="text" name="title" value="{{ old('title', $meeting->title ?? '') }}" required placeholder="e.g. Quarterly Review, On-boarding Discussion">
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Meeting Type <span style="color:var(--accent-red);">*</span></label>
                    <select name="meeting_type" class="ncm-select2" required>
                        <option value="">-- Select Type --</option>
                        @foreach($meetingTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('meeting_type', $meeting->meeting_type ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>Status <span style="color:var(--accent-red);">*</span></label>
                    <select name="meeting_status" class="ncm-select2" required>
                        <option value="">-- Select Status --</option>
                        @foreach($meetingStatuses as $value => $label)
                            <option value="{{ $value }}" {{ old('meeting_status', $meeting->meeting_status ?? 'scheduled') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="sl-field" style="margin-top:20px;">
                <label>Description</label>
                <textarea name="description" rows="3" placeholder="Brief description or agenda for this meeting..." style="width:100%;">{{ old('description', $meeting->description ?? '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Card 2: Schedule --}}
    <div class="sl-card sl-animate d3" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:var(--accent-cyan);"><i class="fas fa-clock"></i> Schedule</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Meeting Date <span style="color:var(--accent-red);">*</span></label>
                    <input type="text" name="meeting_date" class="ncm-datepicker" value="{{ old('meeting_date', isset($meeting) && $meeting->meeting_date ? $meeting->meeting_date->format('Y-m-d') : '') }}" placeholder="Select date..." readonly required>
                </div>
                <div class="sl-field">
                    <label>Meeting Time</label>
                    <input type="time" name="meeting_time" class="ncm-time-input" value="{{ old('meeting_time', $meeting->meeting_time ?? '') }}" placeholder="HH:MM">
                </div>
                <div class="sl-field">
                    <label>Duration (minutes)</label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $meeting->duration_minutes ?? '') }}" min="1" step="1" placeholder="e.g. 60">
                </div>
            </div>
            <div class="sl-field" style="margin-top:20px;">
                <label>Location</label>
                <input type="text" name="location" value="{{ old('location', $meeting->location ?? '') }}" placeholder="e.g. Boardroom A, Zoom Meeting, Client Office">
            </div>
        </div>
    </div>

    {{-- Card 3: Attendees & Outcome --}}
    <div class="sl-card sl-animate d4" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:var(--accent-green);"><i class="fas fa-users"></i> Attendees &amp; Outcome</div>
        </div>
        <div style="padding:24px;">
            <div class="sl-field" style="margin-bottom:20px;">
                <label>Attendees</label>
                <textarea name="attendees" rows="2" placeholder="Comma separated names, e.g. John Smith, Jane Doe, Bob Johnson" style="width:100%;">{{ old('attendees', $meeting->attendees ?? '') }}</textarea>
                <span style="font-size:11px; color:var(--text-muted); margin-top:4px; display:block;"><i class="fas fa-info-circle"></i> Comma separated names</span>
            </div>
            <div class="sl-field">
                <label>Outcome</label>
                <textarea name="outcome" rows="3" placeholder="Summary of what was discussed and decided in this meeting..." style="width:100%;">{{ old('outcome', $meeting->outcome ?? '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Card 4: Follow-up --}}
    <div class="sl-card sl-animate d5" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:var(--accent-amber);"><i class="fas fa-bell"></i> Follow-up</div>
        </div>
        <div style="padding:24px;">
            <div class="sl-field" style="margin-bottom:20px;">
                <label>Follow-up Date</label>
                <input type="text" name="follow_up_date" class="ncm-datepicker" value="{{ old('follow_up_date', isset($meeting) && $meeting->follow_up_date ? $meeting->follow_up_date->format('Y-m-d') : '') }}" placeholder="Select date..." readonly>
            </div>
            <div class="sl-field" style="margin-bottom:20px;">
                <label>Follow-up Notes</label>
                <textarea name="follow_up_notes" rows="3" placeholder="Action items or tasks to follow up after this meeting..." style="width:100%;">{{ old('follow_up_notes', $meeting->follow_up_notes ?? '') }}</textarea>
            </div>
            <div class="sl-field">
                <label>Internal Notes</label>
                <textarea name="notes" rows="3" placeholder="Optional internal notes about this meeting..." style="width:100%;">{{ old('notes', $meeting->notes ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="sl-animate d6" style="margin-top:24px; display:flex; gap:12px;">
        <button type="submit" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-save"></i> {{ isset($meeting) ? 'Update Meeting' : 'Save Meeting' }}</button>
        <a href="{{ route('nexcore.clients.show.meetings', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-times"></i> Cancel</a>
    </div>
</form>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css" rel="stylesheet">
<style>
.select2-container--default .select2-selection--single { background:var(--bg-raised) !important; border:1px solid var(--border-default) !important; border-radius:var(--radius-sm) !important; height:42px !important; }
.select2-container--default .select2-selection--single .select2-selection__rendered { color:var(--text-primary) !important; line-height:42px !important; padding-left:12px !important; font-size:15px !important; }
.select2-container--default .select2-selection--single .select2-selection__arrow { height:42px !important; }
.select2-dropdown { background:var(--bg-surface) !important; border:1px solid var(--border-default) !important; border-radius:var(--radius-sm) !important; }
.select2-search--dropdown .select2-search__field { background:var(--bg-raised) !important; border:1px solid var(--border-default) !important; color:var(--text-primary) !important; border-radius:var(--radius-sm) !important; padding:8px 12px !important; }
.select2-results__option { color:var(--text-secondary) !important; padding:10px 14px !important; font-size:14px !important; }
.select2-results__option--highlighted { background:#2563eb !important; color:#fff !important; }
.select2-container--default .select2-selection--single .select2-selection__placeholder { color:var(--text-muted) !important; }
.flatpickr-calendar { background:var(--bg-surface) !important; border:1px solid var(--border-default) !important; }
.ncm-time-input {
    background: var(--bg-raised) !important;
    border: 1px solid var(--border-default) !important;
    border-radius: var(--radius-sm) !important;
    color: var(--text-primary) !important;
    height: 42px !important;
    padding: 0 12px !important;
    font-size: 15px !important;
    width: 100%;
    box-sizing: border-box;
    font-family: var(--font-mono);
    outline: none;
    transition: border-color 0.2s ease;
}
.ncm-time-input:focus {
    border-color: #2563eb !important;
    box-shadow: 0 0 0 2px rgba(37,99,235,0.15) !important;
}
.ncm-time-input::-webkit-calendar-picker-indicator {
    filter: invert(0.7) brightness(1.2);
    cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
$(function() {
    $('.ncm-select2').select2({ width: '100%', placeholder: '-- Select --', allowClear: true });
    $('.ncm-datepicker').flatpickr({
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'j M Y',
        theme: 'dark',
        allowInput: false
    });
});
</script>
@endpush
