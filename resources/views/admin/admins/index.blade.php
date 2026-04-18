@extends('admin.layouts.app')

@section('title', 'Manage Admins')
@section('header_title', 'Manage Admins')
@section('header_subtitle', 'Create, update, and control admin access')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card gradient-1">
            <h6>Total Admins</h6>
            <h2>{{ $admins->total() }}</h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card gradient-2">
            <h6>Active Admins</h6>
            <h2>{{ $admins->where('status', 'active')->count() }}</h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card gradient-3">
            <h6>Super Admins</h6>
            <h2>{{ $admins->where('is_super_admin', true)->count() }}</h2>
        </div>
    </div>
</div>

<div class="page-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-title">Admin List</h3>
            <p class="page-subtitle">Assign roles to each admin and control module access.</p>
        </div>
        <a href="{{ route('admin.admins.create') }}" class="btn btn-success rounded-pill px-4">
            <i class="bi bi-plus-circle me-1"></i> Create Admin
        </a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>User ID</th>
                <th>Roles</th>
                <th>Super Admin</th>
                <th>Status</th>
                <th width="180">Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse($admins as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="fw-semibold">{{ $item->name }}</td>
                    <td>{{ $item->user_id }}</td>
                    <td>
                        @forelse($item->roles as $role)
                            <span class="badge bg-primary">{{ $role->label }}</span>
                        @empty
                            <span class="text-muted">No role assigned</span>
                        @endforelse
                    </td>
                    <td>
                        @if($item->is_super_admin)
                            <span class="badge bg-dark">Yes</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </td>
                    <td>
                        @if($item->status === 'active')
                            <span class="badge-soft-success">Active</span>
                        @else
                            <span class="badge-soft-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.admins.edit', $item->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>

                        @if(!$item->is_super_admin)
                            <form action="{{ route('admin.admins.destroy', $item->id) }}"
                                  method="POST"
                                  class="d-inline delete-form"
                                  data-confirm-text="This admin account will be permanently removed.">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-pill">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No admins found.</td>
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