<div class="max-w-full mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6">

        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-900">
                Daily Frontdesk Report
                @if($shift)
                    - {{ strtoupper($shift) }} SHIFT
                @endif
            </h1>

            @if($date)
                <p class="mt-1 text-sm text-gray-500">
                    {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}
                </p>
            @endif
        </div>

        {{-- Filters --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Date
                </label>
                <input
                    type="date"
                    wire:model="date"
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Shift
                </label>
                <select
                    wire:model="shift"
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">All</option>
                    <option value="AM">AM</option>
                    <option value="PM">PM</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button
                    wire:click="$refresh"
                    type="button"
                    class="inline-flex justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
                >
                    Apply
                </button>

                <button
                    wire:click="resetFilters"
                    type="button"
                    class="inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Reset
                </button>
            </div>
        </div>

        {{-- Shift Information --}}
        <div class="border-t pt-6 grid grid-cols-2 gap-y-4 text-sm">
            <div class="font-medium text-gray-600">
                Frontdesk Outgoing:
            </div>
            <div class="font-semibold text-gray-900">
                {{ $shiftSummary['frontdesk_outgoing'] ?? '-' }}
            </div>

            <div class="font-medium text-gray-600">
                Shift Open:
            </div>
            <div class="font-semibold text-gray-900">
                {{ $shiftSummary['shift_open'] ?? '-' }}
            </div>

            <div class="font-medium text-gray-600">
                Shift Close:
            </div>
            <div class="font-semibold text-gray-900">
                {{ $shiftSummary['shift_closed'] ?? '-' }}
            </div>
        </div>

        {{-- Cash Drawer --}}
        <div class="mt-8 border-t pt-6">
            <h2 class="text-base font-bold text-gray-900 mb-4">
                Cash Drawer
            </h2>

            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">
                                Description
                            </th>
                            <th class="border border-gray-300 px-3 py-2 text-right text-sm font-semibold text-gray-800">
                                Amount
                            </th>
                            <th class="border border-gray-300 px-3 py-2 text-right text-sm font-semibold text-gray-800">
                                Forwarded Amount
                            </th>
                            <th class="border border-gray-300 px-3 py-2 text-right text-sm font-semibold text-gray-800">
                                Total Amount
                            </th>
                            <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">
                                Remarks
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($cashDrawerRows as $row)
                            <tr>
                                <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">
                                    {{ $row['description'] }}
                                </td>

                                <td class="border border-gray-300 px-3 py-3 text-sm text-right text-gray-900">
                                    {{ number_format((float) $row['amount'], 2) }}
                                </td>

                                <td class="border border-gray-300 px-3 py-3 text-sm text-right text-gray-900">
                                    {{ is_null($row['forwarded_amount']) ? '-' : number_format((float) $row['forwarded_amount'], 2) }}
                                </td>

                                <td class="border border-gray-300 px-3 py-3 text-sm text-right font-semibold text-gray-900">
                                    {{ number_format((float) $row['total_amount'], 2) }}
                                </td>

                                <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">
                                    {{ $row['remarks'] ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="border border-gray-300 px-3 py-3 text-sm text-center text-gray-500">
                                    No cash drawer data found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 border-t pt-6">
    <h2 class="text-base font-bold text-gray-900 mb-4">
        Frontdesk Operation
    </h2>

    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse">
            <thead>
                <tr>
                    <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">
                        Description
                    </th>
                    <th class="border border-gray-300 px-3 py-2 text-right text-sm font-semibold text-gray-800">
                        Total Rooms
                    </th>
                    <th class="border border-gray-300 px-3 py-2 text-right text-sm font-semibold text-gray-800">
                        Amount
                    </th>
                    <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">
                        Remarks
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse($frontdeskOperationRows as $row)
                    <tr>
                        <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">
                            {{ $row['description'] }}
                        </td>

                        <td class="border border-gray-300 px-3 py-3 text-sm text-right text-gray-900">
                            {{ number_format((int) $row['total_rooms']) }}
                        </td>

                        <td class="border border-gray-300 px-3 py-3 text-sm text-right text-gray-900">
                            {{ number_format((float) $row['amount'], 2) }}
                        </td>

                        <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">
                            {{ $row['remarks'] ?: '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="border border-gray-300 px-3 py-3 text-sm text-center text-gray-500">
                            No frontdesk operation data found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-8 border-t pt-6">
    <h2 class="text-base font-bold text-gray-900 mb-4">
        Room Activity
    </h2>

    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse">
            <thead>
                <tr>
                    <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">
                        Description
                    </th>
                    <th class="border border-gray-300 px-3 py-2 text-right text-sm font-semibold text-gray-800">
                        Number of Rooms
                    </th>
                    <th class="border border-gray-300 px-3 py-2 text-right text-sm font-semibold text-gray-800">
                        Amount
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse($roomActivityRows as $row)
                    <tr>
                        <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">
                            {{ $row['description'] }}
                        </td>

                        <td class="border border-gray-300 px-3 py-3 text-sm text-right text-gray-900">
                            {{ number_format((int) $row['number_of_rooms']) }}
                        </td>

                        <td class="border border-gray-300 px-3 py-3 text-sm text-right text-gray-900">
                            {{ number_format((float) $row['amount'], 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="border border-gray-300 px-3 py-3 text-sm text-center text-gray-500">
                            No room activity data found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-8 border-t pt-6">
    <h2 class="text-base font-bold text-gray-900 mb-4">
        Cash Reconciliation
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-lg border border-gray-200 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                Expected Cash
            </p>
            <p class="mt-2 text-base font-semibold text-gray-900">
                {{ number_format((float) ($cashReconciliation['expected_cash'] ?? 0), 2) }}
            </p>
        </div>

        <div class="rounded-lg border border-gray-200 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                Actual Cash
            </p>
            <p class="mt-2 text-base font-semibold text-gray-900">
                {{ number_format((float) ($cashReconciliation['actual_cash'] ?? 0), 2) }}
            </p>
        </div>

        <div class="rounded-lg border border-gray-200 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                Difference
            </p>
            <p class="mt-2 text-base font-semibold text-gray-900">
                {{ number_format((float) ($cashReconciliation['difference'] ?? 0), 2) }}
            </p>
        </div>
    </div>
</div>

<div class="mt-8 border-t pt-6">
    <h2 class="text-base font-bold text-gray-900 mb-4">
        Final Sales
    </h2>

    <div class="grid grid-cols-2 gap-y-4 text-sm">
        <div class="font-medium text-gray-600">
            Gross Sales:
        </div>
        <div class="font-semibold text-gray-900 text-right">
            {{ number_format((float) ($finalSales['gross_sales'] ?? 0), 2) }}
        </div>

        <div class="font-medium text-gray-600">
            Expenses:
        </div>
        <div class="font-semibold text-gray-900 text-right">
            {{ number_format((float) ($finalSales['expenses'] ?? 0), 2) }}
        </div>

        <div class="font-medium text-gray-600">
            Discounts:
        </div>
        <div class="font-semibold text-gray-900 text-right">
            {{ number_format((float) ($finalSales['discounts'] ?? 0), 2) }}
        </div>

        <div class="font-medium text-gray-600">
            Net Sales:
        </div>
        <div class="font-bold text-gray-900 text-right">
            {{ number_format((float) ($finalSales['net_sales'] ?? 0), 2) }}
        </div>
    </div>
</div>

    </div>

</div>