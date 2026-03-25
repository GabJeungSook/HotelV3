<div>
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="font-bold text-xl text-[#009EF5] uppercase">{{ $branchName }}</h1>
            <p class="text-sm text-gray-400">Manage users and credentials for this branch.</p>
        </div>
        <a href="{{ route('superadmin.branches') }}" class="text-sm text-[#009EF5] hover:underline flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Back to Branches
        </a>
    </div>
    {{ $this->table }}
</div>
