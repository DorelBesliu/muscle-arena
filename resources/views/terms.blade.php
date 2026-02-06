@extends('layouts.public')

@section('title', __('ui.terms_title') . ' - ' . __('ui.site_name'))

@push('meta')
    <meta name="description" content="{{ __('ui.terms_meta_description') }}">
    <link rel="canonical" href="{{ url()->current() }}">
@endpush

@section('content')
<main>
    <section class="bg-[#000000] min-h-screen py-20 scroll-mt-[72px]">
        <div class="container mx-auto px-4 max-w-4xl">
            <div class="prose prose-invert prose-lg max-w-none">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-8">{{ __('ui.terms_title') }}</h1>
                
                <h2 class="text-2xl md:text-3xl font-semibold text-white mt-8 mb-4">{{ __('ui.terms_section_1_title') }}</h2>
                <p class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-6">
                    {{ __('ui.terms_section_1_text') }}
                </p>

                <h2 class="text-2xl md:text-3xl font-semibold text-white mt-8 mb-4">{{ __('ui.terms_section_2_title') }}</h2>
                <p class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-4">
                    {{ __('ui.terms_section_2_intro') }}
                </p>
                <ul class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-6 list-disc list-inside space-y-2 ml-4">
                    <li>{{ __('ui.terms_section_2_1') }}</li>
                    <li>{{ __('ui.terms_section_2_2') }}</li>
                    <li>{{ __('ui.terms_section_2_3') }}</li>
                    <li>{{ __('ui.terms_section_2_4') }}</li>
                </ul>

                <h2 class="text-2xl md:text-3xl font-semibold text-white mt-8 mb-4">{{ __('ui.terms_section_3_title') }}</h2>
                <p class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-4">
                    {{ __('ui.terms_section_3_intro') }}
                </p>
                <ul class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-6 list-disc list-inside space-y-2 ml-4">
                    <li>{{ __('ui.terms_section_3_1') }}</li>
                    <li>{{ __('ui.terms_section_3_2') }}</li>
                    <li>{{ __('ui.terms_section_3_3') }}</li>
                </ul>

                <h2 class="text-2xl md:text-3xl font-semibold text-white mt-8 mb-4">{{ __('ui.terms_section_4_title') }}</h2>
                <p class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-4">
                    {{ __('ui.terms_section_4_intro') }}
                </p>
                <ul class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-6 list-disc list-inside space-y-2 ml-4">
                    <li>{{ __('ui.terms_section_4_1') }}</li>
                    <li>{{ __('ui.terms_section_4_2') }}</li>
                    <li>{{ __('ui.terms_section_4_3') }}</li>
                    <li>{{ __('ui.terms_section_4_4') }}</li>
                </ul>

                <h2 class="text-2xl md:text-3xl font-semibold text-white mt-8 mb-4">{{ __('ui.terms_section_5_title') }}</h2>
                <p class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-6">
                    {{ __('ui.terms_section_5_text') }}
                </p>

                <h2 class="text-2xl md:text-3xl font-semibold text-white mt-8 mb-4">{{ __('ui.terms_section_6_title') }}</h2>
                <p class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-6">
                    {{ __('ui.terms_section_6_text') }}
                </p>

                <h2 class="text-2xl md:text-3xl font-semibold text-white mt-8 mb-4">{{ __('ui.terms_section_7_title') }}</h2>
                <p class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-4">
                    {{ __('ui.terms_section_7_intro') }}
                </p>
                <ul class="text-[#CCCCCC] text-base md:text-lg leading-relaxed mb-6 list-none space-y-2 ml-0">
                    <li><strong class="text-white">{{ __('ui.terms_contact_email') }}</strong> info@musclearena.md</li>
                    <li><strong class="text-white">{{ __('ui.terms_contact_phone') }}</strong> +373 60 123 456</li>
                    <li><strong class="text-white">{{ __('ui.terms_contact_address') }}</strong> {{ __('ui.location') }}</li>
                </ul>

                <p class="text-[#CCCCCC] text-sm md:text-base mt-8 italic">
                    {{ __('ui.terms_updated') }}
                </p>
            </div>
        </div>
    </section>
</main>
@endsection
