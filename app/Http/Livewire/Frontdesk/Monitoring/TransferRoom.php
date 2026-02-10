<?php

namespace App\Http\Livewire\Frontdesk\Monitoring;

use App\Models\Rate;
use App\Models\Room;
use App\Models\Type;
use App\Models\Floor;
use App\Models\Guest;
use Livewire\Component;
use WireUi\Traits\Actions;
use App\Models\ActivityLog;
use App\Models\Transaction;
use App\Models\CashOnDrawer;
use App\Models\CheckinDetail;
use App\Models\TransferReason;
use Illuminate\Support\Facades\DB;

class TransferRoom extends Component
{
    use Actions;
    public $guest;
    public $room;
    public $new_room;
    public $excess_amount;
    public $payable_amount;

    public $room_count;
    public $has_rate = false;
    public $assigned_frontdesk = [];

    //selections
    public $types;
    public $selected_type_id;
    public $floors;
    public $selected_floor_id;
    public $rooms;
    public $selected_room_id;
    public $selected_status;
    public $reasons;
    public $selected_reason;
    public $enabled = false;
    public $save_pay_modal = false;
    public $save_excess = false;
    public $is_override = false;

    public function mount($record)
    {
        $this->assigned_frontdesk = auth()->user()->assigned_frontdesks;
        $this->guest =  $this->guest = Guest::where('branch_id', auth()->user()->branch_id)
                ->where('id', $record)
                ->first();
        $this->room = Room::where('branch_id', auth()->user()->branch_id)
                ->where('id', $this->guest->checkInDetail->room_id)
                ->first();
        $this->types = Type::where(
            'branch_id',
            auth()->user()->branch_id
        )->get();
        $this->floors = Floor::where(
            'branch_id',
            auth()->user()->branch_id
        )->get();

        $this->reasons = TransferReason::where('branch_id', auth()->user()->branch_id)
            ->get();
    }

     public function updatedSelectedTypeId()
    {
        $this->selected_floor_id = null;
        $this->selected_room_id = null;
        $this->enabled = false;
        $this->selected_status = null;
        $this->selected_reason = null;

        $this->rooms = Room::where('branch_id', auth()->user()->branch_id)
            ->where('type_id', $this->selected_type_id)
            ->where('floor_id', $this->selected_floor_id)
            ->where('status', 'Available')
            ->get();
        $this->room_count = Room::where('branch_id', auth()->user()->branch_id)
                  ->where('status', 'Available')
                  ->where('type_id', $this->selected_type_id)
                  ->count();
        $hours = $this->guest->checkInDetail->hours_stayed;
        $this->new_room  = Rate::where('branch_id', auth()->user()->branch_id)
                        ->where('type_id', $this->selected_type_id)
                        ->where('is_available', true)
                        ->whereHas('stayingHour', function ($query) use ($hours) {
                            $query->where('branch_id', auth()->user()->branch_id)->where('number', '=', $hours);
                        })
                        ->first();
        $this->excess_amount =  ($this->new_room && isset($this->new_room->amount)) ? max(0, $this->guest->checkInDetail->rate->amount - $this->new_room->amount) : 0;
        $this->payable_amount = ($this->new_room && isset($this->new_room->amount)) ? max(0, $this->new_room->amount - $this->guest->checkInDetail->rate->amount) : 0;;
    }

    public function updatedSelectedFloorId()
    {
        $this->rooms = Room::where('branch_id', auth()->user()->branch_id)
            ->where('type_id', $this->selected_type_id)
            ->where('floor_id', $this->selected_floor_id)
            ->where('status', 'Available')
            ->get();

        if(!$this->rooms->isEmpty()) {
            $this->enabled = true;
        } else {
            $this->enabled = false;
        }
    }

    public function confirmTransfer($is_override)
    {         if($is_override)
        {
            $this->is_override = true;
        }else{
            $this->is_override = false;
        }
        $this->validate([
            'selected_type_id' => 'required',
            'selected_floor_id' => 'required',
            'selected_room_id' => 'required',
            'selected_status' => 'required',
            'selected_reason' => 'required',
        ], [
            'selected_type_id.required' => 'Please select a room type.',
            'selected_floor_id.required' => 'Please select a floor.',
            'selected_room_id.required' => 'Please select a room.',
            'selected_status.required' => 'Please select a status.',
            'selected_reason.required' => 'Please select a reason for transfer.',
        ]);

        if($this->excess_amount > 0) {
             $this->save_pay_modal = true;
        }else{
            $this->dialog()->confirm([
            'title' => 'Are you Sure?',
            'description' => 'transfer guest to new room?',
            'icon' => 'question',
            'accept' => [
                'label' => 'Confirm Transfer',
                'method' => 'saveTransfer',
                'params' => $this->is_override,
            ],
            'reject' => [
                'label' => 'Cancel',
            ],
        ]);
        }
    }

    public function saveTransfer()
    {

        $check_in_detail = CheckinDetail::where(
            'guest_id',
            $this->guest->id
        )->first();
        $reason = TransferReason::find($this->selected_reason);
        DB::beginTransaction();
        $transaction = Transaction::create([
            'branch_id' => auth()->user()->branch_id,
            'cash_drawer_id' => auth()->user()->cash_drawer_id,
            'room_id' => $this->selected_room_id,
            'guest_id' => $this->guest->id,
            'floor_id' => $this->selected_floor_id,
            'transaction_type_id' => 7,
            'assigned_frontdesk_id' => json_encode($this->assigned_frontdesk),
            'description' => 'Room Transfer',
            'payable_amount' => $this->payable_amount,
            'paid_amount' => 0,
            'change_amount' => 0,
            'deposit_amount' => 0,
            'paid_at' => null,
            'override_at' => null,
            'remarks' =>
                'Guest Transfered from Room #' .
                Room::where('id', $check_in_detail->room_id)->first()->number .
                ' (' .
                Type::where('id', $check_in_detail->type_id)->first()->name .
                ') to Room #' .
                Room::where('id', $this->selected_room_id)->first()->number .
                ' (' .
                Type::where('id', $this->selected_type_id)->first()->name .
                ') - Reason: ' .
                $reason->reason,
            'transfer_reason_id' => $this->selected_reason,
            'shift' => (now()->hour >= 8 && now()->hour < 20) ? 'AM' : 'PM',
            'is_override' => $this->is_override,
        ]);

        if($this->save_excess)
        {
             Transaction::create([
                'branch_id' => auth()->user()->branch_id,
                'cash_drawer_id' => auth()->user()->cash_drawer_id,
                'room_id' => $this->guest->room_id,
                'guest_id' => $this->guest->id,
                'floor_id' => Room::where('id', $this->guest->room_id)->first()->floor->id,
                'transaction_type_id' => 2,
                'assigned_frontdesk_id' => json_encode($this->assigned_frontdesk),
                'description' => 'Deposit',
                'payable_amount' => $this->excess_amount,
                'paid_amount' => $this->excess_amount,
                'change_amount' => 0,
                'deposit_amount' => $this->excess_amount,
                'paid_at' => now(),
                'override_at' => null,
                'remarks' => 'Deposit From Transfer Room (Excess Amount)',
                'shift' => (now()->hour >= 8 && now()->hour < 20) ? 'AM' : 'PM',
                'is_override' => false,
            ]);

             //save cash on drawer
            CashOnDrawer::create([
                'branch_id' => auth()->user()->branch_id,
                'frontdesk_id' => auth()->user()->frontdesk->id,
                'cash_drawer_id' => auth()->user()->cash_drawer_id,
                'amount' => $this->excess_amount,
                'transaction_date' => now()->toDateString(),
                'transaction_type' => 'deposit',
                'shift' => (now()->hour >= 8 && now()->hour < 20) ? 'AM' : 'PM',
            ]);
        }

        if($this->selected_status === "Uncleaned")
        {
            Room::where('id', $check_in_detail->room_id)->update([
                'time_to_clean' => now()->addHours(3),
            ]);
        }

        Room::where('id', $check_in_detail->room_id)->update([
            'status' => $this->selected_status,
        ]);

        Room::where('id',  $this->selected_room_id)->update([
            'status' => 'Occupied',
        ]);

         Guest::where('id', $this->guest->id)->update([
            'previous_room_id' => $check_in_detail->room_id,
            'room_id' => $this->selected_room_id,
        ]);

        $new_room = Room::where('id',  $this->selected_room_id)->first();
         CheckinDetail::where('guest_id', $this->guest->id)->update([
            'type_id' => $this->selected_type_id,
            'room_id' => $this->selected_room_id,
        ]);

        ActivityLog::create([
            'branch_id' => auth()->user()->branch_id,
            'user_id' => auth()->user()->id,
            'activity' => 'Room Transfer',
            'description' => 'Guest ' . $check_in_detail->guest->name . ' transferred from Room #' . Room::where('id', $check_in_detail->room_id)->first()->number . ' to Room #' . $new_room->number,
        ]);
        DB::commit();

        $this->dialog()->success(
            $title = 'Success',
            $description = 'Guest has been transferred successfully.'
        );

         return redirect()->route('frontdesk.guest-transaction', [
                'id' => $this->guest->id,
        ]);

    }

    public function cancelTransfer()
    {
        return redirect()->route('frontdesk.guest-transaction', ['id' => $this->guest->id]);
    }
    public function render()
    {
        return view('livewire.frontdesk.monitoring.transfer-room');
    }
}
