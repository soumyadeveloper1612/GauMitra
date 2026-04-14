@extends('admin.layouts.app')

@section('title', 'Report Case Details - GauMitra Admin')

@section('content')
<style>
    .detail-head {
        background: linear-gradient(135deg, #ffffff, #fff7ed, #eff6ff);
        border: 1px solid #e5e7eb;
        border-radius: 24px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    }
    .detail-card {
        background: #fff;
        border: 1px solid #eef2f7;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        margin-bottom: 24px;
    }
    .label {
        font-size: 12px;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 4px;
    }
    .value {
        font-size: 15px;
        color: #111827;
        font-weight: 600;
    }
    .badge-status, .badge-severity {
        padding: 8px 14px;
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

    .timeline {
        position: relative;
        padding-left: 22px;
    }
    .timeline-item {
        position: relative;
        padding: 0 0 18px 18px;
        border-left: 2px solid #e5e7eb;
    }
    .timeline-item:before {
        content: "";
        position: absolute;
        left: -8px;
        top: 4px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #f97316;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #fdba74;
    }
    .timeline-item:last-child {
        border-left: 2px solid transparent;
    }

    .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        background: #fafafa;
        height: 180px;
        cursor: pointer;
    }
    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .3s ease;
    }
    .gallery-item:hover img {
        transform: scale(1.06);
    }
    .gallery-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: end;
        padding: 12px;
        background: linear-gradient(to top, rgba(0,0,0,.55), transparent);
        color: #fff;
        font-size: 13px;
        font-weight: 600;
    }
    .map-frame {
        width: 100%;
        height: 340px;
        border: 0;
        border-radius: 20px;
    }
</style>

@if(session('success'))
    <div class="alert alert-success rounded-4">{{ session('success') }}</div>
@endif

<div class="detail-head d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <div class="text-muted mb-1">Emergency Case Details</div>
        <h2 class="fw-bold mb-2">{{ $report->case_uid ?? 'EC-'.$report->id }}</h2>
        <div class="d-flex gap-2 flex-wrap">
            <span class="badge-severity severity-{{ $report->severity }}">{{ ucfirst($report->severity) }}</span>
            <span class="badge-status status-{{ $report->status }}">{{ ucwords(str_replace('_', ' ', $report->status)) }}</span>
        </div>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('admin.report-cases.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            Back
        </a>
        <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}" target="_blank" class="btn btn-dark rounded-pill px-4">
            Open in Google Maps
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="detail-card">
            <h4 class="fw-bold mb-4">Case Information</h4>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="label">Case Type</div>
                    <div class="value">{{ ucwords(str_replace('_', ' ', $report->case_type)) }}</div>
                </div>
                <div class="col-md-6">
                    <div class="label">Title</div>
                    <div class="value">{{ $report->title }}</div>
                </div>
                <div class="col-md-6">
                    <div class="label">Reporter Name</div>
                    <div class="value">{{ $report->reporter_name ?? 'N/A' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="label">Reporter Mobile</div>
                    <div class="value">{{ $report->reporter_mobile ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="label">Current Handler</div>
                    <div class="value">{{ $report->handler_name ?? 'Not Assigned' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="label">Handler Mobile</div>
                    <div class="value">{{ $report->handler_mobile ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="label">Contact Number</div>
                    <div class="value">{{ $report->contact_number ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="label">Cattle Count</div>
                    <div class="value">{{ $report->cattle_count ?? '-' }}</div>
                </div>
                <div class="col-md-12">
                    <div class="label">Description</div>
                    <div class="value">{{ $report->description ?: 'No description available.' }}</div>
                </div>
                <div class="col-md-12">
                    <div class="label">Address</div>
                    <div class="value">{{ $report->full_address ?: '-' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="label">District</div>
                    <div class="value">{{ $report->district ?? '-' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="label">State</div>
                    <div class="value">{{ $report->state ?? '-' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="label">Pincode</div>
                    <div class="value">{{ $report->pincode ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="label">Latitude</div>
                    <div class="value">{{ $report->latitude }}</div>
                </div>
                <div class="col-md-6">
                    <div class="label">Longitude</div>
                    <div class="value">{{ $report->longitude }}</div>
                </div>
            </div>
        </div>

        <div class="detail-card" id="map-block">
            <h4 class="fw-bold mb-4">Google Map Location View</h4>
            <iframe
                class="map-frame"
                loading="lazy"
                allowfullscreen
                src="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}&z=15&output=embed">
            </iframe>
        </div>

        <div class="detail-card">
            <h4 class="fw-bold mb-4">Report Image Gallery</h4>
            <div class="row g-3">
                @forelse($media as $item)
                    @if($item->media_type === 'photo')
                        <div class="col-md-4">
                            <div class="gallery-item lightbox-trigger"
                                 data-image="{{ asset('storage/' . $item->file_path) }}"
                                 data-title="{{ $item->file_name ?? 'Case Image' }}">
                                <img src="{{ asset('storage/' . $item->file_path) }}" alt="case image">
                                <div class="gallery-overlay">{{ $item->file_name ?? 'View Image' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="col-md-4">
                            <div class="p-3 border rounded-4 h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="fw-bold mb-2">Video File</div>
                                    <div class="text-muted small">{{ $item->file_name ?? 'Media File' }}</div>
                                </div>
                                <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="btn btn-outline-dark rounded-pill mt-3">
                                    Open Video
                                </a>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="col-12 text-muted">No media uploaded for this case.</div>
                @endforelse
            </div>
        </div>

        <div class="detail-card">
            <h4 class="fw-bold mb-4">Timeline Logs</h4>
            <div class="timeline">
                @forelse($logs as $log)
                    <div class="timeline-item">
                        <div class="fw-bold">{{ ucwords(str_replace('_', ' ', $log->action)) }}</div>
                        <div class="text-muted small mb-1">
                            By: {{ $log->user_name ?? 'System / Admin' }} |
                            {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, h:i A') }}
                        </div>

                        @if($log->old_status || $log->new_status)
                            <div class="mb-1">
                                <strong>Status:</strong>
                                {{ $log->old_status ? ucwords(str_replace('_', ' ', $log->old_status)) : 'N/A' }}
                                →
                                {{ $log->new_status ? ucwords(str_replace('_', ' ', $log->new_status)) : 'N/A' }}
                            </div>
                        @endif

                        <div>{{ $log->notes ?: 'No extra note added.' }}</div>
                    </div>
                @empty
                    <div class="text-muted">No timeline logs found.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="detail-card">
            <h4 class="fw-bold mb-4">Quick Status Update</h4>
            <form method="POST" action="{{ route('admin.report-cases.update-status', $report->id) }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">New Status</label>
                    <select name="status" class="form-select rounded-3" required>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @selected($report->status === $status)>
                                {{ ucwords(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Notes</label>
                    <textarea name="notes" rows="4" class="form-control rounded-3" placeholder="Write admin note or reason..."></textarea>
                </div>

                <button class="btn btn-primary w-100 rounded-pill py-2">
                    Update Status
                </button>
            </form>
        </div>

        <div class="detail-card">
            <h4 class="fw-bold mb-4">Assign Primary Handler</h4>
            <form method="POST" action="{{ route('admin.report-cases.assign-handler', $report->id) }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Select Handler</label>
                    <select name="user_id" class="form-select rounded-3" required>
                        <option value="">Choose responder</option>
                        @foreach($responders as $responder)
                            <option value="{{ $responder->id }}" @selected($report->current_handler_id == $responder->id)>
                                {{ $responder->name }} {{ $responder->mobile ? '(' . $responder->mobile . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Notes</label>
                    <textarea name="notes" rows="3" class="form-control rounded-3" placeholder="Assignment note..."></textarea>
                </div>

                <button class="btn btn-success w-100 rounded-pill py-2">
                    Assign Handler
                </button>
            </form>
        </div>

        <div class="detail-card">
            <h4 class="fw-bold mb-4">Assigned Responders</h4>
            @forelse($assignments as $assign)
                <div class="mb-3 pb-3 border-bottom">
                    <div class="fw-semibold">{{ $assign->user_name ?? 'Unknown User' }}</div>
                    <div class="text-muted small">{{ $assign->user_mobile ?? '-' }}</div>
                    <div class="small mt-1">
                        Role: <strong>{{ ucwords(str_replace('_', ' ', $assign->assignment_role)) }}</strong>
                    </div>
                    <div class="small">
                        Status: <strong>{{ ucwords(str_replace('_', ' ', $assign->status)) }}</strong>
                    </div>
                </div>
            @empty
                <div class="text-muted">No assigned responder found.</div>
            @endforelse
        </div>

        <div class="detail-card">
            <h4 class="fw-bold mb-4">Important Time Info</h4>
            <div class="mb-3">
                <div class="label">Created At</div>
                <div class="value">{{ \Carbon\Carbon::parse($report->created_at)->format('d M Y, h:i A') }}</div>
            </div>
            <div class="mb-3">
                <div class="label">Accepted At</div>
                <div class="value">{{ $report->accepted_at ? \Carbon\Carbon::parse($report->accepted_at)->format('d M Y, h:i A') : '-' }}</div>
            </div>
            <div class="mb-3">
                <div class="label">Reached At</div>
                <div class="value">{{ $report->reached_at ? \Carbon\Carbon::parse($report->reached_at)->format('d M Y, h:i A') : '-' }}</div>
            </div>
            <div class="mb-3">
                <div class="label">Resolved At</div>
                <div class="value">{{ $report->resolved_at ? \Carbon\Carbon::parse($report->resolved_at)->format('d M Y, h:i A') : '-' }}</div>
            </div>
            <div>
                <div class="label">Closed At</div>
                <div class="value">{{ $report->closed_at ? \Carbon\Carbon::parse($report->closed_at)->format('d M Y, h:i A') : '-' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="imageLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="lightboxTitle">Image Preview</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center pt-0">
                <img id="lightboxPreview" src="" alt="Preview" class="img-fluid rounded-4" style="max-height:75vh;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.lightbox-trigger').forEach(item => {
        item.addEventListener('click', function () {
            const imageUrl = this.getAttribute('data-image');
            const imageTitle = this.getAttribute('data-title') || 'Image Preview';

            document.getElementById('lightboxPreview').src = imageUrl;
            document.getElementById('lightboxTitle').textContent = imageTitle;

            const modal = new bootstrap.Modal(document.getElementById('imageLightboxModal'));
            modal.show();
        });
    });
</script>
@endpush