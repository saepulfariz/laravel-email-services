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

        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            
            <div class="input-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="text-input" required value="{{ old('name') }}">
            </div>

            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="text-input" required value="{{ old('email') }}">
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
