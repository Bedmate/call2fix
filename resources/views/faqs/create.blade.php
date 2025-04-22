@extends('layouts.app')

@section('title', 'Add Faqs')

@section('content')
<div class="m-4">
    <form action="{{ isset($faq) ? route('admin.faq.update', $faq->id) : route('faqs.store') }}" method="POST">
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
            <select name="account_type" id="account_type">Account Type</select>
            @foreach($roles as $role)
            <select name="{{ $role-> }}" id="account_type">Account Type</select>
            <input type="text" name="account_type" class="form-control" value="{{ old('account_type', $faq->account_type ?? '') }}">
        </div>

        <button class="btn btn-success">{{ isset($faq) ? 'Update FAQ' : 'Create FAQ' }}</button>
    </form>
</div>
@endsection