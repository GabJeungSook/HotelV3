<div>
    {{-- Header --}}
    <div class="mb-4">
        <h1 class="text-lg font-bold text-[#009EF5]">{{ $departmentLabel }} — Transactions</h1>
        <p class="text-xs text-gray-400">Add food and beverage orders to guest bills.</p>
    </div>

    {{-- Guest Table --}}
    <div class="overflow-hidden rounded-lg border border-gray-200 shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Guest</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Contact</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Floor</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Room</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($guests as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $item->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $item->contact ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $item->room?->floor?->number ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">Room #{{ $item->room?->number ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="addTransaction({{ $item->id }})"
                                class="inline-flex items-center gap-1 rounded-md bg-[#009EF5] px-3 py-1.5 text-xs font-medium text-white hover:bg-[#0080cc] transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Add Order
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center">
                            <div class="text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <p class="text-sm">No checked-in guests at the moment.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Food Order Modal --}}
    <x-modal wire:model="food_beverages_modal" align="center">
        <x-card title="Food & Beverages" subtitle="Add items to guest's bill.">
            <div class="space-y-4">
                <x-native-select label="Select Item" wire:model.live="food_id">
                    <option value="">Select Item</option>
                    @foreach($foods as $food)
                        <option value="{{ $food->id }}">{{ $food->name }} — ₱{{ number_format($food->price, 2) }}</option>
                    @endforeach
                </x-native-select>

                <div class="grid grid-cols-2 gap-3">
                    <x-input label="Unit Price" disabled wire:model="food_price" prefix="₱" />
                    <x-input label="Quantity" type="number" min="1" wire:model.live="food_quantity" placeholder="1" />
                </div>

                {{-- Totals --}}
                <div class="rounded-lg bg-gray-50 border p-3 space-y-2">
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Subtotal</span>
                        <span>₱ {{ number_format($food_price * max($food_quantity ?? 1, 1), 2) }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                        <span class="font-semibold text-gray-800">Total</span>
                        <span class="font-bold text-lg text-[#009EF5]">₱ {{ number_format($food_total_amount ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end gap-2">
                    <x-button flat label="Cancel" wire:click="closeModal" />
                    <x-button class="bg-[#009EF5] text-white hover:bg-[#0080cc]" label="Add to Bill" wire:click="addFood" icon="plus" />
                </div>
            </x-slot>
        </x-card>
    </x-modal>
</div>
