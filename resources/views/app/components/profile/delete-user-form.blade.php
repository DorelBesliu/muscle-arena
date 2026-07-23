<div>
    <button
        type="button"
        @click="Alpine.store('confirmPassword').open = true; Alpine.store('confirmPassword').runAfterConfirm = () => $wire.deleteUser()"
        class="inline-flex items-center justify-center gap-2 p-2 md:px-4 md:py-2 text-sm font-medium text-red-500 border border-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all"
        aria-label="{{ __('ui.profile_delete_account_button') }}"
    >
        <x-lucide-trash-2 class="w-4 h-4 shrink-0" />
        <span class="hidden md:inline">{{ __('ui.profile_delete_account_button') }}</span>
    </button>
</div>
