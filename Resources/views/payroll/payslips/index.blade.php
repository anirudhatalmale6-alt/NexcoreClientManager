@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', 'Payslips - ' . $client->company_name)
@section('page_heading', 'PAYSLIPS')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(2,132,199,0.15), rgba(2,132,199,0.05)); border:1px solid rgba(2,132,199,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-file-invoice-dollar" style="color:#0284c7; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Payslips</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; gap:8px;">
            <a href="{{ route('nexcore.clients.show.payroll.payslips.create', $client->id) }}" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-plus"></i> New Payslip</a>
        </div>
    </div>
</div>

@php
    $totalCount  = $payslips->count();
    $totalGross  = $payslips->sum('gross_pay');
    $totalNet    = $payslips->sum('net_pay');
    $totalPaye   = $payslips->sum('paye');
@endphp

<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card" style="border-color:rgba(2,132,199,0.4);">
        <div class="sl-stat-label">Total Payslips</div>
        <div class="sl-stat-value" style="color:#0284c7;">{{ $totalCount }}</div>
        <div class="sl-stat-meta">All payslips</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(2,132,199,0.4);">
        <div class="sl-stat-label">Total Gross</div>
        <div class="sl-stat-value" style="color:#0284c7; font-size:18px; font-family:var(--font-mono);">R {{ number_format($totalGross, 2) }}</div>
        <div class="sl-stat-meta">Gross earnings</div>
    </div>
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Total Net Pay</div>
        <div class="sl-stat-value" style="color:var(--accent-green); font-size:18px; font-family:var(--font-mono);">R {{ number_format($totalNet, 2) }}</div>
        <div class="sl-stat-meta">Take-home pay</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(239,68,68,0.4);">
        <div class="sl-stat-label">Total PAYE</div>
        <div class="sl-stat-value" style="color:var(--accent-red); font-size:18px; font-family:var(--font-mono);">R {{ number_format($totalPaye, 2) }}</div>
        <div class="sl-stat-meta">Tax withheld</div>
    </div>
</div>

{{-- Tab Navigation --}}
<div class="sl-card sl-animate d3" style="margin-bottom:0; border-bottom:none; border-radius:var(--radius-md) var(--radius-md) 0 0;">
    <div style="display:flex; gap:0; border-bottom:2px solid var(--border-subtle); padding:0 4px; flex-wrap:wrap;">
        <button class="payslip-tab active" data-filter="all" onclick="filterPayslips('all', this)">
            <i class="fas fa-layer-group"></i> All Payslips
            <span class="payslip-tab-count">{{ $totalCount }}</span>
        </button>
        <button class="payslip-tab" data-filter="draft" onclick="filterPayslips('draft', this)">
            <i class="fas fa-file-alt"></i> Draft
            <span class="payslip-tab-count">{{ $payslips->filter(fn($p) => $p->status === 'draft')->count() }}</span>
        </button>
        <button class="payslip-tab" data-filter="processed" onclick="filterPayslips('processed', this)">
            <i class="fas fa-cogs"></i> Processed
            <span class="payslip-tab-count">{{ $payslips->filter(fn($p) => $p->status === 'processed')->count() }}</span>
        </button>
        <button class="payslip-tab" data-filter="finalised" onclick="filterPayslips('finalised', this)">
            <i class="fas fa-check-circle"></i> Finalised
            <span class="payslip-tab-count">{{ $payslips->filter(fn($p) => $p->status === 'finalised')->count() }}</span>
        </button>
    </div>
</div>

{{-- Table --}}
<div class="sl-card" style="border-radius:0 0 var(--radius-md) var(--radius-md); margin-top:0;">
    <div class="sl-table-wrap">
        <table class="sl-table" id="payslipsTable">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Employee</th>
                    <th>Pay Period</th>
                    <th class="right">Basic Salary</th>
                    <th class="right">Gross Pay</th>
                    <th class="right">Deductions</th>
                    <th class="right">Net Pay</th>
                    <th class="right">PAYE</th>
                    <th class="center">Status</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payslips as $idx => $payslip)
                <tr class="payslip-row" data-payslip-status="{{ $payslip->status }}">
                    <td style="color:var(--text-muted);" class="payslip-row-num">{{ $idx + 1 }}</td>
                    <td>
                        <div style="font-weight:600; color:var(--text-primary);">
                            {{ optional($payslip->employee)->first_name }} {{ optional($payslip->employee)->last_name }}
                        </div>
                        @if(optional($payslip->employee)->employee_number)
                            <div style="font-size:12px; color:var(--text-muted); font-family:var(--font-mono); margin-top:2px;">{{ $payslip->employee->employee_number }}</div>
                        @endif
                    </td>
                    <td style="font-size:13px; color:var(--text-secondary);">
                        {{ optional($payslip->payPeriod)->name ?? '-' }}
                    </td>
                    <td class="right" style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">
                        R {{ number_format($payslip->basic_salary, 2) }}
                    </td>
                    <td class="right" style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">
                        R {{ number_format($payslip->gross_pay, 2) }}
                    </td>
                    <td class="right" style="font-family:var(--font-mono); font-size:13px; color:var(--accent-red);">
                        R {{ number_format($payslip->total_deductions, 2) }}
                    </td>
                    <td class="right" style="font-family:var(--font-mono); font-size:13px; color:var(--accent-green); font-weight:600;">
                        R {{ number_format($payslip->net_pay, 2) }}
                    </td>
                    <td class="right" style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">
                        R {{ number_format($payslip->paye, 2) }}
                    </td>
                    <td class="center">
                        @switch($payslip->status)
                            @case('draft')
                                <span class="sl-tag sl-tag-amber">Draft</span>
                                @break
                            @case('processed')
                                <span class="sl-tag sl-tag-blue">Processed</span>
                                @break
                            @case('finalised')
                                <span class="sl-tag sl-tag-green">Finalised</span>
                                @break
                            @default
                                <span class="sl-tag">{{ ucfirst($payslip->status) }}</span>
                        @endswitch
                    </td>
                    <td class="center">
                        <div style="display:flex; gap:6px; justify-content:center;">
                            <a href="{{ route('nexcore.clients.show.payroll.payslips.edit', [$client->id, $payslip->id]) }}" style="color:var(--accent-blue); font-size:15px;" title="Edit"><i class="fas fa-pen"></i></a>
                            <form method="POST" action="{{ route('nexcore.clients.show.payroll.payslips.destroy', [$client->id, $payslip->id]) }}" style="display:inline;" onsubmit="return confirm('Delete this payslip?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:none; border:none; color:var(--accent-red); cursor:pointer; font-size:15px;" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="payslip-empty-row">
                    <td colspan="10" style="text-align:center; padding:60px; color:var(--text-muted);">
                        <i class="fas fa-file-invoice-dollar" style="font-size:40px; opacity:0.2; margin-bottom:16px; display:block;"></i>
                        <div style="font-size:16px; font-weight:600; margin-bottom:6px;">No payslips yet</div>
                        <div style="font-size:13px;">Click "New Payslip" to generate the first payslip for this client</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div id="payslipNoResults" style="display:none; text-align:center; padding:48px; color:var(--text-muted);">
        <i class="fas fa-search" style="font-size:32px; opacity:0.2; margin-bottom:12px; display:block;"></i>
        <div style="font-size:15px; font-weight:600;">No payslips in this category</div>
        <div style="font-size:13px; margin-top:4px;">Try a different tab or add a new payslip</div>
    </div>
</div>
@endsection

@push('styles')
<style>
.payslip-tab {
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:14px 20px;
    font-size:13px;
    font-weight:600;
    font-family:var(--font-body);
    color:var(--text-muted);
    background:none;
    border:none;
    border-bottom:3px solid transparent;
    cursor:pointer;
    transition:all 0.25s ease;
    position:relative;
    top:2px;
    letter-spacing:0.3px;
}
.payslip-tab:hover {
    color:var(--text-primary);
    background:rgba(255,255,255,0.02);
}
.payslip-tab.active {
    color:#0284c7;
    border-bottom-color:#0284c7;
}
.payslip-tab.active .payslip-tab-count {
    background:rgba(2,132,199,0.2);
    color:#0284c7;
}
.payslip-tab-count {
    font-size:11px;
    font-weight:700;
    font-family:var(--font-mono);
    padding:2px 7px;
    border-radius:10px;
    background:rgba(148,163,184,0.1);
    color:var(--text-muted);
    transition:all 0.25s ease;
    min-width:20px;
    text-align:center;
}
.payslip-tab i {
    font-size:12px;
    opacity:0.7;
}
.payslip-tab.active i {
    opacity:1;
}
.payslip-row.hidden-row {
    display:none;
}
th.right, td.right {
    text-align:right;
}
</style>
@endpush

@push('scripts')
<script>
function filterPayslips(filter, btn) {
    document.querySelectorAll('.payslip-tab').forEach(function(t) { t.classList.remove('active'); });
    btn.classList.add('active');

    var rows = document.querySelectorAll('.payslip-row');
    var visibleCount = 0;
    var num = 0;

    rows.forEach(function(row) {
        var status = row.getAttribute('data-payslip-status') || '';
        var show = (filter === 'all') ? true : (status === filter);

        if (show) {
            row.classList.remove('hidden-row');
            num++;
            var numCell = row.querySelector('.payslip-row-num');
            if (numCell) numCell.textContent = num;
            visibleCount++;
        } else {
            row.classList.add('hidden-row');
        }
    });

    var noResults = document.getElementById('payslipNoResults');
    var emptyRow  = document.querySelector('.payslip-empty-row');
    if (visibleCount === 0 && !emptyRow) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}
</script>
@endpush
