<div>
      <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">{{ auth()->user()->cash_drawer->name ?? 'No Drawer' }}</h1>
            {{-- <p class="text-sm text-gray-500">Review summary before proceeding.</p> --}}
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200">
            {{-- Table-like header --}}
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                {{-- <div class="text-sm font-medium text-gray-700">Summary</div>
                <div class="text-sm font-medium text-gray-700">Amount</div> --}}
            </div>

            {{-- Rows --}}
            <div class="divide-y divide-gray-200">
                {{-- Transactions --}}
                {{-- <div class="px-6 py-5 flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-gray-900">Transactions</span>
                        <span class="text-xs text-gray-500">Extensions, Food & Beverages, Etc.</span>
                    </div>
                    <div class="w-40 text-right">
                        <span class="inline-block h-5 w-24 rounded font-mono">₱ {{ number_format($total_transactions, 2) }}</span>
                    </div>
                </div> --}}

                {{-- Deposits --}}
                {{-- <div class="px-6 py-5 flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-gray-900">Deposits</span>
                        <span class="text-xs text-gray-500">From check-in and other deposits</span>
                    </div>
                    <div class="w-40 text-right">
                        <span class="inline-block h-5 w-24 rounded font-mono">₱ {{ number_format($total_deposits, 2) }}</span>
                    </div>
                </div> --}}

                {{-- Total --}}
                {{-- <div class="px-6 py-5 flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-gray-900">Total</span>
                        <span class="text-xs text-gray-500">Expected cash on hand</span>
                    </div>
                    <div class="w-40 text-right">
                        <span class="inline-block h-6 w-28 rounded font-mono font-bold text-lg">₱ {{ number_format($total_transactions + $total_deposits, 2) }}</span>
                    </div>
                </div> --}}
                <div class="px-6 py-5 flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-sm font-semibold text-gray-900">Cash In</span>
                    <span class="text-xs text-gray-500">Input beginning cash on hand</span>
                </div>

                <div class="w-48">
                    <div class="relative">
                        
                        @if($previous_snapshot)
                        <div>
                            <span class="inline-block h-5 w-48 rounded font-mono">₱ {{ number_format($previous_snapshot->closing_cash, 2) }}</span>
                            <p class="text-xs text-gray-500">Previous shift closing cash</p>
                        </div>
                        @else
                         <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-semibold">
                            ₱
                        </span>
                        <input 
                            type="number"
                            step="0.01"
                            name="beginning_cash"
                            wire:model="beginning_cash"
                            class="w-full pl-7 pr-3 py-1.5 text-right border rounded-md font-mono text-lg font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="0.00"
                        >
                        @endif
                       
                    </div>
                    @error('beginning_cash') <span class="text-sm text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
            </div>

            {{-- Footer actions (bottom-right logout) --}}
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-end">
                  <button  x-on:confirm="{
                    title: 'Confirm',
                    description: 'Are you sure you want to start the shift with the entered beginning cash?',
                    icon: 'warning',
                    method: 'saveBeginningCash',
                    }"
                    type="button"
                        class="inline-flex items-center rounded-lg bg-yellow-600 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2"
                    >
                        Start Shift
            </button>
            </div>
        </div>
    </div>

</div>
