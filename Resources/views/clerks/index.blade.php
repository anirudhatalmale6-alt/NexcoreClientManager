@extends('nexcore_client_manager::layouts.app')

@section('title', 'Clerks')
@section('page_heading', 'CLERK MANAGEMENT')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <h1 class="sl-page-title">Clerk Registry</h1>
        <span class="sl-page-subtitle">Manage practice clerks and team members</span>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.clerks.create') }}" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-plus"></i> New Clerk</a>
        </div>
    </div>
</div>

<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Total Clerks</div>
        <div class="sl-stat-value" style="color:var(--accent-green);">{{ number_format($total) }}</div>
        <div class="sl-stat-meta">All registered clerks</div>
    </div>
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">Active</div>
        <div class="sl-stat-value" style="color:var(--accent-blue);">{{ number_format($active) }}</div>
        <div class="sl-stat-meta">Currently active</div>
    </div>
    <div class="sl-stat-card amber">
        <div class="sl-stat-label">Inactive</div>
        <div class="sl-stat-value" style="color:var(--accent-amber);">{{ number_format($inactive) }}</div>
        <div class="sl-stat-meta">Left / disabled</div>
    </div>
</div>

<div class="sl-card sl-animate d3 sl-mb-md">
    <form method="GET" action="{{ route('nexcore.clients.clerks.index') }}">
        <div style="display:flex; gap:12px; align-items:end; flex-wrap:wrap;">
            <div class="sl-field" style="flex:1; min-width:220px;">
                <label>Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, employee number, designation..." style="width:100%;">
            </div>
            <div class="sl-field" style="min-width:140px;">
                <label>Status</label>
                <select name="status" style="width:100%; padding:9px 12px; background:var(--bg-raised); border:1px solid var(--border-default); border-radius:var(--radius-sm); color:var(--text-primary); font-size:14px;">
                    <option value="">All</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="sl-field" style="min-width:160px;">
                <label>Role</label>
                <select name="role" style="width:100%; padding:9px 12px; background:var(--bg-raised); border:1px solid var(--border-default); border-radius:var(--radius-sm); color:var(--text-primary); font-size:14px;">
                    <option value="">All Roles</option>
                    <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="administrator" {{ request('role') === 'administrator' ? 'selected' : '' }}>Administrator</option>
                    <option value="practice_manager" {{ request('role') === 'practice_manager' ? 'selected' : '' }}>Practice Manager</option>
                    <option value="clerk" {{ request('role') === 'clerk' ? 'selected' : '' }}>Clerk</option>
                </select>
            </div>
            <div class="sl-field" style="min-width:180px;">
                <label>Practice</label>
                <select name="practice" style="width:100%; padding:9px 12px; background:var(--bg-raised); border:1px solid var(--border-default); border-radius:var(--radius-sm); color:var(--text-primary); font-size:14px;">
                    <option value="">All Practices</option>
                    @foreach($practices as $p)
                        <option value="{{ $p->id }}" {{ request('practice') == $p->id ? 'selected' : '' }}>{{ $p->trading_name ?: $p->practice_name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex; gap:8px;">
                <button type="submit" class="neon-btn neon-btn-cyan" style="padding:9px 18px;"><i class="fas fa-search"></i> Filter</button>
                <a href="{{ route('nexcore.clients.clerks.index') }}" class="neon-btn neon-btn-ghost" style="padding:9px 18px;"><i class="fas fa-times"></i> Clear</a>
            </div>
        </div>
    </form>
</div>

@php
    $roleColors = [
        'super_admin' => '#ef4444',
        'administrator' => '#a855f7',
        'practice_manager' => '#3b82f6',
        'clerk' => '#22c55e',
    ];
    $roleLabels = [
        'super_admin' => 'Super Admin',
        'administrator' => 'Administrator',
        'practice_manager' => 'Practice Manager',
        'clerk' => 'Clerk',
    ];
@endphp

<div class="sl-card sl-animate d4">
    <div class="sl-card-header">
        <div class="sl-card-title"><i class="fas fa-users-cog"></i> Clerks</div>
        <div style="font-size:13px; color:var(--text-muted);">Showing {{ $clerks->firstItem() ?? 0 }}-{{ $clerks->lastItem() ?? 0 }} of {{ $clerks->total() }}</div>
    </div>
    <div class="sl-table-wrap">
        <table class="sl-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Name</th>
                    <th>Employee #</th>
                    <th>Designation</th>
                    <th>Job Title</th>
                    <th>Role</th>
                    <th>Practices</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th class="center">Status</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clerks as $idx => $item)
                <tr>
                    <td style="color:var(--text-muted);">{{ $clerks->firstItem() + $idx }}</td>
                    <td>
                        <div style="font-weight:600; color:var(--text-primary);">{{ $item->first_name }} {{ $item->last_name }}</div>
                        @if($item->known_as)
                            <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">"{{ $item->known_as }}"</div>
                        @endif
                    </td>
                    <td><span style="font-family:var(--font-mono); font-size:13px; color:var(--accent-cyan);">{{ $item->employee_number ?? '-' }}</span></td>
                    <td><span style="font-size:13px; color:var(--text-secondary);">{{ $item->designation ?? '-' }}</span></td>
                    <td><span style="font-size:13px; color:var(--text-secondary);">{{ $item->job_title ?? '-' }}</span></td>
                    <td>
                        @php $rc = isset($roleColors[$item->role]) ? $roleColors[$item->role] : '#94a3b8'; @endphp
                        <span style="font-size:11px; font-weight:700; color:{{ $rc }}; text-transform:uppercase; letter-spacing:0.5px;">{{ isset($roleLabels[$item->role]) ? $roleLabels[$item->role] : $item->role }}</span>
                    </td>
                    <td>
                        @if(isset($practiceMap[$item->id]))
                            @foreach($practiceMap[$item->id] as $pid)
                                <span class="sl-tag" style="font-size:10px; margin-bottom:2px; display:inline-block;">{{ isset($practiceNames[$pid]) ? $practiceNames[$pid] : 'ID:'.$pid }}</span>
                            @endforeach
                        @else
                            <span style="color:var(--text-muted); font-size:12px;">None</span>
                        @endif
                    </td>
                    <td><span style="font-size:13px; color:var(--accent-blue);">{{ $item->email ?? '-' }}</span></td>
                    <td><span style="font-size:13px; color:var(--text-secondary);">{{ $item->mobile ?? $item->phone ?? '-' }}</span></td>
                    <td class="center">@if($item->is_active) <span class="sl-status-dot pass"></span> @else <span class="sl-status-dot fail"></span> @endif</td>
                    <td class="center">
                        <div style="display:flex; gap:6px; justify-content:center;">
                            <a href="{{ route('nexcore.clients.clerks.edit', $item->id) }}" style="color:var(--accent-blue); font-size:15px;" title="Edit"><i class="fas fa-pen"></i></a>
                            <form method="POST" action="{{ route('nexcore.clients.clerks.toggle', $item->id) }}" style="display:inline;">@csrf
                                <button type="submit" style="background:none; border:none; color:var(--accent-amber); cursor:pointer; font-size:15px;" title="Toggle"><i class="fas fa-power-off"></i></button>
                            </form>
                            <form method="POST" action="{{ route('nexcore.clients.clerks.destroy', $item->id) }}" style="display:inline;" onsubmit="return confirm('Delete this clerk?')">@csrf @method('DELETE')
                                <button type="submit" style="background:none; border:none; color:var(--accent-red); cursor:pointer; font-size:15px;" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="11" style="text-align:center; padding:60px; color:var(--text-muted);">
                    <i class="fas fa-users-cog" style="font-size:40px; opacity:0.2; margin-bottom:16px; display:block;"></i>
                    <div style="font-size:16px; font-weight:600; margin-bottom:6px;">No clerks yet</div>
                    <div style="font-size:13px;">Click "New Clerk" to add your first clerk</div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($clerks->hasPages())
    <div style="display:flex; justify-content:center; padding:16px 0 0; gap:4px;">
        @if($clerks->onFirstPage()) <span class="sl-btn sl-btn-ghost" style="opacity:0.4; padding:6px 12px; font-size:13px;">Prev</span>
        @else <a href="{{ $clerks->previousPageUrl() }}" class="sl-btn sl-btn-ghost" style="padding:6px 12px; font-size:13px;">Prev</a> @endif
        @foreach($clerks->getUrlRange(max(1, $clerks->currentPage()-2), min($clerks->lastPage(), $clerks->currentPage()+2)) as $page => $url)
            @if($page == $clerks->currentPage()) <span class="sl-btn sl-btn-primary" style="padding:6px 12px; font-size:13px;">{{ $page }}</span>
            @else <a href="{{ $url }}" class="sl-btn sl-btn-ghost" style="padding:6px 12px; font-size:13px;">{{ $page }}</a> @endif
        @endforeach
        @if($clerks->hasMorePages()) <a href="{{ $clerks->nextPageUrl() }}" class="sl-btn sl-btn-ghost" style="padding:6px 12px; font-size:13px;">Next</a>
        @else <span class="sl-btn sl-btn-ghost" style="opacity:0.4; padding:6px 12px; font-size:13px;">Next</span> @endif
    </div>
    @endif
</div>
@endsection