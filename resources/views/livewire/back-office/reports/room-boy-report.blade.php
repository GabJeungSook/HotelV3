<div class="max-w-full mx-auto py-8 px-4 sm:px-6 lg:px-8">

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- Roomboy --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Room Boy</label>
                <select wire:model="roomboy_id"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All</option>
                    @foreach($roomboys as $rb)
                        <option value="{{ $rb->id }}">{{ $rb->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Shift --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Shift</label>
                <select wire:model="shift"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All</option>
                    <option value="AM">AM</option>
                    <option value="PM">PM</option>
                </select>
            </div>

            {{-- Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date"
                       wire:model="date"
                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
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

        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <div class="text-sm font-semibold text-gray-900">
                ROOM BOY REPORT
            </div>
            <div class="text-sm font-semibold text-gray-700">
                Total Cleaned: {{ $total_cleaned }}
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead>
                    <tr>
                        <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">DATE/TIME</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">ROOM #</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">ROOM BOY</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">SHIFT</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">STATUS</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($reports as $r)
                        <tr>
                            <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($r->created_at)->format('F d, Y h:i A') }}
                            </td>

                            <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">
                                {{ $r->room?->number ?? '—' }}
                            </td>

                            <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">
                                {{ $r->roomboy?->name ?? '—' }}
                            </td>

                            <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">
                                {{ $r->shift ?? '—' }}
                            </td>

                            <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">
                                {{ $r->is_cleaned ? 'CLEANED' : 'NOT CLEANED' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border border-gray-300 px-3 py-6 text-sm text-center text-gray-500">
                                No room boy records found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="p-6 border-t border-gray-200">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-10">
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

    </div>
</div>
