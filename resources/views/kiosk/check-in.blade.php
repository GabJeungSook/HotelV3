<x-kiosk-layout-update>
  <div class="p-6">
    <div>

     {{-- <button id="print_qr" data-value="123" class="p-4 bg-green-500">Print QR</button> --}}
      <x-button id="dashboard" href="{{ route('kiosk.dashboard') }}" label="Select Transaction" icon="arrow-left" positive lg class="font-bold" />
    </div>
    @livewire('kiosk.check-in')
    {{-- <livewire:kiosk.check-in /> --}}
  </div>
</x-kiosk-layout-update>
