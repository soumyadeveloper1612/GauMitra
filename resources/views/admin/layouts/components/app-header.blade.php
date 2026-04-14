<header class="top-header">
    <div class="header-left">
        <button class="menu-toggle" id="menuToggle" type="button">
            <i class="bi bi-list"></i>
        </button>

        <div class="header-page-info">
            <span class="header-badge">
                <i class="bi bi-activity"></i> Admin Panel
            </span>
            <h5>{{ $pageTitle ?? 'GauMitra Dashboard' }}</h5>
            <p>{{ $pageSubtitle ?? 'Manage users, admins, reports and platform activity' }}</p>
        </div>
    </div>

    <div class="header-right">
        <div class="header-status-card">
            <span class="status-dot"></span>
            <div>
                <strong>System Active</strong>
                <small>All services running</small>
            </div>
        </div>

        <div class="admin-profile-card">
            <div class="admin-avatar">
                {{ strtoupper(substr(session('admin_name', 'A'), 0, 1)) }}
            </div>
            <div class="admin-info">
                <h6>{{ session('admin_name', 'Admin') }}</h6>
                <p>{{ session('admin_role', 'Administrator') }}</p>
            </div>
        </div>

        <a href="{{ route('admin.logout') }}" class="logout-btn">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </div>
</header>