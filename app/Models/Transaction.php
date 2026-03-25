<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = [];

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

}
