<?php

namespace App\Traits\ModelRelations;

use App\Models\BranchInventory;
use App\Models\BranchProductPrice;
use App\Models\Product;
use App\Models\ReceiptSetting;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\StockAlert;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\TransactionSequence;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait BranchRelations
{
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function receiptSetting(): HasOne
    {
        return $this->hasOne(ReceiptSetting::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(Product::class, BranchInventory::class, 'branch_id', 'id', 'id', 'product_id');
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(BranchInventory::class);
    }

    public function stockAlerts(): HasMany
    {
        return $this->hasMany(StockAlert::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function branchProductPrices(): HasMany
    {
        return $this->hasMany(BranchProductPrice::class);
    }

    public function transactionSequences(): HasMany
    {
        return $this->hasMany(TransactionSequence::class);
    }
}
