/**
 * Alpine data for Enable 2FA with XHR + password confirmation dialog.
 * Usage in Blade: x-data="enable2faData('{{ route('two-factor.enable') }}', '{{ route('password.confirm.store') }}', '{{ addslashes(__('ui.confirm_password_label')) }}')"
 */
export function enable2faData(enableUrl) {
    return {
        loading: false,
        enableUrl: enableUrl || '',
        get csrfToken() {
            return document.querySelector('meta[name=csrf-token]')?.content || '';
        },
        async enable2fa() {
            this.loading = true;
            try {
                const fd = new FormData();
                fd.append('_token', this.csrfToken);
                const res = await fetch(this.enableUrl, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                    body: fd,
                    credentials: 'same-origin'
                });
                if (res.ok) {
                    window.location.reload();
                    return;
                }
                if (res.status === 423) {
                    const store = window.Alpine.store('confirmPassword');
                    store.open = true;
                    store.runAfterConfirm = () => this.enable2fa();
                } else {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: res.statusText } }));
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: e.message } }));
            } finally {
                this.loading = false;
            }
        }
    };
}

/**
 * Alpine data for confirming 2FA code via XHR; shows password confirmation dialog on 423.
 */
export function confirm2faData(confirmUrl) {
    return {
        code: '',
        loading: false,
        confirmUrl: confirmUrl || '',
        get csrfToken() {
            return document.querySelector('meta[name=csrf-token]')?.content || '';
        },
        async confirm2fa() {
            const code = (this.code || '').trim();
            if (!code) return;
            this.loading = true;
            try {
                const fd = new FormData();
                fd.append('_token', this.csrfToken);
                fd.append('code', code);
                const res = await fetch(this.confirmUrl, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                    body: fd,
                    credentials: 'same-origin'
                });
                if (res.ok) {
                    window.location.reload();
                    return;
                }
                if (res.status === 423) {
                    const store = window.Alpine.store('confirmPassword');
                    store.open = true;
                    store.runAfterConfirm = () => this.confirm2fa();
                } else {
                    const data = await res.json().catch(() => ({}));
                    const msg = data?.message || data?.errors?.code?.[0] || res.statusText;
                    window.toast?.('error', msg);
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: msg } }));
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: e.message } }));
            } finally {
                this.loading = false;
            }
        }
    };
}

/**
 * Alpine data for Disable 2FA via XHR; on 423 shows password dialog.
 * Reuse confirmPassword component from confirmPassword.js for the dialog.
 * Parent listens: @password-confirmed.window="showPasswordDialog = false; disable2fa(true)"
 * Usage in Blade: x-data="disable2fa('{{ route('two-factor.disable') }}', '{{ addslashes(__('ui.are_you_sure_want_to_disable_2fa')) }}')"
 */
export function disable2faData(disableUrl, confirmMessage) {
    return {
        loading: false,
        disableUrl: disableUrl || '',
        confirmMessage: confirmMessage || '',
        get csrfToken() {
            return document.querySelector('meta[name=csrf-token]')?.content || '';
        },
        async disable2fa(skipConfirm = false) {
            if (this.confirmMessage && !skipConfirm && !window.confirm(this.confirmMessage)) return;
            this.loading = true;
            try {
                const res = await fetch(this.disableUrl, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                });
                if (res.ok) {
                    window.location.reload();
                    return;
                }
                if (res.status === 423) {
                    const store = window.Alpine.store('confirmPassword');
                    store.open = true;
                    store.runAfterConfirm = () => this.disable2fa(true);
                } else {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: res.statusText } }));
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: e.message } }));
            } finally {
                this.loading = false;
            }
        },
    };
}

/**
 * Alpine data for downloading 2FA recovery codes as a text file.
 * Usage in Blade: x-data="recoveryCodesDownload(@js($user->recoveryCodes()))"
 */
export function recoveryCodesDownloadData(codes) {
    return {
        codes: codes || [],
        download() {
            const blob = new Blob([this.codes.join('\n')], { type: 'text/plain' });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'recovery-codes.txt';
            a.click();
            URL.revokeObjectURL(a.href);
        },
    };
}
