<?php

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

new #[Layout('layouts.app')] class extends Component
{
    public string $searchTerm = '';

    public bool $isModalOpen = false;

    public ?int $editingId = null;

    public string $newName = '';

    public string $newEmail = '';

    public ?int $deleteConfirmId = null;

    public function mount(): void
    {
        if (auth()->user()?->role !== 'admin') {
            abort(403, 'Access denied.');
        }
    }

    #[Computed]
    public function administrators()
    {
        return User::query()
            ->where('role', 'admin')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    #[Computed]
    public function filteredAdministrators()
    {
        $search = strtolower($this->searchTerm);
        return $this->administrators->filter(function (User $admin) use ($search) {
            if ($search === '') {
                return true;
            }
            return str_contains(strtolower($admin->name), $search)
                || str_contains(strtolower($admin->email), $search);
        })->values();
    }

    public function openAddModal(): void
    {
        $this->newName = '';
        $this->newEmail = '';
        $this->editingId = null;
        $this->isModalOpen = true;
        $this->resetValidation();
    }

    public function openEditModal(int $id): void
    {
        $admin = User::where('id', $id)->where('role', 'admin')->firstOrFail();
        $this->editingId = $id;
        $this->newName = $admin->name;
        $this->newEmail = $admin->email;
        $this->isModalOpen = true;
        $this->resetValidation();
    }

    public function saveAdministrator(): void
    {
        $rules = [
            'newName' => 'required|string|max:255',
            'newEmail' => 'required|email',
        ];
        $uniqueRule = $this->editingId
            ? 'unique:users,email,' . $this->editingId
            : 'unique:users,email';
        $rules['newEmail'] .= '|' . $uniqueRule;

        $this->validate($rules, [
            'newName.required' => __('ui.admins_name_required'),
            'newEmail.required' => __('ui.admins_email_required'),
            'newEmail.email' => __('ui.admins_email_invalid'),
            'newEmail.unique' => __('ui.admins_email_exists'),
        ]);

        if ($this->editingId) {
            User::where('id', $this->editingId)->where('role', 'admin')->update([
                'name' => $this->newName,
                'email' => $this->newEmail,
            ]);
            $this->dispatch('toast', message: __('ui.admins_update_success'));
        } else {
            User::create([
                'name' => $this->newName,
                'email' => $this->newEmail,
                'password' => Hash::make(Str::random(32)),
                'role' => 'admin',
            ]);
            $this->dispatch('toast', message: __('ui.admins_add_success'));
        }

        $this->newName = '';
        $this->newEmail = '';
        $this->editingId = null;
        $this->isModalOpen = false;
        $this->dispatch('$refresh');
    }

    public function cancelModal(): void
    {
        $this->isModalOpen = false;
        $this->newName = '';
        $this->newEmail = '';
        $this->editingId = null;
        $this->resetValidation();
    }

    public function confirmDelete(int $id): void
    {
        if ($id === auth()->id()) {
            return;
        }
        $this->deleteConfirmId = $id;
    }

    public function deleteAdministrator(): void
    {
        if ($this->deleteConfirmId === null) {
            return;
        }
        if ($this->deleteConfirmId === auth()->id()) {
            $this->deleteConfirmId = null;
            return;
        }
        User::where('id', $this->deleteConfirmId)->where('role', 'admin')->delete();
        $this->deleteConfirmId = null;
        $this->dispatch('$refresh');
        $this->dispatch('toast', message: __('ui.admins_delete_success'));
    }

    public function cancelDelete(): void
    {
        $this->deleteConfirmId = null;
    }
}; ?>

<div class="min-h-screen bg-[#000000] text-white">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-4">
            <h3 class="text-lg font-black text-white mb-3">{{ __('ui.sidebar_administrators') }}</h3>

            {{-- Search + Add --}}
            <div class="flex flex-col md:flex-row gap-2 items-stretch md:items-center">
                <div class="flex-1 w-full relative">
                    <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[#CCCCCC]" />
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="searchTerm"
                        placeholder="{{ __('ui.admins_search') }}"
                        class="w-full h-9 pl-10 pr-3 bg-[#111111] border-2 border-[#333333] rounded-xl text-sm text-white placeholder-[#666666] focus:outline-none"
                    />
                </div>
                <button
                    type="button"
                    wire:click="openAddModal"
                    class="inline-flex items-center justify-center gap-2 h-9 px-4 rounded-xl bg-[#F97316] hover:bg-[#ea580c] text-sm font-medium text-white transition-colors w-full md:w-auto"
                >
                    <x-lucide-user-plus class="w-4 h-4" />
                    {{ __('ui.admins_add_admin') }}
                </button>
            </div>

            {{-- Table --}}
            @if($this->filteredAdministrators->isNotEmpty())
                <div class="bg-[#111111] border-2 border-[#333333] rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-[#333333]">
                                    <th class="text-xs font-medium text-[#CCCCCC] px-4 py-3">{{ __('ui.admins_name') }}</th>
                                    <th class="text-xs font-medium text-[#CCCCCC] px-4 py-3">{{ __('ui.admins_email') }}</th>
                                    <th class="text-xs font-medium text-[#CCCCCC] px-4 py-3">{{ __('ui.admins_created_at') }}</th>
                                    <th class="text-xs font-medium text-[#CCCCCC] px-4 py-3">2FA</th>
                                    <th class="text-xs font-medium text-[#CCCCCC] px-4 py-3">{{ __('ui.admins_actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($this->filteredAdministrators as $admin)
                                    <tr class="border-b border-[#333333] hover:bg-[#1a1a1a]">
                                        <td class="px-4 py-2 font-medium text-white text-sm">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#F97316] to-[#EF4444] flex items-center justify-center flex-shrink-0">
                                                    <span class="text-white font-bold text-xs">{{ strtoupper(mb_substr($admin->name, 0, 1)) }}</span>
                                                </div>
                                                <span>{{ $admin->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2">
                                            <div class="flex items-center gap-1.5 text-[#CCCCCC] text-xs">
                                                <x-lucide-mail class="w-3 h-3 flex-shrink-0" />
                                                <span>{{ $admin->email }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2">
                                            <div class="flex items-center gap-1.5 text-[#CCCCCC]">
                                                <x-lucide-calendar class="w-3 h-3 flex-shrink-0" />
                                                <span class="text-xs">{{ $admin->created_at->translatedFormat('d M Y') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($admin->two_factor_confirmed_at)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs bg-green-500/20 text-green-400 border border-green-500/30">
                                                    <x-lucide-shield class="w-3 h-3 flex-shrink-0" />
                                                    {{ __('ui.admins_2fa_enabled') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs bg-gray-500/20 text-gray-400 border border-gray-500/30">
                                                    <x-lucide-shield-off class="w-3 h-3 flex-shrink-0" />
                                                    {{ __('ui.admins_2fa_disabled') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            <div class="flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    wire:click="openEditModal({{ $admin->id }})"
                                                    class="inline-flex items-center justify-center h-8 w-8 rounded-lg border-2 border-[#333333] text-[#CCCCCC] hover:border-[#F97316] hover:text-white transition-colors"
                                                >
                                                    <x-lucide-pencil class="w-4 h-4" />
                                                </button>
                                                @if($admin->id !== auth()->id())
                                                    <button
                                                        type="button"
                                                        wire:click="confirmDelete({{ $admin->id }})"
                                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg border-2 border-[#333333] text-[#CCCCCC] hover:border-red-500 hover:text-red-400 transition-colors"
                                                    >
                                                        <x-lucide-trash-2 class="w-4 h-4" />
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-[#111111] border-2 border-[#333333] rounded-xl p-8 text-center">
                    <x-lucide-inbox class="w-12 h-12 text-[#666666] mx-auto mb-3 opacity-50" />
                    <p class="text-white font-bold text-sm mb-1">{{ __('ui.admins_no_results') }}</p>
                    <p class="text-[#CCCCCC] text-xs">{{ __('ui.admins_no_results_desc') }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    @if($isModalOpen)
        <x-app-dialog wire:key="modal-form-admin" :title="$editingId ? __('ui.admins_edit_admin') : __('ui.admins_add_admin')" maxWidth="lg">
            <x-slot:close>
                <button type="button" wire:click="cancelModal" class="h-8 w-8 flex items-center justify-center rounded-lg border-2 border-[#333333] text-[#CCCCCC] hover:text-white">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </x-slot:close>
            <div class="space-y-4">
                <div>
                    <label for="admin-name" class="block text-sm font-medium text-[#CCCCCC] mb-1">{{ __('ui.admins_name') }}</label>
                    <input id="admin-name" type="text" wire:model="newName" placeholder="{{ __('ui.admins_name_placeholder') }}"
                        class="w-full h-10 px-3 bg-[#000000] border-2 border-[#333333] rounded-xl text-sm text-white placeholder-[#666666] focus:outline-none" />
                    @error('newName')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="admin-email" class="block text-sm font-medium text-[#CCCCCC] mb-1">{{ __('ui.admins_email') }}</label>
                    <input id="admin-email" type="email" wire:model="newEmail" placeholder="{{ __('ui.admins_email_placeholder') }}"
                        class="w-full h-10 px-3 bg-[#000000] border-2 border-[#333333] rounded-xl text-sm text-white placeholder-[#666666] focus:outline-none" />
                    @error('newEmail')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <x-slot:footer>
                <button type="button" wire:click="cancelModal"
                    class="h-9 px-4 rounded-xl border-2 border-[#333333] text-sm font-medium text-[#CCCCCC] hover:text-white">{{ __('ui.admins_cancel') }}</button>
<x-app.components.loading-button
                type="button"
                wire-target="saveAdministrator"
                wire:click="saveAdministrator"
                class="h-9 px-4 rounded-xl bg-[#F97316] hover:bg-[#ea580c] text-sm font-medium text-white min-w-[5rem]"
            >
                <x-slot:label>{{ __('ui.admins_save') }}</x-slot:label>
            </x-app.components.loading-button>
            </x-slot:footer>
        </x-app-dialog>
    @endif

    {{-- Delete confirmation --}}
    @if($deleteConfirmId !== null)
        <x-app-dialog wire:key="modal-delete-admin" :title="__('ui.admins_delete')" maxWidth="md">
            <x-slot:close>
                <button type="button" wire:click="cancelDelete" class="h-8 w-8 flex items-center justify-center rounded-lg border-2 border-[#333333] text-[#CCCCCC] hover:text-white">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </x-slot:close>
            <p class="text-[#CCCCCC] text-sm">{{ __('ui.admins_delete_confirm') }}</p>
            <x-slot:footer>
                <button type="button" wire:click="cancelDelete"
                    class="h-9 px-4 rounded-xl border-2 border-[#333333] text-sm font-medium text-[#CCCCCC] hover:text-white">{{ __('ui.admins_cancel') }}</button>
<x-app.components.loading-button
                type="button"
                wire-target="deleteAdministrator"
                wire:click="deleteAdministrator"
                class="h-9 px-4 rounded-xl bg-red-500 hover:bg-red-600 text-sm font-medium text-white min-w-[5rem]"
            >
                <x-slot:label>{{ __('ui.admins_delete_confirm_btn') }}</x-slot:label>
            </x-app.components.loading-button>
            </x-slot:footer>
        </x-app-dialog>
    @endif
</div>
