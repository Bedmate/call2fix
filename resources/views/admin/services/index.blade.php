@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Services</h1>
            {{-- Search Bar --}}
            <form method="GET" class="mt-2">
                <div class="input-group" style="max-width: 400px;">
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Search by name or slug..." 
                           value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit">Search</button>
                    @if(request('search'))
                        <a href="{{ route('admin.services.index') }}" class="btn btn-outline-danger">Clear</a>
                    @endif
                </div>
            </form>
        </div>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createServiceModal">
            + Add New Service
        </button>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Service Name</th>
                <th>Slug</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($services as $service)
            <tr>
                <td>{{ $loop->iteration + ($services->currentPage() - 1) * $services->perPage() }}</td>
                <td>{{ $service->service_name }}</td>
                <td>{{ $service->service_slug }}</td>
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
            @empty
            <tr>
                <td colspan="5" class="text-center">No services found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $services->appends(['search' => request('search')])->links() }}

    <!-- Create Service Modal -->
    <div class="modal fade" id="createServiceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createServiceForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Service Name *</label>
                            <input type="text" name="service_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug (auto-generated if empty)</label>
                            <input type="text" name="service_slug" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category *</label>
                            <select name="parent_service" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Create Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div class="modal fade" id="editServiceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editServiceForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="edit_service_id">
                        <div class="mb-3">
                            <label class="form-label">Service Name *</label>
                            <input type="text" name="service_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" name="service_slug" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category *</label>
                            <select name="parent_service" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Services Modal -->
    <div class="modal fade" id="viewServicesModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sub-Services</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
// Auto-slug
function bindSlug(nameSel, slugSel) {
    document.querySelector(nameSel)?.addEventListener('input', e => {
        const slug = document.querySelector(slugSel);
        if (!slug.dataset.locked) {
            slug.value = e.target.value
                ? e.target.value.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/[\s-]+/g, '-').trim()
                : '';
        }
    });
}
bindSlug('[name="service_name"]', '[name="service_slug"]');

// Create Service (AJAX)
document.getElementById('createServiceForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    try {
        const res = await fetch("{{ route('admin.services.store') }}", {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
        });
        const data = await res.json();
        
        if (data.success) {
            location.reload(); // or update table via JS
        } else {
            alert('Error: ' + data.message);
        }
    } catch (err) {
        alert('Request failed');
    }
});

// Edit Modal Populate
document.getElementById('editServiceModal').addEventListener('show.bs.modal', (e) => {
    const btn = e.relatedTarget;
    const id = btn.dataset.serviceId;
    document.getElementById('edit_service_id').value = id;
    document.querySelector('#editServiceForm [name="service_name"]').value = btn.dataset.serviceName;
    document.querySelector('#editServiceForm [name="service_slug"]').value = btn.dataset.serviceSlug;
    document.querySelector('#editServiceForm [name="parent_service"]').value = btn.dataset.categoryId;
    
    // Update form action
    document.getElementById('editServiceForm').action = `/cp/services/${id}`;
});

// Edit Service (AJAX)
document.getElementById('editServiceForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('edit_service_id').value;
    const formData = new FormData(e.target);
    
    try {
        const res = await fetch(`/cp/services/${id}`, {
            method: 'POST',
            body: formData,
            headers: { 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-HTTP-Method-Override': 'PUT'
            }
        });
        const data = await res.json();
        
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (err) {
        alert('Update failed');
    }
});

// View Sub-Services (unchanged)
document.getElementById('viewServicesModal').addEventListener('show.bs.modal', async (e) => {
    const id = e.relatedTarget.dataset.serviceId;
    const list = document.getElementById('services-list');
    list.innerHTML = '<p>Loading...</p>';
    
    try {
        const res = await fetch(`/cp/services/${id}/services`);
        const data = await res.json();
        list.innerHTML = data.services?.length 
            ? data.services.map(s => `<p>${s.service_name}</p>`).join('')
            : '<p class="text-muted">No sub-services</p>';
    } catch {
        list.innerHTML = '<p class="text-danger">Failed to load</p>';
    }
});
</script>
@endpush