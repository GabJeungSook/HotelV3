<?php

namespace App\Traits\ModelScopes;

use Illuminate\Database\Eloquent\Builder;

trait ScopesToManagerBranch
{
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('branch_id', auth()->user()->branch_id);
    }
}
