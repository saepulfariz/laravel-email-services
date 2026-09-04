@extends('layouts.app')

@section('title', 'Roles - Email Services')

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
            <h1>Manage Roles</h1>
            <p class="subtitle mb-0">Add, update, or remove roles.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('roles.create') }}" class="btn-primary">Add Role</a>
        </div>
    </div>

    <div class="mb-6">
        <form action="{{ route('roles.index') }}" method="GET" class="flex gap-2 w-full max-w-md">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name..." class="text-input flex-1 !mb-0">
            <button type="submit" class="btn-primary px-4 py-2">Search</button>
            @if(request('search'))
                <a href="{{ route('roles.index') }}" class="btn-ghost px-4 py-2 border border-hairline">Clear</a>
            @endif
        </form>
    </div>

    <div class="card p-0 shadow-none">
        <div class="table-wrapper border-none rounded-xl">
            <table>
                <thead>
                    <tr>
                        <th><a href="{{ sortUrl('name') }}" class="flex items-center no-underline hover:text-ink">Role Name {!! sortIcon('name') !!}</a></th>
                        <th><a href="{{ sortUrl('created_at') }}" class="flex items-center no-underline hover:text-ink">Created {!! sortIcon('created_at') !!}</a></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td class="font-medium text-ink">{{ $role->name }}</td>
                            <td class="text-steel">{{ $role->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="flex gap-2">
                                    <a href="{{ route('roles.edit', $role->id) }}" class="btn-ghost px-2 py-1 text-[13px] border border-hairline">Edit</a>
                                    
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="m-0 form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-ghost px-2 py-1 text-[13px] border border-hairline text-[#d45656]">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <div class="empty-state">
                                    No roles found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-6">
        {{ $roles->links() }}
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
