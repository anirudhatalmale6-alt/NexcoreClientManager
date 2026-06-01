@extends('nexcore_client_manager::layouts.accounting')

@section('title', ($bankAccount ? 'Edit' : 'Link') . ' Bank Account - ' . $client->company_name)
@section('page_heading', ($bankAccount ? 'EDIT' : 'LINK') . ' BANK ACCOUNT')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(245,158,11,0.15), rgba(245,158,11,0.05)); border:1px solid rgba(245,158,11,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-{{ $bankAccount ? 'pen' : 'plus' }}" style="color:#f59e0b; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">{{ $bankAccount ? 'Edit' : 'Link' }} Bank Account | {{ $client->company_name }}</h1>
                <span class="sl-page-subtitle">{{ $bankAccount ? 'Update bank account details' : 'Link a bank account to a GL asset account' }}</span>
            </div>
        </div>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.show.accounting.bank.accounts', $client->id) }}" style="font-size:13px; color:var(--text-muted); text-decoration:none; font-weight:600;">
                <i class="fas fa-arrow-left"></i> Back to Bank Accounts
            </a>
        </div>
    </div>
</div>

@if(session('error'))
<div class="sl-animate d1" style="padding:12px 20px; background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.25); border-radius:10px; color:var(--accent-red); font-size:14px; font-weight:600; margin-bottom:20px;">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
</div>
@endif

<div class="sl-card sl-animate d2">
    <div class="sl-card-header">
        <div class="sl-card-title" style="color:#f59e0b;"><i class="fas fa-university"></i> Bank Account Details</div>
    </div>

    <form method="POST" action="{{ $bankAccount ? route('nexcore.clients.show.accounting.bank.accounts.update', [$client->id, $bankAccount->id]) : route('nexcore.clients.show.accounting.bank.accounts.store', $client->id) }}" style="padding:24px;">
        @csrf
        @if($bankAccount) @method('PUT') @endif

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; max-width:800px;">
            {{-- GL Account --}}
            <div style="grid-column:span 2;">
                <label class="bf-label">GL Asset Account <span style="color:var(--accent-red);">*</span></label>
                <select name="account_id" class="bf-select" required>
                    <option value="">Select GL Account...</option>
                    @foreach($bankGlAccounts as $gl)
                        <option value="{{ $gl->id }}" {{ old('account_id', $bankAccount->account_id ?? '') == $gl->id ? 'selected' : '' }}>
                            {{ $gl->account_code }} - {{ $gl->account_name }}
                        </option>
                    @endforeach
                </select>
                @error('account_id') <span class="bf-error">{{ $message }}</span> @enderror
                <span class="bf-hint">This is the GL account that bank transactions will debit/credit</span>
            </div>

            {{-- Bank --}}
            <div>
                <label class="bf-label">Bank <span style="color:var(--accent-red);">*</span></label>
                @if($bankNames->count() > 0)
                <select name="bank_id" id="bankNameSelect" class="bf-select" required>
                    <option value="">Select Bank...</option>
                    @foreach($bankNames as $bn)
                        <option value="{{ $bn->id }}"
                            data-branch="{{ $bn->branch_code }}"
                            data-swift="{{ $bn->swift_code }}"
                            data-logo="{{ $bn->bank_logo }}"
                            {{ old('bank_id', $bankAccount->bank_id ?? '') == $bn->id ? 'selected' : '' }}>
                            {{ $bn->name }}
                        </option>
                    @endforeach
                </select>
                @else
                <input type="text" name="bank_name" class="bf-input" value="{{ old('bank_name', $bankAccount->bank_name ?? '') }}" required placeholder="e.g. FNB, ABSA, Nedbank...">
                @endif
                @error('bank_id') <span class="bf-error">{{ $message }}</span> @enderror
            </div>

            {{-- Account Number --}}
            <div>
                <label class="bf-label">Account Number <span style="color:var(--accent-red);">*</span></label>
                <input type="text" name="account_number" class="bf-input" value="{{ old('account_number', $bankAccount->account_number ?? '') }}" required placeholder="e.g. 62012345678">
                @error('account_number') <span class="bf-error">{{ $message }}</span> @enderror
            </div>

            {{-- Branch Code (auto-filled from bank selection) --}}
            <div>
                <label class="bf-label">Branch Code <span id="branchAutoLabel" style="font-size:10px; color:var(--accent-green); font-weight:400; text-transform:none; letter-spacing:0; display:none;">(auto-filled)</span></label>
                <input type="text" name="branch_code" id="branchCodeInput" class="bf-input" value="{{ old('branch_code', $bankAccount->branch_code ?? '') }}" placeholder="Auto-filled from bank selection">
            </div>

            {{-- Account Type --}}
            <div>
                <label class="bf-label">Account Type</label>
                @if($accountTypes->count() > 0)
                <select name="account_type" class="bf-select">
                    <option value="">Select Account Type...</option>
                    @foreach($accountTypes as $at)
                        <option value="{{ strtolower($at->name) }}" {{ old('account_type', $bankAccount->account_type ?? '') == strtolower($at->name) ? 'selected' : '' }}>
                            {{ $at->name }}
                        </option>
                    @endforeach
                </select>
                @else
                <select name="account_type" class="bf-select">
                    <option value="current account" {{ old('account_type', $bankAccount->account_type ?? '') == 'current account' ? 'selected' : '' }}>Current Account</option>
                    <option value="savings account" {{ old('account_type', $bankAccount->account_type ?? '') == 'savings account' ? 'selected' : '' }}>Savings Account</option>
                    <option value="transmission account" {{ old('account_type', $bankAccount->account_type ?? '') == 'transmission account' ? 'selected' : '' }}>Transmission Account</option>
                    <option value="credit card account" {{ old('account_type', $bankAccount->account_type ?? '') == 'credit card account' ? 'selected' : '' }}>Credit Card Account</option>
                </select>
                @endif
            </div>
        </div>

        {{-- Opening Balance --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; max-width:800px; margin-top:28px; padding-top:24px; border-top:1px solid var(--border-subtle);">
            <div style="grid-column:span 2; margin-bottom:-4px;">
                <div style="font-size:13px; font-weight:700; color:var(--accent-cyan); text-transform:uppercase; letter-spacing:0.5px;">
                    <i class="fas fa-balance-scale" style="margin-right:6px;"></i> Opening Balance
                </div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">Set the starting balance when linking this account. A GL journal will be auto-created.</div>
            </div>
            <div>
                <label class="bf-label">Opening Balance Date</label>
                <input type="text" name="opening_balance_date" id="obDatePicker" class="bf-input" value="{{ old('opening_balance_date', isset($bankAccount) && $bankAccount->opening_balance_date ? $bankAccount->opening_balance_date->format('Y-m-d') : '') }}" placeholder="Select date..." readonly>
            </div>
            <div>
                <label class="bf-label">Opening Balance Amount</label>
                <input type="number" name="opening_balance_amount" class="bf-input" step="0.01" value="{{ old('opening_balance_amount', $bankAccount->opening_balance_amount ?? '') }}" placeholder="0.00" style="font-family:var(--font-mono); font-size:16px; font-weight:700; color:var(--accent-cyan);">
                <span class="bf-hint">Positive = credit balance (money in bank) &bull; Negative = overdraft</span>
            </div>
        </div>

        {{-- Bank Info Card (shows when bank is selected) --}}
        <div id="bankInfoCard" style="display:none; margin-top:20px; max-width:800px; padding:14px 20px; background:rgba(245,158,11,0.04); border:1px solid rgba(245,158,11,0.2); border-radius:10px;">
            <div style="display:flex; align-items:center; gap:14px;">
                <div id="bankLogoBox" style="width:44px; height:44px; border-radius:8px; background:var(--bg-raised); border:1px solid var(--border-subtle); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
                    <img id="bankLogoImg" src="" alt="" style="max-width:36px; max-height:36px; object-fit:contain; display:none;">
                    <i id="bankLogoIcon" class="fas fa-university" style="color:#f59e0b; font-size:18px;"></i>
                </div>
                <div style="flex:1;">
                    <div style="font-size:14px; font-weight:700; color:var(--text-primary);" id="bankInfoName"></div>
                    <div style="display:flex; gap:20px; margin-top:4px;">
                        <span style="font-size:12px; color:var(--text-muted);">Universal Branch Code: <strong style="color:#f59e0b; font-family:var(--font-mono);" id="bankInfoBranch"></strong></span>
                        <span style="font-size:12px; color:var(--text-muted);" id="bankInfoSwiftWrap">SWIFT: <strong style="color:var(--accent-blue); font-family:var(--font-mono);" id="bankInfoSwift"></strong></span>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top:32px; display:flex; gap:12px;">
            <button type="submit" class="neon-btn neon-btn-amber" style="display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-{{ $bankAccount ? 'save' : 'link' }}"></i> {{ $bankAccount ? 'Update Account' : 'Link Account' }}
            </button>
            <a href="{{ route('nexcore.clients.show.accounting.bank.accounts', $client->id) }}" class="neon-btn" style="display:inline-flex; align-items:center; gap:8px; background:var(--bg-raised); color:var(--text-secondary); border:1px solid var(--border-subtle);">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css" rel="stylesheet">
@endpush

@push('scripts')
<style>
    .bf-label { display:block; font-size:13px; font-weight:600; color:var(--text-secondary); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px; }
    .bf-input, .bf-select { width:100%; padding:10px 14px; background:var(--bg-raised); border:1px solid var(--border-subtle); border-radius:8px; color:var(--text-primary); font-size:14px; transition:border-color 0.15s ease; }
    .bf-input:focus, .bf-select:focus { outline:none; border-color:#f59e0b; box-shadow:0 0 0 2px rgba(245,158,11,0.15); }
    .bf-input::placeholder { color:var(--text-muted); }
    .bf-error { display:block; font-size:12px; color:var(--accent-red); margin-top:4px; }
    .bf-hint { display:block; font-size:12px; color:var(--text-muted); margin-top:4px; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var bankSelect = document.getElementById('bankNameSelect');
    if (!bankSelect) return;

    var branchInput = document.getElementById('branchCodeInput');
    var branchLabel = document.getElementById('branchAutoLabel');
    var infoCard = document.getElementById('bankInfoCard');
    var infoName = document.getElementById('bankInfoName');
    var infoBranch = document.getElementById('bankInfoBranch');
    var infoSwift = document.getElementById('bankInfoSwift');
    var infoSwiftWrap = document.getElementById('bankInfoSwiftWrap');
    var logoImg = document.getElementById('bankLogoImg');
    var logoIcon = document.getElementById('bankLogoIcon');

    function updateBankInfo() {
        var opt = bankSelect.options[bankSelect.selectedIndex];
        if (!opt || !opt.value) {
            infoCard.style.display = 'none';
            branchLabel.style.display = 'none';
            return;
        }

        var branch = opt.getAttribute('data-branch') || '';
        var swift = opt.getAttribute('data-swift') || '';
        var logo = opt.getAttribute('data-logo') || '';

        if (branch) {
            branchInput.value = branch;
            branchLabel.style.display = 'inline';
        }

        infoName.textContent = opt.value;
        infoBranch.textContent = branch || '-';

        if (swift) {
            infoSwift.textContent = swift;
            infoSwiftWrap.style.display = 'inline';
        } else {
            infoSwiftWrap.style.display = 'none';
        }

        if (logo) {
            logoImg.src = '/' + logo;
            logoImg.style.display = 'block';
            logoIcon.style.display = 'none';
        } else {
            logoImg.style.display = 'none';
            logoIcon.style.display = 'block';
        }

        infoCard.style.display = 'block';
    }

    bankSelect.addEventListener('change', updateBankInfo);

    if (bankSelect.value) {
        updateBankInfo();
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var obPicker = document.getElementById('obDatePicker');
    if (obPicker) {
        flatpickr(obPicker, {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'j M Y',
            theme: 'dark',
            allowInput: false
        });
    }
});
</script>
@endpush
