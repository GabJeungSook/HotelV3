<?php

namespace App\Livewire\Frontdesk\Monitoring;

use Carbon\Carbon;
use App\Models\Rate;
use App\Models\Room;
use App\Models\Guest;
use App\Models\Branch;
use Livewire\Component;
use App\Models\ActivityLog;
use App\Models\StayingHour;
use App\Models\Transaction;
use App\Models\CheckinDetail;
use App\Models\ExtensionRate;
use App\Models\StayExtension;
use Illuminate\Support\Facades\DB;
use App\Models\ExtendedGuestReport;
use WireUi\Traits\WireUiActions;

class ExtendGuest extends Component
{
    use WireUiActions;
    public $assigned_frontdesk;
    public $guest;
    public $room;
    public $rate;
    public $stayingHour;
    public $extension_rates;
    public $extension_rates_other;
    public $extension_rate_id;
    public $extension_time_reset;

    public $current_time_alloted;
    public $extended_rate;
    public $total_extended_hours;
    public $initial_amount;
    public $extended_amount;
    public $total_amount;
    public $new_rate;
    public $save_type = 'save';
    public $available_deposit = 0;
    public $amount_paid;
    public $excess_amount = 0;
    public $save_excess = false;
    public $change_modal = false;
    public $deposit_pay_modal = false;
    public $pay_modal = false;
    public function mount($record)
    {
        $this->assigned_frontdesk = auth()->user()->assigned_frontdesks;

        $this->guest = Guest::where('branch_id', auth()->user()->branch_id)
                ->where('id', $record)
                ->first();
        $this->room = Room::where('branch_id', auth()->user()->branch_id)
                ->where('id', $this->guest->checkInDetail->room_id)
                ->first();
        $this->rate = Rate::where('branch_id', auth()->user()->branch_id)
                ->where('id', $this->guest->rate_id)
                ->first();
        $this->stayingHour = StayingHour::where(
                'branch_id',
                auth()->user()->branch_id
            )
                ->where('id', $this->rate->staying_hour_id)
                ->first();


        $this->extension_time_reset = Branch::where(
            'id',
            auth()->user()->branch_id
        )->first()->extension_time_reset;
        $this->total_extended_hours = $this->guest->stayExtensions->sum('hours') > $this->extension_time_reset
            ? $this->guest->stayExtensions->sum('hours') - $this->extension_time_reset
            : $this->guest->stayExtensions->sum('hours');

        $this->current_time_alloted = $this->guest->checkInDetail()->first()->number_of_hours;

        $stayingHourIds = Rate::where('branch_id', auth()->user()->branch_id)
            ->where('type_id', $this->rate->type_id)
            ->distinct()
            ->pluck('staying_hour_id');

        $stayingHours = StayingHour::where('branch_id', auth()->user()->branch_id)
            ->whereIn('id', $stayingHourIds)
            ->pluck('number');

        if($this->current_time_alloted == 0 && $this->guest->checkInDetail()->first()->next_extension_is_original == true)
        {
            $this->extension_rates = ExtensionRate::where(
            'branch_id',
            auth()->user()->branch_id
        )->whereIn('hour', $stayingHours)->get();
        }else{
                 $this->extension_rates = ExtensionRate::where(
            'branch_id',
            auth()->user()->branch_id
        )->get();
        }

        // while ($this->current_time_alloted > $this->extension_time_reset) {
        //     $this->current_time_alloted -= $this->extension_time_reset;
        // }

        $this->initial_amount = 0;
        $this->extended_amount = 0;
        $this->total_amount = 0;
        $this->available_deposit = $this->guest->checkInDetail->deposit_balance ?? 0;
    }

    public function updatedExtensionRateId()
    {
        if ($this->extension_rate_id) {
            $this->extended_rate = ExtensionRate::where('branch_id', auth()->user()->branch_id)
                ->where('id', $this->extension_rate_id)
                ->first();
            $current = $this->current_time_alloted + $this->extended_rate->hour;

            // Priority 1: Post-reset → charge original rate
            if (($this->current_time_alloted == 0)
                && $this->guest->checkInDetail()->first()->next_extension_is_original == true) {
                $rate = Rate::where('branch_id', auth()->user()->branch_id)
                    ->where('type_id', $this->rate->type_id)
                    ->whereHas('stayingHour', function ($query) {
                        $query->where('number', $this->extended_rate->hour);
                    })->first();

                // Fallback: next higher rate if no exact match
                if (!$rate) {
                    $extHour = $this->extended_rate->hour;
                    $rate = Rate::where('rates.branch_id', auth()->user()->branch_id)
                        ->where('rates.type_id', $this->rate->type_id)
                        ->whereHas('stayingHour', fn($q) => $q->where('number', '>=', $extHour))
                        ->join('staying_hours', 'rates.staying_hour_id', '=', 'staying_hours.id')
                        ->orderBy('staying_hours.number', 'asc')
                        ->select('rates.*')
                        ->first();
                }

                $this->initial_amount = $rate?->amount ?? 0;
                $this->extended_amount = 0;
                $this->total_amount = $this->initial_amount + $this->extended_amount;
            }
            // Priority 2: Extension crosses cycle boundary (strictly over, not exact)
            elseif ($current > $this->extension_time_reset) {
                $total_current_hours = $this->current_time_alloted + $this->extended_rate->hour;
                $total_current_hours = $total_current_hours - $this->extension_time_reset;
                if ($total_current_hours > $this->extension_time_reset) {
                    $total_current_hours = $this->extension_time_reset;
                }

                // Find exact rate match, or next higher rate if no exact match
                $rate = Rate::where('branch_id', auth()->user()->branch_id)
                    ->where('type_id', $this->rate->type_id)
                    ->whereHas('stayingHour', function ($query) use ($total_current_hours) {
                        $query->where('number', $total_current_hours);
                    })->first();

                if (!$rate) {
                    $rate = Rate::where('rates.branch_id', auth()->user()->branch_id)
                        ->where('rates.type_id', $this->rate->type_id)
                        ->whereHas('stayingHour', function ($query) use ($total_current_hours) {
                            $query->where('number', '>=', $total_current_hours);
                        })
                        ->join('staying_hours', 'rates.staying_hour_id', '=', 'staying_hours.id')
                        ->orderBy('staying_hours.number', 'asc')
                        ->select('rates.*')
                        ->first();
                }

                $extend_hour = $this->extension_time_reset - $this->current_time_alloted;
                $extend = ExtensionRate::where('branch_id', auth()->user()->branch_id)
                    ->where('hour', $extend_hour)->first();

                $this->initial_amount = $rate?->amount ?? 0;
                $this->extended_amount = $extend?->amount ?? 0;
                $this->total_amount = $this->initial_amount + $this->extended_amount;
            }
            // Priority 3: Normal extension (no cycle crossing)
            else {
                $this->initial_amount = 0;
                $this->extended_amount = $this->extended_rate->amount;
                $this->total_amount = $this->initial_amount + $this->extended_amount;
            }
        }

    }

    public function openPayModal()
    {
        $this->amount_paid = null;
        $this->pay_modal = true;
    }

    public function confirmSave($type)
    {
        $this->save_type = $type;
        $this->change_modal = false;
        $this->excess_amount = 0;
        $this->save_excess = false;

        if ($type === 'pay_deposit') {
            if ($this->available_deposit < $this->total_amount) {
                $this->dialog()->error(
                    $title = 'Insufficient Deposit',
                    $description = 'Guest deposit balance is not enough to cover this transaction.'
                );
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
            $this->pay_modal = false;

            if ($this->amount_paid > $this->total_amount) {
                $this->excess_amount = $this->amount_paid - $this->total_amount;
                $this->change_modal = true;
            } elseif ($this->amount_paid < $this->total_amount) {
                $this->pay_modal = true;
                $this->dialog()->error('Oops!', 'Amount paid is less than the total payable amount.');
            } else {
                $this->saveExtend();
            }
            return;
        }

        // For 'save' type, just save directly
        $this->saveExtend();
    }

    public function confirmDepositPay()
    {
        $this->deposit_pay_modal = false;
        $this->saveExtend();
    }

    public function saveExtend()
    {
        $this->validate([
            'extension_rate_id' => 'required',
        ]);

         if (auth()->user()->branch->extension_time_reset == null) {
             $this->dialog()->error(
                $title = 'Missing Authorization Code',
                $description = 'Admin must add authorization code first'
            );
         }else{
             $check_in_detail = CheckinDetail::where(
                'guest_id',
                $this->guest->id
            )->first();

            $rate = ExtensionRate::where('branch_id', auth()->user()->branch_id)
                ->where('id', $this->extension_rate_id)
                ->first();
             DB::beginTransaction();
                $transaction = Transaction::create([
                    'branch_id' => $check_in_detail->guest->branch_id,
                    'checkin_detail_id' => $check_in_detail->id,
                    'cash_drawer_id' => auth()->user()->cash_drawer_id,
                    'room_id' => $check_in_detail->room_id,
                    'guest_id' => $check_in_detail->guest_id,
                    'floor_id' => $check_in_detail->room->floor_id,
                    'transaction_type_id' => 6,
                    'assigned_frontdesk_id' => json_encode([auth()->id(), auth()->user()->name]),
                    'description' => 'Extension',
                    'payable_amount' => $this->total_amount,
                    'paid_amount' => 0,
                    'change_amount' => 0,
                    'deposit_amount' => 0,
                    'paid_at' => null,
                    'override_at' => null,
                    'remarks' => 'Guest Extension : ' . $rate->hour . ' hours',
                    'shift' => (now()->hour >= 8 && now()->hour < 20) ? 'AM' : 'PM',
                ]);
                StayExtension::create([
                    'guest_id' => $check_in_detail->guest_id,
                    'extension_id' => $rate->id,
                    'hours' => $rate->hour,
                    'amount' => $this->total_amount,
                    'frontdesk_ids' => json_encode([auth()->id(), auth()->user()->name]),
                ]);
                 $cycle_hours = $check_in_detail->number_of_hours;
                 $extension_hours = $rate->hour;
                 $total_hours = $cycle_hours + $extension_hours;

                 while ($total_hours >= $this->extension_time_reset) {
                    $total_hours = $total_hours - $this->extension_time_reset;
                 }
                 $next_extension_is_original = ($total_hours == 0);

                 $check_in_detail->update([
                    'number_of_hours' => $total_hours,
                    'next_extension_is_original' => $next_extension_is_original,
                    'check_out_at' =>  Carbon::parse($check_in_detail->check_out_at)->addHours($rate->hour),
                ]);

                $now = Carbon::now();
                $hour = (int) $now->format('H');
                $shift = $now->format('H:i');

                if ($hour >= 8 && $hour < 20) {
                    $shift_schedule = 'AM';
                    $shift_date = $now->format('F j, Y');
                } else {
                    $shift_schedule = 'PM';
                    // For times between 00:00 and 07:59 the PM shift started the previous day (8pm)
                    $shift_date = $hour < 8 ? $now->copy()->subDay()->format('F j, Y') : $now->format('F j, Y');
                }

                // $shift_date = Carbon::parse(auth()->user()->time_in)->format('F j, Y');
                // $shift = Carbon::parse(auth()->user()->time_in)->format('H:i');
                // $hour = Carbon::parse($shift)->hour;

                // if ($hour >= 8 && $hour < 20) {
                // $shift_schedule = 'AM';
                // } else {
                //     $shift_schedule = 'PM';
                // }

                    ExtendedGuestReport::create([
                        'branch_id' => auth()->user()->branch_id,
                        'room_id' =>  $check_in_detail->room_id,
                        'checkin_details_id' => $check_in_detail->id,
                        'number_of_extension' => 1,
                        'total_hours' => $rate->hour,
                        'shift' => $shift_schedule,
                        'frontdesk_id' => auth()->id(),
                        'partner_name' => auth()->user()->name,
                    ]);

                ActivityLog::create([
                'branch_id' => auth()->user()->branch_id,
                'user_id' => auth()->user()->id,
                'activity' => 'Add Extension',
                'description' => 'Added new extension of ₱' . $this->total_amount . ' for guest ' . $check_in_detail->guest->name,
                ]);
             DB::commit();

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
                    $description = 'Extend successfully saved'
                );

                return redirect()->route('frontdesk.guest-transaction', [
                    'id' => $this->guest->id,
                ]);
         }
    }

    public function cancelExtend()
    {
        return redirect()->route('frontdesk.guest-transaction', ['id' => $this->guest->id]);
    }
    public function render()
    {
        return view('livewire.frontdesk.monitoring.extend-guest');
    }
}
