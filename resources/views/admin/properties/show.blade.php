@extends('layouts.app')

@section('title', 'Property Details')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0">{{ $property->property_name }}</h3>
                    <small>{{ $property->property_type }}</small>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5 class="text-muted">Address</h5>
                        <p>{{ $property->property_address }}</p>
                    </div>

                    <div class="mb-3">
                        <h5 class="text-muted">Nearest Landmark</h5>
                        <p>{{ $property->property_nearest_landmark }}</p>
                    </div>

                    <div class="mb-3">
                        <h5 class="text-muted">Location on Map</h5>
                        <div id="map" style="height: 400px; width: 100%; border-radius: 10px;"></div>
                    </div>
                </div>
                <div class="card-footer text-muted text-end">
                    Created on {{ \Carbon\Carbon::parse($property->created_at)->format('F d, Y') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<!-- Leaflet.js CSS -->
<link rel="stylesheet" href="//unpkg.com/leaflet/dist/leaflet.css" />
@endsection

@section('scripts')
<!-- Leaflet.js JS -->
<script src="//unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var map = L.map('map').setView([
            parseFloat("{{ $property->porperty_latitude }}"), 
            parseFloat("{{ $property->porperty_longitude }}")
        ], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.marker([
            parseFloat("{{ $property->porperty_latitude }}"), 
            parseFloat("{{ $property->porperty_longitude }}")
        ]).addTo(map)
        .bindPopup('<b>{{ $property->property_name }}</b><br>{{ $property->property_address }}')
        .openPopup();
    });
</script>
@endsection
