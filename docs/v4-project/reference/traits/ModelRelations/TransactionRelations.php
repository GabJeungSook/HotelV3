<?php

namespace App\Traits\ModelRelations;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\SaleReturn;
use App\Models\Shift;
use App\Models\TransactionDiscount;
use App\Models\TransactionItem;
use App\Models\TransactionPayment;
use App\Models\TransactionTax;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait TransactionRelations
{
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function voidedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(TransactionPayment::class);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(TransactionDiscount::class);
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(TransactionTax::class);
    }

    public function saleReturns(): HasMany
    {
        return $this->hasMany(SaleReturn::class);
    }
}
