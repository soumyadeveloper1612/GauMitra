@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="page-top-bar mb-4">
        <div>
            <h2 class="mb-1">Gaushala List</h2>
            <p class="mb-0">Manage all registered gaushalas from one place.</p>
        </div>
        <a href="{{ route('admin.gaushalas.create') }}" class="btn btn-add-new">
            <i class="bi bi-plus-circle-fill me-2"></i>Add New Gaushala
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 gaushala-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Gaushala Name</th>
                            <th>Owner / Manager</th>
                            <th>Mobile</th>
                            <th>District</th>
                            <th>State</th>
                            <th>GPS</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gaushalas as $key => $gaushala)
                            <tr>
                                <td>{{ $gaushalas->firstItem() + $key }}</td>
                                <td>
                                    <div class="fw-bold">{{ $gaushala->gaushala_name }}</div>
                                    <small class="text-muted">{{ \Illuminate\Support\Str::limit($gaushala->full_address, 50) }}</small>
                                </td>
                                <td>{{ $gaushala->owner_manager_name }}</td>
                                <td>
                                    <div>{{ $gaushala->mobile_number }}</div>
                                    <small class="text-muted">{{ $gaushala->alternate_number ?: 'N/A' }}</small>
                                </td>
                                <td>{{ $gaushala->district }}</td>
                                <td>{{ $gaushala->state }}</td>
                                <td>
                                    @if($gaushala->latitude && $gaushala->longitude)
                                        <a href="https://www.google.com/maps?q={{ $gaushala->latitude }},{{ $gaushala->longitude }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                                            View Location
                                        </a>
                                    @else
                                        <span class="text-muted">Not Added</span>
                                    @endif
                                </td>
                                <td>
                                    @if($gaushala->status == 'active')
                                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Active</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $gaushala->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">No gaushala records found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($gaushalas->count())
                <div class="p-3">
                    {{ $gaushalas->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .page-top-bar {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        color: #fff;
        border-radius: 20px;
        padding: 24px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15);
    }

    .btn-add-new {
        background: linear-gradient(135deg, #ff7a00, #ff9f43);
        color: #fff;
        border-radius: 14px;
        padding: 12px 18px;
        font-weight: 700;
        border: none;
    }

    .btn-add-new:hover {
        color: #fff;
        opacity: .95;
    }

    .gaushala-table thead th {
        background: #fff7ed;
        color: #7c2d12;
        font-weight: 700;
        border-bottom: 1px solid #fed7aa;
        padding: 16px;
        white-space: nowrap;
    }

    .gaushala-table tbody td {
        padding: 16px;
        vertical-align: middle;
    }

    .gaushala-table tbody tr:hover {
        background: #f8fafc;
    }
</style>
@endsection