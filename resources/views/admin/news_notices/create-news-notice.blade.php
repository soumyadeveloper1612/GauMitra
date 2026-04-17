@extends('admin.layouts.app')

@section('title', 'Create News & Notice')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Create News & Notice</h3>
            <p class="text-muted mb-0">Add important updates for GauMitra users.</p>
        </div>
        <a href="{{ route('admin.news-notices.index') }}" class="btn btn-dark">
            <i class="bi bi-list-ul me-1"></i> Manage News & Notices
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.news-notices.store') }}" method="POST">
                @csrf

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select">
                            <option value="">Select Category</option>
                            @foreach(\App\Models\NewsNotice::categoryOptions() as $key => $label)
                                <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="Enter title">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Short Description</label>
                        <textarea name="short_description" class="form-control" rows="3" placeholder="Write short description">{{ old('short_description') }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Full Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="6" placeholder="Write full description">{{ old('description') }}</textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Notice Date</label>
                        <input type="date" name="notice_date" class="form-control" value="{{ old('notice_date') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Location</label>
                        <input type="text" name="location" class="form-control" value="{{ old('location') }}" placeholder="Enter location">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
                        <select name="priority" class="form-select">
                            @foreach(\App\Models\NewsNotice::priorityOptions() as $key => $label)
                                <option value="{{ $key }}" {{ old('priority', 'medium') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person') }}" placeholder="Enter contact person">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contact Number</label>
                        <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}" placeholder="Enter contact number">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Save News & Notice
                            </button>
                            <a href="{{ route('admin.news-notices.index') }}" class="btn btn-outline-secondary px-4">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: "{{ session('success') }}",
        confirmButtonColor: '#3085d6'
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: "{{ session('error') }}",
        confirmButtonColor: '#d33'
    });
</script>
@endif

@if($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        text: "{{ $errors->first() }}",
        confirmButtonColor: '#d33'
    });
</script>
@endif
@endsection