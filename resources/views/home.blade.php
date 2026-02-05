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
    {{-- Start Hero Section --}}
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

                    <p class="text-sm md:text-xl text-secondary leading-snug max-w-3xl mb-8">
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
                style="background-image: linear-gradient(to right, rgba(17,17,17,1), rgba(17,17,17,0.3)), url('{{ asset('images/men-and-woman-make-sport.png') }}');"
            >
                <div class="absolute inset-0 bg-gradient-to-r from-[#111111] via-[#111111]/80 to-transparent md:via-[#111111]/40" aria-hidden="true"></div>
            </div>
        </div>
    </section>
    {{-- End Hero Section --}}

    {{-- Start About Section --}}
    <section id="about" class="bg-[#000000] py-8 md:py-16 relative scroll-mt-[72px]" data-gym3d-logo="{{ asset('images/logo-white.svg') }}">
        <div class="container mx-auto px-4">
            <div class="mx-auto">
                <div class="grid items-start">
                    <div>
                        <h2 class="text-2xl md:text-[36px] font-black text-white mb-6 leading-tight">
                            {{ __('ui.section_about_title') }}
                        </h2>
                        <p class="text-sm md:text-lg text-secondary leading-relaxed w-full mb-12">
                            {{ __('ui.about_description') }}
                        </p>
                    </div>
                </div>

                {{-- 3D Gym viewer (Three.js) --}}
                <div class="mb-16 gym3d-wrapper" x-data="gym3dData()" id="gym3d-wrapper">
                    <div class="relative h-[50vh] min-h-[300px] bg-[#1a1a1a]" id="gym3d-container">
                        <div id="canvas-container" class="absolute inset-0"></div>
                        {{-- Overlay to block interactions when not in fullscreen (mobile only) --}}
                        <template x-if="isNotFullscreen">
                            <div x-transition
                                 id="gym3d-overlay"
                                 class="md:hidden absolute inset-0 z-50 bg-[#1a1a1a]/30 backdrop-blur-[1px] flex items-center justify-center pointer-events-auto">
                                <div class="gym3d-overlay-card relative z-10">
                                    <h3 class="text-white text-sm font-medium mb-1">{{ __('ui.gym3d_mobile_tap_title') }}</h3>
                                    <p class="text-secondary text-xs">{{ __('ui.gym3d_mobile_tap_description') }}</p>
                                </div>
                            </div>
                        </template>
                        {{-- Mobile controls bar: inside container, positioned absolutely, only visible in fullscreen --}}
                        <template x-if="gym3dFullscreen">
                            <div class="gym3d-controls-bar gym3d-controls-bar-mobile md:hidden">
                                <button type="button" id="btn-zoom-in" title="{{ __('ui.gym3d_btn_zoom_in') }}"><x-lucide-zoom-in class="w-5 h-5" /></button>
                                <button type="button" id="btn-zoom-out" title="{{ __('ui.gym3d_btn_zoom_out') }}"><x-lucide-zoom-out class="w-5 h-5" /></button>
                                <button type="button" id="btn-rotate-left" title="{{ __('ui.gym3d_btn_rotate_left') }}"><x-lucide-rotate-ccw class="w-5 h-5" /></button>
                                <button type="button" id="btn-rotate-right" title="{{ __('ui.gym3d_btn_rotate_right') }}"><x-lucide-rotate-cw class="w-5 h-5" /></button>
                                <button type="button" id="btn-tilt-up" title="{{ __('ui.gym3d_btn_tilt_up') }}"><x-lucide-chevron-up class="w-5 h-5" /></button>
                                <button type="button" id="btn-tilt-down" title="{{ __('ui.gym3d_btn_tilt_down') }}"><x-lucide-chevron-down class="w-5 h-5" /></button>
                                <button type="button" id="btn-move-forward" title="{{ __('ui.gym3d_btn_move_forward') }}"><x-lucide-arrow-up class="w-5 h-5" /></button>
                                <button type="button" id="btn-move-back" title="{{ __('ui.gym3d_btn_move_back') }}"><x-lucide-arrow-down class="w-5 h-5" /></button>
                                <button type="button" id="btn-move-left" title="{{ __('ui.gym3d_btn_move_left') }}"><x-lucide-arrow-left class="w-5 h-5" /></button>
                                <button type="button" id="btn-move-right" title="{{ __('ui.gym3d_btn_move_right') }}"><x-lucide-arrow-right class="w-5 h-5" /></button>
                            </div>
                        </template>
                        {{-- Info icon: opens dialog with drag hint (desktop only) --}}
                        <button type="button" @click="$store.gym3d.helpOpen = true" class="gym3d-control-btn hidden md:flex absolute top-4 left-4 z-20" aria-label="{{ __('ui.gym3d_drag_hint') }}" style="display: none;">
                            <x-lucide-info class="w-5 h-5" />
                        </button>
                        {{-- Info icon: opens dialog with drag hint (mobile fullscreen only) --}}
                        <button x-show="gym3dFullscreen === true"
                                x-cloak
                                type="button"
                                @click="$store.gym3d.helpOpen = true"
                                class="gym3d-control-btn md:hidden absolute top-4 left-4 z-50"
                                aria-label="{{ __('ui.gym3d_drag_hint') }}"
                                style="display: none;">
                            <x-lucide-info class="w-5 h-5" />
                        </button>
                        {{-- Expand/Minimize icon: toggles fullscreen (mobile only) --}}
                        <button type="button"
                                @click="toggleFullscreen()"
                                class="gym3d-control-btn md:hidden absolute top-4 right-4 z-[60]"
                                :aria-label="gym3dFullscreen ? '{{ __('ui.gym3d_exit_fullscreen') }}' : '{{ __('ui.gym3d_expand_fullscreen') }}'">
                            <x-lucide-maximize x-show="!gym3dFullscreen" class="w-5 h-5" />
                            <x-lucide-minimize-2 x-show="gym3dFullscreen" x-cloak class="w-5 h-5" />
                        </button>
                        <div id="floor-info" class="absolute" aria-live="polite">
                            <div class="title">{{ __('ui.gym3d_floor_title') }}</div>
                            <dl>
                                <dt>{{ __('ui.gym3d_floor_surface') }}:</dt><dd>{{ __('ui.gym3d_floor_value_surface') }}</dd>
                                <dt>{{ __('ui.gym3d_floor_dimensions') }}:</dt><dd>{{ __('ui.gym3d_floor_value_dimensions') }}</dd>
                                <dt>{{ __('ui.gym3d_floor_ceiling') }}:</dt><dd>{{ __('ui.gym3d_floor_value_ceiling') }}</dd>
                            </dl>
                        </div>
                        <div id="bench-info" class="absolute" aria-live="polite">
                            <div class="title">{{ __('ui.gym3d_bench_title') }}</div>
                            <dl>
                                <dt>{{ __('ui.gym3d_bench_dimensions') }}:</dt><dd>{{ __('ui.gym3d_bench_value_dimensions') }}</dd>
                                <dt>{{ __('ui.gym3d_bench_weight') }}:</dt><dd>{{ __('ui.gym3d_bench_value_weight') }}</dd>
                                <dt>{{ __('ui.gym3d_bench_type') }}:</dt><dd>{{ __('ui.gym3d_bench_value_type') }}</dd>
                                <dt>{{ __('ui.gym3d_bench_position') }}:</dt><dd>{{ __('ui.gym3d_bench_value_position') }}</dd>
                            </dl>
                            <a href="{{ __('ui.gym3d_bench_link_url') }}" target="_blank" rel="noopener noreferrer" class="gym3d-bench-link">{{ __('ui.gym3d_bench_link') }}</a>
                        </div>
                        <div id="vestiary-boys-info" class="absolute" aria-live="polite">
                            <div class="title">{{ __('ui.gym3d_vestiary_boys_title') }}</div>
                            <dl>
                                <dt>{{ __('ui.gym3d_vestiary_surface') }}:</dt><dd>{{ __('ui.gym3d_vestiary_value_surface') }}</dd>
                                <dt>{{ __('ui.gym3d_vestiary_dimensions') }}:</dt><dd>{{ __('ui.gym3d_vestiary_value_dimensions') }}</dd>
                                <dt>{{ __('ui.gym3d_vestiary_ceiling') }}:</dt><dd>{{ __('ui.gym3d_vestiary_value_ceiling') }}</dd>
                                <dt>{{ __('ui.gym3d_vestiary_lockers_count') }}:</dt><dd>{{ __('ui.gym3d_vestiary_value_lockers') }}</dd>
                                <dt>{{ __('ui.gym3d_vestiary_locker_dimensions') }}:</dt><dd>{{ __('ui.gym3d_vestiary_value_locker_dim') }}</dd>
                                <dt>{{ __('ui.gym3d_vestiary_benches') }}:</dt><dd>{{ __('ui.gym3d_vestiary_value_benches') }}</dd>
                                <dt>{{ __('ui.gym3d_vestiary_bench_dimensions') }}:</dt><dd>{{ __('ui.gym3d_vestiary_value_bench_dim') }}</dd>
                            </dl>
                        </div>
                        <div id="vestiary-girls-info" class="absolute" aria-live="polite">
                            <div class="title">{{ __('ui.gym3d_vestiary_girls_title') }}</div>
                            <dl>
                                <dt>{{ __('ui.gym3d_vestiary_surface') }}:</dt><dd>{{ __('ui.gym3d_vestiary_value_surface') }}</dd>
                                <dt>{{ __('ui.gym3d_vestiary_dimensions') }}:</dt><dd>{{ __('ui.gym3d_vestiary_value_dimensions') }}</dd>
                                <dt>{{ __('ui.gym3d_vestiary_ceiling') }}:</dt><dd>{{ __('ui.gym3d_vestiary_value_ceiling') }}</dd>
                                <dt>{{ __('ui.gym3d_vestiary_lockers_count') }}:</dt><dd>{{ __('ui.gym3d_vestiary_value_lockers') }}</dd>
                                <dt>{{ __('ui.gym3d_vestiary_locker_dimensions') }}:</dt><dd>{{ __('ui.gym3d_vestiary_value_locker_dim') }}</dd>
                                <dt>{{ __('ui.gym3d_vestiary_benches') }}:</dt><dd>{{ __('ui.gym3d_vestiary_value_benches') }}</dd>
                                <dt>{{ __('ui.gym3d_vestiary_bench_dimensions') }}:</dt><dd>{{ __('ui.gym3d_vestiary_value_bench_dim') }}</dd>
                            </dl>
                        </div>
                        {{-- Dialog: drag hint (opened by info icon) - mobile version (absolute position for fullscreen) --}}
                        <div x-show="$store.gym3d.helpOpen"
                             x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="md:hidden absolute inset-0 z-50 flex items-center justify-center p-4"
                             role="dialog"
                             aria-modal="true"
                             aria-labelledby="gym3d-help-title-mobile"
                             @click.self="$store.gym3d.helpOpen = false">
                            <div class="absolute inset-0 bg-black/70" aria-hidden="true"></div>
                            <div class="gym3d-dialog-container" @click.stop>
                            <div class="flex items-center justify-between gap-4 mb-4">
                                <h3 id="gym3d-help-title-mobile" class="text-lg font-semibold text-[#F97316] flex items-center gap-2">
                                    <x-lucide-info class="w-5 h-5 shrink-0" />
                                    {{ __('ui.about_3d_preview') }}
                                </h3>
                                <button type="button" @click="$store.gym3d.helpOpen = false" class="gym3d-close-btn" aria-label="{{ __('ui.close') }}">
                                    <x-lucide-x class="w-5 h-5" />
                                </button>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-secondary text-sm leading-relaxed">{{ __('ui.gym3d_drag_hint') }}</p>
                                </div>
                                <div class="border-t border-[#333333] pt-4">
                                    <h4 class="text-base font-semibold text-white mb-3">{{ __('ui.gym3d_help_controls_title') }}</h4>
                                    <div class="space-y-3 text-sm">
                                        <div class="gym3d-help-item">
                                            <x-lucide-zoom-in class="gym3d-help-icon" />
                                            <span class="text-secondary">{{ __('ui.gym3d_btn_desc_zoom_in') }}</span>
                                        </div>
                                        <div class="gym3d-help-item">
                                            <x-lucide-zoom-out class="gym3d-help-icon" />
                                            <span class="text-secondary">{{ __('ui.gym3d_btn_desc_zoom_out') }}</span>
                                        </div>
                                        <div class="gym3d-help-item">
                                            <x-lucide-rotate-ccw class="gym3d-help-icon" />
                                            <span class="text-secondary">{{ __('ui.gym3d_btn_desc_rotate_left') }}</span>
                                        </div>
                                        <div class="gym3d-help-item">
                                            <x-lucide-rotate-cw class="gym3d-help-icon" />
                                            <span class="text-secondary">{{ __('ui.gym3d_btn_desc_rotate_right') }}</span>
                                        </div>
                                        <div class="gym3d-help-item">
                                            <x-lucide-chevron-up class="gym3d-help-icon" />
                                            <span class="text-secondary">{{ __('ui.gym3d_btn_desc_tilt_up') }}</span>
                                        </div>
                                        <div class="gym3d-help-item">
                                            <x-lucide-chevron-down class="gym3d-help-icon" />
                                            <span class="text-secondary">{{ __('ui.gym3d_btn_desc_tilt_down') }}</span>
                                        </div>
                                        <div class="gym3d-help-item">
                                            <x-lucide-arrow-up class="gym3d-help-icon" />
                                            <span class="text-secondary">{{ __('ui.gym3d_btn_desc_move_forward') }}</span>
                                        </div>
                                        <div class="gym3d-help-item">
                                            <x-lucide-arrow-down class="gym3d-help-icon" />
                                            <span class="text-secondary">{{ __('ui.gym3d_btn_desc_move_back') }}</span>
                                        </div>
                                        <div class="gym3d-help-item">
                                            <x-lucide-arrow-left class="gym3d-help-icon" />
                                            <span class="text-secondary">{{ __('ui.gym3d_btn_desc_move_left') }}</span>
                                        </div>
                                        <div class="gym3d-help-item">
                                            <x-lucide-arrow-right class="gym3d-help-icon" />
                                            <span class="text-secondary">{{ __('ui.gym3d_btn_desc_move_right') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            {{-- Dialog: drag hint (opened by info icon) - desktop version (fixed position) - outside wrapper --}}
            <div x-data="{ get helpOpen() { return Alpine.store('gym3d')?.helpOpen || false; } }"
                 x-show="$store.gym3d.helpOpen"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="hidden md:flex fixed inset-0 z-50 items-center justify-center p-4"
                 role="dialog"
                 aria-modal="true"
                 aria-labelledby="gym3d-help-title-desktop"
                 @click.self="$store.gym3d.helpOpen = false">
                <div class="absolute inset-0 bg-black/70" aria-hidden="true"></div>
                <div class="gym3d-dialog-container" @click.stop>
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <h3 id="gym3d-help-title-desktop" class="text-lg font-semibold text-[#F97316] flex items-center gap-2">
                            <x-lucide-info class="w-5 h-5 shrink-0" />
                            {{ __('ui.about_3d_preview') }}
                        </h3>
                        <button type="button" @click="$store.gym3d.helpOpen = false" class="gym3d-close-btn" aria-label="{{ __('ui.close') }}">
                            <x-lucide-x class="w-5 h-5" />
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <p class="text-secondary text-sm leading-relaxed">{{ __('ui.gym3d_drag_hint') }}</p>
                        </div>
                        <div class="border-t border-[#333333] pt-4">
                            <h4 class="text-base font-semibold text-white mb-3">{{ __('ui.gym3d_help_controls_title') }}</h4>
                            <div class="space-y-3 text-sm">
                                <div class="gym3d-help-item">
                                    <x-lucide-zoom-in class="gym3d-help-icon" />
                                    <span class="text-secondary">{{ __('ui.gym3d_btn_desc_zoom_in') }}</span>
                                </div>
                                <div class="gym3d-help-item">
                                    <x-lucide-zoom-out class="gym3d-help-icon" />
                                    <span class="text-secondary">{{ __('ui.gym3d_btn_desc_zoom_out') }}</span>
                                </div>
                                <div class="gym3d-help-item">
                                    <x-lucide-rotate-ccw class="gym3d-help-icon" />
                                    <span class="text-secondary">{{ __('ui.gym3d_btn_desc_rotate_left') }}</span>
                                </div>
                                <div class="gym3d-help-item">
                                    <x-lucide-rotate-cw class="gym3d-help-icon" />
                                    <span class="text-secondary">{{ __('ui.gym3d_btn_desc_rotate_right') }}</span>
                                </div>
                                <div class="gym3d-help-item">
                                    <x-lucide-chevron-up class="gym3d-help-icon" />
                                    <span class="text-secondary">{{ __('ui.gym3d_btn_desc_tilt_up') }}</span>
                                </div>
                                <div class="gym3d-help-item">
                                    <x-lucide-chevron-down class="gym3d-help-icon" />
                                    <span class="text-secondary">{{ __('ui.gym3d_btn_desc_tilt_down') }}</span>
                                </div>
                                <div class="gym3d-help-item">
                                    <x-lucide-arrow-up class="gym3d-help-icon" />
                                    <span class="text-secondary">{{ __('ui.gym3d_btn_desc_move_forward') }}</span>
                                </div>
                                <div class="gym3d-help-item">
                                    <x-lucide-arrow-down class="gym3d-help-icon" />
                                    <span class="text-secondary">{{ __('ui.gym3d_btn_desc_move_back') }}</span>
                                </div>
                                <div class="gym3d-help-item">
                                    <x-lucide-arrow-left class="gym3d-help-icon" />
                                    <span class="text-secondary">{{ __('ui.gym3d_btn_desc_move_left') }}</span>
                                </div>
                                <div class="gym3d-help-item">
                                    <x-lucide-arrow-right class="gym3d-help-icon" />
                                    <span class="text-secondary">{{ __('ui.gym3d_btn_desc_move_right') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Controls bar: visible permanently on desktop (below canvas), only in fullscreen on mobile (overlay) --}}
            <div class="gym3d-controls-bar gym3d-controls-bar-desktop hidden md:flex">
                <button type="button" id="btn-zoom-in" title="{{ __('ui.gym3d_btn_zoom_in') }}"><x-lucide-zoom-in class="w-5 h-5" /></button>
                <button type="button" id="btn-zoom-out" title="{{ __('ui.gym3d_btn_zoom_out') }}"><x-lucide-zoom-out class="w-5 h-5" /></button>
                <button type="button" id="btn-rotate-left" title="{{ __('ui.gym3d_btn_rotate_left') }}"><x-lucide-rotate-ccw class="w-5 h-5" /></button>
                <button type="button" id="btn-rotate-right" title="{{ __('ui.gym3d_btn_rotate_right') }}"><x-lucide-rotate-cw class="w-5 h-5" /></button>
                <button type="button" id="btn-tilt-up" title="{{ __('ui.gym3d_btn_tilt_up') }}"><x-lucide-chevron-up class="w-5 h-5" /></button>
                <button type="button" id="btn-tilt-down" title="{{ __('ui.gym3d_btn_tilt_down') }}"><x-lucide-chevron-down class="w-5 h-5" /></button>
                <button type="button" id="btn-move-forward" title="{{ __('ui.gym3d_btn_move_forward') }}"><x-lucide-arrow-up class="w-5 h-5" /></button>
                <button type="button" id="btn-move-back" title="{{ __('ui.gym3d_btn_move_back') }}"><x-lucide-arrow-down class="w-5 h-5" /></button>
                <button type="button" id="btn-move-left" title="{{ __('ui.gym3d_btn_move_left') }}"><x-lucide-arrow-left class="w-5 h-5" /></button>
                <button type="button" id="btn-move-right" title="{{ __('ui.gym3d_btn_move_right') }}"><x-lucide-arrow-right class="w-5 h-5" /></button>
            </div>
        </div>

        {{-- Features Cards --}}
        <div class="container mx-auto px-4 mt-12">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Feature 1 --}}
                <div class="bg-[#111111] border border-[#333333] rounded-2xl p-6 md:p-8 hover:border-[#F97316] transition-all group">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F97316]/10 rounded-2xl flex items-center justify-center group-hover:bg-[#F97316] transition-colors flex-shrink-0">
                            <x-lucide-dumbbell class="w-5 h-5 md:w-6 md:h-6 text-[#F97316] group-hover:text-white transition-colors" />
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-white">
                            {{ __('ui.feature1') }}
                        </h3>
                    </div>
                    <p class="text-sm md:text-base text-secondary">
                        {{ __('ui.feature1Desc') }}
                    </p>
                </div>

                {{-- Feature 2 --}}
                <div class="bg-[#111111] border border-[#333333] rounded-2xl p-6 md:p-8 hover:border-[#F97316] transition-all group">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F97316]/10 rounded-2xl flex items-center justify-center group-hover:bg-[#F97316] transition-colors flex-shrink-0">
                            <x-lucide-clipboard-list class="w-5 h-5 md:w-6 md:h-6 text-[#F97316] group-hover:text-white transition-colors" />
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-white">
                            {{ __('ui.feature2') }}
                        </h3>
                    </div>
                    <p class="text-sm md:text-base text-secondary">
                        {{ __('ui.feature2Desc') }}
                    </p>
                </div>

                {{-- Feature 3 - Working Hours --}}
                <div class="bg-[#111111] border border-[#333333] rounded-2xl p-6 md:p-8 hover:border-[#F97316] transition-all group">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F97316]/10 rounded-2xl flex items-center justify-center group-hover:bg-[#F97316] transition-colors flex-shrink-0">
                            <x-lucide-clock class="w-5 h-5 md:w-6 md:h-6 text-[#F97316] group-hover:text-white transition-colors" />
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-white">
                            {{ __('ui.feature3') }}
                        </h3>
                    </div>
                    <p class="text-sm md:text-base text-secondary">
                        {{ __('ui.feature3Desc') }}
                    </p>
                </div>
            </div>
        </div>
    </section>
    {{-- End About Section --}}

    {{-- Start Timeline Section --}}
    <section id="timeline" class="scroll-mt-[72px] py-16 container mx-auto px-4"><h2 class="text-2xl font-bold text-white">{{ __('ui.section_timeline_title') }}</h2><p class="text-secondary mt-2">{{ __('ui.section_placeholder') }}</p></section>
    {{-- End Timeline Section --}}

    {{-- Start Contact Section --}}
    <section id="contact" class="scroll-mt-[72px] py-16 container mx-auto px-4"><h2 class="text-2xl font-bold text-white">{{ __('ui.section_contact_title') }}</h2><p class="text-secondary mt-2">{{ __('ui.section_placeholder') }}</p></section>
    {{-- End Contact Section --}}
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
