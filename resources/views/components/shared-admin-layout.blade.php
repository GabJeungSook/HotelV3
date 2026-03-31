<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>HIMS — {{ auth()->user()->hasRole('superadmin') ? 'Superadmin' : 'Admin' }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>body { font-family: 'DM Sans', sans-serif; }</style>
  @wireUiScripts
  @filamentStyles
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js"></script>
  @livewireStyles
</head>
<body class="bg-gray-50 antialiased" x-data="{ sidebarOpen: false, logout: false }">

  {{-- Mobile sidebar overlay --}}
  <div x-show="sidebarOpen" x-cloak class="relative z-40 md:hidden">
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/60"></div>
    <div class="fixed inset-0 flex">
      <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative flex w-72 flex-col">
        <div class="absolute right-0 top-0 -mr-12 pt-2">
          <button type="button" x-on:click="sidebarOpen = false" class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none text-white">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>
        @include('components.partials.admin-sidebar')
      </div>
    </div>
  </div>

  {{-- Desktop sidebar --}}
  <div class="hidden md:fixed md:inset-y-0 md:flex md:w-64 md:flex-col z-30">
    @include('components.partials.admin-sidebar')
  </div>

  {{-- Main content --}}
  <div class="flex flex-1 flex-col md:pl-64">
    {{-- Top bar --}}
    <div class="sticky top-0 z-20 flex h-14 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:px-6">
      <button type="button" x-on:click="sidebarOpen = true" class="md:hidden -m-2.5 p-2.5 text-gray-500 hover:text-gray-700">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
      </button>
      <div class="flex flex-1 items-center justify-between">
        <div>
          <h1 class="text-xl font-bold text-gray-800">@yield('breadcrumbs')</h1>
          <p class="text-xs text-gray-400">@yield('childBreadcrumbs')</p>
        </div>
        <a href="{{ route('admin.settings') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-[#009EF5] px-3.5 py-2 text-xs font-semibold text-white hover:bg-[#0080cc] transition shadow-sm">
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
          Settings
        </a>
      </div>
    </div>

    {{-- Page content --}}
    <main class="flex-1 p-6">
      {{ $slot }}
    </main>
  </div>

  {{-- Logout modal --}}
  <div x-show="logout" x-cloak class="relative z-50">
    <div x-show="logout" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/50 transition-opacity"></div>
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div x-show="logout" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="w-full max-w-sm rounded-xl bg-white shadow-2xl">
        <div class="p-6 text-center">
          <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900">Logout</h3>
          <p class="mt-1 text-sm text-gray-500">Are you sure you want to logout?</p>
        </div>
        <div class="flex gap-2 border-t border-gray-100 px-6 py-4">
          <button @click="logout = false" class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Cancel</button>
          <form method="POST" action="{{ route('logout') }}" class="flex-1">
            @csrf
            <button type="submit" class="w-full rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition">Logout</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  @filamentScripts
  <x-dialog z-index="z-50" blur="md" align="center" />
  @livewireScriptConfig
</body>
</html>
