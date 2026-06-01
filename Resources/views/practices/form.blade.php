@extends('nexcore_client_manager::layouts.app')

@section('title', $practice ? 'Edit Practice' : 'New Practice')
@section('page_heading', $practice ? 'EDIT PRACTICE' : 'NEW PRACTICE')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <h1 class="sl-page-title">{{ $practice ? 'Edit Practice' : 'Create New Practice' }}</h1>
        <span class="sl-page-subtitle">{{ $practice ? 'Update practice information' : 'Register a new accounting practice' }}</span>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.practices.index') }}" class="neon-btn neon-btn-ghost"><i class="fas fa-arrow-left"></i> Back to Practices</a>
        </div>
    </div>
</div>

@if($errors->any())
<div class="sl-card sl-animate d2 sl-mb-md" style="border:1px solid var(--accent-red); background:rgba(239,68,68,0.06);">
    <div style="padding:12px 16px; display:flex; align-items:start; gap:12px;">
        <i class="fas fa-exclamation-triangle" style="color:var(--accent-red); margin-top:3px;"></i>
        <div>
            @foreach($errors->all() as $error)
                <div style="color:var(--accent-red); font-size:13px; margin-bottom:4px;">{{ $error }}</div>
            @endforeach
        </div>
    </div>
</div>
@endif

<form method="POST" action="{{ $practice ? route('nexcore.clients.practices.update', $practice->id) : route('nexcore.clients.practices.store') }}" enctype="multipart/form-data">
    @csrf
    @if($practice) @method('PUT') @endif

    <div class="sl-card sl-animate d2 sl-mb-md">
        <div class="sl-card-header">
            <div class="sl-card-title"><i class="fas fa-briefcase"></i> Practice Identity</div>
        </div>
        <div style="padding:20px;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-secondary); margin-bottom:8px;"><i class="fas fa-image" style="margin-right:6px; color:var(--accent-green);"></i> Practice Logo</label>
                    @if($practice && $practice->practice_logo)
                    <div style="display:flex; align-items:center; gap:16px; margin-bottom:12px; padding:14px; background:var(--bg-raised); border-radius:var(--radius-sm); border:1px solid var(--border-subtle);">
                        <div style="width:72px; height:72px; border-radius:12px; background:rgba(255,255,255,0.06); border:1px solid var(--border-subtle); display:flex; align-items:center; justify-content:center; overflow:hidden;">
                            <img src="{{ asset($practice->practice_logo) }}" alt="Logo" style="width:100%; height:100%; object-fit:contain; padding:4px;">
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:600; font-size:15px;">{{ $practice->practice_name }}</div>
                            <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">Current logo</div>
                        </div>
                    </div>
                    @endif
                    <div>
                        <input type="file" name="practice_logo" id="practiceLogoInput" accept="image/png,image/jpeg,image/svg+xml,image/webp" style="display:none;" onchange="previewPracticeLogo(this)">
                        <label for="practiceLogoInput" style="display:flex; align-items:center; justify-content:center; gap:10px; padding:16px; border:2px dashed var(--border-default); border-radius:var(--radius-sm); cursor:pointer; color:var(--text-muted); font-size:13px; transition:all 0.2s;" onmouseover="this.style.borderColor='var(--accent-green)';this.style.color='var(--accent-green)'" onmouseout="this.style.borderColor='var(--border-default)';this.style.color='var(--text-muted)'">
                            <i class="fas fa-cloud-upload-alt" style="font-size:18px;"></i>
                            <span id="logoFileName">Click to upload practice logo</span>
                        </label>
                        <div id="logoPreviewWrap" style="display:none; margin-top:10px; padding:10px; background:var(--bg-raised); border-radius:var(--radius-sm); border:1px solid var(--border-subtle); align-items:center;">
                            <img id="logoPreviewImg" src="" alt="Preview" style="max-width:72px; max-height:72px; object-fit:contain; border-radius:8px;">
                            <span style="margin-left:12px; font-size:13px; color:var(--accent-green);"><i class="fas fa-check-circle"></i> New logo selected</span>
                        </div>
                    </div>
                    <div style="font-size:11px; color:var(--text-muted); margin-top:6px;">Square logos work best (e.g. 200x200px)</div>
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-secondary); margin-bottom:8px;"><i class="fas fa-stamp" style="margin-right:6px; color:var(--accent-amber);"></i> Watermark Logo</label>
                    @if($practice && $practice->watermark_logo)
                    <div style="display:flex; align-items:center; gap:16px; margin-bottom:12px; padding:14px; background:var(--bg-raised); border-radius:var(--radius-sm); border:1px solid var(--border-subtle);">
                        <div style="width:72px; height:72px; border-radius:12px; background:rgba(255,255,255,0.06); border:1px solid var(--border-subtle); display:flex; align-items:center; justify-content:center; overflow:hidden;">
                            <img src="{{ asset($practice->watermark_logo) }}" alt="Watermark" style="width:100%; height:100%; object-fit:contain; padding:4px;">
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:600; font-size:15px;">Current Watermark</div>
                            <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">Used on report pages</div>
                        </div>
                    </div>
                    @endif
                    <div>
                        <input type="file" name="watermark_logo" id="watermarkLogoInput" accept="image/png,image/jpeg,image/svg+xml,image/webp" style="display:none;" onchange="previewWatermarkLogo(this)">
                        <label for="watermarkLogoInput" style="display:flex; align-items:center; justify-content:center; gap:10px; padding:16px; border:2px dashed var(--border-default); border-radius:var(--radius-sm); cursor:pointer; color:var(--text-muted); font-size:13px; transition:all 0.2s;" onmouseover="this.style.borderColor='var(--accent-amber)';this.style.color='var(--accent-amber)'" onmouseout="this.style.borderColor='var(--border-default)';this.style.color='var(--text-muted)'">
                            <i class="fas fa-cloud-upload-alt" style="font-size:18px;"></i>
                            <span id="watermarkFileName">Click to upload watermark logo</span>
                        </label>
                        <div id="watermarkPreviewWrap" style="display:none; margin-top:10px; padding:10px; background:var(--bg-raised); border-radius:var(--radius-sm); border:1px solid var(--border-subtle); align-items:center;">
                            <img id="watermarkPreviewImg" src="" alt="Preview" style="max-width:72px; max-height:72px; object-fit:contain; border-radius:8px;">
                            <span style="margin-left:12px; font-size:13px; color:var(--accent-amber);"><i class="fas fa-check-circle"></i> New watermark selected</span>
                        </div>
                    </div>
                    <div style="font-size:11px; color:var(--text-muted); margin-top:6px;">Square image recommended. Used as watermark on report pages.</div>
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="sl-field">
                    <label>Practice Name <span style="color:var(--accent-red);">*</span></label>
                    <input type="text" name="practice_name" value="{{ old('practice_name', $practice->practice_name ?? '') }}" required style="font-size:15px; font-weight:600;">
                </div>
                <div class="sl-field">
                    <label>Trading Name</label>
                    <input type="text" name="trading_name" value="{{ old('trading_name', $practice->trading_name ?? '') }}" style="font-size:15px; font-weight:600;">
                </div>
                <div class="sl-field">
                    <label>Practice Number</label>
                    <input type="text" name="practice_number" value="{{ old('practice_number', $practice->practice_number ?? '') }}" placeholder="e.g. PR-2018/04521" style="font-size:15px; font-weight:600;">
                </div>
                <div class="sl-field">
                    <label>Registration Number</label>
                    <input type="text" name="registration_number" value="{{ old('registration_number', $practice->registration_number ?? '') }}" placeholder="CIPC reg number" style="font-size:15px; font-weight:600;">
                </div>
            </div>
        </div>
    </div>

    <div class="sl-card sl-animate d3 sl-mb-md">
        <div class="sl-card-header">
            <div class="sl-card-title"><i class="fas fa-graduation-cap"></i> Professional Registration</div>
        </div>
        <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <div class="sl-field">
                <label>Professional Body</label>
                <select name="professional_body" style="font-size:15px; font-weight:600;">
                    <option value="">-- Select --</option>
                    <option value="SAICA" {{ old('professional_body', $practice->professional_body ?? '') == 'SAICA' ? 'selected' : '' }}>SAICA</option>
                    <option value="SAIPA" {{ old('professional_body', $practice->professional_body ?? '') == 'SAIPA' ? 'selected' : '' }}>SAIPA</option>
                    <option value="IRBA" {{ old('professional_body', $practice->professional_body ?? '') == 'IRBA' ? 'selected' : '' }}>IRBA</option>
                    <option value="CIMA" {{ old('professional_body', $practice->professional_body ?? '') == 'CIMA' ? 'selected' : '' }}>CIMA</option>
                    <option value="ACCA" {{ old('professional_body', $practice->professional_body ?? '') == 'ACCA' ? 'selected' : '' }}>ACCA</option>
                    <option value="ICBA" {{ old('professional_body', $practice->professional_body ?? '') == 'ICBA' ? 'selected' : '' }}>ICBA</option>
                </select>
            </div>
            <div class="sl-field">
                <label>Professional Body Number</label>
                <input type="text" name="professional_body_number" value="{{ old('professional_body_number', $practice->professional_body_number ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>BEE Level</label>
                <select name="bbbee_level" style="font-size:15px; font-weight:600;">
                    <option value="">-- Select --</option>
                    @for($i = 1; $i <= 8; $i++)
                        <option value="Level {{ $i }}" {{ old('bbbee_level', $practice->bbbee_level ?? '') == 'Level '.$i ? 'selected' : '' }}>Level {{ $i }}</option>
                    @endfor
                    <option value="Non-Compliant" {{ old('bbbee_level', $practice->bbbee_level ?? '') == 'Non-Compliant' ? 'selected' : '' }}>Non-Compliant</option>
                </select>
            </div>
            <div class="sl-field">
                <label>BEE Certificate Expiry</label>
                <input type="date" name="bbbee_certificate_expiry" value="{{ old('bbbee_certificate_expiry', $practice->bbbee_certificate_expiry ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
        </div>
    </div>

    <div class="sl-card sl-animate d3 sl-mb-md">
        <div class="sl-card-header">
            <div class="sl-card-title"><i class="fas fa-file-invoice-dollar"></i> Tax & VAT</div>
        </div>
        <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px;">
            <div class="sl-field">
                <label>Tax Number</label>
                <input type="text" name="tax_number" value="{{ old('tax_number', $practice->tax_number ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>VAT Number</label>
                <input type="text" name="vat_number" value="{{ old('vat_number', $practice->vat_number ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>VAT Registered</label>
                <select name="is_vat_registered" style="font-size:15px; font-weight:600;">
                    <option value="0" {{ old('is_vat_registered', $practice->is_vat_registered ?? 0) == 0 ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('is_vat_registered', $practice->is_vat_registered ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                </select>
            </div>
        </div>
    </div>

    <div class="sl-card sl-animate d4 sl-mb-md">
        <div class="sl-card-header">
            <div class="sl-card-title"><i class="fas fa-phone"></i> Contact Details</div>
        </div>
        <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px;">
            <div class="sl-field">
                <label>Phone</label>
                <input type="text" name="phone_number" value="{{ old('phone_number', $practice->phone_number ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Mobile</label>
                <input type="text" name="mobile_number" value="{{ old('mobile_number', $practice->mobile_number ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Fax</label>
                <input type="text" name="fax_number" value="{{ old('fax_number', $practice->fax_number ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $practice->email ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Website</label>
                <input type="text" name="website" value="{{ old('website', $practice->website ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
        </div>
    </div>

    <div class="sl-card sl-animate d4 sl-mb-md">
        <div class="sl-card-header">
            <div class="sl-card-title"><i class="fas fa-map-marker-alt"></i> Physical Address</div>
        </div>
        <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <div class="sl-field">
                <label>Address Line 1</label>
                <input type="text" name="physical_address_line1" value="{{ old('physical_address_line1', $practice->physical_address_line1 ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Address Line 2</label>
                <input type="text" name="physical_address_line2" value="{{ old('physical_address_line2', $practice->physical_address_line2 ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>City</label>
                <input type="text" name="physical_city" value="{{ old('physical_city', $practice->physical_city ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Province</label>
                <select name="physical_province" style="font-size:15px; font-weight:600;">
                    <option value="">-- Select --</option>
                    @foreach(['Eastern Cape','Free State','Gauteng','KwaZulu-Natal','Limpopo','Mpumalanga','Northern Cape','North West','Western Cape'] as $prov)
                        <option value="{{ $prov }}" {{ old('physical_province', $practice->physical_province ?? '') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sl-field">
                <label>Postal Code</label>
                <input type="text" name="physical_postal_code" value="{{ old('physical_postal_code', $practice->physical_postal_code ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Country</label>
                <input type="text" name="physical_country" value="{{ old('physical_country', $practice->physical_country ?? 'South Africa') }}" style="font-size:15px; font-weight:600;">
            </div>
        </div>
    </div>

    <div class="sl-card sl-animate d5 sl-mb-md">
        <div class="sl-card-header">
            <div class="sl-card-title"><i class="fas fa-envelope"></i> Postal Address</div>
        </div>
        <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <div class="sl-field">
                <label>Address Line 1</label>
                <input type="text" name="postal_address_line1" value="{{ old('postal_address_line1', $practice->postal_address_line1 ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Address Line 2</label>
                <input type="text" name="postal_address_line2" value="{{ old('postal_address_line2', $practice->postal_address_line2 ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>City</label>
                <input type="text" name="postal_city" value="{{ old('postal_city', $practice->postal_city ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Province</label>
                <select name="postal_province" style="font-size:15px; font-weight:600;">
                    <option value="">-- Select --</option>
                    @foreach(['Eastern Cape','Free State','Gauteng','KwaZulu-Natal','Limpopo','Mpumalanga','Northern Cape','North West','Western Cape'] as $prov)
                        <option value="{{ $prov }}" {{ old('postal_province', $practice->postal_province ?? '') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sl-field">
                <label>Postal Code</label>
                <input type="text" name="postal_postal_code" value="{{ old('postal_postal_code', $practice->postal_postal_code ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Country</label>
                <input type="text" name="postal_country" value="{{ old('postal_country', $practice->postal_country ?? 'South Africa') }}" style="font-size:15px; font-weight:600;">
            </div>
        </div>
    </div>

    <div class="sl-card sl-animate d5 sl-mb-md">
        <div class="sl-card-header">
            <div class="sl-card-title"><i class="fas fa-landmark"></i> Banking Details</div>
        </div>
        <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px;">
            <div class="sl-field">
                <label>Bank Name</label>
                <select name="bank_name" style="font-size:15px; font-weight:600;">
                    <option value="">-- Select --</option>
                    @foreach(['ABSA','Capitec Business','First National Bank','Investec','Nedbank','Standard Bank','TymeBank Business','African Bank','Bidvest Bank','Discovery Bank','Grindrod Bank','Mercantile Bank','Sasfin Bank'] as $bank)
                        <option value="{{ $bank }}" {{ old('bank_name', $practice->bank_name ?? '') == $bank ? 'selected' : '' }}>{{ $bank }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sl-field">
                <label>Branch</label>
                <input type="text" name="bank_branch" value="{{ old('bank_branch', $practice->bank_branch ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Branch Code</label>
                <input type="text" name="bank_branch_code" value="{{ old('bank_branch_code', $practice->bank_branch_code ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Account Number</label>
                <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $practice->bank_account_number ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Account Type</label>
                <select name="bank_account_type" style="font-size:15px; font-weight:600;">
                    <option value="">-- Select --</option>
                    @foreach(['Cheque','Savings','Transmission','Current','Call Deposit'] as $type)
                        <option value="{{ $type }}" {{ old('bank_account_type', $practice->bank_account_type ?? '') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="sl-card sl-animate d6 sl-mb-md">
        <div class="sl-card-header">
            <div class="sl-card-title"><i class="fas fa-user-tie"></i> Practice Principal / Managing Partner</div>
        </div>
        <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <div class="sl-field">
                <label>Full Name</label>
                <input type="text" name="principal_name" value="{{ old('principal_name', $practice->principal_name ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Designation</label>
                <input type="text" name="principal_designation" value="{{ old('principal_designation', $practice->principal_designation ?? '') }}" placeholder="e.g. CA(SA), AGA(SA), RA" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Email</label>
                <input type="email" name="principal_email" value="{{ old('principal_email', $practice->principal_email ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Mobile</label>
                <input type="text" name="principal_mobile" value="{{ old('principal_mobile', $practice->principal_mobile ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
        </div>
    </div>

    <div class="sl-animate d6" style="display:flex; gap:12px; justify-content:flex-end; padding-bottom:40px;">
        <a href="{{ route('nexcore.clients.practices.index') }}" class="neon-btn neon-btn-ghost"><i class="fas fa-times"></i> Cancel</a>
        <button type="submit" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-save"></i> {{ $practice ? 'Update Practice' : 'Create Practice' }}</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
function previewPracticeLogo(input) {
    var wrap = document.getElementById('logoPreviewWrap');
    var img = document.getElementById('logoPreviewImg');
    var name = document.getElementById('logoFileName');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) { img.src = e.target.result; wrap.style.display = 'flex'; };
        reader.readAsDataURL(input.files[0]);
        name.textContent = input.files[0].name;
    }
}
function previewWatermarkLogo(input) {
    var wrap = document.getElementById('watermarkPreviewWrap');
    var img = document.getElementById('watermarkPreviewImg');
    var name = document.getElementById('watermarkFileName');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) { img.src = e.target.result; wrap.style.display = 'flex'; };
        reader.readAsDataURL(input.files[0]);
        name.textContent = input.files[0].name;
    }
}
</script>
@endpush