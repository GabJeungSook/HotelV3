<div>
    {{-- Branch selector (superadmin) --}}
    @if($branches->isNotEmpty())
        <div class="mb-4 flex justify-end">
            <select wire:model.live="branchId" class="rounded-lg border-gray-200 text-sm focus:ring-[#009EF5] focus:border-[#009EF5]">
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
    @endif

    {{-- Global settings card --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 mb-5">
        <h3 class="text-sm font-bold text-gray-800 mb-3">Branch Discount Settings</h3>
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Discount Amount</label>
                <div class="flex items-center">
                    <span class="inline-flex items-center rounded-l-lg border border-r-0 border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-500">₱</span>
                    <input type="number" wire:model="discountAmount" min="0" step="1"
                        class="w-32 rounded-r-lg border border-gray-200 px-3 py-2 text-sm focus:border-[#009EF5] focus:ring-2 focus:ring-[#009EF5]/20 focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Global Switch</label>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="discountEnabled" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-[#009EF5]/20 rounded-full peer peer-checked:bg-[#009EF5] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                    <span class="ml-2 text-sm font-medium {{ $discountEnabled ? 'text-[#009EF5]' : 'text-gray-400' }}">
                        {{ $discountEnabled ? 'Enabled' : 'Disabled' }}
                    </span>
                </label>
            </div>
            <button wire:click="saveGlobalSettings" class="rounded-lg bg-[#009EF5] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0080cc] transition shadow-sm">
                Save Settings
            </button>
        </div>
    </div>

    {{-- Discount configurations table --}}
    {{ $this->table }}
</div>
