<div class="max-w-full mx-auto py-8 px-4 sm:px-6 lg:px-8">

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- Shift --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Shift</label>
                <select wire:model.defer="shift" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All</option>
                    <option value="AM">AM</option>
                    <option value="PM">PM</option>
                </select>
            </div>

            {{-- Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" wire:model.defer="date"
                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
            </div>

            {{-- Drawer --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cash Drawer</label>
                <select wire:model.defer="drawer_id" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All</option>
                    @foreach($drawers as $drawer)
                        <option value="{{ $drawer->id }}">{{ $drawer->name }}</option>
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

            {{-- Gray bar + yellow shift label --}}
            <div class="bg-gray-300 px-3 py-2">
                <span class="inline-block bg-gray-300 px-2 py-1 text-xs font-bold tracking-wide text-gray-900">
                    {{ strtoupper($group['shift']) }} SHIFT
                </span>
            </div>

            {{-- Date label --}}
            <div class="px-3 py-2 border-b border-gray-200">
                <span class="inline-block  px-2 py-1 text-sm font-semibold text-gray-900">
                    {{ $group['date_label'] }}
                </span>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800 w-64">
                                Frontdesk Name
                            </th>
                            <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800 w-56">
                                Cash Drawer
                            </th>
                            <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800 w-48">
                                TIME IN
                            </th>
                            <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800 w-48">
                                TIME-OUT
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($group['rows'] as $row)
                            <tr>
                                <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">
                                    {{ $row['frontdesk_name'] }}
                                </td>
                                <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">
                                    {{ $row['drawer_name'] }}
                                </td>
                                <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">
                                    {{ $row['time_in'] }}
                                </td>
                                <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">
                                    {{ $row['time_out'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @empty
            <div class="p-8 text-center text-sm text-gray-500">
                No shift logs found for the selected filters.
            </div>
        @endforelse
    </div>
</div>
