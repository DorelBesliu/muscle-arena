@extends('layouts.public')

@section('title', __('ui.error_404_title') . ' - ' . __('ui.site_name'))

@section('content')
@php
    $locale = session('locale') ?: app()->getLocale() ?: config('locales.default', 'ro');
    $locale = in_array($locale, ['ro', 'en', 'ru'], true) ? $locale : config('locales.default', 'ro');
@endphp
<main class="min-h-[calc(100vh-72px)] pt-[72px] flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-[#111111] border-2 border-[#333333] rounded-3xl p-8 md:p-10 text-center">
            <div class="mb-6 flex justify-center">
                <div class="w-24 h-24 bg-[#333333] rounded-full flex items-center justify-center">
                    <span class="text-4xl font-black text-[#F97316]">404</span>
                </div>
            </div>
            <h1 class="text-2xl md:text-3xl font-black text-white mb-3">
                {{ __('ui.error_404_title') }}
            </h1>
            <p class="text-[#CCCCCC] text-sm md:text-base mb-8">
                {{ __('ui.error_404_message') }}
            </p>
            <a
                href="{{ route('home', ['locale' => $locale]) }}"
                class="inline-flex items-center justify-center gap-2 w-full bg-[#F97316] hover:bg-[#EF4444] text-white font-bold py-4 rounded-xl transition-all hover:scale-105 shadow-xl"
            >
                <x-lucide-arrow-left class="w-5 h-5 shrink-0" />
                {{ __('ui.error_404_back') }}
            </a>
        </div>
    </div>
</main>
@endsection
