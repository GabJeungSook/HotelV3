<div class="p-6">
    <h2 class="text-xl font-semibold text-gray-800 mb-6">Guest Information</h2>

    <div class="grid grid-cols-4 md:grid-cols-4 gap-6">
        {{-- Left: Guest Details --}}
        <div class="space-y-4 col-span-1 text-sm text-gray-700">
            <div>
                <label class="block font-medium mb-1 mt-10">QR Code</label>
               <div class="p-2 bg-gray-100 rounded-md">{{$guest->qr_code}}</div>
            </div>
            <div>
                <label class="block font-medium mb-1">Name</label>
                <div class="p-2 bg-gray-100 rounded-md">{{$guest->name}}</div>
            </div>
            <div>
                <label class="block font-medium mb-1">Contact Number</label>
                <div class="p-2 bg-gray-100 rounded-md">{{ $guest->contact == 'N/A' ? 'N/A' : '09' . $guest->contact }}</div>
            </div>
            <div>
                <label class="block font-medium mb-1">Room Number</label>
                <div class="p-2 bg-gray-100 rounded-md">{{ $room->number }}</div>
            </div>
            {{-- @if ($guest->is_long_stay)
            <div>
                <label class="block font-medium mb-1">Days</label>
                <div class="p-2 bg-gray-100 rounded-md">{{ $guest->number_of_days }}</div>
            </div>
            @else
            <div>
                <label class="block font-medium mb-1">Staying Hours</label>
                <div class="p-2 bg-gray-100 rounded-md">{{ $stayingHour->number }}</div>
            </div>
            @endif --}}
        </div>

        {{-- Right: Billing Details --}}
        <div class="border rounded-md col-span-3 bg-gray-50 p-4 shadow-sm text-sm text-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Billing Statement</h3>
            <div class=" w-full text-lg mb-2">

                <x-native-select label="Select Extension Rate (Hour)" wire:model.live="extension_rate_id">
                    <option selected hidden>Select One</option>

                    @forelse ($extension_rates as $rate)
                      <option value="{{ $rate->id }}">{{ $rate->hour }}</option>
                    @empty
                      <option>No Rate Available</option>
                    @endforelse
                </x-native-select>
                {{-- <hr class="my-2">
                <div class="flex justify-between text-lg my-2">
                    <span class="text-gray-600">Extension Time Reset (hours):</span>
                <span class="text-gray-800 font-medium">{{ $extension_time_reset }}</span>
                </div> --}}
                <div class="flex justify-between text-lg my-2">
                    <span class="text-gray-600">Initial Staying Hour:</span>
                <span class="text-gray-800 font-medium">{{ $stayingHour->number }}</span>
                </div>
                {{-- <div class="flex justify-between text-lg my-2">
                    <span class="text-gray-600">Total Extended Hours:</span>
                <span class="text-gray-800 font-medium">{{ $total_extended_hours }}</span>
                </div> --}}
                <div class="flex justify-between text-lg my-2">
                    <span class="text-gray-600">Extension Rate (hours):</span>
                <span class="text-gray-800 font-medium">{{ $extended_rate->hour ?? 'N/A' }}</span>
                </div>
                 <hr class="my-2">
                <div class="flex justify-between text-lg my-2">
                    <span class="text-gray-600">
                     Total Extension Hours (<span class="text-red-600">resets every {{ $extension_time_reset }} hours</span>) :
                    </span>
                <span class="text-gray-800 font-medium">{{ $current_time_alloted }}</span>
                </div>
                {{-- <span class="text-gray-800 font-medium">₱ {{ number_format($guest->static_amount, 2) }}</span> --}}
            </div>

             {{-- @if($has_discount)
            <div class="flex justify-between text-lg mb-2">
                <span class="text-red-600">Discount: (Senior & PWD)</span>
                <span class="text-red-600 font-medium">– ₱ {{ number_format($discount_amount, 2) }}</span>
            </div>
            @endif --}}
            <hr class="my-2 border-dashed border-gray-600">
            <div class="flex justify-between text-xl mb-2">
                <span class="text-gray-600">Room Rate:</span>
                <span class="text-gray-800 font-medium">₱ {{ number_format($initial_amount, 2) }}</span>
            </div>
            <div class="flex justify-between text-xl mb-2">
                <span class="text-gray-600">Extension Rate:</span>
                <span class="text-gray-800 font-medium">₱ {{ number_format($extended_amount, 2) }}</span>
            </div>

            <div class="flex justify-between text-4xl font-semibold text-gray-800 mt-8 mb-4">
                <span>Total:</span>
                <span>₱ {{ number_format($total_amount, 2) }}</span>
            </div>
{{--
            <div class="mt-20">
                <label class="block font-medium mb-1">Amount Paid</label>
                <x-input wire:model="amountPaid" type="number" placeholder="0.00" class="text-right px-2 py-2" prefix="₱" />
            </div> --}}
        </div>
    </div>

    <div class="flex justify-end mt-6 space-x-2">
        <x-button flat label="Cancel" wire:click="cancelExtend" />
        <x-button blue label="Save" wire:click="confirmSave('save')" />
        <x-button positive label="Save & Pay" wire:click="openPayModal" />
        @if($available_deposit >= $total_amount && $total_amount > 0)
        <x-button warning label="Pay with Deposit" wire:click="confirmSave('pay_deposit')" />
        @endif
    </div>

    {{-- Pay Modal --}}
    <x-modal wire:model="pay_modal" align="center">
        <x-card>
            <div>
                <div class="header flex space-x-1 border-b items-end justify-between py-0.5">
                    <h2 class="text-lg uppercase text-gray-600 font-bold">Payment</h2>
                </div>
                <div class="mt-3 space-y-4">
                    <div class="flex justify-between items-center py-2">
                        <dt class="text-gray-600 text-lg font-semibold">Payable Amount:</dt>
                        <dd class="text-gray-800 text-2xl font-bold">₱ {{ number_format($total_amount, 2) }}</dd>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount Paid</label>
                        <input wire:model.live="amount_paid" type="number" min="0" step="0.01" placeholder="0.00"
                            class="w-full rounded-md border-gray-300 text-2xl font-semibold text-right focus:border-blue-500 focus:ring-blue-500" />
                        @error('amount_paid') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <x-slot name="footer">
                <div class="flex justify-end gap-x-2">
                    <x-button flat label="Cancel" x-on:click="close" />
                    <x-button positive label="Confirm Payment" wire:click="confirmSave('save_pay')" />
                </div>
            </x-slot>
        </x-card>
    </x-modal>

    <x-modal wire:model="change_modal" align="center">
        <x-card>
            <div>
                <div class="header flex space-x-1 border-b items-end justify-between py-0.5">
                    <h2 class="text-lg uppercase text-gray-600 font-bold">Confirmation</h2>
                </div>
                <div class="mt-3">
                    <div class="space-y-4">
                        <dl class="mt-8 p-2 divide-y divide-gray-400 text-sm lg:col-span-5 lg:mt-0">
                            <div class="flex justify-between py-2">
                                <div class="w-full divide-y divide-gray-400">
                                    <div class="flex justify-between items-center py-2">
                                        <dt class="text-gray-600 text-2xl font-bold">Excess Amount:</dt>
                                        <dd class="text-gray-800 text-2xl font-bold">₱ {{ number_format($excess_amount, 2) }}</dd>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <label class="inline-flex flex-col items-start pt-5">
                                    <span class="flex items-center">
                                        <input type="checkbox" wire:model="save_excess" class="form-checkbox rounded text-[#1877F2] focus:ring-[#1877F2] border-gray-300" />
                                        <span class="ml-2 text-lg text-gray-700">Save as Guest Deposit</span>
                                    </span>
                                    <span class="text-xs text-gray-600 mt-1 ml-6">Store the excess amount as a guest deposit (refundable on checkout).</span>
                                </label>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
            <x-slot name="footer">
                <div class="flex justify-end gap-x-2">
                    <x-button red label="Close" x-on:click="close" />
                    <x-button emerald label="Confirm" wire:click="saveExtend" />
                </div>
            </x-slot>
        </x-card>
    </x-modal>

    {{-- Pay with Deposit Confirmation Modal --}}
    <x-modal wire:model="deposit_pay_modal" align="center">
        <x-card>
            <div>
                <div class="header flex space-x-1 border-b items-end justify-between py-0.5">
                    <h2 class="text-lg uppercase text-gray-600 font-bold">Pay with Deposit</h2>
                </div>
                <div class="mt-3">
                    <div class="space-y-4">
                        <dl class="mt-4 p-2 divide-y divide-gray-400 text-sm lg:col-span-5 lg:mt-0">
                            <div class="flex justify-between items-center py-3">
                                <dt class="text-gray-600 text-lg font-semibold">Available Guest Deposit:</dt>
                                <dd class="text-green-600 text-2xl font-bold">₱ {{ number_format($available_deposit, 2) }}</dd>
                            </div>
                            <div class="flex justify-between items-center py-3">
                                <dt class="text-gray-600 text-lg font-semibold">Amount to Deduct:</dt>
                                <dd class="text-red-600 text-2xl font-bold">₱ {{ number_format($total_amount, 2) }}</dd>
                            </div>
                            <div class="flex justify-between items-center py-3">
                                <dt class="text-gray-600 text-lg font-semibold">Balance:</dt>
                                <dd class="text-gray-800 text-2xl font-bold">₱ {{ number_format($available_deposit - $total_amount, 2) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
            <x-slot name="footer">
                <div class="flex justify-end gap-x-2">
                    <x-button red label="Cancel" x-on:click="close" />
                    <x-button emerald label="Confirm Payment" wire:click="confirmDepositPay" />
                </div>
            </x-slot>
        </x-card>
    </x-modal>
</div>
