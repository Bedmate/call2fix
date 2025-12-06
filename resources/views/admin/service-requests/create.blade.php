@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h1 class="mb-4">Create Service Request</h1>

            <form action="{{ route('admin.service-requests.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- SELECT CUSTOMER --}}
                <div class="mb-3">
                    <label class="form-label">Select Customer</label>
                    <select name="user_id" id="user_id" class="form-select" required>
                        <option value="">-- Select Customer --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- SELECT PROPERTY --}}
                <div class="mb-3">
                    <label class="form-label">Select Property</label>
                    <select name="property_id" id="property_id" class="form-select" required>
                        <option value="">-- Select Customer First --</option>
                    </select>
                </div>

                {{-- CATEGORY --}}
                <div class="mb-3">
                    <label class="form-label">Service Category</label>
                    <select name="service_category_id" id="service_category_id" class="form-select">
                        <option value="">-- Optional --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- SERVICE --}}
                <div class="mb-3">
                    <label class="form-label">Service</label>
                    <select name="service_id" id="service_id" class="form-select">
                        <option value="">-- Optional --</option>
                    </select>
                </div>

                {{-- PROBLEM TITLE --}}
                <div class="mb-3">
                    <label class="form-label">Problem Title</label>
                    <input type="text" name="problem_title" class="form-control" required>
                </div>

                {{-- PROBLEM DESCRIPTION --}}
                <div class="mb-3">
                    <label class="form-label">Problem Description</label>
                    <textarea name="problem_description" rows="4" class="form-control" required></textarea>
                </div>

                {{-- INSPECTION DATE & TIME --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Inspection Date</label>
                        <input type="date" name="inspection_date" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Inspection Time</label>
                        <input type="time" name="inspection_time" class="form-control" required>
                    </div>
                </div>

                {{-- BIDDING --}}
                <h4 class="mt-4">Bidding Information (Optional)</h4>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Bidding Start Date</label>
                        <input type="date" name="bidding_start_date" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Bidding End Date</label>
                        <input type="date" name="bidding_end_date" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Bidding Start Time</label>
                        <input type="time" name="bidding_start_time" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Bidding End Time</label>
                        <input type="time" name="bidding_end_time" class="form-control">
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary">Create Service Request</button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- AJAX --}}
<script>
document.getElementById('user_id').addEventListener('change', function () {
    const userId = this.value;

    if (!userId) return;

    fetch(`/admin/get-user-properties/${userId}`)
        .then(res => res.json())
        .then(data => {
            let options = `<option value="">-- Select Property --</option>`;
            data.properties.forEach(p => {
                options += `<option value="${p.id}">
                    ${p.property_name} — ${p.property_address}
                </option>`;
            });
            document.getElementById('property_id').innerHTML = options;
        });
});

// Load services dynamically by category
document.getElementById('service_category_id').addEventListener('change', function () {
    const categoryId = this.value;

    if (!categoryId) {
        document.getElementById('service_id').innerHTML = '<option value="">-- Optional --</option>';
        return;
    }

    fetch(`/admin/get-services/${categoryId}`)
        .then(res => res.json())
        .then(data => {
            let options = `<option value="">-- Select Service --</option>`;
            data.services.forEach(s => {
                options += `<option value="${s.id}">${s.service_name}</option>`;
            });
            document.getElementById('service_id').innerHTML = options;
        });
});
</script>

@endsection
