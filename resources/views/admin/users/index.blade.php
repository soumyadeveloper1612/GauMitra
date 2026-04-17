@extends('admin.layouts.app')

@section('title', 'Users - GauMitra Admin')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <div class="container-fluid">
        <div class="user-hero-box mb-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <span class="hero-badge">User Management</span>
                    <h2 class="hero-title mb-1">Users Directory</h2>
                    <p class="hero-text mb-0">
                        Manage users, track mobile verification, filter account status, export records,
                        and preview addresses in a modern admin view.
                    </p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.users.export', request()->query()) }}" class="btn btn-success btn-action">
                        <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-dark btn-action">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reset
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="summary-card">
                    <div>
                        <h6>Total Users</h6>
                        <h3>{{ $stats['totalUsers'] }}</h3>
                        <p>All registered accounts</p>
                    </div>
                    <div class="summary-icon bg-soft-primary text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="summary-card">
                    <div>
                        <h6>Active Users</h6>
                        <h3>{{ $stats['activeUsers'] }}</h3>
                        <p>Currently active accounts</p>
                    </div>
                    <div class="summary-icon bg-soft-success text-success">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="summary-card">
                    <div>
                        <h6>Verified Users</h6>
                        <h3>{{ $stats['verifiedUsers'] }}</h3>
                        <p>Mobile verified records</p>
                    </div>
                    <div class="summary-icon bg-soft-info text-info">
                        <i class="bi bi-patch-check-fill"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="summary-card">
                    <div>
                        <h6>Filtered Result</h6>
                        <h3>{{ $stats['filteredUsers'] }}</h3>
                        <p>Rows matching current filters</p>
                    </div>
                    <div class="summary-icon bg-soft-warning text-warning">
                        <i class="bi bi-funnel-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-panel mb-4">
            <form method="GET" action="{{ route('admin.users.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-4">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control custom-input"
                            placeholder="Search by name, mobile, status" value="{{ request('search') }}">
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label">User Status</label>
                        <select name="status_filter" class="form-select custom-input">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status_filter') == 'active' ? 'selected' : '' }}>Active
                            </option>
                            <option value="inactive" {{ request('status_filter') == 'inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label">Verification</label>
                        <select name="verified_filter" class="form-select custom-input">
                            <option value="">All Verification</option>
                            <option value="verified" {{ request('verified_filter') == 'verified' ? 'selected' : '' }}>
                                Verified</option>
                            <option value="not_verified"
                                {{ request('verified_filter') == 'not_verified' ? 'selected' : '' }}>Not Verified</option>
                        </select>
                    </div>

                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-dark w-100 btn-action">
                            <i class="bi bi-search me-1"></i> Apply
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-panel">
            <div class="table-responsive">
                <table id="usersTable" class="table align-middle user-table nowrap w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User Info</th>
                            <th>Mobile</th>
                            <th>Status</th>
                            <th>Verification</th>
                            <th>Latest Address</th>
                            <th>Address Count</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $key => $user)
                            <tr>
                                <td>{{ $key + 1 }}</td>

                                <td>
                                    <div class="user-info-cell">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $user->name ?: 'N/A' }}</div>
                                            <small class="text-muted">User ID: {{ $user->id }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $user->mobile ?: 'N/A' }}</div>
                                </td>

                                <td>
                                    @php $status = strtolower($user->status ?? 'inactive'); @endphp

                                    @if ($status === 'active')
                                        <span class="badge custom-badge bg-success-subtle text-success">Active</span>
                                    @elseif($status === 'inactive')
                                        <span class="badge custom-badge bg-secondary-subtle text-secondary">Inactive</span>
                                    @else
                                        <span
                                            class="badge custom-badge bg-warning-subtle text-warning">{{ ucfirst($user->status ?? 'Unknown') }}</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($user->mobile_verified_at)
                                        <span class="badge custom-badge bg-info-subtle text-info">Verified</span>
                                        <div class="small text-muted mt-1">
                                            {{ $user->mobile_verified_at->format('d M Y') }}
                                        </div>
                                    @else
                                        <span class="badge custom-badge bg-danger-subtle text-danger">Not Verified</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($user->latestAddress)
                                        <div class="address-preview">
                                            <div class="fw-semibold text-dark">
                                                {{ $user->latestAddress->city ?: ($user->latestAddress->district ?: 'Address Available') }}
                                            </div>
                                            <div class="small text-muted text-truncate address-truncate">
                                                {{ $user->latestAddress->full_address ?: 'No full address' }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">No address</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="count-pill">{{ $user->addresses_count }}</span>
                                </td>

                                <td>
                                    @if ($user->last_login_at)
                                        <div class="fw-semibold">{{ $user->last_login_at->format('d M Y') }}</div>
                                        <small class="text-muted">{{ $user->last_login_at->format('h:i A') }}</small>
                                    @else
                                        <span class="text-muted">Never</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary action-btn view-address-btn"
                                            data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}">
                                            <i class="bi bi-geo-alt-fill me-1"></i> Address
                                        </button>

                                        <a href="{{ route('admin.users.show', $user->id) }}"
                                            class="btn btn-sm btn-dark action-btn">
                                            <i class="bi bi-eye-fill me-1"></i> View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-people display-6 d-block mb-2"></i>
                                        No users found.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content address-modal-content">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h4 class="modal-title fw-bold" id="addressModalLabel">User Address Details</h4>
                        <p class="text-muted mb-0" id="addressModalSubTitle">Loading address information...</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body pt-3">
                    <div id="addressModalBody">
                        <div class="text-center py-5">
                            <div class="spinner-border text-warning" role="status"></div>
                            <p class="text-muted mt-3 mb-0">Fetching address records...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .user-hero-box {
            background: linear-gradient(135deg, #fff7ed, #ffffff);
            border: 1px solid #fed7aa;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        }

        .hero-badge {
            display: inline-block;
            padding: 6px 12px;
            background: #fff1e6;
            border: 1px solid #fed7aa;
            color: #ea580c;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .hero-title {
            font-weight: 800;
            color: #111827;
        }

        .hero-text {
            color: #6b7280;
            max-width: 760px;
        }

        .btn-action {
            border-radius: 14px;
            padding: 10px 18px;
            font-weight: 600;
        }

        .summary-card {
            background: #fff;
            border: 1px solid #ececec;
            border-radius: 22px;
            padding: 20px;
            min-height: 120px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .summary-card h6 {
            color: #6b7280;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .summary-card h3 {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 4px;
            color: #111827;
        }

        .summary-card p {
            margin-bottom: 0;
            color: #9ca3af;
            font-size: 13px;
        }

        .summary-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .bg-soft-primary {
            background: rgba(59, 130, 246, 0.12);
        }

        .bg-soft-success {
            background: rgba(34, 197, 94, 0.12);
        }

        .bg-soft-info {
            background: rgba(6, 182, 212, 0.12);
        }

        .bg-soft-warning {
            background: rgba(245, 158, 11, 0.12);
        }

        .filter-panel {
            background: #fff;
            border: 1px solid #ececec;
            border-radius: 22px;
            padding: 20px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
        }

        .custom-input {
            min-height: 48px;
            border-radius: 14px;
            border: 1px solid #dbe2ea;
            box-shadow: none !important;
        }

        .table-panel {
            background: #fff;
            border: 1px solid #ececec;
            border-radius: 22px;
            padding: 20px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
        }

        .user-table thead th {
            font-size: 13px;
            font-weight: 700;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
            padding: 16px 14px;
            white-space: nowrap;
        }

        .user-table tbody td {
            padding: 16px 14px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .user-table tbody tr:hover {
            background: #fffaf5;
            transition: 0.25s ease;
        }

        .user-info-cell {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 220px;
        }

        .user-avatar {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f97316, #fb923c);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 16px;
            box-shadow: 0 8px 18px rgba(249, 115, 22, 0.22);
            flex-shrink: 0;
        }

        .custom-badge {
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .address-preview {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 10px 12px;
            min-width: 220px;
        }

        .address-truncate {
            max-width: 240px;
        }

        .count-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 12px;
            border-radius: 999px;
            font-weight: 700;
            color: #111827;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
        }

        .action-btn {
            border-radius: 12px;
            font-weight: 600;
            padding: 8px 14px;
        }

        .address-modal-content {
            border-radius: 24px;
            border: none;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.15);
        }

        .address-item {
            background: linear-gradient(180deg, #ffffff, #fffaf5);
            border: 1px solid #ececec;
            border-radius: 20px;
            padding: 18px;
            margin-bottom: 16px;
        }

        .address-item-title {
            font-weight: 800;
            margin-bottom: 14px;
            color: #111827;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .address-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
        }

        .address-field {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 12px;
        }

        .address-field label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .address-field div {
            color: #111827;
            font-weight: 600;
            word-break: break-word;
        }

        .empty-address-box {
            text-align: center;
            padding: 50px 20px;
            border: 1px dashed #cbd5e1;
            border-radius: 20px;
            background: #f8fafc;
            color: #64748b;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 10px !important;
            margin: 0 2px;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#usersTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'All']
                ],
                order: [
                    [0, 'asc']
                ],
                searching: false,
                columnDefs: [{
                    orderable: false,
                    targets: [8]
                }]
            });

            $(document).on('click', '.view-address-btn', function() {
                const userId = $(this).data('user-id');
                const userName = $(this).data('user-name');
                const modal = new bootstrap.Modal(document.getElementById('addressModal'));

                $('#addressModalLabel').text('User Address Details');
                $('#addressModalSubTitle').text('Loading address details for ' + (userName || 'user') +
                    '...');
                $('#addressModalBody').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-warning" role="status"></div>
                    <p class="text-muted mt-3 mb-0">Fetching address records...</p>
                </div>
            `);
                modal.show();

                let url = "{{ route('admin.users.addresses', ':id') }}".replace(':id', userId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#addressModalSubTitle').text(
                            (response.user.name || 'N/A') + ' • ' + (response.user.mobile ||
                                'No mobile')
                        );

                        if (!response.addresses || response.addresses.length === 0) {
                            $('#addressModalBody').html(`
                            <div class="empty-address-box">
                                <i class="bi bi-geo-alt display-6 d-block mb-2"></i>
                                <h5 class="mb-2">No address found</h5>
                                <p class="mb-0">This user has not added any address yet.</p>
                            </div>
                        `);
                            return;
                        }

                        let html = '';

                        response.addresses.forEach(function(address, index) {
                            html += `
                            <div class="address-item">
                                <div class="address-item-title">
                                    <span><i class="bi bi-geo-alt-fill me-2 text-warning"></i>Address ${index + 1}</span>
                                    <small class="text-muted">${address.created_at ?? ''}</small>
                                </div>

                                <div class="address-grid">
                                    <div class="address-field">
                                        <label>Full Address</label>
                                        <div>${address.full_address ?? 'N/A'}</div>
                                    </div>

                                    <div class="address-field">
                                        <label>Street</label>
                                        <div>${address.street ?? 'N/A'}</div>
                                    </div>

                                    <div class="address-field">
                                        <label>Village</label>
                                        <div>${address.village ?? 'N/A'}</div>
                                    </div>

                                    <div class="address-field">
                                        <label>Police Station</label>
                                        <div>${address.police_station ?? 'N/A'}</div>
                                    </div>

                                    <div class="address-field">
                                        <label>City</label>
                                        <div>${address.city ?? 'N/A'}</div>
                                    </div>

                                    <div class="address-field">
                                        <label>District</label>
                                        <div>${address.district ?? 'N/A'}</div>
                                    </div>

                                    <div class="address-field">
                                        <label>State</label>
                                        <div>${address.state ?? 'N/A'}</div>
                                    </div>

                                    <div class="address-field">
                                        <label>Pincode</label>
                                        <div>${address.pincode ?? 'N/A'}</div>
                                    </div>

                                    <div class="address-field">
                                        <label>Area Name</label>
                                        <div>${address.area_name ?? 'N/A'}</div>
                                    </div>

                                    <div class="address-field">
                                        <label>Latitude</label>
                                        <div>${address.latitude ?? 'N/A'}</div>
                                    </div>

                                    <div class="address-field">
                                        <label>Longitude</label>
                                        <div>${address.longitude ?? 'N/A'}</div>
                                    </div>

                                    <div class="address-field">
                                        <label>Google Place ID</label>
                                        <div>${address.google_place_id ?? 'N/A'}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                        });

                        $('#addressModalBody').html(html);
                    },
                    error: function() {
                        $('#addressModalBody').html(`
                        <div class="empty-address-box">
                            <i class="bi bi-exclamation-triangle display-6 d-block mb-2 text-danger"></i>
                            <h5 class="mb-2">Unable to load address</h5>
                            <p class="mb-0">Something went wrong while fetching user addresses.</p>
                        </div>
                    `);
                    }
                });
            });
        });
    </script>
@endsection
