<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Email Services')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Geist+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <nav class="top-nav">
        <div class="flex items-center gap-4">
            @auth
                <button class="hamburger" onclick="toggleSidebar()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
            @endauth
            <div class="brand">
                <div class="brand-logo"></div>
                Email Services
            </div>
        </div>

        <div class="nav-links">
            @auth
                <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}">Dashboard</a>
                @can('users.view')
                    <a href="/users" class="{{ request()->is('users*') ? 'active' : '' }}">Users</a>
                @endcan
                @can('roles.view')
                    <a href="/roles" class="{{ request()->is('roles*') ? 'active' : '' }}">Roles</a>
                @endcan
                @can('permissions.view')
                    <a href="/permissions" class="{{ request()->is('permissions*') ? 'active' : '' }}">Permissions</a>
                @endcan
            @endauth
            <a href="/api/documentation">API Reference</a>
        </div>

        <div class="flex items-center gap-3">
            @auth
                <form action="/logout" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn-ghost">Logout</button>
                </form>
            @else
                <a href="/login" class="btn-ghost">Login</a>
            @endauth
            <a href="/api/documentation" class="btn-primary"
                style="background: var(--brand-green); color: var(--primary);">View Docs</a>
        </div>
    </nav>

    <div class="layout {{ auth()->check() ? 'has-sidebar' : '' }}">
        @auth
            <div class="overlay" onclick="toggleSidebar()"></div>
            <aside class="sidebar" id="sidebar">
                <div class="flex-1 flex flex-col gap-2">
                    <div class="sidebar-section">Overview</div>
                    <a href="/dashboard" class="sidebar-item {{ request()->is('dashboard') ? 'active' : '' }}">Recent
                        Activity</a>

                    <div class="sidebar-section">Configuration</div>
                    @can('users.view')
                        <a href="/users" class="sidebar-item {{ request()->is('users*') ? 'active' : '' }}">Manage Users</a>
                    @endcan
                    @can('roles.view')
                        <a href="/roles" class="sidebar-item {{ request()->is('roles*') ? 'active' : '' }}">Manage Roles</a>
                    @endcan
                    @can('permissions.view')
                        <a href="/permissions" class="sidebar-item {{ request()->is('permissions*') ? 'active' : '' }}">Manage
                            Permissions</a>
                    @endcan
                    <a href="#" class="sidebar-item">API Keys</a>
                </div>

                <div class="mt-auto pt-4 border-t border-hairline-soft flex items-center gap-3 px-2">
                    @if(auth()->user()->image)
                        <img src="{{ Storage::url(auth()->user()->image) }}" alt="Profile"
                            class="w-9 h-9 rounded-full object-cover border border-hairline">
                    @else
                        <div
                            class="w-9 h-9 rounded-full bg-surface border border-hairline flex items-center justify-center text-steel font-medium text-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="flex-1 overflow-hidden">
                        <div class="text-sm font-medium text-ink truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[11px] text-steel truncate">
                            {{ auth()->user()->username ? '@' . auth()->user()->username : auth()->user()->email }}
                        </div>
                    </div>
                </div>
            </aside>
        @endauth

        <main class="main-content @guest max-w-full flex justify-center items-center pt-[10vh] @endguest">
            @yield('content')
        </main>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar')?.classList.toggle('open');
            document.querySelector('.overlay')?.classList.toggle('open');
        }

        document.addEventListener('DOMContentLoaded', function () {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#00d4a4',
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "{{ session('error') }}",
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d45656',
                });
            @endif
        });
    </script>
    @yield('scripts')
</body>

</html>