@extends('admin.layouts.app')

@section('title', 'Manage Report Cases - GauMitra Admin')

@section('content')
<style>
    :root{
        --gm-primary:#0f172a;
        --gm-secondary:#334155;
        --gm-accent:#f97316;
        --gm-accent-soft:#fff7ed;
        --gm-blue:#2563eb;
        --gm-blue-soft:#eff6ff;
        --gm-green:#059669;
        --gm-green-soft:#ecfdf5;
        --gm-red:#dc2626;
        --gm-red-soft:#fef2f2;
        --gm-purple:#7c3aed;
        --gm-purple-soft:#f5f3ff;
        --gm-border:#e2e8f0;
        --gm-muted:#64748b;
        --gm-bg:#f8fafc;
        --gm-card:#ffffff;
    }

    body {
        background: var(--gm-bg);
    }

    .report-page-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 45%, #f97316 100%);
        border-radius: 28px;
        padding: 28px;
        color: #fff;
        margin-bottom: 24px;
        box-shadow: 0 18px 50px rgba(15, 23, 42, 0.18);
    }

    .report-page-header h2 {
        font-size: 30px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .report-page-header p {
        margin-bottom: 0;
        color: rgba(255,255,255,0.85);
        font-size: 14px;
    }

    .summary-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
        height: 100%;
    }

    .summary-card {
        background: var(--gm-card);
        border: 1px solid var(--gm-border);
        border-radius: 24px;
        padding: 22px;
        height: 100%;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .summary-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
    }

    .summary-card.total::before { background: linear-gradient(90deg, #0f172a, #475569); }
    .summary-card.pending::before { background: linear-gradient(90deg, #f97316, #fb923c); }
    .summary-card.active::before { background: linear-gradient(90deg, #2563eb, #60a5fa); }
    .summary-card.critical::before { background: linear-gradient(90deg, #dc2626, #fb7185); }
    .summary-card.resolved::before { background: linear-gradient(90deg, #059669, #34d399); }

    .summary-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.10);
    }

    .summary-card.active-card {
        border: 2px solid var(--gm-primary);
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.12);
    }

    .summary-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
    }

    .summary-label {
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 800;
        letter-spacing: 0.5px;
        color: var(--gm-muted);
    }

    .summary-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .total .summary-icon { background: #e2e8f0; color: #0f172a; }
    .pending .summary-icon { background: var(--gm-accent-soft); color: var(--gm-accent); }
    .active .summary-icon { background: var(--gm-blue-soft); color: var(--gm-blue); }
    .critical .summary-icon { background: var(--gm-red-soft); color: var(--gm-red); }
    .resolved .summary-icon { background: var(--gm-green-soft); color: var(--gm-green); }

    .summary-number {
        font-size: 34px;
        font-weight: 800;
        color: var(--gm-primary);
        line-height: 1;
    }

    .summary-note {
        margin-top: 10px;
        font-size: 13px;
        color: var(--gm-muted);
    }

    .panel-card {
        background: #fff;
        border: 1px solid var(--gm-border);
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        margin-bottom: 24px;
    }

    .section-title {
        font-size: 20px;
        font-weight: 800;
        color: var(--gm-primary);
        margin-bottom: 18px;
    }

    .form-label {
        font-weight: 700;
        color: var(--gm-secondary);
        font-size: 14px;
    }

    .form-control, .form-select {
        min-height: 48px;
        border-radius: 14px;
        border: 1px solid #dbe2ea;
        box-shadow: none !important;
    }

    .form-control:focus, .form-select:focus {
        border-color: #f97316;
    }

    .filter-btn,
    .reset-btn {
        min-height: 48px;
        border-radius: 999px;
        padding-inline: 22px;
        font-weight: 700;
    }

    .filter-btn {
        background: linear-gradient(135deg, #0f172a, #334155);
        border: none;
        color: #fff;
    }

    .reset-btn {
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #334155;
    }

    .active-filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fff7ed;
        color: #9a3412;
        padding: 10px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        border: 1px solid #fed7aa;
    }

    .badge-status, .badge-severity {
        padding: 8px 13px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        display: inline-block;
        text-transform: capitalize;
    }

    .status-reported, .status-alerted { background: #fff7ed; color: #c2410c; }
    .status-accepted, .status-en_route, .status-reached_site,
    .status-rescue_in_progress, .status-needs_backup,
    .status-treatment_started, .status-shifted_to_gaushala,
    .status-escalated { background: #eff6ff; color: #1d4ed8; }
    .status-resolved, .status-closed { background: #ecfdf5; color: #047857; }
    .status-false_report, .status-cancelled, .status-duplicate_case,
    .status-unable_to_locate { background: #fef2f2; color: #b91c1c; }

    .severity-low { background: #f1f5f9; color: #475569; }
    .severity-medium { background: #fef3c7; color: #92400e; }
    .severity-high { background: #fee2e2; color: #b91c1c; }
    .severity-critical { background: #111827; color: #fff; }

    .case-table-wrap {
        border-radius: 20px;
        overflow: hidden;
    }

    #reportTable thead th {
        background: #f8fafc;
        color: #334155;
        font-size: 13px;
        font-weight: 800;
        border-bottom: 1px solid #e2e8f0 !important;
    }

    #reportTable tbody tr:hover {
        background: #fffaf5;
    }

    .case-id {
        font-weight: 800;
        font-size: 15px;
        color: var(--gm-primary);
        margin-bottom: 4px;
    }

    .case-title {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 2px;
    }

    .case-meta {
        font-size: 12px;
        color: var(--gm-muted);
    }

    .action-btn {
        border-radius: 999px !important;
        font-weight: 700 !important;
        padding: 6px 14px !important;
    }

    .dataTables_wrapper .dataTables_filter input,
    .dataTables_wrapper .dataTables_length select {
        border-radius: 12px;
        border: 1px solid #d1d5db;
        padding: 6px 12px;
        min-height: 40px;
    }

    .dt-buttons .btn {
        border-radius: 999px !important;
        margin-right: 8px;
        padding: 7px 16px;
        font-weight: 700;
    }

    .modal-content {
        border-radius: 26px;
        border: none;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
    }

    @media (max-width: 767px) {
        .report-page-header {
            padding: 22px;
        }

        .summary-number {
            font-size: 28px;
        }
    }
</style>

@if(session('success'))
    <div class="alert alert-success rounded-4">{{ session('success') }}</div>
@endif

<div class="report-page-header">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
      

        @if(!empty($filters['card']))
            <div class="active-filter-chip">
                <i class="bi bi-funnel-fill"></i>
                Current view:
                <span>{{ ucfirst($filters['card']) }}</span>
            </div>
        @endif
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl">
        <a href="{{ route('admin.report-cases.index') }}" class="summary-card-link">
            <div class="summary-card total {{ empty($filters['card']) ? 'active-card' : '' }}">
                <div class="summary-top">
                    <div class="summary-label">Total Reports</div>
                    <div class="summary-icon"><i class="bi bi-files"></i></div>
                </div>
                <div class="summary-number">{{ $summary['total'] ?? 0 }}</div>
                <div class="summary-note">Show all case records</div>
            </div>
        </a>
    </div>

    <div class="col-md-6 col-xl">
        <a href="{{ route('admin.report-cases.index', array_merge(request()->except('page'), ['card' => 'pending'])) }}" class="summary-card-link">
            <div class="summary-card pending {{ ($filters['card'] ?? '') === 'pending' ? 'active-card' : '' }}">
                <div class="summary-top">
                    <div class="summary-label">Pending</div>
                    <div class="summary-icon"><i class="bi bi-hourglass-split"></i></div>
                </div>
                <div class="summary-number">{{ $summary['pending'] ?? 0 }}</div>
                <div class="summary-note">Reported and alerted cases</div>
            </div>
        </a>
    </div>

    <div class="col-md-6 col-xl">
        <a href="{{ route('admin.report-cases.index', array_merge(request()->except('page'), ['card' => 'active'])) }}" class="summary-card-link">
            <div class="summary-card active {{ ($filters['card'] ?? '') === 'active' ? 'active-card' : '' }}">
                <div class="summary-top">
                    <div class="summary-label">Active Rescue</div>
                    <div class="summary-icon"><i class="bi bi-lightning-charge"></i></div>
                </div>
                <div class="summary-number">{{ $summary['active'] ?? 0 }}</div>
                <div class="summary-note">Cases currently under action</div>
            </div>
        </a>
    </div>

    <div class="col-md-6 col-xl">
        <a href="{{ route('admin.report-cases.index', array_merge(request()->except('page'), ['card' => 'critical'])) }}" class="summary-card-link">
            <div class="summary-card critical {{ ($filters['card'] ?? '') === 'critical' ? 'active-card' : '' }}">
                <div class="summary-top">
                    <div class="summary-label">Critical</div>
                    <div class="summary-icon"><i class="bi bi-exclamation-octagon"></i></div>
                </div>
                <div class="summary-number">{{ $summary['critical'] ?? 0 }}</div>
                <div class="summary-note">High-priority immediate cases</div>
            </div>
        </a>
    </div>

    <div class="col-md-6 col-xl">
        <a href="{{ route('admin.report-cases.index', array_merge(request()->except('page'), ['card' => 'resolved'])) }}" class="summary-card-link">
            <div class="summary-card resolved {{ ($filters['card'] ?? '') === 'resolved' ? 'active-card' : '' }}">
                <div class="summary-top">
                    <div class="summary-label">Resolved / Closed</div>
                    <div class="summary-icon"><i class="bi bi-check2-circle"></i></div>
                </div>
                <div class="summary-number">{{ $summary['resolved'] ?? 0 }}</div>
                <div class="summary-note">Completed and closed cases</div>
            </div>
        </a>
    </div>
</div>

<div class="panel-card">
    <div class="section-title">Smart Filter Panel</div>

    <form method="GET" action="{{ route('admin.report-cases.index') }}">
        @if(!empty($filters['card']))
            <input type="hidden" name="card" value="{{ $filters['card'] }}">
        @endif

        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Keyword</label>
                <input type="text" name="keyword" value="{{ $filters['keyword'] ?? '' }}" class="form-control" placeholder="Case ID, title, mobile, district">
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
                <input type="text" name="district" value="{{ $filters['district'] ?? '' }}" class="form-control" placeholder="District name">
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
                <button class="btn filter-btn" type="submit">
                    <i class="bi bi-funnel-fill me-1"></i> Apply Filter
                </button>
                <a href="{{ route('admin.report-cases.index') }}" class="btn reset-btn">
                    <i class="bi bi-arrow-clockwise me-1"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<div class="panel-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <div class="section-title mb-1">Report Case List</div>
            <div class="text-muted small">Click cards above to load matching case data instantly.</div>
        </div>
    </div>

    <div class="case-table-wrap table-responsive">
        <table id="reportTable" class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Case Info</th>
                    <th>Reporter</th>
                    <th>Handler</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>District</th>
                    <th>Created</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $report)
                    <tr>
                        <td>
                            <div class="case-id">{{ $report->case_uid ?? 'EC-'.$report->id }}</div>
                            <div class="case-title">{{ ucwords(str_replace('_', ' ', $report->case_type)) }}</div>
                            <div class="case-meta">{{ $report->title }}</div>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $report->reporter_name ?? 'N/A' }}</div>
                            <div class="case-meta">{{ $report->reporter_mobile ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $report->handler_name ?? 'Not Assigned' }}</div>
                            <div class="case-meta">{{ $report->handler_mobile ?? '-' }}</div>
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
                            <div class="d-flex justify-content-end flex-wrap gap-2">
                                <a href="{{ route('admin.report-cases.show', $report->id) }}" class="btn btn-sm btn-outline-dark action-btn">
                                    View
                                </a>

                                <button type="button"
                                        class="btn btn-sm btn-outline-primary action-btn open-status-modal"
                                        data-bs-toggle="modal"
                                        data-bs-target="#quickStatusModal"
                                        data-id="{{ $report->id }}"
                                        data-uid="{{ $report->case_uid ?? 'EC-'.$report->id }}"
                                        data-status="{{ $report->status }}">
                                    Quick Status
                                </button>

                                <button type="button"
                                        class="btn btn-sm btn-outline-success action-btn open-assign-modal"
                                        data-bs-toggle="modal"
                                        data-bs-target="#assignHandlerModal"
                                        data-id="{{ $report->id }}"
                                        data-uid="{{ $report->case_uid ?? 'EC-'.$report->id }}"
                                        data-handler="{{ $report->handler_name ?? 'Not Assigned' }}">
                                    Assign
                                </button>

                                <a href="{{ route('admin.report-cases.show', $report->id) }}#map-block" class="btn btn-sm btn-outline-secondary action-btn">
                                    Map
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="quickStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="quickStatusForm">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold">Quick Status Update</h5>
                        <small class="text-muted" id="quickStatusCaseLabel"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="form-label">New Status</label>
                        <select name="status" id="quickStatusValue" class="form-select" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}">{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Write quick note..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn reset-btn" data-bs-dismiss="modal">Cancel</button>
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
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold">Assign Primary Handler</h5>
                        <small class="text-muted" id="assignCaseLabel"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="form-label">Select Handler</label>
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
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Write assignment note..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn reset-btn" data-bs-dismiss="modal">Cancel</button>
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