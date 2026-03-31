<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>HOMI — Sign In</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'DM Sans', sans-serif; }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-up { animation: fadeUp 0.6s ease-out forwards; }
    .animate-fade-up-delay { animation: fadeUp 0.6s ease-out 0.15s forwards; opacity: 0; }
  </style>
  @wireUiScripts
  @filamentStyles
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @livewireStyles
</head>
<body class="h-full bg-gray-50 antialiased">
  {{ $slot }}
  @livewireScriptConfig
  @filamentScripts
</body>
</html>
