<?php

namespace App\Http\Livewire\Frontdesk;
use App\Models\CashOnDrawer;
use App\Models\Expense;
use App\Models\ShiftLog;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use WireUi\Traits\Actions;

class CashOnHand extends Component
{
    use Actions;
    public $total_transactions = 0;
    public $total_expenses = 0;
    public $total_deposits = 0;
    public $logout_modal = false;
    public $withdraw_modal = false;
    public $remittance;
    public $description;
    public $authorization_modal = false;
    public $code;

    public function mount()
    {
        $this->total_transactions = CashOnDrawer::where('branch_id', auth()->user()->branch_id)
            ->where('cash_drawer_id', auth()->user()->cash_drawer_id)
            ->where('transaction_date', now()->toDateString())
            ->whereNot('transaction_type', 'deposit')
            ->sum('amount');

        $this->total_expenses = Expense::where('branch_id', auth()->user()->branch_id)
            ->where('user_id', auth()->user()->id)
            ->sum('amount');

        $deposits = CashOnDrawer::where('branch_id', auth()->user()->branch_id)
            ->where('cash_drawer_id', auth()->user()->cash_drawer_id)
            ->where('transaction_date', now()->toDateString())
            ->where('transaction_type', 'deposit')
            ->sum('amount');
        $deductions = CashOnDrawer::where('branch_id', auth()->user()->branch_id)
            ->where('cash_drawer_id', auth()->user()->cash_drawer_id)
            ->where('transaction_date', now()->toDateString())
            ->sum('deduction');

        $this->total_deposits = $deposits - $deductions;

    }

    public function confirmLogout()
    {
        $this->validate([
            'remittance' => 'required|numeric|min:0',
            'description' => 'required'
        ], [
            'remittance.required' => 'Please enter the beginning cash amount.',
            'remittance.numeric' => 'The beginning cash must be a valid number.',
            'remittance.min' => 'The beginning cash cannot be negative.',
            'description.required' => 'Please enter a description for the remittance.'
        ]);

        $this->logout_modal = true;
    }

    public function enterPasscode()
    {
       $this->logout_modal = false;
       $this->authorization_modal = true;

    }

    public function endShiftConfirm()
    {
        $this->validate([
             'code' => 'required|numeric',
        ], [
            'code.required' => 'Please enter your passcode.',
            'code.numeric' => 'The passcode must be a number.',
        ]);

        if(auth()->user()->frontdesk->passcode !== $this->code) {
            $this->dialog()->error(
                $title = 'Unauthorized',
                $description = 'The passcode you entered is incorrect. Please try again.'
            );
            return;

        }else{
            $shift = ShiftLog::where('frontdesk_id', auth()->user()->id)
                ->where('cash_drawer_id', auth()->user()->cash_drawer_id)
                ->whereNull('time_out')
                ->first();
            $shift->end_cash = $this->remittance;
            $shift->description = $this->description;
            $shift->save();

            Auth::logout();

            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('login');
        }
      
    }

    public function render()
    {
        return view('livewire.frontdesk.cash-on-hand');
    }


}
