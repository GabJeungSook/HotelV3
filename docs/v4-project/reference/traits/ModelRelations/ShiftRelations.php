<?php

namespace App\Traits\ModelRelations;

use App\Enums\TransactionStatus;
use App\Models\Branch;
use App\Models\CashDrawerLog;
use App\Models\Expense;
use App\Models\ManagerOverride;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait ShiftRelations
{
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function heldOrders(): HasMany
    {
        return $this->hasMany(Transaction::class)
            ->where('status', TransactionStatus::Held);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function cashDrawerLogs(): HasMany
    {
        return $this->hasMany(CashDrawerLog::class);
    }

    public function managerOverrides(): HasMany
    {
        return $this->hasMany(ManagerOverride::class);
    }
}
