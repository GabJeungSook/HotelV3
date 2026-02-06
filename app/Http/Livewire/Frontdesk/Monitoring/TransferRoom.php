<?php

namespace App\Http\Livewire\Frontdesk\Monitoring;

use App\Models\Guest;
use App\Models\Room;
use Livewire\Component;

class TransferRoom extends Component
{
    public $guest;
    public $room;

    public function mount($record)
    {
        $this->guest =  $this->guest = Guest::where('branch_id', auth()->user()->branch_id)
                ->where('id', $record)
                ->first();
         $this->room = Room::where('branch_id', auth()->user()->branch_id)
                ->where('id', $this->guest->checkInDetail->room_id)
                ->first();
    }

    public function cancelTransfer()
    {
        return redirect()->route('frontdesk.guest-transaction', ['id' => $this->guest->id]);
    }
    public function render()
    {
        return view('livewire.frontdesk.monitoring.transfer-room');
    }
}
