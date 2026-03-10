<?php

namespace App\Http\Livewire\BackOffice;

use Livewire\Component;

class TemporaryFrontdeskReport extends Component
{
    public $date_from;
    public $date_to;
    public function mount()
    {
     $this->date_from = '2026-03-08';
$this->date_to = '2026-03-09';
    }
    public function render()
    {
        return view('livewire.back-office.temporary-frontdesk-report');
    }
}
