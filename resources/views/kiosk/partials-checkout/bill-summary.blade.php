<div class="px-4">
  <div class="max-w-xl mx-auto bg-white rounded-2xl shadow-md border border-gray-100 p-6">
    <h1 class="text-xl font-bold text-gray-800 text-center mb-6">Billing Summary</h1>

    <div class="space-y-3 text-sm">
      <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Guest Details</h2>

      <div class="flex justify-between">
        <span class="text-gray-500">Name</span>
        <span class="font-semibold text-gray-800">{{ $guest->name ?? 'N/A' }}</span>
      </div>

      <div class="flex justify-between">
        <span class="text-gray-500">Contact No.</span>
        <span class="font-semibold text-gray-800">{{ $guest->contact ?? 'N/A' }}</span>
      </div>

      <div class="flex justify-between">
        <span class="text-gray-500">QR Code</span>
        <span class="font-semibold text-gray-800">{{ $guest->qr_code ?? 'N/A' }}</span>
      </div>

      <div class="flex justify-between">
        <span class="text-gray-500">Check-in Time</span>
        <span class="font-semibold text-gray-800">{{ $checkInDetail ? \Carbon\Carbon::parse($checkInDetail->check_in_at)->format('M d, Y g:i A') : 'N/A' }}</span>
      </div>

      <div class="flex justify-between">
        <span class="text-gray-500">Initial Time</span>
        <span class="font-semibold text-gray-800">
          {{
            $checkInDetail
              ? (
                  $checkInDetail->guest->is_long_stay
                    ? ($checkInDetail->hours_stayed * $checkInDetail->guest->number_of_days) . ' hours'
                    : $checkInDetail->hours_stayed . ' hours'
                )
              : 'N/A'
          }}
        </span>
      </div>

      <div class="flex justify-between">
        <span class="text-gray-500">Extension Hours</span>
        <span class="font-semibold text-gray-800">{{ $checkInDetail ? $extension_hours . ' hours' : 'N/A' }}</span>
      </div>

      <div class="flex justify-between">
        <span class="text-gray-500">Total Staying Hours</span>
        <span class="font-semibold text-gray-800">
          {{
            $checkInDetail
              ? (
                  $checkInDetail->guest->is_long_stay
                    ? (($checkInDetail->hours_stayed * $checkInDetail->guest->number_of_days) + $extension_hours) . ' hours'
                    : ($checkInDetail->hours_stayed + $extension_hours) . ' hours'
                )
              : 'N/A'
          }}
        </span>
      </div>
    </div>

    <div class="border-t border-gray-200 my-5"></div>

    <div class="space-y-3 text-sm">
      <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Charges</h2>

      <div class="flex justify-between">
        <span class="text-gray-500">Room Amount</span>
        <span class="font-semibold text-gray-800">&#8369;{{ $checkInDetail ? number_format($room_amount, 2) : '0.00' }}</span>
      </div>

      <div class="flex justify-between">
        <span class="text-gray-500">Other Transactions</span>
        <span class="font-semibold text-gray-800">&#8369;{{ $checkInDetail ? number_format($total_amount, 2) : '0.00' }}</span>
      </div>

      <div class="flex justify-between">
        <span class="text-gray-500">Deposit</span>
        <span class="font-semibold text-gray-800">&#8369;{{ $checkInDetail ? number_format($total_deposit, 2) : '0.00' }}</span>
      </div>

      <div class="bg-blue-50 rounded-xl p-3 mt-3 flex justify-between items-center">
        <span class="text-lg font-bold text-gray-700">Total</span>
        <span class="text-2xl font-bold text-[#009EF5]">&#8369;{{ $checkInDetail ? number_format($room_amount + $total_amount, 2) : '0.00' }}</span>
      </div>
    </div>

    <div class="mt-6 space-y-3">
      <button
        x-on:confirm="{
          title: 'Confirm Check-Out',
          description: 'Are you sure you want to check-out? Please make sure all the details are correct before confirming.',
          icon: 'warning',
          method: 'confirmCheckOut',
        }"
        class="w-full py-3 rounded-xl font-semibold text-white bg-[#009EF5] hover:bg-[#0080cc] shadow-lg shadow-[#009EF5]/25 transition-all duration-200 text-lg">
        CONFIRM CHECK-OUT
      </button>

      <button wire:click="backRoom"
        class="w-full py-3 rounded-xl font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition-all duration-200">
        Back
      </button>
    </div>
  </div>
</div>
