@php
    $admin = \App\Models\AdminUser::find(session('admin_id'));
    $menus = $admin ? $admin->assignedSidebarMenus() : collect();
@endphp

<aside class="sidebar" id="sidebar">

    {{-- Fixed Top Brand --}}
    <div class="sidebar-top">
        <div class="brand-box">
            <div class="brand-icon">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="brand-text">
                <h4>GauMitra</h4>
                <p>{{ $admin && $admin->is_super_admin ? 'Super Admin Panel' : 'Admin Panel' }}</p>
            </div>
        </div>
    </div>

    {{-- Scrollable Menu Area --}}
    <div class="sidebar-scroll">

        <div class="nav-title">Main Menu</div>

        <ul class="sidebar-menu">
            @forelse($menus as $menu)
                @php
                    if (!$menu->canBeSeenBy($admin)) {
                        continue;
                    }

                    $children = $menu->children->filter(function ($child) use ($admin) {
                        return $child->canBeSeenBy($admin);
                    });

                    $hasChildren = $children->count() > 0;

                    $menuActive = $menu->isActiveFor($admin) || $children->contains(function ($child) use ($admin) {
                        return $child->isActiveFor($admin);
                    });

                    $menuUrl = $menu->getUrlFor($admin);
                @endphp

                @if($hasChildren)
                    <li class="menu-item has-submenu {{ $menuActive ? 'open' : '' }}">
                        <a href="javascript:void(0)"
                           class="menu-link submenu-toggle {{ $menuActive ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="{{ $menu->icon ?: 'bi bi-circle' }}"></i>
                            </span>
                            <span class="nav-text">{{ $menu->title }}</span>
                            <span class="submenu-arrow">
                                <i class="bi bi-chevron-down"></i>
                            </span>
                        </a>

                        <ul class="submenu" style="{{ $menuActive ? 'display:block;' : 'display:none;' }}">
                            @foreach($children as $child)
                                <li>
                                    <a href="{{ $child->getUrlFor($admin) }}"
                                       class="{{ $child->isActiveFor($admin) ? 'active' : '' }}">
                                        <span class="submenu-dot"></span>
                                        <span>{{ $child->title }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @else
                    <li class="menu-item">
                        <a href="{{ $menuUrl }}" class="menu-link {{ $menuActive ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="{{ $menu->icon ?: 'bi bi-circle' }}"></i>
                            </span>
                            <span class="nav-text">{{ $menu->title }}</span>
                        </a>
                    </li>
                @endif
            @empty
                <li class="menu-item">
                    <div class="menu-link text-muted">
                        <span class="nav-icon">
                            <i class="bi bi-info-circle"></i>
                        </span>
                        <span class="nav-text">No menu assigned</span>
                    </div>
                </li>
            @endforelse
        </ul>

        <div class="nav-title">System</div>

        <ul class="sidebar-menu pb-4">
            <li class="menu-item">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="menu-link logout-menu w-100 border-0 text-start bg-transparent">
                        <span class="nav-icon">
                            <i class="bi bi-box-arrow-right"></i>
                        </span>
                        <span class="nav-text">Logout</span>
                    </button>
                </form>
            </li>
        </ul>

    </div>
</aside>