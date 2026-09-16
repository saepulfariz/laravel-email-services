@extends('layouts.app')

@section('title', 'Dashboard - Email Services')

@section('content')
    <div class="mb-8">
        <h1>Dashboard Overview</h1>
        <p class="subtitle">Quick insights into your email delivery performance.</p>
    </div>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Sent -->
        <div class="card bg-white p-6 rounded-xl border border-hairline hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-steel text-sm font-medium uppercase tracking-wider mb-1">Total Sent</p>
                    <h2 class="text-3xl font-bold text-ink m-0">{{ number_format($stats['total_sent']) }}</h2>
                </div>
                <div class="w-12 h-12 bg-green-50 text-green-600 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Failed -->
        <div class="card bg-white p-6 rounded-xl border border-hairline hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-steel text-sm font-medium uppercase tracking-wider mb-1">Total Failed</p>
                    <h2 class="text-3xl font-bold text-red-600 m-0">{{ number_format($stats['total_failed']) }}</h2>
                </div>
                <div class="w-12 h-12 bg-red-50 text-red-600 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Services -->
        <div class="card bg-white p-6 rounded-xl border border-hairline hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-steel text-sm font-medium uppercase tracking-wider mb-1">Active Services</p>
                    <h2 class="text-3xl font-bold text-ink m-0">{{ number_format($stats['total_services']) }}</h2>
                </div>
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- API Keys -->
        <div class="card bg-white p-6 rounded-xl border border-hairline hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-steel text-sm font-medium uppercase tracking-wider mb-1">Active API Keys</p>
                    <h2 class="text-3xl font-bold text-ink m-0">{{ number_format($stats['active_keys']) }}</h2>
                </div>
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold m-0">Recent Activity</h2>
        <a href="{{ route('email-logs.index') }}" class="text-sm font-medium text-brand hover:underline">View All Logs</a>
    </div>

    <div class="card p-0 shadow-none">
        <div class="table-wrapper border-none rounded-xl">
            <table>
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Recipient</th>
                        <th>Subject</th>
                        <th>Type</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLogs as $log)
                        <tr>
                            <td>
                                @if($log->status === 'sent')
                                    <span class="badge success">Sent</span>
                                @elseif($log->status === 'failed')
                                    <span class="badge" style="background: var(--surface); color: #d45656;">Failed</span>
                                @else
                                    <span class="badge"
                                        style="background: var(--surface); color: var(--steel);">{{ ucfirst($log->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="truncate text-ink max-w-[200px]" title="{{ $log->to }}">
                                    @php
                                        $toCount = explode(';', $log->to);
                                    @endphp
                                    {{ count($toCount) > 1 ? $toCount[0] . ', ...' : $log->to }}
                                </div>
                            </td>
                            <td>
                                <div class="truncate text-ink max-w-[250px]" title="{{ $log->subject }}">
                                    {{ \Illuminate\Support\Str::limit($log->subject, 30) }}
                                </div>
                            </td>
                            <td><span class="code-sm">{{ $log->type }}</span></td>
                            <td class="text-steel">{{ $log->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    No emails sent yet.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection