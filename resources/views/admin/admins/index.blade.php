@extends('admin.layouts.app')

@section('title', 'Manage Admins')
@section('header_title', 'Manage Admins')
@section('header_subtitle', 'Create, update, and control admin access')

@section('content')
@include('admin.layouts.components.admin-theme')
@php
    $adminCollection = method_exists($admins, 'getCollection') ? $admins->getCollection() : collect($admins);

    $totalAdmins = $totalAdmins ?? (method_exists($admins, 'total') ? $admins->total() : $adminCollection->count());
    $activeAdmins = $activeAdmins ?? $adminCollection->where('status', 'active')->count();
    $superAdmins = $superAdmins ?? $adminCollection->where('is_super_admin', true)->count();
@endphp

<div class="gm-page-hero">
    <div class="gm-hero-kicker">
        <i class="bi bi-people"></i>
        GauMitra Admin Management
    </div>
    <h3 class="gm-hero-title">Manage Admins</h3>
    <p class="gm-hero-subtitle">
        Create, update, monitor, and control admin access for GauMitra rescue operations and dashboard modules.
    </p>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="gm-stat-card gm-gradient-1">
            <div class="gm-stat-icon">
                <i class="bi bi-people-fill"></i>
            </div>
            <h6>Total Admins</h6>
            <h2>{{ $totalAdmins }}</h2>
        </div>
    </div>

    <div class="col-md-4">
        <div class="gm-stat-card gm-gradient-2">
            <div class="gm-stat-icon">
                <i class="bi bi-person-check-fill"></i>
            </div>
            <h6>Active Admins</h6>
            <h2>{{ $activeAdmins }}</h2>
        </div>
    </div>

    <div class="col-md-4">
        <div class="gm-stat-card gm-gradient-3">
            <div class="gm-stat-icon">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h6>Super Admins</h6>
            <h2>{{ $superAdmins }}</h2>
        </div>
    </div>
</div>

<div class="gm-card">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h3 class="gm-card-title">Admin List</h3>
            <p class="gm-card-subtitle">Assign roles to each admin and control module access.</p>
        </div>

        <a href="{{ route('admin.admins.create') }}" class="gm-btn-primary text-decoration-none">
            <i class="bi bi-plus-circle me-1"></i> Create Admin
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
                    <th>Roles</th>
                    <th>Super Admin</th>
                    <th>Status</th>
                    <th width="210">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($admins as $item)
                    <tr>
                        <td class="fw-bold text-muted">
                            {{ method_exists($admins, 'firstItem') ? $admins->firstItem() + $loop->index : $loop->iteration }}
                        </td>

                        <td>
                            <div class="d-flex align-items-center">
                                <span class="gm-avatar">
                                    {{ strtoupper(substr($item->name, 0, 1)) }}
                                </span>
                                <div>
                                    <div class="fw-bold" style="color: var(--gm-brown);">
                                        {{ $item->name }}
                                    </div>
                                    <small class="text-muted">GauMitra Admin</small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="fw-semibold text-dark">
                                {{ $item->user_id }}
                            </span>
                        </td>

                        <td>
                            @forelse($item->roles as $role)
                                <span class="gm-badge-role">
                                    <i class="bi bi-patch-check me-1"></i>{{ $role->label }}
                                </span>
                            @empty
                                <span class="text-muted fw-semibold">No role assigned</span>
                            @endforelse
                        </td>

                        <td>
                            @if($item->is_super_admin)
                                <span class="gm-badge-dark">
                                    <i class="bi bi-shield-lock me-1"></i> Yes
                                </span>
                            @else
                                <span class="badge rounded-pill text-bg-secondary px-3 py-2">No</span>
                            @endif
                        </td>

                        <td>
                            @if($item->status === 'active')
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
                                <a
                                    href="{{ route('admin.admins.edit', $item->id) }}"
                                    class="btn btn-sm btn-outline-primary gm-action-btn"
                                >
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>

                                @if(!$item->is_super_admin)
                                    <form
                                        action="{{ route('admin.admins.destroy', $item->id) }}"
                                        method="POST"
                                        class="d-inline delete-form"
                                        data-confirm-text="This admin account will be marked as deleted. It will not be permanently removed."
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-outline-danger gm-action-btn">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="gm-empty text-center">
                                <i class="bi bi-person-x fs-2 d-block mb-2"></i>
                                No admins found.
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.delete-form');

        deleteForms.forEach(function (form) {
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
@endpush