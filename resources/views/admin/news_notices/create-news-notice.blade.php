@extends('admin.layouts.app')

@section('title', 'Create News & Notice')
@section('header_title', 'Create News & Notice')
@section('header_subtitle', 'Add important updates for GauMitra users')

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

    .gm-input-icon {
        width: 44px;
        height: 44px;
        border-radius: 15px;
        background: linear-gradient(135deg, #fff3db, #ffe0ad);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--gm-deep-saffron);
        font-size: 20px;
        flex: 0 0 auto;
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

    .gm-alert {
        border-radius: 18px;
        border: 1px solid rgba(192, 57, 43, 0.16);
        background: rgba(192, 57, 43, 0.08);
        color: #9f2d22;
    }

    .gm-section-box {
        border: 1px solid rgba(93, 47, 18, 0.1);
        background: linear-gradient(180deg, #fffdfa, #fff7ea);
        border-radius: 24px;
        padding: 22px;
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
    }
</style>

<div class="container-fluid py-4">
    <div class="gm-page-hero">
        <div class="gm-hero-kicker">
            <i class="bi bi-megaphone"></i>
            GauMitra Communication
        </div>
        <h3 class="gm-hero-title">Create News & Notice</h3>
        <p class="gm-hero-subtitle">
            Add rescue updates, gaushala notices, legal awareness, missing cattle alerts, emergency weather alerts, or public information for GauMitra users.
        </p>
    </div>

    <div class="gm-card">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div>
                <h3 class="gm-card-title">New News / Notice</h3>
                <p class="gm-card-subtitle">Fill all required details and publish the update.</p>
            </div>

            <a href="{{ route('admin.news-notices.index') }}" class="gm-btn-light text-decoration-none">
                <i class="bi bi-list-ul me-1"></i> Manage News & Notices
            </a>
        </div>

        @if($errors->any())
            <div class="alert gm-alert mb-4">
                <strong><i class="bi bi-exclamation-triangle me-1"></i> Validation Error</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.news-notices.store') }}" method="POST">
            @csrf

            <div class="gm-section-box">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="gm-form-label">Category <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="gm-input-icon"><i class="bi bi-grid"></i></span>
                            <select name="category" class="form-select gm-form-select">
                                <option value="">Select Category</option>
                                @foreach(\App\Models\NewsNotice::categoryOptions() as $key => $label)
                                    <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="gm-form-label">Title <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="gm-input-icon"><i class="bi bi-type"></i></span>
                            <input
                                type="text"
                                name="title"
                                class="form-control gm-form-control"
                                value="{{ old('title') }}"
                                placeholder="Enter title"
                            >
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="gm-form-label">Short Description</label>
                        <textarea
                            name="short_description"
                            class="form-control gm-form-control"
                            rows="3"
                            placeholder="Write short description"
                        >{{ old('short_description') }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="gm-form-label">Full Description <span class="text-danger">*</span></label>
                        <textarea
                            name="description"
                            class="form-control gm-form-control"
                            rows="6"
                            placeholder="Write full description"
                        >{{ old('description') }}</textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="gm-form-label">Notice Date</label>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="gm-input-icon"><i class="bi bi-calendar-event"></i></span>
                            <input
                                type="date"
                                name="notice_date"
                                class="form-control gm-form-control"
                                value="{{ old('notice_date') }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="gm-form-label">Location</label>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="gm-input-icon"><i class="bi bi-geo-alt"></i></span>
                            <input
                                type="text"
                                name="location"
                                class="form-control gm-form-control"
                                value="{{ old('location') }}"
                                placeholder="Enter location"
                            >
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="gm-form-label">Priority <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="gm-input-icon"><i class="bi bi-exclamation-diamond"></i></span>
                            <select name="priority" class="form-select gm-form-select">
                                @foreach(\App\Models\NewsNotice::priorityOptions() as $key => $label)
                                    <option value="{{ $key }}" {{ old('priority', 'medium') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="gm-form-label">Contact Person</label>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="gm-input-icon"><i class="bi bi-person"></i></span>
                            <input
                                type="text"
                                name="contact_person"
                                class="form-control gm-form-control"
                                value="{{ old('contact_person') }}"
                                placeholder="Enter contact person"
                            >
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="gm-form-label">Contact Number</label>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="gm-input-icon"><i class="bi bi-telephone"></i></span>
                            <input
                                type="text"
                                name="contact_number"
                                class="form-control gm-form-control"
                                value="{{ old('contact_number') }}"
                                placeholder="Enter contact number"
                            >
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="gm-form-label">Status <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="gm-input-icon"><i class="bi bi-toggle-on"></i></span>
                            <select name="status" class="form-select gm-form-select">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-2 pt-2">
                            <button type="submit" class="gm-btn-primary">
                                <i class="bi bi-save me-1"></i> Save News & Notice
                            </button>

                            <a href="{{ route('admin.news-notices.index') }}" class="gm-btn-light text-decoration-none">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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