<div class="p-6">
    <h2 class="text-xl font-semibold text-gray-800 mb-6">Guest Information</h2>

    <div class="grid grid-cols-4 md:grid-cols-4 gap-6">
        {{-- Left: Guest Details --}}
        <div class="space-y-4 col-span-1 text-sm text-gray-700">
            <div>
                <label class="block font-medium mb-1 mt-10">QR Code</label>
                <div class="p-2 bg-gray-100 rounded-md">{{ $guest->qr_code }}</div>
            </div>
            <div>
                <label class="block font-medium mb-1">Name</label>
                <div class="p-2 bg-gray-100 rounded-md">{{ $guest->name }}</div>
            </div>
            <div>
                <label class="block font-medium mb-1">Contact Number</label>
                <div class="p-2 bg-gray-100 rounded-md">{{ $guest->contact == 'N/A' ? 'N/A' : '09' . $guest->contact }}</div>
            </div>
            <div>
                <label class="block font-medium mb-1">Room Number</label>
                <div class="p-2 bg-gray-100 rounded-md">{{ $room->number }}</div>
            </div>
        </div>

        {{-- Right: Guest Deposit --}}
        <div class="border rounded-md col-span-3 bg-gray-50 p-4 shadow-sm text-sm text-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Guest Deposit</h3>

            <div class="w-full text-lg mb-2 space-y-4">
                {{-- Balances Display --}}
                <div>
                    <label class="block font-medium mb-1 text-gray-600">Room Key Deposit Balance</label>
                    <div class="p-2 bg-gray-100 rounded-md text-gray-800 font-medium">
                        ₱ {{ number_format($room_deposit_balance, 2) }}
                    </div>
                </div>

                <div>
                    <label class="block font-medium mb-1 text-gray-600">Guest Deposit Balance</label>
                    <div class="p-2 bg-gray-100 rounded-md text-gray-800 font-medium">
                        ₱ {{ number_format($available_balance, 2) }}
                    </div>
                </div>

                <hr class="my-2 border-dashed border-gray-600">

                {{-- Mode Toggle --}}
                <div class="flex space-x-2 mb-4">
                    <button wire:click="$set('mode', 'add')"
                        class="px-4 py-2 rounded-md font-medium focus:outline-none focus:ring-2 focus:ring-opacity-50
                            {{ $mode === 'add' ? 'bg-[#1877F2] text-white focus:ring-[#1877F2]' : 'bg-gray-200 text-gray-700 hover:bg-gray-300 focus:ring-gray-300' }}">
                        Add Deposit
                    </button>
                    <button wire:click="$set('mode', 'deduct')"
                        class="px-4 py-2 rounded-md font-medium focus:outline-none focus:ring-2 focus:ring-opacity-50
                            {{ $mode === 'deduct' ? 'bg-red-500 text-white focus:ring-red-500' : 'bg-gray-200 text-gray-700 hover:bg-gray-300 focus:ring-gray-300' }}">
                        Deduct Deposit
                    </button>
                </div>

                {{-- Add Deposit Form --}}
                @if ($mode === 'add')
                    <div class="space-y-4">
                        <x-input label="Deposit Amount" wire:model="deposit_amount" type="number" min="1" placeholder="0.00" prefix="₱" />
                        <x-input label="Remarks" wire:model="deposit_remarks" placeholder="Enter remarks" />

                        <div class="flex justify-end mt-4">
                            <x-button positive label="Save Deposit" wire:click="saveDeposit" />
                        </div>
                    </div>
                @endif

                {{-- Deduct Deposit Form --}}
                @if ($mode === 'deduct')
                    <div class="space-y-4">
                        <x-input label="Deduction Amount (Max: ₱ {{ number_format($available_balance, 2) }})"
                            wire:model="deduction_amount" type="number" min="1" max="{{ $available_balance }}" placeholder="0.00" prefix="₱" />

                        <div class="flex justify-end mt-4">
                            <x-button negative label="Save Deduction" wire:click="saveDeduction" />
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="flex justify-end mt-6 space-x-2">
        <div class="flex justify-between items-center w-full">
            <div></div>
            <div class="flex space-x-2">
                <button wire:click="cancel" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-50">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
