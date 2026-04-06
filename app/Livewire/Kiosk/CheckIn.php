<?php

namespace App\Livewire\Kiosk;

use Livewire\Component;
use App\Models\Type;
use App\Models\Room;
use App\Models\Rate;
use App\Models\Floor;
use App\Models\Guest;
use App\Models\StayingHour;
use App\Models\CheckinDetail;
use App\Models\TemporaryCheckInKiosk;
use App\Models\TemporaryReserved;
use App\Models\DiscountConfiguration;
use WireUi\Traits\WireUiActions;
use Illuminate\Support\Facades\DB;

class CheckIn extends Component
{
    use WireUiActions;

    // Guest info
    public $name, $contact;
    public $discountEnabled = false;
    public $discount_available = false;
    public $discount_amount = 0;

    // Selections
    public $type_id;
    public $room_id;
    public $rate_id;
    public $longstay;
    public $floor_id;

    // Data
    public $types = [];
    public $rates = [];
    public $floors = [];

    // Display
    public $room_number, $room_type, $room_floor, $room_pay;
    public $generatedQrCode;
    public bool $showQr = false;

    public function mount()
    {
        $this->types = Type::where('branch_id', auth()->user()->branch_id)->get();
        $this->floors = Floor::where('branch_id', auth()->user()->branch_id)->orderBy('number')->get();
    }

    public function selectType($typeId)
    {
        $this->type_id = $typeId;
        $this->room_id = null;
        $this->rate_id = null;
        $this->rates = [];
        $this->longstay = null;
        $this->floor_id = null;
    }

    public function selectRoom($roomId)
    {
        $this->room_id = $roomId;
        $this->rate_id = null;
        $this->longstay = null;
        $this->rates = Rate::where('branch_id', auth()->user()->branch_id)
            ->where('room_id', $roomId)
            ->with('stayingHour')
            ->get();
    }

    public function selectRate($rateId)
    {
        $this->rate_id = $rateId;
        $this->longstay = null;

        $rate = Rate::with('stayingHour')->find($rateId);
        $room = Room::find($this->room_id);
        $branch = auth()->user()->branch;

        if ($rate && $room && $branch->discount_enabled) {
            $discountEnabled = DiscountConfiguration::where('branch_id', $branch->id)
                ->where('type_id', $room->type_id)
                ->where('staying_hour_id', $rate->staying_hour_id)
                ->where('is_enabled', true)
                ->exists();
            $this->discount_available = $discountEnabled;
        } else {
            $this->discount_available = false;
        }

        if (!$this->discount_available) {
            $this->discountEnabled = false;
            $this->discount_amount = 0;
        }
    }

    public function updatedLongstay()
    {
        $this->rate_id = null;
        if ($this->longstay) {
            $this->discount_available = false;
            $this->discountEnabled = false;
            $this->discount_amount = 0;
        }
    }

    public function applyDiscount()
    {
        if ($this->discountEnabled) {
            $this->discount_amount = auth()->user()->branch->discount_amount;
        } else {
            $this->discount_amount = 0;
        }
    }

    // Computed: total payable
    public function getTotalProperty(): float
    {
        $pay = $this->getRoomPay();
        $deposit = auth()->user()->branch->initial_deposit ?? 200;
        $discount = $this->discountEnabled ? ($this->discount_amount ?? 0) : 0;
        return max(0, $pay + $deposit - $discount);
    }

    public function getRoomPay(): float
    {
        if ($this->longstay && $this->room_id) {
            $long = StayingHour::where('branch_id', auth()->user()->branch_id)->where('number', 24)->first();
            if ($long) {
                $rate = Rate::where('branch_id', auth()->user()->branch_id)
                    ->where('room_id', $this->room_id)
                    ->where('staying_hour_id', $long->id)
                    ->first();
                return $rate ? $rate->amount * $this->longstay : 0;
            }
            return 0;
        }

        if ($this->rate_id) {
            return Rate::find($this->rate_id)?->amount ?? 0;
        }

        return 0;
    }

    public function getStayingHoursProperty(): ?int
    {
        if ($this->rate_id) {
            return Rate::with('stayingHour')->find($this->rate_id)?->stayingHour?->number;
        }
        return null;
    }

    public function canConfirm(): bool
    {
        return $this->type_id && $this->room_id && ($this->rate_id || $this->longstay);
    }

    public function confirmCheckIn()
    {
        // Validate name
        $rules = ['name' => 'required|min:3'];
        if ($this->contact) {
            $rules['contact'] = 'required|min:9|max:9';
        }
        if ($this->longstay) {
            $rules['longstay'] = 'required|integer|min:1|max:31';
        }
        $this->validate($rules);

        // Resolve rate_id for long stay
        if ($this->longstay && !$this->rate_id) {
            $long = StayingHour::where('branch_id', auth()->user()->branch_id)->where('number', 24)->first();
            if ($long) {
                $rate = Rate::where('branch_id', auth()->user()->branch_id)
                    ->where('room_id', $this->room_id)
                    ->where('staying_hour_id', $long->id)
                    ->first();
                if ($rate) {
                    $this->rate_id = $rate->id;
                } else {
                    $this->dialog()->error('Sorry', 'Long stay rate is not set. Please contact the administrator.');
                    return;
                }
            }
        }

        $this->room_pay = $this->getRoomPay();

        $this->dialog()->confirm([
            'title' => 'Confirm Check-In',
            'description' => 'Are you sure you want to check in?',
            'icon' => 'question',
            'accept' => [
                'label' => 'Yes, Check In',
                'method' => 'processCheckIn',
            ],
            'reject' => [
                'label' => 'Cancel',
            ],
        ]);
    }

    public function processCheckIn()
    {
        DB::beginTransaction();

        try {
            // Verify room is still available
            $occupied = Room::where('branch_id', auth()->user()->branch_id)
                ->where('id', $this->room_id)
                ->where('status', 'Occupied')
                ->lockForUpdate()
                ->exists();

            if ($occupied) {
                DB::rollBack();
                $this->dialog()->error('Sorry', 'Room is already occupied. Please select another room.');
                $this->room_id = null;
                return;
            }

            $takenByKiosk = TemporaryCheckInKiosk::where('branch_id', auth()->user()->branch_id)
                ->where('room_id', $this->room_id)->lockForUpdate()->exists();
            $takenByReserved = TemporaryReserved::where('branch_id', auth()->user()->branch_id)
                ->where('room_id', $this->room_id)->lockForUpdate()->exists();

            if ($takenByKiosk || $takenByReserved) {
                DB::rollBack();
                $this->dialog()->error('Sorry', 'Room is already reserved. Please select another room.');
                $this->room_id = null;
                return;
            }

            // Generate QR code
            $count = Guest::whereYear('created_at', now()->year)->lockForUpdate()->count() + 1;
            $count = $count % 10000;
            $this->generatedQrCode = auth()->user()->branch_id . now()->format('y') . str_pad($count, 4, '0', STR_PAD_LEFT);

            // Create guest
            $guest = Guest::create([
                'branch_id' => auth()->user()->branch_id,
                'name' => $this->name,
                'contact' => $this->contact ? '09' . $this->contact : 'N/A',
                'qr_code' => $this->generatedQrCode,
                'room_id' => $this->room_id,
                'rate_id' => $this->rate_id,
                'type_id' => $this->type_id,
                'static_amount' => $this->room_pay,
                'is_long_stay' => $this->longstay ? true : false,
                'number_of_days' => $this->longstay ?? 0,
                'has_discount' => $this->discountEnabled,
                'discount_amount' => $this->discountEnabled ? $this->discount_amount : 0,
            ]);

            TemporaryCheckInKiosk::create([
                'guest_id' => $guest->id,
                'room_id' => $this->room_id,
                'branch_id' => auth()->user()->branch_id,
                'terminated_at' => now()->addMinutes(20),
            ]);

            DB::commit();
            $this->showQr = true;

            event(new \App\Events\CheckInEvent(auth()->user()->branch_id));

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function redirectToHome()
    {
        return redirect()->route('kiosk.house-rules');
    }

    public function render()
    {
        $branchId = auth()->user()->branch_id;

        $excludedRoomIds = TemporaryCheckInKiosk::where('branch_id', $branchId)->pluck('room_id')
            ->merge(TemporaryReserved::where('branch_id', $branchId)->pluck('room_id'))
            ->merge(
                CheckinDetail::where('is_check_out', false)
                    ->whereHas('room', fn ($q) => $q->where('branch_id', $branchId))
                    ->pluck('room_id')
            )
            ->toArray();

        $rooms = collect();
        if ($this->type_id) {
            $rooms = Room::where('branch_id', $branchId)
                ->where('type_id', $this->type_id)
                ->whereIn('status', ['Available', 'Cleaned'])
                ->whereNotIn('id', $excludedRoomIds)
                ->where('is_priority', true)
                ->when($this->floor_id, fn ($q) => $q->where('floor_id', $this->floor_id))
                ->with(['type', 'floor'])
                ->orderBy('number')
                ->get();
        }

        return view('livewire.kiosk.check-in', [
            'rooms' => $rooms,
            'deposit' => auth()->user()->branch->initial_deposit ?? 200,
        ]);
    }
}
