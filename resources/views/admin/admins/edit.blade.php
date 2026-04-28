@extends('admin.layouts.app')

@section('title', 'Edit Admin')
@section('header_title', 'Edit Admin')
@section('header_subtitle', 'Update admin details and roles')

@section('content')
@include('admin.layouts.components.admin-theme')

<div class="gm-page-hero">
    <div class="gm-hero-kicker">
        <i class="bi bi-person-gear"></i>
        GauMitra Admin Control
    </div>
    <h3 class="gm-hero-title">Edit Admin Access</h3>
    <p class="gm-hero-subtitle">
        Update admin profile, status, password, and assigned roles for secure GauMitra panel management.
    </p>
</div>

<div class="gm-card">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h3 class="gm-card-title">Edit Admin</h3>
            <p class="gm-card-subtitle">Update admin information and access roles.</p>
        </div>

        <a href="{{ route('admin.admins.index') }}" class="gm-btn-light text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    @if ($errors->any())
        <div class="alert gm-alert mb-4">
            <strong><i class="bi bi-exclamation-triangle me-1"></i> Please fix these errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.admins.update', $admin->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-md-6">
                <label class="gm-form-label">Admin Name <span class="text-danger">*</span></label>
                <div class="d-flex gap-2 align-items-center">
                    <span class="gm-input-icon"><i class="bi bi-person-badge"></i></span>
                    <input
                        type="text"
                        name="name"
                        class="form-control gm-form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $admin->name) }}"
                        placeholder="Enter admin full name"
                    >
                </div>
                @error('name')
                    <small class="text-danger fw-semibold">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="gm-form-label">User ID <span class="text-danger">*</span></label>
                <div class="d-flex gap-2 align-items-center">
                    <span class="gm-input-icon"><i class="bi bi-person-vcard"></i></span>
                    <input
                        type="text"
                        name="user_id"
                        class="form-control gm-form-control @error('user_id') is-invalid @enderror"
                        value="{{ old('user_id', $admin->user_id) }}"
                        placeholder="Example: admin001"
                    >
                </div>
                @error('user_id')
                    <small class="text-danger fw-semibold">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="gm-form-label">New Password</label>
                <div class="d-flex gap-2 align-items-center">
                    <span class="gm-input-icon"><i class="bi bi-key"></i></span>
                    <input
                        type="password"
                        name="password"
                        class="form-control gm-form-control @error('password') is-invalid @enderror"
                        placeholder="Leave blank to keep old password"
                    >
                </div>
                @error('password')
                    <small class="text-danger fw-semibold">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="gm-form-label">Status <span class="text-danger">*</span></label>
                <div class="d-flex gap-2 align-items-center">
                    <span class="gm-input-icon"><i class="bi bi-toggle-on"></i></span>
                    <select name="status" class="form-select gm-form-select @error('status') is-invalid @enderror">
                        <option value="active" {{ old('status', $admin->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $admin->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                @error('status')
                    <small class="text-danger fw-semibold">{{ $message }}</small>
                @enderror
            </div>

            @if(admin_user()?->is_super_admin)
                <div class="col-12">
                    <div class="gm-role-card">
                        <div class="form-check form-switch gm-switch">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="is_super_admin"
                                value="1"
                                id="is_super_admin"
                                {{ old('is_super_admin', $admin->is_super_admin) ? 'checked' : '' }}
                            >
                            <label class="form-check-label fw-bold text-dark ms-2" for="is_super_admin">
                                Super Admin
                            </label>
                        </div>
                        <small class="text-muted d-block mt-2">
                            Super Admin gets full access to all GauMitra modules and role controls.
                        </small>
                    </div>
                </div>
            @endif

            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div>
                        <label class="gm-form-label mb-1">Assign Roles</label>
                        <p class="text-muted mb-0">Update this admin’s assigned permissions through roles.</p>
                    </div>
                </div>

                <div class="row">
                    @php
                        $selectedRoles = old('roles', $admin->roles->pluck('id')->toArray());
                    @endphp

                    @forelse($roles as $role)
                        <div class="col-md-4 mb-3">
                            <div class="gm-role-card">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="roles[]"
                                        value="{{ $role->id }}"
                                        id="role_{{ $role->id }}"
                                        {{ in_array($role->id, $selectedRoles) ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label fw-bold text-dark" for="role_{{ $role->id }}">
                                        <i class="bi bi-patch-check text-warning me-1"></i>
                                        {{ $role->label }}
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    {{ $role->description ?? 'Role permissions configured by Super Admin.' }}
                                </small>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="gm-empty text-center">
                                <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
                                No roles found. Please create roles first.
                            </div>
                        </div>
                    @endforelse
                </div>

                @error('roles')
                    <small class="text-danger fw-semibold">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                <button type="submit" class="gm-btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Update Admin
                </button>

                <a href="{{ route('admin.admins.index') }}" class="gm-btn-light text-decoration-none">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>
@endsection