@extends('nexcore_client_manager::layouts.app')

@section('title', isset($client) ? 'Edit Client' : 'New Client')
@section('page_heading', isset($client) ? 'EDIT CLIENT' : 'NEW CLIENT')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
.ncm-form-section { background:var(--bg-surface); border:1px solid var(--border-subtle); border-radius:var(--radius-lg); padding:24px; margin-bottom:20px; box-shadow:var(--shadow-card); }
.ncm-form-title { font-size:15px; font-weight:700; letter-spacing:2px; text-transform:uppercase; margin-bottom:20px; padding-bottom:10px; border-bottom:1px solid var(--border-subtle); display:flex; align-items:center; gap:10px; }
.ncm-form-title i { font-size:16px; }
.ncm-form-title.green { color:var(--accent-green); }
.ncm-form-title.cyan { color:var(--accent-cyan); }
.ncm-form-title.blue { color:var(--accent-blue); }
.ncm-form-title.amber { color:var(--accent-amber); }
.ncm-form-grid { display:grid; gap:16px; }
.ncm-form-grid.cols-2 { grid-template-columns:1fr 1fr; }
.ncm-form-grid.cols-3 { grid-template-columns:1fr 1fr 1fr; }
.sl-field label .req { color:var(--accent-red); margin-left:2px; }
.sl-field select { background:var(--bg-raised); border:1px solid var(--border-default); border-radius:var(--radius-sm); padding:9px 12px; color:var(--text-primary); font-size:15px; width:100%; outline:none; appearance:none; -webkit-appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%235a6478' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; padding-right:32px; }
.sl-field select:focus { border-color:var(--accent-green); box-shadow:0 0 0 2px rgba(34,197,94,0.15); }
.sl-field input { width:100%; }
.sl-field textarea { width:100%; background:var(--bg-raised); border:1px solid var(--border-default); border-radius:var(--radius-sm); padding:12px; color:var(--text-primary); font-size:14px; font-family:var(--font-body); line-height:1.6; resize:vertical; outline:none; }
.sl-field textarea:focus { border-color:var(--accent-green); box-shadow:0 0 0 2px rgba(34,197,94,0.15); }
.ncm-hint { font-size:12px; color:var(--text-muted); margin-top:4px; }
.ncm-logo-preview { width:72px; height:72px; border-radius:12px; background:rgba(255,255,255,0.06); border:1px solid var(--border-subtle); display:flex; align-items:center; justify-content:center; overflow:hidden; }
.ncm-logo-preview img { width:100%; height:100%; object-fit:contain; padding:4px; }
@media (max-width:768px) { .ncm-form-grid.cols-2, .ncm-form-grid.cols-3 { grid-template-columns:1fr; } }

/* Flatpickr dark theme overrides */
.flatpickr-calendar { background:var(--bg-surface) !important; border:1px solid var(--border-subtle) !important; box-shadow:0 8px 32px rgba(0,0,0,0.5) !important; }
.flatpickr-day { color:var(--text-primary) !important; }
.flatpickr-day.selected { background:var(--accent-green) !important; border-color:var(--accent-green) !important; color:#fff !important; }
.flatpickr-day:hover { background:rgba(34,197,94,0.2) !important; }
.flatpickr-month, .flatpickr-current-month, .flatpickr-monthDropdown-months { background:var(--bg-raised) !important; color:var(--text-primary) !important; }
.flatpickr-weekday { color:var(--text-muted) !important; }
.flatpickr-months .flatpickr-prev-month, .flatpickr-months .flatpickr-next-month { fill:var(--text-primary) !important; color:var(--text-primary) !important; }
span.flatpickr-weekday { color:var(--accent-cyan) !important; font-weight:600; }
.numInputWrapper span { border-color:var(--border-subtle) !important; }
.numInputWrapper span:hover { background:rgba(34,197,94,0.1) !important; }
.flatpickr-day.today { border-color:var(--accent-amber) !important; }

/* Select2 dark theme overrides */
.select2-container--default .select2-selection--single { background:var(--bg-raised) !important; border:1px solid var(--border-default) !important; border-radius:var(--radius-sm) !important; height:40px !important; }
.select2-container--default .select2-selection--single .select2-selection__rendered { color:var(--text-primary) !important; line-height:40px !important; padding-left:12px !important; font-size:14px !important; }
.select2-container--default .select2-selection--single .select2-selection__arrow { height:40px !important; }
.select2-container--default .select2-selection--single .select2-selection__arrow b { border-color:var(--text-muted) transparent transparent transparent !important; }
.select2-container--default.select2-container--open .select2-selection--single { border-color:var(--accent-green) !important; box-shadow:0 0 0 2px rgba(34,197,94,0.15) !important; }
.select2-dropdown { background:var(--bg-surface) !important; border:1px solid var(--border-subtle) !important; border-radius:var(--radius-sm) !important; box-shadow:0 8px 32px rgba(0,0,0,0.5) !important; }
.select2-search--dropdown .select2-search__field { background:var(--bg-raised) !important; border:1px solid var(--border-default) !important; color:var(--text-primary) !important; border-radius:var(--radius-sm) !important; padding:8px 12px !important; font-size:14px !important; outline:none !important; }
.select2-search--dropdown .select2-search__field:focus { border-color:var(--accent-green) !important; }
.select2-results__option { color:var(--text-primary) !important; padding:8px 12px !important; font-size:14px !important; }
.select2-results__option--highlighted[aria-selected] { background:rgba(34,197,94,0.15) !important; color:var(--accent-green) !important; }
.select2-results__option[aria-selected=true] { background:rgba(6,182,212,0.1) !important; color:var(--accent-cyan) !important; }
.select2-container--default .select2-selection--single .select2-selection__placeholder { color:var(--text-muted) !important; }
.select2-container { width:100% !important; }
</style>
@endpush

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <h1 class="sl-page-title">{{ isset($client) ? 'Edit '.$client->company_name : 'Register New Client' }}</h1>
        <span class="sl-page-subtitle">{{ isset($client) ? 'Update company information' : 'Add a new company to the client registry' }}</span>
        <div style="margin-left:auto;">
            @if(isset($client))
                <a href="{{ route('nexcore.clients.show.dashboard', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            @else
                <a href="{{ route('nexcore.clients.index') }}" class="neon-btn neon-btn-ghost"><i class="fas fa-arrow-left"></i> Back to List</a>
            @endif
        </div>
    </div>
</div>

<form method="POST" action="{{ isset($client) ? route('nexcore.clients.show.update', $client->id) : route('nexcore.clients.store') }}" class="sl-animate d2" enctype="multipart/form-data">
    @csrf
    @if(isset($client)) @method('PUT') @endif

    @if($errors->any())
    <div class="sl-verdict reject sl-mb-md" style="padding:14px 20px;">
        <div class="sl-verdict-icon" style="width:32px;height:32px;font-size:16px;"><i class="fas fa-exclamation-triangle"></i></div>
        <div>@foreach($errors->all() as $error)<div class="sl-verdict-text" style="font-size:14px;">{{ $error }}</div>@endforeach</div>
    </div>
    @endif

    {{-- Company Identity --}}
    <div class="ncm-form-section">
        <div class="ncm-form-title green"><i class="fas fa-building"></i> Company Identity</div>

        <div style="margin-bottom:20px;">
            <label style="display:block; font-size:13px; font-weight:600; color:var(--text-secondary); margin-bottom:8px;"><i class="fas fa-image" style="margin-right:6px; color:var(--accent-green);"></i> Company Logo</label>
            @if(isset($client) && $client->client_logo)
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:12px; padding:14px; background:var(--bg-raised); border-radius:var(--radius-sm); border:1px solid var(--border-subtle);">
                <div class="ncm-logo-preview">
                    <img src="{{ asset($client->client_logo) }}" alt="{{ $client->client_code }}">
                </div>
                <div style="flex:1;">
                    <div style="font-weight:600; font-size:15px;">{{ $client->company_name }}</div>
                    <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">Current logo</div>
                </div>
                <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-size:13px; color:var(--accent-red); padding:6px 12px; border:1px solid rgba(239,68,68,0.3); border-radius:var(--radius-sm); background:rgba(239,68,68,0.06);">
                    <input type="checkbox" name="remove_logo" value="1" style="accent-color:var(--accent-red);"> Remove
                </label>
            </div>
            @endif
            <div>
                <input type="file" name="client_logo" id="clientLogoInput" accept="image/png,image/jpeg,image/svg+xml,image/webp" style="display:none;" onchange="previewLogo(this)">
                <label for="clientLogoInput" style="display:flex; align-items:center; justify-content:center; gap:10px; padding:16px; border:2px dashed var(--border-default); border-radius:var(--radius-sm); cursor:pointer; color:var(--text-muted); font-size:13px; transition:all 0.2s;" onmouseover="this.style.borderColor='var(--accent-green)';this.style.color='var(--accent-green)'" onmouseout="this.style.borderColor='var(--border-default)';this.style.color='var(--text-muted)'">
                    <i class="fas fa-cloud-upload-alt" style="font-size:18px;"></i>
                    <span id="logoFileName">Click to upload company logo (PNG, JPG, SVG, WEBP - max 2MB)</span>
                </label>
                <div id="logoPreviewWrap" style="display:none; margin-top:10px; padding:10px; background:var(--bg-raised); border-radius:var(--radius-sm); border:1px solid var(--border-subtle); align-items:center;">
                    <img id="logoPreviewImg" src="" alt="Preview" style="max-width:72px; max-height:72px; object-fit:contain; border-radius:8px;">
                    <span style="margin-left:12px; font-size:13px; color:var(--accent-green);"><i class="fas fa-check-circle"></i> New logo selected</span>
                </div>
            </div>
            <div class="ncm-hint">Square logos work best (e.g. 200x200px)</div>
        </div>

        <div style="margin-bottom:20px;">
            <label style="display:block; font-size:13px; font-weight:600; color:var(--text-secondary); margin-bottom:8px;"><i class="fas fa-stamp" style="margin-right:6px; color:var(--accent-amber);"></i> Watermark Logo</label>
            @if(isset($client) && $client->watermark_logo)
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:12px; padding:14px; background:var(--bg-raised); border-radius:var(--radius-sm); border:1px solid var(--border-subtle);">
                <div class="ncm-logo-preview">
                    <img src="{{ asset($client->watermark_logo) }}" alt="Watermark">
                </div>
                <div style="flex:1;">
                    <div style="font-weight:600; font-size:15px;">Current Watermark</div>
                    <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">Used on report pages</div>
                </div>
                <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-size:13px; color:var(--accent-red); padding:6px 12px; border:1px solid rgba(239,68,68,0.3); border-radius:var(--radius-sm); background:rgba(239,68,68,0.06);">
                    <input type="checkbox" name="remove_watermark" value="1" style="accent-color:var(--accent-red);"> Remove
                </label>
            </div>
            @endif
            <div>
                <input type="file" name="watermark_logo" id="watermarkLogoInput" accept="image/png,image/jpeg,image/svg+xml,image/webp" style="display:none;" onchange="previewWatermark(this)">
                <label for="watermarkLogoInput" style="display:flex; align-items:center; justify-content:center; gap:10px; padding:16px; border:2px dashed var(--border-default); border-radius:var(--radius-sm); cursor:pointer; color:var(--text-muted); font-size:13px; transition:all 0.2s;" onmouseover="this.style.borderColor='var(--accent-amber)';this.style.color='var(--accent-amber)'" onmouseout="this.style.borderColor='var(--border-default)';this.style.color='var(--text-muted)'">
                    <i class="fas fa-cloud-upload-alt" style="font-size:18px;"></i>
                    <span id="watermarkFileName">Click to upload watermark logo (PNG, JPG, SVG, WEBP - max 2MB)</span>
                </label>
                <div id="watermarkPreviewWrap" style="display:none; margin-top:10px; padding:10px; background:var(--bg-raised); border-radius:var(--radius-sm); border:1px solid var(--border-subtle); align-items:center;">
                    <img id="watermarkPreviewImg" src="" alt="Preview" style="max-width:72px; max-height:72px; object-fit:contain; border-radius:8px;">
                    <span style="margin-left:12px; font-size:13px; color:var(--accent-amber);"><i class="fas fa-check-circle"></i> New watermark selected</span>
                </div>
            </div>
            <div class="ncm-hint">Square image works best. Used as watermark on report pages if uploaded, otherwise practice default is used.</div>
        </div>

        <div class="ncm-form-grid cols-2">
            <div class="sl-field">
                <label>Company Name <span class="req">*</span></label>
                <input type="text" name="company_name" value="{{ old('company_name', $client->company_name ?? '') }}" placeholder="e.g. ABC Trading (Pty) Ltd" required style="font-size:16px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Trading Name</label>
                <input type="text" name="trading_name" value="{{ old('trading_name', $client->trading_name ?? '') }}" placeholder="e.g. ABC Trading">
                <div class="ncm-hint">Trading name if different from registered name</div>
            </div>
        </div>
        <div class="ncm-form-grid cols-3" style="margin-top:16px;">
            <div class="sl-field">
                <label>Registration Number</label>
                <input type="text" name="registration_number" value="{{ old('registration_number', $client->registration_number ?? '') }}" placeholder="e.g. 2020/123456/07" style="font-family:var(--font-mono); font-size:16px; font-weight:600; color:var(--accent-cyan);">
                <div class="ncm-hint">CIPC registration number</div>
            </div>
            <div class="sl-field">
                <label>Company Type</label>
                <select name="company_type_id" class="ncm-select2" data-placeholder="-- Select Type --">
                    <option value=""></option>
                    @foreach($companyTypes as $type)
                        <option value="{{ $type->id }}" {{ old('company_type_id', $client->company_type_id ?? '') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sl-field">
                <label>Company Status</label>
                <select name="company_status_id" class="ncm-select2" data-placeholder="-- Select Status --">
                    <option value=""></option>
                    @foreach($companyStatuses as $status)
                        <option value="{{ $status->id }}" {{ old('company_status_id', $client->company_status_id ?? '') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="ncm-form-grid cols-3" style="margin-top:16px;">
            <div class="sl-field">
                <label>Industry</label>
                <select name="industry_id" class="ncm-select2" data-placeholder="-- Select Industry --">
                    <option value=""></option>
                    @foreach($industries as $ind)
                        <option value="{{ $ind->id }}" {{ old('industry_id', $client->industry_id ?? '') == $ind->id ? 'selected' : '' }}>{{ $ind->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sl-field">
                <label>SARS SIC Code</label>
                <select name="sic_code_id" id="nxSicCodeSelect" class="ncm-select2" data-placeholder="-- Search SIC Code --">
                    <option value=""></option>
                    @foreach($sicCodes as $sic)
                        <option value="{{ $sic->id }}" {{ old('sic_code_id', $client->sic_code_id ?? '') == $sic->id ? 'selected' : '' }}>{{ $sic->sic_code }} - {{ $sic->description }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sl-field">
                <label>BEE Status Level</label>
                <select name="bee_status_id" class="ncm-select2" data-placeholder="-- Select BEE Level --">
                    <option value=""></option>
                    @foreach($beeLevels as $bee)
                        <option value="{{ $bee->id }}" {{ old('bee_status_id', $client->bee_status_id ?? '') == $bee->id ? 'selected' : '' }}>{{ $bee->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="ncm-form-grid cols-3" style="margin-top:16px;">
            <div class="sl-field">
                <label>SARS Profit Code</label>
                <input type="text" name="profit_code" id="nxProfitCode" value="{{ old('profit_code', $client->profit_code ?? '') }}" placeholder="Auto-populates from SIC code" maxlength="20" style="font-family:var(--font-mono); font-weight:600;">
                <div class="ncm-hint">Income/profit source code for tax returns (auto-filled from SIC)</div>
            </div>
            <div class="sl-field">
                <label>SARS Loss Code</label>
                <input type="text" name="loss_code" id="nxLossCode" value="{{ old('loss_code', $client->loss_code ?? '') }}" placeholder="Auto-populates from SIC code" maxlength="20" style="font-family:var(--font-mono); font-weight:600;">
                <div class="ncm-hint">Loss source code for tax returns (auto-filled from SIC)</div>
            </div>
            <div></div>
        </div>
        <div class="ncm-form-grid cols-3" style="margin-top:16px;">
            <div class="sl-field">
                <label>Financial Year End</label>
                <select name="financial_year_end" class="ncm-select2" data-placeholder="-- Select Month --">
                    <option value=""></option>
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ old('financial_year_end', $client->financial_year_end ?? '') == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sl-field">
                <label>Date Incorporated</label>
                <input type="text" class="ncm-datepicker" placeholder="Select date..." readonly
                    data-target="date_incorporated"
                    value="{{ old('date_incorporated', isset($client) && $client->date_incorporated ? $client->date_incorporated->format('j M Y') : '') }}">
                <input type="hidden" name="date_incorporated" id="date_incorporated"
                    value="{{ old('date_incorporated', isset($client) && $client->date_incorporated ? $client->date_incorporated->format('Y-m-d') : '') }}">
            </div>
            <div class="sl-field">
                <label>Date Commenced Trading</label>
                <input type="text" class="ncm-datepicker" placeholder="Select date..." readonly
                    data-target="date_commenced_trading"
                    value="{{ old('date_commenced_trading', isset($client) && $client->date_commenced_trading ? $client->date_commenced_trading->format('j M Y') : '') }}">
                <input type="hidden" name="date_commenced_trading" id="date_commenced_trading"
                    value="{{ old('date_commenced_trading', isset($client) && $client->date_commenced_trading ? $client->date_commenced_trading->format('Y-m-d') : '') }}">
            </div>
        </div>
    </div>

    {{-- Tax & Registration Numbers --}}
    <div class="ncm-form-section">
        <div class="ncm-form-title cyan"><i class="fas fa-file-invoice-dollar"></i> Tax & Registration Numbers</div>
        <div class="ncm-form-grid cols-3">
            <div class="sl-field">
                <label>Income Tax Number</label>
                <input type="text" name="tax_number" value="{{ old('tax_number', $client->tax_number ?? '') }}" placeholder="e.g. 9012345678" maxlength="20" style="font-family:var(--font-mono); font-size:18px; font-weight:700; color:var(--accent-green); letter-spacing:2px;">
                <div class="ncm-hint">SARS income tax reference number</div>
            </div>
            <div class="sl-field">
                <label>VAT Number</label>
                <input type="text" name="vat_number" value="{{ old('vat_number', $client->vat_number ?? '') }}" placeholder="e.g. 4012345678" maxlength="20" style="font-family:var(--font-mono); font-size:18px; font-weight:700; color:var(--accent-blue); letter-spacing:2px;">
                <div class="ncm-hint">SARS VAT registration number</div>
            </div>
            <div class="sl-field">
                <label>VAT Registered</label>
                <select name="is_vat_registered" style="font-size:15px; font-weight:600;">
                    <option value="0" {{ old('is_vat_registered', $client->is_vat_registered ?? 0) == 0 ? 'selected' : '' }}>No - Do not calculate VAT</option>
                    <option value="1" {{ old('is_vat_registered', $client->is_vat_registered ?? 0) == 1 ? 'selected' : '' }}>Yes - Calculate VAT</option>
                </select>
                <div class="ncm-hint">Controls whether VAT is applied during bank allocations</div>
            </div>
            <div class="sl-field">
                <label>PAYE Number</label>
                <input type="text" name="paye_number" value="{{ old('paye_number', $client->paye_number ?? '') }}" placeholder="e.g. 7012345678" maxlength="20" style="font-family:var(--font-mono); font-size:16px; font-weight:700; color:var(--accent-amber); letter-spacing:1px;">
                <div class="ncm-hint">SARS PAYE reference</div>
            </div>
        </div>
        <div class="ncm-form-grid cols-3" style="margin-top:16px;">
            <div class="sl-field">
                <label>SDL Number</label>
                <input type="text" name="sdl_number" value="{{ old('sdl_number', $client->sdl_number ?? '') }}" placeholder="SDL number" maxlength="20" style="font-family:var(--font-mono); font-weight:600;">
                <div class="ncm-hint">Skills Development Levy</div>
            </div>
            <div class="sl-field">
                <label>UIF Number</label>
                <input type="text" name="uif_number" value="{{ old('uif_number', $client->uif_number ?? '') }}" placeholder="UIF number" maxlength="20" style="font-family:var(--font-mono); font-weight:600;">
                <div class="ncm-hint">Unemployment Insurance Fund</div>
            </div>
            <div class="sl-field">
                <label>COIDA Number</label>
                <input type="text" name="coida_number" value="{{ old('coida_number', $client->coida_number ?? '') }}" placeholder="WCA/COIDA number" maxlength="20" style="font-family:var(--font-mono); font-weight:600;">
                <div class="ncm-hint">Compensation for Occupational Injuries</div>
            </div>
        </div>
    </div>

    {{-- Practice & Team Assignment --}}
    <div class="ncm-form-section">
        <div class="ncm-form-title blue"><i class="fas fa-briefcase"></i> Practice & Team Assignment</div>
        <div class="ncm-form-grid cols-2">
            <div class="sl-field">
                <label>Practice</label>
                <select name="practice_id" id="nxPracticeSelect" class="ncm-select2" data-placeholder="-- Select Practice --" onchange="nxPracticeChanged()">
                    <option value=""></option>
                    @foreach($practices as $p)
                        <option value="{{ $p->id }}" {{ old('practice_id', $client->practice_id ?? '') == $p->id ? 'selected' : '' }}>{{ $p->practice_name }}{{ $p->trading_name && $p->trading_name !== $p->practice_name ? ' (t/a '.$p->trading_name.')' : '' }}</option>
                    @endforeach
                </select>
                <div class="ncm-hint">Which practice manages this client</div>
            </div>
            <div></div>
        </div>

        <div id="nxClerkAssignments" style="margin-top:20px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                <label style="font-size:14px; font-weight:700; color:var(--text-primary);"><i class="fas fa-users-cog" style="color:var(--accent-blue); margin-right:6px;"></i> Clerk Assignments by Department</label>
                <button type="button" onclick="nxAddAssignment()" class="neon-btn neon-btn-cyan" style="padding:6px 14px; font-size:11px;"><i class="fas fa-plus"></i> Add Row</button>
            </div>
            <div id="nxAssignRows">
                @if(isset($clerkAssignments) && count($clerkAssignments) > 0)
                    @foreach($clerkAssignments as $idx => $a)
                    <div class="nx-assign-row" style="display:grid; grid-template-columns:1fr 1fr 60px 40px; gap:10px; align-items:end; margin-bottom:8px; padding:10px 14px; background:var(--bg-raised); border:1px solid var(--border-subtle); border-radius:6px;">
                        <div class="sl-field" style="margin:0;">
                            <label style="font-size:11px;">Department</label>
                            <select name="assign_department[]" style="font-size:13px; padding:7px 10px;">
                                <option value="">-- Select --</option>
                                @foreach(['All','Bookkeeping','Tax','Payroll','Audit','Secretarial','Advisory','Other'] as $dept)
                                    <option value="{{ $dept }}" {{ $a->department == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sl-field" style="margin:0;">
                            <label style="font-size:11px;">Clerk</label>
                            <select name="assign_clerk[]" class="nx-clerk-dropdown" style="font-size:13px; padding:7px 10px;">
                                <option value="">-- Select Clerk --</option>
                                @foreach($allClerks as $c)
                                    <option value="{{ $c->id }}" data-practices="{{ implode(',', $practiceClerkLinks->where('clerk_id', $c->id)->pluck('practice_id')->toArray()) }}" {{ $a->clerk_id == $c->id ? 'selected' : '' }}>{{ $c->first_name }} {{ $c->last_name }}{{ $c->designation ? ' ('.$c->designation.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="display:flex; align-items:center; gap:6px; padding-bottom:2px;">
                            <input type="checkbox" name="assign_primary[{{ $idx }}]" value="1" {{ $a->is_primary ? 'checked' : '' }} style="accent-color:var(--accent-green); width:16px; height:16px;">
                            <span style="font-size:11px; color:var(--text-muted);">Primary</span>
                        </div>
                        <button type="button" onclick="this.closest('.nx-assign-row').remove()" style="background:none; border:none; color:var(--accent-red); cursor:pointer; font-size:16px; padding-bottom:2px;"><i class="fas fa-trash"></i></button>
                    </div>
                    @endforeach
                @endif
            </div>
            <div id="nxNoAssign" style="text-align:center; padding:20px; color:var(--text-muted); font-size:13px; {{ isset($clerkAssignments) && count($clerkAssignments) > 0 ? 'display:none;' : '' }}">
                <i class="fas fa-info-circle" style="margin-right:4px;"></i> Select a practice first, then add clerk assignments per department
            </div>
        </div>
    </div>

    {{-- Description --}}
    <div class="ncm-form-section">
        <div class="ncm-form-title amber"><i class="fas fa-align-left"></i> Description</div>
        <div class="sl-field">
            <label>Notes / Description</label>
            <textarea name="description" rows="4" placeholder="Any notes about this client...">{{ old('description', $client->description ?? '') }}</textarea>
        </div>
    </div>

    <div style="display:flex; gap:12px; justify-content:flex-end;">
        @if(isset($client))
            <a href="{{ route('nexcore.clients.show.dashboard', $client->id) }}" class="neon-btn neon-btn-red"><i class="fas fa-times"></i> Cancel</a>
        @else
            <a href="{{ route('nexcore.clients.index') }}" class="neon-btn neon-btn-red"><i class="fas fa-times"></i> Cancel</a>
        @endif
        <button type="submit" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-check"></i> {{ isset($client) ? 'Update Client' : 'Register Client' }}</button>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.ncm-select2').select2({
        allowClear: true,
        width: '100%',
        placeholder: function() { return $(this).data('placeholder'); }
    });

    $('.ncm-datepicker').each(function() {
        var input = this;
        var targetId = $(this).data('target');
        flatpickr(input, {
            dateFormat: 'j M Y',
            altInput: false,
            disableMobile: true,
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    var d = selectedDates[0];
                    var yyyy = d.getFullYear();
                    var mm = String(d.getMonth() + 1).padStart(2, '0');
                    var dd = String(d.getDate()).padStart(2, '0');
                    document.getElementById(targetId).value = yyyy + '-' + mm + '-' + dd;
                } else {
                    document.getElementById(targetId).value = '';
                }
            }
        });
    });
});

var nxSicLookup = {
    @foreach($sicCodes as $sic)
    '{{ $sic->id }}': { profit: '{{ $sic->profit_code ?? '' }}', loss: '{{ $sic->loss_code ?? '' }}' },
    @endforeach
};

$('#nxSicCodeSelect').on('change', function() {
    var sicId = $(this).val();
    if (sicId && nxSicLookup[sicId]) {
        var pc = nxSicLookup[sicId].profit;
        var lc = nxSicLookup[sicId].loss;
        if (pc) document.getElementById('nxProfitCode').value = pc;
        if (lc) document.getElementById('nxLossCode').value = lc;
    }
});

var nxAssignCounter = {{ isset($clerkAssignments) ? count($clerkAssignments) : 0 }};
var nxAllClerks = [
    @foreach($allClerks as $c)
    { id: {{ $c->id }}, name: '{{ addslashes($c->first_name . ' ' . $c->last_name) }}', designation: '{{ addslashes($c->designation ?? '') }}', practices: '{{ implode(',', $practiceClerkLinks->where('clerk_id', $c->id)->pluck('practice_id')->toArray()) }}' },
    @endforeach
];

function nxPracticeChanged() {
    var pid = document.getElementById('nxPracticeSelect').value;
    var dropdowns = document.querySelectorAll('.nx-clerk-dropdown');
    dropdowns.forEach(function(sel) {
        var current = sel.value;
        sel.innerHTML = '<option value="">-- Select Clerk --</option>';
        nxAllClerks.forEach(function(c) {
            if (!pid || c.practices.split(',').indexOf(pid) !== -1) {
                var opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.name + (c.designation ? ' (' + c.designation + ')' : '');
                if (String(c.id) === String(current)) opt.selected = true;
                sel.appendChild(opt);
            }
        });
    });
}

function nxAddAssignment() {
    var pid = document.getElementById('nxPracticeSelect').value;
    if (!pid) {
        alert('Please select a practice first.');
        return;
    }
    var container = document.getElementById('nxAssignRows');
    document.getElementById('nxNoAssign').style.display = 'none';

    var row = document.createElement('div');
    row.className = 'nx-assign-row';
    row.style.cssText = 'display:grid; grid-template-columns:1fr 1fr 60px 40px; gap:10px; align-items:end; margin-bottom:8px; padding:10px 14px; background:var(--bg-raised); border:1px solid var(--border-subtle); border-radius:6px;';

    var deptOpts = '<option value="">-- Select --</option>';
    ['All','Bookkeeping','Tax','Payroll','Audit','Secretarial','Advisory','Other'].forEach(function(d) {
        deptOpts += '<option value="' + d + '">' + d + '</option>';
    });

    var clerkOpts = '<option value="">-- Select Clerk --</option>';
    nxAllClerks.forEach(function(c) {
        if (c.practices.split(',').indexOf(pid) !== -1) {
            clerkOpts += '<option value="' + c.id + '">' + c.name + (c.designation ? ' (' + c.designation + ')' : '') + '</option>';
        }
    });

    row.innerHTML = '<div class="sl-field" style="margin:0;"><label style="font-size:11px;">Department</label><select name="assign_department[]" style="font-size:13px; padding:7px 10px;">' + deptOpts + '</select></div>' +
        '<div class="sl-field" style="margin:0;"><label style="font-size:11px;">Clerk</label><select name="assign_clerk[]" class="nx-clerk-dropdown" style="font-size:13px; padding:7px 10px;">' + clerkOpts + '</select></div>' +
        '<div style="display:flex; align-items:center; gap:6px; padding-bottom:2px;"><input type="checkbox" name="assign_primary[' + nxAssignCounter + ']" value="1" style="accent-color:var(--accent-green); width:16px; height:16px;"><span style="font-size:11px; color:var(--text-muted);">Primary</span></div>' +
        '<button type="button" onclick="this.closest(\'.nx-assign-row\').remove()" style="background:none; border:none; color:var(--accent-red); cursor:pointer; font-size:16px; padding-bottom:2px;"><i class="fas fa-trash"></i></button>';

    container.appendChild(row);
    nxAssignCounter++;
}

function previewLogo(input) {
    var wrap = document.getElementById('logoPreviewWrap');
    var img = document.getElementById('logoPreviewImg');
    var label = document.getElementById('logoFileName');
    if (input.files && input.files[0]) {
        var file = input.files[0];
        if (file.size > 2 * 1024 * 1024) {
            alert('File too large. Maximum 2MB allowed.');
            input.value = '';
            wrap.style.display = 'none';
            label.textContent = 'Click to upload company logo (PNG, JPG, SVG, WEBP - max 2MB)';
            return;
        }
        var reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            wrap.style.display = 'flex';
        };
        reader.readAsDataURL(file);
        label.textContent = file.name;
    } else {
        wrap.style.display = 'none';
        label.textContent = 'Click to upload company logo (PNG, JPG, SVG, WEBP - max 2MB)';
    }
}

function previewWatermark(input) {
    var wrap = document.getElementById('watermarkPreviewWrap');
    var img = document.getElementById('watermarkPreviewImg');
    var label = document.getElementById('watermarkFileName');
    if (input.files && input.files[0]) {
        var file = input.files[0];
        if (file.size > 2 * 1024 * 1024) {
            alert('File too large. Maximum 2MB allowed.');
            input.value = '';
            wrap.style.display = 'none';
            label.textContent = 'Click to upload watermark logo (PNG, JPG, SVG, WEBP - max 2MB)';
            return;
        }
        var reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            wrap.style.display = 'flex';
        };
        reader.readAsDataURL(file);
        label.textContent = file.name;
    } else {
        wrap.style.display = 'none';
        label.textContent = 'Click to upload watermark logo (PNG, JPG, SVG, WEBP - max 2MB)';
    }
}
</script>
@endsection