<?php

namespace App\Livewire\BackOffice;

use Livewire\Component;
use App\Models\Frontdesk;
use App\Models\CheckinDetail;
use App\Models\Transaction;
use App\Models\Floor;
use App\Models\Expense;
use App\Models\Remittance;
use App\Models\ShiftSession;
use App\Models\ShiftSnapshot;
use App\Models\ShiftForwardedGuest;
use Carbon\Carbon;

/**
 * Sales Report V2 - Occupancy-Based (ShiftSession + ShiftSnapshot)
 *
 * For shift mode: Reads pre-calculated values from ShiftSnapshot.
 * For date_range mode: Queries transactions using shift_session_id and deposit_type.
 */
class SalesReportV2 extends Component
{
    // Filter mode: 'date_range' or 'shift'
    public string $filterMode = 'shift';

    // Date range mode properties
    public $date_from;
    public $date_to;
    public $frontdesk;
    public $frontdesk_name;

    // Shift mode properties (name kept for view compatibility — stores shift_session_id)
    public $selectedShiftLogId;
    public array $availableShiftSessions = [];

    public array $salesRows = [];
    public float $totalSales = 0;
    public array $summaryByType = [];
    public array $roomSummary = [];

    public $expensesRows;
    public float $expensesTotal = 0;
    public $remittanceRows;
    public float $remittanceTotal = 0;
    public float $netSales = 0;
    public int $forwardedCount = 0;
    public int $unclaimedCount = 0;

    // Forwarded totals
    public float $forwardedRoom = 0;
    public float $forwardedRoomDeposit = 0;
    public float $forwardedGuestDeposit = 0;

    public float $unclaimedDepositTotal = 0;

    // Checkout and cashout totals
    public float $totalCashouts = 0;
    public float $checkoutRoomAmount = 0;
    public float $checkoutRoomDeposit = 0;
    public float $remainingRoomDeposit = 0;

    // Card modal
    public bool $showCardModal = false;
    public string $cardModalTitle = '';
    public array $cardModalRows = [];
    public float $cardModalTotal = 0;

    // Shift counts (for shift mode display)
    public int $shiftCheckins = 0;
    public int $shiftCheckouts = 0;

    public function mount()
    {
        $this->date_from = now()->toDateString();
        $this->date_to = now()->toDateString();
        $this->salesRows = [];
        $this->totalSales = 0;
        $this->summaryByType = [];
        $this->expensesRows = collect();
        $this->expensesTotal = 0;
        $this->roomSummary = [];
        $this->forwardedRoom = 0;
        $this->forwardedRoomDeposit = 0;
        $this->forwardedGuestDeposit = 0;

        $this->loadAvailableShiftSessions();
        if (!empty($this->availableShiftSessions)) {
            $this->selectedShiftLogId = end($this->availableShiftSessions)['id'];
        }
        $this->generateReport();
    }

    public function render()
    {
        return view('livewire.back-office.sales-report-v2', [
            'frontdesks' => Frontdesk::where('branch_id', auth()->user()->branch_id)->get(),
        ]);
    }

    /**
     * Load all closed shift sessions for this branch.
     */
    private function loadAvailableShiftSessions(): void
    {
        $sessions = ShiftSession::where('branch_id', auth()->user()->branch_id)
            ->where('status', 'closed')
            ->with('members.user')
            ->orderBy('opened_at')
            ->get();

        $this->availableShiftSessions = $sessions->map(function ($session) {
            $memberNames = $session->members->pluck('user.name')->filter()->unique()->implode(', ') ?: 'Unknown';

            return [
                'id' => $session->id,
                'label' => $session->shift_type . ' ' . $session->opened_at->format('M j')
                         . ' - ' . $memberNames
                         . ' (' . $session->opened_at->format('g:i A') . ' - ' . ($session->closed_at?->format('g:i A') ?? '—') . ')',
                'frontdesks' => $memberNames,
                'time_in' => $session->opened_at->toIso8601String(),
                'time_out' => $session->closed_at?->toIso8601String() ?? $session->opened_at->toIso8601String(),
                'time_in_formatted' => $session->opened_at->format('F d, Y g:i A'),
                'time_out_formatted' => $session->closed_at?->format('F d, Y g:i A') ?? '—',
            ];
        })->values()->toArray();
    }

    /**
     * Determine shift type from time_in hour.
     * AM: 6:00 AM - 7:59 PM (hours 6-19)
     * PM: 8:00 PM - 5:59 AM (hours 20-23, 0-5)
     */
    public function getShiftType(Carbon $timeIn): string
    {
        $hour = $timeIn->hour;
        return ($hour >= 6 && $hour < 20) ? 'AM' : 'PM';
    }

    /**
     * When filter mode changes, load shift sessions if needed.
     */
    public function updatedFilterMode()
    {
        if ($this->filterMode === 'shift' && empty($this->availableShiftSessions)) {
            $this->loadAvailableShiftSessions();
        }
    }

    /**
     * Kept for view compatibility (wire:model.live="selectedShiftLogId").
     */
    public function updatedSelectedShiftLogId()
    {
        // No-op; report generates on Apply button click.
    }

    public function generateReport()
    {
        $frontdesk = Frontdesk::find($this->frontdesk);
        $this->frontdesk_name = $frontdesk?->name;

        if ($this->filterMode === 'shift' && $this->selectedShiftLogId) {
            $this->generateShiftReport();
        } else {
            $this->generateDateRangeReport();
        }
    }

    /**
     * Generate report from ShiftSnapshot for a closed shift session.
     */
    private function generateShiftReport(): void
    {
        $session = ShiftSession::with(['snapshot', 'members.user'])->find($this->selectedShiftLogId);
        if (!$session) {
            return;
        }

        $snapshot = $session->snapshot;

        // Build sales rows from transactions in this shift session
        $this->salesRows = $this->buildShiftSalesRows($session);

        // Build forwarded guest rows from ShiftForwardedGuest
        $forwardedRows = $this->buildForwardedGuestRowsFromSession($session);
        $this->salesRows = collect($this->salesRows)
            ->merge($forwardedRows)
            ->sortBy('room_number')
            ->values()
            ->toArray();

        // Populate summary from snapshot if available
        if ($snapshot) {
            $this->summaryByType = [
                'room_charges' => (float) $snapshot->checkin_amount,
                'extensions' => (float) $snapshot->extension_amount,
                'amenities' => (float) $snapshot->amenity_amount,
                'food' => (float) $snapshot->food_amount,
                'damages' => (float) $snapshot->damage_amount,
                'transfers' => (float) $snapshot->transfer_amount,
                'deposits' => (float) ($snapshot->room_deposit_collected + $snapshot->guest_deposit_collected),
                'room_deposits' => (float) $snapshot->room_deposit_collected,
                'guest_deposits' => (float) $snapshot->guest_deposit_collected,
                'cashouts' => (float) $snapshot->cashout_amount,
                'grand_total' => (float) $snapshot->gross_sales,
            ];

            $this->totalSales = (float) $snapshot->gross_sales;
            $this->forwardedCount = (int) $snapshot->forwarded_room_count;
            $this->forwardedRoom = (float) $snapshot->forwarded_room_amount;
            $this->forwardedRoomDeposit = (float) $snapshot->fwd_room_deposit_amount;
            $this->forwardedGuestDeposit = (float) $snapshot->fwd_guest_deposit_amount;
            $this->shiftCheckins = (int) $snapshot->checkin_count;
            $this->shiftCheckouts = (int) $snapshot->checkout_count;
            $this->totalCashouts = (float) $snapshot->cashout_amount;
            $this->checkoutRoomAmount = (float) ($snapshot->checkin_amount > 0
                ? $this->calculateCheckoutRoomAmountFromSession($session)
                : 0);
            $this->checkoutRoomDeposit = (float) $snapshot->checkout_room_deposit;
            $this->remainingRoomDeposit = (float) $snapshot->remaining_room_deposit_amount;
            $this->unclaimedCount = (int) $snapshot->unclaimed_count;
            $this->unclaimedDepositTotal = (float) $snapshot->unclaimed_amount;
            $this->expensesTotal = (float) $snapshot->expenses_amount;
            $this->remittanceTotal = (float) $snapshot->remittance_amount;
            $this->netSales = (float) $snapshot->net_sales;
        } else {
            // Fallback: calculate from rows if no snapshot
            $this->totalSales = collect($this->salesRows)->sum('total');
            $this->summaryByType = $this->buildSummaryByType();
            $this->forwardedCount = collect($this->salesRows)
                ->filter(fn($row) => ($row['is_forwarded_guest_row'] ?? false)
                    && $row['transaction_type'] === 'FWD ROOM'
                    && !str_contains($row['remarks'] ?? '', 'Unclaimed'))
                ->count();
            $this->calculateShiftCountsFromSession($session);
            $this->calculateForwardedTotals();
            $this->totalCashouts = (float) ($this->summaryByType['cashouts'] ?? 0);
            $this->calculateCheckoutTotalsFromSession($session);
            $unclaimedRows = collect($this->salesRows)
                ->filter(fn($row) => ($row['is_forwarded_guest_row'] ?? false) && str_contains($row['remarks'] ?? '', 'Unclaimed'));
            $this->unclaimedCount = $unclaimedRows->count();
            $this->unclaimedDepositTotal = (float) $unclaimedRows->sum('amount');
            $this->netSales = $this->totalSales - $this->expensesTotal;
        }

        // Always build expenses/remittance rows from DB (for the detail tables)
        $this->buildExpensesSummaryFromSession($session);
        $this->buildRemittanceSummaryFromSession($session);

        // If snapshot provided totals, override the row-based sums
        if ($snapshot) {
            $this->expensesTotal = (float) $snapshot->expenses_amount;
            $this->remittanceTotal = (float) $snapshot->remittance_amount;
            $this->netSales = (float) $snapshot->net_sales;
        }

        $this->buildRoomSummaryFromSnapshot($session, $snapshot);
    }

    /**
     * Generate report for date_range mode (occupancy-based query approach).
     */
    private function generateDateRangeReport(): void
    {
        $this->salesRows = $this->buildDateRangeSalesRows();
        $this->totalSales = collect($this->salesRows)->sum('total');
        $this->summaryByType = $this->buildSummaryByType();
        $this->buildExpensesSummary();
        $this->buildRemittanceSummary();
        $this->buildRoomSummary();

        $this->forwardedCount = collect($this->salesRows)
            ->filter(fn($row) => ($row['is_forwarded_guest_row'] ?? false)
                && $row['transaction_type'] === 'FWD ROOM'
                && !str_contains($row['remarks'] ?? '', 'Unclaimed'))
            ->count();

        $this->calculateShiftCountsDateRange();
        $this->calculateForwardedTotals();
        $this->netSales = $this->totalSales - $this->expensesTotal;

        $unclaimedRows = collect($this->salesRows)
            ->filter(fn($row) => ($row['is_forwarded_guest_row'] ?? false) && str_contains($row['remarks'] ?? '', 'Unclaimed'));
        $this->unclaimedCount = $unclaimedRows->count();
        $this->unclaimedDepositTotal = (float) $unclaimedRows->sum('amount');

        $this->totalCashouts = (float) ($this->summaryByType['cashouts'] ?? 0);
        $this->calculateCheckoutTotalsDateRange();
    }

    // ─── Shift Mode: Sales Rows ──────────────────────────────────────

    /**
     * Build sales rows from transactions linked to a shift session.
     */
    private function buildShiftSalesRows(ShiftSession $session): array
    {
        $transactions = Transaction::where('shift_session_id', $session->id)
            ->with(['checkin_details.guest', 'checkin_details.type', 'room.type', 'processedBy', 'transaction_type'])
            ->get();

        return $transactions->map(function ($txn) use ($session) {
            $cd = $txn->checkin_details;
            $total = in_array($txn->transaction_type_id, [2, 5]) ? 0 : (float) $txn->payable_amount;

            // Determine if this guest is forwarded (checked in before this shift)
            $isForwarded = false;
            if ($cd && $cd->check_in_at) {
                $isForwarded = Carbon::parse($cd->check_in_at) < $session->opened_at;
            }

            // Display type for deposits using deposit_type column
            $displayType = $txn->transaction_type?->name ?? '—';
            if ($txn->transaction_type_id == 2) {
                $displayType = $txn->deposit_type === 'room_key' ? 'Room Deposit' : 'Guest Deposit';
            }

            $checkInAt = $cd?->check_in_at ? Carbon::parse($cd->check_in_at) : null;
            $checkOutAt = $cd?->check_out_at ? Carbon::parse($cd->check_out_at) : null;

            return [
                'checkin_detail_id' => $txn->checkin_detail_id,
                'room_number' => $txn->room?->number ?? '—',
                'room_id' => $txn->room_id,
                'room_type' => $txn->room?->type?->name ?? '—',
                'guest_name' => strtoupper($cd?->guest?->name ?? '—'),
                'transaction_type' => $displayType,
                'transaction_type_id' => $txn->transaction_type_id,
                'check_in' => $checkInAt?->format('m-d-Y h:iA') ?? '—',
                'check_out' => $checkOutAt?->format('m-d-Y h:iA') ?? '—',
                'hours_stayed' => $cd?->hours_stayed ? $cd->hours_stayed . ' hrs' : '—',
                'amount' => (float) $txn->payable_amount,
                'remarks' => $txn->remarks,
                'processed_by' => strtoupper($txn->processedBy?->name ?? '—'),
                'shift' => strtoupper($session->shift_type ?? '—'),
                'transaction_date' => $txn->created_at
                    ? Carbon::parse($txn->created_at)->format('m-d-Y h:iA')
                    : '—',
                'total' => $total,
                'is_forwarded' => $isForwarded,
                'is_forwarded_guest_row' => false,
            ];
        })->toArray();
    }

    /**
     * Build forwarded guest rows from ShiftForwardedGuest records.
     */
    private function buildForwardedGuestRowsFromSession(ShiftSession $session): array
    {
        $forwardedGuests = ShiftForwardedGuest::where('shift_session_id', $session->id)
            ->with(['checkinDetail.guest', 'checkinDetail.room.type', 'room.type'])
            ->get();

        $rows = [];
        foreach ($forwardedGuests as $fwd) {
            $cd = $fwd->checkinDetail;
            $room = $fwd->room ?? $cd?->room;
            $checkInAt = $cd?->check_in_at ? Carbon::parse($cd->check_in_at) : null;
            $checkOutAt = $cd?->check_out_at ? Carbon::parse($cd->check_out_at) : null;
            $guestName = strtoupper($cd?->guest?->name ?? '—');
            $roomNumber = $room?->number ?? '—';
            $roomType = $room?->type?->name ?? '—';
            $hoursStayed = $cd?->hours_stayed ? $cd->hours_stayed . ' hrs' : '—';
            $isUnclaimed = $cd && $cd->check_out_at && Carbon::parse($cd->check_out_at) <= $session->opened_at;

            $baseRow = [
                'checkin_detail_id' => $fwd->checkin_detail_id,
                'room_number' => $roomNumber,
                'room_id' => $fwd->room_id,
                'room_type' => $roomType,
                'guest_name' => $guestName,
                'transaction_type_id' => 0,
                'check_in' => $checkInAt?->format('m-d-Y h:iA') ?? '—',
                'check_out' => $checkOutAt?->format('m-d-Y h:iA') ?? '—',
                'hours_stayed' => $hoursStayed,
                'processed_by' => '—',
                'shift' => '—',
                'transaction_date' => '—',
                'total' => 0,
                'is_forwarded' => true,
                'is_forwarded_guest_row' => true,
            ];

            // FWD ROOM row
            if ((float) $fwd->room_charge_amount > 0) {
                $rows[] = array_merge($baseRow, [
                    'transaction_type' => 'FWD ROOM',
                    'amount' => (float) $fwd->room_charge_amount,
                    'remarks' => $isUnclaimed ? 'Unclaimed room charge from checked-out guest' : 'Room charge from previous shift',
                ]);
            }

            // FWD ROOM DEPOSIT row
            if ((float) $fwd->room_deposit_amount > 0) {
                $rows[] = array_merge($baseRow, [
                    'transaction_type' => 'FWD ROOM DEPOSIT',
                    'amount' => (float) $fwd->room_deposit_amount,
                    'remarks' => $isUnclaimed ? 'Unclaimed room deposit from checked-out guest' : 'Room key deposit from previous shift',
                ]);
            }

            // FWD GUEST DEPOSIT row
            if ((float) $fwd->guest_deposit_balance > 0) {
                $rows[] = array_merge($baseRow, [
                    'transaction_type' => 'FWD GUEST DEPOSIT',
                    'amount' => (float) $fwd->guest_deposit_balance,
                    'remarks' => $isUnclaimed ? 'Unclaimed guest deposit from checked-out guest' : 'Guest deposit from previous shift',
                ]);
            }
        }

        return $rows;
    }

    // ─── Shift Mode: Expenses & Remittance ───────────────────────────

    private function buildExpensesSummaryFromSession(ShiftSession $session): void
    {
        $rows = Expense::query()
            ->with(['expenseCategory', 'user'])
            ->where('shift_session_id', $session->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($e) {
                return [
                    'expense_type' => $e->expenseCategory?->name ?? '—',
                    'description' => $e->description ?? '—',
                    'shift' => $e->shift ?? '—',
                    'amount' => (float) $e->amount,
                    'frontdesk' => strtoupper($e->user?->name ?? '—'),
                ];
            });

        $this->expensesRows = $rows;
        $this->expensesTotal = (float) $rows->sum('amount');
    }

    private function buildRemittanceSummaryFromSession(ShiftSession $session): void
    {
        $rows = Remittance::query()
            ->with('user')
            ->where('shift_session_id', $session->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($r) {
                return [
                    'description' => $r->description ?? '—',
                    'amount' => (float) $r->total_remittance,
                    'frontdesk' => strtoupper($r->user?->name ?? '—'),
                ];
            });

        $this->remittanceRows = $rows;
        $this->remittanceTotal = (float) $rows->sum('amount');
    }

    // ─── Shift Mode: Room Summary ────────────────────────────────────

    private function buildRoomSummaryFromSnapshot(ShiftSession $session, ?ShiftSnapshot $snapshot): void
    {
        if ($snapshot && $snapshot->floor_summary) {
            // Use pre-calculated floor summary from snapshot
            $floors = Floor::where('branch_id', auth()->user()->branch_id)
                ->orderBy('number')
                ->get();

            $floorSummary = $snapshot->floor_summary;
            $tableRows = [];
            $totalsPerFloor = [];

            foreach ($floors as $floor) {
                $totalsPerFloor[$floor->id] = 0;
            }

            foreach ($floorSummary as $entry) {
                $rowPerFloor = [];
                $rowTotal = 0;
                foreach ($floors as $floor) {
                    $amount = (float) ($entry['floors'][$floor->id] ?? 0);
                    $rowPerFloor[$floor->id] = $amount;
                    $rowTotal += $amount;
                    $totalsPerFloor[$floor->id] += $amount;
                }
                $tableRows[] = [
                    'description' => $entry['description'] ?? '—',
                    'floors' => $rowPerFloor,
                    'row_total' => $rowTotal,
                ];
            }

            $this->roomSummary = [
                'floors' => $floors->map(fn($f) => ['id' => $f->id, 'number' => $f->number])->all(),
                'rows' => $tableRows,
                'totals' => $totalsPerFloor,
            ];
        } else {
            // Fallback: build from transactions
            $this->buildRoomSummaryFromTransactions($session);
        }
    }

    private function buildRoomSummaryFromTransactions(ShiftSession $session): void
    {
        $floors = Floor::where('branch_id', auth()->user()->branch_id)
            ->orderBy('number')
            ->get();

        $transactions = Transaction::where('shift_session_id', $session->id)
            ->with(['room.floor', 'transaction_type'])
            ->whereNotIn('transaction_type_id', [2, 5])
            ->get();

        if ($transactions->isEmpty()) {
            $this->roomSummary = [
                'floors' => $floors->map(fn($f) => ['id' => $f->id, 'number' => $f->number])->all(),
                'rows' => [],
                'totals' => [],
            ];
            return;
        }

        $grouped = $transactions->groupBy(fn($t) => $t->transaction_type?->name ?? 'Unknown');

        $tableRows = [];
        $totalsPerFloor = [];
        foreach ($floors as $floor) {
            $totalsPerFloor[$floor->id] = 0;
        }

        foreach ($grouped as $typeName => $items) {
            $rowPerFloor = [];
            $rowTotal = 0;
            foreach ($floors as $floor) {
                $amount = (float) $items->filter(fn($t) => $t->room?->floor_id == $floor->id)->sum('payable_amount');
                $rowPerFloor[$floor->id] = $amount;
                $rowTotal += $amount;
                $totalsPerFloor[$floor->id] += $amount;
            }
            $tableRows[] = [
                'description' => $typeName === 'Check In' ? 'Room' : $typeName,
                'floors' => $rowPerFloor,
                'row_total' => $rowTotal,
            ];
        }

        $this->roomSummary = [
            'floors' => $floors->map(fn($f) => ['id' => $f->id, 'number' => $f->number])->all(),
            'rows' => $tableRows,
            'totals' => $totalsPerFloor,
        ];
    }

    // ─── Shift Mode: Helper calculations (fallback when no snapshot) ─

    private function calculateShiftCountsFromSession(ShiftSession $session): void
    {
        $branchId = auth()->user()->branch_id;
        $this->shiftCheckins = CheckinDetail::whereHas('room', fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('check_in_at', [$session->opened_at, $session->closed_at])
            ->count();
        $this->shiftCheckouts = CheckinDetail::whereHas('room', fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('check_out_at', [$session->opened_at, $session->closed_at])
            ->count();
    }

    private function calculateCheckoutRoomAmountFromSession(ShiftSession $session): float
    {
        $branchId = auth()->user()->branch_id;
        $checkoutIds = CheckinDetail::whereHas('room', fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('check_out_at', [$session->opened_at, $session->closed_at])
            ->pluck('id')
            ->toArray();

        if (empty($checkoutIds)) {
            return 0;
        }

        return (float) Transaction::whereIn('checkin_detail_id', $checkoutIds)
            ->where('transaction_type_id', 1)
            ->sum('payable_amount');
    }

    private function calculateCheckoutTotalsFromSession(ShiftSession $session): void
    {
        $branchId = auth()->user()->branch_id;
        $checkoutIds = CheckinDetail::whereHas('room', fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('check_out_at', [$session->opened_at, $session->closed_at])
            ->pluck('id')
            ->toArray();

        if (empty($checkoutIds)) {
            $this->checkoutRoomAmount = 0;
            $this->checkoutRoomDeposit = 0;
            $this->remainingRoomDeposit = ($this->shiftCheckins + $this->forwardedCount) * 200;
            return;
        }

        $this->checkoutRoomAmount = (float) Transaction::whereIn('checkin_detail_id', $checkoutIds)
            ->where('transaction_type_id', 1)
            ->sum('payable_amount');

        $this->checkoutRoomDeposit = $this->shiftCheckouts * 200;
        $remainingGuests = max(0, $this->shiftCheckins + $this->forwardedCount - $this->shiftCheckouts);
        $this->remainingRoomDeposit = $remainingGuests * 200;
    }

    // ─── Date Range Mode: Sales Rows ─────────────────────────────────

    private function buildDateRangeSalesRows(): array
    {
        $occupyingIds = $this->getOccupyingCheckinIds();
        $range = $this->getFilterRange();

        $transactionRows = $this->getDateRangeTransactionRows($occupyingIds, $range);

        return collect($transactionRows)
            ->sortBy('room_number')
            ->values()
            ->toArray();
    }

    private function getDateRangeTransactionRows(array $occupyingIds, array $range): array
    {
        if (empty($occupyingIds)) {
            return [];
        }

        $query = Transaction::query()
            ->with(['checkin_details.guest', 'room.type', 'processedBy', 'transaction_type'])
            ->whereIn('checkin_detail_id', $occupyingIds)
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->when($this->frontdesk, function ($q) {
                $q->where('processed_by_user_id', $this->frontdesk);
            })
            ->orderBy('created_at')
            ->get();

        $dateFrom = $this->date_from ?? now()->toDateString();

        return $query->map(function ($txn) use ($dateFrom) {
            $cd = $txn->checkin_details;
            $total = in_array($txn->transaction_type_id, [2, 5]) ? 0 : (float) $txn->payable_amount;

            // Forwarded in date range mode: checked in before date_from
            $isForwarded = false;
            if ($cd && $cd->check_in_at) {
                $isForwarded = Carbon::parse($cd->check_in_at)->toDateString() < $dateFrom;
            }

            // Display type for deposits using deposit_type column
            $displayType = $txn->transaction_type?->name ?? '—';
            if ($txn->transaction_type_id == 2) {
                $displayType = $txn->deposit_type === 'room_key' ? 'Room Deposit' : 'Guest Deposit';
            }

            $checkInAt = $cd?->check_in_at ? Carbon::parse($cd->check_in_at) : null;
            $checkOutAt = $cd?->check_out_at ? Carbon::parse($cd->check_out_at) : null;

            return [
                'checkin_detail_id' => $txn->checkin_detail_id,
                'room_number' => $txn->room?->number ?? '—',
                'room_id' => $txn->room_id,
                'room_type' => $txn->room?->type?->name ?? '—',
                'guest_name' => strtoupper($cd?->guest?->name ?? '—'),
                'transaction_type' => $displayType,
                'transaction_type_id' => $txn->transaction_type_id,
                'check_in' => $checkInAt?->format('m-d-Y h:iA') ?? '—',
                'check_out' => $checkOutAt?->format('m-d-Y h:iA') ?? '—',
                'hours_stayed' => $cd?->hours_stayed ? $cd->hours_stayed . ' hrs' : '—',
                'amount' => (float) $txn->payable_amount,
                'remarks' => $txn->remarks,
                'processed_by' => strtoupper($txn->processedBy?->name ?? '—'),
                'shift' => '—',
                'transaction_date' => $txn->created_at
                    ? Carbon::parse($txn->created_at)->format('m-d-Y h:iA')
                    : '—',
                'total' => $total,
                'is_forwarded' => $isForwarded,
                'is_forwarded_guest_row' => false,
            ];
        })->toArray();
    }

    // ─── Date Range Mode: Helpers ────────────────────────────────────

    private function calculateShiftCountsDateRange(): void
    {
        $range = $this->getFilterRange();
        $branchId = auth()->user()->branch_id;

        $this->shiftCheckins = CheckinDetail::whereHas('room', fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('check_in_at', [$range['start'], $range['end']])
            ->count();

        $this->shiftCheckouts = CheckinDetail::whereHas('room', fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('check_out_at', [$range['start'], $range['end']])
            ->count();
    }

    private function calculateCheckoutTotalsDateRange(): void
    {
        $range = $this->getFilterRange();
        $branchId = auth()->user()->branch_id;

        $checkoutIds = CheckinDetail::query()
            ->whereHas('room', fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('check_out_at', [$range['start'], $range['end']])
            ->pluck('id')
            ->toArray();

        if (empty($checkoutIds)) {
            $this->checkoutRoomAmount = 0;
            $this->checkoutRoomDeposit = 0;
            $this->remainingRoomDeposit = ($this->shiftCheckins + $this->forwardedCount) * 200;
            return;
        }

        $this->checkoutRoomAmount = (float) Transaction::whereIn('checkin_detail_id', $checkoutIds)
            ->where('transaction_type_id', 1)
            ->sum('payable_amount');

        $this->checkoutRoomDeposit = $this->shiftCheckouts * 200;
        $remainingGuests = max(0, $this->shiftCheckins + $this->forwardedCount - $this->shiftCheckouts);
        $this->remainingRoomDeposit = $remainingGuests * 200;
    }

    /**
     * Calculate forwarded room and deposit totals (date_range mode only).
     */
    private function calculateForwardedTotals(): void
    {
        $forwardedRows = collect($this->salesRows)->filter(fn($row) => $row['is_forwarded']);

        $this->forwardedRoom = (float) $forwardedRows->where('transaction_type_id', 1)->sum('amount');

        // Split deposits by deposit type (using display label)
        $this->forwardedRoomDeposit = (float) $forwardedRows
            ->filter(fn($row) => $row['transaction_type'] === 'Room Deposit')
            ->sum('amount');

        $originalGuestDeposit = (float) $forwardedRows
            ->filter(fn($row) => $row['transaction_type'] === 'Guest Deposit')
            ->sum('amount');

        $forwardedCashouts = (float) $forwardedRows->where('transaction_type_id', 5)->sum('amount');
        $this->forwardedGuestDeposit = max(0, $originalGuestDeposit - $forwardedCashouts);
    }

    // ─── Shared: Summary, Expenses, Remittance, Room Summary ─────────

    /**
     * Build summary totals grouped by transaction type (from salesRows).
     */
    private function buildSummaryByType(): array
    {
        $grouped = collect($this->salesRows)->groupBy('transaction_type_id');

        $summary = [
            'room_charges' => 0,
            'extensions' => 0,
            'amenities' => 0,
            'food' => 0,
            'damages' => 0,
            'transfers' => 0,
            'deposits' => 0,
            'room_deposits' => 0,
            'guest_deposits' => 0,
            'cashouts' => 0,
        ];

        foreach ($grouped as $typeId => $transactions) {
            $sum = collect($transactions)->sum('amount');
            switch ($typeId) {
                case 1: $summary['room_charges'] = $sum; break;
                case 2: $summary['deposits'] = $sum; break;
                case 4: $summary['damages'] = $sum; break;
                case 5: $summary['cashouts'] = $sum; break;
                case 6: $summary['extensions'] = $sum; break;
                case 7: $summary['transfers'] = $sum; break;
                case 8: $summary['amenities'] = $sum; break;
                case 9: $summary['food'] = $sum; break;
            }
        }

        // Calculate deposit breakdown using display type
        foreach ($this->salesRows as $row) {
            if ($row['transaction_type_id'] == 2) {
                if ($row['transaction_type'] === 'Room Deposit') {
                    $summary['room_deposits'] += $row['amount'];
                } else {
                    $summary['guest_deposits'] += $row['amount'];
                }
            }
        }

        $summary['grand_total'] = $summary['room_charges']
            + $summary['extensions']
            + $summary['amenities']
            + $summary['food']
            + $summary['damages']
            + $summary['transfers'];

        return $summary;
    }

    /**
     * Build expenses summary (date_range mode).
     */
    private function buildExpensesSummary(): void
    {
        $range = $this->getFilterRange();

        $rows = Expense::query()
            ->with(['expenseCategory', 'user'])
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->when($this->frontdesk, fn($q) => $q->where('user_id', $this->frontdesk))
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($e) {
                return [
                    'expense_type' => $e->expenseCategory?->name ?? '—',
                    'description' => $e->description ?? '—',
                    'shift' => $e->shift ?? '—',
                    'amount' => (float) $e->amount,
                    'frontdesk' => strtoupper($e->user?->name ?? '—'),
                ];
            });

        $this->expensesRows = $rows;
        $this->expensesTotal = (float) $rows->sum('amount');
    }

    /**
     * Build remittance summary (date_range mode).
     */
    private function buildRemittanceSummary(): void
    {
        $range = $this->getFilterRange();

        $rows = Remittance::query()
            ->with('user')
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->when($this->frontdesk, fn($q) => $q->where('user_id', $this->frontdesk))
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($r) {
                return [
                    'description' => $r->description ?? '—',
                    'amount' => (float) $r->total_remittance,
                    'frontdesk' => strtoupper($r->user?->name ?? '—'),
                ];
            });

        $this->remittanceRows = $rows;
        $this->remittanceTotal = (float) $rows->sum('amount');
    }

    /**
     * Build room summary by floor (date_range mode).
     */
    private function buildRoomSummary(): void
    {
        $floors = Floor::query()
            ->where('branch_id', auth()->user()->branch_id)
            ->orderBy('number')
            ->get();

        $occupyingIds = $this->getOccupyingCheckinIds();

        if (empty($occupyingIds)) {
            $this->roomSummary = [
                'floors' => $floors->map(fn($f) => ['id' => $f->id, 'number' => $f->number])->all(),
                'rows' => [],
                'totals' => [],
            ];
            return;
        }

        $range = $this->getFilterRange();

        $transactions = Transaction::query()
            ->with(['room.floor', 'transaction_type'])
            ->whereIn('checkin_detail_id', $occupyingIds)
            ->whereNotIn('transaction_type_id', [2, 5])
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->when($this->frontdesk, function ($q) {
                $q->where('processed_by_user_id', $this->frontdesk);
            })
            ->get();

        $grouped = $transactions->groupBy(fn($t) => $t->transaction_type?->name ?? 'Unknown');

        $tableRows = [];
        $totalsPerFloor = [];
        foreach ($floors as $floor) {
            $totalsPerFloor[$floor->id] = 0;
        }

        foreach ($grouped as $typeName => $items) {
            $rowPerFloor = [];
            $rowTotal = 0;
            foreach ($floors as $floor) {
                $amount = (float) $items->filter(fn($t) => $t->room?->floor_id == $floor->id)->sum('payable_amount');
                $rowPerFloor[$floor->id] = $amount;
                $rowTotal += $amount;
                $totalsPerFloor[$floor->id] += $amount;
            }
            $tableRows[] = [
                'description' => $typeName === 'Check In' ? 'Room' : $typeName,
                'floors' => $rowPerFloor,
                'row_total' => $rowTotal,
            ];
        }

        $this->roomSummary = [
            'floors' => $floors->map(fn($f) => ['id' => $f->id, 'number' => $f->number])->all(),
            'rows' => $tableRows,
            'totals' => $totalsPerFloor,
        ];
    }

    // ─── Shared: Filter Range & Occupying IDs ────────────────────────

    /**
     * Get the date/time range based on filter mode.
     */
    private function getFilterRange(): array
    {
        if ($this->filterMode === 'shift' && $this->selectedShiftLogId) {
            $session = collect($this->availableShiftSessions)
                ->firstWhere('id', $this->selectedShiftLogId);

            if ($session) {
                return [
                    'start' => Carbon::parse($session['time_in']),
                    'end' => Carbon::parse($session['time_out']),
                ];
            }
        }

        $startDate = $this->date_from ?? now()->toDateString();
        $endDate = $this->date_to ?? now()->toDateString();

        return [
            'start' => Carbon::parse($startDate)->startOfDay(),
            'end' => Carbon::parse($endDate)->endOfDay(),
        ];
    }

    /**
     * Find check-in details where guest was OCCUPYING room during the filter period.
     */
    private function getOccupyingCheckinIds(): array
    {
        $range = $this->getFilterRange();

        return CheckinDetail::query()
            ->whereHas('room', fn($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->where('check_in_at', '<=', $range['end'])
            ->where(function ($q) use ($range) {
                $q->whereNull('check_out_at')
                  ->orWhere('check_out_at', '>=', $range['start']);
            })
            ->pluck('id')
            ->toArray();
    }

    // ─── Card Modal ──────────────────────────────────────────────────

    /**
     * Open card detail modal with filtered rows for the clicked card type.
     */
    public function openCardModal(string $type): void
    {
        $titles = [
            'room_charges' => 'Room Charges',
            'extensions' => 'Extensions',
            'amenities' => 'Amenities',
            'food' => 'Food and Beverages',
            'damages' => 'Damage Charges',
            'transfers' => 'Transfers',
            'room_deposits' => 'Room Deposits',
            'guest_deposits' => 'Guest Deposits',
            'cashouts' => 'Cashouts',
            'checkout_room' => 'Checkout Room Amount',
            'checkout_room_deposit' => 'Checkout Room Deposit',
            'remaining_room_deposit' => 'Remaining Room Deposit',
            'fwd_room' => 'Forwarded Room',
            'fwd_room_deposit' => 'Forwarded Room Deposit',
            'fwd_guest_deposit' => 'Forwarded Guest Deposit',
            'unclaimed_deposits' => 'Unclaimed Guest Deposits',
        ];

        $this->cardModalTitle = $titles[$type] ?? $type;
        $rows = collect($this->salesRows);

        $typeFilterMap = [
            'room_charges' => fn($r) => $r['transaction_type_id'] == 1,
            'extensions' => fn($r) => $r['transaction_type_id'] == 6,
            'amenities' => fn($r) => $r['transaction_type_id'] == 8,
            'food' => fn($r) => $r['transaction_type_id'] == 9,
            'damages' => fn($r) => $r['transaction_type_id'] == 4,
            'transfers' => fn($r) => $r['transaction_type_id'] == 7,
            'cashouts' => fn($r) => $r['transaction_type_id'] == 5,
            'room_deposits' => fn($r) => ($r['transaction_type_id'] == 2 && $r['transaction_type'] === 'Room Deposit') || (($r['is_forwarded_guest_row'] ?? false) && $r['transaction_type'] === 'FWD ROOM DEPOSIT'),
            'guest_deposits' => fn($r) => $r['transaction_type_id'] == 2 && $r['transaction_type'] === 'Guest Deposit',
            'fwd_room' => fn($r) => ($r['is_forwarded_guest_row'] ?? false) && $r['transaction_type'] === 'FWD ROOM',
            'fwd_room_deposit' => fn($r) => ($r['is_forwarded_guest_row'] ?? false) && $r['transaction_type'] === 'FWD ROOM DEPOSIT',
            'fwd_guest_deposit' => fn($r) => ($r['is_forwarded_guest_row'] ?? false) && $r['transaction_type'] === 'FWD GUEST DEPOSIT',
            'unclaimed_deposits' => fn($r) => ($r['is_forwarded_guest_row'] ?? false) && str_contains($r['remarks'] ?? '', 'Unclaimed'),
        ];

        if (isset($typeFilterMap[$type])) {
            $filtered = $rows->filter($typeFilterMap[$type])->values();
            $this->cardModalRows = $filtered->toArray();
            $this->cardModalTotal = (float) $filtered->sum('amount');

            // Override totals to match card values
            if ($type === 'fwd_room_deposit') {
                $this->cardModalTotal = $this->forwardedRoomDeposit;
            } elseif ($type === 'fwd_guest_deposit') {
                $this->cardModalTotal = $this->forwardedGuestDeposit;
            } elseif ($type === 'room_deposits') {
                $this->cardModalTotal = ($this->shiftCheckins + $this->forwardedCount) * 200;
            }
        } elseif (in_array($type, ['checkout_room', 'checkout_room_deposit', 'remaining_room_deposit'])) {
            $this->buildCheckoutModalRows($type);
        } else {
            $this->cardModalRows = [];
            $this->cardModalTotal = 0;
        }

        $this->showCardModal = true;
    }

    /**
     * Build modal rows for checkout-related cards.
     */
    private function buildCheckoutModalRows(string $type): void
    {
        $range = $this->getFilterRange();
        $branchId = auth()->user()->branch_id;

        $checkoutDetails = CheckinDetail::query()
            ->with(['guest', 'room.type'])
            ->whereHas('room', fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('check_out_at', [$range['start'], $range['end']])
            ->get();

        $checkoutIds = $checkoutDetails->pluck('id')->toArray();
        $allCheckoutTransactions = Transaction::whereIn('checkin_detail_id', $checkoutIds)
            ->whereIn('transaction_type_id', [1, 2])
            ->get()
            ->groupBy('checkin_detail_id');

        $rows = [];
        foreach ($checkoutDetails as $cd) {
            $cdTxns = $allCheckoutTransactions->get($cd->id, collect());
            if ($type === 'checkout_room') {
                $amount = (float) $cdTxns->where('transaction_type_id', 1)->sum('payable_amount');
            } elseif ($type === 'checkout_room_deposit') {
                $amount = (float) $cdTxns->where('transaction_type_id', 2)
                    ->where('deposit_type', 'room_key')
                    ->sum('payable_amount');
            } else {
                continue;
            }

            if ($amount > 0) {
                $checkInAt = $cd->check_in_at ? Carbon::parse($cd->check_in_at) : null;
                $checkOutAt = $cd->check_out_at ? Carbon::parse($cd->check_out_at) : null;

                $rows[] = [
                    'room_number' => $cd->room?->number ?? '—',
                    'room_type' => $cd->room?->type?->name ?? '—',
                    'guest_name' => strtoupper($cd->guest?->name ?? '—'),
                    'transaction_type' => $type === 'checkout_room' ? 'Room Charge' : 'Room Deposit',
                    'check_in' => $checkInAt?->format('m-d-Y h:iA') ?? '—',
                    'check_out' => $checkOutAt?->format('m-d-Y h:iA') ?? '—',
                    'amount' => $amount,
                    'remarks' => 'Checked out',
                    'transaction_date' => $checkOutAt?->format('m-d-Y h:iA') ?? '—',
                ];
            }
        }

        if ($type === 'remaining_room_deposit') {
            $checkoutCheckinDetailIds = $checkoutDetails->pluck('id')->toArray();
            $remainingRows = collect($this->salesRows)
                ->filter(function ($r) {
                    if ($r['transaction_type_id'] == 2 && $r['transaction_type'] === 'Room Deposit') {
                        return true;
                    }
                    if (($r['is_forwarded_guest_row'] ?? false) && $r['transaction_type'] === 'FWD ROOM DEPOSIT') {
                        return true;
                    }
                    return false;
                })
                ->filter(function ($r) use ($checkoutCheckinDetailIds) {
                    return !in_array($r['checkin_detail_id'] ?? null, $checkoutCheckinDetailIds);
                })
                ->unique('checkin_detail_id')
                ->values()
                ->toArray();

            $this->cardModalRows = $remainingRows;
            $this->cardModalTotal = count($remainingRows) * 200;
            return;
        }

        $this->cardModalRows = $rows;
        $this->cardModalTotal = (float) collect($rows)->sum('amount');
    }

    // ─── Misc ────────────────────────────────────────────────────────

    public function resetFilters()
    {
        $this->reset(['frontdesk', 'date_from', 'date_to', 'selectedShiftLogId']);
        $this->filterMode = 'date_range';
        $this->date_from = now()->toDateString();
        $this->date_to = now()->toDateString();
        $this->generateReport();
    }

    /**
     * Kept for view compatibility — referenced in blade as a method call.
     */
    public function showCard(string $type): void
    {
        $this->openCardModal($type);
    }
}
