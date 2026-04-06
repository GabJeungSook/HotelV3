<?php

namespace App\Livewire\Frontdesk\Monitoring;

use App\Models\Guest;
use App\Models\Room;
use Livewire\Component;
use App\Models\ActivityLog;
use App\Models\Transaction;
use App\Models\CheckinDetail;
use App\Models\HotelItems;
use Illuminate\Support\Facades\DB;
use WireUi\Traits\WireUiActions;

class DamageCharges extends Component
{
    use WireUiActions;

    public $guest_id;
    public $guest;
    public $room;
    public $check_in_detail;
    public $items;
    public $item_id;
    public $item_price;
    public $additional_amount = 0;
    public $total_amount = 0;
    public $save_type = 'save';
    public $available_deposit = 0;
    public $amount_paid;
    public $excess_amount = 0;
    public $save_excess = false;
    public $change_modal = false;
    public $deposit_pay_modal = false;

    public function mount($id)
    {
        $this->guest_id = $id;

        $this->guest = Guest::where('branch_id', auth()->user()->branch_id)
            ->where('id', $id)
            ->first();

        $this->check_in_detail = CheckinDetail::where('guest_id', $this->guest->id)
            ->where('is_check_out', false)
            ->first();

        $this->room = Room::where('branch_id', auth()->user()->branch_id)
            ->where('id', $this->check_in_detail->room_id)
            ->first();

        $this->items = HotelItems::where(
            'branch_id',
            auth()->user()->branch_id
        )->get();

        $this->available_deposit = $this->check_in_detail->deposit_balance;
    }

    public function updatedItemId()
    {
        $item = HotelItems::where('id', $this->item_id)->first();
        $this->item_price = $item->price;

        $this->total_amount = $this->item_price + $this->additional_amount;
    }

    public function updatedAdditionalAmount()
    {
        if ($this->additional_amount == null || $this->additional_amount == 0) {
            $this->additional_amount = 0;
        }

        $this->total_amount = $this->item_price + $this->additional_amount;
    }

    public function confirmSave($type = 'save')
    {
        $this->save_type = $type;
        $this->change_modal = false;
        $this->excess_amount = 0;
        $this->save_excess = false;

        if ($type === 'pay_deposit') {
            if ($this->available_deposit < $this->total_amount) {
                $this->dialog()->error('Insufficient Deposit', 'Guest deposit balance is not enough.');
                return;
            }
            $this->deposit_pay_modal = true;
            return;
        }

        if ($type === 'save_pay') {
            if (!$this->amount_paid || !is_numeric($this->amount_paid) || $this->amount_paid <= 0) {
                $this->dialog()->error('Oops!', 'Please enter the amount paid.');
                return;
            }
            $this->amount_paid = (float) $this->amount_paid;

            if ($this->amount_paid > $this->total_amount) {
                $this->excess_amount = $this->amount_paid - $this->total_amount;
                $this->change_modal = true;
            } elseif ($this->amount_paid < $this->total_amount) {
                $this->dialog()->error('Oops!', 'Amount paid is less than the total payable amount.');
            } else {
                // Exact amount - save directly
                $this->saveTransaction();
            }
            return;
        }

        // For 'save' type, just save directly
        $this->dialog()->confirm([
            'title' => 'Confirm',
            'description' => 'Save as unpaid bill?',
            'method' => 'saveTransaction',
        ]);
    }

    public function confirmDepositPay()
    {
        $this->deposit_pay_modal = false;
        $this->saveTransaction();
    }

    public function saveTransaction()
    {
        $this->validate(
            [
                'item_id' => 'required',
            ],
            [
                'item_id.required' => 'This field is required',
            ]
        );

        DB::beginTransaction();

        $check_in_detail = CheckinDetail::where('guest_id', $this->guest_id)
            ->where('is_check_out', false)
            ->first();

        $damage_charges = HotelItems::where('branch_id', auth()->user()->branch_id)
            ->where('id', $this->item_id)
            ->first();

        $transaction = Transaction::create([
            'branch_id' => $check_in_detail->guest->branch_id,
            'checkin_detail_id' => $check_in_detail->id,
            'cash_drawer_id' => auth()->user()->cash_drawer_id,
            'room_id' => $check_in_detail->room_id,
            'guest_id' => $check_in_detail->guest_id,
            'floor_id' => $check_in_detail->room->floor_id,
            'transaction_type_id' => 4,
            'assigned_frontdesk_id' => json_encode([auth()->id(), auth()->user()->name]),
            'description' => 'Damage Charges',
            'payable_amount' => $this->total_amount,
            'paid_amount' => 0,
            'change_amount' => 0,
            'deposit_amount' => 0,
            'paid_at' => null,
            'override_at' => null,
            'remarks' => 'Guest Charged for Damage: (1) ' . $damage_charges->name,
            'shift' => (now()->hour >= 8 && now()->hour < 20) ? 'AM' : 'PM',
        ]);

        // Handle payment based on save_type
        if ($this->save_type === 'save_pay') {
            $transaction->update([
                'paid_at' => now(),
                'paid_amount' => $this->amount_paid ?? $transaction->payable_amount,
                'change_amount' => $this->excess_amount,
            ]);

            if ($this->excess_amount > 0 && $this->save_excess) {
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
                    'payable_amount' => $this->excess_amount,
                    'paid_amount' => $this->excess_amount,
                    'deposit_amount' => $this->excess_amount,
                    'paid_at' => now(),
                    'remarks' => 'Deposit From Excess Payment',
                    'shift' => (now()->hour >= 8 && now()->hour < 20) ? 'AM' : 'PM',
                ]);
            }
        } elseif ($this->save_type === 'pay_deposit') {
            $transaction->update([
                'paid_at' => now(),
                'paid_amount' => $transaction->payable_amount,
            ]);
            // Create cashout transaction to deduct from deposit
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
                'payable_amount' => $this->total_amount,
                'paid_amount' => $this->total_amount,
                'change_amount' => 0,
                'deposit_amount' => 0,
                'paid_at' => now(),
                'remarks' => 'Deposit used to pay ' . $transaction->description,
                'shift' => (now()->hour >= 8 && now()->hour < 20) ? 'AM' : 'PM',
            ]);
        }

        ActivityLog::create([
            'branch_id' => auth()->user()->branch_id,
            'user_id' => auth()->user()->id,
            'activity' => 'Add Damage Charges',
            'description' => 'Added new damage charges of ₱' . $this->total_amount . ' for guest ' . $check_in_detail->guest->name,
        ]);

        DB::commit();

        $this->dialog()->success(
            $title = 'Success',
            $description = 'Damage charge successfully saved'
        );

        return redirect()->route('frontdesk.guest-transaction', [
            'id' => $this->guest_id,
        ]);
    }

    public function cancel()
    {
        return redirect()->route('frontdesk.guest-transaction', ['id' => $this->guest_id]);
    }

    public function render()
    {
        return view('livewire.frontdesk.monitoring.damage-charges');
    }
}
