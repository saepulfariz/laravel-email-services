@extends('layouts.app')

@section('title', 'Add User - Email Services')

@section('content')
    <div class="mb-8">
        <a href="{{ route('users.index') }}" class="text-steel no-underline text-sm font-medium">&larr; Back to users</a>
        <h1 class="mt-4">Add New User</h1>
    </div>

    <div class="card max-w-[600px]">
        @if ($errors->any())
            <div class="alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="input-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="text-input" required value="{{ old('name') }}">
            </div>

            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="text-input" value="{{ old('username') }}">
            </div>

            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="text-input" required value="{{ old('email') }}">
            </div>

            <div class="input-group">
                <label for="image">Profile Image</label>
                <input type="file" id="image" name="image" class="text-input" accept="image/*">
            </div>

            <div class="input-group">
                <label class="flex items-center gap-2 cursor-pointer mt-2">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span class="text-sm font-medium text-ink">Active User</span>
                </label>
            </div>

            <div class="input-group">
                <label>Roles</label>
                <div class="mt-2">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($roles as $role)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}" 
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring focus:ring-indigo-200"
                                {{ is_array(old('roles')) && in_array($role->name, old('roles')) ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-ink">{{ $role->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="text-input" required>
            </div>

            <div class="input-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="text-input" required>
            </div>

            <div class="mt-6">
                <button type="submit" class="btn-primary">Create User</button>
            </div>
        </form>
    </div>
@endsection
