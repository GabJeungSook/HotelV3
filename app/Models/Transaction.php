<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected static function booted(): void
    {
        // Auto-fill shift_session_id and processed_by_user_id on creation
        static::creating(function (Transaction $transaction) {
            if (auth()->check()) {
                if (!$transaction->processed_by_user_id) {
                    $transaction->processed_by_user_id = auth()->id();
                }

                if (!$transaction->shift_session_id) {
                    $session = ShiftSession::where('branch_id', auth()->user()->branch_id)
                        ->where('cash_drawer_id', auth()->user()->cash_drawer_id)
                        ->where('status', 'open')
                        ->first();
                    if ($session) {
                        $transaction->shift_session_id = $session->id;
                    }
                }
            }
        });

        // Auto-update deposit balances on checkin_details after transaction created
        static::created(function (Transaction $transaction) {
            if (!$transaction->checkin_detail_id) {
                return;
            }

            $checkinDetail = CheckinDetail::find($transaction->checkin_detail_id);
            if (!$checkinDetail) {
                return;
            }

            // Deposit (type 2) — update balance based on deposit_type
            if ($transaction->transaction_type_id == 2) {
                if ($transaction->deposit_type === 'room_key') {
                    $checkinDetail->increment('room_deposit_balance', $transaction->payable_amount);
                } elseif ($transaction->deposit_type === 'guest') {
                    $checkinDetail->increment('deposit_balance', $transaction->payable_amount);
                }
            }

            // Cashout (type 5) — decrement guest deposit balance
            if ($transaction->transaction_type_id == 5) {
                $checkinDetail->decrement('deposit_balance', $transaction->payable_amount);
            }
        });
    }

    public function checkin_details()
    {
        return $this->belongsTo(CheckinDetail::class, 'checkin_detail_id');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function transaction_type()
    {
        return $this->belongsTo(TransactionType::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function assigned_frontdesk()
    {
        return $this->belongsTo(User::class, 'assigned_frontdesk_id', 'id');
    }

    public function shift_log()
    {
        return $this->belongsTo(ShiftLog::class, 'shift_log_id');
    }

    public function shiftSession()
    {
        return $this->belongsTo(ShiftSession::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }
}
