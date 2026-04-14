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
                            <th>Photo</th>
                            <th>Gaushala Details</th>
                            <th>Contact</th>
                            <th>Capacity</th>
                            <th>Services</th>
                            <th>Emergency</th>
                            <th>Proof</th>
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
                                    @if($gaushala->gaushala_photo)
                                        <img src="{{ asset('storage/' . $gaushala->gaushala_photo) }}" alt="Gaushala Photo" class="gaushala-thumb">
                                    @else
                                        <div class="no-image">No Image</div>
                                    @endif
                                </td>

                                <td>
                                    <div class="fw-bold">{{ $gaushala->gaushala_name }}</div>
                                    <div class="small text-muted mb-1">{{ $gaushala->owner_manager_name }}</div>
                                    <small class="text-muted d-block">{{ \Illuminate\Support\Str::limit($gaushala->full_address, 60) }}</small>
                                    <small class="text-muted d-block">{{ $gaushala->district }}, {{ $gaushala->state }}</small>
                                    <small class="text-muted d-block">Hours: {{ $gaushala->working_hours ?: 'N/A' }}</small>
                                </td>

                                <td>
                                    <div>{{ $gaushala->mobile_number }}</div>
                                    <small class="text-muted">{{ $gaushala->alternate_number ?: 'N/A' }}</small>
                                </td>

                                <td>
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                                        {{ $gaushala->available_capacity }}/{{ $gaushala->total_capacity }}
                                    </span>
                                </td>

                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @if($gaushala->rescue_vehicle)
                                            <span class="badge service-badge">Rescue Vehicle</span>
                                        @endif
                                        @if($gaushala->doctor)
                                            <span class="badge service-badge">Doctor</span>
                                        @endif
                                        @if($gaushala->food_support)
                                            <span class="badge service-badge">Food Support</span>
                                        @endif
                                        @if($gaushala->temporary_shelter)
                                            <span class="badge service-badge">Temporary Shelter</span>
                                        @endif

                                        @if(!$gaushala->rescue_vehicle && !$gaushala->doctor && !$gaushala->food_support && !$gaushala->temporary_shelter)
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    @if($gaushala->emergency_availability === 'yes')
                                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Yes</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">No</span>
                                    @endif
                                </td>

                                <td>
                                    @if($gaushala->registration_proof)
                                        <a href="{{ asset('storage/' . $gaushala->registration_proof) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill">
                                            View Proof
                                        </a>
                                    @else
                                        <span class="text-muted">Not Uploaded</span>
                                    @endif
                                </td>

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
                                <td colspan="11" class="text-center py-5">
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

    .gaushala-thumb {
        width: 62px;
        height: 62px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
    }

    .no-image {
        width: 62px;
        height: 62px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #f3f4f6;
        color: #6b7280;
        font-size: 12px;
        text-align: center;
    }

    .service-badge {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
        padding: 7px 10px;
        border-radius: 999px;
        font-weight: 600;
    }
</style>
@endsection