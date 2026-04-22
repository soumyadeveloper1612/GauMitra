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
            radial-gradient(circle at top left, rgba(249,115,22,0.18), transparent 32%),
            linear-gradient(180deg, #0f172a 0%, #111827 55%, #1f2937 100%);
        color: #fff;
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        z-index: 1000;
        display: flex;
        flex-direction: column;
        border-right: 1px solid rgba(255,255,255,0.06);
        box-shadow: 12px 0 40px rgba(2, 6, 23, 0.18);
        transition: all 0.3s ease;
    }

    .sidebar-top {
        padding: 22px 18px 10px;
    }

    .sidebar-scroll {
        flex: 1;
        overflow-y: auto;
        padding: 0 14px 18px;
    }

    .sidebar-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-scroll::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.12);
        border-radius: 20px;
    }

    .brand-box {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px;
        border-radius: 18px;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.08);
        backdrop-filter: blur(12px);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);
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
        box-shadow: 0 15px 30px rgba(249,115,22,0.25);
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

    .menu-item {
        margin-bottom: 8px;
    }

    .menu-link {
        display: flex;
        align-items: center;
        gap: 12px;
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
    }

    .menu-link::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(249,115,22,0.16), rgba(234,88,12,0.04));
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
        border-color: rgba(255,255,255,0.08);
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
        background: rgba(255,255,255,0.06);
        font-size: 18px;
        color: #fff;
    }

    .menu-link.active .nav-icon,
    .menu-link:hover .nav-icon {
        background: linear-gradient(135deg, #fb923c, #ea580c);
        box-shadow: 0 10px 20px rgba(249,115,22,0.22);
    }

    .nav-text {
        flex: 1;
        white-space: nowrap;
    }

    .nav-badge {
        background: rgba(34,197,94,0.16);
        color: #86efac;
        font-size: 10px;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 999px;
        border: 1px solid rgba(134,239,172,0.18);
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
        border-left: 1px dashed rgba(255,255,255,0.12);
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
        background: rgba(255,255,255,0.08);
        color: #fff;
        transform: translateX(3px);
    }

    .submenu-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: linear-gradient(135deg, #fb923c, #ea580c);
        box-shadow: 0 0 0 4px rgba(249,115,22,0.10);
        flex-shrink: 0;
    }

    .logout-menu:hover,
    .logout-menu.active {
        background: linear-gradient(135deg, rgba(239,68,68,0.20), rgba(220,38,38,0.10));
    }

    .sidebar-footer {
        padding: 14px;
        border-top: 1px solid rgba(255,255,255,0.06);
    }

    .sidebar-footer-card {
        display: flex;
        gap: 12px;
        align-items: center;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 16px;
        padding: 14px;
    }

    .footer-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: linear-gradient(135deg, #22c55e, #16a34a);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 18px;
    }

    .footer-text h6 {
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        color: #fff;
    }

    .footer-text p {
        margin: 2px 0 0;
        font-size: 11px;
        color: #cbd5e1;
    }

    .main-content {
        margin-left: var(--sidebar-width);
        width: calc(100% - var(--sidebar-width));
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
    }

   .top-header {
    position: sticky;
    top: 0;
    z-index: 998;
    background:
        linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,247,237,0.92)),
        rgba(255,255,255,0.88);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
    border-bottom: 1px solid rgba(226,232,240,0.95);
    min-height: 96px;
    padding: 14px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
}

.header-left {
    display: flex;
    align-items: center;
    gap: 14px;
    flex: 1;
    min-width: 0;
}

.header-title-wrap {
    display: flex;
    align-items: center;
    gap: 18px;
    flex-wrap: wrap;
}

.menu-toggle {
    width: 48px;
    height: 48px;
    border: none;
    border-radius: 16px;
    background: linear-gradient(135deg, #fff7ed, #ffedd5);
    color: #ea580c;
    font-size: 20px;
    cursor: pointer;
    box-shadow: 0 12px 24px rgba(249,115,22,0.12);
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.menu-toggle:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 16px 28px rgba(249,115,22,0.16);
}

.header-title h5 {
    margin: 4px 0 2px;
    font-size: 22px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
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
    background: linear-gradient(135deg, #fff7ed, #ffedd5);
    color: #c2410c;
    border: 1px solid #fed7aa;
    border-radius: 999px;
    padding: 6px 12px;
    font-size: 11px;
    font-weight: 800;
    box-shadow: 0 6px 18px rgba(249,115,22,0.08);
}

.header-slogan-card {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 18px;
    overflow: hidden;
    background: linear-gradient(135deg, #1e293b, #0f172a 70%);
    border: 1px solid rgba(251,191,36,0.25);
    box-shadow:
        0 12px 28px rgba(15, 23, 42, 0.18),
        inset 0 1px 0 rgba(255,255,255,0.05);
}

.slogan-glow {
    position: absolute;
    top: -20px;
    right: -30px;
    width: 100px;
    height: 100px;
    background: radial-gradient(circle, rgba(251,191,36,0.28), transparent 65%);
    pointer-events: none;
}

.slogan-icon {
    position: relative;
    z-index: 1;
    width: 44px;
    height: 44px;
    min-width: 44px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f59e0b, #f97316);
    color: #fff;
    font-size: 18px;
    box-shadow: 0 10px 24px rgba(245,158,11,0.28);
}

.slogan-content {
    position: relative;
    z-index: 1;
}

.slogan-label {
    display: inline-block;
    font-size: 10px;
    font-weight: 700;
    color: #fdba74;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    margin-bottom: 2px;
}

.slogan-text {
    margin: 0;
    font-size: 20px;
    font-weight: 900;
    line-height: 1.1;
    letter-spacing: 0.4px;
    background: linear-gradient(135deg, #fde68a 0%, #facc15 35%, #fb923c 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 0 0 18px rgba(251,191,36,0.15);
}

.header-right {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.live-datetime-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    min-width: 210px;
    border-radius: 18px;
    background: linear-gradient(135deg, #eff6ff, #ffffff);
    border: 1px solid #dbeafe;
    box-shadow: 0 10px 24px rgba(59,130,246,0.08);
}

.live-time-icon {
    width: 44px;
    height: 44px;
    min-width: 44px;
    border-radius: 14px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    box-shadow: 0 10px 20px rgba(59,130,246,0.20);
}

.live-datetime-content {
    line-height: 1.2;
}

.live-label {
    display: inline-block;
    font-size: 10px;
    font-weight: 800;
    color: #2563eb;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.live-datetime-content h6 {
    margin: 2px 0 2px;
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
}

.live-datetime-content p {
    margin: 0;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
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
    box-shadow: 0 8px 20px rgba(34,197,94,0.08);
}

.status-dot {
    width: 10px;
    height: 10px;
    background: #22c55e;
    border-radius: 50%;
    box-shadow: 0 0 0 5px rgba(34,197,94,0.15);
    animation: pulseStatus 1.8s infinite;
}

@keyframes pulseStatus {
    0% {
        box-shadow: 0 0 0 0 rgba(34,197,94,0.25);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(34,197,94,0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(34,197,94,0);
    }
}

.admin-profile {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #ffffff;
    padding: 8px 14px;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 10px 24px rgba(15,23,42,0.05);
}

.admin-avatar {
    width: 46px;
    height: 46px;
    border-radius: 14px;
    background: linear-gradient(135deg, #fb923c, #ea580c);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 16px;
    box-shadow: 0 12px 24px rgba(249,115,22,0.18);
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

.logout-form {
    margin: 0;
}

.logout-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: none;
    text-decoration: none;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: #fff;
    padding: 11px 16px;
    border-radius: 14px;
    font-size: 14px;
    font-weight: 700;
    box-shadow: 0 12px 24px rgba(220,38,38,0.16);
    transition: 0.3s ease;
}

.logout-btn:hover {
    color: #fff;
    transform: translateY(-1px);
}

@media (max-width: 1399px) {
    .header-slogan-card {
        padding: 10px 14px;
    }

    .slogan-text {
        font-size: 18px;
    }

    .live-datetime-card {
        min-width: 190px;
    }
}

@media (max-width: 1199px) {
    .header-status-chip {
        display: none;
    }

    .header-title-wrap {
        gap: 12px;
    }

    .live-datetime-card {
        min-width: 180px;
    }
}

@media (max-width: 991px) {
    .top-header {
        padding: 14px 16px;
        min-height: auto;
    }

    .header-left,
    .header-right {
        gap: 10px;
    }

    .header-title p,
    .admin-info,
    .logout-btn span {
        display: none;
    }

    .header-title h5 {
        font-size: 18px;
    }

    .header-slogan-card {
        padding: 10px 12px;
    }

    .slogan-text {
        font-size: 16px;
    }

    .live-datetime-card {
        min-width: auto;
        padding: 8px 12px;
    }

    .live-datetime-content h6 {
        font-size: 15px;
    }

    .live-datetime-content p {
        font-size: 11px;
    }
}

@media (max-width: 767px) {
    .top-header {
        flex-wrap: wrap;
        align-items: flex-start;
    }

    .header-left,
    .header-right {
        width: 100%;
    }

    .header-right {
        justify-content: space-between;
    }

    .header-title-wrap {
        width: 100%;
        flex-direction: column;
        align-items: flex-start;
    }

    .header-slogan-card {
        width: 100%;
    }

    .live-datetime-card {
        flex: 1;
    }
}

@media (max-width: 575px) {
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

    .slogan-icon,
    .live-time-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
    }

    .slogan-text {
        font-size: 15px;
    }
}