@extends('admin.layouts.components.app-layout')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="mb-1">Super Admin Dashboard</h2>
            <p class="text-muted mb-0">Welcome back, {{ session('admin_name') }}</p>
        </div>
        <span class="badge bg-danger px-3 py-2">Super Admin Access</span>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="text-muted">Total Admins</h6>
                    <h3 class="fw-bold">{{ $totalAdmins }}</h3>
                    <small class="text-success">Active: {{ $activeAdmins }}</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="text-muted">Total Users</h6>
                    <h3 class="fw-bold">{{ $totalUsers }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="text-muted">Total Gaushalas</h6>
                    <h3 class="fw-bold">{{ $totalGaushalas }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="text-muted">Total Cases</h6>
                    <h3 class="fw-bold">{{ $totalCases }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="text-muted">News & Notices</h6>
                    <h3 class="fw-bold">{{ $totalNewsNotices }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="text-muted">Roles</h6>
                    <h3 class="fw-bold">{{ $totalRoles }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="text-muted">Permissions</h6>
                    <h3 class="fw-bold">{{ $totalPermissions }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 mt-4">
        <div class="card-body">
            <h5 class="mb-3">Super Admin Controls</h5>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.admins.index') }}" class="btn btn-primary">Manage Admins</a>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-primary">Manage Roles & Permissions</a>
            </div>
        </div>
    </div>
</div>
@endsection