<?php

namespace App\Http\Livewire\Frontdesk;
use App\Models\CashOnDrawer;
use Livewire\Component;

class CashOnHand extends Component
{
    public $total_transactions = 0;
    public $total_deposits = 0;
    public $logout_modal = false;
    public $withdraw_modal = false;

    public function mount()
    {
        $this->total_transactions = CashOnDrawer::where('branch_id', auth()->user()->branch_id)
            ->where('cash_drawer_id', auth()->user()->cash_drawer_id)
            ->where('transaction_date', now()->toDateString())
            ->whereNot('transaction_type', 'deposit')
            ->sum('amount');

        $this->total_deposits = CashOnDrawer::where('branch_id', auth()->user()->branch_id)
            ->where('cash_drawer_id', auth()->user()->cash_drawer_id)
            ->where('transaction_date', now()->toDateString())
            ->where('transaction_type', 'deposit')
            ->sum('amount');
    }
    public function render()
    {
        return view('livewire.frontdesk.cash-on-hand');
    }


}
