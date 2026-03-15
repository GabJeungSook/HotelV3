@php($shift = $this->selectedShift)

<div class="max-w-7xl mx-auto px-4 py-8 space-y-6 bg-gray-100 min-h-screen text-gray-900">
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #daily-shift-summary,
            #daily-shift-summary * {
                visibility: visible;
            }

            #daily-shift-summary {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>

    <!-- Filters -->
    <div class="no-print bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="flex flex-col gap-3">
                <select hidden class="w-full rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option>All</option>
                    <option>Hanah</option>
                    <option>Kathleen</option>
                    <option>Jeneath</option>
                    <option>Ruby Gold</option>
                </select>

                <div class="flex items-end gap-2">
                    <a href="{{ asset($shift['raw_file']) }}"
                       download
                       class="w-full md:w-auto inline-flex items-center gap-2 justify-center rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 16v-8m0 8l-3-3m3 3l3-3M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2"/>
                        </svg>
                        Download Raw File
                    </a>

                    <button type="button" onclick="window.print()" class="w-full md:w-auto inline-flex justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                        Print
                    </button>
                </div>
            </div>

            <div>
                <input hidden disabled type="date" class="w-full rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
            </div>

            <div>
                <input hidden disabled type="date" class="w-full rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
            </div>

            <div class="flex flex-col gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Shift</label>
                    <select wire:model="filter" class="w-full rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($shifts as $key => $item)
                            <option value="{{ $key }}">{{ $item['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    {{-- <button hidden wire:click="changeFilter" type="button" class="w-full md:w-auto inline-flex justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                        Apply
                    </button>

                    <button hidden type="button" class="w-full md:w-auto inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Reset
                    </button> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Straight layout report -->
    <div class="bg-white shadow-sm ring-1 ring-gray-300 p-8">
<div id="daily-shift-summary" class="mx-auto max-w-full text-[16px] leading-tight text-black space-y-8">            <h1 class="text-center font-bold uppercase">DAILY SHIFT SUMMARY REPORT</h1>

            <div class="space-y-1 text-[15px]">
                <p>Frontdesk (Outgoing): {{ $shift['frontdesk_outgoing'] }}</p>
                <p>Frontdesk (Incoming): {{ $shift['frontdesk_incoming'] }}</p>
                <p>Shift Opened: {{ $shift['shift_opened'] }}</p>
                <p>Shift Closed: {{ $shift['shift_closed'] }}</p>
            </div>

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
                        @foreach($shift['cash_drawer'] as $row)
                        <tr>
                            <td class="border border-black px-2 py-1 align-top">
                                <div class="font-bold">{{ $row['description'] }}</div>
                                {{-- @if($row['sub'])
                                <div>{{ $row['sub'] }}</div>
                                @endif --}}
                            </td>
                            <td class="border border-black px-2 py-1 align-top @if($row['is_bold'] ?? false) font-bold @endif">{!! $row['amount'] !!}</td>
                            <td class="border border-black px-2 py-1 align-top">{!! $row['remark'] !!}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Total Cash Received</td>
                            <td class="border border-black px-2 py-1 @if($shift['cash_drawer_total']['is_bold'] ?? false) font-bold @endif">{!! $shift['cash_drawer_total']['amount'] !!}</td>
                            <td class="border border-black px-2 py-1">{!! $shift['cash_drawer_total']['remark'] !!}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div>
                <div class="mb-2 text-[15px]">2.&nbsp;&nbsp;&nbsp;&nbsp;Frontdesk Operation</div>
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
                        @foreach($shift['frontdesk_operation_a'] as $row)
                        <tr>
                            <td class="border border-black px-2 py-1  font-bold">
                                <div class="font-bold">{{ $row['description'] }}</div>
                                {{-- @if($row['sub'])
                                <div>{{ $row['sub'] }}</div>
                                @endif --}}
                            </td>
                            <td class="border border-black px-2 py-1 ">{!! $row['number'] !!}</td>
                            <td class="border border-black px-2 py-1 font-bold">{!! $row['amount'] !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Room status and deposit --}}
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
                        @foreach($shift['frontdesk_operation_b'] as $row)
                        <tr>
                            <td class="border border-black px-2 py-1  font-bold">
                                <div class="font-bold">{{ $row['description'] }}</div>
                                {{-- @if($row['sub'])
                                <div>{{ $row['sub'] }}</div>
                                @endif --}}
                            </td>
                            <td class="border border-black px-2 py-1 ">{!! $row['number'] !!}</td>
                            <td class="border border-black px-2 py-1 font-bold">{!! $row['amount'] !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- final sales --}}
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
                            <td class="border border-black px-2 py-1" colspan="2">{{ $shift['final_sales']['gross_sales'] }}</td>
                        </tr>
                        
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Refund</td>
                            <td class="border border-black px-2 py-1" colspan="2">{{ $shift['final_sales']['refund'] }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Expenses</td>
                            <td class="border border-black px-2 py-1" colspan="2">{{ $shift['final_sales']['expenses'] }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Discounts</td>
                            <td class="border border-black px-2 py-1" colspan="2">{{ $shift['final_sales']['discounts'] }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Net Sales</td>
                            <td class="border border-black px-2 py-1 font-bold" colspan="2">{{ $shift['final_sales']['net_sales'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Cash Position Summary --}}
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
                            <td class="border border-black px-2 py-1" colspan="2">{{ $shift['cash_position_summary']['opening_cash'] }}</td>
                        </tr>
                        
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Forwarded Balance</td>
                            <td class="border border-black px-2 py-1" colspan="2">{{ $shift['cash_position_summary']['forwarded_balance'] }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Net Sales</td>
                            <td class="border border-black px-2 py-1" colspan="2">{{ $shift['cash_position_summary']['net_sales'] }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Remittance</td>
                            <td class="border border-black px-2 py-1" colspan="2">{{ $shift['cash_position_summary']['remittance'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div>
                <div class="mb-2 text-[15px]">3.&nbsp;&nbsp;&nbsp;&nbsp;Cash reconciliation</div>
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
                            <td class="border border-black px-2 py-1" colspan="2">{{ $shift['cash_reconciliation']['expected_cash'] }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Actual Cash</td>
                            <td class="border border-black px-2 py-1" colspan="2">{{ $shift['cash_reconciliation']['actual_cash'] }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold text-red-600">Difference</td>
                            <td class="border border-black px-2 py-1 text-red-600" colspan="2">{{ $shift['cash_reconciliation']['difference'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- <div class="pt-4 pl-2">
                <div class="text-[15px] font-bold mb-2">(Forwarding Computation)</div>
                <div class="text-[15px] font-bold mb-2">Expected Cash computation: </div>
                <div class="text-[15px] font-normal mb-2">(Opening Cash + New Check-in + Extension, Transfer Room, Miscellaneous charges, Food, Drinks, Key Remote Deposit, Guest Deposit) - (Expenses and Remittance)</div>
            </div>
            <div class="pt-4 pl-2">
                <div class="text-[15px] font-bold mb-2">(Backforwarding Computation)</div>
                <div class="text-[15px] font-bold mb-2">Expected Cash computation: </div>
                <div class="text-[15px] font-normal mb-2">(Netsales + Guest Deposit+  Remote Key Deposit + Opening Cash) - (Expenses, and Remittance)</div>
            </div>
             <div class="pt-4 pl-2">
                <div class="text-[15px] mb-2"><span class="font-bold italic">Note:</span> <span>(Perform forward and backward computations to countercheck and ensure that the results are correct.)
                </span> </div>
            </div> --}}

            <div class="pt-16 pl-2">
                <div class="text-[15px] mb-16">Certified and Correct:</div>
                <div class="w-64 border-b mt-10 border-black"></div>
            </div>
        </div>
    </div>
</div>