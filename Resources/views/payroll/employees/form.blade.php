@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', (isset($employee) ? 'Edit' : 'New') . ' Employee - ' . $client->company_name)
@section('page_heading', isset($employee) ? 'EDIT EMPLOYEE' : 'ADD EMPLOYEE')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(5,150,105,0.15), rgba(5,150,105,0.05)); border:1px solid rgba(5,150,105,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-users" style="color:#059669; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">{{ isset($employee) ? 'Edit Employee' : 'Add Employee' }}</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.show.payroll.employees', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-arrow-left"></i> Back to Employees</a>
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
      action="{{ isset($employee) ? route('nexcore.clients.show.payroll.employees.update', [$client->id, $employee->id]) : route('nexcore.clients.show.payroll.employees.store', $client->id) }}">
    @csrf
    @if(isset($employee)) @method('PUT') @endif

    {{-- Card 1: Personal Details --}}
    <div class="sl-card sl-animate d2">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#059669;"><i class="fas fa-user"></i> Personal Details</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:120px 1fr 1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Title</label>
                    <select name="title" class="ncm-select2">
                        <option value="">--</option>
                        @foreach(['Mr' => 'Mr', 'Mrs' => 'Mrs', 'Ms' => 'Ms', 'Miss' => 'Miss', 'Dr' => 'Dr', 'Prof' => 'Prof'] as $key => $label)
                            <option value="{{ $key }}" {{ old('title', $employee->title ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>First Name <span style="color:var(--accent-red);">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name ?? '') }}" required placeholder="First name">
                </div>
                <div class="sl-field">
                    <label>Last Name <span style="color:var(--accent-red);">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name ?? '') }}" required placeholder="Last name">
                </div>
                <div class="sl-field">
                    <label>Gender</label>
                    <select name="gender" class="ncm-select2">
                        <option value="">-- Select --</option>
                        @foreach($genders as $key => $label)
                            <option value="{{ $key }}" {{ old('gender', $employee->gender ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:20px; margin-top:20px;">
                <div class="sl-field">
                    <label>SA ID Number</label>
                    <input type="text" name="id_number" value="{{ old('id_number', $employee->id_number ?? '') }}" style="font-family:var(--font-mono); color:var(--accent-cyan);" placeholder="13 digit ID" maxlength="13">
                </div>
                <div class="sl-field">
                    <label>Tax Number</label>
                    <input type="text" name="tax_number" value="{{ old('tax_number', $employee->tax_number ?? '') }}" style="font-family:var(--font-mono);" placeholder="SARS tax number">
                </div>
                <div class="sl-field">
                    <label>Date of Birth</label>
                    <input type="text" name="date_of_birth" class="ncm-datepicker" value="{{ old('date_of_birth', isset($employee) && $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '') }}" placeholder="Select date..." readonly>
                </div>
                <div class="sl-field">
                    <label>Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $employee->email ?? '') }}" placeholder="employee@example.com" style="color:var(--accent-cyan);">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:20px;">
                <div class="sl-field">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $employee->phone ?? '') }}" style="font-family:var(--font-mono); color:var(--accent-green);" placeholder="e.g. 082 123 4567">
                </div>
            </div>
        </div>
    </div>

    {{-- Card 2: Employment Details --}}
    <div class="sl-card sl-animate d3" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#059669;"><i class="fas fa-briefcase"></i> Employment Details</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Employee Number</label>
                    <input type="text" name="employee_number" value="{{ old('employee_number', $employee->employee_number ?? '') }}" style="font-family:var(--font-mono);" placeholder="e.g. EMP-001">
                </div>
                <div class="sl-field">
                    <label>Position / Job Title</label>
                    <input type="text" name="position" value="{{ old('position', $employee->position ?? '') }}" placeholder="e.g. Senior Accountant">
                </div>
                <div class="sl-field">
                    <label>Department</label>
                    <input type="text" name="department" value="{{ old('department', $employee->department ?? '') }}" placeholder="e.g. Finance">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-top:20px;">
                <div class="sl-field">
                    <label>Start Date</label>
                    <input type="text" name="start_date" class="ncm-datepicker" value="{{ old('start_date', isset($employee) && $employee->start_date ? $employee->start_date->format('Y-m-d') : '') }}" placeholder="Select date..." readonly>
                </div>
                <div class="sl-field">
                    <label>Termination Date</label>
                    <input type="text" name="termination_date" class="ncm-datepicker" value="{{ old('termination_date', isset($employee) && $employee->termination_date ? $employee->termination_date->format('Y-m-d') : '') }}" placeholder="Leave blank if active" readonly>
                </div>
                <div class="sl-field">
                    <label>Employment Status <span style="color:var(--accent-red);">*</span></label>
                    <select name="employment_status" class="ncm-select2" required>
                        <option value="">-- Select Status --</option>
                        @foreach($employmentStatuses as $key => $label)
                            <option value="{{ $key }}" {{ old('employment_status', $employee->employment_status ?? 'active') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 3: Salary & Payment --}}
    <div class="sl-card sl-animate d4" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#059669;"><i class="fas fa-money-bill-wave"></i> Salary &amp; Payment</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Salary Type <span style="color:var(--accent-red);">*</span></label>
                    <select name="salary_type" class="ncm-select2" required>
                        <option value="">-- Select Type --</option>
                        @foreach($salaryTypes as $key => $label)
                            <option value="{{ $key }}" {{ old('salary_type', $employee->salary_type ?? 'monthly') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>Basic Salary (R) <span style="color:var(--accent-red);">*</span></label>
                    <input type="number" name="basic_salary" value="{{ old('basic_salary', $employee->basic_salary ?? '') }}" step="0.01" min="0" required placeholder="0.00" style="font-family:var(--font-mono); font-size:18px; font-weight:700; color:#059669;">
                </div>
                <div class="sl-field">
                    <label>Pay Frequency <span style="color:var(--accent-red);">*</span></label>
                    <select name="pay_frequency" class="ncm-select2" required>
                        <option value="">-- Select Frequency --</option>
                        @foreach($payFrequencies as $key => $label)
                            <option value="{{ $key }}" {{ old('pay_frequency', $employee->pay_frequency ?? 'monthly') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 4: Banking Details --}}
    <div class="sl-card sl-animate d5" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#059669;"><i class="fas fa-university"></i> Banking Details</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Bank Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $employee->bank_name ?? '') }}" placeholder="e.g. FNB, ABSA, Standard Bank">
                </div>
                <div class="sl-field">
                    <label>Branch Code</label>
                    <input type="text" name="bank_branch_code" value="{{ old('bank_branch_code', $employee->bank_branch_code ?? '') }}" style="font-family:var(--font-mono);" placeholder="6-digit code">
                </div>
                <div class="sl-field">
                    <label>Account Number</label>
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $employee->bank_account_number ?? '') }}" style="font-family:var(--font-mono);" placeholder="Account number">
                </div>
                <div class="sl-field">
                    <label>Account Type</label>
                    <select name="bank_account_type" class="ncm-select2">
                        <option value="">-- Select Type --</option>
                        @foreach($accountTypes as $key => $label)
                            <option value="{{ $key }}" {{ old('bank_account_type', $employee->bank_account_type ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 5: Additional --}}
    <div class="sl-card sl-animate d6" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title"><i class="fas fa-sticky-note"></i> Additional Information</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Address</label>
                    <textarea name="address" rows="3" placeholder="Full residential address..." style="width:100%;">{{ old('address', $employee->address ?? '') }}</textarea>
                </div>
                <div class="sl-field">
                    <label>Notes</label>
                    <textarea name="notes" rows="3" placeholder="Optional internal notes..." style="width:100%;">{{ old('notes', $employee->notes ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="sl-animate d7" style="margin-top:24px; display:flex; gap:12px;">
        <button type="submit" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-save"></i> {{ isset($employee) ? 'Update Employee' : 'Save Employee' }}</button>
        <a href="{{ route('nexcore.clients.show.payroll.employees', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-times"></i> Cancel</a>
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
.select2-results__option--highlighted { background:#059669 !important; color:#fff !important; }
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
