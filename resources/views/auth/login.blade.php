<x-guest-layout>
  {{-- Background pattern + blobs --}}
  <div class="fixed inset-0 -z-10 overflow-hidden">
    <svg class="absolute inset-0 h-full w-full" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <pattern id="login-dots" x="0" y="0" width="24" height="24" patternUnits="userSpaceOnUse">
          <circle cx="1.5" cy="1.5" r="1.2" fill="#d1d5db" opacity="0.7" />
        </pattern>
      </defs>
      <rect width="100%" height="100%" fill="url(#login-dots)" />
    </svg>
    <div class="absolute -top-40 -right-40 h-[500px] w-[500px] rounded-full bg-[#009EF5]/5 blur-3xl"></div>
    <div class="absolute -bottom-40 -left-40 h-[400px] w-[400px] rounded-full bg-[#009EF5]/5 blur-3xl"></div>
  </div>

  <div class="flex min-h-full">
    {{-- Left panel: branding --}}
    <div class="hidden lg:flex lg:w-1/2 items-center justify-center relative overflow-hidden">
      <div class="relative z-10 max-w-md px-12">
        <div class="animate-fade-up">
          <img src="{{ asset('images/homiLogo.png') }}" alt="HOMI" class="h-12 w-auto mb-8">
          <h1 class="text-3xl font-bold text-gray-800 leading-tight">
            Welcome back to<br>
            <span class="text-[#009EF5]">HOMI</span> Hotel Management
          </h1>
          <p class="mt-4 text-sm text-gray-400 leading-relaxed">
            Manage your hotel operations, track guests, and monitor rooms — all from one powerful dashboard.
          </p>
        </div>

        {{-- Feature pills --}}
        <div class="animate-fade-up-delay mt-8 flex flex-wrap gap-2">
          <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 border border-blue-100 px-3 py-1.5 text-[11px] font-medium text-[#009EF5]">
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
            Real-time Monitoring
          </span>
          <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-100 px-3 py-1.5 text-[11px] font-medium text-emerald-600">
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
            Multi-Branch
          </span>
          <span class="inline-flex items-center gap-1.5 rounded-full bg-violet-50 border border-violet-100 px-3 py-1.5 text-[11px] font-medium text-violet-600">
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
            Role-Based Access
          </span>
        </div>
      </div>
    </div>

    {{-- Right panel: login form --}}
    <div class="flex w-full lg:w-1/2 items-center justify-center px-6 py-12">
      <div class="w-full max-w-sm">
        {{-- Mobile logo --}}
        <div class="lg:hidden mb-8 text-center animate-fade-up">
          <img src="{{ asset('images/homiLogo.png') }}" alt="HOMI" class="h-10 w-auto mx-auto mb-4">
        </div>

        <div class="animate-fade-up">
          <h2 class="text-2xl font-bold text-gray-800">Sign in</h2>
          <p class="mt-1 text-sm text-gray-400">Enter your credentials to access the dashboard.</p>
        </div>

        <x-validation-errors class="mt-4" />

        <form class="mt-8 space-y-5 animate-fade-up-delay" method="POST" action="{{ route('login') }}">
          @csrf

          <div>
            <label for="email" class="block text-xs font-semibold text-gray-600 mb-1.5">Email Address</label>
            <div class="relative">
              <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
              </div>
              <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                class="block w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-800 placeholder-gray-400 shadow-sm transition-all focus:border-[#009EF5] focus:ring-2 focus:ring-[#009EF5]/20 focus:outline-none"
                placeholder="you@example.com">
            </div>
          </div>

          <div>
            <label for="password" class="block text-xs font-semibold text-gray-600 mb-1.5">Password</label>
            <div class="relative">
              <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
              </div>
              <input id="password" name="password" type="password" required autocomplete="current-password"
                class="block w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-800 placeholder-gray-400 shadow-sm transition-all focus:border-[#009EF5] focus:ring-2 focus:ring-[#009EF5]/20 focus:outline-none"
                placeholder="Enter your password">
            </div>
          </div>

          <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
              <input id="remember-me" name="remember-me" type="checkbox"
                class="h-4 w-4 rounded border-gray-300 text-[#009EF5] focus:ring-[#009EF5]/30 transition">
              <span class="text-xs text-gray-500">Remember me</span>
            </label>
          </div>

          <button type="submit"
            class="group flex w-full items-center justify-center gap-2 rounded-xl bg-[#009EF5] px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-[#009EF5]/25 transition-all duration-300 hover:bg-[#0080cc] hover:shadow-[#009EF5]/40 focus:outline-none focus:ring-2 focus:ring-[#009EF5]/50 focus:ring-offset-2">
            Sign In
            <svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
          </button>
        </form>

        {{-- Footer --}}
        <p class="mt-8 text-center text-[11px] text-gray-300">&copy; {{ date('Y') }} HOMI. All rights reserved.</p>
      </div>
    </div>
  </div>
</x-guest-layout>
