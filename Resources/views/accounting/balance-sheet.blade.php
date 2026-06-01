@extends('nexcore_client_manager::layouts.accounting')

@section('title', 'Balance Sheet - ' . $client->company_name)
@section('page_heading', 'BALANCE SHEET')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(168,85,247,0.15), rgba(168,85,247,0.05)); border:1px solid rgba(168,85,247,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-chart-pie" style="color:#a78bfa; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Balance Sheet | {{ $client->company_name }}</h1>
                <span class="sl-page-subtitle">As at {{ \Carbon\Carbon::parse($toDate)->format('j M Y') }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; align-items:center; gap:12px;">
            @if($isBalanced)
                <span style="display:flex; align-items:center; gap:6px; font-size:13px; font-weight:700; color:var(--accent-green); background:rgba(16,185,129,0.08); padding:8px 16px; border-radius:8px; border:1px solid rgba(16,185,129,0.25);"><i class="fas fa-check-circle"></i> Balanced</span>
            @else
                <span style="display:flex; align-items:center; gap:6px; font-size:13px; font-weight:700; color:var(--accent-red); background:rgba(239,68,68,0.08); padding:8px 16px; border-radius:8px; border:1px solid rgba(239,68,68,0.25);"><i class="fas fa-exclamation-circle"></i> Out of Balance</span>
            @endif
            <span style="font-size:12px; color:var(--text-muted); font-family:var(--font-mono);">{{ now()->format('j M Y, H:i') }}</span>
        </div>
    </div>
</div>

@include('nexcore_client_manager::accounting.partials.period-filter')

{{-- Summary Cards --}}
<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">Total Assets</div>
        <div class="sl-stat-value" style="color:var(--accent-blue); font-size:20px;">R {{ number_format($assetTotal, 2, '.', ' ') }}</div>
        <div class="sl-stat-meta">{{ $assetCount }} account{{ $assetCount != 1 ? 's' : '' }} with activity</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(245,158,11,0.4);">
        <div class="sl-stat-label">Total Liabilities</div>
        <div class="sl-stat-value" style="color:#f59e0b; font-size:20px;">R {{ number_format($liabilityTotal, 2, '.', ' ') }}</div>
        <div class="sl-stat-meta">{{ $liabilityCount }} account{{ $liabilityCount != 1 ? 's' : '' }} with activity</div>
    </div>
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Total Equity</div>
        <div class="sl-stat-value" style="color:var(--accent-green); font-size:20px;">R {{ number_format($equityTotal, 2, '.', ' ') }}</div>
        <div class="sl-stat-meta">{{ $equityCount }} account{{ $equityCount != 1 ? 's' : '' }} with activity</div>
    </div>
    <div class="sl-stat-card" style="border-color:rgba(167,139,250,0.4);">
        <div class="sl-stat-label">Net Profit / Loss</div>
        <div class="sl-stat-value" style="color:#a78bfa; font-size:20px;">R {{ number_format($netProfit, 2, '.', ' ') }}</div>
        <div class="sl-stat-meta">Retained for period</div>
    </div>
</div>

@if($assetCount > 0 || $liabilityCount > 0 || $equityCount > 0)
<div class="sl-card sl-animate d3">
    <div class="sl-card-header" style="display:flex; align-items:center; justify-content:space-between;">
        <div class="sl-card-title" style="color:var(--accent-cyan);"><i class="fas fa-chart-pie"></i> Statement of Financial Position</div>
    </div>

    <div class="sl-table-wrap">
        <table class="sl-table bs-table">
            <thead>
                <tr>
                    <th>Account</th>
                    <th class="right" style="width:180px;">Amount (R)</th>
                    <th class="right" style="width:180px;">Total (R)</th>
                </tr>
            </thead>
            <tbody>
                {{-- ASSETS SECTION --}}
                <tr class="bs-section-header">
                    <td colspan="3">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div class="bs-section-icon" style="color:var(--accent-blue); background:rgba(37,99,235,0.1);">
                                <i class="fas fa-coins"></i>
                            </div>
                            <span style="font-weight:700; font-size:16px; color:var(--text-primary);">Assets</span>
                            <span style="font-size:11px; color:var(--text-muted); font-family:var(--font-mono);">({{ $assetCount }} accounts)</span>
                        </div>
                    </td>
                </tr>

                @forelse($assetGroups as $mainGroup)
                <tr class="bs-main-group">
                    <td colspan="3" style="padding-left:24px; padding-top:14px; padding-bottom:14px;">
                        <span class="mono" style="color:var(--accent-amber); font-weight:700; font-size:18px;">{{ rtrim($mainGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-amber); font-weight:700; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:700; color:var(--accent-amber); font-size:18px; text-transform:uppercase;">{{ $mainGroup['account']->account_name }}</span>
                    </td>
                </tr>
                @foreach($mainGroup['sub_groups'] as $subGroup)
                <tr class="bs-sub-group bs-collapsible" data-target="bs-sub-{{ $subGroup['account']->id }}" onclick="toggleBsSubGroup(this)">
                    <td style="padding-left:48px;">
                        <i class="fas fa-chevron-right bs-chevron"></i>
                        <span class="mono" style="color:var(--accent-blue); font-weight:600; font-size:18px;">{{ rtrim($subGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-blue); font-weight:600; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:600; color:var(--accent-blue); font-size:18px; text-transform:uppercase;">{{ $subGroup['account']->account_name }}</span>
                    </td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:600; color:var(--accent-blue);">R {{ number_format($subGroup['subtotal'], 2, '.', ' ') }}</td>
                </tr>
                @foreach($subGroup['details'] as $detail)
                <tr class="bs-detail-row bs-sub-{{ $subGroup['account']->id }}{{ $loop->last ? ' bs-detail-last' : '' }}" style="display:none;">
                    <td style="padding-left:62px; font-size:16px; color:var(--text-primary);">{{ $detail['account']->account_name }}</td>
                    <td class="right mono" style="font-size:17px; text-align:right; color:var(--accent-blue);">R {{ number_format($detail['amount'], 2, '.', ' ') }}</td>
                    <td></td>
                </tr>
                @endforeach
                @endforeach
                @empty
                <tr class="bs-detail-row"><td colspan="3" style="padding-left:42px; color:var(--text-muted); font-style:italic;">No asset activity</td></tr>
                @endforelse

                <tr class="bs-subtotal-row">
                    <td class="right" style="font-weight:700; font-size:17px; color:#a78bfa; text-transform:uppercase; letter-spacing:0.5px;">Total Assets</td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:700; color:#a78bfa;">R {{ number_format($assetTotal, 2, '.', ' ') }}</td>
                </tr>

                {{-- LIABILITIES SECTION --}}
                <tr class="bs-section-header">
                    <td colspan="3">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div class="bs-section-icon" style="color:#f59e0b; background:rgba(245,158,11,0.1);">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                            <span style="font-weight:700; font-size:16px; color:var(--text-primary);">Liabilities</span>
                            <span style="font-size:11px; color:var(--text-muted); font-family:var(--font-mono);">({{ $liabilityCount }} accounts)</span>
                        </div>
                    </td>
                </tr>

                @forelse($liabilityGroups as $mainGroup)
                <tr class="bs-main-group">
                    <td colspan="3" style="padding-left:24px; padding-top:14px; padding-bottom:14px;">
                        <span class="mono" style="color:var(--accent-amber); font-weight:700; font-size:18px;">{{ rtrim($mainGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-amber); font-weight:700; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:700; color:var(--accent-amber); font-size:18px; text-transform:uppercase;">{{ $mainGroup['account']->account_name }}</span>
                    </td>
                </tr>
                @foreach($mainGroup['sub_groups'] as $subGroup)
                <tr class="bs-sub-group bs-collapsible" data-target="bs-sub-{{ $subGroup['account']->id }}" onclick="toggleBsSubGroup(this)">
                    <td style="padding-left:48px;">
                        <i class="fas fa-chevron-right bs-chevron"></i>
                        <span class="mono" style="color:var(--accent-blue); font-weight:600; font-size:18px;">{{ rtrim($subGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-blue); font-weight:600; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:600; color:var(--accent-blue); font-size:18px; text-transform:uppercase;">{{ $subGroup['account']->account_name }}</span>
                    </td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:600; color:#f59e0b;">R {{ number_format($subGroup['subtotal'], 2, '.', ' ') }}</td>
                </tr>
                @foreach($subGroup['details'] as $detail)
                <tr class="bs-detail-row bs-sub-{{ $subGroup['account']->id }}{{ $loop->last ? ' bs-detail-last' : '' }}" style="display:none;">
                    <td style="padding-left:62px; font-size:16px; color:var(--text-primary);">{{ $detail['account']->account_name }}</td>
                    <td class="right mono" style="font-size:17px; text-align:right; color:#f59e0b;">R {{ number_format($detail['amount'], 2, '.', ' ') }}</td>
                    <td></td>
                </tr>
                @endforeach
                @endforeach
                @empty
                <tr class="bs-detail-row"><td colspan="3" style="padding-left:42px; color:var(--text-muted); font-style:italic;">No liability activity</td></tr>
                @endforelse

                <tr class="bs-subtotal-row">
                    <td class="right" style="font-weight:700; font-size:17px; color:#a78bfa; text-transform:uppercase; letter-spacing:0.5px;">Total Liabilities</td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:700; color:#a78bfa;">R {{ number_format($liabilityTotal, 2, '.', ' ') }}</td>
                </tr>

                {{-- EQUITY SECTION --}}
                <tr class="bs-section-header">
                    <td colspan="3">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div class="bs-section-icon" style="color:var(--accent-green); background:rgba(16,185,129,0.1);">
                                <i class="fas fa-landmark"></i>
                            </div>
                            <span style="font-weight:700; font-size:16px; color:var(--text-primary);">Equity</span>
                            <span style="font-size:11px; color:var(--text-muted); font-family:var(--font-mono);">({{ $equityCount }} accounts)</span>
                        </div>
                    </td>
                </tr>

                @forelse($equityGroups as $mainGroup)
                <tr class="bs-main-group">
                    <td colspan="3" style="padding-left:24px; padding-top:14px; padding-bottom:14px;">
                        <span class="mono" style="color:var(--accent-amber); font-weight:700; font-size:18px;">{{ rtrim($mainGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-amber); font-weight:700; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:700; color:var(--accent-amber); font-size:18px; text-transform:uppercase;">{{ $mainGroup['account']->account_name }}</span>
                    </td>
                </tr>
                @foreach($mainGroup['sub_groups'] as $subGroup)
                <tr class="bs-sub-group bs-collapsible" data-target="bs-sub-{{ $subGroup['account']->id }}" onclick="toggleBsSubGroup(this)">
                    <td style="padding-left:48px;">
                        <i class="fas fa-chevron-right bs-chevron"></i>
                        <span class="mono" style="color:var(--accent-blue); font-weight:600; font-size:18px;">{{ rtrim($subGroup['account']->account_code, '/') }}</span>
                        <span style="color:var(--accent-blue); font-weight:600; font-size:18px; margin:0 8px;">|</span>
                        <span style="font-weight:600; color:var(--accent-blue); font-size:18px; text-transform:uppercase;">{{ $subGroup['account']->account_name }}</span>
                    </td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:600; color:var(--accent-green);">R {{ number_format($subGroup['subtotal'], 2, '.', ' ') }}</td>
                </tr>
                @foreach($subGroup['details'] as $detail)
                <tr class="bs-detail-row bs-sub-{{ $subGroup['account']->id }}{{ $loop->last ? ' bs-detail-last' : '' }}" style="display:none;">
                    <td style="padding-left:62px; font-size:16px; color:var(--text-primary);">{{ $detail['account']->account_name }}</td>
                    <td class="right mono" style="font-size:17px; text-align:right; color:var(--accent-green);">R {{ number_format($detail['amount'], 2, '.', ' ') }}</td>
                    <td></td>
                </tr>
                @endforeach
                @endforeach
                @empty
                <tr class="bs-detail-row"><td colspan="3" style="padding-left:42px; color:var(--text-muted); font-style:italic;">No equity activity</td></tr>
                @endforelse

                {{-- Net Profit / Retained Earnings --}}
                <tr class="bs-sub-group" style="border-top:1px solid rgba(167,139,250,0.2);">
                    <td style="padding-left:48px;">
                        <i class="fas fa-calculator" style="color:#a78bfa; font-size:11px; margin-right:10px;"></i>
                        <span style="font-weight:600; color:#a78bfa; font-size:18px; text-transform:uppercase;">Net Profit / Retained Earnings</span>
                    </td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:600; color:#a78bfa;">R {{ number_format($netProfit, 2, '.', ' ') }}</td>
                </tr>

                <tr class="bs-subtotal-row">
                    <td class="right" style="font-weight:700; font-size:17px; color:#a78bfa; text-transform:uppercase; letter-spacing:0.5px;">Total Equity</td>
                    <td></td>
                    <td class="right mono" style="font-size:17px; font-weight:700; color:#a78bfa;">R {{ number_format($equityTotal + $netProfit, 2, '.', ' ') }}</td>
                </tr>

                {{-- TOTAL LIABILITIES + EQUITY --}}
                <tr class="bs-grand-row">
                    <td class="right" style="font-size:18px; font-weight:800; color:#a78bfa; text-transform:uppercase; letter-spacing:1px;">Total Liabilities &amp; Equity</td>
                    <td></td>
                    <td class="right mono" style="font-size:18px; font-weight:800; color:#a78bfa;">
                        R {{ number_format($totalLiabilitiesEquity, 2, '.', ' ') }}
                    </td>
                </tr>

                {{-- BALANCE CHECK --}}
                <tr class="bs-balance-row">
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
</div>
@else
<div class="sl-card sl-animate d3">
    <div style="text-align:center; padding:80px 40px; color:var(--text-muted);">
        <i class="fas fa-chart-pie" style="font-size:48px; opacity:0.15; margin-bottom:20px; display:block;"></i>
        <div style="font-size:18px; font-weight:700; margin-bottom:8px; color:var(--text-secondary);">No Balance Sheet Activity</div>
        <div style="font-size:14px; max-width:400px; margin:0 auto; line-height:1.6;">
            The balance sheet will populate once journal entries affecting asset, liability, or equity accounts are posted.
        </div>
        <a href="{{ route('nexcore.clients.show.accounting.journals.create', $client->id) }}" class="neon-btn neon-btn-amber" style="margin-top:24px; display:inline-flex;"><i class="fas fa-plus"></i> Create Journal Entry</a>
    </div>
</div>
@endif
@endsection

@push('scripts')
<style>
    .bs-table { border-collapse:separate; border-spacing:0; }
    .bs-section-header td { padding:18px 16px 8px !important; border-bottom:none !important; }
    .bs-section-icon { width:28px; height:28px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:12px; }
    .bs-main-group td { padding:14px 16px 6px !important; border-bottom:none !important; }
    .bs-sub-group td { padding:10px 16px !important; border-bottom:1px solid rgba(255,255,255,0.04) !important; }
    .bs-collapsible { cursor:pointer; }
    .bs-collapsible:hover td { background:rgba(37,99,235,0.04); }
    .bs-chevron { color:var(--accent-blue); font-size:11px; margin-right:10px; transition:transform 0.25s ease; display:inline-block; }
    .bs-collapsible.bs-open .bs-chevron { transform:rotate(90deg); }
    .bs-detail-row td:first-child { border-left:3px solid #3b82f6 !important; }
    .bs-detail-row td { padding-top:7px !important; padding-bottom:7px !important; padding-right:16px !important; border-bottom:1px solid rgba(255,255,255,0.03) !important; }
    .bs-detail-row:hover td { background:rgba(59,130,246,0.06); }
    .bs-detail-last td:first-child { border-bottom-left-radius:10px; border-bottom:3px solid #3b82f6 !important; }
    .bs-detail-last td { padding-bottom:14px !important; }
    .bs-detail-last + tr td { padding-top:14px !important; }
    .bs-subtotal-row td { padding:10px 16px !important; border-bottom:1px solid var(--border-subtle) !important; }
    .bs-grand-row td { padding:16px 16px !important; border-top:2px solid rgba(167,139,250,0.3) !important; border-bottom:2px solid rgba(167,139,250,0.3) !important; background:rgba(167,139,250,0.03); }
    .bs-balance-row td { border-top:3px double rgba(167,139,250,0.5) !important; background:rgba(167,139,250,0.05); }
</style>
<script>
function toggleBsSubGroup(row) {
    var target = row.getAttribute('data-target');
    var details = document.querySelectorAll('.' + target);
    var isOpen = row.classList.contains('bs-open');
    if (isOpen) {
        row.classList.remove('bs-open');
        for (var i = 0; i < details.length; i++) {
            details[i].style.display = 'none';
        }
    } else {
        row.classList.add('bs-open');
        for (var i = 0; i < details.length; i++) {
            details[i].style.display = 'table-row';
        }
    }
}
</script>
@endpush
