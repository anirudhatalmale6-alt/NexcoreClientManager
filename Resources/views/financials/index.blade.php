@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', 'Financials - ' . $client->company_name)
@section('page_heading', 'FINANCIALS')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(34,197,94,0.15), rgba(34,197,94,0.05)); border:1px solid rgba(34,197,94,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-chart-line" style="color:var(--accent-green); font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">Financials</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; gap:8px;">
            <a href="{{ route('nexcore.clients.show.financials.create', $client->id) }}" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-plus"></i> New Record</a>
        </div>
    </div>
</div>

<div class="sl-stats-grid sl-animate d2">
    <div class="sl-stat-card green">
        <div class="sl-stat-label">Total</div>
        <div class="sl-stat-value" style="color:var(--accent-green);">{{ $financials->count() }}</div>
        <div class="sl-stat-meta">All financial records</div>
    </div>
    <div class="sl-stat-card blue">
        <div class="sl-stat-label">In Progress</div>
        <div class="sl-stat-value" style="color:var(--accent-blue);">{{ $financials->filter(fn($f) => $f->status && $f->status->name === 'In Progress')->count() }}</div>
        <div class="sl-stat-meta">Currently in progress</div>
    </div>
    <div class="sl-stat-card amber">
        <div class="sl-stat-label">Completed</div>
        <div class="sl-stat-value" style="color:var(--accent-amber);">{{ $financials->filter(fn($f) => $f->status && in_array($f->status->name, ['Submitted', 'Assessed', 'Paid']))->count() }}</div>
        <div class="sl-stat-meta">Submitted / Assessed / Paid</div>
    </div>
</div>

<div class="sl-card sl-animate d3">
    <div class="sl-card-header">
        <div class="sl-card-title"><i class="fas fa-chart-line"></i> Financial Records</div>
        <div style="font-size:13px; color:var(--text-muted);">{{ $financials->count() }} records</div>
    </div>
    <div class="sl-table-wrap">
        <table class="sl-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Type</th>
                    <th>Financial Year</th>
                    <th>Period</th>
                    <th class="center">Status</th>
                    <th>Prepared By</th>
                    <th>Reviewed By</th>
                    <th>Approved</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($financials as $idx => $fin)
                <tr>
                    <td style="color:var(--text-muted);">{{ $idx + 1 }}</td>
                    <td>
                        @if($fin->financialType)
                            <span class="sl-tag sl-tag-blue">{{ $fin->financialType->code }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td><span style="font-family:var(--font-mono); font-size:14px; font-weight:700; color:var(--accent-amber);">{{ $fin->financial_year }}</span></td>
                    <td style="font-size:13px; color:var(--text-secondary);">
                        @if($fin->period_start && $fin->period_end)
                            {{ $fin->period_start->format('j M Y') }} - {{ $fin->period_end->format('j M Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="center">
                        @if($fin->status)
                            <span class="sl-tag sl-tag-{{ $fin->status->color }}">{{ $fin->status->name }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td style="font-size:13px; color:var(--text-secondary);">{{ $fin->prepared_by ?? '-' }}</td>
                    <td style="font-size:13px; color:var(--text-secondary);">{{ $fin->reviewed_by ?? '-' }}</td>
                    <td style="font-family:var(--font-mono); font-size:13px; color:var(--text-secondary);">{{ $fin->approved_date ? $fin->approved_date->format('j M Y') : '-' }}</td>
                    <td class="center">
                        <div style="display:flex; gap:6px; justify-content:center;">
                            <a href="{{ route('nexcore.clients.show.financials.edit', [$client->id, $fin->id]) }}" style="color:var(--accent-blue); font-size:15px;" title="Edit"><i class="fas fa-pen"></i></a>
                            <form method="POST" action="{{ route('nexcore.clients.show.financials.destroy', [$client->id, $fin->id]) }}" style="display:inline;" onsubmit="return confirm('Delete this financial record?')">@csrf @method('DELETE')
                                <button type="submit" style="background:none; border:none; color:var(--accent-red); cursor:pointer; font-size:15px;" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center; padding:60px; color:var(--text-muted);">
                    <i class="fas fa-chart-line" style="font-size:40px; opacity:0.2; margin-bottom:16px; display:block;"></i>
                    <div style="font-size:16px; font-weight:600; margin-bottom:6px;">No financial records yet</div>
                    <div style="font-size:13px;">Click "New Record" to add the first financial record for this client</div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
