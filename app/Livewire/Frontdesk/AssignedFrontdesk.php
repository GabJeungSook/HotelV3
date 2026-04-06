<?php

namespace App\Livewire\Frontdesk;

use Livewire\Component;
use App\Models\Frontdesk;
use App\Models\ShiftSession;
use App\Models\ShiftMember;
use App\Models\ShiftForwardedGuest;
use App\Models\CashDrawer;
use App\Models\CheckinDetail;
use WireUi\Traits\WireUiActions;
use Illuminate\Support\Facades\DB;

class AssignedFrontdesk extends Component
{
    use WireUiActions;

    public $drawer;
    public $shift;
    public $cash_drawers = [];
    public $active_drawers = [];

    public function mount()
    {
        $currentHour = now()->hour;
        // AM shift: 8:00AM - 7:59PM, PM shift: 8:00PM - 7:59AM
        $this->shift = ($currentHour >= 8 && $currentHour < 20) ? 'AM' : 'PM';

        $branchId = auth()->user()->branch_id;

        // Get all drawers for this branch
        $allDrawers = CashDrawer::where('branch_id', $branchId)->get();
        $this->cash_drawers = $allDrawers->where('is_active', false)->values();

        // Get active drawers with open sessions (for joining as partner)
        $this->active_drawers = $allDrawers->where('is_active', true)->values()->map(function ($drawer) use ($branchId) {
            $session = ShiftSession::where('branch_id', $branchId)
                ->where('cash_drawer_id', $drawer->id)
                ->where('status', 'open')
                ->with('members.user')
                ->first();
            $drawer->open_session = $session;
            $drawer->member_names = $session?->members->pluck('user.name')->join(' & ') ?? '';
            return $drawer;
        });
    }

    public function render()
    {
        return view('livewire.frontdesk.assigned-frontdesk');
    }

    public function saveFrontdesk()
    {
        if (!$this->drawer) {
            $this->dialog()->error('Oops!', 'Please select a cash drawer to proceed.');
            return;
        }

        DB::beginTransaction();

        try {
            $branchId = auth()->user()->branch_id;
            $userId = auth()->id();

            // Auto-close stale sessions open >14 hours
            ShiftSession::where('status', 'open')
                ->where('opened_at', '<', now()->subHours(14))
                ->update(['status' => 'closed', 'closed_at' => DB::raw('DATE_ADD(opened_at, INTERVAL 14 HOUR)')]);

            // Check if user already has an active shift membership — close it
            $existingMembership = ShiftMember::whereNull('left_at')
                ->where('user_id', $userId)
                ->first();
            if ($existingMembership) {
                $existingMembership->update(['left_at' => now()]);
            }

            // Check for open session on this drawer
            $openSession = ShiftSession::where('branch_id', $branchId)
                ->where('cash_drawer_id', $this->drawer)
                ->where('status', 'open')
                ->lockForUpdate()
                ->first();

            if ($openSession) {
                // JOIN existing session as partner
                ShiftMember::create([
                    'shift_session_id' => $openSession->id,
                    'user_id' => $userId,
                    'role' => 'partner',
                    'joined_at' => now(),
                ]);

                auth()->user()->update([
                    'cash_drawer_id' => $this->drawer,
                    'shift' => $openSession->shift_type,
                    'time_in' => now(),
                ]);

                DB::commit();

                $this->dialog()->success('Success', 'Joined shift session as partner.');
                return redirect()->route('frontdesk.room-monitoring');
            }

            // CREATE new session as primary
            $shiftDate = $this->getShiftDate();

            $session = ShiftSession::create([
                'branch_id' => $branchId,
                'cash_drawer_id' => $this->drawer,
                'shift_type' => $this->shift,
                'shift_date' => $shiftDate,
                'status' => 'open',
                'opened_at' => now(),
            ]);

            ShiftMember::create([
                'shift_session_id' => $session->id,
                'user_id' => $userId,
                'role' => 'primary',
                'joined_at' => now(),
            ]);

            // Populate forwarded guests
            $this->populateForwardedGuests($session);

            auth()->user()->update([
                'cash_drawer_id' => $this->drawer,
                'shift' => $this->shift,
                'time_in' => now(),
            ]);

            CashDrawer::where('id', $this->drawer)->update(['is_active' => true]);

            DB::commit();

            $this->dialog()->success('Success', 'Cash drawer selected successfully.');
            return redirect()->route('frontdesk.beginning-cash');

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function getShiftDate(): string
    {
        $hour = now()->hour;
        // PM shift after midnight (0-7) belongs to previous day's PM shift
        if ($this->shift === 'PM' && $hour < 8) {
            return now()->subDay()->toDateString();
        }
        return now()->toDateString();
    }

    private function populateForwardedGuests(ShiftSession $session): void
    {
        $forwardedCheckins = CheckinDetail::where('is_check_out', false)
            ->where('check_in_at', '<', $session->opened_at)
            ->whereHas('room', fn ($q) => $q->where('branch_id', $session->branch_id))
            ->with('room')
            ->get();

        foreach ($forwardedCheckins as $cd) {
            $roomCharge = $cd->transactions()->where('transaction_type_id', 1)->sum('payable_amount');

            ShiftForwardedGuest::create([
                'shift_session_id' => $session->id,
                'checkin_detail_id' => $cd->id,
                'room_id' => $cd->room_id,
                'room_charge_amount' => $roomCharge,
                'room_deposit_amount' => $cd->room_deposit_balance,
                'guest_deposit_balance' => $cd->deposit_balance,
            ]);
        }
    }
}
