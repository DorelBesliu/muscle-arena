<div class="app-members-page min-h-screen bg-[#000000] text-white">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-4">
            <h3 class="text-lg font-black text-white mb-3">{{ __('ui.members_title') }}</h3>

            {{-- Search, Sort, Export, Add --}}
            <div class="flex flex-col md:flex-row gap-2 items-stretch md:items-center">
                <div class="flex-1 w-full relative">
                    <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[#CCCCCC]" />
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="searchTerm"
                        placeholder="{{ __('ui.members_search') }}"
                        class="w-full h-9 pl-10 pr-3 bg-[#111111] border-2 border-[#333333] rounded-xl text-sm text-white placeholder-[#666666] focus:outline-none"
                    />
                </div>
                <select
                    wire:model.live="statusFilter"
                    class="h-9 w-full md:w-auto min-w-[120px] bg-[#000000] border-2 border-[#333333] rounded-xl px-3 py-2 text-sm text-white focus:outline-none cursor-pointer"
                >
                    <option value="">{{ __('ui.members_status_all') }}</option>
                    <option value="active">{{ __('ui.members_active') }}</option>
                    <option value="inactive">{{ __('ui.members_inactive') }}</option>
                </select>
                <a
                    href="{{ route('clients.export') }}?sort={{ $sortBy }}&dir={{ $sortDir }}&q={{ urlencode($searchTerm) }}&status={{ $statusFilter }}"
                    class="inline-flex items-center justify-center gap-2 h-9 px-4 rounded-xl bg-[#111111] border-2 border-[#333333] hover:border-[#F97316] text-sm font-medium text-white transition-colors"
                >
                    <x-lucide-download class="w-4 h-4" />
                    <span>{{ __('ui.members_export') }}</span>
                </a>
                <button
                    type="button"
                    wire:click="openAddModal"
                    class="inline-flex items-center justify-center gap-2 h-9 px-4 rounded-xl bg-[#F97316] hover:bg-[#ea580c] text-sm font-medium text-white transition-colors"
                >
                    <x-lucide-plus class="w-4 h-4" />
                    <span>{{ __('ui.members_add') }}</span>
                </button>
            </div>

            {{-- Mobile: cards --}}
            @if($this->filteredAndSortedMembers->isNotEmpty())
                <div class="block md:hidden space-y-3">
                    @foreach($this->filteredAndSortedMembers as $member)
                        <div class="bg-[#111111] border-2 border-[#333333] rounded-xl p-4">
                            <div class="flex justify-between items-start gap-2 mb-3">
                                <h4 class="font-bold text-white text-sm">
                                    {{ $member->first_name }} {{ $member->last_name }}
                                </h4>
                                <button
                                    type="button"
                                    wire:click="toggleMemberStatus({{ $member->id }})"
                                    class="shrink-0 inline-flex items-center px-2 py-0.5 rounded text-xs border cursor-pointer transition-opacity hover:opacity-80 {{ ($member->status ?? 'active') === 'active' ? 'bg-green-500/20 text-green-400 border-green-500/30' : 'bg-red-500/20 text-red-400 border-red-500/30' }}"
                                >
                                    {{ ($member->status ?? 'active') === 'active' ? __('ui.members_active') : __('ui.members_inactive') }}
                                </button>
                            </div>
                            <div class="space-y-2 text-xs text-[#CCCCCC] mb-4">
                                <div class="flex items-center gap-2">
                                    <x-lucide-mail class="w-3.5 h-3.5 shrink-0 text-[#666666]" />
                                    <span>{{ $member->email ?? '–' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-lucide-phone class="w-3.5 h-3.5 shrink-0 text-[#666666]" />
                                    <span>{{ $member->phone ?? '–' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-lucide-calendar class="w-3.5 h-3.5 shrink-0 text-[#666666]" />
                                    <span>{{ __('ui.members_registration_date') }}: {{ $member->created_at->translatedFormat('d M Y') }}</span>
                                </div>
                                @if($member->subscription_expiry)
                                    <div class="flex items-center gap-2">
                                        <x-lucide-calendar class="w-3.5 h-3.5 shrink-0 text-[#666666]" />
                                        <span>{{ __('ui.members_expiry_date') }}: {{ $member->subscription_expiry->translatedFormat('d M Y') }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex gap-2 pt-2 border-t border-[#333333]">
                                <button
                                    type="button"
                                    wire:click="openEditModal({{ $member->id }})"
                                    class="flex-1 inline-flex items-center justify-center gap-2 h-9 rounded-lg border-2 border-[#333333] text-[#CCCCCC] hover:border-[#F97316] hover:text-white transition-colors text-sm font-medium"
                                >
                                    <x-lucide-pencil class="w-4 h-4" />
                                    {{ __('ui.members_edit_btn') }}
                                </button>
                                <button
                                    type="button"
                                    wire:click="confirmDelete({{ $member->id }})"
                                    class="inline-flex items-center justify-center h-9 w-9 rounded-lg border-2 border-[#333333] text-[#CCCCCC] hover:border-red-500 hover:text-red-400 transition-colors"
                                >
                                    <x-lucide-trash-2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Desktop: table --}}
                <div class="hidden md:block bg-[#111111] border-2 border-[#333333] rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-[#333333]">
                                    <th class="text-xs font-medium text-[#CCCCCC] px-4 py-3">
                                        <button type="button" wire:click="sortByColumn('name')" class="inline-flex items-center gap-1 hover:text-white transition-colors text-left">
                                            {{ __('ui.members_name') }}
                                            @if($sortBy === 'name')
                                                <x-lucide-chevron-down class="w-3.5 h-3.5 shrink-0 {{ $sortDir === 'asc' ? 'rotate-180' : '' }}" />
                                            @else
                                                <span class="inline-flex flex-col items-center leading-none opacity-60">
                                                    <x-lucide-chevron-down class="w-3 h-3 rotate-180 -mb-0.5" />
                                                    <x-lucide-chevron-down class="w-3 h-3" />
                                                </span>
                                            @endif
                                        </button>
                                    </th>
                                    <th class="text-xs font-medium text-[#CCCCCC] px-4 py-3">{{ __('ui.members_contact') }}</th>
                                    <th class="text-xs font-medium text-[#CCCCCC] px-4 py-3">{{ __('ui.members_status') }}</th>
                                    <th class="text-xs font-medium text-[#CCCCCC] px-4 py-3">
                                        <button type="button" wire:click="sortByColumn('registration')" class="inline-flex items-center gap-1 hover:text-white transition-colors text-left">
                                            {{ __('ui.members_registration_date') }}
                                            @if($sortBy === 'registration')
                                                <x-lucide-chevron-down class="w-3.5 h-3.5 shrink-0 {{ $sortDir === 'asc' ? 'rotate-180' : '' }}" />
                                            @else
                                                <span class="inline-flex flex-col items-center leading-none opacity-60">
                                                    <x-lucide-chevron-down class="w-3 h-3 rotate-180 -mb-0.5" />
                                                    <x-lucide-chevron-down class="w-3 h-3" />
                                                </span>
                                            @endif
                                        </button>
                                    </th>
                                    <th class="text-xs font-medium text-[#CCCCCC] px-4 py-3">
                                        <button type="button" wire:click="sortByColumn('expiry')" class="inline-flex items-center gap-1 hover:text-white transition-colors text-left">
                                            {{ __('ui.members_expiry_date') }}
                                            @if($sortBy === 'expiry')
                                                <x-lucide-chevron-down class="w-3.5 h-3.5 shrink-0 {{ $sortDir === 'asc' ? 'rotate-180' : '' }}" />
                                            @else
                                                <span class="inline-flex flex-col items-center leading-none opacity-60">
                                                    <x-lucide-chevron-down class="w-3 h-3 rotate-180 -mb-0.5" />
                                                    <x-lucide-chevron-down class="w-3 h-3" />
                                                </span>
                                            @endif
                                        </button>
                                    </th>
                                    <th class="text-xs font-medium text-[#CCCCCC] px-4 py-3">{{ __('ui.members_actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($this->filteredAndSortedMembers as $member)
                                    <tr class="border-b border-[#333333] hover:bg-[#1a1a1a]">
                                        <td class="px-4 py-2 font-medium text-white text-sm">
                                            {{ $member->first_name }} {{ $member->last_name }}
                                        </td>
                                        <td class="px-4 py-2">
                                            <div class="space-y-0.5">
                                                <div class="flex items-center gap-1.5 text-[#CCCCCC] text-xs">
                                                    <x-lucide-mail class="w-3 h-3 flex-shrink-0" />
                                                    <span>{{ $member->email ?? '–' }}</span>
                                                </div>
                                                <div class="flex items-center gap-1.5 text-[#CCCCCC] text-xs">
                                                    <x-lucide-phone class="w-3 h-3 flex-shrink-0" />
                                                    <span>{{ $member->phone ?? '–' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2">
                                            <button
                                                type="button"
                                                wire:click="toggleMemberStatus({{ $member->id }})"
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs border cursor-pointer transition-opacity hover:opacity-80 {{ ($member->status ?? 'active') === 'active' ? 'bg-green-500/20 text-green-400 border-green-500/30' : 'bg-red-500/20 text-red-400 border-red-500/30' }}"
                                            >
                                                {{ ($member->status ?? 'active') === 'active' ? __('ui.members_active') : __('ui.members_inactive') }}
                                            </button>
                                        </td>
                                        <td class="px-4 py-2">
                                            <div class="flex items-center gap-1.5 text-[#CCCCCC]">
                                                <x-lucide-calendar class="w-3 h-3 flex-shrink-0" />
                                                <span class="text-xs">{{ $member->created_at->translatedFormat('d M Y') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($member->subscription_expiry)
                                                <div class="flex items-center gap-1.5 text-[#CCCCCC]">
                                                    <x-lucide-calendar class="w-3 h-3 flex-shrink-0" />
                                                    <span class="text-xs">{{ $member->subscription_expiry->translatedFormat('d M Y') }}</span>
                                                </div>
                                            @else
                                                <span class="text-[#666666] text-xs">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            <div class="flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    wire:click="openEditModal({{ $member->id }})"
                                                    class="inline-flex items-center justify-center h-8 w-8 rounded-lg border-2 border-[#333333] text-[#CCCCCC] hover:border-[#F97316] hover:text-white transition-colors"
                                                >
                                                    <x-lucide-pencil class="w-4 h-4" />
                                                </button>
                                                <button
                                                    type="button"
                                                    wire:click="confirmDelete({{ $member->id }})"
                                                    class="inline-flex items-center justify-center h-8 w-8 rounded-lg border-2 border-[#333333] text-[#CCCCCC] hover:border-red-500 hover:text-red-400 transition-colors"
                                                >
                                                    <x-lucide-trash-2 class="w-4 h-4" />
                                                </button>
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
                    <p class="text-white font-bold text-sm mb-1">{{ __('ui.members_no_results') }}</p>
                    <p class="text-[#CCCCCC] text-xs">{{ __('ui.members_no_results_desc') }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    @if($isModalOpen)
        <x-app-dialog wire:key="modal-form" :title="$editingId ? __('ui.members_edit') : __('ui.members_add')" maxWidth="lg">
            <x-slot:close>
                <button type="button" wire:click="cancelModal" class="h-8 w-8 flex items-center justify-center rounded-lg border-2 border-[#333333] text-[#CCCCCC] hover:text-white">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </x-slot:close>
            <div class="w-full max-w-full space-y-4" x-data="memberFormDatepickers()" x-init="$nextTick(() => init($el))">
                <div>
                    <label for="member-first-name" class="block text-sm font-medium text-[#CCCCCC] mb-1">{{ __('ui.members_first_name') }}</label>
                    <input id="member-first-name" type="text" wire:model="firstName" placeholder="{{ __('ui.members_first_name') }}"
                        class="w-full h-10 px-3 bg-[#000000] border-2 border-[#333333] rounded-xl text-sm text-white placeholder-[#666666] focus:outline-none" />
                </div>
                <div>
                    <label for="member-last-name" class="block text-sm font-medium text-[#CCCCCC] mb-1">{{ __('ui.members_last_name') }}</label>
                    <input id="member-last-name" type="text" wire:model="lastName" placeholder="{{ __('ui.members_last_name') }}"
                        class="w-full h-10 px-3 bg-[#000000] border-2 border-[#333333] rounded-xl text-sm text-white placeholder-[#666666] focus:outline-none" />
                </div>
                <div>
                    <label for="member-registration-date" class="block text-sm font-medium text-[#CCCCCC] mb-1 cursor-pointer">{{ __('ui.members_registration_date') }}</label>
                    <input id="member-registration-date" type="text" wire:model="registrationDate" placeholder="YYYY-MM-DD" readonly
                        data-datepicker data-wire-property="registrationDate"
                        class="block w-full min-w-0 h-10 px-3 bg-[#000000] border-2 border-[#333333] rounded-xl text-sm text-white focus:outline-none cursor-pointer box-border"
                        autocomplete="off" />
                </div>
                <div>
                    <label for="member-phone" class="block text-sm font-medium text-[#CCCCCC] mb-1">{{ __('ui.members_phone') }}</label>
                    <input id="member-phone" type="text" wire:model="phone" placeholder="{{ __('ui.members_phone') }}"
                        class="w-full h-10 px-3 bg-[#000000] border-2 border-[#333333] rounded-xl text-sm text-white placeholder-[#666666] focus:outline-none" />
                </div>
                <div>
                    <label for="member-email" class="block text-sm font-medium text-[#CCCCCC] mb-1">{{ __('ui.members_email') }}</label>
                    <input id="member-email" type="email" wire:model="email" placeholder="{{ __('ui.members_email') }}"
                        class="w-full h-10 px-3 bg-[#000000] border-2 border-[#333333] rounded-xl text-sm text-white placeholder-[#666666] focus:outline-none" />
                </div>
                <div class="flex items-center justify-between gap-4">
                    <span class="text-sm font-medium text-[#CCCCCC]">{{ __('ui.members_active') }}</span>
                    <button
                        type="button"
                        id="member-status"
                        role="switch"
                        aria-checked="{{ $status === 'active' }}"
                        wire:click="toggleStatus"
                        class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none focus:ring-0 {{ $status === 'active' ? 'bg-[#F97316]' : 'bg-[#333333]' }}"
                    >
                        <span
                            class="pointer-events-none absolute h-6 w-6 rounded-full bg-white shadow transition duration-200 {{ $status === 'active' ? 'left-5 translate-x-0' : 'left-0.5' }}"
                        ></span>
                    </button>
                </div>
                <div>
                    <label for="member-subscription-expiry" class="block text-sm font-medium text-[#CCCCCC] mb-1 cursor-pointer">{{ __('ui.members_subscription_expiry_date') }}</label>
                    <input id="member-subscription-expiry" type="text" wire:model="subscriptionExpiryDate" placeholder="YYYY-MM-DD" readonly
                        data-datepicker data-wire-property="subscriptionExpiryDate"
                        class="block w-full min-w-0 h-10 px-3 bg-[#000000] border-2 border-[#333333] rounded-xl text-sm text-white focus:outline-none cursor-pointer box-border"
                        autocomplete="off" />
                </div>
            </div>
            <x-slot:footer>
                <button type="button" wire:click="cancelModal"
                    class="h-9 px-4 rounded-xl border-2 border-[#333333] text-sm font-medium text-[#CCCCCC] hover:text-white">{{ __('ui.members_cancel') }}</button>
                <x-app.components.loading-button
                    type="button"
                    wire-target="saveMember"
                    wire:click="saveMember"
                    class="h-9 px-4 rounded-xl bg-[#F97316] hover:bg-[#ea580c] text-sm font-medium text-white min-w-[5rem]"
                >
                    <x-slot:label>{{ __('ui.members_save') }}</x-slot:label>
                </x-app.components.loading-button>
            </x-slot:footer>
        </x-app-dialog>
    @endif

    {{-- Delete confirmation modal --}}
    @if($deleteConfirmId !== null)
        <x-app-dialog wire:key="modal-delete" :title="__('ui.members_delete')" maxWidth="md">
            <x-slot:close>
                <button type="button" wire:click="cancelDelete" class="h-8 w-8 flex items-center justify-center rounded-lg border-2 border-[#333333] text-[#CCCCCC] hover:text-white">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </x-slot:close>
            <p class="text-[#CCCCCC] text-sm">{{ __('ui.members_confirm_delete') }}</p>
            <x-slot:footer>
                <button type="button" wire:click="cancelDelete"
                    class="h-9 px-4 rounded-xl border-2 border-[#333333] text-sm font-medium text-[#CCCCCC] hover:text-white">{{ __('ui.members_cancel') }}</button>
                <x-app.components.loading-button
                    type="button"
                    wire-target="doDelete"
                    wire:click="doDelete"
                    class="h-9 px-4 rounded-xl bg-red-500 hover:bg-red-600 text-sm font-medium text-white min-w-[5rem]"
                >
                    <x-slot:label>{{ __('ui.members_delete_confirm_btn') }}</x-slot:label>
                </x-app.components.loading-button>
            </x-slot:footer>
        </x-app-dialog>
    @endif
</div>
