<div>
  <div class="assigned">
    <div class="mb-4">
      <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Shift: {{ $shift }}</h2>
      <p class="text-sm text-gray-400">{{ now()->format('F d, Y h:i A') }}</p>
    </div>

    <div class="border-t pt-4">
      <label class="block text-sm font-medium text-gray-700 mb-2">Select Cash Drawer</label>
      <x-native-select wire:model="drawer">
        <option selected hidden>Select Cash Drawer</option>

        @if($cash_drawers->count() > 0)
          <optgroup label="Available Drawers">
            @foreach ($cash_drawers as $d)
              <option value="{{ $d->id }}">{{ $d->name }}</option>
            @endforeach
          </optgroup>
        @endif

        @if($active_drawers->count() > 0)
          <optgroup label="Join Existing Session">
            @foreach ($active_drawers as $d)
              @if($d->open_session)
                <option value="{{ $d->id }}">{{ $d->name }} — In use by {{ $d->member_names }}</option>
              @endif
            @endforeach
          </optgroup>
        @endif
      </x-native-select>

      <div class="flex justify-end mt-4">
        <x-button label="Proceed" positive right-icon="arrow-right" x-on:confirm="{
          title: 'Are you sure?',
          description: 'Start or join a shift session with the selected cash drawer.',
          icon: 'warning',
          method: 'saveFrontdesk'
        }" />
      </div>
    </div>
  </div>
</div>
