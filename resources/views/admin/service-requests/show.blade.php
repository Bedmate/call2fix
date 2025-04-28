  @extends('layouts.app')

  @section('content')
      <div class="container my-4">
          <h1 class="text-center mb-4">Service Request Details</h1>
          <div class="card shadow-lg p-4">
              <ul class="nav nav-tabs" id="serviceRequestTabs" role="tablist">
                  @foreach ([
                    'details' => 'Request Details',
                    'user' => 'User Info',
                    'service-provider' => 'Service Provider',
                    'artisan' => 'Artisan',
                    'property' => 'Property',
                    'rework-messages' => 'Rework Messages',
                    'check-ins' => 'Check-Ins',
                    'featured-providers' => 'Featured Providers',
                ] as $id => $label)
                      <li class="nav-item" role="presentation">
                          <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ $id }}-tab"
                              data-bs-toggle="tab" data-bs-target="#{{ $id }}" type="button" role="tab"
                              aria-controls="{{ $id }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                              {{ $label }}
                          </button>
                      </li>
                  @endforeach
              </ul>

              <div class="tab-content mt-3" id="serviceRequestTabsContent">
                  <!-- Request Details -->
                  <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                      <h4>Request Details</h4>
                      <table class="table table-bordered">
                          <tr>
                              <th>Problem Title</th>
                              <td>{{ $serviceRequest->problem_title }}</td>
                          </tr>
                          <tr>
                              <th>Description</th>
                              <td>{{ $serviceRequest->problem_description }}</td>
                          </tr>
                          <tr>
                              <th>Inspection Date</th>
                              <td>{{ $serviceRequest->inspection_date->format('Y-m-d') }}</td>
                          </tr>
                          <tr>
                              <th>Inspection Time</th>
                              <td>{{ $serviceRequest->inspection_time }}</td>
                          </tr>
                          <tr>
                              <th>Status</th>
                              <td>{{ $serviceRequest->request_status }}</td>
                          </tr>
                          <tr class="my-3">
                              <td colspan="2">
                                  <div class="col-12 mt-4">
                                      <!--<a href="{{ route('admin.service-requests.edit', $serviceRequest) }}"-->
                                      <!--    class="btn btn-primary me-2">Edit</a>-->
                                      <form action="{{ route('admin.service-requests.destroy', $serviceRequest) }}"
                                          method="POST" class="d-inline">
                                          @csrf
                                          @method('DELETE')
                                          <button type="submit" class="btn btn-danger"
                                              onclick="return confirm('Are you sure?')">Delete</button>
                                      </form>
                                  </div>
                              </td>
                          </tr>
                      </table>

                      <h4>Problem Images</h4>
                      @if ($serviceRequest->problem_images && count($serviceRequest->problem_images) > 0)
                          <div id="problemImagesCarousel" class="carousel slide" data-bs-ride="carousel">
                              <div class="carousel-inner">
                                  @foreach ($serviceRequest->problem_images as $index => $image)
                                        @php
                                            $ext = pathinfo($image, PATHINFO_EXTENSION);
                                        @endphp
                                      <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                        @if (in_array(strtolower($ext), ['mp4', 'webm', 'ogg']))
                                            <video controls class="w-full rounded">
                                                <source src="{{ $image }}" type="video/{{ $ext }}">
                                                Your browser does not support the video tag.
                                            </video>
                                        @else
                                          <img src="{{ $image }}" class="d-block rounded"
                                              alt="Problem Image {{ $index + 1 }}"
                                              style="max-height: 500px; width:100%">
                                        @endif
                                      </div>
                                  @endforeach
                              </div>
                              <button class="carousel-control-prev" type="button" data-bs-target="#problemImagesCarousel"
                                  data-bs-slide="prev">
                                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                  <span class="visually-hidden">Previous</span>
                              </button>
                              <button class="carousel-control-next" type="button" data-bs-target="#problemImagesCarousel"
                                  data-bs-slide="next">
                                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                  <span class="visually-hidden">Next</span>
                              </button>
                          </div>
                      @else
                          <p>No images uploaded for this request.</p>
                      @endif
                  </div>

                  <!-- User Info -->
                  <div class="tab-pane fade" id="user" role="tabpanel" aria-labelledby="user-tab">
                      <h4>User Details</h4>
                      @if ($serviceRequest->user)
                          <div class="card p-3 shadow-sm">
                              <p><strong>Name:</strong>
                                  {{ $serviceRequest->user->first_name.' ' .$serviceRequest->user->last_name }}</p>
                              <p><strong>Email:</strong> {{ $serviceRequest->user->email }}</p>
                              <p><strong>Phone Number:</strong> {{ $serviceRequest->user->phone }}</p>
                          </div>
                      @else
                          <p>No user assigned to this request.</p>
                      @endif
                  </div>

                  <!-- Service Provider -->
                  <div class="tab-pane fade" id="service-provider" role="tabpanel" aria-labelledby="service-provider-tab">
                      <h4>Service Provider</h4>
                      @if ($serviceRequest->service_provider)
                          <div class="card p-3 shadow-sm">
                              <p><strong>Name:</strong>
                                  {{ $serviceRequest->user->first_name.' ' .$serviceRequest->user->last_name }}</p>
                              <p><strong>Email:</strong> {{ $serviceRequest->user->email }}</p>
                              <p><strong>Phone Number:</strong> {{ $serviceRequest->user->phone }}</p>
                          </div>
                      @else
                          <p>No service provider assigned to this request.</p>
                      @endif
                  </div>

                  <!-- Artisan -->
                  <div class="tab-pane fade" id="artisan" role="tabpanel" aria-labelledby="artisan-tab">
                      <h4>Artisan Details</h4>
                      @if ($serviceRequest->artisan)
                          <div class="card p-3 shadow-sm">
                              <p><strong>Name:</strong>
                                  {{ $serviceRequest->user->first_name.' ' .$serviceRequest->user->last_name }}</p>
                              <p><strong>Email:</strong> {{ $serviceRequest->user->email }}</p>
                              <p><strong>Phone Number:</strong> {{ $serviceRequest->user->phone }}</p>
                          </div>
                      @else
                          <p>No artisan assigned to this request.</p>
                      @endif
                  </div>

                  <!-- Property -->
                  <div class="tab-pane fade" id="property" role="tabpanel" aria-labelledby="property-tab">
                      <h4>Property Details</h4>
                      @if ($serviceRequest->property)
                          <div class="card p-3 shadow-sm">
                              <p><strong>Name:</strong> {{ $serviceRequest->property->property_name }}</p>
                              <p><strong>Address:</strong> {{ $serviceRequest->property->property_address }}</p>
                              <p><strong>Property Type:</strong> {{ $serviceRequest->property->property_type }}</p>
                              <p><strong>Property Longitude:</strong> {{ $serviceRequest->property->porperty_longitude }}</p>
                              <p><strong>Property Latitude:</strong> {{ $serviceRequest->property->porperty_latitude }}</p>
                              <p><strong>Nearest Landmark:</strong> {{ $serviceRequest->property->property_nearest_landmark }}</p>
                              <p>
                                <a href="{{ route('admin.properties.show', $serviceRequest->property->id)}}">
                                    <button class="btn btn-sm btn-primary">View Property</button>
                                </a>
                              </p>
                          </div>
                      @else
                          <p>No property assigned to this request.</p>
                      @endif
                  </div>

                  <!-- Rework Messages -->
                  <div class="tab-pane fade" id="rework-messages" role="tabpanel" aria-labelledby="rework-messages-tab">
                      <h4>Rework Messages</h4>
                      @if ($serviceRequest->reworkMessages->count())
                          <ul class="list-group">
                              @foreach ($serviceRequest->reworkMessages as $message)
                                  <li class="list-group-item">{{ $message->content }}
                                      <em>({{ $message->created_at->format('Y-m-d H:i') }})</em>
                                  </li>
                              @endforeach
                          </ul>
                      @else
                          <p>No rework messages for this request.</p>
                      @endif
                  </div>

                  <!-- Check-Ins -->
                  <div class="tab-pane fade" id="check-ins" role="tabpanel" aria-labelledby="check-ins-tab">
                      <h4>Check-Ins</h4>
                      @if ($serviceRequest->checkIns->count())
                          <ul class="list-group">
                              @foreach ($serviceRequest->checkIns as $checkIn)
                                  <li class="list-group-item">{{ $checkIn->description }}
                                      <em>({{ $checkIn->created_at->format('Y-m-d H:i') }})</em>
                                  </li>
                              @endforeach
                          </ul>
                      @else
                          <p>No check-ins for this request.</p>
                      @endif
                  </div>

                  <!-- Featured Providers -->
                  <div class="tab-pane fade" id="featured-providers" role="tabpanel"
                      aria-labelledby="featured-providers-tab">
                      <h4>Featured Providers</h4>
                      @if ($serviceRequest->featuredProviders->count())
                          <div class="row g-3">
                              @foreach ($serviceRequest->featuredProviders as $provider)
                                  <div class="col-md-4">
                                      <div class="card shadow-sm">
                                          <div class="card-body d-flex">
                                              <img src="{{ $provider->profile_picture ?? asset('images/default-avatar.png') }}"
                                                  alt="Profile Picture" class="img-fluid rounded-circle me-3"
                                                  style="width: 80px; height: 80px;">
                                              <div>
                                                  <h5 class="card-title mb-1">{{ $provider->first_name ." ". $provider->last_name }}</h5>
                                                  <p class="card-text mb-2"><strong>Email:</strong> {{ $provider->email }}
                                                  </p>
                                                  <p class="card-text"><strong>Total Work History:</strong>
                                                      {{ $provider->work_history_count }}</p>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              @endforeach
                          </div>
                      @else
                          <p>No featured providers for this request.</p>
                      @endif
                  </div>
              </div>
          </div>

            {{-- Submitted Quotes --}}
            @if (!empty($serviceRequest->submitted_quotes))
            <div class="bg-white shadow-md rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">Submitted Quotes</h2>

                @foreach ($serviceRequest->submitted_quotes as $quote)
                    <div class="border p-4 rounded mb-6">
                        <p><strong>Summary Note:</strong> {{ $quote['summary_note'] ?? 'N/A' }}</p>
                        <p><strong>SLA Start Date:</strong> {{ $quote['sla_start_date'] ?? 'N/A' }}</p>
                        <p><strong>Service VAT:</strong> {{ number_format($quote['service_vat'], 2) }}</p>
                        <p><strong>Total Charges:</strong> {{ number_format($quote['total_charges'], 2) }}</p>

                        {{-- Quote Items --}}
                        @if (!empty($quote['items']))
                        <div class="mt-4">
                            <h3 class="font-semibold mb-2">Quote Items:</h3>
                            <ul class="list-disc ml-6">
                                @foreach ($quote['items'] as $item)
                                <li>
                                    {{ $item['quantity'] ?? '1' }} x {{ $item['itemDescription'] ?? 'Item' }}
                                    (₦{{ number_format($item['price'], 2) }} each, Total: ₦{{ number_format($item['itemTotalPrice'], 2) }})
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        {{-- Attachments --}}
                        @if (!empty($quote['attachments']))
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($quote['attachments'] as $attachment)
                                @php
                                    $ext = pathinfo($attachment, PATHINFO_EXTENSION);
                                @endphp

                                @if (in_array(strtolower($ext), ['mp4', 'webm', 'ogg']))
                                    <video controls class="w-full rounded">
                                        <source src="{{ $attachment }}" type="video/{{ $ext }}">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    <img src="{{ $attachment }}" alt="Attachment" class="w-full h-48 object-cover rounded">
                                @endif
                            @endforeach
                        </div>
                        @endif

                        {{-- Provider Info --}}
                        @if (!empty($quote['provider']))
                        <div class="mt-6">
                            <h3 class="font-semibold mb-2">Service Provider:</h3>
                            <div class="flex items-center gap-4">
                                <img src="{{ $quote['provider']['profile_picture'] ?? 'https://via.placeholder.com/100' }}" class="w-16 h-16 rounded-full" alt="Provider Image">
                                <div>
                                    <p><strong>Name:</strong> {{ $quote['provider']['first_name'] ?? '' }} {{ $quote['provider']['last_name'] ?? '' }}</p>
                                    <p><strong>Email:</strong> {{ $quote['provider']['email'] ?? 'N/A' }}</p>
                                    <p><strong>Phone:</strong> {{ $quote['provider']['phone'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                @endforeach
            </div>
            @endif
      </div>

  @endsection


  @push('script')
      <script>
          document.addEventListener("DOMContentLoaded", function() {
              const carouselElement = document.querySelector("#problemImagesCarousel");

              if (carouselElement) {
                  const carousel = new bootstrap.Carousel(carouselElement, {
                      interval: 5000, // Auto-slide every 5 seconds
                      wrap: true, // Wrap around to the beginning when reaching the end
                      keyboard: true, // Allow keyboard navigation
                  });

                  // Optional: Debugging the carousel
                  console.log("Bootstrap carousel initialized:", carousel);
              }
          });
      </script>
  @endpush
