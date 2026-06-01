@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', (isset($sarsReturn) ? 'Edit' : 'New') . ' SARS Return - ' . $client->company_name)
@section('page_heading', isset($sarsReturn) ? 'EDIT SARS RETURN' : 'NEW SARS RETURN')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(239,68,68,0.15), rgba(239,68,68,0.05)); border:1px solid rgba(239,68,68,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-file-invoice-dollar" style="color:var(--accent-red); font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">{{ isset($sarsReturn) ? 'Edit SARS Return' : 'New SARS Return' }}</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.show.sars', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-arrow-left"></i> Back to SARS Returns</a>
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
      action="{{ isset($sarsReturn) ? route('nexcore.clients.show.sars.update', [$client->id, $sarsReturn->id]) : route('nexcore.clients.show.sars.store', $client->id) }}">
    @csrf
    @if(isset($sarsReturn)) @method('PUT') @endif

    {{-- Card 1: Return Details --}}
    <div class="sl-card sl-animate d2">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:var(--accent-red);"><i class="fas fa-file-invoice-dollar"></i> Return Details</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Return Type <span style="color:var(--accent-red);">*</span></label>
                    <select name="return_type_id" class="ncm-select2" required>
                        <option value="">-- Select Return Type --</option>
                        @foreach($returnTypes as $type)
                            <option value="{{ $type->id }}" {{ old('return_type_id', $sarsReturn->return_type_id ?? '') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>Status <span style="color:var(--accent-red);">*</span></label>
                    <select name="status_id" class="ncm-select2" required>
                        <option value="">-- Select Status --</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ old('status_id', $sarsReturn->status_id ?? '') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>Tax Year <span style="color:var(--accent-red);">*</span></label>
                    <input type="text" name="tax_year" value="{{ old('tax_year', $sarsReturn->tax_year ?? '') }}" placeholder="e.g. 2025" required style="font-family:var(--font-mono); font-weight:600; color:var(--accent-amber);">
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr; gap:20px; margin-top:20px;">
                <div class="sl-field">
                    <label>Tax Period</label>
                    <input type="text" name="tax_period" value="{{ old('tax_period', $sarsReturn->tax_period ?? '') }}" placeholder="e.g. May 2025, Q1 2025">
                </div>
            </div>
        </div>
    </div>

    {{-- Card 2: Dates --}}
    <div class="sl-card sl-animate d3" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:var(--accent-amber);"><i class="fas fa-calendar-alt"></i> Dates</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Due Date</label>
                    <input type="text" name="due_date" class="ncm-datepicker" readonly value="{{ old('due_date', isset($sarsReturn) && $sarsReturn->due_date ? $sarsReturn->due_date->format('Y-m-d') : '') }}" placeholder="Select date...">
                </div>
                <div class="sl-field">
                    <label>Submission Date</label>
                    <input type="text" name="submission_date" class="ncm-datepicker" readonly value="{{ old('submission_date', isset($sarsReturn) && $sarsReturn->submission_date ? $sarsReturn->submission_date->format('Y-m-d') : '') }}" placeholder="Select date...">
                </div>
                <div class="sl-field">
                    <label>Assessment Date</label>
                    <input type="text" name="assessment_date" class="ncm-datepicker" readonly value="{{ old('assessment_date', isset($sarsReturn) && $sarsReturn->assessment_date ? $sarsReturn->assessment_date->format('Y-m-d') : '') }}" placeholder="Select date...">
                </div>
                <div class="sl-field">
                    <label>Payment Due Date</label>
                    <input type="text" name="payment_due_date" class="ncm-datepicker" readonly value="{{ old('payment_due_date', isset($sarsReturn) && $sarsReturn->payment_due_date ? $sarsReturn->payment_due_date->format('Y-m-d') : '') }}" placeholder="Select date...">
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:20px;">
                <div class="sl-field">
                    <label>Payment Date</label>
                    <input type="text" name="payment_date" class="ncm-datepicker" readonly value="{{ old('payment_date', isset($sarsReturn) && $sarsReturn->payment_date ? $sarsReturn->payment_date->format('Y-m-d') : '') }}" placeholder="Select date...">
                </div>
                <div class="sl-field">
                    <label>Reference Number</label>
                    <input type="text" name="reference_number" value="{{ old('reference_number', $sarsReturn->reference_number ?? '') }}" placeholder="e.g. SARS ref number" style="font-family:var(--font-mono); font-weight:600; color:var(--accent-cyan);">
                </div>
            </div>
        </div>
    </div>

    {{-- Card 3: Financial --}}
    <div class="sl-card sl-animate d3" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:var(--accent-green);"><i class="fas fa-coins"></i> Financial</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Amount Due</label>
                    <input type="number" name="amount_due" step="0.01" value="{{ old('amount_due', $sarsReturn->amount_due ?? '0.00') }}" style="font-family:var(--font-mono); font-weight:700; color:var(--accent-green); font-size:16px;">
                </div>
                <div class="sl-field">
                    <label>Amount Paid</label>
                    <input type="number" name="amount_paid" step="0.01" value="{{ old('amount_paid', $sarsReturn->amount_paid ?? '0.00') }}" style="font-family:var(--font-mono); font-weight:600;">
                </div>
                <div class="sl-field">
                    <label>Penalty Amount</label>
                    <input type="number" name="penalty_amount" step="0.01" value="{{ old('penalty_amount', $sarsReturn->penalty_amount ?? '0.00') }}" style="font-family:var(--font-mono); font-weight:600; color:var(--accent-red);">
                </div>
                <div class="sl-field">
                    <label>Interest Amount</label>
                    <input type="number" name="interest_amount" step="0.01" value="{{ old('interest_amount', $sarsReturn->interest_amount ?? '0.00') }}" style="font-family:var(--font-mono); font-weight:600; color:var(--accent-amber);">
                </div>
            </div>
        </div>
    </div>

    {{-- Card 4: Notes --}}
    <div class="sl-card sl-animate d4" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title"><i class="fas fa-sticky-note"></i> Notes</div>
        </div>
        <div style="padding:24px;">
            <div class="sl-field">
                <textarea name="notes" rows="3" placeholder="Optional notes about this SARS return..." style="width:100%;">{{ old('notes', $sarsReturn->notes ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="sl-animate d4" style="margin-top:24px; display:flex; gap:12px;">
        <button type="submit" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-save"></i> {{ isset($sarsReturn) ? 'Update Return' : 'Save Return' }}</button>
        <a href="{{ route('nexcore.clients.show.sars', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-times"></i> Cancel</a>
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
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
$(function() {
    $('.ncm-select2').select2({ width: '100%', placeholder: '-- Select --', allowClear: true });
    $('.ncm-datepicker').flatpickr({ dateFormat: 'Y-m-d', altInput: true, altFormat: 'j M Y', theme: 'dark', allowInput: false });
});
</script>
@endpush
