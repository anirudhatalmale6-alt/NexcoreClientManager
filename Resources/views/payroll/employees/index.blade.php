@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', 'Employees - ' . $client->company_name)
@section('page_heading', 'EMPLOYEES')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(5,150,105,0.15), rgba(5,150,105,0.05)); border:1px solid rgba(5,150,105,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-users" style="color:#059669; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Employees</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; gap:8px;">
            <a href="{{ route('nexcore.clients.show.payroll.employees.create', $client->id) }}" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-plus"></i> New Employee</a>
        </div>
    </div>
</div>

@php
    $totalCount      = $employees->count();
    $activeCount     = $employees->filter(fn($e) => $e->employment_status === 'active')->count();
    $probationCount  = $employees->filter(fn($e) => $e->employment_status === 'probation')->count();
    $suspendedCount  = $employees->filter(fn($e) => $e->employment_status === 'suspended')->count();
    $terminatedCount = $employees->filter(fn($e) => $e->employment_status === 'terminated')->count();
    $totalPayroll    = $employees->filter(fn($e) => $e->employment_status === 'active')->sum('basic_salary');
@endphp

<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Total Employees</div>
        <div class="sl-stat-value" style="color:#059669;">{{ $totalCount }}</div>
        <div class="sl-stat-meta">All employees</div>
    </div>
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Active</div>
        <div class="sl-stat-value" style="color:var(--accent-green);">{{ $activeCount }}</div>
        <div class="sl-stat-meta">Currently employed</div>
    </div>
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">On Probation</div>
        <div class="sl-stat-value" style="color:var(--accent-blue);">{{ $probationCount }}</div>
        <div class="sl-stat-meta">Probation period</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(239,68,68,0.4);">
        <div class="sl-stat-label">Terminated</div>
        <div class="sl-stat-value" style="color:var(--accent-red);">{{ $terminatedCount }}</div>
        <div class="sl-stat-meta">Employment ended</div>
    </div>
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Total Monthly Payroll</div>
        <div class="sl-stat-value" style="color:#059669; font-size:20px;">R {{ number_format($totalPayroll, 2) }}</div>
        <div class="sl-stat-meta">Active employees</div>
    </div>
</div>

{{-- Tab Navigation --}}
<div class="sl-card sl-animate d3" style="margin-bottom:0; border-bottom:none; border-radius:var(--radius-md) var(--radius-md) 0 0;">
    <div style="display:flex; gap:0; border-bottom:2px solid var(--border-subtle); padding:0 4px; flex-wrap:wrap;">
        <button class="emp-tab active" data-filter="all" onclick="filterEmployees('all', this)">
            <i class="fas fa-layer-group"></i> All
            <span class="emp-tab-count">{{ $totalCount }}</span>
        </button>
        <button class="emp-tab" data-filter="active" onclick="filterEmployees('active', this)">
            <i class="fas fa-check-circle"></i> Active
            <span class="emp-tab-count">{{ $activeCount }}</span>
        </button>
        <button class="emp-tab" data-filter="probation" onclick="filterEmployees('probation', this)">
            <i class="fas fa-hourglass-half"></i> Probation
            <span class="emp-tab-count">{{ $probationCount }}</span>
        </button>
        <button class="emp-tab" data-filter="suspended" onclick="filterEmployees('suspended', this)">
            <i class="fas fa-pause-circle"></i> Suspended
            <span class="emp-tab-count">{{ $suspendedCount }}</span>
        </button>
        <button class="emp-tab" data-filter="terminated" onclick="filterEmployees('terminated', this)">
            <i class="fas fa-user-slash"></i> Terminated
            <span class="emp-tab-count">{{ $terminatedCount }}</span>
        </button>
    </div>
</div>

{{-- Table --}}
<div class="sl-card" style="border-radius:0 0 var(--radius-md) var(--radius-md); margin-top:0;">
    <div class="sl-table-wrap">
        <table class="sl-table" id="employeesTable">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Employee No</th>
                    <th>Name</th>
                    <th>ID Number</th>
                    <th>Position</th>
                    <th>Department</th>
                    <th>Salary Type</th>
                    <th>Basic Salary</th>
                    <th>Start Date</th>
                    <th class="center">Status</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $idx => $employee)
                <tr class="emp-row" data-employment-status="{{ $employee->employment_status }}">
                    <td style="color:var(--text-muted);" class="emp-row-num">{{ $idx + 1 }}</td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">
                        {{ $employee->employee_number ?? '-' }}
                    </td>
                    <td>
                        <div style="font-weight:600; color:var(--text-primary);">
                            {{ $employee->first_name }} {{ $employee->last_name }}
                        </div>
                        @if($employee->email)
                            <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">{{ \Illuminate\Support\Str::limit($employee->email, 40) }}</div>
                        @endif
                    </td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">
                        {{ $employee->id_number ?? '-' }}
                    </td>
                    <td style="font-size:13px; color:var(--text-secondary);">{{ $employee->position ?? '-' }}</td>
                    <td style="font-size:13px; color:var(--text-secondary);">{{ $employee->department ?? '-' }}</td>
                    <td>
                        <span style="font-size:13px; color:var(--text-secondary);">{{ ucfirst($employee->salary_type) }}</span>
                    </td>
                    <td>
                        <span style="font-family:var(--font-mono); font-size:13px; color:#059669; font-weight:600;">
                            R {{ number_format($employee->basic_salary, 2) }}
                        </span>
                    </td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">
                        {{ $employee->start_date ? $employee->start_date->format('j M Y') : '-' }}
                    </td>
                    <td class="center">
                        @switch($employee->employment_status)
                            @case('active')
                                <span class="sl-tag sl-tag-green">Active</span>
                                @break
                            @case('probation')
                                <span class="sl-tag sl-tag-blue">Probation</span>
                                @break
                            @case('suspended')
                                <span class="sl-tag sl-tag-amber">Suspended</span>
                                @break
                            @case('terminated')
                                <span class="sl-tag sl-tag-red">Terminated</span>
                                @break
                            @default
                                <span class="sl-tag">{{ ucfirst($employee->employment_status) }}</span>
                        @endswitch
                    </td>
                    <td class="center">
                        <div style="display:flex; gap:6px; justify-content:center;">
                            <a href="{{ route('nexcore.clients.show.payroll.employees.edit', [$client->id, $employee->id]) }}" style="color:var(--accent-blue); font-size:15px;" title="Edit"><i class="fas fa-pen"></i></a>
                            <form method="POST" action="{{ route('nexcore.clients.show.payroll.employees.destroy', [$client->id, $employee->id]) }}" style="display:inline;" onsubmit="return confirm('Delete this employee?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:none; border:none; color:var(--accent-red); cursor:pointer; font-size:15px;" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="emp-empty-row">
                    <td colspan="11" style="text-align:center; padding:60px; color:var(--text-muted);">
                        <i class="fas fa-users" style="font-size:40px; opacity:0.2; margin-bottom:16px; display:block;"></i>
                        <div style="font-size:16px; font-weight:600; margin-bottom:6px;">No employees yet</div>
                        <div style="font-size:13px;">Click "New Employee" to add the first employee for this client</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div id="empNoResults" style="display:none; text-align:center; padding:48px; color:var(--text-muted);">
        <i class="fas fa-search" style="font-size:32px; opacity:0.2; margin-bottom:12px; display:block;"></i>
        <div style="font-size:15px; font-weight:600;">No employees in this category</div>
        <div style="font-size:13px; margin-top:4px;">Try a different tab or add a new employee</div>
    </div>
</div>
@endsection

@push('styles')
<style>
.emp-tab {
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
.emp-tab:hover {
    color:var(--text-primary);
    background:rgba(255,255,255,0.02);
}
.emp-tab.active {
    color:#059669;
    border-bottom-color:#059669;
}
.emp-tab.active .emp-tab-count {
    background:rgba(5,150,105,0.2);
    color:#059669;
}
.emp-tab-count {
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
.emp-tab i {
    font-size:12px;
    opacity:0.7;
}
.emp-tab.active i {
    opacity:1;
}
.emp-row.hidden-row {
    display:none;
}
</style>
@endpush

@push('scripts')
<script>
function filterEmployees(filter, btn) {
    document.querySelectorAll('.emp-tab').forEach(function(t) { t.classList.remove('active'); });
    btn.classList.add('active');

    var rows = document.querySelectorAll('.emp-row');
    var visibleCount = 0;
    var num = 0;

    rows.forEach(function(row) {
        var status = row.getAttribute('data-employment-status') || '';
        var show = filter === 'all' || status === filter;

        if (show) {
            row.classList.remove('hidden-row');
            num++;
            var numCell = row.querySelector('.emp-row-num');
            if (numCell) numCell.textContent = num;
            visibleCount++;
        } else {
            row.classList.add('hidden-row');
        }
    });

    var noResults = document.getElementById('empNoResults');
    var emptyRow  = document.querySelector('.emp-empty-row');
    if (visibleCount === 0 && !emptyRow) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}
</script>
@endpush
