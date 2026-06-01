@extends('nexcore_client_manager::layouts.accounting')

@section('title', 'Accounting Dashboard - ' . $client->company_name)
@section('page_heading', 'ACCOUNTING DASHBOARD')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(245,158,11,0.15), rgba(245,158,11,0.05)); border:1px solid rgba(245,158,11,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-tachometer-alt" style="color:#f59e0b; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Accounting Dashboard</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Chart of Accounts Stats --}}
<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card" style="border-color:rgba(245,158,11,0.4);">
        <div class="sl-stat-label">Total Accounts</div>
        <div class="sl-stat-value" style="color:#f59e0b;">{{ $totalAccounts }}</div>
        <div class="sl-stat-meta">Main: {{ $mainAccounts }} | Sub: {{ $subAccounts }} | Detail: {{ $detailAccounts }}</div>
    </div>
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Revenue</div>
        <div class="sl-stat-value" style="color:var(--accent-green); font-size:20px;">R {{ number_format($revenueTotal, 2) }}</div>
        <div class="sl-stat-meta">Total income</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(239,68,68,0.4);">
        <div class="sl-stat-label">Expenses</div>
        <div class="sl-stat-value" style="color:var(--accent-red); font-size:20px;">R {{ number_format($expenseTotal, 2) }}</div>
        <div class="sl-stat-meta">Total expenses</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba({{ $netProfit >= 0 ? '5,150,105' : '239,68,68' }},0.4);">
        <div class="sl-stat-label">Net Profit</div>
        <div class="sl-stat-value" style="color:{{ $netProfit >= 0 ? 'var(--accent-green)' : 'var(--accent-red)' }}; font-size:20px;">R {{ number_format($netProfit, 2) }}</div>
        <div class="sl-stat-meta">Revenue - Expenses</div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;" class="sl-animate d3">
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">Total Assets</div>
        <div class="sl-stat-value" style="color:var(--accent-blue); font-size:20px;">R {{ number_format($totalAssets, 2) }}</div>
        <div class="sl-stat-meta">Asset accounts</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(168,85,247,0.4);">
        <div class="sl-stat-label">Total Liabilities</div>
        <div class="sl-stat-value" style="color:#a855f7; font-size:20px;">R {{ number_format($totalLiabilities, 2) }}</div>
        <div class="sl-stat-meta">Liability accounts</div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:20px;" class="sl-animate d4">
    {{-- Quick Actions --}}
    <div class="sl-card">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#f59e0b;"><i class="fas fa-bolt"></i> Quick Actions</div>
        </div>
        <div style="padding:24px; display:flex; flex-direction:column; gap:12px;">
            <a href="{{ route('nexcore.clients.show.accounting.accounts', $client->id) }}" class="neon-btn neon-btn-amber" style="justify-content:center;"><i class="fas fa-sitemap"></i> Chart of Accounts ({{ $totalAccounts }})</a>
            <a href="{{ route('nexcore.clients.show.accounting.journals.create', $client->id) }}" class="neon-btn neon-btn-green" style="justify-content:center;"><i class="fas fa-plus"></i> New Journal Entry</a>
            <a href="{{ route('nexcore.clients.show.accounting.trial-balance', $client->id) }}" class="neon-btn neon-btn-blue" style="justify-content:center;"><i class="fas fa-balance-scale"></i> View Trial Balance</a>
            <a href="{{ route('nexcore.clients.show.accounting.income-statement', $client->id) }}" class="neon-btn neon-btn-cyan" style="justify-content:center;"><i class="fas fa-file-invoice-dollar"></i> Income Statement</a>
        </div>
    </div>

    {{-- Recent Journals --}}
    <div class="sl-card">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#f59e0b;"><i class="fas fa-book"></i> Recent Journal Entries</div>
        </div>
        <div class="sl-table-wrap">
            <table class="sl-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Date</th>
                        <th>Narration</th>
                        <th>Amount</th>
                        <th class="center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentJournals as $j)
                    <tr>
                        <td style="font-family:var(--font-mono); font-size:13px; color:#f59e0b;">{{ $j->journal_number }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">{{ $j->journal_date->format('j M Y') }}</td>
                        <td style="font-size:13px; color:var(--text-primary);">{{ \Illuminate\Support\Str::limit($j->description, 35) }}</td>
                        <td style="font-family:var(--font-mono); font-size:13px; font-weight:600; color:var(--accent-green);">R {{ number_format($j->total_debit, 2) }}</td>
                        <td class="center">
                            @if($j->status === 'posted') <span class="sl-tag sl-tag-green">Posted</span>
                            @elseif($j->status === 'reversed') <span class="sl-tag sl-tag-red">Reversed</span>
                            @else <span class="sl-tag sl-tag-amber">Draft</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center; padding:30px; color:var(--text-muted); font-size:13px;">No journal entries yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
