@extends('layouts.app')

@section('content')
@php $serviceAreas = App\Models\ServiceArea::all(); @endphp
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Categories</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-success">Add New Category</a>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Category Name</th>
                <th>Service Area</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $category->category_name }}</td>
                <td>{{ $category->serviceArea->service_area_title }}</td>
                <td>
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editCategoryModal" data-category-id="{{ $category->id }}" data-category-name="{{ $category->category_name }}" data-category-description="{{ $category->category_description }}" data-parent-category="{{ $category->parent_category }}" data-category-image="{{ $category->category_image }}">
                        Edit Category
                    </button>
                    
                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewServicesModal" data-category-id="{{ $category->id }}">
                        View Services
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $categories->links() }}

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.categories.update', 'placeholder') }}" method="POST" enctype="multipart/form-data" id="editCategoryForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="category_name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" {{ old('category_name') }} required>
                        </div>

                        <div class="mb-3">
                            <label for="category_slug" class="form-label">Slug</label>
                            <input type="text" class="form-control" id="category_slug" name="category_slug" {{ old('category_slug') }} required>
                        </div>

                        <div class="mb-3">
                            <label for="parent_category" class="form-label">Parent Service Area</label>
                            <select class="form-select" id="parent_category" name="parent_category" required>
                                @foreach($serviceAreas as $serviceArea)
                                    <option value="{{ $serviceArea->id }}">{{ $serviceArea->service_area_title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="category_description" class="form-label">Description</label>
                            <textarea class="form-control" id="category_description" name="category_description"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="category_image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="category_image" name="category_image">
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
                    <h5 class="modal-title" id="viewServicesModalLabel">Services Under Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="services-list">
                    <!-- Services will be dynamically loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Edit Category Modal
    const editModal = document.getElementById('editCategoryModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-category-id');
        const name = button.getAttribute('data-category-name');
        const slug = button.getAttribute('data-category-slug');
        const desc = button.getAttribute('data-category-description');
        const parent = button.getAttribute('data-parent-category');

        const form = this.querySelector('form');
        form.action = `/admin/categories/${id}`;

        form.querySelector('#category_name').value = name;
        form.querySelector('#category_slug').value = slug || name.toLowerCase().replace(/\s+/g, '-');
        form.querySelector('#category_description').value = desc || '';
        form.querySelector('#parent_category').value = parent || '';
    });

    // View Services Modal
    const viewModal = document.getElementById('viewServicesModal');
    viewModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const categoryId = button.getAttribute('data-category-id');
        const servicesList = this.querySelector('#services-list');

        servicesList.innerHTML = '<p>Loading services...</p>';

        fetch(`/admin/categories/${categoryId}/services`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to load services');
                return response.json();
            })
            .then(data => {
                if (data.services && data.services.length > 0) {
                    servicesList.innerHTML = data.services.map(s => 
                        `<p><strong>${s.service_name}</strong> — ${s.description || ''}</p>`
                    ).join('');
                } else {
                    servicesList.innerHTML = '<p class="text-muted">No services found for this category.</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                servicesList.innerHTML = '<p class="text-danger">Error loading services.</p>';
            });
    });
});
</script>
@endpush
