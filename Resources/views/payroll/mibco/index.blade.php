@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', 'MIBCO Contributions - ' . $client->company_name)
@section('page_heading', 'MIBCO CONTRIBUTIONS')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(168,85,247,0.15), rgba(168,85,247,0.05)); border:1px solid rgba(168,85,247,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-building-columns" style="color:#a855f7; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">MIBCO Contributions</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; gap:12px; align-items:center;">
            <form method="GET" action="{{ route('nexcore.clients.show.payroll.mibco', $client->id) }}" style="display:flex; gap:10px; align-items:center;">
                <label style="font-size:13px; color:var(--text-muted); font-weight:600; white-space:nowrap;">PERIOD:</label>
                <select name="period_id" onchange="this.form.submit()" style="min-width:300px; background:var(--bg-raised); color:var(--text-primary); border:1px solid rgba(168,85,247,0.3); border-radius:var(--radius-sm); padding:10px 14px; font-size:14px; font-family:var(--font-body); cursor:pointer; appearance:auto; outline:none;">
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" {{ $selectedPeriodId == $p->id ? 'selected' : '' }} style="background:var(--bg-surface); color:var(--text-primary);">
                            {{ $p->period_start->format('j M Y') }} - {{ $p->period_end->format('j M Y') }} ({{ ucfirst($p->pay_frequency) }})
                        </option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('nexcore.clients.show.payroll.mibco.create', $client->id) }}" class="neon-btn neon-btn-purple neon-pulse"><i class="fas fa-plus"></i> New Contribution</a>
        </div>
    </div>
</div>

@php
    $totalCount     = $contributions->count();
    $draftCount     = $contributions->filter(fn($c) => $c->status === 'draft')->count();
    $submittedCount = $contributions->filter(fn($c) => $c->status === 'submitted')->count();
    $paidCount      = $contributions->filter(fn($c) => $c->status === 'paid')->count();
    $totalAmount    = $contributions->sum('total_contribution');
    $employeeTotal  = $contributions->sum('total_employee');
    $employerTotal  = $contributions->sum('total_employer');
@endphp

<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card" style="border-color:rgba(168,85,247,0.4);">
        <div class="sl-stat-label">Total Records</div>
        <div class="sl-stat-value" style="color:#a855f7;">{{ $totalCount }}</div>
        <div class="sl-stat-meta">All contributions</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(168,85,247,0.4);">
        <div class="sl-stat-label">Total Contributions</div>
        <div class="sl-stat-value" style="color:#a855f7; font-size:20px;">R {{ number_format($totalAmount, 2) }}</div>
        <div class="sl-stat-meta">Combined total</div>
    </div>
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Employee Share</div>
        <div class="sl-stat-value" style="color:var(--accent-green); font-size:20px;">R {{ number_format($employeeTotal, 2) }}</div>
        <div class="sl-stat-meta">Employee contributions</div>
    </div>
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">Employer Share</div>
        <div class="sl-stat-value" style="color:var(--accent-blue); font-size:20px;">R {{ number_format($employerTotal, 2) }}</div>
        <div class="sl-stat-meta">Employer contributions</div>
    </div>
</div>

{{-- Tab Navigation --}}
<div class="sl-card sl-animate d3" style="margin-bottom:0; border-bottom:none; border-radius:var(--radius-md) var(--radius-md) 0 0;">
    <div style="display:flex; gap:0; border-bottom:2px solid var(--border-subtle); padding:0 4px; flex-wrap:wrap;">
        <button class="mib-tab active" data-filter="all" onclick="filterMibco('all', this)">
            <i class="fas fa-layer-group"></i> All
            <span class="mib-tab-count">{{ $totalCount }}</span>
        </button>
        <button class="mib-tab" data-filter="draft" onclick="filterMibco('draft', this)">
            <i class="fas fa-pencil-alt"></i> Draft
            <span class="mib-tab-count">{{ $draftCount }}</span>
        </button>
        <button class="mib-tab" data-filter="submitted" onclick="filterMibco('submitted', this)">
            <i class="fas fa-paper-plane"></i> Submitted
            <span class="mib-tab-count">{{ $submittedCount }}</span>
        </button>
        <button class="mib-tab" data-filter="paid" onclick="filterMibco('paid', this)">
            <i class="fas fa-check-double"></i> Paid
            <span class="mib-tab-count">{{ $paidCount }}</span>
        </button>
    </div>
</div>

{{-- Table --}}
<div class="sl-card" style="border-radius:0 0 var(--radius-md) var(--radius-md); margin-top:0;">
    <div class="sl-table-wrap">
        <table class="sl-table" id="mibcoTable">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Employee</th>
                    <th>Pay Period</th>
                    <th>Pension (EE)</th>
                    <th>Pension (ER)</th>
                    <th>Death</th>
                    <th>Funeral</th>
                    <th>Sick Pay</th>
                    <th>Total</th>
                    <th class="center">Status</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contributions as $idx => $c)
                <tr class="mib-row" data-mibco-status="{{ $c->status }}">
                    <td style="color:var(--text-muted);" class="mib-row-num">{{ $idx + 1 }}</td>
                    <td>
                        <div style="font-weight:600; color:var(--text-primary);">
                            {{ optional($c->employee)->first_name }} {{ optional($c->employee)->last_name }}
                        </div>
                        <div style="font-size:12px; color:var(--text-muted);">{{ optional($c->employee)->employee_number ?? '-' }}</div>
                    </td>
                    <td>
                        @if($c->payPeriod)
                            <div style="font-size:13px; color:var(--text-secondary);">{{ $c->payPeriod->period_start->format('j M Y') }}</div>
                            <div style="font-size:11px; color:var(--text-muted);">to {{ $c->payPeriod->period_end->format('j M Y') }}</div>
                        @else
                            <span style="color:var(--text-muted);">-</span>
                        @endif
                    </td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">R {{ number_format($c->pension_employee, 2) }}</td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">R {{ number_format($c->pension_employer, 2) }}</td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">R {{ number_format($c->death_benefit, 2) }}</td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">R {{ number_format($c->funeral_benefit, 2) }}</td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">R {{ number_format($c->sick_pay_fund, 2) }}</td>
                    <td>
                        <span style="font-family:var(--font-mono); font-size:13px; color:#a855f7; font-weight:600;">
                            R {{ number_format($c->total_contribution, 2) }}
                        </span>
                    </td>
                    <td class="center">
                        @switch($c->status)
                            @case('draft')
                                <span class="sl-tag sl-tag-amber">Draft</span>
                                @break
                            @case('submitted')
                                <span class="sl-tag sl-tag-blue">Submitted</span>
                                @break
                            @case('paid')
                                <span class="sl-tag sl-tag-green">Paid</span>
                                @break
                        @endswitch
                    </td>
                    <td class="center">
                        <div style="display:flex; gap:6px; justify-content:center;">
                            <a href="{{ route('nexcore.clients.show.payroll.mibco.edit', [$client->id, $c->id]) }}" style="color:var(--accent-blue); font-size:15px;" title="Edit"><i class="fas fa-pen"></i></a>
                            <form method="POST" action="{{ route('nexcore.clients.show.payroll.mibco.destroy', [$client->id, $c->id]) }}" style="display:inline;" onsubmit="return confirm('Delete this MIBCO contribution?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:none; border:none; color:var(--accent-red); cursor:pointer; font-size:15px;" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="mib-empty-row">
                    <td colspan="11" style="text-align:center; padding:60px; color:var(--text-muted);">
                        <i class="fas fa-building-columns" style="font-size:40px; opacity:0.2; margin-bottom:16px; display:block;"></i>
                        <div style="font-size:16px; font-weight:600; margin-bottom:6px;">No MIBCO contributions yet</div>
                        <div style="font-size:13px;">Click "New Contribution" to record MIBCO levies.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .mib-tab { background:none; border:none; color:var(--text-muted); font-size:13px; font-weight:600; padding:14px 20px; cursor:pointer; display:flex; align-items:center; gap:8px; border-bottom:2px solid transparent; margin-bottom:-2px; transition:all 0.2s ease; font-family:var(--font-body); }
    .mib-tab:hover { color:var(--text-secondary); }
    .mib-tab.active { color:#a855f7; border-bottom-color:#a855f7; }
    .mib-tab-count { font-family:var(--font-mono); font-size:11px; background:rgba(168,85,247,0.1); color:#a855f7; padding:2px 8px; border-radius:10px; }
    .mib-tab.active .mib-tab-count { background:rgba(168,85,247,0.2); }
</style>
<script>
function filterMibco(status, btn) {
    document.querySelectorAll('.mib-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    let rows = document.querySelectorAll('.mib-row');
    let num = 0;
    rows.forEach(r => {
        if (status === 'all' || r.dataset.mibcoStatus === status) {
            r.style.display = '';
            num++;
            r.querySelector('.mib-row-num').textContent = num;
        } else {
            r.style.display = 'none';
        }
    });
    let empty = document.querySelector('.mib-empty-row');
    if (empty) empty.style.display = num === 0 ? '' : 'none';
}
</script>
@endpush
