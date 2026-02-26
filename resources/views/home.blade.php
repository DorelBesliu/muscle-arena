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
<main x-data="proposalFormData()" data-success-message="{{ __('ui.proposalFormSuccess') }}">
    {{-- Start Hero Section --}}
    <section id="hero" class="bg-[#111111] relative overflow-hidden scroll-mt-[72px] pt-[32px]">
        {{-- Background decoration --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/2 left-0 w-96 h-96 bg-[#F97316] rounded-full blur-3xl opacity-5"></div>
            <div class="absolute top-1/2 right-0 w-96 h-96 bg-[#F97316] rounded-full blur-3xl opacity-5"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="grid items-center min-h-[370px] md:min-h-[600px] mt-10">
                {{-- Left: Text content (wider on md) --}}
                <div class="py-6 md:py-20 md:pr-12 lg:pr-16 w-full md:w-4/5 lg:max-w-[80rem]">
                    <div class="mb-6">
                        <h2 class="text-2xl md:text-5xl lg:text-6xl font-black mb-2 leading-tight">
                            <span class="text-white block">{{ __('ui.hero_title_line1') }}</span>
                            <span class="text-[#F97316] block mt-2">{{ __('ui.hero_title_line2') }}</span>
                        </h2>
                        <div class="h-1 w-24 bg-[#F97316] mb-2"></div>
                    </div>

                    <p class="text-sm md:text-xl text-secondary leading-snug max-w-4xl mb-2 mb-6">
                        {{ __('ui.hero_description') }}
                    </p>

                    {{-- Proposal button (hero CTA) --}}
                    <button
                        type="button"
                        @click="proposalDialogOpen = true"
                        class="bg-[#F97316] hover:bg-[#EF4444] text-white font-bold px-6 md:px-8 py-3 md:py-4 rounded-xl transition-all hover:scale-105 shadow-xl inline-flex items-center text-base md:text-lg gap-2 md:gap-3"
                    >
                        {{ __('ui.contactProposal') }}
                        <x-lucide-target class="w-4 h-4 md:w-5 md:h-5 shrink-0" />
                    </button>
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

    {{-- Start About Section (content from site_content) --}}
    @php
        $locale = app()->getLocale();
        $about = \App\Models\SiteContent::get('about_' . $locale);
        $aboutTitle = is_array($about) ? (string) ($about['title'] ?? '') : '';
        $aboutDescription = is_array($about) ? (string) ($about['description'] ?? '') : '';
        $aboutDescriptionIsPlain = ! str_contains($aboutDescription, '<');
        $iconsForFeatures = config('icons', ['default' => 'list-checks', 'list-checks' => 'List checks']);
        $aboutFeatureIconsAllowed = array_keys(array_filter($iconsForFeatures, fn ($k) => $k !== 'default', ARRAY_FILTER_USE_KEY));
        $defaultFeatureIcon = $iconsForFeatures['default'] ?? 'list-checks';
        if (! in_array($defaultFeatureIcon, $aboutFeatureIconsAllowed, true)) {
            $defaultFeatureIcon = 'list-checks';
        }
        $aboutFeatures = [];
        foreach (\App\Models\PageAboutProjectFeature::orderBy('sort_order')->get() as $index => $feature) {
            $trans = $feature->translation($locale);
            $icon = in_array($feature->icon, $aboutFeatureIconsAllowed, true) ? $feature->icon : $defaultFeatureIcon;
            $aboutFeatures[] = [
                'title' => $trans ? $trans->title : '',
                'description' => $trans ? $trans->description : '',
                'sort_order' => $index,
                'icon' => $icon,
            ];
        }
    @endphp
    <section id="about" class="bg-[#000000] py-8 md:py-16 relative scroll-mt-[72px]" data-gym3d-logo="{{ asset('images/logo-white.svg') }}">
        <div class="container mx-auto px-4">
            <div class="mx-auto">
                <div class="grid items-start">
                    <div>
                        <h2 class="text-2xl md:text-[36px] font-black text-white mb-6 leading-tight">
                            {{ __('ui.section_about_title') }}
                        </h2>
                        <div class="about-description text-sm md:text-lg text-secondary leading-relaxed w-full mb-12 prose prose-invert prose-p:text-secondary max-w-none">
                            @if($aboutDescriptionIsPlain)
                                {!! nl2br(e($aboutDescription)) !!}
                            @else
                                {!! $aboutDescription !!}
                            @endif
                        </div>
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
                        <button type="button" @click="$store.gym3d.helpOpen = true" class="gym3d-control-btn hidden md:flex absolute top-4 left-4 z-20" aria-label="{{ __('ui.gym3d_drag_hint') }}">
                            <x-lucide-info class="w-5 h-5" />
                        </button>
                        {{-- Info icon: opens dialog with drag hint (mobile fullscreen only) --}}
                        <button x-show="gym3dFullscreen === true"
                                x-cloak
                                type="button"
                                @click.stop="$store.gym3d.helpOpen = !$store.gym3d.helpOpen"
                                class="gym3d-control-btn md:hidden absolute top-4 left-4 z-[60]"
                                aria-label="{{ __('ui.gym3d_drag_hint') }}">
                            <x-lucide-info class="w-5 h-5" />
                        </button>
                        {{-- Expand/Minimize icon: toggles fullscreen (all devices) --}}
                        <button type="button"
                                @click="toggleFullscreen()"
                                class="gym3d-control-btn absolute top-4 right-4 z-[60]"
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
                        <div id="reception-info" class="absolute" aria-live="polite">
                            <div class="title">{{ __('ui.gym3d_reception_title') }}</div>
                            <dl>
                                <dt>{{ __('ui.gym3d_reception_surface') }}:</dt><dd>{{ __('ui.gym3d_reception_value_surface') }}</dd>
                                <dt>{{ __('ui.gym3d_reception_dimensions') }}:</dt><dd>{{ __('ui.gym3d_reception_value_dimensions') }}</dd>
                                <dt>{{ __('ui.gym3d_reception_ceiling') }}:</dt><dd>{{ __('ui.gym3d_reception_value_ceiling') }}</dd>
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
                            <div class="absolute inset-0" aria-hidden="true"></div>
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
                <div class="absolute inset-0" aria-hidden="true"></div>
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

        {{-- Features (from admin) --}}
        @if(count($aboutFeatures) > 0)
        <div class="container mx-auto px-4 mt-12">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($aboutFeatures as $feature)
                <div class="bg-[#111111] border border-[#333333] rounded-2xl p-6 md:p-8 hover:border-[#F97316] transition-all group">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F97316]/10 rounded-2xl flex items-center justify-center group-hover:bg-[#F97316] transition-colors flex-shrink-0">
                            <x-dynamic-component :component="'lucide-' . ($feature['icon'] ?? $defaultFeatureIcon)" class="w-5 h-5 md:w-6 md:h-6 text-[#F97316] group-hover:text-white transition-colors" />
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-white">
                            {{ $feature['title'] ?: '—' }}
                        </h3>
                    </div>
                    <p class="text-sm md:text-base text-secondary">
                        {{ $feature['description'] ?: '—' }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </section>
    {{-- End About Section --}}

    {{-- Start Timeline Section --}}
    <section
        id="timeline"
        class="bg-[#111111] py-8 md:py-16 relative scroll-mt-[72px]"
    >
        {{-- Background pattern --}}
        <div
            class="absolute inset-0 opacity-5"
            style="background-image: radial-gradient(circle at 2px 2px, #F97316 1px, transparent 0); background-size: 40px 40px;"
        ></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-10 md:mb-16">
                    <h2 class="text-xl md:text-[36px] font-black text-white mb-4">
                        {{ __('ui.projectTimelineTitle') }}
                    </h2>
                    <p class="text-sm md:text-xl text-[#CCCCCC]">
                        {{ __('ui.projectTimelineDescription') }}
                    </p>
                </div>
                {{-- Timeline steps (from the admin panel – Road to opening) --}}
                @php
                    $pathToOpeningSteps = \App\Models\PagePathToOpeningStep::with([
                        'translations' => fn ($q) => $q->where('locale', $locale),
                    ])->orderBy('sort_order')->get();

                    $timelineSteps = [];
                    foreach ($pathToOpeningSteps as $step) {
                        $trans = $step->translations->first();
                        $status = $step->status;
                        if ($status === 'in-progress') {
                            $status = 'current';
                        } elseif (! in_array($status, ['completed', 'current', 'upcoming'], true)) {
                            $status = 'upcoming';
                        }
                        $timelineSteps[] = [
                            'title' => $trans?->title ?? '',
                            'description' => $trans?->description ?? '',
                            'date' => '',
                            'status' => $status,
                        ];
                    }
                @endphp
                <div class="max-w-4xl mx-auto">
                    <div class="space-y-6 md:space-y-8">
                        @foreach ($timelineSteps as $index => $step)
                            @php
                                $status = $step['status'] ?? 'upcoming';
                                $title = $step['title'] ?? '';
                                $description = $step['description'] ?? '';
                                $date = $step['date'] ?? '';

                                $iconClass = match($status) {
                                    'completed' => 'bg-green-500/10 text-green-500',
                                    'current' => 'bg-[#F97316]/10 text-[#F97316]',
                                    'next' => 'bg-[#333333] text-[#CCCCCC]',
                                    default => 'bg-[#333333] text-[#CCCCCC]',
                                };

                                $badgeClass = match($status) {
                                    'completed' => 'bg-green-500/10 text-green-500',
                                    'current' => 'bg-[#F97316]/10 text-[#F97316]',
                                    'next' => 'bg-[#333333] text-[#CCCCCC]',
                                    default => 'bg-[#333333] text-[#CCCCCC]',
                                };

                                $badgeText = match($status) {
                                    'completed' => __('ui.timeline_status_completed'),
                                    'current' => __('ui.timeline_status_current'),
                                    'next' => __('ui.timeline_status_next'),
                                    default => __('ui.timeline_status_upcoming'),
                                };
                            @endphp
                            <div class="relative">
                                {{-- Timeline line --}}
                                @if($index !== count($timelineSteps) - 1)
                                    <div class="absolute left-5 md:left-6 top-12 md:top-14 w-0.5 h-full bg-[#333333]"></div>
                                @endif

                                <div class="flex gap-4 md:gap-6">
                                    {{-- Icon --}}
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 md:w-12 md:h-12 rounded-full flex items-center justify-center {{ $iconClass }}">
                                            @if($status === 'completed')
                                                <x-lucide-check-circle-2 class="w-5 h-5 md:w-6 md:h-6" />
                                            @elseif($status === 'current')
                                                <x-lucide-clock class="w-5 h-5 md:w-6 md:h-6" />
                                            @else
                                                <x-lucide-circle class="w-5 h-5 md:w-6 md:h-6" />
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-1 bg-[#111111] border border-[#333333] rounded-xl p-4 md:p-6 shadow-sm hover:border-[#F97316] transition-colors">
                                        <div class="flex items-start justify-between gap-2 md:gap-4 mb-2">
                                            <h3 class="text-base md:text-xl font-bold text-white">{{ $title }}</h3>
                                            {{-- Status only on desktop; on mobile it is already visible on the left (icon) --}}
                                            <span class="hidden md:inline-flex px-3 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">
                                                {{ $badgeText }}
                                            </span>
                                        </div>
                                        @if($date)
                                        <p class="text-xs md:text-sm text-[#CCCCCC] mb-2 md:mb-3">{{ $date }}</p>
                                        @endif
                                        <div class="text-sm md:text-base text-[#CCCCCC] leading-relaxed">
                                            {!! nl2br(e($description)) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- End Timeline Section --}}

    {{-- Start Contact Section (data from Site Content → Contacts) --}}
    @php
        $contactData = \App\Models\SiteContent::get('contact_' . $locale);
        $contactCards = is_array($contactData) && isset($contactData['cards']) && is_array($contactData['cards'])
            ? $contactData['cards']
            : [];
        $contactPhone = is_array($contactData) ? (string) ($contactData['phone'] ?? '') : '';
        $contactEmail = is_array($contactData) ? (string) ($contactData['email'] ?? '') : '';
        $useCards = count($contactCards) > 0;
    @endphp
    <section id="contact" class="bg-[#000000] py-8 md:py-16 relative scroll-mt-[72px]">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <div class="text-center mb-8 md:mb-16">
                    <h2 class="text-2xl md:text-[36px] font-black text-white mb-4">
                        {{ __('ui.contactTitle') }}
                    </h2>
                    <p class="text-base md:text-xl text-[#CCCCCC]">
                        {{ __('ui.contactDescription') }}
                    </p>
                </div>

                @if($useCards)
                    <div class="grid md:grid-cols-2 gap-8">
                        @foreach($contactCards as $card)
                            @php
                                $hasType = is_array($card) && isset($card['type']) && in_array($card['type'], ['phone', 'email'], true);
                                $type = $hasType ? $card['type'] : null;
                                $iconKey = isset($card['icon']) ? $card['icon'] : ($type === 'phone' ? 'phone' : 'mail');
                                $title = $hasType ? ($type === 'phone' ? __('ui.contactPhone') : __('ui.contactEmail')) : (string) ($card['title'] ?? $card['label'] ?? '—');
                                $description = is_array($card) ? (string) ($card['description'] ?? $card['value'] ?? '') : '';
                                $isEmail = $type === 'email' || (!$hasType && $description !== '' && filter_var($description, FILTER_VALIDATE_EMAIL));
                                $isPhone = $type === 'phone' || (!$hasType && $description !== '' && preg_match('/^[\d\s\+\-\(\)]+$/', trim($description)));
                            @endphp
                            <div class="bg-[#000000] border border-[#333333] rounded-2xl p-6 md:p-8 hover:border-[#F97316] transition-all group">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F97316]/10 rounded-full flex items-center justify-center group-hover:bg-[#F97316] transition-colors flex-shrink-0">
                                        @if($iconKey && preg_match('/^[a-z0-9\-]+$/i', $iconKey))
                                            <x-dynamic-component :component="'lucide-' . $iconKey" class="w-5 h-5 md:w-6 md:h-6 text-[#F97316] group-hover:text-white transition-colors" />
                                        @elseif($isEmail)
                                            <x-lucide-mail class="w-5 h-5 md:w-6 md:h-6 text-[#F97316] group-hover:text-white transition-colors" />
                                        @elseif($isPhone)
                                            <x-lucide-phone class="w-5 h-5 md:w-6 md:h-6 text-[#F97316] group-hover:text-white transition-colors" />
                                        @else
                                            <x-lucide-map-pin class="w-5 h-5 md:w-6 md:h-6 text-[#F97316] group-hover:text-white transition-colors" />
                                        @endif
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <h3 class="text-lg md:text-xl font-bold text-white mb-1">{{ $title }}</h3>
                                        @if($type === 'email' && $description !== '')
                                            <a href="mailto:{{ $description }}" class="text-sm md:text-base text-[#CCCCCC] hover:text-[#F97316] transition-colors break-all">{{ $description }}</a>
                                        @elseif($type === 'phone' && $description !== '')
                                            <a href="tel:{{ preg_replace('/\s+/', '', $description) }}" class="text-sm md:text-base text-[#CCCCCC] hover:text-[#F97316] transition-colors">{{ $description }}</a>
                                        @elseif($isEmail && $description !== '')
                                            <a href="mailto:{{ $description }}" class="text-sm md:text-base text-[#CCCCCC] hover:text-[#F97316] transition-colors break-all">{{ $description }}</a>
                                        @elseif($isPhone && $description !== '')
                                            <a href="tel:{{ preg_replace('/\s+/', '', $description) }}" class="text-sm md:text-base text-[#CCCCCC] hover:text-[#F97316] transition-colors">{{ $description }}</a>
                                        @else
                                            <p class="text-sm md:text-base text-[#CCCCCC]">{{ $description ?: '—' }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="grid md:grid-cols-2 gap-8">
                        <div class="bg-[#000000] border border-[#333333] rounded-2xl p-6 md:p-8 hover:border-[#F97316] transition-all group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F97316]/10 rounded-full flex items-center justify-center group-hover:bg-[#F97316] transition-colors flex-shrink-0">
                                    <x-lucide-phone class="w-5 h-5 md:w-6 md:h-6 text-[#F97316] group-hover:text-white transition-colors" />
                                </div>
                                <div class="flex flex-col">
                                    <h3 class="text-lg md:text-xl font-bold text-white mb-1">{{ __('ui.contactPhone') }}</h3>
                                    <a href="tel:{{ $contactPhone }}" class="text-sm md:text-base text-[#CCCCCC] hover:text-[#F97316] transition-colors">
                                        {{ $contactPhone }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="bg-[#000000] border border-[#333333] rounded-2xl p-6 md:p-8 hover:border-[#F97316] transition-all group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F97316]/10 rounded-full flex items-center justify-center group-hover:bg-[#F97316] transition-colors flex-shrink-0">
                                    <x-lucide-mail class="w-5 h-5 md:w-6 md:h-6 text-[#F97316] group-hover:text-white transition-colors" />
                                </div>
                                <div class="flex flex-col">
                                    <h3 class="text-lg md:text-xl font-bold text-white mb-1">{{ __('ui.contactEmail') }}</h3>
                                    <a href="mailto:{{ $contactEmail }}" class="text-sm md:text-base text-[#CCCCCC] hover:text-[#F97316] transition-colors">
                                        {{ $contactEmail }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Proposal Dialog --}}
        <div
            x-show="proposalDialogOpen"
            x-cloak
            class="fixed inset-0 backdrop-blur-md flex items-center justify-center z-50 p-0 md:p-4"
            role="dialog"
            aria-modal="true"
            style="display: none;"
        >
            <div class="bg-[#111111] border-2 border-[#333333] rounded-none md:rounded-3xl max-w-2xl w-full h-full md:h-auto md:max-h-[90vh] overflow-y-auto shadow-2xl" @click.stop>
                {{-- Header --}}
                <div class="sticky top-0 bg-[#111111] border-b border-[#333333] p-6 flex items-center justify-between">
                    <h2 class="text-xl md:text-3xl font-black text-white">
                        {{ __('ui.proposalDialogTitle') }}
                    </h2>
                    <button
                        type="button"
                        @click="proposalDialogOpen = false; proposalFormData = { title: '', message: '' }"
                        class="text-[#CCCCCC] hover:text-white transition-colors p-2 hover:bg-[#333333] rounded-full"
                        aria-label="{{ __('ui.close') }}"
                    >
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Form --}}
                <div class="p-6">
                    <form
                        @submit.prevent="submitProposal()"
                        class="space-y-6"
                    >
                        {{-- Title Field --}}
                        <div>
                            <label
                                for="proposalTitle"
                                class="block text-sm font-medium text-white mb-2"
                            >
                                {{ __('ui.proposalFormTitle') }}
                            </label>
                            <input
                                type="text"
                                id="proposalTitle"
                                x-model="proposalFormData.title"
                                class="w-full bg-[#000000] border-2 border-[#333333] rounded-xl py-3 px-4 text-white placeholder-[#666666] focus:border-[#F97316] focus:outline-none transition-colors"
                                :placeholder="'{{ __('ui.proposalFormTitlePlaceholder') }}'"
                                required
                            />
                        </div>

                        {{-- Message Field --}}
                        <div>
                            <label
                                for="proposalMessage"
                                class="block text-sm font-medium text-white mb-2"
                            >
                                {{ __('ui.proposalFormMessage') }}
                            </label>
                            <textarea
                                id="proposalMessage"
                                x-model="proposalFormData.message"
                                rows="8"
                                class="w-full bg-[#000000] border-2 border-[#333333] rounded-xl py-3 px-4 text-white placeholder-[#666666] focus:border-[#F97316] focus:outline-none transition-colors resize-none"
                                :placeholder="'{{ __('ui.proposalFormMessagePlaceholder') }}'"
                                required
                            ></textarea>
                        </div>

                        {{-- Submit Button --}}
                        <button
                            type="submit"
                            class="w-full bg-[#F97316] hover:bg-[#EF4444] text-white font-bold py-4 rounded-xl transition-all hover:scale-105 shadow-xl"
                        >
                            {{ __('ui.proposalFormSubmit') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    {{-- End Contact Section --}}

    {{-- Footer --}}
    <footer class="bg-[#111111] border-t border-[#333333] py-8">
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
