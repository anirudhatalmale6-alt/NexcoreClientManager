@extends('nexcore_client_manager::layouts.accounting')

@section('title', $config['title'] . ' - ' . $client->company_name)
@section('page_heading', strtoupper($config['title']))

@push('styles')
<style>
.uc-shell {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 48px 24px 64px;
    position: relative;
    overflow: hidden;
    min-height: 70vh;
}

.uc-grid-bg {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,0.015) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.015) 1px, transparent 1px);
    background-size: 60px 60px;
    mask-image: radial-gradient(ellipse 70% 60% at 50% 30%, black 20%, transparent 70%);
    -webkit-mask-image: radial-gradient(ellipse 70% 60% at 50% 30%, black 20%, transparent 70%);
    pointer-events: none;
}

.uc-radial-wash {
    position: absolute;
    width: 700px;
    height: 700px;
    top: -200px;
    left: 50%;
    transform: translateX(-50%);
    border-radius: 50%;
    background: radial-gradient(circle, var(--uc-glow-from) 0%, transparent 70%);
    opacity: 0.12;
    pointer-events: none;
    animation: uc-breathe 6s ease-in-out infinite;
}

/* ── Sonar beacon ── */
.uc-beacon {
    position: relative;
    width: 130px;
    height: 130px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 36px;
    z-index: 2;
    animation: uc-fadeSlideUp 0.8s cubic-bezier(0.22, 1, 0.36, 1) both;
}

.uc-beacon-core {
    width: 90px;
    height: 90px;
    border-radius: 24px;
    background: linear-gradient(145deg, var(--uc-glow-from), var(--uc-glow-to));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    color: #fff;
    position: relative;
    z-index: 3;
    box-shadow:
        0 0 40px var(--uc-glow-from),
        0 0 80px var(--uc-glow-to),
        inset 0 1px 0 rgba(255,255,255,0.15);
}

.uc-ring {
    position: absolute;
    border: 1.5px solid var(--uc-color);
    border-radius: 50%;
    opacity: 0;
    animation: uc-sonar 3.5s ease-out infinite;
}
.uc-ring:nth-child(2) { width: 110px; height: 110px; animation-delay: 0s; }
.uc-ring:nth-child(3) { width: 150px; height: 150px; animation-delay: 0.7s; }
.uc-ring:nth-child(4) { width: 190px; height: 190px; animation-delay: 1.4s; }

/* ── Title block ── */
.uc-title-block {
    text-align: center;
    z-index: 2;
    animation: uc-fadeSlideUp 0.8s cubic-bezier(0.22, 1, 0.36, 1) 0.15s both;
}

.uc-title {
    font-family: 'Montserrat', sans-serif;
    font-size: 32px;
    font-weight: 800;
    letter-spacing: -0.5px;
    color: var(--text-primary);
    margin: 0 0 10px;
    line-height: 1.15;
}

.uc-title-accent {
    display: inline;
    background: linear-gradient(135deg, var(--uc-color), var(--uc-color-end, var(--accent-cyan)));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.uc-desc {
    font-size: 15px;
    color: var(--text-secondary);
    max-width: 560px;
    margin: 0 auto 12px;
    line-height: 1.7;
    font-weight: 400;
}

/* ── Construction badge ── */
.uc-badge-wrap {
    display: flex;
    justify-content: center;
    margin-bottom: 44px;
    z-index: 2;
    animation: uc-fadeSlideUp 0.8s cubic-bezier(0.22, 1, 0.36, 1) 0.3s both;
}

.uc-badge {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 24px;
    border-radius: 100px;
    font-family: var(--font-mono);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    color: var(--uc-color);
    background: rgba(0,0,0,0.4);
    border: 1px solid var(--uc-border);
    position: relative;
    overflow: hidden;
}

.uc-badge::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 60%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.06), transparent);
    animation: uc-shimmer 3s ease-in-out infinite;
}

.uc-badge-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--uc-color);
    box-shadow: 0 0 8px var(--uc-color);
    animation: uc-pulse-dot 1.8s ease-in-out infinite;
}

/* ── Feature cards ── */
.uc-features {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    width: 100%;
    max-width: 820px;
    margin-bottom: 48px;
    z-index: 2;
}

.uc-feat {
    background: linear-gradient(160deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.01) 100%);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 16px;
    padding: 28px 18px 24px;
    text-align: center;
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

.uc-feat:nth-child(1) { animation: uc-cardEnter 0.7s cubic-bezier(0.22, 1, 0.36, 1) 0.4s both; }
.uc-feat:nth-child(2) { animation: uc-cardEnter 0.7s cubic-bezier(0.22, 1, 0.36, 1) 0.52s both; }
.uc-feat:nth-child(3) { animation: uc-cardEnter 0.7s cubic-bezier(0.22, 1, 0.36, 1) 0.64s both; }
.uc-feat:nth-child(4) { animation: uc-cardEnter 0.7s cubic-bezier(0.22, 1, 0.36, 1) 0.76s both; }

.uc-feat::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 16px;
    padding: 1px;
    background: linear-gradient(160deg, var(--uc-border), transparent 60%);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.4s ease;
    pointer-events: none;
}

.uc-feat:hover {
    transform: translateY(-4px);
    border-color: var(--uc-border);
    box-shadow: 0 12px 40px rgba(0,0,0,0.4), 0 0 30px var(--uc-glow-from);
}
.uc-feat:hover::before { opacity: 1; }

.uc-feat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(145deg, var(--uc-glow-from), var(--uc-glow-to));
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 14px;
    font-size: 18px;
    color: #fff;
    box-shadow: 0 4px 20px var(--uc-glow-from);
}

.uc-feat-label {
    font-size: 12px;
    font-weight: 700;
    color: var(--text-secondary);
    letter-spacing: 0.3px;
    line-height: 1.4;
}

.uc-feat-tag {
    display: inline-block;
    margin-top: 10px;
    font-family: var(--font-mono);
    font-size: 9px;
    font-weight: 600;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: var(--text-muted);
    opacity: 0.6;
}

/* ── Return button ── */
.uc-actions {
    z-index: 2;
    animation: uc-fadeSlideUp 0.8s cubic-bezier(0.22, 1, 0.36, 1) 0.9s both;
}

.uc-return-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 32px;
    font-family: 'Montserrat', sans-serif;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    text-decoration: none;
    color: var(--text-primary);
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 12px;
    transition: all 0.35s cubic-bezier(0.22, 1, 0.36, 1);
    position: relative;
    overflow: hidden;
}

.uc-return-btn::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 12px;
    padding: 1.5px;
    background: linear-gradient(135deg, var(--uc-color), transparent 60%);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.35s ease;
}

.uc-return-btn:hover {
    background: rgba(255,255,255,0.07);
    border-color: var(--uc-border);
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.3);
    color: var(--uc-color);
}
.uc-return-btn:hover::before { opacity: 1; }

.uc-return-btn i {
    font-size: 14px;
    transition: transform 0.3s ease;
}
.uc-return-btn:hover i {
    transform: translateX(-3px);
}

/* ── Geometric accents ── */
.uc-geo-line {
    position: absolute;
    background: var(--uc-color);
    opacity: 0.06;
    pointer-events: none;
}
.uc-geo-h {
    height: 1px;
    width: 200px;
    top: 40%;
}
.uc-geo-h.left { left: 0; }
.uc-geo-h.right { right: 0; }
.uc-geo-v {
    width: 1px;
    height: 120px;
    bottom: 10%;
}
.uc-geo-v.left { left: 8%; }
.uc-geo-v.right { right: 8%; }

/* ── Corner brackets ── */
.uc-corner {
    position: absolute;
    width: 20px;
    height: 20px;
    pointer-events: none;
    opacity: 0.08;
}
.uc-corner::before,
.uc-corner::after {
    content: '';
    position: absolute;
    background: var(--uc-color);
}
.uc-corner.tl { top: 24px; left: 24px; }
.uc-corner.tl::before { top: 0; left: 0; width: 20px; height: 1.5px; }
.uc-corner.tl::after { top: 0; left: 0; width: 1.5px; height: 20px; }
.uc-corner.tr { top: 24px; right: 24px; }
.uc-corner.tr::before { top: 0; right: 0; width: 20px; height: 1.5px; }
.uc-corner.tr::after { top: 0; right: 0; width: 1.5px; height: 20px; }
.uc-corner.bl { bottom: 24px; left: 24px; }
.uc-corner.bl::before { bottom: 0; left: 0; width: 20px; height: 1.5px; }
.uc-corner.bl::after { bottom: 0; left: 0; width: 1.5px; height: 20px; }
.uc-corner.br { bottom: 24px; right: 24px; }
.uc-corner.br::before { bottom: 0; right: 0; width: 20px; height: 1.5px; }
.uc-corner.br::after { bottom: 0; right: 0; width: 1.5px; height: 20px; }

/* ── Keyframes ── */
@@keyframes uc-sonar {
    0% { transform: scale(0.6); opacity: 0.6; }
    100% { transform: scale(1.3); opacity: 0; }
}
@@keyframes uc-breathe {
    0%, 100% { opacity: 0.10; transform: translateX(-50%) scale(1); }
    50% { opacity: 0.18; transform: translateX(-50%) scale(1.05); }
}
@@keyframes uc-fadeSlideUp {
    0% { opacity: 0; transform: translateY(24px); }
    100% { opacity: 1; transform: translateY(0); }
}
@@keyframes uc-cardEnter {
    0% { opacity: 0; transform: translateY(30px) scale(0.95); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
@@keyframes uc-shimmer {
    0% { left: -100%; }
    50%, 100% { left: 200%; }
}
@@keyframes uc-pulse-dot {
    0%, 100% { opacity: 1; box-shadow: 0 0 8px var(--uc-color); }
    50% { opacity: 0.4; box-shadow: 0 0 3px var(--uc-color); }
}

/* ── Responsive ── */
@@media (max-width: 768px) {
    .uc-features { grid-template-columns: repeat(2, 1fr); gap: 12px; }
    .uc-title { font-size: 24px; }
    .uc-beacon-core { width: 72px; height: 72px; font-size: 28px; border-radius: 20px; }
    .uc-shell { padding: 32px 16px 48px; }
    .uc-geo-line { display: none; }
}
@@media (max-width: 480px) {
    .uc-features { grid-template-columns: 1fr 1fr; }
    .uc-title { font-size: 20px; }
}
</style>
@endpush

@section('content')
<div class="uc-shell"
     style="--uc-color: {{ $config['color'] }};
            --uc-color-end: {{ $config['color'] }}cc;
            --uc-glow-from: {{ $config['gradient_from'] }};
            --uc-glow-to: {{ $config['gradient_to'] }};
            --uc-border: {{ $config['border_color'] }};">

    {{-- Background layers --}}
    <div class="uc-grid-bg"></div>
    <div class="uc-radial-wash"></div>

    {{-- Corner brackets --}}
    <div class="uc-corner tl"></div>
    <div class="uc-corner tr"></div>
    <div class="uc-corner bl"></div>
    <div class="uc-corner br"></div>

    {{-- Geometric accent lines --}}
    <div class="uc-geo-line uc-geo-h left"></div>
    <div class="uc-geo-line uc-geo-h right"></div>
    <div class="uc-geo-line uc-geo-v left"></div>
    <div class="uc-geo-line uc-geo-v right"></div>

    {{-- Sonar beacon --}}
    <div class="uc-beacon">
        <div class="uc-beacon-core">
            <i class="fas {{ $config['icon'] }}"></i>
        </div>
        <div class="uc-ring"></div>
        <div class="uc-ring"></div>
        <div class="uc-ring"></div>
    </div>

    {{-- Title --}}
    <div class="uc-title-block">
        <h1 class="uc-title"><span class="uc-title-accent">{{ $config['title'] }}</span></h1>
        <p class="uc-desc">{{ $config['description'] }}</p>
    </div>

    {{-- Construction badge --}}
    <div class="uc-badge-wrap">
        <div class="uc-badge">
            <span class="uc-badge-dot"></span>
            Under Construction
        </div>
    </div>

    {{-- Feature cards --}}
    <div class="uc-features">
        @foreach($config['features'] as $feat)
        <div class="uc-feat">
            <div class="uc-feat-icon">
                <i class="fas {{ $feat['icon'] }}"></i>
            </div>
            <div class="uc-feat-label">{{ $feat['label'] }}</div>
            <div class="uc-feat-tag">Coming Soon</div>
        </div>
        @endforeach
    </div>

    {{-- Return button --}}
    <div class="uc-actions">
        <a href="{{ route('nexcore.clients.show.dashboard', $client->id) }}" class="uc-return-btn">
            <i class="fas fa-arrow-left"></i>
            Return to Client Profile
        </a>
    </div>
</div>
@endsection
