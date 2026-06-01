@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', (isset($alert) ? 'Edit' : 'New') . ' Alert - ' . $client->company_name)
@section('page_heading', isset($alert) ? 'EDIT ALERT' : 'NEW ALERT')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(239,68,68,0.15), rgba(239,68,68,0.05)); border:1px solid rgba(239,68,68,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-bell" style="color:#ef4444; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">{{ isset($alert) ? 'Edit Alert' : 'Add Alert' }}</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.show.alerts', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-arrow-left"></i> Back to Alerts</a>
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
      action="{{ isset($alert) ? route('nexcore.clients.show.alerts.update', [$client->id, $alert->id]) : route('nexcore.clients.show.alerts.store', $client->id) }}">
    @csrf
    @if(isset($alert)) @method('PUT') @endif

    {{-- Card 1: Alert Details --}}
    <div class="sl-card sl-animate d2">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#ef4444;"><i class="fas fa-bell"></i> Alert Details</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr; gap:20px;">
                <div class="sl-field">
                    <label>Title <span style="color:var(--accent-red);">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $alert->title ?? '') }}" required placeholder="Enter alert title">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-top:20px;">
                <div class="sl-field">
                    <label>Alert Type <span style="color:var(--accent-red);">*</span></label>
                    <select name="alert_type" class="ncm-select2" required>
                        <option value="">-- Select Type --</option>
                        @foreach($alertTypes as $key => $label)
                            <option value="{{ $key }}" {{ old('alert_type', $alert->alert_type ?? 'manual') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>Severity <span style="color:var(--accent-red);">*</span></label>
                    <select name="severity" class="ncm-select2" required>
                        <option value="">-- Select Severity --</option>
                        @foreach($severities as $key => $label)
                            <option value="{{ $key }}" {{ old('severity', $alert->severity ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>Related Module</label>
                    <select name="related_module" class="ncm-select2">
                        <option value="">-- None --</option>
                        @foreach($modules as $key => $label)
                            <option value="{{ $key }}" {{ old('related_module', $alert->related_module ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 2: Schedule & Notes --}}
    <div class="sl-card sl-animate d3" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#ef4444;"><i class="fas fa-calendar-alt"></i> Schedule &amp; Notes</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Due Date</label>
                    <input type="text" name="due_date" class="ncm-datepicker" value="{{ old('due_date', isset($alert) && $alert->due_date ? $alert->due_date->format('Y-m-d') : '') }}" placeholder="Select date..." readonly>
                </div>
                <div class="sl-field">
                    <label>Description</label>
                    <textarea name="description" rows="3" placeholder="Optional description of this alert..." style="width:100%;">{{ old('description', $alert->description ?? '') }}</textarea>
                </div>
            </div>
            <div style="margin-top:20px;">
                <div class="sl-field">
                    <label>Notes</label>
                    <textarea name="notes" rows="3" placeholder="Optional internal notes..." style="width:100%;">{{ old('notes', $alert->notes ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="sl-animate d4" style="margin-top:24px; display:flex; gap:12px;">
        <button type="submit" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-save"></i> {{ isset($alert) ? 'Update Alert' : 'Save Alert' }}</button>
        <a href="{{ route('nexcore.clients.show.alerts', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-times"></i> Cancel</a>
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
.select2-results__option--highlighted { background:#ef4444 !important; color:#fff !important; }
.select2-container--default .select2-selection--single .select2-selection__placeholder { color:var(--text-muted) !important; }
.flatpickr-calendar { background:var(--bg-surface) !important; border:1px solid var(--border-default) !important; }
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
