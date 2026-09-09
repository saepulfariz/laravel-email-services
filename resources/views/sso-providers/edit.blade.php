@extends('layouts.app')

@section('title', 'Edit SSO Provider - Email Services')

@section('content')
    <div class="mb-8">
        <a href="{{ route('sso-providers.index') }}" class="text-steel no-underline text-sm font-medium">&larr; Back to SSO
            Provider</a>
        <h1 class="mt-4">Edit SSO Provider</h1>
    </div>

    <div class="card max-w-[600px]">
        @if ($errors->any())
            <div class="alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('sso-providers.update', $ssoProvider->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="input-group">
                <label for="name">SSO Provider Name</label>
                <input type="text" id="name" name="name" class="text-input" required
                    value="{{ old('name', $ssoProvider->name) }}">
            </div>

            <div class="input-group">
                <label for="icon">SSO Provider Icon</label>
                <input type="text" id="icon" name="icon" class="text-input" value="{{ old('icon', $ssoProvider->icon) }}">
            </div>

            <div class="input-group">
                <label for="is_active">SSO Provider Active</label>
                <input type="checkbox" id="is_active" name="is_active" class="text-input" {{ old('is_active', $ssoProvider->is_active) ? 'checked' : '' }} value="on">
            </div>

            <div class="input-group">
                <label for="can_register">SSO Provider Can Register</label>
                <input type="checkbox" id="can_register" name="can_register" class="text-input" {{ old('can_register', $ssoProvider->can_register) ? 'checked' : '' }} value="on">
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="btn-primary">Update SSO Provider</button>
                <a href="{{ route('sso-providers.index') }}" class="btn-ghost border border-hairline">Cancel</a>
            </div>
        </form>
    </div>
@endsection