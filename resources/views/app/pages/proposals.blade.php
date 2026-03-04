<div class="app-proposals-page min-h-screen bg-[#000000] text-white">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-4">
            <h3 class="text-lg font-black text-white mb-3">{{ __('ui.proposals_title') }}</h3>

            {{-- Mobile: cards --}}
            @if($this->filteredAndSortedProposals->isNotEmpty())
                <div class="block md:hidden space-y-3">
                    @foreach($this->filteredAndSortedProposals as $proposal)
                        <div class="bg-[#111111] border-2 border-[#333333] rounded-xl p-4">
                            <h4 class="font-bold text-white text-sm mb-2">{{ $proposal->title }}</h4>
                            <p class="text-xs text-[#CCCCCC] whitespace-pre-line line-clamp-3 mb-2">{{ $proposal->message }}</p>
                            <div class="flex items-center gap-2 text-xs text-[#666666]">
                                <x-lucide-calendar class="w-3.5 h-3.5 shrink-0" />
                                <span>{{ $proposal->created_at->translatedFormat('d M Y H:i') }}</span>
                            </div>
                            <details class="mt-3 group">
                                <summary class="text-xs text-[#F97316] cursor-pointer hover:underline">{{ __('ui.proposals_view_full') }}</summary>
                                <p class="mt-2 text-xs text-[#CCCCCC] whitespace-pre-line pt-2 border-t border-[#333333]">{{ $proposal->message }}</p>
                            </details>
                            <div class="mt-3 pt-3 border-t border-[#333333]">
                                <button
                                    type="button"
                                    wire:click="confirmDelete({{ $proposal->id }})"
                                    class="inline-flex items-center gap-1.5 text-xs text-[#CCCCCC] hover:text-red-400 transition-colors"
                                >
                                    <x-lucide-trash-2 class="w-4 h-4" />
                                    {{ __('ui.proposals_delete') }}
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
                                        <button type="button" wire:click="sortByColumn('title')" class="inline-flex items-center gap-1 hover:text-white transition-colors text-left">
                                            {{ __('ui.proposals_title_column') }}
                                            @if($sortBy === 'title')
                                                <x-lucide-chevron-down class="w-3.5 h-3.5 shrink-0 {{ $sortDir === 'asc' ? 'rotate-180' : '' }}" />
                                            @else
                                                <span class="inline-flex flex-col items-center leading-none opacity-60">
                                                    <x-lucide-chevron-down class="w-3 h-3 rotate-180 -mb-0.5" />
                                                    <x-lucide-chevron-down class="w-3 h-3" />
                                                </span>
                                            @endif
                                        </button>
                                    </th>
                                    <th class="text-xs font-medium text-[#CCCCCC] px-4 py-3">{{ __('ui.proposals_message') }}</th>
                                    <th class="text-xs font-medium text-[#CCCCCC] px-4 py-3">
                                        <button type="button" wire:click="sortByColumn('date')" class="inline-flex items-center gap-1 hover:text-white transition-colors text-left">
                                            {{ __('ui.proposals_date') }}
                                            @if($sortBy === 'date')
                                                <x-lucide-chevron-down class="w-3.5 h-3.5 shrink-0 {{ $sortDir === 'asc' ? 'rotate-180' : '' }}" />
                                            @else
                                                <span class="inline-flex flex-col items-center leading-none opacity-60">
                                                    <x-lucide-chevron-down class="w-3 h-3 rotate-180 -mb-0.5" />
                                                    <x-lucide-chevron-down class="w-3 h-3" />
                                                </span>
                                            @endif
                                        </button>
                                    </th>
                                    <th class="text-xs font-medium text-[#CCCCCC] px-4 py-3">{{ __('ui.proposals_actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($this->filteredAndSortedProposals as $proposal)
                                    <tr class="border-b border-[#333333] hover:bg-[#1a1a1a]">
                                        <td class="px-4 py-2 font-medium text-white text-sm max-w-[200px]">
                                            {{ $proposal->title }}
                                        </td>
                                        <td class="px-4 py-2 text-[#CCCCCC] text-sm max-w-md">
                                            <span class="line-clamp-2">{{ $proposal->message }}</span>
                                        </td>
                                        <td class="px-4 py-2 text-[#CCCCCC] text-xs whitespace-nowrap">
                                            {{ $proposal->created_at->translatedFormat('d M Y H:i') }}
                                        </td>
                                        <td class="px-4 py-2">
                                            <button
                                                type="button"
                                                wire:click="confirmDelete({{ $proposal->id }})"
                                                class="inline-flex items-center justify-center h-8 w-8 rounded-lg border-2 border-[#333333] text-[#CCCCCC] hover:border-red-500 hover:text-red-400 transition-colors"
                                                aria-label="{{ __('ui.proposals_delete') }}"
                                            >
                                                <x-lucide-trash-2 class="w-4 h-4" />
                                            </button>
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
                    <p class="text-white font-bold text-sm mb-1">{{ __('ui.proposals_no_results') }}</p>
                    <p class="text-[#CCCCCC] text-xs">{{ __('ui.proposals_no_results_desc') }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Delete confirmation modal --}}
    @if($deleteConfirmId !== null)
        <x-app-dialog wire:key="modal-delete" :title="__('ui.proposals_delete_title')" maxWidth="md">
            <x-slot:close>
                <button type="button" wire:click="cancelDelete" class="h-8 w-8 flex items-center justify-center rounded-lg border-2 border-[#333333] text-[#CCCCCC] hover:text-white">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </x-slot:close>
            <p class="text-[#CCCCCC] text-sm">{{ __('ui.proposals_confirm_delete') }}</p>
            <x-slot:footer>
                <button type="button" wire:click="cancelDelete"
                    class="h-9 px-4 rounded-xl border-2 border-[#333333] text-sm font-medium text-[#CCCCCC] hover:text-white">{{ __('ui.members_cancel') }}</button>
                <x-app.components.loading-button
                    type="button"
                    wire-target="doDelete"
                    wire:click="doDelete"
                    class="h-9 px-4 rounded-xl bg-red-500 hover:bg-red-600 text-sm font-medium text-white min-w-[5rem]"
                >
                    <x-slot:label>{{ __('ui.proposals_delete_confirm_btn') }}</x-slot:label>
                </x-app.components.loading-button>
            </x-slot:footer>
        </x-app-dialog>
    @endif
</div>
