<?php

namespace App\Livewire\Frontdesk\Monitoring;

use App\Models\ActivityLog;
use App\Models\CheckinDetail;
use App\Models\Guest;
use App\Models\Room;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class GuestDeposit extends Component
{
    use WireUiActions;

    public $guest_id;
    public $guest;
    public $room;
    public $check_in_detail;

    public $deposit_amount;
    public $deposit_remarks;
    public $deduction_amount;
    public $mode = 'add';
    public $available_balance = 0;
    public $room_deposit_balance = 0;

    public function mount($id)
    {
        $this->guest_id = $id;

        $this->guest = Guest::where('branch_id', auth()->user()->branch_id)
            ->where('id', $id)
            ->first();

        $this->check_in_detail = CheckinDetail::where('guest_id', $id)
            ->where('is_check_out', false)
            ->first();

        $this->room = Room::where('branch_id', auth()->user()->branch_id)
            ->where('id', $this->check_in_detail->room_id)
            ->first();

        $this->available_balance = $this->check_in_detail->deposit_balance ?? 0;
        $this->room_deposit_balance = $this->check_in_detail->room_deposit_balance ?? 0;
    }

    public function saveDeposit()
    {
        $this->validate([
            'deposit_amount' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        $users = User::role('frontdesk')->get();
        $threshold = now()->subMinutes(5)->timestamp;
        $onlineUsers = [];

        foreach ($users as $user) {
            if ($this->isUserOnline($user, $threshold)) {
                $onlineUsers[] = $user->shiftLogs()->whereNull('time_out')->latest()->first();
            }
        }

        $shiftLogId = collect($onlineUsers)->where('frontdesk_id', auth()->user()->id)->first()->id ?? null;

        Transaction::create([
            'branch_id' => $this->check_in_detail->guest->branch_id,
            'shift_log_id' => $shiftLogId,
            'checkin_detail_id' => $this->check_in_detail->id,
            'cash_drawer_id' => auth()->user()->cash_drawer_id,
            'room_id' => $this->check_in_detail->room_id,
            'guest_id' => $this->check_in_detail->guest_id,
            'floor_id' => $this->check_in_detail->room->floor_id,
            'transaction_type_id' => 2,
            'deposit_type' => 'guest',
            'assigned_frontdesk_id' => json_encode([auth()->id(), auth()->user()->name]),
            'description' => 'Deposit',
            'payable_amount' => $this->deposit_amount,
            'paid_amount' => 0,
            'change_amount' => 0,
            'deposit_amount' => $this->deposit_amount,
            'paid_at' => now(),
            'override_at' => null,
            'remarks' => 'Guest Deposit: ' . $this->deposit_remarks,
            'shift' => (now()->hour >= 8 && now()->hour < 20) ? 'AM' : 'PM',
        ]);

        ActivityLog::create([
            'branch_id' => auth()->user()->branch_id,
            'user_id' => auth()->user()->id,
            'activity' => 'Add Deposit',
            'description' => 'Added new deposit of ₱' . $this->deposit_amount . ' for guest ' . $this->check_in_detail->guest->name,
        ]);

        DB::commit();

        $this->dialog()->success(
            $title = 'Success',
            $description = 'Deposit successfully saved'
        );

        return redirect()->route('frontdesk.guest-transaction', [
            'id' => $this->guest_id,
        ]);
    }

    public function saveDeduction()
    {
        $this->validate([
            'deduction_amount' => 'required|numeric|min:1|max:' . $this->available_balance,
        ]);

        DB::beginTransaction();

        $users = User::role('frontdesk')->get();
        $threshold = now()->subMinutes(5)->timestamp;
        $onlineUsers = [];

        foreach ($users as $user) {
            if ($this->isUserOnline($user, $threshold)) {
                $onlineUsers[] = $user->shiftLogs()->whereNull('time_out')->latest()->first();
            }
        }

        $shiftLogId = collect($onlineUsers)->where('frontdesk_id', auth()->user()->id)->first()->id ?? null;

        Transaction::create([
            'branch_id' => $this->check_in_detail->guest->branch_id,
            'shift_log_id' => $shiftLogId,
            'checkin_detail_id' => $this->check_in_detail->id,
            'cash_drawer_id' => auth()->user()->cash_drawer_id,
            'room_id' => $this->check_in_detail->room_id,
            'guest_id' => $this->check_in_detail->guest_id,
            'floor_id' => $this->check_in_detail->room->floor_id,
            'transaction_type_id' => 5,
            'assigned_frontdesk_id' => json_encode([auth()->id(), auth()->user()->name]),
            'description' => 'Cashout',
            'payable_amount' => $this->deduction_amount,
            'paid_amount' => 0,
            'change_amount' => 0,
            'deposit_amount' => $this->deduction_amount,
            'paid_at' => now(),
            'override_at' => null,
            'remarks' => 'Guest Deduction of Deposit: ₱' . $this->deduction_amount . ' deducted.',
            'shift' => (now()->hour >= 8 && now()->hour < 20) ? 'AM' : 'PM',
        ]);

        ActivityLog::create([
            'branch_id' => auth()->user()->branch_id,
            'user_id' => auth()->user()->id,
            'activity' => 'Deduct Deposit',
            'description' => 'Deducted deposit of ₱' . $this->deduction_amount . ' for guest ' . $this->check_in_detail->guest->name,
        ]);

        DB::commit();

        $this->dialog()->success(
            $title = 'Success',
            $description = 'Deposit deduction successfully saved'
        );

        return redirect()->route('frontdesk.guest-transaction', [
            'id' => $this->guest_id,
        ]);
    }

    public function cancel()
    {
        return redirect()->route('frontdesk.guest-transaction', ['id' => $this->guest_id]);
    }

    private function isUserOnline($user, $threshold)
    {
        return $user->sessions()
            ->where('last_activity', '>=', $threshold)
            ->exists();
    }

    public function render()
    {
        return view('livewire.frontdesk.monitoring.guest-deposit');
    }
}
