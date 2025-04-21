@extends('layouts.app')

@section('title', 'Add Faqs')

@section('content')
<form action="{{ isset($faq) ? route('faqs.update', $faq->id) : route('faqs.store') }}" method="POST">
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
        <label>User Role</label>
        <input type="text" name="user_role" class="form-control" value="{{ old('user_role', $faq->user_role ?? '') }}" required>
    </div>

    <div class="mb-3">
        <label>_Account Type</label>
        <input type="text" name="_account_type" class="form-control" value="{{ old('_account_type', $faq->_account_type ?? '') }}">
    </div>

    <div class="mb-3">
        <label>Account Type</label>
        <input type="text" name="account_type" class="form-control" value="{{ old('account_type', $faq->account_type ?? '') }}">
    </div>

    <button class="btn btn-success">{{ isset($faq) ? 'Update FAQ' : 'Create FAQ' }}</button>
</form>

@endsection