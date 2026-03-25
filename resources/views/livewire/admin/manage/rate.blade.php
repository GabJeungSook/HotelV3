<div>
  <div class="bg-white p-4 rounded-xl">
    <div class="flex justify-between mb-5">
      @if(auth()->user()->hasRole('superadmin') && $branch_id != null)
        <div class="flex space-x-4">
            <x-button wire:click="openAddHour" icon="plus" blue label="Add New Staying Hour" />
            <x-button wire:click="openAdd" icon="plus" blue label="Add New Rate" />
        </div>
      @elseif(auth()->user()->hasRole('admin'))
       <div class="flex space-x-4"></div>
            <x-button wire:click="openAddHour" icon="plus" blue label="Add New Staying Hour" />
            <x-button wire:click="openAdd" icon="plus" blue label="Add New Rate" />
        </div>
      @else
      <div></div>
      @endif
      @if(auth()->user()->hasRole('superadmin'))
          <x-native-select label="Branch" wire:model="branch_id">
              <option selected hidden>Select Branch</option>
                @foreach ($branches as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
          </x-native-select>
          @endif
    </div>
    @if(auth()->user()->hasRole('superadmin'))
    <div class="my-5 text-xl font-semibold text-gray-600">
      {{$branch_name ?? 'No Branch Selected'}}
    </div>
    @endif
    {{ $this->table }}
  </div>

    <x-modal wire:model="add_staying_hour_modal" align="center" max-width="lg">
    <x-card>
      <div class="header flex space-x-2 items-center">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="fill-gray-600">
          <path fill="none" d="M0 0h24v24H0z" />
          <path
            d="M11 11V7h2v4h4v2h-4v4h-2v-4H7v-2h4zm1 11C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16z" />
        </svg>
        <h1 class="text-lg font-semibold uppercase text-gray-600 ">Add New Staying Hour</h1>
      </div>
      <div class="flex mt-5 px-4 flex-col space-y-3">
        <x-input wire:model="number" label="Number of Hours" placeholder="" />
        @php
          $types = App\Models\Type::where('branch_id', auth()->user()->hasRole('superadmin') ? $this->branch_id : auth()->user()->branch_id)->get();
        @endphp
      </div>
      <x-slot name="footer">
        <div class="flex justify-end gap-x-4">
          <x-button flat label="Cancel" x-on:click="close" />

          <x-button positive right-icon="arrow-down-tray" spinner="saveStayingHour" wire:click="saveStayingHour" label="Save" />
        </div>
      </x-slot>
    </x-card>
  </x-modal>


  <x-modal wire:model="add_modal" align="center" max-width="lg">
    <x-card>
      <div class="header flex space-x-2 items-center">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="fill-gray-600">
          <path fill="none" d="M0 0h24v24H0z" />
          <path
            d="M11 11V7h2v4h4v2h-4v4h-2v-4H7v-2h4zm1 11C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16z" />
        </svg>
        <h1 class="text-lg font-semibold uppercase text-gray-600 ">Add New Rates</h1>
      </div>
      <div class="flex mt-5 px-4 flex-col space-y-3">
        <x-native-select label="Select Number of Hours" wire:model="hours_id">
          <option selected hidden>Select Number of Hours</option>
          @foreach ($stayingHours as $hour)
            <option value="{{ $hour->id }}">{{ $hour->number }} hours ({{$hour->branch->name}})</option>
          @endforeach
        </x-native-select>
        <x-input wire:model="amount" label="Amount" placeholder="" />
        @php
          $types = App\Models\Type::where('branch_id', auth()->user()->hasRole('superadmin') ? $this->branch_id : auth()->user()->branch_id)->get();
        @endphp
        <x-native-select label="Select Type" wire:model="type_id">
          <option selected hidden>Select Type</option>
          @foreach ($types as $type)
            <option value="{{ $type->id }}">{{ $type->name }} </option>
          @endforeach
        </x-native-select>
      </div>
      <x-slot name="footer">
        <div class="flex justify-end gap-x-4">
          <x-button flat label="Cancel" x-on:click="close" />

          <x-button positive right-icon="arrow-down-tray" spinner="saveRate" wire:click="saveRate" label="Save" />
        </div>
      </x-slot>
    </x-card>
  </x-modal>

  <x-modal wire:model="edit_modal" align="center" max-width="lg">
    <x-card>
      <div class="header flex space-x-2 items-center">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="fill-gray-600" width="24" height="24">
          <path fill="none" d="M0 0h24v24H0z" />
          <path
            d="M16.757 3l-2 2H5v14h14V9.243l2-2V20a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h12.757zm3.728-.9L21.9 3.516l-9.192 9.192-1.412.003-.002-1.417L20.485 2.1z" />
        </svg>
        <h1 class="text-lg font-semibold uppercase text-gray-600 ">Update Rates</h1>
      </div>
      <div class="flex mt-5 px-4 flex-col space-y-3">
        <x-native-select label="Select Number of Hours" wire:model="hours_id">
          <option selected hidden>Select Number of Hours</option>
          @foreach ($stayingHours as $hour)
            <option value="{{ $hour->id }}">{{ $hour->number  }} hours ({{$hour->branch->name}})</option>
          @endforeach
        </x-native-select>
        <x-input wire:model="amount" label="Amount" placeholder="" />
        @php
          $types = App\Models\Type::where('branch_id', auth()->user()->hasRole('superadmin') ? $this->branch_id : auth()->user()->branch_id)->get();
        @endphp
        <x-native-select label="Select Type" wire:model="type_id">
          <option selected hidden>Select Type</option>
          @foreach ($types as $type)
            <option value="{{ $type->id }}">{{ $type->name }} </option>
          @endforeach
        </x-native-select>
      </div>
      <x-slot name="footer">
        <div class="flex justify-end gap-x-4">
          <x-button flat label="Cancel" x-on:click="close" />

          <x-button positive right-icon="arrow-down-tray" spinner="updateRates" wire:click="updateRates" label="Update" />
        </div>
      </x-slot>
    </x-card>
  </x-modal>

</div>
