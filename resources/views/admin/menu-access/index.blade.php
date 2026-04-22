@extends('admin.layouts.app')

@section('title', 'Menu Access')
@section('header_title', 'Menu Access')
@section('header_subtitle', 'Assign sidebar menus to admins and super admins')

@section('content')
<div class="page-card">
    <div class="mb-4">
        <h3 class="page-title">Admin Menu Assignment</h3>
        <p class="page-subtitle">Select which sidebar menus each admin can see after login.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>User ID</th>
                    <th>Type</th>
                    <th>Roles</th>
                    <th>Assigned Menus</th>
                    <th width="140">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($admins as $admin)
                    <tr>
                        <td>{{ $loop->iteration + ($admins->firstItem() - 1) }}</td>
                        <td class="fw-semibold">{{ $admin->name }}</td>
                        <td>{{ $admin->user_id }}</td>
                        <td>
                            @if($admin->is_super_admin)
                                <span class="badge bg-dark">Super Admin</span>
                            @else
                                <span class="badge bg-primary">Admin</span>
                            @endif
                        </td>
                        <td>
                            @forelse($admin->roles as $role)
                                <span class="badge bg-info text-dark me-1">{{ $role->label }}</span>
                            @empty
                                <span class="text-muted">No role assigned</span>
                            @endforelse
                        </td>
                        <td>
                            <span class="badge bg-success">{{ $admin->sidebarMenus->count() }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.menu-access.edit', $admin->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="bi bi-sliders me-1"></i> Assign
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No admin users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $admins->links() }}
    </div>
</div>
@endsection