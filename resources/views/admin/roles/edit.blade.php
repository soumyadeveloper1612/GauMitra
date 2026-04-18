@extends('admin.layouts.app')

@section('title', 'Edit Role')
@section('header_title', 'Edit Role')
@section('header_subtitle', 'Update role details and permissions')

@section('content')
<div class="page-card">
    <div class="mb-4">
        <h3 class="page-title">Edit Role</h3>
        <p class="page-subtitle">Modify permission mapping for this role.</p>
    </div>

    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Role Label</label>
                <input type="text" name="label" class="form-control" value="{{ old('label', $role->label) }}">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Role Code</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $role->name) }}">
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $role->description) }}</textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="active" {{ old('status', $role->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $role->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold mb-3">Permissions</label>
                @foreach($permissions as $module => $modulePermissions)
                    <div class="border rounded-4 p-3 mb-3">
                        <h6 class="fw-bold mb-3">{{ $module ?: 'General' }}</h6>
                        <div class="row">
                            @foreach($modulePermissions as $permission)
                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="permissions[]"
                                               value="{{ $permission->id }}"
                                               id="permission_{{ $permission->id }}"
                                               {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                            {{ $permission->label }}
                                            <br>
                                            <small class="text-muted">{{ $permission->name }}</small>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="col-12 d-flex gap-2">
                <button class="btn btn-success rounded-pill px-4">Update Role</button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-light rounded-pill px-4">Back</a>
            </div>
        </div>
    </form>
</div>
@endsection