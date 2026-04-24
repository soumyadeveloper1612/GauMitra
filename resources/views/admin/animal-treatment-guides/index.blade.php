@extends('admin.layouts.app')

@section('title', 'Manage Treatment Guides')
@section('header_title', 'Animal Treatment Guides')
@section('header_subtitle', 'Manage medicine, rescue and recovery information for animals')

@section('content')
<div class="page-card">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="page-title">Treatment Guide List</h3>
            <p class="page-subtitle">Show all medicine and animal rescue information here.</p>
        </div>
        <a href="{{ route('admin.animal-treatment-guides.create') }}" class="btn btn-success rounded-pill px-4">
            <i class="bi bi-plus-circle me-1"></i> Add New Guide
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="border rounded-4 p-3 bg-light">
                <h6 class="mb-1">Total</h6>
                <h3 class="mb-0">{{ $stats['total'] }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="border rounded-4 p-3 bg-light">
                <h6 class="mb-1">Active</h6>
                <h3 class="mb-0 text-success">{{ $stats['active'] }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="border rounded-4 p-3 bg-light">
                <h6 class="mb-1">Inactive</h6>
                <h3 class="mb-0 text-secondary">{{ $stats['inactive'] }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="border rounded-4 p-3 bg-light">
                <h6 class="mb-1">Emergency Cases</h6>
                <h3 class="mb-0 text-danger">{{ $stats['emergency'] }}</h3>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.animal-treatment-guides.index') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Search by condition, medicine..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <select name="animal_type" class="form-select">
                <option value="">All Animals</option>
                <option value="cow" {{ request('animal_type') == 'cow' ? 'selected' : '' }}>Cow</option>
                <option value="calf" {{ request('animal_type') == 'calf' ? 'selected' : '' }}>Calf</option>
                <option value="bull" {{ request('animal_type') == 'bull' ? 'selected' : '' }}>Bull</option>
                <option value="buffalo" {{ request('animal_type') == 'buffalo' ? 'selected' : '' }}>Buffalo</option>
                <option value="other" {{ request('animal_type') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="text" name="case_type" class="form-control" placeholder="Case Type" value="{{ request('case_type') }}">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary w-100">
                <i class="bi bi-search me-1"></i> Filter
            </button>
            <a href="{{ route('admin.animal-treatment-guides.index') }}" class="btn btn-light w-100">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Animal</th>
                    <th>Case Type</th>
                    <th>Condition</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Medicines</th>
                    <th width="220">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guides as $key => $guide)
                    <tr>
                        <td>{{ $guides->firstItem() + $key }}</td>
                        <td class="text-capitalize">{{ $guide->animal_type }}</td>
                        <td>{{ $guide->case_type }}</td>
                        <td>
                            <strong>{{ $guide->condition_name }}</strong>
                            <div class="small text-muted mt-1">
                                {{ \Illuminate\Support\Str::limit($guide->symptoms, 70) }}
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $guide->priority == 'emergency' ? 'bg-danger' : 'bg-info' }}">
                                {{ ucfirst($guide->priority) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $guide->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($guide->status) }}
                            </span>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($guide->medicines, 70) }}</td>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button"
                                        class="btn btn-warning btn-sm rounded-pill editGuideBtn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editGuideModal"
                                        data-id="{{ $guide->id }}"
                                        data-animal_type="{{ $guide->animal_type }}"
                                        data-case_type="{{ $guide->case_type }}"
                                        data-condition_name="{{ $guide->condition_name }}"
                                        data-symptoms="{{ $guide->symptoms }}"
                                        data-first_aid_steps="{{ $guide->first_aid_steps }}"
                                        data-medicines="{{ $guide->medicines }}"
                                        data-dosage="{{ $guide->dosage }}"
                                        data-treatment_steps="{{ $guide->treatment_steps }}"
                                        data-recovery_steps="{{ $guide->recovery_steps }}"
                                        data-precautions="{{ $guide->precautions }}"
                                        data-vet_contact_note="{{ $guide->vet_contact_note }}"
                                        data-priority="{{ $guide->priority }}"
                                        data-status="{{ $guide->status }}"
                                        data-sort_order="{{ $guide->sort_order }}">
                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                </button>

                                <form action="{{ route('admin.animal-treatment-guides.destroy', $guide->id) }}" method="POST" class="delete-form d-inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm rounded-pill">
                                        <i class="bi bi-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No treatment guide found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $guides->links() }}
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editGuideModal" tabindex="-1" aria-labelledby="editGuideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <form id="editGuideForm" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title" id="editGuideModalLabel">Edit Treatment Guide</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Animal Type</label>
                            <select name="animal_type" id="edit_animal_type" class="form-select" required>
                                <option value="cow">Cow</option>
                                <option value="calf">Calf</option>
                                <option value="bull">Bull</option>
                                <option value="buffalo">Buffalo</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Case Type</label>
                            <input type="text" name="case_type" id="edit_case_type" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Condition Name</label>
                            <input type="text" name="condition_name" id="edit_condition_name" class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Priority</label>
                            <select name="priority" id="edit_priority" class="form-select" required>
                                <option value="normal">Normal</option>
                                <option value="emergency">Emergency</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input type="number" name="sort_order" id="edit_sort_order" class="form-control" min="0">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Symptoms</label>
                            <textarea name="symptoms" id="edit_symptoms" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">First Aid Steps</label>
                            <textarea name="first_aid_steps" id="edit_first_aid_steps" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Medicines Required</label>
                            <textarea name="medicines" id="edit_medicines" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Dosage Information</label>
                            <textarea name="dosage" id="edit_dosage" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Treatment Steps</label>
                            <textarea name="treatment_steps" id="edit_treatment_steps" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Recovery Steps</label>
                            <textarea name="recovery_steps" id="edit_recovery_steps" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Precautions</label>
                            <textarea name="precautions" id="edit_precautions" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Vet Contact Note</label>
                            <textarea name="vet_contact_note" id="edit_vet_contact_note" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">
                        <i class="bi bi-check-circle me-1"></i> Update Guide
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // success alert
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: @json(session('success')),
            timer: 2200,
            showConfirmButton: false
        });
    @endif

    // delete confirmation
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: 'This record will be deleted permanently.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // edit modal data fill
    document.querySelectorAll('.editGuideBtn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            document.getElementById('editGuideForm').action = "{{ url('admin/animal-treatment-guides/update') }}/" + id;

            document.getElementById('edit_animal_type').value = this.dataset.animal_type || '';
            document.getElementById('edit_case_type').value = this.dataset.case_type || '';
            document.getElementById('edit_condition_name').value = this.dataset.condition_name || '';
            document.getElementById('edit_symptoms').value = this.dataset.symptoms || '';
            document.getElementById('edit_first_aid_steps').value = this.dataset.first_aid_steps || '';
            document.getElementById('edit_medicines').value = this.dataset.medicines || '';
            document.getElementById('edit_dosage').value = this.dataset.dosage || '';
            document.getElementById('edit_treatment_steps').value = this.dataset.treatment_steps || '';
            document.getElementById('edit_recovery_steps').value = this.dataset.recovery_steps || '';
            document.getElementById('edit_precautions').value = this.dataset.precautions || '';
            document.getElementById('edit_vet_contact_note').value = this.dataset.vet_contact_note || '';
            document.getElementById('edit_priority').value = this.dataset.priority || 'normal';
            document.getElementById('edit_status').value = this.dataset.status || 'active';
            document.getElementById('edit_sort_order').value = this.dataset.sort_order || 0;
        });
    });
});
</script>
@endpush