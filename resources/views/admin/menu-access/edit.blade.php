@extends('admin.layouts.app')

@section('title', 'Assign Menu Access')
@section('header_title', 'Assign Menu Access')
@section('header_subtitle', 'Choose sidebar menus for this admin')

@section('content')
<div class="page-card">
    <div class="mb-4">
        <h3 class="page-title">Assign Menu Access</h3>
        <p class="page-subtitle">
            Admin: <strong>{{ $admin->name }}</strong>
            ({{ $admin->is_super_admin ? 'Super Admin' : 'Admin' }})
        </p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-4">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.menu-access.update', $admin->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            @forelse($menus as $menu)
                <div class="col-md-6 mb-4">
                    <div class="border rounded-4 p-3 h-100">
                        <div class="form-check mb-3">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                name="menu_ids[]"
                                value="{{ $menu->id }}"
                                id="menu_{{ $menu->id }}"
                                {{ in_array($menu->id, $assignedMenuIds) ? 'checked' : '' }}
                            >
                            <label class="form-check-label fw-bold" for="menu_{{ $menu->id }}">
                                {{ $menu->title }}
                            </label>
                        </div>

                        @if($menu->children->count())
                            <div class="ps-3 border-start">
                                @foreach($menu->children as $child)
                                    <div class="form-check mb-2">
                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            name="menu_ids[]"
                                            value="{{ $child->id }}"
                                            id="menu_{{ $child->id }}"
                                            {{ in_array($child->id, $assignedMenuIds) ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="menu_{{ $child->id }}">
                                            {{ $child->title }}
                                            <br>
                                            <small class="text-muted">{{ $child->route_name ?? '-' }}</small>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning rounded-4 mb-0">
                        No sidebar menus found. Please create sidebar menus first.
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-3 d-flex gap-2">
            <button type="submit" class="btn btn-success rounded-pill px-4">
                <i class="bi bi-check-circle me-1"></i> Save Menu Access
            </button>
            <a href="{{ route('admin.menu-access.index') }}" class="btn btn-light rounded-pill px-4">Back</a>
        </div>
    </form>
</div>
@endsection