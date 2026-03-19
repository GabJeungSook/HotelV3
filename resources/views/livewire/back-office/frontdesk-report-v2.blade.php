<div class="max-w-5xl mx-auto px-4 py-8 space-y-6 bg-gray-100 min-h-screen text-gray-900">

    {{-- Shift Selector --}}
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Shift</label>
                <select wire:model="selectedShiftLogId"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- Select a completed shift --</option>
                    @foreach($availableShiftSessions as $session)
                        <option value="{{ $session['id'] }}">{{ $session['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button wire:click="generateReport" type="button"
                        class="inline-flex justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                    Refresh
                </button>
            </div>
        </div>
    </div>

    @if(!empty($reportData))
    {{-- Debug Data Output --}}
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 space-y-6">

        <h2 class="text-lg font-bold text-gray-900">DAILY SHIFT SUMMARY REPORT (V2 - Data Verification)</h2>

        {{-- Header --}}
        <div class="bg-gray-50 rounded-lg p-4 space-y-1 text-sm">
            <p><strong>Frontdesk (Outgoing):</strong> {{ $reportData['frontdesk_outgoing'] }}</p>
            <p><strong>Frontdesk (Incoming):</strong> {{ $reportData['frontdesk_incoming'] }}</p>
            <p><strong>Shift Opened:</strong> {{ $reportData['shift_opened'] }}</p>
            <p><strong>Shift Closed:</strong> {{ $reportData['shift_closed'] }}</p>
        </div>

        {{-- 1. Cash Drawer --}}
        <div>
            <h3 class="text-sm font-bold text-gray-700 mb-2">1. Cash Drawer</h3>
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 px-3 py-2 text-left">Description</th>
                        <th class="border border-gray-300 px-3 py-2 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-gray-300 px-3 py-2 font-medium">Opening Cash <span class="text-gray-500 text-xs">(Net Sales from previous shift)</span></td>
                        <td class="border border-gray-300 px-3 py-2 text-right">P {{ number_format($reportData['cash_drawer']['opening_cash'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-3 py-2 font-medium">Key/Remote Deposit <span class="text-gray-500 text-xs">(Room occupied receive)</span></td>
                        <td class="border border-gray-300 px-3 py-2 text-right">P {{ number_format($reportData['cash_drawer']['key_deposit'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-3 py-2 font-medium">Guest Deposit <span class="text-gray-500 text-xs">(Total guest other deposit received)</span></td>
                        <td class="border border-gray-300 px-3 py-2 text-right">P {{ number_format($reportData['cash_drawer']['guest_deposit'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-3 py-2 font-medium">Forwarding Balance <span class="text-gray-500 text-xs">(From previous shift)</span></td>
                        <td class="border border-gray-300 px-3 py-2 text-right">P {{ number_format($reportData['cash_drawer']['forwarding_balance'], 2) }}</td>
                    </tr>
                    <tr class="bg-gray-100 font-bold">
                        <td class="border border-gray-300 px-3 py-2">Total Cash Received</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">P {{ number_format($reportData['cash_drawer']['total'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- 2a. Sales Summary --}}
        <div>
            <h3 class="text-sm font-bold text-gray-700 mb-2">2a. Frontdesk Operation — Sales Summary</h3>
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 px-3 py-2 text-left">Description</th>
                        <th class="border border-gray-300 px-3 py-2 text-right">Number</th>
                        <th class="border border-gray-300 px-3 py-2 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach([
                        'new_checkin' => 'New Check-in',
                        'extension' => 'Extension',
                        'transfer' => 'Transfer',
                        'miscellaneous' => 'Miscellaneous',
                        'food' => 'Food',
                        'drink' => 'Drink',
                        'others' => 'Others',
                    ] as $key => $label)
                    <tr>
                        <td class="border border-gray-300 px-3 py-2 font-medium">
                            {{ $label }}
                            @if($key === 'new_checkin')
                                <span class="text-gray-500 text-xs">(Total New Check-in)</span>
                            @elseif($key === 'others')
                                <span class="text-gray-500 text-xs">(Foods and Drinks from POS)</span>
                            @endif
                        </td>
                        <td class="border border-gray-300 px-3 py-2 text-right">{{ $reportData['sales_summary'][$key]['count'] }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">P {{ number_format($reportData['sales_summary'][$key]['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="bg-gray-100 font-bold">
                        <td class="border border-gray-300 px-3 py-2">TOTAL</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">{{ $reportData['sales_summary']['total']['count'] }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">P {{ number_format($reportData['sales_summary']['total']['amount'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- 2b. Room Status and Deposit --}}
        <div>
            <h3 class="text-sm font-bold text-gray-700 mb-2">2b. Room Status and Deposit</h3>
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 px-3 py-2 text-left">Description</th>
                        <th class="border border-gray-300 px-3 py-2 text-right">Number</th>
                        <th class="border border-gray-300 px-3 py-2 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach([
                        'forwarded_room' => 'Forwarded Room Check-In (Previous Shift)',
                        'key_remote_deposit' => 'Key/Remote Deposit (End of shift occupied)',
                        'forwarded_guest_deposit' => 'Forwarded Room Guest Deposit',
                        'guest_deposit' => 'Guest Deposit (Current shift)',
                        'total_checkout' => 'Total Check-Out',
                        'expenses' => 'Expenses',
                    ] as $key => $label)
                    <tr>
                        <td class="border border-gray-300 px-3 py-2 font-medium">{{ $label }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">{{ $reportData['room_status'][$key]['count'] }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">P {{ number_format($reportData['room_status'][$key]['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- 3. Final Sales --}}
        <div>
            <h3 class="text-sm font-bold text-gray-700 mb-2">3. Final Sales</h3>
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 px-3 py-2 text-left">Description</th>
                        <th class="border border-gray-300 px-3 py-2 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-gray-300 px-3 py-2 font-medium">Gross Sales</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">P {{ number_format($reportData['final_sales']['gross_sales'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-3 py-2 font-medium">Refund</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">P {{ number_format($reportData['final_sales']['refund'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-3 py-2 font-medium">Expenses</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">P {{ number_format($reportData['final_sales']['expenses'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-3 py-2 font-medium">Discounts</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">P {{ number_format($reportData['final_sales']['discounts'], 2) }}</td>
                    </tr>
                    <tr class="bg-gray-100 font-bold">
                        <td class="border border-gray-300 px-3 py-2">Net Sales</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">P {{ number_format($reportData['final_sales']['net_sales'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- 4. Cash Position Summary --}}
        <div>
            <h3 class="text-sm font-bold text-gray-700 mb-2">4. Cash Position Summary</h3>
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 px-3 py-2 text-left">Description</th>
                        <th class="border border-gray-300 px-3 py-2 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-gray-300 px-3 py-2 font-medium">Opening Cash</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">P {{ number_format($reportData['cash_position']['opening_cash'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-3 py-2 font-medium">Forwarded Balance</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">P {{ number_format($reportData['cash_position']['forwarded_balance'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-3 py-2 font-medium">Net Sales</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">P {{ number_format($reportData['cash_position']['net_sales'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-3 py-2 font-medium">Remittance</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">P {{ number_format($reportData['cash_position']['remittance'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- 5. Cash Reconciliation --}}
        <div>
            <h3 class="text-sm font-bold text-gray-700 mb-2">5. Cash Reconciliation</h3>
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 px-3 py-2 text-left">Description</th>
                        <th class="border border-gray-300 px-3 py-2 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-gray-300 px-3 py-2 font-medium">Expected Cash</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">P {{ number_format($reportData['cash_reconciliation']['expected_cash'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-3 py-2 font-medium">Actual Cash</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">P {{ number_format($reportData['cash_reconciliation']['actual_cash'], 2) }}</td>
                    </tr>
                    <tr class="text-red-600 font-bold">
                        <td class="border border-gray-300 px-3 py-2">Difference</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">P {{ number_format($reportData['cash_reconciliation']['difference'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @else
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 text-center text-gray-500">
            Select a shift to generate the report.
        </div>
    @endif
</div>
