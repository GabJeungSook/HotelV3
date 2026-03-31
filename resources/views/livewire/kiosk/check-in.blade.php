<div class="max-w-6xl mx-auto px-2" style="scroll-behavior: smooth;">

    {{-- QR Code Success Screen --}}
    @if($showQr)
    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-10 max-w-md w-full text-center">
            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-blue-50">
                <svg class="h-10 w-10 text-[#009EF5]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-800">Check-in Successful!</h2>
            <p class="mt-2 text-lg text-gray-400">Show this QR code at the front desk.</p>
            <div class="mt-8">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ $generatedQrCode }}" alt="QR Code" class="mx-auto rounded-xl">
            </div>
            <p class="mt-6 font-mono text-2xl font-bold text-gray-800 tracking-widest">{{ $generatedQrCode }}</p>
            <button wire:click="redirectToHome" class="mt-8 w-full rounded-2xl bg-[#009EF5] px-8 py-5 text-lg font-bold text-white shadow-lg shadow-[#009EF5]/25 hover:bg-[#0080cc] transition">
                Done
            </button>
        </div>
    </div>
    @else

    {{-- Back --}}
    <div class="mb-5">
        <a href="{{ route('kiosk.dashboard') }}" class="inline-flex items-center gap-2 text-base text-gray-400 hover:text-gray-600 transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
            Back
        </a>
    </div>

    {{-- Section 1: Guest Info --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-5">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Guest Information</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-500 mb-2">Full Name <span class="text-red-400">*</span></label>
                <input type="text" wire:model="name" placeholder="Enter guest name"
                    class="w-full rounded-2xl border border-gray-200 px-5 py-4 text-lg text-gray-800 placeholder-gray-300 focus:border-[#009EF5] focus:ring-2 focus:ring-[#009EF5]/20 focus:outline-none transition">
                @error('name') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-500 mb-2">Contact Number</label>
                <div class="flex">
                    <span class="inline-flex items-center rounded-l-2xl border border-r-0 border-gray-200 bg-gray-50 px-4 text-lg text-gray-500 font-medium">09</span>
                    <input type="text" wire:model="contact" placeholder="XXXXXXXXX" maxlength="9"
                        class="w-full rounded-r-2xl border border-gray-200 px-5 py-4 text-lg text-gray-800 placeholder-gray-300 focus:border-[#009EF5] focus:ring-2 focus:ring-[#009EF5]/20 focus:outline-none transition">
                </div>
                @error('contact') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Section 2: Select Type --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-5">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Select Room Type</h2>
        <div class="flex flex-wrap gap-3">
            @foreach($types as $type)
                <button wire:click="selectType({{ $type->id }})"
                    class="rounded-2xl px-8 py-4 text-base font-bold border-2 transition-all duration-150 active:scale-95
                    {{ $type_id == $type->id
                        ? 'bg-[#009EF5] text-white border-[#009EF5] shadow-lg shadow-[#009EF5]/25'
                        : 'bg-white text-gray-600 border-gray-200 hover:border-[#009EF5]/40' }}">
                    {{ $type->name }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Section 3: Select Room --}}
    @if($type_id)
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-5"
         x-data x-init="$nextTick(() => $el.scrollIntoView({ behavior: 'smooth', block: 'nearest' }))">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-800">Select Room</h2>
            <div class="flex gap-2">
                <button wire:click="$set('floor_id', null)"
                    class="rounded-full px-4 py-2 text-sm font-semibold transition
                    {{ !$floor_id ? 'bg-[#009EF5] text-white' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                    All Floors
                </button>
                @foreach($floors as $floor)
                    @php
                        $n = $floor->number;
                        $suffix = match(true) {
                            $n % 100 >= 11 && $n % 100 <= 13 => 'th',
                            $n % 10 === 1 => 'st',
                            $n % 10 === 2 => 'nd',
                            $n % 10 === 3 => 'rd',
                            default => 'th',
                        };
                    @endphp
                    <button wire:click="$set('floor_id', {{ $floor->id }})"
                        class="rounded-full px-4 py-2 text-sm font-semibold transition
                        {{ $floor_id == $floor->id ? 'bg-[#009EF5] text-white' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                        {{ $n }}{{ $suffix }} Floor
                    </button>
                @endforeach
            </div>
        </div>
        @if($rooms->count() > 0)
        <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-3">
            @foreach($rooms as $room)
                <button wire:click="selectRoom({{ $room->id }})"
                    class="rounded-2xl border-2 p-4 text-center transition-all duration-150 active:scale-95
                    {{ $room_id == $room->id
                        ? 'bg-[#009EF5] text-white border-[#009EF5] shadow-lg'
                        : 'bg-white text-gray-600 border-gray-200 hover:border-[#009EF5]/40' }}">
                    <div class="text-2xl font-bold">#{{ $room->number }}</div>
                    <div class="text-sm {{ $room_id == $room->id ? 'text-white/70' : 'text-gray-400' }}">Floor {{ $room->floor?->number }}</div>
                </button>
            @endforeach
        </div>
        @else
        <div class="py-10 text-center text-gray-400 text-base">No priority rooms available for this type.</div>
        @endif
    </div>
    @endif

    {{-- Section 4: Select Rate --}}
    @if($room_id)
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-5"
         x-data x-init="$nextTick(() => $el.scrollIntoView({ behavior: 'smooth', block: 'nearest' }))">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Select Rate</h2>
        <div class="flex flex-wrap gap-3 mb-5">
            @foreach($rates as $rate)
                <button wire:click="selectRate({{ $rate->id }})"
                    class="rounded-2xl border-2 px-6 py-4 text-center transition-all duration-150 active:scale-95
                    {{ $rate_id == $rate->id
                        ? 'bg-[#009EF5] text-white border-[#009EF5] shadow-lg'
                        : 'bg-white text-gray-600 border-gray-200 hover:border-[#009EF5]/40' }}">
                    <div class="text-lg font-bold">{{ $rate->stayingHour?->number }} HRS</div>
                    <div class="text-base {{ $rate_id == $rate->id ? 'text-white/80' : 'text-[#009EF5]' }} font-bold">₱{{ number_format($rate->amount, 2) }}</div>
                </button>
            @endforeach
        </div>
        <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
            <span class="text-sm font-bold text-gray-500">Long Stay:</span>
            <input type="number" wire:model.live="longstay" placeholder="Days" min="1" max="31"
                class="w-28 rounded-2xl border border-gray-200 px-4 py-3 text-lg text-center text-gray-800 focus:border-[#009EF5] focus:ring-2 focus:ring-[#009EF5]/20 focus:outline-none transition">
            @if($longstay)
                <span class="text-base text-[#009EF5] font-bold">{{ $longstay }} day(s) — ₱{{ number_format($this->getRoomPay(), 2) }}</span>
            @endif
            @error('longstay') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
        </div>
    </div>
    @endif

    {{-- Section 5: Summary + Confirm --}}
    @if($rate_id || $longstay)
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-5"
         x-data x-init="$nextTick(() => $el.scrollIntoView({ behavior: 'smooth', block: 'nearest' }))">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Booking Summary</h2>
        <div class="bg-blue-50 rounded-2xl p-5 space-y-3">
            <div class="flex justify-between text-base">
                <span class="text-gray-500">Room</span>
                <span class="font-bold text-gray-800">#{{ \App\Models\Room::find($room_id)?->number }} — {{ \App\Models\Type::find($type_id)?->name }}</span>
            </div>
            <div class="flex justify-between text-base">
                <span class="text-gray-500">Duration</span>
                <span class="font-bold text-gray-800">
                    @if($longstay) {{ $longstay }} Day(s) @else {{ $this->stayingHours }} Hours @endif
                </span>
            </div>
            <div class="flex justify-between text-base">
                <span class="text-gray-500">Room Charge</span>
                <span class="font-bold text-gray-800">₱{{ number_format($this->getRoomPay(), 2) }}</span>
            </div>
            <div class="flex justify-between text-base">
                <span class="text-gray-500">Deposit</span>
                <span class="font-bold text-gray-800">₱{{ number_format($deposit, 2) }}</span>
            </div>
            @if($discount_available)
            <div class="flex justify-between items-center text-base pt-3 border-t border-blue-100">
                <div class="flex items-center gap-3">
                    <span class="text-gray-500">Discount (PWD & Senior Citizen)</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="discountEnabled" wire:change="applyDiscount" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-[#009EF5]/20 rounded-full peer peer-checked:bg-[#009EF5] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                    </label>
                </div>
                <span class="font-bold {{ $discountEnabled ? 'text-red-500' : 'text-gray-400' }} text-base">
                    {{ $discountEnabled ? '-₱' . number_format($discount_amount, 2) : '—' }}
                </span>
            </div>
            @endif
            <div class="flex justify-between items-center pt-4 border-t border-blue-200">
                <span class="text-lg font-bold text-gray-800">Total</span>
                <span class="text-3xl font-bold text-[#009EF5]">₱{{ number_format($this->total, 2) }}</span>
            </div>
        </div>

        <button wire:click="confirmCheckIn"
            class="mt-5 w-full rounded-2xl bg-[#009EF5] px-8 py-5 text-lg font-bold text-white shadow-lg shadow-[#009EF5]/25 hover:bg-[#0080cc] active:scale-[0.98] transition-all duration-150">
            CONFIRM CHECK-IN
        </button>
    </div>
    @endif

    @endif
</div>
