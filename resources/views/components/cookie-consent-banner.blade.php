@php
    $measurementId = config('services.google_analytics.measurement_id');
    $locale = app()->getLocale();
@endphp
<div
    x-data="cookieConsentBanner({{ Js::from($measurementId) }})"
    x-show="pendingConsent || $store.cookieConsent.showBanner"
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="translate-y-full opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100"
    class="fixed bottom-0 left-0 right-0 z-[80] p-4 md:p-6"
    role="dialog"
    aria-label="{{ __('ui.cookie_consent_privacy') }}"
>
    <div class="container mx-auto max-w-4xl rounded-2xl border border-[#333333] bg-[#0c0c0c]/95 backdrop-blur-md p-4 shadow-xl md:p-6">
        <p class="mb-4 text-sm text-gray-300 md:text-base">
            {{ __('ui.cookie_consent_message') }}
            <a href="{{ route('privacy', ['locale' => $locale]) }}" class="text-orange-500 underline hover:text-orange-400">
                {{ __('ui.cookie_consent_privacy') }}
            </a>.
        </p>
        <div class="flex flex-wrap items-center gap-3">
            <button
                type="button"
                @click="accept()"
                class="inline-flex items-center justify-center rounded-lg bg-orange-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-orange-600"
            >
                {{ __('ui.cookie_consent_accept') }}
            </button>
            <button
                type="button"
                @click="decline()"
                class="inline-flex items-center justify-center rounded-lg border border-[#333333] bg-transparent px-4 py-2 text-sm font-medium text-gray-300 transition hover:bg-white/5"
            >
                {{ __('ui.cookie_consent_decline') }}
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('cookieConsentBanner', (measurementId) => ({
        storageKey: 'ga_consent',
        pendingConsent: false,

        init() {
            const consent = localStorage.getItem(this.storageKey);
            this.pendingConsent = consent === null;
            if (consent === 'accepted' && measurementId) {
                this.loadGoogleAnalytics(measurementId);
            }
        },

        accept() {
            localStorage.setItem(this.storageKey, 'accepted');
            if (measurementId) {
                this.loadGoogleAnalytics(measurementId);
            }
            this.pendingConsent = false;
            Alpine.store('cookieConsent').showBanner = false;
        },

        decline() {
            localStorage.setItem(this.storageKey, 'declined');
            this.pendingConsent = false;
            Alpine.store('cookieConsent').showBanner = false;
        },

        loadGoogleAnalytics(id) {
            if (window.gtag) {
                window.gtag('consent', 'update', { analytics_storage: 'granted' });
                return;
            }
            window.dataLayer = window.dataLayer || [];
            function gtag(){ window.dataLayer.push(arguments); }
            window.gtag = gtag;
            gtag('js', new Date());
            gtag('consent', 'default', { analytics_storage: 'denied' });
            gtag('consent', 'update', { analytics_storage: 'granted' });
            gtag('config', id);
            const script = document.createElement('script');
            script.async = true;
            script.src = 'https://www.googletagmanager.com/gtag/js?id=' + id;
            document.head.appendChild(script);
        },
    }));
});
</script>
