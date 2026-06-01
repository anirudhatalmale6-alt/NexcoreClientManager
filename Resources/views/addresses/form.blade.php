@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', (isset($link) ? 'Edit' : 'New') . ' Address - ' . $client->company_name)
@section('page_heading', isset($link) ? 'EDIT ADDRESS' : 'NEW ADDRESS')

@push('styles')
<style>
.sl-form-section {
    background: var(--bg-surface);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-lg);
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-card);
}
.sl-form-section-title {
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border-subtle);
    display: flex;
    align-items: center;
    gap: 10px;
}
.sl-form-section-title i { font-size: 16px; }
.sl-form-grid { display: grid; gap: 16px; }
.sl-form-grid.cols-2 { grid-template-columns: 1fr 1fr; }
.sl-form-grid.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
.sl-form-grid.cols-4 { grid-template-columns: 1fr 1fr 1fr 1fr; }

.sl-field label .req { color: var(--accent-red); margin-left: 2px; }
.sl-field select {
    background: var(--bg-raised);
    border: 1px solid var(--border-default);
    border-radius: var(--radius-sm);
    padding: 9px 12px;
    color: var(--text-primary);
    font-size: 15px;
    transition: var(--transition-fast);
    outline: none;
    width: 100%;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%235a6478' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 32px;
}
.sl-field select:focus { border-color: var(--accent-green); box-shadow: 0 0 0 2px rgba(34,197,94,0.15); }
.sl-field textarea {
    background: var(--bg-raised);
    border: 1px solid var(--border-default);
    border-radius: var(--radius-sm);
    padding: 9px 12px;
    color: var(--text-primary);
    font-size: 15px;
    transition: var(--transition-fast);
    outline: none;
    width: 100%;
    resize: vertical;
    min-height: 60px;
}
.sl-field textarea:focus { border-color: var(--accent-green); box-shadow: 0 0 0 2px rgba(34,197,94,0.15); }
.sl-field input { width: 100%; }

.sl-radio-group { display: flex; gap: 8px; flex-wrap: wrap; }
.sl-radio-pill {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border-radius: 20px;
    background: var(--bg-raised);
    border: 1px solid var(--border-default);
    cursor: pointer;
    transition: var(--transition-fast);
    font-size: 14px;
    font-weight: 600;
    color: var(--text-muted);
}
.sl-radio-pill:hover { border-color: var(--border-strong); color: var(--text-secondary); }
.sl-radio-pill input[type="radio"] { display: none; }
.sl-radio-pill.active {
    background: var(--accent-green-dim);
    border-color: rgba(34,197,94,0.3);
    color: var(--accent-green);
}

.sl-gps-display {
    display: flex;
    gap: 16px;
    padding: 12px 16px;
    background: var(--bg-raised);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-sm);
}
.sl-gps-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-family: var(--font-mono);
    font-size: 14px;
    color: var(--text-secondary);
}
.sl-gps-item i { color: var(--accent-cyan); font-size: 13px; }

.sl-secondary-toggle {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 20px;
    background: var(--bg-raised);
    border: 1px solid var(--border-default);
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: var(--transition-fast);
    margin-bottom: 20px;
    color: var(--text-secondary);
    font-size: 15px;
    font-weight: 600;
}
.sl-secondary-toggle:hover { border-color: var(--accent-green); color: var(--text-primary); }
.sl-secondary-toggle i { transition: transform 0.3s; }
.sl-secondary-toggle.open i { transform: rotate(180deg); }
.sl-secondary-panel { display: none; }
.sl-secondary-panel.open { display: block; }

.sl-registry-result {
    padding: 12px 16px;
    border: 1px solid var(--border-default);
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: var(--transition-fast);
    margin-bottom: 6px;
}
.sl-registry-result:hover { border-color: var(--accent-cyan); background: rgba(6,182,212,0.05); }
.sl-registry-result.selected { border-color: var(--accent-green); background: rgba(34,197,94,0.08); }

.sl-selected-address {
    padding: 16px;
    background: rgba(34,197,94,0.06);
    border: 2px solid rgba(34,197,94,0.3);
    border-radius: var(--radius-md);
    margin-top: 12px;
}

@@media (max-width: 768px) {
    .sl-form-grid.cols-2, .sl-form-grid.cols-3, .sl-form-grid.cols-4 { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
@php
    $addr = isset($link) ? $link->address : null;
    $sec = $addr && $addr->secondary ? $addr->secondary : null;
@endphp

<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(6,182,212,0.15), rgba(6,182,212,0.05)); border:1px solid rgba(6,182,212,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-map-marker-alt" style="color:var(--accent-cyan); font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">{{ isset($link) ? 'Edit Address' : 'New Address' }}</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.show.dashboard', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>
</div>

@if($errors->any())
<div class="sl-verdict reject sl-mb-md sl-animate d2" style="padding:14px 20px;">
    <div class="sl-verdict-icon" style="width:32px;height:32px;font-size:16px;"><i class="fas fa-exclamation-triangle"></i></div>
    <div>
        <div class="sl-verdict-text" style="font-size:15px;">Please correct the following errors:</div>
        <ul style="margin:6px 0 0; padding-left:20px; font-size:13px; color:var(--text-secondary);">
            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
</div>
@endif

<form method="POST" id="addressForm"
      action="{{ isset($link) ? route('nexcore.clients.show.addresses.update', [$client->id, $link->id]) : route('nexcore.clients.show.addresses.store', $client->id) }}">
    @csrf
    @if(isset($link)) @method('PUT') @endif
    <input type="hidden" name="existing_address_id" id="existingAddressId" value="">

    {{-- LINK DETAILS --}}
    <div class="sl-form-section sl-animate d2">
        <div class="sl-form-section-title" style="color:var(--accent-cyan);">
            <i class="fas fa-link"></i> Link Details
        </div>
        <div class="sl-form-grid cols-3">
            <div class="sl-field">
                <label>Address Type <span class="req">*</span></label>
                <select name="address_type_id" required>
                    <option value="">-- Select Type --</option>
                    @foreach($addressTypes as $type)
                        <option value="{{ $type->id }}" {{ old('address_type_id', $link->address_type_id ?? '') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sl-field">
                <label>Address Label</label>
                <input type="text" name="address_label" value="{{ old('address_label', $link->address_label ?? '') }}" placeholder="e.g. Head Office, Factory, Depot...">
            </div>
            <div class="sl-field" style="display:flex; align-items:center; gap:16px; padding-top:22px;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer; margin:0;">
                    <input type="hidden" name="is_primary" value="0">
                    <input type="checkbox" name="is_primary" value="1" {{ old('is_primary', $link->is_primary ?? false) ? 'checked' : '' }} style="width:18px; height:18px; accent-color:var(--accent-green);">
                    <span style="font-weight:600; color:var(--accent-green);"><i class="fas fa-star" style="margin-right:4px;"></i> Primary Address</span>
                </label>
            </div>
        </div>
    </div>

    @if(!isset($link))
    {{-- SEARCH EXISTING (create mode only) --}}
    <div class="sl-form-section sl-animate d3">
        <div class="sl-form-section-title" style="color:var(--accent-amber);">
            <i class="fas fa-search"></i> Link Existing Address
            <span style="font-size:12px; color:var(--text-muted); font-weight:500; margin-left:auto;">Search the address registry</span>
        </div>
        <div class="sl-field">
            <input type="text" id="registrySearch" placeholder="Type to search existing addresses (street, city, postal code)..." autocomplete="off">
        </div>
        <div id="registryResults" style="margin-top:8px;"></div>
        <div id="selectedExisting" style="display:none;">
            <div class="sl-selected-address">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                    <span style="font-size:13px; font-weight:700; color:var(--accent-green); text-transform:uppercase; letter-spacing:1px;"><i class="fas fa-check-circle"></i> Selected Address</span>
                    <button type="button" onclick="clearExisting()" style="background:none; border:none; color:var(--accent-red); cursor:pointer; font-size:13px; font-weight:600;"><i class="fas fa-times"></i> Clear</button>
                </div>
                <div id="selectedAddressText" style="font-size:15px; color:var(--text-primary);"></div>
            </div>
        </div>
        <div style="text-align:center; margin-top:16px; padding-top:16px; border-top:1px solid var(--border-subtle);">
            <span style="font-size:13px; color:var(--text-muted);">Address not found?</span>
            <span style="font-size:13px; font-weight:600; color:var(--accent-cyan);"> Fill in the form below to create a new one</span>
        </div>
    </div>
    @endif

    {{-- GOOGLE AUTOCOMPLETE --}}
    <div id="newAddressSection">
        <div class="sl-form-section sl-animate d4">
            <div class="sl-form-section-title" style="color:var(--accent-green);">
                <i class="fab fa-google"></i> Quick Address Search
            </div>
            <div class="sl-field">
                <label>Start typing to search via Google</label>
                <div style="position:relative;">
                    <input type="text" id="googleAutocomplete" placeholder="e.g. 14 Rivonia Road, Sandton, Johannesburg..." autocomplete="off" style="padding-right:80px;">
                    <span style="position:absolute; right:10px; top:50%; transform:translateY(-50%); font-size:11px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--accent-green); background:var(--accent-green-dim); padding:2px 8px; border-radius:var(--radius-sm); pointer-events:none;">Auto-Fill</span>
                </div>
                <div style="margin-top:6px; font-size:12px; color:var(--text-muted);">Select a result to auto-fill the fields below, or fill them manually.</div>
            </div>
        </div>

        {{-- MAIN ADDRESS --}}
        <div class="sl-form-section sl-animate d5">
            <div class="sl-form-section-title" style="color:var(--accent-green);">
                <i class="fas fa-map-marker-alt"></i> Main Address <span style="font-size:12px; color:var(--text-muted); font-weight:500; margin-left:8px;">( required fields )</span>
            </div>

            <div class="sl-form-grid cols-4" style="margin-bottom:16px;">
                <div class="sl-field">
                    <label>Unit Number</label>
                    <input type="text" name="unit_number" value="{{ old('unit_number', $addr->unit_number ?? '') }}" placeholder="e.g. 5, 12A">
                </div>
                <div class="sl-field">
                    <label>Complex / Estate Name</label>
                    <input type="text" name="complex_name" value="{{ old('complex_name', $addr->complex_name ?? '') }}" placeholder="e.g. Willowbrook Estate">
                </div>
                <div class="sl-field">
                    <label>Street Number <span class="req">*</span></label>
                    <input type="text" name="street_number" id="streetNumberField" value="{{ old('street_number', $addr->street_number ?? '') }}" placeholder="e.g. 144" required>
                </div>
                <div class="sl-field">
                    <label>Street Name <span class="req">*</span></label>
                    <input type="text" name="street_name" id="streetNameField" value="{{ old('street_name', $addr->street_name ?? '') }}" placeholder="e.g. Rivonia Road" required>
                </div>
            </div>

            <div class="sl-form-grid cols-3" style="margin-bottom:16px;">
                <div class="sl-field" style="position:relative;">
                    <label>Suburb</label>
                    <input type="text" id="suburbSearch" placeholder="Type to search suburbs..." autocomplete="off" value="{{ old('suburb_name', $addr && $addr->suburb ? $addr->suburb->name : '') }}">
                    <input type="hidden" name="suburb_id" id="suburbId" value="{{ old('suburb_id', $addr->suburb_id ?? '') }}">
                    <div id="suburbDropdown" style="display:none; position:absolute; z-index:100; background:var(--bg-raised); border:1px solid var(--border-default); border-radius:var(--radius-sm); max-height:200px; overflow-y:auto; width:100%; margin-top:2px;"></div>
                </div>
                <div class="sl-field">
                    <label>City / Town <span class="req">*</span></label>
                    <input type="text" name="city" id="cityField" value="{{ old('city', $addr->city ?? '') }}" placeholder="e.g. Johannesburg" required>
                </div>
                <div class="sl-field">
                    <label>Postal Code <span class="req">*</span></label>
                    <input type="text" name="postal_code" id="postalCodeField" value="{{ old('postal_code', $addr->postal_code ?? '') }}" placeholder="e.g. 2196" maxlength="10" required>
                </div>
            </div>

            <div class="sl-form-grid cols-3" style="margin-bottom:16px;">
                <div class="sl-field">
                    <label>Province <span class="req">*</span></label>
                    <select name="province_id" id="provinceSelect" required>
                        <option value="">Select Province</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov->id }}" {{ old('province_id', $addr->province_id ?? '') == $prov->id ? 'selected' : '' }}>{{ $prov->name }} ({{ $prov->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>Municipality</label>
                    <select name="municipality_id" id="municipalitySelect">
                        <option value="">Select Municipality</option>
                    </select>
                </div>
                <div class="sl-field">
                    <label>Ward</label>
                    <select name="ward_id" id="wardSelect">
                        <option value="">Select Ward</option>
                    </select>
                </div>
            </div>

            <div class="sl-form-grid cols-2" style="margin-bottom:16px;">
                <div class="sl-field">
                    <label>Country</label>
                    <select name="country">
                        <option value="ZA" selected>South Africa (ZA)</option>
                    </select>
                </div>
                <div class="sl-field">
                    <label>Address Category <span class="req">*</span></label>
                    <div class="sl-radio-group" id="categoryGroup">
                        @php $cat = old('address_category', $addr->address_category ?? 'Commercial'); @endphp
                        @foreach(['Residential', 'Commercial', 'Industrial', 'Agricultural', 'Mixed Use'] as $c)
                        <label class="sl-radio-pill {{ $cat == $c ? 'active' : '' }}">
                            <input type="radio" name="address_category" value="{{ $c }}" {{ $cat == $c ? 'checked' : '' }}>
                            {{ $c }}
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="sl-form-grid cols-2">
                <div class="sl-field">
                    <label>Google Formatted Address</label>
                    <textarea name="google_formatted_address" id="googleFormatted" rows="2">{{ old('google_formatted_address', $addr->google_formatted_address ?? '') }}</textarea>
                </div>
                <div class="sl-field">
                    <label>GPS Coordinates</label>
                    <div class="sl-gps-display">
                        <div class="sl-gps-item">
                            <i class="fas fa-location-crosshairs"></i>
                            <span>Lat:</span>
                            <input type="text" name="latitude" id="latField" value="{{ old('latitude', $addr->latitude ?? '') }}" placeholder="--" style="width:120px; background:transparent; border:none; color:var(--accent-cyan); font-family:var(--font-mono); font-size:14px; outline:none;">
                        </div>
                        <div class="sl-gps-item">
                            <i class="fas fa-location-crosshairs"></i>
                            <span>Lng:</span>
                            <input type="text" name="longitude" id="lngField" value="{{ old('longitude', $addr->longitude ?? '') }}" placeholder="--" style="width:120px; background:transparent; border:none; color:var(--accent-cyan); font-family:var(--font-mono); font-size:14px; outline:none;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECONDARY DETAILS --}}
        <div class="sl-secondary-toggle sl-animate d6" id="secondaryToggle" onclick="toggleSecondary()">
            <i class="fas fa-chevron-down"></i>
            <span>Extended Address Details</span>
            <span style="font-size:12px; color:var(--text-muted); margin-left:auto;">Optional - farms, sectional titles, compliance</span>
        </div>

        <div class="sl-secondary-panel" id="secondaryPanel">
            <div class="sl-form-section">
                <div class="sl-form-section-title" style="color:var(--accent-blue);"><i class="fas fa-building"></i> Property Details</div>
                <div class="sl-form-grid cols-4" style="margin-bottom:16px;">
                    <div class="sl-field"><label>Floor / Level</label><input type="text" name="floor_level" value="{{ old('floor_level', $sec->floor_level ?? '') }}" placeholder="e.g. 3rd Floor"></div>
                    <div class="sl-field"><label>Building Name</label><input type="text" name="building_name" value="{{ old('building_name', $sec->building_name ?? '') }}" placeholder="e.g. Sandton City Tower"></div>
                    <div class="sl-field"><label>Security Estate</label><input type="text" name="estate_name" value="{{ old('estate_name', $sec->estate_name ?? '') }}" placeholder="e.g. Dainfern Estate"></div>
                    <div class="sl-field"><label>Section Number</label><input type="text" name="section_number" value="{{ old('section_number', $sec->section_number ?? '') }}" placeholder="Sectional title #"></div>
                </div>

                <div class="sl-form-section-title" style="color:var(--accent-amber); margin-top:20px;"><i class="fas fa-tractor"></i> Farm / Rural Address</div>
                <div class="sl-form-grid cols-3" style="margin-bottom:16px;">
                    <div class="sl-field"><label>Farm Name</label><input type="text" name="farm_name" value="{{ old('farm_name', $sec->farm_name ?? '') }}" placeholder="e.g. Farm Rietfontein"></div>
                    <div class="sl-field"><label>Farm Number</label><input type="text" name="farm_number" value="{{ old('farm_number', $sec->farm_number ?? '') }}" placeholder="e.g. 123"></div>
                    <div class="sl-field"><label>Stand Number</label><input type="text" name="stand_number" value="{{ old('stand_number', $sec->stand_number ?? '') }}" placeholder="Township stand #"></div>
                </div>

                <div class="sl-form-section-title" style="color:var(--accent-purple); margin-top:20px;"><i class="fas fa-landmark"></i> Government / Compliance</div>
                <div class="sl-form-grid cols-3" style="margin-bottom:16px;">
                    <div class="sl-field"><label>ERF Number</label><input type="text" name="erf_number" value="{{ old('erf_number', $sec->erf_number ?? '') }}" placeholder="Cadastral reference"></div>
                    <div class="sl-field"><label>SG Code</label><input type="text" name="sg_code" value="{{ old('sg_code', $sec->sg_code ?? '') }}" placeholder="Surveyor General code"></div>
                    <div class="sl-field"><label>Municipal Account</label><input type="text" name="municipal_account_number" value="{{ old('municipal_account_number', $sec->municipal_account_number ?? '') }}" placeholder="Council account #"></div>
                </div>

                <div class="sl-form-section-title" style="color:var(--accent-cyan); margin-top:20px;"><i class="fas fa-satellite"></i> Digital Addressing</div>
                <div class="sl-form-grid cols-3" style="margin-bottom:16px;">
                    <div class="sl-field"><label>Google Plus Code</label><input type="text" name="plus_code" value="{{ old('plus_code', $sec->plus_code ?? '') }}" placeholder="e.g. 2GCJ+Q4 Sandton"></div>
                    <div class="sl-field"><label>What3Words</label><input type="text" name="what3words" value="{{ old('what3words', $sec->what3words ?? '') }}" placeholder="e.g. ///filled.count.soap"></div>
                    <div class="sl-field"><label>Google Place ID</label><input type="text" name="google_place_id" value="{{ old('google_place_id', $sec->google_place_id ?? '') }}" placeholder="Auto-filled from search"></div>
                </div>
                <div class="sl-form-grid cols-2">
                    <div class="sl-field"><label>Map URL</label><input type="text" name="map_url" value="{{ old('map_url', $sec->map_url ?? '') }}" placeholder="Google Maps link"></div>
                    <div class="sl-field">
                        <label>Address Source</label>
                        <select name="address_source">
                            @php $src = old('address_source', $sec->address_source ?? 'Manual'); @endphp
                            <option value="Manual" {{ $src == 'Manual' ? 'selected' : '' }}>Manual Entry</option>
                            <option value="Google" {{ $src == 'Google' ? 'selected' : '' }}>Google Autocomplete</option>
                            <option value="Imported" {{ $src == 'Imported' ? 'selected' : '' }}>Imported / Bulk Upload</option>
                            <option value="SARS" {{ $src == 'SARS' ? 'selected' : '' }}>SARS / Government</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- NOTES --}}
    <div class="sl-form-section sl-animate d7">
        <div class="sl-form-section-title" style="color:var(--text-secondary);"><i class="fas fa-sticky-note"></i> Notes</div>
        <div class="sl-field">
            <textarea name="notes" rows="3" placeholder="Optional notes about this address link...">{{ old('notes', $link->notes ?? '') }}</textarea>
        </div>
    </div>

    {{-- ACTIONS --}}
    <div class="sl-animate d8" style="margin-top:24px; display:flex; gap:12px;">
        <button type="submit" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-save"></i> {{ isset($link) ? 'Update Address' : 'Save Address' }}</button>
        <a href="{{ route('nexcore.clients.show.dashboard', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-times"></i> Cancel</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Radio pill toggling
document.querySelectorAll('.sl-radio-pill input[type="radio"]').forEach(function(r) {
    r.addEventListener('change', function() {
        document.querySelectorAll('.sl-radio-pill').forEach(function(p) { p.classList.remove('active'); });
        if (this.checked) this.closest('.sl-radio-pill').classList.add('active');
    });
});

// Toggle secondary panel
function toggleSecondary() {
    document.getElementById('secondaryToggle').classList.toggle('open');
    document.getElementById('secondaryPanel').classList.toggle('open');
}
@if(isset($link) && $sec)
document.addEventListener('DOMContentLoaded', function() { toggleSecondary(); });
@endif

// Registry search (create mode)
var regTimer = null;
var regSearch = document.getElementById('registrySearch');
if (regSearch) {
    regSearch.addEventListener('input', function() {
        clearTimeout(regTimer);
        var val = this.value.trim();
        if (val.length < 2) { document.getElementById('registryResults').innerHTML = ''; return; }
        regTimer = setTimeout(function() {
            fetch('{{ route("nexcore.clients.show.addresses.search-registry", $client->id) }}?q=' + encodeURIComponent(val))
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var html = '';
                    if (!data.length) {
                        html = '<div style="padding:12px; text-align:center; color:var(--text-muted); font-size:13px;">No matching addresses found in registry</div>';
                    } else {
                        data.forEach(function(a) {
                            html += '<div class="sl-registry-result" onclick="selectExisting(' + a.id + ', this)" data-id="' + a.id + '">' +
                                '<div style="font-size:15px; font-weight:600; color:var(--text-primary);">' + a.line1 + '</div>' +
                                '<div style="font-size:13px; color:var(--text-secondary); margin-top:2px;">' + a.line2 + '</div>' +
                                '<span style="font-size:11px; color:var(--accent-amber); margin-top:4px; display:inline-block;">' + (a.category || '') + '</span>' +
                                '</div>';
                        });
                    }
                    document.getElementById('registryResults').innerHTML = html;
                });
        }, 300);
    });
}

function selectExisting(id, el) {
    document.getElementById('existingAddressId').value = id;
    document.getElementById('newAddressSection').style.display = 'none';
    document.getElementById('selectedExisting').style.display = 'block';
    document.getElementById('selectedAddressText').innerHTML = el.innerHTML;
    document.getElementById('registryResults').innerHTML = '';
    regSearch.value = '';
    // Remove required from hidden fields
    document.querySelectorAll('#newAddressSection [required]').forEach(function(f) { f.removeAttribute('required'); });
}

function clearExisting() {
    document.getElementById('existingAddressId').value = '';
    document.getElementById('newAddressSection').style.display = 'block';
    document.getElementById('selectedExisting').style.display = 'none';
    document.getElementById('selectedAddressText').innerHTML = '';
    // Restore required
    document.getElementById('streetNumberField').setAttribute('required', '');
    document.getElementById('streetNameField').setAttribute('required', '');
    document.getElementById('cityField').setAttribute('required', '');
    document.getElementById('postalCodeField').setAttribute('required', '');
    document.getElementById('provinceSelect').setAttribute('required', '');
}

// Suburb AJAX search
var suburbTimer = null;
var suburbSearch = document.getElementById('suburbSearch');
var suburbDropdown = document.getElementById('suburbDropdown');
var suburbIdField = document.getElementById('suburbId');

if (suburbSearch) {
    suburbSearch.addEventListener('input', function() {
        clearTimeout(suburbTimer);
        var val = this.value.trim();
        if (val.length < 2) { suburbDropdown.style.display = 'none'; return; }
        suburbTimer = setTimeout(function() {
            fetch('/pmpro/lookup/search-suburbs?q=' + encodeURIComponent(val))
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (!data.length) { suburbDropdown.style.display = 'none'; return; }
                    var html = '';
                    data.forEach(function(s) {
                        var dataAttrs = 'data-id="' + s.id + '" data-name="' + (s.name || '').replace(/"/g,'&quot;') + '" data-city="' + (s.city || '').replace(/"/g,'&quot;') + '" data-postal="' + (s.postal_code || '') + '" data-prov="' + (s.province_id || '') + '" data-metro="' + (s.metro_id || '') + '" data-localmuni="' + (s.local_municipality_id || '') + '" data-district="' + (s.district_id || '') + '"';
                        html += '<div style="padding:8px 12px; cursor:pointer; font-size:14px; color:var(--text-secondary); border-bottom:1px solid var(--border-subtle); transition:background 0.15s;" ' +
                            'onmouseover="this.style.background=\'var(--bg-hover)\'" onmouseout="this.style.background=\'transparent\'" ' +
                            dataAttrs + ' onclick="selectSuburbEl(this)">' +
                            '<strong style="color:var(--text-primary);">' + s.name + '</strong>' +
                            '<span style="color:var(--text-muted); margin-left:8px;">' + (s.city || '') + '</span>' +
                            '<span style="float:right; font-family:var(--font-mono); color:var(--accent-cyan);">' + (s.postal_code || '') + '</span>' +
                            '</div>';
                    });
                    suburbDropdown.innerHTML = html;
                    suburbDropdown.style.display = 'block';
                });
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (!suburbSearch.contains(e.target) && !suburbDropdown.contains(e.target)) {
            suburbDropdown.style.display = 'none';
        }
    });
}

function selectSuburbEl(el) {
    var id = el.getAttribute('data-id');
    var name = el.getAttribute('data-name');
    var city = el.getAttribute('data-city');
    var postal = el.getAttribute('data-postal');
    var provId = el.getAttribute('data-prov');
    var metroId = el.getAttribute('data-metro');
    var localMuniId = el.getAttribute('data-localmuni');
    var districtId = el.getAttribute('data-district');

    suburbIdField.value = id;
    suburbSearch.value = name;
    suburbDropdown.style.display = 'none';
    if (city) document.getElementById('cityField').value = city;
    if (postal) document.getElementById('postalCodeField').value = postal;

    if (provId) {
        provinceSelect.value = provId;
        loadMunicipalitiesForProvince(provId, metroId || localMuniId);
    }
}

function selectSuburb(id, name, city, postalCode, provId, metroId, localMuniId) {
    suburbIdField.value = id;
    suburbSearch.value = name;
    suburbDropdown.style.display = 'none';
    if (city) document.getElementById('cityField').value = city;
    if (postalCode) document.getElementById('postalCodeField').value = postalCode;
    if (provId) {
        provinceSelect.value = provId;
        loadMunicipalitiesForProvince(provId, metroId || localMuniId);
    }
}

function setProvinceFromGoogle(provName) {
    if (!provName) return;
    var opts = provinceSelect.options;
    for (var i = 0; i < opts.length; i++) {
        if (opts[i].text.indexOf(provName) !== -1) {
            opts[i].selected = true;
            loadMunicipalitiesForProvince(opts[i].value);
            break;
        }
    }
}

// Province > Municipality > Ward cascade
var provinceSelect = document.getElementById('provinceSelect');
var municipalitySelect = document.getElementById('municipalitySelect');
var wardSelect = document.getElementById('wardSelect');
var _pendingMuniSelect = null;

function loadMunicipalitiesForProvince(pid, autoSelectId) {
    municipalitySelect.innerHTML = '<option value="">Loading...</option>';
    wardSelect.innerHTML = '<option value="">Select Ward</option>';
    _pendingMuniSelect = autoSelectId || null;

    Promise.all([
        fetch('/pmpro/lookup/local-municipalities/' + pid).then(function(r) { return r.json(); }),
        fetch('/pmpro/lookup/metros/' + pid).then(function(r) { return r.json(); })
    ]).then(function(results) {
        var locals = results[0], metros = results[1];
        var html = '<option value="">Select Municipality</option>';
        if (metros.length) {
            html += '<optgroup label="Metropolitan">';
            metros.forEach(function(m) { html += '<option value="' + m.id + '">' + m.name + '</option>'; });
            html += '</optgroup>';
        }
        if (locals.length) {
            html += '<optgroup label="Local Municipality">';
            locals.forEach(function(l) { html += '<option value="' + l.id + '">' + l.name + '</option>'; });
            html += '</optgroup>';
        }
        municipalitySelect.innerHTML = html;

        if (_pendingMuniSelect) {
            municipalitySelect.value = _pendingMuniSelect;
            if (municipalitySelect.value == _pendingMuniSelect) {
                loadWardsForMunicipality(_pendingMuniSelect);
            }
            _pendingMuniSelect = null;
        }
    });
}

function loadWardsForMunicipality(muniId) {
    wardSelect.innerHTML = '<option value="">Loading...</option>';
    fetch('/pmpro/lookup/wards/' + muniId)
        .then(function(r) { return r.json(); })
        .then(function(wards) {
            var html = '<option value="">Select Ward</option>';
            wards.forEach(function(w) {
                var label = 'Ward ' + (w.ward_number || w.id);
                if (w.ward_label) label = w.ward_label;
                html += '<option value="' + w.id + '">' + label + '</option>';
            });
            wardSelect.innerHTML = html;
        });
}

if (provinceSelect) {
    provinceSelect.addEventListener('change', function() {
        var pid = this.value;
        if (!pid) {
            municipalitySelect.innerHTML = '<option value="">Select Municipality</option>';
            wardSelect.innerHTML = '<option value="">Select Ward</option>';
            return;
        }
        loadMunicipalitiesForProvince(pid);
    });
}

if (municipalitySelect) {
    municipalitySelect.addEventListener('change', function() {
        var mid = this.value;
        if (!mid) { wardSelect.innerHTML = '<option value="">Select Ward</option>'; return; }
        loadWardsForMunicipality(mid);
    });
}
</script>

{{-- Google Places API --}}
<script>
function initGoogleAutocomplete() {
    var input = document.getElementById('googleAutocomplete');
    if (!input || typeof google === 'undefined') return;

    var autocomplete = new google.maps.places.Autocomplete(input, {
        componentRestrictions: { country: 'za' },
        fields: ['address_components', 'formatted_address', 'geometry', 'place_id', 'plus_code']
    });

    autocomplete.addListener('place_changed', function() {
        var place = autocomplete.getPlace();
        if (!place.address_components) return;

        var fields = {};
        place.address_components.forEach(function(c) {
            c.types.forEach(function(t) { fields[t] = c.long_name; });
        });

        var sn = document.getElementById('streetNumberField');
        var st = document.getElementById('streetNameField');
        if (sn) sn.value = fields.street_number || '';
        if (st) st.value = fields.route || '';

        document.getElementById('cityField').value = fields.locality || fields.administrative_area_level_2 || '';
        document.getElementById('postalCodeField').value = fields.postal_code || '';

        var googleSuburb = fields.sublocality_level_1 || fields.sublocality || fields.neighborhood || '';
        if (!googleSuburb && place.formatted_address) {
            var parts = place.formatted_address.split(',').map(function(p) { return p.trim(); });
            if (parts.length >= 4) {
                var candidate = parts[1];
                if (candidate && candidate !== (fields.locality || '') && candidate !== (fields.route || '') && !/^\d+$/.test(candidate)) {
                    googleSuburb = candidate;
                }
            }
        }
        if (!googleSuburb && fields.locality && fields.administrative_area_level_2 && fields.locality !== fields.administrative_area_level_2) {
            googleSuburb = fields.locality;
            document.getElementById('cityField').value = fields.administrative_area_level_2;
        }

        var _googleProvince = fields.administrative_area_level_1 || '';
        var _googleLocality = fields.locality || '';

        function applySuburbMatch(match) {
            suburbIdField.value = match.id;
            suburbSearch.value = match.name;
            if (match.province_id) {
                provinceSelect.value = match.province_id;
                loadMunicipalitiesForProvince(match.province_id, match.metro_id || match.local_municipality_id);
            } else {
                setProvinceFromGoogle(_googleProvince);
            }
        }

        if (googleSuburb && suburbSearch) {
            suburbSearch.value = googleSuburb;
            var _city = document.getElementById('cityField').value;
            var _postal = document.getElementById('postalCodeField').value;
            fetch('/pmpro/lookup/search-suburbs?q=' + encodeURIComponent(googleSuburb))
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.length) {
                        var match = data.find(function(s) { return s.name.toLowerCase() === googleSuburb.toLowerCase(); }) || data[0];
                        applySuburbMatch(match);
                    } else if (_googleLocality && _googleLocality.toLowerCase() !== googleSuburb.toLowerCase()) {
                        fetch('/pmpro/lookup/search-suburbs?q=' + encodeURIComponent(_googleLocality))
                            .then(function(r) { return r.json(); })
                            .then(function(data2) {
                                if (data2.length) {
                                    var match2 = data2.find(function(s) { return s.name.toLowerCase() === _googleLocality.toLowerCase(); }) || data2[0];
                                    applySuburbMatch(match2);
                                } else {
                                    setProvinceFromGoogle(_googleProvince);
                                }
                            });
                    } else {
                        setProvinceFromGoogle(_googleProvince);
                    }
                });
        } else if (_googleLocality && suburbSearch) {
            suburbSearch.value = _googleLocality;
            fetch('/pmpro/lookup/search-suburbs?q=' + encodeURIComponent(_googleLocality))
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.length) {
                        var match = data.find(function(s) { return s.name.toLowerCase() === _googleLocality.toLowerCase(); }) || data[0];
                        applySuburbMatch(match);
                    } else {
                        setProvinceFromGoogle(_googleProvince);
                    }
                });
        } else {
            setProvinceFromGoogle(_googleProvince);
        }

        if (place.formatted_address) document.getElementById('googleFormatted').value = place.formatted_address;
        if (place.geometry) {
            document.getElementById('latField').value = place.geometry.location.lat().toFixed(8);
            document.getElementById('lngField').value = place.geometry.location.lng().toFixed(8);
        }
        if (place.place_id) {
            var pid = document.querySelector('[name="google_place_id"]');
            if (pid) {
                pid.value = place.place_id;
                if (!document.getElementById('secondaryPanel').classList.contains('open')) toggleSecondary();
            }
        }
        if (place.plus_code && place.plus_code.global_code) {
            var pc = document.querySelector('[name="plus_code"]');
            if (pc) pc.value = place.plus_code.global_code;
        }

        var sourceField = document.querySelector('[name="address_source"]');
        if (sourceField) sourceField.value = 'Google';
    });
}
</script>
<script>
(function() {
    var s = document.createElement('script');
    s.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDlFzdbBe7bMPm9jrCo6C8340ELKtsZjEw&libraries=places&callback=initGoogleAutocomplete';
    s.async = true;
    s.defer = true;
    document.body.appendChild(s);
})();
</script>
@endpush
