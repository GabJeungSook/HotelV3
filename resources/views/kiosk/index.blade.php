<x-kiosk-layout-update>
  <div class="flex flex-col items-center justify-center min-h-[calc(100vh-120px)] px-6">
    {{-- Logo --}}
    <div class="mb-10">
      <img src="{{ asset('images/homiLogo.png') }}" alt="HOMI" class="h-20 mx-auto">
    </div>

    {{-- Welcome --}}
    <div class="text-center mb-14">
      <h1 class="text-5xl md:text-6xl font-bold text-gray-800">Welcome to HOMI</h1>
      <p class="mt-4 text-xl text-gray-400">Tap an option to get started</p>
    </div>

    {{-- Cards --}}
    <div class="flex flex-col md:flex-row items-center gap-8 w-full max-w-4xl justify-center">
      <a href="{{ route('kiosk.check-in') }}"
        class="group w-full bg-white rounded-3xl shadow-lg border border-gray-100 hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 p-10 flex flex-col items-center justify-center h-72 md:h-80">
        <div class="w-24 h-24 rounded-2xl bg-blue-50 flex items-center justify-center mb-6 group-hover:bg-[#009EF5]/10 transition-colors duration-300">
          <svg class="w-12 h-12 text-[#009EF5]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
          </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-800">CHECK-IN</h2>
        <p class="mt-2 text-lg text-gray-400">Start your stay with us</p>
      </a>

      <a href="{{ route('kiosk.check-out') }}"
        class="group w-full bg-white rounded-3xl shadow-lg border border-gray-100 hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 p-10 flex flex-col items-center justify-center h-72 md:h-80">
        <div class="w-24 h-24 rounded-2xl bg-blue-50 flex items-center justify-center mb-6 group-hover:bg-[#009EF5]/10 transition-colors duration-300">
          <svg class="w-12 h-12 text-[#009EF5]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0110.5 3h6a2.25 2.25 0 012.25 2.25v13.5A2.25 2.25 0 0116.5 21h-6a2.25 2.25 0 01-2.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
          </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-800">CHECK-OUT</h2>
        <p class="mt-2 text-lg text-gray-400">Complete your checkout</p>
      </a>
    </div>
  </div>
</x-kiosk-layout-update>
