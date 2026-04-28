@extends('admin.layouts.app')

@section('title', 'Assign Menu Access')
@section('header_title', 'Assign Menu Access')
@section('header_subtitle', 'Select sidebar menus for this admin')

@section('content')
@include('admin.admins.partials.admin-theme')

<style>
    .gm-access-profile {
        background: linear-gradient(135deg, #fffdfa, #fff3db);
        border: 1px solid rgba(93, 47, 18, 0.14);
        border-radius: 24px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
    }

    .gm-access-avatar {
        width: 64px;
        height: 64px;
        border-radius: 22px;
        background: linear-gradient(135deg, #f57c00, #d35400);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 900;
        box-shadow: 0 14px 28px rgba(211, 84, 0, 0.26);
        flex: 0 0 auto;
    }

    .gm-menu-card {
        height: 100%;
        background: linear-gradient(180deg, #fffdfa, #fff5e5);
        border: 1px solid rgba(93, 47, 18, 0.14);
        border-radius: 24px;
        padding: 20px;
        transition: all 0.22s ease;
    }

    .gm-menu-card:hover {
        transform: translateY(-3px);
        border-color: rgba(245, 124, 0, 0.45);
        box-shadow: 0 14px 30px rgba(93, 47, 18, 0.12);
    }

    .gm-parent-menu {
        padding-bottom: 12px;
        border-bottom: 1px dashed rgba(93, 47, 18, 0.18);
        margin-bottom: 14px;
    }

    .gm-child-menu-box {
        background: rgba(255, 243, 219, 0.72);
        border: 1px solid rgba(245, 124, 0, 0.13);
        border-radius: 18px;
        padding: 14px;
    }

    .gm-menu-card .form-check-input {
        border-color: rgba(93, 47, 18, 0.42);
        cursor: pointer;
    }

    .gm-menu-card .form-check-input:checked {
        background-color: #f57c00;
        border-color: #f57c00;
    }

    .gm-menu-title {
        color: #5d2f12;
        font-weight: 800;
    }

    .gm-menu-child-title {
        color: #3b1d0c;
        font-weight: 700;
    }

    .gm-info-box {
        border-radius: 24px;
        padding: 22px;
        background: linear-gradient(135deg, rgba(245, 124, 0, 0.12), rgba(93, 47, 18, 0.08));
        border: 1px solid rgba(245, 124, 0, 0.22);
        color: #5d2f12;
    }
</style>

<div class="gm-page-hero">
    <div class="gm-hero-kicker">
        <i class="bi bi-sliders"></i>
        GauMitra Menu Permission
    </div>
    <h3 class="gm-hero-title">Assign Menu Access</h3>
    <p class="gm-hero-subtitle">
        Select sidebar menus for this admin. These menus will be visible after login based on assigned access.
    </p>
</div>

<div class="gm-card">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h3 class="gm-card-title">Assign Menus</h3>
            <p class="gm-card-subtitle">
                Manage sidebar access for selected admin.
            </p>
        </div>

        <a href="{{ route('admin.menu-access.index') }}" class="gm-btn-light text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="gm-access-profile">
        <span class="gm-access-avatar">
            {{ strtoupper(substr($admin->name, 0, 1)) }}
        </span>

        <div>
            <h5 class="mb-1 fw-bold" style="color: var(--gm-brown);">
                {{ $admin->name }}
            </h5>

            <div class="text-muted fw-semibold">
                <i class="bi bi-person-vcard me-1"></i> {{ $admin->user_id }}
            </div>

            <div class="mt-2">
                @if($admin->is_super_admin)
                    <span class="gm-badge-dark">
                        <i class="bi bi-shield-lock me-1"></i> Super Admin
                    </span>
                @else
                    <span class="gm-badge-role">
                        <i class="bi bi-person-badge me-1"></i> Admin
                    </span>
                @endif
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert gm-alert fw-semibold">
            <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success rounded-4 fw-semibold">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif

    @if($admin->is_super_admin)
        <div class="gm-info-box">
            <div class="d-flex gap-3 align-items-start">
                <div class="gm-input-icon">
                    <i class="bi bi-infinity"></i>
                </div>

                <div>
                    <h5 class="fw-bold mb-1">Super Admin Has Full Access</h5>
                    <p class="mb-0">
                        This admin automatically gets all active sidebar menus. Manual menu assignment is not required.
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('admin.menu-access.index') }}" class="gm-btn-light text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Back to Menu Access
            </a>
        </div>
    @else
        <form action="{{ route('admin.menu-access.update', $admin->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                <div>
                    <label class="gm-form-label mb-1">Sidebar Menus</label>
                    <p class="text-muted mb-0">Choose parent and child menus for this admin.</p>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" id="checkAllMenus">
                        <i class="bi bi-check2-square me-1"></i> Select All
                    </button>

                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" id="uncheckAllMenus">
                        <i class="bi bi-x-square me-1"></i> Clear
                    </button>
                </div>
            </div>

            <div class="row g-4">
                @forelse($menus as $menu)
                    <div class="col-md-6">
                        <div class="gm-menu-card">
                            <div class="form-check gm-parent-menu">
                                <input
                                    class="form-check-input parent-menu-check"
                                    type="checkbox"
                                    name="menu_ids[]"
                                    value="{{ $menu->id }}"
                                    id="menu_{{ $menu->id }}"
                                    data-parent-id="{{ $menu->id }}"
                                    {{ in_array($menu->id, $selectedMenuIds) ? 'checked' : '' }}
                                >

                                <label class="form-check-label gm-menu-title" for="menu_{{ $menu->id }}">
                                    <i class="{{ $menu->icon ?? 'bi bi-grid' }} text-warning me-1"></i>
                                    {{ $menu->title }}
                                </label>
                            </div>

                            @if($menu->children->count())
                                <div class="gm-child-menu-box">
                                    @foreach($menu->children as $child)
                                        <div class="form-check mb-2">
                                            <input
                                                class="form-check-input child-menu-check child-of-{{ $menu->id }}"
                                                type="checkbox"
                                                name="menu_ids[]"
                                                value="{{ $child->id }}"
                                                id="menu_{{ $child->id }}"
                                                data-parent-id="{{ $menu->id }}"
                                                {{ in_array($child->id, $selectedMenuIds) ? 'checked' : '' }}
                                            >

                                            <label class="form-check-label gm-menu-child-title" for="menu_{{ $child->id }}">
                                                <i class="{{ $child->icon ?? 'bi bi-dot' }} text-warning me-1"></i>
                                                {{ $child->title }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i> No child menu available.
                                </small>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="gm-empty text-center">
                            <i class="bi bi-grid-1x2 fs-2 d-block mb-2"></i>
                            No sidebar menus found.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-4 d-flex flex-wrap gap-2">
                <button type="submit" class="gm-btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Save Menu Access
                </button>

                <a href="{{ route('admin.menu-access.index') }}" class="gm-btn-light text-decoration-none">
                    Cancel
                </a>
            </div>
        </form>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkAllBtn = document.getElementById('checkAllMenus');
        const uncheckAllBtn = document.getElementById('uncheckAllMenus');
        const allChecks = document.querySelectorAll('input[name="menu_ids[]"]');

        if (checkAllBtn) {
            checkAllBtn.addEventListener('click', function () {
                allChecks.forEach(function (checkbox) {
                    checkbox.checked = true;
                });
            });
        }

        if (uncheckAllBtn) {
            uncheckAllBtn.addEventListener('click', function () {
                allChecks.forEach(function (checkbox) {
                    checkbox.checked = false;
                });
            });
        }

        document.querySelectorAll('.parent-menu-check').forEach(function (parentCheck) {
            parentCheck.addEventListener('change', function () {
                const parentId = this.getAttribute('data-parent-id');
                const children = document.querySelectorAll('.child-of-' + parentId);

                children.forEach(function (child) {
                    child.checked = parentCheck.checked;
                });
            });
        });

        document.querySelectorAll('.child-menu-check').forEach(function (childCheck) {
            childCheck.addEventListener('change', function () {
                const parentId = this.getAttribute('data-parent-id');
                const parent = document.querySelector('.parent-menu-check[data-parent-id="' + parentId + '"]');
                const children = document.querySelectorAll('.child-of-' + parentId);

                if (!parent) {
                    return;
                }

                const anyChildChecked = Array.from(children).some(function (child) {
                    return child.checked;
                });

                parent.checked = anyChildChecked;
            });
        });
    });
</script>
@endpush