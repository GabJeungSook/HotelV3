<?php

namespace App\Traits\ModelRelations;

use App\Models\Branch;
use App\Models\CashDrawerLog;
use App\Models\Expense;
use App\Models\ManagerOverride;
use App\Models\PersonalInformation;
use App\Models\Shift;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait UserRelations
{
    public function personalInformation(): HasOne
    {
        return $this->hasOne(PersonalInformation::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class, 'cashier_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'cashier_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'performed_by');
    }

    public function cashDrawerLogs(): HasMany
    {
        return $this->hasMany(CashDrawerLog::class, 'performed_by');
    }

    public function requestedOverrides(): HasMany
    {
        return $this->hasMany(ManagerOverride::class, 'requested_by');
    }

    public function approvedOverrides(): HasMany
    {
        return $this->hasMany(ManagerOverride::class, 'approved_by');
    }
}
