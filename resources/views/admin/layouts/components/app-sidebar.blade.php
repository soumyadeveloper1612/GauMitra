@php
    $admin = \App\Models\AdminUser::find(session('admin_id'));
    $menus = $admin ? $admin->assignedSidebarMenus() : collect();

    $resolveRouteName = function ($menu) {
        if (is_super_admin() && !empty($menu->super_admin_route_name)) {
            return $menu->super_admin_route_name;
        }

        return $menu->route_name;
    };

    $isMenuAllowed = function ($menu) {
        if ($menu->status !== 'active') {
            return false;
        }

        if (!empty($menu->permission_name) && !admin_can($menu->permission_name)) {
            return false;
        }

        return true;
    };

    $isMenuActive = function ($menu) use ($resolveRouteName) {
        $patterns = [];

        if (!empty($menu->route_pattern)) {
            $patterns = array_filter(array_map('trim', explode(',', $menu->route_pattern)));
        }

        $resolvedRoute = $resolveRouteName($menu);

        if (!empty($resolvedRoute)) {
            $patterns[] = $resolvedRoute;
        }

        foreach ($patterns as $pattern) {
            if (request()->routeIs($pattern)) {
                return true;
            }
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
            <p>{{ is_super_admin() ? 'Super Admin Panel' : 'Admin Panel' }}</p>
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
                $menuActive = $isMenuActive($menu) || $children->contains(function ($child) use ($isMenuActive) {
                    return $isMenuActive($child);
                });

                $menuRoute = $resolveRouteName($menu);
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
                                $childRoute = $resolveRouteName($child);
                            @endphp

                            @if($childRoute && Route::has($childRoute))
                                <li>
                                    <a href="{{ route($childRoute) }}" class="{{ $isMenuActive($child) ? 'active' : '' }}">
                                        <span class="submenu-dot"></span>
                                        <span>{{ $child->title }}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </li>
            @else
                @if($isMenuAllowed($menu) && $menuRoute && Route::has($menuRoute))
                    <li class="menu-item">
                        <a href="{{ route($menuRoute) }}" class="menu-link {{ $menuActive ? 'active' : '' }}">
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