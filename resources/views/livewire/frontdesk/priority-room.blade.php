<div>
    {{-- Header --}}
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold text-[#009EF5]">Priority Rooms</h1>
            <p class="text-xs text-gray-400">Click a room to toggle priority. Priority rooms are offered first to kiosk guests.</p>
        </div>
        @if($branches->isNotEmpty())
            <select wire:model.live="branch_id" class="rounded-md border-gray-300 text-sm focus:ring-[#009EF5] focus:border-[#009EF5]">
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
        @endif
    </div>

    {{-- Stats + Bulk Actions --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex gap-3">
            <div class="flex items-center gap-2 rounded-lg bg-blue-50 border border-blue-200 px-3 py-1.5">
                <div class="h-2.5 w-2.5 rounded-full bg-[#009EF5]"></div>
                <span class="text-sm font-semibold text-[#009EF5]">{{ $priorityCount }} Priority</span>
            </div>
            <div class="flex items-center gap-2 rounded-lg bg-gray-50 border border-gray-200 px-3 py-1.5">
                <div class="h-2.5 w-2.5 rounded-full bg-gray-400"></div>
                <span class="text-sm font-semibold text-gray-500">{{ $availableCount }} Available</span>
            </div>
        </div>
        @if($filterType || $filterFloor)
            <div class="flex gap-2">
                <button wire:click="bulkSetPriority"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-[#009EF5] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#0080cc] transition active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    Set All as Priority
                </button>
                <button wire:click="bulkRemovePriority"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Remove All Priority
                </button>
            </div>
        @endif
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-3 mb-5">
        <select wire:model.live="filterType" class="rounded-lg border-gray-300 text-sm focus:ring-[#009EF5] focus:border-[#009EF5] min-w-[140px]">
            <option value="">All Types</option>
            @foreach($types as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterFloor" class="rounded-lg border-gray-300 text-sm focus:ring-[#009EF5] focus:border-[#009EF5] min-w-[140px]">
            <option value="">All Floors</option>
            @foreach($floors as $floor)
                <option value="{{ $floor->id }}">Floor {{ $floor->number }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterPriority" class="rounded-lg border-gray-300 text-sm focus:ring-[#009EF5] focus:border-[#009EF5] min-w-[140px]">
            <option value="">All Rooms</option>
            <option value="yes">Priority Only</option>
            <option value="no">Available Only</option>
        </select>
    </div>

    {{-- Room Grid --}}
    <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
        @forelse($rooms as $room)
            <button wire:click="togglePriority({{ $room->id }})"
                class="relative rounded-xl border-2 p-3 text-left transition-all duration-150 hover:shadow-md active:scale-95 cursor-pointer
                {{ $room->is_priority
                    ? 'border-[#009EF5] bg-blue-50 hover:border-[#0080cc]'
                    : 'border-gray-200 bg-white hover:border-[#009EF5]/40' }}">

                {{-- Priority star --}}
                @if($room->is_priority)
                    <div class="absolute top-2 right-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#009EF5]" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                @endif

                {{-- Room number --}}
                <div class="text-base font-bold {{ $room->is_priority ? 'text-[#009EF5]' : 'text-gray-800' }}">
                    #{{ $room->number }}
                </div>

                {{-- Floor --}}
                <div class="text-xs text-gray-400 mt-0.5">
                    Floor {{ $room->floor?->number }}
                </div>

                {{-- Type badge --}}
                <div class="mt-1.5">
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium
                        {{ $room->is_priority ? 'bg-blue-100 text-[#009EF5]' : 'bg-gray-100 text-gray-600' }}">
                        {{ $room->type?->name }}
                    </span>
                </div>

                {{-- Status --}}
                <div class="mt-1 text-[10px] font-medium {{ $room->is_priority ? 'text-[#009EF5]' : 'text-gray-400' }}">
                    {{ $room->is_priority ? 'PRIORITY' : $room->status }}
                </div>
            </button>
        @empty
            <div class="col-span-full py-16 text-center text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <p class="text-sm">No available or cleaned rooms found.</p>
            </div>
        @endforelse
    </div>
</div>
