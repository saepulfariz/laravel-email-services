@extends('layouts.app')

@section('title', 'Email Logs - Email Services')

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
            <h1>Email Logs</h1>
            <p class="subtitle mb-0">View history of sent emails.</p>
        </div>
    </div>

    <div class="mb-6">
        <form action="{{ route('email-logs.index') }}" method="GET" class="flex gap-2 w-full max-w-md">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by to, subject, status..."
                class="text-input flex-1 !mb-0">
            <button type="submit" class="btn-primary px-4 py-2">Search</button>
            @if(request('search'))
                <a href="{{ route('email-logs.index') }}" class="btn-ghost px-4 py-2 border border-hairline">Clear</a>
            @endif
        </form>
    </div>

    <div class="card p-0 shadow-none">
        <div class="table-wrapper border-none rounded-xl">
            <table>
                <thead>
                    <tr>
                        <th><a href="{{ sortUrl('to') }}" class="flex items-center no-underline hover:text-ink">To
                                {!! sortIcon('to') !!}</a></th>
                        <th><a href="{{ sortUrl('subject') }}" class="flex items-center no-underline hover:text-ink">Subject
                                {!! sortIcon('subject') !!}</a></th>
                        <th><a href="{{ sortUrl('type') }}" class="flex items-center no-underline hover:text-ink">Type
                                {!! sortIcon('type') !!}</a></th>
                        <th><a href="{{ sortUrl('status') }}" class="flex items-center no-underline hover:text-ink">Status
                                {!! sortIcon('status') !!}</a></th>
                        <th><a href="{{ sortUrl('created_at') }}" class="flex items-center no-underline hover:text-ink">Sent
                                At {!! sortIcon('created_at') !!}</a></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($emailLogs as $log)
                        <tr>
                            <td>{{ $log->to }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($log->subject, 30) }}</td>
                            <td>{{ $log->type }}</td>
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
                            <td class="text-steel">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                            <td>
                                <div class="flex gap-2">
                                    <button type="button" data-log="{{ json_encode($log) }}"
                                        onclick="showPreview('{{ route('email-logs.body', $log->id) }}', this.getAttribute('data-log'))"
                                        class="btn-ghost px-2 py-1 text-[13px] border border-hairline">Show Preview</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    No email logs found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $emailLogs->links() }}
    </div>

    <!-- Preview Modal -->
    <div id="previewModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
        <div class="absolute inset-0 bg-black/50" onclick="closePreview()"></div>
        <div
            class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-4xl mx-4 my-8 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between p-4 border-b border-hairline">
                <h3 class="text-lg font-semibold m-0">Email Preview</h3>
                <button type="button" onclick="closePreview()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Email Details Section -->
            <div class="p-4 border-b border-hairline bg-gray-50/50 text-sm overflow-y-auto max-h-[35vh]">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div class="md:col-span-2"><span
                            class="text-steel block text-[11px] font-bold uppercase tracking-wider mb-1">Subject</span><strong
                            id="previewSubject" class="text-ink text-base"></strong></div>

                    <div><span class="text-steel block text-[11px] font-bold uppercase tracking-wider mb-1">Status</span>
                        <div id="previewStatus"></div>
                    </div>
                    <div><span class="text-steel block text-[11px] font-bold uppercase tracking-wider mb-1">Type</span><span
                            id="previewType" class="text-ink font-medium"></span></div>

                    <div><span class="text-steel block text-[11px] font-bold uppercase tracking-wider mb-1">To</span><span
                            id="previewTo" class="text-ink break-all"></span></div>
                    <div id="previewCcContainer" class="hidden"><span
                            class="text-steel block text-[11px] font-bold uppercase tracking-wider mb-1">CC</span><span
                            id="previewCc" class="text-ink break-all"></span></div>

                    <div id="previewBccContainer" class="hidden"><span
                            class="text-steel block text-[11px] font-bold uppercase tracking-wider mb-1">BCC</span><span
                            id="previewBcc" class="text-ink break-all"></span></div>
                    <div id="previewReplyToContainer" class="hidden"><span
                            class="text-steel block text-[11px] font-bold uppercase tracking-wider mb-1">Reply
                            To</span><span id="previewReplyTo" class="text-ink break-all"></span></div>

                </div>

                <div id="previewErrorContainer" class="mt-4 hidden p-3 bg-red-50 rounded-lg border border-red-100">
                    <span class="text-red-600 block text-[11px] font-bold uppercase tracking-wider mb-1">Error
                        Message</span>
                    <span id="previewError" class="text-red-700 break-all font-mono text-xs"></span>
                </div>

                <div id="previewAttachmentsContainer" class="mt-4 hidden">
                    <span class="text-steel block text-[11px] font-bold uppercase tracking-wider mb-1">Attachments</span>
                    <pre id="previewAttachments"
                        class="text-ink text-[11px] font-mono bg-white p-3 rounded-lg border border-hairline overflow-x-auto m-0">
                    </pre>
                </div>
            </div>

            <div class="p-0 flex-1 overflow-hidden bg-white rounded-b-xl relative min-h-[300px]">
                <!-- Loader -->
                <div id="previewLoader" class="absolute inset-0 flex items-center justify-center bg-white z-10">
                    <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
                <iframe id="previewFrame" src="about:blank" class="w-full h-full border-0"
                    onload="document.getElementById('previewLoader').style.display='none'"></iframe>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function showPreview(url, logJson) {
            const log = JSON.parse(logJson);
            const modal = document.getElementById('previewModal');
            const frame = document.getElementById('previewFrame');
            const loader = document.getElementById('previewLoader');

            // Populate text fields
            document.getElementById('previewSubject').textContent = log.subject || '-';
            document.getElementById('previewType').textContent = log.type || '-';
            document.getElementById('previewTo').textContent = log.to || '-';

            // Status badge
            const statusEl = document.getElementById('previewStatus');
            if (log.status === 'sent') {
                statusEl.innerHTML = '<span class="badge success">Sent</span>';
            } else if (log.status === 'failed') {
                statusEl.innerHTML = '<span class="badge" style="background: var(--surface); color: #d45656;">Failed</span>';
            } else {
                statusEl.innerHTML = `<span class="badge" style="background: var(--surface); color: var(--steel);">${log.status ? log.status.charAt(0).toUpperCase() + log.status.slice(1) : '-'}</span>`;
            }

            // Optional fields (hide container if empty)
            const toggleField = (id, value) => {
                const container = document.getElementById(id + 'Container');
                const el = document.getElementById(id);
                if (value) {
                    container.classList.remove('hidden');
                    el.textContent = value;
                } else {
                    container.classList.add('hidden');
                }
            };

            toggleField('previewCc', log.cc);
            toggleField('previewBcc', log.bcc);
            toggleField('previewReplyTo', log.reply_to);
            toggleField('previewError', log.error_message);

            // Attachments
            const attachmentsContainer = document.getElementById('previewAttachmentsContainer');
            const attachmentsEl = document.getElementById('previewAttachments');

            // Check if attachments is string (JSON) or array/object
            let attachmentsData = log.attachments;
            if (typeof attachmentsData === 'string' && attachmentsData.trim().length > 0) {
                try { attachmentsData = JSON.parse(attachmentsData); } catch (e) { }
            }

            if (attachmentsData && (Array.isArray(attachmentsData) ? attachmentsData.length > 0 : Object.keys(attachmentsData).length > 0)) {
                attachmentsContainer.classList.remove('hidden');
                attachmentsEl.textContent = JSON.stringify(attachmentsData, null, 2);
            } else {
                attachmentsContainer.classList.add('hidden');
            }

            // Show modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            loader.style.display = 'flex';
            frame.src = url;
        }

        function closePreview() {
            const modal = document.getElementById('previewModal');
            const frame = document.getElementById('previewFrame');

            modal.classList.add('hidden');
            modal.classList.remove('flex');

            // Reset iframe
            frame.src = 'about:blank';
        }

        // Close modal on escape key
        document.addEventListener('keydown', function (event) {
            if (event.key === "Escape") {
                closePreview();
            }
        });
    </script>
@endsection