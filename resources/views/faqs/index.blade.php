@extends('layouts.app')

@section('title', 'All Faqs')
@section('content')
<div class="container">
    <h2>FAQs</h2>
    <a href="{{ route('admin.faq.create') }}" class="btn btn-primary mb-3">Add New FAQ</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Subject</th>
                <th>User Role</th>
                <th>Account Type</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($faqs as $faq)
            <tr>
                <td>{{ $faq->subject }}</td>
                <td>{{ $faq->user_role }}</td>
                <td>{{ $faq->account_type }}</td>
                <td>{{ $faq->created_at->format('Y-m-d') }}</td>
                <td>
                    <a href="{{ route('faqs.show', $faq->id) }}" class="btn btn-info btn-sm">View</a>
                    <a href="{{ route('faqs.edit', $faq->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('faqs.destroy', $faq->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete FAQ?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
