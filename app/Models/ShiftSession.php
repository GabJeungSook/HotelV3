<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'cash_drawer_id',
        'shift_type',
        'shift_date',
        'status',
        'opened_at',
        'closed_at',
        'opening_cash',
        'closing_cash',
    ];

    protected $casts = [
        'shift_date' => 'date',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_cash' => 'decimal:2',
        'closing_cash' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function cashDrawer()
    {
        return $this->belongsTo(CashDrawer::class);
    }

    public function members()
    {
        return $this->hasMany(ShiftMember::class);
    }

    public function primaryMember()
    {
        return $this->hasOne(ShiftMember::class)->where('role', 'primary');
    }

    public function forwardedGuests()
    {
        return $this->hasMany(ShiftForwardedGuest::class);
    }

    public function snapshot()
    {
        return $this->hasOne(ShiftSnapshot::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function remittances()
    {
        return $this->hasMany(Remittance::class);
    }

    public function checkinDetails()
    {
        return $this->hasMany(CheckinDetail::class, 'check_in_shift_session_id');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function getMemberNamesAttribute(): string
    {
        return $this->members->pluck('user.name')->join(' & ');
    }
}
