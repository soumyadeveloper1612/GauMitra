@extends('admin.layouts.app')

@section('title', 'Menu Access')
@section('header_title', 'Menu Access')
@section('header_subtitle', 'Assign sidebar menus to admins and super admins')

@section('content')
@include('admin.admins.partials.admin-theme')

<style>
    .gm-menu-count {
        width: 42px;
        height: 42px;
        border-radius: 15px;
        background: linear-gradient(135deg, #f57c00, #d35400);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        box-shadow: 0 10px 22px rgba(211, 84, 0, 0.25);
    }

    .gm-type-super {
        background: rgba(59, 29, 12, 0.12);
        color: #3b1d0c;
        border-radius: 999px;
        padding: 7px 12px;
        font-weight: 800;
    }

    .gm-type-admin {
        background: rgba(245, 124, 0, 0.13);
        color: #d35400;
        border-radius: 999px;
        padding: 7px 12px;
        font-weight: 800;
    }

    .gm-mini-role {
        background: #fff0d3;
        color: #5d2f12;
        border: 1px solid rgba(245, 124, 0, 0.25);
        border-radius: 999px;
        padding: 6px 10px;
        font-weight: 700;
        display: inline-block;
        margin: 2px;
        font-size: 12px;
    }
</style>

<div class="gm-page-hero">
    <div class="gm-hero-kicker">
        <i class="bi bi-grid-1x2"></i>
        GauMitra Sidebar Access
    </div>
    <h3 class="gm-hero-title">Admin Menu Assignment</h3>
    <p class="gm-hero-subtitle">
        Select which sidebar menus each admin can see after login. Super Admin users automatically receive full menu access.
    </p>
</div>

<div class="gm-card">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h3 class="gm-card-title">Menu Access List</h3>
            <p class="gm-card-subtitle">
                Manage sidebar visibility for GauMitra admin users.
            </p>
        </div>

        <a href="{{ route('admin.admins.index') }}" class="gm-btn-light text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Admins
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4 fw-semibold">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert gm-alert fw-semibold">
            <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table gm-table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Admin</th>
                    <th>User ID</th>
                    <th>Type</th>
                    <th>Roles</th>
                    <th>Assigned Menus</th>
                    <th width="160">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($admins as $admin)
                    <tr>
                        <td class="fw-bold text-muted">
                            {{ method_exists($admins, 'firstItem') ? $admins->firstItem() + $loop->index : $loop->iteration }}
                        </td>

                        <td>
                            <div class="d-flex align-items-center">
                                <span class="gm-avatar">
                                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                                </span>
                                <div>
                                    <div class="fw-bold" style="color: var(--gm-brown);">
                                        {{ $admin->name }}
                                    </div>
                                    <small class="text-muted">
                                        GauMitra Access User
                                    </small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="fw-semibold text-dark">
                                {{ $admin->user_id }}
                            </span>
                        </td>

                        <td>
                            @if($admin->is_super_admin)
                                <span class="gm-type-super">
                                    <i class="bi bi-shield-lock me-1"></i> Super Admin
                                </span>
                            @else
                                <span class="gm-type-admin">
                                    <i class="bi bi-person-badge me-1"></i> Admin
                                </span>
                            @endif
                        </td>

                        <td>
                            @forelse($admin->roles as $role)
                                <span class="gm-mini-role">
                                    <i class="bi bi-patch-check me-1"></i>{{ $role->label }}
                                </span>
                            @empty
                                <span class="text-muted fw-semibold">No role assigned</span>
                            @endforelse
                        </td>

                        <td>
                            @if($admin->is_super_admin)
                                <span class="gm-badge-dark">
                                    <i class="bi bi-infinity me-1"></i> All
                                </span>
                            @else
                                <span class="gm-menu-count">
                                    {{ $admin->sidebarMenus->count() }}
                                </span>
                            @endif
                        </td>

                        <td>
                            <a
                                href="{{ route('admin.menu-access.edit', $admin->id) }}"
                                class="btn btn-sm btn-outline-primary gm-action-btn"
                            >
                                <i class="bi bi-sliders me-1"></i> Assign
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="gm-empty text-center">
                                <i class="bi bi-person-x fs-2 d-block mb-2"></i>
                                No admin users found.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($admins, 'links'))
        <div class="mt-4">
            {{ $admins->links() }}
        </div>
    @endif
</div>
@endsection