<?php

namespace App\Livewire\Admin\Manage;

use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\Floor;
use App\Models\Room;
use App\Models\Type;
use App\Models\StayingHour;
use App\Models\Rate as RateModel;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Rate extends Component
{
    use WireUiActions;

    public $branch_id;
    public $filter_type_id = '';
    public $filter_floor_id = '';
    public $selected_rooms = [];
    public $select_all = false;
    public $search = '';

    // Rate modal
    public $rate_modal = false;
    public $rate_amounts = [];

    // Staying hour modal
    public $add_staying_hour_modal = false;
    public $number;

    public function mount()
    {
        $this->branch_id = auth()->user()->branch_id;
    }

    public function updatedBranchId()
    {
        $this->reset(['filter_type_id', 'filter_floor_id', 'selected_rooms', 'select_all', 'search']);
    }

    public function updatedSearch()
    {
        $this->reset(['selected_rooms', 'select_all']);
    }

    public function updatedFilterTypeId()
    {
        $this->reset(['selected_rooms', 'select_all']);
    }

    public function updatedFilterFloorId()
    {
        $this->reset(['selected_rooms', 'select_all']);
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected_rooms = $this->getRooms()->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            $this->selected_rooms = [];
        }
    }

    private function getBranchId()
    {
        return auth()->user()->hasRole('superadmin') ? $this->branch_id : auth()->user()->branch_id;
    }

    private function getRooms()
    {
        $branchId = $this->getBranchId();
        if (!$branchId) return collect();

        return Room::where('branch_id', $branchId)
            ->when($this->filter_type_id, fn($q) => $q->where('type_id', $this->filter_type_id))
            ->when($this->filter_floor_id, fn($q) => $q->where('floor_id', $this->filter_floor_id))
            ->when($this->search, fn($q) => $q->where('number', $this->search))
            ->with(['type', 'floor', 'rates.stayingHour'])
            ->orderBy('number')
            ->get();
    }

    public function openRateModal()
    {
        if (empty($this->selected_rooms)) {
            $this->dialog()->error('Oops', 'Please select at least one room.');
            return;
        }

        $branchId = $this->getBranchId();
        $stayingHours = StayingHour::where('branch_id', $branchId)->orderBy('number')->get();

        $this->rate_amounts = [];
        foreach ($stayingHours as $sh) {
            $this->rate_amounts[$sh->id] = [
                'staying_hour_id' => $sh->id,
                'hours' => $sh->number,
                'amount' => '',
            ];
        }

        $this->rate_modal = true;
    }

    public function applyRates()
    {
        $branchId = $this->getBranchId();
        $hasAtLeastOne = false;

        foreach ($this->rate_amounts as $entry) {
            if ($entry['amount'] !== '' && $entry['amount'] !== null) {
                $hasAtLeastOne = true;
                break;
            }
        }

        if (!$hasAtLeastOne) {
            $this->dialog()->error('Oops', 'Please enter at least one rate amount.');
            return;
        }

        $rooms = Room::whereIn('id', $this->selected_rooms)->get();

        foreach ($rooms as $room) {
            foreach ($this->rate_amounts as $entry) {
                if ($entry['amount'] === '' || $entry['amount'] === null) continue;

                RateModel::updateOrCreate(
                    [
                        'branch_id' => $branchId,
                        'room_id' => $room->id,
                        'staying_hour_id' => $entry['staying_hour_id'],
                    ],
                    [
                        'type_id' => $room->type_id,
                        'amount' => $entry['amount'],
                        'is_available' => true,
                    ]
                );
            }
        }

        ActivityLog::create([
            'branch_id' => $branchId,
            'user_id' => auth()->user()->id,
            'activity' => 'Update Rates',
            'description' => 'Updated rates for ' . count($this->selected_rooms) . ' room(s)',
        ]);

        $this->rate_modal = false;
        $this->selected_rooms = [];
        $this->select_all = false;

        $this->dialog()->success('Success', 'Rates have been applied to the selected rooms.');
    }

    public function saveStayingHour()
    {
        $this->validate([
            'number' => 'required|integer|min:1',
        ]);

        $branchId = $this->getBranchId();

        StayingHour::create([
            'branch_id' => $branchId,
            'number' => $this->number,
        ]);

        ActivityLog::create([
            'branch_id' => $branchId,
            'user_id' => auth()->user()->id,
            'activity' => 'Create Staying Hour',
            'description' => 'Created staying hour ' . $this->number . ' hours.',
        ]);

        $this->reset('number');
        $this->add_staying_hour_modal = false;
        $this->dialog()->success('Success', 'Staying hour added successfully.');
    }

    public function render()
    {
        $branchId = $this->getBranchId();

        return view('livewire.admin.manage.rate', [
            'rooms' => $this->getRooms(),
            'types' => $branchId ? Type::where('branch_id', $branchId)->get() : collect(),
            'floors' => $branchId ? Floor::where('branch_id', $branchId)->get() : collect(),
            'stayingHours' => $branchId ? StayingHour::where('branch_id', $branchId)->orderBy('number')->get() : collect(),
            'branches' => Branch::all(),
        ]);
    }
}
