@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', 'Contacts - ' . $client->company_name)
@section('page_heading', 'CONTACTS')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(59,130,246,0.15), rgba(59,130,246,0.05)); border:1px solid rgba(59,130,246,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-address-book" style="color:var(--accent-blue); font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Contacts</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; gap:8px;">
            <a href="{{ route('nexcore.clients.show.contacts.create', $client->id) }}" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-plus"></i> New Contact</a>
        </div>
    </div>
</div>

<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Total Contacts</div>
        <div class="sl-stat-value" style="color:var(--accent-green);">{{ $contacts->count() }}</div>
        <div class="sl-stat-meta">All registered contacts</div>
    </div>
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">Active</div>
        <div class="sl-stat-value" style="color:var(--accent-blue);">{{ $contacts->where('is_active', true)->count() }}</div>
        <div class="sl-stat-meta">Currently active</div>
    </div>
    <div class="sl-stat-card amber">
        <div class="sl-stat-label">Primary</div>
        <div class="sl-stat-value" style="color:var(--accent-amber);">{{ $contacts->where('is_primary', true)->count() }}</div>
        <div class="sl-stat-meta">Primary contact</div>
    </div>
</div>

<div class="sl-card sl-animate d3">
    <div class="sl-card-header">
        <div class="sl-card-title"><i class="fas fa-address-book"></i> Contact List</div>
        <div style="font-size:13px; color:var(--text-muted);">{{ $contacts->count() }} records</div>
    </div>
    <div class="sl-table-wrap">
        <table class="sl-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="width:44px;"></th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Designation</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Office</th>
                    <th class="center">Primary</th>
                    <th class="center">Status</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contacts as $idx => $contact)
                <tr>
                    <td style="color:var(--text-muted);">{{ $idx + 1 }}</td>
                    <td>
                        @if($contact->contact_photo)
                            <img src="{{ asset($contact->contact_photo) }}" style="width:36px; height:36px; object-fit:cover; border-radius:50%; border:2px solid var(--border-subtle);">
                        @else
                            <div style="width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg, rgba(59,130,246,0.2), rgba(124,58,237,0.2)); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:var(--accent-blue);">{{ strtoupper(substr($contact->first_name,0,1) . substr($contact->last_name,0,1)) }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:600; color:var(--text-primary);">{{ $contact->title ? $contact->title->name . ' ' : '' }}{{ $contact->first_name }} {{ $contact->last_name }}</div>
                        @if($contact->id_number)
                            <div style="font-size:11px; font-family:var(--font-mono); color:var(--text-muted);">ID: {{ $contact->id_number }}</div>
                        @endif
                    </td>
                    <td>
                        @if($contact->contactType)
                            <span class="sl-tag sl-tag-blue">{{ $contact->contactType->name }}</span>
                        @else -
                        @endif
                    </td>
                    <td style="font-size:13px; color:var(--text-secondary);">{{ $contact->designation ?? '-' }}</td>
                    <td>
                        @if($contact->email)
                            <a href="mailto:{{ $contact->email }}" style="color:var(--accent-cyan); font-size:13px; text-decoration:none;">{{ $contact->email }}</a>
                        @else -
                        @endif
                    </td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:var(--accent-green);">{{ $contact->mobile_number ?? '-' }}</td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">{{ $contact->office_number ?? '-' }}</td>
                    <td class="center">
                        @if($contact->is_primary) <span style="color:var(--accent-green);"><i class="fas fa-star"></i></span>
                        @else <span style="color:var(--text-muted); opacity:0.3;"><i class="far fa-star"></i></span> @endif
                    </td>
                    <td class="center">@if($contact->is_active) <span class="sl-status-dot pass"></span> @else <span class="sl-status-dot fail"></span> @endif</td>
                    <td class="center">
                        <div style="display:flex; gap:6px; justify-content:center;">
                            <a href="{{ route('nexcore.clients.show.contacts.edit', [$client->id, $contact->id]) }}" style="color:var(--accent-blue); font-size:15px;" title="Edit"><i class="fas fa-pen"></i></a>
                            <form method="POST" action="{{ route('nexcore.clients.show.contacts.toggle', [$client->id, $contact->id]) }}" style="display:inline;">@csrf
                                <button type="submit" style="background:none; border:none; color:var(--accent-amber); cursor:pointer; font-size:15px;" title="Toggle"><i class="fas fa-power-off"></i></button>
                            </form>
                            <form method="POST" action="{{ route('nexcore.clients.show.contacts.destroy', [$client->id, $contact->id]) }}" style="display:inline;" onsubmit="return confirm('Delete this contact?')">@csrf @method('DELETE')
                                <button type="submit" style="background:none; border:none; color:var(--accent-red); cursor:pointer; font-size:15px;" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="11" style="text-align:center; padding:60px; color:var(--text-muted);">
                    <i class="fas fa-address-book" style="font-size:40px; opacity:0.2; margin-bottom:16px; display:block;"></i>
                    <div style="font-size:16px; font-weight:600; margin-bottom:6px;">No contacts yet</div>
                    <div style="font-size:13px;">Click "New Contact" to add the first contact for this client</div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
