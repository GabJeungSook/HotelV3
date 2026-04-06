<?php

namespace App\Services;

use App\Models\ShiftSession;
use App\Models\ShiftSnapshot;
use App\Models\ShiftForwardedGuest;
use App\Models\Transaction;
use App\Models\CheckinDetail;
use App\Models\Expense;
use App\Models\Remittance;

class ShiftSnapshotService
{
    public function createSnapshot(ShiftSession $session): ShiftSnapshot
    {
        $session->load('members.user');

        $timeIn = $session->opened_at;
        $timeOut = $session->closed_at;
        $branchId = $session->branch_id;

        // All transactions in this session
        $txns = Transaction::where('shift_session_id', $session->id)->get();

        // Forwarded guests (populated at shift open)
        $forwarded = ShiftForwardedGuest::where('shift_session_id', $session->id)->get();

        // Check-ins during this session (type 1 transactions)
        $checkinTxns = $txns->where('transaction_type_id', 1);
        $currentCheckinIds = $checkinTxns->pluck('checkin_detail_id')->unique()->filter();

        // Checkouts during this session
        $checkouts = CheckinDetail::where('is_check_out', true)
            ->whereBetween('check_out_at', [$timeIn, $timeOut])
            ->whereHas('room', fn ($q) => $q->where('branch_id', $branchId))
            ->get();

        // --- OPERATIONS A: Sales by type ---
        $checkinCount = $checkinTxns->count();
        $checkinAmount = (float) $checkinTxns->sum('payable_amount');

        $extensionTxns = $txns->where('transaction_type_id', 6);
        $transferTxns = $txns->where('transaction_type_id', 7);
        $damageTxns = $txns->where('transaction_type_id', 4);
        $amenityTxns = $txns->where('transaction_type_id', 8);
        $foodTxns = $txns->where('transaction_type_id', 9);

        // Unclaimed deposits: forwarded guests who checked out this shift with remaining guest deposit
        $forwardedCheckinIds = $forwarded->pluck('checkin_detail_id')->toArray();
        $checkedOutForwarded = $checkouts->whereIn('id', $forwardedCheckinIds);
        $unclaimedCount = 0;
        $unclaimedAmount = 0;
        foreach ($checkedOutForwarded as $cd) {
            if ($cd->deposit_balance > 0) {
                $unclaimedCount++;
                $unclaimedAmount += $cd->deposit_balance;
            }
        }

        // --- OPERATIONS B: Room summary ---
        $forwardedRoomCount = $forwarded->count();
        $forwardedRoomAmount = (float) $forwarded->sum('room_charge_amount');
        $currentRoomCount = $checkinCount;
        $initialDeposit = $session->branch->initial_deposit ?? 200;
        $currentRoomAmount = $currentRoomCount * $initialDeposit;

        // --- DEPOSITS ---
        $roomDepositCollected = (float) $txns->where('transaction_type_id', 2)
            ->where('deposit_type', 'room_key')->sum('payable_amount');
        $guestDepositCollected = (float) $txns->where('transaction_type_id', 2)
            ->where('deposit_type', 'guest')->sum('payable_amount');
        $cashoutAmount = (float) $txns->where('transaction_type_id', 5)->sum('payable_amount');

        // Forwarded deposits (from shift_forwarded_guests snapshot)
        $fwdRoomDepositCount = $forwarded->where('room_deposit_amount', '>', 0)->count();
        $fwdRoomDepositAmount = (float) $forwarded->sum('room_deposit_amount');
        $fwdGuestDepositCount = $forwarded->where('guest_deposit_balance', '>', 0)->count();
        $fwdGuestDepositAmount = (float) $forwarded->sum('guest_deposit_balance');

        // --- CHECKOUT ---
        $checkoutCount = $checkouts->count();
        $checkoutRoomDeposit = $checkoutCount * $initialDeposit;

        // --- REMAINING (forwarded out to next shift) ---
        $remainingRoomDepositCount = max(0, $currentRoomCount + $forwardedRoomCount - $checkoutCount);
        $remainingRoomDepositAmount = $remainingRoomDepositCount * $initialDeposit;

        // Remaining guest deposits: sum deposit_balance from all still-occupying guests
        $allSessionCheckinIds = $currentCheckinIds->merge($forwarded->pluck('checkin_detail_id'))->unique();
        $checkedOutIds = $checkouts->pluck('id')->toArray();
        $stillOccupyingIds = $allSessionCheckinIds->diff($checkedOutIds);
        $remainingGuestDepositCount = 0;
        $remainingGuestDepositAmount = 0;
        if ($stillOccupyingIds->isNotEmpty()) {
            $stillOccupying = CheckinDetail::whereIn('id', $stillOccupyingIds)
                ->where('deposit_balance', '>', 0)->get();
            $remainingGuestDepositCount = $stillOccupying->count();
            $remainingGuestDepositAmount = (float) $stillOccupying->sum('deposit_balance');
        }

        // --- FINAL SALES ---
        $grossSales = (float) $txns->whereNotIn('transaction_type_id', [2, 5])->sum('payable_amount')
                    + $unclaimedAmount;
        $expensesAmount = (float) Expense::where('shift_session_id', $session->id)->sum('amount');
        $netSales = $grossSales - $expensesAmount;

        // --- CASH RECONCILIATION ---
        $remittanceAmount = (float) Remittance::where('shift_session_id', $session->id)->sum('total_remittance');

        // Get previous snapshot for forwarded balance
        $prevSnapshot = ShiftSnapshot::whereHas('shiftSession', function ($q) use ($session) {
            $q->where('branch_id', $session->branch_id)
              ->where('cash_drawer_id', $session->cash_drawer_id)
              ->where('status', 'closed')
              ->where('closed_at', '<', $session->opened_at);
        })->orderByDesc('shift_closed_at')->first();

        $forwardedBalance = $prevSnapshot ? (float) $prevSnapshot->net_sales : 0;

        $depositSubtotal = $remainingRoomDepositAmount + $remainingGuestDepositAmount;
        $expectedCash = ($prevSnapshot?->net_sales ?? 0)
                      + $netSales
                      + $depositSubtotal
                      + $forwardedBalance
                      - $remittanceAmount;
        $actualCash = (float) $session->closing_cash;
        $cashDifference = $expectedCash - $actualCash;

        // --- BIGBOSS FLOOR SUMMARY ---
        $floorSummary = [];
        $floorTxns = $txns->whereNotIn('transaction_type_id', [2, 5]);
        foreach ($floorTxns->groupBy('floor_id') as $floorId => $floorGroup) {
            $floorSummary[$floorId] = [
                'room' => (float) $floorGroup->where('transaction_type_id', 1)->sum('payable_amount'),
                'transfer' => (float) $floorGroup->where('transaction_type_id', 7)->sum('payable_amount'),
                'extend' => (float) $floorGroup->where('transaction_type_id', 6)->sum('payable_amount'),
                'food' => (float) $floorGroup->where('transaction_type_id', 9)->sum('payable_amount'),
                'misc' => (float) $floorGroup->whereIn('transaction_type_id', [4, 8])->sum('payable_amount'),
            ];
        }

        // --- CREATE SNAPSHOT ---
        return ShiftSnapshot::create([
            'shift_session_id' => $session->id,
            'branch_id' => $branchId,
            // Header
            'frontdesk_names' => $session->members->pluck('user.name')->join(' & '),
            'shift_opened_at' => $timeIn,
            'shift_closed_at' => $timeOut,
            // Cash Drawer
            'opening_cash' => $session->opening_cash,
            'closing_cash' => $session->closing_cash,
            // Operations A
            'checkin_count' => $checkinCount,
            'checkin_amount' => $checkinAmount,
            'extension_count' => $extensionTxns->count(),
            'extension_amount' => (float) $extensionTxns->sum('payable_amount'),
            'transfer_count' => $transferTxns->count(),
            'transfer_amount' => (float) $transferTxns->sum('payable_amount'),
            'damage_count' => $damageTxns->count(),
            'damage_amount' => (float) $damageTxns->sum('payable_amount'),
            'amenity_count' => $amenityTxns->count(),
            'amenity_amount' => (float) $amenityTxns->sum('payable_amount'),
            'food_count' => $foodTxns->count(),
            'food_amount' => (float) $foodTxns->sum('payable_amount'),
            'unclaimed_count' => $unclaimedCount,
            'unclaimed_amount' => $unclaimedAmount,
            // Operations B
            'forwarded_room_count' => $forwardedRoomCount,
            'forwarded_room_amount' => $forwardedRoomAmount,
            'current_room_count' => $currentRoomCount,
            'current_room_amount' => $currentRoomAmount,
            // Deposits
            'room_deposit_collected' => $roomDepositCollected,
            'guest_deposit_collected' => $guestDepositCollected,
            'cashout_amount' => $cashoutAmount,
            'fwd_room_deposit_count' => $fwdRoomDepositCount,
            'fwd_room_deposit_amount' => $fwdRoomDepositAmount,
            'fwd_guest_deposit_count' => $fwdGuestDepositCount,
            'fwd_guest_deposit_amount' => $fwdGuestDepositAmount,
            // Checkout
            'checkout_count' => $checkoutCount,
            'checkout_room_deposit' => $checkoutRoomDeposit,
            // Forwarded out
            'remaining_room_deposit_count' => $remainingRoomDepositCount,
            'remaining_room_deposit_amount' => $remainingRoomDepositAmount,
            'remaining_guest_deposit_count' => $remainingGuestDepositCount,
            'remaining_guest_deposit_amount' => $remainingGuestDepositAmount,
            // Final Sales
            'gross_sales' => $grossSales,
            'expenses_amount' => $expensesAmount,
            'net_sales' => $netSales,
            // Cash Reconciliation
            'forwarded_balance' => $forwardedBalance,
            'remittance_amount' => $remittanceAmount,
            'expected_cash' => $expectedCash,
            'actual_cash' => $actualCash,
            'cash_difference' => $cashDifference,
            // BigBoss
            'floor_summary' => $floorSummary,
        ]);
    }
}
