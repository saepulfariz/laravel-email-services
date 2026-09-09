@extends('layouts.app')

@section('title', 'Edit API Key - Email Services')

@section('content')
    <div class="mb-8">
        <a href="{{ route('api-keys.index') }}" class="text-steel no-underline text-sm font-medium">&larr; Back to API Keys</a>
        <h1 class="mt-4">Edit API Key</h1>
        <p class="subtitle mb-0">Update existing API key access configurations.</p>
    </div>

    @if ($errors->any())
        <div class="alert-error mb-4">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('api-keys.update', $apiKey) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Basic Info -->
            <div class="md:col-span-1">
                <div class="card h-full">
                    <div class="input-group">
                        <label>Key Name <span class="text-[#d45656]">*</span></label>
                        <input type="text" name="name" required value="{{ old('name', $apiKey->name) }}" class="text-input">
                    </div>

                    <div class="input-group">
                        <label>Expiration Date</label>
                        <input type="date" name="expires_at" value="{{ old('expires_at', $apiKey->expires_at ? $apiKey->expires_at->format('Y-m-d') : '') }}" class="text-input">
                    </div>

                    <div class="input-group">
                        <label class="flex items-center gap-2 cursor-pointer mt-2">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $apiKey->is_active) ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-ink">Active Status</span>
                        </label>
                        <p class="text-steel text-xs mt-1">If disabled, this key is rejected globally.</p>
                    </div>
                </div>
            </div>

            <!-- Services configuration -->
            <div class="md:col-span-2">
                <div class="card h-full">
                    <h3 class="mt-0 text-lg mb-6">Service Access</h3>

                    @php
                        $activeServiceIds = $apiKey->apiKeyServices->pluck('service_id')->toArray();
                        $servicePivots = $apiKey->apiKeyServices->keyBy('service_id');
                    @endphp

                    <div class="space-y-4">
                        @foreach($services as $service)
                            @php
                                $isChecked = in_array($service->id, old('services', $activeServiceIds));
                                $pivot = $servicePivots->get($service->id);
                            @endphp
                            
                            <div class="border border-hairline rounded-xl overflow-hidden">
                                <div class="bg-surface px-4 py-3 border-b border-hairline flex items-center gap-3">
                                    <input type="checkbox" name="services[]" value="{{ $service->id }}" id="service_{{ $service->id }}" 
                                        class="service-checkbox w-5 h-5 rounded border-hairline text-[#000]"
                                        {{ $isChecked ? 'checked' : '' }}>
                                    <label for="service_{{ $service->id }}" class="font-semibold text-ink cursor-pointer select-none mb-0">
                                        {{ $service->name }}
                                    </label>
                                </div>

                                <div id="settings_{{ $service->id }}" class="service-settings p-4 space-y-4 bg-white {{ $isChecked ? '' : 'hidden' }}">
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="input-group mb-0">
                                            <label class="text-xs">Allowed IPs</label>
                                            <input type="text" name="service_settings[{{ $service->id }}][allowed_ips]" 
                                                class="text-input font-mono text-sm py-1.5"
                                                value="{{ old('service_settings.'.$service->id.'.allowed_ips', $pivot ? implode(', ', (array) $pivot->allowed_ips) : '') }}">
                                        </div>
                                        <div class="input-group mb-0">
                                            <label class="text-xs">Allowed Domains</label>
                                            <input type="text" name="service_settings[{{ $service->id }}][allowed_domains]" 
                                                class="text-input font-mono text-sm py-1.5"
                                                value="{{ old('service_settings.'.$service->id.'.allowed_domains', $pivot ? implode(', ', (array) $pivot->allowed_domains) : '') }}">
                                        </div>
                                    </div>

                                    <div>
                                        <div class="flex items-center justify-between mb-2 mt-4">
                                            <label class="block text-xs font-medium text-ink mb-0">Rate Limits</label>
                                            <button type="button" class="add-rate-limit text-xs text-ink hover:text-steel font-semibold underline bg-transparent border-none cursor-pointer" data-service-id="{{ $service->id }}">+ Add Limit</button>
                                        </div>
                                        <div id="rate_limits_container_{{ $service->id }}" class="space-y-2">
                                            @if($pivot && $pivot->rateLimits->count() > 0)
                                                @foreach($pivot->rateLimits as $index => $rateLimit)
                                                <div class="flex items-center gap-2 bg-surface p-2 rounded border border-hairline">
                                                    <input type="number" name="service_settings[{{ $service->id }}][rate_limits][{{ $index }}][max_requests]" value="{{ $rateLimit->max_requests }}" class="text-input !mb-0 w-1/2 py-1.5 text-sm" required>
                                                    <span class="text-sm text-steel">per</span>
                                                    <select name="service_settings[{{ $service->id }}][rate_limits][{{ $index }}][period]" class="text-input !mb-0 w-1/3 py-1.5 text-sm">
                                                        <option value="minute" {{ $rateLimit->period == 'minute' ? 'selected' : '' }}>Minute</option>
                                                        <option value="hour" {{ $rateLimit->period == 'hour' ? 'selected' : '' }}>Hour</option>
                                                        <option value="day" {{ $rateLimit->period == 'day' ? 'selected' : '' }}>Day</option>
                                                        <option value="week" {{ $rateLimit->period == 'week' ? 'selected' : '' }}>Week</option>
                                                        <option value="month" {{ $rateLimit->period == 'month' ? 'selected' : '' }}>Month</option>
                                                    </select>
                                                    <button type="button" class="text-[#d45656] p-1 bg-transparent border-none cursor-pointer hover:opacity-80" onclick="this.parentElement.remove()">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex gap-3">
            <button type="submit" class="btn-primary">Update Configuration</button>
            <a href="{{ route('api-keys.index') }}" class="btn-ghost border border-hairline">Cancel</a>
        </div>
    </form>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.service-checkbox');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const settingsBlock = document.getElementById('settings_' + this.value);
                if (this.checked) {
                    settingsBlock.classList.remove('hidden');
                } else {
                    settingsBlock.classList.add('hidden');
                }
            });
        });

        document.querySelectorAll('.add-rate-limit').forEach(btn => {
            btn.addEventListener('click', function() {
                const serviceId = this.getAttribute('data-service-id');
                addRateLimitRow(serviceId);
            });
        });

        function addRateLimitRow(serviceId) {
            const container = document.getElementById('rate_limits_container_' + serviceId);
            const index = container.children.length + Math.floor(Math.random() * 100); 
            
            const row = document.createElement('div');
            row.className = 'flex items-center gap-2 bg-surface p-2 rounded border border-hairline';
            row.innerHTML = `
                <input type="number" name="service_settings[${serviceId}][rate_limits][${index}][max_requests]" placeholder="Max Requests" class="text-input !mb-0 w-1/2 py-1.5 text-sm" required>
                <span class="text-sm text-steel">per</span>
                <select name="service_settings[${serviceId}][rate_limits][${index}][period]" class="text-input !mb-0 w-1/3 py-1.5 text-sm">
                    <option value="minute">Minute</option>
                    <option value="hour">Hour</option>
                    <option value="day">Day</option>
                    <option value="week">Week</option>
                    <option value="month">Month</option>
                </select>
                <button type="button" class="text-[#d45656] p-1 bg-transparent border-none cursor-pointer hover:opacity-80" onclick="this.parentElement.remove()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            `;
            container.appendChild(row);
        }
    });
</script>
@endsection
