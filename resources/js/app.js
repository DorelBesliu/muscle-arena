import './bootstrap';
import './toast.js';

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

        // Detect iOS
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        
        window.toggleGym3dFullscreen = function(isFullscreen) {
            const container = document.getElementById('gym3d-container');
            if (!container) return;
            
            // iOS doesn't support Fullscreen API for non-video elements
            // Use CSS-based fullscreen instead
            if (isIOS) {
                if (isFullscreen) {
                    // Exit fullscreen
                    container.classList.remove('gym3d-ios-fullscreen');
                    document.body.style.overflow = '';
                } else {
                    // Enter fullscreen
                    container.classList.add('gym3d-ios-fullscreen');
                    document.body.style.overflow = 'hidden';
                }
                // Trigger manual fullscreen state change
                const event = new Event('gym3d-fullscreen-change');
                event.isFullscreen = !isFullscreen;
                document.dispatchEvent(event);
            } else {
                // Standard Fullscreen API for other browsers
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
            }
        };

        window.gym3dData = function() {
            return {
                gym3dFullscreen: false,
                get isNotFullscreen() {
                    return !this.gym3dFullscreen;
                },
                init() {
                    const container = document.getElementById('gym3d-container');
                    if (!container) {
                        this.gym3dFullscreen = false;
                        return;
                    }
                    const self = this;
                    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
                    
                    const checkFullscreen = () => {
                        if (isIOS) {
                            // For iOS, check the CSS class
                            self.gym3dFullscreen = container.classList.contains('gym3d-ios-fullscreen');
                        } else {
                            // For other browsers, use standard API
                            const isFullscreen = !!(
                                document.fullscreenElement === container ||
                                document.webkitFullscreenElement === container ||
                                document.mozFullScreenElement === container ||
                                document.msFullscreenElement === container
                            );
                            self.gym3dFullscreen = Boolean(isFullscreen);
                        }
                    };
                    
                    // Listen for standard fullscreen events
                    document.addEventListener('fullscreenchange', checkFullscreen);
                    document.addEventListener('webkitfullscreenchange', checkFullscreen);
                    document.addEventListener('mozfullscreenchange', checkFullscreen);
                    document.addEventListener('MSFullscreenChange', checkFullscreen);
                    
                    // Listen for custom iOS fullscreen event
                    document.addEventListener('gym3d-fullscreen-change', (e) => {
                        if (isIOS) {
                            self.gym3dFullscreen = e.isFullscreen;
                        }
                    });
                    
                    checkFullscreen();
                },
                toggleFullscreen() {
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
