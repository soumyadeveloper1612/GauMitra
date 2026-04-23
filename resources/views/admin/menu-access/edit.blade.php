@extends('admin.layouts.app')

@section('title', 'Assign Menu Access')
@section('header_title', 'Assign Menu Access')
@section('header_subtitle', 'Select sidebar menus for this admin')

@section('content')
<div class="page-card">
    <div class="mb-4">
        <h3 class="page-title">Assign Menus</h3>
        <p class="page-subtitle">
            Admin: <strong>{{ $admin->name }}</strong> ({{ $admin->user_id }})
        </p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger rounded-4">{{ session('error') }}</div>
    @endif

    @if($admin->is_super_admin)
        <div class="alert alert-info rounded-4">
            Super Admin automatically gets all active menus. No assignment needed.
        </div>

        <a href="{{ route('admin.menu-access.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            Back
        </a>
    @else
        <form action="{{ route('admin.menu-access.update', $admin->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-4">
                @foreach($menus as $menu)
                    <div class="col-md-6">
                        <div class="border rounded-4 p-3 h-100">
                            <div class="form-check mb-2">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="menu_ids[]"
                                    value="{{ $menu->id }}"
                                    id="menu_{{ $menu->id }}"
                                    {{ in_array($menu->id, $selectedMenuIds) ? 'checked' : '' }}
                                >
                                <label class="form-check-label fw-semibold" for="menu_{{ $menu->id }}">
                                    {{ $menu->title }}
                                </label>
                            </div>

                            @if($menu->children->count())
                                <div class="ms-3 mt-3">
                                    @foreach($menu->children as $child)
                                        <div class="form-check mb-2">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                name="menu_ids[]"
                                                value="{{ $child->id }}"
                                                id="menu_{{ $child->id }}"
                                                {{ in_array($child->id, $selectedMenuIds) ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label" for="menu_{{ $child->id }}">
                                                {{ $child->title }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    Save Menu Access
                </button>

                <a href="{{ route('admin.menu-access.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    Cancel
                </a>
            </div>
        </form>
    @endif
</div>
@endsection