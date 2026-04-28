@extends('admin.layouts.app')

@section('title', 'Manage News & Notices')
@section('header_title', 'Manage News & Notices')
@section('header_subtitle', 'View, edit, and delete news / notice records')

@section('content')
<style>
    :root {
        --gm-saffron: #f57c00;
        --gm-deep-saffron: #d35400;
        --gm-brown: #5d2f12;
        --gm-dark-brown: #3b1d0c;
        --gm-cream: #fff8ec;
        --gm-light: #fff3db;
        --gm-green: #2e7d32;
        --gm-red: #c0392b;
        --gm-border: rgba(93, 47, 18, 0.14);
        --gm-shadow: 0 18px 45px rgba(93, 47, 18, 0.12);
    }

    body {
        background:
            radial-gradient(circle at top left, rgba(245, 124, 0, 0.12), transparent 32%),
            linear-gradient(135deg, #fffaf0 0%, #fff3db 45%, #fff8ec 100%);
    }

    .gm-page-hero {
        position: relative;
        overflow: hidden;
        border-radius: 28px;
        padding: 26px;
        background:
            linear-gradient(135deg, rgba(93, 47, 18, 0.96), rgba(211, 84, 0, 0.94)),
            url("data:image/svg+xml,%3Csvg width='160' height='160' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' stroke='%23ffffff' stroke-opacity='.12' stroke-width='2'%3E%3Cpath d='M80 10 L95 60 L148 60 L105 91 L122 142 L80 110 L38 142 L55 91 L12 60 L65 60 Z'/%3E%3Ccircle cx='80' cy='80' r='54'/%3E%3C/g%3E%3C/svg%3E");
        color: #fff;
        box-shadow: var(--gm-shadow);
        margin-bottom: 24px;
    }

    .gm-page-hero::after {
        content: "";
        position: absolute;
        width: 220px;
        height: 220px;
        right: -70px;
        top: -70px;
        border-radius: 50%;
        background: rgba(255, 193, 7, 0.18);
    }

    .gm-hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.16);
        font-weight: 700;
        font-size: 13px;
        margin-bottom: 12px;
    }

    .gm-hero-title {
        margin: 0;
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -0.4px;
    }

    .gm-hero-subtitle {
        margin: 8px 0 0;
        color: rgba(255, 255, 255, 0.86);
        max-width: 720px;
    }

    .gm-card {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid var(--gm-border);
        border-radius: 26px;
        box-shadow: var(--gm-shadow);
        padding: 26px;
        backdrop-filter: blur(10px);
    }

    .gm-card-title {
        color: var(--gm-brown);
        font-weight: 800;
        margin-bottom: 4px;
    }

    .gm-card-subtitle {
        color: rgba(93, 47, 18, 0.68);
        margin-bottom: 0;
    }

    .gm-btn-primary {
        background: linear-gradient(135deg, var(--gm-saffron), var(--gm-deep-saffron));
        color: #fff;
        border: 0;
        border-radius: 999px;
        padding: 11px 24px;
        font-weight: 800;
        box-shadow: 0 12px 24px rgba(211, 84, 0, 0.24);
    }

    .gm-btn-primary:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 16px 28px rgba(211, 84, 0, 0.32);
    }

    .gm-btn-light {
        background: #fff7ea;
        color: var(--gm-brown);
        border: 1px solid rgba(93, 47, 18, 0.16);
        border-radius: 999px;
        padding: 11px 24px;
        font-weight: 800;
    }

    .gm-btn-light:hover {
        background: #ffe8bf;
        color: var(--gm-dark-brown);
    }

    .gm-stat-card {
        position: relative;
        overflow: hidden;
        border-radius: 26px;
        padding: 22px;
        color: #fff;
        min-height: 135px;
        box-shadow: var(--gm-shadow);
    }

    .gm-stat-card::after {
        content: "";
        position: absolute;
        width: 140px;
        height: 140px;
        right: -45px;
        bottom: -45px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
    }

    .gm-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 17px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.18);
        margin-bottom: 14px;
        font-size: 23px;
    }

    .gm-stat-card h6 {
        margin: 0;
        color: rgba(255, 255, 255, 0.82);
        font-weight: 700;
    }

    .gm-stat-card h2 {
        margin: 4px 0 0;
        font-weight: 900;
        font-size: 32px;
    }

    .gm-gradient-1 {
        background: linear-gradient(135deg, #5d2f12, #b45309);
    }

    .gm-gradient-2 {
        background: linear-gradient(135deg, #2e7d32, #8d6e00);
    }

    .gm-gradient-3 {
        background: linear-gradient(135deg, #a35a00, #f57c00);
    }

    .gm-gradient-4 {
        background: linear-gradient(135deg, #7b1f12, #c0392b);
    }

    .gm-table {
        border-collapse: separate;
        border-spacing: 0 12px;
    }

    .gm-table thead th {
        color: var(--gm-brown);
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 0;
        padding: 0 16px 8px;
        white-space: nowrap;
    }

    .gm-table tbody tr {
        background: #fffdfa;
        box-shadow: 0 10px 24px rgba(93, 47, 18, 0.08);
    }

    .gm-table tbody td {
        border-top: 1px solid rgba(93, 47, 18, 0.08);
        border-bottom: 1px solid rgba(93, 47, 18, 0.08);
        padding: 16px;
        vertical-align: middle;
    }

    .gm-table tbody td:first-child {
        border-left: 1px solid rgba(93, 47, 18, 0.08);
        border-radius: 18px 0 0 18px;
    }

    .gm-table tbody td:last-child {
        border-right: 1px solid rgba(93, 47, 18, 0.08);
        border-radius: 0 18px 18px 0;
    }

    .gm-title-text {
        color: var(--gm-brown);
        font-weight: 800;
        max-width: 260px;
    }

    .gm-small-muted {
        color: rgba(93, 47, 18, 0.62);
        font-weight: 600;
    }

    .gm-badge-category {
        background: #fff0d3;
        color: var(--gm-brown);
        border: 1px solid rgba(245, 124, 0, 0.22);
        border-radius: 999px;
        padding: 7px 11px;
        font-weight: 800;
        display: inline-block;
    }

    .gm-badge-success {
        background: rgba(46, 125, 50, 0.12);
        color: var(--gm-green);
        border-radius: 999px;
        padding: 7px 12px;
        font-weight: 800;
    }

    .gm-badge-warning {
        background: rgba(245, 124, 0, 0.14);
        color: var(--gm-deep-saffron);
        border-radius: 999px;
        padding: 7px 12px;
        font-weight: 800;
    }

    .gm-badge-danger {
        background: rgba(192, 57, 43, 0.12);
        color: var(--gm-red);
        border-radius: 999px;
        padding: 7px 12px;
        font-weight: 800;
    }

    .gm-badge-dark {
        background: rgba(59, 29, 12, 0.12);
        color: var(--gm-dark-brown);
        border-radius: 999px;
        padding: 7px 12px;
        font-weight: 800;
    }

    .gm-action-btn {
        border-radius: 999px;
        font-weight: 800;
        padding: 7px 13px;
    }

    .gm-empty {
        background: #fff7ea;
        border: 1px dashed rgba(93, 47, 18, 0.25);
        border-radius: 22px;
        padding: 32px;
        color: rgba(93, 47, 18, 0.7);
    }

    .gm-alert {
        border-radius: 18px;
        border: 1px solid rgba(192, 57, 43, 0.16);
        background: rgba(192, 57, 43, 0.08);
        color: #9f2d22;
    }

    .gm-modal .modal-content {
        border: 0;
        border-radius: 26px;
        overflow: hidden;
        box-shadow: var(--gm-shadow);
        background: #fffdfa;
    }

    .gm-modal .modal-header {
        background: linear-gradient(135deg, var(--gm-brown), var(--gm-deep-saffron));
        color: #fff;
        border: 0;
        padding: 20px 24px;
    }

    .gm-modal .modal-title {
        font-weight: 800;
    }

    .gm-modal .modal-body {
        background: #fffdfa;
        padding: 24px;
    }

    .gm-modal .modal-footer {
        background: #fff7ea;
        border-top: 1px solid rgba(93, 47, 18, 0.1);
        padding: 18px 24px;
    }

    .gm-form-label {
        color: var(--gm-brown);
        font-weight: 700;
        margin-bottom: 8px;
    }

    .gm-form-control,
    .gm-form-select {
        min-height: 48px;
        border-radius: 16px;
        border: 1px solid rgba(93, 47, 18, 0.18);
        background: #fffdfa;
        color: var(--gm-dark-brown);
        font-weight: 600;
    }

    textarea.gm-form-control {
        min-height: auto;
    }

    .gm-form-control:focus,
    .gm-form-select:focus {
        border-color: var(--gm-saffron);
        box-shadow: 0 0 0 0.22rem rgba(245, 124, 0, 0.16);
    }

    @media (max-width: 767px) {
        .gm-page-hero,
        .gm-card {
            border-radius: 22px;
            padding: 20px;
        }

        .gm-hero-title {
            font-size: 23px;
        }

        .gm-table {
            border-spacing: 0 10px;
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="gm-page-hero">
        <div class="gm-hero-kicker">
            <i class="bi bi-newspaper"></i>
            GauMitra News & Notices
        </div>
        <h3 class="gm-hero-title">Manage News & Notices</h3>
        <p class="gm-hero-subtitle">
            View, edit, and status-manage important GauMitra notices, rescue stories, awareness messages, and public alerts.
        </p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="gm-stat-card gm-gradient-1">
                <div class="gm-stat-icon">
                    <i class="bi bi-collection"></i>
                </div>
                <h6>Total Records</h6>
                <h2>{{ $totalCount }}</h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="gm-stat-card gm-gradient-2">
                <div class="gm-stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <h6>Active</h6>
                <h2>{{ $activeCount }}</h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="gm-stat-card gm-gradient-3">
                <div class="gm-stat-icon">
                    <i class="bi bi-pause-circle"></i>
                </div>
                <h6>Inactive</h6>
                <h2>{{ $inactiveCount }}</h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="gm-stat-card gm-gradient-4">
                <div class="gm-stat-icon">
                    <i class="bi bi-trash3"></i>
                </div>
                <h6>Deleted</h6>
                <h2>{{ $deletedCount }}</h2>
            </div>
        </div>
    </div>

    <div class="gm-card">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div>
                <h3 class="gm-card-title">News & Notice List</h3>
                <p class="gm-card-subtitle">Edit records in modal and delete by status update.</p>
            </div>

            <a href="{{ route('admin.news-notices.create') }}" class="gm-btn-primary text-decoration-none">
                <i class="bi bi-plus-circle me-1"></i> Add New
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success rounded-4 fw-semibold">
                <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert gm-alert fw-semibold">
                <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table gm-table align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Category</th>
                        <th>Title</th>
                        <th>Short Description</th>
                        <th>Date</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th width="190">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($newsNotices as $key => $item)
                        @php
                            $priorityBadgeClass = 'gm-badge-dark';

                            if ($item->priority == 'low') {
                                $priorityBadgeClass = 'gm-badge-dark';
                            } elseif ($item->priority == 'medium') {
                                $priorityBadgeClass = 'gm-badge-warning';
                            } elseif ($item->priority == 'high') {
                                $priorityBadgeClass = 'gm-badge-warning';
                            } elseif ($item->priority == 'urgent') {
                                $priorityBadgeClass = 'gm-badge-danger';
                            }

                            $statusBadgeClass = 'gm-badge-dark';

                            if ($item->status == 'active') {
                                $statusBadgeClass = 'gm-badge-success';
                            } elseif ($item->status == 'inactive') {
                                $statusBadgeClass = 'gm-badge-warning';
                            } elseif ($item->status == 'deleted') {
                                $statusBadgeClass = 'gm-badge-danger';
                            }
                        @endphp

                        <tr>
                            <td class="fw-bold text-muted">
                                {{ $newsNotices->firstItem() + $key }}
                            </td>

                            <td>
                                <span class="gm-badge-category">
                                    <i class="bi bi-tag me-1"></i>{{ $item->category_label }}
                                </span>
                            </td>

                            <td>
                                <div class="gm-title-text">
                                    {{ $item->title }}
                                </div>
                            </td>

                            <td>
                                <span class="gm-small-muted">
                                    {{ \Illuminate\Support\Str::limit($item->short_description, 60) ?: 'N/A' }}
                                </span>
                            </td>

                            <td>
                                <span class="fw-semibold text-dark">
                                    <i class="bi bi-calendar-event text-warning me-1"></i>
                                    {{ $item->notice_date ? $item->notice_date->format('d M Y') : 'N/A' }}
                                </span>
                            </td>

                            <td>
                                <span class="{{ $priorityBadgeClass }}">
                                    {{ ucfirst($item->priority) }}
                                </span>
                            </td>

                            <td>
                                <span class="{{ $statusBadgeClass }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>

                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary gm-action-btn editBtn"
                                        data-id="{{ $item->id }}"
                                        data-update-url="{{ route('admin.news-notices.update', $item->id) }}"
                                        data-category="{{ $item->category }}"
                                        data-title="{{ e($item->title) }}"
                                        data-short_description="{{ e($item->short_description) }}"
                                        data-description="{{ e($item->description) }}"
                                        data-notice_date="{{ $item->notice_date ? $item->notice_date->format('Y-m-d') : '' }}"
                                        data-location="{{ e($item->location) }}"
                                        data-contact_person="{{ e($item->contact_person) }}"
                                        data-contact_number="{{ e($item->contact_number) }}"
                                        data-priority="{{ $item->priority }}"
                                        data-status="{{ $item->status }}"
                                    >
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger gm-action-btn deleteBtn"
                                        data-form-id="delete-form-{{ $item->id }}"
                                    >
                                        <i class="bi bi-trash"></i> Delete
                                    </button>

                                    <form
                                        id="delete-form-{{ $item->id }}"
                                        action="{{ route('admin.news-notices.destroy', $item->id) }}"
                                        method="POST"
                                        style="display:none;"
                                    >
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="gm-empty text-center">
                                    <i class="bi bi-newspaper fs-2 d-block mb-2"></i>
                                    No records found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $newsNotices->links() }}
        </div>
    </div>
</div>

<div class="modal fade gm-modal" id="editNewsNoticeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="editNewsNoticeForm" method="POST">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square me-1"></i> Edit News & Notice
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="gm-form-label">Category</label>
                            <select name="category" id="edit_category" class="form-select gm-form-select" required>
                                @foreach(\App\Models\NewsNotice::categoryOptions() as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="gm-form-label">Title</label>
                            <input type="text" name="title" id="edit_title" class="form-control gm-form-control" required>
                        </div>

                        <div class="col-md-12">
                            <label class="gm-form-label">Short Description</label>
                            <textarea name="short_description" id="edit_short_description" class="form-control gm-form-control" rows="3"></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="gm-form-label">Description</label>
                            <textarea name="description" id="edit_description" class="form-control gm-form-control" rows="5" required></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="gm-form-label">Notice Date</label>
                            <input type="date" name="notice_date" id="edit_notice_date" class="form-control gm-form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="gm-form-label">Location</label>
                            <input type="text" name="location" id="edit_location" class="form-control gm-form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="gm-form-label">Priority</label>
                            <select name="priority" id="edit_priority" class="form-select gm-form-select" required>
                                @foreach(\App\Models\NewsNotice::priorityOptions() as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="gm-form-label">Contact Person</label>
                            <input type="text" name="contact_person" id="edit_contact_person" class="form-control gm-form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="gm-form-label">Contact Number</label>
                            <input type="text" name="contact_number" id="edit_contact_number" class="form-control gm-form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="gm-form-label">Status</label>
                            <select name="status" id="edit_status" class="form-select gm-form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="gm-btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="gm-btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Update Record
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editModalEl = document.getElementById('editNewsNoticeModal');
    const editModal = new bootstrap.Modal(editModalEl);
    const editForm = document.getElementById('editNewsNoticeForm');

    function fillEditForm(button, useOldInput = false) {
        editForm.action = button.getAttribute('data-update-url');

        document.getElementById('edit_category').value =
            useOldInput ? @json(old('category')) || button.getAttribute('data-category') : button.getAttribute('data-category');

        document.getElementById('edit_title').value =
            useOldInput ? @json(old('title')) || button.getAttribute('data-title') : button.getAttribute('data-title');

        document.getElementById('edit_short_description').value =
            useOldInput ? @json(old('short_description')) || button.getAttribute('data-short_description') : button.getAttribute('data-short_description');

        document.getElementById('edit_description').value =
            useOldInput ? @json(old('description')) || button.getAttribute('data-description') : button.getAttribute('data-description');

        document.getElementById('edit_notice_date').value =
            useOldInput ? @json(old('notice_date')) || button.getAttribute('data-notice_date') : button.getAttribute('data-notice_date');

        document.getElementById('edit_location').value =
            useOldInput ? @json(old('location')) || button.getAttribute('data-location') : button.getAttribute('data-location');

        document.getElementById('edit_contact_person').value =
            useOldInput ? @json(old('contact_person')) || button.getAttribute('data-contact_person') : button.getAttribute('data-contact_person');

        document.getElementById('edit_contact_number').value =
            useOldInput ? @json(old('contact_number')) || button.getAttribute('data-contact_number') : button.getAttribute('data-contact_number');

        document.getElementById('edit_priority').value =
            useOldInput ? @json(old('priority')) || button.getAttribute('data-priority') : button.getAttribute('data-priority');

        document.getElementById('edit_status').value =
            useOldInput ? @json(old('status')) || button.getAttribute('data-status') : button.getAttribute('data-status');
    }

    document.querySelectorAll('.editBtn').forEach(button => {
        button.addEventListener('click', function () {
            fillEditForm(this);
            editModal.show();
        });
    });

    document.querySelectorAll('.deleteBtn').forEach(button => {
        button.addEventListener('click', function () {
            const formId = this.getAttribute('data-form-id');

            Swal.fire({
                title: 'Are you sure?',
                text: 'This record will not be permanently deleted. Status will be changed to deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d35400',
                cancelButtonColor: '#5d2f12',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel',
                background: '#fff8ec',
                color: '#5d2f12'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        });
    });

    @if(session('open_edit_modal'))
        const reopenBtn = document.querySelector('.editBtn[data-id="{{ session('open_edit_modal') }}"]');
        if (reopenBtn) {
            fillEditForm(reopenBtn, true);
            editModal.show();
        }
    @endif
});
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: "{{ session('success') }}",
        confirmButtonColor: '#d35400',
        background: '#fff8ec',
        color: '#5d2f12'
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: "{{ session('error') }}",
        confirmButtonColor: '#c0392b',
        background: '#fff8ec',
        color: '#5d2f12'
    });
</script>
@endif

@if($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        text: "{{ $errors->first() }}",
        confirmButtonColor: '#c0392b',
        background: '#fff8ec',
        color: '#5d2f12'
    });
</script>
@endif
@endsection