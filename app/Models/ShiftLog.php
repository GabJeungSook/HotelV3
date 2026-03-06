<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftLog extends Model
{
    protected $guarded = [];
        protected $casts = [
        'time_in'  => 'datetime',
        'time_out' => 'datetime',
    ];

    use HasFactory;

    public function frontdesk()
    {
        return $this->belongsTo(User::class, 'frontdesk_id');
    }

    public function cash_drawer()
    {
        return $this->belongsTo(CashDrawer::class, 'cash_drawer_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
