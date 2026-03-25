<div>
    {{$this->table}}

    <div class="mt-5 bg-white rounded-lg p-6">
        {{-- transfer room table --}}
        <h1 class="font-bold text-xl text-[#009EF5] uppercase">Transfer Room Reasons</h1>
        <div class="py-4">
                <livewire:admin.room-transfer-reasons />
        </div>
        {{-- staying hours table --}}
        <hr class="my-4 border-t border-gray-300">
        <h1 class="font-bold text-xl text-[#009EF5] uppercase">Staying Hours</h1>
        <div class="py-4">
                <livewire:admin.manage-staying-hours />
        </div>
    </div>
</div>
