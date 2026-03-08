<div>
  <div class="flex justify-end space-x-2 px-4">
    <x-button label="Add New Remittance" icon="plus" wire:click="$set('add_modal',true)" positive />
    <x-button label="Print Report" icon="printer" wire:click="redirectReport" amber />
  </div>
  <div class="table w-full px-4">
    <div class=" bg-white p-4 rounded-xl mt-4">
      <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
          <p class="mt-2 text-2xl font-bold text-red-600">&#8369;{{ number_format($total, 2) }}</p>
          <h1 class="text-sm  text-gray-500">Total Remittance</h1>
        </div>

      </div>
      <div class="-mx-4 mt-3 flex flex-col sm:-mx-6 md:mx-0">
        <table class="min-w-full border-t divide-y divide-gray-300">
          <thead>
            <tr>
              <th scope="col"
                class="py-3.5 w-40 pl-4 pr-3 text-left text-sm font-semibold text-gray-600 sm:pl-6 md:pl-0"></th>
              <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-600 sm:pl-6 md:pl-0">
                DATE</th>
              {{-- <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-600 sm:pl-6 md:pl-0">
                FRONTDESK NAME</th> --}}
              <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-600 sm:pl-6 md:pl-0">
                SHIFT</th>
              <th scope="col"
                class="hidden py-3.5 px-3 text-right text-sm font-semibold text-gray-600 sm:table-cell">DESCRIPTION</th>
              <th scope="col"
                class="hidden py-3.5 px-3 text-right text-sm font-semibold text-gray-600 sm:table-cell">AMOUNT</th>
            </tr>
          </thead>
          <tbody>
              @forelse ($remittances as $remittance)
                <tr class="border-b border-gray-200">
                  <td class="hidden py-3 px-3 text-right text-sm text-gray-500 sm:table-cell"></td>
                  <td class="py-3 pl-4 pr-3 text-sm sm:pl-6 md:pl-0">
                    <div class="font-medium text-gray-500 uppercase">{{ Carbon\Carbon::parse($remittance->created_at)->format('F d, Y h:i A') }}</div>
                  </td>
                  {{-- <td class="py-3 pl-4 pr-3 text-sm sm:pl-6 md:pl-0">
                    <div class="font-medium text-gray-500 uppercase">{{ $expense->name }}</div>
                  </td> --}}
                  <td class="py-3 pl-4 pr-3 text-sm sm:pl-6 md:pl-0">
                    <div class="font-medium text-gray-500 uppercase">{{ $remittance->shiftLog->shift }}</div>
                  </td>
                  <td class="hidden py-3 px-3 text-right text-sm text-gray-500 sm:table-cell">
                    {{ $remittance->description ?? null }}</td>
                  <td class="hidden py-3 px-3 text-right text-sm text-gray-500 sm:table-cell">
                    &#8369; {{ number_format($remittance->total_remittance, 2) }}</td>
                </tr>
              @empty
              <tr>
                <td colspan="6" class="py-3 px-3 text-center text-sm text-gray-500">No remittance recorded for this category.</td>
              </tr>
              @endforelse
            <!-- More projects... -->
          </tbody>

        </table>
      </div>
    </div>

  </div>


  <x-modal blur wire:model.defer="add_modal" align="center">
    <x-card>
      <div class="header flex space-x-2 items-center">
        <h1 class="text-lg font-semibold uppercase text-gray-600 ">Add New Remittance</h1>
      </div>
      <div class="mt-5 px-4 grid grid-cols-1 gap-4">
          <x-input label="Amount" type="number" wire:model.defer="remittance_amount" />
        <div>
            <x-textarea label="Description" wire:model.defer="description" />
        </div>
    </div>
      <x-slot name="footer">
        <div class="flex justify-end gap-x-4">
          <x-button flat label="Cancel" x-on:click="close" />

          <x-button positive right-icon="save-as" wire:click="saveRemittance" spinner="saveRemittance"
            label="Save Remittance" />
        </div>
      </x-slot>
    </x-card>
  </x-modal>
</div>
