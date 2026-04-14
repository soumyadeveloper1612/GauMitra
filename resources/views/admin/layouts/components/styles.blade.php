<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f5f7fb;
        color: #1f2937;
    }

    .admin-wrapper {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: 290px;
        background: linear-gradient(180deg, #0f172a 0%, #111827 45%, #1f2937 100%);
        color: #fff;
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        overflow: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
        box-shadow: 10px 0 35px rgba(0, 0, 0, 0.12);
    }

    .sidebar-inner {
        height: 100%;
        overflow-y: auto;
        padding: 22px 18px;
        display: flex;
        flex-direction: column;
    }

    .sidebar.collapsed {
        left: -290px;
    }

    .brand-box {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
        padding: 14px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.07);
        border: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(8px);
    }

    .brand-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        background: linear-gradient(135deg, #f97316, #fb923c);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #fff;
        box-shadow: 0 10px 20px rgba(249, 115, 22, 0.25);
        flex-shrink: 0;
    }

    .brand-text h4 {
        font-size: 18px;
        margin-bottom: 2px;
        color: #fff;
        font-weight: 700;
    }

    .brand-text p {
        margin: 0;
        font-size: 12px;
        color: #cbd5e1;
    }

    .sidebar-search-box {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px;
        padding: 12px 14px;
        margin-bottom: 18px;
    }

    .sidebar-search-box .search-icon {
        color: #94a3b8;
        font-size: 14px;
    }

    .sidebar-search-box input {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        color: #fff;
        font-size: 14px;
    }

    .sidebar-search-box input::placeholder {
        color: #94a3b8;
    }

    .quick-action-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px;
        border-radius: 18px;
        background: linear-gradient(135deg, rgba(249, 115, 22, 0.18), rgba(251, 146, 60, 0.10));
        border: 1px solid rgba(251, 146, 60, 0.18);
        margin-bottom: 20px;
    }

    .quick-action-text h6 {
        margin: 0 0 4px;
        font-size: 14px;
        font-weight: 700;
        color: #fff;
    }

    .quick-action-text p {
        margin: 0;
        font-size: 12px;
        color: #cbd5e1;
    }

    .quick-action-btn {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-size: 18px;
        box-shadow: 0 10px 20px rgba(234, 88, 12, 0.25);
        flex-shrink: 0;
    }

    .quick-action-btn:hover {
        color: #fff;
        transform: translateY(-1px);
    }

    .nav-section {
        margin-bottom: 18px;
    }

    .nav-title {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1.3px;
        color: #94a3b8;
        margin: 0 12px 10px;
        font-weight: 700;
    }

    .nav-list {
        list-style: none;
        padding-left: 0;
        margin: 0;
    }

    .nav-list li {
        margin-bottom: 8px;
    }

    .nav-list li a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px 14px;
        border-radius: 16px;
        color: #e5e7eb;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 15px;
        font-weight: 600;
        position: relative;
    }

    .nav-icon {
        width: 38px;
        height: 38px;
        min-width: 38px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.06);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #cbd5e1;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .nav-text {
        flex: 1;
    }

    .nav-badge {
        background: rgba(34, 197, 94, 0.16);
        color: #86efac;
        border: 1px solid rgba(34, 197, 94, 0.22);
        font-size: 11px;
        font-weight: 700;
        padding: 4px 9px;
        border-radius: 999px;
    }

    .nav-badge-gold {
        background: rgba(250, 204, 21, 0.14);
        color: #fde68a;
        border-color: rgba(250, 204, 21, 0.2);
    }

    .nav-list li a:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
        transform: translateX(4px);
    }

    .nav-list li a:hover .nav-icon {
        background: rgba(249, 115, 22, 0.16);
        color: #fb923c;
    }

    .nav-list li a.active {
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: #fff;
        box-shadow: 0 10px 22px rgba(234, 88, 12, 0.22);
        transform: translateX(4px);
    }

    .nav-list li a.active .nav-icon {
        background: rgba(255, 255, 255, 0.18);
        color: #fff;
    }

    .sidebar-bottom {
        margin-top: auto;
        padding-top: 18px;
    }

    .admin-mini-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.08);
        margin-bottom: 12px;
    }

    .admin-mini-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, #fb923c, #ea580c);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 16px;
        flex-shrink: 0;
    }

    .admin-mini-info h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #fff;
    }

    .admin-mini-info p {
        margin: 2px 0 0;
        font-size: 12px;
        color: #cbd5e1;
        text-transform: capitalize;
    }

    .logout-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px 14px;
        border-radius: 16px;
        color: #fecaca;
        text-decoration: none;
        font-size: 15px;
        font-weight: 600;
        background: rgba(239, 68, 68, 0.08);
        border: 1px solid rgba(239, 68, 68, 0.12);
        transition: all 0.3s ease;
    }

    .logout-link .nav-icon {
        background: rgba(239, 68, 68, 0.12);
        color: #fca5a5;
    }

    .logout-link:hover {
        color: #fff;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        box-shadow: 0 10px 20px rgba(239, 68, 68, 0.22);
    }

    .logout-link:hover .nav-icon {
        background: rgba(255, 255, 255, 0.18);
        color: #fff;
    }

    .main-content {
        margin-left: 290px;
        width: calc(100% - 290px);
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        transition: all 0.3s ease;
    }

    @media (max-width: 991px) {
        .sidebar {
            left: -290px;
        }

        .sidebar.mobile-open {
            left: 0;
        }

        .main-content {
            margin-left: 0;
            width: 100%;
        }
    }
</style>