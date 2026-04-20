@extends('admin.layouts.app')

@section('title', 'Super Admin Dashboard - GauMitra')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="mb-1 fw-bold">Super Admin Dashboard</h2>
            <p class="text-muted mb-0">Welcome back, {{ session('admin_name') }}</p>
        </div>
        <span class="badge bg-danger px-3 py-2 rounded-pill">Super Admin Access</span>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Admins</h6>
                    <h3 class="fw-bold mb-1">{{ $totalAdmins }}</h3>
                    <small class="text-success">Active: {{ $activeAdmins }}</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Users</h6>
                    <h3 class="fw-bold mb-1">{{ $totalUsers }}</h3>
                    <small class="text-secondary">Application users</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Gaushalas</h6>
                    <h3 class="fw-bold mb-1">{{ $totalGaushalas }}</h3>
                    <small class="text-secondary">Registered gaushalas</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Cases</h6>
                    <h3 class="fw-bold mb-1">{{ $totalCases }}</h3>
                    <small class="text-secondary">Emergency cases</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">News & Notices</h6>
                    <h3 class="fw-bold mb-1">{{ $totalNewsNotices }}</h3>
                    <small class="text-secondary">Published notices</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Roles</h6>
                    <h3 class="fw-bold mb-1">{{ $totalRoles }}</h3>
                    <small class="text-secondary">Available roles</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Permissions</h6>
                    <h3 class="fw-bold mb-1">{{ $totalPermissions }}</h3>
                    <small class="text-secondary">System permissions</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 mt-4">
        <div class="card-body">
            <h5 class="mb-3 fw-bold">Super Admin Controls</h5>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.admins.index') }}" class="btn btn-primary rounded-pill px-4">
                    Manage Admins
                </a>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                    Manage Roles & Permissions
                </a>
            </div>
        </div>
    </div>
</div>
@endsection