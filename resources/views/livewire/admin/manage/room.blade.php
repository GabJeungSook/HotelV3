<div>
    <div class="p-4 bg-white rounded-xl ">
        <div class="mb-5">
            <div class="flex space-x-2">
                <x-badge rounded class="font-normal" positive md label="Available" />
                <x-badge rounded class="font-normal" flat positive md label="Occupied" />
                <x-badge rounded class="font-normal" dark flat md label="Reserved" />
                <x-badge rounded class="font-normal" flat violet md label="Maintenance" />
                <x-badge rounded class="font-normal" flat negative md label="Uncleaned" />
                <x-badge rounded class="font-normal" flat red md label="Cleaning" />
                <x-badge rounded class="font-normal" flat blue md label="Cleaned" />
            </div>
        </div>
        <div
            class="sr-only !text-white !bg-green-700 !text-green !bg-green-200  !text-gray-800 !bg-gray-400 !text-indigo-800 !bg-indigo-400 !text-red-800 !bg-red-400 !text-blue-800 !bg-blue-400">
        </div>
        {{ $this->table }}
    </div>
</div>
