@extends('admin.layouts.app')

@section('title', 'Roles & Permissions')
@section('header_title', 'Roles & Permissions')
@section('header_subtitle', 'Manage role-wise access control for admins')

@section('content')
<div class="page-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-title">Role List</h3>
            <p class="page-subtitle">Create roles and assign permissions module-wise.</p>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-success rounded-pill px-4">
            <i class="bi bi-plus-circle me-1"></i> Create Role
        </a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>#</th>
                <th>Role</th>
                <th>Code</th>
                <th>Permissions</th>
                <th>Admins</th>
                <th>Status</th>
                <th width="180">Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse($roles as $role)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="fw-semibold">{{ $role->label }}</td>
                    <td>{{ $role->name }}</td>
                    <td><span class="badge bg-primary">{{ $role->permissions_count }}</span></td>
                    <td><span class="badge bg-info text-dark">{{ $role->admins_count }}</span></td>
                    <td>
                        @if($role->status === 'active')
                            <span class="badge-soft-success">Active</span>
                        @else
                            <span class="badge-soft-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        @if($role->name !== 'super_admin')
                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>

                            <form action="{{ route('admin.roles.destroy', $role->id) }}"
                                  method="POST"
                                  class="d-inline delete-form"
                                  data-confirm-text="This role and its mapping will be deleted.">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-pill">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        @else
                            <span class="badge bg-dark">Protected</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">No roles found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $roles->links() }}
    </div>
</div>
@endsection