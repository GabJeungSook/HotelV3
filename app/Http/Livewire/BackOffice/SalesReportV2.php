<?php

namespace App\Http\Livewire\BackOffice;

use Livewire\Component;
use App\Models\Frontdesk;
use App\Models\CheckinDetail;
use App\Models\Transaction;
use App\Models\Floor;
use App\Models\Expense;
use App\Models\ShiftLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Sales Report V2 - Occupancy-Based
 *
 * This report shows transactions for guests who were OCCUPYING rooms during
 * the selected date range, including guests who checked in before the range
 * but were still staying (forward/carry-over guests).
 *
 * Key differences from original SalesReport:
 * 1. Date filter = guest occupancy period (not transaction created_at)
 * 2. Frontdesk filter = who processed the transaction (not who checked in)
 * 3. Uses LEFT JOIN on shift_logs (handles NULL shift_log_id)
 */
class SalesReportV2 extends Component
{
    // Filter mode: 'date_range' or 'shift'
    public string $filterMode = 'date_range';

    // Date range mode properties
    public $date_from;
    public $date_to;
    public $frontdesk;
    public $frontdesk_name;

    // Shift mode properties
    public $shiftDate;
    public $selectedShiftLogId;
    public array $availableShiftLogs = [];

    public array $salesRows = [];
    public float $totalSales = 0;
    public array $summaryByType = [];
    public array $roomSummary = [];

    public $expensesRows;
    public float $expensesTotal = 0;
    public float $netSales = 0;
    public int $forwardedCount = 0;

    // Forwarded totals
    public float $forwardedRoom = 0;
    public float $forwardedDeposit = 0;

    public function mount()
    {
        $this->date_from = now()->toDateString();
        $this->date_to = now()->toDateString();
        $this->shiftDate = now()->toDateString();
        $this->salesRows = [];
        $this->totalSales = 0;
        $this->summaryByType = [];
        $this->expensesRows = collect();
        $this->expensesTotal = 0;
        $this->roomSummary = [];
        $this->forwardedRoom = 0;
        $this->forwardedDeposit = 0;

        $this->loadAvailableShiftLogs();
        $this->generateReport();
    }

    public function render()
    {
        return view('livewire.back-office.sales-report-v2', [
            'frontdesks' => Frontdesk::where('branch_id', auth()->user()->branch_id)->get(),
        ]);
    }

    /**
     * Load available shift logs when shift date changes.
     */
    public function updatedShiftDate()
    {
        $this->loadAvailableShiftLogs();
        $this->selectedShiftLogId = null;
    }

    /**
     * Load completed shift logs for the selected date.
     */
    private function loadAvailableShiftLogs(): void
    {
        if (!$this->shiftDate) {
            $this->availableShiftLogs = [];
            return;
        }

        $this->availableShiftLogs = ShiftLog::query()
            ->whereDate('time_in', $this->shiftDate)
            ->whereNotNull('time_out') // Completed shifts only
            ->with('frontdesk:id,name')
            ->orderBy('time_in')
            ->get()
            ->map(function ($log) {
                $frontdeskNames = $this->formatFrontdeskNames($log);
                return [
                    'id' => $log->id,
                    'label' => ($log->shift ?? 'N/A') . ' - ' . $frontdeskNames
                             . ' (' . $log->time_in->format('g:i A') . ' - ' . $log->time_out->format('g:i A') . ')',
                    'time_in' => $log->time_in->toDateTimeString(),
                    'time_out' => $log->time_out->toDateTimeString(),
                ];
            })
            ->toArray();
    }

    /**
     * Format frontdesk names from shift log.
     */
    private function formatFrontdeskNames(ShiftLog $log): string
    {
        // Primary frontdesk
        $name = $log->frontdesk?->name ?? 'Unknown';

        // Check for additional frontdesks in frontdesk_ids
        if ($log->frontdesk_ids) {
            $ids = json_decode($log->frontdesk_ids, true) ?? [];
            $ids = array_filter($ids, fn($id) => $id !== 'N/A' && $id != $log->frontdesk_id);

            if (!empty($ids)) {
                $additionalNames = \App\Models\User::whereIn('id', $ids)->pluck('name')->toArray();
                if (!empty($additionalNames)) {
                    $name .= ', ' . implode(', ', $additionalNames);
                }
            }
        }

        return $name;
    }

    /**
     * When filter mode changes, reset related properties.
     */
    public function updatedFilterMode()
    {
        if ($this->filterMode === 'shift') {
            $this->loadAvailableShiftLogs();
        }
    }

    public function generateReport()
    {
        $frontdesk = Frontdesk::find($this->frontdesk);
        $this->frontdesk_name = $frontdesk?->name;

        $this->salesRows = $this->buildSalesRows();
        $this->totalSales = collect($this->salesRows)->sum('total');
        $this->summaryByType = $this->buildSummaryByType();
        $this->buildExpensesSummary();
        $this->buildRoomSummary();
        $this->calculateForwardedTotals();

        // Calculate net sales (Gross - Expenses)
        $this->netSales = $this->totalSales - $this->expensesTotal;

        // Count unique forwarded guests
        $this->forwardedCount = collect($this->salesRows)
            ->filter(fn($row) => $row['is_forwarded'])
            ->unique('guest_name')
            ->count();
    }

    /**
     * Calculate forwarded room and deposit totals.
     */
    private function calculateForwardedTotals(): void
    {
        $forwardedRows = collect($this->salesRows)->filter(fn($row) => $row['is_forwarded']);

        // Forwarded Room = Check In transactions (type 1) from forwarded guests
        $this->forwardedRoom = $forwardedRows
            ->where('transaction_type_id', 1)
            ->sum('amount');

        // Forwarded Deposit = Deposit transactions (type 2) from forwarded guests
        $this->forwardedDeposit = $forwardedRows
            ->where('transaction_type_id', 2)
            ->sum('amount');
    }

    public function resetFilters()
    {
        $this->reset(['frontdesk', 'date_from', 'date_to', 'selectedShiftLogId']);
        $this->filterMode = 'date_range';
        $this->date_from = now()->toDateString();
        $this->date_to = now()->toDateString();
        $this->shiftDate = now()->toDateString();
        $this->loadAvailableShiftLogs();
        $this->generateReport();
    }

    /**
     * Get the date/time range based on filter mode.
     */
    private function getFilterRange(): array
    {
        if ($this->filterMode === 'shift' && $this->selectedShiftLogId) {
            $shiftLog = ShiftLog::find($this->selectedShiftLogId);
            if ($shiftLog && $shiftLog->time_in && $shiftLog->time_out) {
                return [
                    'start' => $shiftLog->time_in,
                    'end' => $shiftLog->time_out,
                ];
            }
        }

        // Default: date range mode
        $startDate = $this->date_from ?? now()->toDateString();
        $endDate = $this->date_to ?? now()->toDateString();

        return [
            'start' => Carbon::parse($startDate)->startOfDay(),
            'end' => Carbon::parse($endDate)->endOfDay(),
        ];
    }

    /**
     * Find check-in details where guest was OCCUPYING room during the filter period.
     *
     * A guest is considered "occupying" if:
     * - They checked in ON or BEFORE the end time, AND
     * - They haven't checked out yet OR checked out ON or AFTER the start time
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

    /**
     * Build sales rows for all transactions of occupying guests.
     * Uses LEFT JOIN on shift_logs to handle transactions with NULL shift_log_id.
     * Filters by transaction processor (shift_log.frontdesk_id), not check-in frontdesk.
     */
    private function buildSalesRows(): array
    {
        $occupyingIds = $this->getOccupyingCheckinIds();

        if (empty($occupyingIds)) {
            return [];
        }

        $range = $this->getFilterRange();

        $query = DB::table('transactions as tr')
            ->leftJoin('shift_logs as sl', 'sl.id', '=', 'tr.shift_log_id')
            ->leftJoin('users as u', 'u.id', '=', 'sl.frontdesk_id')
            ->leftJoin('checkin_details as cd', 'cd.id', '=', 'tr.checkin_detail_id')
            ->leftJoin('guests as g', 'g.id', '=', 'tr.guest_id')
            ->leftJoin('rooms as r', 'r.id', '=', 'tr.room_id')
            ->leftJoin('types as t', 't.id', '=', 'r.type_id')
            ->leftJoin('transaction_types as tt', 'tt.id', '=', 'tr.transaction_type_id')
            ->whereIn('tr.checkin_detail_id', $occupyingIds)
            // Filter transactions by the selected range
            ->whereBetween('tr.created_at', [$range['start'], $range['end']])
            // Filter by WHO PROCESSED the transaction (only in date_range mode)
            ->when($this->filterMode === 'date_range' && $this->frontdesk, function ($q) {
                $q->where('sl.frontdesk_id', $this->frontdesk);
            })
            ->select([
                'r.number as room_number',
                'r.id as room_id',
                't.name as room_type',
                'g.name as guest_name',
                'tt.name as transaction_type',
                'tt.id as transaction_type_id',
                'cd.check_in_at',
                'cd.check_out_at',
                'cd.hours_stayed',
                'tr.payable_amount',
                'tr.remarks',
                'tr.created_at as transaction_date',
                'u.name as processed_by',
                'sl.shift as shift',
                // Subquery to get the check-in transaction's created_at timestamp
                DB::raw('(SELECT t2.created_at FROM transactions t2
                          WHERE t2.checkin_detail_id = tr.checkin_detail_id
                          AND t2.transaction_type_id = 1
                          LIMIT 1) as checkin_transaction_at'),
            ])
            ->orderBy('r.number')
            ->orderBy('tr.created_at')
            ->get();

        $shiftLog = null;
        if ($this->filterMode === 'shift' && $this->selectedShiftLogId) {
            $shiftLog = ShiftLog::find($this->selectedShiftLogId);
        }

        $dateFrom = $this->date_from ?? now()->toDateString();

        return $query->map(function ($row) use ($dateFrom, $shiftLog) {
            // Calculate total excluding deposits (type 2) and cashouts (type 5)
            $total = in_array($row->transaction_type_id, [2, 5]) ? 0 : (float) $row->payable_amount;

            // Determine if guest is "Forwarded"
            $isForwarded = $this->isGuestForwarded($row, $shiftLog, $dateFrom);

            // Determine display label for deposits based on remarks
            $displayType = $row->transaction_type;
            if ($row->transaction_type_id == 2) { // Deposit
                $remarks = strtolower($row->remarks ?? '');
                if (str_contains($remarks, 'room key') || str_contains($remarks, 'tv remote')) {
                    $displayType = 'Room Deposit';
                } else {
                    $displayType = 'Guest Deposit';
                }
            }

            return [
                'room_number' => $row->room_number ?? '—',
                'room_id' => $row->room_id,
                'room_type' => $row->room_type ?? '—',
                'guest_name' => strtoupper($row->guest_name ?? '—'),
                'transaction_type' => $displayType ?? '—',
                'transaction_type_id' => $row->transaction_type_id,
                'check_in' => $row->check_in_at
                    ? Carbon::parse($row->check_in_at)->format('m-d-Y h:iA')
                    : '—',
                'check_out' => $row->check_out_at
                    ? Carbon::parse($row->check_out_at)->format('m-d-Y h:iA')
                    : '—',
                'hours_stayed' => $row->hours_stayed ? $row->hours_stayed . ' hrs' : '—',
                'amount' => (float) $row->payable_amount,
                'remarks' => $row->remarks,
                'processed_by' => strtoupper($row->processed_by ?? '—'),
                'shift' => strtoupper($row->shift ?? '—'),
                'transaction_date' => $row->transaction_date
                    ? Carbon::parse($row->transaction_date)->format('m-d-Y h:iA')
                    : '—',
                'total' => $total,
                'is_forwarded' => $isForwarded,
            ];
        })->toArray();
    }

    /**
     * Determine if a guest is forwarded based on filter mode.
     *
     * Shift mode: Guest is forwarded if their check-in transaction was created
     * BEFORE the selected shift's time_in.
     *
     * Date range mode: Guest is forwarded if their check-in date is before date_from.
     */
    private function isGuestForwarded($row, ?ShiftLog $shiftLog, string $dateFrom): bool
    {
        if ($this->filterMode === 'shift' && $shiftLog) {
            // Shift mode: Check if check-in transaction was before this shift started
            if (!$row->checkin_transaction_at) {
                return false;
            }

            $checkInTransactionTime = Carbon::parse($row->checkin_transaction_at);

            // Guest is forwarded if they checked in BEFORE this shift started
            if ($checkInTransactionTime >= $shiftLog->time_in) {
                return false; // Checked in during this shift
            }

            // Guest was still occupying when shift started
            $checkOutTime = $row->check_out_at ? Carbon::parse($row->check_out_at) : null;
            return $checkOutTime === null || $checkOutTime >= $shiftLog->time_in;
        }

        // Date range mode: use date comparison
        if (!$row->check_in_at) {
            return false;
        }

        return Carbon::parse($row->check_in_at)->toDateString() < $dateFrom;
    }

    /**
     * Build summary totals grouped by transaction type.
     */
    private function buildSummaryByType(): array
    {
        $grouped = collect($this->salesRows)->groupBy('transaction_type_id');

        $summary = [
            'room_charges' => 0,      // Type 1: Check In
            'extensions' => 0,        // Type 6: Extend
            'amenities' => 0,         // Type 8: Amenities
            'food' => 0,              // Type 9: Food and Beverages
            'damages' => 0,           // Type 4: Damage Charges
            'transfers' => 0,         // Type 7: Transfer Room
            'deposits' => 0,          // Type 2: Deposit (not counted in total)
            'room_deposits' => 0,     // Room Key/TV Remote deposits
            'guest_deposits' => 0,    // Other guest deposits
        ];

        foreach ($grouped as $typeId => $transactions) {
            $sum = collect($transactions)->sum('amount');

            switch ($typeId) {
                case 1:
                    $summary['room_charges'] = $sum;
                    break;
                case 2:
                    $summary['deposits'] = $sum;
                    break;
                case 4:
                    $summary['damages'] = $sum;
                    break;
                case 6:
                    $summary['extensions'] = $sum;
                    break;
                case 7:
                    $summary['transfers'] = $sum;
                    break;
                case 8:
                    $summary['amenities'] = $sum;
                    break;
                case 9:
                    $summary['food'] = $sum;
                    break;
            }
        }

        // Calculate deposit breakdown (Room Deposit vs Guest Deposit)
        foreach ($this->salesRows as $row) {
            if ($row['transaction_type_id'] == 2) {
                $remarks = strtolower($row['remarks'] ?? '');
                if (str_contains($remarks, 'room key') || str_contains($remarks, 'tv remote')) {
                    $summary['room_deposits'] += $row['amount'];
                } else {
                    $summary['guest_deposits'] += $row['amount'];
                }
            }
        }

        // Grand total excludes deposits (type 2) and cashouts (type 5)
        $summary['grand_total'] = $summary['room_charges']
            + $summary['extensions']
            + $summary['amenities']
            + $summary['food']
            + $summary['damages']
            + $summary['transfers'];

        return $summary;
    }

    /**
     * Build expenses summary for the filter range.
     */
    private function buildExpensesSummary(): void
    {
        $range = $this->getFilterRange();

        $rows = Expense::query()
            ->with(['expenseCategory', 'user'])
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->when($this->filterMode === 'date_range' && $this->frontdesk, fn($q) => $q->where('user_id', $this->frontdesk))
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($e) {
                return [
                    'expense_type' => $e->expenseCategory?->name ?? '—',
                    'description' => $e->description ?? '—',
                    'shift' => $e->shift ?? '—',
                    'amount' => (float) $e->amount,
                    'frontdesk' => $e->user?->name ?? '—',
                ];
            });

        $this->expensesRows = $rows;
        $this->expensesTotal = (float) $rows->sum('amount');
    }

    /**
     * Build room summary by floor and transaction type.
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
            ->whereNotIn('transaction_type_id', [2, 5]) // Exclude deposits and cashouts
            // Filter transactions by range
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->when($this->filterMode === 'date_range' && $this->frontdesk, function ($q) {
                $q->whereHas('shift_log', fn($q2) => $q2->where('frontdesk_id', $this->frontdesk));
            })
            ->get();

        $grouped = $transactions->groupBy(
            fn($t) => $t->transaction_type?->name ?? 'Unknown'
        );

        $tableRows = [];
        $totalsPerFloor = [];

        foreach ($floors as $floor) {
            $totalsPerFloor[$floor->id] = 0;
        }

        foreach ($grouped as $typeName => $items) {
            $rowPerFloor = [];
            $rowTotal = 0;

            foreach ($floors as $floor) {
                $amount = (float) $items
                    ->filter(fn($t) => $t->room?->floor_id == $floor->id)
                    ->sum('payable_amount');

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
            'floors' => $floors->map(fn($f) => [
                'id' => $f->id,
                'number' => $f->number,
            ])->all(),
            'rows' => $tableRows,
            'totals' => $totalsPerFloor,
        ];
    }
}
