<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">

  <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">

  <style>
    body, * { font-family: 'DM Sans', sans-serif; }

    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: #888; }
    ::-webkit-scrollbar-thumb:hover { background: #555; }
  </style>
  @wireUiScripts
  @filamentStyles
  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <!-- Styles -->
  @livewireStyles
</head>

@if(app()->environment('staging'))
  <div class="fixed top-0 left-0 w-full bg-red-600 text-white text-center py-1 text-sm font-semibold z-50 animate-pulse">
    STAGING ENVIRONMENT
  </div>
  <div style="height: 20px;"></div>
@endif

<body class="antialiased" style="font-family: 'DM Sans', sans-serif;" x-data="{ logout: false }">
  <div class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
    <!-- Header -->
    <header class="bg-gradient-to-r from-[#009EF5] to-[#0077c2] shadow-lg">
      <div class="flex justify-between items-center px-6 py-4">
        <button onclick="goFullscreen()" class="flex items-center space-x-3">
          <img src="{{ asset('images/homiLogo2.png') }}" alt="HOMI Logo" class="h-8 brightness-0 invert">
        </button>
        <button x-on:click="logout = true"
          class="p-2 rounded-lg hover:bg-white/20 transition-colors duration-200">
          <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
          </svg>
        </button>
      </div>
    </header>

    <!-- Content -->
    <main class="p-6">
      {{ $slot }}
    </main>
  </div>

  <!-- Logout Modal -->
  <div x-show="logout" x-cloak class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div x-show="logout" x-cloak x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
      x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
      x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity">
    </div>

    <div class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div x-show="logout" x-cloak x-transition:enter="ease-out duration-300"
          x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
          x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
          x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                  viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">Logout Account</h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">Are you sure you want to logout your account?</p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
            <form method="POST" action="{{ route('logout') }}" class="flex space-x-2">
              @csrf
              <x-button @click="logout=false" label="Cancel" sm icon="x-mark" />
              <x-button href="{{ route('logout') }}"
                onclick="event.preventDefault();
              this.closest('form').submit();" label="Logout"
                icon="arrow-right-start-on-rectangle" sm negative />
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <x-dialog z-index="z-50" blur="md" align="center" />
  @filamentScripts
  @livewireScriptConfig
</body>

<script>
function goFullscreen() {
  const elem = document.documentElement;

  if (
    document.fullscreenElement ||
    document.webkitFullscreenElement ||
    document.msFullscreenElement
  ) {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.webkitExitFullscreen) {
      document.webkitExitFullscreen();
    } else if (document.msExitFullscreen) {
      document.msExitFullscreen();
    }
  } else {
    if (elem.requestFullscreen) {
      elem.requestFullscreen();
    } else if (elem.webkitRequestFullscreen) {
      elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) {
      elem.msRequestFullscreen();
    }
  }
}
</script>
</html>
