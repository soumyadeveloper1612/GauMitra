@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="gaushala-page-header mb-4">
        <div>
            <h2 class="mb-1">Gaushala Registration</h2>
            <p class="mb-0">Register gaushala details, services, capacity, documents and GPS location.</p>
        </div>
        <a href="{{ route('admin.gaushalas.index') }}" class="btn btn-light header-btn">
            <i class="bi bi-list-ul me-2"></i>View All
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0 rounded-4">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Gaushala Name <span class="text-danger">*</span></label>
                        <input type="text" name="gaushala_name" class="form-control custom-input" value="{{ old('gaushala_name') }}" placeholder="Enter gaushala name">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Owner / Manager Name <span class="text-danger">*</span></label>
                        <input type="text" name="owner_manager_name" class="form-control custom-input" value="{{ old('owner_manager_name') }}" placeholder="Enter owner or manager name">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" name="mobile_number" class="form-control custom-input" value="{{ old('mobile_number') }}" placeholder="Enter mobile number" maxlength="10">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Alternate Number</label>
                        <input type="text" name="alternate_number" class="form-control custom-input" value="{{ old('alternate_number') }}" placeholder="Enter alternate number" maxlength="10">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Full Address <span class="text-danger">*</span></label>
                        <textarea name="full_address" rows="4" class="form-control custom-input" placeholder="Enter full address">{{ old('full_address') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">District <span class="text-danger">*</span></label>
                        <input type="text" name="district" class="form-control custom-input" value="{{ old('district') }}" placeholder="Enter district">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">State <span class="text-danger">*</span></label>
                        <input type="text" name="state" class="form-control custom-input" value="{{ old('state') }}" placeholder="Enter state">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Total Capacity <span class="text-danger">*</span></label>
                        <input type="number" name="total_capacity" min="0" class="form-control custom-input" value="{{ old('total_capacity') }}" placeholder="Enter total capacity">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Available Capacity <span class="text-danger">*</span></label>
                        <input type="number" name="available_capacity" min="0" class="form-control custom-input" value="{{ old('available_capacity') }}" placeholder="Enter available capacity">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label d-block">Services Available</label>
                        <div class="service-grid">
                            <label class="service-card">
                                <input type="hidden" name="rescue_vehicle" value="0">
                                <input type="checkbox" name="rescue_vehicle" value="1" {{ old('rescue_vehicle') ? 'checked' : '' }}>
                                <span>Rescue Vehicle</span>
                            </label>

                            <label class="service-card">
                                <input type="hidden" name="doctor" value="0">
                                <input type="checkbox" name="doctor" value="1" {{ old('doctor') ? 'checked' : '' }}>
                                <span>Doctor</span>
                            </label>

                            <label class="service-card">
                                <input type="hidden" name="food_support" value="0">
                                <input type="checkbox" name="food_support" value="1" {{ old('food_support') ? 'checked' : '' }}>
                                <span>Food Support</span>
                            </label>

                            <label class="service-card">
                                <input type="hidden" name="temporary_shelter" value="0">
                                <input type="checkbox" name="temporary_shelter" value="1" {{ old('temporary_shelter') ? 'checked' : '' }}>
                                <span>Temporary Shelter</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Gaushala Photo</label>
                        <input type="file" name="gaushala_photo" class="form-control custom-input file-input" accept=".jpg,.jpeg,.png,.webp">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Registration Proof / ID</label>
                        <input type="file" name="registration_proof" class="form-control custom-input file-input" accept=".jpg,.jpeg,.png,.pdf">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Working Hours</label>
                        <input type="text" name="working_hours" class="form-control custom-input" value="{{ old('working_hours') }}" placeholder="Example: 6:00 AM - 8:00 PM">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Emergency Availability <span class="text-danger">*</span></label>
                        <select name="emergency_availability" class="form-select custom-input">
                            <option value="">Select Emergency Availability</option>
                            <option value="yes" {{ old('emergency_availability') == 'yes' ? 'selected' : '' }}>Yes</option>
                            <option value="no" {{ old('emergency_availability') == 'no' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Latitude</label>
                        <input type="text" id="latitude" name="latitude" class="form-control custom-input" value="{{ old('latitude') }}" placeholder="Enter latitude">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Longitude</label>
                        <input type="text" id="longitude" name="longitude" class="form-control custom-input" value="{{ old('longitude') }}" placeholder="Enter longitude">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select custom-input">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <div class="location-box">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <button type="button" class="btn btn-location" onclick="getLocation()">
                                    <i class="bi bi-geo-alt-fill me-2"></i>Get Current GPS Location
                                </button>

                                <a href="javascript:void(0)" id="mapLink" class="btn btn-outline-primary d-none" target="_blank">
                                    <i class="bi bi-map me-2"></i>Open in Google Maps
                                </a>
                            </div>
                            <small class="text-muted d-block mt-2">
                                Click the button to automatically fetch current latitude and longitude.
                            </small>
                        </div>
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
        background: linear-gradient(135deg, #ff7a00, #ff9f43);
        border-radius: 20px;
        padding: 24px 28px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 10px 30px rgba(255, 122, 0, 0.18);
    }

    .gaushala-page-header h2 {
        font-weight: 700;
        margin: 0;
    }

    .gaushala-page-header p {
        opacity: 0.95;
    }

    .header-btn {
        border-radius: 12px;
        font-weight: 600;
    }

    .gaushala-form-card {
        background: #fff;
    }

    .gaushala-card-header {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        color: #fff;
        padding: 24px;
    }

    .icon-box {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.14);
        font-size: 24px;
    }

    .custom-input {
        height: 48px;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        box-shadow: none !important;
    }

    textarea.custom-input {
        height: auto;
    }

    .file-input {
        padding-top: 10px;
    }

    .custom-input:focus {
        border-color: #ff7a00;
        box-shadow: 0 0 0 0.15rem rgba(255, 122, 0, 0.12) !important;
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 8px;
        color: #1f2937;
    }

    .service-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 14px;
    }

    .service-card {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        font-weight: 600;
        cursor: pointer;
    }

    .service-card input[type="checkbox"] {
        width: 18px;
        height: 18px;
    }

    .location-box {
        background: #fff7ed;
        border: 1px dashed #fdba74;
        border-radius: 16px;
        padding: 18px;
    }

    .btn-location {
        background: #ff7a00;
        color: #fff;
        border-radius: 12px;
        font-weight: 600;
        border: none;
    }

    .btn-location:hover {
        background: #e86f00;
        color: #fff;
    }

    .btn-save {
        background: linear-gradient(135deg, #ff7a00, #ff9f43);
        color: #fff;
        border: none;
        border-radius: 14px;
        height: 48px;
        font-weight: 700;
    }

    .btn-save:hover {
        color: #fff;
        opacity: 0.95;
    }

    .btn-cancel {
        background: #f3f4f6;
        color: #111827;
        border: none;
        border-radius: 14px;
        height: 48px;
        font-weight: 600;
    }

    .form-footer {
        border-top: 1px solid #f1f5f9;
        padding-top: 24px;
    }
</style>

<script>
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    document.getElementById('latitude').value = lat.toFixed(7);
                    document.getElementById('longitude').value = lng.toFixed(7);

                    const mapLink = document.getElementById('mapLink');
                    mapLink.href = `https://www.google.com/maps?q=${lat},${lng}`;
                    mapLink.classList.remove('d-none');
                },
                function(error) {
                    alert('Unable to fetch location. Please allow location access.');
                }
            );
        } else {
            alert('Geolocation is not supported by this browser.');
        }
    }
</script>
@endsection