@extends('admin.layouts.app')

@section('title', 'Create Sidebar Menu')
@section('header_title', 'Create Sidebar Menu')
@section('header_subtitle', 'Add a new sidebar menu item')

@section('content')
<div class="page-card">
    <div class="mb-4">
        <h3 class="page-title">New Sidebar Menu</h3>
        <p class="page-subtitle">Create parent or child sidebar menu.</p>
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

    <form action="{{ route('admin.sidebar-menus.store') }}" method="POST">
        @csrf
        @include('admin.dashboard._forms', ['sidebarMenu' => null])
    </form>
</div>
@endsection