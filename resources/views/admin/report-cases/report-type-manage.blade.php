@extends('admin.layouts.app')

@section('title', 'Manage Report Types')
@section('header_title', 'Manage Report Types')
@section('header_subtitle', 'Control Gau Mitra report categories')

@section('content')
<style>
    .page-card {
        background: #fffaf0;
        border: 1px solid #fde68a;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 12px 35px rgba(146, 64, 14, 0.10);
    }

    .btn-gaumitra {
        background: linear-gradient(135deg, #92400e, #d97706);
        color: #fff;
        border: none;
        border-radius: 14px;
        padding: 10px 18px;
        font-weight: 700;
    }

    .btn-gaumitra:hover {
        color: #fff;
        background: linear-gradient(135deg, #78350f, #b45309);
    }

    .table thead th {
        background: #78350f;
        color: #fff;
        border: none;
        white-space: nowrap;
    }

    .table tbody td {
        vertical-align: middle;
    }

    .status-badge {
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .status-active {
        background: #dcfce7;
        color: #166534;
    }

    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .icon-preview {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 20px;
    }

    .modal-content {
        border-radius: 20px;
        border: none;
    }

    .modal-header {
        background: linear-gradient(135deg, #78350f, #d97706);
        color: #fff;
        border-radius: 20px 20px 0 0;
    }
</style>

<div class="page-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1" style="color:#78350f;font-weight:800;">
                <i class="bi bi-list-check me-2"></i>Manage Report Types
            </h4>
            <p class="mb-0 text-muted">Create, update, activate, deactivate and delete report categories.</p>
        </div>

        <a href="{{ route('admin.report-types.create') }}" class="btn btn-gaumitra">
            <i class="bi bi-plus-circle me-1"></i> Add Report Type
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th width="70">Icon</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Color</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th width="180">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($reportTypes as $item)
                    <tr>
                        <td>
                            <span class="icon-preview" style="background: {{ $item->color_code }}">
                                <i class="{{ $item->icon_class ?: 'bi bi-clipboard-heart' }}"></i>
                            </span>
                        </td>

                        <td>
                            <strong>{{ $item->name }}</strong>
                            @if($item->description)
                                <div class="small text-muted">{{ Str::limit($item->description, 80) }}</div>
                            @endif
                        </td>

                        <td>{{ $item->slug }}</td>

                        <td>
                            <span style="display:inline-block;width:22px;height:22px;border-radius:6px;background:{{ $item->color_code }}"></span>
                            <span class="ms-1">{{ $item->color_code }}</span>
                        </td>

                        <td>{{ $item->sort_order }}</td>

                        <td>
                            <span class="status-badge {{ $item->status == 'active' ? 'status-active' : 'status-inactive' }}">
                                {{ $item->status }}
                            </span>
                        </td>

                        <td>
                            <button class="btn btn-sm btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $item->id }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <form action="{{ route('admin.report-types.destroy', $item->id) }}"
                                  method="POST"
                                  class="d-inline delete-form">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('admin.report-types.update', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="bi bi-pencil-square me-1"></i> Edit Report Type
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Report Type Name</label>
                                                <input type="text" name="name" value="{{ $item->name }}" class="form-control" required>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">Icon Class</label>
                                                <input type="text" name="icon_class" value="{{ $item->icon_class }}" class="form-control">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">Color Code</label>
                                                <input type="color" name="color_code" value="{{ $item->color_code }}" class="form-control form-control-color w-100">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">Sort Order</label>
                                                <input type="number" name="sort_order" value="{{ $item->sort_order }}" class="form-control" min="0">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-select">
                                                    <option value="active" {{ $item->status == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="inactive" {{ $item->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </div>

                                            <div class="col-md-12">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="4">{{ $item->description }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-gaumitra">
                                            <i class="bi bi-save me-1"></i> Update
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No report type found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    document.querySelectorAll('.delete-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: 'This report type will be deleted.',
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