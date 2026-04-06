<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_session_id',
        'branch_id',
        // Header
        'frontdesk_names',
        'shift_opened_at',
        'shift_closed_at',
        // Cash Drawer
        'opening_cash',
        'closing_cash',
        // Operations A: Sales by type
        'checkin_count',
        'checkin_amount',
        'extension_count',
        'extension_amount',
        'transfer_count',
        'transfer_amount',
        'damage_count',
        'damage_amount',
        'amenity_count',
        'amenity_amount',
        'food_count',
        'food_amount',
        'unclaimed_count',
        'unclaimed_amount',
        // Operations B: Room summary
        'forwarded_room_count',
        'forwarded_room_amount',
        'current_room_count',
        'current_room_amount',
        // Deposits
        'room_deposit_collected',
        'guest_deposit_collected',
        'cashout_amount',
        'fwd_room_deposit_count',
        'fwd_room_deposit_amount',
        'fwd_guest_deposit_count',
        'fwd_guest_deposit_amount',
        // Checkout
        'checkout_count',
        'checkout_room_deposit',
        // Forwarded out
        'remaining_room_deposit_count',
        'remaining_room_deposit_amount',
        'remaining_guest_deposit_count',
        'remaining_guest_deposit_amount',
        // Final Sales
        'gross_sales',
        'expenses_amount',
        'net_sales',
        // Cash Reconciliation
        'forwarded_balance',
        'remittance_amount',
        'expected_cash',
        'actual_cash',
        'cash_difference',
        // BigBoss
        'floor_summary',
    ];

    protected $casts = [
        'shift_opened_at' => 'datetime',
        'shift_closed_at' => 'datetime',
        'opening_cash' => 'decimal:2',
        'closing_cash' => 'decimal:2',
        'checkin_amount' => 'decimal:2',
        'extension_amount' => 'decimal:2',
        'transfer_amount' => 'decimal:2',
        'damage_amount' => 'decimal:2',
        'amenity_amount' => 'decimal:2',
        'food_amount' => 'decimal:2',
        'unclaimed_amount' => 'decimal:2',
        'forwarded_room_amount' => 'decimal:2',
        'current_room_amount' => 'decimal:2',
        'room_deposit_collected' => 'decimal:2',
        'guest_deposit_collected' => 'decimal:2',
        'cashout_amount' => 'decimal:2',
        'fwd_room_deposit_amount' => 'decimal:2',
        'fwd_guest_deposit_amount' => 'decimal:2',
        'checkout_room_deposit' => 'decimal:2',
        'remaining_room_deposit_amount' => 'decimal:2',
        'remaining_guest_deposit_amount' => 'decimal:2',
        'gross_sales' => 'decimal:2',
        'expenses_amount' => 'decimal:2',
        'net_sales' => 'decimal:2',
        'forwarded_balance' => 'decimal:2',
        'remittance_amount' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'actual_cash' => 'decimal:2',
        'cash_difference' => 'decimal:2',
        'floor_summary' => 'array',
    ];

    public function shiftSession()
    {
        return $this->belongsTo(ShiftSession::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getSalesTotalCountAttribute(): int
    {
        return $this->checkin_count + $this->extension_count + $this->transfer_count
             + $this->damage_count + $this->amenity_count + $this->food_count;
    }

    public function getSalesTotalAmountAttribute(): float
    {
        return $this->checkin_amount + $this->extension_amount + $this->transfer_amount
             + $this->damage_amount + $this->amenity_amount + $this->food_amount;
    }
}
