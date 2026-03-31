<div>
  <h1 class="text-xl font-bold text-gray-800 mb-6">Select Room</h1>

  <div class="flex flex-wrap gap-2 mb-6">
    @foreach ($floors as $floor)
      @if ($floor->rooms->where('status', 'Available')->where('is_priority', true)->where('type_id', $type_id)->count() > 0)
        <button wire:click="$set('floor_id', {{ $floor->id }})"
          class="rounded-full px-5 py-2 text-sm font-bold transition-all duration-200
            {{ $floor_id == $floor->id
              ? 'bg-[#009EF5] text-white shadow-md'
              : 'bg-white border border-gray-200 text-gray-500 hover:border-[#009EF5]/40' }}">
          {{ $floor->numberWithFormat() }}
        </button>
      @endif
    @endforeach
  </div>

  <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-3">
    @forelse ($rooms as $room)
      <button wire:key="{{ $room->id }}room" wire:click="selectRoom({{ $room->id }})" type="button"
        class="rounded-xl border p-4 text-center transition-all duration-200
          {{ $room_id == $room->id
            ? 'bg-[#009EF5] text-white ring-2 ring-[#009EF5] ring-offset-2 border-transparent shadow-md'
            : 'bg-white border-gray-200 text-gray-600 hover:border-[#009EF5]/40 hover:shadow-lg shadow-sm' }}">
        <h2 class="text-2xl font-bold">{{ $room->numberWithFormat() }}</h2>
        <p class="text-xs {{ $room_id == $room->id ? 'text-white/70' : 'text-gray-400' }}">
          {{ $room->floor->numberWithFormat() }}
        </p>
      </button>
    @empty
      <div class="col-span-full flex justify-center items-center py-12">
        <p class="text-gray-400 font-medium text-lg">No priority rooms available</p>
      </div>
    @endforelse
  </div>

  <div class="flex items-center justify-between mt-8">
    <button x-on:click="step--" wire:click="backRoom"
      class="bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl px-8 py-3 font-medium inline-flex items-center gap-2 transition-colors">
      <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
      </svg>
      Back
    </button>
    @if ($room_id)
      <button wire:click="$set('steps', 3)"
        class="bg-[#009EF5] hover:bg-[#0080cc] text-white rounded-xl px-8 py-3 font-medium shadow-lg shadow-[#009EF5]/25 inline-flex items-center gap-2 transition-colors">
        Next
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
        </svg>
      </button>
    @endif
  </div>
</div>
