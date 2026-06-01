@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', 'Directors - ' . $client->company_name)
@section('page_heading', 'DIRECTORS')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(124,58,237,0.15), rgba(124,58,237,0.05)); border:1px solid rgba(124,58,237,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-user-tie" style="color:var(--accent-purple); font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Directors & Shareholders</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; gap:8px;">
            <a href="{{ route('nexcore.clients.show.directors.create', $client->id) }}" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-plus"></i> New Director</a>
        </div>
    </div>
</div>

<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Total Directors</div>
        <div class="sl-stat-value" style="color:var(--accent-green);">{{ $directors->count() }}</div>
        <div class="sl-stat-meta">All registered directors</div>
    </div>
    <div class="sl-stat-card" style="border-color:var(--accent-purple);">
        <div class="sl-stat-label">Active</div>
        <div class="sl-stat-value" style="color:var(--accent-purple);">{{ $directors->where('is_active', true)->count() }}</div>
        <div class="sl-stat-meta">Currently active</div>
    </div>
    <div class="sl-stat-card amber">
        <div class="sl-stat-label">Total Shareholding</div>
        <div class="sl-stat-value" style="color:var(--accent-amber);">{{ number_format($totalShares, 1) }}%</div>
        <div class="sl-stat-meta">Combined ownership</div>
    </div>
</div>

<div class="sl-card sl-animate d3">
    <div class="sl-card-header">
        <div class="sl-card-title"><i class="fas fa-user-tie"></i> Director Register</div>
        <div style="font-size:13px; color:var(--text-muted);">{{ $directors->count() }} records</div>
    </div>
    <div class="sl-table-wrap">
        <table class="sl-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="width:44px;"></th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>ID Number</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Appointed</th>
                    <th class="center">Shares %</th>
                    <th class="center">Status</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($directors as $idx => $dir)
                <tr>
                    <td style="color:var(--text-muted);">{{ $idx + 1 }}</td>
                    <td>
                        @if($dir->director_photo)
                            <img src="{{ asset($dir->director_photo) }}" style="width:36px; height:36px; object-fit:cover; border-radius:50%; border:2px solid var(--border-subtle);">
                        @else
                            <div style="width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg, rgba(124,58,237,0.2), rgba(59,130,246,0.2)); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:var(--accent-purple);">{{ strtoupper(substr($dir->first_name,0,1) . substr($dir->last_name,0,1)) }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:600; color:var(--text-primary);">{{ $dir->title ? $dir->title->name . ' ' : '' }}{{ $dir->first_name }} {{ $dir->last_name }}</div>
                        @if($dir->nationality && $dir->nationality !== 'South African')
                            <div style="font-size:11px; color:var(--text-muted);">{{ $dir->nationality }}</div>
                        @endif
                    </td>
                    <td>
                        @if($dir->directorType)
                            <span class="sl-tag" style="background:rgba(124,58,237,0.15); color:#a78bfa; border:1px solid rgba(124,58,237,0.3);">{{ $dir->directorType->name }}</span>
                        @else -
                        @endif
                    </td>
                    <td><span style="font-family:var(--font-mono); font-size:13px; color:var(--accent-cyan);">{{ $dir->id_number ?? '-' }}</span></td>
                    <td>
                        @if($dir->email)
                            <a href="mailto:{{ $dir->email }}" style="color:var(--accent-cyan); font-size:13px; text-decoration:none;">{{ $dir->email }}</a>
                        @else -
                        @endif
                    </td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:var(--accent-green);">{{ $dir->mobile_number ?? '-' }}</td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">{{ $dir->appointment_date ? $dir->appointment_date->format('j M Y') : '-' }}</td>
                    <td class="center">
                        @if($dir->shareholding_percentage)
                            <span style="font-family:var(--font-mono); font-size:14px; font-weight:700; color:var(--accent-amber);">{{ number_format($dir->shareholding_percentage, 1) }}%</span>
                        @else
                            <span style="color:var(--text-muted);">-</span>
                        @endif
                    </td>
                    <td class="center">
                        @if($dir->resignation_date)
                            <span class="sl-tag sl-tag-red" style="font-size:10px;">Resigned</span>
                        @elseif($dir->is_active)
                            <span class="sl-status-dot pass"></span>
                        @else
                            <span class="sl-status-dot fail"></span>
                        @endif
                    </td>
                    <td class="center">
                        <div style="display:flex; gap:6px; justify-content:center;">
                            <a href="{{ route('nexcore.clients.show.directors.edit', [$client->id, $dir->id]) }}" style="color:var(--accent-blue); font-size:15px;" title="Edit"><i class="fas fa-pen"></i></a>
                            <form method="POST" action="{{ route('nexcore.clients.show.directors.toggle', [$client->id, $dir->id]) }}" style="display:inline;">@csrf
                                <button type="submit" style="background:none; border:none; color:var(--accent-amber); cursor:pointer; font-size:15px;" title="Toggle"><i class="fas fa-power-off"></i></button>
                            </form>
                            <form method="POST" action="{{ route('nexcore.clients.show.directors.destroy', [$client->id, $dir->id]) }}" style="display:inline;" onsubmit="return confirm('Delete this director?')">@csrf @method('DELETE')
                                <button type="submit" style="background:none; border:none; color:var(--accent-red); cursor:pointer; font-size:15px;" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="11" style="text-align:center; padding:60px; color:var(--text-muted);">
                    <i class="fas fa-user-tie" style="font-size:40px; opacity:0.2; margin-bottom:16px; display:block;"></i>
                    <div style="font-size:16px; font-weight:600; margin-bottom:6px;">No directors yet</div>
                    <div style="font-size:13px;">Click "New Director" to add the first director for this client</div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
