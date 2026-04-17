@extends('admin.layouts.app')

@section('title', 'Dashboard - GauMitra Admin')

@section('content')
    @php
        $adminHealth = $totalAdmins > 0 ? round(($activeAdmins / $totalAdmins) * 100) : 0;
    @endphp

    <style>
        .hero-panel {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #fff7ed 0%, #ffffff 45%, #eff6ff 100%);
            border: 1px solid #fde7c7;
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
            margin-bottom: 24px;
        }

        .hero-panel::before {
            content: "";
            position: absolute;
            right: -80px;
            top: -80px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(249, 115, 22, 0.08);
        }

        .hero-panel::after {
            content: "";
            position: absolute;
            left: -70px;
            bottom: -70px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(37, 99, 235, 0.06);
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .quick-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            color: #ea580c;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid #fed7aa;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .hero-title {
            font-weight: 800;
            font-size: 30px;
            color: #111827;
            margin-bottom: 10px;
        }

        .hero-text {
            color: #6b7280;
            margin-bottom: 20px;
            max-width: 760px;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .hero-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 18px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 700;
            transition: .25s ease;
        }

        .hero-btn-primary {
            background: linear-gradient(135deg, #f97316, #fb923c);
            color: #fff;
            box-shadow: 0 10px 20px rgba(249, 115, 22, 0.22);
        }

        .hero-btn-primary:hover {
            color: #fff;
            transform: translateY(-2px);
        }

        .hero-btn-secondary {
            background: #fff;
            color: #1f2937;
            border: 1px solid #e5e7eb;
        }

        .hero-btn-secondary:hover {
            color: #111827;
            transform: translateY(-2px);
        }

        .stat-link {
            text-decoration: none;
            color: inherit;
            display: block;
            height: 100%;
        }

        .stat-card {
            background: #fff;
            border-radius: 22px;
            padding: 22px;
            height: 100%;
            border: 1px solid #eef2f7;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            transition: .3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.08);
        }

        .stat-flex {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }

        .stat-card h6 {
            font-size: 13px;
            font-weight: 700;
            color: #6b7280;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .stat-card h3 {
            font-size: 30px;
            font-weight: 800;
            margin-bottom: 5px;
            color: #111827;
        }

        .stat-card p {
            margin: 0;
            color: #9ca3af;
            font-size: 13px;
        }

        .icon-badge {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #fff;
            flex-shrink: 0;
        }

        .bg-orange { background: linear-gradient(135deg, #f97316, #fb923c); }
        .bg-blue { background: linear-gradient(135deg, #2563eb, #60a5fa); }
        .bg-green { background: linear-gradient(135deg, #059669, #34d399); }
        .bg-purple { background: linear-gradient(135deg, #7c3aed, #a78bfa); }
        .bg-red { background: linear-gradient(135deg, #dc2626, #fb7185); }
        .bg-dark { background: linear-gradient(135deg, #0f172a, #334155); }

        .section-card {
            background: #fff;
            border-radius: 24px;
            border: 1px solid #eef2f7;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            padding: 24px;
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 18px;
        }

        .breakdown-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 13px 0;
            border-bottom: 1px dashed #e5e7eb;
        }

        .breakdown-row:last-child {
            border-bottom: none;
        }

        .breakdown-label {
            font-weight: 600;
            color: #374151;
        }

        .breakdown-value {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            min-width: 42px;
            padding: 6px 12px;
            text-align: center;
            border-radius: 999px;
            font-weight: 700;
            color: #111827;
        }

        .mini-action-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .mini-action-card {
            display: block;
            text-decoration: none;
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 18px;
            transition: .25s ease;
            color: inherit;
        }

        .mini-action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 26px rgba(15, 23, 42, 0.07);
        }

        .mini-action-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            color: #fff;
            font-size: 20px;
        }

        .mini-action-card h6 {
            margin: 0 0 6px;
            font-weight: 800;
            color: #111827;
        }

        .mini-action-card p {
            margin: 0;
            font-size: 13px;
            color: #6b7280;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .custom-table th {
            background: #f8fafc;
            color: #475569;
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
        }

        .custom-table td {
            vertical-align: middle;
            font-size: 14px;
        }

        .badge-status,
        .badge-severity {
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            display: inline-block;
            text-transform: capitalize;
        }

        .status-reported,
        .status-alerted {
            background: #fff7ed;
            color: #c2410c;
        }

        .status-accepted,
        .status-en_route,
        .status-reached_site,
        .status-rescue_in_progress,
        .status-needs_backup,
        .status-treatment_started,
        .status-shifted_to_gaushala,
        .status-escalated {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .status-resolved,
        .status-closed {
            background: #ecfdf5;
            color: #047857;
        }

        .status-false_report,
        .status-cancelled,
        .status-duplicate_case,
        .status-unable_to_locate {
            background: #fef2f2;
            color: #b91c1c;
        }

        .severity-low {
            background: #f3f4f6;
            color: #374151;
        }

        .severity-medium {
            background: #fef3c7;
            color: #92400e;
        }

        .severity-high {
            background: #fee2e2;
            color: #b91c1c;
        }

        .severity-critical {
            background: #111827;
            color: #fff;
        }

        .empty-box {
            text-align: center;
            padding: 35px 20px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 18px;
            color: #64748b;
        }

        @media (max-width: 767px) {
            .hero-title {
                font-size: 24px;
            }

            .mini-action-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="hero-panel">
        <div class="hero-content">
            <div class="quick-pill">
                <i class="bi bi-stars"></i> Smart Control Center
            </div>

            <div class="hero-title">Welcome to GauMitra Admin Dashboard</div>
            <p class="hero-text">
                Monitor users, admins, emergency rescue activities, and registered gaushalas from one clean and
                professional panel. The dashboard is now upgraded with dedicated Gaushala management access.
            </p>

            <div class="hero-actions">
                <a href="{{ route('admin.gaushalas.create') }}" class="hero-btn hero-btn-primary">
                    <i class="bi bi-plus-circle-fill"></i> Create Gaushala
                </a>

                <a href="{{ route('admin.gaushalas.index') }}" class="hero-btn hero-btn-secondary">
                    <i class="bi bi-house-heart-fill"></i> Manage Gaushala
                </a>

                <a href="{{ route('admin.report-cases.index') }}" class="hero-btn hero-btn-secondary">
                    <i class="bi bi-clipboard2-pulse-fill"></i> Manage Cases
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.users.index') }}" class="stat-link">
                <div class="stat-card">
                    <div class="stat-flex">
                        <div>
                            <h6>Total Users</h6>
                            <h3>{{ $totalUsers }}</h3>
                            <p>Registered application users</p>
                        </div>
                        <div class="icon-badge bg-orange">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-flex">
                    <div>
                        <h6>Total Admins</h6>
                        <h3>{{ $totalAdmins }}</h3>
                        <p>Admin accounts created</p>
                    </div>
                    <div class="icon-badge bg-blue">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-flex">
                    <div>
                        <h6>Active Admins</h6>
                        <h3>{{ $activeAdmins }}</h3>
                        <p>Currently active admin users</p>
                    </div>
                    <div class="icon-badge bg-green">
                        <i class="bi bi-shield-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.gaushalas.index') }}" class="stat-link">
                <div class="stat-card">
                    <div class="stat-flex">
                        <div>
                            <h6>Total Gaushalas</h6>
                            <h3>{{ $totalGaushalas }}</h3>
                            <p>Registered gaushala records</p>
                        </div>
                        <div class="icon-badge bg-purple">
                            <i class="bi bi-house-heart-fill"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.report-cases.index') }}" class="stat-link">
                <div class="stat-card">
                    <div class="stat-flex">
                        <div>
                            <h6>Total Reports</h6>
                            <h3>{{ $totalReports }}</h3>
                            <p>All emergency cases</p>
                        </div>
                        <div class="icon-badge bg-dark">
                            <i class="bi bi-file-earmark-medical-fill"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.report-cases.index') }}" class="stat-link">
                <div class="stat-card">
                    <div class="stat-flex">
                        <div>
                            <h6>Pending Reports</h6>
                            <h3>{{ $pendingReports }}</h3>
                            <p>Waiting for quick response</p>
                        </div>
                        <div class="icon-badge bg-orange">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.report-cases.index') }}" class="stat-link">
                <div class="stat-card">
                    <div class="stat-flex">
                        <div>
                            <h6>Critical Cases</h6>
                            <h3>{{ $criticalReports }}</h3>
                            <p>High priority active cases</p>
                        </div>
                        <div class="icon-badge bg-red">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-flex">
                    <div>
                        <h6>Resolved Today</h6>
                        <h3>{{ $resolvedToday }}</h3>
                        <p>Cases completed today</p>
                    </div>
                    <div class="icon-badge bg-green">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="section-card">
                <div class="section-title">System Overview</div>

                <div class="breakdown-row">
                    <span class="breakdown-label">Active Sessions</span>
                    <span class="breakdown-value">{{ $activeSessions }}</span>
                </div>
                <div class="breakdown-row">
                    <span class="breakdown-label">Registered Gaushalas</span>
                    <span class="breakdown-value">{{ $totalGaushalas }}</span>
                </div>
                <div class="breakdown-row">
                    <span class="breakdown-label">Active Rescue Cases</span>
                    <span class="breakdown-value">{{ $activeRescueReports }}</span>
                </div>
                <div class="breakdown-row">
                    <span class="breakdown-label">False Reports</span>
                    <span class="breakdown-value">{{ $falseReports }}</span>
                </div>
                <div class="breakdown-row">
                    <span class="breakdown-label">Admin Health</span>
                    <span class="breakdown-value">{{ $adminHealth }}%</span>
                </div>
            </div>

            <div class="section-card">
                <div class="section-title">Quick Access</div>

                <div class="mini-action-grid">
                    <a href="{{ route('admin.gaushalas.create') }}" class="mini-action-card">
                        <div class="mini-action-icon bg-orange">
                            <i class="bi bi-plus-circle-fill"></i>
                        </div>
                        <h6>Create Gaushala</h6>
                        <p>Add a new gaushala registration quickly.</p>
                    </a>

                    <a href="{{ route('admin.gaushalas.index') }}" class="mini-action-card">
                        <div class="mini-action-icon bg-purple">
                            <i class="bi bi-house-heart-fill"></i>
                        </div>
                        <h6>Manage Gaushala</h6>
                        <p>View and manage all gaushala records.</p>
                    </a>

                    <a href="{{ route('admin.users.index') }}" class="mini-action-card">
                        <div class="mini-action-icon bg-blue">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h6>Manage Users</h6>
                        <p>Open user listing and account management.</p>
                    </a>

                    <a href="{{ route('admin.report-cases.index') }}" class="mini-action-card">
                        <div class="mini-action-icon bg-red">
                            <i class="bi bi-clipboard2-pulse-fill"></i>
                        </div>
                        <h6>Emergency Cases</h6>
                        <p>Track rescue and report case activities.</p>
                    </a>
                </div>
            </div>

            <div class="section-card">
                <div class="section-title">Case Type Breakdown</div>

                @forelse($caseTypeStats as $item)
                    <div class="breakdown-row">
                        <span class="breakdown-label">{{ ucwords(str_replace('_', ' ', $item->case_type)) }}</span>
                        <span class="breakdown-value">{{ $item->total }}</span>
                    </div>
                @empty
                    <div class="empty-box">No emergency case data available yet.</div>
                @endforelse
            </div>
        </div>

        <div class="col-lg-8">
            <div class="section-card">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div class="section-title mb-0">Recent Emergency Reports</div>
                    <a href="{{ route('admin.report-cases.index') }}" class="btn btn-dark rounded-pill px-4">
                        <i class="bi bi-arrow-right-circle me-1"></i> Manage All Cases
                    </a>
                </div>

                <div class="table-wrap">
                    <table class="table custom-table align-middle">
                        <thead>
                            <tr>
                                <th>Case ID</th>
                                <th>Type</th>
                                <th>Severity</th>
                                <th>Reporter</th>
                                <th>Handler</th>
                                <th>District</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentReports as $report)
                                <tr>
                                    <td class="fw-bold">{{ $report->case_uid ?? 'EC-' . $report->id }}</td>
                                    <td>{{ ucwords(str_replace('_', ' ', $report->case_type)) }}</td>
                                    <td>
                                        <span class="badge-severity severity-{{ $report->severity }}">
                                            {{ ucfirst($report->severity) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $report->reporter_name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $report->reporter_mobile ?? '-' }}</small>
                                    </td>
                                    <td>{{ $report->handler_name ?? 'Not Assigned' }}</td>
                                    <td>{{ $report->district ?? '-' }}</td>
                                    <td>
                                        <span class="badge-status status-{{ $report->status }}">
                                            {{ ucwords(str_replace('_', ' ', $report->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($report->created_at)->format('d M Y, h:i A') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.report-cases.show', $report->id) }}"
                                           class="btn btn-sm btn-outline-dark rounded-pill">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">
                                        <div class="empty-box">No report cases found.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="section-card">
                <div class="section-title">Status Breakdown</div>
                <div class="row g-3">
                    @forelse($statusStats as $item)
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center p-3 rounded-4"
                                 style="background:#f8fafc; border:1px solid #e5e7eb;">
                                <span class="fw-semibold text-dark">
                                    {{ ucwords(str_replace('_', ' ', $item->status)) }}
                                </span>
                                <span class="badge-status status-{{ $item->status }}">{{ $item->total }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="empty-box">No status data available.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection