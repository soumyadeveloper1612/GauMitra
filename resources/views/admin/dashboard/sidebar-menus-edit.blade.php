@extends('admin.layouts.app')

@section('title', 'Edit Sidebar Menu')
@section('header_title', 'Edit Sidebar Menu')
@section('header_subtitle', 'Update sidebar menu item')

@section('content')
<div class="page-card">
    <div class="mb-4">
        <h3 class="page-title">Edit Sidebar Menu</h3>
        <p class="page-subtitle">Update parent or child sidebar menu.</p>
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

    <form action="{{ route('admin.sidebar-menus.update', $sidebarMenu->id) }}" method="POST">
        @csrf
        @method('PUT')

        @include('admin.dashboard._forms', ['sidebarMenu' => $sidebarMenu])
    </form>
</div>
@endsection