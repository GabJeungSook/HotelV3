<?php

namespace App\Http\Livewire\Frontdesk;
use App\Models\CashOnDrawer;
use App\Models\ShiftLog;
use Livewire\Component;
use WireUi\Traits\Actions;

class BeginningCash extends Component
{
    use Actions;
    public $total_transactions = 0;
    public $total_deposits = 0;

    public $previous_shift;
    public $beginning_cash;

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

    public function saveBeginningCash()
    {
        $this->validate([
            'beginning_cash' => 'required|numeric|min:0'
        ], [
            'beginning_cash.required' => 'Please enter the beginning cash amount.',
            'beginning_cash.numeric' => 'The beginning cash must be a valid number.',
            'beginning_cash.min' => 'The beginning cash cannot be negative.'
        ]);

        $shift = ShiftLog::where('frontdesk_id', auth()->user()->id)
            ->where('cash_drawer_id', auth()->user()->cash_drawer_id)
            ->whereNull('time_out')
            ->first();

        $shift->beginning_cash = $this->beginning_cash;
        $shift->save();

         $this->dialog()->success(
            $title = 'Success',
            $description = 'Beginning cash saved successfully'
        );

        return redirect()->route('frontdesk.room-monitoring');

        
    }

    public function render()
    {
        return view('livewire.frontdesk.beginning-cash');
    }
}
