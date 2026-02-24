<div class="pt-10 ">
  <div class="flex items-end justify-between">
    <div>
      <h1 class="font-bold text-red-600">CHECK-OUT</h1>
      <h1 class="text-3xl uppercase font-extrabold text-gray-600">Input QR Code</h1>
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
    <div>
    <div>
        {{-- <h1 class="text-6xl text-center font-bold text-gray-700 mt-5">SCAN QR CODE (CHECK-OUT)</h1> --}}
    </div>
    <div class="flex justify-center mt-16">
        <input wire:model="qr_code" type="text" id="qr_code" class="text-center p-4 text-2xl focus:outline-none w-full mx-14 rounded-md" autofocus autocomplete="off" />
    </div>
    <small  class="flex justify-center mt-3 font-medium text-red-600">*Input QR Code Here*</small>
</div>


  </div>
</div>
<div class="fixed bottom-20 right-0 left-0">
  <div class="flex justify-center">
    @if ($qr_code)
      <x-button label="NEXT" wire:click="$set('steps', 3)" lg class="font-medium " right-icon="chevron-double-right"
        spinner green />
    @endif
  </div>

<script>
    const qrInput = document.getElementById('qr_code');
    qrInput.addEventListener('blur', () => {
        qrInput.focus();
    });
</script>
</div>
