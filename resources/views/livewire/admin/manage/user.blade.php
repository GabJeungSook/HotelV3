<div>
  <div class="bg-white p-4 rounded-xl">
    <div>

      <div class="hidden sm:block" x-data="{ type: 1 }" x-animate>
        <div class="border-b border-gray-200">
          <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button x-on:click="type=1" wire:click="$set('type',1)"
              :class="type == 1 ? 'border-gray-600' : 'border-transparent'"
              class="  text-gray-500  hover:text-gray-700 hover:border-gray-500 whitespace-nowrap flex py-3  border-b-2 font-semibold text-md">
              Users
            </button>
            <button x-on:click="type=2" wire:click="$set('type',2)"
              :class="type == 2 ? 'border-gray-600' : 'border-transparent'"
              class=" text-gray-500 hover:text-gray-700 hover:border-gray-500 whitespace-nowrap flex py-3 px-1 border-b-2 font-semibold text-md">
              Manage Frontdesk
            </button>
            <button x-on:click="type=3" wire:click="$set('type',3)"
              :class="type == 3 ? 'border-gray-600' : 'border-transparent'"
              class=" text-gray-500 hover:text-gray-700 hover:border-gray-500 whitespace-nowrap flex py-3 px-1 border-b-2 font-semibold text-md">
              Roomboy Designation
            </button>
          </nav>
        </div>
        <div class="p-4">
          @if ($type == 1)
            <div>
              <div class="flex mb-5">
                <x-button wire:click="$set('add_modal', true)" icon="plus" blue label="Add New User" />
              </div>
              {{ $this->table }}
            </div>
          @elseif($type == 2)
            <livewire:admin.manage-frondesk />
          @elseif($type == 3)
            <livewire:admin.roomboy-designation />
          @endif
        </div>
      </div>
    </div>
  </div>

  <x-modal wire:model="add_modal" align="center" max-width="xl">
    <x-card>
      <div class="header flex space-x-2 items-center">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="fill-gray-600">
          <path fill="none" d="M0 0h24v24H0z" />
          <path
            d="M11 11V7h2v4h4v2h-4v4h-2v-4H7v-2h4zm1 11C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16z" />
        </svg>
        <h1 class="text-lg font-semibold uppercase text-gray-600 ">Add New User</h1>
      </div>
      <div class="mt-5 px-2 grid grid-cols-2 gap-4 ">
        @if(auth()->user()->hasRole('superadmin'))
        <div class="col-span-2">
          <x-native-select label="Branch" wire:model="branch_id">
              <option selected hidden>Select Branch</option>
                @foreach ($branches as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
          </x-native-select>
        </div>
        @endif
        <x-input label="Name" wire:model="name" />
        <x-input label="Email" wire:model="email" />
        <x-password label="Password" wire:model="password" />
        <x-native-select label="Role" wire:model="role">
          <option selected hidden>Select Role</option>
          <option value="admin">Admin</option>
          <option value="frontdesk">Frontdesk</option>
          <option value="kiosk">Kiosk</option>
          <option value="kitchen">Kitchen</option>
          <option value="roomboy">Roomboy</option>
          <option value="back_office">Back_Office</option>
        </x-native-select>


      </div>
      <x-slot name="footer">
        <div class="flex justify-end gap-x-4">
          <x-button flat label="Cancel" x-on:click="close" />

          <x-button positive right-icon="arrow-down-tray" wire:click="saveUser" spinner="saveUser" label="Save" />
        </div>
      </x-slot>
    </x-card>
  </x-modal>
</div>
