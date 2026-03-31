<?php

namespace App\Livewire\Frontdesk;

use App\Models\ActivityLog;
use App\Models\Room;
use App\Models\Type;
use App\Models\Floor;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class PriorityRoom extends Component
{
    use WireUiActions;

    public $filterType = '';
    public $filterFloor = '';
    public $filterPriority = '';
    public $branch_id;

    private function getBranchId(): int
    {
        return auth()->user()->hasRole('superadmin') ? $this->branch_id : auth()->user()->branch_id;
    }

    public function togglePriority($roomId)
    {
        $room = Room::find($roomId);
        if (!$room) return;

        $room->update(['is_priority' => !$room->is_priority]);

        ActivityLog::create([
            'branch_id' => $this->getBranchId(),
            'user_id' => auth()->id(),
            'activity' => $room->is_priority ? 'Set Priority' : 'Remove Priority',
            'description' => 'Room #' . $room->number . ($room->is_priority ? ' set as priority' : ' removed from priority'),
        ]);
    }

    public function bulkSetPriority()
    {
        $query = Room::where('branch_id', $this->getBranchId())
            ->whereIn('status', ['Available', 'Cleaned'])
            ->where('is_priority', false)
            ->when($this->filterType, fn ($q) => $q->where('type_id', $this->filterType))
            ->when($this->filterFloor, fn ($q) => $q->where('floor_id', $this->filterFloor));

        $count = $query->count();
        $query->update(['is_priority' => true]);

        ActivityLog::create([
            'branch_id' => $this->getBranchId(),
            'user_id' => auth()->id(),
            'activity' => 'Bulk Set Priority',
            'description' => 'Set ' . $count . ' rooms as priority',
        ]);
    }

    public function bulkRemovePriority()
    {
        $query = Room::where('branch_id', $this->getBranchId())
            ->whereIn('status', ['Available', 'Cleaned'])
            ->where('is_priority', true)
            ->when($this->filterType, fn ($q) => $q->where('type_id', $this->filterType))
            ->when($this->filterFloor, fn ($q) => $q->where('floor_id', $this->filterFloor));

        $count = $query->count();
        $query->update(['is_priority' => false]);

        ActivityLog::create([
            'branch_id' => $this->getBranchId(),
            'user_id' => auth()->id(),
            'activity' => 'Bulk Remove Priority',
            'description' => 'Removed ' . $count . ' rooms from priority',
        ]);
    }

    public function render()
    {
        $branchId = $this->getBranchId();

        $rooms = Room::where('branch_id', $branchId)
            ->whereIn('status', ['Available', 'Cleaned'])
            ->with(['type', 'floor'])
            ->when($this->filterType, fn ($q) => $q->where('type_id', $this->filterType))
            ->when($this->filterFloor, fn ($q) => $q->where('floor_id', $this->filterFloor))
            ->when($this->filterPriority === 'yes', fn ($q) => $q->where('is_priority', true))
            ->when($this->filterPriority === 'no', fn ($q) => $q->where('is_priority', false))
            ->orderBy('is_priority', 'desc')
            ->orderBy('number', 'asc')
            ->get();

        return view('livewire.frontdesk.priority-room', [
            'rooms' => $rooms,
            'priorityCount' => $rooms->where('is_priority', true)->count(),
            'availableCount' => $rooms->where('is_priority', false)->count(),
            'types' => Type::where('branch_id', $branchId)->get(),
            'floors' => Floor::where('branch_id', $branchId)->orderBy('number')->get(),
            'branches' => auth()->user()->hasRole('superadmin') ? \App\Models\Branch::all() : collect(),
        ]);
    }
}
