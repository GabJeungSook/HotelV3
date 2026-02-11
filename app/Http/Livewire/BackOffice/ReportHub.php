<?php

namespace App\Http\Livewire\BackOffice;

use Livewire\Component;

class ReportHub extends Component
{
     // optional: keep selection in URL for refresh/share
    public string $report = 'sales';

    protected $queryString = [
        'report' => ['except' => 'sales'],
    ];

    public function getReportsProperty(): array
    {
        // Key => config used by UI + dynamic component
        return [
            'sales' => [
                'label' => 'Sales Report',
                'component' => 'back-office.sales-report',
            ],
            'new-guest' => [
                'label' => 'New Guest Report',
                'component' => 'back-office.reports.new-guest-report',
            ],
            'check-out' => [
                'label' => 'Check-out Guest Report',
                'component' => 'back-office.reports.checkout-guest',
            ],
            'guest-per-room-type' => [
                'label' => 'Guest per Room Type Report',
                'component' => 'back-office.reports.guest-per-room-type',
            ],
            'room-boy' => [
                'label' => 'Room Boy Report',
                'component' => 'back-office.reports.room-boy-report',
            ],
            // 'unoccupied' => [
            //     'label' => 'Unoccupied Room Report',
            //     'component' => 'back-office.reports.unoccupied-room-report',
            // ],
            'extended' => [
                'label' => 'Extended Guest Report',
                'component' => 'back-office.reports.extended-guest-report',
            ],
            'inventory' => [
                'label' => 'Inventory Report',
                'component' => 'back-office.inventory-report',
            ],
            'frontdesk' => [
                'label' => 'Frontdesk Report',
                'component' => 'back-office.reports.frontdesk-report',
            ],
        ];
    }

    public function getActiveComponentProperty(): ?string
    {
        return $this->reports[$this->report]['component'] ?? null;
    }
    public function render()
    {
        return view('livewire.back-office.report-hub');
    }
}
