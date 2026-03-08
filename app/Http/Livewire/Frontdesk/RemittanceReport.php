<?php

namespace App\Http\Livewire\Frontdesk;

use App\Models\User;
use App\Models\Remittance;
use Livewire\Component;

class RemittanceReport extends Component
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
        return view('livewire.frontdesk.remittance-report', [
            'remittances' => Remittance::where('branch_id', auth()->user()->branch_id)->where('user_id', $this->frontdesk_id)
            ->when($this->shift, function ($query) {
                $query->where('shift', $this->shift);
            })
            ->get(),
        ]);
    }
}
