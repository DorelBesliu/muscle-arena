import './bootstrap';
import './toast.js';

import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Placeholder from '@tiptap/extension-placeholder';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('tiptapEditor', (config) => {
        let editor;
        return {
            updatedAt: Date.now(),
            _tiptapInitialSyncDone: false,
            init() {
                const el = document.getElementById(config.initialContentId);
                const initialContent = (el && (el.value !== undefined ? el.value : el.textContent)) ? (el.value !== undefined ? el.value : el.textContent).trim() : '';
                const _this = this;
                editor = new Editor({
                    element: this.$refs.element,
                    extensions: [
                        StarterKit.configure({ link: { openOnClick: false } }),
                        Placeholder.configure({ placeholder: config.placeholder || '…' }),
                    ],
                    content: initialContent || '',
                    editorProps: {
                        attributes: {
                            class: 'tiptap-content min-h-[140px] outline-none',
                        },
                    },
                    onCreate() {
                        _this.updatedAt = Date.now();
                    },
                    onUpdate() {
                        _this.updatedAt = Date.now();
                        if (!window.Livewire || !_this.$wire) return;
                        if (!_this._tiptapInitialSyncDone) {
                            _this._tiptapInitialSyncDone = true;
                            return;
                        }
                        _this.$wire.set(config.wireProperty, editor.getHTML());
                    },
                    onSelectionUpdate() {
                        _this.updatedAt = Date.now();
                    },
                });
            },
            isActive(type, opts = {}) {
                return editor ? editor.isActive(type, opts) : false;
            },
            toggleBold() {
                editor?.chain().focus().toggleBold().run();
            },
            toggleItalic() {
                editor?.chain().focus().toggleItalic().run();
            },
            toggleStrike() {
                editor?.chain().focus().toggleStrike().run();
            },
            toggleBulletList() {
                editor?.chain().focus().toggleBulletList().run();
            },
            toggleOrderedList() {
                editor?.chain().focus().toggleOrderedList().run();
            },
            toggleBlockquote() {
                editor?.chain().focus().toggleBlockquote().run();
            },
            setParagraph() {
                editor?.chain().focus().setParagraph().run();
            },
            toggleHeading(level = 1) {
                editor?.chain().focus().toggleHeading({ level }).run();
            },
            setCode() {
                editor?.chain().focus().toggleCode().run();
            },
            setLink() {
                const url = window.prompt('URL');
                if (url) editor?.chain().focus().setLink({ href: url }).run();
            },
        };
    });
});

/**
 * Initialize Alpine store and Livewire listeners for about-project feature editing.
 * Call from x-init: initAboutFeatureEditing(iconsConfig, $wire, contentLocale)
 */
window.initAboutFeatureEditing = function (iconsConfig, wire, contentLocale) {
    if (!window.__aboutFeatureEditingStore) {
        window.__aboutFeatureEditingStore = true;
        window.Alpine.store('aboutFeatureEditing', {
            id: null,
            index: null,
            editingIcon: null,
            openIconDropdown: false,
            dropdownRect: null,
            iconsConfig: iconsConfig || {},
            contentLocale: (typeof contentLocale === 'string' && contentLocale) ? contentLocale : 'ro',
        });
    }
    var s = window.Alpine.store('aboutFeatureEditing');
    if (s && typeof contentLocale === 'string' && contentLocale) s.contentLocale = contentLocale;
    if (wire) {
        wire.$on('feature-added', (payload) => {
            const s = window.Alpine.store('aboutFeatureEditing');
            s.id = payload?.featureId ?? payload ?? null;
            s.index = payload?.featureIndex ?? null;
            s.editingIcon = payload?.editingIcon ?? 'list-checks';
        });
        wire.$on('feature-saved', (payload) => {
            const featureId = payload?.featureId;
            const icon = payload?.icon;
            if (featureId != null && icon && typeof window.aboutFeatureIconDropdownSyncCollapsedIcon === 'function') {
                window.aboutFeatureIconDropdownSyncCollapsedIcon(featureId, icon);
            }
            const s = window.Alpine.store('aboutFeatureEditing');
            s.id = null;
            s.index = null;
            s.editingIcon = null;
            s.openIconDropdown = false;
            s.dropdownRect = null;
        });
    }
};

function aboutFeatureIconDropdownApplySearch() {
    const list = document.getElementById('about-feature-icon-dropdown-list');
    const searchEl = document.getElementById('about-feature-icon-search');
    if (!list || !searchEl) return;
    const q = (searchEl.value || '').toLowerCase();
    list.querySelectorAll('button.about-feature-icon-option').forEach(function (btn) {
        const label = (btn.dataset.label || '').toLowerCase();
        btn.style.display = !q || label.includes(q) ? '' : 'none';
    });
}

function aboutFeatureIconDropdownBindSearch() {
    var searchInput = document.getElementById('about-feature-icon-search');
    if (searchInput && !searchInput._aboutFeatureSearchBound) {
        searchInput._aboutFeatureSearchBound = true;
        searchInput.addEventListener('input', aboutFeatureIconDropdownApplySearch);
    }
}

window._aboutFeatureIconCache = window._aboutFeatureIconCache || {};

function aboutFeatureIconGetHtml(iconKey) {
    if (!iconKey) return null;
    if (window._aboutFeatureIconCache[iconKey]) return window._aboutFeatureIconCache[iconKey];
    var iconEl = document.getElementById('about-feature-icon-' + iconKey);
    if (iconEl) {
        window._aboutFeatureIconCache[iconKey] = iconEl.innerHTML;
        return iconEl.innerHTML;
    }
    return null;
}

window.aboutFeatureIconDropdownSyncDisplay = function (featureId, iconKey) {
    if (!featureId || !iconKey) return;
    var display = document.getElementById('about-feature-icon-display-' + featureId);
    if (!display) return;
    var html = aboutFeatureIconGetHtml(iconKey);
    if (html) display.innerHTML = html;
};

window.aboutFeatureIconDropdownSyncCollapsedIcon = function (featureId, iconKey) {
    if (!featureId || !iconKey) return;
    var container = document.getElementById('about-feature-collapsed-icon-' + featureId);
    if (!container) return;
    var html = aboutFeatureIconGetHtml(iconKey);
    if (html) container.innerHTML = html;
};

/**
 * Alpine component for the about-project feature icon button (syncs display with store and list).
 * Usage: x-data="aboutFeatureIconButton(featId, initialKey)"
 */
window.aboutFeatureIconButton = function (featId, initialKey) {
    return {
        featId: featId,
        initialKey: initialKey || 'list-checks',
        init() {
            const store = Alpine.store('aboutFeatureEditing');
            if (!store) return;
            this.$watch(
                () => store.editingIcon,
                () => {
                    if (store.id !== this.featId) return;
                    var self = this;
                    this.$nextTick(() => {
                        if (typeof aboutFeatureIconDropdownSyncDisplay === 'function') aboutFeatureIconDropdownSyncDisplay(self.featId, store.editingIcon);
                    });
                }
            );
            this.$watch(
                () => store.id,
                () => {
                    if (store.id !== this.featId) return;
                    var self = this;
                    this.$nextTick(() => {
                        if (typeof aboutFeatureIconDropdownSyncDisplay === 'function') aboutFeatureIconDropdownSyncDisplay(self.featId, store.editingIcon);
                    });
                }
            );
        },
    };
};

var _aboutFeatureIconDropdownLoadPromise = null;

function aboutFeatureIconPopulateCacheFromList(listEl) {
    if (!listEl) return;
    listEl.querySelectorAll('button.about-feature-icon-option').forEach(function (btn) {
        var key = btn.dataset.iconKey;
        var span = document.getElementById('about-feature-icon-' + key);
        if (key && span) {
            window._aboutFeatureIconCache = window._aboutFeatureIconCache || {};
            window._aboutFeatureIconCache[key] = span.innerHTML;
        }
    });
}

/**
 * Load icon list for about-project dropdown (fetch fragment and inject).
 * Fetches once per locale; if already loaded for current locale, reuses stored HTML.
 */
window.loadAboutFeatureIconDropdownContent = function () {
    const listEl = document.getElementById('about-feature-icon-dropdown-list');
    if (!listEl) return Promise.reject(new Error('list container not found'));

    const store = window.Alpine?.store?.('aboutFeatureEditing');
    const locale = (store && store.contentLocale) ? store.contentLocale : 'ro';
    window._aboutFeatureIconListHtmlByLocale = window._aboutFeatureIconListHtmlByLocale || {};
    const cachedHtml = window._aboutFeatureIconListHtmlByLocale[locale];

    if (cachedHtml) {
        var hasContent = listEl.querySelectorAll('button.about-feature-icon-option').length > 0;
        if (!hasContent) {
            listEl.setAttribute('data-loaded', '1');
            listEl.setAttribute('data-loaded-locale', locale);
            listEl.innerHTML = cachedHtml;
            aboutFeatureIconPopulateCacheFromList(listEl);
            aboutFeatureIconDropdownBindSearch();
            aboutFeatureIconDropdownApplySearch();
        } else {
            listEl.setAttribute('data-loaded', '1');
            listEl.setAttribute('data-loaded-locale', locale);
        }
        return Promise.resolve();
    }

    var state = listEl.getAttribute('data-loaded');
    var loadedLocale = listEl.getAttribute('data-loaded-locale');
    if (state === '1' && loadedLocale === locale && listEl.querySelectorAll('button.about-feature-icon-option').length > 0) return Promise.resolve();
    if (state === 'loading' && _aboutFeatureIconDropdownLoadPromise) return _aboutFeatureIconDropdownLoadPromise;

    listEl.setAttribute('data-loaded', 'loading');
    listEl.innerHTML = '<span class="text-[#666666] text-sm">Loading...</span>';
    const fragmentUrl = '/content/about/icon-dropdown-fragment?locale=' + encodeURIComponent(locale);
    _aboutFeatureIconDropdownLoadPromise = fetch(fragmentUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'text/html' } })
        .then(function (r) { return r.text(); })
        .then(function (html) {
            window._aboutFeatureIconListHtmlByLocale[locale] = html;
            listEl.setAttribute('data-loaded', '1');
            listEl.setAttribute('data-loaded-locale', locale);
            listEl.innerHTML = html;
            aboutFeatureIconPopulateCacheFromList(listEl);
            aboutFeatureIconDropdownBindSearch();
            aboutFeatureIconDropdownApplySearch();
        })
        .catch(function () {
            listEl.setAttribute('data-loaded', '0');
            listEl.removeAttribute('data-loaded-locale');
            listEl.innerHTML = '<span class="text-red-500 text-sm">Failed to load icons.</span>';
        });
    return _aboutFeatureIconDropdownLoadPromise;
};

/**
 * Open the shared icon dropdown for about-project feature editing.
 * Positions the dropdown under the clicked button. Icon list is loaded on page load or on first open.
 */
window.openAboutFeatureIconDropdown = function (id, index, initialKey, event) {
    const store = window.Alpine?.store?.('aboutFeatureEditing');
    if (!store) return;
    const btn = event?.target?.closest?.('button') ?? null;
    store.id = id;
    store.index = index;
    store.editingIcon = store.editingIcon || initialKey;
    store.openIconDropdown = true;

    var dropdownEl = document.getElementById('about-feature-icon-dropdown');
    if (dropdownEl && btn) {
        var anchor = document.querySelector('[data-about-feature-dropdown-anchor][data-feature-id="' + id + '"]');
        if (anchor) {
            if (dropdownEl.parentNode !== anchor) {
                anchor.appendChild(dropdownEl);
            }
            var btnRect = btn.getBoundingClientRect();
            var anchorRect = anchor.getBoundingClientRect();
            dropdownEl.style.top = (btnRect.bottom - anchorRect.top + 4) + 'px';
            dropdownEl.style.left = (btnRect.left - anchorRect.left) + 'px';
        }
    }

    loadAboutFeatureIconDropdownContent();
    var listEl = document.getElementById('about-feature-icon-dropdown-list');
    if (listEl && listEl.getAttribute('data-loaded') === '1') aboutFeatureIconDropdownApplySearch();
    aboutFeatureIconDropdownSyncDisplay(id, store.editingIcon);
};

/**
 * Reset about-project icon dropdown store state (close dropdown and clear positioning).
 * Use fullReset: true when closing without choosing (click outside / escape) to clear id/index/editingIcon too.
 */
window.closeAboutFeatureIconDropdown = function (fullReset) {
    const store = window.Alpine?.store?.('aboutFeatureEditing');
    if (!store) return;
    store.openIconDropdown = false;
    store.dropdownRect = null;
    if (fullReset) {
        store.id = null;
        store.index = null;
        store.editingIcon = null;
    }
};

import flatpickr from 'flatpickr';
import 'flatpickr/dist/themes/dark.css';
window.flatpickr = flatpickr;

window.memberFormDatepickers = function () {
    return {
        init(el) {
            if (typeof window.flatpickr === 'undefined') return;
            el.querySelectorAll('input[data-datepicker]').forEach((input) => {
                const prop = input.dataset.wireProperty;
                if (!prop) return;
                if (input._flatpickr) return;
                window.flatpickr(input, {
                    dateFormat: 'Y-m-d',
                    disableMobile: true,
                    onChange: (selected, dateStr) => {
                        input.value = dateStr || '';
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                    },
                });
            });
        },
    };
};

// Register Service Worker for PWA
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js')
            .then(registration => {
                console.log('[PWA] Service Worker registered successfully:', registration.scope);
            })
            .catch(error => {
                console.error('[PWA] Service Worker registration failed:', error);
            });
    });
}

// Proposal form (hero + dialog): on window so Alpine can resolve x-data="proposalFormData()".
window.proposalFormData = function () {
    return {
        proposalDialogOpen: false,
        proposalFormData: { title: '', message: '' },
        init() {
            this._successMessage = this.$el?.dataset?.successMessage || '';
        },
        submitProposal() {
            console.log('Proposal submitted:', this.proposalFormData);
            alert(this._successMessage || 'Sent.');
            this.proposalFormData = { title: '', message: '' };
            this.proposalDialogOpen = false;
        },
    };
};

// Only run our Alpine (stores + start) on public layout. App layout uses Livewire's Alpine.
const isPublicLayout = document.body?.dataset?.layout === 'public';

if (isPublicLayout) {
    import('alpinejs').then(({ default: Alpine }) => {
        window.Alpine = Alpine;

        Alpine.store('public', {
            mobileMenuOpen: false,
            langOpen: false,
            activeSection: 'hero',
        });

        Alpine.store('gym3d', {
            helpOpen: false,
        });

        // Detect iOS - ALWAYS use CSS-based fullscreen for iOS
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
        // Force CSS fullscreen for all iOS devices, regardless of fullscreenEnabled
        const useCSS = isIOS || (isSafari && !document.fullscreenEnabled);

        console.log('[Gym3D] Browser detection:', {
            userAgent: navigator.userAgent,
            isIOS,
            isSafari,
            useCSS,
            fullscreenEnabled: document.fullscreenEnabled,
            willUseCSS: useCSS ? 'YES - CSS Fullscreen' : 'NO - Native API'
        });

        window.toggleGym3dFullscreen = function(isFullscreen) {
            console.log('[Gym3D] toggleGym3dFullscreen called', { isFullscreen, isIOS, useCSS });

            const container = document.getElementById('gym3d-container');
            if (!container) {
                console.error('[Gym3D] Container not found!');
                return;
            }

            // iOS ALWAYS uses CSS-based fullscreen (never requestFullscreen)
            if (isIOS || useCSS) {
                console.log('[Gym3D] Using CSS fullscreen (iOS/Safari)', {
                    isFullscreen,
                    willEnter: !isFullscreen,
                    isIOS,
                    containerClasses: container.className
                });

                if (isFullscreen) {
                    // Exit fullscreen
                    console.log('[Gym3D] Exiting CSS fullscreen');
                    container.classList.remove('gym3d-ios-fullscreen');
                    document.body.classList.remove('gym3d-fullscreen-active');
                    document.body.style.overflow = '';
                    document.body.style.position = '';
                    document.body.style.width = '';
                    document.body.style.height = '';
                    document.body.style.top = '';
                    document.body.style.left = '';
                    document.documentElement.style.overflow = '';
                } else {
                    // Enter fullscreen
                    console.log('[Gym3D] Entering CSS fullscreen');
                    container.classList.add('gym3d-ios-fullscreen');
                    document.body.classList.add('gym3d-fullscreen-active');
                    document.body.style.overflow = 'hidden';
                    document.body.style.position = 'fixed';
                    document.body.style.width = '100%';
                    document.body.style.height = '100%';
                    document.body.style.top = '0';
                    document.body.style.left = '0';
                    document.documentElement.style.overflow = 'hidden';
                    // Scroll to top to ensure fullscreen starts at top
                    window.scrollTo(0, 0);

                    console.log('[Gym3D] CSS fullscreen applied, classes:', container.className);
                }

                // Trigger manual fullscreen state change
                const event = new Event('gym3d-fullscreen-change');
                event.isFullscreen = !isFullscreen;
                document.dispatchEvent(event);
                console.log('[Gym3D] Dispatched gym3d-fullscreen-change event', { newState: !isFullscreen });

                return; // Exit early - NEVER use requestFullscreen on iOS
            }

            // Standard Fullscreen API for non-iOS browsers only
            console.log('[Gym3D] Using native fullscreen API (Non-iOS)', { isFullscreen });
            if (isFullscreen) {
                if (document.exitFullscreen) document.exitFullscreen();
                else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
                else if (document.mozCancelFullScreen) document.mozCancelFullScreen();
                else if (document.msExitFullscreen) document.msExitFullscreen();
            } else {
                if (container.requestFullscreen) container.requestFullscreen();
                else if (container.webkitRequestFullscreen) container.webkitRequestFullscreen();
                else if (container.mozRequestFullScreen) container.mozRequestFullScreen();
                else if (container.msRequestFullscreen) container.msRequestFullscreen();
            }
        };

        window.gym3dData = function() {
            return {
                gym3dFullscreen: false,
                get isNotFullscreen() {
                    return !this.gym3dFullscreen;
                },
                init() {
                    console.log('[Gym3D] gym3dData init called');
                    const container = document.getElementById('gym3d-container');
                    if (!container) {
                        console.error('[Gym3D] Container not found in init!');
                        this.gym3dFullscreen = false;
                        return;
                    }
                    const self = this;
                    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
                    const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
                    // Force CSS fullscreen for all iOS devices
                    const useCSS = isIOS || (isSafari && !document.fullscreenEnabled);

                    console.log('[Gym3D] Init with:', { isIOS, isSafari, useCSS, willUseCSS: isIOS || useCSS });

                    const checkFullscreen = () => {
                        if (isIOS || useCSS) {
                            // For iOS/Safari, ONLY check the CSS class (never use fullscreen API)
                            const wasFullscreen = self.gym3dFullscreen;
                            self.gym3dFullscreen = container.classList.contains('gym3d-ios-fullscreen');
                            if (wasFullscreen !== self.gym3dFullscreen) {
                                console.log('[Gym3D] Fullscreen state changed (CSS):', self.gym3dFullscreen);
                            }
                        } else {
                            // For non-iOS browsers, use standard API
                            const isFullscreen = !!(
                                document.fullscreenElement === container ||
                                document.webkitFullscreenElement === container ||
                                document.mozFullScreenElement === container ||
                                document.msFullscreenElement === container
                            );
                            self.gym3dFullscreen = Boolean(isFullscreen);
                        }
                    };

                    // Only listen for standard fullscreen events on non-iOS
                    if (!isIOS) {
                        document.addEventListener('fullscreenchange', checkFullscreen);
                        document.addEventListener('webkitfullscreenchange', checkFullscreen);
                        document.addEventListener('mozfullscreenchange', checkFullscreen);
                        document.addEventListener('MSFullscreenChange', checkFullscreen);
                    }

                    // Always listen for custom CSS fullscreen event (used by iOS)
                    document.addEventListener('gym3d-fullscreen-change', (e) => {
                        console.log('[Gym3D] Received gym3d-fullscreen-change event:', e.isFullscreen);
                        if (isIOS || useCSS) {
                            self.gym3dFullscreen = e.isFullscreen;
                            console.log('[Gym3D] Updated gym3dFullscreen to:', self.gym3dFullscreen);
                        }
                    });

                    checkFullscreen();
                    console.log('[Gym3D] Initial fullscreen state:', self.gym3dFullscreen);
                },
                toggleFullscreen() {
                    console.log('[Gym3D] toggleFullscreen method called, current state:', this.gym3dFullscreen);
                    toggleGym3dFullscreen(this.gym3dFullscreen);
                },
                openHelp() {
                    const store = Alpine.store('gym3d');
                    if (store) store.helpOpen = true;
                },
                closeHelp() {
                    const store = Alpine.store('gym3d');
                    if (store) store.helpOpen = false;
                },
                get helpOpen() {
                    const store = Alpine.store('gym3d');
                    return store ? store.helpOpen : false;
                }
            };
        };

        Alpine.start();

        // scrollToSection(id) – used by mobile menu (public layout)
        window.scrollToSection = function (id) {
            const el = document.querySelector(typeof id === 'string' && id.startsWith('#') ? id : '#' + id);
            if (!el) return;
            const navHeight = 72;
            const top = el.getBoundingClientRect().top + window.pageYOffset - navHeight;
            window.scrollTo({ top, behavior: 'smooth' });
        };

        function observeSections() {
            const ids = ['hero', 'about', 'timeline', 'contact'];
            const store = Alpine.store('public');
            if (!store) return;

            const updateActiveSection = () => {
                const navHeight = 72;
                const scrollPosition = window.scrollY + navHeight + 100;
                let activeId = 'hero';
                let minDistance = Infinity;

                ids.forEach((id) => {
                    const el = document.getElementById(id);
                    if (!el) return;
                    const rect = el.getBoundingClientRect();
                    const elementTop = rect.top + window.scrollY;
                    const distance = Math.abs(elementTop - scrollPosition);
                    if (rect.top <= navHeight + 100 && rect.bottom > navHeight && distance < minDistance) {
                        minDistance = distance;
                        activeId = id;
                    }
                });
                store.activeSection = activeId;
            };

            let ticking = false;
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        updateActiveSection();
                        ticking = false;
                    });
                    ticking = true;
                }
            });
            updateActiveSection();

            const observer = new IntersectionObserver(
                () => updateActiveSection(),
                { rootMargin: '-72px 0px -50% 0px', threshold: [0, 0.1, 0.5, 1] }
            );
            ids.forEach((id) => {
                const el = document.getElementById(id);
                if (el) observer.observe(el);
            });
        }

        function initGym3dIfPresent() {
            const section = document.querySelector('[data-gym3d-logo]');
            if (!section) return;
            const container = section.querySelector('#canvas-container');
            if (!container) return;
            const logoUrl = section.getAttribute('data-gym3d-logo') || null;
            import('./gym3d.js').then(({ initGym3d }) => initGym3d(container, { logoUrl }));
        }

        function revealBody() {
            if (document.body) document.body.removeAttribute('x-cloak');
            observeSections();
            initGym3dIfPresent();
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', revealBody);
        } else {
            revealBody();
        }
    });
} else {
    // App layout: only remove x-cloak; Livewire provides Alpine
    function revealBody() {
        if (document.body) document.body.removeAttribute('x-cloak');
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', revealBody);
    } else {
        revealBody();
    }
}
