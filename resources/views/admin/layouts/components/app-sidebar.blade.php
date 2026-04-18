@php
    $reportMenuOpen = request()->routeIs('admin.report-cases.*');
    $adminMenuOpen = request()->routeIs('admin.admins.*') || request()->routeIs('admin.roles.*');
    $gaushalaMenuOpen = request()->routeIs('admin.gaushalas.*');
    $newsNoticeMenuOpen = request()->routeIs('admin.news-notices.*');
@endphp

<aside class="sidebar" id="sidebar">
    <div class="brand-box">
        <div class="brand-icon">
            <i class="bi bi-shield-check"></i>
        </div>
        <div class="brand-text">
            <h4>GauMitra</h4>
            <p>Admin Control Panel</p>
        </div>
    </div>

    <div class="nav-title">Main Menu</div>

    <ul class="sidebar-menu">
        @if(admin_can('dashboard.view'))
            <li class="menu-item">
                <a href="{{ route('admin.dashboard') }}"
                   class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="bi bi-grid-fill"></i></span>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
        @endif

        @if(admin_can('users.view'))
            <li class="menu-item">
                <a href="{{ route('admin.users.index') }}"
                   class="menu-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="bi bi-people-fill"></i></span>
                    <span class="nav-text">Users</span>
                </a>
            </li>
        @endif

        @if(admin_can('gaushala.create') || admin_can('gaushala.view'))
            <li class="menu-item has-submenu {{ $gaushalaMenuOpen ? 'open' : '' }}">
                <a href="javascript:void(0)" class="menu-link submenu-toggle {{ $gaushalaMenuOpen ? 'active' : '' }}">
                    <span class="nav-icon"><i class="bi bi-house-heart-fill"></i></span>
                    <span class="nav-text">Gaushala</span>
                    <span class="submenu-arrow"><i class="bi bi-chevron-down"></i></span>
                </a>

                <ul class="submenu" style="{{ $gaushalaMenuOpen ? 'display:block;' : 'display:none;' }}">
                    @if(admin_can('gaushala.create'))
                        <li>
                            <a href="{{ route('admin.gaushalas.create') }}"
                               class="{{ request()->routeIs('admin.gaushalas.create') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Create Gaushala</span>
                            </a>
                        </li>
                    @endif

                    @if(admin_can('gaushala.view'))
                        <li>
                            <a href="{{ route('admin.gaushalas.index') }}"
                               class="{{ request()->routeIs('admin.gaushalas.index') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Manage Gaushala</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        @if(admin_can('report_cases.view'))
            <li class="menu-item has-submenu {{ $reportMenuOpen ? 'open' : '' }}">
                <a href="javascript:void(0)" class="menu-link submenu-toggle {{ $reportMenuOpen ? 'active' : '' }}">
                    <span class="nav-icon"><i class="bi bi-clipboard-data-fill"></i></span>
                    <span class="nav-text">Reports</span>
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
                </ul>
            </li>
        @endif

        @if(admin_can('news_notice.create') || admin_can('news_notice.view'))
            <li class="menu-item has-submenu {{ $newsNoticeMenuOpen ? 'open' : '' }}">
                <a href="javascript:void(0)" class="menu-link submenu-toggle {{ $newsNoticeMenuOpen ? 'active' : '' }}">
                    <span class="nav-icon"><i class="bi bi-megaphone-fill"></i></span>
                    <span class="nav-text">News & Notices</span>
                    <span class="submenu-arrow"><i class="bi bi-chevron-down"></i></span>
                </a>

                <ul class="submenu" style="{{ $newsNoticeMenuOpen ? 'display:block;' : 'display:none;' }}">
                    @if(admin_can('news_notice.create'))
                        <li>
                            <a href="{{ route('admin.news-notices.create') }}"
                               class="{{ request()->routeIs('admin.news-notices.create') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Create News & Notice</span>
                            </a>
                        </li>
                    @endif

                    @if(admin_can('news_notice.view'))
                        <li>
                            <a href="{{ route('admin.news-notices.index') }}"
                               class="{{ request()->routeIs('admin.news-notices.index') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Manage News & Notices</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        @if(admin_can('admins.manage') || admin_can('roles.manage'))
            <li class="menu-item has-submenu {{ $adminMenuOpen ? 'open' : '' }}">
                <a href="javascript:void(0)" class="menu-link submenu-toggle {{ $adminMenuOpen ? 'active' : '' }}">
                    <span class="nav-icon"><i class="bi bi-shield-lock-fill"></i></span>
                    <span class="nav-text">Admin Management</span>
                    <span class="submenu-arrow"><i class="bi bi-chevron-down"></i></span>
                </a>

                <ul class="submenu" style="{{ $adminMenuOpen ? 'display:block;' : 'display:none;' }}">
                    @if(admin_can('admins.manage'))
                        <li>
                            <a href="{{ route('admin.admins.index') }}"
                               class="{{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>All Admins</span>
                            </a>
                        </li>
                    @endif

                    @if(admin_can('roles.manage'))
                        <li>
                            <a href="{{ route('admin.roles.index') }}"
                               class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Roles & Permissions</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
    </ul>

    <div class="nav-title">System</div>

    <ul class="sidebar-menu">
        <li class="menu-item">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="menu-link w-100 border-0 text-start bg-transparent">
                    <span class="nav-icon"><i class="bi bi-box-arrow-right"></i></span>
                    <span class="nav-text">Logout</span>
                </button>
            </form>
        </li>
    </ul>
</aside>