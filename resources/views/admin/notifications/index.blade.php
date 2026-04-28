@extends('admin.layouts.app')

@section('title', 'Send Notification')
@section('header_title', 'Send Notification')
@section('header_subtitle', 'Send custom, case, news, area-wise and user-wise notifications')

@section('content')
<style>
    :root {
        --gm-saffron: #f57c00;
        --gm-deep-saffron: #d35400;
        --gm-brown: #5d2f12;
        --gm-dark-brown: #3b1d0c;
        --gm-cream: #fff8ec;
        --gm-light: #fff3db;
        --gm-soft: #fff7ea;
        --gm-green: #2e7d32;
        --gm-red: #c0392b;
        --gm-blue: #1d4ed8;
        --gm-border: rgba(93, 47, 18, 0.14);
        --gm-shadow: 0 18px 45px rgba(93, 47, 18, 0.12);
    }

    body {
        background:
            radial-gradient(circle at top left, rgba(245, 124, 0, 0.13), transparent 32%),
            linear-gradient(135deg, #fffaf0 0%, #fff3db 45%, #fff8ec 100%);
    }

    .notify-hero {
        position: relative;
        overflow: hidden;
        background:
            linear-gradient(135deg, rgba(93, 47, 18, 0.97), rgba(211, 84, 0, 0.95)),
            url("data:image/svg+xml,%3Csvg width='160' height='160' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' stroke='%23ffffff' stroke-opacity='.12' stroke-width='2'%3E%3Cpath d='M80 10 L95 60 L148 60 L105 91 L122 142 L80 110 L38 142 L55 91 L12 60 L65 60 Z'/%3E%3Ccircle cx='80' cy='80' r='54'/%3E%3C/g%3E%3C/svg%3E");
        border-radius: 28px;
        padding: 28px;
        color: #fff;
        box-shadow: var(--gm-shadow);
        margin-bottom: 24px;
    }

    .notify-hero::after {
        content: "";
        position: absolute;
        width: 230px;
        height: 230px;
        right: -75px;
        top: -75px;
        border-radius: 50%;
        background: rgba(255, 193, 7, 0.18);
    }

    .notify-hero h3 {
        font-weight: 900;
        letter-spacing: -0.4px;
    }

    .notify-hero p {
        color: rgba(255, 255, 255, 0.84);
        font-weight: 500;
    }

    .notify-card {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid var(--gm-border);
        border-radius: 26px;
        box-shadow: var(--gm-shadow);
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .notify-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid rgba(93, 47, 18, 0.1);
        background: linear-gradient(135deg, #fff8ec, #fff0d3);
    }

    .notify-card-header h5 {
        color: var(--gm-brown);
        font-weight: 900;
    }

    .notify-card-body {
        padding: 24px;
    }

    .stat-box {
        background: rgba(255, 255, 255, 0.17);
        border: 1px solid rgba(255, 255, 255, 0.24);
        border-radius: 20px;
        padding: 16px;
        position: relative;
        overflow: hidden;
    }

    .stat-box::after {
        content: "";
        position: absolute;
        width: 60px;
        height: 60px;
        right: -20px;
        bottom: -20px;
        background: rgba(255, 255, 255, 0.14);
        border-radius: 50%;
    }

    .stat-box h3 {
        margin: 0;
        font-weight: 900;
        font-size: 28px;
    }

    .stat-box span {
        font-size: 13px;
        opacity: .92;
        font-weight: 700;
    }

    .section-title {
        font-weight: 900;
        color: var(--gm-brown);
        margin-bottom: 8px;
    }

    .form-label {
        font-weight: 800;
        color: var(--gm-brown);
        font-size: 13px;
        margin-bottom: 8px;
    }

    .form-control,
    .form-select {
        border-radius: 16px;
        border: 1px solid rgba(93, 47, 18, 0.18);
        min-height: 48px;
        background: #fffdfa;
        color: var(--gm-dark-brown);
        font-weight: 600;
    }

    textarea.form-control {
        min-height: auto;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--gm-saffron);
        box-shadow: 0 0 0 .22rem rgba(245, 124, 0, .16);
    }

    .target-panel {
        border: 1px dashed rgba(93, 47, 18, 0.26);
        border-radius: 22px;
        padding: 20px;
        background: linear-gradient(180deg, #fffdfa, #fff7ea);
    }

    .preview-box {
        border-radius: 22px;
        padding: 20px;
        background: linear-gradient(135deg, #fff3db, #fff8ec);
        border: 1px solid rgba(245, 124, 0, 0.22);
    }

    .preview-number {
        font-size: 30px;
        font-weight: 900;
        color: var(--gm-deep-saffron);
        line-height: 1;
    }

    .history-table {
        border-collapse: separate;
        border-spacing: 0 10px;
        padding: 0 16px 16px;
    }

    .history-table th {
        font-size: 12px;
        text-transform: uppercase;
        color: var(--gm-brown);
        background: transparent;
        border: 0;
        font-weight: 900;
        letter-spacing: 0.4px;
        padding: 14px 12px 8px;
        white-space: nowrap;
    }

    .history-table tbody tr {
        background: #fffdfa;
        box-shadow: 0 10px 24px rgba(93, 47, 18, 0.08);
    }

    .history-table tbody td {
        border-top: 1px solid rgba(93, 47, 18, 0.08);
        border-bottom: 1px solid rgba(93, 47, 18, 0.08);
        padding: 15px 12px;
        vertical-align: middle;
    }

    .history-table tbody td:first-child {
        border-left: 1px solid rgba(93, 47, 18, 0.08);
        border-radius: 18px 0 0 18px;
    }

    .history-table tbody td:last-child {
        border-right: 1px solid rgba(93, 47, 18, 0.08);
        border-radius: 0 18px 18px 0;
    }

    .badge {
        border-radius: 999px;
        padding: 7px 11px;
        font-weight: 800;
    }

    .badge-soft-success {
        background: rgba(46, 125, 50, 0.13);
        color: var(--gm-green);
    }

    .badge-soft-danger {
        background: rgba(192, 57, 43, 0.13);
        color: var(--gm-red);
    }

    .badge-soft-warning {
        background: rgba(245, 124, 0, 0.16);
        color: var(--gm-deep-saffron);
    }

    .badge-soft-info {
        background: #fff0d3;
        color: var(--gm-brown);
        border: 1px solid rgba(245, 124, 0, 0.18);
    }

    .btn-send {
        border-radius: 999px;
        padding: 12px 24px;
        font-weight: 900;
        background: linear-gradient(135deg, var(--gm-saffron), var(--gm-deep-saffron));
        border: 0;
        color: #fff;
        box-shadow: 0 12px 24px rgba(211, 84, 0, 0.24);
    }

    .btn-send:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 16px 30px rgba(211, 84, 0, 0.32);
    }

    .btn-send:disabled {
        opacity: .65;
        cursor: not-allowed;
    }

    .btn-preview {
        border-radius: 999px;
        padding: 12px 18px;
        font-weight: 900;
        color: var(--gm-green);
        border-color: rgba(46, 125, 50, 0.42);
        background: #fffdfa;
    }

    .btn-preview:hover {
        background: var(--gm-green);
        color: #fff;
        border-color: var(--gm-green);
    }

    .small-help {
        font-size: 12px;
        color: rgba(93, 47, 18, 0.66);
        font-weight: 600;
    }

    .user-result-card {
        border: 1px solid rgba(93, 47, 18, 0.12);
        border-radius: 18px;
        padding: 14px 16px;
        background: #fffdfa;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        box-shadow: 0 8px 18px rgba(93, 47, 18, 0.06);
    }

    .user-result-card:hover {
        background: #fff7ea;
        border-color: rgba(245, 124, 0, 0.32);
    }

    .user-name {
        font-weight: 900;
        color: var(--gm-brown);
    }

    .user-mobile {
        font-size: 13px;
        color: var(--gm-dark-brown);
        font-weight: 700;
    }

    .user-address {
        font-size: 12px;
        color: rgba(93, 47, 18, 0.62);
        font-weight: 600;
    }

    .user-chip {
        background: #fff0d3;
        border: 1px solid rgba(245, 124, 0, 0.28);
        color: var(--gm-brown);
        border-radius: 999px;
        padding: 9px 13px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .user-chip button {
        border: 0;
        background: transparent;
        color: var(--gm-red);
        font-weight: 900;
        line-height: 1;
        font-size: 18px;
    }

    .token-badge-ok {
        background: rgba(46, 125, 50, 0.13);
        color: var(--gm-green);
        border-radius: 999px;
        padding: 5px 9px;
        font-size: 11px;
        font-weight: 900;
    }

    .token-badge-no {
        background: rgba(192, 57, 43, 0.13);
        color: var(--gm-red);
        border-radius: 999px;
        padding: 5px 9px;
        font-size: 11px;
        font-weight: 900;
    }

    .image-preview-box img {
        width: 100%;
        max-height: 180px;
        object-fit: cover;
        border-radius: 18px;
        border: 1px solid rgba(93, 47, 18, 0.14);
        box-shadow: 0 10px 22px rgba(93, 47, 18, 0.1);
    }

    .alert {
        border-radius: 18px;
        font-weight: 700;
    }

    .alert-success {
        background: rgba(46, 125, 50, 0.12);
        border-color: rgba(46, 125, 50, 0.22);
        color: var(--gm-green);
    }

    .alert-danger {
        background: rgba(192, 57, 43, 0.1);
        border-color: rgba(192, 57, 43, 0.22);
        color: var(--gm-red);
    }

    .alert-warning {
        background: rgba(245, 124, 0, 0.12);
        border-color: rgba(245, 124, 0, 0.22);
        color: var(--gm-deep-saffron);
    }

    .alert-info {
        background: #fff0d3;
        border-color: rgba(245, 124, 0, 0.22);
        color: var(--gm-brown);
    }

    .btn-success,
    .btn-outline-success:hover {
        background: var(--gm-saffron) !important;
        border-color: var(--gm-saffron) !important;
        color: #fff !important;
    }

    .btn-outline-secondary {
        border-radius: 999px;
        font-weight: 800;
        color: var(--gm-brown);
        border-color: rgba(93, 47, 18, 0.24);
    }

    .btn-outline-secondary:hover {
        background: var(--gm-brown);
        color: #fff;
        border-color: var(--gm-brown);
    }

    .bg-light.text-dark {
        background: #fff0d3 !important;
        color: var(--gm-brown) !important;
        border: 1px solid rgba(245, 124, 0, 0.2);
    }

    a.small {
        color: var(--gm-deep-saffron);
        font-weight: 800;
        text-decoration: none;
    }

    a.small:hover {
        color: var(--gm-brown);
        text-decoration: underline;
    }

    @media (max-width: 767px) {
        .notify-hero,
        .notify-card {
            border-radius: 22px;
        }

        .notify-hero {
            padding: 22px;
        }

        .notify-card-body {
            padding: 20px;
        }

        .history-table {
            border-spacing: 0 8px;
            padding: 0 10px 10px;
        }
    }
</style>

<div class="container-fluid py-4">

    @if(session('success'))
        <div class="alert alert-success rounded-4">
            <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger rounded-4">
            <i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-4">
            <strong><i class="bi bi-exclamation-triangle me-1"></i>Please fix these errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="notify-hero">
        <div class="row align-items-center g-3">
            <div class="col-lg-6">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-3"
                     style="background: rgba(255,255,255,0.16); font-weight: 800; font-size: 13px;">
                    <i class="bi bi-bell-fill"></i>
                    GauMitra Alert System
                </div>

                <h3 class="mb-1 fw-bold">Notification Center</h3>
                <p class="mb-0 opacity-75">
                    Send alerts to all users, selected areas, or selected registered users.
                </p>
            </div>

            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="stat-box">
                            <h3>{{ $stats['total'] }}</h3>
                            <span>Total</span>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="stat-box">
                            <h3>{{ $stats['sent'] }}</h3>
                            <span>Sent</span>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="stat-box">
                            <h3>{{ $stats['partially_failed'] }}</h3>
                            <span>Partial</span>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="stat-box">
                            <h3>{{ $stats['failed'] }}</h3>
                            <span>Failed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="notificationForm"
          action="{{ route('admin.notifications.send') }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="notify-card">
                    <div class="notify-card-header">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-pencil-square me-1"></i>Create Notification
                        </h5>
                    </div>

                    <div class="notify-card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Notification Type</label>
                                <select name="notification_type" class="form-select" required>
                                    <option value="general" @selected(old('notification_type') === 'general')>General Information</option>
                                    <option value="case_report" @selected(old('notification_type') === 'case_report')>Case Report Alert</option>
                                    <option value="news_notice" @selected(old('notification_type') === 'news_notice')>News / Notice</option>
                                    <option value="rescue_alert" @selected(old('notification_type') === 'rescue_alert')>Rescue Alert</option>
                                    <option value="legal_awareness" @selected(old('notification_type') === 'legal_awareness')>Legal Awareness</option>
                                    <option value="gaushala_requirement" @selected(old('notification_type') === 'gaushala_requirement')>Gaushala Requirement</option>
                                    <option value="weather_alert" @selected(old('notification_type') === 'weather_alert')>Weather / Emergency Alert</option>
                                    <option value="custom" @selected(old('notification_type') === 'custom')>Custom</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Platform</label>
                                <select name="platform" class="form-select">
                                    <option value="">All Platforms</option>
                                    <option value="android" @selected(old('platform') === 'android')>Android</option>
                                    <option value="ios" @selected(old('platform') === 'ios')>iOS</option>
                                    <option value="web" @selected(old('platform') === 'web')>Web</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Related Content Type</label>
                                <select name="related_type" class="form-select">
                                    <option value="">No Related Content</option>
                                    <option value="custom" @selected(old('related_type') === 'custom')>Custom</option>
                                    <option value="emergency_case" @selected(old('related_type') === 'emergency_case')>Emergency Case</option>
                                    <option value="news_notice" @selected(old('related_type') === 'news_notice')>News / Notice</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Related ID</label>
                                <input type="number"
                                       name="related_id"
                                       class="form-control"
                                       value="{{ old('related_id') }}"
                                       placeholder="Example: Case ID or News ID">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Notification Title</label>
                                <input type="text"
                                       name="title"
                                       class="form-control"
                                       value="{{ old('title') }}"
                                       maxlength="150"
                                       required
                                       placeholder="Example: Injured cow reported near Grand Road">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Notification Message</label>
                                <textarea name="message"
                                          class="form-control"
                                          rows="5"
                                          maxlength="500"
                                          required
                                          placeholder="Write the notification message here...">{{ old('message') }}</textarea>
                                <div class="small-help mt-1">Maximum 500 characters recommended for mobile notification.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Upload Notification Photo</label>
                                <input type="file"
                                       name="notification_image"
                                       id="notificationImage"
                                       class="form-control"
                                       accept="image/png,image/jpeg,image/jpg,image/webp">
                                <div class="small-help mt-1">
                                    Optional. JPG, PNG, WEBP allowed. Max 2MB.
                                </div>

                                <div id="imagePreviewWrap" class="image-preview-box mt-2" style="display:none;">
                                    <img id="imagePreview" src="" alt="Notification image preview">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Image URL</label>
                                <input type="url"
                                       name="image_url"
                                       class="form-control"
                                       value="{{ old('image_url') }}"
                                       placeholder="Optional image URL">
                                <div class="small-help mt-1">
                                    Use this only if image is already hosted online.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Action URL / Deep Link</label>
                                <input type="text"
                                       name="action_url"
                                       class="form-control"
                                       value="{{ old('action_url') }}"
                                       placeholder="Example: gaumitra://case/12">
                            </div>

                        </div>
                    </div>
                </div>

                <div class="notify-card mt-4">
                    <div class="notify-card-header">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-people-fill me-1"></i>Target Audience
                        </h5>
                    </div>

                    <div class="notify-card-body">
                        <div class="target-panel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Send Target</label>
                                    <select name="target_scope" id="targetScope" class="form-select" required>
                                        <option value="all" @selected(old('target_scope') === 'all')>All Active Users</option>
                                        <option value="area" @selected(old('target_scope') === 'area')>Area-wise Users</option>
                                        <option value="users" @selected(old('target_scope') === 'users')>Selected Registered Users</option>
                                    </select>
                                </div>

                                <div class="col-md-6 d-flex align-items-end">
                                    <button type="button" id="previewBtn" class="btn btn-outline-success btn-preview w-100">
                                        <i class="bi bi-eye me-1"></i> Preview Target Users
                                    </button>
                                </div>
                            </div>

                            <div id="areaFilters" class="row g-3 mt-2" style="display:none;">
                                <div class="col-md-4">
                                    <label class="form-label">State</label>
                                    <select name="state" id="stateSelect" class="form-select">
                                        <option value="">All States</option>
                                        @foreach($states as $item)
                                            <option value="{{ $item }}" @selected(old('state') == $item)>
                                                {{ $item }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">District</label>
                                    <select name="district" id="districtSelect" class="form-select">
                                        <option value="">All Districts</option>
                                        @foreach($districts as $item)
                                            <option value="{{ $item }}" @selected(old('district') == $item)>
                                                {{ $item }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Area Name</label>
                                    <select name="area_name" id="areaSelect" class="form-select">
                                        <option value="">All Areas</option>
                                        @foreach($areas as $item)
                                            <option value="{{ $item }}" @selected(old('area_name') == $item)>
                                                {{ $item }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div id="userFilters" class="mt-3" style="display:none;">
                                <label class="form-label">Search Registered User</label>

                                <div class="input-group">
                                    <input type="text"
                                           id="userSearchInput"
                                           class="form-control"
                                           placeholder="Search by user name or mobile number">
                                    <button type="button" id="clearUserSearchBtn" class="btn btn-outline-secondary">
                                        Clear
                                    </button>
                                </div>

                                <div class="small-help mt-1">
                                    Search by registered mobile number or name. Users without active Firebase token cannot receive notifications.
                                </div>

                                <div id="userSearchResults" class="mt-3"></div>

                                <div id="selectedUsersBox" class="mt-3" style="display:none;">
                                    <div class="section-title mb-2">Selected Users</div>
                                    <div id="selectedUsersList" class="d-flex flex-wrap gap-2"></div>
                                    <div id="selectedUsersInputs"></div>
                                </div>
                            </div>
                        </div>

                        <div id="previewResult" class="preview-box mt-3" style="display:none;">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="preview-number" id="previewUsers">0</div>
                                    <div class="small-help">Users</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="preview-number" id="previewDevices">0</div>
                                    <div class="small-help">Devices</div>
                                </div>
                                <div class="col-md-2">
                                    <div class="preview-number" id="previewAndroid">0</div>
                                    <div class="small-help">Android</div>
                                </div>
                                <div class="col-md-2">
                                    <div class="preview-number" id="previewIos">0</div>
                                    <div class="small-help">iOS</div>
                                </div>
                                <div class="col-md-2">
                                    <div class="preview-number" id="previewWeb">0</div>
                                    <div class="small-help">Web</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" id="sendBtn" class="btn btn-send">
                                <i class="bi bi-send-fill me-1"></i> Send Notification Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="notify-card">
                    <div class="notify-card-header">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-bullseye me-1"></i>Target Options
                        </h5>
                    </div>
                    <div class="notify-card-body">
                        <div class="mb-3">
                            <div class="section-title">All Active Users</div>
                            <p class="small-help mb-0">
                                Send notification to all users who have active Firebase device token.
                            </p>
                        </div>

                        <div class="mb-3">
                            <div class="section-title">Area-wise Users</div>
                            <p class="small-help mb-0">
                                Select State, District, or Area Name from saved user addresses only.
                            </p>
                        </div>

                        <div class="mb-3">
                            <div class="section-title">Selected Registered Users</div>
                            <p class="small-help mb-0">
                                Search user by name or mobile number and send custom notification to selected users.
                            </p>
                        </div>

                        <div>
                            <div class="section-title">Photo Notification</div>
                            <p class="small-help mb-0">
                                Upload a photo or paste an image URL to send rich notification content.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="notify-card mt-4">
                    <div class="notify-card-header">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-shield-check me-1"></i>Before Sending
                        </h5>
                    </div>
                    <div class="notify-card-body">
                        <ul class="small-help mb-0">
                            <li>Preview target users before sending.</li>
                            <li>Use area-wise filter for emergency alerts.</li>
                            <li>Use selected user filter for personal/custom alerts.</li>
                            <li>Make sure users have active Firebase token.</li>
                            <li>Click send only once; button locks while sending.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="notify-card mt-4">
        <div class="notify-card-header">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-clock-history me-1"></i>Notification History
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 history-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Title</th>
                        <th>Target</th>
                        <th>Users</th>
                        <th>Devices</th>
                        <th>Success</th>
                        <th>Failed</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($campaigns as $campaign)
                        <tr>
                            <td>{{ $campaign->created_at?->format('d M Y, h:i A') }}</td>
                            <td>
                                <span class="badge badge-soft-info">
                                    {{ str_replace('_', ' ', ucfirst($campaign->notification_type)) }}
                                </span>
                            </td>
                            <td>
                                <strong style="color: var(--gm-brown);">{{ $campaign->title }}</strong>
                                <div class="small-help">{{ Str::limit($campaign->message, 70) }}</div>

                                @if($campaign->image_url)
                                    <div class="mt-1">
                                        <a href="{{ $campaign->image_url }}" target="_blank" class="small">
                                            View image
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ ucfirst($campaign->target_scope) }}
                                </span>
                            </td>
                            <td>{{ $campaign->total_users }}</td>
                            <td>{{ $campaign->total_devices }}</td>
                            <td class="text-success fw-bold">{{ $campaign->success_count }}</td>
                            <td class="text-danger fw-bold">{{ $campaign->failure_count }}</td>
                            <td>
                                @php
                                    $statusClass = match($campaign->status) {
                                        'sent' => 'badge-soft-success',
                                        'failed' => 'badge-soft-danger',
                                        'partially_failed' => 'badge-soft-warning',
                                        'sending' => 'badge-soft-info',
                                        default => 'bg-secondary'
                                    };
                                @endphp

                                <span class="badge {{ $statusClass }}">
                                    {{ str_replace('_', ' ', ucfirst($campaign->status)) }}
                                </span>

                                @if($campaign->error_message)
                                    <div class="small-help text-danger mt-1">
                                        {{ Str::limit($campaign->error_message, 80) }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-bell-slash fs-2 d-block mb-2"></i>
                                No notification history found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($campaigns->hasPages())
            <div class="p-3">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const targetScope = document.getElementById('targetScope');
    const areaFilters = document.getElementById('areaFilters');
    const userFilters = document.getElementById('userFilters');
    const previewBtn = document.getElementById('previewBtn');
    const sendBtn = document.getElementById('sendBtn');
    const form = document.getElementById('notificationForm');
    const previewResult = document.getElementById('previewResult');

    const stateSelect = document.getElementById('stateSelect');
    const districtSelect = document.getElementById('districtSelect');
    const areaSelect = document.getElementById('areaSelect');

    const userSearchInput = document.getElementById('userSearchInput');
    const userSearchResults = document.getElementById('userSearchResults');
    const selectedUsersBox = document.getElementById('selectedUsersBox');
    const selectedUsersList = document.getElementById('selectedUsersList');
    const selectedUsersInputs = document.getElementById('selectedUsersInputs');
    const clearUserSearchBtn = document.getElementById('clearUserSearchBtn');

    const notificationImage = document.getElementById('notificationImage');
    const imagePreviewWrap = document.getElementById('imagePreviewWrap');
    const imagePreview = document.getElementById('imagePreview');

    let selectedUsers = {};
    let searchTimer = null;
    let isSubmitting = false;

    if (notificationImage) {
        notificationImage.addEventListener('change', function () {
            const file = this.files && this.files[0];

            if (!file) {
                imagePreviewWrap.style.display = 'none';
                imagePreview.src = '';
                return;
            }

            imagePreview.src = URL.createObjectURL(file);
            imagePreviewWrap.style.display = 'block';
        });
    }

    function toggleTargetPanels() {
        areaFilters.style.display = 'none';
        userFilters.style.display = 'none';

        if (targetScope.value === 'area') {
            areaFilters.style.display = 'flex';
        }

        if (targetScope.value === 'users') {
            userFilters.style.display = 'block';
        }

        previewResult.style.display = 'none';
    }

    function buildPayloadFromForm() {
        const formData = new FormData(form);
        const payload = {};

        formData.forEach((value, key) => {
            if (value instanceof File) {
                return;
            }

            if (key.endsWith('[]')) {
                const cleanKey = key.replace('[]', '');

                if (!payload[cleanKey]) {
                    payload[cleanKey] = [];
                }

                payload[cleanKey].push(value);
            } else {
                payload[key] = value;
            }
        });

        return payload;
    }

    function updateSelectOptions(selectElement, options, placeholder) {
        selectElement.innerHTML = `<option value="">${placeholder}</option>`;

        options.forEach(item => {
            const option = document.createElement('option');
            option.value = item;
            option.textContent = item;
            selectElement.appendChild(option);
        });
    }

    function loadAddressOptions(column, extraParams = {}) {
        const params = new URLSearchParams();
        params.append('column', column);

        Object.keys(extraParams).forEach(key => {
            if (extraParams[key]) {
                params.append(key, extraParams[key]);
            }
        });

        return fetch(`{{ route('admin.notifications.address-options') }}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => data.data || []);
    }

    stateSelect.addEventListener('change', function () {
        const state = stateSelect.value;

        updateSelectOptions(districtSelect, [], 'Loading Districts...');
        updateSelectOptions(areaSelect, [], 'Loading Areas...');

        loadAddressOptions('district', { state: state })
            .then(options => {
                updateSelectOptions(districtSelect, options, 'All Districts');
            });

        loadAddressOptions('area_name', { state: state })
            .then(options => {
                updateSelectOptions(areaSelect, options, 'All Areas');
            });
    });

    districtSelect.addEventListener('change', function () {
        const state = stateSelect.value;
        const district = districtSelect.value;

        updateSelectOptions(areaSelect, [], 'Loading Areas...');

        loadAddressOptions('area_name', {
            state: state,
            district: district
        }).then(options => {
            updateSelectOptions(areaSelect, options, 'All Areas');
        });
    });

    function renderSelectedUsers() {
        selectedUsersList.innerHTML = '';
        selectedUsersInputs.innerHTML = '';

        const users = Object.values(selectedUsers);

        if (users.length === 0) {
            selectedUsersBox.style.display = 'none';
            return;
        }

        selectedUsersBox.style.display = 'block';

        users.forEach(user => {
            const chip = document.createElement('div');
            chip.className = 'user-chip';

            const text = document.createElement('span');
            text.textContent = `${user.name} - ${user.mobile}`;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.textContent = '×';

            removeBtn.addEventListener('click', function () {
                delete selectedUsers[user.id];
                renderSelectedUsers();
            });

            chip.appendChild(text);
            chip.appendChild(removeBtn);
            selectedUsersList.appendChild(chip);

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_user_ids[]';
            input.value = user.id;
            selectedUsersInputs.appendChild(input);
        });
    }

    function renderUserSearchResults(users) {
        userSearchResults.innerHTML = '';

        if (users.length === 0) {
            userSearchResults.innerHTML = `
                <div class="alert alert-warning rounded-4 mb-0">
                    No registered user found.
                </div>
            `;
            return;
        }

        users.forEach(user => {
            const card = document.createElement('div');
            card.className = 'user-result-card';

            const left = document.createElement('div');

            const name = document.createElement('div');
            name.className = 'user-name';
            name.textContent = user.name;

            const mobile = document.createElement('div');
            mobile.className = 'user-mobile';
            mobile.textContent = user.mobile;

            const address = document.createElement('div');
            address.className = 'user-address';
            address.textContent = user.address;

            const badgeWrap = document.createElement('div');
            badgeWrap.className = 'mt-1';

            const badge = document.createElement('span');
            badge.className = user.can_receive ? 'token-badge-ok' : 'token-badge-no';
            badge.textContent = user.can_receive
                ? `${user.active_devices_count} Active Device`
                : 'No Active Device';

            badgeWrap.appendChild(badge);

            left.appendChild(name);
            left.appendChild(mobile);
            left.appendChild(address);
            left.appendChild(badgeWrap);

            const right = document.createElement('div');

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn btn-sm btn-success rounded-pill fw-bold';
            button.textContent = selectedUsers[user.id] ? 'Selected' : 'Select';
            button.disabled = !user.can_receive || !!selectedUsers[user.id];

            button.addEventListener('click', function () {
                selectedUsers[user.id] = user;
                renderSelectedUsers();
                renderUserSearchResults(users);
            });

            right.appendChild(button);

            card.appendChild(left);
            card.appendChild(right);

            userSearchResults.appendChild(card);
        });
    }

    function searchUsers(query) {
        if (query.length < 2) {
            userSearchResults.innerHTML = '';
            return;
        }

        userSearchResults.innerHTML = `
            <div class="alert alert-info rounded-4 mb-0">
                Searching users...
            </div>
        `;

        const params = new URLSearchParams();
        params.append('q', query);

        fetch(`{{ route('admin.notifications.search-users') }}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            renderUserSearchResults(data.data || []);
        })
        .catch(() => {
            userSearchResults.innerHTML = `
                <div class="alert alert-danger rounded-4 mb-0">
                    User search failed.
                </div>
            `;
        });
    }

    userSearchInput.addEventListener('keyup', function () {
        clearTimeout(searchTimer);

        const query = this.value.trim();

        searchTimer = setTimeout(() => {
            searchUsers(query);
        }, 400);
    });

    clearUserSearchBtn.addEventListener('click', function () {
        userSearchInput.value = '';
        userSearchResults.innerHTML = '';
    });

    targetScope.addEventListener('change', toggleTargetPanels);
    toggleTargetPanels();

    previewBtn.addEventListener('click', function () {
        const payload = buildPayloadFromForm();

        previewBtn.disabled = true;
        previewBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Checking...';

        fetch("{{ route('admin.notifications.preview') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json",
                "Content-Type": "application/json"
            },
            body: JSON.stringify(payload)
        })
        .then(async response => {
            const data = await response.json();

            if (!response.ok) {
                let message = data.message || 'Preview failed';

                if (data.errors) {
                    message = Object.values(data.errors).flat().join('\n');
                }

                throw new Error(message);
            }

            return data;
        })
        .then(data => {
            document.getElementById('previewUsers').innerText = data.data.total_users;
            document.getElementById('previewDevices').innerText = data.data.total_devices;
            document.getElementById('previewAndroid').innerText = data.data.android_devices;
            document.getElementById('previewIos').innerText = data.data.ios_devices;
            document.getElementById('previewWeb').innerText = data.data.web_devices;

            previewResult.style.display = 'block';
        })
        .catch(error => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Preview Error',
                    text: error.message,
                    confirmButtonColor: '#c0392b',
                    background: '#fff8ec',
                    color: '#5d2f12'
                });
            } else {
                alert(error.message);
            }
        })
        .finally(() => {
            previewBtn.disabled = false;
            previewBtn.innerHTML = '<i class="bi bi-eye me-1"></i> Preview Target Users';
        });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (isSubmitting) {
            return;
        }

        const submitNow = () => {
            isSubmitting = true;

            if (sendBtn) {
                sendBtn.disabled = true;
                sendBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Sending...';
            }

            form.submit();
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Send notification?',
                text: 'This notification will be sent to selected users immediately.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, send now',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d35400',
                cancelButtonColor: '#5d2f12',
                background: '#fff8ec',
                color: '#5d2f12'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitNow();
                }
            });
        } else {
            if (confirm('Send notification now?')) {
                submitNow();
            }
        }
    });
});
</script>
@endsection