<?php

namespace App\Livewire\Frontdesk\Monitoring;

use App\Models\ActivityLog;
use App\Models\CashOnDrawer;
use App\Models\CheckinDetail;
use App\Models\Floor;
use App\Models\Guest;
use App\Models\Rate;
use App\Models\Room;
use App\Models\TemporaryCheckInKiosk;
use App\Models\Transaction;
use App\Models\TransferedGuestReport;
use App\Models\TransferReason;
use App\Models\Type;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class TransferRoom extends Component
{
    use WireUiActions;
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
    public $authorization_modal = false;
    public $test_modal = false;
    public $code;
    public $current_room_rate;
    public $new_room_rate;
    public $save_type = 'save';
    public $available_deposit = 0;
    public $amount_paid;
    public $payment_excess = 0;
    public $change_modal = false;
    public $deposit_pay_modal = false;

    public function mount($record)
    {
        $this->assigned_frontdesk = auth()->user()->assigned_frontdesks;
        $this->guest =  $this->guest = Guest::where('branch_id', auth()->user()->branch_id)
                ->where('id', $record)
                ->first();

        $is_long_stay = $this->guest->is_long_stay;
        $days_stayed = $this->guest->number_of_days;
        $amount = $this->guest->checkInDetail->rate->amount;
        $this->current_room_rate = $is_long_stay ? $amount * $days_stayed : $amount;


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
        $this->available_deposit = $this->guest->checkInDetail->deposit_balance ?? 0;
    }

     public function updatedSelectedTypeId()
    {
        $this->selected_floor_id = null;
        $this->selected_room_id = null;
        $this->enabled = false;
        $this->selected_status = null;
        $this->selected_reason = null;

        $kiosk_reservation = TemporaryCheckInKiosk::where('branch_id', auth()->user()->branch_id)
            ->pluck('room_id')->toArray();

        $reserved = \App\Models\TemporaryReserved::where('branch_id', auth()->user()->branch_id)->pluck('room_id')->toArray();

        $occupied = CheckinDetail::where('is_check_out', false)
            ->whereHas('room', fn($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->pluck('room_id')->toArray();

        $excludedRoomIds = array_merge($kiosk_reservation, $reserved, $occupied);

        $this->rooms = Room::where('branch_id', auth()->user()->branch_id)
            ->where('type_id', $this->selected_type_id)
            ->where('floor_id', $this->selected_floor_id)
            ->whereNotIn('id', $excludedRoomIds)
            ->where('status', 'Available')
            ->get();
        $this->room_count = Room::where('branch_id', auth()->user()->branch_id)
                  ->where('status', 'Available')
                  ->where('type_id', $this->selected_type_id)
                  ->count();
        $this->new_room = null;
        $this->new_room_rate = 0;
        $this->excess_amount = 0;
        $this->payable_amount = 0;
    }

    public function updatedSelectedRoomId()
    {
        if (!$this->selected_room_id) {
            $this->new_room = null;
            $this->new_room_rate = 0;
            $this->excess_amount = 0;
            $this->payable_amount = 0;
            return;
        }

        $hours = $this->guest->checkInDetail->hours_stayed;
        $this->new_room = Rate::where('branch_id', auth()->user()->branch_id)
                        ->where('room_id', $this->selected_room_id)
                        ->where('is_available', true)
                        ->whereHas('stayingHour', function ($query) use ($hours) {
                            $query->where('branch_id', auth()->user()->branch_id)->where('number', '=', $hours);
                        })
                        ->first();
        $this->new_room_rate = $this->guest->is_long_stay ? ($this->new_room ? $this->new_room->amount * $this->guest->number_of_days : 0) : ($this->new_room ? $this->new_room->amount : 0);

        $this->excess_amount =  ($this->new_room && isset($this->new_room_rate)) ? max(0, $this->current_room_rate - $this->new_room_rate) : 0;
        $this->payable_amount = ($this->new_room && isset($this->new_room_rate)) ? max(0, $this->new_room_rate - $this->current_room_rate) : 0;
    }

    public function updatedSelectedFloorId()
    {
         $kiosk_reservation = TemporaryCheckInKiosk::where('branch_id', auth()->user()->branch_id)
            ->pluck('room_id')->toArray();

        $reserved = \App\Models\TemporaryReserved::where('branch_id', auth()->user()->branch_id)->pluck('room_id')->toArray();

        $occupied = CheckinDetail::where('is_check_out', false)
            ->whereHas('room', fn($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->pluck('room_id')->toArray();

        $excludedRoomIds = array_merge($kiosk_reservation, $reserved, $occupied);

        $this->rooms = Room::where('branch_id', auth()->user()->branch_id)
            ->where('type_id', $this->selected_type_id)
            ->where('floor_id', $this->selected_floor_id)
            ->whereNotIn('id', $excludedRoomIds)
            ->where('status', 'Available')
            ->get();

        if(!$this->rooms->isEmpty()) {
            $this->enabled = true;
        } else {
            $this->enabled = false;
        }
    }

    /**
     * Validate all form fields.
     */
    private function validateFields()
    {
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
    }

    /**
     * Called by Override button. Sets override flag then follows same flow.
     */
    public function overrideTransfer()
    {
        $this->validateFields();
        $this->is_override = true;

        // Follow same flow: handle excess/payment first, then auth last
        $this->proceedToExcessOrAuth();
    }

    /**
     * Called after normal buttons or after payment validation in confirmSave.
     * Handles: rate excess modal → then auth (if override) or confirm dialog.
     */
    public function proceedToExcessOrAuth()
    {
        // If downgrade has rate excess → show save_pay_modal first
        if ($this->excess_amount > 0) {
            $this->save_pay_modal = true;
            return;
        }

        // No rate excess → go to auth or confirm
        $this->proceedToAuthOrSave();
    }

    /**
     * Called after rate excess decision (from save_pay_modal confirm button)
     * or directly if no rate excess.
     */
    public function proceedToAuthOrSave()
    {
        $this->save_pay_modal = false;

        if ($this->is_override) {
            $this->code = null;
            $this->authorization_modal = true;
        } else {
            $this->dialog()->confirm([
                'title' => 'Are you Sure?',
                'description' => 'Transfer guest to new room?',
                'icon' => 'question',
                'accept' => [
                    'label' => 'Confirm Transfer',
                    'method' => 'saveTransfer',
                ],
                'reject' => [
                    'label' => 'Cancel',
                ],
            ]);
        }
    }

    /**
     * Called from authorization modal confirm button.
     */
    public function validateCodeAndSave()
    {
        if (auth()->user()->branch->autorization_code != $this->code) {
            $this->code = null;
            $this->dialog()->error('Oops', 'Wrong authorization code.');
            return;
        }

        $this->authorization_modal = false;
        $this->saveTransfer();
    }

    /**
     * Called by Save, Save & Pay, Pay with Deposit buttons (normal flow, not override).
     */
    public function confirmSave($type)
    {
        $this->validateFields();
        $this->save_type = $type;
        $this->change_modal = false;
        $this->payment_excess = 0;

        if ($type === 'pay_deposit') {
            if ($this->available_deposit < $this->payable_amount) {
                $this->dialog()->error('Insufficient Deposit', 'Guest deposit balance is not enough.');
                return;
            }
            $this->deposit_pay_modal = true;
            return;
        }

        if ($type === 'save_pay' && $this->payable_amount > 0) {
            if (!$this->amount_paid || !is_numeric($this->amount_paid) || $this->amount_paid <= 0) {
                $this->dialog()->error('Oops!', 'Please enter the amount paid.');
                return;
            }

            $this->amount_paid = (float) $this->amount_paid;

            if ($this->amount_paid > $this->payable_amount) {
                $this->payment_excess = $this->amount_paid - $this->payable_amount;
                $this->change_modal = true;
                return;
            } elseif ($this->amount_paid < $this->payable_amount) {
                $this->dialog()->error('Oops!', 'Amount paid is less than the total payable amount.');
                return;
            }
        }

        $this->proceedToExcessOrAuth();
    }

    /**
     * Called from change_modal (payment excess) confirm button.
     * Payment excess already decided, now proceed to rate excess or auth.
     */
    public function confirmPaymentExcess()
    {
        $this->change_modal = false;
        $this->proceedToExcessOrAuth();
    }

    public function confirmDepositPay()
    {
        $this->deposit_pay_modal = false;
        $this->proceedToExcessOrAuth();
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
            'checkin_detail_id' => $check_in_detail->id,
            'cash_drawer_id' => auth()->user()->cash_drawer_id,
            'room_id' => $this->selected_room_id,
            'guest_id' => $this->guest->id,
            'floor_id' => $this->selected_floor_id,
            'transaction_type_id' => 7,
            'assigned_frontdesk_id' => json_encode([auth()->id(), auth()->user()->name]),
            'description' => 'Room Transfer',
            'payable_amount' => $this->payable_amount,
            'paid_amount' => 0,
            'change_amount' => 0,
            'deposit_amount' => 0,
            'paid_at' => now()->toDateString(),
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
                'checkin_detail_id' => $check_in_detail->id,
                'cash_drawer_id' => auth()->user()->cash_drawer_id,
                'room_id' => $this->guest->room_id,
                'guest_id' => $this->guest->id,
                'floor_id' => Room::where('id', $this->guest->room_id)->first()->floor->id,
                'transaction_type_id' => 2,
                'deposit_type' => 'guest',
                'assigned_frontdesk_id' => json_encode([auth()->id(), auth()->user()->name]),
                'description' => 'Deposit',
                'payable_amount' => $this->excess_amount,
                'paid_amount' => $this->excess_amount,
                'change_amount' => 0,
                'deposit_amount' => $this->excess_amount,
                'paid_at' => Carbon::now()->toDateTimeString(),
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

        $initial_deposit = auth()->user()->branch->initial_deposit;

         Guest::where('id', $this->guest->id)->update([
            'previous_room_id' => $check_in_detail->room_id,
            'room_id' => $this->selected_room_id,
            'type_id' => $this->selected_type_id,
            'rate_id' => $this->new_room ? $this->new_room->id : $this->guest->rate_id,
            'static_amount' => ($this->new_room_rate + $initial_deposit),
        ]);


        $new_room = Room::where('id',  $this->selected_room_id)->first();

        TransferedGuestReport::create([
            'checkin_detail_id' => $check_in_detail->id,
            'previous_room_id' => $check_in_detail->room_id,
            'new_room_id' => $new_room->id,
            'rate_id' => $this->guest->rate_id,
            'previous_amount' => $check_in_detail->static_room_amount,
            'new_amount' => $this->new_room_rate,
            'original_check_in_time' => $transaction->created_at,
        ]);

         CheckinDetail::where('guest_id', $this->guest->id)->update([
            'type_id' => $this->selected_type_id,
            'room_id' => $this->selected_room_id,
            'rate_id' => $this->new_room ? $this->new_room->id : $this->guest->rate_id,
            'static_room_amount' => $this->new_room_rate,
            'static_amount' => ($this->new_room_rate + $initial_deposit),
        ]);




        ActivityLog::create([
            'branch_id' => auth()->user()->branch_id,
            'user_id' => auth()->user()->id,
            'activity' => 'Room Transfer',
            'description' => 'Guest ' . $check_in_detail->guest->name . ' transferred from Room #' . Room::where('id', $check_in_detail->room_id)->first()->number . ' to Room #' . $new_room->number,
        ]);
        DB::commit();

        // Handle payment based on save_type
        if ($this->save_type === 'save_pay') {
            $transaction->update([
                'paid_at' => now(),
                'paid_amount' => $this->amount_paid ?? $transaction->payable_amount,
                'change_amount' => $this->payment_excess,
            ]);

            // If payment excess and save_excess checked, create guest deposit
            if ($this->payment_excess > 0 && $this->save_excess) {
                Transaction::create([
                    'branch_id' => auth()->user()->branch_id,
                    'checkin_detail_id' => $check_in_detail->id,
                    'cash_drawer_id' => auth()->user()->cash_drawer_id,
                    'room_id' => $check_in_detail->room_id,
                    'guest_id' => $check_in_detail->guest_id,
                    'floor_id' => $check_in_detail->room->floor_id,
                    'transaction_type_id' => 2,
                    'deposit_type' => 'guest',
                    'assigned_frontdesk_id' => json_encode([auth()->id(), auth()->user()->name]),
                    'description' => 'Deposit',
                    'payable_amount' => $this->payment_excess,
                    'paid_amount' => $this->payment_excess,
                    'deposit_amount' => $this->payment_excess,
                    'paid_at' => now(),
                    'remarks' => 'Deposit From Excess Payment (Transfer)',
                    'shift' => (now()->hour >= 8 && now()->hour < 20) ? 'AM' : 'PM',
                ]);
            }
        } elseif ($this->save_type === 'pay_deposit') {
            $payable = $transaction->payable_amount;
            $transaction->update([
                'paid_at' => now(),
                'paid_amount' => $payable,
            ]);

            Transaction::create([
                'branch_id' => auth()->user()->branch_id,
                'checkin_detail_id' => $check_in_detail->id,
                'cash_drawer_id' => auth()->user()->cash_drawer_id,
                'room_id' => $check_in_detail->room_id,
                'guest_id' => $check_in_detail->guest_id,
                'floor_id' => $check_in_detail->room->floor_id,
                'transaction_type_id' => 5,
                'assigned_frontdesk_id' => json_encode([auth()->id(), auth()->user()->name]),
                'description' => 'Cashout',
                'payable_amount' => $payable,
                'paid_amount' => $payable,
                'change_amount' => 0,
                'deposit_amount' => 0,
                'paid_at' => now(),
                'remarks' => 'Deposit used to pay ' . $transaction->description,
                'shift' => (now()->hour >= 8 && now()->hour < 20) ? 'AM' : 'PM',
            ]);
        }

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
