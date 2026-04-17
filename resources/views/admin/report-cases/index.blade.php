@extends('admin.layouts.app')

@section('title', 'Manage Report Cases - GauMitra Admin')

@section('content')
<style>
    :root{
        --rc-primary:#0f172a;
        --rc-primary-soft:#f8fafc;
        --rc-secondary:#334155;
        --rc-muted:#64748b;
        --rc-border:#e2e8f0;
        --rc-card:#ffffff;
        --rc-bg:#f1f5f9;
        --rc-accent:#ea580c;
        --rc-accent-soft:#fff7ed;
        --rc-blue:#2563eb;
        --rc-blue-soft:#eff6ff;
        --rc-green:#059669;
        --rc-green-soft:#ecfdf5;
        --rc-red:#dc2626;
        --rc-red-soft:#fef2f2;
        --rc-shadow:0 10px 30px rgba(15,23,42,.06);
        --rc-shadow-lg:0 18px 45px rgba(15,23,42,.12);
        --rc-radius:22px;
    }

    body{
        background: var(--rc-bg);
    }

    .report-shell{
        padding: 4px 0 12px;
    }

    .page-hero{
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #334155 100%);
        border-radius: 28px;
        padding: 28px;
        color: #fff;
        margin-bottom: 24px;
        box-shadow: var(--rc-shadow-lg);
        position: relative;
        overflow: hidden;
    }

    .page-hero::after{
        content:'';
        position:absolute;
        right:-80px;
        top:-80px;
        width:220px;
        height:220px;
        border-radius:50%;
        background: rgba(255,255,255,.06);
    }

    .page-hero small{
        display:inline-block;
        font-size:12px;
        letter-spacing:.08em;
        text-transform:uppercase;
        color: rgba(255,255,255,.7);
        margin-bottom:8px;
        font-weight:700;
    }

    .page-hero h2{
        margin:0 0 8px;
        font-size:30px;
        font-weight:800;
    }

    .page-hero p{
        margin:0;
        color: rgba(255,255,255,.82);
        max-width: 760px;
        font-size:14px;
    }

    .hero-chip{
        display:inline-flex;
        align-items:center;
        gap:8px;
        background: rgba(255,255,255,.12);
        border:1px solid rgba(255,255,255,.18);
        color:#fff;
        border-radius:999px;
        padding:10px 16px;
        font-size:13px;
        font-weight:700;
        backdrop-filter: blur(6px);
    }

    .summary-link{
        display:block;
        text-decoration:none;
        color:inherit;
        height:100%;
    }

    .summary-card{
        background: var(--rc-card);
        border:1px solid var(--rc-border);
        border-radius: 24px;
        padding: 22px;
        box-shadow: var(--rc-shadow);
        height:100%;
        transition: .25s ease;
        position:relative;
        overflow:hidden;
    }

    .summary-card::before{
        content:"";
        position:absolute;
        inset:0 auto auto 0;
        width:100%;
        height:5px;
    }

    .summary-total::before{ background: linear-gradient(90deg,#0f172a,#475569); }
    .summary-pending::before{ background: linear-gradient(90deg,#ea580c,#fb923c); }
    .summary-active::before{ background: linear-gradient(90deg,#2563eb,#60a5fa); }
    .summary-critical::before{ background: linear-gradient(90deg,#dc2626,#fb7185); }
    .summary-resolved::before{ background: linear-gradient(90deg,#059669,#34d399); }

    .summary-card:hover{
        transform: translateY(-6px);
        box-shadow: 0 18px 36px rgba(15,23,42,.10);
    }

    .summary-card.active-summary{
        border: 2px solid #0f172a;
        box-shadow: 0 16px 36px rgba(15,23,42,.12);
    }

    .summary-head{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:12px;
        margin-bottom:16px;
    }

    .summary-title{
        font-size:12px;
        letter-spacing:.08em;
        text-transform:uppercase;
        color:var(--rc-muted);
        font-weight:800;
        margin-bottom:8px;
    }

    .summary-value{
        font-size:34px;
        line-height:1;
        color:var(--rc-primary);
        font-weight:800;
    }

    .summary-note{
        color:var(--rc-muted);
        font-size:13px;
        margin-top:10px;
    }

    .summary-icon{
        width:50px;
        height:50px;
        border-radius:16px;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:20px;
        flex-shrink:0;
    }

    .summary-total .summary-icon{ background:#e2e8f0; color:#0f172a; }
    .summary-pending .summary-icon{ background:var(--rc-accent-soft); color:var(--rc-accent); }
    .summary-active .summary-icon{ background:var(--rc-blue-soft); color:var(--rc-blue); }
    .summary-critical .summary-icon{ background:var(--rc-red-soft); color:var(--rc-red); }
    .summary-resolved .summary-icon{ background:var(--rc-green-soft); color:var(--rc-green); }

    .panel-card{
        background: var(--rc-card);
        border:1px solid var(--rc-border);
        border-radius: var(--rc-radius);
        box-shadow: var(--rc-shadow);
        margin-bottom: 24px;
    }

    .panel-header{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:16px;
        padding:22px 24px 0;
        flex-wrap:wrap;
    }

    .panel-title{
        margin:0;
        color:var(--rc-primary);
        font-size:20px;
        font-weight:800;
    }

    .panel-subtitle{
        margin:4px 0 0;
        color:var(--rc-muted);
        font-size:13px;
    }

    .panel-body{
        padding:24px;
    }

    .filter-grid .form-label{
        color:var(--rc-secondary);
        font-size:13px;
        font-weight:700;
        margin-bottom:8px;
    }

    .form-control,
    .form-select{
        min-height:48px;
        border-radius:14px;
        border:1px solid #d7e0ea;
        box-shadow:none !important;
        color:#0f172a;
    }

    .form-control:focus,
    .form-select:focus{
        border-color:#94a3b8;
    }

    .primary-btn,
    .secondary-btn{
        min-height:48px;
        border-radius:999px;
        padding:0 22px;
        font-weight:700;
    }

    .primary-btn{
        background:#0f172a;
        color:#fff;
        border:none;
    }

    .primary-btn:hover{
        background:#111827;
        color:#fff;
    }

    .secondary-btn{
        background:#fff;
        color:#334155;
        border:1px solid #cbd5e1;
    }

    .secondary-btn:hover{
        background:#f8fafc;
        color:#0f172a;
    }

    .active-chip{
        display:inline-flex;
        align-items:center;
        gap:8px;
        border-radius:999px;
        padding:8px 14px;
        background:#fff7ed;
        color:#9a3412;
        font-weight:700;
        font-size:13px;
        border:1px solid #fed7aa;
    }

    .table-wrap{
        border-radius:18px;
        overflow:hidden;
    }

    #reportTable{
        margin-bottom:0 !important;
    }

    #reportTable thead th{
        background:#f8fafc;
        color:#475569;
        font-size:12px;
        font-weight:800;
        text-transform:uppercase;
        letter-spacing:.04em;
        border-bottom:1px solid #e2e8f0 !important;
        vertical-align:middle;
        padding:16px 14px;
        white-space:nowrap;
    }

    #reportTable tbody td{
        vertical-align:middle;
        padding:16px 14px;
        border-color:#edf2f7;
    }

    #reportTable tbody tr:hover{
        background:#fcfcfd;
    }

    .case-main-id{
        font-size:15px;
        font-weight:800;
        color:var(--rc-primary);
        margin-bottom:4px;
    }

    .case-main-title{
        font-size:14px;
        font-weight:700;
        color:#1e293b;
        margin-bottom:3px;
    }

    .case-main-meta{
        font-size:12px;
        color:var(--rc-muted);
    }

    .user-block{
        line-height:1.3;
    }

    .user-block .name{
        font-size:14px;
        color:#0f172a;
        font-weight:700;
        margin-bottom:4px;
    }

    .user-block .meta{
        font-size:12px;
        color:var(--rc-muted);
    }

    .badge-status,
    .badge-severity{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        border-radius:999px;
        padding:8px 12px;
        font-size:12px;
        font-weight:800;
        text-transform:capitalize;
        white-space:nowrap;
    }

    .status-reported, .status-alerted{ background:#fff7ed; color:#c2410c; }
    .status-accepted, .status-en_route, .status-reached_site,
    .status-rescue_in_progress, .status-needs_backup,
    .status-treatment_started, .status-shifted_to_gaushala,
    .status-escalated{ background:#eff6ff; color:#1d4ed8; }
    .status-resolved, .status-closed{ background:#ecfdf5; color:#047857; }
    .status-false_report, .status-cancelled, .status-duplicate_case,
    .status-unable_to_locate{ background:#fef2f2; color:#b91c1c; }

    .severity-low{ background:#f1f5f9; color:#475569; }
    .severity-medium{ background:#fef3c7; color:#92400e; }
    .severity-high{ background:#fee2e2; color:#b91c1c; }
    .severity-critical{ background:#111827; color:#fff; }

    .action-stack{
        display:flex;
        justify-content:flex-end;
        flex-wrap:wrap;
        gap:8px;
    }

    .table-action-btn{
        border-radius:999px !important;
        font-size:12px !important;
        font-weight:700 !important;
        padding:7px 14px !important;
    }

    .empty-state{
        padding:36px 18px;
        text-align:center;
        color:var(--rc-muted);
    }

    .empty-state .icon{
        width:62px;
        height:62px;
        margin:0 auto 14px;
        border-radius:50%;
        display:flex;
        align-items:center;
        justify-content:center;
        background:#f8fafc;
        color:#94a3b8;
        font-size:24px;
    }

    .modal-content{
        border:none;
        border-radius:26px;
        box-shadow:0 24px 60px rgba(15,23,42,.18);
    }

    .modal-header{
        padding:22px 24px 0;
    }

    .modal-body{
        padding:20px 24px;
    }

    .modal-footer{
        padding:0 24px 24px;
    }

    .modal-title{
        color:#0f172a;
        font-weight:800;
    }

    .dataTables_wrapper .dataTables_filter input,
    .dataTables_wrapper .dataTables_length select{
        border-radius:12px;
        border:1px solid #d1d5db;
        min-height:40px;
        padding:6px 12px;
        box-shadow:none !important;
    }

    .dt-buttons .btn{
        border-radius:999px !important;
        margin-right:8px;
        padding:7px 14px;
        font-weight:700;
    }

    @media (max-width: 991px){
        .page-hero{
            padding:22px;
        }
        .summary-value{
            font-size:28px;
        }
    }

    @media (max-width: 767px){
        .panel-header,
        .panel-body{
            padding-left:18px;
            padding-right:18px;
        }

        .page-hero h2{
            font-size:24px;
        }

        .action-stack{
            justify-content:flex-start;
        }
    }
</style>

<div class="report-shell">
    @if(session('success'))
        <div class="alert alert-success rounded-4 border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-4 border-0 shadow-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="page-hero">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <small>Emergency case admin panel</small>
                <h2>Manage Report Cases</h2>
                <p>Monitor all reported rescue cases, filter faster, assign responders, and update case progress from one clear dashboard.</p>
            </div>

            @if(!empty($filters['card']))
                <div class="hero-chip">
                    <i class="bi bi-funnel-fill"></i>
                    Active View: {{ ucfirst($filters['card']) }}
                </div>
            @endif
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl">
            <a href="{{ route('admin.report-cases.index') }}" class="summary-link">
                <div class="summary-card summary-total {{ empty($filters['card']) ? 'active-summary' : '' }}">
                    <div class="summary-head">
                        <div>
                            <div class="summary-title">Total Reports</div>
                            <div class="summary-value">{{ $summary['total'] ?? 0 }}</div>
                        </div>
                        <div class="summary-icon"><i class="bi bi-files"></i></div>
                    </div>
                    <div class="summary-note">All case records</div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-xl">
            <a href="{{ route('admin.report-cases.index', array_merge(request()->except('page'), ['card' => 'pending'])) }}" class="summary-link">
                <div class="summary-card summary-pending {{ ($filters['card'] ?? '') === 'pending' ? 'active-summary' : '' }}">
                    <div class="summary-head">
                        <div>
                            <div class="summary-title">Pending</div>
                            <div class="summary-value">{{ $summary['pending'] ?? 0 }}</div>
                        </div>
                        <div class="summary-icon"><i class="bi bi-hourglass-split"></i></div>
                    </div>
                    <div class="summary-note">New or alerted cases</div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-xl">
            <a href="{{ route('admin.report-cases.index', array_merge(request()->except('page'), ['card' => 'active'])) }}" class="summary-link">
                <div class="summary-card summary-active {{ ($filters['card'] ?? '') === 'active' ? 'active-summary' : '' }}">
                    <div class="summary-head">
                        <div>
                            <div class="summary-title">Active Rescue</div>
                            <div class="summary-value">{{ $summary['active'] ?? 0 }}</div>
                        </div>
                        <div class="summary-icon"><i class="bi bi-lightning-charge"></i></div>
                    </div>
                    <div class="summary-note">Cases in live action</div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-xl">
            <a href="{{ route('admin.report-cases.index', array_merge(request()->except('page'), ['card' => 'critical'])) }}" class="summary-link">
                <div class="summary-card summary-critical {{ ($filters['card'] ?? '') === 'critical' ? 'active-summary' : '' }}">
                    <div class="summary-head">
                        <div>
                            <div class="summary-title">Critical</div>
                            <div class="summary-value">{{ $summary['critical'] ?? 0 }}</div>
                        </div>
                        <div class="summary-icon"><i class="bi bi-exclamation-octagon"></i></div>
                    </div>
                    <div class="summary-note">Immediate attention cases</div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-xl">
            <a href="{{ route('admin.report-cases.index', array_merge(request()->except('page'), ['card' => 'resolved'])) }}" class="summary-link">
                <div class="summary-card summary-resolved {{ ($filters['card'] ?? '') === 'resolved' ? 'active-summary' : '' }}">
                    <div class="summary-head">
                        <div>
                            <div class="summary-title">Resolved / Closed</div>
                            <div class="summary-value">{{ $summary['resolved'] ?? 0 }}</div>
                        </div>
                        <div class="summary-icon"><i class="bi bi-check2-circle"></i></div>
                    </div>
                    <div class="summary-note">Completed cases</div>
                </div>
            </a>
        </div>
    </div>

    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3 class="panel-title">Filter Cases</h3>
                <p class="panel-subtitle">Use the filters below to quickly find the right report case.</p>
            </div>

            @if(
                !empty($filters['keyword']) ||
                !empty($filters['status']) ||
                !empty($filters['severity']) ||
                !empty($filters['case_type']) ||
                !empty($filters['district']) ||
                !empty($filters['date_from']) ||
                !empty($filters['date_to']) ||
                !empty($filters['card'])
            )
                <div class="active-chip">
                    <i class="bi bi-check2-circle"></i>
                    Filter applied
                </div>
            @endif
        </div>

        <div class="panel-body">
            <form method="GET" action="{{ route('admin.report-cases.index') }}">
                @if(!empty($filters['card']))
                    <input type="hidden" name="card" value="{{ $filters['card'] }}">
                @endif

                <div class="row g-3 filter-grid">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input
                            type="text"
                            name="keyword"
                            value="{{ $filters['keyword'] ?? '' }}"
                            class="form-control"
                            placeholder="Case ID, title, mobile..."
                        >
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>
                                    {{ ucwords(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Severity</label>
                        <select name="severity" class="form-select">
                            <option value="">All Severity</option>
                            <option value="low" @selected(($filters['severity'] ?? '') === 'low')>Low</option>
                            <option value="medium" @selected(($filters['severity'] ?? '') === 'medium')>Medium</option>
                            <option value="high" @selected(($filters['severity'] ?? '') === 'high')>High</option>
                            <option value="critical" @selected(($filters['severity'] ?? '') === 'critical')>Critical</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Case Type</label>
                        <select name="case_type" class="form-select">
                            <option value="">All Types</option>
                            <option value="accident" @selected(($filters['case_type'] ?? '') === 'accident')>Accident</option>
                            <option value="injured_cattle" @selected(($filters['case_type'] ?? '') === 'injured_cattle')>Injured Cattle</option>
                            <option value="illegal_transport" @selected(($filters['case_type'] ?? '') === 'illegal_transport')>Illegal Transport</option>
                            <option value="abandoned_cattle" @selected(($filters['case_type'] ?? '') === 'abandoned_cattle')>Abandoned Cattle</option>
                            <option value="dead_cattle" @selected(($filters['case_type'] ?? '') === 'dead_cattle')>Dead Cattle</option>
                            <option value="medical_emergency" @selected(($filters['case_type'] ?? '') === 'medical_emergency')>Medical Emergency</option>
                            <option value="missing_cattle" @selected(($filters['case_type'] ?? '') === 'missing_cattle')>Missing Cattle</option>
                            <option value="rescue_needed" @selected(($filters['case_type'] ?? '') === 'rescue_needed')>Rescue Needed</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">District</label>
                        <input
                            type="text"
                            name="district"
                            value="{{ $filters['district'] ?? '' }}"
                            class="form-control"
                            placeholder="District name"
                        >
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control">
                    </div>

                    <div class="col-md-8 d-flex align-items-end gap-2 flex-wrap">
                        <button type="submit" class="btn primary-btn">
                            <i class="bi bi-funnel-fill me-1"></i> Apply Filter
                        </button>

                        <a href="{{ route('admin.report-cases.index') }}" class="btn secondary-btn">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3 class="panel-title">Report Case List</h3>
                <p class="panel-subtitle">Click on a case to view full details, update status, or assign a handler.</p>
            </div>
        </div>

        <div class="panel-body pt-3">
            <div class="table-wrap table-responsive">
                <table id="reportTable" class="table align-middle">
                    <thead>
                        <tr>
                            <th>Case</th>
                            <th>Reporter</th>
                            <th>Handler</th>
                            <th>Severity</th>
                            <th>Status</th>
                            <th>District</th>
                            <th>Created At</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr>
                                <td>
                                    <div class="case-main-id">{{ $report->case_uid ?? 'EC-'.$report->id }}</div>
                                    <div class="case-main-title">{{ ucwords(str_replace('_', ' ', $report->case_type)) }}</div>
                                    <div class="case-main-meta">{{ $report->title ?: 'No title available' }}</div>
                                </td>

                                <td>
                                    <div class="user-block">
                                        <div class="name">{{ $report->reporter_name ?? 'N/A' }}</div>
                                        <div class="meta">{{ $report->reporter_mobile ?? '-' }}</div>
                                    </div>
                                </td>

                                <td>
                                    <div class="user-block">
                                        <div class="name">{{ $report->handler_name ?? 'Not Assigned' }}</div>
                                        <div class="meta">{{ $report->handler_mobile ?? '-' }}</div>
                                    </div>
                                </td>

                                <td>
                                    <span class="badge-severity severity-{{ $report->severity }}">
                                        {{ ucfirst($report->severity) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge-status status-{{ $report->status }}">
                                        {{ ucwords(str_replace('_', ' ', $report->status)) }}
                                    </span>
                                </td>

                                <td>{{ $report->district ?? '-' }}</td>

                                <td>{{ \Carbon\Carbon::parse($report->created_at)->format('d M Y, h:i A') }}</td>

                                <td class="text-end">
                                    <div class="action-stack">
                                        <a href="{{ route('admin.report-cases.show', $report->id) }}" class="btn btn-outline-dark table-action-btn">
                                            View
                                        </a>

                                        <button
                                            type="button"
                                            class="btn btn-outline-primary table-action-btn open-status-modal"
                                            data-bs-toggle="modal"
                                            data-bs-target="#quickStatusModal"
                                            data-id="{{ $report->id }}"
                                            data-uid="{{ $report->case_uid ?? 'EC-'.$report->id }}"
                                            data-status="{{ $report->status }}"
                                        >
                                            Status
                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-outline-success table-action-btn open-assign-modal"
                                            data-bs-toggle="modal"
                                            data-bs-target="#assignHandlerModal"
                                            data-id="{{ $report->id }}"
                                            data-uid="{{ $report->case_uid ?? 'EC-'.$report->id }}"
                                            data-handler="{{ $report->handler_name ?? 'Not Assigned' }}"
                                        >
                                            Assign
                                        </button>

                                        <a href="{{ route('admin.report-cases.show', $report->id) }}#map-block" class="btn btn-outline-secondary table-action-btn">
                                            Map
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <div class="icon">
                                            <i class="bi bi-inbox"></i>
                                        </div>
                                        <h6 class="fw-bold mb-2 text-dark">No report cases found</h6>
                                        <div>Try changing filters or reset the search to see more records.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="quickStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="quickStatusForm">
                @csrf
                <div class="modal-header border-0">
                    <div>
                        <h5 class="modal-title">Quick Status Update</h5>
                        <small class="text-muted" id="quickStatusCaseLabel"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Status</label>
                        <select name="status" id="quickStatusValue" class="form-select" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}">{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Write update note..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="assignHandlerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="assignHandlerForm">
                @csrf
                <div class="modal-header border-0">
                    <div>
                        <h5 class="modal-title">Assign Primary Handler</h5>
                        <small class="text-muted" id="assignCaseLabel"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Handler</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Choose responder</option>
                            @foreach($responders as $responder)
                                <option value="{{ $responder->id }}">
                                    {{ $responder->name }} {{ $responder->mobile ? '(' . $responder->mobile . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Write assignment note..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn secondary-btn" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Assign Handler</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function () {
        $('#reportTable').DataTable({
            order: [[6, 'desc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            dom:
                "<'row mb-3'<'col-md-6 d-flex align-items-center gap-2'B><'col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
            buttons: [
                {
                    extend: 'copyHtml5',
                    text: 'Copy',
                    className: 'btn btn-outline-dark'
                },
                {
                    extend: 'csvHtml5',
                    text: 'CSV',
                    className: 'btn btn-outline-primary'
                },
                {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn btn-outline-success'
                },
                {
                    extend: 'print',
                    text: 'Print',
                    className: 'btn btn-outline-secondary'
                }
            ]
        });

        $('.open-status-modal').on('click', function () {
            const id = $(this).data('id');
            const uid = $(this).data('uid');
            const status = $(this).data('status');
            const action = "{{ url('admin/report-cases') }}/" + id + "/status";

            $('#quickStatusForm').attr('action', action);
            $('#quickStatusCaseLabel').text('Case: ' + uid);
            $('#quickStatusValue').val(status);
        });

        $('.open-assign-modal').on('click', function () {
            const id = $(this).data('id');
            const uid = $(this).data('uid');
            const handler = $(this).data('handler');
            const action = "{{ url('admin/report-cases') }}/" + id + "/assign-handler";

            $('#assignHandlerForm').attr('action', action);
            $('#assignCaseLabel').text('Case: ' + uid + ' | Current: ' + handler);
        });
    });
</script>
@endpush