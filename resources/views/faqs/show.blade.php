@extends('layouts.app')

@section('title', 'View Faq')

@section('content')
<div class="container">
    <h3>{{ $faq->subject }}</h3>
    <p>{{ $faq->message }}</p>

    <ul class="list-group mt-3">
        <li class="list-group-item">User Role: {{ $faq->user_role }}</li>
        <li class="list-group-item">_Account Type: {{ $faq->_account_type }}</li>
        <li class="list-group-item">Account Type: {{ $faq->account_type }}</li>
        <li class="list-group-item">Created: {{ $faq->created_at->diffForHumans() }}</li>
    </ul>

    <a href="{{ route('admin.faq.index') }}" class="btn btn-secondary mt-3">Back</a>
</div>
@endsection
