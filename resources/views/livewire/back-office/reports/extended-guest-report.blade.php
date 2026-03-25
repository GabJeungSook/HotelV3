<div class="max-w-full mx-auto py-8 px-4 sm:px-6 lg:px-8">

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- Shift --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Shift</label>
                <select wire:model="shift" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All</option>
                    <option value="AM">AM</option>
                    <option value="PM">PM</option>
                </select>
            </div>

            {{-- Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" wire:model="date"
                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
            </div>

            {{-- Room --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                <select wire:model="room_id" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}">{{ $room->number }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Buttons --}}
            <div class="flex items-end gap-2">
                <button wire:click="$refresh" type="button"
                        class="w-full md:w-auto inline-flex justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                    Apply
                </button>

                <button wire:click="resetFilters" type="button"
                        class="w-full md:w-auto inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Reset
                </button>
            </div>
        </div>
    </div>

    {{-- Report --}}
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
        @forelse($groups as $group)

            {{-- Gray bar + Yellow label --}}
            <div class="bg-gray-300 px-3 py-2">
                <span class="inline-block bg-gray-300 px-2 py-1 text-xs font-bold tracking-wide text-gray-900">
                    {{ $group['label'] }}
                </span>
            </div>

            {{-- Date label --}}
            <div class="px-3 py-2 border-b border-gray-200">
                <span class="inline-block px-2 py-1 text-sm font-semibold text-gray-900">
                    {{ $group['date_label'] }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800 w-28">
                                ROOM No.
                            </th>
                            <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800 w-56">
                                Room Type
                            </th>
                            <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">
                                Date and Time
                            </th>
                            <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800 w-32">
                                No. Hrs
                            </th>
                            <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800 w-32">
                                Total Hrs
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($group['rows'] as $row)
                            @php
                                $entries = $row['entries'] ?? [];
                                $rowspan = max(count($entries), 1);
                            @endphp

                            <tr>
                                <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900 align-top" rowspan="{{ $rowspan }}">
                                    {{ $row['number'] }}
                                </td>

                                <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900 align-top" rowspan="{{ $rowspan }}">
                                    {{ $row['room_type'] }}
                                </td>

                                <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">
                                    {{ $entries[0]['date_time'] ?? '—' }}
                                </td>

                                <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">
                                    {{ $entries[0]['no_hrs'] ?? '—' }}
                                </td>

                                <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900 align-top" rowspan="{{ $rowspan }}">
                                    {{ $row['total_hrs'] }}
                                </td>
                            </tr>

                            @for($i = 1; $i < count($entries); $i++)
                                <tr>
                                    <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">
                                        {{ $entries[$i]['date_time'] }}
                                    </td>
                                    <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">
                                        {{ $entries[$i]['no_hrs'] }}
                                    </td>
                                </tr>
                            @endfor
                        @endforeach
                    </tbody>
                </table>
            </div>

        @empty
            <div class="p-8 text-center text-sm text-gray-500">
                No extended guest records found for the selected filters.
            </div>
        @endforelse
    </div>
</div>
