@extends('admin.layouts.app')

@section('title', 'Create Cow Condition')
@section('header_title', 'Create Cow Condition')
@section('header_subtitle', 'Add cow health or emergency condition')

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
                <i class="bi bi-heart-pulse me-2"></i>Create Cow Condition
            </h4>
            <p class="mb-0 text-muted">Example: Bleeding, Fracture, Weakness, Pregnant Cow, Road Accident</p>
        </div>

        <a href="{{ route('admin.cow-conditions.index') }}" class="btn btn-light-custom">
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

    <form action="{{ route('admin.cow-conditions.store') }}" method="POST">
        @csrf

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Report Type</label>
                <select name="report_type_id" class="form-select">
                    <option value="">Select Report Type</option>
                    @foreach($reportTypes as $reportType)
                        <option value="{{ $reportType->id }}" {{ old('report_type_id') == $reportType->id ? 'selected' : '' }}>
                            {{ $reportType->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Condition Name <span class="text-danger">*</span></label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       class="form-control"
                       placeholder="Example: Heavy Bleeding"
                       required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Severity Level <span class="text-danger">*</span></label>
                <select name="severity_level" class="form-select" required>
                    <option value="low" {{ old('severity_level') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('severity_level', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('severity_level') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="critical" {{ old('severity_level') == 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Icon Class</label>
                <input type="text"
                       name="icon_class"
                       value="{{ old('icon_class', 'bi bi-heart-pulse') }}"
                       class="form-control"
                       placeholder="bi bi-heart-pulse">
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
                <label class="form-label">Symptoms</label>
                <textarea name="symptoms"
                          class="form-control"
                          rows="3"
                          placeholder="Write visible symptoms">{{ old('symptoms') }}</textarea>
            </div>

            <div class="col-md-12">
                <label class="form-label">First Aid Steps</label>
                <textarea name="first_aid_steps"
                          class="form-control"
                          rows="4"
                          placeholder="Write first aid or rescue steps">{{ old('first_aid_steps') }}</textarea>
            </div>

            <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea name="description"
                          class="form-control"
                          rows="3"
                          placeholder="Write extra details">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-gaumitra">
                <i class="bi bi-save me-1"></i> Save Cow Condition
            </button>

            <a href="{{ route('admin.cow-conditions.index') }}" class="btn btn-light-custom">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection