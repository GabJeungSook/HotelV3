<div class="px-4">
  <div class="text-center mb-6">
    <h1 class="text-xl font-bold text-gray-800">Select Your Room</h1>
    <p class="text-sm text-gray-500 mt-1">Choose the room you'd like to check out from</p>
  </div>

  <div class="flex flex-wrap justify-center gap-2 mb-6">
    @foreach ($floors as $floor)
      @if ($floor->rooms->where('status', 'Occupied')->count() > 0)
        <button wire:click="$set('floor_id', {{ $floor->id }})"
          class="py-2 px-5 rounded-full text-sm font-bold transition-all duration-200
            {{ $floor_id == $floor->id
              ? 'bg-[#009EF5] text-white shadow-lg shadow-[#009EF5]/25'
              : 'bg-white text-gray-600 border border-gray-200 hover:border-[#009EF5] hover:text-[#009EF5]' }}">
          {{ $floor->numberWithFormat() }}
        </button>
      @endif
    @endforeach
  </div>

  <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-3">
    @forelse ($rooms as $room)
      <button wire:click="selectRoom({{ $room->id }})" type="button"
        class="rounded-xl p-4 text-center border transition-all duration-200
          {{ $room_id == $room->id
            ? 'bg-[#009EF5] text-white ring-2 ring-[#009EF5] ring-offset-2 border-transparent shadow-lg shadow-[#009EF5]/25'
            : 'bg-white text-gray-600 border-gray-200 hover:border-[#009EF5] hover:shadow-md' }}">
        <span class="text-2xl font-bold block">{{ $room->numberWithFormat() }}</span>
      </button>
    @empty
      <div class="col-span-full flex justify-center items-center py-16">
        <p class="font-semibold text-gray-400 text-lg">No occupied rooms available</p>
      </div>
    @endforelse
  </div>

  <div class="flex justify-center gap-3 mt-10">
    <a href="{{ route('kiosk.dashboard') }}"
      class="px-6 py-3 rounded-xl font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition-all duration-200">
      Back
    </a>
    @if ($room_id)
      <button wire:click="$set('steps', 2)"
        class="px-8 py-3 rounded-xl font-semibold text-white bg-[#009EF5] hover:bg-[#0080cc] shadow-lg shadow-[#009EF5]/25 transition-all duration-200 flex items-center gap-2">
        NEXT
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
        </svg>
      </button>
    @endif
  </div>
</div>
