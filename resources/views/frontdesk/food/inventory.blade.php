@section('breadcrumbs', 'Food Inventory')
<x-shared-admin-layout>
    <div>
    <livewire:frontdesk.food.inventory :record="$record" />
    </div>
  </x-shared-admin-layout>
