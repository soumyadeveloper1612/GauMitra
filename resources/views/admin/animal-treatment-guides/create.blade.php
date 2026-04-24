@extends('admin.layouts.app')

@section('title', 'Add Treatment Guide')
@section('header_title', 'Add Treatment Guide')
@section('header_subtitle', 'Add rescue, disease, medicine and recovery information')

@section('content')
<div class="page-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-title">Add Animal Treatment Guide</h3>
            <p class="page-subtitle">Create a complete medicine and rescue information record.</p>
        </div>
        <a href="{{ route('admin.animal-treatment-guides.index') }}" class="btn btn-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-1"></i> Back to Manage
        </a>
    </div>

    <form action="{{ route('admin.animal-treatment-guides.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Animal Type</label>
                <select name="animal_type" class="form-select" required>
                    <option value="">Select Animal</option>
                    <option value="cow" {{ old('animal_type') == 'cow' ? 'selected' : '' }}>Cow</option>
                    <option value="calf" {{ old('animal_type') == 'calf' ? 'selected' : '' }}>Calf</option>
                    <option value="bull" {{ old('animal_type') == 'bull' ? 'selected' : '' }}>Bull</option>
                    <option value="buffalo" {{ old('animal_type') == 'buffalo' ? 'selected' : '' }}>Buffalo</option>
                    <option value="other" {{ old('animal_type') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('animal_type') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Case Type</label>
                <input type="text" name="case_type" class="form-control" value="{{ old('case_type') }}" placeholder="Example: Accident, Wound, Fever, Fracture" required>
                @error('case_type') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Condition Name</label>
                <input type="text" name="condition_name" class="form-control" value="{{ old('condition_name') }}" placeholder="Example: Leg Injury After Road Accident" required>
                @error('condition_name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Priority</label>
                <select name="priority" class="form-select" required>
                    <option value="normal" {{ old('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="emergency" {{ old('priority') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select" required>
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Symptoms</label>
                <textarea name="symptoms" class="form-control" rows="3" placeholder="Enter symptoms">{{ old('symptoms') }}</textarea>
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">First Aid Steps</label>
                <textarea name="first_aid_steps" class="form-control" rows="5" placeholder="Write immediate rescue steps">{{ old('first_aid_steps') }}</textarea>
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Medicines Required</label>
                <textarea name="medicines" class="form-control" rows="4" placeholder="Write medicines name and use">{{ old('medicines') }}</textarea>
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Dosage Information</label>
                <textarea name="dosage" class="form-control" rows="3" placeholder="Write dosage instructions">{{ old('dosage') }}</textarea>
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Treatment Steps</label>
                <textarea name="treatment_steps" class="form-control" rows="5" placeholder="Write full treatment process">{{ old('treatment_steps') }}</textarea>
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Recovery Steps</label>
                <textarea name="recovery_steps" class="form-control" rows="5" placeholder="Write recovery guidance till animal becomes stable">{{ old('recovery_steps') }}</textarea>
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Precautions</label>
                <textarea name="precautions" class="form-control" rows="4" placeholder="Write important precautions">{{ old('precautions') }}</textarea>
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Vet Contact Note</label>
                <textarea name="vet_contact_note" class="form-control" rows="3" placeholder="Example: Consult veterinary doctor immediately for fractures or deep bleeding">{{ old('vet_contact_note') }}</textarea>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-success rounded-pill px-4">
                <i class="bi bi-check-circle me-1"></i> Save Guide
            </button>
            <a href="{{ route('admin.animal-treatment-guides.index') }}" class="btn btn-light rounded-pill px-4">Cancel</a>
        </div>
    </form>
</div>
@endsection