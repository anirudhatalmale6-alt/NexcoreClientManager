@extends('nexcore_client_manager::layouts.accounting')

@section('title', 'Management Report - ' . $client->company_name)
@section('page_heading', 'MANAGEMENT REPORT')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg, rgba(167,139,250,0.2), rgba(59,130,246,0.15)); border:1px solid rgba(167,139,250,0.4); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-briefcase" style="color:#a78bfa; font-size:18px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Management Report | {{ $client->company_name }}</h1>
                <span class="sl-page-subtitle">{{ \Carbon\Carbon::parse($fromDate)->format('j M Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('j M Y') }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; align-items:center; gap:12px;">
            <span style="display:flex; align-items:center; gap:6px; font-size:13px; font-weight:700; color:#a78bfa; background:rgba(167,139,250,0.08); padding:8px 16px; border-radius:8px; border:1px solid rgba(167,139,250,0.25);"><i class="fas fa-layer-group"></i> Combined Report</span>
            <span style="font-size:12px; color:var(--text-muted); font-family:var(--font-mono);">{{ now()->format('j M Y, H:i') }}</span>
        </div>
    </div>
</div>

@include('nexcore_client_manager::accounting.partials.period-filter')

{{-- ═══════════════════════════════════════════════════════════════════
     EXECUTIVE SUMMARY DASHBOARD
     ═══════════════════════════════════════════════════════════════════ --}}
<div class="mp-executive sl-animate d2">
    <div class="mp-exec-header">
        <div class="mp-exec-title"><i class="fas fa-chart-pie"></i> Executive Summary</div>
    </div>
    <div class="mp-exec-grid">
        <div class="mp-exec-card mp-exec-revenue">
            <div class="mp-exec-icon"><i class="fas fa-arrow-trend-up"></i></div>
            <div class="mp-exec-label">Total Revenue</div>
            <div class="mp-exec-value" style="color:var(--accent-green);">R {{ number_format($revenueTotal, 2, '.', ' ') }}</div>
            <div class="mp-exec-sub">{{ $revenueCount }} account{{ $revenueCount != 1 ? 's' : '' }}</div>
        </div>
        <div class="mp-exec-card mp-exec-expenses">
            <div class="mp-exec-icon" style="color:#f59e0b; background:rgba(245,158,11,0.1); border-color:rgba(245,158,11,0.3);"><i class="fas fa-arrow-down"></i></div>
            <div class="mp-exec-label">Total Expenses</div>
            <div class="mp-exec-value" style="color:#f59e0b;">R {{ number_format($cosTotal + $expenseTotal, 2, '.', ' ') }}</div>
            <div class="mp-exec-sub">COS + Operating</div>
        </div>
        <div class="mp-exec-card mp-exec-profit">
            <div class="mp-exec-icon" style="color:{{ $netProfit >= 0 ? 'var(--accent-green)' : 'var(--accent-red)' }}; background:rgba({{ $netProfit >= 0 ? '16,185,129' : '239,68,68' }},0.1); border-color:rgba({{ $netProfit >= 0 ? '16,185,129' : '239,68,68' }},0.3);"><i class="fas fa-{{ $netProfit >= 0 ? 'trophy' : 'exclamation-triangle' }}"></i></div>
            <div class="mp-exec-label">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</div>
            <div class="mp-exec-value" style="color:{{ $netProfit >= 0 ? 'var(--accent-green)' : 'var(--accent-red)' }};">R {{ number_format(abs($netProfit), 2, '.', ' ') }}</div>
            @if($revenueTotal > 0)
            <div class="mp-exec-sub">{{ number_format(($netProfit / $revenueTotal) * 100, 1) }}% margin</div>
            @endif
        </div>
        <div class="mp-exec-card mp-exec-assets">
            <div class="mp-exec-icon" style="color:var(--accent-blue); background:rgba(37,99,235,0.1); border-color:rgba(37,99,235,0.3);"><i class="fas fa-coins"></i></div>
            <div class="mp-exec-label">Total Assets</div>
            <div class="mp-exec-value" style="color:var(--accent-blue);">R {{ number_format($assetTotal, 2, '.', ' ') }}</div>
            <div class="mp-exec-sub">{{ $assetCount }} account{{ $assetCount != 1 ? 's' : '' }}</div>
        </div>
        <div class="mp-exec-card mp-exec-liab">
            <div class="mp-exec-icon" style="color:#a855f7; background:rgba(168,85,247,0.1); border-color:rgba(168,85,247,0.3);"><i class="fas fa-hand-holding-usd"></i></div>
            <div class="mp-exec-label">Total Liabilities</div>
            <div class="mp-exec-value" style="color:#a855f7;">R {{ number_format($liabilityTotal, 2, '.', ' ') }}</div>
            <div class="mp-exec-sub">{{ $liabilityCount }} account{{ $liabilityCount != 1 ? 's' : '' }}</div>
        </div>
        <div class="mp-exec-card mp-exec-equity">
            <div class="mp-exec-icon" style="color:var(--accent-cyan); background:rgba(6,182,212,0.1); border-color:rgba(6,182,212,0.3);"><i class="fas fa-landmark"></i></div>
            <div class="mp-exec-label">Total Equity</div>
            <div class="mp-exec-value" style="color:var(--accent-cyan);">R {{ number_format($equityTotal + $netProfit, 2, '.', ' ') }}</div>
            <div class="mp-exec-sub">{{ $equityCount }} account{{ $equityCount != 1 ? 's' : '' }}</div>
        </div>
    </div>

    @if($revenueTotal > 0)
    <div class="mp-exec-margins">
        <div class="mp-margin-item">
            <span class="mp-margin-label">Gross Margin</span>
            <div class="mp-margin-bar">
                @php $gpPct = max(0, min(100, ($grossProfit / $revenueTotal) * 100)); @endphp
                <div class="mp-margin-fill" style="width:{{ $gpPct }}%; background:linear-gradient(90deg, #a78bfa, #c4b5fd);"></div>
            </div>
            <span class="mp-margin-pct" style="color:#a78bfa;">{{ number_format($gpPct, 1) }}%</span>
        </div>
        <div class="mp-margin-item">
            <span class="mp-margin-label">Net Margin</span>
            <div class="mp-margin-bar">
                @php $npPct = max(0, min(100, ($netProfit / $revenueTotal) * 100)); @endphp
                <div class="mp-margin-fill" style="width:{{ $npPct }}%; background:linear-gradient(90deg, #22c55e, #4ade80);"></div>
            </div>
            <span class="mp-margin-pct" style="color:var(--accent-green);">{{ number_format($npPct, 1) }}%</span>
        </div>
        <div class="mp-margin-item">
            <span class="mp-margin-label">Expense Ratio</span>
            <div class="mp-margin-bar">
                @php $ePct = max(0, min(100, (($cosTotal + $expenseTotal) / $revenueTotal) * 100)); @endphp
                <div class="mp-margin-fill" style="width:{{ $ePct }}%; background:linear-gradient(90deg, #f59e0b, #fbbf24);"></div>
            </div>
            <span class="mp-margin-pct" style="color:#f59e0b;">{{ number_format($ePct, 1) }}%</span>
        </div>
    </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════════════════════
     SECTION NAVIGATION
     ═══════════════════════════════════════════════════════════════════ --}}
<div class="mp-nav sl-animate d2">
    <a href="#mp-income" class="mp-nav-btn mp-nav-active" onclick="mpScrollTo('mp-income', this)">
        <i class="fas fa-file-invoice-dollar"></i> Income Statement
    </a>
    <a href="#mp-balance" class="mp-nav-btn" onclick="mpScrollTo('mp-balance', this)">
        <i class="fas fa-chart-pie"></i> Balance Sheet
    </a>
    <a href="#mp-trial" class="mp-nav-btn" onclick="mpScrollTo('mp-trial', this)">
        <i class="fas fa-balance-scale"></i> Trial Balance
    </a>
    <div class="mp-nav-status">
        @if($isBalanced && $tbIsBalanced)
            <span style="color:var(--accent-green);"><i class="fas fa-check-circle"></i> All Balanced</span>
        @else
            <span style="color:var(--accent-red);"><i class="fas fa-exclamation-triangle"></i> Check Balance</span>
        @endif
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════════════
     1. INCOME STATEMENT
     ═══════════════════════════════════════════════════════════════════ --}}
<div id="mp-income" class="mp-section sl-animate d3">
    <div class="mp-section-header mp-section-cyan">
        <div class="mp-section-icon"><i class="fas fa-file-invoice-dollar"></i></div>
        <div>
            <div class="mp-section-title">Income Statement</div>
            <div class="mp-section-sub">Profit & Loss for {{ \Carbon\Carbon::parse($fromDate)->format('j M Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('j M Y') }}</div>
        </div>
        <div class="mp-section-badge" style="color:{{ $netProfit >= 0 ? 'var(--accent-green)' : 'var(--accent-red)' }}; border-color:rgba({{ $netProfit >= 0 ? '16,185,129' : '239,68,68' }},0.4); background:rgba({{ $netProfit >= 0 ? '16,185,129' : '239,68,68' }},0.06);">
            Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}: R {{ number_format(abs($netProfit), 2, '.', ' ') }}
        </div>
    </div>

    @if($revenueCount > 0 || $cosCount > 0 || $expenseCount > 0)
    <div class="sl-table-wrap">
        <table class="sl-table mp-is-table">
            <thead>
                <tr>
                    <th>Account</th>
                    <th class="right" style="width:180px;">Amount (R)</th>
                    <th class="right" style="width:180px;">Total (R)</th>
                </tr>
            </thead>
            <tbody>
                {{-- REVENUE --}}
                <tr class="mp-is-section-header">
                    <td colspan="3">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div class="mp-is-section-icon" style="color:var(--accent-green); background:rgba(16,185,129,0.1);"><i class="fas fa-arrow-trend-up"></i></div>
                            <span style="font-weight:700; font-size:14px; color:var(--text-primary);">Revenue</span>
                            <span style="font-size:11px; color:var(--text-muted); font-family:var(--font-mono);">({{ $revenueCount }} accounts)</span>
                        </div>
                    </td>
                </tr>

                @forelse($revenueGroups as $mainGroup)
                <tr class="mp-is-main-group">
                    <td colspan="3" style="padding-left:24px; padding-top:14px; padding-bottom:14px;">
                        <span class="mono" style="color:var(--accent-amber); font-weight:700; font-size:18px;">{{ rtrim($mainGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-amber); font-weight:700; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:700; color:var(--accent-amber); font-size:18px; text-transform:uppercase;">{{ $mainGroup['account']->account_name }}</span>
                    </td>
                </tr>
                @foreach($mainGroup['sub_groups'] as $subGroup)
                <tr class="mp-is-sub-group mp-is-collapsible" data-target="mp-is-sub-{{ $subGroup['account']->id }}" onclick="mpToggleSub(this)">
                    <td style="padding-left:48px;">
                        <i class="fas fa-chevron-right mp-is-chevron"></i>
                        <span class="mono" style="color:var(--accent-blue); font-weight:600; font-size:18px;">{{ rtrim($subGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-blue); font-weight:600; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:600; color:var(--accent-blue); font-size:18px; text-transform:uppercase;">{{ $subGroup['account']->account_name }}</span>
                    </td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:600; color:var(--accent-green);">R {{ number_format($subGroup['subtotal'], 2, '.', ' ') }}</td>
                </tr>
                @foreach($subGroup['details'] as $detail)
                <tr class="mp-is-detail-row mp-is-sub-{{ $subGroup['account']->id }}{{ $loop->last ? ' mp-is-detail-last' : '' }}" style="display:none;">
                    <td style="padding-left:62px; font-size:16px; color:var(--text-primary);">{{ $detail['account']->account_name }}</td>
                    <td class="right mono" style="font-size:17px; color:var(--accent-green);">R {{ number_format($detail['amount'], 2, '.', ' ') }}</td>
                    <td></td>
                </tr>
                @endforeach
                @endforeach
                @empty
                <tr class="mp-is-detail-row"><td colspan="3" style="padding-left:42px; color:var(--text-muted); font-style:italic;">No revenue activity</td></tr>
                @endforelse

                <tr class="mp-is-subtotal-row">
                    <td class="right" style="font-weight:700; font-size:17px; color:#a78bfa; text-transform:uppercase; letter-spacing:0.5px;">Total Revenue</td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:700; color:#a78bfa;">R {{ number_format($revenueTotal, 2, '.', ' ') }}</td>
                </tr>

                {{-- COST OF SALES --}}
                <tr class="mp-is-section-header">
                    <td colspan="3">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div class="mp-is-section-icon" style="color:#f59e0b; background:rgba(245,158,11,0.1);"><i class="fas fa-receipt"></i></div>
                            <span style="font-weight:700; font-size:14px; color:var(--text-primary);">Less: Cost of Sales</span>
                            <span style="font-size:11px; color:var(--text-muted); font-family:var(--font-mono);">({{ $cosCount }} accounts)</span>
                        </div>
                    </td>
                </tr>

                @forelse($cosGroups as $mainGroup)
                <tr class="mp-is-main-group">
                    <td colspan="3" style="padding-left:24px; padding-top:14px; padding-bottom:14px;">
                        <span class="mono" style="color:var(--accent-amber); font-weight:700; font-size:18px;">{{ rtrim($mainGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-amber); font-weight:700; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:700; color:var(--accent-amber); font-size:18px; text-transform:uppercase;">{{ $mainGroup['account']->account_name }}</span>
                    </td>
                </tr>
                @foreach($mainGroup['sub_groups'] as $subGroup)
                <tr class="mp-is-sub-group mp-is-collapsible" data-target="mp-is-sub-{{ $subGroup['account']->id }}" onclick="mpToggleSub(this)">
                    <td style="padding-left:48px;">
                        <i class="fas fa-chevron-right mp-is-chevron"></i>
                        <span class="mono" style="color:var(--accent-blue); font-weight:600; font-size:18px;">{{ rtrim($subGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-blue); font-weight:600; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:600; color:var(--accent-blue); font-size:18px; text-transform:uppercase;">{{ $subGroup['account']->account_name }}</span>
                    </td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:600; color:#f59e0b;">R {{ number_format($subGroup['subtotal'], 2, '.', ' ') }}</td>
                </tr>
                @foreach($subGroup['details'] as $detail)
                <tr class="mp-is-detail-row mp-is-sub-{{ $subGroup['account']->id }}{{ $loop->last ? ' mp-is-detail-last' : '' }}" style="display:none;">
                    <td style="padding-left:62px; font-size:16px; color:var(--text-primary);">{{ $detail['account']->account_name }}</td>
                    <td class="right mono" style="font-size:17px; color:#f59e0b;">R {{ number_format($detail['amount'], 2, '.', ' ') }}</td>
                    <td></td>
                </tr>
                @endforeach
                @endforeach
                @empty
                <tr class="mp-is-detail-row"><td colspan="3" style="padding-left:42px; color:var(--text-muted); font-style:italic;">No cost of sales activity</td></tr>
                @endforelse

                <tr class="mp-is-subtotal-row">
                    <td class="right" style="font-weight:700; font-size:17px; color:#a78bfa; text-transform:uppercase; letter-spacing:0.5px;">Total Cost of Sales</td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:700; color:#a78bfa;">R {{ number_format($cosTotal, 2, '.', ' ') }}</td>
                </tr>

                {{-- GROSS PROFIT --}}
                <tr class="mp-is-profit-row">
                    <td class="right" style="font-size:18px; font-weight:800; color:#a78bfa; text-transform:uppercase; letter-spacing:1px;">Gross Profit</td>
                    <td></td>
                    <td class="right mono" style="font-size:18px; font-weight:800; color:#a78bfa;">R {{ number_format(abs($grossProfit), 2, '.', ' ') }}</td>
                </tr>

                {{-- OPERATING EXPENSES --}}
                <tr class="mp-is-section-header">
                    <td colspan="3">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div class="mp-is-section-icon" style="color:#f59e0b; background:rgba(245,158,11,0.1);"><i class="fas fa-arrow-down"></i></div>
                            <span style="font-weight:700; font-size:14px; color:var(--text-primary);">Less: Operating Expenses</span>
                            <span style="font-size:11px; color:var(--text-muted); font-family:var(--font-mono);">({{ $expenseCount }} accounts)</span>
                        </div>
                    </td>
                </tr>

                @forelse($expenseGroups as $mainGroup)
                <tr class="mp-is-main-group">
                    <td colspan="3" style="padding-left:24px; padding-top:14px; padding-bottom:14px;">
                        <span class="mono" style="color:var(--accent-amber); font-weight:700; font-size:18px;">{{ rtrim($mainGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-amber); font-weight:700; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:700; color:var(--accent-amber); font-size:18px; text-transform:uppercase;">{{ $mainGroup['account']->account_name }}</span>
                    </td>
                </tr>
                @foreach($mainGroup['sub_groups'] as $subGroup)
                <tr class="mp-is-sub-group mp-is-collapsible" data-target="mp-is-sub-{{ $subGroup['account']->id }}" onclick="mpToggleSub(this)">
                    <td style="padding-left:48px;">
                        <i class="fas fa-chevron-right mp-is-chevron"></i>
                        <span class="mono" style="color:var(--accent-blue); font-weight:600; font-size:18px;">{{ rtrim($subGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-blue); font-weight:600; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:600; color:var(--accent-blue); font-size:18px; text-transform:uppercase;">{{ $subGroup['account']->account_name }}</span>
                    </td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:600; color:#f59e0b;">R {{ number_format($subGroup['subtotal'], 2, '.', ' ') }}</td>
                </tr>
                @foreach($subGroup['details'] as $detail)
                <tr class="mp-is-detail-row mp-is-sub-{{ $subGroup['account']->id }}{{ $loop->last ? ' mp-is-detail-last' : '' }}" style="display:none;">
                    <td style="padding-left:62px; font-size:16px; color:var(--text-primary);">{{ $detail['account']->account_name }}</td>
                    <td class="right mono" style="font-size:17px; color:#f59e0b;">R {{ number_format($detail['amount'], 2, '.', ' ') }}</td>
                    <td></td>
                </tr>
                @endforeach
                @endforeach
                @empty
                <tr class="mp-is-detail-row"><td colspan="3" style="padding-left:42px; color:var(--text-muted); font-style:italic;">No expense activity</td></tr>
                @endforelse

                <tr class="mp-is-subtotal-row">
                    <td class="right" style="font-weight:700; font-size:17px; color:#a78bfa; text-transform:uppercase; letter-spacing:0.5px;">Total Operating Expenses</td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:700; color:#a78bfa;">R {{ number_format($expenseTotal, 2, '.', ' ') }}</td>
                </tr>

                {{-- NET PROFIT --}}
                <tr class="mp-is-net-profit-row">
                    <td class="right" style="font-size:19px; font-weight:800; text-transform:uppercase; letter-spacing:1.5px; color:#a78bfa;">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</td>
                    <td></td>
                    <td class="right mono" style="font-size:19px; font-weight:800; color:#a78bfa;">R {{ number_format(abs($netProfit), 2, '.', ' ') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align:center; padding:60px 40px; color:var(--text-muted);">
        <i class="fas fa-file-invoice-dollar" style="font-size:40px; opacity:0.15; margin-bottom:16px; display:block;"></i>
        <div style="font-size:16px; font-weight:700; color:var(--text-secondary);">No Income & Expense Activity</div>
        <div style="font-size:13px; margin-top:6px;">Post journal entries to see the income statement here.</div>
    </div>
    @endif
</div>


{{-- ═══════════════════════════════════════════════════════════════════
     2. BALANCE SHEET
     ═══════════════════════════════════════════════════════════════════ --}}
<div id="mp-balance" class="mp-section sl-animate d4">
    <div class="mp-section-header mp-section-purple">
        <div class="mp-section-icon" style="color:#a78bfa; background:rgba(167,139,250,0.15); border-color:rgba(167,139,250,0.4);"><i class="fas fa-chart-pie"></i></div>
        <div>
            <div class="mp-section-title">Balance Sheet</div>
            <div class="mp-section-sub">Statement of Financial Position as at {{ \Carbon\Carbon::parse($toDate)->format('j M Y') }}</div>
        </div>
        <div class="mp-section-badge" style="color:{{ $isBalanced ? 'var(--accent-green)' : 'var(--accent-red)' }}; border-color:rgba({{ $isBalanced ? '16,185,129' : '239,68,68' }},0.4); background:rgba({{ $isBalanced ? '16,185,129' : '239,68,68' }},0.06);">
            <i class="fas fa-{{ $isBalanced ? 'check-circle' : 'exclamation-circle' }}"></i> {{ $isBalanced ? 'Balanced' : 'Out of Balance' }}
        </div>
    </div>

    @if($assetCount > 0 || $liabilityCount > 0 || $equityCount > 0)
    <div class="sl-table-wrap">
        <table class="sl-table mp-bs-table">
            <thead>
                <tr>
                    <th>Account</th>
                    <th class="right" style="width:180px;">Amount (R)</th>
                    <th class="right" style="width:180px;">Total (R)</th>
                </tr>
            </thead>
            <tbody>
                {{-- ASSETS --}}
                <tr class="mp-bs-section-header">
                    <td colspan="3">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div class="mp-bs-section-icon" style="color:var(--accent-blue); background:rgba(37,99,235,0.1);"><i class="fas fa-coins"></i></div>
                            <span style="font-weight:700; font-size:16px; color:var(--text-primary);">Assets</span>
                            <span style="font-size:11px; color:var(--text-muted); font-family:var(--font-mono);">({{ $assetCount }} accounts)</span>
                        </div>
                    </td>
                </tr>

                @forelse($assetGroups as $mainGroup)
                <tr class="mp-bs-main-group">
                    <td colspan="3" style="padding-left:24px; padding-top:14px; padding-bottom:14px;">
                        <span class="mono" style="color:var(--accent-amber); font-weight:700; font-size:18px;">{{ rtrim($mainGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-amber); font-weight:700; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:700; color:var(--accent-amber); font-size:18px; text-transform:uppercase;">{{ $mainGroup['account']->account_name }}</span>
                    </td>
                </tr>
                @foreach($mainGroup['sub_groups'] as $subGroup)
                <tr class="mp-bs-sub-group mp-bs-collapsible" data-target="mp-bs-sub-{{ $subGroup['account']->id }}" onclick="mpToggleBsSub(this)">
                    <td style="padding-left:48px;">
                        <i class="fas fa-chevron-right mp-bs-chevron"></i>
                        <span class="mono" style="color:var(--accent-blue); font-weight:600; font-size:18px;">{{ rtrim($subGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-blue); font-weight:600; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:600; color:var(--accent-blue); font-size:18px; text-transform:uppercase;">{{ $subGroup['account']->account_name }}</span>
                    </td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:600; color:var(--accent-blue);">R {{ number_format($subGroup['subtotal'], 2, '.', ' ') }}</td>
                </tr>
                @foreach($subGroup['details'] as $detail)
                <tr class="mp-bs-detail-row mp-bs-sub-{{ $subGroup['account']->id }}{{ $loop->last ? ' mp-bs-detail-last' : '' }}" style="display:none;">
                    <td style="padding-left:62px; font-size:16px; color:var(--text-primary);">{{ $detail['account']->account_name }}</td>
                    <td class="right mono" style="font-size:17px; color:var(--accent-blue);">R {{ number_format($detail['amount'], 2, '.', ' ') }}</td>
                    <td></td>
                </tr>
                @endforeach
                @endforeach
                @empty
                <tr class="mp-bs-detail-row"><td colspan="3" style="padding-left:42px; color:var(--text-muted); font-style:italic;">No asset activity</td></tr>
                @endforelse

                <tr class="mp-bs-subtotal-row">
                    <td class="right" style="font-weight:700; font-size:17px; color:#a78bfa; text-transform:uppercase; letter-spacing:0.5px;">Total Assets</td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:700; color:#a78bfa;">R {{ number_format($assetTotal, 2, '.', ' ') }}</td>
                </tr>

                {{-- LIABILITIES --}}
                <tr class="mp-bs-section-header">
                    <td colspan="3">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div class="mp-bs-section-icon" style="color:#f59e0b; background:rgba(245,158,11,0.1);"><i class="fas fa-hand-holding-usd"></i></div>
                            <span style="font-weight:700; font-size:16px; color:var(--text-primary);">Liabilities</span>
                            <span style="font-size:11px; color:var(--text-muted); font-family:var(--font-mono);">({{ $liabilityCount }} accounts)</span>
                        </div>
                    </td>
                </tr>

                @forelse($liabilityGroups as $mainGroup)
                <tr class="mp-bs-main-group">
                    <td colspan="3" style="padding-left:24px; padding-top:14px; padding-bottom:14px;">
                        <span class="mono" style="color:var(--accent-amber); font-weight:700; font-size:18px;">{{ rtrim($mainGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-amber); font-weight:700; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:700; color:var(--accent-amber); font-size:18px; text-transform:uppercase;">{{ $mainGroup['account']->account_name }}</span>
                    </td>
                </tr>
                @foreach($mainGroup['sub_groups'] as $subGroup)
                <tr class="mp-bs-sub-group mp-bs-collapsible" data-target="mp-bs-sub-{{ $subGroup['account']->id }}" onclick="mpToggleBsSub(this)">
                    <td style="padding-left:48px;">
                        <i class="fas fa-chevron-right mp-bs-chevron"></i>
                        <span class="mono" style="color:var(--accent-blue); font-weight:600; font-size:18px;">{{ rtrim($subGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-blue); font-weight:600; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:600; color:var(--accent-blue); font-size:18px; text-transform:uppercase;">{{ $subGroup['account']->account_name }}</span>
                    </td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:600; color:#f59e0b;">R {{ number_format($subGroup['subtotal'], 2, '.', ' ') }}</td>
                </tr>
                @foreach($subGroup['details'] as $detail)
                <tr class="mp-bs-detail-row mp-bs-sub-{{ $subGroup['account']->id }}{{ $loop->last ? ' mp-bs-detail-last' : '' }}" style="display:none;">
                    <td style="padding-left:62px; font-size:16px; color:var(--text-primary);">{{ $detail['account']->account_name }}</td>
                    <td class="right mono" style="font-size:17px; color:#f59e0b;">R {{ number_format($detail['amount'], 2, '.', ' ') }}</td>
                    <td></td>
                </tr>
                @endforeach
                @endforeach
                @empty
                <tr class="mp-bs-detail-row"><td colspan="3" style="padding-left:42px; color:var(--text-muted); font-style:italic;">No liability activity</td></tr>
                @endforelse

                <tr class="mp-bs-subtotal-row">
                    <td class="right" style="font-weight:700; font-size:17px; color:#a78bfa; text-transform:uppercase; letter-spacing:0.5px;">Total Liabilities</td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:700; color:#a78bfa;">R {{ number_format($liabilityTotal, 2, '.', ' ') }}</td>
                </tr>

                {{-- EQUITY --}}
                <tr class="mp-bs-section-header">
                    <td colspan="3">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div class="mp-bs-section-icon" style="color:var(--accent-green); background:rgba(16,185,129,0.1);"><i class="fas fa-landmark"></i></div>
                            <span style="font-weight:700; font-size:16px; color:var(--text-primary);">Equity</span>
                            <span style="font-size:11px; color:var(--text-muted); font-family:var(--font-mono);">({{ $equityCount }} accounts)</span>
                        </div>
                    </td>
                </tr>

                @forelse($equityGroups as $mainGroup)
                <tr class="mp-bs-main-group">
                    <td colspan="3" style="padding-left:24px; padding-top:14px; padding-bottom:14px;">
                        <span class="mono" style="color:var(--accent-amber); font-weight:700; font-size:18px;">{{ rtrim($mainGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-amber); font-weight:700; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:700; color:var(--accent-amber); font-size:18px; text-transform:uppercase;">{{ $mainGroup['account']->account_name }}</span>
                    </td>
                </tr>
                @foreach($mainGroup['sub_groups'] as $subGroup)
                <tr class="mp-bs-sub-group mp-bs-collapsible" data-target="mp-bs-sub-{{ $subGroup['account']->id }}" onclick="mpToggleBsSub(this)">
                    <td style="padding-left:48px;">
                        <i class="fas fa-chevron-right mp-bs-chevron"></i>
                        <span class="mono" style="color:var(--accent-blue); font-weight:600; font-size:18px;">{{ rtrim($subGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-blue); font-weight:600; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:600; color:var(--accent-blue); font-size:18px; text-transform:uppercase;">{{ $subGroup['account']->account_name }}</span>
                    </td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:600; color:var(--accent-green);">R {{ number_format($subGroup['subtotal'], 2, '.', ' ') }}</td>
                </tr>
                @foreach($subGroup['details'] as $detail)
                <tr class="mp-bs-detail-row mp-bs-sub-{{ $subGroup['account']->id }}{{ $loop->last ? ' mp-bs-detail-last' : '' }}" style="display:none;">
                    <td style="padding-left:62px; font-size:16px; color:var(--text-primary);">{{ $detail['account']->account_name }}</td>
                    <td class="right mono" style="font-size:17px; color:var(--accent-green);">R {{ number_format($detail['amount'], 2, '.', ' ') }}</td>
                    <td></td>
                </tr>
                @endforeach
                @endforeach
                @empty
                <tr class="mp-bs-detail-row"><td colspan="3" style="padding-left:42px; color:var(--text-muted); font-style:italic;">No equity activity</td></tr>
                @endforelse

                {{-- Net Profit / Retained Earnings --}}
                <tr class="mp-bs-sub-group" style="border-top:1px solid rgba(167,139,250,0.2);">
                    <td style="padding-left:48px;">
                        <i class="fas fa-calculator" style="color:#a78bfa; font-size:11px; margin-right:10px;"></i>
                        <span style="font-weight:600; color:#a78bfa; font-size:18px; text-transform:uppercase;">Net Profit / Retained Earnings</span>
                    </td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:600; color:#a78bfa;">R {{ number_format($netProfit, 2, '.', ' ') }}</td>
                </tr>

                <tr class="mp-bs-subtotal-row">
                    <td class="right" style="font-weight:700; font-size:17px; color:#a78bfa; text-transform:uppercase; letter-spacing:0.5px;">Total Equity</td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:700; color:#a78bfa;">R {{ number_format($equityTotal + $netProfit, 2, '.', ' ') }}</td>
                </tr>

                {{-- TOTAL L+E --}}
                <tr class="mp-bs-grand-row">
                    <td class="right" style="font-size:18px; font-weight:800; color:#a78bfa; text-transform:uppercase; letter-spacing:1px;">Total Liabilities & Equity</td>
                    <td></td>
                    <td class="right mono" style="font-size:18px; font-weight:800; color:#a78bfa;">R {{ number_format($totalLiabilitiesEquity, 2, '.', ' ') }}</td>
                </tr>

                {{-- BALANCE CHECK --}}
                <tr class="mp-bs-balance-row">
                    <td colspan="3" style="text-align:center; padding:16px !important;">
                        @if($isBalanced)
                            <span style="color:var(--accent-green); font-weight:700; font-size:15px;"><i class="fas fa-check-circle"></i> Balance Sheet is in Balance</span>
                        @else
                            <span style="color:var(--accent-red); font-weight:700; font-size:15px;"><i class="fas fa-exclamation-triangle"></i> Out of Balance by R {{ number_format(abs($assetTotal - $totalLiabilitiesEquity), 2, '.', ' ') }}</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align:center; padding:60px 40px; color:var(--text-muted);">
        <i class="fas fa-chart-pie" style="font-size:40px; opacity:0.15; margin-bottom:16px; display:block;"></i>
        <div style="font-size:16px; font-weight:700; color:var(--text-secondary);">No Balance Sheet Activity</div>
        <div style="font-size:13px; margin-top:6px;">Post journal entries to see the balance sheet here.</div>
    </div>
    @endif
</div>


{{-- ═══════════════════════════════════════════════════════════════════
     3. TRIAL BALANCE
     ═══════════════════════════════════════════════════════════════════ --}}
<div id="mp-trial" class="mp-section sl-animate d5">
    <div class="mp-section-header mp-section-amber">
        <div class="mp-section-icon" style="color:#f59e0b; background:rgba(245,158,11,0.15); border-color:rgba(245,158,11,0.4);"><i class="fas fa-balance-scale"></i></div>
        <div>
            <div class="mp-section-title">Trial Balance</div>
            <div class="mp-section-sub">{{ \Carbon\Carbon::parse($fromDate)->format('j M Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('j M Y') }}</div>
        </div>
        <div class="mp-section-badge" style="color:{{ $tbIsBalanced ? 'var(--accent-green)' : 'var(--accent-red)' }}; border-color:rgba({{ $tbIsBalanced ? '16,185,129' : '239,68,68' }},0.4); background:rgba({{ $tbIsBalanced ? '16,185,129' : '239,68,68' }},0.06);">
            <i class="fas fa-{{ $tbIsBalanced ? 'check-circle' : 'exclamation-circle' }}"></i> {{ $tbIsBalanced ? 'Balanced' : 'Out of Balance (R ' . number_format(abs($totalDebits - $totalCredits), 2, '.', ' ') . ')' }}
        </div>
    </div>

    @if($accountCount > 0)
    <div class="sl-table-wrap">
        <table class="sl-table mp-tb-table">
            <thead>
                <tr>
                    <th style="width:140px;">Account Code</th>
                    <th>Account Name</th>
                    <th class="right" style="width:180px;">Debit (R)</th>
                    <th class="right" style="width:180px;">Credit (R)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sortedGroups as $type => $group)
                <tr class="mp-tb-group-header">
                    <td colspan="4">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div class="mp-tb-group-icon" style="color:{{ $typeColors[$type] ?? '#f59e0b' }}; background:{{ $typeColors[$type] ?? '#f59e0b' }}15;">
                                <i class="fas {{ $typeIcons[$type] ?? 'fa-folder' }}"></i>
                            </div>
                            <span style="font-weight:700; font-size:16px; color:var(--text-primary);">{{ $typeLabels[$type] ?? ucfirst($type) }}</span>
                            <span style="font-size:11px; color:var(--text-muted); font-family:var(--font-mono);">({{ count($group['accounts']) }} accounts)</span>
                        </div>
                    </td>
                </tr>

                @foreach($group['accounts'] as $row)
                <tr class="mp-tb-account-row">
                    <td style="font-family:var(--font-mono); font-size:15px; color:#f59e0b; font-weight:600; padding-left:42px;">{{ $row['account']->account_code }}</td>
                    <td style="color:var(--text-primary); font-size:15px; {{ $row['account']->account_level == 2 ? 'font-weight:600;' : 'padding-left:20px;' }}">{{ $row['account']->account_name }}</td>
                    <td class="right mono" style="font-size:15px; text-align:right; color:{{ $row['debit'] > 0 ? 'var(--accent-blue)' : 'var(--text-muted)' }};">
                        {{ $row['debit'] > 0 ? 'R ' . number_format($row['debit'], 2, '.', ' ') : '-' }}
                    </td>
                    <td class="right mono" style="font-size:15px; text-align:right; color:{{ $row['credit'] > 0 ? 'var(--accent-green)' : 'var(--text-muted)' }};">
                        {{ $row['credit'] > 0 ? 'R ' . number_format($row['credit'], 2, '.', ' ') : '-' }}
                    </td>
                </tr>
                @endforeach

                <tr class="mp-tb-subtotal-row">
                    <td></td>
                    <td style="text-align:right; font-size:14px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.5px;">{{ $typeLabels[$type] ?? ucfirst($type) }} Total</td>
                    <td class="right mono" style="font-size:15px; font-weight:700; color:var(--accent-blue); text-align:right;">
                        {{ $group['subtotal_debit'] > 0 ? 'R ' . number_format($group['subtotal_debit'], 2, '.', ' ') : '-' }}
                    </td>
                    <td class="right mono" style="font-size:15px; font-weight:700; color:var(--accent-green); text-align:right;">
                        {{ $group['subtotal_credit'] > 0 ? 'R ' . number_format($group['subtotal_credit'], 2, '.', ' ') : '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="mp-tb-grand-total">
                    <td></td>
                    <td class="right" style="font-size:15px; font-weight:800; color:var(--text-primary); text-transform:uppercase; letter-spacing:1px;">Grand Total</td>
                    <td class="right mono" style="font-size:15px; font-weight:800; color:var(--accent-blue); text-align:right; white-space:nowrap;">R {{ number_format($totalDebits, 2, '.', ' ') }}</td>
                    <td class="right mono" style="font-size:15px; font-weight:800; color:var(--accent-green); text-align:right; white-space:nowrap;">R {{ number_format($totalCredits, 2, '.', ' ') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @else
    <div style="text-align:center; padding:60px 40px; color:var(--text-muted);">
        <i class="fas fa-balance-scale" style="font-size:40px; opacity:0.15; margin-bottom:16px; display:block;"></i>
        <div style="font-size:16px; font-weight:700; color:var(--text-secondary);">No Posted Journals</div>
        <div style="font-size:13px; margin-top:6px;">Post journal entries to see the trial balance here.</div>
    </div>
    @endif
</div>

{{-- Report Footer --}}
<div class="mp-footer sl-animate d5">
    <div style="display:flex; align-items:center; gap:10px;">
        <div class="mp-footer-logo">
            <div style="display:grid; grid-template-columns:8px 8px; gap:2px;">
                <div style="width:8px; height:8px; border-radius:2px; background:#059669;"></div>
                <div style="width:8px; height:8px; border-radius:2px; background:#2563eb;"></div>
                <div style="width:8px; height:8px; border-radius:2px; background:#d97706;"></div>
                <div style="width:8px; height:8px; border-radius:2px; background:#7c3aed;"></div>
            </div>
        </div>
        <span style="font-weight:700; font-size:13px; color:var(--text-secondary);">NexCore</span>
        <span style="color:var(--text-muted); font-size:12px;">Management Report</span>
    </div>
    <div style="font-size:12px; color:var(--text-muted);">
        Generated {{ now()->format('j M Y, H:i') }} | {{ $client->company_name }}
    </div>
</div>
@endsection

@push('scripts')
<style>
    /* ─── Executive Summary ─── */
    .mp-executive {
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 0;
        margin-bottom: 20px;
        overflow: hidden;
    }
    .mp-exec-header {
        padding: 20px 28px 0;
    }
    .mp-exec-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .mp-exec-title i { color: #a78bfa; font-size: 14px; }
    .mp-exec-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 16px;
        padding: 20px 28px;
    }
    .mp-exec-card {
        background: var(--bg-raised);
        border: 1px solid var(--border-subtle);
        border-radius: 12px;
        padding: 18px 16px;
        text-align: center;
        transition: all 0.3s;
    }
    .mp-exec-card:hover {
        border-color: rgba(167,139,250,0.3);
        box-shadow: 0 0 20px rgba(167,139,250,0.08);
        transform: translateY(-2px);
    }
    .mp-exec-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-size: 14px;
        color: var(--accent-green);
        background: rgba(16,185,129,0.1);
        border: 1px solid rgba(16,185,129,0.3);
    }
    .mp-exec-label { font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
    .mp-exec-value { font-size: 17px; font-weight: 800; font-family: var(--font-mono); }
    .mp-exec-sub { font-size: 11px; color: var(--text-muted); margin-top: 4px; }
    .mp-exec-margins {
        padding: 0 28px 24px;
        display: flex;
        gap: 24px;
    }
    .mp-margin-item { flex: 1; }
    .mp-margin-label { font-size: 12px; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 6px; }
    .mp-margin-bar { height: 8px; background: var(--bg-raised); border-radius: 4px; overflow: hidden; flex: 1; }
    .mp-margin-fill { height: 100%; border-radius: 4px; transition: width 0.8s ease; }
    .mp-margin-pct { font-size: 14px; font-weight: 700; font-family: var(--font-mono); display: block; margin-top: 4px; }

    /* ─── Section Navigation ─── */
    .mp-nav {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 20px;
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        margin-bottom: 20px;
        position: sticky;
        top: 0;
        z-index: 20;
    }
    .mp-nav-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        border-radius: 30px;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-muted);
        background: var(--bg-raised);
        border: 1px solid var(--border-subtle);
        text-decoration: none;
        transition: all 0.3s;
        cursor: pointer;
    }
    .mp-nav-btn:hover {
        color: var(--accent-blue);
        border-color: rgba(59,130,246,0.4);
        background: rgba(59,130,246,0.06);
    }
    .mp-nav-btn.mp-nav-active {
        color: var(--accent-green);
        border-color: rgba(34,197,94,0.5);
        background: rgba(34,197,94,0.08);
        box-shadow: 0 0 12px rgba(34,197,94,0.15);
    }
    .mp-nav-btn i { font-size: 12px; }
    .mp-nav-status { margin-left: auto; font-size: 13px; font-weight: 700; }

    /* ─── Section Cards ─── */
    .mp-section {
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .mp-section-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 22px 28px;
        border-bottom: 1px solid var(--border-subtle);
    }
    .mp-section-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: var(--accent-cyan);
        background: rgba(6,182,212,0.15);
        border: 1px solid rgba(6,182,212,0.4);
        flex-shrink: 0;
    }
    .mp-section-title { font-size: 18px; font-weight: 800; color: var(--text-primary); text-transform: uppercase; letter-spacing: 1px; }
    .mp-section-sub { font-size: 13px; color: var(--text-muted); margin-top: 2px; }
    .mp-section-badge {
        margin-left: auto;
        font-size: 13px;
        font-weight: 700;
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* ─── Income Statement Table ─── */
    .mp-is-table { border-collapse: separate; border-spacing: 0; }
    .mp-is-section-header td { padding: 18px 16px 8px !important; border-bottom: none !important; }
    .mp-is-section-icon { width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 12px; }
    .mp-is-main-group td { padding: 14px 16px 6px !important; border-bottom: none !important; }
    .mp-is-sub-group td { padding: 10px 16px !important; border-bottom: 1px solid rgba(255,255,255,0.04) !important; }
    .mp-is-collapsible { cursor: pointer; }
    .mp-is-collapsible:hover td { background: rgba(37,99,235,0.04); }
    .mp-is-chevron { color: var(--accent-blue); font-size: 11px; margin-right: 10px; transition: transform 0.25s ease; display: inline-block; }
    .mp-is-collapsible.mp-is-open .mp-is-chevron { transform: rotate(90deg); }
    .mp-is-detail-row td:first-child { border-left: 3px solid #3b82f6 !important; }
    .mp-is-detail-row td { padding-top: 7px !important; padding-bottom: 7px !important; padding-right: 16px !important; border-bottom: 1px solid rgba(255,255,255,0.03) !important; }
    .mp-is-detail-row:hover td { background: rgba(59,130,246,0.06); }
    .mp-is-detail-last td:first-child { border-bottom-left-radius: 10px; border-bottom: 3px solid #3b82f6 !important; }
    .mp-is-detail-last td { padding-bottom: 14px !important; }
    .mp-is-detail-last + tr td { padding-top: 14px !important; }
    .mp-is-subtotal-row td { padding: 10px 16px !important; border-bottom: 1px solid var(--border-subtle) !important; }
    .mp-is-profit-row td { padding: 16px 16px !important; border-top: 2px solid rgba(167,139,250,0.3) !important; border-bottom: 2px solid rgba(167,139,250,0.3) !important; background: rgba(167,139,250,0.03); }
    .mp-is-net-profit-row td { padding: 20px 16px !important; border-top: 3px double rgba(167,139,250,0.5) !important; background: rgba(167,139,250,0.05); }

    /* ─── Balance Sheet Table ─── */
    .mp-bs-table { border-collapse: separate; border-spacing: 0; }
    .mp-bs-section-header td { padding: 18px 16px 8px !important; border-bottom: none !important; }
    .mp-bs-section-icon { width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 12px; }
    .mp-bs-main-group td { padding: 14px 16px 6px !important; border-bottom: none !important; }
    .mp-bs-sub-group td { padding: 10px 16px !important; border-bottom: 1px solid rgba(255,255,255,0.04) !important; }
    .mp-bs-collapsible { cursor: pointer; }
    .mp-bs-collapsible:hover td { background: rgba(37,99,235,0.04); }
    .mp-bs-chevron { color: var(--accent-blue); font-size: 11px; margin-right: 10px; transition: transform 0.25s ease; display: inline-block; }
    .mp-bs-collapsible.mp-bs-open .mp-bs-chevron { transform: rotate(90deg); }
    .mp-bs-detail-row td:first-child { border-left: 3px solid #3b82f6 !important; }
    .mp-bs-detail-row td { padding-top: 7px !important; padding-bottom: 7px !important; padding-right: 16px !important; border-bottom: 1px solid rgba(255,255,255,0.03) !important; }
    .mp-bs-detail-row:hover td { background: rgba(59,130,246,0.06); }
    .mp-bs-detail-last td:first-child { border-bottom-left-radius: 10px; border-bottom: 3px solid #3b82f6 !important; }
    .mp-bs-detail-last td { padding-bottom: 14px !important; }
    .mp-bs-detail-last + tr td { padding-top: 14px !important; }
    .mp-bs-subtotal-row td { padding: 10px 16px !important; border-bottom: 1px solid var(--border-subtle) !important; }
    .mp-bs-grand-row td { padding: 16px 16px !important; border-top: 2px solid rgba(167,139,250,0.3) !important; border-bottom: 2px solid rgba(167,139,250,0.3) !important; background: rgba(167,139,250,0.03); }
    .mp-bs-balance-row td { border-top: 3px double rgba(167,139,250,0.5) !important; background: rgba(167,139,250,0.05); }

    /* ─── Trial Balance Table ─── */
    .mp-tb-table { border-collapse: separate; border-spacing: 0; }
    .mp-tb-group-header td { padding: 16px 16px 8px !important; border-bottom: none !important; }
    .mp-tb-group-icon { width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 12px; }
    .mp-tb-account-row td { padding: 8px 16px !important; border-bottom: 1px solid rgba(255,255,255,0.03) !important; }
    .mp-tb-account-row:hover td { background: rgba(245,158,11,0.03); }
    .mp-tb-subtotal-row td { padding: 10px 16px !important; border-bottom: 2px solid var(--border-subtle) !important; }
    .mp-tb-grand-total td { padding: 18px 16px !important; border-top: 3px double rgba(245,158,11,0.4) !important; background: rgba(245,158,11,0.03); }

    /* ─── Footer ─── */
    .mp-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 24px;
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        margin-bottom: 20px;
    }

    /* ─── Responsive ─── */
    @media (max-width: 1200px) {
        .mp-exec-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 768px) {
        .mp-exec-grid { grid-template-columns: repeat(2, 1fr); }
        .mp-exec-margins { flex-direction: column; gap: 12px; }
        .mp-nav { flex-wrap: wrap; }
        .mp-nav-status { width: 100%; text-align: center; }
    }
</style>
<script>
function mpToggleSub(row) {
    var target = row.getAttribute('data-target');
    var details = document.querySelectorAll('.' + target);
    var isOpen = row.classList.contains('mp-is-open');
    if (isOpen) {
        row.classList.remove('mp-is-open');
        for (var i = 0; i < details.length; i++) details[i].style.display = 'none';
    } else {
        row.classList.add('mp-is-open');
        for (var i = 0; i < details.length; i++) details[i].style.display = 'table-row';
    }
}

function mpToggleBsSub(row) {
    var target = row.getAttribute('data-target');
    var details = document.querySelectorAll('.' + target);
    var isOpen = row.classList.contains('mp-bs-open');
    if (isOpen) {
        row.classList.remove('mp-bs-open');
        for (var i = 0; i < details.length; i++) details[i].style.display = 'none';
    } else {
        row.classList.add('mp-bs-open');
        for (var i = 0; i < details.length; i++) details[i].style.display = 'table-row';
    }
}

function mpScrollTo(id, btn) {
    var el = document.getElementById(id);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    var btns = document.querySelectorAll('.mp-nav-btn');
    for (var i = 0; i < btns.length; i++) btns[i].classList.remove('mp-nav-active');
    if (btn) btn.classList.add('mp-nav-active');
}
</script>
@endpush
