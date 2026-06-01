@extends('nexcore_client_manager::layouts.accounting')

@section('title', 'Setup COA - ' . $client->company_name)
@section('page_heading', 'SETUP COA')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(168,85,247,0.15), rgba(168,85,247,0.05)); border:1px solid rgba(168,85,247,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-magic" style="color:#a855f7; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Setup Chart of Accounts | {{ $client->company_name }}</h1>
                <span class="sl-page-subtitle">Seed a full chart of accounts from an industry template</span>
            </div>
        </div>
    </div>
</div>

{{-- Warning Banner --}}
<div class="sl-animate d1" style="padding:14px 20px; background:rgba(245,158,11,0.06); border:1px solid rgba(245,158,11,0.25); border-radius:10px; color:#f59e0b; font-size:13px; font-weight:600; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
    <i class="fas fa-shield-alt" style="font-size:16px;"></i>
    <span>Safety: This will ONLY work for clients with an empty chart of accounts. Clients that already have accounts are protected and cannot be overwritten.</span>
</div>

<form method="POST" action="{{ route('nexcore.clients.show.accounting.setup-coa.seed', $client->id) }}" id="seedForm">
    @csrf

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
        {{-- Left: Client Selection --}}
        <div class="sl-card sl-animate d2">
            <div class="sl-card-header">
                <div class="sl-card-title" style="color:#a855f7;"><i class="fas fa-building"></i> Select Company</div>
            </div>
            <div style="padding:24px;">
                <label class="coa-label">NexCore Client <span style="color:var(--accent-red);">*</span></label>
                <select name="target_client_id" id="clientSelect" class="coa-select" required>
                    <option value="">-- Select a Client --</option>
                    @foreach($allClients as $c)
                        <option value="{{ $c->id }}"
                            data-accounts="{{ $c->account_count }}"
                            data-code="{{ $c->client_code }}"
                            {{ $c->id == $client->id ? 'selected' : '' }}>
                            {{ $c->company_name }} ({{ $c->client_code }})
                        </option>
                    @endforeach
                </select>
                @error('target_client_id') <span class="coa-error">{{ $message }}</span> @enderror

                {{-- Client Status Indicator --}}
                <div id="clientStatus" style="margin-top:16px; display:none;">
                    <div id="clientReady" class="coa-status-box coa-status-ready" style="display:none;">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong id="readyClientName"></strong>
                            <span>No existing accounts - ready for template setup</span>
                        </div>
                    </div>
                    <div id="clientBlocked" class="coa-status-box coa-status-blocked" style="display:none;">
                        <i class="fas fa-lock"></i>
                        <div>
                            <strong id="blockedClientName"></strong>
                            <span>Already has <strong id="blockedCount"></strong> accounts - cannot seed from template</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Template Selection --}}
        <div class="sl-card sl-animate d3">
            <div class="sl-card-header">
                <div class="sl-card-title" style="color:#a855f7;"><i class="fas fa-layer-group"></i> Select Template</div>
            </div>
            <div style="padding:24px;">
                <label class="coa-label">Industry Template <span style="color:var(--accent-red);">*</span></label>
                <select name="template_id" id="templateSelect" class="coa-select" required>
                    <option value="">-- Select a Template --</option>
                    @foreach($templates as $t)
                        <option value="{{ $t->id }}"
                            data-industry="{{ $t->industry_type }}"
                            data-desc="{{ $t->description }}"
                            data-total="{{ $t->item_count }}"
                            data-l1="{{ $t->level1_count }}"
                            data-l2="{{ $t->level2_count }}"
                            data-l3="{{ $t->level3_count }}">
                            {{ $t->template_name }}
                        </option>
                    @endforeach
                </select>
                @error('template_id') <span class="coa-error">{{ $message }}</span> @enderror

                <div style="margin-top:20px;">
                    <label class="coa-label">VAT Registration <span style="color:var(--accent-red);">*</span></label>
                    <select name="vat_registered" id="vatSelect" class="coa-select" required>
                        <option value="1">VAT Registered</option>
                        <option value="0">Not VAT Registered</option>
                    </select>
                    <div id="vatHint" style="margin-top:8px; font-size:12px; color:var(--text-muted); line-height:1.5;">
                        <i class="fas fa-info-circle" style="margin-right:4px;"></i>
                        <span id="vatHintText">Accounts will be seeded with VAT types from the template (Standard 15%, Zero Rated, Exempt).</span>
                    </div>
                </div>

                {{-- Template Preview --}}
                <div id="templatePreview" style="margin-top:16px; display:none;">
                    <div class="coa-preview-card">
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                            <span class="coa-industry-badge" id="previewIndustry"></span>
                        </div>
                        <div id="previewDesc" style="font-size:13px; color:var(--text-muted); line-height:1.5; margin-bottom:16px;"></div>
                        <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:10px;">
                            <div class="coa-mini-stat">
                                <span class="coa-mini-val" style="color:#a855f7;" id="previewTotal">0</span>
                                <span class="coa-mini-lbl">Total</span>
                            </div>
                            <div class="coa-mini-stat">
                                <span class="coa-mini-val" style="color:#f59e0b;" id="previewL1">0</span>
                                <span class="coa-mini-lbl">Main</span>
                            </div>
                            <div class="coa-mini-stat">
                                <span class="coa-mini-val" style="color:var(--accent-blue);" id="previewL2">0</span>
                                <span class="coa-mini-lbl">Sub</span>
                            </div>
                            <div class="coa-mini-stat">
                                <span class="coa-mini-val" style="color:var(--accent-green);" id="previewL3">0</span>
                                <span class="coa-mini-lbl">Detail</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Bar --}}
    <div class="sl-card sl-animate d4" style="margin-top:20px;">
        <div style="padding:24px; display:flex; align-items:center; justify-content:space-between;">
            <div style="display:flex; align-items:center; gap:12px;">
                <i class="fas fa-info-circle" style="color:var(--text-muted); font-size:16px;"></i>
                <span style="font-size:13px; color:var(--text-muted);">This will create all accounts from the selected template for the chosen client. This action seeds the full 3-level hierarchy (Main, Sub, Detail).</span>
            </div>
            <div style="display:flex; gap:12px;">
                <a href="{{ route('nexcore.clients.show.accounting.accounts', $client->id) }}" class="neon-btn" style="display:inline-flex; align-items:center; gap:8px; background:var(--bg-raised); color:var(--text-secondary); border:1px solid var(--border-subtle);">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" id="seedBtn" class="neon-btn neon-btn-purple" style="display:inline-flex; align-items:center; gap:8px;" disabled>
                    <i class="fas fa-magic"></i> Seed Chart of Accounts
                </button>
            </div>
        </div>
    </div>
</form>

{{-- Confirmation Modal --}}
<div id="confirmModal" class="coa-modal-overlay" style="display:none;">
    <div class="coa-modal">
        <div style="text-align:center; margin-bottom:20px;">
            <div style="width:56px; height:56px; border-radius:50%; background:rgba(168,85,247,0.1); border:2px solid rgba(168,85,247,0.3); display:inline-flex; align-items:center; justify-content:center; margin-bottom:12px;">
                <i class="fas fa-magic" style="color:#a855f7; font-size:24px;"></i>
            </div>
            <h3 style="font-size:18px; font-weight:700; color:var(--text-primary); margin:0 0 8px;">Confirm Chart of Accounts Setup</h3>
        </div>
        <div style="background:var(--bg-raised); border:1px solid var(--border-subtle); border-radius:10px; padding:16px; margin-bottom:20px;">
            <div style="display:grid; grid-template-columns:auto 1fr; gap:8px 16px; font-size:14px;">
                <span style="color:var(--text-muted); font-weight:600;">Client:</span>
                <span style="color:var(--text-primary); font-weight:700;" id="confirmClient"></span>
                <span style="color:var(--text-muted); font-weight:600;">Template:</span>
                <span style="color:#a855f7; font-weight:700;" id="confirmTemplate"></span>
                <span style="color:var(--text-muted); font-weight:600;">Accounts:</span>
                <span style="color:var(--accent-green); font-weight:700; font-family:var(--font-mono);" id="confirmCount"></span>
                <span style="color:var(--text-muted); font-weight:600;">VAT Status:</span>
                <span style="font-weight:700;" id="confirmVat"></span>
            </div>
        </div>
        <div style="font-size:13px; color:var(--text-muted); text-align:center; margin-bottom:20px;">
            This will create all accounts from the template. Are you sure?
        </div>
        <div style="display:flex; gap:12px; justify-content:center;">
            <button type="button" id="confirmCancel" class="neon-btn" style="display:inline-flex; align-items:center; gap:8px; background:var(--bg-raised); color:var(--text-secondary); border:1px solid var(--border-subtle);">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" id="confirmSeed" class="neon-btn neon-btn-purple" style="display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-check"></i> Yes, Seed Accounts
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .coa-label { display:block; font-size:13px; font-weight:600; color:var(--text-secondary); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px; }
    .coa-select { width:100%; padding:10px 14px; background:var(--bg-raised); border:1px solid var(--border-subtle); border-radius:8px; color:var(--text-primary); font-size:14px; transition:border-color 0.15s ease; }
    .coa-select:focus { outline:none; border-color:#a855f7; box-shadow:0 0 0 2px rgba(168,85,247,0.15); }
    .coa-error { display:block; font-size:12px; color:var(--accent-red); margin-top:4px; }

    .coa-status-box { display:flex; align-items:center; gap:12px; padding:14px 16px; border-radius:10px; font-size:13px; }
    .coa-status-box i { font-size:20px; flex-shrink:0; }
    .coa-status-box strong { display:block; font-size:14px; margin-bottom:2px; }
    .coa-status-box span { color:inherit; opacity:0.85; }
    .coa-status-ready { background:rgba(16,185,129,0.06); border:1px solid rgba(16,185,129,0.25); color:var(--accent-green); }
    .coa-status-blocked { background:rgba(239,68,68,0.06); border:1px solid rgba(239,68,68,0.25); color:var(--accent-red); }

    .coa-preview-card { background:var(--bg-raised); border:1px solid rgba(168,85,247,0.2); border-radius:10px; padding:16px; }
    .coa-industry-badge { display:inline-block; padding:4px 12px; background:rgba(168,85,247,0.1); border:1px solid rgba(168,85,247,0.25); border-radius:20px; font-size:12px; font-weight:700; color:#a855f7; text-transform:uppercase; letter-spacing:0.5px; }

    .coa-mini-stat { text-align:center; background:var(--bg-surface); border:1px solid var(--border-subtle); border-radius:8px; padding:10px 6px; }
    .coa-mini-val { display:block; font-size:20px; font-weight:800; font-family:var(--font-mono); }
    .coa-mini-lbl { display:block; font-size:10px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-top:2px; }

    .coa-modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(4px); display:flex; align-items:center; justify-content:center; z-index:9999; }
    .coa-modal { background:var(--bg-surface); border:1px solid var(--border-subtle); border-radius:16px; padding:32px; max-width:480px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.5); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var clientSelect = document.getElementById('clientSelect');
    var templateSelect = document.getElementById('templateSelect');
    var seedBtn = document.getElementById('seedBtn');
    var seedForm = document.getElementById('seedForm');
    var confirmModal = document.getElementById('confirmModal');

    function updateClientStatus() {
        var opt = clientSelect.options[clientSelect.selectedIndex];
        var statusDiv = document.getElementById('clientStatus');
        var readyDiv = document.getElementById('clientReady');
        var blockedDiv = document.getElementById('clientBlocked');

        if (!opt || !opt.value) {
            statusDiv.style.display = 'none';
            updateSeedBtn();
            return;
        }

        var accounts = parseInt(opt.getAttribute('data-accounts')) || 0;
        var name = opt.text;
        statusDiv.style.display = 'block';

        if (accounts === 0) {
            readyDiv.style.display = 'flex';
            blockedDiv.style.display = 'none';
            document.getElementById('readyClientName').textContent = name;
        } else {
            readyDiv.style.display = 'none';
            blockedDiv.style.display = 'flex';
            document.getElementById('blockedClientName').textContent = name;
            document.getElementById('blockedCount').textContent = accounts;
        }
        updateSeedBtn();
    }

    function updateTemplatePreview() {
        var opt = templateSelect.options[templateSelect.selectedIndex];
        var preview = document.getElementById('templatePreview');

        if (!opt || !opt.value) {
            preview.style.display = 'none';
            updateSeedBtn();
            return;
        }

        document.getElementById('previewIndustry').textContent = opt.getAttribute('data-industry');
        document.getElementById('previewDesc').textContent = opt.getAttribute('data-desc') || 'No description available.';
        document.getElementById('previewTotal').textContent = opt.getAttribute('data-total');
        document.getElementById('previewL1').textContent = opt.getAttribute('data-l1');
        document.getElementById('previewL2').textContent = opt.getAttribute('data-l2');
        document.getElementById('previewL3').textContent = opt.getAttribute('data-l3');
        preview.style.display = 'block';
        updateSeedBtn();
    }

    function updateSeedBtn() {
        var clientOpt = clientSelect.options[clientSelect.selectedIndex];
        var templateOpt = templateSelect.options[templateSelect.selectedIndex];
        var clientValid = clientOpt && clientOpt.value && (parseInt(clientOpt.getAttribute('data-accounts')) || 0) === 0;
        var templateValid = templateOpt && templateOpt.value;
        seedBtn.disabled = !(clientValid && templateValid);
    }

    var vatSelect = document.getElementById('vatSelect');

    function updateVatHint() {
        var val = vatSelect.value;
        var hint = document.getElementById('vatHintText');
        if (val === '1') {
            hint.textContent = 'Accounts will be seeded with VAT types from the template (Standard 15%, Zero Rated, Exempt).';
        } else {
            hint.textContent = 'All accounts will be seeded with VAT type set to "None" since the company is not VAT registered.';
        }
    }

    vatSelect.addEventListener('change', updateVatHint);

    clientSelect.addEventListener('change', updateClientStatus);
    templateSelect.addEventListener('change', updateTemplatePreview);

    // Trigger initial state
    updateClientStatus();
    updateTemplatePreview();

    // Confirmation modal
    seedForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var clientOpt = clientSelect.options[clientSelect.selectedIndex];
        var templateOpt = templateSelect.options[templateSelect.selectedIndex];
        document.getElementById('confirmClient').textContent = clientOpt.text;
        document.getElementById('confirmTemplate').textContent = templateOpt.text;
        document.getElementById('confirmCount').textContent = templateOpt.getAttribute('data-total') + ' accounts';
        var isVat = vatSelect.value === '1';
        var confirmVatEl = document.getElementById('confirmVat');
        confirmVatEl.textContent = isVat ? 'VAT Registered' : 'Not VAT Registered';
        confirmVatEl.style.color = isVat ? 'var(--accent-green)' : '#f59e0b';
        confirmModal.style.display = 'flex';
    });

    document.getElementById('confirmCancel').addEventListener('click', function() {
        confirmModal.style.display = 'none';
    });

    document.getElementById('confirmSeed').addEventListener('click', function() {
        confirmModal.style.display = 'none';
        seedBtn.disabled = true;
        seedBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Seeding...';
        seedForm.removeEventListener('submit', arguments.callee);
        seedForm.submit();
    });

    confirmModal.addEventListener('click', function(e) {
        if (e.target === confirmModal) confirmModal.style.display = 'none';
    });
});
</script>
@endpush
