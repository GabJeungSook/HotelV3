<div>
  <div class="bg-white p-4 rounded-xl">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-5">
      <div class="flex space-x-4">
        <x-button wire:click="openRateModal" icon="pencil-square" blue label="Set Rates" />
        <x-button wire:click="$set('add_staying_hour_modal', true)" icon="plus" flat label="Add Staying Hour" />
      </div>

      <div class="flex items-center space-x-4">
        <div class="w-40">
          <x-input wire:model.live.debounce.300ms="search" label="Search" placeholder="Room number" icon="magnifying-glass" />
        </div>
        @if(auth()->user()->hasRole('superadmin'))
        <div class="w-48">
          <x-native-select label="Branch" wire:model.live="branch_id">
            <option selected hidden>Select Branch</option>
            @foreach ($branches as $branch)
              <option value="{{ $branch->id }}">{{ $branch->name }}</option>
            @endforeach
          </x-native-select>
        </div>
        @endif
        <div class="w-40">
          <x-native-select label="Type" wire:model.live="filter_type_id">
            <option value="">All Types</option>
            @foreach ($types as $type)
              <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
          </x-native-select>
        </div>
        <div class="w-40">
          <x-native-select label="Floor" wire:model.live="filter_floor_id">
            <option value="">All Floors</option>
            @foreach ($floors as $floor)
              <option value="{{ $floor->id }}">Floor {{ $floor->number }}</option>
            @endforeach
          </x-native-select>
        </div>
      </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b">
          <tr>
            <th class="px-4 py-3 w-10">
              <input type="checkbox" wire:model.live="select_all" class="form-checkbox rounded text-[#009EF5] focus:ring-[#009EF5] border-gray-300" />
            </th>
            <th class="px-4 py-3">Room</th>
            <th class="px-4 py-3">Type</th>
            <th class="px-4 py-3">Floor</th>
            @foreach ($stayingHours as $sh)
              <th class="px-4 py-3 text-center">{{ $sh->number }}h</th>
            @endforeach
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse ($rooms as $room)
            <tr class="hover:bg-gray-50 {{ in_array((string)$room->id, $selected_rooms) ? 'bg-blue-50' : '' }}">
              <td class="px-4 py-3">
                <input type="checkbox" wire:model.live="selected_rooms" value="{{ $room->id }}" class="form-checkbox rounded text-[#009EF5] focus:ring-[#009EF5] border-gray-300" />
              </td>
              <td class="px-4 py-3 font-medium text-gray-800">{{ $room->number }}</td>
              <td class="px-4 py-3 text-gray-600">{{ $room->type->name }}</td>
              <td class="px-4 py-3 text-gray-600">{{ $room->floor->number }}</td>
              @foreach ($stayingHours as $sh)
                @php
                  $rate = $room->rates->firstWhere('staying_hour_id', $sh->id);
                @endphp
                <td class="px-4 py-3 text-center">
                  @if($rate)
                    <span class="text-gray-800 font-medium">{{ number_format($rate->amount, 2) }}</span>
                  @else
                    <span class="text-gray-300">-</span>
                  @endif
                </td>
              @endforeach
            </tr>
          @empty
            <tr>
              <td colspan="{{ 4 + $stayingHours->count() }}" class="px-4 py-8 text-center text-gray-400">
                @if(auth()->user()->hasRole('superadmin') && !$branch_id)
                  Select a branch to view rooms
                @else
                  No rooms found
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($rooms->count() > 0)
    <div class="mt-3 text-sm text-gray-500">
      {{ count($selected_rooms) }} of {{ $rooms->count() }} room(s) selected
    </div>
    @endif
  </div>

  {{-- Set Rates Modal --}}
  <x-modal wire:model="rate_modal" align="center" max-width="lg">
    <x-card>
      <div class="header flex space-x-1 border-b items-end justify-between py-0.5">
        <h2 class="text-lg uppercase text-gray-600 font-bold">Set Rates for {{ count($selected_rooms) }} Room(s)</h2>
      </div>
      <div class="mt-4 space-y-4">
        <p class="text-sm text-gray-500">Enter the rate amount for each staying hour. Leave blank to skip.</p>
        @foreach ($rate_amounts as $key => $entry)
          <div class="flex items-center justify-between space-x-4">
            <label class="text-gray-700 font-medium w-32">{{ $entry['hours'] }} hours</label>
            <input wire:model="rate_amounts.{{ $key }}.amount" type="number" min="0" step="1" placeholder="0"
              class="w-full rounded-md border-gray-300 text-right text-lg font-semibold focus:border-blue-500 focus:ring-blue-500" />
          </div>
        @endforeach
      </div>
      <x-slot name="footer">
        <div class="flex justify-end gap-x-4">
          <x-button flat label="Cancel" x-on:click="close" />
          <x-button positive label="Apply Rates" wire:click="applyRates" spinner="applyRates" />
        </div>
      </x-slot>
    </x-card>
  </x-modal>

  {{-- Add Staying Hour Modal --}}
  <x-modal wire:model="add_staying_hour_modal" align="center" max-width="md">
    <x-card>
      <div class="header flex space-x-1 border-b items-end justify-between py-0.5">
        <h2 class="text-lg uppercase text-gray-600 font-bold">Add Staying Hour</h2>
      </div>
      <div class="mt-4">
        <x-input wire:model="number" label="Number of Hours" type="number" placeholder="e.g. 6" />
      </div>
      <x-slot name="footer">
        <div class="flex justify-end gap-x-4">
          <x-button flat label="Cancel" x-on:click="close" />
          <x-button positive label="Save" wire:click="saveStayingHour" spinner="saveStayingHour" />
        </div>
      </x-slot>
    </x-card>
  </x-modal>
</div>
