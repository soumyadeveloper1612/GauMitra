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
        width: 270px;
        background: linear-gradient(180deg, #111827 0%, #1f2937 100%);
        color: #fff;
        padding: 24px 18px;
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        overflow-y: auto;
        transition: all 0.3s ease;
        z-index: 1000;
        box-shadow: 8px 0 30px rgba(0,0,0,0.08);
    }

    .sidebar.collapsed {
        left: -270px;
    }

    .brand-box {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 28px;
        padding: 14px;
        border-radius: 16px;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.08);
    }

    .brand-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: linear-gradient(135deg, #f97316, #fb923c);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #fff;
        box-shadow: 0 10px 20px rgba(249,115,22,0.25);
    }

    .brand-text h4 {
        font-size: 18px;
        margin-bottom: 2px;
        color: #fff;
    }

    .brand-text p {
        margin: 0;
        font-size: 12px;
        color: #cbd5e1;
    }

    .nav-title {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #94a3b8;
        margin: 18px 12px 10px;
    }

    .sidebar ul {
        list-style: none;
        padding-left: 0;
    }

    .sidebar ul li {
        margin-bottom: 8px;
    }

    .sidebar ul li a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px 14px;
        border-radius: 14px;
        color: #e5e7eb;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 15px;
        font-weight: 500;
    }

    .sidebar ul li a:hover,
    .sidebar ul li a.active {
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: #fff;
        transform: translateX(4px);
        box-shadow: 0 10px 22px rgba(234,88,12,0.22);
    }

    .main-content {
        margin-left: 270px;
        width: calc(100% - 270px);
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        transition: all 0.3s ease;
    }

    .top-header {
        height: 78px;
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        padding: 0 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 999;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .menu-toggle {
        width: 42px;
        height: 42px;
        border: none;
        border-radius: 12px;
        background: #fff7ed;
        color: #ea580c;
        font-size: 18px;
        cursor: pointer;
    }

    .header-title h5 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #111827;
    }

    .header-title p {
        margin: 0;
        font-size: 13px;
        color: #6b7280;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .admin-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f8fafc;
        padding: 8px 14px;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
    }

    .admin-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, #fb923c, #ea580c);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
    }

    .admin-info h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
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
        padding: 10px 16px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
    }

    .logout-btn:hover {
        color: #fff;
        opacity: 0.95;
    }

    .content-body {
        padding: 26px;
        flex: 1;
    }

    .page-title-box {
        background: linear-gradient(135deg, #fff7ed, #ffffff);
        border: 1px solid #fed7aa;
        border-radius: 22px;
        padding: 28px;
        margin-bottom: 24px;
        box-shadow: 0 15px 35px rgba(15,23,42,0.05);
    }

    .page-title-box h2 {
        font-size: 30px;
        font-weight: 800;
        margin-bottom: 8px;
        color: #111827;
    }

    .page-title-box p {
        margin: 0;
        color: #6b7280;
        font-size: 15px;
    }

    .stat-card {
        background: #fff;
        border-radius: 22px;
        padding: 22px;
        box-shadow: 0 14px 34px rgba(15,23,42,0.05);
        border: 1px solid #eef2f7;
        height: 100%;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
    }

    .stat-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
    }

    .stat-icon {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #fff;
    }

    .bg-orange { background: linear-gradient(135deg, #f97316, #ea580c); }
    .bg-blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .bg-green { background: linear-gradient(135deg, #22c55e, #16a34a); }
    .bg-purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .bg-red { background: linear-gradient(135deg, #ef4444, #dc2626); }

    .stat-card h6 {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 8px;
    }

    .stat-card h3 {
        font-size: 30px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 6px;
    }

    .stat-card p {
        margin: 0;
        font-size: 13px;
        color: #6b7280;
    }

    .dashboard-panel {
        background: #fff;
        border-radius: 22px;
        padding: 24px;
        box-shadow: 0 14px 34px rgba(15,23,42,0.05);
        border: 1px solid #eef2f7;
        height: 100%;
    }

    .dashboard-panel h4 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 18px;
        color: #111827;
    }

    .report-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .report-item:last-child {
        border-bottom: none;
    }

    .report-item .label {
        font-weight: 600;
        color: #374151;
    }

    .report-item .value {
        font-weight: 700;
        color: #ea580c;
    }

    .quick-badge {
        display: inline-block;
        background: #fff7ed;
        color: #ea580c;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .footer {
        background: #fff;
        border-top: 1px solid #e5e7eb;
        padding: 16px 24px;
        text-align: center;
        color: #6b7280;
        font-size: 14px;
    }

    @media (max-width: 991px) {
        .sidebar {
            left: -270px;
        }

        .sidebar.mobile-open {
            left: 0;
        }

        .main-content {
            margin-left: 0;
            width: 100%;
        }

        .top-header {
            padding: 0 16px;
        }

        .content-body {
            padding: 16px;
        }

        .header-right {
            gap: 8px;
        }

        .admin-info {
            display: none;
        }
    }
</style>