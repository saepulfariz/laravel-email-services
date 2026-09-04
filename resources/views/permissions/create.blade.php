@extends('layouts.app')

@section('title', 'Add Permission - Email Services')

@section('content')
    <div class="mb-8">
        <a href="{{ route('permissions.index') }}" class="text-steel no-underline text-sm font-medium">&larr; Back to permissions</a>
        <h1 class="mt-4">Add New Permission</h1>
    </div>

    <div class="card max-w-[600px]">
        @if ($errors->any())
            <div class="alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('permissions.store') }}" method="POST">
            @csrf
            
            <div class="input-group">
                <label for="name">Permission Name</label>
                <input type="text" id="name" name="name" class="text-input" required value="{{ old('name') }}">
            </div>

            <div class="mt-6">
                <button type="submit" class="btn-primary">Create Permission</button>
            </div>
        </form>
    </div>
@endsection
