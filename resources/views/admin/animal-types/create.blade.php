@extends('admin.layouts.app')

@section('title', 'Create Animal Type')
@section('header_title', 'Create Animal Type')
@section('header_subtitle', 'Add animal type master data')

@section('content')
<style>
    .page-card {
        background: #fffaf0;
        border: 1px solid #fde68a;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 12px 35px rgba(146, 64, 14, 0.10);
    }

    .form-label {
        font-weight: 700;
        color: #78350f;
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        border-color: #fcd34d;
        padding: 11px 14px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #d97706;
        box-shadow: 0 0 0 0.2rem rgba(217, 119, 6, 0.18);
    }

    .btn-gaumitra {
        background: linear-gradient(135deg, #92400e, #d97706);
        color: #fff;
        border: none;
        border-radius: 14px;
        padding: 11px 22px;
        font-weight: 700;
    }

    .btn-gaumitra:hover {
        color: #fff;
        background: linear-gradient(135deg, #78350f, #b45309);
    }

    .btn-light-custom {
        border-radius: 14px;
        padding: 11px 22px;
        font-weight: 700;
        border: 1px solid #fcd34d;
        background: #fff;
        color: #78350f;
    }
</style>

<div class="page-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1" style="color:#78350f;font-weight:800;">
                <i class="bi bi-gitlab me-2"></i>Create Animal Type
            </h4>
            <p class="mb-0 text-muted">Example: Cow, Bull, Calf, Buffalo, Dog, Other Animal</p>
        </div>

        <a href="{{ route('admin.animal-types.index') }}" class="btn btn-light-custom">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-3">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.animal-types.store') }}" method="POST">
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Animal Type Name <span class="text-danger">*</span></label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       class="form-control"
                       placeholder="Example: Cow"
                       required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Icon Class</label>
                <input type="text"
                       name="icon_class"
                       value="{{ old('icon_class', 'bi bi-gitlab') }}"
                       class="form-control"
                       placeholder="bi bi-gitlab">
            </div>

            <div class="col-md-3">
                <label class="form-label">Color Code</label>
                <input type="color"
                       name="color_code"
                       value="{{ old('color_code', '#b45309') }}"
                       class="form-control form-control-color w-100">
            </div>

            <div class="col-md-3">
                <label class="form-label">Sort Order</label>
                <input type="number"
                       name="sort_order"
                       value="{{ old('sort_order', 0) }}"
                       class="form-control"
                       min="0">
            </div>

            <div class="col-md-3">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea name="description"
                          class="form-control"
                          rows="4"
                          placeholder="Write short details about this animal type">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-gaumitra">
                <i class="bi bi-save me-1"></i> Save Animal Type
            </button>

            <a href="{{ route('admin.animal-types.index') }}" class="btn btn-light-custom">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection