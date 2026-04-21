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

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Role Label <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="label"
                    id="label"
                    class="form-control @error('label') is-invalid @enderror"
                    value="{{ old('label') }}"
                    placeholder="Example: Content Manager"
                    required
                >
                @error('label')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Role Code <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}"
                    placeholder="Example: content_manager"
                    required
                >
                <small class="text-muted">Use unique code like: content_manager, report_manager</small>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Description</label>
                <textarea
                    name="description"
                    class="form-control @error('description') is-invalid @enderror"
                    rows="3"
                >{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold mb-3">Permissions</label>

                @php
                    $oldPermissions = old('permissions', []);
                @endphp

                @forelse($permissions as $module => $modulePermissions)
                    <div class="border rounded-4 p-3 mb-3">
                        <h6 class="fw-bold mb-3">{{ $module ?: 'General' }}</h6>
                        <div class="row">
                            @foreach($modulePermissions as $permission)
                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="permissions[]"
                                            value="{{ $permission->id }}"
                                            id="permission_{{ $permission->id }}"
                                            {{ in_array($permission->id, $oldPermissions) ? 'checked' : '' }}
                                        >
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
                @empty
                    <div class="alert alert-warning mb-0">
                        No active permissions found. Please create permissions first.
                    </div>
                @endforelse
            </div>

            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-success rounded-pill px-4">Save Role</button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-light rounded-pill px-4">Back</a>
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('label').addEventListener('keyup', function () {
    const label = this.value.trim().toLowerCase();
    const slug = label
        .replace(/[^a-z0-9\s]/g, '')
        .replace(/\s+/g, '_');

    const nameField = document.getElementById('name');
    if (!nameField.value) {
        nameField.value = slug;
    }
});
</script>
@endsection