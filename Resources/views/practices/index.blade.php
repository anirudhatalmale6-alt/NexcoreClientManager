@extends('nexcore_client_manager::layouts.app')

@section('title', 'Practices')
@section('page_heading', 'PRACTICE MANAGEMENT')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <h1 class="sl-page-title">Practice Registry</h1>
        <span class="sl-page-subtitle">Manage accounting practices and firms</span>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.practices.create') }}" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-plus"></i> New Practice</a>
        </div>
    </div>
</div>

<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Total Practices</div>
        <div class="sl-stat-value" style="color:var(--accent-green);">{{ number_format($total) }}</div>
        <div class="sl-stat-meta">All registered practices</div>
    </div>
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">Active</div>
        <div class="sl-stat-value" style="color:var(--accent-blue);">{{ number_format($active) }}</div>
        <div class="sl-stat-meta">Currently active</div>
    </div>
    <div class="sl-stat-card amber">
        <div class="sl-stat-label">Inactive</div>
        <div class="sl-stat-value" style="color:var(--accent-amber);">{{ number_format($inactive) }}</div>
        <div class="sl-stat-meta">Dormant / disabled</div>
    </div>
</div>

<div class="sl-card sl-animate d3 sl-mb-md">
    <form method="GET" action="{{ route('nexcore.clients.practices.index') }}">
        <div style="display:flex; gap:12px; align-items:end; flex-wrap:wrap;">
            <div class="sl-field" style="flex:1; min-width:220px;">
                <label>Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Practice name, number, registration, email, principal..." style="width:100%;">
            </div>
            <div class="sl-field" style="min-width:160px;">
                <label>Status</label>
                <select name="status" style="width:100%; padding:9px 12px; background:var(--bg-raised); border:1px solid var(--border-default); border-radius:var(--radius-sm); color:var(--text-primary); font-size:14px;">
                    <option value="">All</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="sl-field" style="min-width:160px;">
                <label>Professional Body</label>
                <select name="body" style="width:100%; padding:9px 12px; background:var(--bg-raised); border:1px solid var(--border-default); border-radius:var(--radius-sm); color:var(--text-primary); font-size:14px;">
                    <option value="">All Bodies</option>
                    @foreach($bodies as $body)
                        <option value="{{ $body }}" {{ request('body') == $body ? 'selected' : '' }}>{{ $body }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex; gap:8px;">
                <button type="submit" class="neon-btn neon-btn-cyan" style="padding:9px 18px;"><i class="fas fa-search"></i> Filter</button>
                <a href="{{ route('nexcore.clients.practices.index') }}" class="neon-btn neon-btn-ghost" style="padding:9px 18px;"><i class="fas fa-times"></i> Clear</a>
            </div>
        </div>
    </form>
</div>

<div class="sl-card sl-animate d4">
    <div class="sl-card-header">
        <div class="sl-card-title"><i class="fas fa-briefcase"></i> Practices</div>
        <div style="font-size:13px; color:var(--text-muted);">Showing {{ $practices->firstItem() ?? 0 }}-{{ $practices->lastItem() ?? 0 }} of {{ $practices->total() }}</div>
    </div>
    <div class="sl-table-wrap">
        <table class="sl-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Practice Name</th>
                    <th>Practice No.</th>
                    <th>Professional Body</th>
                    <th>Principal</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th class="center">BEE</th>
                    <th class="center">VAT</th>
                    <th class="center">Status</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($practices as $idx => $item)
                <tr>
                    <td style="color:var(--text-muted);">{{ $practices->firstItem() + $idx }}</td>
                    <td>
                        <div style="font-weight:600; color:var(--text-primary);">{{ $item->practice_name }}</div>
                        @if($item->trading_name && $item->trading_name !== $item->practice_name)
                            <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">t/a {{ $item->trading_name }}</div>
                        @endif
                    </td>
                    <td><span style="font-family:var(--font-mono); font-size:13px; color:var(--accent-cyan);">{{ $item->practice_number ?? '-' }}</span></td>
                    <td><span class="sl-tag sl-tag-amber" style="font-weight:700;">{{ $item->professional_body ?? '-' }}</span></td>
                    <td>
                        <div style="font-weight:500; color:var(--text-primary); font-size:13px;">{{ $item->principal_name ?? '-' }}</div>
                        @if($item->principal_designation)
                            <div style="font-size:11px; color:var(--text-muted);">{{ $item->principal_designation }}</div>
                        @endif
                    </td>
                    <td><span style="font-size:13px; color:var(--accent-blue);">{{ $item->email ?? '-' }}</span></td>
                    <td><span style="font-size:13px; color:var(--text-secondary);">{{ $item->phone_number ?? '-' }}</span></td>
                    <td class="center"><span class="sl-tag" style="font-size:11px; font-weight:700; color:var(--accent-green);">{{ $item->bbbee_level ?? '-' }}</span></td>
                    <td class="center">
                        @if($item->is_vat_registered)
                            <span style="color:var(--accent-green); font-weight:700; font-size:12px;">YES</span>
                        @else
                            <span style="color:var(--text-muted); font-size:12px;">NO</span>
                        @endif
                    </td>
                    <td class="center">@if($item->is_active) <span class="sl-status-dot pass"></span> @else <span class="sl-status-dot fail"></span> @endif</td>
                    <td class="center">
                        <div style="display:flex; gap:6px; justify-content:center;">
                            <a href="{{ route('nexcore.clients.practices.edit', $item->id) }}" style="color:var(--accent-blue); font-size:15px;" title="Edit"><i class="fas fa-pen"></i></a>
                            <form method="POST" action="{{ route('nexcore.clients.practices.toggle', $item->id) }}" style="display:inline;">@csrf
                                <button type="submit" style="background:none; border:none; color:var(--accent-amber); cursor:pointer; font-size:15px;" title="Toggle"><i class="fas fa-power-off"></i></button>
                            </form>
                            <form method="POST" action="{{ route('nexcore.clients.practices.destroy', $item->id) }}" style="display:inline;" onsubmit="return confirm('Delete this practice?')">@csrf @method('DELETE')
                                <button type="submit" style="background:none; border:none; color:var(--accent-red); cursor:pointer; font-size:15px;" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="11" style="text-align:center; padding:60px; color:var(--text-muted);">
                    <i class="fas fa-briefcase" style="font-size:40px; opacity:0.2; margin-bottom:16px; display:block;"></i>
                    <div style="font-size:16px; font-weight:600; margin-bottom:6px;">No practices yet</div>
                    <div style="font-size:13px;">Click "New Practice" to add your first practice</div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($practices->hasPages())
    <div style="display:flex; justify-content:center; padding:16px 0 0; gap:4px;">
        @if($practices->onFirstPage()) <span class="sl-btn sl-btn-ghost" style="opacity:0.4; padding:6px 12px; font-size:13px;">Prev</span>
        @else <a href="{{ $practices->previousPageUrl() }}" class="sl-btn sl-btn-ghost" style="padding:6px 12px; font-size:13px;">Prev</a> @endif
        @foreach($practices->getUrlRange(max(1, $practices->currentPage()-2), min($practices->lastPage(), $practices->currentPage()+2)) as $page => $url)
            @if($page == $practices->currentPage()) <span class="sl-btn sl-btn-primary" style="padding:6px 12px; font-size:13px;">{{ $page }}</span>
            @else <a href="{{ $url }}" class="sl-btn sl-btn-ghost" style="padding:6px 12px; font-size:13px;">{{ $page }}</a> @endif
        @endforeach
        @if($practices->hasMorePages()) <a href="{{ $practices->nextPageUrl() }}" class="sl-btn sl-btn-ghost" style="padding:6px 12px; font-size:13px;">Next</a>
        @else <span class="sl-btn sl-btn-ghost" style="opacity:0.4; padding:6px 12px; font-size:13px;">Next</span> @endif
    </div>
    @endif
</div>
@endsection