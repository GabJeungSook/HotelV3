<?php

namespace App\Livewire\Frontdesk;

use App\Models\Expense;
use App\Models\Remittance;
use App\Models\ShiftSession;
use App\Models\ShiftMember;
use App\Models\CashDrawer;
use App\Models\Transaction;
use App\Services\ShiftSnapshotService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use WireUi\Traits\WireUiActions;

class CashOnHand extends Component
{
    use WireUiActions;

    public $total_transactions = 0;
    public $total_expenses = 0;
    public $total_remittances = 0;
    public $total_deposits = 0;
    public $logout_modal = false;
    public $authorization_modal = false;
    public $remittance;
    public $description;
    public $code;
    public $current_session;

    public function mount()
    {
        $user = auth()->user();

        // Find the current open shift session for this user's drawer
        $this->current_session = ShiftSession::where('branch_id', $user->branch_id)
            ->where('cash_drawer_id', $user->cash_drawer_id)
            ->where('status', 'open')
            ->first();

        if (!$this->current_session) {
            return;
        }

        $sessionId = $this->current_session->id;

        // Total sales transactions (exclude deposits type 2 and cashouts type 5)
        $this->total_transactions = (float) Transaction::where('shift_session_id', $sessionId)
            ->whereNotIn('transaction_type_id', [2, 5])
            ->sum('payable_amount');

        // Total expenses for this session
        $this->total_expenses = (float) Expense::where('shift_session_id', $sessionId)
            ->sum('amount');

        // Total remittances for this session
        $this->total_remittances = (float) Remittance::where('shift_session_id', $sessionId)
            ->sum('total_remittance');

        // Total deposits (type 2) minus cashouts (type 5) for this session
        $deposits = (float) Transaction::where('shift_session_id', $sessionId)
            ->where('transaction_type_id', 2)
            ->sum('payable_amount');
        $cashouts = (float) Transaction::where('shift_session_id', $sessionId)
            ->where('transaction_type_id', 5)
            ->sum('payable_amount');
        $this->total_deposits = $deposits - $cashouts;
    }

    public function confirmLogout()
    {
        $this->validate([
            'remittance' => 'required|numeric|min:0',
        ], [
            'remittance.required' => 'Please enter the ending cash amount.',
            'remittance.numeric' => 'The ending cash must be a valid number.',
            'remittance.min' => 'The ending cash cannot be negative.',
        ]);

        $this->logout_modal = true;
    }

    public function enterPasscode()
    {
        $this->logout_modal = false;
        $this->authorization_modal = true;
    }

    public function endShiftConfirm()
    {
        $this->validate([
            'code' => 'required|numeric',
        ], [
            'code.required' => 'Please enter your passcode.',
            'code.numeric' => 'The passcode must be a number.',
        ]);

        if (auth()->user()->frontdesk->passcode !== $this->code) {
            $this->dialog()->error('Unauthorized', 'The passcode you entered is incorrect. Please try again.');
            return;
        }

        DB::beginTransaction();

        try {
            $session = ShiftSession::where('branch_id', auth()->user()->branch_id)
                ->where('cash_drawer_id', auth()->user()->cash_drawer_id)
                ->where('status', 'open')
                ->lockForUpdate()
                ->first();

            if (!$session) {
                DB::rollBack();
                $this->dialog()->error('Error', 'No open shift session found.');
                return;
            }

            // Set closing cash and close the session
            $session->update([
                'closing_cash' => $this->remittance,
                'closed_at' => now(),
                'status' => 'closed',
            ]);

            // Calculate and write the shift snapshot
            $snapshotService = new ShiftSnapshotService();
            $snapshotService->createSnapshot($session);

            // Close all shift members
            ShiftMember::where('shift_session_id', $session->id)
                ->whereNull('left_at')
                ->update(['left_at' => now()]);

            // Deactivate cash drawer
            CashDrawer::where('id', $session->cash_drawer_id)->update(['is_active' => false]);

            // Clear cash_drawer_id for all members of this session
            $memberUserIds = ShiftMember::where('shift_session_id', $session->id)
                ->pluck('user_id');
            \App\Models\User::whereIn('id', $memberUserIds)->update(['cash_drawer_id' => null]);

            DB::commit();

            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('login');

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.frontdesk.cash-on-hand');
    }
}
