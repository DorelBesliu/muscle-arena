<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        {{-- PWA Manifest --}}
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#F97316">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Muscle Arena 3D">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('scripts')
    </head>
    <body class="font-sans antialiased bg-[#000000] text-white" x-cloak data-layout="app">
        {{-- Toast container (top right) --}}
        <div
            class="fixed top-4 right-4 z-[100] flex flex-col gap-2 max-w-sm w-full pointer-events-none"
            x-data="toastContainer(@js(__('ui.profile_saved')))"
            x-on:profile-updated.window="addToast(savedMessage)"
            x-on:password-updated.window="addToast(savedMessage)"
            x-on:toast.window="addToast($event.detail?.message ?? $event.detail ?? savedMessage)"
        >
            <template x-for="toast in toasts" :key="toast.id">
                <div
                    x-show="true"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-8"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 translate-x-8"
                    class="pointer-events-auto px-4 py-3 rounded-lg bg-[#1e1e1e] border border-[#444444] shadow-lg text-sm text-white flex items-center gap-2"
                >
                    <span class="w-2 h-2 rounded-full bg-green-500 shrink-0"></span>
                    <span x-text="toast.message"></span>
                </div>
            </template>
        </div>

        <div
            class="flex min-h-screen bg-[#000000]"
            x-data="{ sidebarOpen: false }"
            x-on:open-sidebar.window="sidebarOpen = true"
        >
            @include('app.components.sidebar')

            <div class="flex-1 flex flex-col min-w-0">
                <livewire:components.topbar />

                @if (isset($header))
                    <header class="bg-[#111111] border-b border-[#333333]">
                        <div class="max-w-screen-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @elseif(View::hasSection('header'))
                    <header class="bg-[#111111] border-b border-[#333333]">
                        <div class="max-w-screen-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            @yield('header')
                        </div>
                    </header>
                @endif

                <main class="flex-1">
                    @isset($slot)
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endisset
                </main>
            </div>
        </div>
    </body>
</html>
