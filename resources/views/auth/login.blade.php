@extends('layouts.app')

@section('title', 'Log in - Email Services')

@section('content')
    <div class="flex flex-col items-center justify-center min-h-[calc(100vh-120px)] px-6 py-12 w-full">
        <div
            class="bg-canvas border border-hairline rounded-xl px-8 py-10 w-full max-w-[400px] shadow-[0_4px_12px_0_rgba(0,0,0,0.04)]">
            <h1 class="text-2xl font-semibold mb-2 text-center">Welcome back</h1>
            <p class="text-steel text-center text-sm mb-6">Log in to manage your email services.</p>

            @if ($errors->any())
                <div class="alert-error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="/login" method="POST">
                @csrf

                <div class="input-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="text-input" required autofocus
                        value="{{ old('email') }}">
                </div>

                <div class="input-group mt-4">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="text-input" required>
                </div>

                <button type="submit" class="btn-primary w-full mt-6 py-3 text-[15px] flex justify-center">Log in</button>
                @php
                    $activeSsoProviders = \App\Models\SsoProvider::where('is_active', true)->get();
                @endphp

                @if($activeSsoProviders->count() > 0)
                    <div class="relative mt-4 mb-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-hairline"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-canvas text-steel text-[12px] font-medium tracking-wide">
                                OR SIGN IN WITH
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        @foreach($activeSsoProviders as $provider)
                            <a href="{{ route('sso.redirect', $provider->name) }}"
                                class="btn-ghost w-full flex items-center justify-center gap-2 border border-hairline py-2.5 text-[14px] no-underline">
                                @if($provider->icon && $provider->icon !== '-')
                                    <i class="{{ $provider->icon }} text-lg"></i>
                                @endif
                                Sign in using {{ ucfirst($provider->name) }}
                            </a>
                        @endforeach
                    </div>
                @endif

            </form>
        </div>
    </div>
@endsection