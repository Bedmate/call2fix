@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Services</h1> {{-- Fixed title --}}

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Service Name</th>
                <th>Category</th> {{-- Fixed label --}}
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($services as $service)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $service->service_name }}</td>
                <td>{{ $service->category?->category_name }}</td>
                <td>
                    <button type="button" class="btn btn-primary btn-sm" 
                        data-bs-toggle="modal" 
                        data-bs-target="#editserviceModal"
                        data-service-id="{{ $service->id }}"
                        data-service-name="{{ $service->service_name }}"
                        data-service-slug="{{ $service->service_slug }}"
                        data-service-description="{{ $service->service_description }}"
                        data-category-id="{{ $service->category_id }}"
                        data-service-image="{{ $service->service_image }}">
                        Edit Service
                    </button>

                    <button type="button" class="btn btn-info btn-sm" 
                        data-bs-toggle="modal" 
                        data-bs-target="#viewServicesModal" 
                        data-service-id="{{ $service->id }}">
                        View Sub-Services
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $services->links() }}

    <!-- Edit Service Modal -->
    <div class="modal fade" id="editserviceModal" tabindex="-1" aria-labelledby="editserviceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editserviceModalLabel">Edit Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editserviceForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="service_name" class="form-label">Service Name</label>
                            <input type="text" class="form-control" id="service_name" name="service_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="service_slug" class="form-label">Slug</label>
                            <input type="text" class="form-control" id="service_slug" name="service_slug" required>
                        </div>

                        <div class="mb-3">
                            <label for="parent_service" class="form-label">Category</label>
                            <select class="form-select" id="parent_service" name="parent_service" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="service_description" class="form-label">Description</label>
                            <textarea class="form-control" id="service_description" name="service_description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="service_image" class="form-label">Image (Optional)</label>
                            <input type="file" class="form-control" id="service_image" name="service_image">
                        </div>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Services Modal -->
    <div class="modal fade" id="viewServicesModal" tabindex="-1" aria-labelledby="viewServicesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewServicesModalLabel">Sub-Services</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="services-list">
                    <p>Loading...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Populate Edit Modal
    document.getElementById('editserviceModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const serviceId = button.getAttribute('data-service-id');
        const serviceName = button.getAttribute('data-service-name');
        const serviceSlug = button.getAttribute('data-service-slug');
        const serviceDescription = button.getAttribute('data-service-description');
        const categoryId = button.getAttribute('data-category-id');

        const form = this.querySelector('form');
        form.action = "{{ route('admin.services.update', '') }}" + '/' + serviceId;

        // Fill form fields
        document.getElementById('service_name').value = serviceName || '';
        document.getElementById('service_slug').value = serviceSlug || '';
        document.getElementById('service_description').value = serviceDescription || '';
        
        const parentSelect = document.getElementById('parent_service');
        parentSelect.value = categoryId || '';

        // Ensure the correct option is selected (in case value doesn't auto-select)
        Array.from(parentSelect.options).forEach(option => {
            option.selected = option.value === categoryId;
        });
    });

    // Load Sub-Services
    document.getElementById('viewServicesModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const serviceId = button.getAttribute('data-service-id');
        const servicesList = document.getElementById('services-list');
        servicesList.innerHTML = '<p>Loading...</p>';

        fetch(`/cp/services/${serviceId}/services`)
            .then(response => response.json())
            .then(data => {
                if (data.services && data.services.length > 0) {
                    servicesList.innerHTML = data.services.map(s => 
                        `<p class="mb-1">${s.service_name || '[Unnamed]'}</p>`
                    ).join('');
                } else {
                    servicesList.innerHTML = '<p class="text-muted">No sub-services found.</p>';
                }
            })
            .catch(err => {
                console.error('Failed to load services:', err);
                servicesList.innerHTML = '<p class="text-danger">Error loading services.</p>';
            });
    });

    // Optional: Auto-generate slug from name (UX enhancement)
    document.getElementById('service_name')?.addEventListener('input', function() {
        const slugField = document.getElementById('service_slug');
        if (!slugField.dataset.locked) {
            slugField.value = this.value
                ? this.value.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '')
                : '';
        }
    });
</script>
@endpush