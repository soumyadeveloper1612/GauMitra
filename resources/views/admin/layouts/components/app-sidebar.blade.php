@php
    $admin = \App\Models\AdminUser::find(session('admin_id'));
    $menus = $admin ? $admin->assignedSidebarMenus() : collect();

    $isMenuAllowed = function ($menu) {
        if ($menu->status !== 'active') {
            return false;
        }

        if (!empty($menu->permission_name) && !admin_can($menu->permission_name)) {
            return false;
        }

        return true;
    };

    $getMenuUrl = function ($menu, $admin) {
        if ($menu->slug === 'dashboard') {
            return $admin->is_super_admin
                ? route('superadmin.dashboard')
                : route('admin.dashboard');
        }

        if (!empty($menu->route_name) && \Illuminate\Support\Facades\Route::has($menu->route_name)) {
            return route($menu->route_name);
        }

        if (!empty($menu->custom_url)) {
            return $menu->custom_url;
        }

        return 'javascript:void(0)';
    };

    $isMenuActive = function ($menu, $admin) {
        if ($menu->slug === 'dashboard') {
            return $admin->is_super_admin
                ? request()->routeIs('superadmin.dashboard')
                : request()->routeIs('admin.dashboard');
        }

        if (!empty($menu->active_pattern)) {
            $patterns = array_filter(array_map('trim', explode(',', $menu->active_pattern)));

            foreach ($patterns as $pattern) {
                if (request()->routeIs($pattern)) {
                    return true;
                }
            }
        }

        if (!empty($menu->route_name)) {
            return request()->routeIs($menu->route_name);
        }

        return false;
    };
@endphp

<aside class="sidebar" id="sidebar">
    <div class="brand-box">
        <div class="brand-icon">
            <i class="bi bi-shield-check"></i>
        </div>
        <div class="brand-text">
            <h4>GauMitra</h4>
            <p>{{ $admin && $admin->is_super_admin ? 'Super Admin Panel' : 'Admin Panel' }}</p>
        </div>
    </div>

    <div class="nav-title">Main Menu</div>

    <ul class="sidebar-menu">
        @forelse($menus as $menu)
            @php
                $children = $menu->children->filter(function ($child) use ($isMenuAllowed) {
                    return $isMenuAllowed($child);
                });

                $hasChildren = $children->count() > 0;
                $menuActive = $isMenuActive($menu, $admin) || $children->contains(function ($child) use ($isMenuActive, $admin) {
                    return $isMenuActive($child, $admin);
                });

                $menuUrl = $getMenuUrl($menu, $admin);
            @endphp

            @if($hasChildren)
                <li class="menu-item has-submenu {{ $menuActive ? 'open' : '' }}">
                    <a href="javascript:void(0)" class="menu-link submenu-toggle {{ $menuActive ? 'active' : '' }}">
                        <span class="nav-icon"><i class="{{ $menu->icon ?: 'bi bi-circle' }}"></i></span>
                        <span class="nav-text">{{ $menu->title }}</span>
                        <span class="submenu-arrow"><i class="bi bi-chevron-down"></i></span>
                    </a>

                    <ul class="submenu" style="{{ $menuActive ? 'display:block;' : 'display:none;' }}">
                        @foreach($children as $child)
                            @php
                                $childUrl = $getMenuUrl($child, $admin);
                            @endphp

                            <li>
                                <a href="{{ $childUrl }}" class="{{ $isMenuActive($child, $admin) ? 'active' : '' }}">
                                    <span class="submenu-dot"></span>
                                    <span>{{ $child->title }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @else
                @if($isMenuAllowed($menu))
                    <li class="menu-item">
                        <a href="{{ $menuUrl }}" class="menu-link {{ $menuActive ? 'active' : '' }}">
                            <span class="nav-icon"><i class="{{ $menu->icon ?: 'bi bi-circle' }}"></i></span>
                            <span class="nav-text">{{ $menu->title }}</span>
                        </a>
                    </li>
                @endif
            @endif
        @empty
            <li class="menu-item">
                <div class="menu-link text-muted">
                    <span class="nav-icon"><i class="bi bi-info-circle"></i></span>
                    <span class="nav-text">No menu assigned</span>
                </div>
            </li>
        @endforelse
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