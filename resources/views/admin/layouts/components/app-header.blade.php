<header class="top-header">
    <div class="header-left">
        <button class="menu-toggle" id="menuToggle" type="button">
            <i class="bi bi-list"></i>
        </button>

        <div class="header-title-wrap">
            <div class="header-title">
                <div class="header-badge">
                    <i class="bi bi-lightning-charge-fill"></i>
                    <span>Admin Panel</span>
                </div>

                <h5>@yield('page_title', 'GauMitra Dashboard')</h5>
                <p>@yield('page_subtitle', 'Manage users, report cases, admins and system activity')</p>
            </div>

            <div class="header-slogan-card">
                <div class="slogan-icon">
                    <i class="bi bi-stars"></i>
                </div>
                <div class="slogan-content">
                    <span class="slogan-label">Sacred Principle</span>
                    <h6 class="slogan-text">धर्मो रक्षति रक्षितः</h6>
                </div>
                <div class="slogan-glow"></div>
            </div>
        </div>
    </div>

    <div class="header-right">
        <div class="live-datetime-card">
            <div class="live-time-icon">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="live-datetime-content">
                <span class="live-label">Live Time</span>
                <h6 id="liveTime">--:--:--</h6>
                <p id="liveDate">Loading date...</p>
            </div>
        </div>

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

        <form action="{{ route('admin.logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</header>