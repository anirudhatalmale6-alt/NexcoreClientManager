<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexCore — System Master Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="/public/nexcore/system_messages/css/system_master_messages.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Montserrat', sans-serif;
            background: #080a0f;
            color: #cbd5e1;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        :root {
            --nx-text-title: #f1f5f9;
            --nx-text-heading: #e2e8f0;
            --nx-text-body: #cbd5e1;
            --nx-text-subtitle: #94a3b8;
            --nx-text-label: #94a3b8;
            --nx-text-value: #e2e8f0;
            --nx-text-muted: #64748b;
            --nx-text-caption: #475569;
            --nx-text-disabled: #334155;
            --nx-lt-title: #0f172a;
            --nx-lt-heading: #1e293b;
            --nx-lt-body: #334155;
            --nx-lt-label: #475569;
            --nx-lt-placeholder: #94a3b8;
            --accent-cyan: #06b6d4;
            --accent-blue: #3b82f6;
            --accent-green: #22c55e;
            --accent-red: #ef4444;
            --accent-amber: #f59e0b;
            --accent-purple: #8b5cf6;
            --accent-pink: #ec4899;
            --bg-deepest: #080a0f;
            --bg-deep: #0c0f16;
            --bg-surface: #111520;
            --bg-raised: #161b28;
            --border-subtle: rgba(255,255,255,0.06);
            --border-default: rgba(255,255,255,0.09);
            --border-strong: rgba(255,255,255,0.14);
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 14px;
            --shadow-card: 0 1px 3px rgba(0,0,0,0.4), 0 4px 12px rgba(0,0,0,0.2);
            --shadow-elevated: 0 4px 16px rgba(0,0,0,0.5), 0 8px 32px rgba(0,0,0,0.3);
            --transition-fast: 0.15s cubic-bezier(0.4,0,0.2,1);
            --transition-smooth: 0.3s cubic-bezier(0.4,0,0.2,1);
        }

        /* ===================== LAYOUT ===================== */
        .smf-shell { display: flex; min-height: 100vh; }

        .smf-sidebar {
            width: 240px;
            min-width: 240px;
            background: var(--bg-deep);
            border-right: 1px solid var(--border-subtle);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            overflow-y: auto;
            z-index: 100;
            padding: 28px 0;
        }
        .smf-sidebar::-webkit-scrollbar { width: 4px; }
        .smf-sidebar::-webkit-scrollbar-track { background: transparent; }
        .smf-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 2px; }

        .smf-brand {
            padding: 0 24px 24px;
            border-bottom: 1px solid var(--border-subtle);
            margin-bottom: 20px;
        }
        .smf-brand-logo {
            width: 100%;
            max-width: 200px;
            height: auto;
            margin-bottom: 10px;
        }
        .smf-brand-sub {
            font-size: 11px;
            font-weight: 600;
            color: var(--nx-text-muted);
            margin-top: 4px;
        }
        .smf-brand-ver {
            font-size: 9px;
            font-weight: 700;
            color: var(--accent-cyan);
            background: rgba(6,182,212,0.1);
            padding: 3px 8px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 10px;
            letter-spacing: 0.5px;
        }

        .smf-nav-heading {
            font-size: 10px;
            font-weight: 700;
            color: var(--nx-text-caption);
            text-transform: uppercase;
            letter-spacing: 1.2px;
            padding: 12px 24px 8px;
        }
        .smf-nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 24px;
            font-size: 12px;
            font-weight: 600;
            color: var(--nx-text-subtitle);
            text-decoration: none;
            transition: all var(--transition-fast);
            border-left: 3px solid transparent;
        }
        .smf-nav-link:hover {
            color: var(--nx-text-heading);
            background: rgba(255,255,255,0.03);
            border-left-color: var(--accent-cyan);
        }
        .smf-nav-link i { width: 16px; text-align: center; font-size: 11px; }

        .smf-main {
            margin-left: 240px;
            flex: 1;
            min-height: 100vh;
        }

        .smf-topbar {
            height: 56px;
            background: var(--bg-deep);
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 90;
        }
        .smf-topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .smf-topbar-left span:first-child {
            font-size: 12px;
            font-weight: 700;
            color: var(--nx-text-subtitle);
        }
        .smf-topbar-sep {
            width: 1px;
            height: 18px;
            background: var(--border-default);
        }
        .smf-topbar-left span:last-child {
            font-size: 12px;
            font-weight: 600;
            color: var(--nx-text-muted);
        }
        .smf-topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .smf-topbar-badge {
            font-size: 9px;
            font-weight: 800;
            color: #fbbf24;
            background: rgba(251,191,36,0.1);
            border: 1px solid rgba(251,191,36,0.2);
            padding: 4px 12px;
            border-radius: 20px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .smf-topbar-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 11px;
            font-weight: 700;
            color: #e2e8f0;
            background: linear-gradient(135deg, rgba(6,182,212,0.15), rgba(59,130,246,0.15));
            border: 1px solid rgba(6,182,212,0.25);
            padding: 7px 16px;
            border-radius: 8px;
            text-decoration: none;
            letter-spacing: 0.3px;
            transition: all 0.25s ease;
        }
        .smf-topbar-btn:hover {
            background: linear-gradient(135deg, rgba(6,182,212,0.25), rgba(59,130,246,0.25));
            border-color: rgba(6,182,212,0.4);
            color: #06b6d4;
            box-shadow: 0 0 12px rgba(6,182,212,0.15);
        }
        .smf-topbar-btn i {
            font-size: 10px;
        }

        .smf-content { padding: 32px; }

        /* ===================== PAGE HEADER ===================== */
        .smf-page-header {
            margin-bottom: 40px;
            padding-bottom: 32px;
            border-bottom: 1px solid var(--border-subtle);
        }
        .smf-page-header h1 {
            font-size: 32px;
            font-weight: 900;
            color: var(--nx-text-title);
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }
        .smf-page-header p {
            font-size: 14px;
            font-weight: 500;
            color: var(--nx-text-subtitle);
            max-width: 640px;
            line-height: 1.6;
        }

        /* ===================== SECTIONS ===================== */
        .smf-section {
            margin-bottom: 56px;
        }
        .smf-section-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-subtle);
        }
        .smf-section-num {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 800;
            color: #fff;
        }
        .smf-section-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--nx-text-heading);
        }
        .smf-section-count {
            font-size: 10px;
            font-weight: 700;
            color: var(--nx-text-muted);
            background: var(--bg-raised);
            padding: 3px 10px;
            border-radius: 10px;
            margin-left: auto;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ===================== SPECIMEN GRID ===================== */
        .smf-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }
        .smf-grid-wide {
            grid-template-columns: 1fr;
        }
        .smf-grid-2 {
            grid-template-columns: repeat(2, 1fr);
        }
        .smf-grid-3 {
            grid-template-columns: repeat(3, 1fr);
        }
        .smf-grid-4 {
            grid-template-columns: repeat(4, 1fr);
        }

        .smf-specimen {
            background: rgba(255,255,255,0.02);
            border: 1.5px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 24px;
            position: relative;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            box-shadow: 0 2px 8px rgba(0,0,0,0.25), 0 1px 3px rgba(0,0,0,0.15);
        }
        .smf-specimen:hover {
            border-color: #f59e0b;
            background-color: #2a1e08;
            transform: translateY(-5px);
            box-shadow: 0 0 14px rgba(245,158,11,0.4), 0 0 28px rgba(245,158,11,0.18), 0 8px 32px rgba(0,0,0,0.35);
        }

        .smf-code {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
        }
        .smf-code-cyan { background: rgba(6,182,212,0.12); color: #22d3ee; border: 1px solid rgba(6,182,212,0.2); }
        .smf-code-blue { background: rgba(59,130,246,0.12); color: #60a5fa; border: 1px solid rgba(59,130,246,0.2); }
        .smf-code-green { background: rgba(34,197,94,0.12); color: #4ade80; border: 1px solid rgba(34,197,94,0.2); }
        .smf-code-amber { background: rgba(245,158,11,0.12); color: #fbbf24; border: 1px solid rgba(245,158,11,0.2); }
        .smf-code-purple { background: rgba(139,92,246,0.12); color: #a78bfa; border: 1px solid rgba(139,92,246,0.2); }
        .smf-code-red { background: rgba(239,68,68,0.12); color: #f87171; border: 1px solid rgba(239,68,68,0.2); }
        .smf-code-pink { background: rgba(236,72,153,0.12); color: #f472b6; border: 1px solid rgba(236,72,153,0.2); }

        .smf-name {
            font-size: 11px;
            font-weight: 600;
            color: var(--nx-text-muted);
            margin-bottom: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .smf-render {
            min-height: 36px;
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }

        .smf-specs {
            font-size: 10px;
            font-weight: 600;
            color: var(--nx-text-caption);
            font-family: 'Courier New', monospace;
            line-height: 1.6;
            padding-top: 12px;
            border-top: 1px solid var(--border-subtle);
        }

        /* ===================== TEXT SPECIMENS ===================== */
        .demo-t1 { font-size: 28px; font-weight: 800; color: var(--nx-text-title); letter-spacing: 0.5px; }
        .demo-t2 { font-size: 18px; font-weight: 700; color: var(--nx-text-heading); letter-spacing: 0.5px; }
        .demo-t3 { font-size: 16px; font-weight: 700; color: var(--nx-text-title); letter-spacing: 0.5px; }
        .demo-t4 { font-size: 14px; font-weight: 600; color: var(--nx-text-subtitle); letter-spacing: 0.2px; }
        .demo-t5 { font-size: 14px; font-weight: 500; color: var(--nx-text-body); line-height: 1.6; letter-spacing: 0.3px; }
        .demo-t6 { font-size: 12px; font-weight: 700; color: var(--nx-text-label); text-transform: uppercase; letter-spacing: 0.8px; }
        .demo-t7 { font-size: 14px; font-weight: 600; color: var(--nx-text-value); letter-spacing: 0.2px; }
        .demo-t8 { font-size: 12px; font-weight: 500; color: var(--nx-text-muted); letter-spacing: 0.3px; }
        .demo-t9 { font-size: 10px; font-weight: 700; color: var(--nx-text-caption); text-transform: uppercase; letter-spacing: 1px; }

        /* ===================== FONT WEIGHT DEMOS ===================== */
        .demo-fw { font-size: 18px; color: var(--nx-text-heading); letter-spacing: 0.2px; }

        /* ===================== BUTTON SPECIMENS ===================== */
        .demo-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 13px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all var(--transition-smooth);
            text-decoration: none;
        }
        .demo-b1 {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: #fff;
            box-shadow: 0 0 20px rgba(6,182,212,0.25);
        }
        .demo-b1:hover { box-shadow: 0 0 30px rgba(6,182,212,0.4); transform: translateY(-1px); }
        .demo-b2 {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
            box-shadow: 0 0 20px rgba(34,197,94,0.25);
        }
        .demo-b2:hover { box-shadow: 0 0 30px rgba(34,197,94,0.4); transform: translateY(-1px); }
        .demo-b3 {
            background: transparent;
            color: var(--nx-text-subtitle);
            border: 1.5px solid var(--border-strong);
        }
        .demo-b3:hover { color: var(--nx-text-heading); border-color: var(--accent-cyan); background: rgba(6,182,212,0.06); }
        .demo-b4 {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff;
            box-shadow: 0 0 20px rgba(239,68,68,0.25);
        }
        .demo-b4:hover { box-shadow: 0 0 30px rgba(239,68,68,0.4); transform: translateY(-1px); }
        .demo-b5 {
            width: 38px;
            height: 38px;
            padding: 0;
            border-radius: 8px;
            background: rgba(255,255,255,0.04);
            border: 1.5px solid var(--border-subtle);
            color: var(--nx-text-subtitle);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        .demo-b5:hover { color: var(--accent-cyan); border-color: rgba(6,182,212,0.3); background: rgba(6,182,212,0.06); }

        /* ===================== ICON BUTTON SPECIMENS ===================== */
        .demo-ib {
            width: 40px;
            height: 40px;
            padding: 0;
            border-radius: 9px;
            background: rgba(255,255,255,0.04);
            border: 1.5px solid var(--border-subtle);
            color: var(--nx-text-subtitle);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
            transition: all 0.3s cubic-bezier(0.25,0.46,0.45,0.94);
        }
        .demo-ib:hover { transform: translateY(-2px); }
        .demo-ib-edit:hover { color: #22d3ee; border-color: #06b6d4; background: rgba(6,182,212,0.1); box-shadow: 0 0 12px rgba(6,182,212,0.3); }
        .demo-ib-view:hover { color: #60a5fa; border-color: #3b82f6; background: rgba(59,130,246,0.1); box-shadow: 0 0 12px rgba(59,130,246,0.3); }
        .demo-ib-delete:hover { color: #f87171; border-color: #ef4444; background: rgba(239,68,68,0.1); box-shadow: 0 0 12px rgba(239,68,68,0.3); }
        .demo-ib-email:hover { color: #4ade80; border-color: #22c55e; background: rgba(34,197,94,0.1); box-shadow: 0 0 12px rgba(34,197,94,0.3); }
        .demo-ib-share:hover { color: #a78bfa; border-color: #8b5cf6; background: rgba(139,92,246,0.1); box-shadow: 0 0 12px rgba(139,92,246,0.3); }
        .demo-ib-whatsapp:hover { color: #25D366; border-color: #25D366; background: rgba(37,211,102,0.1); box-shadow: 0 0 12px rgba(37,211,102,0.3); }
        .demo-ib-calc:hover { color: #fbbf24; border-color: #f59e0b; background: rgba(245,158,11,0.1); box-shadow: 0 0 12px rgba(245,158,11,0.3); }
        .demo-ib-print:hover { color: #60a5fa; border-color: #3b82f6; background: rgba(59,130,246,0.1); box-shadow: 0 0 12px rgba(59,130,246,0.3); }
        .demo-ib-download:hover { color: #22d3ee; border-color: #06b6d4; background: rgba(6,182,212,0.1); box-shadow: 0 0 12px rgba(6,182,212,0.3); }
        .demo-ib-phone:hover { color: #4ade80; border-color: #22c55e; background: rgba(34,197,94,0.1); box-shadow: 0 0 12px rgba(34,197,94,0.3); }
        .demo-ib-link:hover { color: #a78bfa; border-color: #8b5cf6; background: rgba(139,92,246,0.1); box-shadow: 0 0 12px rgba(139,92,246,0.3); }
        .demo-ib-settings:hover { color: #fbbf24; border-color: #f59e0b; background: rgba(245,158,11,0.1); box-shadow: 0 0 12px rgba(245,158,11,0.3); }
        .demo-ib-copy:hover { color: #22d3ee; border-color: #06b6d4; background: rgba(6,182,212,0.1); box-shadow: 0 0 12px rgba(6,182,212,0.3); }
        .demo-ib-archive:hover { color: #fbbf24; border-color: #f59e0b; background: rgba(245,158,11,0.1); box-shadow: 0 0 12px rgba(245,158,11,0.3); }
        .demo-ib-toggle:hover { color: #4ade80; border-color: #22c55e; background: rgba(34,197,94,0.1); box-shadow: 0 0 12px rgba(34,197,94,0.3); }
        .demo-ib-delink:hover { color: #f87171; border-color: #ef4444; background: rgba(239,68,68,0.1); box-shadow: 0 0 12px rgba(239,68,68,0.3); }
        .demo-ib-weight:hover { color: #fbbf24; border-color: #f59e0b; background: rgba(245,158,11,0.1); box-shadow: 0 0 12px rgba(245,158,11,0.3); }
        .demo-ib-lab:hover { color: #a78bfa; border-color: #8b5cf6; background: rgba(139,92,246,0.1); box-shadow: 0 0 12px rgba(139,92,246,0.3); }
        .demo-ib-inventory:hover { color: #60a5fa; border-color: #3b82f6; background: rgba(59,130,246,0.1); box-shadow: 0 0 12px rgba(59,130,246,0.3); }
        .demo-ib-fuel:hover { color: #fbbf24; border-color: #f59e0b; background: rgba(245,158,11,0.1); box-shadow: 0 0 12px rgba(245,158,11,0.3); }
        .demo-b6 {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: #fff;
            box-shadow: 0 0 20px rgba(139,92,246,0.25);
        }
        .demo-b6:hover { box-shadow: 0 0 30px rgba(139,92,246,0.4); transform: translateY(-1px); }
        .demo-b7 {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #fff;
            box-shadow: 0 0 20px rgba(245,158,11,0.25);
        }
        .demo-b7:hover { box-shadow: 0 0 30px rgba(245,158,11,0.4); transform: translateY(-1px); }
        .demo-b8 {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: #fff;
            box-shadow: 0 0 20px rgba(59,130,246,0.25);
        }
        .demo-b8:hover { box-shadow: 0 0 30px rgba(59,130,246,0.4); transform: translateY(-1px); }
        .demo-b9 {
            background: linear-gradient(135deg, #ec4899, #db2777);
            color: #fff;
            box-shadow: 0 0 20px rgba(236,72,153,0.25);
        }
        .demo-b9:hover { box-shadow: 0 0 30px rgba(236,72,153,0.4); transform: translateY(-1px); }

        /* ===================== BADGE SPECIMENS ===================== */
        .demo-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .demo-badge i { font-size: 7px; }
        .demo-bg1 { background: rgba(34,197,94,0.12); color: #4ade80; border: 1px solid rgba(34,197,94,0.2); }
        .demo-bg2 { background: rgba(239,68,68,0.12); color: #f87171; border: 1px solid rgba(239,68,68,0.2); }
        .demo-bg3 { background: rgba(245,158,11,0.12); color: #fbbf24; border: 1px solid rgba(245,158,11,0.2); }
        .demo-bg4 { background: rgba(6,182,212,0.12); color: #22d3ee; border: 1px solid rgba(6,182,212,0.2); }
        .demo-bg5 { background: rgba(139,92,246,0.12); color: #a78bfa; border: 1px solid rgba(139,92,246,0.2); }
        .demo-bg6 { background: rgba(59,130,246,0.12); color: #60a5fa; border: 1px solid rgba(59,130,246,0.2); }
        .demo-bg7 { background: rgba(236,72,153,0.12); color: #f472b6; border: 1px solid rgba(236,72,153,0.2); }
        .demo-bg8 { background: rgba(255,255,255,0.06); color: var(--nx-text-subtitle); border: 1px solid var(--border-default); }

        /* ===================== CARD SPECIMENS ===================== */
        .demo-card-c1 {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 24px;
        }
        .demo-card-c2 {
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        .demo-card-c2::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }
        .demo-card-c2.accent-cyan::before { background: linear-gradient(90deg, #06b6d4, #0891b2); }
        .demo-card-c2.accent-green::before { background: linear-gradient(90deg, #22c55e, #16a34a); }
        .demo-card-c2.accent-blue::before { background: linear-gradient(90deg, #3b82f6, #2563eb); }
        .demo-card-c2.accent-amber::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
        .demo-card-c2 .stat-val { font-size: 28px; font-weight: 900; color: var(--nx-text-title); }
        .demo-card-c2 .stat-label { font-size: 12px; font-weight: 700; color: var(--nx-text-label); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 6px; }
        .demo-card-c2 .stat-meta { font-size: 11px; font-weight: 500; color: var(--nx-text-muted); margin-top: 4px; }

        .demo-card-c3 {
            background: rgba(255,255,255,0.025);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all var(--transition-smooth);
            border-left: 3px solid transparent;
        }
        .demo-card-c3:hover {
            background: rgba(255,255,255,0.04);
            border-left-color: var(--accent-cyan);
            box-shadow: 0 0 20px rgba(6,182,212,0.08);
        }
        .demo-card-c3 .c3-avatar {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
        }
        .demo-card-c3 .c3-info { flex: 1; }
        .demo-card-c3 .c3-name { font-size: 15px; font-weight: 700; color: var(--nx-text-title); }
        .demo-card-c3 .c3-sub { font-size: 12px; font-weight: 500; color: var(--nx-text-muted); margin-top: 3px; }
        .demo-card-c3 .c3-actions { display: flex; gap: 8px; }

        /* ===================== FORM SPECIMENS (Light) ===================== */
        .smf-light-panel {
            background: #fff;
            border-radius: var(--radius-lg);
            padding: 28px;
            box-shadow: var(--shadow-elevated);
        }
        .demo-form-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--nx-lt-label);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .demo-form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: var(--nx-lt-title);
            background: #fff;
            outline: none;
            transition: all var(--transition-fast);
        }
        .demo-form-input:focus { border-color: var(--accent-cyan); box-shadow: 0 0 0 3px rgba(6,182,212,0.1); }
        .demo-form-input::placeholder { color: var(--nx-lt-placeholder); font-weight: 400; }
        .demo-form-select {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 13px;
            font-weight: 600;
            color: var(--nx-lt-title);
            background: #fff;
            outline: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2394a3b8'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 20px;
            cursor: pointer;
        }
        .demo-form-select:focus { border-color: var(--accent-cyan); box-shadow: 0 0 0 3px rgba(6,182,212,0.1); }
        .demo-form-textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: var(--nx-lt-title);
            background: #fff;
            outline: none;
            resize: vertical;
            min-height: 80px;
        }
        .demo-form-textarea:focus { border-color: var(--accent-cyan); box-shadow: 0 0 0 3px rgba(6,182,212,0.1); }
        .demo-form-toggle {
            position: relative;
            width: 44px;
            height: 24px;
            background: #cbd5e1;
            border-radius: 12px;
            cursor: pointer;
            transition: all var(--transition-fast);
        }
        .demo-form-toggle.active { background: var(--accent-cyan); }
        .demo-form-toggle::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 18px;
            height: 18px;
            background: #fff;
            border-radius: 50%;
            transition: all var(--transition-fast);
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .demo-form-toggle.active::after { left: 23px; }
        .demo-form-check {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--nx-lt-body);
            cursor: pointer;
        }
        .demo-form-check input[type="checkbox"] {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            accent-color: var(--accent-cyan);
        }

        /* ===================== AVATAR SPECIMENS ===================== */
        .demo-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
        }
        .demo-av-lg { width: 80px; height: 80px; font-size: 26px; border-radius: 16px; }
        .demo-av-md { width: 48px; height: 48px; font-size: 16px; }
        .demo-av-sm { width: 36px; height: 36px; font-size: 12px; border-radius: 8px; }

        /* ===================== STAT BLOCK SPECIMENS ===================== */
        .demo-stat-lg .stat-number { font-size: 36px; font-weight: 900; color: var(--nx-text-title); }
        .demo-stat-lg .stat-label { font-size: 12px; font-weight: 700; color: var(--nx-text-label); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 6px; }
        .demo-stat-lg .stat-change { font-size: 11px; font-weight: 600; margin-top: 4px; display: flex; align-items: center; gap: 4px; }
        .demo-stat-lg .stat-change.up { color: #4ade80; }
        .demo-stat-lg .stat-change.down { color: #f87171; }

        .demo-stat-sm { display: flex; align-items: center; gap: 12px; }
        .demo-stat-sm .stat-number { font-size: 22px; font-weight: 800; color: var(--nx-text-title); }
        .demo-stat-sm .stat-label { font-size: 11px; font-weight: 600; color: var(--nx-text-muted); }

        /* ===================== ACTION BAR ===================== */
        .demo-action-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            background: rgba(255,255,255,0.025);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
        }
        .demo-search-input {
            flex: 1;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border-default);
            border-radius: 8px;
            padding: 9px 14px 9px 36px;
            font-family: 'Montserrat', sans-serif;
            font-size: 13px;
            font-weight: 500;
            color: var(--nx-text-heading);
            outline: none;
        }
        .demo-search-input::placeholder { color: var(--nx-text-muted); font-weight: 400; }
        .demo-search-input:focus { border-color: var(--accent-cyan); }
        .demo-search-wrap { position: relative; flex: 1; }
        .demo-search-wrap i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--nx-text-muted);
            font-size: 12px;
        }

        /* ===================== LAYOUT GRID DEMOS ===================== */
        .demo-grid-cell {
            padding: 16px;
            background: rgba(6,182,212,0.06);
            border: 1px dashed rgba(6,182,212,0.2);
            border-radius: 8px;
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            color: var(--accent-cyan);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .demo-grid-row {
            display: grid;
            gap: 16px;
            margin-bottom: 16px;
        }
        .demo-grid-row.g2 { grid-template-columns: repeat(2, 1fr); }
        .demo-grid-row.g3 { grid-template-columns: repeat(3, 1fr); }
        .demo-grid-row.g4 { grid-template-columns: repeat(4, 1fr); }

        /* ===================== RESPONSIVE ===================== */
        @@media (max-width: 1024px) {
            .smf-grid-3, .smf-grid-4 { grid-template-columns: repeat(2, 1fr); }
        }
        @@media (max-width: 768px) {
            .smf-sidebar { display: none; }
            .smf-main { margin-left: 0; }
            .smf-grid, .smf-grid-2, .smf-grid-3, .smf-grid-4 { grid-template-columns: 1fr; }
            .smf-content { padding: 20px; }
        }

        /* ===================== LIGHT TEXT SPECIMENS ===================== */
        .demo-lt1 { font-size: 20px; font-weight: 800; color: var(--nx-lt-title); }
        .demo-lt2 { font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; }
        .demo-lt3 { font-size: 11px; font-weight: 700; color: var(--nx-lt-label); text-transform: uppercase; letter-spacing: 0.5px; }
        .demo-lt4 { font-size: 14px; font-weight: 600; color: var(--nx-lt-title); }
        .demo-lt5 { font-size: 13px; font-weight: 400; color: var(--nx-lt-placeholder); }

        /* ===================== SECTION LINE DEMOS ===================== */
        .demo-section-line {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: 8px;
            margin-bottom: 12px;
        }
        .demo-section-line i {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        .demo-section-line span {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .demo-section-line .line {
            flex: 1;
            height: 1px;
        }
        .sec-cyan { background: rgba(6,182,212,0.06); }
        .sec-cyan i { background: rgba(6,182,212,0.15); color: #06b6d4; }
        .sec-cyan span { color: #06b6d4; }
        .sec-cyan .line { background: rgba(6,182,212,0.2); }
        .sec-blue { background: rgba(59,130,246,0.06); }
        .sec-blue i { background: rgba(59,130,246,0.15); color: #3b82f6; }
        .sec-blue span { color: #3b82f6; }
        .sec-blue .line { background: rgba(59,130,246,0.2); }
        .sec-green { background: rgba(34,197,94,0.06); }
        .sec-green i { background: rgba(34,197,94,0.15); color: #22c55e; }
        .sec-green span { color: #22c55e; }
        .sec-green .line { background: rgba(34,197,94,0.2); }
        .sec-purple { background: rgba(139,92,246,0.06); }
        .sec-purple i { background: rgba(139,92,246,0.15); color: #8b5cf6; }
        .sec-purple span { color: #8b5cf6; }
        .sec-purple .line { background: rgba(139,92,246,0.2); }
        .sec-amber { background: rgba(245,158,11,0.06); }
        .sec-amber i { background: rgba(245,158,11,0.15); color: #f59e0b; }
        .sec-amber span { color: #f59e0b; }
        .sec-amber .line { background: rgba(245,158,11,0.2); }

        /* ===================== MESSAGE BOX SPECIMENS ===================== */
        .demo-msg-trigger {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 12px;
            font-weight: 700;
            border: 1.5px solid var(--border-subtle);
            background: rgba(255,255,255,0.04);
            color: var(--nx-text-subtitle);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .demo-msg-trigger:hover { transform: translateY(-1px); }
        .demo-msg-trigger.tr-green:hover { color: #4ade80; border-color: #22c55e; background: rgba(34,197,94,0.08); }
        .demo-msg-trigger.tr-red:hover { color: #f87171; border-color: #ef4444; background: rgba(239,68,68,0.08); }
        .demo-msg-trigger.tr-amber:hover { color: #fbbf24; border-color: #f59e0b; background: rgba(245,158,11,0.08); }
        .demo-msg-trigger.tr-blue:hover { color: #60a5fa; border-color: #3b82f6; background: rgba(59,130,246,0.08); }
        .demo-msg-trigger.tr-cyan:hover { color: #22d3ee; border-color: #06b6d4; background: rgba(6,182,212,0.08); }
        .demo-msg-trigger.tr-purple:hover { color: #a78bfa; border-color: #8b5cf6; background: rgba(139,92,246,0.08); }
        .demo-msg-trigger.tr-grey:hover { color: var(--nx-text-body); border-color: var(--border-strong); background: rgba(255,255,255,0.06); }

        /* NxAlert styles loaded from: /public/nexcore/system_messages/css/system_master_messages.css */

        /* Print-friendly */
        @@media print {
            .smf-sidebar { display: none; }
            .smf-main { margin-left: 0; }
            body { background: #fff; color: #000; }
        }
    </style>
</head>
<body>
    <div class="smf-shell">
        <!-- ==================== SIDEBAR ==================== -->
        <aside class="smf-sidebar">
            <div class="smf-brand">
                <img src="/public/images/brand/nexcore-logo-dark.jpg" alt="NexCore Africa" class="smf-brand-logo">
                <div class="smf-brand-sub">Design System</div>
                <div class="smf-brand-ver">Gold Standard v1.0</div>
            </div>

            <div class="smf-nav-heading">Foundation</div>
            <a href="#sec-font" class="smf-nav-link"><i class="fas fa-text-height"></i> Font Family</a>

            <div class="smf-nav-heading">Components</div>
            <a href="#sec-text-dark" class="smf-nav-link"><i class="fas fa-font"></i> Text - Dark (T1-T9)</a>
            <a href="#sec-text-light" class="smf-nav-link"><i class="fas fa-sun"></i> Text - Light (LT1-LT5)</a>
            <a href="#sec-buttons" class="smf-nav-link"><i class="fas fa-mouse-pointer"></i> Buttons (B1-B9)</a>
            <a href="#sec-icon-buttons" class="smf-nav-link"><i class="fas fa-hand-pointer"></i> Icon Buttons (IB1-IB20)</a>
            <a href="#sec-messages" class="smf-nav-link"><i class="fas fa-comment-alt"></i> Messages (M1-M6)</a>
            <a href="#sec-badges" class="smf-nav-link"><i class="fas fa-tag"></i> Badges (BG1-BG8)</a>
            <a href="#sec-cards" class="smf-nav-link"><i class="fas fa-square"></i> Cards (C1-C3)</a>
            <a href="#sec-forms" class="smf-nav-link"><i class="fas fa-keyboard"></i> Form Elements (F1-F6)</a>
            <a href="#sec-sections" class="smf-nav-link"><i class="fas fa-layer-group"></i> Form Sections (FS1-FS5)</a>
            <a href="#sec-stats" class="smf-nav-link"><i class="fas fa-chart-bar"></i> Stat Blocks (ST1-ST2)</a>
            <a href="#sec-avatars" class="smf-nav-link"><i class="fas fa-user-circle"></i> Avatars (AV1-AV3)</a>
            <a href="#sec-actionbar" class="smf-nav-link"><i class="fas fa-sliders-h"></i> Action Bars (AB1)</a>
            <a href="#sec-grids" class="smf-nav-link"><i class="fas fa-th"></i> Layout Grids (G1-G3)</a>
            <a href="#sec-sidebar" class="smf-nav-link"><i class="fas fa-columns"></i> Sidebar (SB1)</a>
        </aside>

        <!-- ==================== MAIN CONTENT ==================== -->
        <main class="smf-main">
            <div class="smf-topbar">
                <div class="smf-topbar-left">
                    <span>NexCore Enterprise Platform</span>
                    <div class="smf-topbar-sep"></div>
                    <span>System Master Form</span>
                </div>
                <div class="smf-topbar-right">
                    <div class="smf-topbar-badge">Gold Standard</div>
                    <a href="/nexcore/system/system_master_page" class="smf-topbar-btn"><i class="fas fa-arrow-left"></i> Return to System Menu</a>
                </div>
            </div>

            <div class="smf-content">
                <!-- Page Header -->
                <div class="smf-page-header">
                    <h1>System Master Form</h1>
                    <p>The definitive visual rulebook for every element in the NexCore platform. Each component is named, defined, and locked. Reference by code name to ensure pixel-perfect consistency across the entire system.</p>
                </div>

                <!-- ==================== SECTION 0: FONT FAMILY ==================== -->
                <div class="smf-section" id="sec-font">
                    <div class="smf-section-header">
                        <div class="smf-section-num" style="background: linear-gradient(135deg, #f8fafc, #94a3b8); color: #0f172a;">F</div>
                        <div class="smf-section-title">Font Family — Montserrat</div>
                        <div class="smf-section-count">System Default</div>
                    </div>
                    <div class="demo-card-c1" style="margin-bottom: 24px;">
                        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 24px;">
                            <div style="font-size: 64px; font-weight: 900; color: var(--nx-text-title); letter-spacing: -1px; line-height: 1;">Aa</div>
                            <div>
                                <div style="font-size: 22px; font-weight: 800; color: var(--nx-text-title); letter-spacing: 0.5px;">Montserrat</div>
                                <div style="font-size: 12px; font-weight: 500; color: var(--nx-text-muted); margin-top: 4px;">Google Fonts &middot; Sans-Serif &middot; Variable Weight 300-900</div>
                                <div style="font-size: 11px; font-weight: 600; color: var(--accent-cyan); margin-top: 6px;">THE ONLY FONT USED ACROSS THE ENTIRE NEXCORE PLATFORM</div>
                            </div>
                        </div>
                        <div style="border-top: 1px solid var(--border-subtle); padding-top: 20px;">
                            <div class="demo-t6" style="margin-bottom: 16px;">Available Weights</div>
                            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
                                <div style="padding: 16px; background: var(--bg-raised); border-radius: 8px; border: 1px solid var(--border-subtle);">
                                    <div class="demo-fw" style="font-weight: 300;">Light 300</div>
                                    <div class="smf-specs" style="border: none; padding-top: 8px;">Rarely used</div>
                                </div>
                                <div style="padding: 16px; background: var(--bg-raised); border-radius: 8px; border: 1px solid var(--border-subtle);">
                                    <div class="demo-fw" style="font-weight: 400;">Regular 400</div>
                                    <div class="smf-specs" style="border: none; padding-top: 8px;">Placeholders</div>
                                </div>
                                <div style="padding: 16px; background: var(--bg-raised); border-radius: 8px; border: 1px solid var(--border-subtle);">
                                    <div class="demo-fw" style="font-weight: 500;">Medium 500</div>
                                    <div class="smf-specs" style="border: none; padding-top: 8px;">Body text, muted</div>
                                </div>
                                <div style="padding: 16px; background: var(--bg-raised); border-radius: 8px; border: 1px solid var(--border-subtle);">
                                    <div class="demo-fw" style="font-weight: 600;">SemiBold 600</div>
                                    <div class="smf-specs" style="border: none; padding-top: 8px;">Values, subtitles</div>
                                </div>
                                <div style="padding: 16px; background: var(--bg-raised); border-radius: 8px; border: 1px solid var(--border-subtle);">
                                    <div class="demo-fw" style="font-weight: 700;">Bold 700</div>
                                    <div class="smf-specs" style="border: none; padding-top: 8px;">Headings, labels</div>
                                </div>
                                <div style="padding: 16px; background: var(--bg-raised); border-radius: 8px; border: 1px solid var(--border-subtle);">
                                    <div class="demo-fw" style="font-weight: 800;">ExtraBold 800</div>
                                    <div class="smf-specs" style="border: none; padding-top: 8px;">Titles, stats, nav</div>
                                </div>
                                <div style="padding: 16px; background: var(--bg-raised); border-radius: 8px; border: 1px solid var(--border-subtle);">
                                    <div class="demo-fw" style="font-weight: 900;">Black 900</div>
                                    <div class="smf-specs" style="border: none; padding-top: 8px;">Page titles, hero</div>
                                </div>
                            </div>
                        </div>
                        <div style="border-top: 1px solid var(--border-subtle); padding-top: 20px; margin-top: 20px;">
                            <div class="demo-t6" style="margin-bottom: 16px;">Letter Spacing Standard</div>
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
                                <div style="padding: 12px 16px; background: rgba(6,182,212,0.04); border: 1px solid rgba(6,182,212,0.1); border-radius: 8px;">
                                    <div style="font-size: 11px; font-weight: 700; color: var(--accent-cyan);">STANDARD: 0.5px</div>
                                    <div style="font-size: 11px; font-weight: 500; color: var(--nx-text-muted); margin-top: 4px;">Titles &amp; headings (16-28px)</div>
                                </div>
                                <div style="padding: 12px 16px; background: rgba(6,182,212,0.04); border: 1px solid rgba(6,182,212,0.1); border-radius: 8px;">
                                    <div style="font-size: 11px; font-weight: 700; color: var(--accent-cyan);">NEUTRAL: 0px</div>
                                    <div style="font-size: 11px; font-weight: 500; color: var(--nx-text-muted); margin-top: 4px;">Headings (16-18px)</div>
                                </div>
                                <div style="padding: 12px 16px; background: rgba(6,182,212,0.04); border: 1px solid rgba(6,182,212,0.1); border-radius: 8px;">
                                    <div style="font-size: 11px; font-weight: 700; color: var(--accent-cyan);">SLIGHT: 0.2-0.3px</div>
                                    <div style="font-size: 11px; font-weight: 500; color: var(--nx-text-muted); margin-top: 4px;">Body, values, subtitles</div>
                                </div>
                                <div style="padding: 12px 16px; background: rgba(245,158,11,0.04); border: 1px solid rgba(245,158,11,0.1); border-radius: 8px;">
                                    <div style="font-size: 11px; font-weight: 700; color: var(--accent-amber);">WIDE: 0.8px</div>
                                    <div style="font-size: 11px; font-weight: 500; color: var(--nx-text-muted); margin-top: 4px;">Uppercase labels (12px)</div>
                                </div>
                                <div style="padding: 12px 16px; background: rgba(245,158,11,0.04); border: 1px solid rgba(245,158,11,0.1); border-radius: 8px;">
                                    <div style="font-size: 11px; font-weight: 700; color: var(--accent-amber);">WIDEST: 1px+</div>
                                    <div style="font-size: 11px; font-weight: 500; color: var(--nx-text-muted); margin-top: 4px;">Micro/caption uppercase</div>
                                </div>
                                <div style="padding: 12px 16px; background: rgba(139,92,246,0.04); border: 1px solid rgba(139,92,246,0.1); border-radius: 8px;">
                                    <div style="font-size: 11px; font-weight: 700; color: var(--accent-purple);">RULE</div>
                                    <div style="font-size: 11px; font-weight: 500; color: var(--nx-text-muted); margin-top: 4px;">Smaller + uppercase = wider</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== SECTION 1: TEXT - DARK ==================== -->
                <div class="smf-section" id="sec-text-dark">
                    <div class="smf-section-header">
                        <div class="smf-section-num" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">1</div>
                        <div class="smf-section-title">Text Hierarchy — Dark Background</div>
                        <div class="smf-section-count">9 Styles</div>
                    </div>
                    <div class="smf-grid smf-grid-3">
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-cyan">T1</div>
                            <div class="smf-name">Page Title</div>
                            <div class="smf-render"><span class="demo-t1">Director Master</span></div>
                            <div class="smf-specs">28px &middot; Weight 800 &middot; #f1f5f9 &middot; ls 0.5px</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-cyan">T2</div>
                            <div class="smf-name">Section Heading</div>
                            <div class="smf-render"><span class="demo-t2">Personal Details</span></div>
                            <div class="smf-specs">18px &middot; Weight 700 &middot; #e2e8f0 &middot; ls 0.5px</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-cyan">T3</div>
                            <div class="smf-name">Card Title</div>
                            <div class="smf-render"><span class="demo-t3">Yudeshan Gounden</span></div>
                            <div class="smf-specs">16px &middot; Weight 700 &middot; #f1f5f9 &middot; ls 0.5px</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-cyan">T4</div>
                            <div class="smf-name">Subtitle</div>
                            <div class="smf-render"><span class="demo-t4">Finance Manager at GTI Logistics</span></div>
                            <div class="smf-specs">14px &middot; Weight 600 &middot; #94a3b8 &middot; ls 0.2px</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-cyan">T5</div>
                            <div class="smf-name">Body Text</div>
                            <div class="smf-render"><span class="demo-t5">The platform provides enterprise-grade management of clients, contacts, directors, and financial records.</span></div>
                            <div class="smf-specs">14px &middot; Weight 500 &middot; #cbd5e1 &middot; lh 1.6 &middot; ls 0.3px</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-cyan">T6</div>
                            <div class="smf-name">Label</div>
                            <div class="smf-render"><span class="demo-t6">Email Address</span></div>
                            <div class="smf-specs">12px &middot; Weight 700 &middot; #94a3b8 &middot; UPPERCASE &middot; ls 0.8px</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-cyan">T7</div>
                            <div class="smf-name">Data Value</div>
                            <div class="smf-render"><span class="demo-t7">info@nexsus.co.za</span></div>
                            <div class="smf-specs">14px &middot; Weight 600 &middot; #e2e8f0 &middot; ls 0.2px</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-cyan">T8</div>
                            <div class="smf-name">Muted Text</div>
                            <div class="smf-render"><span class="demo-t8">Last updated 3 hours ago</span></div>
                            <div class="smf-specs">12px &middot; Weight 500 &middot; #64748b &middot; ls 0.3px</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-cyan">T9</div>
                            <div class="smf-name">Caption / Micro</div>
                            <div class="smf-render"><span class="demo-t9">Registration No. 2024/012345/07</span></div>
                            <div class="smf-specs">10px &middot; Weight 700 &middot; #475569 &middot; UPPERCASE &middot; ls 1px</div>
                        </div>
                    </div>
                </div>

                <!-- ==================== SECTION 2: TEXT - LIGHT ==================== -->
                <div class="smf-section" id="sec-text-light">
                    <div class="smf-section-header">
                        <div class="smf-section-num" style="background: linear-gradient(135deg, #f59e0b, #d97706);">2</div>
                        <div class="smf-section-title">Text Hierarchy — Light Background (Forms / Modals)</div>
                        <div class="smf-section-count">5 Styles</div>
                    </div>
                    <div class="smf-light-panel">
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
                            <div style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px;">
                                <div class="smf-code smf-code-amber" style="margin-bottom: 12px;">LT1</div>
                                <div style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Modal Title</div>
                                <div class="demo-lt1" style="margin-bottom: 12px;">Add New Director</div>
                                <div style="font-size: 10px; font-weight: 600; color: #94a3b8; font-family: 'Courier New', monospace;">20px &middot; Weight 800 &middot; #0f172a</div>
                            </div>
                            <div style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px;">
                                <div class="smf-code smf-code-amber" style="margin-bottom: 12px;">LT2</div>
                                <div style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Section Title</div>
                                <div class="demo-lt2" style="color: #06b6d4; margin-bottom: 12px;">Personal Details</div>
                                <div style="font-size: 10px; font-weight: 600; color: #94a3b8; font-family: 'Courier New', monospace;">13px &middot; Weight 800 &middot; Section Color &middot; UPPERCASE</div>
                            </div>
                            <div style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px;">
                                <div class="smf-code smf-code-amber" style="margin-bottom: 12px;">LT3</div>
                                <div style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Form Label</div>
                                <div class="demo-lt3" style="margin-bottom: 12px;">First Name</div>
                                <div style="font-size: 10px; font-weight: 600; color: #94a3b8; font-family: 'Courier New', monospace;">11px &middot; Weight 700 &middot; #475569 &middot; UPPERCASE</div>
                            </div>
                            <div style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px;">
                                <div class="smf-code smf-code-amber" style="margin-bottom: 12px;">LT4</div>
                                <div style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Input Text</div>
                                <div class="demo-lt4" style="margin-bottom: 12px;">Yudeshan Gounden</div>
                                <div style="font-size: 10px; font-weight: 600; color: #94a3b8; font-family: 'Courier New', monospace;">14px &middot; Weight 600 &middot; #0f172a</div>
                            </div>
                            <div style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px;">
                                <div class="smf-code smf-code-amber" style="margin-bottom: 12px;">LT5</div>
                                <div style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Placeholder Text</div>
                                <div class="demo-lt5" style="margin-bottom: 12px;">Enter first name...</div>
                                <div style="font-size: 10px; font-weight: 600; color: #94a3b8; font-family: 'Courier New', monospace;">13px &middot; Weight 400 &middot; #94a3b8</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== SECTION 3: BUTTONS ==================== -->
                <div class="smf-section" id="sec-buttons">
                    <div class="smf-section-header">
                        <div class="smf-section-num" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">3</div>
                        <div class="smf-section-title">Buttons</div>
                        <div class="smf-section-count">9 Styles</div>
                    </div>
                    <div class="smf-grid smf-grid-3">
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-blue">B1</div>
                            <div class="smf-name">Primary Action</div>
                            <div class="smf-render"><button class="demo-btn demo-b1"><i class="fas fa-plus"></i> New Director</button></div>
                            <div class="smf-specs">Gradient Cyan &middot; 13px &middot; Weight 700 &middot; Glow Shadow</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-blue">B2</div>
                            <div class="smf-name">Success / Save</div>
                            <div class="smf-render"><button class="demo-btn demo-b2"><i class="fas fa-check"></i> Save Changes</button></div>
                            <div class="smf-specs">Gradient Green &middot; 13px &middot; Weight 700 &middot; Glow Shadow</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-blue">B3</div>
                            <div class="smf-name">Ghost / Secondary</div>
                            <div class="smf-render"><button class="demo-btn demo-b3"><i class="fas fa-filter"></i> Filters</button></div>
                            <div class="smf-specs">Transparent &middot; Border 1.5px &middot; #94a3b8 &middot; Hover Cyan</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-blue">B4</div>
                            <div class="smf-name">Danger / Delete</div>
                            <div class="smf-render"><button class="demo-btn demo-b4"><i class="fas fa-trash"></i> Delete</button></div>
                            <div class="smf-specs">Gradient Red &middot; 13px &middot; Weight 700 &middot; Glow Shadow</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-purple">B5</div>
                            <div class="smf-name">Accent / Secondary CTA</div>
                            <div class="smf-render"><button class="demo-btn demo-b6"><i class="fas fa-download"></i> Export</button></div>
                            <div class="smf-specs">Gradient Purple &middot; 13px &middot; Weight 700 &middot; Glow Shadow</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-amber">B6</div>
                            <div class="smf-name">Warning / Review</div>
                            <div class="smf-render"><button class="demo-btn demo-b7"><i class="fas fa-exclamation-triangle"></i> Review</button></div>
                            <div class="smf-specs">Gradient Amber &middot; 13px &middot; Weight 700 &middot; Glow Shadow</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-blue">B7</div>
                            <div class="smf-name">Info / View</div>
                            <div class="smf-render"><button class="demo-btn demo-b8"><i class="fas fa-eye"></i> View Details</button></div>
                            <div class="smf-specs">Gradient Blue &middot; 13px &middot; Weight 700 &middot; Glow Shadow</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-pink">B8</div>
                            <div class="smf-name">Share / Invite</div>
                            <div class="smf-render"><button class="demo-btn demo-b9"><i class="fas fa-share-alt"></i> Share</button></div>
                            <div class="smf-specs">Gradient Pink &middot; 13px &middot; Weight 700 &middot; Glow Shadow</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-pink">B9</div>
                            <div class="smf-name">Message Box Button</div>
                            <div class="smf-render"><button class="demo-btn demo-b9" style="min-width: 160px; text-align: center;"><i class="fas fa-check"></i> OK</button></div>
                            <div class="smf-specs">Gradient Pink &middot; 13px &middot; Weight 700 &middot; Min 160px &middot; Used for Message Boxes</div>
                        </div>
                    </div>
                </div>

                <!-- ==================== SECTION 3B: ICON BUTTONS ==================== -->
                <div class="smf-section" id="sec-icon-buttons">
                    <div class="smf-section-header">
                        <div class="smf-section-num" style="background: linear-gradient(135deg, #64748b, #475569);">3b</div>
                        <div class="smf-section-title">Icon Buttons</div>
                        <div class="smf-section-count">20 Styles</div>
                    </div>
                    <div class="smf-grid" style="grid-template-columns: repeat(5, 1fr);">
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-cyan">IB1</div>
                            <div class="smf-name">Edit</div>
                            <div class="smf-render" style="justify-content: center;"><button class="demo-ib demo-ib-edit"><i class="fas fa-pen"></i></button></div>
                            <div class="smf-specs">Hover: Cyan</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-blue">IB2</div>
                            <div class="smf-name">View</div>
                            <div class="smf-render" style="justify-content: center;"><button class="demo-ib demo-ib-view"><i class="fas fa-eye"></i></button></div>
                            <div class="smf-specs">Hover: Blue</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-red">IB3</div>
                            <div class="smf-name">Delete</div>
                            <div class="smf-render" style="justify-content: center;"><button class="demo-ib demo-ib-delete"><i class="fas fa-trash"></i></button></div>
                            <div class="smf-specs">Hover: Red</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-green">IB4</div>
                            <div class="smf-name">Email</div>
                            <div class="smf-render" style="justify-content: center;"><button class="demo-ib demo-ib-email"><i class="fas fa-envelope"></i></button></div>
                            <div class="smf-specs">Hover: Green</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-purple">IB5</div>
                            <div class="smf-name">Share</div>
                            <div class="smf-render" style="justify-content: center;"><button class="demo-ib demo-ib-share"><i class="fas fa-share-alt"></i></button></div>
                            <div class="smf-specs">Hover: Purple</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-green">IB6</div>
                            <div class="smf-name">WhatsApp</div>
                            <div class="smf-render" style="justify-content: center;"><button class="demo-ib demo-ib-whatsapp"><i class="fab fa-whatsapp"></i></button></div>
                            <div class="smf-specs">Hover: WA Green</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-amber">IB7</div>
                            <div class="smf-name">Calculator</div>
                            <div class="smf-render" style="justify-content: center;"><button class="demo-ib demo-ib-calc"><i class="fas fa-calculator"></i></button></div>
                            <div class="smf-specs">Hover: Amber</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-blue">IB8</div>
                            <div class="smf-name">Print</div>
                            <div class="smf-render" style="justify-content: center;"><button class="demo-ib demo-ib-print"><i class="fas fa-print"></i></button></div>
                            <div class="smf-specs">Hover: Blue</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-cyan">IB9</div>
                            <div class="smf-name">Download</div>
                            <div class="smf-render" style="justify-content: center;"><button class="demo-ib demo-ib-download"><i class="fas fa-download"></i></button></div>
                            <div class="smf-specs">Hover: Cyan</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-green">IB10</div>
                            <div class="smf-name">Phone</div>
                            <div class="smf-render" style="justify-content: center;"><button class="demo-ib demo-ib-phone"><i class="fas fa-phone"></i></button></div>
                            <div class="smf-specs">Hover: Green</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-purple">IB11</div>
                            <div class="smf-name">Link / URL</div>
                            <div class="smf-render" style="justify-content: center;"><button class="demo-ib demo-ib-link"><i class="fas fa-link"></i></button></div>
                            <div class="smf-specs">Hover: Purple</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-amber">IB12</div>
                            <div class="smf-name">Settings</div>
                            <div class="smf-render" style="justify-content: center;"><button class="demo-ib demo-ib-settings"><i class="fas fa-cog"></i></button></div>
                            <div class="smf-specs">Hover: Amber</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-cyan">IB13</div>
                            <div class="smf-name">Copy</div>
                            <div class="smf-render" style="justify-content: center;"><button class="demo-ib demo-ib-copy"><i class="fas fa-copy"></i></button></div>
                            <div class="smf-specs">Hover: Cyan</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-amber">IB14</div>
                            <div class="smf-name">Archive</div>
                            <div class="smf-render" style="justify-content: center;"><button class="demo-ib demo-ib-archive"><i class="fas fa-archive"></i></button></div>
                            <div class="smf-specs">Hover: Amber</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-green">IB15</div>
                            <div class="smf-name">Toggle On/Off</div>
                            <div class="smf-render" style="justify-content: center;"><button class="demo-ib demo-ib-toggle"><i class="fas fa-power-off"></i></button></div>
                            <div class="smf-specs">Hover: Green</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-red">IB16</div>
                            <div class="smf-name">Delink</div>
                            <div class="smf-render" style="justify-content: center;"><button class="demo-ib demo-ib-delink"><i class="fas fa-unlink"></i></button></div>
                            <div class="smf-specs">Hover: Red</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-amber">IB17</div>
                            <div class="smf-name">Weight / Scale</div>
                            <div class="smf-render" style="justify-content: center;"><button class="demo-ib demo-ib-weight"><i class="fas fa-scale-balanced"></i></button></div>
                            <div class="smf-specs">Hover: Amber</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-purple">IB18</div>
                            <div class="smf-name">Lab Test</div>
                            <div class="smf-render" style="justify-content: center;"><button class="demo-ib demo-ib-lab"><i class="fas fa-flask"></i></button></div>
                            <div class="smf-specs">Hover: Purple</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-blue">IB19</div>
                            <div class="smf-name">Inventory</div>
                            <div class="smf-render" style="justify-content: center;"><button class="demo-ib demo-ib-inventory"><i class="fas fa-boxes-stacked"></i></button></div>
                            <div class="smf-specs">Hover: Blue</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-amber">IB20</div>
                            <div class="smf-name">Fuel</div>
                            <div class="smf-render" style="justify-content: center;"><button class="demo-ib demo-ib-fuel"><i class="fas fa-gas-pump"></i></button></div>
                            <div class="smf-specs">Hover: Amber</div>
                        </div>
                    </div>
                </div>

                <!-- ==================== SECTION 3C: MESSAGE BOXES ==================== -->
                <div class="smf-section" id="sec-messages">
                    <div class="smf-section-header">
                        <div class="smf-section-num" style="background: linear-gradient(135deg, #f59e0b, #d97706);">3c</div>
                        <div class="smf-section-title">Message Boxes (nxAlert)</div>
                        <div class="smf-section-count">6 Styles</div>
                    </div>
                    <div class="smf-grid smf-grid-3" style="margin-bottom: 24px;">
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-green">M1</div>
                            <div class="smf-name">Success</div>
                            <button class="demo-msg-trigger tr-green" onclick="NxAlert.success('Success', 'Record has been saved successfully.')"><i class="fas fa-check-circle"></i> Test M1</button>
                            <div class="smf-specs">NxAlert.success()</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-red">M2</div>
                            <div class="smf-name">Error</div>
                            <button class="demo-msg-trigger tr-red" onclick="NxAlert.error('Error', 'Something went wrong. Please try again.')"><i class="fas fa-times-circle"></i> Test M2</button>
                            <div class="smf-specs">NxAlert.error()</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-amber">M3</div>
                            <div class="smf-name">Warning</div>
                            <button class="demo-msg-trigger tr-amber" onclick="NxAlert.warning('Warning', 'Please review before proceeding.')"><i class="fas fa-exclamation-triangle"></i> Test M3</button>
                            <div class="smf-specs">NxAlert.warning()</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-blue">M4</div>
                            <div class="smf-name">Info</div>
                            <button class="demo-msg-trigger tr-blue" onclick="NxAlert.info('Information', 'For your information — this is a general notice.')"><i class="fas fa-info-circle"></i> Test M4</button>
                            <div class="smf-specs">NxAlert.info()</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-cyan">M5</div>
                            <div class="smf-name">Confirm</div>
                            <button class="demo-msg-trigger tr-cyan" onclick="NxAlert.confirm('Confirm Action', 'Are you sure you want to proceed?')"><i class="fas fa-question-circle"></i> Test M5</button>
                            <div class="smf-specs">NxAlert.confirm()</div>
                        </div>
                        <div class="smf-specimen" style="text-align: center;">
                            <div class="smf-code smf-code-red">M6</div>
                            <div class="smf-name">Delete</div>
                            <button class="demo-msg-trigger tr-red" onclick="NxAlert.delete('Delete Record', 'Krish Moodley')"><i class="fas fa-trash"></i> Test M6</button>
                            <div class="smf-specs">NxAlert.delete()</div>
                        </div>
                    </div>
                </div>

                <!-- ==================== SECTION 4: BADGES ==================== -->
                <div class="smf-section" id="sec-badges">
                    <div class="smf-section-header">
                        <div class="smf-section-num" style="background: linear-gradient(135deg, #22c55e, #16a34a);">4</div>
                        <div class="smf-section-title">Badges &amp; Tags</div>
                        <div class="smf-section-count">8 Styles</div>
                    </div>
                    <div class="smf-grid smf-grid-4">
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-green">BG1</div>
                            <div class="smf-name">Active / Success</div>
                            <div class="smf-render"><span class="demo-badge demo-bg1"><i class="fas fa-circle"></i> Active</span></div>
                            <div class="smf-specs">Green &middot; 10px &middot; Weight 700 &middot; Pill</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-red">BG2</div>
                            <div class="smf-name">Inactive / Error</div>
                            <div class="smf-render"><span class="demo-badge demo-bg2"><i class="fas fa-circle"></i> Inactive</span></div>
                            <div class="smf-specs">Red &middot; 10px &middot; Weight 700 &middot; Pill</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-amber">BG3</div>
                            <div class="smf-name">Pending / Warning</div>
                            <div class="smf-render"><span class="demo-badge demo-bg3"><i class="fas fa-circle"></i> Pending</span></div>
                            <div class="smf-specs">Amber &middot; 10px &middot; Weight 700 &middot; Pill</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-cyan">BG4</div>
                            <div class="smf-name">Info / Highlight</div>
                            <div class="smf-render"><span class="demo-badge demo-bg4"><i class="fas fa-star"></i> Primary</span></div>
                            <div class="smf-specs">Cyan &middot; 10px &middot; Weight 700 &middot; Pill</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-purple">BG5</div>
                            <div class="smf-name">Category / Type</div>
                            <div class="smf-render"><span class="demo-badge demo-bg5">Accounts</span></div>
                            <div class="smf-specs">Purple &middot; 10px &middot; Weight 700 &middot; Pill</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-blue">BG6</div>
                            <div class="smf-name">Role / Department</div>
                            <div class="smf-render"><span class="demo-badge demo-bg6">Finance</span></div>
                            <div class="smf-specs">Blue &middot; 10px &middot; Weight 700 &middot; Pill</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-pink">BG7</div>
                            <div class="smf-name">Special / Premium</div>
                            <div class="smf-render"><span class="demo-badge demo-bg7"><i class="fas fa-gem"></i> Premium</span></div>
                            <div class="smf-specs">Pink &middot; 10px &middot; Weight 700 &middot; Pill</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code" style="background: rgba(255,255,255,0.06); color: var(--nx-text-subtitle); border: 1px solid var(--border-default);">BG8</div>
                            <div class="smf-name">Neutral / Default</div>
                            <div class="smf-render"><span class="demo-badge demo-bg8">Resigned</span></div>
                            <div class="smf-specs">Neutral &middot; 10px &middot; Weight 700 &middot; Pill</div>
                        </div>
                    </div>
                </div>

                <!-- ==================== SECTION 5: CARDS ==================== -->
                <div class="smf-section" id="sec-cards">
                    <div class="smf-section-header">
                        <div class="smf-section-num" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">5</div>
                        <div class="smf-section-title">Cards</div>
                        <div class="smf-section-count">3 Styles</div>
                    </div>

                    <!-- C1 - Standard Card -->
                    <div style="margin-bottom: 28px;">
                        <div class="smf-code smf-code-purple" style="margin-bottom: 12px;">C1 &mdash; Standard Card</div>
                        <div class="demo-card-c1">
                            <div class="demo-t3" style="margin-bottom: 8px;">Standard Container Card</div>
                            <div class="demo-t5">Used as a general container for grouping content. Features subtle background, subtle border, and 10px radius. Suitable for wrapping any block of related information.</div>
                            <div class="smf-specs" style="margin-top: 16px;">bg: rgba(255,255,255,0.03) &middot; border: rgba(255,255,255,0.06) &middot; radius: 10px &middot; padding: 24px</div>
                        </div>
                    </div>

                    <!-- C2 - Stat Cards -->
                    <div style="margin-bottom: 28px;">
                        <div class="smf-code smf-code-purple" style="margin-bottom: 12px;">C2 &mdash; Stat Card (with accent top bar)</div>
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
                            <div class="demo-card-c2 accent-cyan">
                                <div class="stat-label">Total Directors</div>
                                <div class="stat-val">24</div>
                                <div class="stat-meta">Across 8 companies</div>
                            </div>
                            <div class="demo-card-c2 accent-green">
                                <div class="stat-label">Active</div>
                                <div class="stat-val">18</div>
                                <div class="stat-meta">Currently serving</div>
                            </div>
                            <div class="demo-card-c2 accent-amber">
                                <div class="stat-label">Pending Review</div>
                                <div class="stat-val">4</div>
                                <div class="stat-meta">Awaiting approval</div>
                            </div>
                            <div class="demo-card-c2 accent-blue">
                                <div class="stat-label">Resigned</div>
                                <div class="stat-val">2</div>
                                <div class="stat-meta">Past 12 months</div>
                            </div>
                        </div>
                        <div class="smf-specs" style="margin-top: 12px;">bg: rgba(255,255,255,0.04) &middot; 3px gradient top bar &middot; radius: 10px &middot; padding: 20px</div>
                    </div>

                    <!-- C3 - List Item Card -->
                    <div>
                        <div class="smf-code smf-code-purple" style="margin-bottom: 12px;">C3 &mdash; List Item Card (Full Width, Hover Glow)</div>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <div class="demo-card-c3">
                                <div class="c3-avatar" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">YG</div>
                                <div class="c3-info">
                                    <div class="c3-name">Yudeshan Gounden</div>
                                    <div class="c3-sub">Finance Manager &middot; GTI Logistics</div>
                                </div>
                                <span class="demo-badge demo-bg1" style="font-size: 9px;"><i class="fas fa-circle"></i> Active</span>
                                <div class="c3-actions">
                                    <button class="demo-btn demo-b5" style="width: 32px; height: 32px;"><i class="fas fa-pen" style="font-size: 11px;"></i></button>
                                    <button class="demo-btn demo-b5" style="width: 32px; height: 32px;"><i class="fas fa-eye" style="font-size: 11px;"></i></button>
                                </div>
                            </div>
                            <div class="demo-card-c3">
                                <div class="c3-avatar" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">TN</div>
                                <div class="c3-info">
                                    <div class="c3-name">Themba Ndlovu</div>
                                    <div class="c3-sub">Fleet Manager &middot; Primary Contact</div>
                                </div>
                                <span class="demo-badge demo-bg4" style="font-size: 9px;"><i class="fas fa-star"></i> Primary</span>
                                <div class="c3-actions">
                                    <button class="demo-btn demo-b5" style="width: 32px; height: 32px;"><i class="fas fa-pen" style="font-size: 11px;"></i></button>
                                    <button class="demo-btn demo-b5" style="width: 32px; height: 32px;"><i class="fas fa-eye" style="font-size: 11px;"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="smf-specs" style="margin-top: 12px;">bg: rgba(255,255,255,0.025) &middot; border-left 3px on hover &middot; radius: 10px &middot; flex row &middot; padding: 20px 24px</div>
                    </div>
                </div>

                <!-- ==================== SECTION 6: FORM ELEMENTS ==================== -->
                <div class="smf-section" id="sec-forms">
                    <div class="smf-section-header">
                        <div class="smf-section-num" style="background: linear-gradient(135deg, #f59e0b, #d97706);">6</div>
                        <div class="smf-section-title">Form Elements (Light Theme)</div>
                        <div class="smf-section-count">6 Styles</div>
                    </div>
                    <div class="smf-light-panel">
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
                            <!-- F1 -->
                            <div style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px;">
                                <div class="smf-code smf-code-amber" style="margin-bottom: 12px;">F1</div>
                                <div style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">Text Input</div>
                                <div class="demo-form-label">First Name</div>
                                <input type="text" class="demo-form-input" placeholder="Enter first name..." value="Yudeshan">
                                <div style="font-size: 10px; font-weight: 600; color: #94a3b8; font-family: 'Courier New', monospace; margin-top: 10px;">14px &middot; Weight 600 &middot; #0f172a &middot; border #e2e8f0</div>
                            </div>
                            <!-- F2 -->
                            <div style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px;">
                                <div class="smf-code smf-code-amber" style="margin-bottom: 12px;">F2</div>
                                <div style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">Select / Dropdown</div>
                                <div class="demo-form-label">Identity Type</div>
                                <select class="demo-form-select">
                                    <option value="">Select type...</option>
                                    <option value="SA ID" selected>SA ID</option>
                                    <option value="Passport">Passport</option>
                                </select>
                                <div style="font-size: 10px; font-weight: 600; color: #94a3b8; font-family: 'Courier New', monospace; margin-top: 10px;">13px &middot; Weight 600 &middot; #0f172a &middot; custom arrow</div>
                            </div>
                            <!-- F3 -->
                            <div style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px;">
                                <div class="smf-code smf-code-amber" style="margin-bottom: 12px;">F3</div>
                                <div style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">Date Picker</div>
                                <div class="demo-form-label">Date of Birth</div>
                                <input type="text" class="demo-form-input" placeholder="Select date..." value="15 Mar 1985" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27%2394a3b8%27%3E%3Cpath d=%27M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z%27/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 12px center; background-size: 18px;">
                                <div style="font-size: 10px; font-weight: 600; color: #94a3b8; font-family: 'Courier New', monospace; margin-top: 10px;">14px &middot; flatpickr &middot; format: j M Y</div>
                            </div>
                            <!-- F4 -->
                            <div style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px;">
                                <div class="smf-code smf-code-amber" style="margin-bottom: 12px;">F4</div>
                                <div style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">Textarea</div>
                                <div class="demo-form-label">Notes</div>
                                <textarea class="demo-form-textarea" placeholder="Enter notes...">Director appointed on 15 Mar 2024 with full signing authority.</textarea>
                                <div style="font-size: 10px; font-weight: 600; color: #94a3b8; font-family: 'Courier New', monospace; margin-top: 10px;">14px &middot; Weight 500 &middot; min-height 80px &middot; resizable</div>
                            </div>
                            <!-- F5 -->
                            <div style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px;">
                                <div class="smf-code smf-code-amber" style="margin-bottom: 12px;">F5</div>
                                <div style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">Toggle / Switch</div>
                                <div class="demo-form-label">Active Status</div>
                                <div style="display: flex; gap: 24px; margin-top: 8px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div class="demo-form-toggle" onclick="this.classList.toggle('active')"></div>
                                        <span style="font-size: 12px; font-weight: 600; color: #64748b;">Off</span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div class="demo-form-toggle active" onclick="this.classList.toggle('active')"></div>
                                        <span style="font-size: 12px; font-weight: 600; color: #0f172a;">On</span>
                                    </div>
                                </div>
                                <div style="font-size: 10px; font-weight: 600; color: #94a3b8; font-family: 'Courier New', monospace; margin-top: 10px;">44x24px &middot; Cyan when active &middot; White knob</div>
                            </div>
                            <!-- F6 -->
                            <div style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px;">
                                <div class="smf-code smf-code-amber" style="margin-bottom: 12px;">F6</div>
                                <div style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">Checkbox</div>
                                <div class="demo-form-label">Permissions</div>
                                <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 8px;">
                                    <label class="demo-form-check"><input type="checkbox" checked> Signing Authority</label>
                                    <label class="demo-form-check"><input type="checkbox" checked> Financial Access</label>
                                    <label class="demo-form-check"><input type="checkbox"> Admin Rights</label>
                                </div>
                                <div style="font-size: 10px; font-weight: 600; color: #94a3b8; font-family: 'Courier New', monospace; margin-top: 10px;">18x18px &middot; Cyan accent &middot; 13px label</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== SECTION 7: FORM SECTIONS ==================== -->
                <div class="smf-section" id="sec-sections">
                    <div class="smf-section-header">
                        <div class="smf-section-num" style="background: linear-gradient(135deg, #ec4899, #db2777);">7</div>
                        <div class="smf-section-title">Form Section Headers (Light Theme)</div>
                        <div class="smf-section-count">5 Colors</div>
                    </div>
                    <div class="smf-light-panel">
                        <div style="margin-bottom: 8px;">
                            <div class="smf-code smf-code-cyan" style="margin-bottom: 16px;">FS1-FS5 &mdash; Section Headers with Color Coding</div>
                        </div>
                        <div class="demo-section-line sec-cyan">
                            <i class="fas fa-user"></i>
                            <span>FS1 &mdash; Personal Details</span>
                            <div class="line"></div>
                        </div>
                        <div class="demo-section-line sec-blue">
                            <i class="fas fa-id-card"></i>
                            <span>FS2 &mdash; Identification &amp; Tax</span>
                            <div class="line"></div>
                        </div>
                        <div class="demo-section-line sec-purple">
                            <i class="fas fa-building"></i>
                            <span>FS3 &mdash; SARS &amp; Contact</span>
                            <div class="line"></div>
                        </div>
                        <div class="demo-section-line sec-green">
                            <i class="fas fa-briefcase"></i>
                            <span>FS4 &mdash; Employment</span>
                            <div class="line"></div>
                        </div>
                        <div class="demo-section-line sec-amber">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>FS5 &mdash; Addresses</span>
                            <div class="line"></div>
                        </div>
                        <div style="font-size: 10px; font-weight: 600; color: #94a3b8; font-family: 'Courier New', monospace; margin-top: 16px; padding-top: 12px; border-top: 1px solid #e2e8f0;">
                            13px &middot; Weight 800 &middot; UPPERCASE &middot; Color-coded icon + text + line &middot; Section accent bg
                        </div>
                    </div>
                </div>

                <!-- ==================== SECTION 8: STAT BLOCKS ==================== -->
                <div class="smf-section" id="sec-stats">
                    <div class="smf-section-header">
                        <div class="smf-section-num" style="background: linear-gradient(135deg, #ef4444, #dc2626);">8</div>
                        <div class="smf-section-title">Stat Blocks</div>
                        <div class="smf-section-count">2 Styles</div>
                    </div>
                    <div class="smf-grid smf-grid-2">
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-red">ST1</div>
                            <div class="smf-name">Large Stat Block</div>
                            <div class="smf-render">
                                <div class="demo-stat-lg">
                                    <div class="stat-number">1,247</div>
                                    <div class="stat-label">Total Revenue (ZAR)</div>
                                    <div class="stat-change up"><i class="fas fa-arrow-up"></i> 12.4% from last month</div>
                                </div>
                            </div>
                            <div class="smf-specs">Number: 36px Weight 900 #f1f5f9 &middot; Label: 12px Weight 700 #94a3b8</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-red">ST2</div>
                            <div class="smf-name">Compact Stat (Inline)</div>
                            <div class="smf-render">
                                <div style="display: flex; gap: 40px;">
                                    <div class="demo-stat-sm">
                                        <div class="stat-number" style="color: var(--accent-cyan);">18</div>
                                        <div class="stat-label">Active</div>
                                    </div>
                                    <div class="demo-stat-sm">
                                        <div class="stat-number" style="color: var(--accent-amber);">4</div>
                                        <div class="stat-label">Pending</div>
                                    </div>
                                    <div class="demo-stat-sm">
                                        <div class="stat-number" style="color: var(--accent-red);">2</div>
                                        <div class="stat-label">Resigned</div>
                                    </div>
                                </div>
                            </div>
                            <div class="smf-specs">Number: 22px Weight 800 &middot; Accent color &middot; Label: 11px Weight 600 #64748b</div>
                        </div>
                    </div>
                </div>

                <!-- ==================== SECTION 9: AVATARS ==================== -->
                <div class="smf-section" id="sec-avatars">
                    <div class="smf-section-header">
                        <div class="smf-section-num" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">9</div>
                        <div class="smf-section-title">Avatars</div>
                        <div class="smf-section-count">3 Sizes</div>
                    </div>
                    <div class="smf-grid smf-grid-3">
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-cyan">AV1</div>
                            <div class="smf-name">Large Avatar (80px)</div>
                            <div class="smf-render" style="justify-content: center;">
                                <div class="demo-avatar demo-av-lg" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">YG</div>
                            </div>
                            <div class="smf-specs">80x80px &middot; radius 16px &middot; 26px initials &middot; Gradient bg</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-cyan">AV2</div>
                            <div class="smf-name">Medium Avatar (48px)</div>
                            <div class="smf-render" style="gap: 12px; justify-content: center;">
                                <div class="demo-avatar demo-av-md" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">TN</div>
                                <div class="demo-avatar demo-av-md" style="background: linear-gradient(135deg, #22c55e, #16a34a);">SM</div>
                                <div class="demo-avatar demo-av-md" style="background: linear-gradient(135deg, #f59e0b, #d97706);">RP</div>
                            </div>
                            <div class="smf-specs">48x48px &middot; radius 12px &middot; 16px initials &middot; Gradient bg</div>
                        </div>
                        <div class="smf-specimen">
                            <div class="smf-code smf-code-cyan">AV3</div>
                            <div class="smf-name">Small Avatar (36px)</div>
                            <div class="smf-render" style="gap: 10px; justify-content: center;">
                                <div class="demo-avatar demo-av-sm" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">KS</div>
                                <div class="demo-avatar demo-av-sm" style="background: linear-gradient(135deg, #ec4899, #db2777);">JD</div>
                                <div class="demo-avatar demo-av-sm" style="background: linear-gradient(135deg, #ef4444, #dc2626);">AB</div>
                                <div class="demo-avatar demo-av-sm" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">MZ</div>
                            </div>
                            <div class="smf-specs">36x36px &middot; radius 8px &middot; 12px initials &middot; Gradient bg</div>
                        </div>
                    </div>
                </div>

                <!-- ==================== SECTION 10: ACTION BAR ==================== -->
                <div class="smf-section" id="sec-actionbar">
                    <div class="smf-section-header">
                        <div class="smf-section-num" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">10</div>
                        <div class="smf-section-title">Action Bars</div>
                        <div class="smf-section-count">1 Style</div>
                    </div>
                    <div style="margin-bottom: 16px;">
                        <div class="smf-code smf-code-blue" style="margin-bottom: 12px;">AB1 &mdash; Search + Action Bar</div>
                    </div>
                    <div class="demo-action-bar">
                        <div class="demo-search-wrap">
                            <i class="fas fa-search"></i>
                            <input type="text" class="demo-search-input" placeholder="Search directors by name, ID, or company...">
                        </div>
                        <button class="demo-btn demo-b3" style="padding: 9px 16px; font-size: 12px;"><i class="fas fa-filter"></i> Filter</button>
                        <button class="demo-btn demo-b3" style="padding: 9px 16px; font-size: 12px;"><i class="fas fa-sort"></i> Sort</button>
                        <button class="demo-btn demo-b1" style="padding: 9px 18px; font-size: 12px;"><i class="fas fa-plus"></i> New Director</button>
                    </div>
                    <div class="smf-specs" style="margin-top: 12px;">
                        bg: rgba(255,255,255,0.025) &middot; border subtle &middot; radius: 10px &middot; padding: 14px 20px &middot; flex row &middot; gap: 12px
                    </div>
                </div>

                <!-- ==================== SECTION 11: LAYOUT GRIDS ==================== -->
                <div class="smf-section" id="sec-grids">
                    <div class="smf-section-header">
                        <div class="smf-section-num" style="background: linear-gradient(135deg, #22c55e, #16a34a);">11</div>
                        <div class="smf-section-title">Layout Grids (Form Columns)</div>
                        <div class="smf-section-count">3 Layouts</div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <div class="smf-code smf-code-green" style="margin-bottom: 12px;">G1 &mdash; 2-Column Grid (nx-form-row)</div>
                        <div class="demo-grid-row g2">
                            <div class="demo-grid-cell">Column 1 of 2</div>
                            <div class="demo-grid-cell">Column 2 of 2</div>
                        </div>
                        <div class="smf-specs">grid-template-columns: repeat(2, 1fr) &middot; gap: 16px</div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <div class="smf-code smf-code-green" style="margin-bottom: 12px;">G2 &mdash; 3-Column Grid (nx-form-row-3)</div>
                        <div class="demo-grid-row g3">
                            <div class="demo-grid-cell">Column 1 of 3</div>
                            <div class="demo-grid-cell">Column 2 of 3</div>
                            <div class="demo-grid-cell">Column 3 of 3</div>
                        </div>
                        <div class="smf-specs">grid-template-columns: repeat(3, 1fr) &middot; gap: 16px</div>
                    </div>

                    <div>
                        <div class="smf-code smf-code-green" style="margin-bottom: 12px;">G3 &mdash; 4-Column Grid (nx-form-row-4)</div>
                        <div class="demo-grid-row g4">
                            <div class="demo-grid-cell">Col 1 of 4</div>
                            <div class="demo-grid-cell">Col 2 of 4</div>
                            <div class="demo-grid-cell">Col 3 of 4</div>
                            <div class="demo-grid-cell">Col 4 of 4</div>
                        </div>
                        <div class="smf-specs">grid-template-columns: repeat(4, 1fr) &middot; gap: 16px</div>
                    </div>
                </div>

                <!-- ==================== SECTION 12: SIDEBAR ==================== -->
                <div class="smf-section" id="sec-sidebar">
                    <div class="smf-section-header">
                        <div class="smf-section-num" style="background: linear-gradient(135deg, #059669, #047857);">12</div>
                        <div class="smf-section-title">System Sidebar (SB1)</div>
                        <div class="smf-section-count">1 Component</div>
                    </div>

                    <div class="smf-specimen" style="margin-bottom: 16px;">
                        <div class="smf-code smf-code-green">SB1</div>
                        <div class="smf-name">System Master Sidebar</div>
                        <div class="smf-specs">
                            Fixed left &middot; 260px expanded / 72px collapsed &middot; Dark gradient background &middot; Montserrat font<br>
                            Features: Collapse/expand with memory, mobile slide-in, active item highlighting, tooltips when collapsed, Ctrl+B shortcut<br>
                            Files: system_master_sidebar.css + system_master_sidebar.js + system_master_sidebar.blade.php<br>
                            Branding: Logo from /public/nexcore/branding/ &middot; 4-colour gradient footer line<br>
                            Usage: @@include('nexcore.system_master_sidebar', ['menuItems' => [...]])
                        </div>
                    </div>

                    <!-- Live Preview - embedded in a container -->
                    <div style="position: relative; height: 500px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border-default); background: #0a0e1a;">
                        <link href="/public/nexcore/system_sidebar/css/system_master_sidebar.css" rel="stylesheet">
                        <aside class="nxsb" id="nxSidebarDemo" style="position: absolute; z-index: 10;">
                            <div class="nxsb-brand">
                                <a href="#" title="NexCore Home" onclick="return false;">
                                    <img src="/public/nexcore/branding/nexcore-logo-light.jpg" alt="NexCore" class="nxsb-brand-logo">
                                    <img src="/public/nexcore/branding/nexcore-icon.png" alt="N" class="nxsb-brand-icon">
                                </a>
                                <button class="nxsb-toggle" onclick="var s=document.getElementById('nxSidebarDemo');s.classList.toggle('nxsb-collapsed');" title="Toggle Sidebar">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                            </div>
                            <nav class="nxsb-menu">
                                <div class="nxsb-group">
                                    <div class="nxsb-group-label">Main</div>
                                    <a href="#" class="nxsb-item nxsb-active" data-tooltip="Dashboard" onclick="return false;">
                                        <span class="nxsb-item-icon"><i class="fas fa-th-large"></i></span>
                                        <span class="nxsb-item-text">Dashboard</span>
                                    </a>
                                    <a href="#" class="nxsb-item" data-tooltip="Directors" onclick="return false;">
                                        <span class="nxsb-item-icon"><i class="fas fa-user-tie"></i></span>
                                        <span class="nxsb-item-text">Directors</span>
                                        <span class="nxsb-badge">24</span>
                                    </a>
                                    <a href="#" class="nxsb-item" data-tooltip="Clients" onclick="return false;">
                                        <span class="nxsb-item-icon"><i class="fas fa-building"></i></span>
                                        <span class="nxsb-item-text">Clients</span>
                                    </a>
                                    <a href="#" class="nxsb-item" data-tooltip="Contacts" onclick="return false;">
                                        <span class="nxsb-item-icon"><i class="fas fa-address-book"></i></span>
                                        <span class="nxsb-item-text">Contacts</span>
                                    </a>
                                </div>
                                <div class="nxsb-divider"></div>
                                <div class="nxsb-group">
                                    <div class="nxsb-group-label">Practice</div>
                                    <a href="#" class="nxsb-item" data-tooltip="Tasks" onclick="return false;">
                                        <span class="nxsb-item-icon"><i class="fas fa-tasks"></i></span>
                                        <span class="nxsb-item-text">Tasks</span>
                                        <span class="nxsb-badge" style="background: var(--nxsb-accent-amber);">5</span>
                                    </a>
                                    <a href="#" class="nxsb-item" data-tooltip="Meetings" onclick="return false;">
                                        <span class="nxsb-item-icon"><i class="fas fa-calendar-alt"></i></span>
                                        <span class="nxsb-item-text">Meetings</span>
                                    </a>
                                    <a href="#" class="nxsb-item" data-tooltip="Documents" onclick="return false;">
                                        <span class="nxsb-item-icon"><i class="fas fa-folder-open"></i></span>
                                        <span class="nxsb-item-text">Documents</span>
                                    </a>
                                </div>
                                <div class="nxsb-divider"></div>
                                <div class="nxsb-group">
                                    <div class="nxsb-group-label">System</div>
                                    <a href="#" class="nxsb-item" data-tooltip="Settings" onclick="return false;">
                                        <span class="nxsb-item-icon"><i class="fas fa-cog"></i></span>
                                        <span class="nxsb-item-text">Settings</span>
                                    </a>
                                    <a href="#" class="nxsb-item" data-tooltip="Audit Trail" onclick="return false;">
                                        <span class="nxsb-item-icon"><i class="fas fa-history"></i></span>
                                        <span class="nxsb-item-text">Audit Trail</span>
                                    </a>
                                </div>
                            </nav>
                            <div class="nxsb-footer">
                                <div class="nxsb-footer-text">NexCore Africa Proprietary Limited</div>
                                <div class="nxsb-footer-line"></div>
                            </div>
                        </aside>
                        <!-- Sample content area next to sidebar -->
                        <div style="margin-left: 260px; padding: 32px; transition: margin-left 0.3s ease;" id="nxSidebarDemoContent">
                            <div style="color: rgba(255,255,255,0.4); font-size: 13px; font-family: 'Montserrat', sans-serif; letter-spacing: 0.5px;">
                                <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Click the chevron to collapse/expand the sidebar
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== QUICK REFERENCE ==================== -->
                <div class="smf-section" id="sec-reference" style="margin-bottom: 80px;">
                    <div class="smf-section-header">
                        <div class="smf-section-num" style="background: linear-gradient(135deg, #64748b, #475569);">*</div>
                        <div class="smf-section-title">Quick Reference Table</div>
                    </div>
                    <div class="demo-card-c1">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align: left; padding: 10px 16px; font-size: 10px; font-weight: 700; color: var(--nx-text-caption); text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid var(--border-default);">Code</th>
                                    <th style="text-align: left; padding: 10px 16px; font-size: 10px; font-weight: 700; color: var(--nx-text-caption); text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid var(--border-default);">Element</th>
                                    <th style="text-align: left; padding: 10px 16px; font-size: 10px; font-weight: 700; color: var(--nx-text-caption); text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid var(--border-default);">Size</th>
                                    <th style="text-align: left; padding: 10px 16px; font-size: 10px; font-weight: 700; color: var(--nx-text-caption); text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid var(--border-default);">Weight</th>
                                    <th style="text-align: left; padding: 10px 16px; font-size: 10px; font-weight: 700; color: var(--nx-text-caption); text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid var(--border-default);">Color</th>
                                    <th style="text-align: left; padding: 10px 16px; font-size: 10px; font-weight: 700; color: var(--nx-text-caption); text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid var(--border-default);">Letter Sp.</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 12px;">
                                <tr style="border-bottom: 1px solid var(--border-subtle);">
                                    <td style="padding: 8px 16px;"><span class="smf-code smf-code-cyan" style="margin: 0; font-size: 9px; padding: 2px 8px;">T1</span></td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-value); font-weight: 600;">Page Title</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">28px</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">800</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">#f1f5f9</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">0.5px</td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-subtle);">
                                    <td style="padding: 8px 16px;"><span class="smf-code smf-code-cyan" style="margin: 0; font-size: 9px; padding: 2px 8px;">T2</span></td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-value); font-weight: 600;">Section Heading</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">18px</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">700</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">#e2e8f0</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">0.5px</td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-subtle);">
                                    <td style="padding: 8px 16px;"><span class="smf-code smf-code-cyan" style="margin: 0; font-size: 9px; padding: 2px 8px;">T3</span></td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-value); font-weight: 600;">Card Title</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">16px</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">700</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">#f1f5f9</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">0.5px</td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-subtle);">
                                    <td style="padding: 8px 16px;"><span class="smf-code smf-code-cyan" style="margin: 0; font-size: 9px; padding: 2px 8px;">T4</span></td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-value); font-weight: 600;">Subtitle</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">14px</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">600</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">#94a3b8</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">0.2px</td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-subtle);">
                                    <td style="padding: 8px 16px;"><span class="smf-code smf-code-cyan" style="margin: 0; font-size: 9px; padding: 2px 8px;">T5</span></td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-value); font-weight: 600;">Body Text</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">14px</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">500</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">#cbd5e1</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">0.3px</td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-subtle);">
                                    <td style="padding: 8px 16px;"><span class="smf-code smf-code-cyan" style="margin: 0; font-size: 9px; padding: 2px 8px;">T6</span></td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-value); font-weight: 600;">Label</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">12px</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">700</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">#94a3b8 UPPER</td>
                                    <td style="padding: 8px 16px; color: var(--accent-amber); font-family: 'Courier New', monospace; font-weight: 700;">0.8px</td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-subtle);">
                                    <td style="padding: 8px 16px;"><span class="smf-code smf-code-cyan" style="margin: 0; font-size: 9px; padding: 2px 8px;">T7</span></td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-value); font-weight: 600;">Data Value</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">14px</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">600</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">#e2e8f0</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">0.2px</td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-subtle);">
                                    <td style="padding: 8px 16px;"><span class="smf-code smf-code-cyan" style="margin: 0; font-size: 9px; padding: 2px 8px;">T8</span></td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-value); font-weight: 600;">Muted Text</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">12px</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">500</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">#64748b</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">0.3px</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 16px;"><span class="smf-code smf-code-cyan" style="margin: 0; font-size: 9px; padding: 2px 8px;">T9</span></td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-value); font-weight: 600;">Caption / Micro</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">10px</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">700</td>
                                    <td style="padding: 8px 16px; color: var(--nx-text-muted); font-family: 'Courier New', monospace;">#475569 UPPER</td>
                                    <td style="padding: 8px 16px; color: var(--accent-amber); font-family: 'Courier New', monospace; font-weight: 700;">1px</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div><!-- /smf-content -->
        </main>
    </div>
<!-- NxAlert JS loaded from: /public/nexcore/system_messages/js/system_master_messages.js -->
<script src="/public/nexcore/system_messages/js/system_master_messages.js"></script>
</body>
</html>
