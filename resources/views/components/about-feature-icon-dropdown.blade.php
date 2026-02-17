<div
    id="about-feature-icon-dropdown"
    x-data="{ search: '' }"
    x-show="Alpine.store('aboutFeatureEditing')?.openIconDropdown && Alpine.store('aboutFeatureEditing')?.id != null"
    x-cloak
    x-on:click.outside="closeAboutFeatureIconDropdown(false)"
    x-on:keydown.escape.window="closeAboutFeatureIconDropdown(false)"
    class="absolute z-[100] flex flex-col bg-[#111111] border-2 border-[#333333] rounded-xl shadow-xl mt-1"
    style="display: none; width: 18rem; max-height: min(20rem, 70vh);"
>
    <div class="flex flex-col flex-1 min-h-0 overflow-hidden rounded-xl">
        <input
            type="text"
            id="about-feature-icon-search"
            x-model="search"
            placeholder="{{ __('ui.content_about_icon_search') }}..."
            class="m-2 px-3 py-2 bg-[#000000] border border-[#333333] rounded-lg text-sm text-white placeholder-[#666666] focus:border-[#F97316] focus:outline-none shrink-0"
        />
        <div class="overflow-y-auto p-2 space-y-0.5 flex-1" style="height: 16rem;">
            <div id="about-feature-icon-dropdown-list" class="space-y-0.5" data-loaded="0"></div>
        </div>
    </div>
</div>
