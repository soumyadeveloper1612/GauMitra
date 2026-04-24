<header class="top-header">
    <div class="header-left">
        <button class="menu-toggle" id="menuToggle">
            <i class="bi bi-list"></i>
        </button>

        <div class="header-title">
            <h5>@yield('page_title', 'GauMitra Dashboard')</h5>
        </div>
    </div>

    <div class="header-center">
        <div class="header-mantra">
            <i class="bi bi-shield-check"></i>
            <span>धर्मो रक्षति रक्षितः</span>
        </div>

        <div class="header-date-time">
            <i class="bi bi-calendar2-week"></i>
            <div>
                <strong id="liveDate">{{ now()->format('d M Y') }}</strong>
                <small id="liveTime">{{ now()->format('h:i:s A') }}</small>
            </div>
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

        <form action="{{ route('admin.logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</header>