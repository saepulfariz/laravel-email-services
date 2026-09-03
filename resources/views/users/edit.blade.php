@extends('layouts.app')

@section('title', 'Edit User - Email Services')

@section('content')
    <div class="mb-8">
        <a href="{{ route('users.index') }}" class="text-steel no-underline text-sm font-medium">&larr; Back to users</a>
        <h1 class="mt-4">Edit User</h1>
    </div>

    <div class="card max-w-[600px]">
        @if ($errors->any())
            <div class="alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="input-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="text-input" required value="{{ old('name', $user->name) }}">
            </div>

            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="text-input" value="{{ old('username', $user->username) }}">
            </div>

            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="text-input" required value="{{ old('email', $user->email) }}">
            </div>

            <div class="input-group">
                <label for="image">Profile Image</label>
                @if($user->image)
                    <div class="mb-2">
                        <img src="{{ Storage::url($user->image) }}" alt="Profile" class="w-16 h-16 rounded-full object-cover border border-hairline">
                    </div>
                @endif
                <input type="file" id="image" name="image" class="text-input" accept="image/*">
            </div>

            <div class="input-group">
                <label class="flex items-center gap-2 cursor-pointer mt-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-ink">Active User</span>
                </label>
            </div>

            <div class="my-8 pt-4 border-t border-hairline-soft">
                <p class="text-sm text-steel mb-4">Leave password fields blank if you don't want to change the password.</p>
            </div>

            <div class="input-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" class="text-input">
            </div>

            <div class="input-group">
                <label for="password_confirmation">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="text-input">
            </div>

            <div class="mt-6">
                <button type="submit" class="btn-primary">Update User</button>
            </div>
        </form>
    </div>
@endsection
