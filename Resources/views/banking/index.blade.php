@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', 'Banking - ' . $client->company_name)
@section('page_heading', 'BANKING')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(34,197,94,0.15), rgba(34,197,94,0.05)); border:1px solid rgba(34,197,94,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-landmark" style="color:var(--accent-green); font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Banking</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; gap:8px;">
            <a href="{{ route('nexcore.clients.show.banking.create', $client->id) }}" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-plus"></i> New Account</a>
        </div>
    </div>
</div>

<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Total Accounts</div>
        <div class="sl-stat-value" style="color:var(--accent-green);">{{ $accounts->count() }}</div>
        <div class="sl-stat-meta">All bank accounts</div>
    </div>
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">Active</div>
        <div class="sl-stat-value" style="color:var(--accent-blue);">{{ $accounts->where('is_active', true)->count() }}</div>
        <div class="sl-stat-meta">Currently active</div>
    </div>
    <div class="sl-stat-card amber">
        <div class="sl-stat-label">Primary</div>
        <div class="sl-stat-value" style="color:var(--accent-amber);">{{ $accounts->where('is_primary', true)->count() }}</div>
        <div class="sl-stat-meta">Primary account</div>
    </div>
</div>

<div class="sl-card sl-animate d3">
    <div class="sl-card-header">
        <div class="sl-card-title"><i class="fas fa-landmark"></i> Bank Accounts</div>
        <div style="font-size:13px; color:var(--text-muted);">{{ $accounts->count() }} records</div>
    </div>
    <div class="sl-table-wrap">
        <table class="sl-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="width:44px;"></th>
                    <th>Bank</th>
                    <th>Label</th>
                    <th>Account Type</th>
                    <th>Account Name</th>
                    <th>Account Number</th>
                    <th>Branch Code</th>
                    <th class="center">Primary</th>
                    <th class="center">Status</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $idx => $acct)
                <tr>
                    <td style="color:var(--text-muted);">{{ $idx + 1 }}</td>
                    <td>
                        @if($acct->bank && $acct->bank->bank_logo)
                            <img src="{{ asset($acct->bank->bank_logo) }}" style="width:32px; height:32px; object-fit:contain; border-radius:6px; background:rgba(255,255,255,0.08); padding:2px;">
                        @else
                            <div style="width:32px; height:32px; border-radius:6px; background:var(--bg-raised); display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; color:var(--text-muted);"><i class="fas fa-landmark"></i></div>
                        @endif
                    </td>
                    <td style="font-weight:600; color:var(--text-primary);">{{ $acct->bank->name ?? '-' }}</td>
                    <td><span class="sl-tag sl-tag-amber">{{ $acct->account_label ?? '-' }}</span></td>
                    <td style="font-size:13px; color:var(--text-secondary);">{{ $acct->accountType->name ?? '-' }}</td>
                    <td style="font-size:13px; color:var(--text-secondary);">{{ $acct->account_name }}</td>
                    <td><span style="font-family:var(--font-mono); font-size:15px; font-weight:700; color:var(--accent-green); letter-spacing:1px;">{{ $acct->account_number }}</span></td>
                    <td><span style="font-family:var(--font-mono); font-size:13px; color:var(--accent-cyan); font-weight:600;">{{ $acct->branch_code ?? '-' }}</span></td>
                    <td class="center">
                        @if($acct->is_primary) <span style="color:var(--accent-green);"><i class="fas fa-star"></i></span>
                        @else <span style="color:var(--text-muted); opacity:0.3;"><i class="far fa-star"></i></span> @endif
                    </td>
                    <td class="center">@if($acct->is_active) <span class="sl-status-dot pass"></span> @else <span class="sl-status-dot fail"></span> @endif</td>
                    <td class="center">
                        <div style="display:flex; gap:6px; justify-content:center;">
                            <a href="{{ route('nexcore.clients.show.banking.edit', [$client->id, $acct->id]) }}" style="color:var(--accent-blue); font-size:15px;" title="Edit"><i class="fas fa-pen"></i></a>
                            <form method="POST" action="{{ route('nexcore.clients.show.banking.toggle', [$client->id, $acct->id]) }}" style="display:inline;">@csrf
                                <button type="submit" style="background:none; border:none; color:var(--accent-amber); cursor:pointer; font-size:15px;" title="Toggle"><i class="fas fa-power-off"></i></button>
                            </form>
                            <form method="POST" action="{{ route('nexcore.clients.show.banking.destroy', [$client->id, $acct->id]) }}" style="display:inline;" onsubmit="return confirm('Delete this bank account?')">@csrf @method('DELETE')
                                <button type="submit" style="background:none; border:none; color:var(--accent-red); cursor:pointer; font-size:15px;" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="11" style="text-align:center; padding:60px; color:var(--text-muted);">
                    <i class="fas fa-landmark" style="font-size:40px; opacity:0.2; margin-bottom:16px; display:block;"></i>
                    <div style="font-size:16px; font-weight:600; margin-bottom:6px;">No bank accounts yet</div>
                    <div style="font-size:13px;">Click "New Account" to add the first bank account for this client</div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
