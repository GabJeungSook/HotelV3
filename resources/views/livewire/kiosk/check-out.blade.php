<div class="max-w-6xl mx-auto px-2">
  {{-- Back --}}
  <div class="mb-5">
    <a href="{{ route('kiosk.dashboard') }}" class="inline-flex items-center gap-2 text-base text-gray-400 hover:text-gray-600 transition">
      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
      Back
    </a>
  </div>

  {{-- Progress --}}
  <div class="flex items-center justify-center gap-2 mb-8">
    @for($i = 1; $i <= 3; $i++)
      <div class="flex items-center gap-2">
        <div class="flex items-center justify-center h-10 w-10 rounded-full text-sm font-bold
          {{ $steps >= $i ? 'bg-[#009EF5] text-white' : 'bg-gray-200 text-gray-400' }}">{{ $i }}</div>
        @if($i < 3)<div class="w-10 h-0.5 {{ $steps > $i ? 'bg-[#009EF5]' : 'bg-gray-200' }}"></div>@endif
      </div>
    @endfor
  </div>

  @if($steps == 1)
    @include('kiosk.partials-checkout.select-room-check-out')
  @elseif($steps == 2)
    @include('kiosk.partials-checkout.input-qr-code')
  @elseif($steps == 3)
    @include('kiosk.partials-checkout.bill-summary')
  @endif
</div>
