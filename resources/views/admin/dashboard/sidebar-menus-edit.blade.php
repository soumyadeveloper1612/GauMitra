@extends('admin.layouts.app')

@section('title', 'Edit Sidebar Menu')
@section('header_title', 'Edit Sidebar Menu')
@section('header_subtitle', 'Update sidebar menu item')

@section('content')
@include('admin.dashboard.sidebar-menu-theme')

<div class="gm-page-hero">
    <div class="gm-hero-kicker">
        <i class="bi bi-pencil-square"></i>
        GauMitra Sidebar Update
    </div>
    <h3 class="gm-hero-title">Edit Sidebar Menu</h3>
    <p class="gm-hero-subtitle">
        Update parent or child sidebar menu details, route, permission, icon, order, and status.
    </p>
</div>

<div class="gm-card">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h3 class="gm-card-title">Edit Sidebar Menu</h3>
            <p class="gm-card-subtitle">Update parent or child sidebar menu.</p>
        </div>

        <a href="{{ route('admin.sidebar-menus.index') }}" class="gm-btn-light text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert gm-alert mb-4">
            <strong><i class="bi bi-exclamation-triangle me-1"></i> Please fix these errors:</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.sidebar-menus.update', $sidebarMenu->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="gm-section-box">
            @include('admin.dashboard._forms', ['sidebarMenu' => $sidebarMenu])
        </div>
    </form>
</div>
@endsection