@extends('layouts.app')

@section('title', 'API Keys - Email Services')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1>API Keys</h1>
            <p class="subtitle mb-0">Manage API Keys and their service access.</p>
        </div>
        <div class="flex items-center gap-3">
            @can('api-keys.create')
            <a href="{{ route('api-keys.create') }}" class="btn-primary">Generate Key</a>
            @endcan
        </div>
    </div>

    @if(session('plain_token'))
        <div class="mb-6 p-4 bg-[#e8f5e9] border border-[#c8e6c9] rounded-xl flex items-start gap-3">
            <svg class="w-6 h-6 text-[#2e7d32] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div class="flex-1">
                <h3 class="text-[#1b5e20] font-bold m-0 text-base">API Key Generated!</h3>
                <p class="text-[#2e7d32] text-sm mt-1 mb-3">Please copy this token now. You will not be able to see it again.</p>
                <div class="bg-white border border-[#c8e6c9] rounded-lg px-3 py-2 flex items-center gap-2 max-w-xl">
                    <code class="text-sm text-ink flex-1 font-mono break-all">{{ session('plain_token') }}</code>
                    <button type="button" onclick="navigator.clipboard.writeText('{{ session('plain_token') }}'); this.innerText='Copied!';" class="text-xs font-semibold bg-[#e8f5e9] hover:bg-[#c8e6c9] text-[#2e7d32] px-3 py-1.5 rounded transition-colors shrink-0 border-0 cursor-pointer">
                        Copy
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="mb-6">
        <form action="{{ route('api-keys.index') }}" method="GET" class="flex gap-2 w-full max-w-md">
            <input type="text" name="search" value="{{ request('search') ?? $search ?? '' }}" placeholder="Search keys..." class="text-input flex-1 !mb-0">
            <button type="submit" class="btn-primary px-4 py-2">Search</button>
            @if(request('search') || (isset($search) && $search != ''))
                <a href="{{ route('api-keys.index') }}" class="btn-ghost px-4 py-2 border border-hairline">Clear</a>
            @endif
        </form>
    </div>

    <div class="card p-0 shadow-none">
        <div class="table-wrapper border-none rounded-xl">
            <table>
                <thead>
                    <tr>
                        <th>Key Name</th>
                        <th>User</th>
                        <th>Services</th>
                        <th class="text-center">Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($apiKeys as $key)
                        <tr>
                            <td class="font-medium text-ink">
                                {{ $key->name }}
                                @if($key->expires_at)
                                    <div class="text-xs text-steel font-normal mt-0.5">Expires: {{ $key->expires_at->format('M d, Y') }}</div>
                                @endif
                            </td>
                            <td>{{ $key->user->name ?? 'Unknown' }}</td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($key->apiKeyServices as $pivot)
                                        <span class="badge" style="background: var(--surface); color: var(--ink);">
                                            {{ $pivot->service->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-center">
                                @if($key->is_active && (!$key->expires_at || $key->expires_at->isFuture()))
                                    <span class="badge success">Active</span>
                                @else
                                    <span class="badge" style="background: var(--surface); color: var(--steel);">Inactive / Expired</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    @can('api-keys.edit')
                                    <a href="{{ route('api-keys.edit', $key) }}" class="btn-ghost px-2 py-1 text-[13px] border border-hairline">Edit</a>
                                    @endcan
                                    
                                    @can('api-keys.delete')
                                    <form action="{{ route('api-keys.destroy', $key) }}" method="POST" class="m-0 form-delete" data-name="{{ $key->name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-ghost px-2 py-1 text-[13px] border border-hairline text-[#d45656]">Revoke</button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    No API Keys found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($apiKeys->hasPages())
        <div class="mt-6">
            {{ $apiKeys->links() }}
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
                        title: 'Revoke Key?',
                        text: `You are about to permanently delete API Key "${name}". Applications using this key will lose access!`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d45656',
                        cancelButtonColor: '#888888',
                        confirmButtonText: 'Yes, Revoke!',
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
