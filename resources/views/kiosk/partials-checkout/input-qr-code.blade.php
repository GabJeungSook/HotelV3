<div class="px-4">
  <div class="max-w-lg mx-auto bg-white rounded-2xl shadow-md border border-gray-100 p-8 text-center">
    <div class="flex justify-center mb-4">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-20 w-20 text-[#009EF5]">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
      </svg>
    </div>

    <h1 class="text-xl font-bold text-gray-800">Scan QR Code</h1>
    <p class="text-sm text-gray-500 mt-1 mb-6">Scan or enter your transaction code below</p>

    <input wire:model="qr_code" type="text" id="qr_code"
      class="w-full text-center text-2xl p-4 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#009EF5] focus:border-transparent transition-all duration-200"
      autofocus autocomplete="off" placeholder="QR Code" />

    <div class="flex justify-center gap-3 mt-8">
      <button wire:click="backRoom"
        class="px-6 py-3 rounded-xl font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition-all duration-200">
        Back
      </button>
      @if ($qr_code)
        <button wire:click="validateQR"
          class="px-8 py-3 rounded-xl font-semibold text-white bg-[#009EF5] hover:bg-[#0080cc] shadow-lg shadow-[#009EF5]/25 transition-all duration-200 flex items-center gap-2">
          NEXT
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
          </svg>
        </button>
      @endif
    </div>
  </div>
</div>

<script>
  const qrInput = document.getElementById('qr_code');
  qrInput.addEventListener('blur', () => {
    qrInput.focus();
  });
</script>
