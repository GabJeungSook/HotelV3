<?php

namespace App\Http\Livewire\BackOffice;

use Livewire\Component;

class TemporaryFrontdeskReport extends Component
{
    public $date_from;
    public $date_to;
    public $filter;
    public $shift;
    public function mount()
    {
     $this->date_from = '2026-03-08';
     $this->date_to = '2026-03-09';
     $this->filter = 1;
     $this->shift = 'PM';
    }

    public function changeFilter()
    {
        $this->filter == 1 ? $this->shift = 'PM' : $this->shift = 'AM';
        dd($this->shift);
    }
    
    public function render()
    {
        return view('livewire.back-office.temporary-frontdesk-report');
    }
}
