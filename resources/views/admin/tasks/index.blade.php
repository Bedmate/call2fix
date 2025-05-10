@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h4>Tasks Management</h4>
        <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary">Create Task</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Title</th>
                    <th>Refs Needed</th>
                    <th>Pay/Invite</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Default</th>
                    <th>Providers Only</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                    <tr>
                        <td>{{ $task->task_title }}</td>
                        <td>{{ $task->ref_required_to_complete }}</td>
                        <td>{{ $task->pay_per_invite }}</td>
                        <td>{{ $task->start_date }}</td>
                        <td>{{ $task->end_date }}</td>
                        <td>{{ $task->is_default ? 'Yes' : 'No' }}</td>
                        <td>{{ $task->providers_only ? 'Yes' : 'No' }}</td>
                        <td>
                            <a href="{{ route('admin.tasks.edit', $task->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete task?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">No tasks found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $tasks->links() }}
</div>
@endsection
