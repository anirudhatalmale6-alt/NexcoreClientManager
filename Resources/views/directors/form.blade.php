@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', (isset($director) ? 'Edit' : 'New') . ' Director - ' . $client->company_name)
@section('page_heading', isset($director) ? 'EDIT DIRECTOR' : 'NEW DIRECTOR')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(124,58,237,0.15), rgba(124,58,237,0.05)); border:1px solid rgba(124,58,237,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-user-tie" style="color:var(--accent-purple); font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">{{ isset($director) ? 'Edit Director' : 'New Director' }}</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.show.directors', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-arrow-left"></i> Back to Directors</a>
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

<form method="POST" enctype="multipart/form-data"
      action="{{ isset($director) ? route('nexcore.clients.show.directors.update', [$client->id, $director->id]) : route('nexcore.clients.show.directors.store', $client->id) }}">
    @csrf
    @if(isset($director)) @method('PUT') @endif

    <div class="sl-card sl-animate d2">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:var(--accent-purple);"><i class="fas fa-user"></i> Personal Information</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:120px 1fr 1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Title</label>
                    <select name="title_id" class="ncm-select2">
                        <option value="">--</option>
                        @foreach($titles as $t)
                            <option value="{{ $t->id }}" {{ old('title_id', $director->title_id ?? '') == $t->id ? 'selected' : '' }}>{{ $t->abbreviation ?: $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>First Name <span style="color:var(--accent-red);">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name', $director->first_name ?? '') }}" required>
                </div>
                <div class="sl-field">
                    <label>Last Name <span style="color:var(--accent-red);">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name', $director->last_name ?? '') }}" required>
                </div>
                <div class="sl-field">
                    <label>Director Type <span style="color:var(--accent-red);">*</span></label>
                    <select name="director_type_id" class="ncm-select2" required>
                        <option value="">-- Select Type --</option>
                        @foreach($directorTypes as $type)
                            <option value="{{ $type->id }}" {{ old('director_type_id', $director->director_type_id ?? '') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:20px; margin-top:20px;">
                <div class="sl-field">
                    <label>SA ID Number</label>
                    <input type="text" name="id_number" value="{{ old('id_number', $director->id_number ?? '') }}" style="font-family:var(--font-mono); color:var(--accent-cyan);" placeholder="13 digit ID">
                </div>
                <div class="sl-field">
                    <label>Passport Number</label>
                    <input type="text" name="passport_number" value="{{ old('passport_number', $director->passport_number ?? '') }}" style="font-family:var(--font-mono);" placeholder="For foreign nationals">
                </div>
                <div class="sl-field">
                    <label>Nationality</label>
                    <input type="text" name="nationality" value="{{ old('nationality', $director->nationality ?? 'South African') }}">
                </div>
                <div class="sl-field">
                    <label>Date of Birth</label>
                    <input type="text" name="date_of_birth" class="ncm-datepicker" value="{{ old('date_of_birth', isset($director) && $director->date_of_birth ? $director->date_of_birth->format('Y-m-d') : '') }}" placeholder="Select date..." readonly>
                </div>
            </div>
        </div>
    </div>

    <div class="sl-card sl-animate d3" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:var(--accent-cyan);"><i class="fas fa-phone-alt"></i> Contact Details</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $director->email ?? '') }}" style="color:var(--accent-cyan);">
                </div>
                <div class="sl-field">
                    <label>Mobile Number</label>
                    <input type="text" name="mobile_number" value="{{ old('mobile_number', $director->mobile_number ?? '') }}" style="font-family:var(--font-mono); color:var(--accent-green);">
                </div>
                <div class="sl-field">
                    <label>Office Number</label>
                    <input type="text" name="office_number" value="{{ old('office_number', $director->office_number ?? '') }}" style="font-family:var(--font-mono);">
                </div>
            </div>
            <div class="sl-field" style="margin-top:20px;">
                <label>Residential Address</label>
                <textarea name="residential_address" rows="2" style="width:100%;" placeholder="Full residential address...">{{ old('residential_address', $director->residential_address ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="sl-card sl-animate d4" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:var(--accent-amber);"><i class="fas fa-gavel"></i> Appointment & Shareholding</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Date of Appointment</label>
                    <input type="text" name="appointment_date" class="ncm-datepicker" value="{{ old('appointment_date', isset($director) && $director->appointment_date ? $director->appointment_date->format('Y-m-d') : '') }}" placeholder="Select date..." readonly>
                </div>
                <div class="sl-field">
                    <label>Date of Resignation</label>
                    <input type="text" name="resignation_date" class="ncm-datepicker" value="{{ old('resignation_date', isset($director) && $director->resignation_date ? $director->resignation_date->format('Y-m-d') : '') }}" placeholder="Leave blank if active" readonly>
                </div>
                <div class="sl-field">
                    <label>Shareholding %</label>
                    <input type="number" name="shareholding_percentage" step="0.01" min="0" max="100" value="{{ old('shareholding_percentage', $director->shareholding_percentage ?? '') }}" style="font-family:var(--font-mono); font-size:18px; font-weight:700; color:var(--accent-amber);" placeholder="0.00">
                </div>
            </div>
        </div>
    </div>

    <div class="sl-card sl-animate d5" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title"><i class="fas fa-camera"></i> Photo & Notes</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Director Photo</label>
                    @if(isset($director) && $director->director_photo)
                        <div style="margin-bottom:12px; display:flex; align-items:center; gap:12px;">
                            <img src="{{ asset($director->director_photo) }}" style="width:60px; height:60px; object-fit:cover; border-radius:50%; border:2px solid var(--border-subtle);">
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer; color:var(--accent-red); font-size:13px;">
                                <input type="checkbox" name="remove_photo" value="1" style="accent-color:var(--accent-red);"> Remove photo
                            </label>
                        </div>
                    @endif
                    <input type="file" name="director_photo" accept="image/*" style="font-size:13px;">
                </div>
                <div class="sl-field">
                    <label>Notes</label>
                    <textarea name="notes" rows="3" placeholder="Optional notes..." style="width:100%;">{{ old('notes', $director->notes ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="sl-animate d6" style="margin-top:24px; display:flex; gap:12px;">
        <button type="submit" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-save"></i> {{ isset($director) ? 'Update Director' : 'Save Director' }}</button>
        <a href="{{ route('nexcore.clients.show.directors', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-times"></i> Cancel</a>
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
.select2-results__option--highlighted { background:var(--accent-cyan) !important; color:#fff !important; }
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
