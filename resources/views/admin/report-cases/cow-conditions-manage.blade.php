@extends('admin.layouts.app')

@section('title', 'Manage Cow Conditions')
@section('header_title', 'Manage Cow Conditions')
@section('header_subtitle', 'Control cow emergency condition master data')

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

    .severity-badge,
    .status-badge {
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .severity-low {
        background: #dcfce7;
        color: #166534;
    }

    .severity-medium {
        background: #fef3c7;
        color: #92400e;
    }

    .severity-high {
        background: #ffedd5;
        color: #9a3412;
    }

    .severity-critical {
        background: #fee2e2;
        color: #991b1b;
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
                <i class="bi bi-heart-pulse me-2"></i>Manage Cow Conditions
            </h4>
            <p class="mb-0 text-muted">Manage cow condition, severity, symptoms and rescue guidance.</p>
        </div>

        <a href="{{ route('admin.cow-conditions.create') }}" class="btn btn-gaumitra">
            <i class="bi bi-plus-circle me-1"></i> Add Cow Condition
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
                    <th>Condition</th>
                    <th>Report Type</th>
                    <th>Severity</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th width="180">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($cowConditions as $item)
                    <tr>
                        <td>
                            <span class="icon-preview" style="background: {{ $item->color_code }}">
                                <i class="{{ $item->icon_class ?: 'bi bi-heart-pulse' }}"></i>
                            </span>
                        </td>

                        <td>
                            <strong>{{ $item->name }}</strong>
                            <div class="small text-muted">{{ $item->slug }}</div>

                            @if($item->symptoms)
                                <div class="small text-muted mt-1">
                                    <strong>Symptoms:</strong> {{ Str::limit($item->symptoms, 80) }}
                                </div>
                            @endif
                        </td>

                        <td>
                            {{ $item->reportType?->name ?? 'Not Linked' }}
                        </td>

                        <td>
                            <span class="severity-badge severity-{{ $item->severity_level }}">
                                {{ $item->severity_level }}
                            </span>
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
                                    data-bs-target="#editConditionModal{{ $item->id }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <form action="{{ route('admin.cow-conditions.destroy', $item->id) }}"
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

                    <div class="modal fade" id="editConditionModal{{ $item->id }}" tabindex="-1">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('admin.cow-conditions.update', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="bi bi-pencil-square me-1"></i> Edit Cow Condition
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Report Type</label>
                                                <select name="report_type_id" class="form-select">
                                                    <option value="">Select Report Type</option>
                                                    @foreach($reportTypes as $reportType)
                                                        <option value="{{ $reportType->id }}" {{ $item->report_type_id == $reportType->id ? 'selected' : '' }}>
                                                            {{ $reportType->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Condition Name</label>
                                                <input type="text" name="name" value="{{ $item->name }}" class="form-control" required>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Severity Level</label>
                                                <select name="severity_level" class="form-select" required>
                                                    <option value="low" {{ $item->severity_level == 'low' ? 'selected' : '' }}>Low</option>
                                                    <option value="medium" {{ $item->severity_level == 'medium' ? 'selected' : '' }}>Medium</option>
                                                    <option value="high" {{ $item->severity_level == 'high' ? 'selected' : '' }}>High</option>
                                                    <option value="critical" {{ $item->severity_level == 'critical' ? 'selected' : '' }}>Critical</option>
                                                </select>
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
                                                <label class="form-label">Symptoms</label>
                                                <textarea name="symptoms" class="form-control" rows="3">{{ $item->symptoms }}</textarea>
                                            </div>

                                            <div class="col-md-12">
                                                <label class="form-label">First Aid Steps</label>
                                                <textarea name="first_aid_steps" class="form-control" rows="4">{{ $item->first_aid_steps }}</textarea>
                                            </div>

                                            <div class="col-md-12">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="3">{{ $item->description }}</textarea>
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
                            No cow condition found.
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
                text: 'This cow condition will be deleted.',
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