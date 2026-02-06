@extends('layouts.public')

@section('title', __('ui.privacy_title') . ' - ' . __('ui.site_name'))

@push('meta')
    <meta name="description" content="{{ __('ui.privacy_meta_description') }}">
    <link rel="canonical" href="{{ url()->current() }}">
@endpush

@section('content')
<main>
    <section class="bg-[#000000] min-h-screen py-20 scroll-mt-[72px]">
        <div class="container mx-auto px-4 max-w-4xl">
            <div class="prose prose-invert prose-lg max-w-none">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-8">{{ __('ui.privacy_title') }}</h1>

                <h2 class="text-2xl md:text-3xl font-semibold text-white mt-8 mb-4">{{ __('ui.privacy_introduction_title') }}</h2>
                <p class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-6">
                    {{ __('ui.privacy_introduction_text') }}
                </p>

                <h2 class="text-2xl md:text-3xl font-semibold text-white mt-8 mb-4">{{ __('ui.privacy_info_collected_title') }}</h2>
                <p class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-4">
                    {{ __('ui.privacy_info_collected_intro') }}
                </p>
                <ul class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-6 list-disc list-inside space-y-2 ml-4">
                    <li><strong class="text-white">{{ __('ui.privacy_info_personal') }}</strong> {{ __('ui.privacy_info_personal_desc') }}</li>
                    <li><strong class="text-white">{{ __('ui.privacy_info_technical') }}</strong> {{ __('ui.privacy_info_technical_desc') }}</li>
                </ul>

                <h2 class="text-2xl md:text-3xl font-semibold text-white mt-8 mb-4">{{ __('ui.privacy_how_use_title') }}</h2>
                <p class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-4">
                    {{ __('ui.privacy_how_use_intro') }}
                </p>
                <ul class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-6 list-disc list-inside space-y-2 ml-4">
                    <li>{{ __('ui.privacy_use_1') }}</li>
                    <li>{{ __('ui.privacy_use_2') }}</li>
                    <li>{{ __('ui.privacy_use_3') }}</li>
                </ul>

                <h2 class="text-2xl md:text-3xl font-semibold text-white mt-8 mb-4">{{ __('ui.privacy_storage_title') }}</h2>
                <p class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-6">
                    {{ __('ui.privacy_storage_text') }}
                </p>

                <h2 class="text-2xl md:text-3xl font-semibold text-white mt-8 mb-4">{{ __('ui.privacy_rights_title') }}</h2>
                <p class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-4">
                    {{ __('ui.privacy_rights_intro') }}
                </p>
                <ul class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-6 list-disc list-inside space-y-2 ml-4">
                    <li>{{ __('ui.privacy_rights_1') }}</li>
                    <li>{{ __('ui.privacy_rights_2') }}</li>
                    <li>{{ __('ui.privacy_rights_3') }}</li>
                    <li>{{ __('ui.privacy_rights_4') }}</li>
                    <li>{{ __('ui.privacy_rights_5') }}</li>
                </ul>

                <h2 class="text-2xl md:text-3xl font-semibold text-white mt-8 mb-4">{{ __('ui.privacy_retention_title') }}</h2>
                <p class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-6">
                    {{ __('ui.privacy_retention_text') }}
                </p>

                <h2 class="text-2xl md:text-3xl font-semibold text-white mt-8 mb-4">{{ __('ui.privacy_disclosure_title') }}</h2>
                <p class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-6">
                    {{ __('ui.privacy_disclosure_text') }}
                </p>
                <ul class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-6 list-disc list-inside space-y-2 ml-4">
                    <li>{{ __('ui.privacy_disclosure_1') }}</li>
                    <li>{{ __('ui.privacy_disclosure_2') }}</li>
                </ul>

                <h2 class="text-2xl md:text-3xl font-semibold text-white mt-8 mb-4">{{ __('ui.privacy_cookies_title') }}</h2>
                <p class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-6">
                    {{ __('ui.privacy_cookies_text') }}
                </p>

                <h2 class="text-2xl md:text-3xl font-semibold text-white mt-8 mb-4">{{ __('ui.privacy_changes_title') }}</h2>
                <p class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-6">
                    {{ __('ui.privacy_changes_text') }}
                </p>

                <h2 class="text-2xl md:text-3xl font-semibold text-white mt-8 mb-4">{{ __('ui.privacy_contact_title') }}</h2>
                <p class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-4">
                    {{ __('ui.privacy_contact_text') }}
                </p>
                <ul class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-6 list-none space-y-2 ml-0">
                    <li><strong class="text-white">{{ __('ui.privacy_contact_email') }}</strong> info@musclearena.md</li>
                    <li><strong class="text-white">{{ __('ui.privacy_contact_phone') }}</strong> +373 60 123 456</li>
                    <li><strong class="text-white">{{ __('ui.privacy_contact_address') }}</strong> {{ __('ui.location') }}</li>
                </ul>

                <p class="text-[#CCCCCC] text-sm md:text-base mt-8 italic">
                    {{ __('ui.privacy_updated') }}
                </p>
            </div>
        </div>
    </section>
</main>
@endsection
