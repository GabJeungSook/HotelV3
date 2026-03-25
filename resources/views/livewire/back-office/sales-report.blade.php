<div class="max-w-full mx-auto py-8 px-4 sm:px-6 lg:px-8">
    {{-- <style>
@page {
    size: A4 landscape;
    margin: 12mm;
}

@media print {

    .no-print {
        display: none !important;
    }

    body {
        transform: scale(0.5);
        transform-origin: top left;
        width: 125%;
    }

    table {
        width: 100% !important;
        table-layout: fixed;
        font-size: 10px !important;
    }

    th, td {
        padding: 2px !important;
        overflow-wrap: break-word;
    }
}
</style> --}}
    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4 mb-6">
        <div class="grid grid-cols-3 md:grid-cols-3 gap-4">

            {{-- Frontdesk --}}
            <div class="flex flex-col gap-1">
                <label class="block text-sm font-medium text-gray-700">Frontdesk</label>
                <select wire:model="frontdesk"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All</option>
                    @foreach($frontdesks as $fd)
                        <option value="{{ $fd->user_id }}">{{ $fd->name }}</option>
                    @endforeach
                </select>

                  {{-- <div class="flex items-end gap-2 mt-3">
                    <button wire:click="generateReport" type="button"
                            class="w-full md:w-auto inline-flex justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                        Apply
                    </button>

                    <button wire:click="resetFilters" type="button"
                            class="w-full md:w-auto inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Reset
                    </button>
                </div> --}}

                 {{-- <div class="flex items-end gap-2">
                    <button @click="printOut($refs.printContainer.outerHTML);" type="button"
                            class="w-full md:w-auto inline-flex justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                        Print
                    </button>
                </div> --}}
            </div>

            {{-- Date From --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" wire:model="date_from"
                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
            </div>

            {{-- Date To --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                <input type="date" wire:model="date_to"
                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
            <div class="flex justify-end items-end gap-2 mt-3">
                    <button wire:click="generateReport" type="button"
                            class="w-full md:w-auto inline-flex justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                        Apply
                    </button>

                    <button wire:click="resetFilters" type="button"
                            class="w-full md:w-auto inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Reset
                    </button>
                </div>
            </div>
            {{-- Time From --}}
            {{-- <div>
                <label class="block text-sm font-medium text-gray-700">Time From</label>
                <input type="time"
                    wire:model="time_from"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm">
            </div> --}}
            {{-- Time To --}}
            {{-- <div>
                <label class="block text-sm font-medium text-gray-700">Time To</label>
                <input type="time"
                    wire:model="time_to"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm">

                      
            </div> --}}

            {{-- Shift + Buttons --}}
            <div class="flex flex-col gap-3">
                {{-- <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Shift</label>
                    <select wire:model="shift"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All</option>
                        <option value="AM">AM</option>
                        <option value="PM">PM</option>
                    </select>
                </div> --}}

                {{-- <div class="flex items-end gap-2">
                    <button wire:click="generateReport" type="button"
                            class="w-full md:w-auto inline-flex justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                        Apply
                    </button>

                    <button wire:click="resetFilters" type="button"
                            class="w-full md:w-auto inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Reset
                    </button>
                </div> --}}
            </div>
        </div>

        {{-- Column Toggles (still uniform; placed under filters) --}}
        {{-- <div class="mt-4 flex flex-wrap gap-4">
            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" wire:model.live="showExtend" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-gray-700">Extend</span>
            </label>

            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" wire:model.live="showAmenities" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-gray-700">Amenities</span>
            </label>

            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" wire:model.live="showFood" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-gray-700">Food</span>
            </label>

            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" wire:model.live="showDamages" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-gray-700">Damages</span>
            </label>

            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" wire:model.live="showTransfer" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-gray-700">Transfer Room</span>
            </label>
            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" wire:model.live="showDeposits" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-gray-700">Client Deposit</span>
            </label>
        </div> --}}
    </div>

    {{-- Report --}}
    <div class="bg-white rounded-sm shadow-sm ring-1 ring-gray-200 overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
        <div class="text-sm font-semibold text-gray-900">SALES REPORT</div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse">
            <thead>
                <tr>
                    <th class="border border-gray-300 px-2 py-2 text-left text-sm font-semibold text-gray-800 w-28">ROOM #</th>
                    <th class="border border-gray-300 px-2 py-2 text-left text-sm font-semibold text-gray-800 w-56">ROOM TYPE</th>
                    <th class="border border-gray-300 px-2 py-2 text-left text-sm font-semibold text-gray-800 w-64">GUEST NAME</th>
                    <th class="border border-gray-300 px-2 py-2 text-left text-sm font-semibold text-gray-800 w-28">TYPE</th>
                    <th class="border border-gray-300 px-2 py-2 text-left text-sm font-semibold text-gray-800">CHECK-IN</th>
                    <th class="border border-gray-300 px-2 py-2 text-left text-sm font-semibold text-gray-800">CHECK-OUT</th>
                    <th class="border border-gray-300 px-2 py-2 text-left text-sm font-semibold text-gray-800 w-32">INITIAL HRS</th>
                    <th class="border border-gray-300 px-2 py-2 text-left text-sm font-semibold text-gray-800 w-36">ROOM AMOUNT</th>

                    @if($showExtend)
                        <th class="border border-gray-300 px-2 py-2 text-left text-sm font-semibold text-gray-800 w-36">EXTEND</th>
                    @endif
                    @if($showAmenities)
                        <th class="border border-gray-300 px-2 py-2 text-left text-sm font-semibold text-gray-800 w-36">AMENITIES</th>
                    @endif
                    @if($showFood)
                        <th class="border border-gray-300 px-2 py-2 text-left text-sm font-semibold text-gray-800 w-36">FOOD</th>
                    @endif
                    @if($showDamages)
                        <th class="border border-gray-300 px-2 py-2 text-left text-sm font-semibold text-gray-800 w-36">DAMAGES</th>
                    @endif
                    @if($showTransfer)
                        <th class="border border-gray-300 px-2 py-2 text-left text-sm font-semibold text-gray-800 w-36">TRANSFER</th>
                    @endif
                    {{-- @if($showDeposits)
                    <th class="border border-gray-300 px-2 py-2 text-left text-sm font-semibold text-gray-800 w-36">
                        CLIENT DEPOSIT
                    </th>
                    @endif

                    <th class="border border-gray-300 px-2 py-2 text-left text-sm font-semibold text-gray-800 w-56">FRONTDESK</th>
                    <th class="border border-gray-300 px-2 py-2 text-left text-sm font-semibold text-gray-800 w-24">SHIFT</th> --}}
                    <th class="border border-gray-300 px-2 py-2 text-left text-sm font-semibold text-gray-800 w-56">ROOM DEPOSIT</th>
                    <th class="border border-gray-300 px-2 py-2 text-left text-sm font-semibold text-gray-800 w-24">CLIENT DEPOSIT</th> 
                    <th class="border border-gray-300 px-2 py-2 text-left text-sm font-semibold text-gray-800 w-40">FRONTDESK</th>
                    <th class="border border-gray-300 px-2 py-2 text-left text-sm font-semibold text-gray-800 w-40">TRANSACTION DATE</th>
                    <th class="border border-gray-300 px-2 py-2 text-left text-sm font-semibold text-gray-800 w-40">TOTAL</th>
                </tr>
            </thead>

            <tbody>
                @forelse($salesRooms as $row)
                    {{-- @foreach($room['rows'] as $index => $row) --}}
                        <tr>
                           <td class="border border-gray-300 px-2 py-3 text-sm font-semibold">
                                {{ $row['room_number'] }}
                            </td>

                            <td class="border border-gray-300 px-2 py-3 text-sm text-gray-900">{{ $row['room_type'] }}</td>
                            <td class="border border-gray-300 px-2 py-3 text-sm text-gray-900">{{ $row['guest_name'] }}</td>
                            <td class="border border-gray-300 px-2 py-3 text-sm text-gray-900">{{ $row['type'] }}</td>
                            <td class="border border-gray-300 px-2 py-3 text-sm text-gray-900">{{ $row['check_in'] }}</td>
                            <td class="border border-gray-300 px-2 py-3 text-sm text-gray-900">{{ $row['check_out'] }}</td>
                            <td class="border border-gray-300 px-2 py-3 text-sm text-gray-900">{{ $row['initial_hrs'] }}</td>

                            <td class="border border-gray-300 px-2 py-3 text-sm text-gray-900">
                                ₱ {{ number_format($row['room_amount'], 2) }}
                            </td>

                            @if($showExtend)
                                <td class="border border-gray-300 px-2 py-3 text-sm text-gray-900">
                                    ₱ {{ number_format($row['extend_amount'], 2) }}
                                </td>
                            @endif

                            @if($showAmenities)
                                <td class="border border-gray-300 px-2 py-3 text-sm text-gray-900">
                                    ₱ {{ number_format($row['amenities_amount'], 2) }}
                                </td>
                            @endif

                            @if($showFood)
                                <td class="border border-gray-300 px-2 py-3 text-sm text-gray-900">
                                    ₱ {{ number_format($row['food_amount'], 2) }}
                                </td>
                            @endif

                            @if($showDamages)
                                <td class="border border-gray-300 px-2 py-3 text-sm text-gray-900">
                                    ₱ {{ number_format($row['damages_amount'], 2) }}
                                </td>
                            @endif

                            @if($showTransfer)
                                <td class="border border-gray-300 px-2 py-3 text-sm text-gray-900">
                                    ₱ {{ number_format($row['transfer_amount'], 2) }}
                                </td>
                            @endif
                            {{-- @if($showDeposits)
                        <td class="border border-gray-300 px-2 py-3 text-sm text-gray-900">
                            ₱ {{ number_format($row['deposit_amount'], 2) }}
                        </td>
                        @endif --}}

                            {{-- <td class="border border-gray-300 px-3 py-2 text-sm text-gray-900">{{ $row['frontdesk_name'] }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-sm text-gray-900">{{ $row['shift'] }}</td> --}}
                            <td class="border border-gray-300 px-3 py-2 text-sm text-gray-900">₱ {{ number_format($row['room_deposit'], 2) }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-sm text-gray-900">₱ {{ number_format($row['client_deposit'], 2) }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-sm text-gray-900">{{$row['frontdesk'] }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-sm text-gray-900">{{$row['created_at'] }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-sm text-gray-900 font-semibold">
                                ₱ {{ number_format($row['total'], 2) }}
                            </td>

                        </tr>
                    {{-- @endforeach --}}
                @empty
                    <tr>
                        <td colspan="18"
                            class="border border-gray-300 px-2 py-6 text-sm text-center text-gray-500">
                            No sales records found for the selected filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


        {{-- Totals + signatories --}}
        <div class="p-6 border-t border-gray-200">
            <div class="flex justify-end">
                <div class="text-gray-900 font-semibold">
                    TOTAL SALES: ₱ {{ number_format($totalSales, 2) }}
                </div>
            </div>

            <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 gap-10">
                <div class="text-gray-700">
                    <div class="text-sm font-semibold">Prepared By:</div>
                    <div class="mt-10 w-64 border-b border-gray-400"></div>
                </div>

                <div class="text-gray-700">
                    <div class="text-sm font-semibold">Verified By:</div>
                    <div class="mt-10 w-64 border-b border-gray-400"></div>
                </div>
            </div>
        </div>

        
        {{-- Room Summary --}}
        <div class="flex justify-end items-end gap-2 border-t border-gray-200 p-6">
                <button @click="printOut($refs.printContainer.outerHTML);" type="button"
                class="w-full md:w-auto inline-flex justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                Print
            </button>
        </div>
        {{-- printable area --}}
        <div  x-ref="printContainer">
                    <div class="p-6 ">
        {{-- Header --}}
        <div class="border-t py-6 grid grid-cols-2 gap-y-4 text-sm">
            <div class="font-medium text-gray-600">
                Frontdesk:
            </div>
            <div class="font-semibold text-gray-900">
                {{ $frontdesk_name ?? '-' }}
            </div>

            <div class="font-medium text-gray-600">
                Date:
            </div>
            <div class="font-semibold text-gray-900">
                {{ 
                $startDateTime && $endDateTime
                    ? $startDateTime->format('M d Y h:i A') . ' - ' . $endDateTime->format('M d Y h:i A')
                    : '-'
                }}
            </div>

            <div class="font-medium text-gray-600">
                Shift:
            </div>
            <div class="font-semibold text-gray-900">
                {{ $shift ?? '-' }}
            </div>
        </div>
            

    <div class="text-center font-semibold text-gray-900 mb-4">ROOM SUMMARY</div>

    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse">
            <thead>
                <tr>
                    <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">Description</th>
                    @foreach(($roomSummary['floors'] ?? []) as $f)
                        <th class="border border-gray-300 px-3 py-2 text-right text-sm font-semibold text-gray-800">
                             {{ $f['number'] }}{{ $f['number'] == 1 ? 'st' : ($f['number'] == 2 ? 'nd' : ($f['number'] == 3 ? 'rd' : 'th')) }} Floor
                        </th>
                    @endforeach
                    <th class="border border-gray-300 px-3 py-2 text-right text-sm font-semibold text-gray-800">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($roomSummary['rows'] ?? []) as $r)
                    <tr>
                        <td class="border border-gray-300 px-3 py-2 text-sm font-medium">{{ $r['description'] }}</td>

                        @foreach(($roomSummary['floors'] ?? []) as $f)
                            <td class="border border-gray-300 px-3 py-2 text-sm text-right">
                                ₱ {{ number_format($r['floors'][$f['id']] ?? 0, 2) }}
                            </td>
                        @endforeach

                        <td class="border border-gray-300 px-3 py-2 text-sm text-right font-semibold">
                            ₱ {{ number_format($r['row_total'] ?? 0, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ (count($roomSummary['floors'] ?? []) + 2) }}" class="border border-gray-300 px-3 py-8 text-center text-sm text-gray-500">
                            No room summary data for the selected filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer totals per floor --}}
    <div class="bg-gray-50 px-3 py-2 text-sm font-semibold border border-gray-300 border-t-0">
        <div class="flex justify-between">
            <span>TOTAL</span>
            <span>₱ {{ number_format(collect($roomSummary['totals'] ?? [])->sum(), 2) }}</span>
        </div>
    </div>
</div>

        {{-- Reports Summary --}}
<div class="p-6 border-t border-gray-200">
    <div class="text-center font-semibold text-gray-900 mb-4">
        REPORTS SUMMARY
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        {{-- Card component --}}
        @php
            $cards = [
                ['title' => 'GUEST PER ACCOMMODATION', 'key' => 'guest_per_accommodation', 'left' => 'Room Type', 'right' => 'Guest Number'],
                ['title' => 'UNOCCUPIED ROOMS', 'key' => 'unoccupied_rooms', 'left' => 'Room Type', 'right' => 'Room Number'],
                ['title' => 'UNDER REPAIR ROOM', 'key' => 'under_repair_rooms', 'left' => 'Room Type', 'right' => 'Room Number'],
                ['title' => 'GROUP CHECK-IN TIME', 'key' => 'group_checkin_time', 'left' => 'Time', 'right' => 'Guest'],
                ['title' => 'GROUP CHECK-OUT TIME', 'key' => 'group_checkout_time', 'left' => 'Time', 'right' => 'Guest'],
            ];
        @endphp

        @foreach($cards as $c)
                <div class="rounded-lg border border-gray-300 overflow-hidden bg-white flex flex-col min-h-[220px]">
                <div class="bg-gray-100 text-xs font-semibold text-gray-700 px-3 py-2 text-center">
                    {{ $c['title'] }}
                </div>

                <div class="px-3 py-2 flex-1 flex flex-col">
                    <div class="grid grid-cols-2 text-[11px] font-semibold text-gray-700 border-b border-gray-200 pb-2">
                        <div>{{ $c['left'] }}</div>
                        <div class="text-right">{{ $c['right'] }}</div>
                    </div>

                    @php
                        $data = $summary[$c['key']] ?? [];
                        $total = collect($data)->sum('value');

                        $footerLabel = match($c['key']) {
                            'guest_per_accommodation',
                            'group_checkin_time',
                            'group_checkout_time' => 'Total Guests',
                            default => 'Total Rooms',
                        };
                    @endphp

                    <div class="flex-1">
                        <div class="divide-y divide-gray-100">
                            @forelse($data as $row)
                                <div class="grid grid-cols-2 py-2 text-[11px] text-gray-700">
                                    <div class="truncate">{{ $row['label'] }}</div>
                                    <div class="text-right font-medium">{{ number_format($row['value']) }}</div>
                                </div>
                            @empty
                                <div class="py-10 text-center text-xs text-gray-400">
                                    No data
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Footer pinned --}}
                <div class="bg-gray-100 px-3 py-2 text-[11px] text-gray-800 font-semibold border-t border-gray-300 flex justify-between items-center">
                    <span>{{ $footerLabel }}</span>
                    <span>{{ number_format($total) }}</span>
                </div>
            </div>

        @endforeach
    </div>

    {{-- expense summary --}}
    <div class="p-6 border-t border-gray-200">
    <div class="text-center font-semibold text-gray-900 mb-4">EXPENSE SUMMARY</div>

    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse">
            <thead>
                <tr>
                    <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">Expense Type</th>
                    <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">Description</th>
                    <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800 w-28">Shift</th>
                    <th class="border border-gray-300 px-3 py-2 text-right text-sm font-semibold text-gray-800 w-32">Amount</th>
                    <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800 w-56">Frontdesk</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expensesRows ?? [] as $e)
                    <tr>
                        <td class="border border-gray-300 px-3 py-2 text-sm">{{ $e['expense_type'] }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-sm">{{ $e['description'] }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-sm">{{ $e['shift'] }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-sm text-right">₱ {{ number_format($e['amount'], 2) }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-sm">{{ $e['frontdesk'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="border border-gray-300 px-3 py-8 text-center text-sm text-gray-500">
                            No expenses found for the selected filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-gray-50 px-3 py-2 text-sm font-semibold flex justify-end border border-gray-300 border-t-0">
        TOTAL EXPENSES: ₱ {{ number_format($expensesTotal ?? 0, 2) }}
    </div>
</div>


</div>


        </div>


    </div>
</div>
