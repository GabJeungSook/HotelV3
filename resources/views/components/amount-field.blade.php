@props([
    'label',
    'amount',
    'sub',
    'remark'
])

<div class="space-y-2">

    <label class="text-sm font-medium text-gray-700">
        {{ $label }}
    </label>

    <div class="flex items-center gap-2">

        <input
            type="text"
            wire:model="{{ $amount }}"
            placeholder="Amount"
            class="border rounded-lg px-3 py-2 w-full"
        >

        <span>(</span>

        <input
            type="text"
            wire:model="{{ $sub }}"
            placeholder="Sub Amount"
            class="border rounded-lg px-3 py-2 w-full"
        >

        <span>)</span>

    </div>

    <input
        type="text"
        wire:model="{{ $remark }}"
        placeholder="Remarks"
        class="border rounded-lg px-3 py-2 w-full"
    >

</div>