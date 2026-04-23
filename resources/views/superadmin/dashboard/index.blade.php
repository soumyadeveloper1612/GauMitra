@extends('admin.layouts.app')

@section('title', 'Super Admin Dashboard - GauMitra')
@section('header_title', 'Super Admin Dashboard')
@section('header_subtitle', 'Centralized control panel for admins, users, access, reports and master modules')

@section('content')
@php
    $statCards = [
        [
            'title' => 'Total Admins',
            'value' => $totalAdmins,
            'subtitle' => 'Active: ' . $activeAdmins,
            'icon' => 'bi-people-fill',
            'route' => route('admin.admins.index'),
            'theme' => 'admins',
        ],
        [
            'title' => 'Total Users',
            'value' => $totalUsers,
            'subtitle' => 'Application users',
            'icon' => 'bi-person-lines-fill',
            'route' => route('admin.users.index'),
            'theme' => 'users',
        ],
        [
            'title' => 'Total Gaushalas',
            'value' => $totalGaushalas,
            'subtitle' => 'Registered gaushalas',
            'icon' => 'bi-house-heart-fill',
            'route' => route('admin.gaushalas.index'),
            'theme' => 'gaushalas',
        ],
        [
            'title' => 'Total Cases',
            'value' => $totalCases,
            'subtitle' => 'Emergency cases',
            'icon' => 'bi-clipboard2-pulse-fill',
            'route' => route('admin.report-cases.index'),
            'theme' => 'cases',
        ],
        [
            'title' => 'News & Notices',
            'value' => $totalNewsNotices,
            'subtitle' => 'Published notices',
            'icon' => 'bi-megaphone-fill',
            'route' => route('admin.news-notices.index'),
            'theme' => 'news',
        ],
        [
            'title' => 'Roles',
            'value' => $totalRoles,
            'subtitle' => 'Available roles',
            'icon' => 'bi-shield-lock-fill',
            'route' => route('admin.roles.index'),
            'theme' => 'roles',
        ],
        [
            'title' => 'Permissions',
            'value' => $totalPermissions,
            'subtitle' => 'System permissions',
            'icon' => 'bi-key-fill',
            'route' => route('admin.roles.index'),
            'theme' => 'permissions',
        ],
    ];

    $quickActions = [
        [
            'title' => 'Manage Admins',
            'desc' => 'Create, edit and control admin accounts',
            'icon' => 'bi-people-fill',
            'route' => route('admin.admins.index'),
            'btn' => 'Open Admins',
            'theme' => 'admins',
        ],
        [
            'title' => 'Roles & Permissions',
            'desc' => 'Control module-wise access and permissions',
            'icon' => 'bi-shield-check',
            'route' => route('admin.roles.index'),
            'btn' => 'Open Roles',
            'theme' => 'roles',
        ],
        [
            'title' => 'Assign Menu Access',
            'desc' => 'Assign sidebar menus for each admin login',
            'icon' => 'bi-menu-button-wide-fill',
            'route' => route('admin.menu-access.index'),
            'btn' => 'Assign Menus',
            'theme' => 'menu',
        ],
        [
            'title' => 'Gaushala Management',
            'desc' => 'Monitor and manage all registered gaushalas',
            'icon' => 'bi-house-heart-fill',
            'route' => route('admin.gaushalas.index'),
            'btn' => 'View Gaushalas',
            'theme' => 'gaushalas',
        ],
        [
            'title' => 'Case Management',
            'desc' => 'Review all reported rescue and emergency cases',
            'icon' => 'bi-clipboard2-pulse-fill',
            'route' => route('admin.report-cases.index'),
            'btn' => 'View Cases',
            'theme' => 'cases',
        ],
        [
            'title' => 'News & Notices',
            'desc' => 'Create and manage official news and notices',
            'icon' => 'bi-megaphone-fill',
            'route' => route('admin.news-notices.index'),
            'btn' => 'Open Notices',
            'theme' => 'news',
        ],
    ];
@endphp

<style>
    .super-admin-dashboard {
        --bg-soft: #f6f8fc;
        --card-bg: rgba(255, 255, 255, 0.92);
        --border-soft: rgba(15, 23, 42, 0.08);
        --shadow-soft: 0 20px 45px rgba(15, 23, 42, 0.08);
        --shadow-hover: 0 24px 50px rgba(15, 23, 42, 0.14);
    }

    .super-admin-dashboard .hero-panel {
        position: relative;
        overflow: hidden;
        border-radius: 28px;
        padding: 34px;
        background:
            radial-gradient(circle at top right, rgba(255, 255, 255, 0.18), transparent 24%),
            radial-gradient(circle at bottom left, rgba(255, 255, 255, 0.14), transparent 18%),
            linear-gradient(135deg, #0f172a 0%, #172554 45%, #1d4ed8 100%);
        color: #fff;
        box-shadow: 0 24px 70px rgba(29, 78, 216, 0.22);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .super-admin-dashboard .hero-panel::before {
        content: "";
        position: absolute;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.06);
        top: -60px;
        right: -40px;
    }

    .super-admin-dashboard .hero-panel::after {
        content: "";
        position: absolute;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.07);
        bottom: -25px;
        left: -25px;
    }

    .super-admin-dashboard .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.16);
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.4px;
        margin-bottom: 16px;
    }

    .super-admin-dashboard .hero-title {
        font-size: 2.3rem;
        font-weight: 800;
        line-height: 1.15;
        margin-bottom: 12px;
    }

    .super-admin-dashboard .hero-subtitle {
        font-size: 1rem;
        line-height: 1.8;
        color: rgba(255, 255, 255, 0.86);
        max-width: 760px;
        margin-bottom: 24px;
    }

    .super-admin-dashboard .hero-metric-wrap {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .super-admin-dashboard .hero-metric {
        background: rgba(255, 255, 255, 0.09);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 20px;
        padding: 16px 18px;
        backdrop-filter: blur(8px);
    }

    .super-admin-dashboard .hero-metric .label {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.75);
        text-transform: uppercase;
        letter-spacing: 0.7px;
        margin-bottom: 6px;
    }

    .super-admin-dashboard .hero-metric .value {
        font-size: 1.5rem;
        font-weight: 800;
        color: #fff;
        margin-bottom: 0;
    }

    .super-admin-dashboard .hero-action-group .btn {
        min-width: 185px;
        padding: 13px 22px;
        border-radius: 999px;
        font-weight: 700;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
    }

    .super-admin-dashboard .hero-action-group .btn-light {
        color: #0f172a;
    }

    .super-admin-dashboard .hero-action-group .btn-outline-light {
        border-width: 1.5px;
    }

    .super-admin-dashboard .section-title {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .super-admin-dashboard .section-subtitle {
        color: #64748b;
        margin-bottom: 0;
    }

    .super-admin-dashboard .stat-card-link,
    .super-admin-dashboard .quick-card-link {
        text-decoration: none;
        color: inherit;
    }

    .super-admin-dashboard .stat-card {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        padding: 22px;
        background: var(--card-bg);
        border: 1px solid var(--border-soft);
        box-shadow: var(--shadow-soft);
        transition: all 0.28s ease;
        height: 100%;
    }

    .super-admin-dashboard .stat-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-hover);
        border-color: rgba(37, 99, 235, 0.18);
    }

    .super-admin-dashboard .stat-card::before {
        content: "";
        position: absolute;
        inset: 0 auto auto 0;
        width: 100%;
        height: 4px;
        background: var(--theme-color);
    }

    .super-admin-dashboard .stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.45rem;
        color: #fff;
        background: var(--theme-gradient);
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.14);
    }

    .super-admin-dashboard .stat-title {
        font-size: 0.98rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 10px;
    }

    .super-admin-dashboard .stat-value {
        font-size: 2.2rem;
        line-height: 1;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 10px;
    }

    .super-admin-dashboard .stat-subtitle {
        color: #64748b;
        font-size: 0.95rem;
        margin-bottom: 0;
    }

    .super-admin-dashboard .stat-arrow {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #f8fafc;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #0f172a;
        font-size: 1rem;
        border: 1px solid rgba(15, 23, 42, 0.07);
        transition: all 0.25s ease;
    }

    .super-admin-dashboard .stat-card:hover .stat-arrow {
        background: var(--theme-color);
        color: #fff;
        border-color: var(--theme-color);
    }

    .super-admin-dashboard .glass-panel {
        background: var(--card-bg);
        border: 1px solid var(--border-soft);
        box-shadow: var(--shadow-soft);
        border-radius: 28px;
    }

    .super-admin-dashboard .quick-card {
        border-radius: 22px;
        padding: 22px;
        border: 1px solid rgba(15, 23, 42, 0.07);
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        transition: all 0.28s ease;
        height: 100%;
    }

    .super-admin-dashboard .quick-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
        border-color: rgba(37, 99, 235, 0.18);
    }

    .super-admin-dashboard .quick-card .icon-wrap {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.25rem;
        margin-bottom: 18px;
        background: var(--theme-gradient);
    }

    .super-admin-dashboard .quick-card h5 {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 10px;
    }

    .super-admin-dashboard .quick-card p {
        font-size: 0.94rem;
        color: #64748b;
        line-height: 1.7;
        min-height: 54px;
    }

    .super-admin-dashboard .quick-card .btn {
        border-radius: 999px;
        padding: 10px 18px;
        font-weight: 700;
    }

    .super-admin-dashboard .authority-panel {
        padding: 26px;
    }

    .super-admin-dashboard .authority-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 14px 0;
        border-bottom: 1px dashed rgba(15, 23, 42, 0.08);
    }

    .super-admin-dashboard .authority-row:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .super-admin-dashboard .authority-label {
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 3px;
    }

    .super-admin-dashboard .authority-text {
        font-size: 0.9rem;
        color: #64748b;
        margin-bottom: 0;
    }

    .super-admin-dashboard .mini-badge {
        min-width: 92px;
        text-align: center;
        padding: 8px 12px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 0.86rem;
    }

    .theme-admins {
        --theme-color: #f97316;
        --theme-gradient: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
    }

    .theme-users {
        --theme-color: #2563eb;
        --theme-gradient: linear-gradient(135deg, #60a5fa 0%, #2563eb 100%);
    }

    .theme-gaushalas {
        --theme-color: #8b5cf6;
        --theme-gradient: linear-gradient(135deg, #a78bfa 0%, #8b5cf6 100%);
    }

    .theme-cases {
        --theme-color: #ef4444;
        --theme-gradient: linear-gradient(135deg, #fb7185 0%, #ef4444 100%);
    }

    .theme-news {
        --theme-color: #14b8a6;
        --theme-gradient: linear-gradient(135deg, #2dd4bf 0%, #14b8a6 100%);
    }

    .theme-roles {
        --theme-color: #6366f1;
        --theme-gradient: linear-gradient(135deg, #818cf8 0%, #6366f1 100%);
    }

    .theme-permissions {
        --theme-color: #0f766e;
        --theme-gradient: linear-gradient(135deg, #14b8a6 0%, #0f766e 100%);
    }

    .theme-menu {
        --theme-color: #1d4ed8;
        --theme-gradient: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    @media (max-width: 991px) {
        .super-admin-dashboard .hero-title {
            font-size: 1.8rem;
        }

        .super-admin-dashboard .hero-metric-wrap {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container-fluid py-4 super-admin-dashboard">
    <div class="hero-panel mb-4">
        <div class="row align-items-center g-4 position-relative" style="z-index:2;">
            <div class="col-xl-8">
                <span class="hero-badge">
                    <i class="bi bi-stars"></i> Super Admin Command Center
                </span>

                <h1 class="hero-title">Welcome back, {{ session('admin_name') ?: 'Super Admin' }}</h1>

                <p class="hero-subtitle">
                    Monitor platform health, manage admins, control menu visibility, review reports,
                    and supervise all core modules from one premium dashboard.
                </p>

                <div class="d-flex flex-wrap gap-3 hero-action-group">
                    <a href="{{ route('admin.admins.index') }}" class="btn btn-light">
                        <i class="bi bi-people-fill me-2"></i> Manage Admins
                    </a>

                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-light">
                        <i class="bi bi-shield-lock-fill me-2"></i> Roles & Permissions
                    </a>

                    <a href="{{ route('admin.menu-access.index') }}" class="btn btn-outline-light">
                        <i class="bi bi-menu-button-wide-fill me-2"></i> Assign Menu Access
                    </a>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="hero-metric-wrap">
                    <div class="hero-metric">
                        <div class="label">Admins</div>
                        <h4 class="value mb-0">{{ $totalAdmins }}</h4>
                    </div>
                    <div class="hero-metric">
                        <div class="label">Users</div>
                        <h4 class="value mb-0">{{ $totalUsers }}</h4>
                    </div>
                    <div class="hero-metric">
                        <div class="label">Permissions</div>
                        <h4 class="value mb-0">{{ $totalPermissions }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-3">
        <div>
            <h3 class="section-title">Platform Overview</h3>
            <p class="section-subtitle">All cards are clickable and open the related management section.</p>
        </div>
        <span class="badge rounded-pill bg-danger-subtle text-danger px-3 py-2 border border-danger-subtle">
            <i class="bi bi-shield-fill-check me-1"></i> Super Admin Access
        </span>
    </div>

    <div class="row g-4">
        @foreach($statCards as $card)
            <div class="col-md-6 col-xl-3">
                <a href="{{ $card['route'] }}" class="stat-card-link">
                    <div class="stat-card theme-{{ $card['theme'] }}">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div class="stat-icon">
                                <i class="bi {{ $card['icon'] }}"></i>
                            </div>
                            <div class="stat-arrow">
                                <i class="bi bi-arrow-up-right"></i>
                            </div>
                        </div>

                        <div class="stat-title">{{ $card['title'] }}</div>
                        <div class="stat-value">{{ $card['value'] }}</div>
                        <p class="stat-subtitle">{{ $card['subtitle'] }}</p>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mt-1">
        <div class="col-xl-8">
            <div class="glass-panel p-4 h-100">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                    <div>
                        <h3 class="section-title mb-1">Super Admin Controls</h3>
                        <p class="section-subtitle">Fast access to the most important administrative operations.</p>
                    </div>
                </div>

                <div class="row g-4">
                    @foreach($quickActions as $action)
                        <div class="col-md-6">
                            <a href="{{ $action['route'] }}" class="quick-card-link">
                                <div class="quick-card theme-{{ $action['theme'] }}">
                                    <div class="icon-wrap">
                                        <i class="bi {{ $action['icon'] }}"></i>
                                    </div>
                                    <h5>{{ $action['title'] }}</h5>
                                    <p>{{ $action['desc'] }}</p>
                                    <span class="btn btn-outline-dark">
                                        {{ $action['btn'] }} <i class="bi bi-arrow-right ms-1"></i>
                                    </span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="glass-panel authority-panel h-100">
                <h3 class="section-title mb-1">Authority Snapshot</h3>
                <p class="section-subtitle mb-3">Quick summary of system control and access coverage.</p>

                <div class="authority-row">
                    <div>
                        <div class="authority-label">Admin Accounts</div>
                        <p class="authority-text">Total admin operators registered in the platform.</p>
                    </div>
                    <span class="mini-badge bg-warning-subtle text-warning-emphasis">{{ $totalAdmins }}</span>
                </div>

                <div class="authority-row">
                    <div>
                        <div class="authority-label">Active Admins</div>
                        <p class="authority-text">Currently active administrative users.</p>
                    </div>
                    <span class="mini-badge bg-success-subtle text-success-emphasis">{{ $activeAdmins }}</span>
                </div>

                <div class="authority-row">
                    <div>
                        <div class="authority-label">Role Management</div>
                        <p class="authority-text">Role structures used for permission assignment.</p>
                    </div>
                    <span class="mini-badge bg-primary-subtle text-primary-emphasis">{{ $totalRoles }}</span>
                </div>

                <div class="authority-row">
                    <div>
                        <div class="authority-label">Permission Library</div>
                        <p class="authority-text">System permissions mapped to secure modules.</p>
                    </div>
                    <span class="mini-badge bg-info-subtle text-info-emphasis">{{ $totalPermissions }}</span>
                </div>

                <div class="authority-row">
                    <div>
                        <div class="authority-label">Menu Assignment</div>
                        <p class="authority-text">Assign sidebar menus to each admin as needed.</p>
                    </div>
                    <a href="{{ route('admin.menu-access.index') }}" class="mini-badge bg-dark text-white text-decoration-none">
                        Open
                    </a>
                </div>

                <div class="authority-row">
                    <div>
                        <div class="authority-label">Cases Monitoring</div>
                        <p class="authority-text">Emergency and report-case review center.</p>
                    </div>
                    <span class="mini-badge bg-danger-subtle text-danger-emphasis">{{ $totalCases }}</span>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.menu-access.index') }}" class="btn btn-dark w-100 rounded-pill py-3 fw-bold">
                        <i class="bi bi-menu-button-wide-fill me-2"></i> Assign Menu Access
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection