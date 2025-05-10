@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4>Edit Task</h4>
    <form action="{{ route('admin.tasks.update', $task->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.tasks.form', ['task' => $task])
        <button class="btn btn-success">Update Task</button>
    </form>
</div>
@endsection
