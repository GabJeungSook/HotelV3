<div>
      <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Cash on Hand ({{ auth()->user()->cash_drawer->name ?? 'No Drawer' }})</h1>
            <p class="text-sm text-gray-500">Review summary before proceeding.</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200">
            {{-- Table-like header --}}
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div class="text-sm font-medium text-gray-700">Summary</div>
                <div class="text-sm font-medium text-gray-700">Amount</div>
            </div>

            {{-- Rows --}}
            <div class="divide-y divide-gray-200">
                {{-- Transactions --}}
                <div class="px-6 py-5 flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-gray-900">Transactions</span>
                        <span class="text-xs text-gray-500">Extensions, Food & Beverages, Etc.</span>
                    </div>
                    <div class="w-40 text-right">
                        <span class="inline-block h-5 w-24 rounded font-mono">₱ {{ number_format($total_transactions, 2) }}</span>
                    </div>
                </div>

                {{-- Deposits --}}
                <div class="px-6 py-5 flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-gray-900">Deposits</span>
                        <span class="text-xs text-gray-500">From check-in and other deposits</span>
                    </div>
                    <div class="w-40 text-right">
                        <span class="inline-block h-5 w-24 rounded font-mono">₱ {{ number_format($total_deposits, 2) }}</span>
                    </div>
                </div>

                {{-- Total --}}
                <div class="px-6 py-5 flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-gray-900">Total</span>
                        <span class="text-xs text-gray-500">Expected cash on hand</span>
                    </div>
                    <div class="w-40 text-right">
                        <span class="inline-block h-6 w-28 rounded font-mono font-bold text-lg">₱ {{ number_format($total_transactions + $total_deposits, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Footer actions (bottom-right logout) --}}
            <div class="px-6 py-4 space-x-4 border-t border-gray-200 flex items-center justify-end">
                <button
                        type="button"
                        wire:click="$set('withdraw_modal', true)"
                        class="inline-flex items-center rounded-lg bg-yellow-600 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2">
                        Withdraw Cash
                </button>
                <button type="button" wire:click="$set('logout_modal', true)" class="inline-flex items-center rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    End Shift
                </button>
                {{-- <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        End Shift
                    </button>
                </form> --}}
            </div>
        </div>
    </div>

    {{-- Logout Modal --}}
     <x-modal wire:model.defer="logout_modal" align="center">
      <x-card>
        <div>
          <div class="header flex space-x-1 border-b items-end justify-between py-0.5">
            <h2 class="text-lg uppercase text-gray-600 font-bold">Confirm Logout</h2>
          </div>
          <div class="mt-3">
            <div class="space-y-4">
              <dl class="mt-8 p-2 divide-y divide-gray-400 text-sm lg:col-span-5 lg:mt-0">
                <div class="flex justify-between py-2">
                    <div class="w-full divide-y divide-gray-400 ">
                        <span class="text-xl font-semibold mb-8">Are you sure you want to end your shift?</span>
                        <dl class="mt-2 text-sm lg:col-span-5 lg:mt-0">
                            <div class="flex justify-between py-2">
                                <dt class="text-gray-600 text-lg font-bold">Total Cash on Drawer:</dt>
                                <dd class="text-gray-800 text-lg font-bold">₱ {{ number_format($total_transactions + $total_deposits, 2) }}</dd>
                            </div>
                    </div>
                </div>
              </dl>
            </div>
          </div>
        </div>
        <x-slot name="footer">
          <div class="flex justify-end space-x-2">
              <x-button red label="Close" x-on:click="close" />
              <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-button emerald label="Confirm" type="submit" />
                </form>
          </div>
        </x-slot>
      </x-card>
    </x-modal>

        {{-- Withdraw Modal --}}
     <x-modal wire:model.defer="withdraw_modal" align="center">
      <x-card>
        <div>
          <div class="header flex space-x-1 border-b items-end justify-between py-0.5">
            <h2 class="text-lg uppercase text-gray-600 font-bold">Withdraw Cash</h2>
          </div>
          <div class="mt-3">
            <div class="space-y-4">
              <dl class="mt-8 p-2 divide-y divide-gray-400 text-sm lg:col-span-5 lg:mt-0">
                <div class="flex justify-between py-2">
                    <div class="w-full divide-y divide-gray-400 ">
                        <span class="text-xl font-semibold mb-8">Are you sure you want to withdraw cash?</span>
                        <dl class="mt-2 text-sm lg:col-span-5 lg:mt-0">
                            <div class="flex justify-between py-2">
                                <dt class="text-gray-600 text-lg font-bold">Total Cash on Drawer:</dt>
                                <dd class="text-gray-800 text-lg font-bold">₱ {{ number_format($total_transactions + $total_deposits, 2) }}</dd>
                            </div>
                    </div>
                </div>
              </dl>
            </div>
          </div>
        </div>
        <x-slot name="footer">
          <div class="flex justify-end space-x-2">
              <x-button red label="Close" x-on:click="close" />
              <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-button emerald label="Confirm" type="submit" />
                </form>
          </div>
        </x-slot>
      </x-card>
    </x-modal>

</div>
