@extends('admin.layouts.app')

@section('title', 'Create Admin')
@section('header_title', 'Create Admin')
@section('header_subtitle', 'Add a new admin and assign roles')

@section('content')
@include('admin.layouts.components.admin-theme')

<div class="gm-page-hero">
    <div class="gm-hero-kicker">
        <i class="bi bi-shield-lock"></i>
        GauMitra Admin Access
    </div>
    <h3 class="gm-hero-title">Create New Admin</h3>
    <p class="gm-hero-subtitle">
        Add a trusted GauMitra team member and assign proper roles for managing rescue, gaushala, notification, and dashboard modules.
    </p>
</div>

<div class="gm-card">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h3 class="gm-card-title">New Admin Details</h3>
            <p class="gm-card-subtitle">Fill in the admin details and choose roles carefully.</p>
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

    <form action="{{ route('admin.admins.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            <div class="col-md-6">
                <label class="gm-form-label">Admin Name <span class="text-danger">*</span></label>
                <div class="d-flex gap-2 align-items-center">
                    <span class="gm-input-icon"><i class="bi bi-person-badge"></i></span>
                    <input
                        type="text"
                        name="name"
                        class="form-control gm-form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
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
                        value="{{ old('user_id') }}"
                        placeholder="Example: admin001"
                    >
                </div>
                @error('user_id')
                    <small class="text-danger fw-semibold">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="gm-form-label">Password <span class="text-danger">*</span></label>
                <div class="d-flex gap-2 align-items-center">
                    <span class="gm-input-icon"><i class="bi bi-key"></i></span>
                    <input
                        type="password"
                        name="password"
                        class="form-control gm-form-control @error('password') is-invalid @enderror"
                        placeholder="Enter secure password"
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
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                {{ old('is_super_admin') ? 'checked' : '' }}
                            >
                            <label class="form-check-label fw-bold text-dark ms-2" for="is_super_admin">
                                Make this admin Super Admin
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
                        <p class="text-muted mb-0">Select one or more roles for this admin.</p>
                    </div>
                </div>

                <div class="row">
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
                                        {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
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
                    <i class="bi bi-check-circle me-1"></i> Save Admin
                </button>

                <a href="{{ route('admin.admins.index') }}" class="gm-btn-light text-decoration-none">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>
@endsection