@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="gaushala-page-header mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-house-heart-fill me-2"></i>Gaushala Registration
            </h2>
            <p class="mb-0">Register gaushala details, services, capacity, documents and members.</p>
        </div>

        <a href="{{ route('admin.gaushalas.index') }}" class="btn btn-light header-btn">
            <i class="bi bi-list-ul me-2"></i>View All
        </a>
    </div>

    <div class="card gaushala-form-card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header gaushala-card-header border-0">
            <div class="d-flex align-items-center">
                <div class="icon-box me-3">
                    <i class="bi bi-house-heart-fill"></i>
                </div>
                <div>
                    <h4 class="mb-1">New Gaushala Entry</h4>
                    <p class="mb-0">Fill all required details carefully.</p>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('admin.gaushalas.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="section-title">
                    <i class="bi bi-info-circle-fill"></i>
                    <span>Basic Information</span>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Gaushala Name <span class="text-danger">*</span></label>
                        <input type="text" name="gaushala_name" class="form-control custom-input"
                               value="{{ old('gaushala_name') }}" placeholder="Enter gaushala name">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Owner / Manager Name <span class="text-danger">*</span></label>
                        <input type="text" name="owner_manager_name" class="form-control custom-input"
                               value="{{ old('owner_manager_name') }}" placeholder="Enter owner or manager name">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" name="mobile_number" class="form-control custom-input"
                               value="{{ old('mobile_number') }}" placeholder="Enter mobile number" maxlength="10">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Alternate Number</label>
                        <input type="text" name="alternate_number" class="form-control custom-input"
                               value="{{ old('alternate_number') }}" placeholder="Enter alternate number" maxlength="10">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Full Address <span class="text-danger">*</span></label>
                        <textarea name="full_address" rows="4" class="form-control custom-input"
                                  placeholder="Enter full address">{{ old('full_address') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">District <span class="text-danger">*</span></label>
                        <input type="text" name="district" class="form-control custom-input"
                               value="{{ old('district') }}" placeholder="Enter district">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">State <span class="text-danger">*</span></label>
                        <input type="text" name="state" class="form-control custom-input"
                               value="{{ old('state') }}" placeholder="Enter state">
                    </div>
                </div>

                <div class="section-title mt-5">
                    <i class="bi bi-people-fill"></i>
                    <span>Gaushala Members</span>
                </div>

                <div id="memberWrapper">
                    @php
                        $oldMembers = old('members', [
                            ['name' => '', 'phone' => '']
                        ]);
                    @endphp

                    @foreach($oldMembers as $index => $member)
                        <div class="member-row row g-3 align-items-end mb-3">
                            <div class="col-md-5">
                                <label class="form-label">Member Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="members[{{ $index }}][name]"
                                       value="{{ $member['name'] ?? '' }}"
                                       class="form-control custom-input"
                                       placeholder="Enter member name">
                            </div>

                            <div class="col-md-5">
                                <label class="form-label">Member Phone <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="members[{{ $index }}][phone]"
                                       value="{{ $member['phone'] ?? '' }}"
                                       class="form-control custom-input"
                                       maxlength="10"
                                       placeholder="Enter 10 digit phone number">
                            </div>

                            <div class="col-md-2">
                                <button type="button" class="btn btn-remove-member w-100" onclick="removeMemberRow(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" class="btn btn-add-member" onclick="addMemberRow()">
                    <i class="bi bi-plus-circle me-1"></i>Add More Member
                </button>

                <div class="section-title mt-5">
                    <i class="bi bi-box-seam-fill"></i>
                    <span>Capacity & Services</span>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Total Capacity <span class="text-danger">*</span></label>
                        <input type="number" name="total_capacity" min="0" class="form-control custom-input"
                               value="{{ old('total_capacity') }}" placeholder="Enter total capacity">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Available Capacity <span class="text-danger">*</span></label>
                        <input type="number" name="available_capacity" min="0" class="form-control custom-input"
                               value="{{ old('available_capacity') }}" placeholder="Enter available capacity">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label d-block">Services Available</label>

                        <div class="service-grid">
                            <label class="service-card">
                                <input type="hidden" name="rescue_vehicle" value="0">
                                <input type="checkbox" name="rescue_vehicle" value="1" {{ old('rescue_vehicle') ? 'checked' : '' }}>
                                <span><i class="bi bi-truck-front-fill me-1"></i>Rescue Vehicle</span>
                            </label>

                            <label class="service-card">
                                <input type="hidden" name="doctor" value="0">
                                <input type="checkbox" name="doctor" value="1" {{ old('doctor') ? 'checked' : '' }}>
                                <span><i class="bi bi-heart-pulse-fill me-1"></i>Doctor</span>
                            </label>

                            <label class="service-card">
                                <input type="hidden" name="food_support" value="0">
                                <input type="checkbox" name="food_support" value="1" {{ old('food_support') ? 'checked' : '' }}>
                                <span><i class="bi bi-basket-fill me-1"></i>Food Support</span>
                            </label>

                            <label class="service-card">
                                <input type="hidden" name="temporary_shelter" value="0">
                                <input type="checkbox" name="temporary_shelter" value="1" {{ old('temporary_shelter') ? 'checked' : '' }}>
                                <span><i class="bi bi-house-check-fill me-1"></i>Temporary Shelter</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="section-title mt-5">
                    <i class="bi bi-file-earmark-image-fill"></i>
                    <span>Documents & Availability</span>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Gaushala Photo</label>
                        <input type="file" name="gaushala_photo" class="form-control custom-input file-input" accept=".jpg,.jpeg,.png,.webp">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Registration Proof / ID</label>
                        <input type="file" name="registration_proof" class="form-control custom-input file-input" accept=".jpg,.jpeg,.png,.pdf">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Working Hours</label>
                        <input type="text" name="working_hours" class="form-control custom-input"
                               value="{{ old('working_hours') }}" placeholder="Example: 6:00 AM - 8:00 PM">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Emergency Availability <span class="text-danger">*</span></label>
                        <select name="emergency_availability" class="form-select custom-input">
                            <option value="">Select Emergency Availability</option>
                            <option value="yes" {{ old('emergency_availability') == 'yes' ? 'selected' : '' }}>Yes</option>
                            <option value="no" {{ old('emergency_availability') == 'no' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select custom-input">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-footer mt-4">
                    <button type="submit" class="btn btn-save px-4">
                        <i class="bi bi-check-circle-fill me-2"></i>Save Registration
                    </button>

                    <a href="{{ route('admin.gaushalas.index') }}" class="btn btn-cancel px-4 ms-2">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .gaushala-page-header {
        background: linear-gradient(135deg, #78350f, #d97706);
        border-radius: 20px;
        padding: 24px 28px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 12px 35px rgba(146, 64, 14, 0.18);
    }

    .gaushala-page-header h2 {
        font-weight: 800;
        margin: 0;
    }

    .header-btn {
        border-radius: 14px;
        font-weight: 700;
    }

    .gaushala-form-card {
        background: #fffaf0;
        border: 1px solid #fde68a !important;
    }

    .gaushala-card-header {
        background: linear-gradient(135deg, #0f172a, #78350f);
        color: #fff;
        padding: 24px;
    }

    .icon-box {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.15);
        font-size: 26px;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #78350f;
        font-size: 18px;
        font-weight: 800;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px dashed #fbbf24;
    }

    .custom-input {
        min-height: 48px;
        border-radius: 14px;
        border: 1px solid #fcd34d;
        box-shadow: none !important;
        background: #fff;
    }

    textarea.custom-input {
        height: auto;
    }

    .file-input {
        padding-top: 10px;
    }

    .custom-input:focus {
        border-color: #d97706;
        box-shadow: 0 0 0 0.18rem rgba(217, 119, 6, 0.15) !important;
    }

    .form-label {
        font-weight: 700;
        margin-bottom: 8px;
        color: #78350f;
    }

    .service-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 14px;
    }

    .service-card {
        border: 1px solid #fcd34d;
        border-radius: 16px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        font-weight: 700;
        cursor: pointer;
        color: #78350f;
    }

    .service-card input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #d97706;
    }

    .member-row {
        background: #fff7ed;
        border: 1px solid #fed7aa;
        border-radius: 18px;
        padding: 16px 10px;
    }

    .btn-add-member {
        background: #fff;
        color: #92400e;
        border: 1px dashed #d97706;
        border-radius: 14px;
        padding: 10px 18px;
        font-weight: 800;
    }

    .btn-add-member:hover {
        background: #fffbeb;
        color: #78350f;
    }

    .btn-remove-member {
        background: #fee2e2;
        color: #991b1b;
        border: none;
        border-radius: 14px;
        height: 48px;
        font-weight: 700;
    }

    .btn-save {
        background: linear-gradient(135deg, #92400e, #d97706);
        color: #fff;
        border: none;
        border-radius: 14px;
        height: 48px;
        font-weight: 800;
    }

    .btn-save:hover {
        color: #fff;
        background: linear-gradient(135deg, #78350f, #b45309);
    }

    .btn-cancel {
        background: #f3f4f6;
        color: #111827;
        border: none;
        border-radius: 14px;
        height: 48px;
        font-weight: 700;
    }

    .form-footer {
        border-top: 1px solid #fde68a;
        padding-top: 24px;
    }
</style>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: @json(session('success')),
            timer: 2300,
            showConfirmButton: false
        });
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'error',
            title: 'Server Error',
            text: @json(session('error')),
            confirmButtonColor: '#b45309'
        });
    });
</script>
@endif

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            html: `{!! implode('<br>', $errors->all()) !!}`,
            confirmButtonColor: '#b45309'
        });
    });
</script>
@endif

<script>
    let memberIndex = {{ count(old('members', [['name' => '', 'phone' => '']])) }};

    function addMemberRow() {
        const wrapper = document.getElementById('memberWrapper');

        const html = `
            <div class="member-row row g-3 align-items-end mb-3">
                <div class="col-md-5">
                    <label class="form-label">Member Name <span class="text-danger">*</span></label>
                    <input type="text"
                           name="members[${memberIndex}][name]"
                           class="form-control custom-input"
                           placeholder="Enter member name">
                </div>

                <div class="col-md-5">
                    <label class="form-label">Member Phone <span class="text-danger">*</span></label>
                    <input type="text"
                           name="members[${memberIndex}][phone]"
                           class="form-control custom-input"
                           maxlength="10"
                           placeholder="Enter 10 digit phone number">
                </div>

                <div class="col-md-2">
                    <button type="button" class="btn btn-remove-member w-100" onclick="removeMemberRow(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;

        wrapper.insertAdjacentHTML('beforeend', html);
        memberIndex++;
    }

    function removeMemberRow(button) {
        const rows = document.querySelectorAll('.member-row');

        if (rows.length <= 1) {
            Swal.fire({
                icon: 'warning',
                title: 'Required',
                text: 'At least one Gaushala member is required.',
                confirmButtonColor: '#b45309'
            });
            return;
        }

        button.closest('.member-row').remove();
    }
</script>
@endsection