<?php

namespace App\Livewire\BackOffice\Reports;

use Livewire\Component;
use App\Models\RoomBoyReport as reportQuery;
use App\Models\User;

class RoomBoyReport extends Component
{
    public $roomboy_id;
    public $shift;
    public $date;

    public $total_cleaned = 0;

    public function render()
    {
        $roomboys = User::whereHas('roles', fn($q) => $q->where('name', 'roomboy'))->get();

        $query = reportQuery::query()
            ->whereHas('room', function ($q) {
                $q->where('branch_id', auth()->user()->branch_id);
            })
            ->with([
                'room',
                'roomboy', // make sure RoomBoyReport has roomboy() relationship
            ])
            ->when($this->roomboy_id, fn($q) => $q->where('roomboy_id', $this->roomboy_id))
            ->when($this->shift, fn($q) => $q->where('shift', $this->shift))
            ->when($this->date, fn($q) => $q->whereDate('created_at', $this->date))
            ->orderByDesc('created_at');

        $reports = $query->get();

        // total cleaned under the same filters
        $this->total_cleaned = $reports->where('is_cleaned', true)->count();

        return view('livewire.back-office.reports.room-boy-report', [
            'reports' => $reports,
            'roomboys' => $roomboys,
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['roomboy_id', 'shift', 'date']);
    }
}
