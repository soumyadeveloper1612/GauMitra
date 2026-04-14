<style>
    .top-header {
        height: 86px;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(14px);
        border-bottom: 1px solid #e5e7eb;
        padding: 0 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        position: sticky;
        top: 0;
        z-index: 999;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
    }

    .menu-toggle {
        width: 46px;
        height: 46px;
        border: none;
        border-radius: 14px;
        background: linear-gradient(135deg, #fff7ed, #ffedd5);
        color: #ea580c;
        font-size: 20px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 18px rgba(234, 88, 12, 0.10);
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .menu-toggle:hover {
        transform: translateY(-1px);
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: #fff;
    }

    .header-page-info {
        min-width: 0;
    }

    .header-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff7ed;
        color: #ea580c;
        border: 1px solid #fed7aa;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .header-page-info h5 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        color: #111827;
        line-height: 1.2;
    }

    .header-page-info p {
        margin: 4px 0 0;
        font-size: 13px;
        color: #6b7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 540px;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-shrink: 0;
    }

    .header-status-card {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        padding: 10px 14px;
        border-radius: 16px;
        min-width: 170px;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 0 5px rgba(34, 197, 94, 0.15);
        flex-shrink: 0;
    }

    .header-status-card strong {
        display: block;
        font-size: 13px;
        color: #111827;
        line-height: 1.1;
    }

    .header-status-card small {
        display: block;
        font-size: 11px;
        color: #6b7280;
        margin-top: 2px;
    }

    .admin-profile-card {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        padding: 8px 14px 8px 8px;
        border-radius: 18px;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04);
    }

    .admin-avatar {
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

    .admin-info h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #111827;
        line-height: 1.2;
    }

    .admin-info p {
        margin: 2px 0 0;
        font-size: 12px;
        color: #6b7280;
        text-transform: capitalize;
    }

    .logout-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        padding: 12px 16px;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 700;
        box-shadow: 0 10px 20px rgba(239, 68, 68, 0.18);
        transition: all 0.3s ease;
    }

    .logout-btn:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 14px 26px rgba(239, 68, 68, 0.24);
    }

    @media (max-width: 1200px) {
        .header-status-card {
            display: none;
        }

        .header-page-info p {
            max-width: 360px;
        }
    }

    @media (max-width: 991px) {
        .top-header {
            padding: 12px 16px;
            height: auto;
            flex-wrap: wrap;
            align-items: center;
        }

        .header-left {
            width: 100%;
        }

        .header-page-info h5 {
            font-size: 20px;
        }

        .header-page-info p {
            white-space: normal;
            max-width: 100%;
        }

        .header-right {
            width: 100%;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .admin-profile-card {
            flex: 1;
            min-width: 200px;
        }
    }

    @media (max-width: 575px) {
        .logout-btn span {
            display: none;
        }

        .logout-btn {
            padding: 12px;
        }

        .admin-info p {
            display: none;
        }

        .header-page-info h5 {
            font-size: 18px;
        }
    }
</style>