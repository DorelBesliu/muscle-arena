/**
 * Alpine.js data for the toast container (top-right notifications).
 * Used by the app layout. Pass the "Saved." message from the server:
 *   x-data="toastContainer(savedMessage)"
 */
window.toastContainer = function (savedMessage) {
    return {
        toasts: [],
        savedMessage: savedMessage || 'Saved.',
        addToast(message) {
            const id = Date.now();
            this.toasts.push({ id, message });
            setTimeout(() => {
                this.toasts = this.toasts.filter((t) => t.id !== id);
            }, 4000);
        },
    };
};
