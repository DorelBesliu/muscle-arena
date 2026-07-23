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
        {{-- Toast container (top center) --}}
        <div
            class="fixed top-4 left-1/2 -translate-x-1/2 z-[100] flex flex-col gap-2 max-w-sm w-full pointer-events-none"
            x-data="toastContainer(@js(__('ui.profile_saved')))"
            x-on:profile-updated.window="addToast(savedMessage)"
            x-on:password-updated.window="addToast(savedMessage)"
            x-on:toast.window="addToast($event.detail?.message ?? $event.detail ?? savedMessage, $event.detail?.type ?? 'success')"
        >
            <template x-for="toast in toasts" :key="toast.id">
                <div
                    x-show="true"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="pointer-events-auto px-4 py-3 rounded-lg bg-[#1e1e1e] border border-[#444444] shadow-lg text-sm text-white flex items-center gap-2"
                >
                    <span class="w-2 h-2 rounded-full shrink-0" :class="toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'"></span>
                    <span x-text="toast.message"></span>
                </div>
            </template>
        </div>

        <div
            x-show="Alpine.store('confirmPassword').open" x-cloak
            x-data="confirmPassword('{{ route('password.confirm.store') }}', '{{ addslashes(__('ui.confirm_password_label')) }}')"
            @password-confirmed.window="Alpine.store('confirmPassword').runAfterConfirm?.(); Alpine.store('confirmPassword').runAfterConfirm = null; Alpine.store('confirmPassword').open = false"
            @close-password-dialog.window="Alpine.store('confirmPassword').open = false; Alpine.store('confirmPassword').runAfterConfirm = null"
            @confirm-password.window="confirmPassword()"
            class="fixed inset-0 z-[90] flex items-center justify-center p-4 bg-black/70"
        >
            <x-confirm-password-dialog>
                <x-slot:close>
                    <button type="button" @click="$dispatch('close-password-dialog')" class="h-8 w-8 flex items-center justify-center rounded-lg border-2 border-[#333333] text-[#CCCCCC] hover:text-white">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </x-slot:close>
                <x-slot:footer>
                    <button type="button" @click="$dispatch('close-password-dialog')" class="h-9 px-4 rounded-xl border-2 border-[#333333] text-sm font-medium text-[#CCCCCC] hover:text-white">
                        {{ __('ui.close') }}
                    </button>
                    <button type="button" @click="confirmPassword()" :disabled="loading" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-[#F97316] hover:bg-[#ea580c] rounded-lg transition-colors disabled:opacity-70 disabled:cursor-wait">
                        <span x-show="loading" class="inline-flex items-center gap-2">
                            <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                            {{ __('ui.confirm_password_button') }}
                        </span>
                        <span x-show="!loading" x-cloak>{{ __('ui.confirm_password_button') }}</span>
                    </button>
                </x-slot:footer>
            </x-confirm-password-dialog>
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
