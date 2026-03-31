<div class="flex justify-center">
  <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8 max-w-md w-full text-center">
    <div class="flex justify-center mb-4">
      <div class="h-16 w-16 rounded-full bg-[#009EF5]/10 flex items-center justify-center">
        <svg class="w-8 h-8 text-[#009EF5]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
    </div>

    <h1 class="text-xl font-bold text-gray-800 mb-1">Check-in Successful!</h1>
    <p class="text-sm text-gray-500 mb-6">Show this QR code at the front desk</p>

    <div class="flex justify-center mb-4">
      <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $generatedQrCode }}"
        alt="QR Code: {{ $generatedQrCode }}" class="rounded-lg">
    </div>

    <p class="font-mono text-lg font-bold text-gray-700 mb-6">{{ $generatedQrCode }}</p>

    <button wire:click="redirectToHome"
      class="w-full bg-[#009EF5] hover:bg-[#0080cc] text-white rounded-xl px-8 py-3 font-bold shadow-lg shadow-[#009EF5]/25 transition-colors">
      Done
    </button>
  </div>
</div>
