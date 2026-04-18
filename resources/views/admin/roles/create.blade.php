@extends('admin.layouts.app')

@section('title', 'Create Role')
@section('header_title', 'Create Role')
@section('header_subtitle', 'Create a role and map permissions')

@section('content')
<div class="page-card">
    <div class="mb-4">
        <h3 class="page-title">New Role</h3>
        <p class="page-subtitle">Choose which permissions belong to this role.</p>
    </div>

    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Role Label</label>
                <input type="text" name="label" class="form-control" value="{{ old('label') }}" placeholder="Example: Content Manager">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Role Code</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Example: content_manager">
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
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
                                               id="permission_{{ $permission->id }}">
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
                <button class="btn btn-success rounded-pill px-4">Save Role</button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-light rounded-pill px-4">Back</a>
            </div>
        </div>
    </form>
</div>
@endsection