<x-kiosk-layout-update>
<div class="min-h-screen flex items-center justify-center px-4">

  <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-10 max-w-md w-full text-center">

    <div class="flex justify-center">
      <svg class="w-24 h-24" viewBox="0 0 52 52">
        <circle class="check-circle" cx="26" cy="26" r="25" fill="none" />
        <path class="check-mark" fill="none" d="M14 27l7 7 16-16" />
      </svg>
    </div>

    <h1 class="mt-6 text-2xl font-bold text-gray-800">Check-out Successful!</h1>

    <p class="mt-3 text-gray-500">
      Please proceed to the front desk to complete the process.
    </p>

    <div class="mt-8">
      <a href="{{ route('kiosk.house-rules') }}"
        class="block w-full py-3 rounded-xl font-semibold text-white bg-[#009EF5] hover:bg-[#0080cc] shadow-lg shadow-[#009EF5]/25 transition-all duration-200">
        Back to Home
      </a>
    </div>

  </div>

</div>

<style>
.check-circle {
  stroke: #009EF5;
  stroke-width: 2;
  stroke-dasharray: 157;
  stroke-dashoffset: 157;
  animation: draw-circle 0.6s ease-out forwards;
}

.check-mark {
  stroke: #009EF5;
  stroke-width: 3;
  stroke-linecap: round;
  stroke-linejoin: round;
  stroke-dasharray: 48;
  stroke-dashoffset: 48;
  animation: draw-check 0.4s 0.6s ease-out forwards;
}

@keyframes draw-circle {
  to { stroke-dashoffset: 0; }
}

@keyframes draw-check {
  to { stroke-dashoffset: 0; }
}
</style>
</x-kiosk-layout-update>
