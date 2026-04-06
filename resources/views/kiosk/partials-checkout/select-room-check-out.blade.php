<div class="px-4">
  <div class="max-w-lg mx-auto bg-white rounded-2xl shadow-md border border-gray-100 p-8 text-center">
    <div class="flex justify-center mb-4">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-20 w-20 text-[#009EF5]">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
      </svg>
    </div>

    <h1 class="text-xl font-bold text-gray-800">Enter Room Number</h1>
    <p class="text-sm text-gray-500 mt-1 mb-6">Input your room number to proceed with check-out</p>

    <input wire:model.live="room_number" type="number" id="room_number"
      class="w-full text-center text-2xl p-4 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#009EF5] focus:border-transparent transition-all duration-200"
      autofocus autocomplete="off" placeholder="Room Number" />

    <div class="flex justify-center gap-3 mt-8">
      <a href="{{ route('kiosk.dashboard') }}"
        class="px-6 py-3 rounded-xl font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition-all duration-200">
        Back
      </a>
      @if ($room_number)
        <button wire:click="findRoom"
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
  const roomInput = document.getElementById('room_number');
  roomInput.addEventListener('blur', () => {
    roomInput.focus();
  });
</script>
