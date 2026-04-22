@extends('admin.layouts.app')

@section('title', 'Sidebar Menus')
@section('header_title', 'Sidebar Menus')
@section('header_subtitle', 'Add and manage sidebar menu items')

@section('content')
<div class="page-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-title">Sidebar Menu List</h3>
            <p class="page-subtitle">Manage parent and child menus for admin sidebar.</p>
        </div>
        <a href="{{ route('admin.sidebar-menus.create') }}" class="btn btn-success rounded-pill px-4">
            <i class="bi bi-plus-circle me-1"></i> Add Sidebar Menu
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger rounded-4">{{ session('error') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Key</th>
                    <th>Parent</th>
                    <th>Route</th>
                    <th>Permission</th>
                    <th>Sort</th>
                    <th>Status</th>
                    <th width="180">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menus as $menu)
                    <tr>
                        <td>{{ $loop->iteration + ($menus->firstItem() - 1) }}</td>
                        <td class="fw-semibold">
                            @if($menu->parent_id)
                                <span class="ms-3">— {{ $menu->title }}</span>
                            @else
                                {{ $menu->title }}
                            @endif
                        </td>
                        <td>{{ $menu->menu_key }}</td>
                        <td>{{ $menu->parent?->title ?? 'Parent Menu' }}</td>
                        <td>{{ $menu->route_name ?? '-' }}</td>
                        <td>{{ $menu->permission_name ?? '-' }}</td>
                        <td>{{ $menu->sort_order }}</td>
                        <td>
                            @if($menu->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.sidebar-menus.edit', $menu->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>

                            <form action="{{ route('admin.sidebar-menus.destroy', $menu->id) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this menu?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">No sidebar menus found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $menus->links() }}
    </div>
</div>
@endsection