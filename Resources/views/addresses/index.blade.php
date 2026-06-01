@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', 'Addresses - ' . $client->company_name)
@section('page_heading', 'ADDRESSES')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(6,182,212,0.15), rgba(6,182,212,0.05)); border:1px solid rgba(6,182,212,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-map-marker-alt" style="color:var(--accent-cyan); font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Addresses</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; gap:8px;">
            <a href="{{ route('nexcore.clients.show.addresses.create', $client->id) }}" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-plus"></i> New Address</a>
        </div>
    </div>
</div>

<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Total Addresses</div>
        <div class="sl-stat-value" style="color:var(--accent-green);">{{ $addresses->count() }}</div>
        <div class="sl-stat-meta">All registered addresses</div>
    </div>
    <div class="sl-stat-card cyan" style="border-color:var(--accent-cyan);">
        <div class="sl-stat-label">Active</div>
        <div class="sl-stat-value" style="color:var(--accent-cyan);">{{ $addresses->where('is_active', true)->count() }}</div>
        <div class="sl-stat-meta">Currently active</div>
    </div>
    <div class="sl-stat-card amber">
        <div class="sl-stat-label">Primary</div>
        <div class="sl-stat-value" style="color:var(--accent-amber);">{{ $addresses->where('is_primary', true)->count() }}</div>
        <div class="sl-stat-meta">Primary address</div>
    </div>
</div>

<div class="sl-card sl-animate d3">
    <div class="sl-card-header">
        <div class="sl-card-title"><i class="fas fa-map-marker-alt"></i> Address List</div>
        <div style="font-size:13px; color:var(--text-muted);">{{ $addresses->count() }} records</div>
    </div>
    <div class="sl-table-wrap">
        <table class="sl-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Label</th>
                    <th>Type</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>Province</th>
                    <th>Postal Code</th>
                    <th class="center">Primary</th>
                    <th class="center">Status</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($addresses as $idx => $addr)
                <tr>
                    <td style="color:var(--text-muted);">{{ $idx + 1 }}</td>
                    <td>
                        <span style="font-weight:600; color:var(--text-primary);">{{ $addr->address_label ?? '-' }}</span>
                    </td>
                    <td>
                        @if($addr->addressType)
                            <span class="sl-tag sl-tag-blue">{{ $addr->addressType->name }}</span>
                        @else
                            <span style="color:var(--text-muted);">-</span>
                        @endif
                    </td>
                    <td>
                        <div style="font-size:13px; color:var(--text-primary);">{{ $addr->address_line_1 }}</div>
                        @if($addr->address_line_2)
                            <div style="font-size:12px; color:var(--text-muted);">{{ $addr->address_line_2 }}</div>
                        @endif
                        @if($addr->suburb)
                            <div style="font-size:12px; color:var(--text-muted);">{{ $addr->suburb }}</div>
                        @endif
                    </td>
                    <td style="font-size:13px; color:var(--text-secondary);">{{ $addr->city }}</td>
                    <td style="font-size:13px; color:var(--text-secondary);">{{ $addr->province->name ?? '-' }}</td>
                    <td><span style="font-family:var(--font-mono); font-size:13px; color:var(--accent-cyan); font-weight:600;">{{ $addr->postal_code ?? '-' }}</span></td>
                    <td class="center">
                        @if($addr->is_primary)
                            <span style="color:var(--accent-green); font-size:14px;" title="Primary"><i class="fas fa-star"></i></span>
                        @else
                            <span style="color:var(--text-muted); font-size:14px; opacity:0.3;"><i class="far fa-star"></i></span>
                        @endif
                    </td>
                    <td class="center">
                        @if($addr->is_active) <span class="sl-status-dot pass"></span> @else <span class="sl-status-dot fail"></span> @endif
                    </td>
                    <td class="center">
                        <div style="display:flex; gap:6px; justify-content:center;">
                            <a href="{{ route('nexcore.clients.show.addresses.edit', [$client->id, $addr->id]) }}" style="color:var(--accent-blue); font-size:15px;" title="Edit"><i class="fas fa-pen"></i></a>
                            <form method="POST" action="{{ route('nexcore.clients.show.addresses.toggle', [$client->id, $addr->id]) }}" style="display:inline;">@csrf
                                <button type="submit" style="background:none; border:none; color:var(--accent-amber); cursor:pointer; font-size:15px;" title="Toggle"><i class="fas fa-power-off"></i></button>
                            </form>
                            <form method="POST" action="{{ route('nexcore.clients.show.addresses.destroy', [$client->id, $addr->id]) }}" style="display:inline;" onsubmit="return confirm('Delete this address?')">@csrf @method('DELETE')
                                <button type="submit" style="background:none; border:none; color:var(--accent-red); cursor:pointer; font-size:15px;" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" style="text-align:center; padding:60px; color:var(--text-muted);">
                    <i class="fas fa-map-marker-alt" style="font-size:40px; opacity:0.2; margin-bottom:16px; display:block;"></i>
                    <div style="font-size:16px; font-weight:600; margin-bottom:6px;">No addresses yet</div>
                    <div style="font-size:13px;">Click "New Address" to add the first address for this client</div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
