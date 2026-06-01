@extends('nexcore_client_manager::layouts.app')

@section('title', 'All Clients')
@section('page_heading', 'ALL CLIENTS')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <h1 class="sl-page-title">Client Registry</h1>
        <span class="sl-page-subtitle">Manage your complete client portfolio</span>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.create') }}" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-plus"></i> New Client</a>
        </div>
    </div>
</div>

<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Total Clients</div>
        <div class="sl-stat-value" style="color:var(--accent-green);">{{ number_format($stats['total'] ?? 0) }}</div>
        <div class="sl-stat-meta">All registered companies</div>
    </div>
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">Active</div>
        <div class="sl-stat-value" style="color:var(--accent-blue);">{{ number_format($stats['active'] ?? 0) }}</div>
        <div class="sl-stat-meta">Currently active</div>
    </div>
    <div class="sl-stat-card amber">
        <div class="sl-stat-label">Inactive</div>
        <div class="sl-stat-value" style="color:var(--accent-amber);">{{ number_format($stats['inactive'] ?? 0) }}</div>
        <div class="sl-stat-meta">Dormant / disabled</div>
    </div>
</div>

<div class="sl-card sl-animate d3 sl-mb-md">
    <form method="GET" action="{{ route('nexcore.clients.index') }}">
        <div style="display:flex; gap:12px; align-items:end; flex-wrap:wrap;">
            <div class="sl-field" style="flex:1; min-width:220px;">
                <label>Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Company name, code, reg number, tax number..." style="width:100%;">
            </div>
            <div class="sl-field" style="min-width:160px;">
                <label>Status</label>
                <select name="status" style="width:100%; padding:9px 12px; background:var(--bg-raised); border:1px solid var(--border-default); border-radius:var(--radius-sm); color:var(--text-primary); font-size:14px;">
                    <option value="">All</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="sl-field" style="min-width:180px;">
                <label>Company Type</label>
                <select name="company_type" style="width:100%; padding:9px 12px; background:var(--bg-raised); border:1px solid var(--border-default); border-radius:var(--radius-sm); color:var(--text-primary); font-size:14px;">
                    <option value="">All Types</option>
                    @foreach($companyTypes as $type)
                        <option value="{{ $type->id }}" {{ request('company_type') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex; gap:8px;">
                <button type="submit" class="neon-btn neon-btn-cyan" style="padding:9px 18px;"><i class="fas fa-search"></i> Filter</button>
                <a href="{{ route('nexcore.clients.index') }}" class="neon-btn neon-btn-ghost" style="padding:9px 18px;"><i class="fas fa-times"></i> Clear</a>
            </div>
        </div>
    </form>
</div>

<div class="sl-card sl-animate d4">
    <div class="sl-card-header">
        <div class="sl-card-title"><i class="fas fa-building"></i> Clients</div>
        <div style="font-size:13px; color:var(--text-muted);">Showing {{ $clients->firstItem() ?? 0 }}-{{ $clients->lastItem() ?? 0 }} of {{ $clients->total() }}</div>
    </div>
    <div class="sl-table-wrap">
        <table class="sl-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="width:44px;"></th>
                    <th>Company</th>
                    <th>Code</th>
                    <th>Reg Number</th>
                    <th>Tax Number</th>
                    <th class="center">Status</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $idx => $item)
                <tr ondblclick="window.location='{{ route('nexcore.clients.show.dashboard', $item->id) }}'" style="cursor:pointer;">
                    <td style="color:var(--text-muted);">{{ $clients->firstItem() + $idx }}</td>
                    <td>
                        @if($item->client_logo)
                            <img src="{{ asset($item->client_logo) }}" alt="{{ $item->client_code }}" style="width:32px; height:32px; object-fit:contain; border-radius:6px; background:rgba(255,255,255,0.08); padding:2px;">
                        @else
                            <div style="width:32px; height:32px; border-radius:6px; background:var(--bg-raised); display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; color:var(--text-muted);">{{ substr($item->client_code ?? '?', 0, 3) }}</div>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('nexcore.clients.show.dashboard', $item->id) }}" style="color:var(--text-primary); text-decoration:none; font-weight:600;" onmouseover="this.style.color='var(--accent-cyan)'" onmouseout="this.style.color='var(--text-primary)'">{{ $item->company_name }}</a>
                        @if($item->trading_name && $item->trading_name !== $item->company_name)
                            <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">t/a {{ $item->trading_name }}</div>
                        @endif
                    </td>
                    <td><span class="sl-tag sl-tag-amber" style="font-family:var(--font-mono); font-weight:700;">{{ $item->client_code ?? '-' }}</span></td>
                    <td><span style="font-family:var(--font-mono); font-size:13px; color:var(--accent-cyan);">{{ $item->registration_number ?? '-' }}</span></td>
                    <td><span style="font-family:var(--font-mono); font-size:13px; color:var(--accent-green);">{{ $item->tax_number ?? '-' }}</span></td>
                    <td class="center">@if($item->is_active) <span class="sl-status-dot pass"></span> @else <span class="sl-status-dot fail"></span> @endif</td>
                    <td class="center">
                        <div style="display:flex; gap:6px; justify-content:center;">
                            <a href="{{ route('nexcore.clients.show.dashboard', $item->id) }}" style="color:var(--accent-cyan); font-size:15px;" title="Dashboard"><i class="fas fa-th-large"></i></a>
                            <a href="{{ route('nexcore.clients.show.edit', $item->id) }}" style="color:var(--accent-blue); font-size:15px;" title="Edit"><i class="fas fa-pen"></i></a>
                            <form method="POST" action="{{ route('nexcore.clients.show.toggle', $item->id) }}" style="display:inline;">@csrf
                                <button type="submit" style="background:none; border:none; color:var(--accent-amber); cursor:pointer; font-size:15px;" title="Toggle"><i class="fas fa-power-off"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center; padding:60px; color:var(--text-muted);">
                    <i class="fas fa-building" style="font-size:40px; opacity:0.2; margin-bottom:16px; display:block;"></i>
                    <div style="font-size:16px; font-weight:600; margin-bottom:6px;">No clients yet</div>
                    <div style="font-size:13px;">Click "New Client" to add your first company</div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($clients->hasPages())
    <div style="display:flex; justify-content:center; padding:16px 0 0; gap:4px;">
        @if($clients->onFirstPage()) <span class="sl-btn sl-btn-ghost" style="opacity:0.4; padding:6px 12px; font-size:13px;">Prev</span>
        @else <a href="{{ $clients->previousPageUrl() }}" class="sl-btn sl-btn-ghost" style="padding:6px 12px; font-size:13px;">Prev</a> @endif
        @foreach($clients->getUrlRange(max(1, $clients->currentPage()-2), min($clients->lastPage(), $clients->currentPage()+2)) as $page => $url)
            @if($page == $clients->currentPage()) <span class="sl-btn sl-btn-primary" style="padding:6px 12px; font-size:13px;">{{ $page }}</span>
            @else <a href="{{ $url }}" class="sl-btn sl-btn-ghost" style="padding:6px 12px; font-size:13px;">{{ $page }}</a> @endif
        @endforeach
        @if($clients->hasMorePages()) <a href="{{ $clients->nextPageUrl() }}" class="sl-btn sl-btn-ghost" style="padding:6px 12px; font-size:13px;">Next</a>
        @else <span class="sl-btn sl-btn-ghost" style="opacity:0.4; padding:6px 12px; font-size:13px;">Next</span> @endif
    </div>
    @endif
</div>
@endsection