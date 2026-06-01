@extends('nexcore_client_manager::layouts.nerve-centre')

@section('title', $client->company_name . ' — Nerve Centre')
@section('topbar_module', 'Client Manager')
@section('topbar_page', $client->company_name)

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('content')

<div class="sl-animate d1">
    <div class="sl-page-header" style="display:flex; align-items:center; justify-content:space-between;">
        <div id="nxHeaderDefault" style="display:flex; align-items:center; gap:16px;">
            @if($client->client_logo)
                <div style="width:52px; height:52px; border-radius:12px; background:rgba(255,255,255,0.06); border:1px solid var(--border-subtle); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
                    <img src="{{ asset($client->client_logo) }}" alt="{{ $client->client_code }}" style="width:100%; height:100%; object-fit:contain; padding:4px;">
                </div>
            @else
                <div style="width:52px; height:52px; border-radius:12px; background:var(--bg-raised); border:1px solid var(--border-subtle); display:flex; align-items:center; justify-content:center; font-size:16px; font-weight:800; color:var(--text-muted); font-family:var(--font-mono); letter-spacing:1px; flex-shrink:0;">{{ substr($client->client_code ?? '?', 0, 3) }}</div>
            @endif
            <div>
                <h1 class="sl-page-title" style="margin:0;">{{ $client->company_name }}</h1>
                <div style="display:flex; align-items:center; gap:10px; margin-top:4px;">
                    <span class="sl-tag sl-tag-amber" style="font-family:var(--font-mono); font-weight:700; font-size:13px;">{{ $client->client_code }}</span>
                    @if($client->companyType)
                        <span class="sl-tag sl-tag-blue">{{ $client->companyType->name }}</span>
                    @endif
                    @if($client->is_active)
                        <span class="sl-tag sl-tag-green">Active</span>
                    @else
                        <span class="sl-tag sl-tag-red">Inactive</span>
                    @endif
                </div>
            </div>
        </div>
        <div id="nxHeaderDocView" style="display:none; align-items:center; gap:14px;">
            <div style="width:46px; height:46px; border-radius:12px; background:linear-gradient(135deg, rgba(124,58,237,0.15), rgba(139,92,246,0.1)); border:1px solid rgba(124,58,237,0.3); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i class="fas fa-folder-open" style="color:#a78bfa; font-size:18px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Document Manager <span style="color:rgba(255,255,255,0.2); margin:0 10px;">|</span> {{ $client->company_name }}</h1>
                <div style="display:flex; align-items:center; gap:10px; margin-top:4px;">
                    <span class="sl-tag sl-tag-amber" style="font-family:var(--font-mono); font-weight:700; font-size:13px;">{{ $client->client_code }}</span>
                </div>
            </div>
        </div>
        <div id="nxHeaderUploadBtn" style="display:none;">
            <button type="button" class="nxd-upload-trigger" onclick="nxToggleUploadForm()">
                <i class="fas fa-cloud-upload-alt"></i> Upload Document
            </button>
        </div>
    </div>
</div>

{{-- Document Stats Cards --}}
@php
    $now = \Carbon\Carbon::now();
    $topTotalDocs    = $documents->count();
    $topActiveDocs   = $documents->filter(function($d) { return $d->is_active; })->count();
    $topExpiringSoon = $documents->filter(function($d) use ($now) {
        return $d->expiry_date && $d->expiry_date->isFuture() && $d->expiry_date->diffInDays($now) <= 30;
    })->count();
    $topExpiredDocs  = $documents->filter(function($d) {
        return $d->expiry_date && $d->expiry_date->isPast();
    })->count();
@endphp
<div class="sl-animate d2">
    <div class="nxd-stats-bar" style="margin-bottom:0;">
        <div class="nxd-stat-pill nxd-sp-total">
            <div class="nxd-stat-icon"><i class="fas fa-folder-open"></i></div>
            <div>
                <div class="nxd-stat-num">{{ $topTotalDocs }}</div>
                <div class="nxd-stat-label">Total Documents</div>
            </div>
        </div>
        <div class="nxd-stat-pill nxd-sp-active">
            <div class="nxd-stat-icon"><i class="fas fa-check-circle"></i></div>
            <div>
                <div class="nxd-stat-num">{{ $topActiveDocs }}</div>
                <div class="nxd-stat-label">Active</div>
            </div>
        </div>
        <div class="nxd-stat-pill nxd-sp-expiring">
            <div class="nxd-stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div>
                <div class="nxd-stat-num">{{ $topExpiringSoon }}</div>
                <div class="nxd-stat-label">Expiring Soon</div>
            </div>
        </div>
        <div class="nxd-stat-pill nxd-sp-expired">
            <div class="nxd-stat-icon"><i class="fas fa-times-circle"></i></div>
            <div>
                <div class="nxd-stat-num">{{ $topExpiredDocs }}</div>
                <div class="nxd-stat-label">Expired</div>
            </div>
        </div>
    </div>
</div>

{{-- Main Navigation Tabs --}}
<div class="sl-animate d3" id="nxMainNavTabs" style="margin-top:20px; margin-bottom:0;">
    <div class="nx-quick-tabs">
        <a href="javascript:void(0);" class="nx-tab-badge nx-tab-white nx-tab-active" data-tab="company" onclick="nxSwitchTab('company', this)">
            <i class="fas fa-building"></i>
            <span class="nx-tab-label">Company</span>
        </a>
        <a href="javascript:void(0);" class="nx-tab-badge nx-tab-cyan" data-tab="addresses" onclick="nxSwitchTab('addresses', this)">
            <i class="fas fa-map-marker-alt"></i>
            <span class="nx-tab-label">Addresses</span>
            @if($addresses->count())<span class="nx-tab-count">{{ $addresses->count() }}</span>@endif
        </a>
        <a href="javascript:void(0);" class="nx-tab-badge nx-tab-blue" data-tab="contacts" onclick="nxSwitchTab('contacts', this)">
            <i class="fas fa-address-book"></i>
            <span class="nx-tab-label">Contacts</span>
            @if($contacts->count())<span class="nx-tab-count">{{ $contacts->count() }}</span>@endif
        </a>
        <a href="javascript:void(0);" class="nx-tab-badge nx-tab-green" data-tab="banking" onclick="nxSwitchTab('banking', this)">
            <i class="fas fa-landmark"></i>
            <span class="nx-tab-label">Banking</span>
            @if($bankAccounts->count())<span class="nx-tab-count">{{ $bankAccounts->count() }}</span>@endif
        </a>
        <a href="javascript:void(0);" class="nx-tab-badge nx-tab-amber" data-tab="directors" onclick="nxSwitchTab('directors', this)">
            <i class="fas fa-user-tie"></i>
            <span class="nx-tab-label">Directors</span>
            @if($directors->count())<span class="nx-tab-count">{{ $directors->count() }}</span>@endif
        </a>
        <a href="javascript:void(0);" class="nx-tab-badge nx-tab-red" data-tab="tasks" onclick="nxSwitchTab('tasks', this)">
            <i class="fas fa-tasks"></i>
            <span class="nx-tab-label">Tasks</span>
            @if($tasks->count())<span class="nx-tab-count">{{ $tasks->count() }}</span>@endif
        </a>
        {{-- Documents tab removed - accessed via sidebar --}}
        <a href="javascript:void(0);" class="nx-tab-badge nx-tab-purple" data-tab="audit" onclick="nxSwitchTab('audit', this)">
            <i class="fas fa-history"></i>
            <span class="nx-tab-label">Audit Trail</span>
            @if($auditTrail->count())<span class="nx-tab-count">{{ $auditTrail->count() }}</span>@endif
        </a>
    </div>
</div>

{{-- ============================================================
     TAB CONTENT PANELS
     ============================================================ --}}
<div id="nx-tab-frame" style="margin-top:20px;">

    {{-- COMPANY PANEL --}}
    <div class="nx-panel" id="panel-company" style="display:block;">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            <div class="sl-card">
                <div class="sl-card-header">
                    <div class="sl-card-title"><i class="fas fa-building"></i> Company Details</div>
                </div>
                <div class="sl-result-row">
                    <span class="sl-result-label">Company Name</span>
                    <span class="sl-result-value" style="font-weight:600;">{{ $client->company_name }}</span>
                </div>
                @if($client->trading_name)
                <div class="sl-result-row">
                    <span class="sl-result-label">Trading As</span>
                    <span class="sl-result-value">{{ $client->trading_name }}</span>
                </div>
                @endif
                <div class="sl-result-row">
                    <span class="sl-result-label">Registration No.</span>
                    <span class="sl-result-value" style="font-family:var(--font-mono); font-weight:600; color:var(--accent-cyan);">{{ $client->registration_number ?? '-' }}</span>
                </div>
                <div class="sl-result-row">
                    <span class="sl-result-label">Company Type</span>
                    <span class="sl-result-value">{{ $client->companyType->name ?? '-' }}</span>
                </div>
                <div class="sl-result-row">
                    <span class="sl-result-label">SIC Code</span>
                    <span class="sl-result-value">
                        @if($client->sicCode)
                            <span style="font-family:var(--font-mono); color:var(--accent-green); font-weight:600;">{{ $client->sicCode->sic_code }}</span>
                            <span style="font-size:12px; color:var(--text-muted); margin-left:6px;">{{ $client->sicCode->description }}</span>
                        @else - @endif
                    </span>
                </div>
                <div class="sl-result-row">
                    <span class="sl-result-label">Incorporated</span>
                    <span class="sl-result-value" style="font-family:var(--font-mono); font-size:13px;">{{ $client->date_incorporated ? $client->date_incorporated->format('j M Y') : '-' }}</span>
                </div>
                <div class="sl-result-row">
                    <span class="sl-result-label">Commenced Trading</span>
                    <span class="sl-result-value" style="font-family:var(--font-mono); font-size:13px;">{{ $client->date_commenced_trading ? $client->date_commenced_trading->format('j M Y') : '-' }}</span>
                </div>
            </div>
            <div class="sl-card">
                <div class="sl-card-header">
                    <div class="sl-card-title" style="color:var(--accent-cyan);"><i class="fas fa-file-invoice-dollar"></i> Tax & Registration Numbers</div>
                </div>
                <div class="sl-result-row">
                    <span class="sl-result-label">Income Tax Number</span>
                    <span class="sl-result-value" style="font-family:var(--font-mono); font-size:16px; font-weight:700; color:var(--accent-green); letter-spacing:1px;">{{ $client->tax_number ?? '-' }}</span>
                </div>
                <div class="sl-result-row">
                    <span class="sl-result-label">VAT Number</span>
                    <span class="sl-result-value" style="font-family:var(--font-mono); font-size:16px; font-weight:700; color:var(--accent-blue); letter-spacing:1px;">{{ $client->vat_number ?? '-' }}</span>
                </div>
                <div class="sl-result-row">
                    <span class="sl-result-label">PAYE Number</span>
                    <span class="sl-result-value" style="font-family:var(--font-mono); font-size:14px; font-weight:600; color:var(--accent-amber);">{{ $client->paye_number ?? '-' }}</span>
                </div>
                <div class="sl-result-row">
                    <span class="sl-result-label">SDL Number</span>
                    <span class="sl-result-value" style="font-family:var(--font-mono); font-size:14px; font-weight:600; color:var(--text-secondary);">{{ $client->sdl_number ?? '-' }}</span>
                </div>
                <div class="sl-result-row">
                    <span class="sl-result-label">UIF Number</span>
                    <span class="sl-result-value" style="font-family:var(--font-mono); font-size:14px; font-weight:600; color:var(--text-secondary);">{{ $client->uif_number ?? '-' }}</span>
                </div>
                <div class="sl-result-row">
                    <span class="sl-result-label">COIDA Number</span>
                    <span class="sl-result-value" style="font-family:var(--font-mono); font-size:14px; font-weight:600; color:var(--text-secondary);">{{ $client->coida_number ?? '-' }}</span>
                </div>
            </div>
        </div>
        @if($client->description)
        <div class="sl-card" style="margin-top:20px;">
            <div class="sl-card-header"><div class="sl-card-title"><i class="fas fa-align-left"></i> Description</div></div>
            <div style="padding:20px; color:var(--text-secondary); font-size:14px; line-height:1.8;">{!! nl2br(e($client->description)) !!}</div>
        </div>
        @endif
        <div class="sl-card" style="margin-top:20px;">
            <div class="sl-card-header"><div class="sl-card-title"><i class="fas fa-cog"></i> Status & Audit</div></div>
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:0;">
                <div class="sl-result-row" style="border-right:1px solid var(--border-subtle);"><span class="sl-result-label">Active</span><span class="sl-result-value">@if($client->is_active) <span class="sl-status-dot pass"></span> Yes @else <span class="sl-status-dot fail"></span> No @endif</span></div>
                <div class="sl-result-row" style="border-right:1px solid var(--border-subtle);"><span class="sl-result-label">Created</span><span class="sl-result-value" style="font-family:var(--font-mono); font-size:13px;">{{ $client->created_at ? $client->created_at->format('j M Y H:i') : '-' }}</span></div>
                <div class="sl-result-row" style="border-right:1px solid var(--border-subtle);"><span class="sl-result-label">Last Updated</span><span class="sl-result-value" style="font-family:var(--font-mono); font-size:13px;">{{ $client->updated_at ? $client->updated_at->format('j M Y H:i') : '-' }}</span></div>
                <div class="sl-result-row"><span class="sl-result-label">Client Code</span><span class="sl-result-value" style="font-family:var(--font-mono); font-weight:700; color:var(--accent-amber);">{{ $client->client_code }}</span></div>
            </div>
        </div>
    </div>

    {{-- ADDRESSES PANEL --}}
    <div class="nx-panel" id="panel-addresses" style="display:none;">
        <div class="sl-card">
            <div class="sl-card-header">
                <div class="sl-card-title"><i class="fas fa-map-marker-alt" style="color:#00d2d3;"></i> Addresses</div>
                <button type="button" onclick="nxToggleLinkPanel()" class="neon-btn neon-btn-green" style="font-size:12px; padding:6px 14px;" id="btnLinkAddress"><i class="fas fa-link"></i> Link Address</button>
            </div>

            {{-- LINK ADDRESS PANEL (hidden by default) --}}
            <div id="nxLinkAddressPanel" style="display:none; padding:20px; border-bottom:1px solid var(--border-subtle); background:rgba(0,210,211,0.02);">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
                    <div style="width:36px; height:36px; border-radius:8px; background:rgba(0,210,211,0.1); border:1px solid rgba(0,210,211,0.2); display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-search" style="color:#00d2d3; font-size:14px;"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:14px; font-weight:700; color:var(--text-primary); text-transform:uppercase; letter-spacing:1px;">Link Existing Address</div>
                        <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">Select from the central address registry or type to filter</div>
                    </div>
                    <div style="display:flex; gap:8px; align-items:center;">
                        <a href="{{ route('nexcore.clients.show.addresses.create', $client->id) }}" class="nx-add-new-badge"><i class="fas fa-plus"></i> Add New Address</a>
                        <button type="button" onclick="nxToggleLinkPanel()" class="nx-close-badge" title="Close"><i class="fas fa-times"></i> Close</button>
                    </div>
                </div>

                {{-- Link details row --}}
                <div style="display:grid; grid-template-columns:1fr 1fr auto; gap:12px; margin-bottom:16px;">
                    <div>
                        <label style="font-size:12px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:4px;">Address Type</label>
                        <select id="nxLinkType" style="width:100%; padding:8px 12px; background:var(--bg-raised); border:1px solid var(--border-default); border-radius:6px; color:var(--text-primary); font-size:14px;">
                            <option value="">-- Select Type --</option>
                            @foreach($addressTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:4px;">Label</label>
                        <input type="text" id="nxLinkLabel" placeholder="e.g. Head Office, Factory..." style="width:100%; padding:8px 12px; background:var(--bg-raised); border:1px solid var(--border-default); border-radius:6px; color:var(--text-primary); font-size:14px;">
                    </div>
                    <div style="display:flex; align-items:flex-end; padding-bottom:2px;">
                        <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-size:13px; font-weight:600; color:var(--accent-green); white-space:nowrap;">
                            <input type="checkbox" id="nxLinkPrimary" style="width:16px; height:16px; accent-color:var(--accent-green);">
                            <i class="fas fa-star" style="font-size:11px;"></i> Primary
                        </label>
                    </div>
                </div>

                {{-- Search input --}}
                <div style="position:relative; margin-bottom:12px;">
                    <input type="text" id="nxRegistrySearchInput" placeholder="Type to filter addresses..." autocomplete="off" style="width:100%; padding:10px 14px 10px 38px; background:var(--bg-raised); border:1px solid var(--border-default); border-radius:8px; color:var(--text-primary); font-size:15px; transition:border-color 0.2s;">
                    <i class="fas fa-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:14px;"></i>
                </div>

                {{-- Results --}}
                <div id="nxRegistryResults" style="max-height:320px; overflow-y:auto;"></div>

                {{-- Bottom link removed - ADD NEW ADDRESS button is now at top right of panel --}}
            </div>

            {{-- LINKED ADDRESSES LIST --}}
            <div style="padding:20px;">
                @forelse($addresses as $idx => $link)
                @php
                    $a = $link->address;
                    $streetLine = trim(($a->unit_number ? 'Unit ' . $a->unit_number . ', ' : '') . ($a->complex_name ? $a->complex_name . ', ' : '') . $a->street_number . ' ' . $a->street_name);
                    $cityLine = trim($a->city . ', ' . ($a->province ? $a->province->name : '') . ', ' . $a->postal_code, ', ');
                    $mapQuery = trim($streetLine . ', ' . ($a->suburb ? $a->suburb->name . ', ' : '') . $cityLine, ', ');
                @endphp
                <div class="nx-addr-card" id="nxAddrCard{{ $idx }}" style="margin-bottom:12px; transition:all 0.25s ease; border-left:3px solid transparent; padding-left:12px; border-radius:8px;" data-idx="{{ $idx }}">
                    <div style="display:flex; align-items:flex-start; gap:16px;">
                        <div style="flex-shrink:0; width:42px; height:42px; border-radius:10px; background:rgba(0,210,211,0.08); border:1px solid rgba(0,210,211,0.15); display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-map-marker-alt" style="color:#00d2d3; font-size:18px;"></i>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
                                <span style="font-size:16px; font-weight:700; color:var(--text-primary); text-transform:uppercase; letter-spacing:0.5px;">{{ $link->address_label ?? 'Address ' . ($idx + 1) }}</span>
                                <span style="font-size:13px; color:var(--text-muted); background:rgba(255,255,255,0.05); padding:2px 10px; border-radius:6px;">{{ $link->addressType->name ?? '-' }}</span>
                                @if($link->is_primary)<span style="font-size:11px; color:var(--accent-green); font-weight:700; background:rgba(16,185,129,0.1); padding:2px 8px; border-radius:6px;"><i class="fas fa-star" style="font-size:10px;"></i> Primary</span>@endif
                                @if($link->is_active)<span class="nx-status-badge nx-status-active"><i class="fas fa-check-circle"></i> Active</span>@else<span class="nx-status-badge nx-status-inactive"><i class="fas fa-times-circle"></i> Inactive</span>@endif
                            </div>
                            <div style="font-size:15px; color:var(--text-secondary); line-height:1.7;">
                                {{ $streetLine }}
                            </div>
                            <div style="font-size:15px; color:var(--text-secondary); line-height:1.7;">
                                @if($a->suburb){{ $a->suburb->name }}, @endif
{{ $a->city }}@if($a->province), {{ $a->province->name }}@endif
@if($a->postal_code), <span style="font-family:var(--font-mono); color:var(--accent-cyan);">{{ $a->postal_code }}</span>@endif, South Africa
                            </div>
                        </div>
                        <div style="display:flex; gap:8px; align-items:center; flex-shrink:0;">
                            <button type="button" onclick="nxShowMap({{ $idx }})" style="width:36px; height:36px; border-radius:8px; background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.2); display:flex; align-items:center; justify-content:center; color:#10b981; font-size:15px; cursor:pointer; transition:all 0.2s ease;" title="Show on Map" onmouseover="this.style.background='rgba(16,185,129,0.2)';this.style.transform='scale(1.1)'" onmouseout="this.style.background='rgba(16,185,129,0.1)';this.style.transform='scale(1)'"><i class="fas fa-map-marked-alt"></i></button>
                            <a href="{{ route('nexcore.clients.show.addresses.edit', [$client->id, $link->id]) }}" style="width:36px; height:36px; border-radius:8px; background:rgba(59,130,246,0.1); border:1px solid rgba(59,130,246,0.2); display:flex; align-items:center; justify-content:center; color:#3b82f6; font-size:14px; text-decoration:none; transition:all 0.2s ease;" title="Edit" onmouseover="this.style.background='rgba(59,130,246,0.2)';this.style.transform='scale(1.1)'" onmouseout="this.style.background='rgba(59,130,246,0.1)';this.style.transform='scale(1)'"><i class="fas fa-pen"></i></a>
                            <form method="POST" action="{{ route('nexcore.clients.show.addresses.toggle', [$client->id, $link->id]) }}" style="display:inline;">@csrf<button type="submit" style="width:36px; height:36px; border-radius:8px; background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.2); display:flex; align-items:center; justify-content:center; color:#f59e0b; font-size:14px; cursor:pointer; transition:all 0.2s ease;" title="Toggle" onmouseover="this.style.background='rgba(245,158,11,0.2)';this.style.transform='scale(1.1)'" onmouseout="this.style.background='rgba(245,158,11,0.1)';this.style.transform='scale(1)'"><i class="fas fa-power-off"></i></button></form>
                            <form id="nxUnlinkForm{{ $link->id }}" method="POST" action="{{ route('nexcore.clients.show.addresses.destroy', [$client->id, $link->id]) }}" style="display:inline;">@csrf @method('DELETE')</form><button type="button" onclick="nxUnlinkAddress({{ $link->id }}, '{{ strtoupper($link->address_label ?? 'Address ' . ($idx + 1)) }}')" style="width:36px; height:36px; border-radius:8px; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2); display:flex; align-items:center; justify-content:center; color:#ef4444; font-size:14px; cursor:pointer; transition:all 0.2s ease;" title="Unlink" onmouseover="this.style.background='rgba(239,68,68,0.2)';this.style.transform='scale(1.1)'" onmouseout="this.style.background='rgba(239,68,68,0.1)';this.style.transform='scale(1)'"><i class="fas fa-unlink"></i></button>
                        </div>
                    </div>
                </div>
                @empty
                <div style="text-align:center; padding:40px; color:var(--text-muted);">
                    <i class="fas fa-map-marker-alt" style="font-size:28px; opacity:0.2; display:block; margin-bottom:10px;"></i>No addresses linked yet
                    <div style="margin-top:10px;">
                        <button type="button" onclick="nxToggleLinkPanel()" style="font-size:13px; font-weight:700; color:var(--accent-cyan); background:none; border:1px solid rgba(0,210,211,0.3); padding:6px 16px; border-radius:6px; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.borderColor='rgba(0,210,211,0.6)';this.style.background='rgba(0,210,211,0.05)'" onmouseout="this.style.borderColor='rgba(0,210,211,0.3)';this.style.background='none'"><i class="fas fa-link" style="margin-right:4px;"></i> Link an Address</button>
                    </div>
                </div>
                @endforelse
            </div>

            {{-- EMBEDDED GOOGLE MAP --}}
            @if($addresses->count())
            <div style="padding:0 20px 24px;">
                <div class="nx-map-wrapper">
                    {{-- Glow ring behind the map --}}
                    <div class="nx-map-glow"></div>
                    {{-- Map container --}}
                    <div class="nx-map-container">
                        <div id="nxEmbedMap" style="width:100%; height:420px;"></div>
                        {{-- Floating info card overlaid on map --}}
                        <div class="nx-map-infocard" id="nxMapInfoCard">
                            <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                                <div class="nx-map-info-icon">
                                    <i class="fas fa-map-pin"></i>
                                </div>
                                <div style="flex:1; min-width:0;">
                                    <div class="nx-map-info-label" id="nxMapLabel">Location</div>
                                    <div class="nx-map-info-addr" id="nxMapAddress"></div>
                                </div>
                                <div class="nx-map-live-badge">
                                    <span class="nx-map-live-dot"></span>
                                    LIVE
                                </div>
                            </div>
                            <div style="display:flex; align-items:center; justify-content:space-between; padding-top:10px; border-top:1px solid rgba(255,255,255,0.06);">
                                <div class="nx-map-coords" id="nxMapCoordsText">
                                    <i class="fas fa-crosshairs"></i> --
                                </div>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <a id="nxMapWhatsApp" href="#" target="_blank" class="nx-map-wa-btn">
                                        <i class="fab fa-whatsapp"></i> Share
                                    </a>
                                    <a id="nxMapExternal" href="#" target="_blank" class="nx-map-gmaps-btn">
                                        <i class="fas fa-directions"></i> Get Directions
                                    </a>
                                </div>
                            </div>
                        </div>
                        {{-- Subtle vignette edges --}}
                        <div class="nx-map-vignette"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- CONTACTS PANEL --}}
    <div class="nx-panel" id="panel-contacts" style="display:none;">
        <div class="sl-card">
            <div class="sl-card-header">
                <div class="sl-card-title"><i class="fas fa-address-book" style="color:#60a5fa;"></i> Contacts</div>
                <a href="{{ route('nexcore.clients.show.contacts.create', $client->id) }}" class="neon-btn neon-btn-green" style="font-size:12px; padding:6px 14px;"><i class="fas fa-plus"></i> New Contact</a>
            </div>

            @php
                $conAccents = ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ec4899','#06b6d4'];
            @endphp

            <div style="padding:20px;">
                @forelse($contacts as $idx => $con)
                @php $ac = $conAccents[$idx % count($conAccents)]; @endphp
                <div style="margin-bottom:12px; border-left:3px solid {{ $ac }}; padding-left:12px; border-radius:8px; transition:all 0.25s ease;" onmouseover="this.style.background='rgba(255,255,255,0.02)';this.style.borderLeftWidth='4px'" onmouseout="this.style.background='transparent';this.style.borderLeftWidth='3px'">
                    <div style="display:flex; align-items:flex-start; gap:16px;">

                        {{-- Avatar --}}
                        <div style="flex-shrink:0; width:56px; height:56px; border-radius:12px; overflow:hidden; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg, {{ $ac }}, {{ $ac }}99); box-shadow:0 2px 12px {{ $ac }}33;">
                            @if($con->contact_photo)
                                <img src="{{ asset('storage/' . $con->contact_photo) }}" alt="{{ $con->first_name }}" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <span style="font-size:18px; font-weight:800; color:rgba(255,255,255,0.95); letter-spacing:1px;">{{ strtoupper(substr($con->first_name ?? '', 0, 1)) }}{{ strtoupper(substr($con->last_name ?? '', 0, 1)) }}</span>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div style="flex:1; min-width:0;">
                            {{-- Row 1: Name + badges --}}
                            <div style="display:flex; align-items:center; gap:10px; margin-bottom:4px; flex-wrap:wrap;">
                                <span style="font-size:16px; font-weight:700; color:var(--text-primary);">{{ ($con->title ? ($con->title->abbreviation ?: $con->title->name) : '') . ' ' . $con->first_name . ' ' . $con->last_name }}</span>
                                <span style="font-size:13px; color:var(--text-muted); background:rgba(255,255,255,0.05); padding:2px 10px; border-radius:6px;">{{ $con->contactType->name ?? 'Contact' }}</span>
                                @if($con->is_primary)<span style="font-size:11px; color:var(--accent-green); font-weight:700; background:rgba(16,185,129,0.1); padding:2px 8px; border-radius:6px;"><i class="fas fa-star" style="font-size:10px;"></i> Primary</span>@endif
                                @if($con->is_active)<span class="nx-status-badge nx-status-active"><i class="fas fa-check-circle"></i> Active</span>@else<span class="nx-status-badge nx-status-inactive"><i class="fas fa-times-circle"></i> Inactive</span>@endif
                            </div>
                            {{-- Row 2: Designation --}}
                            @if($con->designation)
                            <div style="font-size:13px; color:var(--text-secondary); margin-bottom:6px;">{{ $con->designation }}</div>
                            @endif
                            {{-- Row 3: Contact details flowing naturally --}}
                            <div style="font-size:14px; color:var(--text-secondary); line-height:1.8; display:flex; flex-wrap:wrap; gap:6px 20px;">
                                @if($con->email)
                                <span><i class="fas fa-envelope" style="color:{{ $ac }}; font-size:12px; margin-right:6px;"></i><a href="mailto:{{ $con->email }}" style="color:var(--accent-cyan); text-decoration:none;">{{ $con->email }}</a></span>
                                @endif
                                @if($con->mobile_number)
                                <span><i class="fas fa-mobile-alt" style="color:{{ $ac }}; font-size:12px; margin-right:6px;"></i><span style="font-family:var(--font-mono);">{{ $con->mobile_number }}</span></span>
                                @endif
                                @if($con->office_number)
                                <span><i class="fas fa-phone" style="color:{{ $ac }}; font-size:12px; margin-right:6px;"></i><span style="font-family:var(--font-mono);">{{ $con->office_number }}</span></span>
                                @endif
                                @if($con->fax_number)
                                <span><i class="fas fa-fax" style="color:{{ $ac }}; font-size:12px; margin-right:6px;"></i><span style="font-family:var(--font-mono);">{{ $con->fax_number }}</span></span>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div style="display:flex; gap:8px; align-items:center; flex-shrink:0;">
                            <a href="{{ route('nexcore.clients.show.contacts.edit', [$client->id, $con->id]) }}" style="width:36px; height:36px; border-radius:8px; background:rgba(59,130,246,0.1); border:1px solid rgba(59,130,246,0.2); display:flex; align-items:center; justify-content:center; color:#3b82f6; font-size:14px; text-decoration:none; transition:all 0.2s ease;" title="Edit" onmouseover="this.style.background='rgba(59,130,246,0.2)';this.style.transform='scale(1.1)'" onmouseout="this.style.background='rgba(59,130,246,0.1)';this.style.transform='scale(1)'"><i class="fas fa-pen"></i></a>
                            <form method="POST" action="{{ route('nexcore.clients.show.contacts.toggle', [$client->id, $con->id]) }}" style="display:inline;">@csrf<button type="submit" style="width:36px; height:36px; border-radius:8px; background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.2); display:flex; align-items:center; justify-content:center; color:#f59e0b; font-size:14px; cursor:pointer; transition:all 0.2s ease;" title="Toggle" onmouseover="this.style.background='rgba(245,158,11,0.2)';this.style.transform='scale(1.1)'" onmouseout="this.style.background='rgba(245,158,11,0.1)';this.style.transform='scale(1)'"><i class="fas fa-power-off"></i></button></form>
                            <form method="POST" action="{{ route('nexcore.clients.show.contacts.destroy', [$client->id, $con->id]) }}" style="display:inline;" onsubmit="return confirm('Delete this contact?')">@csrf @method('DELETE')<button type="submit" style="width:36px; height:36px; border-radius:8px; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2); display:flex; align-items:center; justify-content:center; color:#ef4444; font-size:14px; cursor:pointer; transition:all 0.2s ease;" title="Delete" onmouseover="this.style.background='rgba(239,68,68,0.2)';this.style.transform='scale(1.1)'" onmouseout="this.style.background='rgba(239,68,68,0.1)';this.style.transform='scale(1)'"><i class="fas fa-trash-alt"></i></button></form>
                        </div>
                    </div>
                </div>
                @empty
                <div style="text-align:center; padding:40px; color:var(--text-muted);">
                    <i class="fas fa-address-book" style="font-size:28px; opacity:0.2; display:block; margin-bottom:10px;"></i>No contacts linked yet
                    <div style="margin-top:10px;">
                        <a href="{{ route('nexcore.clients.show.contacts.create', $client->id) }}" style="font-size:13px; font-weight:700; color:var(--accent-cyan); background:none; border:1px solid rgba(0,210,211,0.3); padding:6px 16px; border-radius:6px; text-decoration:none; transition:all 0.2s;" onmouseover="this.style.borderColor='rgba(0,210,211,0.6)';this.style.background='rgba(0,210,211,0.05)'" onmouseout="this.style.borderColor='rgba(0,210,211,0.3)';this.style.background='none'"><i class="fas fa-plus" style="margin-right:4px;"></i> Add a Contact</a>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- BANKING PANEL --}}
    <div class="nx-panel" id="panel-banking" style="display:none;">
        <div class="sl-card">
            <div class="sl-card-header">
                <div class="sl-card-title"><i class="fas fa-landmark" style="color:#f59e0b;"></i> Bank Accounts</div>
                <a href="{{ route('nexcore.clients.show.accounting.bank.accounts.create', $client->id) }}" class="neon-btn neon-btn-green" style="font-size:12px; padding:6px 14px;"><i class="fas fa-plus"></i> Link Bank Account</a>
            </div>

            <div style="padding:20px;">
                @forelse($bankAccounts as $ba)
                <div style="margin-bottom:12px; border-left:3px solid #f59e0b; padding-left:12px; border-radius:8px; transition:all 0.25s ease; {{ !$ba->is_active ? 'opacity:0.5;' : '' }}" onmouseover="this.style.background='rgba(255,255,255,0.02)';this.style.borderLeftWidth='4px'" onmouseout="this.style.background='transparent';this.style.borderLeftWidth='3px'">
                    <div style="display:flex; align-items:flex-start; gap:16px;">

                        {{-- Bank Icon --}}
                        <div style="flex-shrink:0; width:42px; height:42px; border-radius:10px; background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.15); display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-university" style="color:#f59e0b; font-size:18px;"></i>
                        </div>

                        {{-- Info --}}
                        <div style="flex:1; min-width:0;">
                            {{-- Row 1: Bank name + status --}}
                            <div style="display:flex; align-items:center; gap:10px; margin-bottom:4px; flex-wrap:wrap;">
                                <span style="font-size:16px; font-weight:700; color:var(--text-primary);">{{ $ba->bank_name }}</span>
                                @if($ba->is_active)<span class="nx-status-badge nx-status-active"><i class="fas fa-check-circle"></i> Active</span>@else<span class="nx-status-badge nx-status-inactive"><i class="fas fa-times-circle"></i> Inactive</span>@endif
                            </div>
                            {{-- Row 2: Account number + type + branch --}}
                            <div style="font-size:14px; color:var(--text-secondary); line-height:1.8; display:flex; flex-wrap:wrap; gap:6px 20px;">
                                <span style="font-family:var(--font-mono); font-weight:700; color:#f59e0b; font-size:15px;">{{ $ba->account_number }}</span>
                                <span>{{ ucfirst($ba->account_type) }} Account</span>
                                @if($ba->branch_code)
                                <span>Branch: <span style="font-family:var(--font-mono);">{{ $ba->branch_code }}</span></span>
                                @endif
                            </div>
                            {{-- Row 3: GL Account link --}}
                            @if($ba->glAccount)
                            <div style="font-size:13px; color:var(--accent-cyan); margin-top:4px;">
                                <i class="fas fa-link" style="font-size:10px; margin-right:4px;"></i>
                                GL: <span style="font-family:var(--font-mono);">{{ $ba->glAccount->account_code }}</span> — {{ $ba->glAccount->account_name }}
                            </div>
                            @endif
                            {{-- Row 4: Transaction stats --}}
                            <div style="display:flex; gap:20px; margin-top:8px; flex-wrap:wrap;">
                                <span style="font-size:13px; color:var(--text-muted);">Transactions: <span style="font-weight:700; color:var(--accent-blue); font-family:var(--font-mono);">{{ $ba->total_transactions }}</span></span>
                                <span style="font-size:13px; color:var(--text-muted);">Unallocated: <span style="font-weight:700; color:{{ $ba->unallocated_count > 0 ? 'var(--accent-red)' : 'var(--accent-green)' }}; font-family:var(--font-mono);">{{ $ba->unallocated_count > 0 ? $ba->unallocated_count : 'All Allocated' }}</span></span>
                                <span style="font-size:13px; color:var(--text-muted);">Posted: <span style="font-weight:700; color:var(--accent-green); font-family:var(--font-mono);">{{ $ba->posted_count }}</span></span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div style="display:flex; gap:8px; align-items:center; flex-shrink:0; flex-wrap:wrap;">
                            <a href="{{ route('nexcore.clients.show.accounting.bank.statements', [$client->id, $ba->id]) }}" style="padding:8px 14px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none; background:rgba(59,130,246,0.1); color:var(--accent-blue); border:1px solid rgba(59,130,246,0.3); display:inline-flex; align-items:center; gap:6px; transition:all 0.15s ease;" onmouseover="this.style.background='rgba(59,130,246,0.2)'" onmouseout="this.style.background='rgba(59,130,246,0.1)'" title="Statements"><i class="fas fa-file-alt"></i> Statements</a>
                            <a href="{{ route('nexcore.clients.show.accounting.bank.import', [$client->id, $ba->id]) }}" style="padding:8px 14px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none; background:rgba(245,158,11,0.1); color:#f59e0b; border:1px solid rgba(245,158,11,0.3); display:inline-flex; align-items:center; gap:6px; transition:all 0.15s ease;" onmouseover="this.style.background='rgba(245,158,11,0.2)'" onmouseout="this.style.background='rgba(245,158,11,0.1)'" title="Import"><i class="fas fa-file-import"></i> Import</a>
                            <a href="{{ route('nexcore.clients.show.accounting.bank.allocate', [$client->id, $ba->id]) }}" style="padding:8px 14px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none; background:rgba(16,185,129,0.1); color:var(--accent-green); border:1px solid rgba(16,185,129,0.3); display:inline-flex; align-items:center; gap:6px; transition:all 0.15s ease;" onmouseover="this.style.background='rgba(16,185,129,0.2)'" onmouseout="this.style.background='rgba(16,185,129,0.1)'" title="Allocate"><i class="fas fa-tags"></i> Allocate</a>
                            <a href="{{ route('nexcore.clients.show.accounting.bank.accounts.edit', [$client->id, $ba->id]) }}" style="width:36px; height:36px; border-radius:8px; background:rgba(59,130,246,0.1); border:1px solid rgba(59,130,246,0.2); display:flex; align-items:center; justify-content:center; color:#3b82f6; font-size:14px; text-decoration:none; transition:all 0.2s ease;" title="Edit" onmouseover="this.style.background='rgba(59,130,246,0.2)';this.style.transform='scale(1.1)'" onmouseout="this.style.background='rgba(59,130,246,0.1)';this.style.transform='scale(1)'"><i class="fas fa-pen"></i></a>
                            <form method="POST" action="{{ route('nexcore.clients.show.accounting.bank.accounts.toggle', [$client->id, $ba->id]) }}" style="display:inline;">@csrf<button type="submit" style="width:36px; height:36px; border-radius:8px; background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.2); display:flex; align-items:center; justify-content:center; color:#f59e0b; font-size:14px; cursor:pointer; transition:all 0.2s ease;" title="Toggle" onmouseover="this.style.background='rgba(245,158,11,0.2)';this.style.transform='scale(1.1)'" onmouseout="this.style.background='rgba(245,158,11,0.1)';this.style.transform='scale(1)'"><i class="fas fa-power-off"></i></button></form>
                        </div>
                    </div>
                </div>
                @empty
                <div style="text-align:center; padding:40px; color:var(--text-muted);">
                    <i class="fas fa-university" style="font-size:28px; opacity:0.2; display:block; margin-bottom:10px;"></i>No bank accounts linked yet
                    <div style="margin-top:10px;">
                        <a href="{{ route('nexcore.clients.show.accounting.bank.accounts.create', $client->id) }}" style="font-size:13px; font-weight:700; color:var(--accent-cyan); background:none; border:1px solid rgba(0,210,211,0.3); padding:6px 16px; border-radius:6px; text-decoration:none; transition:all 0.2s;" onmouseover="this.style.borderColor='rgba(0,210,211,0.6)';this.style.background='rgba(0,210,211,0.05)'" onmouseout="this.style.borderColor='rgba(0,210,211,0.3)';this.style.background='none'"><i class="fas fa-plus" style="margin-right:4px;"></i> Link Bank Account</a>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- DIRECTORS PANEL --}}
    <div class="nx-panel" id="panel-directors" style="display:none;">
        <div class="sl-card">
            <div class="sl-card-header">
                <div class="sl-card-title"><i class="fas fa-user-tie" style="color:#f59e0b;"></i> Directors & Shareholders</div>
                <a href="{{ route('nexcore.clients.show.directors.create', $client->id) }}" class="neon-btn neon-btn-green" style="font-size:12px; padding:6px 14px;"><i class="fas fa-plus"></i> New Director</a>
            </div>
            <div style="overflow-x:auto;">
                <table class="sl-table" style="width:100%;">
                    <thead><tr><th>#</th><th>Name</th><th>Type</th><th>ID Number</th><th>Email</th><th>Mobile</th><th>Appointed</th><th>Shares %</th><th class="center">Status</th><th class="center">Actions</th></tr></thead>
                    <tbody>
                    @forelse($directors as $idx => $dir)
                        <tr>
                            <td style="color:var(--text-muted);">{{ $idx + 1 }}</td>
                            <td style="font-weight:600;">{{ ($dir->title ? ($dir->title->abbreviation ?: $dir->title->name) : '') . ' ' . $dir->first_name . ' ' . $dir->last_name }}</td>
                            <td style="font-size:13px; color:var(--text-secondary);">{{ $dir->directorType->name ?? '-' }}</td>
                            <td style="font-family:var(--font-mono); font-size:13px;">{{ $dir->id_number ?? '-' }}</td>
                            <td style="font-size:13px;">{{ $dir->email ?? '-' }}</td>
                            <td style="font-family:var(--font-mono); font-size:13px;">{{ $dir->mobile_number ?? '-' }}</td>
                            <td style="font-family:var(--font-mono); font-size:13px;">{{ $dir->date_appointed ? $dir->date_appointed->format('j M Y') : '-' }}</td>
                            <td style="font-family:var(--font-mono); font-size:13px; color:var(--accent-amber); font-weight:600;">{{ $dir->shareholding_percentage ? $dir->shareholding_percentage . '%' : '-' }}</td>
                            <td class="center">@if($dir->is_active) <span class="sl-status-dot pass"></span> @else <span class="sl-status-dot fail"></span> @endif</td>
                            <td class="center">
                                <div style="display:flex; gap:6px; justify-content:center;">
                                    <a href="{{ route('nexcore.clients.show.directors.edit', [$client->id, $dir->id]) }}" style="color:var(--accent-blue); font-size:14px;" title="Edit"><i class="fas fa-pen"></i></a>
                                    <form method="POST" action="{{ route('nexcore.clients.show.directors.toggle', [$client->id, $dir->id]) }}" style="display:inline;">@csrf<button type="submit" style="background:none; border:none; color:var(--accent-amber); cursor:pointer; font-size:14px;" title="Toggle"><i class="fas fa-power-off"></i></button></form>
                                    <form method="POST" action="{{ route('nexcore.clients.show.directors.destroy', [$client->id, $dir->id]) }}" style="display:inline;" onsubmit="return confirm('Delete this director?')">@csrf @method('DELETE')<button type="submit" style="background:none; border:none; color:var(--accent-red); cursor:pointer; font-size:14px;" title="Delete"><i class="fas fa-trash"></i></button></form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" style="text-align:center; padding:40px; color:var(--text-muted);"><i class="fas fa-user-tie" style="font-size:28px; opacity:0.2; display:block; margin-bottom:10px;"></i>No directors yet</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ============================================================
         TASKS PANEL
         ============================================================ --}}
    <div class="nx-panel" id="panel-tasks" style="display:none;">
        <div class="sl-card">
            <div class="sl-card-header" style="display:flex; align-items:center; justify-content:space-between;">
                <div class="sl-card-title" style="color:#f87171;"><i class="fas fa-tasks"></i> Tasks</div>
                <a href="{{ route('nexcore.clients.show.tasks.create', $client->id) }}" class="neon-btn neon-btn-green" style="font-size:12px; padding:6px 14px;"><i class="fas fa-plus"></i> New Task</a>
            </div>
            <div class="sl-table-wrap">
                <table class="sl-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Assigned To</th>
                            <th class="center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($tasks as $idx => $task)
                        <tr>
                            <td style="font-family:var(--font-mono); color:var(--text-muted);">{{ $idx + 1 }}</td>
                            <td style="font-weight:600; color:var(--text-primary);">{{ $task->title }}</td>
                            <td>
                                @php
                                    $priColors = ['high' => '#ef4444', 'medium' => '#f59e0b', 'low' => '#10b981'];
                                    $priColor = $priColors[$task->priority ?? 'low'] ?? '#64748b';
                                @endphp
                                <span style="display:inline-flex; align-items:center; font-size:11px; font-weight:700; letter-spacing:0.5px; padding:3px 10px; border-radius:20px; text-transform:uppercase; color:{{ $priColor }}; background:{{ $priColor }}18; border:1px solid {{ $priColor }}33;">{{ ucfirst($task->priority ?? 'Normal') }}</span>
                            </td>
                            <td>
                                @php
                                    $statColors = ['pending' => '#f59e0b', 'in_progress' => '#3b82f6', 'completed' => '#10b981', 'cancelled' => '#64748b'];
                                    $statColor = $statColors[$task->task_status ?? 'pending'] ?? '#64748b';
                                    $statLabel = str_replace('_', ' ', ucfirst($task->task_status ?? 'Pending'));
                                @endphp
                                <span style="display:inline-flex; align-items:center; font-size:11px; font-weight:700; letter-spacing:0.5px; padding:3px 10px; border-radius:20px; text-transform:uppercase; color:{{ $statColor }}; background:{{ $statColor }}18; border:1px solid {{ $statColor }}33;">{{ $statLabel }}</span>
                            </td>
                            <td style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">
                                @if($task->due_date)
                                    {{ $task->due_date->format('j M Y') }}
                                    @if($task->due_date->isPast() && !in_array($task->task_status, ['completed', 'cancelled']))
                                        <span style="color:#ef4444; font-size:10px; font-weight:700; margin-left:4px;">OVERDUE</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td style="color:var(--text-secondary);">{{ $task->assigned_to ?? '-' }}</td>
                            <td class="center">
                                <div style="display:flex; gap:6px; justify-content:center;">
                                    <a href="{{ route('nexcore.clients.show.tasks.edit', [$client->id, $task->id]) }}" class="nx-action-btn nx-action-edit" title="Edit"><i class="fas fa-pen"></i></a>
                                    <form method="POST" action="{{ route('nexcore.clients.show.tasks.destroy', [$client->id, $task->id]) }}" style="display:inline;" onsubmit="return confirm('Delete this task?')">@csrf @method('DELETE')<button type="submit" class="nx-action-btn nx-action-delete" title="Delete"><i class="fas fa-trash"></i></button></form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align:center; padding:40px; color:var(--text-muted);"><i class="fas fa-tasks" style="font-size:28px; opacity:0.2; display:block; margin-bottom:10px;"></i>No tasks yet</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="nx-panel" id="panel-documents" style="display:none;">
    <style>
    /* ═══════════════════════════════════════════════════════════════
       NexCore Documents Panel — Ultra Premium Dark Theme
       ═══════════════════════════════════════════════════════════════ */
    
    /* --- Glass Stats Pills --- */
    .nxd-stats-bar {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }
    .nxd-stat-pill {
        position: relative;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 20px;
        border-radius: 14px;
        background: rgba(255,255,255,0.025);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255,255,255,0.06);
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .nxd-stat-pill:hover {
        border-color: rgba(255,255,255,0.1);
        transform: translateY(-1px);
        box-shadow: 0 8px 32px rgba(0,0,0,0.2);
    }
    .nxd-stat-pill::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
        border-radius: 14px 14px 0 0;
    }
    .nxd-stat-pill.nxd-sp-total::before  { background: linear-gradient(90deg, #7c3aed, #a78bfa); }
    .nxd-stat-pill.nxd-sp-active::before { background: linear-gradient(90deg, #059669, #10b981); }
    .nxd-stat-pill.nxd-sp-expiring::before { background: linear-gradient(90deg, #d97706, #f59e0b); }
    .nxd-stat-pill.nxd-sp-expired::before { background: linear-gradient(90deg, #dc2626, #ef4444); }
    
    .nxd-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        flex-shrink: 0;
    }
    .nxd-sp-total .nxd-stat-icon   { background: rgba(124,58,237,0.12); color: #a78bfa; border: 1px solid rgba(124,58,237,0.2); }
    .nxd-sp-active .nxd-stat-icon  { background: rgba(16,185,129,0.12); color: #34d399; border: 1px solid rgba(16,185,129,0.2); }
    .nxd-sp-expiring .nxd-stat-icon{ background: rgba(245,158,11,0.12); color: #fbbf24; border: 1px solid rgba(245,158,11,0.2); }
    .nxd-sp-expired .nxd-stat-icon { background: rgba(239,68,68,0.12); color: #f87171; border: 1px solid rgba(239,68,68,0.2); }
    
    .nxd-stat-num {
        font-size: 26px;
        font-weight: 800;
        font-family: var(--font-mono);
        line-height: 1;
    }
    .nxd-sp-total .nxd-stat-num   { color: #a78bfa; }
    .nxd-sp-active .nxd-stat-num  { color: #34d399; }
    .nxd-sp-expiring .nxd-stat-num{ color: #fbbf24; }
    .nxd-sp-expired .nxd-stat-num { color: #f87171; }
    
    .nxd-stat-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-muted);
        margin-top: 2px;
    }
    
    /* --- Upload Button --- */
    .nxd-upload-trigger {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 22px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: #fff;
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        border: 1px solid rgba(124,58,237,0.5);
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 16px rgba(124,58,237,0.25);
        text-transform: uppercase;
    }
    .nxd-upload-trigger:hover {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        box-shadow: 0 6px 24px rgba(124,58,237,0.4);
        transform: translateY(-1px);
    }
    .nxd-upload-trigger i {
        font-size: 14px;
    }
    
    /* --- Upload Form Panel --- */
    .nxd-upload-panel {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease, padding 0.4s ease, box-shadow 0.4s ease;
        opacity: 0;
        background: rgba(245,158,11,0.06);
        border: 2px solid rgba(245,158,11,0.7);
        border-radius: 14px;
        margin-bottom: 0;
    }
    .nxd-upload-panel.nxd-open {
        max-height: 800px;
        opacity: 1;
        padding: 24px;
        margin-bottom: 16px;
        box-shadow: 0 0 20px rgba(245,158,11,0.25), 0 0 40px rgba(245,158,11,0.1);
    }
    .nxd-upload-panel .nxd-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .nxd-upload-panel .nxd-form-full {
        grid-column: 1 / -1;
    }
    .nxd-form-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-muted);
        margin-bottom: 6px;
    }
    .nxd-form-input,
    .nxd-form-select,
    .nxd-form-textarea {
        width: 100%;
        padding: 10px 14px;
        background: var(--bg-deepest);
        border: 1px solid var(--border-default);
        border-radius: 8px;
        color: var(--text-primary);
        font-size: 14px;
        font-family: 'Montserrat', sans-serif;
        transition: border-color 0.25s ease, box-shadow 0.25s ease;
        box-sizing: border-box;
    }
    .nxd-form-input:focus,
    .nxd-form-select:focus,
    .nxd-form-textarea:focus {
        outline: none;
        border-color: rgba(124,58,237,0.5);
        box-shadow: 0 0 0 3px rgba(124,58,237,0.1);
    }
    .nxd-form-textarea {
        min-height: 80px;
        resize: vertical;
    }
    
    /* Drag-and-Drop File Zone */
    .nxd-dropzone {
        position: relative;
        padding: 28px 20px;
        border: 2px dashed rgba(124,58,237,0.25);
        border-radius: 12px;
        background: rgba(124,58,237,0.02);
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .nxd-dropzone:hover,
    .nxd-dropzone.nxd-drag-over {
        border-color: rgba(124,58,237,0.5);
        background: rgba(124,58,237,0.06);
        box-shadow: 0 0 20px rgba(124,58,237,0.08);
    }
    .nxd-dropzone-icon {
        font-size: 28px;
        color: #7c3aed;
        margin-bottom: 8px;
        opacity: 0.6;
    }
    .nxd-dropzone-text {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 500;
    }
    .nxd-dropzone-text strong {
        color: #a78bfa;
        font-weight: 700;
    }
    .nxd-dropzone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }
    .nxd-file-preview {
        display: none;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
        padding: 8px 12px;
        background: rgba(124,58,237,0.08);
        border-radius: 8px;
        font-size: 13px;
        color: #c4b5fd;
        font-family: var(--font-mono);
    }
    .nxd-file-preview i {
        color: #7c3aed;
    }
    .nxd-file-preview .nxd-file-remove {
        margin-left: auto;
        color: var(--accent-red);
        cursor: pointer;
        font-size: 14px;
        opacity: 0.7;
        transition: opacity 0.2s;
    }
    .nxd-file-preview .nxd-file-remove:hover {
        opacity: 1;
    }
    
    /* Form Actions */
    .nxd-form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid rgba(255,255,255,0.04);
    }
    .nxd-btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 24px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(124,58,237,0.2);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .nxd-btn-submit:hover {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        box-shadow: 0 6px 20px rgba(124,58,237,0.35);
    }
    .nxd-btn-cancel {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
        background: rgba(255,255,255,0.04);
        border: 1px solid var(--border-subtle);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .nxd-btn-cancel:hover {
        color: var(--text-primary);
        border-color: var(--border-default);
        background: rgba(255,255,255,0.06);
    }
    
    /* --- Category Tabs (within Documents) --- */
    .nxd-category-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
        padding: 0 4px;
    }
    .nxd-tabs-wrap {
        display: flex;
        gap: 0;
        border-bottom: 2px solid var(--border-subtle);
        flex: 1;
        flex-wrap: wrap;
    }
    
    /* --- Document Cards --- */
    .nxd-doc-card {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 18px 20px;
        border-radius: 12px;
        background: rgba(255,255,255,0.015);
        border: 1px solid rgba(255,255,255,0.05);
        margin-bottom: 10px;
        transition: all 0.3s ease;
        overflow: visible;
    }
    .nxd-doc-card::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, #7c3aed, #a78bfa);
        border-radius: 12px 0 0 12px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .nxd-doc-card:hover {
        background: rgba(124,58,237,0.03);
        border-color: rgba(124,58,237,0.12);
        box-shadow: 0 4px 24px rgba(0,0,0,0.15);
        transform: translateX(2px);
    }
    .nxd-doc-card:hover::before {
        opacity: 1;
    }
    
    /* File type icon badge */
    .nxd-filetype-badge {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 900;
        flex-shrink: 0;
        position: relative;
        transition: all 0.3s ease;
    }
    .nxd-doc-card:hover .nxd-filetype-badge { transform: scale(1.05); }
    .nxd-ft-pdf {
        background: linear-gradient(135deg, rgba(239,68,68,0.15), rgba(239,68,68,0.06));
        color: #f87171; border: 1px solid rgba(239,68,68,0.25);
        box-shadow: 0 0 16px rgba(239,68,68,0.1), inset 0 1px 0 rgba(255,255,255,0.05);
    }
    .nxd-doc-card:hover .nxd-ft-pdf { box-shadow: 0 0 24px rgba(239,68,68,0.2), 0 0 8px rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.4); }
    .nxd-ft-pdf i { filter: drop-shadow(0 0 4px rgba(239,68,68,0.4)); }
    .nxd-ft-doc {
        background: linear-gradient(135deg, rgba(59,130,246,0.15), rgba(59,130,246,0.06));
        color: #60a5fa; border: 1px solid rgba(59,130,246,0.25);
        box-shadow: 0 0 16px rgba(59,130,246,0.1), inset 0 1px 0 rgba(255,255,255,0.05);
    }
    .nxd-doc-card:hover .nxd-ft-doc { box-shadow: 0 0 24px rgba(59,130,246,0.2), 0 0 8px rgba(59,130,246,0.1); border-color: rgba(59,130,246,0.4); }
    .nxd-ft-doc i { filter: drop-shadow(0 0 4px rgba(59,130,246,0.4)); }
    .nxd-ft-xls {
        background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(16,185,129,0.06));
        color: #34d399; border: 1px solid rgba(16,185,129,0.25);
        box-shadow: 0 0 16px rgba(16,185,129,0.1), inset 0 1px 0 rgba(255,255,255,0.05);
    }
    .nxd-doc-card:hover .nxd-ft-xls { box-shadow: 0 0 24px rgba(16,185,129,0.2), 0 0 8px rgba(16,185,129,0.1); border-color: rgba(16,185,129,0.4); }
    .nxd-ft-xls i { filter: drop-shadow(0 0 4px rgba(16,185,129,0.4)); }
    .nxd-ft-img {
        background: linear-gradient(135deg, rgba(245,158,11,0.15), rgba(245,158,11,0.06));
        color: #fbbf24; border: 1px solid rgba(245,158,11,0.25);
        box-shadow: 0 0 16px rgba(245,158,11,0.1), inset 0 1px 0 rgba(255,255,255,0.05);
    }
    .nxd-doc-card:hover .nxd-ft-img { box-shadow: 0 0 24px rgba(245,158,11,0.2), 0 0 8px rgba(245,158,11,0.1); border-color: rgba(245,158,11,0.4); }
    .nxd-ft-img i { filter: drop-shadow(0 0 4px rgba(245,158,11,0.4)); }
    .nxd-ft-zip {
        background: linear-gradient(135deg, rgba(139,92,246,0.15), rgba(139,92,246,0.06));
        color: #a78bfa; border: 1px solid rgba(139,92,246,0.25);
        box-shadow: 0 0 16px rgba(139,92,246,0.1), inset 0 1px 0 rgba(255,255,255,0.05);
    }
    .nxd-doc-card:hover .nxd-ft-zip { box-shadow: 0 0 24px rgba(139,92,246,0.2), 0 0 8px rgba(139,92,246,0.1); border-color: rgba(139,92,246,0.4); }
    .nxd-ft-zip i { filter: drop-shadow(0 0 4px rgba(139,92,246,0.4)); }
    .nxd-ft-default {
        background: linear-gradient(135deg, rgba(148,163,184,0.12), rgba(148,163,184,0.04));
        color: #94a3b8; border: 1px solid rgba(148,163,184,0.2);
        box-shadow: 0 0 12px rgba(148,163,184,0.06), inset 0 1px 0 rgba(255,255,255,0.05);
    }
    .nxd-doc-card:hover .nxd-ft-default { box-shadow: 0 0 20px rgba(148,163,184,0.15); border-color: rgba(148,163,184,0.35); }
    .nxd-ft-default i { filter: drop-shadow(0 0 3px rgba(148,163,184,0.3)); }

    .nxd-filetype-ext {
        position: absolute;
        bottom: -3px;
        right: -3px;
        font-size: 8px;
        font-weight: 800;
        padding: 2px 5px;
        border-radius: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        line-height: 1;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }
    .nxd-ft-pdf .nxd-filetype-ext  { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; }
    .nxd-ft-doc .nxd-filetype-ext  { background: linear-gradient(135deg, #3b82f6, #2563eb); color: #fff; }
    .nxd-ft-xls .nxd-filetype-ext  { background: linear-gradient(135deg, #10b981, #059669); color: #fff; }
    .nxd-ft-img .nxd-filetype-ext  { background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; }
    .nxd-ft-zip .nxd-filetype-ext  { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: #fff; }
    .nxd-ft-default .nxd-filetype-ext { background: linear-gradient(135deg, #64748b, #475569); color: #fff; }
    
    /* Doc info area */
    .nxd-doc-info {
        flex: 1;
        min-width: 0;
    }
    .nxd-doc-title-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 4px;
        flex-wrap: wrap;
    }
    .nxd-doc-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-primary);
    }
    .nxd-doc-type-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
        background: rgba(124,58,237,0.12);
        color: #a78bfa;
        border: 1px solid rgba(124,58,237,0.2);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-family: var(--font-mono);
    }
    .nxd-doc-meta {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-top: 6px;
        font-size: 12px;
        color: var(--text-muted);
    }
    .nxd-doc-meta i {
        font-size: 11px;
        margin-right: 4px;
        opacity: 0.6;
    }
    .nxd-doc-meta .nxd-expiry-warn {
        font-weight: 700;
    }
    
    /* Document action menu */
    .nxd-doc-actions {
        position: relative;
        flex-shrink: 0;
    }
    .nxd-menu-trigger {
        width: 38px; height: 38px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.08);
        background: rgba(255,255,255,0.03); color: rgba(255,255,255,0.5); font-size: 18px;
        display: flex; align-items: center; justify-content: center; cursor: pointer;
        transition: all 0.25s ease;
    }
    .nxd-menu-trigger:hover { background: rgba(124,58,237,0.12); border-color: rgba(124,58,237,0.3); color: #a78bfa; }
    .nxd-menu-trigger.active { background: rgba(124,58,237,0.15); border-color: rgba(124,58,237,0.4); color: #c4b5fd; box-shadow: 0 4px 16px rgba(124,58,237,0.2); }
    .nxd-action-menu {
        display: none; position: absolute; top: calc(100% + 6px); right: 0; z-index: 200;
        min-width: 200px; padding: 6px 0; border-radius: 12px;
        background: rgba(15,20,35,0.97); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,0.08); box-shadow: 0 12px 40px rgba(0,0,0,0.5), 0 0 1px rgba(255,255,255,0.1);
    }
    .nxd-action-menu.open { display: block; }
    .nxd-menu-item {
        display: flex; align-items: center; gap: 12px; padding: 10px 16px; font-size: 13px; font-weight: 500;
        color: rgba(255,255,255,0.7); cursor: pointer; transition: all 0.2s ease; text-decoration: none; border: none; background: none; width: 100%;
    }
    .nxd-menu-item:hover { background: rgba(255,255,255,0.05); color: #fff; }
    .nxd-menu-item i { width: 18px; text-align: center; font-size: 14px; }
    .nxd-menu-item.nxd-mi-view i { color: #a78bfa; }
    .nxd-menu-item.nxd-mi-email i { color: #60a5fa; }
    .nxd-menu-item.nxd-mi-wa i { color: #25D366; }
    .nxd-menu-item.nxd-mi-ver i { color: #06b6d4; }
    .nxd-menu-item.nxd-mi-edit i { color: #3b82f6; }
    .nxd-menu-item.nxd-mi-del { color: rgba(239,68,68,0.8); }
    .nxd-menu-item.nxd-mi-del:hover { background: rgba(239,68,68,0.08); color: #ef4444; }
    .nxd-menu-item.nxd-mi-del i { color: #ef4444; }
    .nxd-menu-sep { height: 1px; background: rgba(255,255,255,0.06); margin: 4px 0; }
    
    /* Tooltip */
    .nxd-tooltip {
        position: relative;
    }
    .nxd-tooltip::after {
        content: attr(data-tip);
        position: absolute;
        bottom: calc(100% + 6px);
        left: 50%;
        transform: translateX(-50%) scale(0.9);
        padding: 4px 10px;
        border-radius: 6px;
        background: rgba(15,23,42,0.95);
        color: #e2e8f0;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
        pointer-events: none;
        opacity: 0;
        transition: all 0.2s ease;
        z-index: 100;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .nxd-tooltip:hover::after {
        opacity: 1;
        transform: translateX(-50%) scale(1);
    }
    
    /* Version dropdown */
    .nxd-ver-dropdown {
        position: absolute;
        top: calc(100% + 4px);
        right: 0;
        min-width: 220px;
        background: var(--bg-deepest);
        border: 1px solid rgba(124,58,237,0.2);
        border-radius: 10px;
        padding: 10px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.4);
        z-index: 200;
        display: none;
        animation: nxdDropIn 0.2s ease;
    }
    .nxd-ver-dropdown.nxd-ver-open {
        display: block;
    }
    .nxd-ver-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 10px;
        border-radius: 6px;
        font-size: 12px;
        color: var(--text-secondary);
    }
    .nxd-ver-item i {
        color: #7c3aed;
        font-size: 11px;
    }
    .nxd-ver-current {
        font-size: 10px;
        font-weight: 700;
        color: #10b981;
        background: rgba(16,185,129,0.1);
        padding: 2px 6px;
        border-radius: 4px;
        margin-left: auto;
    }
    
    /* --- Empty State --- */
    .nxd-empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    .nxd-empty-icon {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        background: rgba(124,58,237,0.06);
        border: 1px solid rgba(124,58,237,0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    .nxd-empty-icon i {
        font-size: 32px;
        color: #7c3aed;
        opacity: 0.3;
    }
    .nxd-empty-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 6px;
    }
    .nxd-empty-desc {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 20px;
    }
    
    /* --- Animations --- */
    @@keyframes nxdDropIn {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @@keyframes nxdPulseGlow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(124,58,237,0); }
        50%      { box-shadow: 0 0 12px 2px rgba(124,58,237,0.15); }
    }
    @@keyframes nxdSlideUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    
    .nxd-doc-card {
        animation: nxdSlideUp 0.35s ease backwards;
    }
    .nxd-doc-card:nth-child(1)  { animation-delay: 0s; }
    .nxd-doc-card:nth-child(2)  { animation-delay: 0.04s; }
    .nxd-doc-card:nth-child(3)  { animation-delay: 0.08s; }
    .nxd-doc-card:nth-child(4)  { animation-delay: 0.12s; }
    .nxd-doc-card:nth-child(5)  { animation-delay: 0.16s; }
    .nxd-doc-card:nth-child(6)  { animation-delay: 0.2s; }
    .nxd-doc-card:nth-child(7)  { animation-delay: 0.24s; }
    .nxd-doc-card:nth-child(8)  { animation-delay: 0.28s; }
    .nxd-doc-card:nth-child(9)  { animation-delay: 0.32s; }
    .nxd-doc-card:nth-child(10) { animation-delay: 0.36s; }
    
    /* --- Status badge styling for documents --- */
    .nxd-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    
    /* --- Responsive --- */
    @@media (max-width: 900px) {
        .nxd-stats-bar { grid-template-columns: repeat(2, 1fr); }
        .nxd-upload-panel .nxd-form-grid { grid-template-columns: 1fr; }
        .nxd-doc-card { flex-direction: column; gap: 12px; }
        .nxd-doc-actions { justify-content: flex-start; }
    }
    @@media (max-width: 600px) {
        .nxd-stats-bar { grid-template-columns: 1fr; }
        .nxd-stat-pill { padding: 12px 16px; }
        .nxd-stat-num { font-size: 22px; }
    }
    </style>

    @php
        $now = \Carbon\Carbon::now();
        $totalDocs    = $documents->count();
        $activeDocs   = $documents->filter(function($d) { return $d->is_active; })->count();
        $expiringSoon = $documents->filter(function($d) use ($now) {
            return $d->expiry_date && $d->expiry_date->isFuture() && $d->expiry_date->diffInDays($now) <= 30;
        })->count();
        $expiredDocs  = $documents->filter(function($d) {
            return $d->expiry_date && $d->expiry_date->isPast();
        })->count();

        // Sub-tab definitions matching main category tabs
        $docSubTabs = [
            'doc-all'        => ['label' => 'All',              'icon' => 'fas fa-layer-group',         'codes' => null],
            'doc-cipc'       => ['label' => 'CIPC',             'icon' => 'fas fa-stamp',               'codes' => ['REG','MOI','ID','BEE']],
            'doc-sars'       => ['label' => 'SARS',             'icon' => 'fas fa-file-invoice-dollar',  'codes' => ['TCC','SARS']],
            'doc-coida'      => ['label' => 'COIDA',            'icon' => 'fas fa-hard-hat',            'codes' => ['COIDA']],
            'doc-payroll'    => ['label' => 'Payroll',          'icon' => 'fas fa-money-check-alt',     'codes' => ['PAYROLL']],
            'doc-contracts'  => ['label' => 'Contracts',        'icon' => 'fas fa-file-signature',      'codes' => ['POA']],
            'doc-financials' => ['label' => 'Financials',       'icon' => 'fas fa-chart-line',          'codes' => ['AFS']],
            'doc-bankstmt'   => ['label' => 'Bank Statements',  'icon' => 'fas fa-university',          'codes' => ['BANK']],
            'doc-other'      => ['label' => 'Other',            'icon' => 'fas fa-archive',             'codes' => 'OTHER'],
        ];

        $knownCodes = [];
        foreach ($docSubTabs as $tab) {
            if (is_array($tab['codes'])) {
                $knownCodes = array_merge($knownCodes, $tab['codes']);
            }
        }

        // Group-to-type mapping for cascading dropdowns
        $docGroups = [
            'CIPC' => [
                ['id' => 3, 'code' => 'REG', 'name' => 'Company Registration (CK/CoR)'],
                ['id' => 4, 'code' => 'MOI', 'name' => 'MOI / Articles'],
                ['id' => 1, 'code' => 'ID',  'name' => 'ID Document'],
                ['id' => 5, 'code' => 'BEE', 'name' => 'BEE Certificate'],
            ],
            'SARS' => [
                ['id' => 2, 'code' => 'TCC',  'name' => 'Tax Clearance Certificate'],
                ['id' => 8, 'code' => 'SARS', 'name' => 'SARS Letter'],
            ],
            'COIDA' => [
                ['id' => 10, 'code' => 'OTHER', 'name' => 'COIDA Document'],
            ],
            'Payroll' => [
                ['id' => 10, 'code' => 'OTHER', 'name' => 'Payroll Document'],
            ],
            'Contracts' => [
                ['id' => 7, 'code' => 'POA', 'name' => 'Power of Attorney'],
            ],
            'Financials' => [
                ['id' => 6, 'code' => 'AFS', 'name' => 'Financial Statements'],
            ],
            'Bank Statements' => [
                ['id' => 9, 'code' => 'BANK', 'name' => 'Bank Statement'],
            ],
            'Other' => [
                ['id' => 10, 'code' => 'OTHER', 'name' => 'Other'],
            ],
        ];
    @endphp

    {{-- Stats bar moved to top of dashboard --}}

    {{-- ═══ DOCUMENT CATEGORY TABS ═══ --}}
    @php
        $catTabs = [
            'doc-cipc'       => ['label' => 'CIPC',             'icon' => 'fas fa-stamp',               'color' => 'nx-tab-cyan'],
            'doc-sars'       => ['label' => 'SARS',             'icon' => 'fas fa-file-invoice-dollar',  'color' => 'nx-tab-amber'],
            'doc-coida'      => ['label' => 'COIDA',            'icon' => 'fas fa-hard-hat',            'color' => 'nx-tab-green'],
            'doc-payroll'    => ['label' => 'Payroll',          'icon' => 'fas fa-money-check-alt',     'color' => 'nx-tab-blue'],
            'doc-contracts'  => ['label' => 'Contracts',        'icon' => 'fas fa-file-signature',      'color' => 'nx-tab-red'],
            'doc-financials' => ['label' => 'Financials',       'icon' => 'fas fa-chart-line',          'color' => 'nx-tab-violet'],
            'doc-bankstmt'   => ['label' => 'Bank Statements',  'icon' => 'fas fa-university',          'color' => 'nx-tab-green'],
            'doc-other'      => ['label' => 'Other',            'icon' => 'fas fa-archive',             'color' => 'nx-tab-purple'],
        ];
        $cipcSubTabs = [
            'doc-cipc-company'  => ['label' => 'Company'],
            'doc-cipc-annual'   => ['label' => 'Annual Returns'],
            'doc-cipc-beno'     => ['label' => 'Beneficial Ownership'],
            'doc-cipc-secr'     => ['label' => 'Secretarial Services'],
            'doc-cipc-bbbee'    => ['label' => 'BB BEE Certificates'],
            'doc-cipc-shares'   => ['label' => 'Share Certificates'],
        ];
        $sarsSubTabs = [
            'doc-sars-income'   => ['label' => 'Income Tax'],
            'doc-sars-personal' => ['label' => 'Personal Tax'],
            'doc-sars-prov'     => ['label' => 'Provisional Tax'],
            'doc-sars-emp201'   => ['label' => 'EMP 201'],
            'doc-sars-emp501'   => ['label' => 'EMP 501'],
            'doc-sars-vat'      => ['label' => 'Value Added Tax'],
            'doc-sars-customs'  => ['label' => 'Customs'],
            'doc-sars-royal'    => ['label' => 'Royalties'],
            'doc-sars-pin'      => ['label' => 'Tax Pin'],
        ];
        $coidaSubTabs = [
            'doc-coida-wc'      => ['label' => 'Workmans Comp'],
            'doc-coida-rm'      => ['label' => 'Rand Mutual'],
            'doc-coida-roe'     => ['label' => 'Returns of Earning'],
            'doc-coida-logs'    => ['label' => 'Letter of Good Standing'],
            'doc-coida-pay'     => ['label' => 'COIDA Payments'],
        ];
        $payrollSubTabs = [
            'doc-payroll-contracts' => ['label' => 'Employee Contracts'],
            'doc-payroll-mibco'     => ['label' => 'MIBCO'],
            'doc-payroll-packs'     => ['label' => 'Payroll Packs'],
            'doc-payroll-timesheets'=> ['label' => 'Time Sheets'],
            'doc-payroll-payslips'  => ['label' => 'Payslips'],
            'doc-payroll-other'     => ['label' => 'Other Working Papers'],
        ];
        $contractsSubTabs = [
            'doc-con-business'  => ['label' => 'Business Contracts'],
            'doc-con-service'   => ['label' => 'Service Contracts'],
            'doc-con-aod'       => ['label' => 'Acknowledgement of Debt'],
            'doc-con-custcred'  => ['label' => 'Customer Credit App'],
            'doc-con-vendcred'  => ['label' => 'Vendor Credit App'],
            'doc-con-supplier'  => ['label' => 'Supplier Contracts'],
            'doc-con-tenders'   => ['label' => 'Tenders'],
            'doc-con-general'   => ['label' => 'General'],
        ];
        $bankstmtSubTabs = [
            'doc-bank-older'    => ['label' => 'Older'],
            'doc-bank-2021'     => ['label' => '2021'],
            'doc-bank-2022'     => ['label' => '2022'],
            'doc-bank-2023'     => ['label' => '2023'],
            'doc-bank-2024'     => ['label' => '2024'],
            'doc-bank-2025'     => ['label' => '2025'],
            'doc-bank-2026'     => ['label' => '2026'],
            'doc-bank-2027'     => ['label' => '2027'],
            'doc-bank-2028'     => ['label' => '2028'],
            'doc-bank-2029'     => ['label' => '2029'],
            'doc-bank-2030'     => ['label' => '2030'],
        ];
        $financialsSubTabs = [
            'doc-fin-wp'        => ['label' => 'Working Papers'],
            'doc-fin-sched'     => ['label' => 'Schedules'],
            'doc-fin-mgmt'      => ['label' => 'Management Pack'],
            'doc-fin-draft'     => ['label' => 'Draft Financials'],
            'doc-fin-final'     => ['label' => 'Financials'],
            'doc-fin-general'   => ['label' => 'General'],
            'doc-fin-other'     => ['label' => 'Other'],
        ];
        $otherSubTabs = [
            'doc-other-corr'    => ['label' => 'Correspondence'],
            'doc-other-emails'  => ['label' => 'Emails'],
            'doc-other-general' => ['label' => 'General'],
            'doc-other-summons' => ['label' => 'Summons'],
            'doc-other-legal'   => ['label' => 'Legal Matters'],
        ];
    @endphp
    <style>
    .nxd-cat-tabs { display:flex; gap:16px; flex-wrap:nowrap; margin-bottom:16px; }
    .nxd-cat-btn {
        display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:14px 10px; border-radius:12px;
        font-size:13px; font-weight:700; letter-spacing:0.5px; white-space:nowrap; cursor:pointer;
        transition:all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        flex:1 1 auto; text-align:center; position:relative; overflow:hidden;
        text-transform:uppercase;
    }
    .nxd-cat-btn::before {
        content:''; position:absolute; top:0; left:0; right:0; bottom:0;
        background:radial-gradient(ellipse at 50% 0%, rgba(255,255,255,0.08) 0%, transparent 70%);
        opacity:0; transition:opacity 0.35s ease;
    }
    .nxd-cat-btn:hover::before { opacity:1; }
    .nxd-cat-btn:hover { transform:translateY(-2px); }
    .nxd-cat-btn i { font-size:14px; transition:all 0.3s ease; }
    .nxd-cat-btn:hover i { transform:scale(1.15); }

    /* --- Per-color DEFAULT + hover + active states --- */
    .nxd-cat-btn[data-cat="doc-cipc"] { background:rgba(6,182,212,0.08); border:1px solid rgba(6,182,212,0.2); color:rgba(103,232,249,0.75); }
    .nxd-cat-btn[data-cat="doc-cipc"]:hover { background:rgba(6,182,212,0.15); border-color:rgba(6,182,212,0.45); color:#67e8f9; box-shadow:0 4px 20px rgba(6,182,212,0.2), 0 0 15px rgba(6,182,212,0.08); }
    .nxd-cat-btn[data-cat="doc-cipc"].nxd-cat-active { background:rgba(6,182,212,0.2); border-color:rgba(6,182,212,0.6); color:#67e8f9; box-shadow:0 4px 24px rgba(6,182,212,0.3), 0 0 20px rgba(6,182,212,0.15); }

    .nxd-cat-btn[data-cat="doc-sars"] { background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.2); color:rgba(252,211,77,0.75); }
    .nxd-cat-btn[data-cat="doc-sars"]:hover { background:rgba(245,158,11,0.15); border-color:rgba(245,158,11,0.45); color:#fcd34d; box-shadow:0 4px 20px rgba(245,158,11,0.2), 0 0 15px rgba(245,158,11,0.08); }
    .nxd-cat-btn[data-cat="doc-sars"].nxd-cat-active { background:rgba(245,158,11,0.2); border-color:rgba(245,158,11,0.6); color:#fcd34d; box-shadow:0 4px 24px rgba(245,158,11,0.3), 0 0 20px rgba(245,158,11,0.15); }

    .nxd-cat-btn[data-cat="doc-coida"] { background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.2); color:rgba(110,231,183,0.75); }
    .nxd-cat-btn[data-cat="doc-coida"]:hover { background:rgba(16,185,129,0.15); border-color:rgba(16,185,129,0.45); color:#6ee7b7; box-shadow:0 4px 20px rgba(16,185,129,0.2), 0 0 15px rgba(16,185,129,0.08); }
    .nxd-cat-btn[data-cat="doc-coida"].nxd-cat-active { background:rgba(16,185,129,0.2); border-color:rgba(16,185,129,0.6); color:#6ee7b7; box-shadow:0 4px 24px rgba(16,185,129,0.3), 0 0 20px rgba(16,185,129,0.15); }

    .nxd-cat-btn[data-cat="doc-payroll"] { background:rgba(59,130,246,0.08); border:1px solid rgba(59,130,246,0.2); color:rgba(147,197,253,0.75); }
    .nxd-cat-btn[data-cat="doc-payroll"]:hover { background:rgba(59,130,246,0.15); border-color:rgba(59,130,246,0.45); color:#93c5fd; box-shadow:0 4px 20px rgba(59,130,246,0.2), 0 0 15px rgba(59,130,246,0.08); }
    .nxd-cat-btn[data-cat="doc-payroll"].nxd-cat-active { background:rgba(59,130,246,0.2); border-color:rgba(59,130,246,0.6); color:#93c5fd; box-shadow:0 4px 24px rgba(59,130,246,0.3), 0 0 20px rgba(59,130,246,0.15); }

    .nxd-cat-btn[data-cat="doc-contracts"] { background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2); color:rgba(252,165,165,0.75); }
    .nxd-cat-btn[data-cat="doc-contracts"]:hover { background:rgba(239,68,68,0.15); border-color:rgba(239,68,68,0.45); color:#fca5a5; box-shadow:0 4px 20px rgba(239,68,68,0.2), 0 0 15px rgba(239,68,68,0.08); }
    .nxd-cat-btn[data-cat="doc-contracts"].nxd-cat-active { background:rgba(239,68,68,0.2); border-color:rgba(239,68,68,0.6); color:#fca5a5; box-shadow:0 4px 24px rgba(239,68,68,0.3), 0 0 20px rgba(239,68,68,0.15); }

    .nxd-cat-btn[data-cat="doc-financials"] { background:rgba(139,92,246,0.08); border:1px solid rgba(139,92,246,0.2); color:rgba(196,181,253,0.75); }
    .nxd-cat-btn[data-cat="doc-financials"]:hover { background:rgba(139,92,246,0.15); border-color:rgba(139,92,246,0.45); color:#c4b5fd; box-shadow:0 4px 20px rgba(139,92,246,0.2), 0 0 15px rgba(139,92,246,0.08); }
    .nxd-cat-btn[data-cat="doc-financials"].nxd-cat-active { background:rgba(139,92,246,0.2); border-color:rgba(139,92,246,0.6); color:#c4b5fd; box-shadow:0 4px 24px rgba(139,92,246,0.3), 0 0 20px rgba(139,92,246,0.15); }

    .nxd-cat-btn[data-cat="doc-bankstmt"] { background:rgba(5,150,105,0.08); border:1px solid rgba(5,150,105,0.2); color:rgba(110,231,183,0.75); }
    .nxd-cat-btn[data-cat="doc-bankstmt"]:hover { background:rgba(5,150,105,0.15); border-color:rgba(5,150,105,0.45); color:#6ee7b7; box-shadow:0 4px 20px rgba(5,150,105,0.2), 0 0 15px rgba(5,150,105,0.08); }
    .nxd-cat-btn[data-cat="doc-bankstmt"].nxd-cat-active { background:rgba(5,150,105,0.2); border-color:rgba(5,150,105,0.6); color:#6ee7b7; box-shadow:0 4px 24px rgba(5,150,105,0.3), 0 0 20px rgba(5,150,105,0.15); }

    .nxd-cat-btn[data-cat="doc-other"] { background:rgba(168,85,247,0.08); border:1px solid rgba(168,85,247,0.2); color:rgba(216,180,254,0.75); }
    .nxd-cat-btn[data-cat="doc-other"]:hover { background:rgba(168,85,247,0.15); border-color:rgba(168,85,247,0.45); color:#d8b4fe; box-shadow:0 4px 20px rgba(168,85,247,0.2), 0 0 15px rgba(168,85,247,0.08); }
    .nxd-cat-btn[data-cat="doc-other"].nxd-cat-active { background:rgba(168,85,247,0.2); border-color:rgba(168,85,247,0.6); color:#d8b4fe; box-shadow:0 4px 24px rgba(168,85,247,0.3), 0 0 20px rgba(168,85,247,0.15); }

    /* --- Active button glow --- */
    .nxd-cat-btn.nxd-cat-active { transform:translateY(-1px); }
    .nxd-cat-btn.nxd-cat-active i { filter:drop-shadow(0 0 4px currentColor); }

    /* --- Sub-tabs with matching neon style --- */
    .nxd-sub-row {
        display:flex; gap:8px; padding:12px 0 6px 0; margin-bottom:12px; border-top:1px solid rgba(255,255,255,0.06);
    }
    .nxd-sub-btn {
        padding:8px 16px; font-size:11px; font-weight:700; border-radius:8px;
        background:rgba(255,255,255,0.04); border:1px solid var(--border-subtle);
        color:var(--text-muted); cursor:pointer; transition:all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-transform:uppercase; letter-spacing:0.5px;
    }
    .nxd-sub-btn:hover { background:rgba(6,182,212,0.08); color:var(--accent-cyan); border-color:rgba(6,182,212,0.4); transform:translateY(-1px); box-shadow:0 3px 12px rgba(6,182,212,0.15); }
    .nxd-sub-btn.active { background:rgba(6,182,212,0.12); color:var(--accent-cyan); border-color:var(--accent-cyan); box-shadow:0 4px 16px rgba(6,182,212,0.2), 0 0 12px rgba(6,182,212,0.08); }
    .nxd-sub-btn.active i, .nxd-sub-btn.active { text-shadow:0 0 8px rgba(6,182,212,0.3); }
    </style>

    <div class="nxd-cat-tabs">
        @foreach($catTabs as $catKey => $catTab)
            <button class="nxd-cat-btn {{ $loop->first ? 'nxd-cat-active' : '' }}" data-cat="{{ $catKey }}" onclick="nxCatFilter('{{ $catKey }}', this)">
                <i class="{{ $catTab['icon'] }}"></i> {{ $catTab['label'] }}
            </button>
        @endforeach
    </div>
    <div class="nxd-sub-row" id="cipcSubTabs" style="display:none;">
        @foreach($cipcSubTabs as $subKey => $subTab)
            <button class="nxd-sub-btn {{ $loop->first ? 'active' : '' }}" data-sub="{{ $subKey }}" onclick="nxSubFilter('{{ $subKey }}', this, 'cipcSubTabs')">{{ $subTab['label'] }}</button>
        @endforeach
    </div>
    <div class="nxd-sub-row" id="sarsSubTabs" style="display:none;">
        @foreach($sarsSubTabs as $subKey => $subTab)
            <button class="nxd-sub-btn {{ $subKey === 'doc-sars-emp201' ? 'active' : '' }}" data-sub="{{ $subKey }}" onclick="nxSubFilter('{{ $subKey }}', this, 'sarsSubTabs')">{{ $subTab['label'] }}</button>
        @endforeach
    </div>
    <div class="nxd-sub-row" id="coidaSubTabs" style="display:none;">
        @foreach($coidaSubTabs as $subKey => $subTab)
            <button class="nxd-sub-btn {{ $loop->first ? 'active' : '' }}" data-sub="{{ $subKey }}" onclick="nxSubFilter('{{ $subKey }}', this, 'coidaSubTabs')">{{ $subTab['label'] }}</button>
        @endforeach
    </div>
    <div class="nxd-sub-row" id="payrollSubTabs" style="display:none;">
        @foreach($payrollSubTabs as $subKey => $subTab)
            <button class="nxd-sub-btn {{ $loop->first ? 'active' : '' }}" data-sub="{{ $subKey }}" onclick="nxSubFilter('{{ $subKey }}', this, 'payrollSubTabs')">{{ $subTab['label'] }}</button>
        @endforeach
    </div>
    <div class="nxd-sub-row" id="contractsSubTabs" style="display:none;">
        @foreach($contractsSubTabs as $subKey => $subTab)
            <button class="nxd-sub-btn {{ $loop->first ? 'active' : '' }}" data-sub="{{ $subKey }}" onclick="nxSubFilter('{{ $subKey }}', this, 'contractsSubTabs')">{{ $subTab['label'] }}</button>
        @endforeach
    </div>
    <div class="nxd-sub-row" id="bankstmtSubTabs" style="display:none;">
        @foreach($bankstmtSubTabs as $subKey => $subTab)
            <button class="nxd-sub-btn {{ $loop->first ? 'active' : '' }}" data-sub="{{ $subKey }}" onclick="nxSubFilter('{{ $subKey }}', this, 'bankstmtSubTabs')">{{ $subTab['label'] }}</button>
        @endforeach
    </div>
    <div class="nxd-sub-row" id="financialsSubTabs" style="display:none;">
        @foreach($financialsSubTabs as $subKey => $subTab)
            <button class="nxd-sub-btn {{ $loop->first ? 'active' : '' }}" data-sub="{{ $subKey }}" onclick="nxSubFilter('{{ $subKey }}', this, 'financialsSubTabs')">{{ $subTab['label'] }}</button>
        @endforeach
    </div>
    <div class="nxd-sub-row" id="otherSubTabs" style="display:none;">
        @foreach($otherSubTabs as $subKey => $subTab)
            <button class="nxd-sub-btn {{ $loop->first ? 'active' : '' }}" data-sub="{{ $subKey }}" onclick="nxSubFilter('{{ $subKey }}', this, 'otherSubTabs')">{{ $subTab['label'] }}</button>
        @endforeach
    </div>

    {{-- ═══ UPLOAD FORM (button moved to panel header) ═══ --}}

    <div class="nxd-upload-panel" id="nxdUploadPanel">
        <form method="POST" action="{{ route('nexcore.clients.show.documents.store', $client->id) }}" enctype="multipart/form-data" id="nxdUploadForm">
            @csrf
    
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:20px;">
                <div style="width:40px; height:40px; border-radius:10px; background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.2); display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-cloud-upload-alt" style="color:#f59e0b; font-size:16px;"></i>
                </div>
                <div>
                    <div style="font-size:15px; font-weight:700; color:var(--text-primary); text-transform:uppercase; letter-spacing:0.8px;">Upload New Document</div>
                    <div style="font-size:12px; color:var(--text-muted); margin-top:1px;">Fill in details and attach your file</div>
                </div>
            </div>
    
            <div class="nxd-form-grid">
                {{-- Document Group --}}
                <div>
                    <label class="nxd-form-label">Document Group</label>
                    <select name="document_group" class="nxd-form-select" id="nxdDocGroup" onchange="nxDocGroupChange()">
                        <option value="">-- Select Group --</option>
                        @foreach($docCategories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->tab_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Document Type (cascading) --}}
                <div>
                    <label class="nxd-form-label">Document Type</label>
                    <select name="document_type_id" class="nxd-form-select" id="nxdDocType" required onchange="nxDocTypeChange()">
                        <option value="">-- Select Group First --</option>
                    </select>
                </div>
    
                {{-- Title --}}
                <div>
                    <label class="nxd-form-label">Document Title</label>
                    <input type="text" name="title" class="nxd-form-input" id="nxdDocTitle" placeholder="e.g. 2025 Tax Clearance" required>
                </div>
    
                {{-- Expiry Date --}}
                <div>
                    <label class="nxd-form-label">Expiry Date <span style="font-weight:400; color:var(--text-muted); text-transform:none;">(optional)</span></label>
                    <input type="text" name="expiry_date" class="nxd-form-input nxd-flatpickr" placeholder="Select date..." autocomplete="off">
                </div>
    
                {{-- File Upload (Drag & Drop) --}}
                <div class="nxd-form-full">
                    <label class="nxd-form-label">Attach File</label>
                    <div class="nxd-dropzone" id="nxdDropzone">
                        <input type="file" name="document_file" id="nxdFileInput" onchange="nxdFileSelected(this)" required>
                        <div class="nxd-dropzone-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <div class="nxd-dropzone-text">Drag & drop your file here, or <strong>click to browse</strong></div>
                        <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">PDF, DOC, XLS, JPG, PNG, ZIP up to 20MB</div>
                    </div>
                    <div class="nxd-file-preview" id="nxdFilePreview">
                        <i class="fas fa-file"></i>
                        <span id="nxdFileName">filename.pdf</span>
                        <span id="nxdFileSize" style="color:var(--text-muted); font-size:11px;"></span>
                        <span class="nxd-file-remove" onclick="nxdClearFile()" title="Remove file"><i class="fas fa-times"></i></span>
                    </div>
                </div>
    
                {{-- Notes --}}
                <div class="nxd-form-full">
                    <label class="nxd-form-label">Notes <span style="font-weight:400; color:var(--text-muted); text-transform:none;">(optional)</span></label>
                    <textarea name="notes" class="nxd-form-textarea" placeholder="Add any notes about this document..."></textarea>
                </div>
            </div>
    
            <div class="nxd-form-actions">
                <button type="button" class="nxd-btn-cancel" onclick="nxToggleUploadForm()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="nxd-btn-submit">
                    <i class="fas fa-upload"></i> Upload Document
                </button>
            </div>
        </form>
    </div>
    
    {{-- Inner sub-tabs replaced by main category tabs above --}}
    
    {{-- ═══ DOCUMENT CARDS ═══ --}}
    <div class="sl-card" style="border-radius:var(--radius-md, 12px); margin-top:0;">
        <div style="padding:16px 20px;" id="nxdDocList">
    
            @forelse($documents as $idx => $doc)
                @php
                    $isExpired      = $doc->expiry_date && $doc->expiry_date->isPast();
                    $isExpiringSoon = $doc->expiry_date && $doc->expiry_date->isFuture() && $doc->expiry_date->diffInDays($now) <= 30;
                    $expiryColor    = $isExpired ? 'var(--accent-red)' : ($isExpiringSoon ? 'var(--accent-amber)' : 'var(--text-muted)');
                    $typeCode       = $doc->documentType->code ?? '';
                    if (empty($typeCode)) { $typeCode = 'REG'; }
    
                    // File type class
                    $ft = strtolower($doc->file_type ?? '');
                    if (strpos($ft, 'pdf') !== false) {
                        $ftClass = 'nxd-ft-pdf'; $ftIcon = 'fas fa-file-pdf'; $ftExt = 'PDF';
                    } elseif (strpos($ft, 'doc') !== false || strpos($ft, 'word') !== false) {
                        $ftClass = 'nxd-ft-doc'; $ftIcon = 'fas fa-file-word'; $ftExt = 'DOC';
                    } elseif (strpos($ft, 'xls') !== false || strpos($ft, 'sheet') !== false || strpos($ft, 'csv') !== false) {
                        $ftClass = 'nxd-ft-xls'; $ftIcon = 'fas fa-file-excel'; $ftExt = 'XLS';
                    } elseif (strpos($ft, 'jpg') !== false || strpos($ft, 'jpeg') !== false || strpos($ft, 'png') !== false || strpos($ft, 'gif') !== false || strpos($ft, 'image') !== false || strpos($ft, 'bmp') !== false || strpos($ft, 'webp') !== false) {
                        $ftClass = 'nxd-ft-img'; $ftIcon = 'fas fa-file-image'; $ftExt = 'IMG';
                    } elseif (strpos($ft, 'zip') !== false || strpos($ft, 'rar') !== false || strpos($ft, '7z') !== false || strpos($ft, 'tar') !== false) {
                        $ftClass = 'nxd-ft-zip'; $ftIcon = 'fas fa-file-archive'; $ftExt = 'ZIP';
                    } else {
                        $ftClass = 'nxd-ft-default'; $ftIcon = 'fas fa-file-alt'; $ftExt = strtoupper(pathinfo($doc->file_name ?? '', PATHINFO_EXTENSION) ?: 'FILE');
                    }
    
                    // File URL
                    $fileUrl = $doc->file_path ? asset('uploads/documents/' . $doc->file_path) : '#';
    
                    // Status badge styling
                    $sColor = $doc->status->color ?? 'muted';
                    if ($sColor === 'green') {
                        $sBg = 'rgba(16,185,129,0.12)'; $sFg = '#34d399'; $sBorder = 'rgba(16,185,129,0.25)';
                    } elseif ($sColor === 'red') {
                        $sBg = 'rgba(239,68,68,0.12)'; $sFg = '#f87171'; $sBorder = 'rgba(239,68,68,0.25)';
                    } elseif ($sColor === 'amber') {
                        $sBg = 'rgba(245,158,11,0.12)'; $sFg = '#fbbf24'; $sBorder = 'rgba(245,158,11,0.25)';
                    } elseif ($sColor === 'blue') {
                        $sBg = 'rgba(59,130,246,0.12)'; $sFg = '#60a5fa'; $sBorder = 'rgba(59,130,246,0.25)';
                    } elseif ($sColor === 'cyan') {
                        $sBg = 'rgba(6,182,212,0.12)'; $sFg = '#22d3ee'; $sBorder = 'rgba(6,182,212,0.25)';
                    } else {
                        $sBg = 'rgba(148,163,184,0.08)'; $sFg = '#94a3b8'; $sBorder = 'rgba(148,163,184,0.15)';
                    }
                @endphp
    
                <div class="nxd-doc-card nx-doc-row" data-type-code="{{ $typeCode }}">
                    {{-- File Type Icon --}}
                    <div class="nxd-filetype-badge {{ $ftClass }}">
                        <i class="{{ $ftIcon }}"></i>
                        <span class="nxd-filetype-ext">{{ \Illuminate\Support\Str::limit($ftExt, 4, '') }}</span>
                    </div>
    
                    {{-- Document Info --}}
                    <div class="nxd-doc-info">
                        <div class="nxd-doc-title-row">
                            <span class="nxd-doc-title">{{ $doc->title }}</span>
                            @if($typeCode)
                                <span class="nxd-doc-type-badge">{{ $typeCode }}</span>
                            @endif
                            @if($doc->documentType)
                                <span style="font-size:12px; color:var(--text-muted);">{{ $doc->documentType->name }}</span>
                            @endif
                            @if($doc->status)
                                <span class="nxd-status-badge" style="background:{{ $sBg }}; color:{{ $sFg }}; border:1px solid {{ $sBorder }};">
                                    {{ $doc->status->name }}
                                </span>
                            @endif
                        </div>
    
                        @if($doc->description)
                            <div style="font-size:12px; color:var(--text-secondary); margin-bottom:4px;">{{ \Illuminate\Support\Str::limit($doc->description, 100) }}</div>
                        @endif
    
                        <div class="nxd-doc-meta">
                            {{-- File info --}}
                            @if($doc->file_name)
                                <span><i class="fas fa-paperclip"></i> <span style="font-family:var(--font-mono);">{{ \Illuminate\Support\Str::limit($doc->file_name, 35) }}</span></span>
                            @endif
                            @if($doc->file_size)
                                <span><i class="fas fa-database"></i> {{ number_format($doc->file_size / 1024, 1) }} KB</span>
                            @endif
    
                            {{-- Upload date, time & user --}}
                            <span><i class="fas fa-calendar-plus"></i> Uploaded {{ $doc->created_at->format('j M Y') }} at {{ $doc->created_at->format('H:i') }}</span>
                            @if($doc->uploader)
                                <span><i class="fas fa-user"></i> {{ $doc->uploader->name }}</span>
                            @elseif($doc->creator)
                                <span><i class="fas fa-user"></i> {{ $doc->creator->name }}</span>
                            @endif
    
                            {{-- Expiry date --}}
                            @if($doc->expiry_date)
                                <span class="nxd-expiry-warn" style="color:{{ $expiryColor }};">
                                    <i class="fas fa-calendar-times"></i>
                                    @if($isExpired)
                                        Expired {{ $doc->expiry_date->format('j M Y') }}
                                    @elseif($isExpiringSoon)
                                        Expires {{ $doc->expiry_date->format('j M Y') }} ({{ $doc->expiry_date->diffInDays($now) }}d left)
                                    @else
                                        Expires {{ $doc->expiry_date->format('j M Y') }}
                                    @endif
                                </span>
                            @else
                                <span style="color:var(--text-muted);"><i class="fas fa-infinity"></i> No expiry</span>
                            @endif
                        </div>
                    </div>
    
                    {{-- Action Buttons --}}
                    <div class="nxd-doc-actions">
                        <button type="button" class="nxd-menu-trigger" onclick="nxdToggleMenu(this)"><i class="fas fa-ellipsis-v"></i></button>
                        <div class="nxd-action-menu">
                            @if($doc->file_path)
                                <a href="{{ $fileUrl }}" target="_blank" class="nxd-menu-item nxd-mi-view"><i class="fas fa-eye"></i> View / Open</a>
                            @endif
                            <button type="button" class="nxd-menu-item nxd-mi-email" onclick="void(0)"><i class="fas fa-envelope"></i> Email Document</button>
                            <a href="https://wa.me/?text={{ urlencode('Document: ' . $doc->title . ' - ' . $fileUrl) }}" target="_blank" class="nxd-menu-item nxd-mi-wa"><i class="fab fa-whatsapp"></i> Share via WhatsApp</a>
                            <button type="button" class="nxd-menu-item nxd-mi-ver" onclick="nxdToggleVersions(this)"><i class="fas fa-layer-group"></i> Version History</button>
                            <a href="{{ route('nexcore.clients.show.documents.edit', [$client->id, $doc->id]) }}" class="nxd-menu-item nxd-mi-edit"><i class="fas fa-pen"></i> Edit Document</a>
                            <div class="nxd-menu-sep"></div>
                            <form method="POST" action="{{ route('nexcore.clients.show.documents.destroy', [$client->id, $doc->id]) }}" style="margin:0;" onsubmit="return nxConfirmDelete(event, 'document')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="nxd-menu-item nxd-mi-del"><i class="fas fa-trash-alt"></i> Delete Document</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="nxd-empty-state nx-doc-empty-all" id="nxdEmptyAll">
                    <div class="nxd-empty-icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div class="nxd-empty-title">No Documents Uploaded Yet</div>
                    <div class="nxd-empty-desc">Click "Upload Document" above to add the first document for this client.</div>
                    <button type="button" class="nxd-upload-trigger" onclick="nxToggleUploadForm()" style="font-size:12px; padding:8px 18px;">
                        <i class="fas fa-cloud-upload-alt"></i> Upload Your First Document
                    </button>
                </div>
            @endforelse
        </div>
    
        {{-- No results for filtered category --}}
        <div id="nxDocNoResults" style="display:none;">
            <div class="nxd-empty-state">
                <div class="nxd-empty-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="nxd-empty-title">No Documents in This Category</div>
                <div class="nxd-empty-desc">Upload a document or try a different category tab.</div>
            </div>
        </div>
    </div>
    
    <script>
    /* ═══════════════════════════════════════════════════════════════
       NexCore Documents Panel — JavaScript
       ═══════════════════════════════════════════════════════════════ */

    // --- Toggle Upload Form ---
    function nxToggleUploadForm() {
        var panel = document.getElementById('nxdUploadPanel');
        if (!panel) return;
        panel.classList.toggle('nxd-open');
    }

    // --- Cascading Group → Type Dropdown (from database) ---
    @php
        $catTypesForJs = [];
        foreach ($docCategories as $cat) {
            $types = isset($docTypesByCategory[$cat->id]) ? $docTypesByCategory[$cat->id] : collect([]);
            $arr = [];
            foreach ($types as $t) {
                $arr[] = ['id' => $t->id, 'name' => $t->name];
            }
            $catTypesForJs[$cat->id] = $arr;
        }
    @endphp
    var _nxdGroupTypeMap = {!! json_encode($catTypesForJs) !!};

    function nxDocGroupChange() {
        var groupSel = document.getElementById('nxdDocGroup');
        var typeSel  = document.getElementById('nxdDocType');
        if (!groupSel || !typeSel) return;

        var group = groupSel.value;
        typeSel.innerHTML = '';

        if (!group || !_nxdGroupTypeMap[group]) {
            typeSel.innerHTML = '<option value="">-- Select Group First --</option>';
            return;
        }

        typeSel.innerHTML = '<option value="">-- Select Type --</option>';
        var types = _nxdGroupTypeMap[group];
        for (var i = 0; i < types.length; i++) {
            var opt = document.createElement('option');
            opt.value = types[i].id;
            opt.textContent = types[i].name;
            typeSel.appendChild(opt);
        }

        if (types.length === 1) {
            typeSel.value = types[0].id;
            nxDocTypeChange();
        }
    }

    function nxDocTypeChange() {
        var typeSel = document.getElementById('nxdDocType');
        var titleInput = document.getElementById('nxdDocTitle');
        if (!typeSel || !titleInput) return;
        var selected = typeSel.options[typeSel.selectedIndex];
        if (selected && selected.value) {
            titleInput.value = selected.textContent;
        } else {
            titleInput.value = '';
        }
    }

    // --- Category Tab Filtering ---
    var _nxDocCodeMap = {
        'doc-cipc':            ['REG','MOI','ID','BEE'],
        'doc-cipc-company':    ['REG','MOI','ID'],
        'doc-cipc-annual':     ['REG'],
        'doc-cipc-beno':       ['REG'],
        'doc-cipc-secr':       ['MOI'],
        'doc-cipc-bbbee':      ['BEE'],
        'doc-cipc-shares':     ['MOI'],
        'doc-sars':            ['TCC','SARS'],
        'doc-sars-income':     ['SARS'],
        'doc-sars-personal':   ['SARS'],
        'doc-sars-prov':       ['SARS'],
        'doc-sars-emp201':     ['SARS'],
        'doc-sars-emp501':     ['SARS'],
        'doc-sars-vat':        ['SARS'],
        'doc-sars-customs':    ['SARS'],
        'doc-sars-royal':      ['SARS'],
        'doc-sars-pin':        ['TCC'],
        'doc-coida':           ['COIDA'],
        'doc-coida-wc':        ['COIDA'],
        'doc-coida-rm':        ['COIDA'],
        'doc-coida-roe':       ['COIDA'],
        'doc-coida-logs':      ['COIDA'],
        'doc-coida-pay':       ['COIDA'],
        'doc-payroll':         ['PAYROLL'],
        'doc-payroll-contracts': ['PAYROLL'],
        'doc-payroll-mibco':   ['PAYROLL'],
        'doc-payroll-packs':   ['PAYROLL'],
        'doc-payroll-timesheets': ['PAYROLL'],
        'doc-payroll-payslips': ['PAYROLL'],
        'doc-payroll-other':   ['PAYROLL'],
        'doc-contracts':       ['POA'],
        'doc-con-business':    ['POA'],
        'doc-con-service':     ['POA'],
        'doc-con-aod':         ['POA'],
        'doc-con-custcred':    ['POA'],
        'doc-con-vendcred':    ['POA'],
        'doc-con-supplier':    ['POA'],
        'doc-con-tenders':     ['POA'],
        'doc-con-general':     ['POA'],
        'doc-financials':      ['AFS'],
        'doc-fin-wp':          ['AFS'],
        'doc-fin-sched':       ['AFS'],
        'doc-fin-mgmt':        ['AFS'],
        'doc-fin-draft':       ['AFS'],
        'doc-fin-final':       ['AFS'],
        'doc-fin-general':     ['AFS'],
        'doc-fin-other':       ['AFS'],
        'doc-bankstmt':        ['BANK'],
        'doc-bank-older':      ['BANK'],
        'doc-bank-2021':       ['BANK'],
        'doc-bank-2022':       ['BANK'],
        'doc-bank-2023':       ['BANK'],
        'doc-bank-2024':       ['BANK'],
        'doc-bank-2025':       ['BANK'],
        'doc-bank-2026':       ['BANK'],
        'doc-bank-2027':       ['BANK'],
        'doc-bank-2028':       ['BANK'],
        'doc-bank-2029':       ['BANK'],
        'doc-bank-2030':       ['BANK'],
        'doc-other-corr':      ['OTHER'],
        'doc-other-emails':    ['OTHER'],
        'doc-other-general':   ['OTHER'],
        'doc-other-summons':   ['OTHER'],
        'doc-other-legal':     ['OTHER']
    };
    var _nxDocAllKnown = [];
    ['doc-cipc','doc-sars','doc-coida','doc-payroll','doc-contracts','doc-financials','doc-bankstmt'].forEach(function(k) {
        if (_nxDocCodeMap[k]) _nxDocAllKnown = _nxDocAllKnown.concat(_nxDocCodeMap[k]);
    });

    // Sub-tab panels mapped to their parent
    var _nxSubPanels = {
        'doc-cipc': 'cipcSubTabs',
        'doc-sars': 'sarsSubTabs',
        'doc-coida': 'coidaSubTabs',
        'doc-payroll': 'payrollSubTabs',
        'doc-contracts': 'contractsSubTabs',
        'doc-bankstmt': 'bankstmtSubTabs',
        'doc-financials': 'financialsSubTabs',
        'doc-other': 'otherSubTabs'
    };

    // Document category tab click handler
    function nxCatFilter(catKey, btn) {
        // Update active state on category tabs
        document.querySelectorAll('.nxd-cat-btn').forEach(function(t) {
            t.classList.remove('nxd-cat-active');
        });
        if (btn) btn.classList.add('nxd-cat-active');

        // Show/hide sub-tab panels
        Object.keys(_nxSubPanels).forEach(function(parentKey) {
            var panel = document.getElementById(_nxSubPanels[parentKey]);
            if (panel) {
                panel.style.display = (catKey === parentKey) ? 'flex' : 'none';
            }
            // Reset sub-tab active state when showing
            if (catKey === parentKey) {
                panel.querySelectorAll('.nxd-sub-btn').forEach(function(t) { t.classList.remove('active'); });
                var allBtn = panel.querySelector('.nxd-sub-btn[data-sub="' + parentKey + '"]');
                if (allBtn) allBtn.classList.add('active');
            }
        });

        nxDocFilter(catKey, null);
    }

    // Generic sub-tab filter
    function nxSubFilter(subKey, btn, panelId) {
        document.querySelectorAll('#' + panelId + ' .nxd-sub-btn').forEach(function(t) {
            t.classList.remove('active');
        });
        if (btn) btn.classList.add('active');
        nxDocFilter(subKey, null);
    }

    function nxDocFilter(subKey, btn) {
        // Update active tab
        document.querySelectorAll('.doc-subtab').forEach(function(t) {
            t.classList.remove('nx-subtab-active');
        });
        if (btn) btn.classList.add('nx-subtab-active');

        var rows = document.querySelectorAll('.nxd-doc-card.nx-doc-row');
        var visibleCount = 0;

        rows.forEach(function(row) {
            var code = (row.getAttribute('data-type-code') || '').toUpperCase();
            var show = false;

            if (subKey === 'doc-all') {
                show = true;
            } else if (subKey === 'doc-other') {
                show = _nxDocAllKnown.indexOf(code) === -1;
            } else if (_nxDocCodeMap[subKey]) {
                show = _nxDocCodeMap[subKey].indexOf(code) !== -1;
            }

            if (show) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide empty states
        var noResults = document.getElementById('nxDocNoResults');
        var emptyAll  = document.getElementById('nxdEmptyAll');

        if (noResults) {
            if (visibleCount === 0 && !emptyAll) {
                noResults.style.display = 'block';
            } else if (visibleCount === 0 && emptyAll && subKey !== 'doc-all') {
                // Hide the "no docs at all" empty state, show the category empty
                emptyAll.style.display = 'none';
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
                if (emptyAll && subKey === 'doc-all') {
                    // Show global empty state only on All tab when no docs exist
                    emptyAll.style.display = '';
                }
            }
        }
    }
    
    // --- File Selection Preview ---
    function nxdFileSelected(input) {
        var preview = document.getElementById('nxdFilePreview');
        var nameEl  = document.getElementById('nxdFileName');
        var sizeEl  = document.getElementById('nxdFileSize');
        if (!input.files || !input.files.length) {
            preview.style.display = 'none';
            return;
        }
        var file = input.files[0];
        nameEl.textContent = file.name;
        sizeEl.textContent = '(' + (file.size / 1024).toFixed(1) + ' KB)';
        preview.style.display = 'flex';
    }
    
    function nxdClearFile() {
        var input   = document.getElementById('nxdFileInput');
        var preview = document.getElementById('nxdFilePreview');
        if (input) input.value = '';
        if (preview) preview.style.display = 'none';
    }
    
    // --- Drag-and-Drop Enhancement ---
    (function() {
        var dz = document.getElementById('nxdDropzone');
        if (!dz) return;
        ['dragenter','dragover'].forEach(function(evt) {
            dz.addEventListener(evt, function(e) {
                e.preventDefault();
                dz.classList.add('nxd-drag-over');
            });
        });
        ['dragleave','drop'].forEach(function(evt) {
            dz.addEventListener(evt, function(e) {
                e.preventDefault();
                dz.classList.remove('nxd-drag-over');
            });
        });
        dz.addEventListener('drop', function(e) {
            var input = document.getElementById('nxdFileInput');
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length && input) {
                input.files = e.dataTransfer.files;
                nxdFileSelected(input);
            }
        });
    })();
    
    // --- Version Dropdown Toggle ---
    function nxdToggleMenu(trigger) {
        var menu = trigger.nextElementSibling;
        var isOpen = menu.classList.contains('open');
        document.querySelectorAll('.nxd-action-menu.open').forEach(function(m) { m.classList.remove('open'); });
        document.querySelectorAll('.nxd-menu-trigger.active').forEach(function(t) { t.classList.remove('active'); });
        if (!isOpen) { menu.classList.add('open'); trigger.classList.add('active'); }
    }
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.nxd-doc-actions')) {
            document.querySelectorAll('.nxd-action-menu.open').forEach(function(m) { m.classList.remove('open'); });
            document.querySelectorAll('.nxd-menu-trigger.active').forEach(function(t) { t.classList.remove('active'); });
        }
    });

    function nxdToggleVersions(btn) {
        // Close all others first
        document.querySelectorAll('.nxd-ver-dropdown').forEach(function(dd) {
            if (dd !== btn.nextElementSibling) {
                dd.classList.remove('nxd-ver-open');
            }
        });
        var dropdown = btn.parentElement.querySelector('.nxd-ver-dropdown');
        if (dropdown) {
            dropdown.classList.toggle('nxd-ver-open');
        }
    }
    
    // Close version dropdowns on outside click
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.nxd-act-ver') && !e.target.closest('.nxd-ver-dropdown')) {
            document.querySelectorAll('.nxd-ver-dropdown').forEach(function(dd) {
                dd.classList.remove('nxd-ver-open');
            });
        }
    });
    
    // --- Initialize Flatpickr for Expiry Date ---
    (function() {
        if (typeof flatpickr !== 'undefined') {
            flatpickr('.nxd-flatpickr', {
                dateFormat: 'j M Y',
                altInput: true,
                altFormat: 'j M Y',
                disableMobile: true,
                theme: 'dark'
            });
        } else {
            // Load flatpickr if not already available
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css';
            document.head.appendChild(link);
    
            var darkTheme = document.createElement('link');
            darkTheme.rel = 'stylesheet';
            darkTheme.href = 'https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css';
            document.head.appendChild(darkTheme);
    
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/flatpickr';
            script.onload = function() {
                flatpickr('.nxd-flatpickr', {
                    dateFormat: 'j M Y',
                    altInput: true,
                    altFormat: 'j M Y',
                    disableMobile: true,
                    theme: 'dark'
                });
            };
            document.head.appendChild(script);
        }
    })();
    </script>
    </div>


    {{-- ============================================================
         AUDIT TRAIL PANEL
         ============================================================ --}}
    <div class="nx-panel" id="panel-audit" style="display:none;">
        <div class="sl-card">
            <div class="sl-card-header">
                <div class="sl-card-title" style="color:#a78bfa;"><i class="fas fa-history"></i> Audit Trail</div>
            </div>
            <div class="sl-table-wrap">
                <table class="sl-table">
                    <thead>
                        <tr>
                            <th style="width:160px;">Date / Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Module</th>
                            <th>Description</th>
                            <th style="width:120px;">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($auditTrail as $entry)
                        <tr>
                            <td style="font-family:var(--font-mono); font-size:12px; color:var(--text-muted); white-space:nowrap;">{{ $entry->created_at ? $entry->created_at->format('j M Y H:i') : '-' }}</td>
                            <td style="font-weight:600; color:var(--text-primary);">{{ $entry->user_name ?? '-' }}</td>
                            <td>
                                @php
                                    $actColors = ['created' => '#10b981', 'updated' => '#3b82f6', 'deleted' => '#ef4444'];
                                    $actColor = $actColors[$entry->action ?? ''] ?? '#64748b';
                                @endphp
                                <span style="display:inline-flex; align-items:center; font-size:11px; font-weight:700; letter-spacing:0.5px; padding:3px 10px; border-radius:20px; text-transform:uppercase; color:{{ $actColor }}; background:{{ $actColor }}18; border:1px solid {{ $actColor }}33;">{{ ucfirst($entry->action ?? '-') }}</span>
                            </td>
                            <td style="font-size:12px; font-weight:600; color:var(--accent-cyan); text-transform:capitalize;">{{ $entry->module ?? '-' }}</td>
                            <td style="font-size:13px; color:var(--text-secondary);">{{ $entry->description ?? '-' }}</td>
                            <td style="font-family:var(--font-mono); font-size:11px; color:var(--text-muted);">{{ $entry->ip_address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center; padding:40px; color:var(--text-muted);"><i class="fas fa-history" style="font-size:28px; opacity:0.2; display:block; margin-bottom:10px;"></i>No audit trail entries yet</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- ============================================================
     STYLES
     ============================================================ --}}
<style>
.nx-quick-tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: center;
}
.nx-tab-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 20px;
    border-radius: 12px;
    text-decoration: none;
    font-family: 'Poppins', var(--font-sans);
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.3px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.7);
    position: relative;
    overflow: hidden;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}
.nx-tab-badge::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 12px;
    opacity: 0;
    transition: opacity 0.35s ease;
}
.nx-tab-badge:hover, .nx-tab-badge.nx-tab-active {
    transform: translateY(-2px) scale(1.03);
    color: #fff;
}
.nx-tab-badge:hover::before, .nx-tab-badge.nx-tab-active::before {
    opacity: 1;
}
.nx-tab-badge i {
    font-size: 15px;
    transition: transform 0.3s ease, text-shadow 0.3s ease;
    position: relative;
    z-index: 1;
}
.nx-tab-badge:hover i, .nx-tab-badge.nx-tab-active i {
    transform: scale(1.15);
}
.nx-tab-label { position: relative; z-index: 1; }
.nx-tab-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    height: 22px;
    padding: 0 6px;
    border-radius: 11px;
    font-size: 11px;
    font-weight: 800;
    font-family: var(--font-mono);
    position: relative;
    z-index: 1;
}

/* White (Company) */
.nx-tab-white { border-color: rgba(255,255,255,0.15); }
.nx-tab-white i { color: #e2e8f0; }
.nx-tab-white::before { background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.02) 100%); }
.nx-tab-white:hover, .nx-tab-white.nx-tab-active { border-color: rgba(255,255,255,0.4); box-shadow: 0 0 20px rgba(255,255,255,0.12), 0 0 40px rgba(255,255,255,0.04), inset 0 0 20px rgba(255,255,255,0.03); }
.nx-tab-white:hover i, .nx-tab-white.nx-tab-active i { text-shadow: 0 0 12px rgba(255,255,255,0.4); color:#fff; }

/* Cyan */
.nx-tab-cyan { border-color: rgba(0,210,211,0.2); }
.nx-tab-cyan i { color: #00d2d3; }
.nx-tab-cyan .nx-tab-count { background: rgba(0,210,211,0.15); color: #00d2d3; }
.nx-tab-cyan::before { background: linear-gradient(135deg, rgba(0,210,211,0.12) 0%, rgba(0,210,211,0.03) 100%); }
.nx-tab-cyan:hover, .nx-tab-cyan.nx-tab-active { border-color: rgba(0,210,211,0.5); box-shadow: 0 0 20px rgba(0,210,211,0.2), 0 0 40px rgba(0,210,211,0.08), inset 0 0 20px rgba(0,210,211,0.05); }
.nx-tab-cyan:hover i, .nx-tab-cyan.nx-tab-active i { text-shadow: 0 0 12px rgba(0,210,211,0.6); }

/* Blue */
.nx-tab-blue { border-color: rgba(59,130,246,0.2); }
.nx-tab-blue i { color: #3b82f6; }
.nx-tab-blue .nx-tab-count { background: rgba(59,130,246,0.15); color: #3b82f6; }
.nx-tab-blue::before { background: linear-gradient(135deg, rgba(59,130,246,0.12) 0%, rgba(59,130,246,0.03) 100%); }
.nx-tab-blue:hover, .nx-tab-blue.nx-tab-active { border-color: rgba(59,130,246,0.5); box-shadow: 0 0 20px rgba(59,130,246,0.2), 0 0 40px rgba(59,130,246,0.08), inset 0 0 20px rgba(59,130,246,0.05); }
.nx-tab-blue:hover i, .nx-tab-blue.nx-tab-active i { text-shadow: 0 0 12px rgba(59,130,246,0.6); }

/* Green */
.nx-tab-green { border-color: rgba(16,185,129,0.2); }
.nx-tab-green i { color: #10b981; }
.nx-tab-green .nx-tab-count { background: rgba(16,185,129,0.15); color: #10b981; }
.nx-tab-green::before { background: linear-gradient(135deg, rgba(16,185,129,0.12) 0%, rgba(16,185,129,0.03) 100%); }
.nx-tab-green:hover, .nx-tab-green.nx-tab-active { border-color: rgba(16,185,129,0.5); box-shadow: 0 0 20px rgba(16,185,129,0.2), 0 0 40px rgba(16,185,129,0.08), inset 0 0 20px rgba(16,185,129,0.05); }
.nx-tab-green:hover i, .nx-tab-green.nx-tab-active i { text-shadow: 0 0 12px rgba(16,185,129,0.6); }

/* Amber */
.nx-tab-amber { border-color: rgba(245,158,11,0.2); }
.nx-tab-amber i { color: #f59e0b; }
.nx-tab-amber .nx-tab-count { background: rgba(245,158,11,0.15); color: #f59e0b; }
.nx-tab-amber::before { background: linear-gradient(135deg, rgba(245,158,11,0.12) 0%, rgba(245,158,11,0.03) 100%); }
.nx-tab-amber:hover, .nx-tab-amber.nx-tab-active { border-color: rgba(245,158,11,0.5); box-shadow: 0 0 20px rgba(245,158,11,0.2), 0 0 40px rgba(245,158,11,0.08), inset 0 0 20px rgba(245,158,11,0.05); }
.nx-tab-amber:hover i, .nx-tab-amber.nx-tab-active i { text-shadow: 0 0 12px rgba(245,158,11,0.6); }

/* Purple */
.nx-tab-purple { border-color: rgba(139,92,246,0.2); }
.nx-tab-purple i { color: #8b5cf6; }
.nx-tab-purple .nx-tab-count { background: rgba(139,92,246,0.15); color: #8b5cf6; }
.nx-tab-purple::before { background: linear-gradient(135deg, rgba(139,92,246,0.12) 0%, rgba(139,92,246,0.03) 100%); }
.nx-tab-purple:hover, .nx-tab-purple.nx-tab-active { border-color: rgba(139,92,246,0.5); box-shadow: 0 0 20px rgba(139,92,246,0.2), 0 0 40px rgba(139,92,246,0.08), inset 0 0 20px rgba(139,92,246,0.05); }
.nx-tab-purple:hover i, .nx-tab-purple.nx-tab-active i { text-shadow: 0 0 12px rgba(139,92,246,0.6); }

/* Red */
.nx-tab-red { border-color: rgba(239,68,68,0.2); }
.nx-tab-red i { color: #ef4444; }
.nx-tab-red .nx-tab-count { background: rgba(239,68,68,0.15); color: #ef4444; }
.nx-tab-red::before { background: linear-gradient(135deg, rgba(239,68,68,0.12) 0%, rgba(239,68,68,0.03) 100%); }
.nx-tab-red:hover, .nx-tab-red.nx-tab-active { border-color: rgba(239,68,68,0.5); box-shadow: 0 0 20px rgba(239,68,68,0.2), 0 0 40px rgba(239,68,68,0.08), inset 0 0 20px rgba(239,68,68,0.05); }
.nx-tab-red:hover i, .nx-tab-red.nx-tab-active i { text-shadow: 0 0 12px rgba(239,68,68,0.6); }

/* Teal */
.nx-tab-teal { border-color: rgba(20,184,166,0.2); }
.nx-tab-teal i { color: #14b8a6; }
.nx-tab-teal .nx-tab-count { background: rgba(20,184,166,0.15); color: #14b8a6; }
.nx-tab-teal::before { background: linear-gradient(135deg, rgba(20,184,166,0.12) 0%, rgba(20,184,166,0.03) 100%); }
.nx-tab-teal:hover, .nx-tab-teal.nx-tab-active { border-color: rgba(20,184,166,0.5); box-shadow: 0 0 20px rgba(20,184,166,0.2), 0 0 40px rgba(20,184,166,0.08), inset 0 0 20px rgba(20,184,166,0.05); }
.nx-tab-teal:hover i, .nx-tab-teal.nx-tab-active i { text-shadow: 0 0 12px rgba(20,184,166,0.6); }

.nx-tab-violet { border-color: rgba(124,58,237,0.2); }
.nx-tab-violet i { color: #7c3aed; }
.nx-tab-violet .nx-tab-count { background: rgba(124,58,237,0.15); color: #a78bfa; }
.nx-tab-violet::before { background: linear-gradient(135deg, rgba(124,58,237,0.12) 0%, rgba(124,58,237,0.03) 100%); }
.nx-tab-violet:hover, .nx-tab-violet.nx-tab-active { border-color: rgba(124,58,237,0.5); box-shadow: 0 0 20px rgba(124,58,237,0.2), 0 0 40px rgba(124,58,237,0.08), inset 0 0 20px rgba(124,58,237,0.05); }
.nx-tab-violet:hover i, .nx-tab-violet.nx-tab-active i { text-shadow: 0 0 12px rgba(124,58,237,0.6); }

/* Neon Add New Badge */
.nx-add-new-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    text-decoration: none;
    color: #10b981;
    background: rgba(16,185,129,0.08);
    border: 1px solid rgba(16,185,129,0.2);
    cursor: pointer;
    transition: all 0.25s ease;
}
.nx-add-new-badge:hover {
    background: rgba(16,185,129,0.15);
    border-color: rgba(16,185,129,0.45);
    box-shadow: 0 0 12px rgba(16,185,129,0.15), inset 0 0 8px rgba(16,185,129,0.05);
    transform: translateY(-1px);
    color: #10b981;
}
.nx-add-new-badge i {
    font-size: 11px;
}

/* Neon Close Badge */
.nx-close-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: #ef4444;
    background: rgba(239,68,68,0.08);
    border: 1px solid rgba(239,68,68,0.2);
    cursor: pointer;
    transition: all 0.25s ease;
}
.nx-close-badge:hover {
    background: rgba(239,68,68,0.15);
    border-color: rgba(239,68,68,0.45);
    box-shadow: 0 0 12px rgba(239,68,68,0.15), inset 0 0 8px rgba(239,68,68,0.05);
    transform: translateY(-1px);
}
.nx-close-badge i {
    font-size: 11px;
}

/* Neon Status Badges */
.nx-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.5px;
    padding: 3px 10px;
    border-radius: 20px;
    text-transform: uppercase;
    margin-left: 4px;
}
.nx-status-active {
    color: #10b981;
    background: rgba(16,185,129,0.1);
    border: 1px solid rgba(16,185,129,0.25);
    box-shadow: 0 0 8px rgba(16,185,129,0.15), inset 0 0 8px rgba(16,185,129,0.05);
}
.nx-status-inactive {
    color: #ef4444;
    background: rgba(239,68,68,0.1);
    border: 1px solid rgba(239,68,68,0.25);
    box-shadow: 0 0 8px rgba(239,68,68,0.15), inset 0 0 8px rgba(239,68,68,0.05);
}

/* Panel transitions */
.nx-panel {
    animation: nxFadeIn 0.35s ease;
}
@@keyframes nxFadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
@@keyframes nxPulseGreen {
    0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(16,185,129,0.4); }
    50% { opacity: 0.7; box-shadow: 0 0 0 4px rgba(16,185,129,0); }
}
@@keyframes nxGlowPulse {
    0%, 100% { opacity: 0.4; }
    50% { opacity: 0.7; }
}

/* Premium Map Styles */
.nx-map-wrapper {
    position: relative;
    border-radius: 16px;
}
.nx-map-glow {
    position: absolute;
    inset: -2px;
    border-radius: 18px;
    background: linear-gradient(135deg, rgba(16,185,129,0.3), rgba(0,210,211,0.15), rgba(59,130,246,0.2), rgba(16,185,129,0.3));
    background-size: 300% 300%;
    animation: nxGlowPulse 4s ease-in-out infinite;
    z-index: 0;
    filter: blur(1px);
}
.nx-map-container {
    position: relative;
    z-index: 1;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.08);
    box-shadow: 0 8px 32px rgba(0,0,0,0.4), 0 2px 8px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.04);
}
.nx-map-vignette {
    position: absolute;
    inset: 0;
    pointer-events: none;
    box-shadow: inset 0 0 60px rgba(10,14,26,0.5), inset 0 -40px 60px rgba(10,14,26,0.4);
    border-radius: 16px;
    z-index: 2;
}
.nx-map-infocard {
    position: absolute;
    bottom: 16px;
    left: 16px;
    right: 16px;
    z-index: 10;
    padding: 16px 20px;
    background: rgba(10,14,26,0.88);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-radius: 14px;
    border: 1px solid rgba(255,255,255,0.08);
    box-shadow: 0 8px 32px rgba(0,0,0,0.5), 0 0 0 1px rgba(16,185,129,0.06);
    transition: all 0.3s ease;
}
.nx-map-info-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: linear-gradient(135deg, #10b981, #059669);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 16px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(16,185,129,0.3);
}
.nx-map-info-label {
    font-size: 15px;
    font-weight: 700;
    color: #fff;
    letter-spacing: 0.5px;
}
.nx-map-info-addr {
    font-size: 12px;
    color: rgba(255,255,255,0.5);
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.nx-map-live-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 20px;
    background: rgba(16,185,129,0.12);
    border: 1px solid rgba(16,185,129,0.2);
    font-size: 10px;
    font-weight: 700;
    color: #10b981;
    letter-spacing: 1.5px;
    flex-shrink: 0;
}
.nx-map-live-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #10b981;
    display: inline-block;
    animation: nxPulseGreen 2s infinite;
}
.nx-map-coords {
    font-family: var(--font-mono);
    font-size: 11px;
    color: rgba(255,255,255,0.35);
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.nx-map-coords i {
    color: rgba(16,185,129,0.5);
    font-size: 10px;
}
.nx-map-gmaps-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    color: #00d2d3;
    text-decoration: none;
    padding: 6px 14px;
    border-radius: 8px;
    background: rgba(0,210,211,0.08);
    border: 1px solid rgba(0,210,211,0.15);
    transition: all 0.2s ease;
    letter-spacing: 0.3px;
}
.nx-map-wa-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    color: #25d366;
    text-decoration: none;
    padding: 6px 14px;
    border-radius: 8px;
    background: rgba(37,211,102,0.08);
    border: 1px solid rgba(37,211,102,0.15);
    transition: all 0.2s ease;
    letter-spacing: 0.3px;
}
.nx-map-wa-btn:hover {
    background: rgba(37,211,102,0.18);
    border-color: rgba(37,211,102,0.4);
    box-shadow: 0 0 16px rgba(37,211,102,0.12);
    transform: translateY(-1px);
}
.nx-map-gmaps-btn:hover {
    background: rgba(0,210,211,0.15);
    border-color: rgba(0,210,211,0.35);
    box-shadow: 0 0 16px rgba(0,210,211,0.1);
    transform: translateY(-1px);
}

@@media (max-width: 768px) {
    .nx-quick-tabs { gap: 6px; }
    .nx-tab-badge { padding: 8px 12px; font-size: 12px; }
    .nx-tab-label { display: none; }
}

/* NexCore SweetAlert White Premium Theme */
.swal2-container { z-index: 99999 !important; }
.nx-swal-popup {
    background: #ffffff !important;
    border-radius: 20px !important;
    box-shadow: 0 25px 80px rgba(0,0,0,0.12), 0 8px 24px rgba(0,0,0,0.06) !important;
    padding: 32px 28px 24px !important;
    border: none !important;
}
.nx-swal-popup.nx-swal-success { border-top: 4px solid #059669 !important; }
.nx-swal-popup.nx-swal-warning { border-top: 4px solid #e11d48 !important; }
.nx-swal-popup.nx-swal-info { border-top: 4px solid #2563eb !important; }
.nx-swal-popup.nx-swal-confirm { border-top: 4px solid #0891b2 !important; }
.nx-swal-popup.nx-swal-error { border-top: 4px solid #d97706 !important; }
.nx-swal-title {
    color: #1e293b !important;
    font-weight: 800 !important;
    font-size: 20px !important;
    letter-spacing: 0.3px !important;
    font-family: 'Poppins', 'Montserrat', sans-serif !important;
    margin-top: 8px !important;
}
.nx-swal-html {
    color: #64748b !important;
    font-size: 14px !important;
    line-height: 1.7 !important;
    font-family: 'Poppins', sans-serif !important;
}
.nx-swal-actions .swal2-confirm {
    font-family: 'Poppins', sans-serif !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    letter-spacing: 1px !important;
    text-transform: uppercase !important;
    border-radius: 10px !important;
    padding: 12px 32px !important;
    border: none !important;
    box-shadow: 0 4px 14px rgba(0,0,0,0.1) !important;
    transition: all 0.2s ease !important;
}
.nx-swal-actions .swal2-cancel {
    font-family: 'Poppins', sans-serif !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    letter-spacing: 1px !important;
    text-transform: uppercase !important;
    border-radius: 10px !important;
    padding: 12px 32px !important;
    background: #f1f5f9 !important;
    color: #64748b !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: none !important;
}
.nx-swal-actions .swal2-cancel:hover { background: #e2e8f0 !important; color: #475569 !important; }
.swal2-popup .swal2-icon { display: none !important; }
.nx-swal-logo { margin: 0 auto 8px; display: block; }
.nx-swal-popup .swal2-image { margin: 0 auto 12px !important; }

/* ─── Sub-Tab Navigation Bar ─── */
.nx-subtab-bar {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    padding: 14px 16px;
    margin-bottom: 16px;
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 14px;
    position: relative;
    overflow: hidden;
}
.nx-subtab-bar::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(139,92,246,0.03) 0%, rgba(20,184,166,0.02) 50%, rgba(217,119,6,0.03) 100%);
    pointer-events: none;
}
.nx-subtab {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 10px;
    text-decoration: none;
    font-family: 'Poppins', var(--font-sans);
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.3px;
    color: rgba(255,255,255,0.5);
    background: transparent;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.25s ease;
    position: relative;
    z-index: 1;
}
.nx-subtab i {
    font-size: 12px;
    transition: all 0.25s ease;
}
.nx-subtab:hover {
    color: rgba(255,255,255,0.8);
    background: rgba(255,255,255,0.04);
    border-color: rgba(255,255,255,0.08);
}
.nx-subtab.nx-subtab-active {
    color: #fff;
    background: rgba(139,92,246,0.12);
    border-color: rgba(139,92,246,0.3);
    box-shadow: 0 0 16px rgba(139,92,246,0.1), inset 0 0 12px rgba(139,92,246,0.04);
}
.nx-subtab.nx-subtab-active i {
    text-shadow: 0 0 8px rgba(139,92,246,0.5);
}
.nx-subtab-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    padding: 0 5px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 800;
    font-family: var(--font-mono);
    background: rgba(255,255,255,0.06);
    color: rgba(255,255,255,0.4);
}
.nx-subtab.nx-subtab-active .nx-subtab-count {
    background: rgba(139,92,246,0.2);
    color: #c4b5fd;
}

/* Sub-panel animations */
.nx-subpanel {
    animation: nxSubFadeIn 0.3s ease;
}
@@keyframes nxSubFadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Contacts — uses inline styles matching addresses pattern */

/* ─── Action Buttons ─── */
.nx-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 8px;
    border: 1px solid transparent;
    background: transparent;
    cursor: pointer;
    font-size: 13px;
    transition: all 0.2s ease;
}
.nx-action-edit { color: var(--accent-blue); }
.nx-action-edit:hover { background: rgba(59,130,246,0.1); border-color: rgba(59,130,246,0.2); }
.nx-action-delete { color: var(--accent-red); }
.nx-action-delete:hover { background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.2); }
.nx-action-view { color: var(--accent-cyan); text-decoration: none; }
.nx-action-view:hover { background: rgba(0,210,211,0.1); border-color: rgba(0,210,211,0.2); }

/* ─── Empty Cell ─── */
.nx-empty-cell {
    text-align: center !important;
    padding: 40px 20px !important;
    color: var(--text-muted) !important;
    font-size: 13px !important;
}
.nx-empty-cell i {
    font-size: 28px;
    opacity: 0.15;
    display: block;
    margin-bottom: 10px;
}

@@media (max-width: 768px) {
    .nx-subtab-bar { gap: 4px; padding: 10px 10px; }
    .nx-subtab { padding: 6px 10px; font-size: 11px; }
    .nx-subtab .nx-subtab-count { display: none; }
}

/* ─── Document Sub-tabs ─── */
.doc-subtab {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 12px 16px;
    font-size: 12px;
    font-weight: 600;
    font-family: var(--font-body);
    color: var(--text-muted);
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    transition: all 0.25s ease;
    position: relative;
    top: 2px;
    letter-spacing: 0.3px;
}
.doc-subtab:hover {
    color: var(--text-primary);
    background: rgba(255,255,255,0.02);
}
.doc-subtab.nx-subtab-active {
    color: #a78bfa;
    border-bottom-color: #7c3aed;
}
.doc-subtab.nx-subtab-active .doc-subtab-count {
    background: rgba(124,58,237,0.2);
    color: #a78bfa;
}
.doc-subtab-count {
    font-size: 10px;
    font-weight: 700;
    font-family: var(--font-mono);
    padding: 2px 6px;
    border-radius: 10px;
    background: rgba(148,163,184,0.1);
    color: var(--text-muted);
    transition: all 0.25s ease;
    min-width: 18px;
    text-align: center;
}
.doc-subtab i {
    font-size: 11px;
    opacity: 0.7;
}
.doc-subtab.nx-subtab-active i {
    opacity: 1;
}
@@media (max-width: 768px) {
    .doc-subtab { padding: 8px 10px; font-size: 11px; }
    .doc-subtab-count { display: none; }
}

/* Sidebar styles are in the nerve-centre layout */

</style>

{{-- SweetAlert2 --}}
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
var NxAlert = {
    _logo: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB4PSIyIiB5PSIyIiB3aWR0aD0iMjgiIGhlaWdodD0iMjgiIHJ4PSI2IiBmaWxsPSIjMDU5NjY5Ii8+PHJlY3QgeD0iMzQiIHk9IjIiIHdpZHRoPSIyOCIgaGVpZ2h0PSIyOCIgcng9IjYiIGZpbGw9IiMyNTYzZWIiLz48cmVjdCB4PSIyIiB5PSIzNCIgd2lkdGg9IjI4IiBoZWlnaHQ9IjI4IiByeD0iNiIgZmlsbD0iI2Q5NzcwNiIvPjxyZWN0IHg9IjM0IiB5PSIzNCIgd2lkdGg9IjI4IiBoZWlnaHQ9IjI4IiByeD0iNiIgZmlsbD0iIzdjM2FlZCIvPjwvc3ZnPgo=',
    _colors: {
        success:  { btn: '#059669', pastel: '#d1fae5', cls: 'nx-swal-success' },
        warning:  { btn: '#e11d48', pastel: '#ffe4e6', cls: 'nx-swal-warning' },
        info:     { btn: '#2563eb', pastel: '#dbeafe', cls: 'nx-swal-info' },
        confirm:  { btn: '#0891b2', pastel: '#cffafe', cls: 'nx-swal-confirm' },
        error:    { btn: '#d97706', pastel: '#fef3c7', cls: 'nx-swal-error' }
    },

    _fire: function(type, title, message, opts) {
        var c = this._colors[type] || this._colors.info;
        var config = {
            imageUrl: this._logo,
            imageWidth: 56,
            imageHeight: 56,
            title: title,
            html: message,
            confirmButtonText: (opts && opts.confirmText) || 'OK',
            confirmButtonColor: c.btn,
            background: '#ffffff',
            showCancelButton: !!(opts && opts.showCancel),
            cancelButtonText: (opts && opts.cancelText) || 'CANCEL',
            customClass: {
                popup: 'nx-swal-popup ' + c.cls,
                title: 'nx-swal-title',
                htmlContainer: 'nx-swal-html',
                actions: 'nx-swal-actions'
            }
        };
        return Swal.fire(config);
    },

    success: function(title, message) {
        return this._fire('success', title, message);
    },
    warning: function(title, message) {
        return this._fire('warning', title, message);
    },
    info: function(title, message) {
        return this._fire('info', title, message);
    },
    error: function(title, message) {
        return this._fire('error', title, message);
    },
    confirm: function(title, message, confirmText) {
        return this._fire('confirm', title, message, {
            showCancel: true,
            confirmText: confirmText || 'YES, CONFIRM',
            cancelText: 'CANCEL'
        });
    }
};
</script>

{{-- ============================================================
     TAB SWITCHING JS
     ============================================================ --}}
<script>
var nxTabOrder = ['company','addresses','contacts','banking','directors','tasks','documents','audit'];
var nxCurrentTab = 0;

function nxSwitchTab(tabName, el) {
    document.querySelectorAll('.nx-panel').forEach(function(p) { p.style.display = 'none'; });
    document.querySelectorAll('.nx-tab-badge').forEach(function(b) { b.classList.remove('nx-tab-active'); });
    var panel = document.getElementById('panel-' + tabName);
    if (panel) {
        panel.style.display = 'block';
        panel.style.animation = 'none';
        panel.offsetHeight;
        panel.style.animation = 'nxFadeIn 0.35s ease';
    }
    if (el) el.classList.add('nx-tab-active');
    nxCurrentTab = nxTabOrder.indexOf(tabName);
    if (nxCurrentTab < 0) nxCurrentTab = 0;
    var hdrBtn = document.getElementById('nxHeaderUploadBtn');
    if (hdrBtn) hdrBtn.style.display = (tabName === 'documents') ? 'block' : 'none';
    var navTabs = document.getElementById('nxMainNavTabs');
    if (navTabs) navTabs.style.display = (tabName === 'documents') ? 'none' : 'block';
    var hdrDefault = document.getElementById('nxHeaderDefault');
    var hdrDoc = document.getElementById('nxHeaderDocView');
    if (hdrDefault) hdrDefault.style.display = (tabName === 'documents') ? 'none' : 'flex';
    if (hdrDoc) hdrDoc.style.display = (tabName === 'documents') ? 'flex' : 'none';
}

function nxSwitchSub(parentTab, subName, el) {
    var parentPanel = document.getElementById('panel-' + parentTab);
    if (!parentPanel) return;
    parentPanel.querySelectorAll('.nx-subpanel').forEach(function(sp) { sp.style.display = 'none'; });
    parentPanel.querySelectorAll('.nx-subtab').forEach(function(st) { st.classList.remove('nx-subtab-active'); });
    var subPanel = document.getElementById('sub-' + subName);
    if (subPanel) {
        subPanel.style.display = 'block';
        subPanel.style.animation = 'none';
        subPanel.offsetHeight;
        subPanel.style.animation = 'nxSubFadeIn 0.3s ease';
    }
    if (el) el.classList.add('nx-subtab-active');
}

function nxConfirmDelete(e, itemName) {
    e.preventDefault();
    var form = e.target;
    NxAlert._fire('warning', 'Delete ' + itemName.charAt(0).toUpperCase() + itemName.slice(1), 'Are you sure you want to delete this ' + itemName + '?<br><span style="font-size:12px; color:#94a3b8;">This action cannot be undone.</span>', {
        showCancel: true,
        confirmText: 'YES, DELETE',
        cancelText: 'CANCEL'
    }).then(function(result) {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

// Auto-switch tab from URL parameter (e.g. ?tab=addresses or ?tab=documents&sub=doc-registrations)
(function() {
    var params = new URLSearchParams(window.location.search);
    var tab = params.get('tab');
    if (tab && nxTabOrder.indexOf(tab) >= 0) {
        var tabEl = document.querySelector('.nx-tab-badge[data-tab="' + tab + '"]');
        nxSwitchTab(tab, tabEl);
        var sub = params.get('sub');
        if (sub) {
            if (tab === 'documents' && sub.indexOf('doc-') === 0) {
                var docBtn = document.querySelector('.doc-subtab[data-sub="' + sub + '"]');
                if (docBtn) nxDocFilter(sub, docBtn);
            } else {
                var subEl = document.querySelector('.nx-subtab[data-sub="' + sub + '"]');
                if (subEl) nxSwitchSub(tab, sub, subEl);
            }
        }
    }
})();

// --- Embedded Google Map ---
var _nxAddrData = [
@foreach($addresses as $idx => $link)
@php
    $a = $link->address;
    $sl = trim(($a->unit_number ? 'Unit '.$a->unit_number.', ' : '').($a->complex_name ? $a->complex_name.', ' : '').$a->street_number.' '.$a->street_name);
    $cl = trim($a->city.', '.($a->province ? $a->province->name : '').', '.$a->postal_code, ', ');
    $mq = trim($sl.', '.($a->suburb ? $a->suburb->name.', ' : '').$cl, ', ');
    $lbl = $link->address_label ?? ('Address '.($idx+1));
@endphp
{
    label: {!! json_encode($lbl) !!},
    query: {!! json_encode($mq) !!},
    lat: {{ $a->latitude ? $a->latitude : 'null' }},
    lng: {{ $a->longitude ? $a->longitude : 'null' }},
    isPrimary: {{ $link->is_primary ? 'true' : 'false' }}
},
@endforeach
];

var _nxMap = null;
var _nxMarker = null;
var _nxGeocoder = null;
var _nxActiveMapIdx = -1;

function nxInitAddrMap() {
    var container = document.getElementById('nxEmbedMap');
    if (!container || typeof google === 'undefined' || !google.maps) return;

    _nxMap = new google.maps.Map(container, {
        zoom: 15,
        center: { lat: -26.2041, lng: 28.0473 },
        mapTypeControl: true,
        mapTypeControlOptions: { style: google.maps.MapTypeControlStyle.DROPDOWN_MENU, position: google.maps.ControlPosition.TOP_RIGHT },
        streetViewControl: true,
        fullscreenControl: true,
        zoomControl: true
    });
    _nxGeocoder = new google.maps.Geocoder();
    _nxMarker = new google.maps.Marker({ map: _nxMap });

    // Show primary address by default, or first address
    var defaultIdx = 0;
    for (var i = 0; i < _nxAddrData.length; i++) {
        if (_nxAddrData[i].isPrimary) { defaultIdx = i; break; }
    }
    if (_nxAddrData.length > 0) nxShowMap(defaultIdx);
}

function nxShowMap(idx) {
    if (!_nxMap || idx < 0 || idx >= _nxAddrData.length) return;

    var addr = _nxAddrData[idx];
    _nxActiveMapIdx = idx;

    // Update header
    var labelEl = document.getElementById('nxMapLabel');
    if (labelEl) labelEl.textContent = addr.label;
    var addrEl = document.getElementById('nxMapAddress');
    if (addrEl) addrEl.textContent = addr.query;

    // Update external link
    var extLink = document.getElementById('nxMapExternal');
    var mapsUrl = 'https://www.google.com/maps/dir/?api=1&destination=' + encodeURIComponent(addr.query);
    if (extLink) extLink.href = mapsUrl;

    // Update WhatsApp share
    var waLink = document.getElementById('nxMapWhatsApp');
    if (waLink) {
        var waText = addr.label + '\n' + addr.query + '\n\nDirections: ' + mapsUrl;
        waLink.href = 'https://wa.me/?text=' + encodeURIComponent(waText);
    }

    // Highlight active card
    document.querySelectorAll('.nx-addr-card').forEach(function(c) {
        c.style.borderLeft = '3px solid transparent';
        c.style.paddingLeft = '12px';
        c.style.background = '';
    });
    var activeCard = document.getElementById('nxAddrCard' + idx);
    if (activeCard) {
        activeCard.style.borderLeft = '3px solid #10b981';
        activeCard.style.background = 'rgba(16,185,129,0.03)';
    }

    function _nxUpdateCoords(lat, lng) {
        var coordsEl = document.getElementById('nxMapCoordsText');
        if (coordsEl) coordsEl.textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
    }

    if (addr.lat && addr.lng) {
        var pos = { lat: parseFloat(addr.lat), lng: parseFloat(addr.lng) };
        _nxMap.panTo(pos);
        _nxMap.setZoom(16);
        _nxMarker.setPosition(pos);
        _nxMarker.setTitle(addr.label);
        _nxUpdateCoords(pos.lat, pos.lng);
    } else {
        _nxGeocoder.geocode({ address: addr.query + ', South Africa' }, function(results, status) {
            if (status === 'OK' && results[0]) {
                var loc = results[0].geometry.location;
                _nxMap.panTo(loc);
                _nxMap.setZoom(16);
                _nxMarker.setPosition(loc);
                _nxMarker.setTitle(addr.label);
                _nxUpdateCoords(loc.lat(), loc.lng());
            }
        });
    }
}

// Load Google Maps API for the embedded map
(function() {
    if (typeof google !== 'undefined' && google.maps) {
        nxInitAddrMap();
        return;
    }
    var checkInterval = setInterval(function() {
        if (typeof google !== 'undefined' && google.maps) {
            clearInterval(checkInterval);
            nxInitAddrMap();
        }
    }, 500);
    setTimeout(function() { clearInterval(checkInterval); }, 15000);
    // Load script if not already loaded
    if (!document.querySelector('script[src*="maps.googleapis.com"]')) {
        var s = document.createElement('script');
        s.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDlFzdbBe7bMPm9jrCo6C8340ELKtsZjEw&callback=nxInitAddrMap';
        s.async = true;
        s.defer = true;
        document.body.appendChild(s);
    }
})();

// --- Link Address Panel ---
function nxToggleLinkPanel() {
    var panel = document.getElementById('nxLinkAddressPanel');
    if (panel.style.display === 'none') {
        panel.style.display = 'block';
        document.getElementById('nxRegistrySearchInput').focus();
        nxLoadRegistryAddresses('');
    } else {
        panel.style.display = 'none';
        document.getElementById('nxRegistryResults').innerHTML = '';
        document.getElementById('nxRegistrySearchInput').value = '';
    }
}

function nxLoadRegistryAddresses(query) {
    var container = document.getElementById('nxRegistryResults');
    container.innerHTML = '<div style="text-align:center; padding:20px; color:var(--text-muted); font-size:13px;"><i class="fas fa-spinner fa-spin" style="margin-right:6px;"></i>Loading...</div>';
    fetch('{{ route("nexcore.clients.show.addresses.search-registry", $client->id) }}?q=' + encodeURIComponent(query))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data.length) {
                container.innerHTML = '<div style="text-align:center; padding:24px; color:var(--text-muted); font-size:13px;"><i class="fas fa-search" style="font-size:20px; opacity:0.3; display:block; margin-bottom:8px;"></i>No addresses found in the registry</div>';
                return;
            }
            var html = '<div style="font-size:12px; color:var(--text-muted); margin-bottom:10px; padding-left:4px;">' + data.length + ' address' + (data.length !== 1 ? 'es' : '') + (query ? ' matching "' + query + '"' : ' in registry') + '</div>';
            data.forEach(function(a) {
                var addrText = a.line1 + ', ' + a.line2;
                html += '<div class="nx-reg-result" data-id="' + a.id + '" data-addr="' + addrText.replace(/"/g, '&quot;') + '" ondblclick="nxLinkExisting(' + a.id + ', this)" style="padding:14px 16px; border:1px solid var(--border-default); border-radius:8px; cursor:pointer; transition:all 0.2s ease; margin-bottom:8px; background:var(--bg-raised);" onmouseover="this.style.borderColor=\'rgba(0,210,211,0.4)\';this.style.background=\'rgba(0,210,211,0.03)\'" onmouseout="if(!this.classList.contains(\'nx-reg-selected\')){this.style.borderColor=\'var(--border-default)\';this.style.background=\'var(--bg-raised)\'}">' +
                    '<div style="font-size:15px; font-weight:600; color:var(--text-primary);"><i class="fas fa-map-marker-alt" style="color:#00d2d3; font-size:13px; margin-right:6px;"></i>' + a.line1 + '</div>' +
                    '<div style="font-size:13px; color:var(--text-secondary); margin-top:4px; padding-left:22px;">' + a.line2 + '</div>' +
                    (a.category ? '<span style="font-size:11px; color:var(--accent-amber); margin-top:6px; display:inline-block; padding-left:22px;">' + a.category + '</span>' : '') +
                    '<div style="font-size:11px; color:var(--text-muted); margin-top:6px; padding-left:22px; opacity:0.5;"><i class="fas fa-mouse" style="margin-right:4px;"></i>Double-click to link</div>' +
                    '</div>';
            });
            container.innerHTML = html;
        });
}

var _nxRegTimer = null;
var _nxRegInput = document.getElementById('nxRegistrySearchInput');
if (_nxRegInput) {
    _nxRegInput.addEventListener('input', function() {
        clearTimeout(_nxRegTimer);
        var val = this.value.trim();
        _nxRegTimer = setTimeout(function() {
            nxLoadRegistryAddresses(val);
        }, 300);
    });
}

// Auto-fill label from address type selection
var _nxLinkTypeEl = document.getElementById('nxLinkType');
var _nxLinkLabelEl = document.getElementById('nxLinkLabel');
var _nxLabelManuallyEdited = false;
if (_nxLinkTypeEl && _nxLinkLabelEl) {
    _nxLinkLabelEl.addEventListener('input', function() {
        _nxLabelManuallyEdited = true;
    });
    _nxLinkTypeEl.addEventListener('change', function() {
        var selectedText = this.options[this.selectedIndex].text;
        if (!_nxLabelManuallyEdited || !_nxLinkLabelEl.value.trim()) {
            _nxLinkLabelEl.value = (this.value ? selectedText : '');
            _nxLabelManuallyEdited = false;
        }
    });
}

function nxUnlinkAddress(linkId, label) {
    var totalAddresses = document.querySelectorAll('.nx-addr-card').length;
    if (totalAddresses <= 1) {
        NxAlert.warning('Cannot Unlink', 'This is the only address linked to this client.<br><br>Please <strong>add a new address</strong> before unlinking this one.<br><span style="font-size:12px; color:#94a3b8;">A client must always have at least one address.</span>');
        return;
    }
    NxAlert._fire('warning', 'Unlink Address', 'Are you sure you want to unlink <strong style="color:#e11d48;">' + label + '</strong> from this client?<br><span style="font-size:12px; color:#94a3b8;">The address will remain in the central registry.</span>', {
        showCancel: true,
        confirmText: 'YES, UNLINK',
        cancelText: 'CANCEL'
    }).then(function(result) {
        if (result.isConfirmed) {
            document.getElementById('nxUnlinkForm' + linkId).submit();
        }
    });
}

function nxLinkExisting(addressId, el) {
    var typeEl = document.getElementById('nxLinkType');
    var labelEl = document.getElementById('nxLinkLabel');
    var typeId = typeEl.value;
    var label = labelEl.value.trim();
    var addrText = el.getAttribute('data-addr') || 'this address';

    if (!typeId) {
        NxAlert.warning('Address Type Required', 'Please select an address type before linking.').then(function() { typeEl.focus(); });
        return;
    }

    if (!label) {
        NxAlert.warning('Label Required', 'Please enter an address label<br>(e.g. Head Office, Warehouse, Factory).').then(function() { labelEl.focus(); });
        return;
    }

    NxAlert.confirm(
        'Link Address',
        'Are you sure you want to link<br><strong style="color:#0891b2;">' + addrText + '</strong><br>as <strong style="color:#059669;">"' + label + '"</strong>?',
        'YES, LINK IT'
    ).then(function(result) {
        if (result.isConfirmed) {
            var isPrimary = document.getElementById('nxLinkPrimary').checked ? 1 : 0;
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("nexcore.clients.show.addresses.store", $client->id) }}';
            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                '<input type="hidden" name="existing_address_id" value="' + addressId + '">' +
                '<input type="hidden" name="address_type_id" value="' + typeId + '">' +
                '<input type="hidden" name="address_label" value="' + label.replace(/"/g, '&quot;') + '">' +
                '<input type="hidden" name="is_primary" value="' + isPrimary + '">';
            document.body.appendChild(form);
            form.submit();
        }
    });
}

</script>

{{-- Sidebar JS is loaded via the shared partial --}}

@endsection
