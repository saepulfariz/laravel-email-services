@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1>Recent Activity</h1>
    <p class="subtitle">View the latest emails processed by the service.</p>

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
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                <span class="badge {{ strtolower($log->status) }}">
                                    {{ $log->status }}
                                </span>
                            </td>
                            <td>
                                <div class="truncate" title="{{ $log->to }}">
                                    {{ $log->to }}
                                </div>
                            </td>
                            <td>
                                <div class="truncate" title="{{ $log->subject }}">
                                    {{ $log->subject }}
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
