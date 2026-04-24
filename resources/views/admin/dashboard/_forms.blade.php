<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Menu Title <span class="text-danger">*</span></label>
        <input type="text"
               name="title"
               class="form-control"
               value="{{ old('title', $sidebarMenu->title ?? '') }}"
               required>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Slug</label>
        <input type="text"
               name="slug"
               class="form-control"
               value="{{ old('slug', $sidebarMenu->slug ?? '') }}"
               placeholder="example: sidebar-menus">
        <small class="text-muted">Leave blank to auto-generate from title.</small>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Parent Menu</label>
        <select name="parent_id" class="form-select">
            <option value="">Parent Menu</option>
            @foreach($parents as $parent)
                <option value="{{ $parent->id }}"
                    {{ old('parent_id', $sidebarMenu->parent_id ?? '') == $parent->id ? 'selected' : '' }}>
                    {{ $parent->title }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Icon Class</label>
        <input type="text"
               name="icon"
               class="form-control"
               value="{{ old('icon', $sidebarMenu->icon ?? '') }}"
               placeholder="bi bi-people-fill">
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Admin Route Name</label>
        <input type="text"
               name="route_name"
               class="form-control"
               value="{{ old('route_name', $sidebarMenu->route_name ?? '') }}"
               placeholder="admin.sidebar-menus.index">
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Super Admin Route Name</label>
        <input type="text"
               name="super_admin_route_name"
               class="form-control"
               value="{{ old('super_admin_route_name', $sidebarMenu->super_admin_route_name ?? '') }}"
               placeholder="admin.sidebar-menus.index">
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Active Pattern</label>
        <input type="text"
               name="active_pattern"
               class="form-control"
               value="{{ old('active_pattern', $sidebarMenu->active_pattern ?? '') }}"
               placeholder="admin.sidebar-menus.*">
        <small class="text-muted">Example: admin.users.* or admin.sidebar-menus.*</small>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Custom URL</label>
        <input type="text"
               name="custom_url"
               class="form-control"
               value="{{ old('custom_url', $sidebarMenu->custom_url ?? '') }}"
               placeholder="https://example.com or /custom-url">
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Permission Name</label>
        <select name="permission_name" class="form-select">
            <option value="">No Permission Required</option>
            @foreach($permissions as $module => $items)
                <optgroup label="{{ $module }}">
                    @foreach($items as $permission)
                        <option value="{{ $permission->name }}"
                            {{ old('permission_name', $sidebarMenu->permission_name ?? '') == $permission->name ? 'selected' : '' }}>
                            {{ $permission->label }} — {{ $permission->name }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label fw-semibold">Sort Order</label>
        <input type="number"
               name="sort_order"
               class="form-control"
               value="{{ old('sort_order', $sidebarMenu->sort_order ?? 0) }}">
    </div>

    <div class="col-md-3">
        <label class="form-label fw-semibold">Status</label>
        <select name="status" class="form-select" required>
            <option value="active" {{ old('status', $sidebarMenu->status ?? 'active') === 'active' ? 'selected' : '' }}>
                Active
            </option>
            <option value="inactive" {{ old('status', $sidebarMenu->status ?? '') === 'inactive' ? 'selected' : '' }}>
                Inactive
            </option>
        </select>
    </div>

    <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-success rounded-pill px-4">
            <i class="bi bi-check-circle me-1"></i> Save Menu
        </button>

        <a href="{{ route('admin.sidebar-menus.index') }}" class="btn btn-light rounded-pill px-4">
            Back
        </a>
    </div>
</div>