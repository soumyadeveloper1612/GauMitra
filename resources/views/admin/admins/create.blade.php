@extends('admin.layouts.app')

@section('title', 'Create Admin')
@section('header_title', 'Create Admin')
@section('header_subtitle', 'Add a new admin and assign roles')

@section('content')
<div class="page-card">
    <div class="mb-4">
        <h3 class="page-title">New Admin</h3>
        <p class="page-subtitle">Fill in the admin details and choose roles.</p>
    </div>

    <form action="{{ route('admin.admins.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Admin Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">User ID</label>
                <input type="text" name="user_id" class="form-control" value="{{ old('user_id') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            @if(admin_user()?->is_super_admin)
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_super_admin" value="1" id="is_super_admin">
                    <label class="form-check-label fw-semibold" for="is_super_admin">Make this admin Super Admin</label>
                </div>
            </div>
            @endif

            <div class="col-12">
                <label class="form-label fw-semibold mb-3">Assign Roles</label>
                <div class="row">
                    @foreach($roles as $role)
                        <div class="col-md-4 mb-3">
                            <div class="border rounded-4 p-3 h-100">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}">
                                    <label class="form-check-label fw-semibold" for="role_{{ $role->id }}">
                                        {{ $role->label }}
                                    </label>
                                </div>
                                <small class="text-muted">{{ $role->description }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-12 d-flex gap-2">
                <button class="btn btn-success rounded-pill px-4">Save Admin</button>
                <a href="{{ route('admin.admins.index') }}" class="btn btn-light rounded-pill px-4">Back</a>
            </div>
        </div>
    </form>
</div>
@endsection