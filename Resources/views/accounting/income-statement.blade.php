@extends('nexcore_client_manager::layouts.accounting')

@section('title', 'Income Statement - ' . $client->company_name)
@section('page_heading', 'INCOME STATEMENT')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(6,182,212,0.15), rgba(6,182,212,0.05)); border:1px solid rgba(6,182,212,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-file-invoice-dollar" style="color:var(--accent-cyan); font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Income Statement | {{ $client->company_name }}</h1>
                <span class="sl-page-subtitle">{{ \Carbon\Carbon::parse($fromDate)->format('j M Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('j M Y') }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; align-items:center; gap:12px;">
            <span style="font-size:12px; color:var(--text-muted); font-family:var(--font-mono);">{{ now()->format('j M Y, H:i') }}</span>
        </div>
    </div>
</div>

@include('nexcore_client_manager::accounting.partials.period-filter')

{{-- Summary Cards --}}
<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Total Revenue</div>
        <div class="sl-stat-value" style="color:var(--accent-green); font-size:20px;">R {{ number_format($revenueTotal, 2, '.', ' ') }}</div>
        <div class="sl-stat-meta">{{ $revenueCount }} account{{ $revenueCount != 1 ? 's' : '' }} with activity</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(245,158,11,0.4);">
        <div class="sl-stat-label">Cost of Sales</div>
        <div class="sl-stat-value" style="color:#f59e0b; font-size:20px;">R {{ number_format($cosTotal, 2, '.', ' ') }}</div>
        <div class="sl-stat-meta">{{ $cosCount }} account{{ $cosCount != 1 ? 's' : '' }} with activity</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba({{ $grossProfit >= 0 ? '16,185,129' : '239,68,68' }},0.4);">
        <div class="sl-stat-label">Gross Profit</div>
        <div class="sl-stat-value" style="color:{{ $grossProfit >= 0 ? 'var(--accent-green)' : 'var(--accent-red)' }}; font-size:20px;">R {{ number_format($grossProfit, 2, '.', ' ') }}</div>
        @if($revenueTotal > 0)
        <div class="sl-stat-meta">{{ number_format(($grossProfit / $revenueTotal) * 100, 1, '.', ',') }}% margin</div>
        @endif
    </div>
    <div class="sl-stat-card" style="border-color:rgba({{ $netProfit >= 0 ? '16,185,129' : '239,68,68' }},0.4);">
        <div class="sl-stat-label">Net Profit</div>
        <div class="sl-stat-value" style="color:{{ $netProfit >= 0 ? 'var(--accent-green)' : 'var(--accent-red)' }}; font-size:20px;">R {{ number_format($netProfit, 2, '.', ' ') }}</div>
        @if($revenueTotal > 0)
        <div class="sl-stat-meta">{{ number_format(($netProfit / $revenueTotal) * 100, 1, '.', ',') }}% margin</div>
        @endif
    </div>
</div>

@if($revenueCount > 0 || $cosCount > 0 || $expenseCount > 0)
<div class="sl-card sl-animate d3">
    <div class="sl-card-header" style="display:flex; align-items:center; justify-content:space-between;">
        <div class="sl-card-title" style="color:var(--accent-cyan);"><i class="fas fa-file-invoice-dollar"></i> Profit &amp; Loss Statement</div>
    </div>

    <div class="sl-table-wrap">
        <table class="sl-table is-table">
            <thead>
                <tr>
                    <th>Account</th>
                    <th class="right" style="width:180px;">Amount (R)</th>
                    <th class="right" style="width:180px;">Total (R)</th>
                </tr>
            </thead>
            <tbody>
                {{-- REVENUE SECTION --}}
                <tr class="is-section-header">
                    <td colspan="3">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div class="is-section-icon" style="color:var(--accent-green); background:rgba(16,185,129,0.1);">
                                <i class="fas fa-arrow-trend-up"></i>
                            </div>
                            <span style="font-weight:700; font-size:14px; color:var(--text-primary);">Revenue</span>
                            <span style="font-size:11px; color:var(--text-muted); font-family:var(--font-mono);">({{ $revenueCount }} accounts)</span>
                        </div>
                    </td>
                </tr>

                @forelse($revenueGroups as $mainGroup)
                <tr class="is-main-group">
                    <td colspan="3" style="padding-left:24px; padding-top:14px; padding-bottom:14px;">
                        <span class="mono" style="color:var(--accent-amber); font-weight:700; font-size:18px;">{{ rtrim($mainGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-amber); font-weight:700; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:700; color:var(--accent-amber); font-size:18px; text-transform:uppercase;">{{ $mainGroup['account']->account_name }}</span>
                    </td>
                </tr>
                @foreach($mainGroup['sub_groups'] as $subGroup)
                <tr class="is-sub-group is-collapsible" data-target="sub-{{ $subGroup['account']->id }}" onclick="toggleSubGroup(this)">
                    <td style="padding-left:48px;">
                        <i class="fas fa-chevron-right is-chevron"></i>
                        <span class="mono" style="color:var(--accent-blue); font-weight:600; font-size:18px;">{{ rtrim($subGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-blue); font-weight:600; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:600; color:var(--accent-blue); font-size:18px; text-transform:uppercase;">{{ $subGroup['account']->account_name }}</span>
                    </td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:600; color:var(--accent-green);">R {{ number_format($subGroup['subtotal'], 2, '.', ' ') }}</td>
                </tr>
                @foreach($subGroup['details'] as $detail)
                <tr class="is-detail-row sub-{{ $subGroup['account']->id }}{{ $loop->last ? ' is-detail-last' : '' }}" style="display:none;">
                    <td style="padding-left:62px; font-size:16px; color:var(--text-primary);">{{ $detail['account']->account_name }}</td>
                    <td class="right mono" style="font-size:17px; color:var(--accent-green);">R {{ number_format($detail['amount'], 2, '.', ' ') }}</td>
                    <td></td>
                </tr>
                @endforeach
                @endforeach
                @empty
                <tr class="is-detail-row"><td colspan="3" style="padding-left:42px; color:var(--text-muted); font-style:italic;">No revenue activity</td></tr>
                @endforelse

                <tr class="is-subtotal-row">
                    <td class="right" style="font-weight:700; font-size:17px; color:#a78bfa; text-transform:uppercase; letter-spacing:0.5px;">Total Revenue</td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:700; color:#a78bfa;">R {{ number_format($revenueTotal, 2, '.', ' ') }}</td>
                </tr>

                {{-- COST OF SALES SECTION --}}
                <tr class="is-section-header">
                    <td colspan="3">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div class="is-section-icon" style="color:#f59e0b; background:rgba(245,158,11,0.1);">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <span style="font-weight:700; font-size:14px; color:var(--text-primary);">Less: Cost of Sales</span>
                            <span style="font-size:11px; color:var(--text-muted); font-family:var(--font-mono);">({{ $cosCount }} accounts)</span>
                        </div>
                    </td>
                </tr>

                @forelse($cosGroups as $mainGroup)
                <tr class="is-main-group">
                    <td colspan="3" style="padding-left:24px; padding-top:14px; padding-bottom:14px;">
                        <span class="mono" style="color:var(--accent-amber); font-weight:700; font-size:18px;">{{ rtrim($mainGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-amber); font-weight:700; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:700; color:var(--accent-amber); font-size:18px; text-transform:uppercase;">{{ $mainGroup['account']->account_name }}</span>
                    </td>
                </tr>
                @foreach($mainGroup['sub_groups'] as $subGroup)
                <tr class="is-sub-group is-collapsible" data-target="sub-{{ $subGroup['account']->id }}" onclick="toggleSubGroup(this)">
                    <td style="padding-left:48px;">
                        <i class="fas fa-chevron-right is-chevron"></i>
                        <span class="mono" style="color:var(--accent-blue); font-weight:600; font-size:18px;">{{ rtrim($subGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-blue); font-weight:600; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:600; color:var(--accent-blue); font-size:18px; text-transform:uppercase;">{{ $subGroup['account']->account_name }}</span>
                    </td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:600; color:#f59e0b;">R {{ number_format($subGroup['subtotal'], 2, '.', ' ') }}</td>
                </tr>
                @foreach($subGroup['details'] as $detail)
                <tr class="is-detail-row sub-{{ $subGroup['account']->id }}{{ $loop->last ? ' is-detail-last' : '' }}" style="display:none;">
                    <td style="padding-left:62px; font-size:16px; color:var(--text-primary);">{{ $detail['account']->account_name }}</td>
                    <td class="right mono" style="font-size:17px; color:#f59e0b;">R {{ number_format($detail['amount'], 2, '.', ' ') }}</td>
                    <td></td>
                </tr>
                @endforeach
                @endforeach
                @empty
                <tr class="is-detail-row"><td colspan="3" style="padding-left:42px; color:var(--text-muted); font-style:italic;">No cost of sales activity</td></tr>
                @endforelse

                <tr class="is-subtotal-row">
                    <td class="right" style="font-weight:700; font-size:17px; color:#a78bfa; text-transform:uppercase; letter-spacing:0.5px;">Total Cost of Sales</td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:700; color:#a78bfa;">R {{ number_format($cosTotal, 2, '.', ' ') }}</td>
                </tr>

                {{-- GROSS PROFIT --}}
                <tr class="is-profit-row">
                    <td class="right" style="font-size:18px; font-weight:800; color:#a78bfa; text-transform:uppercase; letter-spacing:1px;">Gross Profit</td>
                    <td></td>
                    <td class="right mono" style="font-size:18px; font-weight:800; color:#a78bfa;">
                        R {{ number_format(abs($grossProfit), 2, '.', ' ') }}
                    </td>
                </tr>

                {{-- OPERATING EXPENSES SECTION --}}
                <tr class="is-section-header">
                    <td colspan="3">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div class="is-section-icon" style="color:#f59e0b; background:rgba(245,158,11,0.1);">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                            <span style="font-weight:700; font-size:14px; color:var(--text-primary);">Less: Operating Expenses</span>
                            <span style="font-size:11px; color:var(--text-muted); font-family:var(--font-mono);">({{ $expenseCount }} accounts)</span>
                        </div>
                    </td>
                </tr>

                @forelse($expenseGroups as $mainGroup)
                <tr class="is-main-group">
                    <td colspan="3" style="padding-left:24px; padding-top:14px; padding-bottom:14px;">
                        <span class="mono" style="color:var(--accent-amber); font-weight:700; font-size:18px;">{{ rtrim($mainGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-amber); font-weight:700; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:700; color:var(--accent-amber); font-size:18px; text-transform:uppercase;">{{ $mainGroup['account']->account_name }}</span>
                    </td>
                </tr>
                @foreach($mainGroup['sub_groups'] as $subGroup)
                <tr class="is-sub-group is-collapsible" data-target="sub-{{ $subGroup['account']->id }}" onclick="toggleSubGroup(this)">
                    <td style="padding-left:48px;">
                        <i class="fas fa-chevron-right is-chevron"></i>
                        <span class="mono" style="color:var(--accent-blue); font-weight:600; font-size:18px;">{{ rtrim($subGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-blue); font-weight:600; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:600; color:var(--accent-blue); font-size:18px; text-transform:uppercase;">{{ $subGroup['account']->account_name }}</span>
                    </td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:600; color:#f59e0b;">R {{ number_format($subGroup['subtotal'], 2, '.', ' ') }}</td>
                </tr>
                @foreach($subGroup['details'] as $detail)
                <tr class="is-detail-row sub-{{ $subGroup['account']->id }}{{ $loop->last ? ' is-detail-last' : '' }}" style="display:none;">
                    <td style="padding-left:62px; font-size:16px; color:var(--text-primary);">{{ $detail['account']->account_name }}</td>
                    <td class="right mono" style="font-size:17px; color:#f59e0b;">R {{ number_format($detail['amount'], 2, '.', ' ') }}</td>
                    <td></td>
                </tr>
                @endforeach
                @endforeach
                @empty
                <tr class="is-detail-row"><td colspan="3" style="padding-left:42px; color:var(--text-muted); font-style:italic;">No expense activity</td></tr>
                @endforelse

                <tr class="is-subtotal-row">
                    <td class="right" style="font-weight:700; font-size:17px; color:#a78bfa; text-transform:uppercase; letter-spacing:0.5px;">Total Operating Expenses</td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:700; color:#a78bfa;">R {{ number_format($expenseTotal, 2, '.', ' ') }}</td>
                </tr>

                {{-- NET PROFIT --}}
                <tr class="is-net-profit-row">
                    <td class="right" style="font-size:19px; font-weight:800; text-transform:uppercase; letter-spacing:1.5px; color:#a78bfa;">
                        Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}
                    </td>
                    <td></td>
                    <td class="right mono" style="font-size:19px; font-weight:800; color:#a78bfa;">
                        R {{ number_format(abs($netProfit), 2, '.', ' ') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Margin Analysis --}}
@if($revenueTotal > 0)
<div class="sl-card sl-animate d4">
    <div class="sl-card-header">
        <div class="sl-card-title" style="color:#f59e0b;"><i class="fas fa-chart-bar"></i> Margin Analysis</div>
    </div>
    <div style="padding:24px;">
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:24px;">
            <div>
                <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:8px;">
                    <span style="font-size:13px; font-weight:600; color:var(--text-secondary);">Gross Margin</span>
                    <span style="font-size:14px; font-weight:700; font-family:var(--font-mono); color:#a78bfa;">{{ number_format(($grossProfit / $revenueTotal) * 100, 1, '.', ',') }}%</span>
                </div>
                <div style="height:8px; background:var(--bg-raised); border-radius:4px; overflow:hidden;">
                    @php $gpPct = max(0, min(100, ($grossProfit / $revenueTotal) * 100)); @endphp
                    <div style="height:100%; width:{{ $gpPct }}%; background:linear-gradient(90deg, #a78bfa, #c4b5fd); border-radius:4px; transition:width 0.6s ease;"></div>
                </div>
            </div>
            <div>
                <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:8px;">
                    <span style="font-size:13px; font-weight:600; color:var(--text-secondary);">Net Margin</span>
                    <span style="font-size:14px; font-weight:700; font-family:var(--font-mono); color:#a78bfa;">{{ number_format(($netProfit / $revenueTotal) * 100, 1, '.', ',') }}%</span>
                </div>
                <div style="height:8px; background:var(--bg-raised); border-radius:4px; overflow:hidden;">
                    @php $npPct = max(0, min(100, ($netProfit / $revenueTotal) * 100)); @endphp
                    <div style="height:100%; width:{{ $npPct }}%; background:linear-gradient(90deg, #a78bfa, #c4b5fd); border-radius:4px; transition:width 0.6s ease;"></div>
                </div>
            </div>
            <div>
                <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:8px;">
                    <span style="font-size:13px; font-weight:600; color:var(--text-secondary);">Expense Ratio</span>
                    <span style="font-size:14px; font-weight:700; font-family:var(--font-mono); color:#f59e0b;">{{ number_format(($expenseTotal / $revenueTotal) * 100, 1, '.', ',') }}%</span>
                </div>
                <div style="height:8px; background:var(--bg-raised); border-radius:4px; overflow:hidden;">
                    @php $ePct = max(0, min(100, ($expenseTotal / $revenueTotal) * 100)); @endphp
                    <div style="height:100%; width:{{ $ePct }}%; background:linear-gradient(90deg, #f59e0b, #fbbf24); border-radius:4px; transition:width 0.6s ease;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@else
<div class="sl-card sl-animate d3">
    <div style="text-align:center; padding:80px 40px; color:var(--text-muted);">
        <i class="fas fa-file-invoice-dollar" style="font-size:48px; opacity:0.15; margin-bottom:20px; display:block;"></i>
        <div style="font-size:18px; font-weight:700; margin-bottom:8px; color:var(--text-secondary);">No Income &amp; Expense Activity</div>
        <div style="font-size:14px; max-width:400px; margin:0 auto; line-height:1.6;">
            The income statement will populate once journal entries affecting revenue, cost of sales, or expense accounts are posted.
        </div>
        <a href="{{ route('nexcore.clients.show.accounting.journals.create', $client->id) }}" class="neon-btn neon-btn-amber" style="margin-top:24px; display:inline-flex;"><i class="fas fa-plus"></i> Create Journal Entry</a>
    </div>
</div>
@endif
@endsection

@push('scripts')
<style>
    .is-table { border-collapse:separate; border-spacing:0; }
    .is-section-header td { padding:18px 16px 8px !important; border-bottom:none !important; }
    .is-section-icon { width:28px; height:28px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:12px; }
    .is-main-group td { padding:14px 16px 6px !important; border-bottom:none !important; }
    .is-sub-group td { padding:10px 16px !important; border-bottom:1px solid rgba(255,255,255,0.04) !important; }
    .is-collapsible { cursor:pointer; }
    .is-collapsible:hover td { background:rgba(37,99,235,0.04); }
    .is-chevron { color:var(--accent-blue); font-size:11px; margin-right:10px; transition:transform 0.25s ease; display:inline-block; }
    .is-collapsible.is-open .is-chevron { transform:rotate(90deg); }
    .is-detail-row td:first-child { border-left:3px solid #3b82f6 !important; }
    .is-detail-row td { padding-top:7px !important; padding-bottom:7px !important; padding-right:16px !important; border-bottom:1px solid rgba(255,255,255,0.03) !important; }
    .is-detail-row:hover td { background:rgba(59,130,246,0.06); }
    .is-detail-last td:first-child { border-bottom-left-radius:10px; border-bottom:3px solid #3b82f6 !important; }
    .is-detail-last td { padding-bottom:14px !important; }
    .is-detail-last + tr td { padding-top:14px !important; }
    .is-subtotal-row td { padding:10px 16px !important; border-bottom:1px solid var(--border-subtle) !important; }
    .is-profit-row td { padding:16px 16px !important; border-top:2px solid rgba(167,139,250,0.3) !important; border-bottom:2px solid rgba(167,139,250,0.3) !important; background:rgba(167,139,250,0.03); }
    .is-net-profit-row td { padding:20px 16px !important; border-top:3px double rgba(167,139,250,0.5) !important; background:rgba(167,139,250,0.05); }
</style>
<script>
function toggleSubGroup(row) {
    var target = row.getAttribute('data-target');
    var details = document.querySelectorAll('.' + target);
    var isOpen = row.classList.contains('is-open');
    if (isOpen) {
        row.classList.remove('is-open');
        for (var i = 0; i < details.length; i++) {
            details[i].style.display = 'none';
        }
    } else {
        row.classList.add('is-open');
        for (var i = 0; i < details.length; i++) {
            details[i].style.display = 'table-row';
        }
    }
}
</script>
@endpush
