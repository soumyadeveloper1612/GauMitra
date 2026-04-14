<aside class="sidebar" id="sidebar">
    <div class="sidebar-inner">

        <div class="brand-box">
            <div class="brand-icon">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="brand-text">
                <h4>GauMitra</h4>
                <p>Admin Control Panel</p>
            </div>
        </div>

        <div class="sidebar-search-box">
            <div class="search-icon">
                <i class="bi bi-search"></i>
            </div>
            <input type="text" placeholder="Search menu..." id="sidebarMenuSearch">
        </div>

        <div class="quick-action-card">
            <div class="quick-action-text">
                <h6>Quick Action</h6>
                <p>Manage emergency reports faster</p>
            </div>
            <a href="{{ route('admin.report-cases.index') }}" class="quick-action-btn">
                <i class="bi bi-lightning-charge-fill"></i>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-title">Overview</div>

            <ul class="nav-list">
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-grid-fill"></i></span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <div class="nav-title">Management</div>

            <ul class="nav-list">
                <li>
                    <a href="{{ route('admin.users.index') }}"
                       class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-people-fill"></i></span>
                        <span class="nav-text">Users</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.gaushalas.index') }}"
                       class="{{ request()->routeIs('admin.gaushalas.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-house-heart-fill"></i></span>
                        <span class="nav-text">Gaushalas</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.report-cases.index') }}"
                       class="{{ request()->routeIs('admin.report-cases.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-clipboard-data-fill"></i></span>
                        <span class="nav-text">Report Cases</span>
                        <span class="nav-badge">Live</span>
                    </a>
                </li>

                <li>
                    <a href="javascript:void(0)"
                       class="{{ request()->routeIs('admin.admin-users.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="bi bi-shield-lock-fill"></i></span>
                        <span class="nav-text">Admin Management</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <div class="nav-title">Configuration</div>

            <ul class="nav-list">
                <li>
                    <a href="javascript:void(0)">
                        <span class="nav-icon"><i class="bi bi-gear-fill"></i></span>
                        <span class="nav-text">Settings</span>
                    </a>
                </li>

                @if(session('admin_role') === 'superadmin')
                    <li>
                        <a href="{{ route('superadmin.dashboard') }}"
                           class="{{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                            <span class="nav-icon"><i class="bi bi-stars"></i></span>
                            <span class="nav-text">Super Admin</span>
                            <span class="nav-badge nav-badge-gold">Pro</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        <div class="sidebar-bottom">
            <div class="admin-mini-card">
                <div class="admin-mini-avatar">
                    {{ strtoupper(substr(session('admin_name', 'A'), 0, 1)) }}
                </div>
                <div class="admin-mini-info">
                    <h6>{{ session('admin_name', 'Admin') }}</h6>
                    <p>{{ session('admin_role', 'Administrator') }}</p>
                </div>
            </div>

            <a href="{{ route('admin.logout') }}" class="logout-link">
                <span class="nav-icon"><i class="bi bi-box-arrow-right"></i></span>
                <span class="nav-text">Logout</span>
            </a>
        </div>

    </div>
</aside>