<div>
  <h1 class="text-xl font-bold text-gray-800 mb-6">Select Rate</h1>

  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
    @foreach ($rates as $rate)
      <button wire:key="{{ $rate->id }}rate" wire:click="selectRate({{ $rate->id }})" type="button"
        class="rounded-xl border p-5 text-center transition-all duration-200
          {{ $rate_id == $rate->id
            ? 'bg-[#009EF5] text-white ring-2 ring-[#009EF5] ring-offset-2 border-transparent shadow-md'
            : 'bg-white border-gray-200 text-gray-600 hover:border-[#009EF5]/40 hover:shadow-lg shadow-sm' }}">
        <h2 class="text-2xl font-bold uppercase">{{ $rate->stayingHour->number }} Hours</h2>
        <p class="text-lg font-bold mt-1 {{ $rate_id == $rate->id ? 'text-white' : 'text-[#009EF5]' }}">
          &#8369;{{ number_format($rate->amount, 2) }}
        </p>
      </button>
    @endforeach
  </div>

  <div class="mt-6">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 max-w-sm">
      <h2 class="text-lg font-bold text-gray-800 mb-3">Long Stay</h2>
      <label class="block text-sm text-gray-500 mb-1">Enter number of days:</label>
      <input type="number" step="1" min="1" wire:model="longstay"
        class="w-full rounded-xl border-gray-200 text-2xl font-bold text-gray-700 focus:border-[#009EF5] focus:ring-[#009EF5] transition-colors">
      @error('longstay')
        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
      @enderror
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
    @if ($rate_id != null || $longstay != null)
      <button wire:click="proceedFillUp"
        class="bg-[#009EF5] hover:bg-[#0080cc] text-white rounded-xl px-8 py-3 font-medium shadow-lg shadow-[#009EF5]/25 inline-flex items-center gap-2 transition-colors">
        Next
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
        </svg>
      </button>
    @endif
  </div>
</div>
