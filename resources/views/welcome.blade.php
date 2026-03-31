<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HOMI — Hotel Management System</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css'])
  <style>
    body { font-family: 'DM Sans', sans-serif; }
    @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-12px); } }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .animate-float { animation: float 6s ease-in-out infinite; }
    .animate-fade-up { animation: fadeUp 0.8s ease-out forwards; }
    .animate-fade-up-delay-1 { animation: fadeUp 0.8s ease-out 0.15s forwards; opacity: 0; }
    .animate-fade-up-delay-2 { animation: fadeUp 0.8s ease-out 0.3s forwards; opacity: 0; }
    .animate-fade-up-delay-3 { animation: fadeUp 0.8s ease-out 0.45s forwards; opacity: 0; }
    .animate-fade-in { animation: fadeIn 1.2s ease-out forwards; }
  </style>
</head>
<body class="bg-white antialiased overflow-x-hidden">

  @if(app()->environment('staging'))
    <div class="fixed top-0 left-0 w-full bg-red-600 text-white text-center py-1 text-xs font-semibold z-50">
      STAGING ENVIRONMENT
    </div>
  @endif

  {{-- Background pattern + decorations --}}
  <div class="fixed inset-0 -z-10 overflow-hidden">
    {{-- Subtle dot pattern --}}
    <svg class="absolute inset-0 h-full w-full" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <pattern id="dot-pattern" x="0" y="0" width="24" height="24" patternUnits="userSpaceOnUse">
          <circle cx="1.5" cy="1.5" r="1.2" fill="#d1d5db" opacity="0.7" />
        </pattern>
      </defs>
      <rect width="100%" height="100%" fill="url(#dot-pattern)" />
    </svg>
    {{-- Gradient blobs --}}
    <div class="absolute -top-40 -right-40 h-[500px] w-[500px] rounded-full bg-[#009EF5]/5 blur-3xl"></div>
    <div class="absolute -bottom-40 -left-40 h-[400px] w-[400px] rounded-full bg-[#009EF5]/5 blur-3xl"></div>
    <div class="absolute top-1/3 right-1/4 h-[300px] w-[300px] rounded-full bg-[#009EF5]/[0.03] blur-3xl"></div>
  </div>

  {{-- Navigation --}}
  <nav class="relative z-10 mx-auto flex max-w-6xl items-center justify-between px-6 py-6 lg:px-8">
    <a href="/" class="flex items-center">
      <img src="{{ asset('images/homiLogo.png') }}" alt="HOMI" class="h-9 w-auto">
    </a>
    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-300 transition-all duration-200">
      Sign In
      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
    </a>
  </nav>

  {{-- Hero Section --}}
  <section class="relative z-10 mx-auto max-w-6xl px-6 lg:px-8 pt-16 sm:pt-24 lg:pt-32 pb-20">
    <div class="flex flex-col lg:flex-row items-center gap-16 lg:gap-24">
      {{-- Left: Text --}}
      <div class="flex-1 text-center lg:text-left">
        <div class="animate-fade-up inline-flex items-center gap-2 rounded-full bg-[#009EF5]/10 px-4 py-1.5 mb-6">
          <div class="h-1.5 w-1.5 rounded-full bg-[#009EF5] animate-pulse"></div>
          <span class="text-xs font-semibold text-[#009EF5] uppercase tracking-wider">Hotel Management System</span>
        </div>

        <h1 class="animate-fade-up-delay-1 text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 leading-[1.1] tracking-tight">
          Smarter way to
          <span class="relative">
            <span class="text-[#009EF5]">manage</span>
            <svg class="absolute -bottom-1 left-0 w-full" viewBox="0 0 200 8" fill="none"><path d="M1 5.5C47 2 153 2 199 5.5" stroke="#009EF5" stroke-width="2" stroke-linecap="round" opacity="0.3"/></svg>
          </span>
          <br>your hotel.
        </h1>

        <p class="animate-fade-up-delay-2 mt-6 text-lg text-gray-500 max-w-lg mx-auto lg:mx-0 leading-relaxed">
          Streamline check-ins, room management, billing, and operations — all from one powerful platform.
        </p>

        <div class="animate-fade-up-delay-3 mt-10 flex flex-col sm:flex-row items-center gap-4 justify-center lg:justify-start">
          <a href="{{ route('login') }}" class="group inline-flex items-center gap-2.5 rounded-xl bg-[#009EF5] px-7 py-3.5 text-sm font-semibold text-white shadow-lg shadow-[#009EF5]/25 hover:bg-[#0080cc] hover:shadow-[#009EF5]/40 transition-all duration-300">
            Get Started
            <svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
          </a>
        </div>
      </div>

      {{-- Right: Room Preview --}}
      <div class="flex-1 relative animate-fade-in">
        <div class="relative max-w-md mx-auto">
          {{-- Main room image --}}
          <div class="animate-float rounded-2xl overflow-hidden shadow-2xl shadow-gray-300/40">
            <img src="https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=800&q=80" alt="Hotel Room" class="w-full h-72 object-cover">
            {{-- Overlay gradient --}}
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/60 via-transparent to-transparent rounded-2xl"></div>
            {{-- Room info overlay --}}
            <div class="absolute bottom-0 left-0 right-0 p-5">
              <div class="flex items-end justify-between">
                <div>
                  <p class="text-white/70 text-[10px] uppercase tracking-widest font-medium">Deluxe Room</p>
                  <p class="text-white text-lg font-bold mt-0.5">Modern Comfort</p>
                </div>
                <div class="flex items-center gap-1 rounded-full bg-white/20 backdrop-blur-sm px-2.5 py-1">
                  <div class="h-1.5 w-1.5 rounded-full bg-emerald-400"></div>
                  <span class="text-white text-[10px] font-semibold">Available</span>
                </div>
              </div>
            </div>
          </div>

          {{-- Floating type cards from DB --}}
          @if($types->count() > 0)
          <div class="absolute -top-3 -right-3 sm:-right-6 rounded-xl overflow-hidden shadow-lg border-2 border-white animate-fade-up-delay-2 w-28" style="animation-delay: 0.6s;">
            <img src="https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=300&q=80" alt="{{ $types[0]->name }}" class="w-full h-20 object-cover">
            <div class="bg-white px-2 py-1.5">
              <p class="text-[10px] font-bold text-gray-800">{{ $types[0]->name }}</p>
              <p class="text-[9px] text-[#009EF5] font-semibold">{{ $types[0]->rooms_count }} Rooms</p>
            </div>
          </div>
          @endif

          @if($types->count() > 1)
          <div class="absolute -bottom-3 -left-3 sm:-left-6 rounded-xl overflow-hidden shadow-lg border-2 border-white animate-fade-up-delay-3 w-28" style="animation-delay: 0.9s;">
            <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=300&q=80" alt="{{ $types[1]->name }}" class="w-full h-20 object-cover">
            <div class="bg-white px-2 py-1.5">
              <p class="text-[10px] font-bold text-gray-800">{{ $types[1]->name }}</p>
              <p class="text-[9px] text-[#009EF5] font-semibold">{{ $types[1]->rooms_count }} Rooms</p>
            </div>
          </div>
          @endif

          {{-- Total rooms pill --}}
          <div class="absolute top-3 -left-3 sm:-left-6 rounded-xl bg-white shadow-lg border border-gray-100 px-3 py-2.5 animate-fade-up-delay-2" style="animation-delay: 0.5s;">
            <div class="flex items-center gap-2">
              <div class="flex h-7 w-7 items-center justify-center rounded-full bg-[#009EF5]/10">
                <svg class="h-3.5 w-3.5 text-[#009EF5]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
              </div>
              <div>
                <p class="text-xs font-bold text-gray-800">{{ $totalRooms }}</p>
                <p class="text-[9px] text-gray-400 -mt-0.5">Total Rooms</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- Features Section --}}
  <section class="relative z-10 mx-auto max-w-6xl px-6 lg:px-8 pb-24">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
      <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6 hover:shadow-md hover:border-gray-200 transition-all duration-300">
        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 mb-4">
          <svg class="h-5 w-5 text-[#009EF5]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-1.997M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
        </div>
        <h3 class="text-sm font-bold text-gray-800">Guest Management</h3>
        <p class="mt-1.5 text-xs text-gray-400 leading-relaxed">Seamless check-in, check-out, and guest tracking with real-time room monitoring.</p>
      </div>

      <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6 hover:shadow-md hover:border-gray-200 transition-all duration-300">
        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 mb-4">
          <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 7.5h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" /></svg>
        </div>
        <h3 class="text-sm font-bold text-gray-800">Multi-Branch</h3>
        <p class="mt-1.5 text-xs text-gray-400 leading-relaxed">Manage multiple hotel branches from a single dashboard with role-based access control.</p>
      </div>

      <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6 hover:shadow-md hover:border-gray-200 transition-all duration-300">
        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-50 mb-4">
          <svg class="h-5 w-5 text-violet-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
        </div>
        <h3 class="text-sm font-bold text-gray-800">Reports & Analytics</h3>
        <p class="mt-1.5 text-xs text-gray-400 leading-relaxed">Comprehensive reports on occupancy, revenue, and operations to drive better decisions.</p>
      </div>
    </div>
  </section>

  {{-- Footer --}}
  <footer class="relative z-10 border-t border-gray-100 py-6">
    <div class="mx-auto max-w-6xl px-6 lg:px-8 flex items-center justify-between">
      <img src="{{ asset('images/homiLogo.png') }}" alt="HOMI" class="h-6 w-auto opacity-40">
      <p class="text-xs text-gray-300">&copy; {{ date('Y') }} HOMI. All rights reserved.</p>
    </div>
  </footer>

</body>
</html>
