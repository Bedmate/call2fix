@extends('layouts.app')

@section('title', 'Manage Category Sliders')
@section('content')
<div class="container mt-5">
    <div class="d-flex justify-between gap-5">
        <h2>Manage Category Sliders</h2>
        <a href="{{ route('admin.categories.sliders.add') }}">
            <button class="btn btn-info" type="button" data-toggle="modal" data-target="#slider">Add New Slider</button>
        </a>
    </div>

    <div class="row">
        <div class="table-responsive">
            <table class="table table-light table-striped table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Image Thumbnail</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sliders as $slider)
                    <tr>
                        <td><img class="img-thumbnail" src="" alt=""></td>
                        <td>
                            @if($slider->slider_status == 1) 
                            <span class="badge badge-success">Active</span>
                            @else
                            <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="gap-4 items-center">
                                <a href="{{ route('admin.categories.edit_category_slider', $slider->id) }}" class="btn btn-primary">
                                    <i class="fa fa-edit p-3 bg-primary"></i>
                                </a>
                                <form action="{{ route('admin.categories.delete_category_slider', $slider->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this slider?')">
                                        <i class="fa fa-trash bg-danger p-3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- // modal to add new slider and statusus -->
    <div id="slider" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form method="post" action="{{ route('admin.categories.sliders.add') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group my-3">
                            <label for="slider_image">Image:</label>
                            <input type="file" name="slider_image" id="slider_image" class="form-control">
                        </div>
                        <div class="form-group my-3">
                            <label for="slider_status">Status:</label>
                            <select name="slider_status" id="slider_status" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary py-2 px-6">Add Slider</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection