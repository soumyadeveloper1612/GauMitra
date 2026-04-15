<header class="top-header">
    <div class="header-left">
        <button class="menu-toggle" id="menuToggle">
            <i class="bi bi-list"></i>
        </button>

        <div class="header-title">
            <div class="header-badge">
                <i class="bi bi-lightning-charge-fill"></i>
                <span>Admin Panel</span>
            </div>
            <h5>@yield('page_title', 'GauMitra Dashboard')</h5>
            <p>@yield('page_subtitle', 'Manage users, report cases, admins and system activity')</p>
        </div>
    </div>

    <div class="header-right">
        <div class="header-status-chip">
            <span class="status-dot"></span>
            <span>System Online</span>
        </div>

        <div class="admin-profile">
            <div class="admin-avatar">
                {{ strtoupper(substr(session('admin_name', 'A'), 0, 1)) }}
            </div>
            <div class="admin-info">
                <h6>{{ session('admin_name', 'Admin') }}</h6>
                <p>ID: {{ session('admin_user_id', 'admin') }}</p>
            </div>
        </div>

        <a href="{{ route('admin.logout') }}" class="logout-btn">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </div>
</header>