@extends('layouts.app')

@section('title', 'Log in - Email Services')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[calc(100vh-120px)] px-6 py-12 w-full">
    <div class="bg-canvas border border-hairline rounded-xl px-8 py-10 w-full max-w-[400px] shadow-[0_4px_12px_0_rgba(0,0,0,0.04)]">
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
                <input type="email" id="email" name="email" class="text-input" required autofocus value="{{ old('email') }}">
            </div>

            <div class="input-group mt-4">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="text-input" required>
            </div>

            <button type="submit" class="btn-primary w-full mt-6 py-3 text-[15px] flex justify-center">Log in</button>
        </form>
    </div>
</div>
@endsection
