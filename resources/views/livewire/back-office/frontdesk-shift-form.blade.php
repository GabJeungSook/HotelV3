<div>

<div class="flex mb-5">
<x-button wire:click="redirectToTable" icon="arrow-left" red label="Back" />
</div>

<div class="max-w-6xl mx-auto p-6 space-y-8">

<form wire:submit.prevent="save" class="space-y-8">

@include('livewire.back-office.form')

<button class="bg-blue-600 text-white px-6 py-2 rounded-lg">
Save Shift
</button>

</form>

</div>

</div>