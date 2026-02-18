<div class="min-h-screen bg-[#000000] text-white">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-6 mb-8">
            <div class="space-y-4">
                <div class="flex items-center justify-between gap-4">
                    <label class="text-sm font-medium text-[#CCCCCC]">{{ __('ui.contactCards') }}</label>
                    <x-app.components.content-view-on-site-button :content-locale="$contentLocale" hash="#contact" class="!h-8" />
                </div>
                <div class="space-y-3">
                    @foreach($contactCards as $index => $card)
                        @php
                            $iconKey = $card['icon'] ?? config('icons.default', 'phone');
                            if (!isset($iconsConfig[$iconKey]) || $iconKey === 'default') {
                                $iconKey = config('icons.default', 'phone');
                            }
                        @endphp
                        <div class="bg-[#111111] border-2 border-[#333333] rounded-xl" wire:key="contact-card-{{ $card['id'] }}">
                            <div class="p-3">
                                @if($editingContactCardId === $card['id'])
                                    <div class="flex gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-[#333333] flex items-center justify-center shrink-0 text-[#F97316]">
                                            <x-dynamic-component :component="'lucide-' . $iconKey" class="w-4 h-4" />
                                        </div>
                                        <div class="flex-1 space-y-2">
                                            <h5 class="text-sm font-semibold text-white">{{ $this->getTypeLabel($card['type']) }}</h5>
                                            <input
                                                type="text"
                                                wire:model="contactCards.{{ $index }}.description"
                                                class="w-full h-9 px-3 bg-[#111111] border-2 border-[#333333] rounded-xl text-sm text-white placeholder-[#666666] focus:border-[#F97316] focus:outline-none"
                                                placeholder="{{ __('ui.contactCardDescription') }}"
                                            />
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <x-app.components.loading-button
                                                wire-target="saveContactCard"
                                                wire:click="saveContactCard('{{ $card['id'] }}')"
                                                class="h-8 w-8 rounded-lg text-[#666666] hover:text-green-500 hover:bg-[#111111] transition-colors"
                                                title="{{ __('ui.content_roadmap_save') }}"
                                            >
                                                <x-lucide-save class="w-4 h-4" />
                                            </x-app.components.loading-button>
                                            <button
                                                type="button"
                                                wire:click="cancelEditContactCard"
                                                class="h-8 w-8 flex items-center justify-center rounded-lg text-[#666666] hover:text-[#F97316] hover:bg-[#111111] transition-colors"
                                                title="{{ __('ui.admins_cancel') }}"
                                            >
                                                <x-lucide-x class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-[#333333] flex items-center justify-center shrink-0 text-[#F97316]">
                                            <x-dynamic-component :component="'lucide-' . $iconKey" class="w-4 h-4" />
                                        </div>
                                        <div class="flex-1 space-y-1 min-w-0">
                                            <h5 class="text-sm font-semibold text-white">{{ $this->getTypeLabel($card['type']) }}</h5>
                                            <p class="text-sm text-[#999999]">{{ $card['description'] ?: '—' }}</p>
                                        </div>
                                        <button
                                            type="button"
                                            wire:click="editContactCard('{{ $card['id'] }}')"
                                            class="h-8 w-8 flex items-center justify-center rounded-lg text-[#666666] hover:text-[#F97316] hover:bg-[#111111] transition-colors flex-shrink-0"
                                        >
                                            <x-lucide-pencil class="w-4 h-4" />
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
