@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', (isset($account) ? 'Edit' : 'New') . ' Bank Account - ' . $client->company_name)
@section('page_heading', isset($account) ? 'EDIT BANK ACCOUNT' : 'NEW BANK ACCOUNT')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(34,197,94,0.15), rgba(34,197,94,0.05)); border:1px solid rgba(34,197,94,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-landmark" style="color:var(--accent-green); font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">{{ isset($account) ? 'Edit Bank Account' : 'New Bank Account' }}</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.show.banking', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-arrow-left"></i> Back to Banking</a>
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
      action="{{ isset($account) ? route('nexcore.clients.show.banking.update', [$client->id, $account->id]) : route('nexcore.clients.show.banking.store', $client->id) }}">
    @csrf
    @if(isset($account)) @method('PUT') @endif

    <div class="sl-card sl-animate d2">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:var(--accent-green);"><i class="fas fa-landmark"></i> Bank Account Details</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Bank <span style="color:var(--accent-red);">*</span></label>
                    <select name="bank_id" class="ncm-select2" required>
                        <option value="">-- Select Bank --</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank->id }}" {{ old('bank_id', $account->bank_id ?? '') == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>Account Type <span style="color:var(--accent-red);">*</span></label>
                    <select name="account_type_id" class="ncm-select2" required>
                        <option value="">-- Select Type --</option>
                        @foreach($accountTypes as $type)
                            <option value="{{ $type->id }}" {{ old('account_type_id', $account->account_type_id ?? '') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>Account Label</label>
                    <input type="text" name="account_label" value="{{ old('account_label', $account->account_label ?? '') }}" placeholder="e.g. Primary, Payroll, Petty Cash...">
                </div>
            </div>

            @if(isset($glAccounts) && $glAccounts->count())
            <div style="margin-top:20px;">
                <div class="sl-field">
                    <label><i class="fas fa-book" style="color:var(--accent-cyan); margin-right:4px;"></i> Link to Chart of Accounts (GL Account)</label>
                    <select name="gl_account_id" class="ncm-select2">
                        <option value="">-- Not Linked --</option>
                        @foreach($glAccounts as $gl)
                            <option value="{{ $gl->id }}" {{ old('gl_account_id', $account->gl_account_id ?? '') == $gl->id ? 'selected' : '' }}>{{ $gl->account_code }} — {{ $gl->account_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:20px;">
                <div class="sl-field">
                    <label>Account Name <span style="color:var(--accent-red);">*</span></label>
                    <input type="text" name="account_name" value="{{ old('account_name', $account->account_name ?? $client->company_name) }}" required>
                </div>
                <div class="sl-field">
                    <label>Account Number <span style="color:var(--accent-red);">*</span></label>
                    <input type="text" name="account_number" value="{{ old('account_number', $account->account_number ?? '') }}" required style="font-family:var(--font-mono); font-size:18px; font-weight:700; color:var(--accent-green); letter-spacing:2px;">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-top:20px;">
                <div class="sl-field">
                    <label>Branch Code</label>
                    <input type="text" name="branch_code" value="{{ old('branch_code', $account->branch_code ?? '') }}" placeholder="e.g. 250655" style="font-family:var(--font-mono); font-weight:600; color:var(--accent-cyan);">
                </div>
                <div class="sl-field">
                    <label>SWIFT Code</label>
                    <input type="text" name="swift_code" value="{{ old('swift_code', $account->swift_code ?? '') }}" placeholder="e.g. FIRNZAJJ" style="font-family:var(--font-mono); font-weight:600; color:var(--accent-blue);">
                </div>
                <div class="sl-field" style="display:flex; align-items:center; gap:16px; padding-top:22px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; margin:0;">
                        <input type="hidden" name="is_primary" value="0">
                        <input type="checkbox" name="is_primary" value="1" {{ old('is_primary', $account->is_primary ?? false) ? 'checked' : '' }} style="width:18px; height:18px; accent-color:var(--accent-green);">
                        <span style="font-weight:600; color:var(--accent-green);"><i class="fas fa-star" style="margin-right:4px;"></i> Primary Account</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="sl-card sl-animate d3" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title"><i class="fas fa-sticky-note"></i> Notes</div>
        </div>
        <div style="padding:24px;">
            <div class="sl-field">
                <textarea name="notes" rows="3" placeholder="Optional notes about this account..." style="width:100%;">{{ old('notes', $account->notes ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="sl-animate d4" style="margin-top:24px; display:flex; gap:12px;">
        <button type="submit" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-save"></i> {{ isset($account) ? 'Update Account' : 'Save Account' }}</button>
        <a href="{{ route('nexcore.clients.show.banking', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-times"></i> Cancel</a>
    </div>
</form>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
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
<script>$(function() { $('.ncm-select2').select2({ width: '100%', placeholder: '-- Select --', allowClear: true }); });</script>
@endpush
