<header class="top-header">
    <div class="header-left">
        <button class="menu-toggle" id="menuToggle">
            <i class="bi bi-list"></i>
        </button>

        <div class="header-title">
            <h5>GauMitra Dashboard</h5>
            <p>Manage users, admins, reports and platform activity</p>
        </div>
    </div>

    <div class="header-right">
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
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</header>