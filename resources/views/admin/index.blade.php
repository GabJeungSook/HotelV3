@section('breadcrumbs', 'Admin Dashboard - ' . auth()->user()->branch->name)
<x-shared-admin-layout>
  <div>
    <livewire:components.dashboard />
  </div>
</x-shared-admin-layout>
