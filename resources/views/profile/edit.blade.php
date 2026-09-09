@extends('layouts.app')

@section('title', 'Profile Settings - Email Services')

@section('content')
    <div class="mb-8">
        <h1 class="mt-4">Profile Settings</h1>
        <p class="subtitle mb-0">Update your profile information and security settings.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card">
            <h2 class="text-xl mb-4 font-semibold text-ink">Change Password</h2>
            
            @if ($errors->any())
                <div class="alert-error mb-4">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('profile.password.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="input-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" class="text-input" required>
                </div>

                <div class="input-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" class="text-input" required>
                </div>

                <div class="input-group">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="text-input" required>
                </div>

                <div class="mt-6">
                    <button type="submit" class="btn-primary">Update Password</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h2 class="text-xl mb-2 font-semibold text-ink">SSO Providers</h2>
            <p class="text-steel text-sm mb-6">Link your account with supported Single Sign-On providers.</p>

            <div class="space-y-4">
                @foreach($ssoProviders as $provider)
                    @php
                        $linked = $user->ssoAccounts->where('sso_provider_id', $provider->id)->first();
                    @endphp
                    <div class="flex items-center justify-between p-4 border border-hairline rounded-xl bg-surface">
                        <div class="flex items-center gap-3">
                            @if($provider->icon && $provider->icon !== '-')
                                <i class="{{ $provider->icon }} text-2xl text-steel"></i>
                            @else
                                <div class="w-10 h-10 rounded-full bg-white border border-hairline flex items-center justify-center font-bold text-ink">
                                    {{ substr($provider->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <h3 class="m-0 text-base font-semibold">{{ $provider->name }}</h3>
                                @if($linked)
                                    <span class="text-xs" style="color: #00d4a4; font-weight: 500;">Linked ({{ $linked->email ?? $linked->provider_account_email }})</span>
                                @else
                                    <span class="text-xs text-steel font-medium">Not linked</span>
                                @endif
                            </div>
                        </div>
                        
                        @if($linked)
                            <form action="{{ route('sso.unlink', $provider->name) }}" method="POST" class="m-0 form-delete" data-name="{{ $provider->name }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-ghost text-[#d45656] border border-hairline px-3 py-1.5 text-sm">Unlink</button>
                            </form>
                        @else
                            <a href="{{ route('sso.redirect', $provider->name) }}" class="btn-ghost border border-hairline px-3 py-1.5 text-sm">Link Account</a>
                        @endif
                    </div>
                @endforeach

                @if($ssoProviders->isEmpty())
                    <div class="empty-state">
                        No SSO providers are currently active.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.form-delete');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const providerName = this.getAttribute('data-name');
                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you want to unlink your ${providerName} account?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d45656',
                    cancelButtonColor: '#888888',
                    confirmButtonText: 'Yes, unlink it!'
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
