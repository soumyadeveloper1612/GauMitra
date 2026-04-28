@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="show-page-header mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-house-heart-fill me-2"></i>Gaushala Details
            </h2>
            <p class="mb-0">View complete gaushala registration, members, services and documents.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.gaushalas.index') }}" class="btn btn-light header-btn">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>

            <button type="button" class="btn btn-print" onclick="window.print()">
                <i class="bi bi-printer-fill me-2"></i>Print
            </button>
        </div>
    </div>

    <div class="row g-4">

        {{-- Left Profile Card --}}
        <div class="col-lg-4">
            <div class="profile-card">
                <div class="profile-image-box">
                    @if($gaushala->gaushala_photo)
                        <img src="{{ asset('storage/' . $gaushala->gaushala_photo) }}"
                             alt="Gaushala Photo"
                             class="profile-image">
                    @else
                        <div class="profile-no-image">
                            <i class="bi bi-house-heart-fill"></i>
                            <span>No Photo</span>
                        </div>
                    @endif
                </div>

                <div class="text-center mt-3">
                    <h4 class="mb-1">{{ $gaushala->gaushala_name }}</h4>
                    <p class="text-muted mb-2">{{ $gaushala->owner_manager_name }}</p>

                    @if($gaushala->status == 'active')
                        <span class="status-badge status-active">Active</span>
                    @elseif($gaushala->status == 'inactive')
                        <span class="status-badge status-inactive">Inactive</span>
                    @else
                        <span class="status-badge status-deleted">Deleted</span>
                    @endif
                </div>

                <div class="quick-info mt-4">
                    <div class="quick-info-item">
                        <span class="quick-icon">
                            <i class="bi bi-telephone-fill"></i>
                        </span>
                        <div>
                            <small>Mobile Number</small>
                            <strong>{{ $gaushala->mobile_number }}</strong>
                        </div>
                    </div>

                    <div class="quick-info-item">
                        <span class="quick-icon">
                            <i class="bi bi-phone-fill"></i>
                        </span>
                        <div>
                            <small>Alternate Number</small>
                            <strong>{{ $gaushala->alternate_number ?: 'N/A' }}</strong>
                        </div>
                    </div>

                    <div class="quick-info-item">
                        <span class="quick-icon">
                            <i class="bi bi-clock-fill"></i>
                        </span>
                        <div>
                            <small>Working Hours</small>
                            <strong>{{ $gaushala->working_hours ?: 'N/A' }}</strong>
                        </div>
                    </div>

                    <div class="quick-info-item">
                        <span class="quick-icon">
                            <i class="bi bi-calendar-check-fill"></i>
                        </span>
                        <div>
                            <small>Registered On</small>
                            <strong>{{ $gaushala->created_at ? $gaushala->created_at->format('d M Y') : 'N/A' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Details --}}
        <div class="col-lg-8">

            {{-- Basic Details --}}
            <div class="detail-card mb-4">
                <div class="detail-card-header">
                    <i class="bi bi-info-circle-fill"></i>
                    <h5>Basic Information</h5>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-box">
                            <small>Gaushala Name</small>
                            <strong>{{ $gaushala->gaushala_name }}</strong>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-box">
                            <small>Owner / Manager Name</small>
                            <strong>{{ $gaushala->owner_manager_name }}</strong>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="info-box">
                            <small>Full Address</small>
                            <strong>{{ $gaushala->full_address }}</strong>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-box">
                            <small>District</small>
                            <strong>{{ $gaushala->district }}</strong>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-box">
                            <small>State</small>
                            <strong>{{ $gaushala->state }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Capacity --}}
            <div class="detail-card mb-4">
                <div class="detail-card-header">
                    <i class="bi bi-box-seam-fill"></i>
                    <h5>Capacity Details</h5>
                </div>

                <div class="capacity-grid">
                    <div class="capacity-box">
                        <span>Total Capacity</span>
                        <strong>{{ $gaushala->total_capacity }}</strong>
                    </div>

                    <div class="capacity-box available">
                        <span>Available Capacity</span>
                        <strong>{{ $gaushala->available_capacity }}</strong>
                    </div>

                    <div class="capacity-box occupied">
                        <span>Occupied</span>
                        <strong>{{ max(($gaushala->total_capacity ?? 0) - ($gaushala->available_capacity ?? 0), 0) }}</strong>
                    </div>
                </div>
            </div>

            {{-- Services --}}
            <div class="detail-card mb-4">
                <div class="detail-card-header">
                    <i class="bi bi-shield-check"></i>
                    <h5>Services Available</h5>
                </div>

                <div class="service-grid">
                    <div class="service-box {{ $gaushala->rescue_vehicle ? 'enabled' : 'disabled' }}">
                        <i class="bi bi-truck-front-fill"></i>
                        <span>Rescue Vehicle</span>
                        <strong>{{ $gaushala->rescue_vehicle ? 'Available' : 'Not Available' }}</strong>
                    </div>

                    <div class="service-box {{ $gaushala->doctor ? 'enabled' : 'disabled' }}">
                        <i class="bi bi-heart-pulse-fill"></i>
                        <span>Doctor</span>
                        <strong>{{ $gaushala->doctor ? 'Available' : 'Not Available' }}</strong>
                    </div>

                    <div class="service-box {{ $gaushala->food_support ? 'enabled' : 'disabled' }}">
                        <i class="bi bi-basket-fill"></i>
                        <span>Food Support</span>
                        <strong>{{ $gaushala->food_support ? 'Available' : 'Not Available' }}</strong>
                    </div>

                    <div class="service-box {{ $gaushala->temporary_shelter ? 'enabled' : 'disabled' }}">
                        <i class="bi bi-house-check-fill"></i>
                        <span>Temporary Shelter</span>
                        <strong>{{ $gaushala->temporary_shelter ? 'Available' : 'Not Available' }}</strong>
                    </div>
                </div>
            </div>

            {{-- Emergency --}}
            <div class="detail-card mb-4">
                <div class="detail-card-header">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <h5>Emergency Availability</h5>
                </div>

                @if($gaushala->emergency_availability === 'yes')
                    <div class="emergency-box yes">
                        <i class="bi bi-check-circle-fill"></i>
                        <div>
                            <strong>Emergency Support Available</strong>
                            <p class="mb-0">This gaushala is available for emergency rescue support.</p>
                        </div>
                    </div>
                @else
                    <div class="emergency-box no">
                        <i class="bi bi-x-circle-fill"></i>
                        <div>
                            <strong>Emergency Support Not Available</strong>
                            <p class="mb-0">This gaushala is not available for emergency rescue support.</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Members --}}
            <div class="detail-card mb-4">
                <div class="detail-card-header">
                    <i class="bi bi-people-fill"></i>
                    <h5>Gaushala Members</h5>
                </div>

                <div class="member-list">
                    @forelse($gaushala->members as $member)
                        <div class="member-card">
                            <div class="member-avatar">
                                {{ strtoupper(substr($member->member_name, 0, 1)) }}
                            </div>

                            <div class="member-info">
                                <strong>{{ $member->member_name }}</strong>
                                <span>
                                    <i class="bi bi-telephone-fill me-1"></i>
                                    {{ $member->member_phone }}
                                </span>
                            </div>

                            @if($member->status == 'active')
                                <span class="member-status active">Active</span>
                            @else
                                <span class="member-status inactive">Inactive</span>
                            @endif
                        </div>
                    @empty
                        <div class="empty-box">
                            <i class="bi bi-info-circle"></i>
                            No members added.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Documents --}}
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="bi bi-file-earmark-text-fill"></i>
                    <h5>Documents</h5>
                </div>

                <div class="document-grid">
                    <div class="document-box">
                        <div>
                            <i class="bi bi-image-fill"></i>
                            <strong>Gaushala Photo</strong>
                            <small>Uploaded image file</small>
                        </div>

                        @if($gaushala->gaushala_photo)
                            <a href="{{ asset('storage/' . $gaushala->gaushala_photo) }}"
                               target="_blank"
                               class="btn btn-sm btn-doc">
                                View
                            </a>
                        @else
                            <span class="text-muted">Not Uploaded</span>
                        @endif
                    </div>

                    <div class="document-box">
                        <div>
                            <i class="bi bi-file-earmark-check-fill"></i>
                            <strong>Registration Proof</strong>
                            <small>Proof / ID document</small>
                        </div>

                        @if($gaushala->registration_proof)
                            <a href="{{ asset('storage/' . $gaushala->registration_proof) }}"
                               target="_blank"
                               class="btn btn-sm btn-doc">
                                View
                            </a>
                        @else
                            <span class="text-muted">Not Uploaded</span>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .show-page-header {
        background: linear-gradient(135deg, #78350f, #d97706);
        border-radius: 22px;
        padding: 24px 28px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 12px 35px rgba(146, 64, 14, 0.18);
    }

    .show-page-header h2 {
        font-weight: 800;
        margin: 0;
    }

    .header-btn,
    .btn-print {
        border-radius: 14px;
        font-weight: 700;
        padding: 10px 16px;
    }

    .btn-print {
        background: #0f172a;
        color: #fff;
        border: none;
    }

    .btn-print:hover {
        background: #1e293b;
        color: #fff;
    }

    .profile-card,
    .detail-card {
        background: #fffaf0;
        border: 1px solid #fde68a;
        border-radius: 22px;
        padding: 22px;
        box-shadow: 0 12px 35px rgba(146, 64, 14, 0.08);
    }

    .profile-image-box {
        width: 100%;
        height: 260px;
        border-radius: 20px;
        overflow: hidden;
        background: #fff7ed;
        border: 1px solid #fed7aa;
    }

    .profile-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-no-image {
        width: 100%;
        height: 100%;
        display: flex;
        gap: 8px;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #92400e;
        font-weight: 800;
    }

    .profile-no-image i {
        font-size: 48px;
    }

    .status-badge {
        padding: 8px 16px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .status-active {
        background: #dcfce7;
        color: #166534;
    }

    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-deleted {
        background: #f3f4f6;
        color: #374151;
    }

    .quick-info {
        display: grid;
        gap: 14px;
    }

    .quick-info-item {
        background: #fff;
        border: 1px solid #fed7aa;
        border-radius: 16px;
        padding: 14px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .quick-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: linear-gradient(135deg, #92400e, #d97706);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .quick-info-item small,
    .info-box small {
        display: block;
        color: #9a3412;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .quick-info-item strong,
    .info-box strong {
        color: #1f2937;
        font-weight: 800;
    }

    .detail-card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #78350f;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px dashed #fbbf24;
    }

    .detail-card-header h5 {
        margin: 0;
        font-weight: 800;
    }

    .info-box {
        background: #fff;
        border: 1px solid #fed7aa;
        border-radius: 16px;
        padding: 16px;
        height: 100%;
    }

    .capacity-grid,
    .service-grid,
    .document-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 16px;
    }

    .capacity-box {
        background: #fff;
        border: 1px solid #fed7aa;
        border-radius: 18px;
        padding: 18px;
        text-align: center;
    }

    .capacity-box span {
        display: block;
        color: #9a3412;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .capacity-box strong {
        font-size: 32px;
        color: #78350f;
        font-weight: 900;
    }

    .capacity-box.available strong {
        color: #166534;
    }

    .capacity-box.occupied strong {
        color: #b91c1c;
    }

    .service-box {
        background: #fff;
        border-radius: 18px;
        padding: 18px;
        border: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .service-box i {
        font-size: 28px;
    }

    .service-box span {
        font-weight: 800;
    }

    .service-box strong {
        font-size: 13px;
    }

    .service-box.enabled {
        border-color: #bbf7d0;
        background: #f0fdf4;
        color: #166534;
    }

    .service-box.disabled {
        border-color: #fecaca;
        background: #fef2f2;
        color: #991b1b;
    }

    .emergency-box {
        border-radius: 18px;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .emergency-box i {
        font-size: 32px;
    }

    .emergency-box.yes {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .emergency-box.no {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .member-list {
        display: grid;
        gap: 12px;
    }

    .member-card {
        background: #fff;
        border: 1px solid #fed7aa;
        border-radius: 18px;
        padding: 14px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .member-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, #92400e, #d97706);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
    }

    .member-info {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .member-info strong {
        color: #1f2937;
        font-weight: 800;
    }

    .member-info span {
        color: #6b7280;
        font-size: 13px;
    }

    .member-status {
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 800;
    }

    .member-status.active {
        background: #dcfce7;
        color: #166534;
    }

    .member-status.inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .document-box {
        background: #fff;
        border: 1px solid #fed7aa;
        border-radius: 18px;
        padding: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .document-box i {
        color: #d97706;
        font-size: 26px;
        display: block;
        margin-bottom: 5px;
    }

    .document-box strong {
        display: block;
        color: #1f2937;
        font-weight: 800;
    }

    .document-box small {
        color: #6b7280;
    }

    .btn-doc {
        background: #78350f;
        color: #fff;
        border-radius: 999px;
        padding: 7px 14px;
        font-weight: 700;
    }

    .btn-doc:hover {
        background: #92400e;
        color: #fff;
    }

    .empty-box {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        padding: 16px;
        color: #64748b;
        font-weight: 700;
    }

    @media print {
        .sidebar,
        .top-header,
        .header-btn,
        .btn-print {
            display: none !important;
        }

        .container-fluid {
            padding: 0 !important;
        }

        .profile-card,
        .detail-card,
        .show-page-header {
            box-shadow: none !important;
        }
    }
</style>
@endsection