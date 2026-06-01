@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', (isset($period) ? 'Edit' : 'New') . ' Pay Period - ' . $client->company_name)
@section('page_heading', isset($period) ? 'EDIT PAY PERIOD' : 'NEW PAY PERIOD')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(13,148,136,0.15), rgba(13,148,136,0.05)); border:1px solid rgba(13,148,136,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-calendar-week" style="color:#0d9488; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">{{ isset($period) ? 'Edit Pay Period' : 'Add Pay Period' }}</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.show.payroll.periods', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-arrow-left"></i> Back to Pay Periods</a>
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
      action="{{ isset($period) ? route('nexcore.clients.show.payroll.periods.update', [$client->id, $period->id]) : route('nexcore.clients.show.payroll.periods.store', $client->id) }}">
    @csrf
    @if(isset($period)) @method('PUT') @endif

    {{-- Card 1: Period Details --}}
    <div class="sl-card sl-animate d2">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#0d9488;"><i class="fas fa-calendar-week"></i> Period Details</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr; gap:20px;">
                <div class="sl-field">
                    <label>Period Name <span style="color:var(--accent-red);">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $period->name ?? '') }}" required placeholder="e.g. April 2025 Monthly Payroll">
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:20px;">
                <div class="sl-field">
                    <label>Pay Frequency <span style="color:var(--accent-red);">*</span></label>
                    <select name="pay_frequency" class="ncm-select2" required>
                        <option value="">-- Select Frequency --</option>
                        @foreach($payFrequencies as $key => $label)
                            <option value="{{ $key }}" {{ old('pay_frequency', $period->pay_frequency ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>Status <span style="color:var(--accent-red);">*</span></label>
                    <select name="status" class="ncm-select2" required>
                        <option value="">-- Select Status --</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ old('status', $period->status ?? 'draft') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 2: Dates --}}
    <div class="sl-card sl-animate d3" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#0d9488;"><i class="fas fa-calendar-alt"></i> Dates</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Period Start <span style="color:var(--accent-red);">*</span></label>
                    <input type="text" name="period_start" class="ncm-datepicker" value="{{ old('period_start', isset($period) && $period->period_start ? $period->period_start->format('Y-m-d') : '') }}" placeholder="Select date..." readonly>
                </div>
                <div class="sl-field">
                    <label>Period End <span style="color:var(--accent-red);">*</span></label>
                    <input type="text" name="period_end" class="ncm-datepicker" value="{{ old('period_end', isset($period) && $period->period_end ? $period->period_end->format('Y-m-d') : '') }}" placeholder="Select date..." readonly>
                </div>
                <div class="sl-field">
                    <label>Payment Date <span style="color:var(--accent-red);">*</span></label>
                    <input type="text" name="payment_date" class="ncm-datepicker" value="{{ old('payment_date', isset($period) && $period->payment_date ? $period->payment_date->format('Y-m-d') : '') }}" placeholder="Select date..." readonly>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 3: Notes --}}
    <div class="sl-card sl-animate d4" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#0d9488;"><i class="fas fa-sticky-note"></i> Notes</div>
        </div>
        <div style="padding:24px;">
            <div class="sl-field">
                <textarea name="notes" rows="3" placeholder="Optional internal notes about this pay period..." style="width:100%;">{{ old('notes', $period->notes ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="sl-animate d5" style="margin-top:24px; display:flex; gap:12px;">
        <button type="submit" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-save"></i> {{ isset($period) ? 'Update Pay Period' : 'Save Pay Period' }}</button>
        <a href="{{ route('nexcore.clients.show.payroll.periods', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-times"></i> Cancel</a>
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
.select2-results__option--highlighted { background:#0d9488 !important; color:#fff !important; }
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
