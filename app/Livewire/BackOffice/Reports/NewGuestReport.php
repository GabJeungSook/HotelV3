<?php

namespace App\Livewire\BackOffice\Reports;

use Livewire\Component;
use App\Models\NewGuestReport as reportQuery;
use App\Models\Room;
use App\Models\Frontdesk;

class NewGuestReport extends Component
{
    public $frontdesk_id;
    public $shift;
    public $date;
    public $time;

    public $total_guest = 0;

    public function render()
    {
        $query = reportQuery::query()
            ->where('branch_id', auth()->user()->branch_id)
            ->with([
                'room',
                'checkinDetail.guest',
                'checkinDetail',
                'checkinDetail.frontdesk',
                'checkinDetail.extendedGuestReports',
            ])
            ->when($this->frontdesk_id, fn($q) =>
                $q->where('frontdesk_id', $this->frontdesk_id)
            )
            ->when($this->shift, fn($q) =>
                $q->where('shift', $this->shift)
            )
            ->when($this->date, fn($q) =>
                $q->whereDate('created_at', $this->date)
            )
            ->when($this->time, fn($q) =>
                $q->whereTime('created_at', '<=', $this->time)
            )
            ->orderByDesc('created_at');

        $reports = $query->get();

        $this->total_guest = $reports->count();

        return view('livewire.back-office.reports.new-guest-report', [
            'reports' => $reports,
            'frontdesks' => Frontdesk::where(
                'branch_id',
                auth()->user()->branch_id
            )->get(),
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['frontdesk_id', 'shift', 'date', 'time']);
    }
}
