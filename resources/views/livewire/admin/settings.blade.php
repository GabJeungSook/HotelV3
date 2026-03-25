<div>
  @if(auth()->user()->hasRole('superadmin'))
  <div class="mt-5">
    <div>
       <h1 class="font-bold text-xl text-gray-600 uppercase">Superadmin Settings</h1>
       <h1 class="text-sm text-gray-400"> Settings and configurations for all branches.</h1>
       <div class="mt-5">
         <livewire:admin.superadmin-settings />
       </div>
    </div>
  </div>
  @else
    <div class="mt-5">
    <div>
      <h1 class="font-bold text-xl text-[#009EF5]">{{auth()->user()->branch->name}}</h1>
      <h1 class="text-sm text-gray-400"> Settings and configurations for this branch.</h1>

      <div class="mt-6">
        <div class="px-4 sm:px-6 lg:px-8">

          <div class="mt-8 flex flex-col">
            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
              <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <table class="min-w-full divide-y divide-gray-300">
                  <thead>
                    <tr>
                      <th scope="col"
                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 md:pl-0"></th>
                      <th scope="col" class="py-3.5 px-3 text-left text-sm font-semibold text-gray-900"></th>

                      <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 md:pr-0">
                        <span class="sr-only">Edit</span>
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200">
                    <tr>
                      <td
                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-600 uppercase sm:pl-6 md:pl-0">
                        Admin Authorization Code</td>
                      <td class="whitespace-nowrap py-4 px-3 text-lg text-green-500">***** </td>

                      <td
                        class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 md:pr-0">
                        @if (auth()->user()->branch->autorization_code == null)
                          <x-button xs positive icon="pencil-square" wire:click="openModal('code')"
                            spinner="openModal('code')" label="ADD" />
                        @else
                          <x-button xs positive icon="pencil-square" wire:click="openModal('code')"
                            spinner="openModal('code')" label="UPDATE" />
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <td
                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-600 uppercase sm:pl-6 md:pl-0">
                        Extension Time Reset</td>
                      <td class="whitespace-nowrap py-4 px-3 text-sm text-gray-500">
                        {{ auth()->user()->branch->extension_time_reset }} Hours
                      <td
                        class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 md:pr-0">
                        @if (auth()->user()->branch->extension_time_reset == null)
                          <x-button xs positive icon="pencil-square" wire:click="openModal('extension')"
                            spinner="openModal('extension')" label="ADD" />
                        @else
                          <x-button xs positive icon="pencil-square" wire:click="openModal('extension')"
                            spinner="openModal('extension')" label="UPDATE" />
                        @endif
                      </td>
                    </tr>
                     <tr>
                      <td
                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-600 uppercase sm:pl-6 md:pl-0">
                        INITIAL DEPOSIT</td>
                    <td class="whitespace-nowrap py-4 px-3 text-sm text-gray-500">
                        &#8369; {{ number_format(auth()->user()->branch->initial_deposit, 2) }}
                      <td
                        class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 md:pr-0">
                        @if (auth()->user()->branch->extension_time_reset == null)
                          <x-button xs positive icon="pencil-square" wire:click="openModal('initial_deposit')"
                            spinner="openModal('initial_deposit')" label="ADD" />
                        @else
                          <x-button xs positive icon="pencil-square" wire:click="openModal('initial_deposit')"
                            spinner="openModal('initial_deposit')" label="UPDATE" />
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <td
                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-600 uppercase sm:pl-6 md:pl-0">
                        DISCOUNT</td>
                    <td class="whitespace-nowrap py-4 px-3 text-sm text-gray-500 flex items-center justify-between">
                        &#8369; {{ number_format(auth()->user()->branch->discount_amount, 2) }}
                          <div class="ml-4">
                                @if(auth()->user()->branch->discount_enabled)
                                    <span class="inline-block mt-1 px-2 py-0.5 text-xs font-semibold rounded bg-green-100 text-green-800">
                                        Enabled
                                    </span>
                                @else
                                    <span class="inline-block mt-1 px-2 py-0.5 text-xs font-semibold rounded bg-red-100 text-red-800">
                                        Disabled
                                    </span>
                                @endif
                            </div>
                            </td>
                    <td
                        class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 md:pr-0">
                          <x-button xs positive icon="pencil-square" wire:click="openModal('discount')"
                            spinner="openModal('discount')" label="UPDATE" />
                      </td>
                    </tr>
                                        <tr>
                      <td
                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-600 uppercase sm:pl-6 md:pl-0">
                        KIOSK CHECK-IN TIME LIMIT (Minutes)</td>
                        <td class="whitespace-nowrap py-4 px-3 text-sm text-gray-500 flex items-center justify-between">
                        {{ auth()->user()->branch->kiosk_time_limit }}
                         </td>
                    <td
                        class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 md:pr-0">
                          <x-button xs positive icon="pencil-square" wire:click="openModal('kiosk_time_limit')"
                            spinner="openModal('kiosk_time_limit')" label="UPDATE" />
                      </td>
                    </tr>

                    <!-- More people... -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        {{-- transfer room table --}}
        <hr class="my-4 border-t border-gray-300">
        <h1 class="font-bold text-xl text-[#009EF5] uppercase">Transfer Room Reasons</h1>
        <div class="py-4">
            <livewire:admin.room-transfer-reasons />
        </div>
        {{-- staying hours table --}}
        <hr class="my-4 border-t border-gray-300">
        <h1 class="font-bold text-xl text-[#009EF5] uppercase">Staying Hours</h1>
        <div class="py-4">
            <livewire:admin.manage-staying-hours />
        </div>

      </div>
    </div>
  </div>

    <x-modal wire:model="code_modal" max-width="md" align="center">
    <x-card title="AUTHORIZATION CODE">

      <div class="flex flex-col space-y-2">
        @if ($editMode === true)
          <x-password label="Old Code" value="" wire:model="old_code" placeholder="Enter code" />
          <x-password label="New Authorization Code" wire:model="code" placeholder="Enter code" />
        @else
          <x-password wire:model="code" placeholder="Enter code" />
        @endif
      </div>

      <x-slot name="footer">
        <div class="flex justify-end gap-x-2">
          <x-button flat negative label="Cancel" x-on:click="close" />
          <x-button positive right-icon="arrow-small-right" wire:click="saveCode" spinner="saveCode" label="Save" />
        </div>
      </x-slot>
    </x-card>
  </x-modal>

  <x-modal wire:model="extension_modal" max-width="md" align="center">
    <x-card title="EXTENSION TIME RESET">

      <div class="flex flex-col space-y-2">
        @if ($editMode === true)
          <x-native-select label="Time" wire:model="reset_time">
            <option selected hidden>Select time reset</option>
            @foreach ($extensions as $extension)
              <option value="{{ $extension->hour }}">{{ $extension->hour }}</option>
            @endforeach
          </x-native-select>
        @else
          <x-native-select label="Time" wire:model="reset_time">
            <option selected hidden>Select time reset</option>
            @foreach ($extensions as $extension)
              <option value="{{ $extension->hour }}">{{ $extension->hour }}</option>
            @endforeach
          </x-native-select>
        @endif
      </div>

      <x-slot name="footer">
        <div class="flex justify-end gap-x-2">
          <x-button flat negative label="Cancel" x-on:click="close" />
          <x-button positive right-icon="arrow-small-right" wire:click="saveExtension" spinner="saveExtension"
            label="Save" />
        </div>
      </x-slot>
    </x-card>
  </x-modal>

   <x-modal wire:model="initial_deposit_modal" max-width="md" align="center">
    <x-card title="INITIAL DEPOSIT">

      <div class="flex flex-col space-y-2">
        <x-input wire:model="initial_deposit" label="Inital Deposit" placeholder="" prefix="₱"/>
      </div>

      <x-slot name="footer">
        <div class="flex justify-end gap-x-2">
          <x-button flat negative label="Cancel" x-on:click="close" />
          <x-button positive right-icon="arrow-small-right" wire:click="saveInitialDeposit" spinner="saveInitialDeposit"
            label="Save" />
        </div>
      </x-slot>
    </x-card>
  </x-modal>

    <x-modal wire:model="discount_modal" max-width="md" align="center">
    <x-card title="DISCOUNT">

      <div class="flex flex-col space-y-2 mb-5">
        <x-toggle md left-label="Enable Discount" wire:model="discount_enabled" />
      </div>

      <div class="flex flex-col space-y-2">
        <x-input wire:model="discount_amount" label="Amount" placeholder="" prefix="₱"/>
      </div>

      <x-slot name="footer">
        <div class="flex justify-end gap-x-2">
          <x-button flat negative label="Cancel" x-on:click="close" />
          <x-button positive right-icon="arrow-small-right" wire:click="saveDiscount" spinner="saveDiscount"
            label="Save" />
        </div>
      </x-slot>
    </x-card>
  </x-modal>

    <x-modal wire:model="kiosk_time_limit_modal" max-width="md" align="center">
    <x-card title="KIOSK TIME LIMIT">

      <div class="flex flex-col space-y-2">
        <x-input wire:model="kiosk_time_limit" label="Kiosk Time Limit (Minutes)" placeholder=""/>
      </div>

      <x-slot name="footer">
        <div class="flex justify-end gap-x-2">
          <x-button flat negative label="Cancel" x-on:click="close" />
          <x-button positive right-icon="arrow-small-right" wire:click="saveKioskTimeLimit" spinner="saveKioskTimeLimit"
            label="Save" />
        </div>
      </x-slot>
    </x-card>
  </x-modal>
  @endif



</div>
