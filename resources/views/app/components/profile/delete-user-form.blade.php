<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use function Livewire\Volt\rules;
use function Livewire\Volt\state;

state(['password' => '', 'showModal' => false]);

rules(['password' => ['required', 'string', 'current_password']]);

$deleteUser = function (Logout $logout) {
    try {
        $this->validate();
    } catch (ValidationException $e) {
        $this->showModal = true;
        throw $e;
    }

    tap(Auth::user(), $logout(...))->delete();

    $this->redirect('/', navigate: true);
};

?>

<div>
    <button
        type="button"
        x-data
        x-on:click="$dispatch('open-modal', 'confirm-user-deletion')"
        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-500 border border-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all"
    >
        <x-lucide-trash-2 class="w-4 h-4" />
        {{ __('ui.profile_delete_account_button') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$showModal" focusable>
        <form wire:submit="deleteUser" class="p-6 bg-[#111111] border border-[#333333] rounded-xl text-white">
            @csrf
            <h2 class="text-lg font-bold text-white">
                {{ __('ui.profile_delete_dialog_title') }}
            </h2>
            <p class="mt-1 text-sm text-[#CCCCCC]">
                {{ __('ui.profile_delete_dialog_desc') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" :value="__('Password')" class="sr-only" />
                <x-text-input
                    wire:model="password"
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full h-9 text-sm bg-[#000000] border-[#333333] text-white focus:border-[#333333] focus:ring-0 rounded-md"
                    placeholder="{{ __('Password') }}"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', 'confirm-user-deletion')"
                    class="flex-1 px-4 py-2 text-sm font-medium text-[#CCCCCC] border border-[#333333] rounded-lg hover:bg-[#333333] hover:text-white transition-colors"
                >
                    {{ __('ui.close') }}
                </button>
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 transition-colors disabled:opacity-70 disabled:cursor-wait"
                >
                    <span wire:loading.remove wire:target="deleteUser" class="inline-flex items-center gap-2">
                        <x-lucide-trash-2 class="w-4 h-4" />
                        {{ __('ui.profile_delete_confirm') }}
                    </span>
                    <span wire:loading wire:target="deleteUser" class="inline-flex items-center gap-2">
                        <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                        {{ __('ui.profile_delete_confirm') }}
                    </span>
                </button>
            </div>
        </form>
    </x-modal>
</div>
