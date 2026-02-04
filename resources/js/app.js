import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('public', {
    mobileMenuOpen: false,
    langOpen: false,
    activeSection: 'hero',
});

Alpine.start();

// scrollToSection(id) – folosit de meniul mobil (layout) pentru smooth scroll cu offset 72px
window.scrollToSection = function (id) {
    const el = document.querySelector(typeof id === 'string' && id.startsWith('#') ? id : '#' + id);
    if (!el) return;
    const navHeight = 72;
    const top = el.getBoundingClientRect().top + window.pageYOffset - navHeight;
    window.scrollTo({ top, behavior: 'smooth' });
};

// Observă secțiunile și actualizează activeSection pentru meniul mobil (ca în React)
function observeSections() {
    const ids = ['hero', 'about', 'investment', 'timeline', 'contact'];
    const store = Alpine.store('public');
    if (!store) return;
    const observer = new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                if (!entry.isIntersecting) continue;
                const id = entry.target.id;
                if (ids.includes(id)) store.activeSection = id;
            }
        },
        { rootMargin: '-72px 0px -50% 0px', threshold: 0 }
    );
    ids.forEach((id) => {
        const el = document.getElementById(id);
        if (el) observer.observe(el);
    });
}

function revealBody() {
    if (document.body) document.body.removeAttribute('x-cloak');
    observeSections();
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', revealBody);
} else {
    revealBody();
}
