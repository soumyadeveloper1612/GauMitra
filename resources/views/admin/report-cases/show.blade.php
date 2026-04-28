@extends('admin.layouts.app')

@section('title', 'Report Case Details - GauMitra Admin')

@section('content')
@php
    $caseUid = $report->case_uid ?? 'EC-' . $report->id;

    $animalName = $report->animalType?->name ?? 'N/A';
    $animalIcon = $report->animalType?->icon_class ?: 'bi bi-gitlab';

    $reportTypeName = $report->reportType?->name ?? ucwords(str_replace('_', ' ', $report->case_type ?? 'Report Case'));
    $reportTypeIcon = $report->reportType?->icon_class ?: 'bi bi-clipboard-heart';

    $conditionName = $report->animalCondition?->name ?? 'N/A';
    $conditionSeverity = $report->animalCondition?->severity_level ?? $report->severity;

    $reporterName = $report->reporter?->name ?? $report->reporter_name ?? 'N/A';
    $reporterMobile = $report->reporter?->mobile ?? $report->reporter_mobile ?? '-';

    $handlerName = $report->currentHandler?->name ?? $report->handler_name ?? 'Not Assigned';
    $handlerMobile = $report->currentHandler?->mobile ?? $report->handler_mobile ?? '-';

    $hasLocation = !empty($report->latitude) && !empty($report->longitude);
@endphp

<style>
    :root {
        --gm-dark: #1f1308;
        --gm-dark-2: #3b2109;
        --gm-brown: #78350f;
        --gm-brown-2: #92400e;
        --gm-saffron: #d97706;
        --gm-gold: #f59e0b;
        --gm-cream: #fff7ed;
        --gm-cream-2: #fffbeb;
        --gm-border: #fed7aa;
        --gm-muted: #78716c;
        --gm-card: #ffffff;
        --gm-bg: #fffaf0;

        --gm-blue: #2563eb;
        --gm-blue-soft: #eff6ff;
        --gm-green: #059669;
        --gm-green-soft: #ecfdf5;
        --gm-red: #dc2626;
        --gm-red-soft: #fef2f2;
        --gm-gray-soft: #f8fafc;

        --gm-shadow: 0 12px 32px rgba(120, 53, 15, .08);
        --gm-shadow-lg: 0 22px 50px rgba(120, 53, 15, .18);
    }

    body {
        background: var(--gm-bg);
    }

    .case-shell {
        padding: 4px 0 18px;
    }

    .case-hero {
        background:
            radial-gradient(circle at top right, rgba(245, 158, 11, .28), transparent 32%),
            linear-gradient(135deg, var(--gm-dark) 0%, var(--gm-brown) 62%, var(--gm-saffron) 100%);
        border-radius: 30px;
        padding: 30px;
        color: #fff;
        margin-bottom: 24px;
        box-shadow: var(--gm-shadow-lg);
        position: relative;
        overflow: hidden;
    }

    .case-hero::after {
        content: "";
        position: absolute;
        right: -90px;
        top: -90px;
        width: 245px;
        height: 245px;
        border-radius: 50%;
        background: rgba(255,255,255,.09);
    }

    .case-hero::before {
        content: "";
        position: absolute;
        left: -80px;
        bottom: -90px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(255,255,255,.05);
    }

    .case-hero-content {
        position: relative;
        z-index: 1;
    }

    .hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: rgba(255,255,255,.78);
        font-size: 12px;
        font-weight: 900;
        margin-bottom: 9px;
    }

    .case-hero h2 {
        margin: 0 0 10px;
        font-size: 32px;
        font-weight: 900;
        letter-spacing: -.03em;
    }

    .case-hero p {
        margin: 0;
        font-size: 14px;
        color: rgba(255,255,255,.86);
        max-width: 780px;
        line-height: 1.65;
    }

    .hero-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        position: relative;
        z-index: 1;
    }

    .hero-btn {
        border-radius: 999px !important;
        padding: 10px 18px !important;
        font-weight: 800 !important;
    }

    .badge-status,
    .badge-severity {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 8px 13px;
        font-size: 12px;
        font-weight: 900;
        text-transform: capitalize;
        white-space: nowrap;
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
        background: var(--gm-blue-soft);
        color: #1d4ed8;
    }

    .status-resolved,
    .status-closed {
        background: var(--gm-green-soft);
        color: #047857;
    }

    .status-false_report,
    .status-cancelled,
    .status-duplicate_case,
    .status-unable_to_locate {
        background: var(--gm-red-soft);
        color: #b91c1c;
    }

    .severity-low {
        background: #f1f5f9;
        color: #475569;
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

    .content-card {
        background: var(--gm-card);
        border: 1px solid var(--gm-border);
        border-radius: 24px;
        box-shadow: var(--gm-shadow);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .content-card .card-head {
        padding: 22px 24px 0;
    }

    .content-card .card-title {
        margin: 0;
        color: var(--gm-dark);
        font-size: 20px;
        font-weight: 900;
    }

    .content-card .card-subtitle {
        margin: 5px 0 0;
        font-size: 13px;
        color: var(--gm-muted);
        font-weight: 600;
    }

    .content-card .card-body {
        padding: 24px;
    }

    .master-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }

    .master-card {
        background: linear-gradient(180deg, #fffaf0, #fff);
        border: 1px solid var(--gm-border);
        border-radius: 20px;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        min-height: 105px;
    }

    .master-icon {
        width: 50px;
        height: 50px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--gm-brown), var(--gm-saffron));
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .master-label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--gm-muted);
        font-weight: 900;
        margin-bottom: 5px;
    }

    .master-value {
        color: var(--gm-dark);
        font-size: 16px;
        font-weight: 900;
        line-height: 1.35;
    }

    .mini-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .mini-stat {
        background: var(--gm-cream-2);
        border: 1px solid var(--gm-border);
        border-radius: 18px;
        padding: 16px;
    }

    .mini-stat-label {
        font-size: 12px;
        color: var(--gm-muted);
        font-weight: 900;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .mini-stat-value {
        font-size: 15px;
        color: var(--gm-dark);
        font-weight: 900;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .info-box {
        background: #fff;
        border: 1px solid #ffedd5;
        border-radius: 18px;
        padding: 16px 18px;
    }

    .info-box.full {
        grid-column: 1 / -1;
    }

    .info-label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: var(--gm-muted);
        font-weight: 900;
        margin-bottom: 6px;
    }

    .info-value {
        color: #111827;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.5;
        word-break: break-word;
    }

    .condition-help {
        background: #fffbeb;
        border: 1px dashed #f59e0b;
        border-radius: 18px;
        padding: 16px;
        color: var(--gm-brown);
    }

    .condition-help strong {
        display: block;
        font-weight: 900;
        margin-bottom: 5px;
    }

    .map-frame {
        width: 100%;
        height: 360px;
        border: 0;
        border-radius: 18px;
    }

    .map-empty {
        background: var(--gm-gray-soft);
        border: 1px dashed #cbd5e1;
        border-radius: 18px;
        padding: 28px;
        color: #64748b;
        font-weight: 800;
        text-align: center;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .gallery-item {
        position: relative;
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid var(--gm-border);
        background: #f8fafc;
        min-height: 200px;
        cursor: pointer;
    }

    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .28s ease;
    }

    .gallery-item:hover img {
        transform: scale(1.05);
    }

    .gallery-caption {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        padding: 12px;
        background: linear-gradient(to top, rgba(31,19,8,.78), transparent);
        color: #fff;
        font-size: 13px;
        font-weight: 800;
    }

    .video-card {
        border: 1px solid var(--gm-border);
        border-radius: 18px;
        padding: 16px;
        background: #fff;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .timeline {
        position: relative;
        padding-left: 24px;
    }

    .timeline-item {
        position: relative;
        padding: 0 0 18px 18px;
        border-left: 2px solid #fed7aa;
    }

    .timeline-item::before {
        content: "";
        position: absolute;
        left: -8px;
        top: 3px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: var(--gm-saffron);
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #fdba74;
    }

    .timeline-item:last-child {
        border-left: 2px solid transparent;
        padding-bottom: 0;
    }

    .timeline-title {
        font-size: 15px;
        color: var(--gm-dark);
        font-weight: 900;
        margin-bottom: 4px;
    }

    .timeline-meta {
        font-size: 12px;
        color: var(--gm-muted);
        margin-bottom: 6px;
        font-weight: 600;
    }

    .sidebar-stack .content-card {
        margin-bottom: 20px;
    }

    .form-label {
        color: var(--gm-brown);
        font-size: 13px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .form-control,
    .form-select {
        min-height: 48px;
        border-radius: 14px;
        border: 1px solid #fcd34d;
        box-shadow: none !important;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--gm-saffron);
        box-shadow: 0 0 0 .18rem rgba(217, 119, 6, .14) !important;
    }

    .submit-btn {
        width: 100%;
        border-radius: 999px !important;
        min-height: 46px;
        font-weight: 900 !important;
        border: none !important;
        background: linear-gradient(135deg, var(--gm-brown), var(--gm-saffron)) !important;
        color: #fff !important;
    }

    .submit-btn:hover {
        background: linear-gradient(135deg, var(--gm-dark-2), var(--gm-brown-2)) !important;
        color: #fff !important;
    }

    .assigned-item {
        padding: 14px 0;
        border-bottom: 1px solid #ffedd5;
    }

    .assigned-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .assigned-name {
        font-size: 15px;
        font-weight: 900;
        color: var(--gm-dark);
        margin-bottom: 3px;
    }

    .assigned-meta {
        font-size: 12px;
        color: var(--gm-muted);
        margin-bottom: 6px;
    }

    .time-list {
        display: grid;
        gap: 14px;
    }

    .time-item {
        background: #fffbeb;
        border: 1px solid var(--gm-border);
        border-radius: 18px;
        padding: 15px 16px;
    }

    .time-item .label {
        font-size: 12px;
        font-weight: 900;
        color: var(--gm-muted);
        margin-bottom: 4px;
        text-transform: uppercase;
    }

    .time-item .value {
        font-size: 14px;
        font-weight: 800;
        color: var(--gm-dark);
    }

    .empty-note {
        color: var(--gm-muted);
        font-size: 14px;
        font-weight: 700;
    }

    @media (max-width: 1199px) {
        .mini-stat-grid,
        .master-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991px) {
        .gallery-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767px) {
        .case-hero {
            padding: 22px;
        }

        .case-hero h2 {
            font-size: 24px;
        }

        .info-grid,
        .mini-stat-grid,
        .gallery-grid,
        .master-grid {
            grid-template-columns: 1fr;
        }

        .content-card .card-head,
        .content-card .card-body {
            padding-left: 18px;
            padding-right: 18px;
        }
    }
</style>

<div class="case-shell">

    @if(session('success'))
        <div class="alert alert-success rounded-4 border-0 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger rounded-4 border-0 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-4 border-0 shadow-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="case-hero">
        <div class="case-hero-content">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <div class="hero-kicker">
                        <i class="bi bi-shield-check"></i>
                        Emergency Case Details
                    </div>

                    <h2>{{ $caseUid }}</h2>

                    <p>
                        Review full report information, animal master data, condition details, media evidence, location,
                        case timeline and responder assignment from one GauMitra admin dashboard.
                    </p>

                    <div class="d-flex gap-2 flex-wrap mt-3">
                        <span class="badge-severity severity-{{ $report->severity }}">
                            {{ ucfirst($report->severity) }}
                        </span>

                        <span class="badge-status status-{{ $report->status }}">
                            {{ ucwords(str_replace('_', ' ', $report->status)) }}
                        </span>
                    </div>
                </div>

                <div class="hero-actions">
                    <a href="{{ route('admin.report-cases.index') }}" class="btn btn-outline-light hero-btn">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>

                    @if($hasLocation)
                        <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}"
                           target="_blank"
                           class="btn btn-light hero-btn">
                            <i class="bi bi-geo-alt me-1"></i> Open Map
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <div class="col-lg-8">

            <div class="content-card">
                <div class="card-head">
                    <h3 class="card-title">Master Case Classification</h3>
                    <p class="card-subtitle">Dynamic Animal Type, Report Type and Animal Condition selected from app.</p>
                </div>

                <div class="card-body">
                    <div class="master-grid">
                        <div class="master-card">
                            <div class="master-icon">
                                <i class="{{ $animalIcon }}"></i>
                            </div>
                            <div>
                                <div class="master-label">Animal Type</div>
                                <div class="master-value">{{ $animalName }}</div>
                            </div>
                        </div>

                        <div class="master-card">
                            <div class="master-icon">
                                <i class="{{ $reportTypeIcon }}"></i>
                            </div>
                            <div>
                                <div class="master-label">Report Type</div>
                                <div class="master-value">{{ $reportTypeName }}</div>
                            </div>
                        </div>

                        <div class="master-card">
                            <div class="master-icon">
                                <i class="{{ $report->animalCondition?->icon_class ?: 'bi bi-heart-pulse' }}"></i>
                            </div>
                            <div>
                                <div class="master-label">Animal Condition</div>
                                <div class="master-value">{{ $conditionName }}</div>
                            </div>
                        </div>
                    </div>

                    @if($report->animalCondition)
                        <div class="condition-help">
                            <strong>
                                <i class="bi bi-info-circle-fill me-1"></i>
                                Condition Guidance
                            </strong>

                            <div class="mb-2">
                                <strong class="d-inline">Symptoms:</strong>
                                {{ $report->animalCondition->symptoms ?: 'No symptoms added.' }}
                            </div>

                            <div>
                                <strong class="d-inline">First Aid Steps:</strong>
                                {{ $report->animalCondition->first_aid_steps ?: 'No first aid steps added.' }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="content-card">
                <div class="card-head">
                    <h3 class="card-title">Case Overview</h3>
                    <p class="card-subtitle">Core case details at a glance.</p>
                </div>

                <div class="card-body">
                    <div class="mini-stat-grid mb-4">
                        <div class="mini-stat">
                            <div class="mini-stat-label">Reporter</div>
                            <div class="mini-stat-value">{{ $reporterName }}</div>
                        </div>

                        <div class="mini-stat">
                            <div class="mini-stat-label">Handler</div>
                            <div class="mini-stat-value">{{ $handlerName }}</div>
                        </div>

                        <div class="mini-stat">
                            <div class="mini-stat-label">District</div>
                            <div class="mini-stat-value">{{ $report->district ?? '-' }}</div>
                        </div>

                        <div class="mini-stat">
                            <div class="mini-stat-label">Condition Severity</div>
                            <div class="mini-stat-value">{{ ucfirst($conditionSeverity ?: '-') }}</div>
                        </div>
                    </div>

                    <div class="info-grid">
                        <div class="info-box">
                            <div class="info-label">Title</div>
                            <div class="info-value">{{ $report->title ?: '-' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Contact Number</div>
                            <div class="info-value">{{ $report->contact_number ?? '-' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Reporter Mobile</div>
                            <div class="info-value">{{ $reporterMobile }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Handler Mobile</div>
                            <div class="info-value">{{ $handlerMobile }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Cattle / Animal Count</div>
                            <div class="info-value">{{ $report->cattle_count ?? '-' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">State / Pincode</div>
                            <div class="info-value">{{ $report->state ?? '-' }} / {{ $report->pincode ?? '-' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Vehicle Number</div>
                            <div class="info-value">{{ $report->vehicle_number ?: '-' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Vehicle Details</div>
                            <div class="info-value">{{ $report->vehicle_details ?: '-' }}</div>
                        </div>

                        <div class="info-box full">
                            <div class="info-label">Description</div>
                            <div class="info-value">{{ $report->description ?: 'No description available.' }}</div>
                        </div>

                        <div class="info-box full">
                            <div class="info-label">Full Address</div>
                            <div class="info-value">{{ $report->full_address ?: '-' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Area Name</div>
                            <div class="info-value">{{ $report->area_name ?: '-' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Landmark</div>
                            <div class="info-value">{{ $report->land_mark ?: '-' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Road Name</div>
                            <div class="info-value">{{ $report->road_name ?: '-' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">City</div>
                            <div class="info-value">{{ $report->city ?: '-' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Latitude</div>
                            <div class="info-value">{{ $report->latitude ?? '-' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Longitude</div>
                            <div class="info-value">{{ $report->longitude ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-card" id="map-block">
                <div class="card-head">
                    <h3 class="card-title">Location Map</h3>
                    <p class="card-subtitle">Case location preview for quick verification.</p>
                </div>

                <div class="card-body">
                    @if($hasLocation)
                        <iframe
                            class="map-frame"
                            loading="lazy"
                            allowfullscreen
                            src="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}&z=15&output=embed">
                        </iframe>
                    @else
                        <div class="map-empty">
                            <i class="bi bi-geo-alt-fill me-1"></i>
                            Location coordinates are not available for this case.
                        </div>
                    @endif
                </div>
            </div>

            <div class="content-card">
                <div class="card-head">
                    <h3 class="card-title">Media Evidence</h3>
                    <p class="card-subtitle">Uploaded case photos and video files.</p>
                </div>

                <div class="card-body">
                    @if($media->count())
                        <div class="gallery-grid">
                            @foreach($media as $item)
                                @if($item->media_type === 'photo')
                                    <div class="gallery-item lightbox-trigger"
                                         data-image="{{ asset('storage/' . $item->file_path) }}"
                                         data-title="{{ $item->file_name ?? basename($item->file_path) }}">
                                        <img src="{{ asset('storage/' . $item->file_path) }}" alt="case media">
                                        <div class="gallery-caption">
                                            {{ $item->file_name ?? 'View Image' }}
                                        </div>
                                    </div>
                                @else
                                    <div class="video-card">
                                        <div>
                                            <div class="fw-bold text-dark mb-2">
                                                <i class="bi bi-play-circle-fill text-warning me-1"></i>
                                                Video File
                                            </div>
                                            <div class="text-muted small">
                                                {{ $item->file_name ?? basename($item->file_path) }}
                                            </div>
                                        </div>

                                        <a href="{{ asset('storage/' . $item->file_path) }}"
                                           target="_blank"
                                           class="btn btn-outline-dark rounded-pill mt-3">
                                            Open Video
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="empty-note">No media uploaded for this case.</div>
                    @endif
                </div>
            </div>

            <div class="content-card">
                <div class="card-head">
                    <h3 class="card-title">Timeline Logs</h3>
                    <p class="card-subtitle">Track complete case activity history.</p>
                </div>

                <div class="card-body">
                    @if($logs->count())
                        <div class="timeline">
                            @foreach($logs as $log)
                                <div class="timeline-item">
                                    <div class="timeline-title">
                                        {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                    </div>

                                    <div class="timeline-meta">
                                        By:
                                        {{ $log->user?->name ?? $log->user_name ?? 'System / Admin' }}
                                        |
                                        {{ $log->created_at ? $log->created_at->format('d M Y, h:i A') : '-' }}
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
                            @endforeach
                        </div>
                    @else
                        <div class="empty-note">No timeline logs found.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sidebar-stack">

                <div class="content-card">
                    <div class="card-head">
                        <h3 class="card-title">Quick Status Update</h3>
                        <p class="card-subtitle">Change case progress directly from this panel.</p>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.report-cases.update-status', $report->id) }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">New Status</label>
                                <select name="status" class="form-select" required>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" @selected($report->status === $status)>
                                            {{ ucwords(str_replace('_', ' ', $status)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" rows="4" class="form-control" placeholder="Write admin note or reason..."></textarea>
                            </div>

                            <button class="btn submit-btn">
                                <i class="bi bi-arrow-repeat me-1"></i>
                                Update Status
                            </button>
                        </form>
                    </div>
                </div>

                <div class="content-card">
                    <div class="card-head">
                        <h3 class="card-title">Assign Primary Handler</h3>
                        <p class="card-subtitle">Select the responder responsible for this case.</p>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.report-cases.assign-handler', $report->id) }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Select Handler</label>
                                <select name="user_id" class="form-select" required>
                                    <option value="">Choose responder</option>
                                    @foreach($responders as $responder)
                                        <option value="{{ $responder->id }}" @selected($report->current_handler_id == $responder->id)>
                                            {{ $responder->name }} {{ $responder->mobile ? '(' . $responder->mobile . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" rows="3" class="form-control" placeholder="Assignment note..."></textarea>
                            </div>

                            <button class="btn submit-btn">
                                <i class="bi bi-person-check me-1"></i>
                                Assign Handler
                            </button>
                        </form>
                    </div>
                </div>

                <div class="content-card">
                    <div class="card-head">
                        <h3 class="card-title">Assigned Responders</h3>
                        <p class="card-subtitle">Responder list connected with this case.</p>
                    </div>

                    <div class="card-body">
                        @forelse($assignments as $assign)
                            <div class="assigned-item">
                                <div class="assigned-name">
                                    {{ $assign->user?->name ?? $assign->user_name ?? 'Unknown User' }}
                                </div>

                                <div class="assigned-meta">
                                    {{ $assign->user?->mobile ?? $assign->user_mobile ?? '-' }}
                                </div>

                                <div class="small mb-1">
                                    Role:
                                    <strong>{{ ucwords(str_replace('_', ' ', $assign->assignment_role)) }}</strong>
                                </div>

                                <div class="small">
                                    Status:
                                    <strong>{{ ucwords(str_replace('_', ' ', $assign->status)) }}</strong>
                                </div>
                            </div>
                        @empty
                            <div class="empty-note">No assigned responder found.</div>
                        @endforelse
                    </div>
                </div>

                <div class="content-card">
                    <div class="card-head">
                        <h3 class="card-title">Important Time Info</h3>
                        <p class="card-subtitle">Important milestone timestamps for this case.</p>
                    </div>

                    <div class="card-body">
                        <div class="time-list">
                            <div class="time-item">
                                <div class="label">Created At</div>
                                <div class="value">
                                    {{ $report->created_at ? $report->created_at->format('d M Y, h:i A') : '-' }}
                                </div>
                            </div>

                            <div class="time-item">
                                <div class="label">Accepted At</div>
                                <div class="value">
                                    {{ $report->accepted_at ? $report->accepted_at->format('d M Y, h:i A') : '-' }}
                                </div>
                            </div>

                            <div class="time-item">
                                <div class="label">En Route At</div>
                                <div class="value">
                                    {{ $report->en_route_at ? $report->en_route_at->format('d M Y, h:i A') : '-' }}
                                </div>
                            </div>

                            <div class="time-item">
                                <div class="label">Reached At</div>
                                <div class="value">
                                    {{ $report->reached_at ? $report->reached_at->format('d M Y, h:i A') : '-' }}
                                </div>
                            </div>

                            <div class="time-item">
                                <div class="label">Rescue Started At</div>
                                <div class="value">
                                    {{ $report->rescue_started_at ? $report->rescue_started_at->format('d M Y, h:i A') : '-' }}
                                </div>
                            </div>

                            <div class="time-item">
                                <div class="label">Resolved At</div>
                                <div class="value">
                                    {{ $report->resolved_at ? $report->resolved_at->format('d M Y, h:i A') : '-' }}
                                </div>
                            </div>

                            <div class="time-item">
                                <div class="label">Closed At</div>
                                <div class="value">
                                    {{ $report->closed_at ? $report->closed_at->format('d M Y, h:i A') : '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="imageLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark border-0 rounded-4">
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
    document.querySelectorAll('.lightbox-trigger').forEach(function (item) {
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