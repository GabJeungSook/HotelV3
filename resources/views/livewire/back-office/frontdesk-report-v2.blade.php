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
                            <th class="border border-black px-2 py-1 text-left font-bold w-[50%]">Description</th>
                            <th class="border border-black px-2 py-1 text-right font-bold w-[50%]">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1 align-top">
                                <div class="font-bold">Net Sales Received</div>
                                <div>(From Previous Shift)</div>
                            </td>
                            <td class="border border-black px-2 py-1 align-top text-right">{{ $reportData['cash_drawer']['net_sales_prev'] > 0 ? '₱ ' . number_format($reportData['cash_drawer']['net_sales_prev'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 align-top">
                                <div class="font-bold">Key Deposit</div>
                                <div>(From Previous Shift)</div>
                            </td>
                            <td class="border border-black px-2 py-1 align-top text-right">{{ $reportData['cash_drawer']['key_deposit_prev'] > 0 ? '₱ ' . number_format($reportData['cash_drawer']['key_deposit_prev'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 align-top">
                                <div class="font-bold">Guest Deposit</div>
                                <div>(From Previous Shift)</div>
                            </td>
                            <td class="border border-black px-2 py-1 align-top text-right">{{ $reportData['cash_drawer']['guest_deposit_prev'] > 0 ? '₱ ' . number_format($reportData['cash_drawer']['guest_deposit_prev'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 align-top">
                                <div class="font-bold">Forwarded Balance</div>
                                <div>(From Previous Shift)</div>
                            </td>
                            <td class="border border-black px-2 py-1 align-top text-right">{{ $reportData['cash_drawer']['forwarded_balance'] > 0 ? '₱ ' . number_format($reportData['cash_drawer']['forwarded_balance'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 align-top font-bold">Cash Received</td>
                            <td class="border border-black px-2 py-1 align-top text-right">
                                ₱ {{ number_format($reportData['cash_drawer']['cash_received'], 2) }}
                                @if($reportData['cash_drawer']['has_previous'] && $reportData['cash_drawer']['cash_difference'] > 0)
                                    <span class="text-red-600 font-bold">(₱{{ number_format($reportData['cash_drawer']['cash_difference'], 2) }})</span>
                                @elseif($reportData['cash_drawer']['has_previous'] && $reportData['cash_drawer']['cash_difference'] < 0)
                                    <span class="text-green-600 font-bold">(₱{{ number_format(abs($reportData['cash_drawer']['cash_difference']), 2) }})</span>
                                @endif
                            </td>
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
                            <th class="border border-black px-2 py-1 text-right font-bold w-[34%]">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach([
                            'new_checkin' => ['label' => 'New Check-in', 'sub' => 'Total New Check-in'],
                            'extension' => ['label' => 'Extension', 'sub' => null],
                            'transfer' => ['label' => 'Transfer', 'sub' => null],
                        ] as $key => $info)
                        <tr>
                            <td class="border border-black px-2 py-1">
                                <div class="font-bold">{{ $info['label'] }}</div>
                                @if($info['sub'])
                                <div class="font-normal">({{ $info['sub'] }})</div>
                                @endif
                            </td>
                            <td class="border border-black px-2 py-1">{{ $reportData['sales_summary'][$key]['count'] ?: '-' }}</td>
                            <td class="border border-black px-2 py-1 font-bold text-right">{{ $reportData['sales_summary'][$key]['amount'] > 0 ? '₱ ' . number_format($reportData['sales_summary'][$key]['amount'], 2) : '-' }}</td>
                        </tr>
                        @endforeach
                        {{-- Miscellaneous breakdown --}}
                        @php $misc = $reportData['sales_summary']['miscellaneous']; @endphp
                        <tr>
                            <td class="border border-black px-2 py-1 align-top font-bold" rowspan="4">Miscellaneous Charges</td>
                            <td class="border border-black px-2 py-1">{{ $misc['breakdown']['amenities']['count'] ?: '-' }}</td>
                            <td class="border border-black px-2 py-1">
                                <div class="flex justify-between">
                                    <span>Amenities</span>
                                    <span class="font-bold">{{ $misc['breakdown']['amenities']['amount'] > 0 ? '₱ ' . number_format($misc['breakdown']['amenities']['amount'], 2) : '-' }}</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1">{{ $misc['breakdown']['damages']['count'] ?: '-' }}</td>
                            <td class="border border-black px-2 py-1">
                                <div class="flex justify-between">
                                    <span>Damages</span>
                                    <span class="font-bold">{{ $misc['breakdown']['damages']['amount'] > 0 ? '₱ ' . number_format($misc['breakdown']['damages']['amount'], 2) : '-' }}</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1">{{ $misc['breakdown']['unclaimed']['count'] ?: '-' }}</td>
                            <td class="border border-black px-2 py-1">
                                <div class="flex justify-between">
                                    <span>Unclaimed Deposit</span>
                                    <span class="font-bold">{{ $misc['breakdown']['unclaimed']['amount'] > 0 ? '₱ ' . number_format($misc['breakdown']['unclaimed']['amount'], 2) : '-' }}</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">{{ $misc['count'] ?: '-' }}</td>
                            <td class="border border-black px-2 py-1 font-bold text-right">{{ $misc['amount'] > 0 ? '₱ ' . number_format($misc['amount'], 2) : '-' }}</td>
                        </tr>
                        @foreach([
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
                            <td class="border border-black px-2 py-1 font-bold text-right">{{ $reportData['sales_summary'][$key]['amount'] > 0 ? '₱ ' . number_format($reportData['sales_summary'][$key]['amount'], 2) : '-' }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Total</td>
                            <td class="border border-black px-2 py-1 font-bold">{{ $reportData['sales_summary']['total']['count'] ?: '-' }}</td>
                            <td class="border border-black px-2 py-1 font-bold text-right">{{ $reportData['sales_summary']['total']['amount'] > 0 ? '₱ ' . number_format($reportData['sales_summary']['total']['amount'], 2) : '-' }}</td>
                        </tr>
                    </tbody>
                </table>

                {{-- 2b. Room Summary --}}
                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;b.&nbsp;&nbsp;&nbsp;&nbsp;Room Summary</span>
                <table class="w-full border-collapse table-fixed border border-black text-[15px] mt-3 mb-8">
                    <thead>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[50%]">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[16%]">Number</th>
                            <th class="border border-black px-2 py-1 text-right font-bold w-[34%]">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1">
                                <div class="font-bold">Rooms Forwarded</div>
                                <div>(From Previous Shift)</div>
                            </td>
                            <td class="border border-black px-2 py-1">{{ $reportData['room_summary']['forwarded_prev']['count'] ?: '-' }}</td>
                            <td class="border border-black px-2 py-1 font-bold text-right">{{ $reportData['room_summary']['forwarded_prev']['amount'] > 0 ? '₱ ' . number_format($reportData['room_summary']['forwarded_prev']['amount'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1">
                                <div class="font-bold">Room Total</div>
                                <div>(Current Shift)</div>
                            </td>
                            <td class="border border-black px-2 py-1">{{ $reportData['room_summary']['current_shift']['count'] ?: '-' }}</td>
                            <td class="border border-black px-2 py-1 font-bold text-right">{{ $reportData['room_summary']['current_shift']['amount'] > 0 ? '₱ ' . number_format($reportData['room_summary']['current_shift']['amount'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Subtotal</td>
                            <td class="border border-black px-2 py-1 font-bold">{{ $reportData['room_summary_subtotal']['count'] ?: '-' }}</td>
                            <td class="border border-black px-2 py-1 font-bold text-right">{{ $reportData['room_summary_subtotal']['amount'] > 0 ? '₱ ' . number_format($reportData['room_summary_subtotal']['amount'], 2) : '-' }}</td>
                        </tr>
                    </tbody>
                </table>

                {{-- 2c. Guest Deposit Summary --}}
                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;c.&nbsp;&nbsp;&nbsp;&nbsp;Guest Deposit Summary</span>
                <table class="w-full border-collapse table-fixed border border-black text-[15px] mt-3 mb-5">
                    <thead>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[50%]">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[16%]">Number</th>
                            <th class="border border-black px-2 py-1 text-right font-bold w-[34%]">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1">
                                <div class="font-bold">Guest Forwarded Deposit</div>
                                <div>(From Previous Shift)</div>
                            </td>
                            <td class="border border-black px-2 py-1">{{ $reportData['guest_deposit_summary']['forwarded_prev']['count'] ?: '-' }}</td>
                            <td class="border border-black px-2 py-1 font-bold text-right">{{ $reportData['guest_deposit_summary']['forwarded_prev']['amount'] > 0 ? '₱ ' . number_format($reportData['guest_deposit_summary']['forwarded_prev']['amount'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1">
                                <div class="font-bold">Guest Total Deposit</div>
                                <div>(Current Shift)</div>
                            </td>
                            <td class="border border-black px-2 py-1">{{ $reportData['guest_deposit_summary']['current_shift']['count'] ?: '-' }}</td>
                            <td class="border border-black px-2 py-1 font-bold text-right">{{ $reportData['guest_deposit_summary']['current_shift']['amount'] > 0 ? '₱ ' . number_format($reportData['guest_deposit_summary']['current_shift']['amount'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Subtotal</td>
                            <td class="border border-black px-2 py-1 font-bold">{{ $reportData['guest_deposit_subtotal']['count'] ?: '-' }}</td>
                            <td class="border border-black px-2 py-1 font-bold text-right">{{ $reportData['guest_deposit_subtotal']['amount'] > 0 ? '₱ ' . number_format($reportData['guest_deposit_subtotal']['amount'], 2) : '-' }}</td>
                        </tr>
                    </tbody>
                </table>

                {{-- 2d. Check-out Summary --}}
                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;d.&nbsp;&nbsp;&nbsp;&nbsp;Check-out Summary</span>
                <table class="w-full border-collapse table-fixed border border-black text-[15px] mt-3 mb-5">
                    <thead>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[50%]">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[16%]">Number</th>
                            <th class="border border-black px-2 py-1 text-right font-bold w-[34%]">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1">
                                <div class="font-bold">Total Check-out</div>
                                <div>(Current Shift)</div>
                            </td>
                            <td class="border border-black px-2 py-1">{{ $reportData['checkout_summary']['count'] ?: '-' }}</td>
                            <td class="border border-black px-2 py-1 font-bold text-right">{{ $reportData['checkout_summary']['amount'] > 0 ? '₱ ' . number_format($reportData['checkout_summary']['amount'], 2) : '-' }}</td>
                        </tr>
                    </tbody>
                </table>

                {{-- 2e. Forwarded Deposit Summary --}}
                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;e.&nbsp;&nbsp;&nbsp;&nbsp;Forwarded Deposit Summary</span>
                <table class="w-full border-collapse table-fixed border border-black text-[15px] mt-3 mb-8">
                    <thead>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[50%]">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[16%]">Number</th>
                            <th class="border border-black px-2 py-1 text-right font-bold w-[34%]">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1">
                                <div class="font-bold">Total Room Deposit</div>
                                <div>(At End of Shift)</div>
                            </td>
                            <td class="border border-black px-2 py-1">{{ $reportData['forwarded_deposit_summary']['room_deposit']['count'] ?: '-' }}</td>
                            <td class="border border-black px-2 py-1 font-bold text-right">{{ $reportData['forwarded_deposit_summary']['room_deposit']['amount'] > 0 ? '₱ ' . number_format($reportData['forwarded_deposit_summary']['room_deposit']['amount'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1">
                                <div class="font-bold">Total Guest Deposit</div>
                                <div>(At End of Shift)</div>
                            </td>
                            <td class="border border-black px-2 py-1">{{ $reportData['forwarded_deposit_summary']['guest_deposit']['count'] ?: '-' }}</td>
                            <td class="border border-black px-2 py-1 font-bold text-right">{{ $reportData['forwarded_deposit_summary']['guest_deposit']['amount'] > 0 ? '₱ ' . number_format($reportData['forwarded_deposit_summary']['guest_deposit']['amount'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Subtotal</td>
                            <td class="border border-black px-2 py-1 font-bold">{{ ($reportData['forwarded_deposit_summary']['room_deposit']['count'] + $reportData['forwarded_deposit_summary']['guest_deposit']['count']) ?: '-' }}</td>
                            <td class="border border-black px-2 py-1 font-bold text-right">{{ ($reportData['forwarded_deposit_summary']['room_deposit']['amount'] + $reportData['forwarded_deposit_summary']['guest_deposit']['amount']) > 0 ? '₱ ' . number_format($reportData['forwarded_deposit_summary']['room_deposit']['amount'] + $reportData['forwarded_deposit_summary']['guest_deposit']['amount'], 2) : '-' }}</td>
                        </tr>
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
                            <th class="border border-black px-2 py-1 text-right font-bold w-[45%]" colspan="2">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Gross Sales</td>
                            <td class="border border-black px-2 py-1 text-right" colspan="2">{{ $reportData['final_sales']['gross_sales'] > 0 ? '₱ ' . number_format($reportData['final_sales']['gross_sales'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Refund</td>
                            <td class="border border-black px-2 py-1 text-right" colspan="2">{{ $reportData['final_sales']['refund'] > 0 ? '₱ ' . number_format($reportData['final_sales']['refund'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Expenses</td>
                            <td class="border border-black px-2 py-1 text-right" colspan="2">{{ $reportData['final_sales']['expenses'] > 0 ? '₱ ' . number_format($reportData['final_sales']['expenses'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Discounts</td>
                            <td class="border border-black px-2 py-1 text-right" colspan="2">{{ $reportData['final_sales']['discounts'] > 0 ? '₱ ' . number_format($reportData['final_sales']['discounts'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Net Sales</td>
                            <td class="border border-black px-2 py-1 font-bold text-right" colspan="2">{{ $reportData['final_sales']['net_sales'] != 0 ? '₱ ' . number_format($reportData['final_sales']['net_sales'], 2) : '-' }}</td>
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
                            <th class="border border-black px-2 py-1 text-right font-bold w-[45%]" colspan="2">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1">
                                <div class="font-bold">Net Sales Received</div>
                                <div>(From Previous Shift)</div>
                            </td>
                            <td class="border border-black px-2 py-1 text-right" colspan="2">{{ $reportData['cash_drawer']['net_sales_prev'] > 0 ? '₱ ' . number_format($reportData['cash_drawer']['net_sales_prev'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1">
                                <div class="font-bold">Net Sales Received</div>
                                <div>(From Current Shift)</div>
                            </td>
                            <td class="border border-black px-2 py-1 text-right" colspan="2">{{ $reportData['final_sales']['net_sales'] != 0 ? '₱ ' . number_format($reportData['final_sales']['net_sales'], 2) : '-' }}</td>
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
                            <th class="border border-black px-2 py-1 text-right font-bold w-[45%]" colspan="2">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Expected Cash</td>
                            <td class="border border-black px-2 py-1 text-right" colspan="2">{{ $reportData['cash_reconciliation']['expected_cash'] != 0 ? '₱ ' . number_format($reportData['cash_reconciliation']['expected_cash'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Actual Cash</td>
                            <td class="border border-black px-2 py-1 text-right" colspan="2">{{ $reportData['cash_reconciliation']['actual_cash'] > 0 ? '₱ ' . number_format($reportData['cash_reconciliation']['actual_cash'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold text-red-600">Difference</td>
                            <td class="border border-black px-2 py-1 text-red-600 text-right" colspan="2">{{ $reportData['cash_reconciliation']['difference'] != 0 ? '₱ ' . number_format($reportData['cash_reconciliation']['difference'], 2) : '-' }}</td>
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
