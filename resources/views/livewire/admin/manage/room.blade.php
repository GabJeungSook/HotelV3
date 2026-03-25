<div>
  <div class="p-4 bg-white rounded-xl ">
    <div class=" mb-5">
      <x-button wire:click="$set('add_modal', true)" icon="plus" blue label="Add New Room" />
      <div class="mt-5 flex space-x-2">
        <x-badge rounded class="font-normal" positive md label="Available" />
        <x-badge rounded class="font-normal" flat positive md label="Occupied" />
        <x-badge rounded class="font-normal" dark flat md label="Reserved" />
        <x-badge rounded class="font-normal" flat violet md label="Maintenance" />
        <x-badge rounded class="font-normal" flat negative md label="Uncleaned" />
        <x-badge rounded class="font-normal" flat red md label="Cleaning" />
        <x-badge rounded class="font-normal" flat blue md label="Cleaned" />
      </div>
    </div>
    <div
      class="sr-only !text-white !bg-green-700 !text-green !bg-green-200  !text-gray-800 !bg-gray-400 !text-indigo-800 !bg-indigo-400 !text-red-800 !bg-red-400 !text-blue-800 !bg-blue-400">
    </div>
    {{ $this->table }}
  </div>

  <x-modal wire:model="add_modal" align="center" max-width="xl">
    <x-card>
      <div class="header flex space-x-2 items-center">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="fill-gray-600">
          <path fill="none" d="M0 0h24v24H0z" />
          <path
            d="M11 11V7h2v4h4v2h-4v4h-2v-4H7v-2h4zm1 11C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16z" />
        </svg>
        <h1 class="text-lg font-semibold uppercase text-gray-600 ">Add New Room</h1>
      </div>
      <div class="mt-5 px-2 grid grid-cols-2 gap-4 ">
        <div class="col-span-2">
         @if(auth()->user()->hasRole('superadmin'))
          <x-native-select label="Branch" wire:model="branch_id">
              <option selected hidden>Select Branch</option>
                @foreach ($branches as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
          </x-native-select>
          @endif
        </div>
        <x-input label="Number" wire:model="number" />
        <x-native-select label="Type" wire:model="type">
          <option selected hidden>Select Type</option>
          @foreach ($types as $type)
            <option value="{{ $type->id }}">{{ $type->name }}</option>
          @endforeach
        </x-native-select>
        <x-native-select label="Floor" wire:model="floor">
          <option selected hidden>Select Floors</option>
          @foreach ($floors as $floor)
            <option value="{{ $floor->id }}" class="uppercase">{{ $floor->numberWithFormat() }}</option>
          @endforeach
        </x-native-select>
        <x-native-select label="Status" wire:model="status">
          <option selected hidden>Select Room Status</option>
          <option value="Available">Available</option>
          <option value="Occupied">Occupied</option>
          <option value="Reserved">Reserved</option>
          <option value="Maintenance">Maintenance</option>
          <option value="Uncleaned">Uncleaned</option>
          <option value="Cleaning">Cleaning</option>
          <option value="Cleaned">Cleaned</option>
        </x-native-select>
        <div class="col-span-2">
          <x-textarea label="Description" placeholder="Leave black if none" />
        </div>

      </div>
      <x-slot name="footer">
        <div class="flex justify-end gap-x-4">
          <x-button flat label="Cancel" x-on:click="close" />

          <x-button positive right-icon="arrow-down-tray" wire:click="saveRoom" spinner="saveRoom" label="Save" />
        </div>
      </x-slot>
    </x-card>
  </x-modal>
</div>
