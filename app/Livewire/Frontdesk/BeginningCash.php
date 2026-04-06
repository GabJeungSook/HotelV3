<?php

namespace App\Livewire\Frontdesk;

use App\Models\ShiftSession;
use App\Models\ShiftSnapshot;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class BeginningCash extends Component
{
    use WireUiActions;

    public $beginning_cash;
    public $previous_snapshot;
    public $current_session;

    public function mount()
    {
        $user = auth()->user();
        $branchId = $user->branch_id;

        // Get current open session for this user's drawer
        $this->current_session = ShiftSession::where('branch_id', $branchId)
            ->where('cash_drawer_id', $user->cash_drawer_id)
            ->where('status', 'open')
            ->first();

        // Get previous shift's snapshot (last closed session for same drawer)
        $this->previous_snapshot = ShiftSnapshot::whereHas('shiftSession', function ($q) use ($branchId, $user) {
            $q->where('branch_id', $branchId)
              ->where('cash_drawer_id', $user->cash_drawer_id)
              ->where('status', 'closed');
        })
        ->orderByDesc('shift_closed_at')
        ->first();
    }

    public function saveBeginningCash()
    {
        $this->validate([
            'beginning_cash' => 'required|numeric|min:0',
        ], [
            'beginning_cash.required' => 'Please enter the beginning cash amount.',
            'beginning_cash.numeric' => 'The beginning cash must be a valid number.',
            'beginning_cash.min' => 'The beginning cash cannot be negative.',
        ]);

        if ($this->current_session) {
            $this->current_session->update([
                'opening_cash' => $this->beginning_cash,
            ]);
        }

        $this->dialog()->success('Success', 'Beginning cash saved successfully.');

        return redirect()->route('frontdesk.room-monitoring');
    }

    public function render()
    {
        return view('livewire.frontdesk.beginning-cash');
    }
}
