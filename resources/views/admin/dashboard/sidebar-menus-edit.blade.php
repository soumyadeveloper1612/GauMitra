@extends('admin.layouts.app')

@section('title', 'Edit Sidebar Menu')
@section('header_title', 'Edit Sidebar Menu')
@section('header_subtitle', 'Update sidebar menu details')

@section('content')
<div class="page-card">
    <div class="mb-4">
        <h3 class="page-title">Edit Sidebar Menu</h3>
        <p class="page-subtitle">Update this sidebar menu item.</p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger rounded-4">{{ session('error') }}</div>
    @endif

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
        @include('admin.dashboard._forms')
    </form>
</div>
@endsection