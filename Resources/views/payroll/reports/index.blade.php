@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', 'Payroll Reports - ' . $client->company_name)
@section('page_heading', 'PAYROLL REPORTS')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(245,158,11,0.15), rgba(245,158,11,0.05)); border:1px solid rgba(245,158,11,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-chart-bar" style="color:#f59e0b; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Payroll Reports</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; gap:12px; align-items:center;">
            <form method="GET" action="{{ route('nexcore.clients.show.payroll.reports', $client->id) }}" style="display:flex; gap:10px; align-items:center;">
                <label style="font-size:13px; color:var(--text-muted); font-weight:600; white-space:nowrap;">PAY PERIOD:</label>
                <select name="period_id" onchange="this.form.submit()" style="min-width:320px; background:var(--bg-raised); color:var(--text-primary); border:1px solid rgba(245,158,11,0.3); border-radius:var(--radius-sm); padding:10px 14px; font-size:14px; font-family:var(--font-body); cursor:pointer; appearance:auto; outline:none; transition:border-color 0.2s;">
                    <option value="" style="background:var(--bg-surface); color:var(--text-muted);">-- Select Period --</option>
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" {{ $selectedPeriodId == $p->id ? 'selected' : '' }} style="background:var(--bg-surface); color:var(--text-primary);">
                            {{ $p->period_start->format('j M Y') }} - {{ $p->period_end->format('j M Y') }} ({{ ucfirst($p->pay_frequency) }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
</div>

@if($selectedPeriod)
{{-- Summary Stats --}}
<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card" style="border-color:rgba(245,158,11,0.4);">
        <div class="sl-stat-label">Gross Payroll</div>
        <div class="sl-stat-value" style="color:#f59e0b; font-size:20px;">R {{ number_format($summaryTotals['gross_pay'], 2) }}</div>
        <div class="sl-stat-meta">Total gross pay</div>
    </div>
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Net Pay</div>
        <div class="sl-stat-value" style="color:var(--accent-green); font-size:20px;">R {{ number_format($summaryTotals['net_pay'], 2) }}</div>
        <div class="sl-stat-meta">Total take-home</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(239,68,68,0.4);">
        <div class="sl-stat-label">Total Deductions</div>
        <div class="sl-stat-value" style="color:var(--accent-red); font-size:20px;">R {{ number_format($summaryTotals['total_deductions'], 2) }}</div>
        <div class="sl-stat-meta">All deductions</div>
    </div>
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">Employer Cost</div>
        <div class="sl-stat-value" style="color:var(--accent-blue); font-size:20px;">R {{ number_format($summaryTotals['employer_cost'], 2) }}</div>
        <div class="sl-stat-meta">Total cost to company</div>
    </div>
</div>

{{-- Report Tabs --}}
<div class="sl-card sl-animate d3" style="margin-bottom:0; border-bottom:none; border-radius:var(--radius-md) var(--radius-md) 0 0;">
    <div style="display:flex; gap:0; border-bottom:2px solid var(--border-subtle); padding:0 4px; flex-wrap:wrap;">
        <button class="rpt-tab active" onclick="showReport('summary', this)">
            <i class="fas fa-file-invoice-dollar"></i> Payroll Summary
        </button>
        <button class="rpt-tab" onclick="showReport('employee-cost', this)">
            <i class="fas fa-users-cog"></i> Employee Cost
        </button>
        <button class="rpt-tab" onclick="showReport('statutory', this)">
            <i class="fas fa-landmark"></i> Statutory Returns
        </button>
        <button class="rpt-tab" onclick="showReport('mibco', this)">
            <i class="fas fa-building-columns"></i> MIBCO Summary
        </button>
    </div>
</div>

{{-- Report Content --}}
<div class="sl-card" style="border-radius:0 0 var(--radius-md) var(--radius-md); margin-top:0;">

    {{-- Tab 1: Payroll Summary --}}
    <div id="report-summary" class="rpt-panel">
        <div style="padding:20px 24px 8px; border-bottom:1px solid var(--border-subtle);">
            <h3 style="margin:0; font-size:16px; color:#f59e0b; font-weight:700;"><i class="fas fa-file-invoice-dollar"></i> Payroll Summary Report</h3>
            <p style="margin:4px 0 12px; font-size:13px; color:var(--text-muted);">Period: {{ $selectedPeriod->period_start->format('j M Y') }} - {{ $selectedPeriod->period_end->format('j M Y') }}</p>
        </div>
        <div class="sl-table-wrap">
            <table class="sl-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Basic Salary</th>
                        <th>Overtime</th>
                        <th>Gross Pay</th>
                        <th>PAYE</th>
                        <th>UIF (EE)</th>
                        <th>Total Ded.</th>
                        <th>Net Pay</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payslips as $idx => $ps)
                    <tr>
                        <td style="color:var(--text-muted);">{{ $idx + 1 }}</td>
                        <td style="font-weight:600; color:var(--text-primary);">{{ optional($ps->employee)->first_name }} {{ optional($ps->employee)->last_name }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($ps->basic_salary, 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($ps->overtime_amount, 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px; font-weight:600;">R {{ number_format($ps->gross_pay, 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px; color:var(--accent-red);">R {{ number_format($ps->paye, 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($ps->uif_employee, 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px; color:var(--accent-red);">R {{ number_format($ps->total_deductions, 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px; color:var(--accent-green); font-weight:700;">R {{ number_format($ps->net_pay, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="9" style="text-align:center; padding:40px; color:var(--text-muted);">No payslips for this period</td></tr>
                    @endforelse
                </tbody>
                @if($payslips->count())
                <tfoot>
                    <tr style="background:rgba(245,158,11,0.06); font-weight:700;">
                        <td colspan="2" style="font-size:14px; color:#f59e0b;">TOTALS</td>
                        <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($summaryTotals['basic_salary'], 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($summaryTotals['overtime_amount'], 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px; font-weight:700;">R {{ number_format($summaryTotals['gross_pay'], 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px; color:var(--accent-red);">R {{ number_format($summaryTotals['paye'], 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($summaryTotals['uif_employee'], 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px; color:var(--accent-red);">R {{ number_format($summaryTotals['total_deductions'], 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:14px; color:var(--accent-green); font-weight:800;">R {{ number_format($summaryTotals['net_pay'], 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Tab 2: Employee Cost --}}
    <div id="report-employee-cost" class="rpt-panel" style="display:none;">
        <div style="padding:20px 24px 8px; border-bottom:1px solid var(--border-subtle);">
            <h3 style="margin:0; font-size:16px; color:#f59e0b; font-weight:700;"><i class="fas fa-users-cog"></i> Employee Cost Report</h3>
            <p style="margin:4px 0 12px; font-size:13px; color:var(--text-muted);">Period: {{ $selectedPeriod->period_start->format('j M Y') }} - {{ $selectedPeriod->period_end->format('j M Y') }}</p>
        </div>
        <div class="sl-table-wrap">
            <table class="sl-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Basic Salary</th>
                        <th>Gross Pay</th>
                        <th>UIF (ER)</th>
                        <th>SDL</th>
                        <th>MIBCO (ER)</th>
                        <th>Total Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payslips as $idx => $ps)
                    @php
                        $empMibco = $mibco->where('employee_id', $ps->employee_id)->first();
                        $mibcoEr = $empMibco ? $empMibco->total_employer : 0;
                    @endphp
                    <tr>
                        <td style="color:var(--text-muted);">{{ $idx + 1 }}</td>
                        <td style="font-weight:600; color:var(--text-primary);">{{ optional($ps->employee)->first_name }} {{ optional($ps->employee)->last_name }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($ps->basic_salary, 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($ps->gross_pay, 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($ps->uif_employer, 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($ps->sdl, 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px; color:#a855f7;">R {{ number_format($mibcoEr, 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px; color:var(--accent-blue); font-weight:700;">R {{ number_format($ps->employer_cost + $mibcoEr, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center; padding:40px; color:var(--text-muted);">No payslips for this period</td></tr>
                    @endforelse
                </tbody>
                @if($payslips->count())
                <tfoot>
                    <tr style="background:rgba(245,158,11,0.06); font-weight:700;">
                        <td colspan="2" style="font-size:14px; color:#f59e0b;">TOTALS</td>
                        <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($summaryTotals['basic_salary'], 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($summaryTotals['gross_pay'], 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($summaryTotals['uif_employer'], 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($summaryTotals['sdl'], 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px; color:#a855f7;">R {{ number_format($mibcoTotals['total_employer'], 2) }}</td>
                        <td style="font-family:var(--font-mono); font-size:14px; color:var(--accent-blue); font-weight:800;">R {{ number_format($summaryTotals['employer_cost'] + $mibcoTotals['total_employer'], 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Tab 3: Statutory Returns --}}
    <div id="report-statutory" class="rpt-panel" style="display:none;">
        <div style="padding:20px 24px 8px; border-bottom:1px solid var(--border-subtle);">
            <h3 style="margin:0; font-size:16px; color:#f59e0b; font-weight:700;"><i class="fas fa-landmark"></i> Statutory Returns Report</h3>
            <p style="margin:4px 0 12px; font-size:13px; color:var(--text-muted);">Period: {{ $selectedPeriod->period_start->format('j M Y') }} - {{ $selectedPeriod->period_end->format('j M Y') }} | For SARS submission</p>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-bottom:24px;">
                <div style="background:rgba(239,68,68,0.06); border:1px solid rgba(239,68,68,0.25); border-radius:var(--radius-md); padding:24px; text-align:center;">
                    <div style="font-size:11px; text-transform:uppercase; color:var(--text-muted); letter-spacing:1.5px; margin-bottom:6px;">PAYE (Pay As You Earn)</div>
                    <div style="font-size:28px; font-weight:800; color:var(--accent-red); font-family:var(--font-mono);">R {{ number_format($summaryTotals['paye'], 2) }}</div>
                    <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">Monthly PAYE deduction</div>
                </div>
                <div style="background:rgba(59,130,246,0.06); border:1px solid rgba(59,130,246,0.25); border-radius:var(--radius-md); padding:24px; text-align:center;">
                    <div style="font-size:11px; text-transform:uppercase; color:var(--text-muted); letter-spacing:1.5px; margin-bottom:6px;">UIF (Total)</div>
                    <div style="font-size:28px; font-weight:800; color:var(--accent-blue); font-family:var(--font-mono);">R {{ number_format($summaryTotals['uif_employee'] + $summaryTotals['uif_employer'], 2) }}</div>
                    <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">EE: R {{ number_format($summaryTotals['uif_employee'], 2) }} + ER: R {{ number_format($summaryTotals['uif_employer'], 2) }}</div>
                </div>
                <div style="background:rgba(245,158,11,0.06); border:1px solid rgba(245,158,11,0.25); border-radius:var(--radius-md); padding:24px; text-align:center;">
                    <div style="font-size:11px; text-transform:uppercase; color:var(--text-muted); letter-spacing:1.5px; margin-bottom:6px;">SDL (Skills Dev Levy)</div>
                    <div style="font-size:28px; font-weight:800; color:#f59e0b; font-family:var(--font-mono);">R {{ number_format($summaryTotals['sdl'], 2) }}</div>
                    <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">1% of gross payroll</div>
                </div>
            </div>

            <div style="background:rgba(168,85,247,0.06); border:1px solid rgba(168,85,247,0.25); border-radius:var(--radius-md); padding:20px; text-align:center;">
                <div style="font-size:11px; text-transform:uppercase; color:var(--text-muted); letter-spacing:1.5px; margin-bottom:6px;">Total Monthly Statutory Liability</div>
                <div style="font-size:36px; font-weight:800; color:#a855f7; font-family:var(--font-mono);">R {{ number_format($summaryTotals['paye'] + $summaryTotals['uif_employee'] + $summaryTotals['uif_employer'] + $summaryTotals['sdl'], 2) }}</div>
            </div>

            <div class="sl-table-wrap" style="margin-top:24px;">
                <table class="sl-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Gross Pay</th>
                            <th>PAYE</th>
                            <th>UIF (EE)</th>
                            <th>UIF (ER)</th>
                            <th>SDL</th>
                            <th>Total Statutory</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payslips as $idx => $ps)
                        <tr>
                            <td style="color:var(--text-muted);">{{ $idx + 1 }}</td>
                            <td style="font-weight:600; color:var(--text-primary);">{{ optional($ps->employee)->first_name }} {{ optional($ps->employee)->last_name }}</td>
                            <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($ps->gross_pay, 2) }}</td>
                            <td style="font-family:var(--font-mono); font-size:13px; color:var(--accent-red);">R {{ number_format($ps->paye, 2) }}</td>
                            <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($ps->uif_employee, 2) }}</td>
                            <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($ps->uif_employer, 2) }}</td>
                            <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($ps->sdl, 2) }}</td>
                            <td style="font-family:var(--font-mono); font-size:13px; color:#a855f7; font-weight:700;">R {{ number_format($ps->paye + $ps->uif_employee + $ps->uif_employer + $ps->sdl, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="8" style="text-align:center; padding:40px; color:var(--text-muted);">No payslips for this period</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Tab 4: MIBCO Summary --}}
    <div id="report-mibco" class="rpt-panel" style="display:none;">
        <div style="padding:20px 24px 8px; border-bottom:1px solid var(--border-subtle);">
            <h3 style="margin:0; font-size:16px; color:#f59e0b; font-weight:700;"><i class="fas fa-building-columns"></i> MIBCO Contributions Summary</h3>
            <p style="margin:4px 0 12px; font-size:13px; color:var(--text-muted);">Period: {{ $selectedPeriod->period_start->format('j M Y') }} - {{ $selectedPeriod->period_end->format('j M Y') }}</p>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:24px;">
                <div style="background:rgba(168,85,247,0.06); border:1px solid rgba(168,85,247,0.2); border-radius:var(--radius-md); padding:16px; text-align:center;">
                    <div style="font-size:11px; text-transform:uppercase; color:var(--text-muted); letter-spacing:1px;">Pension</div>
                    <div style="font-size:20px; font-weight:700; color:#a855f7; font-family:var(--font-mono); margin-top:4px;">R {{ number_format($mibcoTotals['pension_employee'] + $mibcoTotals['pension_employer'], 2) }}</div>
                    <div style="font-size:11px; color:var(--text-muted);">EE: R {{ number_format($mibcoTotals['pension_employee'], 2) }} | ER: R {{ number_format($mibcoTotals['pension_employer'], 2) }}</div>
                </div>
                <div style="background:rgba(168,85,247,0.06); border:1px solid rgba(168,85,247,0.2); border-radius:var(--radius-md); padding:16px; text-align:center;">
                    <div style="font-size:11px; text-transform:uppercase; color:var(--text-muted); letter-spacing:1px;">Death & Funeral</div>
                    <div style="font-size:20px; font-weight:700; color:#a855f7; font-family:var(--font-mono); margin-top:4px;">R {{ number_format($mibcoTotals['death_benefit'] + $mibcoTotals['funeral_benefit'], 2) }}</div>
                    <div style="font-size:11px; color:var(--text-muted);">Death: R {{ number_format($mibcoTotals['death_benefit'], 2) }} | Funeral: R {{ number_format($mibcoTotals['funeral_benefit'], 2) }}</div>
                </div>
                <div style="background:rgba(168,85,247,0.06); border:1px solid rgba(168,85,247,0.2); border-radius:var(--radius-md); padding:16px; text-align:center;">
                    <div style="font-size:11px; text-transform:uppercase; color:var(--text-muted); letter-spacing:1px;">Sick Pay</div>
                    <div style="font-size:20px; font-weight:700; color:#a855f7; font-family:var(--font-mono); margin-top:4px;">R {{ number_format($mibcoTotals['sick_pay_fund'], 2) }}</div>
                </div>
                <div style="background:rgba(168,85,247,0.06); border:1px solid rgba(168,85,247,0.2); border-radius:var(--radius-md); padding:16px; text-align:center;">
                    <div style="font-size:11px; text-transform:uppercase; color:var(--text-muted); letter-spacing:1px;">Holiday Fund</div>
                    <div style="font-size:20px; font-weight:700; color:#a855f7; font-family:var(--font-mono); margin-top:4px;">R {{ number_format($mibcoTotals['holiday_fund'], 2) }}</div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-bottom:24px;">
                <div style="background:rgba(5,150,105,0.06); border:1px solid rgba(5,150,105,0.25); border-radius:var(--radius-md); padding:20px; text-align:center;">
                    <div style="font-size:11px; text-transform:uppercase; color:var(--text-muted); letter-spacing:1.5px; margin-bottom:6px;">Employee Total</div>
                    <div style="font-size:24px; font-weight:800; color:var(--accent-green); font-family:var(--font-mono);">R {{ number_format($mibcoTotals['total_employee'], 2) }}</div>
                </div>
                <div style="background:rgba(59,130,246,0.06); border:1px solid rgba(59,130,246,0.25); border-radius:var(--radius-md); padding:20px; text-align:center;">
                    <div style="font-size:11px; text-transform:uppercase; color:var(--text-muted); letter-spacing:1.5px; margin-bottom:6px;">Employer Total</div>
                    <div style="font-size:24px; font-weight:800; color:var(--accent-blue); font-family:var(--font-mono);">R {{ number_format($mibcoTotals['total_employer'], 2) }}</div>
                </div>
                <div style="background:rgba(168,85,247,0.1); border:1px solid rgba(168,85,247,0.35); border-radius:var(--radius-md); padding:20px; text-align:center;">
                    <div style="font-size:11px; text-transform:uppercase; color:var(--text-muted); letter-spacing:1.5px; margin-bottom:6px;">Grand Total MIBCO</div>
                    <div style="font-size:28px; font-weight:800; color:#a855f7; font-family:var(--font-mono);">R {{ number_format($mibcoTotals['total_contribution'], 2) }}</div>
                </div>
            </div>

            <div class="sl-table-wrap">
                <table class="sl-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Pension (EE)</th>
                            <th>Pension (ER)</th>
                            <th>Death</th>
                            <th>Funeral</th>
                            <th>Sick Pay</th>
                            <th>Holiday</th>
                            <th>Total</th>
                            <th class="center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mibco as $idx => $m)
                        <tr>
                            <td style="color:var(--text-muted);">{{ $idx + 1 }}</td>
                            <td style="font-weight:600; color:var(--text-primary);">{{ optional($m->employee)->first_name }} {{ optional($m->employee)->last_name }}</td>
                            <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($m->pension_employee, 2) }}</td>
                            <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($m->pension_employer, 2) }}</td>
                            <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($m->death_benefit, 2) }}</td>
                            <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($m->funeral_benefit, 2) }}</td>
                            <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($m->sick_pay_fund, 2) }}</td>
                            <td style="font-family:var(--font-mono); font-size:13px;">R {{ number_format($m->holiday_fund, 2) }}</td>
                            <td style="font-family:var(--font-mono); font-size:13px; color:#a855f7; font-weight:700;">R {{ number_format($m->total_contribution, 2) }}</td>
                            <td class="center">
                                @if($m->status === 'paid') <span class="sl-tag sl-tag-green">Paid</span>
                                @elseif($m->status === 'submitted') <span class="sl-tag sl-tag-blue">Submitted</span>
                                @else <span class="sl-tag sl-tag-amber">Draft</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="10" style="text-align:center; padding:40px; color:var(--text-muted);">No MIBCO contributions for this period</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@else
<div class="sl-card sl-animate d2">
    <div style="text-align:center; padding:80px 20px; color:var(--text-muted);">
        <i class="fas fa-chart-bar" style="font-size:48px; opacity:0.2; margin-bottom:16px; display:block;"></i>
        <div style="font-size:18px; font-weight:600; margin-bottom:8px;">Select a Pay Period</div>
        <div style="font-size:14px;">Choose a pay period from the dropdown above to view payroll reports.</div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<style>
    .rpt-tab { background:none; border:none; color:var(--text-muted); font-size:13px; font-weight:600; padding:14px 20px; cursor:pointer; display:flex; align-items:center; gap:8px; border-bottom:2px solid transparent; margin-bottom:-2px; transition:all 0.2s ease; font-family:var(--font-body); }
    .rpt-tab:hover { color:var(--text-secondary); }
    .rpt-tab.active { color:#f59e0b; border-bottom-color:#f59e0b; }
</style>
<script>
function showReport(id, btn) {
    document.querySelectorAll('.rpt-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.rpt-panel').forEach(p => p.style.display = 'none');
    document.getElementById('report-' + id).style.display = '';
}
</script>
@endpush
