<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tasks;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Tasks::latest()->paginate(10);
        return view('admin.tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('admin.tasks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'task_title' => 'required|string|max:255',
            'task_description' => 'nullable|string',
            'ref_required_to_complete' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'pay_per_invite' => 'required|numeric',
            'must_complete_to_get_paid' => 'required|boolean',
            'providers_only' => 'required|boolean',
        ]);

        Tasks::create($request->all());
        return redirect()->route('admin.tasks.index')->with('success', 'Task created successfully.');
    }

    public function edit($id)
    {
        $task = Tasks::findOrFail($id);
        return view('admin.tasks.edit', compact('task'));
    }

    public function update(Request $request, $id)
    {
        $task = Tasks::findOrFail($id);

        $request->validate([
            'task_title' => 'required|string|max:255',
            'task_description' => 'nullable|string',
            'ref_required_to_complete' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'pay_per_invite' => 'required|numeric',
            'must_complete_to_get_paid' => 'required|boolean',
            'providers_only' => 'required|boolean',
        ]);

        $task->update($request->all());
        return redirect()->route('admin.tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy($id)
    {
        Tasks::findOrFail($id)->delete();
        return redirect()->route('admin.tasks.index')->with('success', 'Task deleted successfully.');
    }
}
