<div>
    <nav class="fi-tabs flex max-w-full gap-x-1 overflow-x-auto fi-contained border-b border-gray-200 px-3 py-2.5">
        <button type="button" wire:click="$set('type', 1)"
            class="fi-tabs-item group flex items-center justify-center gap-x-2 whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium outline-none transition duration-75
            {{ $type == 1 ? 'fi-active fi-tabs-item-active' : 'hover:bg-gray-50' }}"
            style="{{ $type == 1 ? 'background-color: rgba(0, 158, 245, 0.08);' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="h-5 w-5 shrink-0 transition duration-75 {{ $type == 1 ? 'text-[#009EF5]' : 'text-gray-400' }}">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-1.997M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
            </svg>
            <span class="fi-tabs-item-label transition duration-75 {{ $type == 1 ? 'text-[#009EF5]' : 'text-gray-500' }}">Users</span>
        </button>
        <button type="button" wire:click="$set('type', 2)"
            class="fi-tabs-item group flex items-center justify-center gap-x-2 whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium outline-none transition duration-75
            {{ $type == 2 ? 'fi-active fi-tabs-item-active' : 'hover:bg-gray-50' }}"
            style="{{ $type == 2 ? 'background-color: rgba(0, 158, 245, 0.08);' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="h-5 w-5 shrink-0 transition duration-75 {{ $type == 2 ? 'text-[#009EF5]' : 'text-gray-400' }}">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
            </svg>
            <span class="fi-tabs-item-label transition duration-75 {{ $type == 2 ? 'text-[#009EF5]' : 'text-gray-500' }}">Manage Frontdesk</span>
        </button>
        <button type="button" wire:click="$set('type', 3)"
            class="fi-tabs-item group flex items-center justify-center gap-x-2 whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium outline-none transition duration-75
            {{ $type == 3 ? 'fi-active fi-tabs-item-active' : 'hover:bg-gray-50' }}"
            style="{{ $type == 3 ? 'background-color: rgba(0, 158, 245, 0.08);' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="h-5 w-5 shrink-0 transition duration-75 {{ $type == 3 ? 'text-[#009EF5]' : 'text-gray-400' }}">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.384 3.112 1.03-5.994-4.354-4.247 6.02-.876L11.42 1.5l2.689 5.665 6.02.876-4.354 4.247 1.03 5.994-5.384-3.112z" />
            </svg>
            <span class="fi-tabs-item-label transition duration-75 {{ $type == 3 ? 'text-[#009EF5]' : 'text-gray-500' }}">Roomboy Designation</span>
        </button>
    </nav>

    <div class="pt-2">
        @if ($type == 1)
            {{ $this->table }}
        @elseif ($type == 2)
            <livewire:admin.manage-frondesk />
        @elseif ($type == 3)
            <livewire:admin.roomboy-designation />
        @endif
    </div>
</div>
