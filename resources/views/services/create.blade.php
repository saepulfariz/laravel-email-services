@extends('layouts.app')

@section('title', 'Add Service - Email Services')

@section('content')
    <div class="mb-8">
        <a href="{{ route('services.index') }}" class="text-steel no-underline text-sm font-medium">&larr; Back to services</a>
        <h1 class="mt-4">Add New Service</h1>
        <p class="subtitle mb-0">Add a new service to the system.</p>
    </div>

    <div class="card max-w-[600px]">
        @if ($errors->any())
            <div class="alert-error mb-4">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('services.store') }}" method="POST">
            @csrf

            <div class="input-group">
                <label for="name">Service Name <span class="text-[#d45656]">*</span></label>
                <input type="text" name="name" id="name" required value="{{ old('name') }}" class="text-input" placeholder="e.g. Email Service">
            </div>

            <div class="input-group">
                <label for="slug">Slug <span class="text-steel text-xs font-normal">(Optional, auto-generated if empty)</span></label>
                <input type="text" name="slug" id="slug" value="{{ old('slug') }}" class="text-input font-mono text-sm" placeholder="e.g. email-service">
            </div>

            <div class="input-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" rows="3" class="text-input" placeholder="Describe what this service does...">{{ old('description') }}</textarea>
            </div>

            <div class="input-group">
                <label class="flex items-center gap-2 cursor-pointer mt-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-ink">Active Status</span>
                </label>
                <p class="text-steel text-sm mt-1">Enable or disable this service across the API.</p>
            </div>

            <div class="mt-6">
                <button type="submit" class="btn-primary">Save Service</button>
            </div>
        </form>
    </div>
@endsection
