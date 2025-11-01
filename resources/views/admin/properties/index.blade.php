@extends('layouts.app')

@section('title', 'Manage Properties')
@section('content')
<div class="container">
    <h1>Properties</h1>

 <div class="flex gap-3 items-center">
       <!-- Search and Filter Form -->
    <!-- <form method="GET" action="{{ route('admin.properties.index') }}">
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by Property name or User ID" value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form> -->

    <!-- <a href="{{ route('admin.properties.create') }}" class="btn btn-success mb-3 hidden" hidden>Add New Property</a> -->
 </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Property Name</th>
                <th>Address</th>
                <th>Type</th>
                <th>Nearest Landmark</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($properties as $property)
            <tr>
                <td>{{ $property->property_name }}</td>
                <td>{{ $property->property_address }}</td>
                <td>{{ $property->property_type }}</td>
                <td>{{ $property->property_nearest_landmark }}</td>
                <td>
                    <a href="{{ route('admin.properties.show', $property->id) }}" class="btn btn-info btn-sm">View</a>
                    <a href="{{ route('admin.properties.edit', $property->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('admin.properties.destroy', $property->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $properties->links() }}
</div>
@endsection
