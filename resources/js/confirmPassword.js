/**
 * Reusable Alpine data for "confirm password" flow.
 * POSTs password to the given URL; on success dispatches 'password-confirmed' so the parent can run the next action (e.g. confirm2fa, enable2fa, deleteAccount).
 *
 * Usage in Blade: wrap the confirm-password dialog in
 *   x-data="confirmPassword('{{ route('password.confirm.store') }}', '{{ addslashes(__('ui.confirm_password_label')) }}')"
 * Parent listens: @password-confirmed.window="showPasswordDialog = false; yourNextAction()"
 */
export function confirmPasswordData(passwordConfirmUrl, passwordLabel) {
    return {
        password: '',
        passwordError: null,
        loading: false,
        passwordConfirmUrl: passwordConfirmUrl || '',
        passwordLabel: passwordLabel || 'Password',
        get csrfToken() {
            return document.querySelector('meta[name=csrf-token]')?.content || '';
        },
        async confirmPassword() {
            this.passwordError = null;
            if (!this.password.trim()) {
                this.passwordError = this.passwordLabel;
                return;
            }
            this.loading = true;
            try {
                const fd = new FormData();
                fd.append('_token', this.csrfToken);
                fd.append('password', this.password);
                const res = await fetch(this.passwordConfirmUrl, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                    body: fd,
                    credentials: 'same-origin'
                });
                if (res.ok) {
                    this.password = '';
                    window.dispatchEvent(new CustomEvent('password-confirmed'));
                } else {
                    const data = await res.json().catch(() => ({}));
                    this.passwordError = data?.errors?.password?.[0] || data?.message || this.passwordLabel;
                }
            } catch (e) {
                this.passwordError = e.message;
            } finally {
                this.loading = false;
            }
        },
    };
}
