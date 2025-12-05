@extends('layouts.app')

@section('title', 'Property Details')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0">{{ ucfirst($property->property_name) }}</h3>
                    <small>{{ $property->property_type }}</small>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5 class="text-muted">Address</h5>
                        <p>{{ $property->property_address }}</p>
                    </div>
                    <div class="mb-3">
                        <h5 class="text-muted">Property Longitude</h5>
                        <p>{{ $property->porperty_longitude }}</p>
                    </div>
                    <div class="mb-3">
                        <h5 class="text-muted">Property Latitude</h5>
                        <p>{{ $property->porperty_latitude }}</p>
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

@push('styles')
<!-- Leaflet.js CSS -->
<link rel="stylesheet" href="//unpkg.com/leaflet/dist/leaflet.css" />
@endpush

@push('scripts')
<!-- Leaflet.js JS -->
<script src="//unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        var lat = parseFloat("{{ $property->porperty_latitude }}");
        var lng = parseFloat("{{ $property->porperty_longitude }}");
        console.log([lat, lng])

        if (!lat || !lng) {
            console.error("Invalid coordinates");
            return;
        }

        var map = new maplibregl.Map({
            container: 'map',
            style: {
                version: 8,
                sources: {
                    osm: {
                        type: 'raster',
                        tiles: [
                            'https://a.tile.openstreetmap.org/{z}/{x}/{y}.png',
                            'https://b.tile.openstreetmap.org/{z}/{x}/{y}.png',
                            'https://c.tile.openstreetmap.org/{z}/{x}/{y}.png'
                        ],
                        tileSize: 256,
                        attribution: '&copy; OpenStreetMap contributors'
                    }
                },
                layers: [{
                    id: 'osm-layer',
                    type: 'raster',
                    source: 'osm'
                }]
            },
            center: [lng, lat],
            zoom: 15
        });

        // marker
        new maplibregl.Marker()
            .setLngLat([lng, lat])
            .setPopup(
                new maplibregl.Popup().setHTML(
                    `<b>{{ $property->property_name }}</b><br>{{ $property->property_address }}`
                )
            )
            .addTo(map);
    });
</script>
@endpush