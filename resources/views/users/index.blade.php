@extends('layouts.app')

@section('title', 'Users - Email Services')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1>Manage Users</h1>
            <p class="subtitle mb-0">Add, update, or remove dashboard users.</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn-primary">Add User</a>
    </div>

    @if (session('success'))
        <div class="badge success p-3 text-sm mb-6 block font-medium normal-case">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert-error">
            {{ session('error') }}
        </div>
    @endif

    <div class="card p-0 shadow-none">
        <div class="table-wrapper border-none rounded-xl">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="font-medium">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td class="text-steel">{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="flex gap-2">
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn-ghost px-2 py-1 text-[13px] border border-hairline">Edit</a>
                                    
                                    @if(auth()->id() !== $user->id)
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-ghost px-2 py-1 text-[13px] border border-hairline text-[#d45656]">Delete</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    No users found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
