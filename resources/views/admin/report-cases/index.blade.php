@extends('admin.layouts.app')

@section('title', 'Manage Report Cases - GauMitra Admin')

@section('content')
<style>
    .page-head {
        background: linear-gradient(135deg, #eff6ff, #ffffff, #fff7ed);
        border: 1px solid #dbeafe;
        border-radius: 24px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    }
    .page-head h2 { font-size: 28px; font-weight: 800; color: #111827; margin-bottom: 6px; }
    .page-head p { color: #6b7280; margin-bottom: 0; }

    .summary-card {
        background: #fff;
        border: 1px solid #eef2f7;
        border-radius: 22px;
        padding: 20px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
        height: 100%;
    }
    .summary-card h6 {
        font-size: 13px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 10px;
    }
    .summary-card h3 {
        font-size: 30px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 0;
    }

    .filter-card, .list-card {
        background: #fff;
        border: 1px solid #eef2f7;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    }
    .filter-card { margin-bottom: 24px; }

    .badge-status, .badge-severity {
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
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

    .severity-low { background: #f3f4f6; color: #374151; }
    .severity-medium { background: #fef3c7; color: #92400e; }
    .severity-high { background: #fee2e2; color: #b91c1c; }
    .severity-critical { background: #111827; color: #fff; }

    .case-id {
        font-weight: 800;
        color: #111827;
    }
    .case-meta {
        font-size: 12px;
        color: #6b7280;
    }

    .table > :not(caption) > * > * {
        vertical-align: middle;
    }

    .dt-buttons .btn {
        border-radius: 999px !important;
        margin-right: 8px;
        padding: 7px 16px;
        font-weight: 600;
    }

    .dataTables_wrapper .dataTables_filter input,
    .dataTables_wrapper .dataTables_length select {
        border-radius: 12px;
        border: 1px solid #d1d5db;
        padding: 6px 12px;
    }

    .modal-content {
        border-radius: 22px;
        border: none;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.18);
    }
</style>

<div class="page-head">
    <h2>Manage Report Cases</h2>
    <p>Search, sort, export, assign handlers, and update case status directly from this command screen.</p>
</div>

@if(session('success'))
    <div class="alert alert-success rounded-4">{{ session('success') }}</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl">
        <div class="summary-card">
            <h6>Total Reports</h6>
            <h3>{{ $summary['total'] ?? 0 }}</h3>
        </div>
    </div>
    <div class="col-md-6 col-xl">
        <div class="summary-card">
            <h6>Pending</h6>
            <h3>{{ $summary['pending'] ?? 0 }}</h3>
        </div>
    </div>
    <div class="col-md-6 col-xl">
        <div class="summary-card">
            <h6>Active Rescue</h6>
            <h3>{{ $summary['active'] ?? 0 }}</h3>
        </div>
    </div>
    <div class="col-md-6 col-xl">
        <div class="summary-card">
            <h6>Critical</h6>
            <h3>{{ $summary['critical'] ?? 0 }}</h3>
        </div>
    </div>
    <div class="col-md-6 col-xl">
        <div class="summary-card">
            <h6>Resolved / Closed</h6>
            <h3>{{ $summary['resolved'] ?? 0 }}</h3>
        </div>
    </div>
</div>

<div class="filter-card">
    <form method="GET" action="{{ route('admin.report-cases.index') }}">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Keyword Filter</label>
                <input type="text" name="keyword" value="{{ $filters['keyword'] ?? '' }}" class="form-control rounded-3" placeholder="Case ID, title, mobile, district">
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select rounded-3">
                    <option value="">All Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>
                            {{ ucwords(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Severity</label>
                <select name="severity" class="form-select rounded-3">
                    <option value="">All Severity</option>
                    <option value="low" @selected(($filters['severity'] ?? '') === 'low')>Low</option>
                    <option value="medium" @selected(($filters['severity'] ?? '') === 'medium')>Medium</option>
                    <option value="high" @selected(($filters['severity'] ?? '') === 'high')>High</option>
                    <option value="critical" @selected(($filters['severity'] ?? '') === 'critical')>Critical</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Case Type</label>
                <select name="case_type" class="form-select rounded-3">
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
                <label class="form-label fw-semibold">District</label>
                <input type="text" name="district" value="{{ $filters['district'] ?? '' }}" class="form-control rounded-3" placeholder="District name">
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Date From</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control rounded-3">
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Date To</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control rounded-3">
            </div>

            <div class="col-md-8 d-flex align-items-end gap-2">
                <button class="btn btn-dark rounded-pill px-4" type="submit">
                    <i class="bi bi-funnel-fill me-1"></i> Apply Server Filter
                </button>
                <a href="{{ route('admin.report-cases.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    Reset
                </a>
            </div>
        </div>
    </form>
</div>

<div class="list-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h4 class="mb-0 fw-bold">Report Case List</h4>
        <span class="text-muted">Use table search, sorting, Excel/CSV/Print export below</span>
    </div>

    <div class="table-responsive">
        <table id="reportTable" class="table table-striped align-middle">
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
                            <div>{{ ucwords(str_replace('_', ' ', $report->case_type)) }}</div>
                            <div class="case-meta">{{ $report->title }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $report->reporter_name ?? 'N/A' }}</div>
                            <div class="case-meta">{{ $report->reporter_mobile ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $report->handler_name ?? 'Not Assigned' }}</div>
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
                                <a href="{{ route('admin.report-cases.show', $report->id) }}" class="btn btn-sm btn-outline-dark rounded-pill">
                                    View
                                </a>

                                <button type="button"
                                        class="btn btn-sm btn-outline-primary rounded-pill open-status-modal"
                                        data-bs-toggle="modal"
                                        data-bs-target="#quickStatusModal"
                                        data-id="{{ $report->id }}"
                                        data-uid="{{ $report->case_uid ?? 'EC-'.$report->id }}"
                                        data-status="{{ $report->status }}">
                                    Quick Status
                                </button>

                                <button type="button"
                                        class="btn btn-sm btn-outline-success rounded-pill open-assign-modal"
                                        data-bs-toggle="modal"
                                        data-bs-target="#assignHandlerModal"
                                        data-id="{{ $report->id }}"
                                        data-uid="{{ $report->case_uid ?? 'EC-'.$report->id }}"
                                        data-handler="{{ $report->handler_name ?? 'Not Assigned' }}">
                                    Assign
                                </button>

                                <a href="{{ route('admin.report-cases.show', $report->id) }}#map-block" class="btn btn-sm btn-outline-secondary rounded-pill">
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
                        <label class="form-label fw-semibold">New Status</label>
                        <select name="status" id="quickStatusValue" class="form-select rounded-3" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}">{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control rounded-3" rows="4" placeholder="Write quick note..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
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
                        <label class="form-label fw-semibold">Select Handler</label>
                        <select name="user_id" class="form-select rounded-3" required>
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
                        <textarea name="notes" class="form-control rounded-3" rows="4" placeholder="Write assignment note..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
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