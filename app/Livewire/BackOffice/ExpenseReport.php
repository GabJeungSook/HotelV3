<?php

namespace App\Livewire\BackOffice;

use App\Models\User;
use App\Models\Expense;
use Livewire\Component;

class ExpenseReport extends Component
{
    public $shift;
    public $frontdesk;
    public $frontdesk_id;

    public function mount()
    {
        $this->frontdesk_id = auth()->user()->id;
    }

    public function render()
    {
        return view('livewire.back-office.expense-report', [
            'expenses' => Expense::whereHas('expenseCategory', function ($query) {
                $query->where('branch_id', auth()->user()->branch_id);
            })
            ->where('user_id', $this->frontdesk_id)
            ->when($this->shift, function ($query) {
                $query->where('shift', $this->shift);
            })
            ->get(),
        ]);
    }
}
