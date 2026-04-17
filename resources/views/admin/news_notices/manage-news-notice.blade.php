@extends('admin.layouts.app')

@section('title', 'Manage News & Notices')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1">Manage News & Notices</h3>
            <p class="text-muted mb-0">View, edit, and delete news / notice records.</p>
        </div>
        <a href="{{ route('admin.news-notices.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add New
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Records</h6>
                    <h3 class="mb-0">{{ $totalCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Active</h6>
                    <h3 class="mb-0 text-success">{{ $activeCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Inactive</h6>
                    <h3 class="mb-0 text-warning">{{ $inactiveCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Deleted</h6>
                    <h3 class="mb-0 text-danger">{{ $deletedCount }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Title</th>
                            <th>Short Description</th>
                            <th>Date</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th width="180">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($newsNotices as $key => $item)
                            @php
                                $priorityClass = 'dark';
                                if ($item->priority == 'low') {
                                    $priorityClass = 'secondary';
                                } elseif ($item->priority == 'medium') {
                                    $priorityClass = 'info';
                                } elseif ($item->priority == 'high') {
                                    $priorityClass = 'warning';
                                } elseif ($item->priority == 'urgent') {
                                    $priorityClass = 'danger';
                                }

                                $statusClass = 'secondary';
                                if ($item->status == 'active') {
                                    $statusClass = 'success';
                                } elseif ($item->status == 'inactive') {
                                    $statusClass = 'warning';
                                }
                            @endphp

                            <tr>
                                <td>{{ $newsNotices->firstItem() + $key }}</td>
                                <td>{{ $item->category_label }}</td>
                                <td>{{ $item->title }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($item->short_description, 60) }}</td>
                                <td>{{ $item->notice_date ? $item->notice_date->format('d M Y') : 'N/A' }}</td>
                                <td><span class="badge bg-{{ $priorityClass }}">{{ ucfirst($item->priority) }}</span></td>
                                <td><span class="badge bg-{{ $statusClass }}">{{ ucfirst($item->status) }}</span></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button type="button"
                                            class="btn btn-sm btn-primary editBtn"
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
                                            data-status="{{ $item->status }}">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>

                                        <button type="button"
                                            class="btn btn-sm btn-danger deleteBtn"
                                            data-form-id="delete-form-{{ $item->id }}">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>

                                        <form id="delete-form-{{ $item->id }}"
                                            action="{{ route('admin.news-notices.destroy', $item->id) }}"
                                            method="POST" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $newsNotices->links() }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editNewsNoticeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="editNewsNoticeForm" method="POST">
            @csrf
            @method('PUT')

            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title">Edit News & Notice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category</label>
                            <select name="category" id="edit_category" class="form-select" required>
                                @foreach(\App\Models\NewsNotice::categoryOptions() as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Short Description</label>
                            <textarea name="short_description" id="edit_short_description" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="5" required></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Notice Date</label>
                            <input type="date" name="notice_date" id="edit_notice_date" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Location</label>
                            <input type="text" name="location" id="edit_location" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Priority</label>
                            <select name="priority" id="edit_priority" class="form-select" required>
                                @foreach(\App\Models\NewsNotice::priorityOptions() as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Person</label>
                            <input type="text" name="contact_person" id="edit_contact_person" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Number</label>
                            <input type="text" name="contact_number" id="edit_contact_number" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Record</button>
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
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it'
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
        text: "{{ session('success') }}"
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: "{{ session('error') }}"
    });
</script>
@endif

@if($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        text: "{{ $errors->first() }}"
    });
</script>
@endif
@endsection