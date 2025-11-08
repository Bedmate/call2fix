@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Services</h1>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createServiceModal">
            + Add New Service
        </button>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Service Name</th>
                <th>Category</th>
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
                        data-bs-target="#editServiceModal"
                        data-service-id="{{ $service->id }}"
                        data-service-name="{{ $service->service_name }}"
                        data-service-slug="{{ $service->service_slug }}"
                        data-category-id="{{ $service->category_id }}">
                        Edit
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

    <!-- Create Service Modal -->
    <div class="modal fade" id="createServiceModal" tabindex="-1" aria-labelledby="createServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createServiceModalLabel">Add New Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createServiceForm" method="POST" action="{{ route('admin.services.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="create_service_name" class="form-label">Service Name</label>
                            <input type="text" class="form-control" id="create_service_name" name="service_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="create_service_slug" class="form-label">Slug</label>
                            <input type="text" class="form-control" id="create_service_slug" name="service_slug" required>
                        </div>

                        <div class="mb-3">
                            <label for="create_parent_service" class="form-label">Category</label>
                            <select class="form-select" id="create_parent_service" name="parent_service" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">Create Service</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div class="modal fade" id="editServiceModal" tabindex="-1" aria-labelledby="editServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editServiceModalLabel">Edit Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editServiceForm" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="edit_service_name" class="form-label">Service Name</label>
                            <input type="text" class="form-control" id="edit_service_name" name="service_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_service_slug" class="form-label">Slug</label>
                            <input type="text" class="form-control" id="edit_service_slug" name="service_slug" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_parent_service" class="form-label">Category</label>
                            <select class="form-select" id="edit_parent_service" name="parent_service" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Services Modal (unchanged) -->
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
    // Auto-generate slug from service name (for both modals)
    function setupSlugSync(nameInputId, slugInputId) {
        const nameInput = document.getElementById(nameInputId);
        const slugInput = document.getElementById(slugInputId);
        if (!nameInput || !slugInput) return;

        nameInput.addEventListener('input', function () {
            slugInput.value = this.value
                ? this.value.toLowerCase().trim().replace(/[^a-z0-9\s-]/g, '').replace(/[\s-]+/g, '-').replace(/^-+|-+$/g, '')
                : '';
        });
    }

    // Initialize slug sync
    setupSlugSync('create_service_name', 'create_service_slug');
    setupSlugSync('edit_service_name', 'edit_service_slug');

    // Populate Edit Modal
    document.getElementById('editServiceModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const serviceId = button.getAttribute('data-service-id');
        const serviceName = button.getAttribute('data-service-name');
        const serviceSlug = button.getAttribute('data-service-slug');
        const categoryId = button.getAttribute('data-category-id');

        const form = this.querySelector('form');
        form.action = "{{ route('admin.services.update', '') }}" + '/' + serviceId;

        document.getElementById('edit_service_name').value = serviceName || '';
        document.getElementById('edit_service_slug').value = serviceSlug || '';
        document.getElementById('edit_parent_service').value = categoryId || '';
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
                if (data.services?.length) {
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

    // Optional: Reset create form on close
    document.getElementById('createServiceModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('createServiceForm').reset();
    });
</script>
@endpush