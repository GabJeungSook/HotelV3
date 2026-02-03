<div>
      <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Beginning Cash ({{ auth()->user()->cash_drawer->name ?? 'No Drawer' }})</h1>
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
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-end">
                  <a href="{{ route('frontdesk.room-monitoring') }}"
                        class="inline-flex items-center rounded-lg bg-yellow-600 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2"
                    >
                        Start Shift
            </a>
            </div>
        </div>
    </div>

</div>
