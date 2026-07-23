/**
 * Alpine data for the modal component.
 * Focus trap (tab/shift+tab), escape to close, body overflow lock.
 * Usage in Blade: x-data="modalData(@js($show), '{{ addslashes($name) }}', @js($attributes->has('focusable')))"
 */
export function modalData(initialShow = false, name = '', focusable = false) {
    return {
        show: initialShow,
        focusables() {
            const selector = 'a, button, input:not([type="hidden"]), textarea, select, details, [tabindex]:not([tabindex="-1"])';
            return [...this.$el.querySelectorAll(selector)]
                .filter(el => !el.hasAttribute('disabled'));
        },
        firstFocusable() { return this.focusables()[0]; },
        lastFocusable() { return this.focusables().slice(-1)[0]; },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable(); },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable(); },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1); },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1; },
        init() {
            this.$watch('show', (value) => {
                if (value) {
                    document.body.classList.add('overflow-y-hidden');
                    if (focusable) {
                        setTimeout(() => this.firstFocusable()?.focus(), 100);
                    }
                } else {
                    document.body.classList.remove('overflow-y-hidden');
                }
            });
        },
    };
}
