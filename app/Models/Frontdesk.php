<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frontdesk extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function assignedFrontdesks()
    {
        return $this->hasMany(AssignedFrontdesk::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function cash_drawer()
    {
        return $this->belongsTo(CashDrawer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shiftLogs()
    {
        return $this->hasMany(ShiftLog::class);
    }
}
