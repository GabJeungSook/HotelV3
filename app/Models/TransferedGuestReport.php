<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferedGuestReport extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function checkin_detail()
    {
        return $this->belongsTo(CheckinDetail::class);
    }

    public function previous_room()
    {
        return $this->belongsTo(Room::class, 'previous_room_id');
    }

    public function new_room()
    {
        return $this->belongsTo(Room::class, 'new_room_id');
    }

    public function rate()
    {
        return $this->belongsTo(Rate::class);
    }

}
