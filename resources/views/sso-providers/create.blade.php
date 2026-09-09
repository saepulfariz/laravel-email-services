@extends('layouts.app')

@section('title', 'Add SSO Provider - Email Services')

@section('content')
    <div class="mb-8">
        <a href="{{ route('sso-providers.index') }}" class="text-steel no-underline text-sm font-medium">&larr; Back to SSO
            Provider</a>
        <h1 class="mt-4">Add New SSO Provider</h1>
    </div>

    <div class="card max-w-[600px]">
        @if ($errors->any())
            <div class="alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('sso-providers.store') }}" method="POST">
            @csrf

            <div class="input-group">
                <label for="name">SSO Provider Name</label>
                <input type="text" id="name" name="name" class="text-input" required value="{{ old('name') }}">
            </div>

            <div class="input-group">
                <label for="icon">SSO Provider Icon</label>
                <input type="text" id="icon" name="icon" class="text-input" value="{{ old('icon') }}">
            </div>

            <div class="input-group">
                <label for="is_active">SSO Provider Active</label>
                <input type="checkbox" id="is_active" name="is_active" class="text-input" value="{{ old('is_active') }}">
            </div>

            <div class="input-group">
                <label for="can_register">SSO Provider Can Register</label>
                <input type="checkbox" id="can_register" name="can_register" class="text-input"
                    value="{{ old('can_register') }}">
            </div>

            <div class="mt-6">
                <button type="submit" class="btn-primary">Create SSO Provider</button>
            </div>
        </form>
    </div>
@endsection