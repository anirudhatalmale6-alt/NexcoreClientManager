<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NexCore Client Manager | @yield('title', 'Clients')</title>
    <link rel="shortcut icon" type="image/png" href="/public/smartdash/images/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/public/css/nexcore_gl_master.css?v={{ time() }}" rel="stylesheet">
    <style>
    .sl-app { font-size: 15px; }
    .sl-field label { font-size: 13px; }
    .sl-field input, .sl-field select, .sl-field textarea { font-size: 15px; }
    .sl-stat-value { font-size: 22px; }
    .sl-stat-label { font-size: 13px; }
    .sl-table th { font-size: 13px; }
    .sl-table td { font-size: 14px; }
    .sl-result-label { font-size: 14px; }
    .sl-result-value { font-size: 14px; }
    .sl-tag { font-size: 12px; }
    .sl-page-title { font-size: 22px; }
    .sl-page-subtitle { font-size: 14px; }
    .sl-nav-item { font-size: 14px; }
    .sl-btn { font-size: 14px; }
    .sl-topbar-module, .sl-topbar-module-name { font-size: 14px; }
    .sl-card-title { font-size: 15px; }

    .neon-btn {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        text-decoration: none;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        font-family: var(--font-body);
        line-height: 1.4;
    }
    .neon-btn::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 6px;
        padding: 2px;
        background: linear-gradient(135deg, var(--neon-from), var(--neon-to));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: 0.8;
        transition: opacity 0.3s;
    }
    .neon-btn::after {
        content: '';
        position: absolute;
        inset: -2px;
        border-radius: 8px;
        background: linear-gradient(135deg, var(--neon-from), var(--neon-to));
        opacity: 0;
        filter: blur(12px);
        transition: opacity 0.4s;
        z-index: -1;
    }
    .neon-btn:hover::before { opacity: 1; }
    .neon-btn:hover::after { opacity: 0.4; }
    .neon-btn:hover { transform: translateY(-2px); }
    .neon-btn:active { transform: translateY(0) scale(0.98); }
    .neon-btn i { font-size: 12px; transition: transform 0.3s; }
    .neon-btn:hover i { transform: scale(1.15); }

    .neon-btn-green { --neon-from: #22c55e; --neon-to: #10b981; color: #22c55e; background: rgba(34, 197, 94, 0.08); }
    .neon-btn-green:hover { color: #4ade80; background: rgba(34, 197, 94, 0.15); box-shadow: 0 0 20px rgba(34, 197, 94, 0.2), 0 0 40px rgba(34, 197, 94, 0.1); }
    .neon-btn-green:hover i { filter: drop-shadow(0 0 4px rgba(34, 197, 94, 0.6)); }

    .neon-btn-cyan { --neon-from: #06b6d4; --neon-to: #0ea5e9; color: #06b6d4; background: rgba(6, 182, 212, 0.08); }
    .neon-btn-cyan:hover { color: #22d3ee; background: rgba(6, 182, 212, 0.15); box-shadow: 0 0 20px rgba(6, 182, 212, 0.2), 0 0 40px rgba(6, 182, 212, 0.1); }
    .neon-btn-cyan:hover i { filter: drop-shadow(0 0 4px rgba(6, 182, 212, 0.6)); }

    .neon-btn-blue { --neon-from: #3b82f6; --neon-to: #6366f1; color: #3b82f6; background: rgba(59, 130, 246, 0.08); }
    .neon-btn-blue:hover { color: #60a5fa; background: rgba(59, 130, 246, 0.15); box-shadow: 0 0 20px rgba(59, 130, 246, 0.2), 0 0 40px rgba(59, 130, 246, 0.1); }
    .neon-btn-blue:hover i { filter: drop-shadow(0 0 4px rgba(59, 130, 246, 0.6)); }

    .neon-btn-amber { --neon-from: #f59e0b; --neon-to: #d97706; color: #f59e0b; background: rgba(245, 158, 11, 0.08); }
    .neon-btn-amber:hover { color: #fbbf24; background: rgba(245, 158, 11, 0.15); box-shadow: 0 0 20px rgba(245, 158, 11, 0.2), 0 0 40px rgba(245, 158, 11, 0.1); }
    .neon-btn-amber:hover i { filter: drop-shadow(0 0 4px rgba(245, 158, 11, 0.6)); }

    .neon-btn-purple { --neon-from: #a855f7; --neon-to: #7c3aed; color: #a855f7; background: rgba(168, 85, 247, 0.08); }
    .neon-btn-purple:hover { color: #c084fc; background: rgba(168, 85, 247, 0.15); box-shadow: 0 0 20px rgba(168, 85, 247, 0.2), 0 0 40px rgba(168, 85, 247, 0.1); }
    .neon-btn-purple:hover i { filter: drop-shadow(0 0 4px rgba(168, 85, 247, 0.6)); }

    .neon-btn-red { --neon-from: #ef4444; --neon-to: #dc2626; color: #ef4444; background: rgba(239, 68, 68, 0.08); }
    .neon-btn-red:hover { color: #f87171; background: rgba(239, 68, 68, 0.15); box-shadow: 0 0 20px rgba(239, 68, 68, 0.2), 0 0 40px rgba(239, 68, 68, 0.1); }
    .neon-btn-red:hover i { filter: drop-shadow(0 0 4px rgba(239, 68, 68, 0.6)); }

    .neon-btn-ghost { --neon-from: #64748b; --neon-to: #94a3b8; color: #94a3b8; background: rgba(148, 163, 184, 0.05); }
    .neon-btn-ghost:hover { color: #cbd5e1; background: rgba(148, 163, 184, 0.1); box-shadow: 0 0 15px rgba(148, 163, 184, 0.15), 0 0 30px rgba(148, 163, 184, 0.08); }

    @keyframes neonPulse {
        0%, 100% { box-shadow: 0 0 8px rgba(34, 197, 94, 0.15); }
        50% { box-shadow: 0 0 16px rgba(34, 197, 94, 0.25), 0 0 32px rgba(34, 197, 94, 0.1); }
    }
    .neon-pulse { animation: neonPulse 3s ease-in-out infinite; }
    .neon-pulse:hover { animation: none; }

    .ncm-client-header { display:flex; align-items:center; gap:16px; padding:16px 20px; background:var(--bg-surface); border-bottom:1px solid var(--border-subtle); }
    .ncm-client-logo { width:44px; height:44px; border-radius:10px; background:var(--bg-raised); border:1px solid var(--border-subtle); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0; }
    .ncm-client-logo img { width:100%; height:100%; object-fit:contain; padding:3px; }
    .ncm-client-logo .initials { font-size:14px; font-weight:800; color:var(--text-muted); font-family:var(--font-mono); letter-spacing:1px; }
    .ncm-nav-divider { height:1px; background:var(--border-subtle); margin:8px 16px; }
    </style>
    @stack('styles')
</head>
<body>
<div class="sl-app">
    <aside class="sl-sidebar">
        <div class="sl-brand">
            <div class="sl-brand-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect x="2" y="2" width="9" height="9" rx="2" fill="#059669"/>
                    <rect x="13" y="2" width="9" height="9" rx="2" fill="#2563eb"/>
                    <rect x="2" y="13" width="9" height="9" rx="2" fill="#d97706"/>
                    <rect x="13" y="13" width="9" height="9" rx="2" fill="#7c3aed"/>
                </svg>
            </div>
            <div class="sl-brand-text">
                <span class="sl-brand-name">NexCore</span>
                <span class="sl-brand-module">Client Manager</span>
            </div>
        </div>

        @if(isset($client))
        <div class="ncm-client-header">
            <div class="ncm-client-logo">
                @if($client->client_logo)
                    <img src="{{ asset($client->client_logo) }}" alt="{{ $client->client_code }}">
                @else
                    <span class="initials">{{ substr($client->client_code ?? '?', 0, 3) }}</span>
                @endif
            </div>
            <div style="min-width:0;">
                <div style="font-weight:700; font-size:13px; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $client->company_name }}</div>
                <div style="font-size:11px; font-family:var(--font-mono); color:var(--accent-amber); font-weight:600;">{{ $client->client_code }}</div>
            </div>
        </div>

        <nav class="sl-nav-section">
            <div class="sl-nav-label">Client</div>
            <a href="{{ route('nexcore.clients.show.dashboard', $client->id) }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.show.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> Overview
            </a>
            <a href="{{ route('nexcore.clients.show.edit', $client->id) }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.show.edit') ? 'active' : '' }}">
                <i class="fas fa-building"></i> Company Info
            </a>
        </nav>

        <nav class="sl-nav-section">
            <div class="sl-nav-label">Details</div>
            <a href="{{ route('nexcore.clients.show.addresses', $client->id) }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.show.addresses*') ? 'active' : '' }}">
                <i class="fas fa-map-marker-alt"></i> Addresses
                @if($client->addresses_count ?? $client->addresses()->count()) <span style="margin-left:auto; font-size:11px; font-family:var(--font-mono); color:var(--accent-green); font-weight:700;">{{ $client->addresses_count ?? $client->addresses()->count() }}</span> @endif
            </a>
            <a href="{{ route('nexcore.clients.show.contacts', $client->id) }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.show.contacts*') ? 'active' : '' }}">
                <i class="fas fa-address-book"></i> Contacts
                @if($client->contacts_count ?? $client->contacts()->count()) <span style="margin-left:auto; font-size:11px; font-family:var(--font-mono); color:var(--accent-green); font-weight:700;">{{ $client->contacts_count ?? $client->contacts()->count() }}</span> @endif
            </a>
            <a href="{{ route('nexcore.clients.show.banking', $client->id) }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.show.banking*') ? 'active' : '' }}">
                <i class="fas fa-landmark"></i> Banking
                @if($client->bank_accounts_count ?? $client->bankAccounts()->count()) <span style="margin-left:auto; font-size:11px; font-family:var(--font-mono); color:var(--accent-green); font-weight:700;">{{ $client->bank_accounts_count ?? $client->bankAccounts()->count() }}</span> @endif
            </a>
            <a href="{{ route('nexcore.clients.show.directors', $client->id) }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.show.directors*') ? 'active' : '' }}">
                <i class="fas fa-user-tie"></i> Directors
                @if($client->directors_count ?? $client->directors()->count()) <span style="margin-left:auto; font-size:11px; font-family:var(--font-mono); color:var(--accent-green); font-weight:700;">{{ $client->directors_count ?? $client->directors()->count() }}</span> @endif
            </a>
        </nav>

        <nav class="sl-nav-section">
            <div class="sl-nav-label">Compliance</div>
            <a href="{{ route('nexcore.clients.show.sars', $client->id) }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.show.sars*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i> SARS Returns
                @if($client->sarsReturns()->count()) <span style="margin-left:auto; font-size:11px; font-family:var(--font-mono); color:var(--accent-green); font-weight:700;">{{ $client->sarsReturns()->count() }}</span> @endif
            </a>
            <a href="{{ route('nexcore.clients.show.cipc', $client->id) }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.show.cipc*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-check"></i> CIPC Returns
                @if($client->cipcReturns()->count()) <span style="margin-left:auto; font-size:11px; font-family:var(--font-mono); color:var(--accent-green); font-weight:700;">{{ $client->cipcReturns()->count() }}</span> @endif
            </a>
            <a href="{{ route('nexcore.clients.show.financials', $client->id) }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.show.financials*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Financials
                @if($client->financials()->count()) <span style="margin-left:auto; font-size:11px; font-family:var(--font-mono); color:var(--accent-green); font-weight:700;">{{ $client->financials()->count() }}</span> @endif
            </a>
        </nav>

        <nav class="sl-nav-section">
            <div class="sl-nav-label">Management</div>
            <a href="{{ route('nexcore.clients.show.documents', $client->id) }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.show.documents*') ? 'active' : '' }}">
                <i class="fas fa-folder-open"></i> Documents
                @if($client->documents()->count()) <span style="margin-left:auto; font-size:11px; font-family:var(--font-mono); color:var(--accent-green); font-weight:700;">{{ $client->documents()->count() }}</span> @endif
            </a>
            <a href="{{ route('nexcore.clients.show.tasks', $client->id) }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.show.tasks*') ? 'active' : '' }}">
                <i class="fas fa-tasks"></i> Tasks
                @if($client->tasks()->count()) <span style="margin-left:auto; font-size:11px; font-family:var(--font-mono); color:var(--accent-green); font-weight:700;">{{ $client->tasks()->count() }}</span> @endif
            </a>
            <a href="{{ route('nexcore.clients.show.meetings', $client->id) }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.show.meetings*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> Meetings
                @if($client->meetings()->count()) <span style="margin-left:auto; font-size:11px; font-family:var(--font-mono); color:var(--accent-green); font-weight:700;">{{ $client->meetings()->count() }}</span> @endif
            </a>
            <a href="{{ route('nexcore.clients.show.alerts', $client->id) }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.show.alerts*') ? 'active' : '' }}">
                <i class="fas fa-bell"></i> Alerts
                @if($client->alerts()->where('is_dismissed', false)->count()) <span style="margin-left:auto; font-size:11px; font-family:var(--font-mono); color:var(--accent-red); font-weight:700;">{{ $client->alerts()->where('is_dismissed', false)->count() }}</span> @endif
            </a>
            <a href="{{ route('nexcore.clients.show.audit', $client->id) }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.show.audit*') ? 'active' : '' }}">
                <i class="fas fa-history"></i> Audit Trail
            </a>
        </nav>

        <nav class="sl-nav-section">
            <div class="sl-nav-label">Payroll</div>
            <a href="{{ route('nexcore.clients.show.payroll.employees', $client->id) }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.show.payroll.employees*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Employees
                @if($client->employees()->count()) <span style="margin-left:auto; font-size:11px; font-family:var(--font-mono); color:var(--accent-green); font-weight:700;">{{ $client->employees()->count() }}</span> @endif
            </a>
            <a href="{{ route('nexcore.clients.show.payroll.periods', $client->id) }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.show.payroll.periods*') ? 'active' : '' }}">
                <i class="fas fa-calendar-week"></i> Pay Periods
                @if($client->payPeriods()->count()) <span style="margin-left:auto; font-size:11px; font-family:var(--font-mono); color:var(--accent-green); font-weight:700;">{{ $client->payPeriods()->count() }}</span> @endif
            </a>
            <a href="{{ route('nexcore.clients.show.payroll.payslips', $client->id) }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.show.payroll.payslips*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i> Payslips
                @if($client->payslips()->count()) <span style="margin-left:auto; font-size:11px; font-family:var(--font-mono); color:var(--accent-green); font-weight:700;">{{ $client->payslips()->count() }}</span> @endif
            </a>
            <a href="{{ route('nexcore.clients.show.payroll.mibco', $client->id) }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.show.payroll.mibco*') ? 'active' : '' }}">
                <i class="fas fa-building-columns"></i> MIBCO
                @if($client->mibcoContributions()->count()) <span style="margin-left:auto; font-size:11px; font-family:var(--font-mono); color:var(--accent-green); font-weight:700;">{{ $client->mibcoContributions()->count() }}</span> @endif
            </a>
            <a href="{{ route('nexcore.clients.show.payroll.reports', $client->id) }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.show.payroll.reports*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i> Reports
            </a>
        </nav>

        <nav class="sl-nav-section">
            <div class="sl-nav-label">Accounting</div>
            <a href="{{ route('nexcore.clients.show.accounting.dashboard', $client->id) }}" class="sl-nav-item" style="color:#f59e0b; font-weight:700;">
                <i class="fas fa-calculator"></i> Open Accounting
                <span style="margin-left:auto;"><i class="fas fa-external-link-alt" style="font-size:10px; color:var(--text-muted);"></i></span>
            </a>
        </nav>

        <div class="ncm-nav-divider"></div>
        @endif

        <nav class="sl-nav-section">
            <div class="sl-nav-label">Practice Management</div>
            <a href="{{ route('nexcore.practices.index') }}" class="sl-nav-item {{ request()->routeIs('nexcore.practices.*') ? 'active' : '' }}">
                <i class="fas fa-briefcase"></i> Practices
            </a>
            <a href="{{ route('nexcore.clients.clerks.index') }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.clerks.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i> Clerks
            </a>
        </nav>

        <nav class="sl-nav-section">
            <div class="sl-nav-label">Company Setup</div>
            <a href="{{ route('nexcore.clients.create') }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.create') ? 'active' : '' }}">
                <i class="fas fa-plus-circle"></i> Add New Company
            </a>
            <a href="{{ route('nexcore.clients.index') }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.index') ? 'active' : '' }}">
                <i class="fas fa-building"></i> All Companies
            </a>
        </nav>

        <nav class="sl-nav-section">
            <div class="sl-nav-label">Navigation</div>
            <a href="{{ route('nexcore.clients.index') }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.index') ? 'active' : '' }}">
                <i class="fas fa-list"></i> All Clients
            </a>
            <a href="/nexcore" class="sl-nav-item {{ request()->is('nexcore') && !request()->is('nexcore/*') ? 'active' : '' }}">
                <i class="fas fa-tasks"></i> Task Center
            </a>
            <a href="{{ route('nexcore.clients.manage-coa') }}" class="sl-nav-item {{ request()->routeIs('nexcore.clients.manage-coa') ? 'active' : '' }}">
                <i class="fas fa-sitemap"></i> Manage COA
            </a>
            <a href="{{ route('system-settings.dashboard') }}" class="sl-nav-item">
                <i class="fas fa-cog"></i> System Settings
            </a>
            <a href="/home" class="sl-nav-item">
                <i class="fas fa-arrow-left"></i> Back to CIMS
            </a>
        </nav>

        <div class="ncm-nav-divider"></div>

        <nav class="sl-nav-section">
            <div class="sl-nav-label">Account</div>
            @if(auth()->check())
                @php
                    $userAvatar = auth()->user()->avatar ?? '';
                    $hasAvatar = $userAvatar && $userAvatar !== 'no_avatar.png' && strpos($userAvatar, 'default_avatar') === false;
                    if ($hasAvatar) {
                        $avatarUrl = (strpos($userAvatar, 'http') === 0 || strpos($userAvatar, '/') === 0) ? $userAvatar : asset('storage/avatars/' . $userAvatar);
                    } else {
                        $avatarUrl = '';
                    }
                    $userInitials = strtoupper(substr(auth()->user()->first_name ?? 'U', 0, 1)) . strtoupper(substr(auth()->user()->last_name ?? '', 0, 1));
                @endphp
                <div style="padding:8px 16px 6px; display:flex; align-items:center; gap:10px;">
                    @if($hasAvatar)
                        <img src="{{ $avatarUrl }}" alt="avatar" style="width:32px; height:32px; border-radius:50%; object-fit:cover; border:2px solid var(--accent-cyan); flex-shrink:0;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div style="width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg, var(--accent-cyan), var(--accent-blue)); display:none; align-items:center; justify-content:center; font-weight:700; font-size:12px; color:#fff; flex-shrink:0;">
                            {{ $userInitials }}
                        </div>
                    @else
                        <div style="width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg, var(--accent-cyan), var(--accent-blue)); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:12px; color:#fff; flex-shrink:0;">
                            {{ $userInitials }}
                        </div>
                    @endif
                    <div style="min-width:0;">
                        <div style="font-weight:600; font-size:12px; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                        <div style="font-size:10px; color:var(--text-muted);">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <a href="/logout" class="sl-nav-item" style="color:var(--accent-red);" onclick="event.preventDefault(); document.getElementById('nx-logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <form id="nx-logout-form" action="/logout" method="POST" style="display:none;">@csrf</form>
            @else
                <a href="/login" class="sl-nav-item" style="color:var(--accent-green);">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            @endif
        </nav>
    </aside>

    <main class="sl-main">
        <header class="sl-topbar">
            <div class="sl-topbar-left">
                <span class="sl-topbar-module">Client Manager</span>
                <span class="sl-topbar-sep">/</span>
                <span class="sl-topbar-module-name">@yield('page_heading', 'Clients')</span>
            </div>
            <div class="sl-topbar-right">
                <div class="sl-topbar-datetime">
                    <i class="far fa-clock"></i>
                    <span id="ss-clock">--:--</span>
                    <span class="sl-topbar-sep">|</span>
                    <i class="far fa-calendar-alt"></i>
                    <span id="ss-date">--</span>
                </div>
            </div>
        </header>

        <section class="sl-content">
            @if(session('success'))
                <div class="sl-verdict accept sl-mb-md" style="padding:14px 20px;">
                    <div class="sl-verdict-icon" style="width:32px;height:32px;font-size:16px;"><i class="fas fa-check"></i></div>
                    <div>
                        <div class="sl-verdict-text" style="font-size:15px;">{{ session('success') }}</div>
                    </div>
                </div>
            @endif

            @yield('content')
        </section>
    </main>
</div>

<script>
function updateClock() {
    var now = new Date();
    var h = String(now.getHours()).padStart(2,'0');
    var m = String(now.getMinutes()).padStart(2,'0');
    document.getElementById('ss-clock').textContent = h + ':' + m;
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    document.getElementById('ss-date').textContent = now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
}
updateClock();
setInterval(updateClock, 30000);
</script>
@stack('scripts')
</body>
</html>