<?php

namespace App\Traits\ModelScopes;

use Illuminate\Database\Eloquent\Builder;

trait ScopesToCashier
{
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('cashier_id', auth()->id());
    }
}
