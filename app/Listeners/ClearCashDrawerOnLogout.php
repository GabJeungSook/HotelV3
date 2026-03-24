<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ClearCashDrawerOnLogout
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Logout  $event
     * @return void
     */
    public function handle(Logout $event)
    {
         // $event->user can be null in some edge cases; guard it.
        if (! $event->user) {
            return;
        }

        if ($event->user->cash_drawer_id) {
            \App\Models\CashDrawer::where('id', $event->user->cash_drawer_id)
                ->update(['is_active' => false]);
        }

        $event->user->forceFill([
            'cash_drawer_id' => null,
        ])->save();

        //update shift logs table
        $shift_log = \App\Models\ShiftLog::where('frontdesk_id', $event->user->id)
            ->whereNull('time_out')
            ->latest()
            ->first();
        if ($shift_log) {
            $shift_log->time_out = now();
            $shift_log->end_cash = $shift_log->end_cash ?: 1.00;
            $shift_log->save();
        }
    }
}
