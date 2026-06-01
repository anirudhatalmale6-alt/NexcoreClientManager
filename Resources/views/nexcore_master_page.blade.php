<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexCore — Master Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="/public/nexcore/system_messages/css/system_master_messages.css" rel="stylesheet">
    <link href="/public/nexcore/system_sidebar/css/system_master_sidebar.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Montserrat', sans-serif;
            background: #0a0e1a;
            color: #ffffff;
            min-height: 100vh;
        }

        /* Main content area pushed by sidebar */
        .nxmp-content {
            margin-left: var(--nxsb-width, 260px);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
        }

        .nxsb.nxsb-collapsed ~ .nxsb-overlay ~ .nxmp-content,
        .nxmp-content.nxsb-content-collapsed {
            margin-left: var(--nxsb-width-collapsed, 72px);
        }

        /* Coming Soon Centre */
        .nxmp-hero {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            text-align: center;
        }

        .nxmp-hero-icon {
            width: 120px;
            height: 120px;
            border-radius: 24px;
            background: linear-gradient(135deg, rgba(5,150,105,0.15), rgba(37,99,235,0.15));
            border: 1px solid rgba(5,150,105,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 32px;
        }

        .nxmp-hero-icon i {
            font-size: 48px;
            background: linear-gradient(135deg, #059669, #2563eb, #d97706, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nxmp-hero-title {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 12px;
            background: linear-gradient(135deg, #ffffff 0%, rgba(255,255,255,0.7) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nxmp-hero-subtitle {
            font-size: 15px;
            font-weight: 500;
            color: rgba(255,255,255,0.4);
            letter-spacing: 0.3px;
            margin-bottom: 48px;
            max-width: 500px;
            line-height: 1.6;
        }

        /* Test Buttons Row */
        .nxmp-test-buttons {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .nxmp-test-btn {
            font-family: 'Montserrat', sans-serif;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 12px 28px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 180px;
        }

        .nxmp-test-btn-error {
            background: linear-gradient(135deg, #d97706, #b45309);
            color: #ffffff;
            box-shadow: 0 0 20px rgba(217,119,6,0.25);
        }

        .nxmp-test-btn-error:hover {
            box-shadow: 0 0 30px rgba(217,119,6,0.4);
            transform: translateY(-2px);
        }

        .nxmp-test-btn-delete {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: #ffffff;
            box-shadow: 0 0 20px rgba(220,38,38,0.25);
        }

        .nxmp-test-btn-delete:hover {
            box-shadow: 0 0 30px rgba(220,38,38,0.4);
            transform: translateY(-2px);
        }

        /* 4-colour gradient line */
        .nxmp-gradient-line {
            width: 200px;
            height: 3px;
            border-radius: 2px;
            background: linear-gradient(90deg, #059669, #2563eb, #d97706, #7c3aed);
            margin: 40px auto 0;
        }

        @@media (max-width: 768px) {
            .nxmp-content { margin-left: 0; }
            .nxmp-hero-title { font-size: 24px; }
        }
    </style>
</head>
<body>

{{-- System Sidebar --}}
<aside class="nxsb" id="nxSidebar">
    <div class="nxsb-brand">
        <a href="/nexcore/clients" title="NexCore Home">
            <img src="/public/nexcore/branding/nexcore-logo-light.jpg" alt="NexCore" class="nxsb-brand-logo">
            <img src="/public/nexcore/branding/nexcore-icon.png" alt="N" class="nxsb-brand-icon">
        </a>
        <button class="nxsb-toggle" onclick="NxSidebar.toggle()" title="Toggle Sidebar (Ctrl+B)">
            <i class="fas fa-chevron-left" id="nxSbToggleIcon"></i>
        </button>
    </div>
    <nav class="nxsb-menu">
        <div class="nxsb-group">
            <div class="nxsb-group-label">Main</div>
            <a href="/nexcore/clients/master-page" class="nxsb-item nxsb-active" data-tooltip="Dashboard">
                <span class="nxsb-item-icon"><i class="fas fa-th-large"></i></span>
                <span class="nxsb-item-text">Dashboard</span>
            </a>
            <a href="#" class="nxsb-item" data-tooltip="Directors">
                <span class="nxsb-item-icon"><i class="fas fa-user-tie"></i></span>
                <span class="nxsb-item-text">Directors</span>
            </a>
            <a href="#" class="nxsb-item" data-tooltip="Clients">
                <span class="nxsb-item-icon"><i class="fas fa-building"></i></span>
                <span class="nxsb-item-text">Clients</span>
            </a>
            <a href="#" class="nxsb-item" data-tooltip="Contacts">
                <span class="nxsb-item-icon"><i class="fas fa-address-book"></i></span>
                <span class="nxsb-item-text">Contacts</span>
            </a>
        </div>
        <div class="nxsb-divider"></div>
        <div class="nxsb-group">
            <div class="nxsb-group-label">Practice</div>
            <a href="#" class="nxsb-item" data-tooltip="Tasks">
                <span class="nxsb-item-icon"><i class="fas fa-tasks"></i></span>
                <span class="nxsb-item-text">Tasks</span>
                <span class="nxsb-badge" style="background: var(--nxsb-accent-amber);">5</span>
            </a>
            <a href="#" class="nxsb-item" data-tooltip="Meetings">
                <span class="nxsb-item-icon"><i class="fas fa-calendar-alt"></i></span>
                <span class="nxsb-item-text">Meetings</span>
            </a>
            <a href="#" class="nxsb-item" data-tooltip="Documents">
                <span class="nxsb-item-icon"><i class="fas fa-folder-open"></i></span>
                <span class="nxsb-item-text">Documents</span>
            </a>
        </div>
        <div class="nxsb-divider"></div>
        <div class="nxsb-group">
            <div class="nxsb-group-label">System</div>
            <a href="/nexcore/clients/system-master" class="nxsb-item" data-tooltip="Gold Standard">
                <span class="nxsb-item-icon"><i class="fas fa-crown"></i></span>
                <span class="nxsb-item-text">Gold Standard</span>
            </a>
            <a href="#" class="nxsb-item" data-tooltip="Settings">
                <span class="nxsb-item-icon"><i class="fas fa-cog"></i></span>
                <span class="nxsb-item-text">Settings</span>
            </a>
        </div>
    </nav>
    <div class="nxsb-footer">
        <div class="nxsb-footer-text">NexCore Africa Proprietary Limited</div>
        <div class="nxsb-footer-line"></div>
    </div>
</aside>

<div class="nxsb-overlay"></div>

{{-- Main Content --}}
<div class="nxmp-content nxsb-content" id="nxMainContent">
    <div class="nxmp-hero">
        <div class="nxmp-hero-icon">
            <i class="fas fa-rocket"></i>
        </div>
        <div class="nxmp-hero-title">Something Awesome Is Coming Soon</div>
        <div class="nxmp-hero-subtitle">
            The NexCore Enterprise Platform is being built from the ground up.
            Every component, every pixel, every interaction — crafted to perfection.
        </div>

        <div class="nxmp-test-buttons">
            <button class="nxmp-test-btn nxmp-test-btn-error" onclick="NxAlert.error('Error', 'Something went wrong. Please try again.')">
                <i class="fas fa-times-circle" style="margin-right: 8px;"></i> Test Error (M2)
            </button>
            <button class="nxmp-test-btn nxmp-test-btn-delete" onclick="NxAlert.delete('Delete Record', 'Krish Moodley')">
                <i class="fas fa-trash" style="margin-right: 8px;"></i> Test Delete (M6)
            </button>
        </div>

        <div class="nxmp-gradient-line"></div>
    </div>
</div>

<script src="/public/nexcore/system_sidebar/js/system_master_sidebar.js"></script>
<script src="/public/nexcore/system_messages/js/system_master_messages.js"></script>
</body>
</html>
