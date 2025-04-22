@extends('layouts.app')

@section('title', 'Edit Faqs')

@section('content')
<div class="m-4">
    <form action="{{ isset($faq) ? route('admin.faq.update', $faq->id) : route('admin.faq.store') }}" method="POST">
        @csrf
        @if(isset($faq))
            @method('PUT')
        @endif

        <div class="mb-3">
            <label>Subject</label>
            <input type="text" name="subject" class="form-control" value="{{ old('subject', $faq->subject ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label>Message</label>
            <textarea name="message" class="form-control" rows="4" required>{{ old('message', $faq->message ?? '') }}</textarea>
        </div>

        <div class="mb-3">
            <label>Account Type</label>
            <select name="account_type" id="account_type" class="form-control">
                @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ str_replace("_", " ", $role->name) }}</option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-success">{{ isset($faq) ? 'Update FAQ' : 'Create FAQ' }}</button>
    </form>
</div>
@endsection