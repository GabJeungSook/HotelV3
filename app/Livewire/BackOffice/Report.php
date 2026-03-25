<?php

namespace App\Livewire\BackOffice;

use Livewire\Component;

class Report extends Component
{
    public $report_type;
    public $report_modal = false;
    public function render()
    {
        return view('livewire.back-office.report');
    }

    public function redirectSalesReport()
    {
        return redirect()->route('back-office.sales-report');
    }

    public function redirectInventoryReport()
    {
         return redirect()->route('back-office.inventory-report');
    }

    public function redirectFrontdeskReport()
    {
         return redirect()->route('back-office.frontdesk-report');
    }

    public function redirectExtendedGuestReport()
    {
         return redirect()->route('back-office.extended-guest-report');
    }

    public function openReport($id)
    {
        $this->report_type = $id;
        $this->report_modal = true;
    }
}
