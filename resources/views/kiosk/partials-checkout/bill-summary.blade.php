<div class="pt-10">
  <div class="flex items-end justify-between">
    <div>
      <h1 class="font-bold text-red-600">CHECK-OUT</h1>
      <h1 class="text-3xl uppercase font-extrabold text-gray-600">Bill Summary</h1>
    </div>

    <div>
      <button x-on:click="step--" wire:click="backRoom"
        class="bg-gray-50 outline-blue-500 border border-blue-500 p-2 px-4 flex space-x-1 rounded-full">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
          stroke="currentColor" class="w-6 text-blue-500 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75L3 12m0 0l3.75-3.75M3 12h18" />
        </svg>
        <span class="font-semibold text-blue-500 uppercase">Back</span>
      </button>
    </div>
  </div>

  <div class="mt-5">
    <div
      class="w-full flex flex-col lg:flex-row space-y-5 lg:space-y-0 lg:space-x-5 bg-gray-50 border-2 border-blue-500 bg-opacity-75 rounded-2xl">

      <div class="flex-1 p-5 px-2 md:px-10">
        <div class="bg-white rounded-xl p-4">
          <div class="flex flex-col space-y-3">
            <div
              class="relative flex flex-col justify-between overflow-hidden rounded-lg
              before:absolute before:bottom-[2.5rem] before:-left-2 before:h-5 before:w-5 before:rounded-full before:bg-white
              after:absolute after:bottom-[2.5rem] after:-right-2 after:h-5 after:w-5 after:rounded-full after:bg-white">

              {{-- DETAILS --}}
              <div class="flex flex-col justify-between flex-1">
                <section class="p-3">
                  <h1 class="font-bold text-gray-600 uppercase">Check-in Details</h1>

                  @php
                    // dummy data
                    $name = 'Juan Dela Cruz';
                    $contact = '0917 123 4567';
                    $qr = 'QRCODE-ABC123';
                    $checkInTime = 'Feb 25, 2026 02:15 PM';
                    $initialTime = '2 Hours';
                    $totalExtensionHours = '3 Hours';
                    $totalStayingHours = '5 Hours';

                    $totalAmount = 1500;
                    $totalDeposit = 500;
                  @endphp

                  <div class="mt-4 space-y-3 text-gray-600">
                    <div class="flex justify-between">
                      <dt class="font-medium">Name</dt>
                      <dd class="font-semibold">{{ $guest->name ?? 'N/A' }}</dd>
                    </div>

                    <div class="flex justify-between">
                      <dt class="font-medium">Contact No.</dt>
                      <dd class="font-semibold uppercase">{{ $guest->contact ?? 'N/A' }}</dd>
                    </div>

                    <div class="flex justify-between">
                      <dt class="font-medium">QR Code</dt>
                      <dd class="font-semibold">{{ $guest->qr_code ?? 'N/A' }}</dd>
                    </div>

                    <div class="flex justify-between">
                      <dt class="font-medium">Check In time</dt>
                      <dd class="font-semibold">{{ $checkInDetail ? \Carbon\Carbon::parse($checkInDetail->check_in_at)->format('M d, Y g:i A') : 'N/A' }}</dd>
                    </div>

                    <div class="flex justify-between">
                      <dt class="font-medium">Initial Time</dt>
                      <dd class="font-semibold">{{ $checkInDetail ? $checkInDetail->hours_stayed.' hours' : 'N/A' }}</dd>
                    </div>

                    <div class="flex justify-between">
                      <dt class="font-medium">Total Extension Hours</dt>
                      <dd class="font-semibold">{{ $checkInDetail ? $extension_hours.' hours' : 'N/A' }}</dd>
                    </div>

                    <div class="flex justify-between">
                      <dt class="font-medium">Total Staying Hours</dt>
                      <dd class="font-semibold">{{ $checkInDetail ? ($checkInDetail->hours_stayed + $extension_hours).' hours' : 'N/A' }}</dd>
                    </div>
                  </div>
                </section>

                <section class="border border-gray-400 border-dashed"></section>
              </div>

              {{-- AMOUNTS --}}
              <section class="p-3">
                <div class="space-y-2">
                    <div class="flex justify-between text-gray-700">
                    <dt class="font-bold">Room Amount</dt>
                    <dd class="font-semibold">&#8369;{{ $checkInDetail ? number_format($room_amount, 2) : 0 }}</dd>
                  </div>
                  <div class="flex justify-between text-gray-700">
                    <dt class="font-bold">Other Transactions</dt>
                    <dd class="font-semibold">&#8369;{{ $checkInDetail ? number_format($total_amount, 2) : 0 }}</dd>
                  </div>

                  <div class="flex justify-between text-gray-700">
                    <dt class="font-bold">Total Deposit</dt>
                    <dd class="font-semibold">&#8369;{{ $checkInDetail ? number_format($total_deposit, 2) : 0 }}</dd>
                  </div>

                  <div class="flex justify-between text-green-600 pt-2 border-t border-gray-300">
                    <dt class="font-bold text-xl">Total</dt>
                    <dd class="font-semibold text-lg">
                      &#8369;{{ $checkInDetail ? number_format(($room_amount +  $total_amount) + $total_deposit, 2) : 0}}
                    </dd>
                  </div>
                </div>
              </section>

            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="fixed bottom-20 right-0 left-0">
    <div class="flex justify-center">

        <x-button
          label="Confirm Check-Out"
           x-on:confirm="{
            title: 'Confirm Check-Out',
            description: 'Are you sure you want to check-out? Please make sure all the details are correct before confirming.',
            icon: 'warning',
            method: 'confirmCheckOut',
            }"
          lg
          class="font-medium"
          right-icon="check"
          spinner
          green
        />
    </div>
  </div>
</div>
