<div>
  <h1 class="text-xl font-bold text-gray-800 mb-6">Select Room Type</h1>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach ($types as $type)
      <button wire:key="{{ $type->id }}" wire:click="selectType({{ $type->id }})" type="button"
        class="h-32 rounded-2xl border p-6 flex items-center gap-4 transition-all duration-200
          {{ $type_id == $type->id
            ? 'bg-[#009EF5] text-white ring-2 ring-[#009EF5] ring-offset-2 border-transparent shadow-md'
            : 'bg-white border-gray-200 text-gray-600 hover:border-[#009EF5]/40 hover:shadow-lg shadow-sm' }}">
        <svg class="w-10 h-10 flex-shrink-0 {{ $type_id == $type->id ? 'text-white' : 'text-[#009EF5]' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
        </svg>
        <div class="text-left">
          <h2 class="text-lg font-bold uppercase">{{ $type->name }}</h2>
          <p class="text-sm {{ $type_id == $type->id ? 'text-white/80' : 'text-gray-400' }}">
            {{ $type->rooms->whereIn('status', ['Available', 'Cleaned'])->where('is_priority', true)->count() }} rooms available
          </p>
        </div>
      </button>
    @endforeach
  </div>

  <div class="flex items-center justify-between mt-8">
    <a href="{{ route('kiosk.dashboard') }}"
      class="bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl px-8 py-3 font-medium inline-flex items-center gap-2 transition-colors">
      <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
      </svg>
      Back
    </a>
    @if ($type_id)
      <button wire:click="$set('steps', 2)"
        class="bg-[#009EF5] hover:bg-[#0080cc] text-white rounded-xl px-8 py-3 font-medium shadow-lg shadow-[#009EF5]/25 inline-flex items-center gap-2 transition-colors">
        Next
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
        </svg>
      </button>
    @endif
  </div>
</div>
