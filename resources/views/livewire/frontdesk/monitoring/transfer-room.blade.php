<div class="p-6">
    <h2 class="text-xl font-semibold text-gray-800 mb-6">Guest Information</h2>

    <div class="grid grid-cols-4 md:grid-cols-4 gap-6">
        {{-- Left: Guest Details --}}
        <div class="space-y-4 col-span-1text-sm text-gray-700">
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
        </div>

        {{-- Right: Billing Details --}}
        <div class="border rounded-md  col-span-3 bg-gray-50 p-4 shadow-sm text-sm text-gray-700">

            {{-- <h3 class="text-lg font-semibold text-gray-700 mb-4">Billing Statement</h3> --}}
            <div class=" w-full text-lg mb-2">
                <div class="flex space-x-5 text-lg my-2 w-full">
                    <div class="w-full">
                        <x-native-select label="Type" wire:model="selected_type_id">
                            <option selected hidden>Select One</option>
                            @forelse ($types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @empty
                            <option>No Type Available</option>
                            @endforelse
                        </x-native-select>
                    </div>
                    <div class="w-full">
                        <x-native-select label="Floor" wire:model="selected_floor_id">
                            <option selected hidden>Select One</option>
                            @forelse ($floors as $floor)
                                <option value="{{ $floor->id }}">{{ $floor->numberWithFormat() }}</option>
                            @empty
                                <option>No Floor Available</option>
                            @endforelse
                        </x-native-select>
                    </div>
                </div>
                <div class="flex space-x-5 text-lg my-2 w-full">
                    <div class="w-full">
                @if (!$this->enabled)
                <x-native-select disabled label="Room">
                    <option selected>No Room Available</option>
                  </x-native-select>
                @else
                <x-native-select label="Room" wire:model="selected_room_id">
                    <option selected hidden>Select One</option>
                    @forelse ($rooms as $room)
                      <option value="{{ $room->id }}">{{ $room->numberWithFormat() }}</option>
                    @empty
                      <option>No Room Available</option>
                    @endforelse
                  </x-native-select>
                @endif
                    </div>
                    <div class="w-full">
                        @if(!$this->enabled)
                         <x-native-select disabled label="Status">
                            <option selected>No Room Available</option>
                         </x-native-select>
                        @else
                        <x-native-select label="Status" wire:model="selected_status">
                            <option selected hidden>Select One</option>
                            <option value="Uncleaned">Uncleaned</option>
                            <option value="Cleaned">Cleaned</option>
                        </x-native-select>
                        @endif
                    </div>
                </div>
                <div class="w-full">
                        @if(!$this->enabled)
                         <x-native-select disabled label="Reason">
                            <option selected>No Room Available</option>
                         </x-native-select>
                        @else
                        <x-native-select label="Reason" wire:model="selected_reason">
                            <option selected hidden>Select One</option>
                        @forelse ($reasons as $reason)
                        <option value="{{ $reason->id }}">{{ $reason->reason }}</option>
                        @empty
                        <option>No Reason Available</option>
                        @endforelse
                        </x-native-select>
                        @endif
                    </div>
                @if($guest->transferTransactions()->count() == 2)
                <div class="bg-red-100 text-red-800 p-2 rounded-md mt-4 flex justify-between items-center">
                    <p class="text-sm font-medium">Note: The guest have already transferred rooms twice and cannot be transferred further.</p>
                    <button class="text-sm font-medium text-red-100 hover:text-red-200 hover:bg-red-800 bg-red-600 px-3 rounded-sm" wire:click="overrideTransfer">Override</button>
                </div>
                @elseif($guest->extendTransactions()->count() > 0)
                <div class="bg-red-100 text-red-800 p-2 rounded-md mt-4 flex justify-between items-center">
                    <p class="text-sm font-medium">Note: The guest have already extended their stay and cannot be transferred.</p>
                    <button class="text-sm font-medium text-red-100 hover:text-red-200 hover:bg-red-800 bg-red-600 px-3 rounded-sm" wire:click="overrideTransfer">Override</button>
                </div>
                @elseif($guest->is_long_stay)
                  <div class="bg-blue-100 text-blue-800 p-2 rounded-md mt-4 flex justify-between items-center">
                    <p class="text-sm font-medium">Note: This is a long stay transaction. Calculation of rates are multiplied by the number of days stayed. ({{ $guest->number_of_days }} days)</p>
                </div>
                @endif
                <div class="flex justify-between text-xl my-2 mt-5">
                    <span class="text-gray-600">Current Room Rate:</span>
                <span class="text-gray-800 font-medium">₱ {{ number_format($current_room_rate ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between text-xl my-2">
                    <span class="text-gray-600">New Room Rate:</span>
                <span class="text-gray-800 font-medium">₱ {{ number_format($new_room_rate  ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between text-xl my-2">
                    <span class="text-gray-600">Excess Amount:</span>
                <span class="text-gray-800 font-medium">₱ {{ number_format($excess_amount ?? 0, 2) }}</span>
                </div>
            </div>

           <hr class="my-2 border-dashed border-gray-600">

           <div class="flex justify-between text-4xl font-semibold text-gray-800 mt-8 mb-4">
                <span>Payable Amount:</span>
                <span>₱ {{ number_format($payable_amount, 2) }}</span>
            </div>
            <!-- amount paid -->
            <!-- @if($payable_amount > 0)
               <div class="mt-20 flex items-center justify-between gap-6">
    <label class="font-semibold text-xl whitespace-nowrap">
        Amount Paid
    </label>

    <div class="relative w-1/2">
        <span class="absolute left-0 top-1/2 -translate-y-1/2 text-gray-700 font-semibold text-3xl">
            ₱
        </span>
<input
    wire:model.defer="amountPaid"
    type="number"
    autofocus
    min="0"
    placeholder="0.00"
    class="
        w-full
        text-right
        text-3xl
        font-semibold
        bg-transparent
        border-0
        border-b-2
        border-gray-400
        focus:border-blue-600
        focus:outline-none
        focus:ring-0
        focus:shadow-none
        appearance-none

        pl-8
        pb-1
    "
/>
    </div>
</div>
@endif -->
<!-- amount paid end -->
        </div>
    </div>

    <div class="flex justify-end mt-6 space-x-2">
        <div class="flex justify-between items-center w-full">
            <div>
                {{-- @if ($rate->has_discount)
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model="has_discount" class="form-checkbox rounded text-[#1877F2] focus:ring-[#1877F2] border-gray-300" />
                    <span class="ml-2 text-sm text-gray-700">Grant Discount</span>
                </label>
                @endif --}}

            </div>
            <div class="flex space-x-2">
                <button wire:click="cancelTransfer" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-50">
                    Cancel
                </button>
                  {{-- <button wire:click="savePayExtend" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-[#1877F2] focus:ring-opacity-50">
                    Save & Pay
                </button> --}}
                {{-- <button x-on:confirm="{
                        title: 'Confirm Save',
                        description: 'Are you sure you want to transfer guest?',
                        icon: 'warning',
                        method: 'saveTransfer'
                    }" class="px-4 py-2 bg-[#1877F2] text-white rounded-md hover:bg-[#5194ec] focus:outline-none focus:ring-2 focus:ring-[#1877F2] focus:ring-opacity-50">
                    Save
                </button> --}}
                @if($guest->transferTransactions()->count() < 2 && $guest->extendTransactions()->count() == 0)
                <button wire:click="confirmTransfer" class="px-4 py-2 bg-[#1877F2] text-white rounded-md hover:bg-[#5194ec] focus:outline-none focus:ring-2 focus:ring-[#1877F2] focus:ring-opacity-50">
                    Save & Pay
                </button>
                @endif
            </div>
        </div>
    </div>

    {{-- modal for submission --}}
    <x-modal wire:model.defer="save_pay_modal" align="center">
      <x-card>
        <div>
          <div class="header flex space-x-1 border-b items-end justify-between py-0.5">
            <h2 class="text-lg uppercase text-gray-600 font-bold">Confirmation</h2>
          </div>
          <div class="mt-3">
            <div class="space-y-4">
              <dl class="mt-8 p-2 divide-y divide-gray-400 text-sm lg:col-span-5 lg:mt-0">
                <div class="flex justify-between py-2">
                    <div class="w-full divide-y divide-gray-400 ">
                        <div class="flex justify-between items-center py-2">
                            <dt class="text-gray-600 text-2xl font-bold">Excess Amount:</dt>
                            <dd class="text-gray-800 text-2xl font-bold">₱ {{ number_format($excess_amount, 2) }}</dd>
                        </div>
                    </div>
                </div>
                 {{-- add checkbox for save excess --}}
                    <div class="flex items-center">
                        <label class="inline-flex flex-col items-start pt-5">
                            <span class="flex items-center">
                                <input type="checkbox" wire:model="save_excess" class="form-checkbox rounded text-[#1877F2] focus:ring-[#1877F2] border-gray-300" />
                                <span class="ml-2 text-lg text-gray-700">Save excess amount</span>
                            </span>
                            <span class="text-xs text-gray-600 mt-1 ml-6">Save this amount as a deposit to the guest's account.</span>
                        </label>
                    </div>
              </dl>
            </div>
          </div>
        </div>

        <x-slot name="footer">
          <div class="flex justify-end s gap-x-2">
              <x-button red label="Close" x-on:click="close" />
              <x-button emerald label="Confirm" wire:click="saveTransfer" />
          </div>
        </x-slot>
      </x-card>
    </x-modal>

    {{-- moadal for authorization code --}}
      <x-modal wire:model.defer="authorization_modal" align="center" max-width="md">
    <x-card>
      <div class="flex space-x-1">
        <h1 class=" text-xl font-bold text-gray-600">AUTHORIZATION CODE</h1>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5 fill-green-600">
          <path fill="none" d="M0 0h24v24H0z" />
          <path d="M17 14h-4.341a6 6 0 1 1 0-4H23v4h-2v4h-4v-4zM7 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4z" />
        </svg>
      </div>
      <div class="mt-7">
        <input type="password" wire:model="code"
          class="w-full text-lg
      @error('code')
          border-red-500
      @enderror
        rounded-lg">
      </div>
      @error('code')
        <span class="text-sm text-red-500 mt-1">{{ $message }}</span>
      @enderror
      <div class="mt-5 flex justify-end items-center space-x-2">
        <x-button x-on:click="close" label="CANCEL" sm negative />
        <x-button label="PROCEED" sm positive wire:click="confirmTransfer" spinner="confirmTransfer" />

      </div>

    </x-card>
  </x-modal>
</div>
