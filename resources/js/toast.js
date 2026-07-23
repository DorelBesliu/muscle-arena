/**
 * Alpine.js data for the toast container (top-center notifications).
 * Used by the app layout. Pass the "Saved." message from the server:
 *   x-data="toastContainer(savedMessage)"
 */
window.toastContainer = function (savedMessage, type = 'success') {
    return {
        type: type || 'success',
        toasts: [],
        savedMessage: savedMessage || 'Saved.',
        addToast(message, type = 'success') {
            const maxToasts = 3;
            if (this.toasts.length >= maxToasts) {
                this.toasts.shift();
            }
            const id = Date.now();
            this.toasts.push({ id, message, type });
            setTimeout(() => {
                this.toasts = this.toasts.filter((t) => t.id !== id);
            }, 4000);
        },
    };
};
