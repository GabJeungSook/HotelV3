<div>
  <div class="bg-white p-4 rounded-xl">
    <div class="flex mb-5">
      <x-button wire:click="$set('add_modal', true)" icon="plus" blue label="Add New Types" />
    </div>
    {{ $this->table }}
  </div>

  <x-modal wire:model="add_modal" align="center" max-width="lg">
    <x-card>
      <div class="header flex space-x-2 items-center">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="fill-gray-600">
          <path fill="none" d="M0 0h24v24H0z" />
          <path
            d="M11 11V7h2v4h4v2h-4v4h-2v-4H7v-2h4zm1 11C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16z" />
        </svg>
        <h1 class="text-lg font-semibold uppercase text-gray-600 ">Add New Types</h1>
      </div>
      <div class="mt-5 px-4">
        <div class="space-y-4">
          @if(auth()->user()->hasRole('superadmin'))
          <x-native-select label="Branch" wire:model="branch_id">
              <option selected hidden>Select Branch</option>
                @foreach ($branches as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
          </x-native-select>
          @endif
        <x-input label="Name" wire:model="name" />
        </div>
      </div>
      <x-slot name="footer">
        <div class="flex justify-end gap-x-4">
          <x-button flat label="Cancel" x-on:click="close" />

          <x-button positive right-icon="arrow-down-tray" wire:click="saveType" spinner="saveType" label="Save" />
        </div>
      </x-slot>
    </x-card>
  </x-modal>
</div>
