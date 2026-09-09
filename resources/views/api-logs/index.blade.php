@extends('layouts.app')

@section('title', 'API Logs - Email Services')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1>API Logs</h1>
            <p class="subtitle mb-0">Audit log of all API requests made using API Keys.</p>
        </div>
    </div>

    <div class="mb-6">
        <form action="{{ route('api-logs.index') }}" method="GET" class="flex gap-2 w-full max-w-md">
            <input type="text" name="search" value="{{ request('search') ?? $search ?? '' }}" placeholder="Search endpoint or IP..." class="text-input flex-1 !mb-0">
            <button type="submit" class="btn-primary px-4 py-2">Search</button>
            @if(request('search') || (isset($search) && $search != ''))
                <a href="{{ route('api-logs.index') }}" class="btn-ghost px-4 py-2 border border-hairline">Clear</a>
            @endif
        </form>
    </div>

    <div class="card p-0 shadow-none">
        <div class="table-wrapper border-none rounded-xl">
            <table>
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>API Key & User</th>
                        <th>Service</th>
                        <th>Endpoint</th>
                        <th>Status</th>
                        <th>IP / Domain</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($apiLogs as $log)
                        <tr>
                            <td class="text-steel font-mono text-xs">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                            </td>
                            <td>
                                <div class="font-medium text-ink">{{ $log->apiKey->name ?? 'Unknown Key' }}</div>
                                <div class="text-xs text-steel">{{ $log->apiKey->user->name ?? 'Unknown User' }}</div>
                            </td>
                            <td>
                                <span class="badge" style="background: var(--surface); color: var(--ink);">
                                    {{ $log->service->name ?? 'Unknown' }}
                                </span>
                            </td>
                            <td class="font-mono text-xs">
                                <span class="font-bold text-ink mr-1">{{ $log->method }}</span>
                                {{ Str::limit($log->endpoint, 40) }}
                            </td>
                            <td>
                                @if($log->status_code >= 200 && $log->status_code < 300)
                                    <span class="badge success">{{ $log->status_code }}</span>
                                @elseif($log->status_code == 429)
                                    <span class="badge" style="background: #fff8e1; color: #f57f17;">{{ $log->status_code }}</span>
                                @else
                                    <span class="badge" style="background: #ffebee; color: #d32f2f;">{{ $log->status_code }}</span>
                                @endif
                                
                                @if($log->message)
                                    <span class="text-xs text-steel ml-1 truncate max-w-[100px] inline-block align-bottom" title="{{ $log->message }}">{{ $log->message }}</span>
                                @endif
                            </td>
                            <td class="text-xs">
                                <div class="text-ink">{{ $log->ip }}</div>
                                <div class="text-steel">{{ $log->domain ?? '-' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    No logs found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if ($apiLogs->hasPages())
        <div class="mt-6">
            {{ $apiLogs->links() }}
        </div>
    @endif
@endsection
