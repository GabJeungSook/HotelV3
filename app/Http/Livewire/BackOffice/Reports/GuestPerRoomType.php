<?php

namespace App\Http\Livewire\BackOffice\Reports;

use Livewire\Component;
use App\Models\CheckOutGuestReport;
use App\Models\Frontdesk;
use App\Models\Type;

class GuestPerRoomType extends Component
{
    public $frontdesk_id;
    public $room_type_id;
    public $shift;
    public $date;
    public $time;

    public $total_guest = 0;

    public function render()
    {
        $query = CheckOutGuestReport::query()
            ->whereHas('room', function ($q) {
                $q->where('branch_id', auth()->user()->branch_id)
                  ->when($this->room_type_id, fn($q2) => $q2->where('type_id', $this->room_type_id));
            })
            ->with([
                'room.type',
                'checkinDetail.guest',
                'checkinDetail',
                'frontdesk',
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

        return view('livewire.back-office.reports.guest-per-room-type', [
            'reports' => $reports,
            'frontdesks' => Frontdesk::where('branch_id', auth()->user()->branch_id)->get(),
            'room_types' => Type::where('branch_id', auth()->user()->branch_id)->get(),
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['frontdesk_id', 'room_type_id', 'shift', 'date', 'time']);
    }
}
