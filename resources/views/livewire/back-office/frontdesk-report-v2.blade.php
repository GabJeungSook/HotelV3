<div class="max-w-7xl mx-auto px-4 py-8 space-y-6 bg-gray-100 min-h-screen text-gray-900">
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #daily-shift-summary-v2,
            #daily-shift-summary-v2 * {
                visibility: visible;
            }

            #daily-shift-summary-v2 {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>

    {{-- Filters --}}
    <div class="no-print bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Shift</label>
                <select wire:model="selectedShiftLogId"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- Select a completed shift --</option>
                    @foreach($availableShiftSessions as $session)
                        <option value="{{ $session['id'] }}">{{ $session['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button wire:click="generateReport" type="button"
                        class="inline-flex justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                    Refresh
                </button>
                <button type="button" onclick="window.print()"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print
                </button>
            </div>
        </div>
    </div>

    @if(!empty($reportData))
    {{-- Report --}}
    <div class="bg-white shadow-sm ring-1 ring-gray-300 p-8">
        <div id="daily-shift-summary-v2" class="mx-auto max-w-full text-[16px] leading-tight text-black space-y-8">
            <h1 class="text-center font-bold uppercase">DAILY SHIFT SUMMARY REPORT</h1>

            <div class="space-y-1 text-[15px]">
                <p>Frontdesk (Outgoing): {{ $reportData['frontdesk_outgoing'] }}</p>
                <p>Frontdesk (Incoming): {{ $reportData['frontdesk_incoming'] }}</p>
                <p>Shift Opened: {{ $reportData['shift_opened'] }}</p>
                <p>Shift Closed: {{ $reportData['shift_closed'] }}</p>
            </div>

            {{-- 1. Cash Drawer --}}
            <div>
                <div class="mb-2 text-[15px]">1.&nbsp;&nbsp;&nbsp;&nbsp;Cash Drawer</div>
                <table class="w-full border-collapse table-fixed border border-black text-[15px]">
                    <thead>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[39%]">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[27%]">Amount</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[34%]">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1 align-top">
                                <div class="font-bold">Opening Cash</div>
                                <div>(Net Sales Receive from previous Shift)</div>
                            </td>
                            <td class="border border-black px-2 py-1 align-top">{{ $reportData['cash_drawer']['opening_cash'] > 0 ? '₱ ' . number_format($reportData['cash_drawer']['opening_cash'], 2) : '-' }}</td>
                            <td class="border border-black px-2 py-1 align-top">-</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 align-top">
                                <div class="font-bold">Key Deposit</div>
                                <div>(Room occupied receive)</div>
                            </td>
                            <td class="border border-black px-2 py-1 align-top">{{ $reportData['cash_drawer']['key_deposit'] > 0 ? '₱ ' . number_format($reportData['cash_drawer']['key_deposit'], 2) : '-' }}</td>
                            <td class="border border-black px-2 py-1 align-top">-</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 align-top">
                                <div class="font-bold">Guest Deposit</div>
                                <div>(Total client other deposit received)</div>
                            </td>
                            <td class="border border-black px-2 py-1 align-top">{{ $reportData['cash_drawer']['guest_deposit'] > 0 ? '₱ ' . number_format($reportData['cash_drawer']['guest_deposit'], 2) : '-' }}</td>
                            <td class="border border-black px-2 py-1 align-top">-</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 align-top">
                                <div class="font-bold">Forwarding Balance</div>
                                <div>(From previous Shift)</div>
                            </td>
                            <td class="border border-black px-2 py-1 align-top">{{ $reportData['cash_drawer']['forwarding_balance'] > 0 ? '₱ ' . number_format($reportData['cash_drawer']['forwarding_balance'], 2) : '-' }}</td>
                            <td class="border border-black px-2 py-1 align-top">-</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Total Cash Received</td>
                            <td class="border border-black px-2 py-1 font-bold">{{ $reportData['cash_drawer']['total'] > 0 ? '₱ ' . number_format($reportData['cash_drawer']['total'], 2) : '-' }}</td>
                            <td class="border border-black px-2 py-1"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 2. Frontdesk Operation --}}
            <div>
                <div class="mb-2 text-[15px]">2.&nbsp;&nbsp;&nbsp;&nbsp;Frontdesk Operation</div>

                {{-- 2a. Sales Summary --}}
                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;a.&nbsp;&nbsp;&nbsp;&nbsp;Sales Summary</span>
                <table class="w-full border-collapse table-fixed border border-black text-[15px] mt-3 mb-5">
                    <thead>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[50%]">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[16%]">Number</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[34%]">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach([
                            'new_checkin' => ['label' => 'New Check-in', 'sub' => 'Total New Check-in'],
                            'extension' => ['label' => 'Extension', 'sub' => null],
                            'transfer' => ['label' => 'Transfer', 'sub' => null],
                            'miscellaneous' => ['label' => 'Miscellaneous charges', 'sub' => null],
                            'food' => ['label' => 'Food', 'sub' => null],
                            'drink' => ['label' => 'Drink', 'sub' => null],
                            'others' => ['label' => 'Others', 'sub' => 'Foods and Drinks from POS'],
                        ] as $key => $info)
                        <tr>
                            <td class="border border-black px-2 py-1">
                                <div class="font-bold">{{ $info['label'] }}</div>
                                @if($info['sub'])
                                <div class="font-normal">({{ $info['sub'] }})</div>
                                @endif
                            </td>
                            <td class="border border-black px-2 py-1">{{ $reportData['sales_summary'][$key]['count'] ?: '-' }}</td>
                            <td class="border border-black px-2 py-1 font-bold">{{ $reportData['sales_summary'][$key]['amount'] > 0 ? '₱ ' . number_format($reportData['sales_summary'][$key]['amount'], 2) : '-' }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Total</td>
                            <td class="border border-black px-2 py-1 font-bold">{{ $reportData['sales_summary']['total']['count'] ?: '-' }}</td>
                            <td class="border border-black px-2 py-1 font-bold">{{ $reportData['sales_summary']['total']['amount'] > 0 ? '₱ ' . number_format($reportData['sales_summary']['total']['amount'], 2) : '-' }}</td>
                        </tr>
                    </tbody>
                </table>

                {{-- 2b. Room Status and Deposit --}}
                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;b.&nbsp;&nbsp;&nbsp;&nbsp;Room Status and Deposit</span>
                <table class="w-full border-collapse table-fixed border border-black text-[15px] mt-3 mb-8">
                    <thead>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[50%]">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[16%]">Number</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[34%]">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach([
                            'forwarded_room' => ['label' => 'Forwarded Room Check-In', 'sub' => 'Previous Shift'],
                            'key_remote_deposit' => ['label' => 'Key/Remote Deposit', 'sub' => 'Current room occupied at the end shift'],
                            'forwarded_guest_deposit' => ['label' => 'Forwarded Room Guest Deposit', 'sub' => 'Previous Shift'],
                            'guest_deposit' => ['label' => 'Guest Deposit', 'sub' => null],
                            'total_checkout' => ['label' => 'Total Check-Out', 'sub' => null],
                            'expenses' => ['label' => 'Expenses', 'sub' => null],
                        ] as $key => $info)
                        <tr>
                            <td class="border border-black px-2 py-1">
                                <div class="font-bold">{{ $info['label'] }}</div>
                                @if($info['sub'])
                                <div class="font-normal">({{ $info['sub'] }})</div>
                                @endif
                            </td>
                            <td class="border border-black px-2 py-1">{{ $reportData['room_status'][$key]['count'] ?: '-' }}</td>
                            <td class="border border-black px-2 py-1 font-bold">{{ $reportData['room_status'][$key]['amount'] > 0 ? '₱ ' . number_format($reportData['room_status'][$key]['amount'], 2) : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- 3. Final Sales --}}
            <div>
                <div class="mb-2 text-[15px]">3.&nbsp;&nbsp;&nbsp;&nbsp;Final Sales</div>
                <table class="w-full border-collapse table-fixed border border-black text-[15px]">
                    <thead>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[55%]">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[45%]" colspan="2">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Gross Sales</td>
                            <td class="border border-black px-2 py-1" colspan="2">{{ $reportData['final_sales']['gross_sales'] > 0 ? '₱ ' . number_format($reportData['final_sales']['gross_sales'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Refund</td>
                            <td class="border border-black px-2 py-1" colspan="2">{{ $reportData['final_sales']['refund'] > 0 ? '₱ ' . number_format($reportData['final_sales']['refund'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Expenses</td>
                            <td class="border border-black px-2 py-1" colspan="2">{{ $reportData['final_sales']['expenses'] > 0 ? '₱ ' . number_format($reportData['final_sales']['expenses'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Discounts</td>
                            <td class="border border-black px-2 py-1" colspan="2">{{ $reportData['final_sales']['discounts'] > 0 ? '₱ ' . number_format($reportData['final_sales']['discounts'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Net Sales</td>
                            <td class="border border-black px-2 py-1 font-bold" colspan="2">{{ $reportData['final_sales']['net_sales'] != 0 ? '₱ ' . number_format($reportData['final_sales']['net_sales'], 2) : '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 4. Cash Position Summary --}}
            <div>
                <div class="mb-2 text-[15px]">4.&nbsp;&nbsp;&nbsp;&nbsp;Cash Position Summary</div>
                <table class="w-full border-collapse table-fixed border border-black text-[15px]">
                    <thead>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[55%]">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[45%]" colspan="2">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Opening Cash</td>
                            <td class="border border-black px-2 py-1" colspan="2">{{ $reportData['cash_position']['opening_cash'] > 0 ? '₱ ' . number_format($reportData['cash_position']['opening_cash'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Forwarded Balance</td>
                            <td class="border border-black px-2 py-1" colspan="2">{{ $reportData['cash_position']['forwarded_balance'] > 0 ? '₱ ' . number_format($reportData['cash_position']['forwarded_balance'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Net Sales</td>
                            <td class="border border-black px-2 py-1" colspan="2">{{ $reportData['cash_position']['net_sales'] != 0 ? '₱ ' . number_format($reportData['cash_position']['net_sales'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Remittance</td>
                            <td class="border border-black px-2 py-1" colspan="2">{{ $reportData['cash_position']['remittance'] > 0 ? '₱ ' . number_format($reportData['cash_position']['remittance'], 2) : '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 5. Cash Reconciliation --}}
            <div>
                <div class="mb-2 text-[15px]">5.&nbsp;&nbsp;&nbsp;&nbsp;Cash Reconciliation</div>
                <table class="w-full border-collapse table-fixed border border-black text-[15px]">
                    <thead>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[55%]">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[45%]" colspan="2">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Expected Cash</td>
                            <td class="border border-black px-2 py-1" colspan="2">{{ $reportData['cash_reconciliation']['expected_cash'] != 0 ? '₱ ' . number_format($reportData['cash_reconciliation']['expected_cash'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Actual Cash</td>
                            <td class="border border-black px-2 py-1" colspan="2">{{ $reportData['cash_reconciliation']['actual_cash'] > 0 ? '₱ ' . number_format($reportData['cash_reconciliation']['actual_cash'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold text-red-600">Difference</td>
                            <td class="border border-black px-2 py-1 text-red-600" colspan="2">{{ $reportData['cash_reconciliation']['difference'] != 0 ? '₱ ' . number_format($reportData['cash_reconciliation']['difference'], 2) : '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Signature --}}
            <div class="pt-16 pl-2">
                <div class="text-[15px] mb-16">Certified and Correct:</div>
                <div class="w-64 border-b mt-10 border-black"></div>
            </div>
        </div>
    </div>
    @else
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 text-center text-gray-500">
            Select a shift to generate the report.
        </div>
    @endif
</div>
