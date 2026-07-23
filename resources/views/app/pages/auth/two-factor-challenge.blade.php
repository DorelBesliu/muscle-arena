@extends('layouts.public')

@section('title', __('Two-Factor Challenge'))

@section('content')
<main class="min-h-[calc(100vh-72px)] pt-[72px] flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-lg">
        <div class="bg-[#111111] border-2 border-[#333333] rounded-3xl p-8 md:p-10">
            <div class="text-center mb-8">
                <h2 class="text-4xl font-black text-white mb-2">{{ __('ui.enter_code') }}</h1>
            </div>

            @if ($errors->any())
                <div class="mb-1 bg-red-500/10 text-red-400 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('two-factor.login.store') }}" class="space-y-6" x-data="{ loading: false }" x-on:submit="loading = true">
                @csrf
                <div>
                    <input type="text" id="code" name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6"
                           value="{{ old('code') }}"
                           class="w-full bg-[#000000] border-2 border-[#333333] rounded-xl py-3 px-4 text-white placeholder-[#666666] focus:border-[#F97316] focus:outline-none transition-colors"
                           placeholder="000000" required autofocus>
                </div>
                <button type="submit" x-bind:disabled="loading"
                    class="w-full inline-flex items-center justify-center gap-2 bg-[#F97316] hover:bg-[#ea580c] disabled:opacity-70 text-white font-bold py-4 rounded-xl transition-all shadow-xl">
                    <span x-show="!loading">{{ __('ui.profile_verify') }}</span>
                    <span x-show="loading" x-cloak><x-lucide-loader-2 class="w-6 h-6 animate-spin" /></span>
                </button>
            </form>
        </div>
    </div>
</main>
@endsection
