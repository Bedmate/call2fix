@php
    $task = $task ?? null;
@endphp

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Task Title</label>
        <input type="text" name="task_title" value="{{ old('task_title', $task->task_title ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">References Required</label>
        <input type="number" name="ref_required_to_complete" value="{{ old('ref_required_to_complete', $task->ref_required_to_complete ?? '') }}" class="form-control" required>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="task_description" class="form-control" rows="4" required>{{ old('task_description', $task->task_description ?? '') }}</textarea>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Start Date</label>
        <input type="datetime-local" name="start_date" value="{{ old('start_date', isset($task) ? \Illuminate\Support\Carbon::parse($task->start_date)->format('Y-m-d\TH:i') : '') }}" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">End Date</label>
        <input type="datetime-local" name="end_date" value="{{ old('end_date', isset($task) ? \Illuminate\Support\Carbon::parse($task->end_date)->format('Y-m-d\TH:i') : '') }}" class="form-control" required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Pay per Invite</label>
        <input type="number" name="pay_per_invite" value="{{ old('pay_per_invite', $task->pay_per_invite ?? '') }}" step="0.01" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Must Complete to be Paid</label>
        <select name="must_complete_to_get_paid" class="form-select" required>
            <option value="1" {{ old('must_complete_to_get_paid', $task->must_complete_to_get_paid ?? '') == 1 ? 'selected' : '' }}>Yes</option>
            <option value="0" {{ old('must_complete_to_get_paid', $task->must_complete_to_get_paid ?? '') == 0 ? 'selected' : '' }}>No</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Providers Only</label>
        <select name="providers_only" class="form-select" required>
            <option value="1" {{ old('providers_only', $task->providers_only ?? '') == 1 ? 'selected' : '' }}>Yes</option>
            <option value="0" {{ old('providers_only', $task->providers_only ?? '') == 0 ? 'selected' : '' }}>No</option>
        </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Reward Info One</label>
        <input type="text" name="how_to_get_reward_one" value="{{ old('how_to_get_reward_one', $task->how_to_get_reward_one ?? '') }}" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label">Reward Info Two</label>
        <input type="text" name="how_to_get_reward_two" value="{{ old('how_to_get_reward_two', $task->how_to_get_reward_two ?? '') }}" class="form-control">
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Set as Default</label>
    <select name="is_default" class="form-select" required>
        <option value="1" {{ old('is_default', $task->is_default ?? '') == 1 ? 'selected' : '' }}>Yes</option>
        <option value="0" {{ old('is_default', $task->is_default ?? '') == 0 ? 'selected' : '' }}>No</option>
    </select>
</div>
