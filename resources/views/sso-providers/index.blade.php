@extends('layouts.app')

@section('title', 'SSO Provider - Email Services')

@php
    function sortUrl($column)
    {
        $direction = request('sort') === $column && request('direction') === 'asc' ? 'desc' : 'asc';
        return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $direction]);
    }

    function sortIcon($column)
    {
        if (request('sort') !== $column)
            return '<span class="text-hairline ml-1 opacity-50">⇅</span>';
        return request('direction') === 'asc' ? '<span class="text-ink ml-1">↑</span>' : '<span class="text-ink ml-1">↓</span>';
    }
@endphp

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1>Manage SSO</h1>
            <p class="subtitle mb-0">Add, update, or remove SSO provider.</p>
        </div>
        <div class="flex items-center gap-3">
            @can('sso-providers.create')
                <a href="{{ route('sso-providers.create') }}" class="btn-primary">Add SSO Provider</a>
            @endcan
        </div>
    </div>

    <div class="mb-6">
        <form action="{{ route('sso-providers.index') }}" method="GET" class="flex gap-2 w-full max-w-md">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name..."
                class="text-input flex-1 !mb-0">
            <button type="submit" class="btn-primary px-4 py-2">Search</button>
            @if(request('search'))
                <a href="{{ route('sso-providers.index') }}" class="btn-ghost px-4 py-2 border border-hairline">Clear</a>
            @endif
        </form>
    </div>

    <div class="card p-0 shadow-none">
        <div class="table-wrapper border-none rounded-xl">
            <table>
                <thead>
                    <tr>
                        <th><a href="{{ sortUrl('name') }}" class="flex items-center no-underline hover:text-ink">SSO
                                Provider Name {!! sortIcon('name') !!}</a></th>
                        <th><a href="{{ sortUrl('can_register') }}"
                                class="flex items-center no-underline hover:text-ink">Can Register
                                {!! sortIcon('can_register') !!}</a></th>
                        <th><a href="{{ sortUrl('is_active') }}"
                                class="flex items-center no-underline hover:text-ink">Active
                                {!! sortIcon('is_active') !!}</a></th>
                        <th><a href="{{ sortUrl('created_at') }}"
                                class="flex items-center no-underline hover:text-ink">Created
                                {!! sortIcon('created_at') !!}</a></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ssoProviders as $sso)
                        <tr>
                            <td class="font-medium text-ink">{{ $sso->name }}</td>
                            <td class="text-steel">{{ $sso->can_register ? 'Yes' : 'No' }}</td>
                            <td class="text-steel">{{ $sso->is_active ? 'Yes' : 'No' }}</td>
                            <td class="text-steel">{{ $sso->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="flex gap-2">
                                    @can('sso-providers.edit')
                                        <a href="{{ route('sso-providers.edit', $sso->id) }}"
                                            class="btn-ghost px-2 py-1 text-[13px] border border-hairline">Edit</a>
                                    @endcan

                                    @can('sso-providers.delete')
                                        <form action="{{ route('sso-providers.destroy', $sso->id) }}" method="POST"
                                            class="m-0 form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn-ghost px-2 py-1 text-[13px] border border-hairline text-[#d45656]">Delete</button>
                                        </form>
                                    @endcan
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
        {{ $ssoProviders->links() }}
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