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
                    @if($filter == 1)
                    <a href="{{ asset('raw-files/PM SALES DATA- March 8-9, 2026.xlsx') }}"
                       download
                       class="w-full md:w-auto inline-flex items-center gap-2 justify-center rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 16v-8m0 8l-3-3m3 3l3-3M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2"/>
                        </svg>
                        Download Raw File
                    </a>
                    @else
                        <a href="{{ asset('raw-files/AM SALES DATA- March 9, 2026.xlsx') }}"
                       download
                       class="w-full md:w-auto inline-flex items-center gap-2 justify-center rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 16v-8m0 8l-3-3m3 3l3-3M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2"/>
                        </svg>
                        Download Raw File
                    </a>

                    @endif

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
                        <option value="1" selected>PM SHIFT - March 8-9, 2026</option>
                        <option value="2">AM SHIFT - March 8-9, 2026</option>
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
    @if($filter == 1)
    <div class="bg-white shadow-sm ring-1 ring-gray-300 p-8">
        <div id="daily-shift-summary" class="mx-auto max-w-[900px] text-[14px] leading-tight text-black space-y-8">
            <h1 class="text-center font-bold uppercase">DAILY SHIFT SUMMARY REPORT</h1>

            <div class="space-y-1 text-[13px]">
                <p>Frontdesk (Outgoing): Hannah</p>
                <p>Frontdesk (Incoming): Jeneath</p>
                <p>Shift Opened: March 08, 2026 08:03 PM</p>
                <p>Shift Closed: March 09, 2026 07:27 AM</p>
            </div>

            <div>
                <div class="mb-2 text-[13px]">1.&nbsp;&nbsp;&nbsp;&nbsp;Cash Drawer</div>
                <table class="w-full border-collapse table-fixed border border-black text-[13px]">
                    <thead>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[39%]">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[27%]">Amount</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[34%]">Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1 align-top">
                                <div class="font-bold">Opening Cash</div>
                                <div>(Net Sales Receive from previous Shift)</div>
                            </td>
                            <td class="border border-black px-2 py-1 align-top font-bold">₱ 1.00</td>
                            <td class="border border-black px-2 py-1 align-top">-</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 align-top">
                                <div class="font-bold">Key/Remote Deposit</div>
                                <div>(Room occupied receive)</div>
                            </td>
                            <td class="border border-black px-2 py-1 align-top">-</td>
                            <td class="border border-black px-2 py-1 align-top">-</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 align-top">
                                <div class="font-bold">Other Deposit</div>
                                <div>(Total client other deposit received)</div>
                            </td>
                            <td class="border border-black px-2 py-1 align-top">-</td>
                            <td class="border border-black px-2 py-1 align-top">-</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Total Cash Received</td>
                            <td class="border border-black px-2 py-1">-</td>
                            <td class="border border-black px-2 py-1">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div>
                <div class="mb-2 text-[13px]">2.&nbsp;&nbsp;&nbsp;&nbsp;Frontdesk Operation</div>
                <table class="w-full border-collapse table-fixed border border-black text-[13px] mb-8">
                    <thead>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[50%]">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[16%]">Number</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[34%]">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1 align-top">
                                <div class="font-bold">New Check-in</div>
                                <div>(Total New Check-in)</div>
                            </td>
                            <td class="border border-black px-2 py-1 align-top">92</td>
                            <td class="border border-black px-2 py-1 align-top font-bold">₱ 34,440.00</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Extension</td>
                            <td class="border border-black px-2 py-1">7</td>
                            <td class="border border-black px-2 py-1">₱ 1,568</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Miscellaneous charges</td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1">₱ 300</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Food</td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1">₱ 282</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Drink</td>
                            <td class="border border-black px-2 py-1">-</td>
                            <td class="border border-black px-2 py-1">-</td>
                        </tr>
                    </tbody>
                </table>

                <table class="w-full border-collapse table-fixed border border-black text-[13px]">
                    <thead>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[50%]">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[16%]">Number</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[34%]">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1 align-top">
                                <div class="font-bold">Key/Remote Deposit</div>
                                <div>(Current room occupied at the end shift)</div>
                            </td>
                            <td class="border border-black px-2 py-1 align-top">47</td>
                            <td class="border border-black px-2 py-1 align-top font-bold">₱ 9,400</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Total Check-out</td>
                            <td class="border border-black px-2 py-1">45</td>
                            <td class="border border-black px-2 py-1 font-bold">₱ 9,000</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Other deposit</td>
                            <td class="border border-black px-2 py-1">-</td>
                            <td class="border border-black px-2 py-1">-</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Expenses</td>
                            <td class="border border-black px-2 py-1">-</td>
                            <td class="border border-black px-2 py-1">-</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Remittance</td>
                            <td class="border border-black px-2 py-1">-</td>
                            <td class="border border-black px-2 py-1"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div>
                <div class="mb-2 text-[13px]">3.&nbsp;&nbsp;&nbsp;&nbsp;Cash reconciliation</div>
                <table class="w-full border-collapse table-fixed border border-black text-[13px]">
                    <thead>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[55%]">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[45%]" colspan="2">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Expected Cash</td>
                            <td class="border border-black px-2 py-1" colspan="2">₱ 45,991.00</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Actual Cash</td>
                            <td class="border border-black px-2 py-1" colspan="2">₱ 45,716.00</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold text-red-600">Difference</td>
                            <td class="border border-black px-2 py-1 text-red-600" colspan="2">₱ 275.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div>
                <div class="mb-2 text-[13px]">4.&nbsp;&nbsp;&nbsp;&nbsp;Final Sales</div>
                <table class="w-full border-collapse table-fixed border border-black text-[13px]">
                    <thead>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[55%]">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[45%]" colspan="2">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Gross Sales</td>
                            <td class="border border-black px-2 py-1" colspan="2">₱ 45,990.00</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Expenses</td>
                            <td class="border border-black px-2 py-1" colspan="2">-</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Discounts</td>
                            <td class="border border-black px-2 py-1" colspan="2">-</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold align-top">Key/ Remote<br>Deposit</td>
                            <td class="border border-black px-2 py-1" colspan="2">₱ 9,400.00</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Other Deposit</td>
                            <td class="border border-black px-2 py-1" colspan="2"></td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Net Sales</td>
                            <td class="border border-black px-2 py-1 font-bold" colspan="2">₱ 36,008.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="pt-16 pl-2">
                <div class="text-[14px] mb-16">Certified and Correct:</div>
                <div class="w-64 border-b mt-10 border-black"></div>
            </div>
        </div>
    </div>
    @else
    <div class="bg-white shadow-sm ring-1 ring-gray-300 p-8">
        <div id="daily-shift-summary" class="mx-auto max-w-[900px] text-[14px] leading-tight text-black space-y-8">
            <h1 class="text-center font-bold uppercase">DAILY SHIFT SUMMARY REPORT</h1>

            <div class="space-y-1 text-[13px]">
                <p>Frontdesk (Outgoing): Jeneath</p>
                <p>Frontdesk (Incoming): Kathleen Drew</p>
                <p>Shift Opened: March 09, 2026 07:28 AM</p>
                <p>Shift Closed: March 09, 2026 08:57 PM</p>
            </div>

            <div>
                <div class="mb-2 text-[13px]">1.&nbsp;&nbsp;&nbsp;&nbsp;Cash Drawer</div>
                <table class="w-full border-collapse table-fixed border border-black text-[13px]">
                    <thead>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[39%]">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[27%]">Amount</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[34%]">Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1 align-top">
                                <div class="font-bold">Opening Cash</div>
                                <div>(Net Sales Receive from previous Shift)</div>
                            </td>
                            <td class="border border-black px-2 py-1 align-top font-bold">₱ 36,008.00</td>
                            <td class="border border-black px-2 py-1 align-top">-</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 align-top">
                                <div class="font-bold">Key/Remote Deposit</div>
                                <div>(Room occupied receive)</div>
                            </td>
                            <td class="border border-black px-2 py-1 align-top">₱ 9,400.00</td>
                            <td class="border border-black px-2 py-1 align-top">-</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 align-top">
                                <div class="font-bold">Other Deposit</div>
                                <div>(Total client other deposit received)</div>
                            </td>
                            <td class="border border-black px-2 py-1 align-top">-</td>
                            <td class="border border-black px-2 py-1 align-top">-</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Total Cash Received</td>
                            <td class="border border-black px-2 py-1 font-bold">₱ 45,716.00</td>
                            <td class="border border-black px-2 py-1">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div>
                <div class="mb-2 text-[13px]">2.&nbsp;&nbsp;&nbsp;&nbsp;Frontdesk Operation</div>
                <table class="w-full border-collapse table-fixed border border-black text-[13px] mb-8">
                    <thead>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[50%]">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[16%]">Number</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[34%]">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1 align-top">
                                <div class="font-bold">New Check-in</div>
                                <div>(Total New Check-in)</div>
                            </td>
                            <td class="border border-black px-2 py-1 align-top">118</td>
                            <td class="border border-black px-2 py-1 align-top font-bold">₱48,384.00</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Extension</td>
                            <td class="border border-black px-2 py-1">-</td>
                            <td class="border border-black px-2 py-1">₱336.00</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Transfer Room</td>
                            <td class="border border-black px-2 py-1">-</td>
                            <td class="border border-black px-2 py-1">₱ 600.00</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Miscellaneous charges</td>
                            <td class="border border-black px-2 py-1">-</td>
                            <td class="border border-black px-2 py-1">₱ 250.00</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Food</td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1">₱ 110.00</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Drink</td>
                            <td class="border border-black px-2 py-1">-</td>
                            <td class="border border-black px-2 py-1">-</td>
                        </tr>
                    </tbody>
                </table>

                <table class="w-full border-collapse table-fixed border border-black text-[13px]">
                    <thead>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[50%]">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[16%]">Number</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[34%]">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1 align-top">
                                <div class="font-bold">Key/Remote Deposit</div>
                                <div>(Current room occupied at the end shift)</div>
                            </td>
                            <td class="border border-black px-2 py-1 align-top">3</td>
                            <td class="border border-black px-2 py-1 align-top font-bold">₱ 600</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Total Check-out</td>
                            <td class="border border-black px-2 py-1">115</td>
                            <td class="border border-black px-2 py-1 font-bold">₱ 19,400.00</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Other deposit</td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Expenses</td>
                            <td class="border border-black px-2 py-1">-</td>
                            <td class="border border-black px-2 py-1">₱ 9,407.00</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Remittance</td>
                            <td class="border border-black px-2 py-1">-</td>
                            <td class="border border-black px-2 py-1">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div>
                <div class="mb-2 text-[13px]">3.&nbsp;&nbsp;&nbsp;&nbsp;Cash reconciliation</div>
                <table class="w-full border-collapse table-fixed border border-black text-[13px]">
                    <thead>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[55%]">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[45%]" colspan="2">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Expected Cash</td>
                            <td class="border border-black px-2 py-1" colspan="2">₱ 86,589.00</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Actual Cash</td>
                            <td class="border border-black px-2 py-1" colspan="2">₱ 86,594.00</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold text-red-600">Difference</td>
                            <td class="border border-black px-2 py-1 text-red-600 font-bold" colspan="2">5</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div>
                <div class="mb-2 text-[13px]">4.&nbsp;&nbsp;&nbsp;&nbsp;Final Sales</div>
                <table class="w-full border-collapse table-fixed border border-black text-[13px]">
                    <thead>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[40%]">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[25%]">Number</th>
                            <th class="border border-black px-2 py-1 text-left font-bold w-[35%]">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Gross Sales</td>
                            <td class="border border-black px-2 py-1">₱ 50,280.00</td>
                            <td class="border border-black px-2 py-1"></td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Expenses</td>
                            <td class="border border-black px-2 py-1">₱ 9,407.00</td>
                            <td class="border border-black px-2 py-1"></td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Discounts</td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Key/Remote Deposit</td>
                            <td class="border border-black px-2 py-1">₱ 600.00</td>
                            <td class="border border-black px-2 py-1"></td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1 font-bold">Net Sales</td>
                            <td class="border border-black px-2 py-1 font-bold">₱ 40,273.00</td>
                            <td class="border border-black px-2 py-1"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="pt-16 pl-2">
                <div class="text-[14px] mb-16">Certified and Correct:</div>
                <div class="w-48 border-b mt-10 border-black"></div>
            </div>
        </div>
    </div>
    @endif
</div>