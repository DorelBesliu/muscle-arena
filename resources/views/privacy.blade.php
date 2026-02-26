@extends('layouts.public')

@section('title', __('ui.privacy_title') . ' - ' . __('ui.site_name'))

@push('meta')
    <meta name="description" content="{{ __('ui.privacy_meta_description') }}">
    <link rel="canonical" href="{{ url()->current() }}">
@endpush

@php
    $privacyData = \App\Models\SiteContent::get('privacy_content_' . app()->getLocale());
    $privacyContent = (string) ($privacyData['content'] ?? '');
@endphp

@section('content')
<main>
    <section class="bg-[#000000] min-h-screen pt-24 pb-20 scroll-mt-[72px]">
        <div class="container mx-auto px-4">
            <div class="prose prose-invert prose-lg max-w-none prose-headings:text-white prose-p:text-[#CCCCCC] prose-li:text-[#CCCCCC] prose-strong:text-white">
                @if ($privacyContent !== '')
                    <div class="privacy-db-content
                        [&_h1]:text-4xl [&_h1]:md:text-5xl [&_h1]:font-bold [&_h1]:text-white [&_h1]:mb-8
                        [&_h2]:text-2xl [&_h2]:md:text-3xl [&_h2]:font-semibold [&_h2]:text-white [&_h2]:mt-8 [&_h2]:mb-4
                        [&_h3]:text-xl [&_h3]:md:text-2xl [&_h3]:font-semibold [&_h3]:text-white [&_h3]:mt-6 [&_h3]:mb-3
                        [&_p]:text-[#CCCCCC] [&_p]:text-base [&_p]:md:text-lg [&_p]:leading-relaxed [&_p]:mb-6
                        [&_ul]:list-disc [&_ul]:list-inside [&_ul]:space-y-2 [&_ul]:ml-4 [&_ul]:mb-6 [&_ul]:text-[#CCCCCC] [&_ul]:text-base [&_ul]:md:text-lg
                        [&_li]:text-[#CCCCCC]
                        [&_li_p]:inline [&_li_p]:m-0 [&_li_p]:first:mt-0 [&_li_p]:last:mb-0
                        [&_strong]:text-white">
                        {!! $privacyContent !!}
                    </div>
                @else
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

                    <h2 id="cookies" class="text-2xl md:text-3xl font-semibold text-white mt-8 mb-4">{{ __('ui.privacy_cookies_title') }}</h2>
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
                @endif
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-[#111111] border-t border-[#333333] py-8 mt-auto">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-sm text-[#CCCCCC]">
                    {{ __('ui.footerText') }}
                </p>
                <div class="flex items-center gap-6" x-data>
                    <a
                        href="{{ route('privacy', ['locale' => app()->getLocale()]) }}"
                        class="text-sm text-[#CCCCCC] hover:text-[#F97316] transition-colors underline underline-offset-2"
                    >
                        {{ __('ui.privacyPolicy') }}
                    </a>
                    <a
                        href="{{ route('terms', ['locale' => app()->getLocale()]) }}"
                        class="text-sm text-[#CCCCCC] hover:text-[#F97316] transition-colors underline underline-offset-2"
                    >
                        {{ __('ui.termsConditions') }}
                    </a>
                    @if(config('services.google_analytics.measurement_id'))
                        <button
                            type="button"
                            @click="$store.cookieConsent.showBanner = true"
                            class="text-sm text-[#CCCCCC] hover:text-[#F97316] transition-colors underline underline-offset-2"
                        >
                            {{ __('ui.cookie_consent_settings') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </footer>
</main>
@endsection
