@extends('layouts.app')

@section('title', 'Users - Email Services')

@php
    function sortUrl($column) {
        $direction = request('sort') === $column && request('direction') === 'asc' ? 'desc' : 'asc';
        return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $direction]);
    }
    
    function sortIcon($column) {
        if (request('sort') !== $column) return '<span class="text-hairline ml-1 opacity-50">⇅</span>';
        return request('direction') === 'asc' ? '<span class="text-ink ml-1">↑</span>' : '<span class="text-ink ml-1">↓</span>';
    }
@endphp

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1>Manage Users</h1>
            <p class="subtitle mb-0">Add, update, or remove dashboard users.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('users.export', request()->query()) }}" class="btn-ghost border border-hairline">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export Excel
            </a>
            @can('users.create')
            <a href="{{ route('users.create') }}" class="btn-primary">Add User</a>
            @endcan
        </div>
    </div>

    <div class="mb-6">
        <form action="{{ route('users.index') }}" method="GET" class="flex gap-2 w-full max-w-md">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email..." class="text-input flex-1 !mb-0">
            <button type="submit" class="btn-primary px-4 py-2">Search</button>
            @if(request('search'))
                <a href="{{ route('users.index') }}" class="btn-ghost px-4 py-2 border border-hairline">Clear</a>
            @endif
        </form>
    </div>

    <div class="card p-0 shadow-none">
        <div class="table-wrapper border-none rounded-xl">
            <table>
                <thead>
                    <tr>
                        <th><a href="{{ sortUrl('name') }}" class="flex items-center no-underline hover:text-ink">User {!! sortIcon('name') !!}</a></th>
                        <th><a href="{{ sortUrl('email') }}" class="flex items-center no-underline hover:text-ink">Email {!! sortIcon('email') !!}</a></th>
                        <th><a href="{{ sortUrl('is_active') }}" class="flex items-center no-underline hover:text-ink">Status {!! sortIcon('is_active') !!}</a></th>
                        <th>Roles</th>
                        <th><a href="{{ sortUrl('created_at') }}" class="flex items-center no-underline hover:text-ink">Joined {!! sortIcon('created_at') !!}</a></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    @if($user->image)
                                        <img src="{{ Storage::url($user->image) }}" alt="" class="w-8 h-8 rounded-full object-cover border border-hairline">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-surface border border-hairline flex items-center justify-center text-steel font-medium text-xs">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium text-ink">{{ $user->name }}</div>
                                        @if($user->username)
                                            <div class="text-[12px] text-steel">{{ '@' . $user->username }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge success">Active</span>
                                @else
                                    <span class="badge" style="background: var(--surface); color: var(--steel);">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge" style="background: var(--surface); color: var(--ink);">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td class="text-steel">{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="flex gap-2">
                                    @can('users.edit')
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn-ghost px-2 py-1 text-[13px] border border-hairline">Edit</a>
                                    @endcan
                                    
                                    @can('users.delete')
                                    @if(auth()->id() !== $user->id)
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="m-0 form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-ghost px-2 py-1 text-[13px] border border-hairline text-[#d45656]">Delete</button>
                                    </form>
                                    @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
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
    
    <div class="mt-6">
        {{ $users->links() }}
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.form-delete');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d45656',
                    cancelButtonColor: '#888888',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection
