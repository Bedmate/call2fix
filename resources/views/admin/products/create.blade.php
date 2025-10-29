  @extends('layouts.app')

  @section('content')
  <div class="container py-5">
      <div class="row justify-content-center">
          <div class="col-md-8">
              <h1 class="mb-4">Create Product</h1>

              <form action="{{ route('admin.products.store') }}" method="POST">
                  @csrf

                  <div class="mb-3">
                      <label for="name" class="form-label">Name</label>
                      <input type="text" name="name" id="name" class="form-control" required>
                  </div>

                  <div class="mb-3">
                      <label for="description" class="form-label">Description</label>
                      <textarea name="description" id="description" rows="4" class="form-control" required></textarea>
                  </div>

                  <div class="mb-3">
                      <label for="price" class="form-label">Price</label>
                      <input type="number" name="price" id="price" step="0.01" class="form-control" required>
                  </div>

                  <div class="mb-3">
                      <label for="category_id" class="form-label">Category</label>
                      <select name="category_id" id="category_id" class="form-select" required>
                          @foreach($categories as $category)
                          <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                          @endforeach
                      </select>
                  </div>

                  <div class="mb-3" id="services-container" style="display: none;">
                      <label for="services" class="form-label">Services (Optional)</label>
                      <select name="services[]" id="services" class="form-select" multiple>
                          <!-- Options will be loaded here dynamically -->
                      </select>
                      <small class="text-muted">Hold Ctrl/Cmd to select multiple services.</small>
                  </div>

                  <div class="mb-3">
                      <label for="seller_id" class="form-label">Seller</label>
                      <select name="seller_id" id="seller_id" class="form-select" required>
                          @foreach($sellers as $seller)
                          <option value="{{ $seller->id }}">{{ "$seller->first_name $seller->last_name" }}</option>
                          @endforeach
                      </select>
                  </div>

                  <div class="mb-3">
                      <label for="stock" class="form-label">Stock</label>
                      <input type="number" name="stock" id="stock" class="form-control" required>
                  </div>

                  <div class="mb-3">
                      <label for="sku" class="form-label">SKU</label>
                      <input type="text" name="sku" id="sku" class="form-control" required>
                  </div>

                  <div class="mb-3">
                      <label for="product_currency" class="form-label">Currency</label>
                      <select name="product_currency" id="product_currency" class="form-select" required>
                          <option value="NGN">NGN</option>
                          <option value="GHC">GHC</option>
                          <option value="CFA">CFA</option>
                          <option value="USD">USD</option>
                          <option value="CAD">CAD</option>
                      </select>
                  </div>

                  <div class="mb-3">
                      <label for="weight" class="form-label">Weight</label>
                      <input type="number" name="weight" id="weight" step="0.01" class="form-control">
                  </div>

                  <div class="mb-3">
                      <label for="dimensions" class="form-label">Dimensions</label>
                      <input type="text" name="dimensions" id="dimensions" class="form-control">
                  </div>

                  <div class="mb-3">
                      <label for="product_image" class="form-label">Product Image</label>
                      <input type="file" name="product_image" id="product_image" class="form-control">
                  </div>

                  <div class="mb-3 form-check">
                      <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active">
                      <label class="form-check-label" for="is_active">Is Active</label>
                  </div>

                  <div class="mb-3 form-check">
                      <input type="checkbox" name="is_leasable" value="1" class="form-check-input" id="is_leasable">
                      <label class="form-check-label" for="is_leasable">Is Leasable</label>
                  </div>

                  <button type="submit" class="btn btn-primary">Create Product</button>
              </form>
          </div>
      </div>
  </div>
  @endsection


  @push('scripts')
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          const categoryIdSelect = document.getElementById('category_id');
          const servicesContainer = document.getElementById('services-container');
          const servicesSelect = document.getElementById('services');
          const token = '{{ csrf_token() }}'; // Only if you need CSRF for internal routes (not for your external API)

          categoryIdSelect.addEventListener('change', async function() {
              const categoryId = this.value;

              if (!categoryId) {
                  servicesContainer.style.display = 'none';
                  servicesSelect.innerHTML = '';
                  return;
              }

              // Show loading state
              servicesSelect.innerHTML = '<option>Loading services...</option>';
              servicesContainer.style.display = 'block';

              try {
                  const response = await fetch(`https://call2fix-app.alphamead.com/api/v1/categories/service/${categoryId}`, {
                      method: 'GET',
                      headers: {
                          'Authorization': 'Bearer 0xt8lYKjrYqjTzepApvwHwQ506dVYeOtrjLCVnJ2Ou8a7a88f9',
                          'Accept': 'application/json',
                      }
                  });

                  if (!response.ok) {
                      throw new Error('Failed to fetch services');
                  }

                  const data = await response.json();

                  // Clear current options
                  servicesSelect.innerHTML = '';

                  // Populate services
                  if (data && Array.isArray(data.data)) {
                      data.data.forEach(service => {
                          const option = document.createElement('option');
                          option.value = service.id; // assuming service has `id`
                          option.textContent = service.name || service.title || service.id; // adjust based on actual field
                          servicesSelect.appendChild(option);
                      });

                      if (data.data.length === 0) {
                          servicesSelect.innerHTML = '<option>No services available</option>';
                      }>
                  } else {
                      servicesSelect.innerHTML = '<option>No services found</option>';
                  }

              } catch (error) {
                  console.error('Error fetching services:', error);
                  servicesSelect.innerHTML = '<option>Error loading services</option>';
              }
          });
      });
  </script>
  @endpush