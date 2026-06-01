<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="pf-console sl-animate d2 sl-mb-md">
    <div class="pf-console-header">
        <div class="pf-console-title">
            <div class="pf-title-icon"><i class="fas fa-satellite-dish"></i></div>
            <div>
                <div class="pf-title-main">Reporting Period</div>
                <div class="pf-title-sub">Financial Period Selector</div>
            </div>
        </div>
        <div class="pf-period-badge">
            <div class="pf-badge-dot"></div>
            <i class="fas fa-clock"></i>
            <span>{{ \Carbon\Carbon::parse($fromDate)->format('j M Y') }} &mdash; {{ \Carbon\Carbon::parse($toDate)->format('j M Y') }}</span>
        </div>
    </div>

    <form method="GET" id="periodForm">
        <input type="hidden" name="preset" id="pfPreset" value="{{ $preset ?? 'this_month' }}">
        <input type="hidden" name="from_date" id="pfFromHidden" value="{{ $fromDate }}">
        <input type="hidden" name="to_date" id="pfToHidden" value="{{ $toDate }}">

        <div class="pf-grid">
            <div class="pf-row">
                <button type="button" class="pf-btn pf-preset {{ ($preset ?? 'this_month') === 'this_month' ? 'active' : '' }}" data-preset="this_month" style="--pf-i:0"><span class="pf-btn-border"></span><span class="pf-btn-glow"></span><span class="pf-btn-content"><i class="fas fa-calendar-day"></i><span class="pf-label">This Month</span></span></button>
                <button type="button" class="pf-btn pf-preset {{ ($preset ?? '') === 'last_month' ? 'active' : '' }}" data-preset="last_month" style="--pf-i:1"><span class="pf-btn-border"></span><span class="pf-btn-glow"></span><span class="pf-btn-content"><i class="fas fa-calendar-minus"></i><span class="pf-label">Last Month</span></span></button>
                <button type="button" class="pf-btn pf-preset {{ ($preset ?? '') === 'q1' ? 'active' : '' }}" data-preset="q1" style="--pf-i:2"><span class="pf-btn-border"></span><span class="pf-btn-glow"></span><span class="pf-btn-content"><i class="fas fa-dice-one"></i><span class="pf-label">Q1</span><span class="pf-sub">Mar-May</span></span></button>
                <button type="button" class="pf-btn pf-preset {{ ($preset ?? '') === 'q2' ? 'active' : '' }}" data-preset="q2" style="--pf-i:3"><span class="pf-btn-border"></span><span class="pf-btn-glow"></span><span class="pf-btn-content"><i class="fas fa-dice-two"></i><span class="pf-label">Q2</span><span class="pf-sub">Jun-Aug</span></span></button>
                <button type="button" class="pf-btn pf-preset {{ ($preset ?? '') === 'q3' ? 'active' : '' }}" data-preset="q3" style="--pf-i:4"><span class="pf-btn-border"></span><span class="pf-btn-glow"></span><span class="pf-btn-content"><i class="fas fa-dice-three"></i><span class="pf-label">Q3</span><span class="pf-sub">Sep-Nov</span></span></button>
                <button type="button" class="pf-btn pf-preset {{ ($preset ?? '') === 'q4' ? 'active' : '' }}" data-preset="q4" style="--pf-i:5"><span class="pf-btn-border"></span><span class="pf-btn-glow"></span><span class="pf-btn-content"><i class="fas fa-dice-four"></i><span class="pf-label">Q4</span><span class="pf-sub">Dec-Feb</span></span></button>
            </div>
            <div class="pf-row">
                <button type="button" class="pf-btn pf-preset {{ ($preset ?? '') === 'h1' ? 'active' : '' }}" data-preset="h1" style="--pf-i:6"><span class="pf-btn-border"></span><span class="pf-btn-glow"></span><span class="pf-btn-content"><i class="fas fa-step-forward"></i><span class="pf-label">1st Half</span><span class="pf-sub">Mar-Aug</span></span></button>
                <button type="button" class="pf-btn pf-preset {{ ($preset ?? '') === 'h2' ? 'active' : '' }}" data-preset="h2" style="--pf-i:7"><span class="pf-btn-border"></span><span class="pf-btn-glow"></span><span class="pf-btn-content"><i class="fas fa-fast-forward"></i><span class="pf-label">2nd Half</span><span class="pf-sub">Sep-Feb</span></span></button>
                <button type="button" class="pf-btn pf-preset {{ ($preset ?? '') === 'this_year' ? 'active' : '' }}" data-preset="this_year" style="--pf-i:8"><span class="pf-btn-border"></span><span class="pf-btn-glow"></span><span class="pf-btn-content"><i class="fas fa-calendar"></i><span class="pf-label">This Year</span></span></button>
                <button type="button" class="pf-btn pf-preset {{ ($preset ?? '') === 'last_year' ? 'active' : '' }}" data-preset="last_year" style="--pf-i:9"><span class="pf-btn-border"></span><span class="pf-btn-glow"></span><span class="pf-btn-content"><i class="fas fa-calendar-times"></i><span class="pf-label">Last Year</span></span></button>
                <button type="button" class="pf-btn pf-preset {{ ($preset ?? '') === 'ytd' ? 'active' : '' }}" data-preset="ytd" style="--pf-i:10"><span class="pf-btn-border"></span><span class="pf-btn-glow"></span><span class="pf-btn-content"><i class="fas fa-chart-line"></i><span class="pf-label">YTD</span></span></button>
                <button type="button" class="pf-btn pf-preset {{ ($preset ?? '') === 'all' ? 'active' : '' }}" data-preset="all" style="--pf-i:11"><span class="pf-btn-border"></span><span class="pf-btn-glow"></span><span class="pf-btn-content"><i class="fas fa-infinity"></i><span class="pf-label">All Time</span></span></button>
                <button type="button" class="pf-btn pf-preset {{ ($preset ?? '') === 'custom' ? 'active' : '' }}" data-preset="custom" style="--pf-i:12"><span class="pf-btn-border"></span><span class="pf-btn-glow"></span><span class="pf-btn-content"><i class="fas fa-sliders-h"></i><span class="pf-label">Custom</span></span></button>
            </div>
        </div>

        <div id="pfCustomRow" style="display:{{ ($preset ?? '') === 'custom' ? 'flex' : 'none' }}; align-items:flex-end; gap:14px; margin-top:16px; padding-top:16px; border-top:1px solid rgba(245,158,11,0.15);">
            <div class="sl-field" style="flex:0 0 180px;">
                <label>From Date</label>
                <input type="text" id="pfFrom" value="{{ \Carbon\Carbon::parse($fromDate)->format('j M Y') }}" readonly>
            </div>
            <div class="sl-field" style="flex:0 0 180px;">
                <label>To Date</label>
                <input type="text" id="pfTo" value="{{ \Carbon\Carbon::parse($toDate)->format('j M Y') }}" readonly>
            </div>
            <button type="submit" class="sl-badge-btn sl-badge-btn-amber" style="margin-bottom:1px;">
                <i class="fas fa-sync-alt"></i> Apply Filter
            </button>
        </div>
    </form>
</div>

@push('scripts')
<style>
    /* ═══════════════════════════════════════════════════════
       HOLOGRAPHIC COMMAND CONSOLE — Period Filter
       ═══════════════════════════════════════════════════════ */

    .pf-console {
        background: linear-gradient(168deg, rgba(15,19,32,0.95), rgba(10,14,26,0.98));
        border: 1px solid rgba(245,158,11,0.15);
        border-radius: 16px;
        padding: 0;
        overflow: hidden;
        position: relative;
        backdrop-filter: blur(20px);
        margin-bottom: 20px;
    }
    .pf-console::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse at 20% 0%, rgba(245,158,11,0.06) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 100%, rgba(59,130,246,0.04) 0%, transparent 50%);
        pointer-events: none;
        z-index: 0;
    }
    .pf-console::after {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(245,158,11,0.5) 20%, rgba(251,191,36,0.8) 50%, rgba(245,158,11,0.5) 80%, transparent);
        z-index: 1;
    }

    /* ─── Header ─── */
    .pf-console-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 28px 16px;
        position: relative;
        z-index: 2;
    }
    .pf-console-title {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .pf-title-icon {
        width: 40px; height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(6,182,212,0.15), rgba(6,182,212,0.06));
        border: 1px solid rgba(6,182,212,0.35);
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; color: #06b6d4;
        box-shadow: 0 0 20px rgba(6,182,212,0.12), inset 0 0 12px rgba(6,182,212,0.05);
        animation: pfIconPulse 3s ease-in-out infinite;
    }
    .pf-title-main {
        font-size: 16px; font-weight: 800; color: #ffffff;
        letter-spacing: 1.5px; text-transform: uppercase;
        text-shadow: 0 0 14px rgba(255,255,255,0.12);
    }
    .pf-title-sub {
        font-size: 11px; color: rgba(148,163,184,0.6);
        letter-spacing: 2px; text-transform: uppercase; margin-top: 2px;
        font-weight: 600;
    }

    /* ─── Period Badge (animated border shimmer) ─── */
    .pf-period-badge {
        display: inline-flex; align-items: center; gap: 10px;
        padding: 10px 22px;
        border-radius: 40px;
        font-size: 15px; font-weight: 700;
        letter-spacing: 0.8px; text-transform: uppercase;
        color: #60a5fa;
        position: relative;
        background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(59,130,246,0.04));
        border: 1px solid rgba(59,130,246,0.3);
        overflow: hidden;
        text-shadow: 0 0 14px rgba(59,130,246,0.4);
        box-shadow: 0 0 20px rgba(59,130,246,0.12), 0 4px 16px rgba(0,0,0,0.3);
    }
    .pf-period-badge::before {
        content: '';
        position: absolute;
        inset: -1px;
        border-radius: 40px;
        background: conic-gradient(from var(--pf-badge-angle, 0deg), transparent, rgba(59,130,246,0.6), transparent, rgba(96,165,250,0.4), transparent);
        opacity: 0.4;
        animation: pfBadgeSpin 4s linear infinite;
        z-index: -1;
    }
    .pf-period-badge i {
        font-size: 12px;
        filter: drop-shadow(0 0 6px rgba(59,130,246,0.7));
    }
    .pf-badge-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 8px rgba(34,197,94,0.6), 0 0 16px rgba(34,197,94,0.3);
        animation: pfDotPulse 2s ease-in-out infinite;
    }

    /* ─── Grid ─── */
    .pf-grid {
        padding: 4px 28px 24px;
        position: relative;
        z-index: 2;
    }
    .pf-row {
        display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap;
    }
    .pf-row:last-child { margin-bottom: 0; }

    /* ═══════════════════════════════════════════════════════
       BUTTON — The Holographic Tile
       ═══════════════════════════════════════════════════════ */
    .pf-btn {
        position: relative;
        display: inline-flex; align-items: center;
        padding: 0; margin: 0;
        border: none; background: none;
        cursor: pointer; user-select: none;
        border-radius: 12px;
        animation: pfTileBootUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
        animation-delay: calc(var(--pf-i) * 0.06s);
        flex-shrink: 0;
    }

    /* Animated gradient border */
    .pf-btn-border {
        position: absolute; inset: 0;
        border-radius: 12px;
        padding: 1px;
        background: conic-gradient(
            from calc(var(--pf-i) * 30deg),
            rgba(245,158,11,0.15),
            rgba(217,119,6,0.4),
            rgba(245,158,11,0.15),
            rgba(251,191,36,0.3),
            rgba(245,158,11,0.15)
        );
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        transition: all 0.4s;
        animation: pfBorderRotate 8s linear infinite;
        animation-delay: calc(var(--pf-i) * -0.6s);
    }

    /* Inner glow layer */
    .pf-btn-glow {
        position: absolute; inset: 1px;
        border-radius: 11px;
        background:
            radial-gradient(ellipse at 30% 20%, rgba(245,158,11,0.08), transparent 60%),
            radial-gradient(ellipse at 70% 80%, rgba(217,119,6,0.04), transparent 50%),
            linear-gradient(160deg, rgba(20,24,40,0.95), rgba(12,16,28,0.98));
        backdrop-filter: blur(12px);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
    }

    /* Content */
    .pf-btn-content {
        position: relative; z-index: 2;
        display: inline-flex; align-items: center; gap: 8px;
        padding: 11px 20px;
        font-family: var(--font-body);
        transition: all 0.4s;
    }
    .pf-btn-content i {
        font-size: 13px;
        color: #d97706;
        transition: all 0.4s;
        filter: drop-shadow(0 0 4px rgba(245,158,11,0.2));
    }
    .pf-label {
        font-size: 13px; font-weight: 700;
        color: #e2a32e;
        letter-spacing: 0.5px;
        text-shadow: 0 0 12px rgba(245,158,11,0.15);
        transition: all 0.4s;
    }
    .pf-sub {
        font-size: 10px; font-weight: 500;
        color: rgba(245,158,11,0.35);
        letter-spacing: 0.3px;
        transition: all 0.4s;
    }

    /* ─── Hover: Reactor Ignition ─── */
    .pf-btn:hover .pf-btn-border {
        background: conic-gradient(
            from calc(var(--pf-i) * 30deg),
            rgba(245,158,11,0.3),
            rgba(251,191,36,0.8),
            rgba(245,158,11,0.3),
            rgba(253,224,71,0.6),
            rgba(245,158,11,0.3)
        );
    }
    .pf-btn:hover .pf-btn-glow {
        background:
            radial-gradient(ellipse at 30% 20%, rgba(245,158,11,0.18), transparent 55%),
            radial-gradient(ellipse at 70% 80%, rgba(251,191,36,0.1), transparent 45%),
            radial-gradient(ellipse at 50% 50%, rgba(245,158,11,0.06), transparent 70%),
            linear-gradient(160deg, rgba(25,28,45,0.95), rgba(15,18,32,0.98));
        box-shadow:
            0 0 30px rgba(245,158,11,0.15),
            0 0 60px rgba(245,158,11,0.06),
            inset 0 0 30px rgba(245,158,11,0.04);
    }
    .pf-btn:hover {
        transform: translateY(-3px) scale(1.02);
        z-index: 5;
    }
    .pf-btn:hover .pf-btn-content i {
        color: #fbbf24;
        filter: drop-shadow(0 0 10px rgba(251,191,36,0.8));
        transform: scale(1.2);
    }
    .pf-btn:hover .pf-label {
        color: #fef3c7;
        text-shadow: 0 0 20px rgba(251,191,36,0.5);
    }
    .pf-btn:hover .pf-sub {
        color: rgba(251,191,36,0.6);
    }
    .pf-btn:active {
        transform: translateY(-1px) scale(0.98);
    }

    /* ═══════════════════════════════════════════════════════
       ACTIVE STATE — Electric Blue Reactor Core
       ═══════════════════════════════════════════════════════ */
    .pf-btn.active .pf-btn-border {
        background: conic-gradient(
            from calc(var(--pf-i) * 30deg),
            rgba(59,130,246,0.2),
            rgba(96,165,250,0.7),
            rgba(59,130,246,0.2),
            rgba(147,187,253,0.5),
            rgba(59,130,246,0.2)
        );
        animation: pfBorderRotate 4s linear infinite;
    }
    .pf-btn.active .pf-btn-glow {
        background:
            radial-gradient(ellipse at 30% 20%, rgba(59,130,246,0.2), transparent 55%),
            radial-gradient(ellipse at 70% 80%, rgba(96,165,250,0.12), transparent 45%),
            radial-gradient(ellipse at 50% 50%, rgba(59,130,246,0.06), transparent 70%),
            linear-gradient(160deg, rgba(15,22,48,0.97), rgba(10,15,35,0.99));
        box-shadow:
            0 0 24px rgba(59,130,246,0.18),
            0 0 48px rgba(59,130,246,0.08),
            inset 0 0 24px rgba(59,130,246,0.05);
        animation: pfActivePulse 3s ease-in-out infinite;
    }
    .pf-btn.active .pf-btn-content i {
        color: #93bbfd;
        filter: drop-shadow(0 0 8px rgba(96,165,250,0.7));
    }
    .pf-btn.active .pf-label {
        color: #bfdbfe;
        text-shadow: 0 0 16px rgba(59,130,246,0.4);
    }
    .pf-btn.active .pf-sub {
        color: rgba(96,165,250,0.5);
    }

    /* Active + Hover */
    .pf-btn.active:hover .pf-btn-border {
        background: conic-gradient(
            from calc(var(--pf-i) * 30deg),
            rgba(59,130,246,0.3),
            rgba(147,187,253,0.9),
            rgba(59,130,246,0.3),
            rgba(191,219,254,0.7),
            rgba(59,130,246,0.3)
        );
    }
    .pf-btn.active:hover .pf-btn-glow {
        box-shadow:
            0 0 36px rgba(59,130,246,0.25),
            0 0 72px rgba(59,130,246,0.1),
            inset 0 0 36px rgba(59,130,246,0.06);
    }
    .pf-btn.active:hover .pf-btn-content i {
        filter: drop-shadow(0 0 14px rgba(147,187,253,0.9));
    }
    .pf-btn.active:hover .pf-label {
        color: #e0edff;
        text-shadow: 0 0 24px rgba(96,165,250,0.6);
    }

    /* ═══════════════════════════════════════════════════════
       KEYFRAMES
       ═══════════════════════════════════════════════════════ */

    @keyframes pfTileBootUp {
        0% {
            opacity: 0;
            transform: translateY(16px) scale(0.85) rotateX(8deg);
            filter: brightness(0.3);
        }
        60% {
            opacity: 1;
            filter: brightness(1.2);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1) rotateX(0);
            filter: brightness(1);
        }
    }

    @keyframes pfBorderRotate {
        to { background-position: 360deg; }
    }

    @supports (background: conic-gradient(from 0deg, red, blue)) {
        @keyframes pfBorderRotate {
            0% { filter: hue-rotate(0deg); }
            100% { filter: hue-rotate(15deg); }
        }
    }

    @keyframes pfActivePulse {
        0%, 100% {
            box-shadow:
                0 0 24px rgba(59,130,246,0.18),
                0 0 48px rgba(59,130,246,0.08),
                inset 0 0 24px rgba(59,130,246,0.05);
        }
        50% {
            box-shadow:
                0 0 32px rgba(59,130,246,0.25),
                0 0 64px rgba(59,130,246,0.12),
                inset 0 0 32px rgba(59,130,246,0.08);
        }
    }

    @keyframes pfIconPulse {
        0%, 100% { box-shadow: 0 0 20px rgba(6,182,212,0.12), inset 0 0 12px rgba(6,182,212,0.05); }
        50% { box-shadow: 0 0 28px rgba(6,182,212,0.22), inset 0 0 16px rgba(6,182,212,0.1); }
    }

    @keyframes pfBadgeSpin {
        to { --pf-badge-angle: 360deg; }
    }

    @keyframes pfDotPulse {
        0%, 100% { opacity: 1; box-shadow: 0 0 8px rgba(34,197,94,0.6), 0 0 16px rgba(34,197,94,0.3); }
        50% { opacity: 0.6; box-shadow: 0 0 4px rgba(34,197,94,0.4), 0 0 8px rgba(34,197,94,0.2); }
    }

    @property --pf-badge-angle {
        syntax: '<angle>';
        initial-value: 0deg;
        inherits: false;
    }

    /* ─── Responsive ─── */
    @media (max-width: 900px) {
        .pf-console-header { flex-direction: column; gap: 12px; align-items: flex-start; }
        .pf-btn-content { padding: 9px 14px; }
        .pf-label { font-size: 12px; }
    }
</style>
<script>
(function() {
    var presets = document.querySelectorAll('.pf-preset');
    var fromHidden = document.getElementById('pfFromHidden');
    var toHidden = document.getElementById('pfToHidden');
    var presetInput = document.getElementById('pfPreset');
    var form = document.getElementById('periodForm');
    var customRow = document.getElementById('pfCustomRow');

    function parseISO(str) {
        var p = str.split('-');
        return new Date(parseInt(p[0]), parseInt(p[1]) - 1, parseInt(p[2]));
    }

    var fpFrom = flatpickr('#pfFrom', {
        dateFormat: 'j M Y',
        defaultDate: parseISO('{{ $fromDate }}'),
        onChange: function(selectedDates) {
            if (selectedDates[0]) {
                fromHidden.value = formatISO(selectedDates[0]);
            }
        }
    });

    var fpTo = flatpickr('#pfTo', {
        dateFormat: 'j M Y',
        defaultDate: parseISO('{{ $toDate }}'),
        onChange: function(selectedDates) {
            if (selectedDates[0]) {
                toHidden.value = formatISO(selectedDates[0]);
            }
        }
    });

    function formatISO(d) {
        return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
    }

    function getFY() {
        var now = new Date();
        var y = now.getFullYear();
        var m = now.getMonth();
        return m >= 2 ? y : y - 1;
    }

    function getPresetDates(key) {
        var now = new Date();
        var y = now.getFullYear();
        var m = now.getMonth();
        var fy = getFY();

        switch(key) {
            case 'this_month':
                return { from: new Date(y, m, 1), to: new Date(y, m + 1, 0) };
            case 'last_month':
                return { from: new Date(y, m - 1, 1), to: new Date(y, m, 0) };
            case 'q1':
                return { from: new Date(fy, 2, 1), to: new Date(fy, 5, 0) };
            case 'q2':
                return { from: new Date(fy, 5, 1), to: new Date(fy, 8, 0) };
            case 'q3':
                return { from: new Date(fy, 8, 1), to: new Date(fy, 11, 0) };
            case 'q4':
                return { from: new Date(fy, 11, 1), to: new Date(fy + 1, 2, 0) };
            case 'h1':
                return { from: new Date(fy, 2, 1), to: new Date(fy, 8, 0) };
            case 'h2':
                return { from: new Date(fy, 8, 1), to: new Date(fy + 1, 2, 0) };
            case 'this_year':
                return { from: new Date(fy, 2, 1), to: new Date(fy + 1, 2, 0) };
            case 'last_year':
                return { from: new Date(fy - 1, 2, 1), to: new Date(fy, 2, 0) };
            case 'ytd':
                return { from: new Date(fy, 2, 1), to: now };
            case 'all':
                return { from: new Date(2000, 0, 1), to: new Date(y + 1, 11, 31) };
            default:
                return null;
        }
    }

    presets.forEach(function(btn) {
        btn.addEventListener('click', function() {
            presets.forEach(function(b) { b.classList.remove('active'); });
            btn.classList.add('active');
            var key = btn.dataset.preset;
            presetInput.value = key;

            if (key === 'custom') {
                customRow.style.display = 'flex';
                return;
            }

            customRow.style.display = 'none';

            var dates = getPresetDates(key);
            if (dates) {
                fromHidden.value = formatISO(dates.from);
                toHidden.value = formatISO(dates.to);
                fpFrom.setDate(dates.from, false);
                fpTo.setDate(dates.to, false);
                form.submit();
            }
        });
    });
})();
</script>
@endpush
