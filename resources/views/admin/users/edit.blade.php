@extends('layouts.app')

@section('title', 'Edit User')
@section('content')
<div class="container mt-5">
    <h2>Edit User</h2>
    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
        @csrf
        @method('PUT')

        @php
            $roles = ['artisan', 'admin', 'customer', 'super_admin'];
            $accountTypes = ['private_accounts', 'business_accounts'];
            $subAccountTypes = ['normal', 'department', 'sub_account'];
        @endphp

        <div class="row">
            <!-- Basic Info -->
            @foreach (['first_name', 'last_name', 'username', 'email', 'phone'] as $field)
                <div class="mb-3 col-md-6">
                    <label class="form-label">{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                    <input type="{{ $field === 'email' ? 'email' : 'text' }}" name="{{ $field }}" value="{{ $user->$field }}" class="form-control" required>
                </div>
            @endforeach

            <!-- Select Dropdowns -->
            <div class="mb-3 col-md-6">
                <label class="form-label">Account Type</label>
                <select name="account_type" class="form-select">
                    @foreach ($accountTypes as $type)
                        <option value="{{ $type }}" {{ $user->account_type === $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3 col-md-6">
                <label class="form-label">Current Role</label>
                <select name="current_role" class="form-select">
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" {{ $user->current_role === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3 col-md-6">
                <label class="form-label">Main Account Role</label>
                <select name="main_account_role" class="form-select">
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" {{ $user->main_account_role === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3 col-md-6">
                <label class="form-label">Sub Account Type</label>
                <select name="sub_account_type" class="form-select">
                    @foreach ($subAccountTypes as $type)
                        <option value="{{ $type }}" {{ $user->sub_account_type === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Profile Picture -->
            <div class="mb-3 col-md-12">
                <label class="form-label">Profile Picture URL</label>
                <input type="url" name="profile_picture" value="{{ $user->profile_picture }}" class="form-control">
            </div>

            <!-- Coordinates -->
            @foreach (['latitude', 'longitude'] as $field)
                <div class="mb-3 col-md-6">
                    <label class="form-label">{{ ucfirst($field) }}</label>
                    <input type="text" name="{{ $field }}" value="{{ $user->$field }}" class="form-control">
                </div>
            @endforeach

            <!-- Toggles -->
            @foreach ([
                'is_social', 'is_department', 'can_hold_wallet', 'is_guest', 
                'is_notification_enabled', 'business_verification_status'
            ] as $toggle)
                <div class="mb-3 col-md-6">
                    <label class="form-label">{{ ucwords(str_replace('_', ' ', $toggle)) }}</label>
                    <select name="{{ $toggle }}" class="form-select">
                        <option value="1" {{ $user->$toggle ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ !$user->$toggle ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            @endforeach

            <!-- Optional Fields -->
            @foreach ([
                'device_id', 'country_dialing_code', 'description', 
                'department_description', 'referred_by', 'referred_by_earnings',
                'current_department_id', 'parent_account_id', 'service_provider_id'
            ] as $field)
                <div class="mb-3 col-md-6">
                    <label class="form-label">{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                    <input type="text" name="{{ $field }}" value="{{ $user->$field }}" class="form-control">
                </div>
            @endforeach

            <!-- Submit -->
            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary">Update User</button>
            </div>
        </div>
    </form>
</div>
@endsection