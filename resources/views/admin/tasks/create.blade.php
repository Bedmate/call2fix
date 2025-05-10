@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4>Create New Task</h4>
    <form action="{{ route('admin.tasks.store') }}" method="POST">
        @csrf
        @include('admin.tasks.form')
        <button class="btn btn-primary">Create Task</button>
    </form>
</div>
@endsection
