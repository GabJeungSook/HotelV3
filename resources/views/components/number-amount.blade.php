
@props([
    'label',
    'number',
    'amount'
])
<div class="space-y-1">
    
    <label class="text-sm font-medium text-gray-700">
        {{ $label }}
    </label>

    <div class="flex gap-2">

        <input
            type="text"
            wire:model="{{ $number }}"
            placeholder="Number"
            class="border rounded-lg px-3 py-2 w-full focus:ring focus:ring-blue-200"
        >

        <input
            type="text"
            wire:model="{{ $amount }}"
            placeholder="Amount"
            class="border rounded-lg px-3 py-2 w-full focus:ring focus:ring-blue-200"
        >

    </div>

</div>