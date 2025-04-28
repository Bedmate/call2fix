{{-- resources/views/service-request-view.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Service Request Details</h1>

    {{-- Request Info --}}
    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Request Information</h2>

        <p><strong>Problem Title:</strong> {{ $request->problem_title ?? 'N/A' }}</p>
        <p><strong>Description:</strong> {{ $request->problem_description ?? 'N/A' }}</p>
        <p><strong>Status:</strong> {{ $request->request_status ?? 'N/A' }}</p>
        <p><strong>Inspection Date:</strong> {{ $request->inspection_date ? \Carbon\Carbon::parse($request->inspection_date)->format('d M, Y') : 'N/A' }}</p>
        <p><strong>Inspection Time:</strong> {{ $request->inspection_time ?? 'N/A' }}</p>
        <p><strong>Total Cost:</strong> {{ $request->formatted_price ?? number_format($request->total_cost, 2) }}</p>
    </div>

    {{-- Problem Images --}}
    @if (!empty($request->problem_images))
    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Problem Media</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach ($request->problem_images as $media)
                @php
                    $ext = pathinfo($media, PATHINFO_EXTENSION);
                @endphp

                @if (in_array(strtolower($ext), ['mp4', 'webm', 'ogg']))
                    {{-- Video --}}
                    <video controls class="w-full rounded">
                        <source src="{{ $media }}" type="video/{{ $ext }}">
                        Your browser does not support the video tag.
                    </video>
                @else
                    {{-- Image --}}
                    <img src="{{ $media }}" alt="Problem Image" class="w-full h-48 object-cover rounded">
                @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- User Info --}}
    @if (!empty($request->user))
    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">User Information</h2>

        <div class="flex items-center gap-4 mb-4">
            <img src="{{ $request->user['profile_picture'] ?? 'https://via.placeholder.com/100' }}" class="w-16 h-16 rounded-full" alt="User Image">
            <div>
                <p><strong>Name:</strong> {{ $request->user['first_name'] ?? '' }} {{ $request->user['last_name'] ?? '' }}</p>
                <p><strong>Email:</strong> {{ $request->user['email'] ?? 'N/A' }}</p>
                <p><strong>Phone:</strong> {{ $request->user['phone'] ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Submitted Quotes --}}
    @if (!empty($request->submitted_quotes))
    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Submitted Quotes</h2>

        @foreach ($request->submitted_quotes as $quote)
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
