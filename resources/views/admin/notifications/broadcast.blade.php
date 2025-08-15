@extends('layouts.app')

@section('title', 'Send Broadcast Notification')

@section('content')
<div class="container">
    <h4 class="mb-4">Send Broadcast Notification</h4>

    {{-- Show Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.notifications.broadcast.send') }}" method="POST">
        @csrf

        {{-- Title --}}
        <div class="mb-3">
            <label for="title" class="form-label">Notification Title</label>
            <input type="text" 
                   name="title" 
                   id="title" 
                   class="form-control @error('title') is-invalid @enderror"
                   value="{{ old('title') }}" 
                   maxlength="255" 
                   required>
            @error('title')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Message --}}
        <div class="mb-3">
            <label for="message" class="form-label">Notification Message</label>
            <textarea name="message" 
                      id="message" 
                      rows="4" 
                      class="form-control @error('message') is-invalid @enderror" 
                      maxlength="255" 
                      required>{{ old('message') }}</textarea>
            @error('message')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Roles --}}
        <div class="mb-3">
            <label class="form-label">Select User Roles</label>
            <select name="user_role[]" 
                    class="form-control select2 @error('user_role') is-invalid @enderror" 
                    multiple required>
                @foreach(["artisan", "providers", "co-operate_accounts", "private_accounts", "affiliates", "suppliers", "department"] as $role)
                    <option value="{{ $role }}" {{ $role ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
            @error('user_role')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary">Send Broadcast</button>
    </form>
</div>
@endsection

@push('styles')
    {{-- Select2 CSS --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
    {{-- jQuery (required for Select2) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- Select2 JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select one or more roles",
                allowClear: true
            });
        });
    </script>
@endpush
