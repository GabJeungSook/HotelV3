<div>
  <div class="assigned">
    <ul role="list" class="divide-y divide-gray-200" x-animate>
      @forelse ($get_frontdesk as $frontdesk)
        @php
          $frontdesk = \App\Models\Frontdesk::where('id', $frontdesk)->first();
        @endphp
        <li class="flex py-2">
          <x-avatar md label="{{ $frontdesk->name[0] . '' . $frontdesk->name[1] }}" class="uppercase" />
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-900">{{ $frontdesk->name }}</p>
            <p class="text-sm text-gray-500">{{ $frontdesk->number }}</p>
          </div>
          <div class="ml-auto">
            <div class="ml-3">
            <p class="text-sm font-medium text-gray-900 text-right">SHIFT</p>
            <p class="text-sm font-semibold text-gray-800 text-right">{{ $shift }}</p>
            <p class="text-sm font-semibold text-gray-800 text-right">{{ now()->format('Y-m-d H:i:s') }}</p>
          </div>
          </div>
        </li>
      @empty
        <div>Please assign a frontdesk...</div>
      @endforelse
    </ul>
    <div class="flex justify-between  border-t pt-2 ">
       <x-native-select wire:model="drawer">
            <option selected hidden>Select Cash Drawer</option>
            @foreach ($cash_drawers as $drawer)
                <option value="{{ $drawer->id }}">{{ $drawer->name }}</option>
            @endforeach
        </x-native-select>
      @if (collect($this->get_frontdesk)->count() > 0)
        <x-button label="Save" sm positive right-icon="save-as" x-on:confirm="{
        title: 'Are you sure?',
        description      : 'You want to save assigned frontdesk',
        icon: 'warning',
        method: 'saveFrontdesk'
    }" />
      @endif

    </div>
  </div>
  <div class="mt-10">
    {{-- <div>
      <h1 class="border-b-2 font-bold text-green-600 uppercase">Frontdesk List</h1>
      <div class="mt-6 flow-root">
        <ul role="list" class="-my-5 divide-y divide-gray-200">
          @forelse ($frontdesks as $frontdesk)
            <li class="py-3">
              <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-8 fill-green-600">
                    <path fill="none" d="M0 0h24v24H0z" />
                    <path d="M5 20h14v2H5v-2zm7-2a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                  </svg>
                </div>
                <div class="min-w-0 flex-1">
                  <p class="truncate text-sm font-medium text-gray-900">{{ $frontdesk->name }}</p>
                  <p class="truncate text-sm text-gray-500">{{ $frontdesk->number }}</p>
                </div>
                <div>
                  <x-button rounded label="Assign" wire:click="assignFrontdesk({{ $frontdesk->id }})"
                    spinner="assignFrontdesk({{ $frontdesk->id }})" slate sm right-icon="arrow-narrow-right" />
                </div>
              </div>
            </li>
          @empty
            <div>No frontdesk available</div>
          @endforelse

        </ul>
      </div>

    </div> --}}

  </div>

  <x-modal wire:model.defer="partner_modal" max-width="sm" align="center">
    <x-card title="Partner's Name">

      <x-input label="Name" placeholder="enter name" wire:model.defer="name" />

      <x-slot name="footer">
        <div class="flex justify-end gap-x-4">
          <x-button flat label="Cancel" x-on:click="close" />
          <x-button positive label="Save and Proceed" right-icon="arrow-right" wire:click="savePartner"
            spinner="savePartner" />
        </div>
      </x-slot>
    </x-card>
  </x-modal>
</div>
