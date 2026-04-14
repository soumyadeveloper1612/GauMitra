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

    <ul>
        <li>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.users.index') }}"
                class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>Users</span>
            </a>
        </li>

        <li>
            <a href="javascript:void(0)">
                <i class="bi bi-clipboard-data-fill"></i>
                <span>Reports</span>
            </a>
        </li>

        <li>
            <a href="javascript:void(0)">
                <i class="bi bi-shield-lock-fill"></i>
                <span>Admin Management</span>
            </a>
        </li>

        <li>
            <a href="javascript:void(0)">
                <i class="bi bi-gear-fill"></i>
                <span>Settings</span>
            </a>
        </li>
    </ul>

    <div class="nav-title">System</div>

    <ul>
        <li>
            <a href="{{ route('admin.logout') }}">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</aside>
