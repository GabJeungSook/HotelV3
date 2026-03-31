<div>
    {{-- Today's Summary --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <div class="rounded-xl bg-white p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Checked In Today</p>
                    <p class="mt-2 text-3xl font-bold text-gray-800">{{ $check_in_today }}</p>
                    <p class="mt-0.5 text-xs text-gray-400">guests</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50">
                    <svg class="h-6 w-6 text-[#009EF5]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Checked Out Today</p>
                    <p class="mt-2 text-3xl font-bold text-gray-800">{{ $check_out_today }}</p>
                    <p class="mt-0.5 text-xs text-gray-400">guests</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50">
                    <svg class="h-6 w-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtered Stats --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-gray-800">{{ $cardLabel }} Overview</h2>
            <select wire:model.live="cardFilter" class="rounded-lg border-gray-200 bg-white text-xs font-medium text-gray-600 shadow-sm focus:ring-[#009EF5] focus:border-[#009EF5] py-1.5 pl-3 pr-8">
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
                <option value="year">This Year</option>
                <option value="overall">Overall</option>
            </select>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Check In --}}
            <div class="rounded-xl bg-white p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50">
                        <svg class="h-4.5 w-4.5 text-[#009EF5]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $total_check_in }}</p>
                        <p class="text-[11px] text-gray-400">Check In</p>
                    </div>
                </div>
            </div>
            {{-- Total Check Out --}}
            <div class="rounded-xl bg-white p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50">
                        <svg class="h-4.5 w-4.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M22 10.5h-6m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $total_check_out }}</p>
                        <p class="text-[11px] text-gray-400">Check Out</p>
                    </div>
                </div>
            </div>
            {{-- Available Rooms --}}
            <div class="rounded-xl bg-white p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-violet-50">
                        <svg class="h-4.5 w-4.5 text-violet-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $total_available_rooms }}</p>
                        <p class="text-[11px] text-gray-400">Available</p>
                    </div>
                </div>
            </div>
            {{-- Cleaning Rooms --}}
            <div class="rounded-xl bg-white p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-50">
                        <svg class="h-4.5 w-4.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $total_cleaning_rooms }}</p>
                        <p class="text-[11px] text-gray-400">Cleaning</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    @if(auth()->user()->hasAnyRole(['admin', 'superadmin', 'back_office', 'frontdesk']))
        <livewire:components.chart />
    @endif
</div>
