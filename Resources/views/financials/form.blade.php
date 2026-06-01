@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', (isset($financial) ? 'Edit' : 'New') . ' Financial Record - ' . $client->company_name)
@section('page_heading', isset($financial) ? 'EDIT FINANCIAL RECORD' : 'NEW FINANCIAL RECORD')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(34,197,94,0.15), rgba(34,197,94,0.05)); border:1px solid rgba(34,197,94,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-chart-line" style="color:var(--accent-green); font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">{{ isset($financial) ? 'Edit Financial Record' : 'New Financial Record' }}</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.show.financials', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-arrow-left"></i> Back to Financials</a>
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
      action="{{ isset($financial) ? route('nexcore.clients.show.financials.update', [$client->id, $financial->id]) : route('nexcore.clients.show.financials.store', $client->id) }}">
    @csrf
    @if(isset($financial)) @method('PUT') @endif

    <div class="sl-card sl-animate d2">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:var(--accent-green);"><i class="fas fa-chart-line"></i> Financial Details</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Financial Type <span style="color:var(--accent-red);">*</span></label>
                    <select name="financial_type_id" class="ncm-select2" required>
                        <option value="">-- Select Type --</option>
                        @foreach($financialTypes as $type)
                            <option value="{{ $type->id }}" {{ old('financial_type_id', $financial->financial_type_id ?? '') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>Status <span style="color:var(--accent-red);">*</span></label>
                    <select name="status_id" class="ncm-select2" required>
                        <option value="">-- Select Status --</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ old('status_id', $financial->status_id ?? '') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>Financial Year <span style="color:var(--accent-red);">*</span></label>
                    <input type="text" name="financial_year" value="{{ old('financial_year', $financial->financial_year ?? '') }}" required placeholder="e.g. 2025" style="font-family:var(--font-mono); font-weight:700; color:var(--accent-amber);">
                </div>
            </div>
        </div>
    </div>

    <div class="sl-card sl-animate d3" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:var(--accent-cyan);"><i class="fas fa-calendar-alt"></i> Period & Review</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Period Start</label>
                    <input type="text" name="period_start" class="ncm-datepicker" value="{{ old('period_start', isset($financial) && $financial->period_start ? $financial->period_start->format('Y-m-d') : '') }}" placeholder="Select date..." readonly>
                </div>
                <div class="sl-field">
                    <label>Period End</label>
                    <input type="text" name="period_end" class="ncm-datepicker" value="{{ old('period_end', isset($financial) && $financial->period_end ? $financial->period_end->format('Y-m-d') : '') }}" placeholder="Select date..." readonly>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-top:20px;">
                <div class="sl-field">
                    <label>Prepared By</label>
                    <input type="text" name="prepared_by" value="{{ old('prepared_by', $financial->prepared_by ?? '') }}" placeholder="Name of preparer">
                </div>
                <div class="sl-field">
                    <label>Reviewed By</label>
                    <input type="text" name="reviewed_by" value="{{ old('reviewed_by', $financial->reviewed_by ?? '') }}" placeholder="Name of reviewer">
                </div>
                <div class="sl-field">
                    <label>Approved Date</label>
                    <input type="text" name="approved_date" class="ncm-datepicker" value="{{ old('approved_date', isset($financial) && $financial->approved_date ? $financial->approved_date->format('Y-m-d') : '') }}" placeholder="Select date..." readonly>
                </div>
            </div>
        </div>
    </div>

    <div class="sl-card sl-animate d4" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title"><i class="fas fa-sticky-note"></i> Notes</div>
        </div>
        <div style="padding:24px;">
            <div class="sl-field">
                <textarea name="notes" rows="3" placeholder="Optional notes about this financial record..." style="width:100%;">{{ old('notes', $financial->notes ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="sl-animate d5" style="margin-top:24px; display:flex; gap:12px;">
        <button type="submit" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-save"></i> {{ isset($financial) ? 'Update Record' : 'Save Record' }}</button>
        <a href="{{ route('nexcore.clients.show.financials', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-times"></i> Cancel</a>
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
