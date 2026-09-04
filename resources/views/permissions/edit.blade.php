@extends('layouts.app')

@section('title', 'Edit Permission - Email Services')

@section('content')
    <div class="mb-8">
        <a href="{{ route('permissions.index') }}" class="text-steel no-underline text-sm font-medium">&larr; Back to permissions</a>
        <h1 class="mt-4">Edit Permission</h1>
    </div>

    <div class="card max-w-[600px]">
        @if ($errors->any())
            <div class="alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="input-group">
                <label for="name">Permission Name</label>
                <input type="text" id="name" name="name" class="text-input" required value="{{ old('name', $permission->name) }}">
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="btn-primary">Update Permission</button>
                <a href="{{ route('permissions.index') }}" class="btn-ghost border border-hairline">Cancel</a>
            </div>
        </form>
    </div>
@endsection
