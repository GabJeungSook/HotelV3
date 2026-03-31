<div>
  <h1 class="text-xl font-bold text-gray-800 mb-6">Confirm Details</h1>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl border border-gray-100 shadow-md p-6">
      <h2 class="font-bold text-gray-700 text-lg mb-4">Guest Information</h2>
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Complete Name</label>
          <input type="text" wire:model="name"
            class="w-full rounded-xl border-gray-200 px-4 py-3 text-gray-700 focus:border-[#009EF5] focus:ring-[#009EF5] transition-colors"
            placeholder="Enter guest name">
          @error('name')
            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
          @enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Contact Number (Optional)</label>
          <div class="flex rounded-xl shadow-sm">
            <span class="inline-flex items-center rounded-l-xl border border-r-0 border-gray-200 bg-gray-50 px-4 text-gray-500 text-sm font-medium">09</span>
            <input type="number" wire:model="contact"
              class="block w-full rounded-none rounded-r-xl border-gray-200 px-4 py-3 text-gray-700 focus:border-[#009EF5] focus:ring-[#009EF5] transition-colors"
              placeholder="XXXXXXXXX">
          </div>
          @error('contact')
            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
          @enderror
        </div>

        @if($discount_available)
          <label class="flex items-center justify-between cursor-pointer select-none mt-4 p-3 rounded-xl bg-gray-50">
            <span class="text-sm font-semibold text-gray-700">Senior Citizen & PWD Discount</span>
            <div class="relative">
              <input type="checkbox" wire:model="discountEnabled" wire:change="applyDiscount" class="sr-only peer">
              <div class="w-12 h-7 bg-gray-300 rounded-full peer-checked:bg-[#009EF5] transition-colors duration-300"></div>
              <div class="absolute top-0.5 left-0.5 w-6 h-6 bg-white rounded-full shadow-md transition-transform duration-300 peer-checked:translate-x-5"></div>
            </div>
          </label>
        @endif
      </div>
    </div>

    <div class="bg-blue-50 rounded-xl border border-blue-100 shadow-md p-6">
      <h2 class="font-bold text-gray-700 text-lg mb-4">Booking Summary</h2>
      <div class="space-y-4">
        <div class="flex justify-between items-start">
          <div>
            <p class="font-bold text-gray-800 uppercase">{{ $room_type }}</p>
            <p class="text-sm text-gray-500">RM #{{ $room_number }} | {{ $room_floor }}</p>
            @if ($longstay != null)
              <p class="text-sm text-gray-500">{{ $longstay }} Day{{ $longstay > 1 ? 's' : '' }}</p>
            @else
              <p class="text-sm text-gray-500">{{ $room_rate }} Hour{{ $room_rate > 1 ? 's' : '' }}</p>
            @endif
          </div>
          <span class="font-bold text-gray-800 text-lg">&#8369;{{ number_format($room_pay, 2) }}</span>
        </div>

        <div class="flex justify-between items-start">
          <div>
            <p class="font-bold text-gray-800">Check-in Deposit</p>
            <p class="text-sm text-gray-500">Room key & TV Remote</p>
          </div>
          <span class="font-bold text-gray-800 text-lg">&#8369;200.00</span>
        </div>

        <div class="flex justify-between items-start">
          <div>
            <p class="font-bold text-gray-800">Discount</p>
            <p class="text-sm text-gray-500">Senior Citizen & PWD</p>
          </div>
          <span class="font-bold text-red-500 text-lg">-&#8369;{{ number_format($discount_amount, 2) }}</span>
        </div>

        <div class="border-t border-blue-200 pt-4 flex justify-between items-center">
          <span class="font-bold text-gray-700 text-lg">Total Charge</span>
          <span class="text-2xl font-bold text-[#009EF5]">&#8369;{{ number_format(($room_pay + 200) - $discount_amount, 2) }}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="flex items-center justify-between mt-8">
    <button x-on:click="step--"
      class="bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl px-8 py-3 font-medium inline-flex items-center gap-2 transition-colors">
      <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
      </svg>
      Back
    </button>
    @if ($name)
      <button wire:click="confirmTransaction" wire:loading.attr="disabled"
        class="bg-[#009EF5] hover:bg-[#0080cc] text-white rounded-xl px-8 py-3 font-bold shadow-lg shadow-[#009EF5]/25 inline-flex items-center gap-2 transition-colors">
        <span wire:loading.remove wire:target="confirmTransaction">Confirm</span>
        <span wire:loading wire:target="confirmTransaction">Processing...</span>
        <svg wire:loading.remove wire:target="confirmTransaction" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
        </svg>
      </button>
    @endif
  </div>
</div>
