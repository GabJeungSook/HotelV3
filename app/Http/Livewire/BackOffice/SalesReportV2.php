<?php

namespace App\Http\Livewire\BackOffice;

use Livewire\Component;
use App\Models\Frontdesk;
use App\Models\CheckinDetail;
use App\Models\Transaction;
use App\Models\Floor;
use App\Models\Expense;
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
    public $date_from;
    public $date_to;
    public $frontdesk;
    public $frontdesk_name;
    public $shift;

    public array $salesRows = [];
    public float $totalSales = 0;
    public array $summaryByType = [];
    public array $roomSummary = [];

    public $expensesRows;
    public float $expensesTotal = 0;

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

        $this->generateReport();
    }

    public function render()
    {
        return view('livewire.back-office.sales-report-v2', [
            'frontdesks' => Frontdesk::where('branch_id', auth()->user()->branch_id)->get(),
        ]);
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
    }

    public function resetFilters()
    {
        $this->reset(['frontdesk', 'date_from', 'date_to', 'shift']);
        $this->date_from = now()->toDateString();
        $this->date_to = now()->toDateString();
        $this->generateReport();
    }

    /**
     * Find check-in details where guest was OCCUPYING room during the date range.
     *
     * A guest is considered "occupying" if:
     * - They checked in ON or BEFORE the end date, AND
     * - They haven't checked out yet OR checked out ON or AFTER the start date
     */
    private function getOccupyingCheckinIds(): array
    {
        $startDate = $this->date_from ?? now()->toDateString();
        $endDate = $this->date_to ?? now()->toDateString();

        return CheckinDetail::query()
            ->whereHas('room', fn($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->where('check_in_at', '<=', $endDate . ' 23:59:59')
            ->where(function ($q) use ($startDate) {
                $q->whereNull('check_out_at')
                  ->orWhere('check_out_at', '>=', $startDate . ' 00:00:00');
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

        $query = DB::table('transactions as tr')
            ->leftJoin('shift_logs as sl', 'sl.id', '=', 'tr.shift_log_id')
            ->leftJoin('users as u', 'u.id', '=', 'sl.frontdesk_id')
            ->leftJoin('checkin_details as cd', 'cd.id', '=', 'tr.checkin_detail_id')
            ->leftJoin('guests as g', 'g.id', '=', 'tr.guest_id')
            ->leftJoin('rooms as r', 'r.id', '=', 'tr.room_id')
            ->leftJoin('types as t', 't.id', '=', 'r.type_id')
            ->leftJoin('transaction_types as tt', 'tt.id', '=', 'tr.transaction_type_id')
            ->whereIn('tr.checkin_detail_id', $occupyingIds)
            // Filter by WHO PROCESSED the transaction (via shift_log)
            ->when($this->frontdesk, function ($q) {
                $q->where('sl.frontdesk_id', $this->frontdesk);
            })
            // Filter by shift if specified
            ->when($this->shift, function ($q) {
                $q->where('sl.shift', $this->shift);
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
            ])
            ->orderBy('r.number')
            ->orderBy('tr.created_at')
            ->get();

        $dateFrom = $this->date_from ?? now()->toDateString();

        return $query->map(function ($row) use ($dateFrom) {
            // Calculate total excluding deposits (type 2) and cashouts (type 5)
            $total = in_array($row->transaction_type_id, [2, 5]) ? 0 : (float) $row->payable_amount;

            // Determine if guest is "Forwarded" (checked in before report date range)
            $isForwarded = $row->check_in_at
                ? Carbon::parse($row->check_in_at)->toDateString() < $dateFrom
                : false;

            return [
                'room_number' => $row->room_number ?? '—',
                'room_id' => $row->room_id,
                'room_type' => $row->room_type ?? '—',
                'guest_name' => strtoupper($row->guest_name ?? '—'),
                'transaction_type' => $row->transaction_type ?? '—',
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
     * Build expenses summary for the date range.
     */
    private function buildExpensesSummary(): void
    {
        $startDate = $this->date_from ?? now()->toDateString();
        $endDate = $this->date_to ?? now()->toDateString();

        $rows = Expense::query()
            ->with(['expenseCategory', 'user'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->when($this->shift, fn($q) => $q->where('shift', $this->shift))
            ->when($this->frontdesk, fn($q) => $q->where('user_id', $this->frontdesk))
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

        $transactions = Transaction::query()
            ->with(['room.floor', 'transaction_type'])
            ->whereIn('checkin_detail_id', $occupyingIds)
            ->whereNotIn('transaction_type_id', [2, 5]) // Exclude deposits and cashouts
            ->when($this->frontdesk, function ($q) {
                $q->whereHas('shift_log', fn($q2) => $q2->where('frontdesk_id', $this->frontdesk));
            })
            ->when($this->shift, function ($q) {
                $q->whereHas('shift_log', fn($q2) => $q2->where('shift', $this->shift));
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
