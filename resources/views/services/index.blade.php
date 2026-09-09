@extends('layouts.app')

@section('title', 'Services Management - Email Services')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1>Services Management</h1>
            <p class="subtitle mb-0">Manage all available API services.</p>
        </div>
        <div class="flex items-center gap-3">
            @can('services.create')
            <a href="{{ route('services.create') }}" class="btn-primary">New Service</a>
            @endcan
        </div>
    </div>

    <div class="mb-6">
        <form action="{{ route('services.index') }}" method="GET" class="flex gap-2 w-full max-w-md">
            <input type="text" name="search" value="{{ request('search') ?? $search ?? '' }}" placeholder="Search service..." class="text-input flex-1 !mb-0">
            <button type="submit" class="btn-primary px-4 py-2">Search</button>
            @if(request('search') || isset($search) && $search != '')
                <a href="{{ route('services.index') }}" class="btn-ghost px-4 py-2 border border-hairline">Clear</a>
            @endif
        </form>
    </div>

    <div class="card p-0 shadow-none">
        <div class="table-wrapper border-none rounded-xl">
            <table>
                <thead>
                    <tr>
                        <th class="w-16 text-center">No</th>
                        <th>Service Name</th>
                        <th>Slug</th>
                        <th class="text-center">Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($services as $service)
                        <tr>
                            <td class="text-center text-steel font-medium">
                                {{ ($services->currentPage() - 1) * $services->perPage() + $loop->iteration }}
                            </td>
                            <td class="font-medium text-ink">
                                {{ $service->name }}
                            </td>
                            <td>
                                <span class="font-mono text-ink bg-surface border border-hairline px-2 py-1 rounded text-xs">{{ $service->slug }}</span>
                            </td>
                            <td class="text-center">
                                @if($service->is_active)
                                    <span class="badge success">Active</span>
                                @else
                                    <span class="badge" style="background: var(--surface); color: var(--steel);">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    @can('services.edit')
                                    <a href="{{ route('services.edit', $service) }}" class="btn-ghost px-2 py-1 text-[13px] border border-hairline">Edit</a>
                                    @endcan
                                    
                                    @can('services.delete')
                                    <form action="{{ route('services.destroy', $service) }}" method="POST" class="m-0 form-delete" data-name="{{ $service->name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-ghost px-2 py-1 text-[13px] border border-hairline text-[#d45656]">Delete</button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    No services found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($services->hasPages())
        <div class="mt-6">
            {{ $services->links() }}
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.form-delete');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const name = this.getAttribute('data-name');
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        text: `You are about to delete service "${name}". This action cannot be undone!`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d45656',
                        cancelButtonColor: '#888888',
                        confirmButtonText: 'Yes, delete!',
                        cancelButtonText: 'Cancel'
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
