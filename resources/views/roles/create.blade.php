@extends('layouts.app')

@section('title', 'Add Role - Email Services')

@section('content')
    <div class="mb-8">
        <a href="{{ route('roles.index') }}" class="text-steel no-underline text-sm font-medium">&larr; Back to roles</a>
        <h1 class="mt-4">Add New Role</h1>
    </div>

    <div class="card max-w-[600px]">
        @if ($errors->any())
            <div class="alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            
            <div class="input-group">
                <label for="name">Role Name</label>
                <input type="text" id="name" name="name" class="text-input" required value="{{ old('name') }}">
            </div>

            <div class="input-group">
                <label>Permissions</label>
                <div class="mt-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($permissions as $group => $items)
                        <div class="bg-gray-100 shadow rounded-xl p-4">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-300 pb-2">{{ ucfirst($group) }}</h2>
                            <div class="space-y-2">
                                @foreach ($items as $permission)
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="check-create-{{ $permission->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring focus:ring-indigo-200" {{ is_array(old('permissions')) && in_array($permission->name, old('permissions')) ? 'checked' : '' }}>
                                    <label for="check-create-{{ $permission->id }}" class="text-gray-700">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="btn-primary">Create Role</button>
            </div>
        </form>
    </div>
@endsection
