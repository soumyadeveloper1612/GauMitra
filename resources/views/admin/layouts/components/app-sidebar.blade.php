@php
    $reportMenuOpen = request()->routeIs('admin.report-cases.*');
    $adminMenuOpen = request()->routeIs('admin.admins.*') || request()->routeIs('admin.roles.*');
    $gaushalaMenuOpen = request()->routeIs('admin.gaushalas.*');
@endphp

<aside class="sidebar" id="sidebar">
    <div class="sidebar-top">
        <div class="brand-box">
            <div class="brand-icon">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="brand-text">
                <h4>GauMitra</h4>
                <p>Admin Control Panel</p>
            </div>
        </div>
    </div>

    <div class="sidebar-scroll">
        <div class="nav-title">Main Menu</div>

        <ul class="sidebar-menu">
            <li class="menu-item">
                <a href="{{ route('admin.dashboard') }}"
                    class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="bi bi-grid-fill"></i></span>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{ route('admin.users.index') }}"
                    class="menu-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="bi bi-people-fill"></i></span>
                    <span class="nav-text">Users</span>
                </a>
            </li>

            <li class="menu-item has-submenu {{ $gaushalaMenuOpen ? 'open' : '' }}">
                <a href="javascript:void(0)" class="menu-link submenu-toggle {{ $gaushalaMenuOpen ? 'active' : '' }}">
                    <span class="nav-icon"><i class="bi bi-house-heart-fill"></i></span>
                    <span class="nav-text">Gaushala</span>
                    <span class="nav-badge">New</span>
                    <span class="submenu-arrow"><i class="bi bi-chevron-down"></i></span>
                </a>

                <ul class="submenu" style="{{ $gaushalaMenuOpen ? 'display:block;' : 'display:none;' }}">
                    <li>
                        <a href="{{ route('admin.gaushalas.create') }}"
                            class="{{ request()->routeIs('admin.gaushalas.create') ? 'active' : '' }}">
                            <span class="submenu-dot"></span>
                            <span>Create Gaushala</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.gaushalas.index') }}"
                            class="{{ request()->routeIs('admin.gaushalas.index') ? 'active' : '' }}">
                            <span class="submenu-dot"></span>
                            <span>Manage Gaushala</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item has-submenu {{ $reportMenuOpen ? 'open' : '' }}">
                <a href="javascript:void(0)" class="menu-link submenu-toggle {{ $reportMenuOpen ? 'active' : '' }}">
                    <span class="nav-icon"><i class="bi bi-clipboard-data-fill"></i></span>
                    <span class="nav-text">Reports</span>
                    <span class="nav-badge">Live</span>
                    <span class="submenu-arrow"><i class="bi bi-chevron-down"></i></span>
                </a>

                <ul class="submenu" style="{{ $reportMenuOpen ? 'display:block;' : 'display:none;' }}">
                    <li>
                        <a href="{{ route('admin.report-cases.index') }}"
                            class="{{ request()->routeIs('admin.report-cases.*') ? 'active' : '' }}">
                            <span class="submenu-dot"></span>
                            <span>Report Cases</span>
                        </a>
                    </li>

                    <li>
                        <a href="javascript:void(0)">
                            <span class="submenu-dot"></span>
                            <span>Emergency Alerts</span>
                        </a>
                    </li>

                    <li>
                        <a href="javascript:void(0)">
                            <span class="submenu-dot"></span>
                            <span>Case Tracking</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item has-submenu {{ $adminMenuOpen ? 'open' : '' }}">
                <a href="javascript:void(0)" class="menu-link submenu-toggle {{ $adminMenuOpen ? 'active' : '' }}">
                    <span class="nav-icon"><i class="bi bi-shield-lock-fill"></i></span>
                    <span class="nav-text">Admin Management</span>
                    <span class="submenu-arrow"><i class="bi bi-chevron-down"></i></span>
                </a>

                <ul class="submenu" style="{{ $adminMenuOpen ? 'display:block;' : 'display:none;' }}">
                    <li>
                        <a href="javascript:void(0)">
                            <span class="submenu-dot"></span>
                            <span>All Admins</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)">
                            <span class="submenu-dot"></span>
                            <span>Roles & Permissions</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#newsNoticeMenu" role="button"
                    aria-expanded="false">
                    <i class="bi bi-megaphone"></i>
                    <span>News & Notices</span>
                </a>
                <div class="collapse" id="newsNoticeMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a href="{{ route('admin.news-notices.create') }}" class="nav-link">Create News &
                                Notice</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.news-notices.index') }}" class="nav-link">Manage News &
                                Notices</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="menu-item">
                <a href="javascript:void(0)" class="menu-link">
                    <span class="nav-icon"><i class="bi bi-gear-fill"></i></span>
                    <span class="nav-text">Settings</span>
                </a>
            </li>
        </ul>

        <div class="nav-title">System</div>

        <ul class="sidebar-menu">
            <li class="menu-item">
                <a href="{{ route('admin.logout') }}" class="menu-link logout-menu">
                    <span class="nav-icon"><i class="bi bi-box-arrow-right"></i></span>
                    <span class="nav-text">Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-footer">
        <div class="sidebar-footer-card">
            <div class="footer-icon">
                <i class="bi bi-activity"></i>
            </div>
            <div class="footer-text">
                <h6>System Status</h6>
                <p>All services operational</p>
            </div>
        </div>
    </div>
</aside>
