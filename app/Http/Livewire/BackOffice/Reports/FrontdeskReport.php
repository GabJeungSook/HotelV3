<?php

namespace App\Http\Livewire\BackOffice\Reports;

use Livewire\Component;
use App\Models\CashDrawer;
use App\Models\ShiftLog;
use Carbon\Carbon;

class FrontdeskReport extends Component
{
    public $shift = '';
    public $date = ''; // YYYY-MM-DD
    public $drawer_id = '';

    public function mount()
    {
        // default date today (optional)
         $this->date = $this->date ?: now()->toDateString();
    }

     public function resetFilters()
    {
        $this->reset(['shift', 'date', 'drawer_id']);
    }

      public function getDrawersProperty()
    {
        return CashDrawer::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

        public function getGroupsProperty()
    {
        $logs = ShiftLog::query()
            ->with([
                // change fields if your frontdesk model uses a different name column
                'frontdesk:id,name',
                'cash_drawer:id,name',
            ])
            ->when($this->shift !== '', fn($q) => $q->where('shift', $this->shift))
            ->when($this->drawer_id !== '', fn($q) => $q->where('cash_drawer_id', $this->drawer_id))
            ->when($this->date !== '', fn($q) => $q->whereDate('time_in', $this->date))
            ->orderBy('shift')
            ->orderBy('time_in')
            ->get();

        // group by shift + date(time_in)
        return $logs->groupBy(function ($log) {
                $shift = $log->shift ?? 'UNKNOWN';
                $date  = optional($log->time_in)->toDateString() ?? 'UNKNOWN_DATE';
                return $shift . '|' . $date;
            })
            ->map(function ($items, $key) {
                [$shift, $dateStr] = explode('|', $key);

                $dateLabel = $dateStr !== 'UNKNOWN_DATE'
                    ? Carbon::parse($dateStr)->format('F d, Y')
                    : '—';

                return [
                    'shift' => $shift,
                    'date_label' => $dateLabel,
                    'rows' => $items->map(function ($log) {
                        return [
                            'frontdesk_name' => $log->frontdesk->name ?? '—',
                            'drawer_name'    => $log->cash_drawer?->name ?? '—',
                            'time_in'        => $log->time_in ? $log->time_in->format('h:i A') : '—',
                            'time_out'       => $log->time_out ? $log->time_out->format('h:i A') : '—',
                        ];
                    })->values()->all(),
                ];
            })
            ->values()
            ->all();
    }

    public function render()
    {
        return view('livewire.back-office.reports.frontdesk-report', [
            'groups' => $this->groups,
            'drawers' => $this->drawers,
        ]);
    }
}
