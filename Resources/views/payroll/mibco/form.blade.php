@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', (isset($contribution) ? 'Edit' : 'New') . ' MIBCO Contribution - ' . $client->company_name)
@section('page_heading', isset($contribution) ? 'EDIT MIBCO CONTRIBUTION' : 'ADD MIBCO CONTRIBUTION')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(168,85,247,0.15), rgba(168,85,247,0.05)); border:1px solid rgba(168,85,247,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-building-columns" style="color:#a855f7; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">{{ isset($contribution) ? 'Edit MIBCO Contribution' : 'Add MIBCO Contribution' }}</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.show.payroll.mibco', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-arrow-left"></i> Back to MIBCO</a>
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
      action="{{ isset($contribution) ? route('nexcore.clients.show.payroll.mibco.update', [$client->id, $contribution->id]) : route('nexcore.clients.show.payroll.mibco.store', $client->id) }}">
    @csrf
    @if(isset($contribution)) @method('PUT') @endif

    {{-- Card 1: Details --}}
    <div class="sl-card sl-animate d2">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#a855f7;"><i class="fas fa-info-circle"></i> Contribution Details</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Employee <span style="color:var(--accent-red);">*</span></label>
                    <select name="employee_id" class="ncm-select2" required>
                        <option value="">-- Select Employee --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('employee_id', $contribution->employee_id ?? '') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_number ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>Pay Period <span style="color:var(--accent-red);">*</span></label>
                    <select name="pay_period_id" class="ncm-select2" required>
                        <option value="">-- Select Period --</option>
                        @foreach($periods as $p)
                            <option value="{{ $p->id }}" {{ old('pay_period_id', $contribution->pay_period_id ?? '') == $p->id ? 'selected' : '' }}>
                                {{ $p->period_start->format('j M Y') }} - {{ $p->period_end->format('j M Y') }} ({{ ucfirst($p->pay_frequency) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>Status <span style="color:var(--accent-red);">*</span></label>
                    <select name="status" class="ncm-select2" required>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ old('status', $contribution->status ?? 'draft') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 2: Pension & Provident --}}
    <div class="sl-card sl-animate d3">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#a855f7;"><i class="fas fa-piggy-bank"></i> Pension & Provident Fund</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Pension (Employee)</label>
                    <input type="number" name="pension_employee" step="0.01" min="0" value="{{ old('pension_employee', $contribution->pension_employee ?? '0.00') }}" placeholder="0.00" class="mibco-calc">
                </div>
                <div class="sl-field">
                    <label>Pension (Employer)</label>
                    <input type="number" name="pension_employer" step="0.01" min="0" value="{{ old('pension_employer', $contribution->pension_employer ?? '0.00') }}" placeholder="0.00" class="mibco-calc">
                </div>
                <div class="sl-field">
                    <label>Provident (Employee)</label>
                    <input type="number" name="provident_employee" step="0.01" min="0" value="{{ old('provident_employee', $contribution->provident_employee ?? '0.00') }}" placeholder="0.00" class="mibco-calc">
                </div>
                <div class="sl-field">
                    <label>Provident (Employer)</label>
                    <input type="number" name="provident_employer" step="0.01" min="0" value="{{ old('provident_employer', $contribution->provident_employer ?? '0.00') }}" placeholder="0.00" class="mibco-calc">
                </div>
            </div>
        </div>
    </div>

    {{-- Card 3: Benefits & Funds --}}
    <div class="sl-card sl-animate d4">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#a855f7;"><i class="fas fa-shield-halved"></i> Benefits & Funds (Employer)</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Death Benefit</label>
                    <input type="number" name="death_benefit" step="0.01" min="0" value="{{ old('death_benefit', $contribution->death_benefit ?? '0.00') }}" placeholder="0.00" class="mibco-calc">
                </div>
                <div class="sl-field">
                    <label>Funeral Benefit</label>
                    <input type="number" name="funeral_benefit" step="0.01" min="0" value="{{ old('funeral_benefit', $contribution->funeral_benefit ?? '0.00') }}" placeholder="0.00" class="mibco-calc">
                </div>
                <div class="sl-field">
                    <label>Sick Pay Fund</label>
                    <input type="number" name="sick_pay_fund" step="0.01" min="0" value="{{ old('sick_pay_fund', $contribution->sick_pay_fund ?? '0.00') }}" placeholder="0.00" class="mibco-calc">
                </div>
                <div class="sl-field">
                    <label>Holiday Fund</label>
                    <input type="number" name="holiday_fund" step="0.01" min="0" value="{{ old('holiday_fund', $contribution->holiday_fund ?? '0.00') }}" placeholder="0.00" class="mibco-calc">
                </div>
            </div>
        </div>
    </div>

    {{-- Card 4: Totals (auto-calc) --}}
    <div class="sl-card sl-animate d5">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#a855f7;"><i class="fas fa-calculator"></i> Calculated Totals</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
                <div style="background:rgba(168,85,247,0.06); border:1px solid rgba(168,85,247,0.2); border-radius:var(--radius-md); padding:20px; text-align:center;">
                    <div style="font-size:12px; text-transform:uppercase; color:var(--text-muted); letter-spacing:1px; margin-bottom:8px;">Employee Total</div>
                    <div id="calcEmployeeTotal" style="font-size:24px; font-weight:700; color:var(--accent-green); font-family:var(--font-mono);">R 0.00</div>
                </div>
                <div style="background:rgba(168,85,247,0.06); border:1px solid rgba(168,85,247,0.2); border-radius:var(--radius-md); padding:20px; text-align:center;">
                    <div style="font-size:12px; text-transform:uppercase; color:var(--text-muted); letter-spacing:1px; margin-bottom:8px;">Employer Total</div>
                    <div id="calcEmployerTotal" style="font-size:24px; font-weight:700; color:var(--accent-blue); font-family:var(--font-mono);">R 0.00</div>
                </div>
                <div style="background:rgba(168,85,247,0.1); border:1px solid rgba(168,85,247,0.35); border-radius:var(--radius-md); padding:20px; text-align:center;">
                    <div style="font-size:12px; text-transform:uppercase; color:var(--text-muted); letter-spacing:1px; margin-bottom:8px;">Grand Total</div>
                    <div id="calcGrandTotal" style="font-size:28px; font-weight:800; color:#a855f7; font-family:var(--font-mono);">R 0.00</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 5: Notes --}}
    <div class="sl-card sl-animate d6">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#a855f7;"><i class="fas fa-sticky-note"></i> Notes</div>
        </div>
        <div style="padding:24px;">
            <div class="sl-field">
                <label>Notes</label>
                <textarea name="notes" rows="3" placeholder="Additional notes..." style="width:100%; background:var(--bg-raised); color:var(--text-primary); border:1px solid var(--border-default); border-radius:var(--radius-sm); padding:10px 14px; font-family:var(--font-body); font-size:15px; resize:vertical;">{{ old('notes', $contribution->notes ?? '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="sl-animate d7" style="display:flex; justify-content:flex-end; gap:12px; margin-top:20px;">
        <a href="{{ route('nexcore.clients.show.payroll.mibco', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-times"></i> Cancel</a>
        <button type="submit" class="neon-btn neon-btn-purple neon-pulse"><i class="fas fa-save"></i> {{ isset($contribution) ? 'Update Contribution' : 'Save Contribution' }}</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
function recalcMibco() {
    let v = name => parseFloat(document.querySelector('[name="'+name+'"]').value) || 0;
    let ee = v('pension_employee') + v('provident_employee');
    let er = v('pension_employer') + v('provident_employer') + v('death_benefit') + v('funeral_benefit') + v('sick_pay_fund') + v('holiday_fund');
    document.getElementById('calcEmployeeTotal').textContent = 'R ' + ee.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    document.getElementById('calcEmployerTotal').textContent = 'R ' + er.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    document.getElementById('calcGrandTotal').textContent = 'R ' + (ee + er).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}
document.querySelectorAll('.mibco-calc').forEach(el => el.addEventListener('input', recalcMibco));
recalcMibco();
</script>
@endpush
