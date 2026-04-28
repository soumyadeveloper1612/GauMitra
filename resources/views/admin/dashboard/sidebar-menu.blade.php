@extends('admin.layouts.app')

@section('title', 'Sidebar Menus')
@section('header_title', 'Sidebar Menus')
@section('header_subtitle', 'Add and manage sidebar menu items')

@section('content')
@include('admin.dashboard.sidebar-menu-theme')

<div class="gm-page-hero">
    <div class="gm-hero-kicker">
        <i class="bi bi-layout-sidebar-inset"></i>
        GauMitra Sidebar Control
    </div>
    <h3 class="gm-hero-title">Sidebar Menu List</h3>
    <p class="gm-hero-subtitle">
        Manage parent and child menus for the GauMitra admin sidebar, route access, permissions, and active patterns.
    </p>
</div>

<div class="gm-card">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h3 class="gm-card-title">Sidebar Menus</h3>
            <p class="gm-card-subtitle">Manage parent and child menus for admin sidebar.</p>
        </div>

        <a href="{{ route('admin.sidebar-menus.create') }}" class="gm-btn-primary text-decoration-none">
            <i class="bi bi-plus-circle me-1"></i> Add Sidebar Menu
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4 fw-semibold">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert gm-alert">
            <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table gm-table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Parent</th>
                    <th>Route</th>
                    <th>Active Pattern</th>
                    <th>Permission</th>
                    <th>Sort</th>
                    <th>Status</th>
                    <th width="200">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($menus as $menu)
                    <tr>
                        <td class="fw-bold text-muted">
                            {{ method_exists($menus, 'firstItem') ? $menus->firstItem() + $loop->index : $loop->iteration }}
                        </td>

                        <td>
                            <div class="d-flex align-items-center">
                                <span class="gm-menu-icon">
                                    <i class="{{ $menu->icon ?? 'bi bi-grid' }}"></i>
                                </span>

                                <div>
                                    <div class="gm-menu-title-text">
                                        @if($menu->parent_id)
                                            <span class="text-muted me-1">—</span>{{ $menu->title }}
                                        @else
                                            {{ $menu->title }}
                                        @endif
                                    </div>

                                    <small class="gm-small-help">
                                        {{ $menu->parent_id ? 'Child Menu' : 'Parent Menu' }}
                                    </small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="fw-semibold text-dark">
                                {{ $menu->slug }}
                            </span>
                        </td>

                        <td>
                            <span class="gm-parent-chip">
                                {{ $menu->parent?->title ?? 'Parent Menu' }}
                            </span>
                        </td>

                        <td>
                            <span class="gm-small-help">
                                {{ $menu->route_name ?? '-' }}
                            </span>
                        </td>

                        <td>
                            <span class="gm-small-help">
                                {{ $menu->active_pattern ?? '-' }}
                            </span>
                        </td>

                        <td>
                            @if($menu->permission_name)
                                <span class="gm-badge-dark">
                                    {{ $menu->permission_name }}
                                </span>
                            @else
                                <span class="text-muted fw-semibold">No Permission</span>
                            @endif
                        </td>

                        <td>
                            <span class="fw-bold" style="color: var(--gm-brown);">
                                {{ $menu->sort_order }}
                            </span>
                        </td>

                        <td>
                            @if($menu->status === 'active')
                                <span class="gm-badge-success">
                                    <i class="bi bi-check-circle me-1"></i> Active
                                </span>
                            @else
                                <span class="gm-badge-danger">
                                    <i class="bi bi-x-circle me-1"></i> Inactive
                                </span>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.sidebar-menus.edit', $menu->id) }}"
                                   class="btn btn-sm btn-outline-primary gm-action-btn">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>

                                <form action="{{ route('admin.sidebar-menus.destroy', $menu->id) }}"
                                      method="POST"
                                      class="d-inline delete-form"
                                      data-confirm-text="This sidebar menu will be deleted.">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-outline-danger gm-action-btn">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">
                            <div class="gm-empty text-center">
                                <i class="bi bi-layout-sidebar-inset fs-2 d-block mb-2"></i>
                                No sidebar menus found.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($menus, 'links'))
        <div class="mt-4">
            {{ $menus->links() }}
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const confirmText = form.getAttribute('data-confirm-text') || 'Are you sure?';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: confirmText,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d35400',
                    cancelButtonColor: '#5d2f12',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel',
                    background: '#fff8ec',
                    color: '#5d2f12'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if (confirm(confirmText)) {
                    form.submit();
                }
            }
        });
    });
});
</script>
@endsection