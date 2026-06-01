@extends('nexcore_client_manager::layouts.accounting')

@section('title', (isset($account) ? 'Edit' : 'New') . ' Account - ' . $client->company_name)
@section('page_heading', isset($account) ? 'EDIT ACCOUNT' : 'ADD ACCOUNT')

@php $isEdit = isset($account); @endphp

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(245,158,11,0.15), rgba(245,158,11,0.05)); border:1px solid rgba(245,158,11,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-sitemap" style="color:#f59e0b; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">{{ $isEdit ? 'Edit Account' : 'Add Account' }}</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.show.accounting.accounts', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-arrow-left"></i> Back to Accounts</a>
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

<form method="POST"
      action="{{ $isEdit ? route('nexcore.clients.show.accounting.accounts.update', [$client->id, $account->id]) : route('nexcore.clients.show.accounting.accounts.store', $client->id) }}"
      id="accountForm">
    @csrf
    @if($isEdit) @method('PUT') @endif

    {{-- Account Level & Code --}}
    <div class="sl-card sl-animate d2">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#f59e0b;"><i class="fas fa-layer-group"></i> Account Level & Code</div>
            @if($isEdit)
                <span style="font-size:11px; color:var(--text-muted); background:rgba(245,158,11,0.1); padding:4px 10px; border-radius:6px;"><i class="fas fa-lock"></i> Read-only</span>
            @endif
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-bottom:24px; {{ $isEdit ? 'opacity:0.6; pointer-events:none;' : '' }}">
                @php $currentLevel = old('account_level', $account->account_level ?? ''); @endphp
                <label class="acc-level-option {{ $currentLevel == '1' ? 'selected' : '' }}" onclick="selectLevel(1, this)">
                    <input type="radio" name="account_level" value="1" {{ $currentLevel == '1' ? 'checked' : '' }} {{ $isEdit ? '' : 'required' }} style="display:none;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div style="width:36px; height:36px; border-radius:8px; background:rgba(245,158,11,0.15); display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-folder" style="color:#f59e0b; font-size:14px;"></i>
                        </div>
                        <div>
                            <div style="font-weight:700; font-size:14px; color:var(--text-primary);">Main Account</div>
                            <div style="font-size:11px; color:var(--text-muted);">Level 1 - Top-level group</div>
                        </div>
                    </div>
                </label>
                <label class="acc-level-option {{ $currentLevel == '2' ? 'selected' : '' }}" onclick="selectLevel(2, this)">
                    <input type="radio" name="account_level" value="2" {{ $currentLevel == '2' ? 'checked' : '' }} style="display:none;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div style="width:36px; height:36px; border-radius:8px; background:rgba(59,130,246,0.15); display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-folder-open" style="color:var(--accent-blue); font-size:14px;"></i>
                        </div>
                        <div>
                            <div style="font-weight:700; font-size:14px; color:var(--text-primary);">Sub Account</div>
                            <div style="font-size:11px; color:var(--text-muted);">Level 2 - Sub-group</div>
                        </div>
                    </div>
                </label>
                <label class="acc-level-option {{ $currentLevel == '3' ? 'selected' : '' }}" onclick="selectLevel(3, this)">
                    <input type="radio" name="account_level" value="3" {{ $currentLevel == '3' ? 'checked' : '' }} style="display:none;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div style="width:36px; height:36px; border-radius:8px; background:rgba(16,185,129,0.15); display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-file-alt" style="color:var(--accent-green); font-size:14px;"></i>
                        </div>
                        <div>
                            <div style="font-weight:700; font-size:14px; color:var(--text-primary);">Detail Account</div>
                            <div style="font-size:11px; color:var(--text-muted);">Level 3 - Transaction-level</div>
                        </div>
                    </div>
                </label>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr 1fr 2fr; gap:16px; align-items:end;">
                <div class="sl-field">
                    <label>Segment 1 <span style="color:var(--accent-red);">*</span></label>
                    <input type="text" name="segment1" id="seg1" value="{{ old('segment1', $account->segment1 ?? '') }}" {{ $isEdit ? 'readonly' : 'required' }} placeholder="e.g. 1000" maxlength="20" style="font-family:var(--font-mono); text-align:center; {{ $isEdit ? 'opacity:0.6;' : '' }}" oninput="buildAccountCode()">
                </div>
                <div class="sl-field" id="seg2Wrap">
                    <label>Segment 2</label>
                    <input type="text" name="segment2" id="seg2" value="{{ old('segment2', $account->segment2 ?? '') }}" {{ $isEdit ? 'readonly' : '' }} placeholder="e.g. 1000" maxlength="20" style="font-family:var(--font-mono); text-align:center; {{ $isEdit ? 'opacity:0.6;' : '' }}" oninput="buildAccountCode()">
                </div>
                <div class="sl-field" id="seg3Wrap">
                    <label>Segment 3</label>
                    <input type="text" name="segment3" id="seg3" value="{{ old('segment3', $account->segment3 ?? '') }}" {{ $isEdit ? 'readonly' : '' }} placeholder="e.g. 1000" maxlength="20" style="font-family:var(--font-mono); text-align:center; {{ $isEdit ? 'opacity:0.6;' : '' }}" oninput="buildAccountCode()">
                </div>
                <div class="sl-field">
                    <label>Account Code (auto-generated)</label>
                    <input type="text" name="account_code" id="accountCode" value="{{ old('account_code', $account->account_code ?? '') }}" readonly style="font-family:var(--font-mono); font-weight:700; color:#f59e0b; background:rgba(245,158,11,0.05); border-color:rgba(245,158,11,0.3); font-size:16px; letter-spacing:1px;">
                </div>
            </div>
        </div>
    </div>

    {{-- Account Details --}}
    <div class="sl-card sl-animate d3">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#f59e0b;"><i class="fas fa-info-circle"></i> Account Details</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Account Name <span style="color:var(--accent-red);">*</span></label>
                    <input type="text" name="account_name" value="{{ old('account_name', $account->account_name ?? '') }}" required placeholder="e.g. Bank - FNB Current">
                </div>
                <div class="sl-field">
                    <label>Account Type @if(!$isEdit)<span style="color:var(--accent-red);">*</span>@else <i class="fas fa-lock" style="font-size:10px; color:var(--text-muted);"></i>@endif</label>
                    @if($isEdit)
                        <input type="text" value="{{ $accountTypes[$account->account_type] ?? ucfirst($account->account_type) }}" readonly style="opacity:0.6;">
                        <input type="hidden" name="account_type" value="{{ $account->account_type }}">
                    @else
                        <select name="account_type" required class="sl-select">
                            <option value="">-- Select Type --</option>
                            @foreach($accountTypes as $key => $label)
                                <option value="{{ $key }}" {{ old('account_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-top:20px;">
                <div class="sl-field">
                    <label>Normal Balance @if(!$isEdit)<span style="color:var(--accent-red);">*</span>@else <i class="fas fa-lock" style="font-size:10px; color:var(--text-muted);"></i>@endif</label>
                    @if($isEdit)
                        <input type="text" value="{{ ucfirst($account->normal_balance) }}" readonly style="opacity:0.6;">
                        <input type="hidden" name="normal_balance" value="{{ $account->normal_balance }}">
                    @else
                        <select name="normal_balance" required class="sl-select">
                            <option value="">-- Select --</option>
                            @foreach($normalBalances as $key => $label)
                                <option value="{{ $key }}" {{ old('normal_balance') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="sl-field">
                    <label>VAT Type</label>
                    <select name="vat_type" class="sl-select">
                        @foreach($vatTypes as $key => $label)
                            <option value="{{ $key }}" {{ old('vat_type', $account->vat_type ?? 'none') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>Parent Account @if($isEdit) <i class="fas fa-lock" style="font-size:10px; color:var(--text-muted);"></i>@endif</label>
                    @if($isEdit)
                        @php $parentName = $account->parent_id ? ($parentAccounts->firstWhere('id', $account->parent_id)->account_name ?? 'N/A') : 'None'; @endphp
                        <input type="text" value="{{ $parentName }}" readonly style="opacity:0.6;">
                        <input type="hidden" name="parent_id" value="{{ $account->parent_id }}">
                    @else
                        <select name="parent_id" class="sl-select">
                            <option value="">-- None (Top Level) --</option>
                            @foreach($parentAccounts as $pa)
                                <option value="{{ $pa->id }}" {{ old('parent_id') == $pa->id ? 'selected' : '' }}>
                                    {{ $pa->account_code }} - {{ $pa->account_name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 120px 120px; gap:20px; margin-top:20px;">
                <div class="sl-field">
                    <label>Description</label>
                    <textarea name="description" rows="2" placeholder="Optional description..." style="width:100%; background:var(--bg-raised); color:var(--text-primary); border:1px solid var(--border-default); border-radius:var(--radius-sm); padding:10px 14px; font-family:var(--font-body); font-size:15px; resize:vertical;">{{ old('description', $account->description ?? '') }}</textarea>
                </div>
                <div class="sl-field">
                    <label>Header? @if($isEdit) <i class="fas fa-lock" style="font-size:10px; color:var(--text-muted);"></i>@endif</label>
                    @if($isEdit)
                        <input type="text" value="{{ $account->is_header ? 'Yes' : 'No' }}" readonly style="opacity:0.6;">
                        <input type="hidden" name="is_header" value="{{ $account->is_header ? '1' : '0' }}">
                    @else
                        <select name="is_header" class="sl-select">
                            <option value="1" {{ old('is_header', false) ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('is_header', false) ? '' : 'selected' }}>No</option>
                        </select>
                    @endif
                </div>
                <div class="sl-field">
                    <label>Active</label>
                    <select name="is_active" class="sl-select">
                        <option value="1" {{ old('is_active', $account->is_active ?? true) ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('is_active', $account->is_active ?? true) ? '' : 'selected' }}>No</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="sl-animate d4" style="display:flex; justify-content:flex-end; gap:12px; margin-top:20px;">
        <a href="{{ route('nexcore.clients.show.accounting.accounts', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-times"></i> Cancel</a>
        <button type="submit" class="neon-btn neon-btn-amber neon-pulse"><i class="fas fa-save"></i> {{ $isEdit ? 'Update Account' : 'Save Account' }}</button>
    </div>
</form>
@endsection

@push('scripts')
<style>
    .acc-level-option { display:block; padding:16px; border:2px solid var(--border-subtle); border-radius:var(--radius-md); cursor:pointer; transition:all 0.2s ease; background:var(--bg-raised); }
    .acc-level-option:hover { border-color:var(--border-default); background:rgba(245,158,11,0.03); }
    .acc-level-option.selected { border-color:#f59e0b; background:rgba(245,158,11,0.06); box-shadow:0 0 12px rgba(245,158,11,0.1); }
    .sl-select { background:var(--bg-raised); color:var(--text-primary); border:1px solid var(--border-default); border-radius:var(--radius-sm); padding:10px 14px; font-size:15px; font-family:var(--font-body); width:100%; }
</style>
<script>
function selectLevel(level, el) {
    @if(!$isEdit)
    document.querySelectorAll('.acc-level-option').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
    var s2 = document.getElementById('seg2Wrap');
    var s3 = document.getElementById('seg3Wrap');
    var seg2 = document.getElementById('seg2');
    var seg3 = document.getElementById('seg3');
    if (level === 1) {
        s2.style.opacity = '0.3'; s2.style.pointerEvents = 'none';
        s3.style.opacity = '0.3'; s3.style.pointerEvents = 'none';
        seg2.value = ''; seg3.value = '';
    } else if (level === 2) {
        s2.style.opacity = '1'; s2.style.pointerEvents = 'auto';
        s3.style.opacity = '0.3'; s3.style.pointerEvents = 'none';
        seg3.value = '';
    } else {
        s2.style.opacity = '1'; s2.style.pointerEvents = 'auto';
        s3.style.opacity = '1'; s3.style.pointerEvents = 'auto';
    }
    buildAccountCode();
    @endif
}

function buildAccountCode() {
    var s1 = document.getElementById('seg1').value || '';
    var s2 = document.getElementById('seg2').value || '';
    var s3 = document.getElementById('seg3').value || '';
    var code = s1 + '/' + s2 + '/' + s3;
    document.getElementById('accountCode').value = code;
}

document.addEventListener('DOMContentLoaded', function() {
    @if(!$isEdit)
    var level = document.querySelector('input[name="account_level"]:checked');
    if (level) {
        selectLevel(parseInt(level.value), level.closest('.acc-level-option'));
    } else {
        document.getElementById('seg2Wrap').style.opacity = '0.3';
        document.getElementById('seg2Wrap').style.pointerEvents = 'none';
        document.getElementById('seg3Wrap').style.opacity = '0.3';
        document.getElementById('seg3Wrap').style.pointerEvents = 'none';
    }
    @endif
    buildAccountCode();
});
</script>
@endpush
