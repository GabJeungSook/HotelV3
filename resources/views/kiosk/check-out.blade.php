<x-kiosk-layout-update>
  <div class="p-6">
    <div>
      <x-button id="dashboard" href="{{ route('kiosk.dashboard') }}" label="Select Transaction" icon="arrow-left" positive lg class="font-bold" />
    </div>
    @livewire('kiosk.check-out')
  </div>
</x-kiosk-layout-update>
