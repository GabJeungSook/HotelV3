<?php

namespace App\Http\Livewire\Frontdesk;
use App\Models\CashOnDrawer;
use App\Models\ShiftLog;
use Livewire\Component;

class BeginningCash extends Component
{
    public $total_transactions = 0;
    public $total_deposits = 0;

    public $previous_shift;

    public function mount()
    {
        $user = auth()->user();
        $this->previous_shift = ShiftLog::with('frontdesk:id,name')
        ->where('cash_drawer_id', $user->cash_drawer_id)
        ->whereNotNull('time_out')
        ->orderByDesc('time_out')
        ->first();

        $this->total_transactions = CashOnDrawer::where('branch_id', $user->branch_id)
            ->where('cash_drawer_id', $user->cash_drawer_id)
            ->whereBetween('transaction_date', [
            $this->previous_shift?->time_in?->toDateString() ?? now()->toDateString(),
            $this->previous_shift?->time_out?->toDateString() ?? now()->toDateString()
            ])
            ->whereNot('transaction_type', 'deposit')
            ->sum('amount');



        $this->total_deposits = CashOnDrawer::where('branch_id', $user->branch_id)
            ->where('cash_drawer_id', $user->cash_drawer_id)
            ->whereBetween('transaction_date', [
            $this->previous_shift?->time_in?->toDateString() ?? now()->toDateString(),
            $this->previous_shift?->time_out?->toDateString() ?? now()->toDateString()
            ])
            ->where('transaction_type', 'deposit')
            ->sum('amount');
    }

    public function render()
    {
        return view('livewire.frontdesk.beginning-cash');
    }
}
