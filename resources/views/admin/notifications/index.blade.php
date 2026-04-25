@extends('admin.layouts.app')

@section('title', 'Send Notification')
@section('header_title', 'Send Notification')
@section('header_subtitle', 'Send custom, case, news and area-wise notifications')

@section('content')
<style>
    .notify-hero {
        background: linear-gradient(135deg, #0f766e, #16a34a);
        border-radius: 22px;
        padding: 24px;
        color: #fff;
        box-shadow: 0 16px 40px rgba(15, 118, 110, .22);
        margin-bottom: 22px;
    }

    .notify-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, .06);
        overflow: hidden;
    }

    .notify-card-header {
        padding: 18px 22px;
        border-bottom: 1px solid #eef2f7;
        background: #f8fafc;
    }

    .notify-card-body {
        padding: 22px;
    }

    .stat-box {
        background: rgba(255,255,255,.16);
        border: 1px solid rgba(255,255,255,.24);
        border-radius: 18px;
        padding: 16px;
    }

    .stat-box h3 {
        margin: 0;
        font-weight: 800;
        font-size: 26px;
    }

    .stat-box span {
        font-size: 13px;
        opacity: .92;
    }

    .section-title {
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 12px;
    }

    .form-label {
        font-weight: 700;
        color: #334155;
        font-size: 13px;
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        border-color: #dbe3ef;
        min-height: 44px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #16a34a;
        box-shadow: 0 0 0 .18rem rgba(22, 163, 74, .12);
    }

    .target-panel {
        border: 1px dashed #cbd5e1;
        border-radius: 18px;
        padding: 18px;
        background: #f8fafc;
    }

    .preview-box {
        border-radius: 18px;
        padding: 18px;
        background: linear-gradient(135deg, #ecfdf5, #f0fdfa);
        border: 1px solid #bbf7d0;
    }

    .preview-number {
        font-size: 28px;
        font-weight: 900;
        color: #15803d;
        line-height: 1;
    }

    .history-table th {
        font-size: 12px;
        text-transform: uppercase;
        color: #64748b;
        background: #f8fafc;
    }

    .badge-soft-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-soft-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-soft-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-soft-info {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .btn-send {
        border-radius: 14px;
        padding: 12px 22px;
        font-weight: 800;
        background: linear-gradient(135deg, #16a34a, #0f766e);
        border: 0;
        color: #fff;
    }

    .btn-send:hover {
        color: #fff;
        opacity: .95;
    }

    .btn-preview {
        border-radius: 14px;
        padding: 12px 18px;
        font-weight: 800;
    }

    .small-help {
        font-size: 12px;
        color: #64748b;
    }
</style>

<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success rounded-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger rounded-4">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-4">
            <strong>Please fix these errors:</strong>
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
                <h3 class="mb-1 fw-bold">Notification Center</h3>
                <p class="mb-0 opacity-75">
                    Send alerts to all users or target users by area, district, village, pincode and platform.
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

    <form id="notificationForm" action="{{ route('admin.notifications.send') }}" method="POST">
        @csrf

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="notify-card">
                    <div class="notify-card-header">
                        <h5 class="mb-0 fw-bold">Create Notification</h5>
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
                                <label class="form-label">Related Content Type</label>
                                <select name="related_type" class="form-select">
                                    <option value="">No Related Content</option>
                                    <option value="custom" @selected(old('related_type') === 'custom')>Custom</option>
                                    <option value="emergency_case" @selected(old('related_type') === 'emergency_case')>Emergency Case</option>
                                    <option value="news_notice" @selected(old('related_type') === 'news_notice')>News / Notice</option>
                                </select>
                                <div class="small-help mt-1">Optional. Use when notification belongs to case/news.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Related ID</label>
                                <input type="number" name="related_id" class="form-control"
                                       value="{{ old('related_id') }}"
                                       placeholder="Example: Case ID or News ID">
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

                            <div class="col-12">
                                <label class="form-label">Notification Title</label>
                                <input type="text" name="title" class="form-control"
                                       value="{{ old('title') }}"
                                       maxlength="150"
                                       required
                                       placeholder="Example: Injured cow reported near Grand Road">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Notification Message</label>
                                <textarea name="message" class="form-control"
                                          rows="5"
                                          maxlength="500"
                                          required
                                          placeholder="Write the notification message here...">{{ old('message') }}</textarea>
                                <div class="small-help mt-1">Maximum 500 characters recommended for mobile notification.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Image URL</label>
                                <input type="text" name="image_url" class="form-control"
                                       value="{{ old('image_url') }}"
                                       placeholder="Optional image URL">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Action URL / Deep Link</label>
                                <input type="text" name="action_url" class="form-control"
                                       value="{{ old('action_url') }}"
                                       placeholder="Example: gaumitra://case/12">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="notify-card mt-4">
                    <div class="notify-card-header">
                        <h5 class="mb-0 fw-bold">Target Audience</h5>
                    </div>

                    <div class="notify-card-body">
                        <div class="target-panel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Send Target</label>
                                    <select name="target_scope" id="targetScope" class="form-select" required>
                                        <option value="all" @selected(old('target_scope') === 'all')>All Active Users</option>
                                        <option value="area" @selected(old('target_scope') === 'area')>Area-wise Users</option>
                                    </select>
                                </div>

                                <div class="col-md-6 d-flex align-items-end">
                                    <button type="button" id="previewBtn" class="btn btn-outline-success btn-preview w-100">
                                        Preview Target Users
                                    </button>
                                </div>
                            </div>

                            <div id="areaFilters" class="row g-3 mt-2" style="display:none;">
                                <div class="col-md-4">
                                    <label class="form-label">State</label>
                                    <input list="stateList" name="state" class="form-control"
                                           value="{{ old('state') }}" placeholder="Select / type state">
                                    <datalist id="stateList">
                                        @foreach($states as $item)
                                            <option value="{{ $item }}"></option>
                                        @endforeach
                                    </datalist>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">District</label>
                                    <input list="districtList" name="district" class="form-control"
                                           value="{{ old('district') }}" placeholder="Select / type district">
                                    <datalist id="districtList">
                                        @foreach($districts as $item)
                                            <option value="{{ $item }}"></option>
                                        @endforeach
                                    </datalist>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">City</label>
                                    <input list="cityList" name="city" class="form-control"
                                           value="{{ old('city') }}" placeholder="Select / type city">
                                    <datalist id="cityList">
                                        @foreach($cities as $item)
                                            <option value="{{ $item }}"></option>
                                        @endforeach
                                    </datalist>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Police Station</label>
                                    <input list="policeStationList" name="police_station" class="form-control"
                                           value="{{ old('police_station') }}" placeholder="Select / type police station">
                                    <datalist id="policeStationList">
                                        @foreach($policeStations as $item)
                                            <option value="{{ $item }}"></option>
                                        @endforeach
                                    </datalist>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Village</label>
                                    <input list="villageList" name="village" class="form-control"
                                           value="{{ old('village') }}" placeholder="Select / type village">
                                    <datalist id="villageList">
                                        @foreach($villages as $item)
                                            <option value="{{ $item }}"></option>
                                        @endforeach
                                    </datalist>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Pincode</label>
                                    <input list="pincodeList" name="pincode" class="form-control"
                                           value="{{ old('pincode') }}" placeholder="Select / type pincode">
                                    <datalist id="pincodeList">
                                        @foreach($pincodes as $item)
                                            <option value="{{ $item }}"></option>
                                        @endforeach
                                    </datalist>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Area Name</label>
                                    <input list="areaList" name="area_name" class="form-control"
                                           value="{{ old('area_name') }}" placeholder="Select / type area">
                                    <datalist id="areaList">
                                        @foreach($areas as $item)
                                            <option value="{{ $item }}"></option>
                                        @endforeach
                                    </datalist>
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
                            <button type="submit" class="btn btn-send">
                                Send Notification Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="notify-card">
                    <div class="notify-card-header">
                        <h5 class="mb-0 fw-bold">Quick Message Ideas</h5>
                    </div>
                    <div class="notify-card-body">
                        <div class="mb-3">
                            <div class="section-title">Case Alert</div>
                            <p class="small-help mb-0">
                                “Emergency case reported in your area. Volunteers nearby are requested to respond.”
                            </p>
                        </div>

                        <div class="mb-3">
                            <div class="section-title">News / Notice</div>
                            <p class="small-help mb-0">
                                “New GauMitra notice published. Please check the latest update in the app.”
                            </p>
                        </div>

                        <div class="mb-3">
                            <div class="section-title">Gaushala Requirement</div>
                            <p class="small-help mb-0">
                                “Nearby gaushala requires support for fodder, medicine or rescue assistance.”
                            </p>
                        </div>

                        <div>
                            <div class="section-title">Area-wise Alert</div>
                            <p class="small-help mb-0">
                                Select district, village, pincode or police station to notify only relevant users.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="notify-card mt-4">
                    <div class="notify-card-header">
                        <h5 class="mb-0 fw-bold">Before Sending</h5>
                    </div>
                    <div class="notify-card-body">
                        <ul class="small-help mb-0">
                            <li>Use clear title and short message.</li>
                            <li>Preview target users before sending.</li>
                            <li>Use area-wise filters for emergency alerts.</li>
                            <li>Make sure users have active FCM tokens.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="notify-card mt-4">
        <div class="notify-card-header">
            <h5 class="mb-0 fw-bold">Notification History</h5>
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
                                <strong>{{ $campaign->title }}</strong>
                                <div class="small-help">{{ Str::limit($campaign->message, 70) }}</div>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const targetScope = document.getElementById('targetScope');
    const areaFilters = document.getElementById('areaFilters');
    const previewBtn = document.getElementById('previewBtn');
    const form = document.getElementById('notificationForm');
    const previewResult = document.getElementById('previewResult');

    function toggleAreaFilters() {
        if (targetScope.value === 'area') {
            areaFilters.style.display = 'flex';
        } else {
            areaFilters.style.display = 'none';
        }
    }

    toggleAreaFilters();
    targetScope.addEventListener('change', toggleAreaFilters);

    previewBtn.addEventListener('click', function () {
        const formData = new FormData(form);
        const payload = {};

        formData.forEach((value, key) => {
            payload[key] = value;
        });

        previewBtn.disabled = true;
        previewBtn.innerText = 'Checking...';

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
                    text: error.message
                });
            } else {
                alert(error.message);
            }
        })
        .finally(() => {
            previewBtn.disabled = false;
            previewBtn.innerText = 'Preview Target Users';
        });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const submitForm = () => {
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
                confirmButtonColor: '#16a34a'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitForm();
                }
            });
        } else {
            if (confirm('Send notification now?')) {
                submitForm();
            }
        }
    });
});
</script>
@endsection