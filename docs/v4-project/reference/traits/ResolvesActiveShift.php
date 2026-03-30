<?php

namespace App\Traits;

use App\Models\Shift;
use App\Models\User;

trait ResolvesActiveShift
{
    protected function activeShift(User $user): ?Shift
    {
        return Shift::where('cashier_id', $user->id)
            ->where('branch_id', $user->branch_id)
            ->where('status', 'open')
            ->latest()
            ->first();
    }
}
