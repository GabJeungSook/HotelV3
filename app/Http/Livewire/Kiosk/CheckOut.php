<?php

namespace App\Http\Livewire\Kiosk;

use App\Models\Floor;
use App\Models\Room;
use Livewire\Component;

class CheckOut extends Component
{
    public $steps;
    public $floors = [];
    public $branchId;
    public $floor_id, $room_id;
    public $qr_code;

    public function mount()
    {
        $this->floors = Floor::get();
        $this->steps = 1;
    }
    public function render()
    {
        $query = Room::where('status', 'Occupied')
        ->with(['latestCheckInDetail.guest'])
        ->orderBy('number', 'asc');

        if (!empty($this->floor_id)) {
            $query->where('floor_id', $this->floor_id);
        }


        return view('livewire.kiosk.check-out', [
            'rooms' => Room::where('status', 'Occupied')
                ->when($this->floor_id, function ($query) {
                    return $query->where('floor_id', $this->floor_id);
                })
                ->with(['latestCheckInDetail.guest'])
                ->orderBy('number', 'asc')
                ->get(),
        ]);
    }

    public function selectRoom($room_id)
    {
        $this->room_id = $room_id;
    }


      public function backRoom()
    {
        $this->floor_id = null;
    }
}
