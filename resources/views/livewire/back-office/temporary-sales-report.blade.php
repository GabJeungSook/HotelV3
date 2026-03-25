<div class="max-w-7xl mx-auto px-4 py-8 space-y-6 bg-gray-100 min-h-screen text-gray-900">
    <style>
@media print {

    body * {
        visibility: hidden;
    }

    #daily-sales-summary,
    #daily-sales-summary * {
        visibility: visible;
    }

    #daily-sales-summary {
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
                {{-- <label class="block text-sm font-medium text-gray-700">Frontdesk</label> --}}
                <select hidden class="w-full rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option>All</option>
                    <option>Hanah</option>
                    <option>Kathleen</option>
                    <option>Jeneath</option>
                    <option>Ruby Gold</option>
                </select>

                <div class="flex items-end gap-2">
                    <button type="button" onclick="window.print()" class="w-full md:w-auto inline-flex justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">Print</button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
                <input disabled type="date" wire:model="date_from" class="w-full rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                <input disabled type="date" wire:model="date_to" class="w-full rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
            </div>

            <div class="flex flex-col gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Shift</label>
                    <select disabled class="w-full rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        {{-- <option>All</option>
                        <option>AM</option> --}}
                        <option selected>PM</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="button" class="w-full md:w-auto inline-flex justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                        Apply
                    </button>

                    <button type="button" class="w-full md:w-auto inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Reset
                    </button>
                </div>
            </div>
        </div>

        {{-- <div class="mt-4 flex flex-wrap gap-4">
            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-gray-700">Extend</span>
            </label>

            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-gray-700">Amenities</span>
            </label>

            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-gray-700">Food</span>
            </label>

            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-gray-700">Damages</span>
            </label>

            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-gray-700">Transfer Room</span>
            </label>
        </div> --}}
    </div>

    <!-- Static report design copied from image -->
    <div class="bg-white shadow-sm ring-1 ring-gray-300 p-8">
        <div id="daily-sales-summary" class="max-w-6xl mx-auto text-[15px] leading-tight text-black">
            <h1 class="text-center font-bold tracking-wide uppercase mb-4">DAILY SALES SUMMARY REPORT</h1>

            <div class="space-y-1 mb-10 pl-6">
                <p>Frontdesk (Outgoing): Hanah and Kathleen</p>
                <p>Frontdesk (Incoming): Jeneath and Ruby Gold</p>
                <p>Shift Opened: March 08, 2026 08:03 PM</p>
                <p>Shift Closed: March 09, 2026 08:00 AM</p>
            </div>

            <div class="mb-8">
                <h2 class="font-bold uppercase mb-3">ROOM SUMMARY</h2>

                <table class="w-full border-collapse table-fixed border border-black text-[14px]">
                    <thead>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-bold">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-bold">1st Floor</th>
                            <th class="border border-black px-2 py-1 text-left font-bold">2nd Floor</th>
                            <th class="border border-black px-2 py-1 text-left font-bold">3rd Floor</th>
                            <th class="border border-black px-2 py-1 text-left font-bold">4th Floor</th>
                            <th class="border border-black px-2 py-1 text-left font-bold">5th Floor</th>
                            <th class="border border-black px-2 py-1 text-left font-bold">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1">Room</td>
                            <td class="border border-black px-2 py-1">₱</td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1">Food and Beverages</td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1">Amenities</td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1">Transfer Room</td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1">Extend</td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1">Damages Charges</td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                        </tr>
                        <tr>
                            <td colspan="7" class="border border-black px-2 py-1 font-bold">TOTAL: ₱ 50,182.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 max-w-2xl">
                <table class="border-collapse table-fixed border border-black text-[14px] w-full">
                    <thead>
                        <tr>
                            <th colspan="2" class="border border-black px-2 py-1 text-center font-bold uppercase">GUEST PER ACCOMODATION</th>
                        </tr>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left italic font-bold">Room Type</th>
                            <th class="border border-black px-2 py-1 text-left italic font-bold">Guest #</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1">Single Size Bed</td>
                            <td class="border border-black px-2 py-1"></td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1">Double Size Bed</td>
                            <td class="border border-black px-2 py-1"></td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1">Twin size Bed</td>
                            <td class="border border-black px-2 py-1"></td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1">Total Guests</td>
                            <td class="border border-black px-2 py-1 text-right">120</td>
                        </tr>
                    </tbody>
                </table>

                <table class="border-collapse table-fixed border border-black text-[14px] w-full">
                    <thead>
                        <tr>
                            <th colspan="2" class="border border-black px-2 py-1 text-center font-bold uppercase">UNOCCUPIED ROOMS</th>
                        </tr>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left italic font-bold">Room Type</th>
                            <th class="border border-black px-2 py-1 text-left italic font-bold">Guest #</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1">Single Size Bed</td>
                            <td class="border border-black px-2 py-1"></td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1">Double Size Bed</td>
                            <td class="border border-black px-2 py-1"></td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1">Twin size Bed</td>
                            <td class="border border-black px-2 py-1"></td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-1">Total Guests</td>
                            <td class="border border-black px-2 py-1 text-right">120</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div>
                <table class="w-full border-collapse table-fixed border border-black text-[14px]">
                    <thead>
                        <tr>
                            <th colspan="3" class="border border-black px-2 py-1 text-center font-bold uppercase">EXPENSE SUMMARY</th>
                        </tr>
                        <tr>
                            <th class="border border-black px-2 py-1 text-left font-normal">Expense Type</th>
                            <th class="border border-black px-2 py-1 text-left font-normal">Description</th>
                            <th class="border border-black px-2 py-1 text-left font-normal">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-3"></td>
                            <td class="border border-black px-2 py-3"></td>
                            <td class="border border-black px-2 py-3"></td>
                        </tr>
                        <tr>
                            <td class="border border-black px-2 py-3"></td>
                            <td class="border border-black px-2 py-3"></td>
                            <td class="border border-black px-2 py-3"></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="border border-black px-2 py-1">TOTAL: ₱</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
