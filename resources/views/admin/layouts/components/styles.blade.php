<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    :root {
        --sidebar-width: 290px;
        --sidebar-mini-width: 92px;
        --primary: #f97316;
        --primary-dark: #ea580c;
        --dark: #0f172a;
        --dark-2: #111827;
        --dark-3: #1e293b;
        --text: #111827;
        --muted: #6b7280;
        --border: #e5e7eb;
        --bg: #f8fafc;
        --white: #ffffff;
        --danger: #dc2626;
        --success: #16a34a;
    }

    html,
    body {
        width: 100%;
        min-height: 100%;
    }

    body {
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
        color: var(--text);
        overflow-x: hidden;
    }

    .admin-wrapper {
        display: flex;
        min-height: 100vh;
    }

    .sidebar-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        opacity: 0;
        visibility: hidden;
        transition: 0.3s ease;
        z-index: 999;
    }

    .sidebar-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .sidebar {
        width: var(--sidebar-width);
        background:
            radial-gradient(circle at top left, rgba(249, 115, 22, 0.18), transparent 32%),
            linear-gradient(180deg, #0f172a 0%, #111827 55%, #1f2937 100%);
        color: #fff;
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        max-height: 100vh;
        z-index: 1000;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border-right: 1px solid rgba(255, 255, 255, 0.06);
        box-shadow: 12px 0 40px rgba(2, 6, 23, 0.18);
        transition: all 0.3s ease;
    }

    .sidebar-top {
        flex-shrink: 0;
        padding: 22px 18px 10px;
    }

    .sidebar-scroll {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 0 14px 28px;
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.22) transparent;
    }

    .sidebar-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-scroll::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.22);
        border-radius: 20px;
    }

    .sidebar-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.34);
    }

    .brand-box {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(12px);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06);
    }

    .brand-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: linear-gradient(135deg, #fb923c, #ea580c);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #fff;
        box-shadow: 0 15px 30px rgba(249, 115, 22, 0.25);
        flex-shrink: 0;
    }

    .brand-text h4 {
        font-size: 20px;
        font-weight: 800;
        margin-bottom: 2px;
        color: #fff;
    }

    .brand-text p {
        margin: 0;
        font-size: 12px;
        color: #cbd5e1;
    }

    .nav-title {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #94a3b8;
        margin: 22px 12px 12px;
        font-weight: 700;
    }

    .sidebar-menu {
        list-style: none;
        padding-left: 0;
        margin: 0;
    }

    .sidebar-menu:last-child {
        padding-bottom: 20px;
    }

    .menu-item {
        margin-bottom: 8px;
    }

    .menu-link {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        color: #e2e8f0;
        text-decoration: none;
        padding: 13px 14px;
        border-radius: 16px;
        position: relative;
        overflow: hidden;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .menu-link::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(249, 115, 22, 0.16), rgba(234, 88, 12, 0.04));
        opacity: 0;
        transition: all 0.3s ease;
    }

    .menu-link:hover::before,
    .menu-link.active::before {
        opacity: 1;
    }

    .menu-link:hover,
    .menu-link.active {
        color: #fff;
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.18);
        transform: translateX(3px);
    }

    .nav-icon,
    .nav-text,
    .nav-badge,
    .submenu-arrow {
        position: relative;
        z-index: 1;
    }

    .nav-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.06);
        font-size: 18px;
        color: #fff;
    }

    .menu-link.active .nav-icon,
    .menu-link:hover .nav-icon {
        background: linear-gradient(135deg, #fb923c, #ea580c);
        box-shadow: 0 10px 20px rgba(249, 115, 22, 0.22);
    }

    .nav-text {
        flex: 1;
        white-space: nowrap;
    }

    .nav-badge {
        background: rgba(34, 197, 94, 0.16);
        color: #86efac;
        font-size: 10px;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 999px;
        border: 1px solid rgba(134, 239, 172, 0.18);
    }

    .submenu-arrow {
        font-size: 13px;
        transition: transform 0.3s ease;
    }

    .has-submenu.open .submenu-arrow {
        transform: rotate(180deg);
    }

    .submenu {
        list-style: none;
        margin: 8px 0 2px 18px;
        padding: 10px 0 4px 18px;
        border-left: 1px dashed rgba(255, 255, 255, 0.12);
        display: none;
    }

    .submenu li {
        margin-bottom: 6px;
    }

    .submenu a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        color: #cbd5e1;
        text-decoration: none;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
        transition: 0.3s ease;
    }

    .submenu a:hover,
    .submenu a.active {
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
        transform: translateX(3px);
    }

    .submenu-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: linear-gradient(135deg, #fb923c, #ea580c);
        box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.10);
        flex-shrink: 0;
    }

    .logout-menu {
        color: #fecaca !important;
    }

    .logout-menu .nav-icon {
        background: rgba(239, 68, 68, 0.16);
    }

    .logout-menu:hover,
    .logout-menu.active {
        color: #ffffff !important;
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.24), rgba(220, 38, 38, 0.12)) !important;
    }

    .main-content {
        margin-left: var(--sidebar-width);
        width: calc(100% - var(--sidebar-width));
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
    }

    .header-center {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        min-width: 260px;
    }

    .header-mantra {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        padding: 11px 18px;
        border-radius: 999px;
        background:
            radial-gradient(circle at top left, rgba(251, 146, 60, 0.25), transparent 35%),
            linear-gradient(135deg, #fff7ed, #ffedd5);
        border: 1px solid #fed7aa;
        color: #9a3412;
        font-size: 16px;
        font-weight: 900;
        letter-spacing: 0.2px;
        white-space: nowrap;
        box-shadow: 0 12px 28px rgba(249, 115, 22, 0.13);
    }

    .header-mantra i {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #fb923c, #ea580c);
        color: #fff;
        font-size: 14px;
        box-shadow: 0 8px 18px rgba(249, 115, 22, 0.22);
    }

    .header-date-time {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 9px 14px;
        border-radius: 16px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
        white-space: nowrap;
    }

    .header-date-time i {
        width: 34px;
        height: 34px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eff6ff;
        color: #2563eb;
        font-size: 16px;
    }

    .header-date-time strong {
        display: block;
        font-size: 13px;
        font-weight: 800;
        color: #111827;
        line-height: 1.1;
    }

    .header-date-time small {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        margin-top: 2px;
    }

    @media (max-width: 1399px) {
        .header-center {
            justify-content: flex-end;
        }

        .header-mantra {
            font-size: 14px;
            padding: 10px 14px;
        }
    }

    @media (max-width: 1199px) {
        .header-center {
            display: none;
        }
    }

    @media (max-width: 991px) {
        .top-header {
            gap: 10px;
        }
    }

    .top-header {
        position: sticky;
        top: 0;
        z-index: 998;
        background: rgba(255, 255, 255, 0.90);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(226, 232, 240, 0.9);
        min-height: 84px;
        padding: 14px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .menu-toggle {
        width: 46px;
        height: 46px;
        border: none;
        border-radius: 14px;
        background: linear-gradient(135deg, #fff7ed, #ffedd5);
        color: var(--primary-dark);
        font-size: 20px;
        cursor: pointer;
        box-shadow: 0 10px 20px rgba(249, 115, 22, 0.10);
        transition: 0.3s ease;
    }

    .menu-toggle:hover {
        transform: translateY(-1px);
    }

    .header-title h5 {
        margin: 4px 0 2px;
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
    }

    .header-title p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
        font-weight: 500;
    }

    .header-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff7ed;
        color: var(--primary-dark);
        border: 1px solid #fed7aa;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 11px;
        font-weight: 700;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-status-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
        padding: 10px 14px;
        border-radius: 14px;
        font-size: 13px;
        font-weight: 700;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        background: #22c55e;
        border-radius: 50%;
        box-shadow: 0 0 0 5px rgba(34, 197, 94, 0.15);
    }

    .admin-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #ffffff;
        padding: 8px 14px;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    }

    .admin-avatar {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        background: linear-gradient(135deg, #fb923c, #ea580c);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 16px;
        box-shadow: 0 12px 24px rgba(249, 115, 22, 0.18);
    }

    .admin-info h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 800;
        color: #111827;
    }

    .admin-info p {
        margin: 0;
        font-size: 12px;
        color: #6b7280;
    }

    .logout-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        padding: 11px 16px;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 700;
        box-shadow: 0 12px 24px rgba(220, 38, 38, 0.16);
        transition: 0.3s ease;
        border: none;
    }

    .logout-btn:hover {
        color: #fff;
        transform: translateY(-1px);
    }

    .content-body {
        flex: 1;
        padding: 24px;
    }

    .footer {
        background: rgba(255, 255, 255, 0.75);
        border-top: 1px solid #e5e7eb;
        padding: 16px 24px;
        text-align: center;
        color: #64748b;
        font-size: 14px;
    }

    .admin-wrapper.sidebar-collapsed .sidebar {
        width: var(--sidebar-mini-width);
    }

    .admin-wrapper.sidebar-collapsed .main-content {
        margin-left: var(--sidebar-mini-width);
        width: calc(100% - var(--sidebar-mini-width));
    }

    .admin-wrapper.sidebar-collapsed .brand-text,
    .admin-wrapper.sidebar-collapsed .nav-title,
    .admin-wrapper.sidebar-collapsed .nav-text,
    .admin-wrapper.sidebar-collapsed .nav-badge,
    .admin-wrapper.sidebar-collapsed .submenu-arrow,
    .admin-wrapper.sidebar-collapsed .submenu {
        display: none !important;
    }

    .admin-wrapper.sidebar-collapsed .sidebar-top {
        padding-left: 10px;
        padding-right: 10px;
    }

    .admin-wrapper.sidebar-collapsed .sidebar-scroll {
        padding-left: 10px;
        padding-right: 10px;
    }

    .admin-wrapper.sidebar-collapsed .menu-link {
        justify-content: center;
        padding: 13px 10px;
    }

    .admin-wrapper.sidebar-collapsed .brand-box {
        justify-content: center;
        padding: 14px 10px;
    }

    .admin-wrapper.sidebar-collapsed .nav-icon {
        margin-right: 0;
    }

    @media (max-width: 1199px) {
        .header-status-chip {
            display: none;
        }
    }

    @media (max-width: 991px) {
        .sidebar {
            transform: translateX(-100%);
            width: var(--sidebar-width);
            height: 100dvh;
            max-height: 100dvh;
        }

        .sidebar.mobile-open {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0 !important;
            width: 100% !important;
        }

        .top-header {
            padding: 14px 16px;
        }

        .content-body {
            padding: 16px;
        }

        .admin-info,
        .logout-btn span,
        .header-title p {
            display: none;
        }

        .header-title h5 {
            font-size: 18px;
        }

        .sidebar-scroll {
            padding-bottom: 45px;
        }
    }

    @media (max-width: 575px) {
        .header-right {
            gap: 8px;
        }

        .admin-profile {
            padding: 6px 8px;
        }

        .admin-avatar {
            width: 40px;
            height: 40px;
            font-size: 14px;
        }

        .logout-btn {
            padding: 10px 12px;
        }

        .content-body {
            padding: 14px;
        }
    }
</style>
