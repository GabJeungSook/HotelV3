<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftForwardedGuest extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_session_id',
        'checkin_detail_id',
        'room_id',
        'room_charge_amount',
        'room_deposit_amount',
        'guest_deposit_balance',
    ];

    protected $casts = [
        'room_charge_amount' => 'decimal:2',
        'room_deposit_amount' => 'decimal:2',
        'guest_deposit_balance' => 'decimal:2',
    ];

    public function shiftSession()
    {
        return $this->belongsTo(ShiftSession::class);
    }

    public function checkinDetail()
    {
        return $this->belongsTo(CheckinDetail::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
