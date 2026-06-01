@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', (isset($payslip) ? 'Edit' : 'New') . ' Payslip - ' . $client->company_name)
@section('page_heading', isset($payslip) ? 'EDIT PAYSLIP' : 'NEW PAYSLIP')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(2,132,199,0.15), rgba(2,132,199,0.05)); border:1px solid rgba(2,132,199,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-file-invoice-dollar" style="color:#0284c7; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">{{ isset($payslip) ? 'Edit Payslip' : 'Add Payslip' }}</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.show.payroll.payslips', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-arrow-left"></i> Back to Payslips</a>
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
      action="{{ isset($payslip) ? route('nexcore.clients.show.payroll.payslips.update', [$client->id, $payslip->id]) : route('nexcore.clients.show.payroll.payslips.store', $client->id) }}">
    @csrf
    @if(isset($payslip)) @method('PUT') @endif

    {{-- Card 1: Payslip Details --}}
    <div class="sl-card sl-animate d2">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#0284c7;"><i class="fas fa-file-invoice-dollar"></i> Payslip Details</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Employee <span style="color:var(--accent-red);">*</span></label>
                    <select name="employee_id" class="ncm-select2" required>
                        <option value="">-- Select Employee --</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id', $payslip->employee_id ?? '') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->employee_number ? '[' . $employee->employee_number . '] ' : '' }}{{ $employee->first_name }} {{ $employee->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>Pay Period <span style="color:var(--accent-red);">*</span></label>
                    <select name="pay_period_id" class="ncm-select2" required>
                        <option value="">-- Select Pay Period --</option>
                        @foreach($periods as $period)
                            <option value="{{ $period->id }}" {{ old('pay_period_id', $payslip->pay_period_id ?? '') == $period->id ? 'selected' : '' }}>
                                {{ $period->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>Status <span style="color:var(--accent-red);">*</span></label>
                    <select name="status" class="ncm-select2" required>
                        <option value="">-- Select Status --</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ old('status', $payslip->status ?? 'draft') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 2: Earnings --}}
    <div class="sl-card sl-animate d3" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#0284c7;"><i class="fas fa-coins"></i> Earnings</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Basic Salary <span style="color:var(--accent-red);">*</span></label>
                    <input type="number" name="basic_salary" value="{{ old('basic_salary', $payslip->basic_salary ?? '0.00') }}" step="0.01" min="0" placeholder="0.00" required>
                </div>
                <div class="sl-field">
                    <label>Overtime Hours</label>
                    <input type="number" name="overtime_hours" value="{{ old('overtime_hours', $payslip->overtime_hours ?? '0.00') }}" step="0.01" min="0" placeholder="0.00">
                </div>
                <div class="sl-field">
                    <label>Overtime Amount</label>
                    <input type="number" name="overtime_amount" value="{{ old('overtime_amount', $payslip->overtime_amount ?? '0.00') }}" step="0.01" min="0" placeholder="0.00">
                </div>
            </div>
        </div>
    </div>

    {{-- Card 3: Statutory Deductions --}}
    <div class="sl-card sl-animate d4" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#0284c7;"><i class="fas fa-percentage"></i> Statutory Deductions</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>PAYE</label>
                    <input type="number" name="paye" value="{{ old('paye', $payslip->paye ?? '0.00') }}" step="0.01" min="0" placeholder="0.00">
                </div>
                <div class="sl-field">
                    <label>UIF Employee</label>
                    <input type="number" name="uif_employee" value="{{ old('uif_employee', $payslip->uif_employee ?? '0.00') }}" step="0.01" min="0" placeholder="0.00">
                </div>
                <div class="sl-field">
                    <label>UIF Employer</label>
                    <input type="number" name="uif_employer" value="{{ old('uif_employer', $payslip->uif_employer ?? '0.00') }}" step="0.01" min="0" placeholder="0.00">
                </div>
                <div class="sl-field">
                    <label>SDL</label>
                    <input type="number" name="sdl" value="{{ old('sdl', $payslip->sdl ?? '0.00') }}" step="0.01" min="0" placeholder="0.00">
                </div>
            </div>
        </div>
    </div>

    {{-- Card 4: Summary --}}
    <div class="sl-card sl-animate d5" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#0284c7;"><i class="fas fa-calculator"></i> Summary</div>
        </div>
        <div style="padding:24px;">
            <div style="font-size:12px; color:var(--text-muted); margin-bottom:16px; font-style:italic;">Enter the computed totals for this payslip. These are used for reporting and pay period aggregation.</div>
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Gross Pay <span style="color:var(--accent-red);">*</span></label>
                    <input type="number" name="gross_pay" value="{{ old('gross_pay', $payslip->gross_pay ?? '0.00') }}" step="0.01" min="0" placeholder="0.00" required style="background:var(--bg-raised); opacity:0.85;">
                </div>
                <div class="sl-field">
                    <label>Total Deductions <span style="color:var(--accent-red);">*</span></label>
                    <input type="number" name="total_deductions" value="{{ old('total_deductions', $payslip->total_deductions ?? '0.00') }}" step="0.01" min="0" placeholder="0.00" required style="background:var(--bg-raised); opacity:0.85;">
                </div>
                <div class="sl-field">
                    <label>Net Pay <span style="color:var(--accent-red);">*</span></label>
                    <input type="number" name="net_pay" value="{{ old('net_pay', $payslip->net_pay ?? '0.00') }}" step="0.01" min="0" placeholder="0.00" required style="background:var(--bg-raised); opacity:0.85;">
                </div>
                <div class="sl-field">
                    <label>Employer Cost <span style="color:var(--accent-red);">*</span></label>
                    <input type="number" name="employer_cost" value="{{ old('employer_cost', $payslip->employer_cost ?? '0.00') }}" step="0.01" min="0" placeholder="0.00" required style="background:var(--bg-raised); opacity:0.85;">
                </div>
            </div>
        </div>
    </div>

    {{-- Card 5: Notes --}}
    <div class="sl-card sl-animate d6" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#0284c7;"><i class="fas fa-sticky-note"></i> Notes</div>
        </div>
        <div style="padding:24px;">
            <div class="sl-field">
                <textarea name="notes" rows="3" placeholder="Optional internal notes about this payslip..." style="width:100%;">{{ old('notes', $payslip->notes ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="sl-animate d7" style="margin-top:24px; display:flex; gap:12px;">
        <button type="submit" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-save"></i> {{ isset($payslip) ? 'Update Payslip' : 'Save Payslip' }}</button>
        <a href="{{ route('nexcore.clients.show.payroll.payslips', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-times"></i> Cancel</a>
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
.select2-results__option--highlighted { background:#0284c7 !important; color:#fff !important; }
.select2-container--default .select2-selection--single .select2-selection__placeholder { color:var(--text-muted) !important; }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function() {
    $('.ncm-select2').select2({ width: '100%', placeholder: '-- Select --', allowClear: true });
});
</script>
@endpush
