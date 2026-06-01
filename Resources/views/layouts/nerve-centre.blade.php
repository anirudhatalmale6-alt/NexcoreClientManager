<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NexCore | @yield('title', 'Client Nerve Centre')</title>
    <link rel="shortcut icon" type="image/png" href="/public/smartdash/images/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/public/css/nexcore_gl_master.css?v={{ time() }}" rel="stylesheet">
    <style>
    html, body {
        margin: 0;
        padding: 0;
        height: 100%;
        overflow: hidden;
        background: var(--bg-base, #0a0e1a);
    }
    .nx-nerve-centre-shell {
        display: flex;
        height: 100vh;
        width: 100vw;
        overflow: hidden;
        position: relative;
    }
    .nx-nc-content-area {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        height: 100vh;
        overflow: hidden;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .nx-nc-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 48px;
        min-height: 48px;
        padding: 0 24px;
        background: rgba(10,14,26,0.6);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(255,255,255,0.04);
        z-index: 40;
    }
    .nx-nc-topbar-left {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: rgba(255,255,255,0.5);
    }
    .nx-nc-topbar-left .nx-nc-brand {
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    .nx-nc-topbar-left .nx-nc-brand svg { opacity: 0.7; }
    .nx-nc-topbar-left .nx-nc-brand-name {
        font-weight: 700;
        font-size: 13px;
        color: rgba(255,255,255,0.6);
        letter-spacing: 0.5px;
    }
    .nx-nc-topbar-sep {
        color: rgba(255,255,255,0.15);
        font-weight: 300;
    }
    .nx-nc-topbar-module {
        font-weight: 600;
        color: rgba(255,255,255,0.4);
    }
    .nx-nc-topbar-page {
        font-weight: 600;
        color: rgba(255,255,255,0.7);
    }
    .nx-nc-topbar-right {
        display: flex;
        align-items: center;
        gap: 16px;
        font-size: 12px;
        color: rgba(255,255,255,0.4);
        font-family: var(--font-mono, 'Courier New', monospace);
    }
    .nx-nc-topbar-right i { font-size: 11px; opacity: 0.5; }
    .nx-nc-scroll {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,0.08) transparent;
    }
    .nx-nc-scroll::-webkit-scrollbar { width: 6px; }
    .nx-nc-scroll::-webkit-scrollbar-track { background: transparent; }
    .nx-nc-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 3px; }
    .nx-nc-scroll::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.15); }
    .nx-nc-body {
        padding: 20px 28px 40px;
    }

    @@media (max-width: 900px) {
        .nx-nerve-centre-shell { flex-direction: column; }
        .nx-nc-content-area { height: auto; min-height: 0; flex: 1; }
        .nx-nc-topbar { padding: 0 16px; }
        .nx-nc-body { padding: 16px; }
    }

    /* ═══ Shared Nerve Centre Sidebar Styles ═══ */
    .nx-sidebar { width:270px; min-width:270px; height:100vh; background:linear-gradient(180deg,#080b16 0%,#0d1220 40%,#0f1528 100%); border-right:1px solid rgba(255,255,255,0.06); overflow:hidden; transition:width 0.35s cubic-bezier(0.4,0,0.2,1),min-width 0.35s cubic-bezier(0.4,0,0.2,1); z-index:50; display:flex; flex-direction:column; flex-shrink:0; position:relative; }
    .nx-sidebar::before { content:''; position:absolute; inset:0; background:linear-gradient(180deg,rgba(139,92,246,0.04) 0%,rgba(20,184,166,0.02) 50%,rgba(217,119,6,0.015) 100%); pointer-events:none; }
    .nx-sidebar::after { content:''; position:absolute; top:0; right:0; width:1px; height:100%; background:linear-gradient(180deg,rgba(139,92,246,0.15),rgba(20,184,166,0.1) 50%,rgba(217,119,6,0.08)); }
    .nx-sidebar-inner { display:flex; flex-direction:column; height:100%; position:relative; z-index:1; }
    .nx-sb-brand { display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid rgba(255,255,255,0.04); }
    .nx-sb-brand-link { display:flex; align-items:center; gap:10px; text-decoration:none; }
    .nx-sb-brand-text { font-size:15px; font-weight:800; color:rgba(255,255,255,0.85); letter-spacing:1.5px; text-transform:uppercase; font-family:'Montserrat',var(--font-sans); }
    .nx-sb-client { display:flex; align-items:center; gap:12px; padding:18px 16px; border-bottom:1px solid rgba(255,255,255,0.06); position:relative; overflow:hidden; }
    .nx-sb-client::after { content:''; position:absolute; bottom:0; left:16px; right:16px; height:1px; background:linear-gradient(90deg,transparent,rgba(139,92,246,0.2),rgba(20,184,166,0.2),transparent); }
    .nx-sb-avatar { width:40px; height:40px; min-width:40px; border-radius:10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:rgba(255,255,255,0.5); font-family:var(--font-mono); letter-spacing:1px; overflow:hidden; transition:all 0.3s ease; }
    .nx-sb-avatar img { width:100%; height:100%; object-fit:contain; padding:3px; }
    .nx-sb-client-info { flex:1; min-width:0; transition:opacity 0.25s ease,width 0.25s ease; }
    .nx-sb-client-name { font-size:13px; font-weight:700; color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; letter-spacing:0.3px; }
    .nx-sb-client-code { font-size:11px; font-family:var(--font-mono); color:rgba(139,92,246,0.7); font-weight:600; letter-spacing:1px; margin-top:2px; }
    .nx-sb-toggle { width:28px; height:28px; min-width:28px; border-radius:8px; background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); color:rgba(255,255,255,0.4); display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:11px; transition:all 0.25s ease; }
    .nx-sb-toggle:hover { background:rgba(139,92,246,0.12); border-color:rgba(139,92,246,0.3); color:#c4b5fd; }
    .nx-sb-nav { flex:1; overflow-y:auto; overflow-x:hidden; padding:12px 0; scrollbar-width:thin; scrollbar-color:rgba(255,255,255,0.08) transparent; }
    .nx-sb-nav::-webkit-scrollbar { width:4px; }
    .nx-sb-nav::-webkit-scrollbar-track { background:transparent; }
    .nx-sb-nav::-webkit-scrollbar-thumb { background:rgba(255,255,255,0.08); border-radius:2px; }
    .nx-sb-link { display:flex; align-items:center; gap:12px; padding:10px 16px; text-decoration:none; color:rgba(255,255,255,0.6); font-size:13px; font-weight:600; transition:all 0.2s ease; position:relative; border-left:3px solid transparent; }
    .nx-sb-link:hover { color:#fff; background:rgba(255,255,255,0.03); }
    .nx-sb-link.nx-sb-link-active { color:#fff; background:rgba(139,92,246,0.08); border-left-color:#8b5cf6; }
    .nx-sb-link.nx-sb-link-active i { color:#8b5cf6; }
    .nx-sb-link i { width:20px; text-align:center; font-size:14px; transition:all 0.2s ease; }
    .nx-sb-section { margin-top:4px; }
    .nx-sb-heading { display:flex; align-items:center; justify-content:space-between; width:100%; padding:10px 16px; background:none; border:none; border-left:3px solid transparent; cursor:pointer; color:rgba(255,255,255,0.5); font-size:12px; font-weight:700; letter-spacing:0.5px; text-transform:uppercase; transition:all 0.2s ease; font-family:'Poppins',var(--font-sans); }
    .nx-sb-heading:hover { color:rgba(255,255,255,0.8); background:rgba(255,255,255,0.02); }
    .nx-sb-heading-left { display:flex; align-items:center; gap:10px; }
    .nx-sb-heading-left i { width:20px; text-align:center; font-size:14px; }
    .nx-sb-arrow { font-size:10px; transition:transform 0.3s ease; opacity:0.5; }
    .nx-sb-children { overflow:hidden; transition:max-height 0.35s cubic-bezier(0.4,0,0.2,1); max-height:0; }
    .nx-sb-child { display:flex; align-items:center; gap:10px; padding:8px 16px 8px 48px; text-decoration:none; color:rgba(255,255,255,0.45); font-size:12.5px; font-weight:500; transition:all 0.2s ease; position:relative; border-left:3px solid transparent; }
    .nx-sb-child::before { content:''; position:absolute; left:28px; top:50%; width:8px; height:1px; background:rgba(255,255,255,0.08); }
    .nx-sb-child i { width:16px; text-align:center; font-size:12px; opacity:0.6; }
    .nx-sb-child:hover { color:rgba(255,255,255,0.85); background:rgba(255,255,255,0.03); padding-left:52px; }
    .nx-sb-child.nx-sb-link-active { color:#fff; background:rgba(139,92,246,0.06); border-left-color:rgba(139,92,246,0.5); }
    .nx-sb-child.nx-sb-link-active i { opacity:1; }
    .nx-sb-badge { margin-left:auto; font-size:10px; font-weight:700; font-family:var(--font-mono); color:rgba(255,255,255,0.3); background:rgba(255,255,255,0.04); padding:2px 7px; border-radius:8px; min-width:20px; text-align:center; }
    .nx-sb-footer { padding:12px 16px; border-top:1px solid rgba(255,255,255,0.06); position:relative; }
    .nx-sb-footer::before { content:''; position:absolute; top:0; left:16px; right:16px; height:1px; background:linear-gradient(90deg,transparent,rgba(139,92,246,0.15),transparent); }
    .nx-sb-back { display:flex; align-items:center; gap:10px; text-decoration:none; color:rgba(255,255,255,0.4); font-size:12px; font-weight:600; padding:8px 12px; border-radius:8px; transition:all 0.2s ease; }
    .nx-sb-back:hover { color:rgba(255,255,255,0.8); background:rgba(255,255,255,0.04); }
    .nx-sb-back i { font-size:11px; width:20px; text-align:center; }
    /* Collapsed */
    .nx-sb-collapsed .nx-sidebar { width:62px; min-width:62px; }
    .nx-sb-collapsed .nx-sb-text { opacity:0; width:0; overflow:hidden; white-space:nowrap; transition:opacity 0.2s ease,width 0.2s ease; }
    .nx-sb-collapsed .nx-sb-client-info { opacity:0; width:0; overflow:hidden; }
    .nx-sb-collapsed .nx-sb-badge { display:none; }
    .nx-sb-collapsed .nx-sb-children { max-height:0 !important; }
    .nx-sb-collapsed .nx-sb-heading { justify-content:center; padding:10px 0; }
    .nx-sb-collapsed .nx-sb-heading-left { justify-content:center; gap:0; }
    .nx-sb-collapsed .nx-sb-heading-left i { font-size:16px; }
    .nx-sb-collapsed .nx-sb-link { justify-content:center; padding:12px 0; }
    .nx-sb-collapsed .nx-sb-link i { font-size:16px; }
    .nx-sb-collapsed .nx-sb-child { padding:8px 0; justify-content:center; }
    .nx-sb-collapsed .nx-sb-child::before { display:none; }
    .nx-sb-collapsed .nx-sb-back { justify-content:center; padding:8px 0; }
    .nx-sb-collapsed .nx-sb-toggle { margin-left:0; }
    .nx-sb-collapsed .nx-sb-client { justify-content:center; padding:14px 10px; }
    .nx-sb-collapsed .nx-sb-brand { justify-content:center; padding:14px 10px; }
    .nx-sb-collapsed .nx-sb-brand-link { gap:0; }
    .nx-sb-collapsed .nx-sb-brand-text { opacity:0; width:0; overflow:hidden; }
    .nx-sb-collapsed .nx-sb-heading:hover, .nx-sb-collapsed .nx-sb-link:hover, .nx-sb-collapsed .nx-sb-child:hover, .nx-sb-collapsed .nx-sb-back:hover { position:relative; }
    @@media (max-width: 900px) {
        .nx-sidebar { width:100% !important; min-width:100% !important; height:auto; max-height:50vh; border-right:none; border-bottom:1px solid rgba(255,255,255,0.06); }
        .nx-sb-nav { max-height:250px; }
        .nx-sb-collapsed .nx-sidebar { display:none; }
        .nx-sb-text { opacity:1 !important; width:auto !important; }
        .nx-sb-client-info { opacity:1 !important; width:auto !important; }
        .nx-sb-badge { display:inline-flex !important; }
        .nx-sb-brand-text { opacity:1 !important; width:auto !important; }
    }
    </style>
    @stack('styles')
</head>
<body>
<div class="nx-nerve-centre-shell" id="nxNerveCentre">
    @yield('sidebar')

    <div class="nx-nc-content-area">
        <div class="nx-nc-topbar">
            <div class="nx-nc-topbar-left">
                <a href="{{ route('nexcore.clients.index') }}" class="nx-nc-brand" title="Back to NexCore">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <rect x="2" y="2" width="9" height="9" rx="2" fill="#059669"/>
                        <rect x="13" y="2" width="9" height="9" rx="2" fill="#2563eb"/>
                        <rect x="2" y="13" width="9" height="9" rx="2" fill="#d97706"/>
                        <rect x="13" y="13" width="9" height="9" rx="2" fill="#7c3aed"/>
                    </svg>
                    <span class="nx-nc-brand-name">NexCore</span>
                </a>
                <span class="nx-nc-topbar-sep">/</span>
                <span class="nx-nc-topbar-module">@yield('topbar_module', 'Client Manager')</span>
                <span class="nx-nc-topbar-sep">/</span>
                <span class="nx-nc-topbar-page">@yield('topbar_page', 'Dashboard')</span>
            </div>
            <div class="nx-nc-topbar-right">
                <span><i class="far fa-clock"></i> <span id="nxNcClock">--:--</span></span>
                <span class="nx-nc-topbar-sep">|</span>
                <span><i class="far fa-calendar-alt"></i> <span id="nxNcDate">--</span></span>
            </div>
        </div>

        <div class="nx-nc-scroll">
            <div class="nx-nc-body">
                @if(session('success'))
                    <div class="sl-verdict accept sl-mb-md" style="padding:14px 20px;">
                        <div class="sl-verdict-icon" style="width:32px;height:32px;font-size:16px;"><i class="fas fa-check"></i></div>
                        <div>
                            <div class="sl-verdict-text" style="font-size:15px;">{{ session('success') }}</div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    function updateClock() {
        var now = new Date();
        var h = String(now.getHours()).padStart(2,'0');
        var m = String(now.getMinutes()).padStart(2,'0');
        var el = document.getElementById('nxNcClock');
        if (el) el.textContent = h + ':' + m;
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var dEl = document.getElementById('nxNcDate');
        if (dEl) dEl.textContent = now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
    }
    updateClock();
    setInterval(updateClock, 30000);
})();
</script>
@stack('scripts')
</body>
</html>
