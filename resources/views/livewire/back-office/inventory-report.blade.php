<div class="max-w-full mx-auto py-8 px-4 sm:px-6 lg:px-8">

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- Category --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select wire:model="category_id"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Item --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Item</label>
                <select wire:model="item_id"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>

            <div></div>

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

        <div class="px-4 py-3 border-b border-gray-200">
            <div class="text-sm font-semibold text-gray-900">INVENTORY REPORT</div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead>
                    <tr>
                        <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">Item Code</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">Item Name</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">Category</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">Unit</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">Opening Stock</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">Stock In</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">Stock Out</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">Wastage</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">Closing Stock</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">Unit Cost</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold text-gray-800">Total Value</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($inventories as $row)
                        <tr>
                            <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">{{ $row['item_code'] }}</td>
                            <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">{{ $row['item_name'] }}</td>
                            <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">{{ $row['category'] }}</td>
                            <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">{{ $row['unit'] }}</td>
                            <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">{{ $row['opening_stock'] }}</td>
                            <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">{{ $row['stock_in'] }}</td>
                            <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">{{ $row['stock_out'] }}</td>
                            <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">{{ $row['wastage'] }}</td>
                            <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900 font-semibold">{{ $row['closing_stock'] }}</td>
                            <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">₱ {{ number_format($row['unit_cost'], 2) }}</td>
                            <td class="border border-gray-300 px-3 py-3 text-sm text-gray-900">₱ {{ number_format($row['total_value'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="border border-gray-300 px-3 py-6 text-sm text-center text-gray-500">
                                No inventory records found.
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
