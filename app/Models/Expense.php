<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\Frontdesk::class, 'user_id'); // since user_id IS frontdesk
    }

    public function shift_log()
    {
        return $this->belongsTo(ShiftLog::class, 'shift_log_id');
    }
}
