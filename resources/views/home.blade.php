@extends('layouts.public')

@section('title', __('ui.meta_title'))

@push('meta')
    <meta name="description" content="{{ __('ui.meta_description') }}">
    @foreach (config('locales.supported', ['ro', 'ru', 'en']) as $loc)
        <link rel="alternate" hreflang="{{ $loc }}" href="{{ url('/'.$loc) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ url('/'.config('locales.default', 'ro')) }}">
    <link rel="canonical" href="{{ url()->current() }}">
@endpush

@section('content')
<main>
    {{-- Hero Section (React-style: bg #111, orbs, grid, image right) --}}
    <section id="hero" class="bg-[#111111] relative overflow-hidden scroll-mt-[72px] pt-[72px]">
        {{-- Background decoration --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/2 left-0 w-96 h-96 bg-[#F97316] rounded-full blur-3xl opacity-5"></div>
            <div class="absolute top-1/2 right-0 w-96 h-96 bg-[#F97316] rounded-full blur-3xl opacity-5"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="grid items-center min-h-[370px] md:min-h-[600px] mt-10">
                {{-- Left: Text content (75% width on md) --}}
                <div class="py-6 md:py-20 md:pr-12 lg:pr-16 w-full md:w-3/4">
                    <div class="mb-8">
                        <h2 class="text-2xl md:text-5xl lg:text-6xl font-black mb-4 leading-tight">
                            <span class="text-white block">{{ __('ui.hero_title_line1') }}</span>
                            <span class="text-[#F97316] block mt-2">{{ __('ui.hero_title_line2') }}</span>
                        </h2>
                        <div class="h-1 w-24 bg-[#F97316] mb-6"></div>
                    </div>

                    <p class="text-sm md:text-xl text-[#CCCCCC] leading-snug max-w-3xl mb-8">
                        {{ __('ui.hero_description') }}
                    </p>

                    @guest
                        <a href="{{ route('register') }}" class="bg-[#F97316] hover:bg-[#EF4444] text-white font-bold px-6 md:px-8 py-3 md:py-4 rounded-xl transition-all hover:scale-105 shadow-xl inline-flex items-center text-base md:text-lg gap-2 md:gap-3">
                            {{ __('ui.cta_join') }}
                            <x-lucide-target class="w-4 h-4 md:w-5 md:h-5 shrink-0" />
                        </a>
                    @else
                        <a href="{{ url('/dashboard') }}" class="bg-[#F97316] hover:bg-[#EF4444] text-white font-bold px-6 md:px-8 py-3 md:py-4 rounded-xl transition-all hover:scale-105 shadow-xl inline-flex items-center text-base md:text-lg gap-2 md:gap-3">
                            {{ __('ui.cta_dashboard') }}
                            <x-lucide-target class="w-4 h-4 md:w-5 md:h-5 shrink-0" />
                        </a>
                    @endguest
                </div>
            </div>
        </div>

        {{-- Right: Background image --}}
        <div class="absolute top-0 right-0 bottom-0 w-full md:w-1/3 pointer-events-none">
            <div
                class="absolute inset-0 bg-cover bg-center"
                style="background-image: linear-gradient(to right, rgba(17,17,17,1), rgba(17,17,17,0.3)), url('{{ asset('storage/men-and-woman-make-sport.png') }}');"
            >
                <div class="absolute inset-0 bg-gradient-to-r from-[#111111] via-[#111111]/80 to-transparent md:via-[#111111]/40" aria-hidden="true"></div>
            </div>
        </div>
    </section>

    {{-- Placeholder sections for topbar scroll targets --}}
    <section id="about" class="scroll-mt-[72px] py-16 container mx-auto px-4"><h2 class="text-2xl font-bold text-white">{{ __('ui.section_about_title') }}</h2><p class="text-[#CCCCCC] mt-2">{{ __('ui.section_placeholder') }}</p></section>
    <section id="investment" class="scroll-mt-[72px] py-16 container mx-auto px-4"><h2 class="text-2xl font-bold text-white">{{ __('ui.section_investment_title') }}</h2><p class="text-[#CCCCCC] mt-2">{{ __('ui.section_placeholder') }}</p></section>
    <section id="timeline" class="scroll-mt-[72px] py-16 container mx-auto px-4"><h2 class="text-2xl font-bold text-white">{{ __('ui.section_timeline_title') }}</h2><p class="text-[#CCCCCC] mt-2">{{ __('ui.section_placeholder') }}</p></section>
    <section id="contact" class="scroll-mt-[72px] py-16 container mx-auto px-4"><h2 class="text-2xl font-bold text-white">{{ __('ui.section_contact_title') }}</h2><p class="text-[#CCCCCC] mt-2">{{ __('ui.section_placeholder') }}</p></section>
</main>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('a[data-scroll-section][href^="#"]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            var id = this.getAttribute('href');
            if (id === '#') return;
            var el = document.querySelector(id);
            if (!el) return;
            e.preventDefault();
            var navHeight = 72;
            var top = el.getBoundingClientRect().top + window.pageYOffset - navHeight;
            window.scrollTo({ top: top, behavior: 'smooth' });
            if (window.Alpine && Alpine.store('public')) Alpine.store('public').mobileMenuOpen = false;
        });
    });
</script>
@endpush
