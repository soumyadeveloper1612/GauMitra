@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="page-top-bar mb-4">
        <div>
            <h2 class="mb-1">Gaushala List</h2>
            <p class="mb-0">Manage all registered gaushalas from one place.</p>
        </div>

        <a href="{{ route('admin.gaushalas.create') }}" class="btn btn-add-new">
            <i class="bi bi-plus-circle-fill me-2"></i>Add New Gaushala
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 gaushala-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Photo</th>
                            <th>Gaushala Details</th>
                            <th>Contact</th>
                            <th>Members</th>
                            <th>Capacity</th>
                            <th>Services</th>
                            <th>Emergency</th>
                            <th>Proof</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th width="130">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($gaushalas as $key => $gaushala)
                            <tr>
                                <td>{{ $gaushalas->firstItem() + $key }}</td>

                                <td>
                                    @if($gaushala->gaushala_photo)
                                        <img src="{{ asset('storage/' . $gaushala->gaushala_photo) }}" alt="Gaushala Photo" class="gaushala-thumb">
                                    @else
                                        <div class="no-image">No Image</div>
                                    @endif
                                </td>

                                <td>
                                    <div class="fw-bold">{{ $gaushala->gaushala_name }}</div>
                                    <div class="small text-muted mb-1">{{ $gaushala->owner_manager_name }}</div>
                                    <small class="text-muted d-block">{{ \Illuminate\Support\Str::limit($gaushala->full_address, 60) }}</small>
                                    <small class="text-muted d-block">{{ $gaushala->district }}, {{ $gaushala->state }}</small>
                                    <small class="text-muted d-block">Hours: {{ $gaushala->working_hours ?: 'N/A' }}</small>
                                </td>

                                <td>
                                    <div>{{ $gaushala->mobile_number }}</div>
                                    <small class="text-muted">{{ $gaushala->alternate_number ?: 'N/A' }}</small>
                                </td>

                                <td>
                                    @forelse($gaushala->members as $member)
                                        <div class="member-chip mb-1">
                                            <strong>{{ $member->member_name }}</strong>
                                            <small>{{ $member->member_phone }}</small>
                                        </div>
                                    @empty
                                        <span class="text-muted">No Members</span>
                                    @endforelse
                                </td>

                                <td>
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                                        {{ $gaushala->available_capacity }}/{{ $gaushala->total_capacity }}
                                    </span>
                                </td>

                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @if($gaushala->rescue_vehicle)
                                            <span class="badge service-badge">Rescue Vehicle</span>
                                        @endif

                                        @if($gaushala->doctor)
                                            <span class="badge service-badge">Doctor</span>
                                        @endif

                                        @if($gaushala->food_support)
                                            <span class="badge service-badge">Food Support</span>
                                        @endif

                                        @if($gaushala->temporary_shelter)
                                            <span class="badge service-badge">Temporary Shelter</span>
                                        @endif

                                        @if(!$gaushala->rescue_vehicle && !$gaushala->doctor && !$gaushala->food_support && !$gaushala->temporary_shelter)
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    @if($gaushala->emergency_availability === 'yes')
                                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Yes</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">No</span>
                                    @endif
                                </td>

                                <td>
                                    @if($gaushala->registration_proof)
                                        <a href="{{ asset('storage/' . $gaushala->registration_proof) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill">
                                            View Proof
                                        </a>
                                    @else
                                        <span class="text-muted">Not Uploaded</span>
                                    @endif
                                </td>

                                <td>
                                    @if($gaushala->status == 'active')
                                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Active</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">Inactive</span>
                                    @endif
                                </td>

                                <td>{{ $gaushala->created_at->format('d M Y') }}</td>

                                <td>
                                    <button type="button"
                                            class="btn btn-sm btn-warning rounded-pill"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editGaushalaModal{{ $gaushala->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <form action="{{ route('admin.gaushalas.destroy', $gaushala->id) }}"
                                          method="POST"
                                          class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-danger rounded-pill">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <div class="modal fade" id="editGaushalaModal{{ $gaushala->id }}" tabindex="-1">
                                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content border-0 rounded-4">
                                        <form action="{{ route('admin.gaushalas.update', $gaushala->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header edit-modal-header">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-pencil-square me-2"></i>Edit Gaushala
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Gaushala Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="gaushala_name" class="form-control custom-input" value="{{ $gaushala->gaushala_name }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Owner / Manager Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="owner_manager_name" class="form-control custom-input" value="{{ $gaushala->owner_manager_name }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                                        <input type="text" name="mobile_number" class="form-control custom-input" value="{{ $gaushala->mobile_number }}" maxlength="10" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Alternate Number</label>
                                                        <input type="text" name="alternate_number" class="form-control custom-input" value="{{ $gaushala->alternate_number }}" maxlength="10">
                                                    </div>

                                                    <div class="col-md-12">
                                                        <label class="form-label">Full Address <span class="text-danger">*</span></label>
                                                        <textarea name="full_address" rows="3" class="form-control custom-input" required>{{ $gaushala->full_address }}</textarea>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">District <span class="text-danger">*</span></label>
                                                        <input type="text" name="district" class="form-control custom-input" value="{{ $gaushala->district }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">State <span class="text-danger">*</span></label>
                                                        <input type="text" name="state" class="form-control custom-input" value="{{ $gaushala->state }}" required>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="section-title-modal">
                                                            <i class="bi bi-people-fill"></i>
                                                            <span>Gaushala Members</span>
                                                        </div>

                                                        <div id="editMemberWrapper{{ $gaushala->id }}">
                                                            @forelse($gaushala->members as $mIndex => $member)
                                                                <div class="member-row row g-3 align-items-end mb-3">
                                                                    <div class="col-md-5">
                                                                        <label class="form-label">Member Name <span class="text-danger">*</span></label>
                                                                        <input type="text"
                                                                               name="members[{{ $mIndex }}][name]"
                                                                               value="{{ $member->member_name }}"
                                                                               class="form-control custom-input"
                                                                               required>
                                                                    </div>

                                                                    <div class="col-md-5">
                                                                        <label class="form-label">Member Phone <span class="text-danger">*</span></label>
                                                                        <input type="text"
                                                                               name="members[{{ $mIndex }}][phone]"
                                                                               value="{{ $member->member_phone }}"
                                                                               class="form-control custom-input"
                                                                               maxlength="10"
                                                                               required>
                                                                    </div>

                                                                    <div class="col-md-2">
                                                                        <button type="button" class="btn btn-remove-member w-100" onclick="removeMemberRow(this)">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <div class="member-row row g-3 align-items-end mb-3">
                                                                    <div class="col-md-5">
                                                                        <label class="form-label">Member Name <span class="text-danger">*</span></label>
                                                                        <input type="text" name="members[0][name]" class="form-control custom-input" required>
                                                                    </div>

                                                                    <div class="col-md-5">
                                                                        <label class="form-label">Member Phone <span class="text-danger">*</span></label>
                                                                        <input type="text" name="members[0][phone]" class="form-control custom-input" maxlength="10" required>
                                                                    </div>

                                                                    <div class="col-md-2">
                                                                        <button type="button" class="btn btn-remove-member w-100" onclick="removeMemberRow(this)">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @endforelse
                                                        </div>

                                                        <button type="button"
                                                                class="btn btn-add-member"
                                                                data-wrapper="editMemberWrapper{{ $gaushala->id }}"
                                                                data-index="{{ max($gaushala->members->count(), 1) }}"
                                                                onclick="addEditMemberRow(this)">
                                                            <i class="bi bi-plus-circle me-1"></i>Add More Member
                                                        </button>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Total Capacity <span class="text-danger">*</span></label>
                                                        <input type="number" name="total_capacity" min="0" class="form-control custom-input" value="{{ $gaushala->total_capacity }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Available Capacity <span class="text-danger">*</span></label>
                                                        <input type="number" name="available_capacity" min="0" class="form-control custom-input" value="{{ $gaushala->available_capacity }}" required>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <label class="form-label d-block">Services Available</label>

                                                        <div class="service-grid">
                                                            <label class="service-card">
                                                                <input type="hidden" name="rescue_vehicle" value="0">
                                                                <input type="checkbox" name="rescue_vehicle" value="1" {{ $gaushala->rescue_vehicle ? 'checked' : '' }}>
                                                                <span>Rescue Vehicle</span>
                                                            </label>

                                                            <label class="service-card">
                                                                <input type="hidden" name="doctor" value="0">
                                                                <input type="checkbox" name="doctor" value="1" {{ $gaushala->doctor ? 'checked' : '' }}>
                                                                <span>Doctor</span>
                                                            </label>

                                                            <label class="service-card">
                                                                <input type="hidden" name="food_support" value="0">
                                                                <input type="checkbox" name="food_support" value="1" {{ $gaushala->food_support ? 'checked' : '' }}>
                                                                <span>Food Support</span>
                                                            </label>

                                                            <label class="service-card">
                                                                <input type="hidden" name="temporary_shelter" value="0">
                                                                <input type="checkbox" name="temporary_shelter" value="1" {{ $gaushala->temporary_shelter ? 'checked' : '' }}>
                                                                <span>Temporary Shelter</span>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Gaushala Photo</label>
                                                        <input type="file" name="gaushala_photo" class="form-control custom-input" accept=".jpg,.jpeg,.png,.webp">

                                                        @if($gaushala->gaushala_photo)
                                                            <small class="text-muted d-block mt-1">Current photo uploaded.</small>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Registration Proof / ID</label>
                                                        <input type="file" name="registration_proof" class="form-control custom-input" accept=".jpg,.jpeg,.png,.pdf">

                                                        @if($gaushala->registration_proof)
                                                            <small class="text-muted d-block mt-1">Current proof uploaded.</small>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Working Hours</label>
                                                        <input type="text" name="working_hours" class="form-control custom-input" value="{{ $gaushala->working_hours }}">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Emergency Availability <span class="text-danger">*</span></label>
                                                        <select name="emergency_availability" class="form-select custom-input" required>
                                                            <option value="yes" {{ $gaushala->emergency_availability == 'yes' ? 'selected' : '' }}>Yes</option>
                                                            <option value="no" {{ $gaushala->emergency_availability == 'no' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Status <span class="text-danger">*</span></label>
                                                        <select name="status" class="form-select custom-input" required>
                                                            <option value="active" {{ $gaushala->status == 'active' ? 'selected' : '' }}>Active</option>
                                                            <option value="inactive" {{ $gaushala->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-save-modal rounded-pill">
                                                    <i class="bi bi-save me-1"></i>Update Gaushala
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center py-5">
                                    <div class="text-muted">No gaushala records found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($gaushalas->count())
                <div class="p-3">
                    {{ $gaushalas->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .member-chip {
        background: #fff7ed;
        border: 1px solid #fed7aa;
        border-radius: 12px;
        padding: 7px 10px;
        display: inline-flex;
        flex-direction: column;
        color: #7c2d12;
        margin-right: 4px;
    }

    .member-chip small {
        color: #9a3412;
    }

    .page-top-bar {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        color: #fff;
        border-radius: 20px;
        padding: 24px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15);
    }

    .btn-add-new {
        background: linear-gradient(135deg, #ff7a00, #ff9f43);
        color: #fff;
        border-radius: 14px;
        padding: 12px 18px;
        font-weight: 700;
        border: none;
    }

    .btn-add-new:hover {
        color: #fff;
        opacity: .95;
    }

    .gaushala-table thead th {
        background: #fff7ed;
        color: #7c2d12;
        font-weight: 700;
        border-bottom: 1px solid #fed7aa;
        padding: 16px;
        white-space: nowrap;
    }

    .gaushala-table tbody td {
        padding: 16px;
        vertical-align: middle;
    }

    .gaushala-table tbody tr:hover {
        background: #f8fafc;
    }

    .gaushala-thumb {
        width: 62px;
        height: 62px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
    }

    .no-image {
        width: 62px;
        height: 62px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #f3f4f6;
        color: #6b7280;
        font-size: 12px;
        text-align: center;
    }

    .service-badge {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
        padding: 7px 10px;
        border-radius: 999px;
        font-weight: 600;
    }

    .edit-modal-header {
        background: linear-gradient(135deg, #78350f, #d97706);
        color: #fff;
        border-radius: 1rem 1rem 0 0;
    }

    .custom-input {
        min-height: 46px;
        border-radius: 14px;
        border: 1px solid #fcd34d;
        box-shadow: none !important;
    }

    .custom-input:focus {
        border-color: #d97706;
        box-shadow: 0 0 0 0.18rem rgba(217, 119, 6, 0.15) !important;
    }

    .form-label {
        font-weight: 700;
        color: #78350f;
    }

    .service-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        gap: 12px;
    }

    .service-card {
        border: 1px solid #fcd34d;
        border-radius: 14px;
        padding: 13px;
        background: #fff;
        color: #78350f;
        font-weight: 700;
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .member-row {
        background: #fff7ed;
        border: 1px solid #fed7aa;
        border-radius: 16px;
        padding: 12px 8px;
    }

    .btn-add-member {
        background: #fff;
        color: #92400e;
        border: 1px dashed #d97706;
        border-radius: 14px;
        padding: 9px 15px;
        font-weight: 800;
    }

    .btn-remove-member {
        background: #fee2e2;
        color: #991b1b;
        border: none;
        border-radius: 14px;
        height: 46px;
    }

    .btn-save-modal {
        background: linear-gradient(135deg, #92400e, #d97706);
        color: #fff;
        border: none;
        font-weight: 800;
        padding: 10px 20px;
    }

    .btn-save-modal:hover {
        color: #fff;
        background: linear-gradient(135deg, #78350f, #b45309);
    }

    .section-title-modal {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #78350f;
        font-size: 17px;
        font-weight: 800;
        margin: 12px 0 15px;
        padding-bottom: 10px;
        border-bottom: 1px dashed #fbbf24;
    }
</style>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: @json(session('success')),
            timer: 2200,
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
    function addEditMemberRow(button) {
        const wrapperId = button.getAttribute('data-wrapper');
        const wrapper = document.getElementById(wrapperId);

        let index = parseInt(button.getAttribute('data-index'));

        const html = `
            <div class="member-row row g-3 align-items-end mb-3">
                <div class="col-md-5">
                    <label class="form-label">Member Name <span class="text-danger">*</span></label>
                    <input type="text"
                           name="members[${index}][name]"
                           class="form-control custom-input"
                           required>
                </div>

                <div class="col-md-5">
                    <label class="form-label">Member Phone <span class="text-danger">*</span></label>
                    <input type="text"
                           name="members[${index}][phone]"
                           class="form-control custom-input"
                           maxlength="10"
                           required>
                </div>

                <div class="col-md-2">
                    <button type="button" class="btn btn-remove-member w-100" onclick="removeMemberRow(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;

        wrapper.insertAdjacentHTML('beforeend', html);
        button.setAttribute('data-index', index + 1);
    }

    function removeMemberRow(button) {
        const modalBody = button.closest('.modal-body');
        const rows = modalBody.querySelectorAll('.member-row');

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

    document.querySelectorAll('.delete-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: 'This Gaushala will be moved to deleted status.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#b91c1c',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection