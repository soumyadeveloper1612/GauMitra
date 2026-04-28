<div class="row g-4">
    <div class="col-md-6">
        <label class="gm-form-label">Menu Title <span class="text-danger">*</span></label>
        <div class="d-flex gap-2 align-items-center">
            <span class="gm-input-icon">
                <i class="bi bi-type"></i>
            </span>

            <input type="text"
                   name="title"
                   class="form-control gm-form-control"
                   value="{{ old('title', $sidebarMenu->title ?? '') }}"
                   placeholder="Example: Manage Admins"
                   required>
        </div>
    </div>

    <div class="col-md-6">
        <label class="gm-form-label">Slug</label>
        <div class="d-flex gap-2 align-items-center">
            <span class="gm-input-icon">
                <i class="bi bi-link-45deg"></i>
            </span>

            <input type="text"
                   name="slug"
                   class="form-control gm-form-control"
                   value="{{ old('slug', $sidebarMenu->slug ?? '') }}"
                   placeholder="example: sidebar-menus">
        </div>
        <small class="gm-small-help ms-5">Leave blank to auto-generate from title.</small>
    </div>

    <div class="col-md-6">
        <label class="gm-form-label">Parent Menu</label>
        <div class="d-flex gap-2 align-items-center">
            <span class="gm-input-icon">
                <i class="bi bi-diagram-3"></i>
            </span>

            <select name="parent_id" class="form-select gm-form-select">
                <option value="">Parent Menu</option>
                @foreach($parents as $parent)
                    <option value="{{ $parent->id }}"
                        {{ old('parent_id', $sidebarMenu->parent_id ?? '') == $parent->id ? 'selected' : '' }}>
                        {{ $parent->title }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-6">
        <label class="gm-form-label">Icon Class</label>
        <div class="d-flex gap-2 align-items-center">
            <span class="gm-input-icon">
                <i class="bi bi-stars"></i>
            </span>

            <input type="text"
                   name="icon"
                   class="form-control gm-form-control"
                   value="{{ old('icon', $sidebarMenu->icon ?? '') }}"
                   placeholder="bi bi-people-fill">
        </div>
        <small class="gm-small-help ms-5">Use Bootstrap Icon class. Example: bi bi-grid-fill</small>
    </div>

    <div class="col-md-6">
        <label class="gm-form-label">Admin Route Name</label>
        <div class="d-flex gap-2 align-items-center">
            <span class="gm-input-icon">
                <i class="bi bi-signpost"></i>
            </span>

            <input type="text"
                   name="route_name"
                   class="form-control gm-form-control"
                   value="{{ old('route_name', $sidebarMenu->route_name ?? '') }}"
                   placeholder="admin.sidebar-menus.index">
        </div>
    </div>

    <div class="col-md-6">
        <label class="gm-form-label">Super Admin Route Name</label>
        <div class="d-flex gap-2 align-items-center">
            <span class="gm-input-icon">
                <i class="bi bi-shield-lock"></i>
            </span>

            <input type="text"
                   name="super_admin_route_name"
                   class="form-control gm-form-control"
                   value="{{ old('super_admin_route_name', $sidebarMenu->super_admin_route_name ?? '') }}"
                   placeholder="admin.sidebar-menus.index">
        </div>
    </div>

    <div class="col-md-6">
        <label class="gm-form-label">Active Pattern</label>
        <div class="d-flex gap-2 align-items-center">
            <span class="gm-input-icon">
                <i class="bi bi-bullseye"></i>
            </span>

            <input type="text"
                   name="active_pattern"
                   class="form-control gm-form-control"
                   value="{{ old('active_pattern', $sidebarMenu->active_pattern ?? '') }}"
                   placeholder="admin.sidebar-menus.*">
        </div>
        <small class="gm-small-help ms-5">Example: admin.users.* or admin.sidebar-menus.*</small>
    </div>

    <div class="col-md-6">
        <label class="gm-form-label">Custom URL</label>
        <div class="d-flex gap-2 align-items-center">
            <span class="gm-input-icon">
                <i class="bi bi-globe"></i>
            </span>

            <input type="text"
                   name="custom_url"
                   class="form-control gm-form-control"
                   value="{{ old('custom_url', $sidebarMenu->custom_url ?? '') }}"
                   placeholder="https://example.com or /custom-url">
        </div>
    </div>

    <div class="col-md-6">
        <label class="gm-form-label">Permission Name</label>
        <div class="d-flex gap-2 align-items-center">
            <span class="gm-input-icon">
                <i class="bi bi-key"></i>
            </span>

            <select name="permission_name" class="form-select gm-form-select">
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
    </div>

    <div class="col-md-3">
        <label class="gm-form-label">Sort Order</label>
        <div class="d-flex gap-2 align-items-center">
            <span class="gm-input-icon">
                <i class="bi bi-sort-numeric-down"></i>
            </span>

            <input type="number"
                   name="sort_order"
                   class="form-control gm-form-control"
                   value="{{ old('sort_order', $sidebarMenu->sort_order ?? 0) }}">
        </div>
    </div>

    <div class="col-md-3">
        <label class="gm-form-label">Status <span class="text-danger">*</span></label>
        <div class="d-flex gap-2 align-items-center">
            <span class="gm-input-icon">
                <i class="bi bi-toggle-on"></i>
            </span>

            <select name="status" class="form-select gm-form-select" required>
                <option value="active" {{ old('status', $sidebarMenu->status ?? 'active') === 'active' ? 'selected' : '' }}>
                    Active
                </option>

                <option value="inactive" {{ old('status', $sidebarMenu->status ?? '') === 'inactive' ? 'selected' : '' }}>
                    Inactive
                </option>
            </select>
        </div>
    </div>

    <div class="col-12 d-flex flex-wrap gap-2 pt-2">
        <button type="submit" class="gm-btn-primary">
            <i class="bi bi-check-circle me-1"></i> Save Menu
        </button>

        <a href="{{ route('admin.sidebar-menus.index') }}" class="gm-btn-light text-decoration-none">
            Back
        </a>
    </div>
</div>