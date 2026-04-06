<div class="max-w-full mx-auto py-6 px-4" x-data="{
    printSection(refName, title) {
        const el = this.$refs[refName];
        if (!el) return;
        const printWindow = window.open('', '_blank');
        const doc = printWindow.document;
        doc.open();
        doc.write(`<!DOCTYPE html><html><head><title>${title}</title>
            <style>body{font-family:Arial,sans-serif;font-size:10px;margin:15px}table{width:100%;border-collapse:collapse;margin-bottom:15px}th,td{border:1px solid #ccc;padding:3px 5px;text-align:left}th{background:#f5f5f5;font-weight:600}.text-right{text-align:right}.font-bold{font-weight:700}.text-center{text-align:center}.section-title{font-size:13px;font-weight:700;margin:12px 0 6px}</style>
            </head><body>${el.innerHTML}</body></html>`);
        doc.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }
}">

    {{-- Session Selector --}}
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4 mb-6">
        <div class="flex items-end gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Shift Session</label>
                <select wire:model.live="selectedShiftLogId" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Select a shift</option>
                    @foreach($availableShiftSessions as $s)
                        <option value="{{ $s['id'] }}">{{ $s['label'] }}</option>
                    @endforeach
                </select>
            </div>
            @if($selectedSession)
            <button @click="printSection('bigBossReport', 'Big Boss Report')"
                class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/></svg>
                Print
            </button>
            @endif
        </div>
    </div>

    @if($selectedSession)
    <div x-ref="bigBossReport">

        {{-- Header --}}
        <div class="text-center mb-4">
            <div class="text-lg font-bold">BIG BOSS REPORT</div>
            <div class="text-sm text-gray-600">{{ $selectedSession['frontdesks'] }}</div>
            <div class="text-sm text-gray-600">{{ $selectedSession['date_formatted'] ?? '' }} | {{ $selectedSession['shift_type'] ?? '' }} Shift</div>
            <div class="text-xs text-gray-500">{{ $selectedSession['time_in_formatted'] }} — {{ $selectedSession['time_out_formatted'] }}</div>
        </div>

        {{-- Summary Table --}}
        <div class="mb-6">
            <div class="text-sm font-bold mb-2 uppercase">Sales Summary by Floor</div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-2 py-1">Category</th>
                            @foreach($floors as $floor)
                                <th class="border px-2 py-1 text-center">{{ $floor->number }}F</th>
                            @endforeach
                            <th class="border px-2 py-1 text-center">No Room</th>
                            <th class="border px-2 py-1 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($summaryRows as $row)
                            <tr class="{{ $row['label'] === 'GROSS TOTAL' ? 'bg-gray-100 font-bold' : '' }}">
                                <td class="border px-2 py-1">{{ $row['label'] }}</td>
                                @foreach($floors as $floor)
                                    <td class="border px-2 py-1 text-right">{{ number_format($row['floors'][$floor->id] ?? 0, 2) }}</td>
                                @endforeach
                                <td class="border px-2 py-1 text-right">{{ number_format($row['no_room'] ?? 0, 2) }}</td>
                                <td class="border px-2 py-1 text-right font-semibold">{{ number_format($row['total'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Frontdesk Chart --}}
        @foreach($frontdeskChart as $floorChart)
        <div class="mb-4">
            <div class="text-sm font-bold mb-1">{{ $floorChart['floor']->number }}{{ match(true) { $floorChart['floor']->number % 10 === 1 && $floorChart['floor']->number !== 11 => 'st', $floorChart['floor']->number % 10 === 2 && $floorChart['floor']->number !== 12 => 'nd', $floorChart['floor']->number % 10 === 3 && $floorChart['floor']->number !== 13 => 'rd', default => 'th' } }} Floor</div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs border">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border px-1 py-1">Room</th>
                            <th class="border px-1 py-1">Type</th>
                            <th class="border px-1 py-1">Status</th>
                            <th class="border px-1 py-1">Guest</th>
                            <th class="border px-1 py-1">Check-In</th>
                            <th class="border px-1 py-1">Check-Out</th>
                            <th class="border px-1 py-1">Hrs</th>
                            <th class="border px-1 py-1 text-right">Room</th>
                            <th class="border px-1 py-1 text-right">Transfer</th>
                            <th class="border px-1 py-1 text-right">Extend</th>
                            <th class="border px-1 py-1 text-right">Foods</th>
                            <th class="border px-1 py-1 text-right">Misc</th>
                            <th class="border px-1 py-1 text-right">Deposit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($floorChart['rooms'] as $row)
                            <tr class="{{ $row['is_forwarded'] ? 'bg-yellow-50' : '' }} {{ $row['status'] === 'Available' ? 'text-gray-400' : '' }}">
                                @if($row['rowspan'] > 0)
                                    <td class="border px-1 py-1 font-semibold" rowspan="{{ $row['rowspan'] }}">{{ $row['number'] }}</td>
                                    <td class="border px-1 py-1" rowspan="{{ $row['rowspan'] }}">{{ $row['type'] }}</td>
                                @endif
                                <td class="border px-1 py-1">
                                    @if($row['status'] === 'FWD')
                                        <span class="text-orange-600 font-semibold">FWD</span>
                                    @elseif($row['status'] === 'Available')
                                        <span class="text-gray-400">Available</span>
                                    @else
                                        <span class="text-green-600">Occupied</span>
                                    @endif
                                </td>
                                <td class="border px-1 py-1">{{ $row['guest'] }}</td>
                                <td class="border px-1 py-1">{{ $row['check_in'] }}</td>
                                <td class="border px-1 py-1">{{ $row['check_out'] }}</td>
                                <td class="border px-1 py-1 text-center">{{ $row['initial_hours'] }}</td>
                                <td class="border px-1 py-1 text-right">{{ $row['room_rate'] ? number_format($row['room_rate'], 2) : '' }}</td>
                                <td class="border px-1 py-1 text-right">{{ $row['transfer'] ? number_format($row['transfer'], 2) : '' }}</td>
                                <td class="border px-1 py-1 text-right">{{ $row['extend'] ? number_format($row['extend'], 2) : '' }}</td>
                                <td class="border px-1 py-1 text-right">{{ $row['foods'] ? number_format($row['foods'], 2) : '' }}</td>
                                <td class="border px-1 py-1 text-right">{{ $row['misc'] ? number_format($row['misc'], 2) : '' }}</td>
                                <td class="border px-1 py-1 text-right">{{ $row['deposit'] ? number_format($row['deposit'], 2) : '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach

        {{-- Room Cleaning Chart --}}
        <div class="mb-6">
            <div class="text-sm font-bold mb-2 uppercase">Room Cleaning Chart</div>
            @foreach($roomCleaningChart as $floorChart)
            <div class="mb-3">
                <div class="text-xs font-semibold mb-1">{{ $floorChart['floor']->number }}F</div>
                <table class="min-w-full text-xs border">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border px-2 py-1">Room</th>
                            <th class="border px-2 py-1">Time</th>
                            <th class="border px-2 py-1">Elapse</th>
                            <th class="border px-2 py-1">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($floorChart['rooms'] as $room)
                        <tr>
                            <td class="border px-2 py-1 font-semibold">{{ $room['number'] }}</td>
                            <td class="border px-2 py-1">{{ $room['time'] }}</td>
                            <td class="border px-2 py-1">{{ $room['elapse'] }}</td>
                            <td class="border px-2 py-1">
                                @if($room['status'] === 'Clean')
                                    <span class="text-green-600">Clean</span>
                                @elseif($room['status'] === 'In-use')
                                    <span class="text-blue-600">In-use</span>
                                @else
                                    <span class="text-gray-400">Vacant</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
        </div>

        {{-- Roomboy Logs --}}
        @if(!empty($roomboyLogs))
        <div class="mb-6">
            <div class="text-sm font-bold mb-2 uppercase">Roomboy Activity Logs</div>
            @foreach($roomboyLogs as $log)
            <div class="mb-3">
                <div class="text-xs font-bold mb-1">{{ $log['name'] }}</div>
                <table class="min-w-full text-xs border">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border px-2 py-1">#</th>
                            <th class="border px-2 py-1">Room</th>
                            <th class="border px-2 py-1">Floor</th>
                            <th class="border px-2 py-1">Time</th>
                            <th class="border px-2 py-1">Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($log['entries'] as $entry)
                        <tr>
                            <td class="border px-2 py-1">{{ $entry['number'] }}</td>
                            <td class="border px-2 py-1">{{ $entry['room_number'] }}</td>
                            <td class="border px-2 py-1">{{ $entry['floor_number'] }}</td>
                            <td class="border px-2 py-1">{{ $entry['time'] }}</td>
                            <td class="border px-2 py-1">{{ $entry['elapse'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Statistics --}}
        <div class="mb-6">
            <div class="text-sm font-bold mb-2 uppercase">Statistics</div>
            <table class="text-xs border w-auto">
                <tr><td class="border px-3 py-1 font-semibold">Total New Guests</td><td class="border px-3 py-1 text-right">{{ $totalNewGuest }}</td></tr>
                <tr><td class="border px-3 py-1 font-semibold">Total Extended Guests</td><td class="border px-3 py-1 text-right">{{ $totalExtendedGuest }}</td></tr>
                <tr><td class="border px-3 py-1 font-semibold">Unoccupied Rooms</td><td class="border px-3 py-1 text-right">{{ $totalUnoccupiedRooms }}</td></tr>
                <tr><td class="border px-3 py-1 font-semibold">Unoccupied Room Numbers</td><td class="border px-3 py-1">{{ $unoccupiedRoomNumbers ?: '—' }}</td></tr>
                <tr><td class="border px-3 py-1 font-semibold">Cleaning Order</td><td class="border px-3 py-1">{{ $cleaningOrder ?: '—' }}</td></tr>
                <tr><td class="border px-3 py-1 font-semibold">Maintenance Rooms</td><td class="border px-3 py-1">{{ $maintenanceRooms ?: '—' }}</td></tr>
            </table>
        </div>

        {{-- Expenses --}}
        @if($expenses->count() > 0)
        <div class="mb-6">
            <div class="text-sm font-bold mb-2 uppercase">Expenses</div>
            <table class="min-w-full text-xs border">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border px-2 py-1">Category</th>
                        <th class="border px-2 py-1">Description</th>
                        <th class="border px-2 py-1 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $expense)
                    <tr>
                        <td class="border px-2 py-1">{{ $expense->expenseCategory?->name ?? '—' }}</td>
                        <td class="border px-2 py-1">{{ $expense->description ?? '—' }}</td>
                        <td class="border px-2 py-1 text-right">{{ number_format($expense->amount, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="bg-gray-100 font-bold">
                        <td class="border px-2 py-1" colspan="2">Total Expenses</td>
                        <td class="border px-2 py-1 text-right">{{ number_format($expensesTotal, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

    </div>
    @else
        <div class="text-center py-16 text-gray-400">Select a shift session to generate the report.</div>
    @endif
</div>
