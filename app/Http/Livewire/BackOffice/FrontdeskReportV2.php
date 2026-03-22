<?php

namespace App\Http\Livewire\BackOffice;

use Livewire\Component;
use App\Models\ShiftLog;
use App\Models\Transaction;
use App\Models\CheckinDetail;
use App\Models\Expense;
use App\Models\Remittance;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FrontdeskReportV2 extends Component
{
    public $selectedShiftLogId;
    public array $availableShiftSessions = [];
    public array $reportData = [];

    public function mount()
    {
        $this->loadAvailableShiftSessions();
        if (!empty($this->availableShiftSessions)) {
            $this->selectedShiftLogId = end($this->availableShiftSessions)['id'];
        }
        $this->generateReport();
    }

    public function updatedSelectedShiftLogId()
    {
        $this->generateReport();
    }

    public function generateReport()
    {
        if (!$this->selectedShiftLogId) {
            $this->reportData = [];
            return;
        }

        $session = $this->getSelectedSession();
        if (!$session) {
            $this->reportData = [];
            return;
        }

        $logIds = $session['log_ids'];
        $shiftLogs = ShiftLog::whereIn('id', $logIds)->with('frontdesk:id,name')->get();

        // Use the same time range as SalesReportV2: load primary ShiftLog from DB
        $primaryShiftLog = ShiftLog::find($this->selectedShiftLogId);
        $timeIn = $primaryShiftLog->time_in;
        $timeOut = $primaryShiftLog->time_out;
        $branchId = auth()->user()->branch_id;

        // Opening Cash
        $openingCash = $this->calculateOpeningCash($shiftLogs);

        // Actual Cash (end_cash)
        $actualCash = $this->calculateActualCash($shiftLogs);

        // Frontdesk names
        $outgoingNames = $shiftLogs->map(fn($l) => $l->frontdesk?->name)->filter()->unique()->implode(', ');
        $incomingNames = $this->getIncomingFrontdesks($timeIn, $timeOut, $branchId);

        // Use occupying-guest approach (same as SalesReportV2) for accurate counts
        $occupyingIds = CheckinDetail::query()
            ->whereHas('room', fn($q) => $q->where('branch_id', $branchId))
            ->where('check_in_at', '<=', $timeOut)
            ->where(function ($q) use ($timeIn) {
                $q->whereNull('check_out_at')
                  ->orWhere('check_out_at', '>=', $timeIn);
            })
            ->pluck('id')
            ->toArray();

        // All transactions for occupying guests within this shift's time range (same as SalesReportV2)
        $transactions = Transaction::whereIn('checkin_detail_id', $occupyingIds)
            ->whereBetween('created_at', [$timeIn, $timeOut])
            ->get();

        // Sales Summary (Operation A)
        $checkins = $transactions->where('transaction_type_id', 1);
        $extensions = $transactions->where('transaction_type_id', 6);
        $transfers = $transactions->where('transaction_type_id', 7);
        $amenities = $transactions->where('transaction_type_id', 8);
        $food = $transactions->where('transaction_type_id', 9);
        $damages = $transactions->where('transaction_type_id', 4);
        $cashouts = $transactions->where('transaction_type_id', 5);
        $deposits = $transactions->where('transaction_type_id', 2);

        $roomDeposits = $deposits->filter(fn($t) => str_contains(strtolower($t->remarks ?? ''), 'room key') || str_contains(strtolower($t->remarks ?? ''), 'tv remote'));
        $guestDeposits = $deposits->filter(fn($t) => !str_contains(strtolower($t->remarks ?? ''), 'room key') && !str_contains(strtolower($t->remarks ?? ''), 'tv remote'));

        // Miscellaneous = amenities (type 8) + damages (type 4)
        $miscCount = $amenities->count() + $damages->count();
        $miscAmount = (float) $amenities->sum('payable_amount') + (float) $damages->sum('payable_amount');

        $salesSummary = [
            'new_checkin' => ['count' => $checkins->count(), 'amount' => (float) $checkins->sum('payable_amount')],
            'extension' => ['count' => $extensions->count(), 'amount' => (float) $extensions->sum('payable_amount')],
            'transfer' => ['count' => $transfers->count(), 'amount' => (float) $transfers->sum('payable_amount')],
            'miscellaneous' => ['count' => $miscCount, 'amount' => $miscAmount],
            'food' => ['count' => $food->count(), 'amount' => (float) $food->sum('payable_amount')],
            'drink' => ['count' => 0, 'amount' => 0],
            'others' => ['count' => 0, 'amount' => 0],
        ];
        $salesSummary['total'] = [
            'count' => collect($salesSummary)->sum('count'),
            'amount' => collect($salesSummary)->sum('amount'),
        ];

        // Gross Sales (excludes deposits type 2 and cashouts type 5)
        $grossSales = (float) $transactions->whereNotIn('transaction_type_id', [2, 5])->sum('payable_amount');

        // Forwarded guests
        $forwarded = $this->getForwardedData($timeIn, $branchId);

        // Checkouts during this shift
        $checkoutDetails = CheckinDetail::query()
            ->whereHas('room', fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('check_out_at', [$timeIn, $timeOut])
            ->get();
        $checkoutCount = $checkoutDetails->count();

        // Checkout room deposit amount (returned deposits)
        $checkoutIds = $checkoutDetails->pluck('id')->toArray();
        $checkoutRoomDeposit = empty($checkoutIds) ? 0 : (float) Transaction::whereIn('checkin_detail_id', $checkoutIds)
            ->where('transaction_type_id', 2)
            ->where('remarks', 'Deposit From Check In (Room Key & TV Remote)')
            ->sum('payable_amount');

        // Rooms still occupied at shift end — count + their room deposits
        $occupiedAtEnd = CheckinDetail::query()
            ->whereHas('room', fn($q) => $q->where('branch_id', $branchId))
            ->where('check_in_at', '<=', $timeOut)
            ->where(function ($q) use ($timeOut) {
                $q->whereNull('check_out_at')
                  ->orWhere('check_out_at', '>', $timeOut);
            })
            ->pluck('id')
            ->toArray();

        $endShiftRoomDepositCount = count($occupiedAtEnd);
        $endShiftRoomDeposit = empty($occupiedAtEnd) ? 0 : (float) Transaction::whereIn('checkin_detail_id', $occupiedAtEnd)
            ->where('transaction_type_id', 2)
            ->where('remarks', 'Deposit From Check In (Room Key & TV Remote)')
            ->sum('payable_amount');

        // Current shift guest deposits minus cashouts
        $currentGuestDeposit = max(0, (float) $guestDeposits->sum('payable_amount') - (float) $cashouts->sum('payable_amount'));

        // Expenses
        $expenses = Expense::whereBetween('created_at', [$timeIn, $timeOut])->get();
        $totalExpenses = (float) $expenses->sum('amount');

        // Remittance
        $remittances = Remittance::whereBetween('created_at', [$timeIn, $timeOut])->get();
        $totalRemittance = (float) $remittances->sum('total_remittance');

        // Net Sales
        $netSales = $grossSales - $totalExpenses;

        // Forwarding Balance (previous shift net sales)
        $forwardingBalance = $this->getPreviousShiftNetSales($timeIn, $branchId);

        // Cash Drawer
        $fwdRoomDeposit = $forwarded['room_deposit'];
        $fwdGuestDeposit = $forwarded['guest_deposit'];
        $totalCashReceived = $openingCash + $fwdRoomDeposit + $fwdGuestDeposit + $forwardingBalance;

        // Cash Reconciliation: Net Sales + Key/Remote Deposit + Guest Deposit + Opening Cash
        $expectedCash = $netSales + $endShiftRoomDeposit + $currentGuestDeposit + $openingCash;
        $difference = $expectedCash - $actualCash;

        // Room Status and Deposit (Operation B)
        $roomStatus = [
            'forwarded_room' => ['count' => $forwarded['room_count'], 'amount' => $forwarded['room_amount']],
            'key_remote_deposit' => ['count' => $endShiftRoomDepositCount, 'amount' => $endShiftRoomDeposit],
            'forwarded_guest_deposit' => ['count' => $forwarded['guest_deposit_count'], 'amount' => $fwdGuestDeposit],
            'guest_deposit' => ['count' => $guestDeposits->count(), 'amount' => $currentGuestDeposit],
            'total_checkout' => ['count' => $checkoutCount, 'amount' => $checkoutRoomDeposit],
            'expenses' => ['count' => $expenses->count(), 'amount' => $totalExpenses],
        ];

        $this->reportData = [
            'frontdesk_outgoing' => $outgoingNames ?: '—',
            'frontdesk_incoming' => $incomingNames ?: '—',
            'shift_opened' => $shiftLogs->min('time_in')->format('F d, Y g:i A'),
            'shift_closed' => $shiftLogs->max('time_out')->format('F d, Y g:i A'),

            'cash_drawer' => [
                'opening_cash' => $openingCash,
                'key_deposit' => $fwdRoomDeposit,
                'guest_deposit' => $fwdGuestDeposit,
                'forwarding_balance' => $forwardingBalance,
                'total' => $totalCashReceived,
            ],

            'sales_summary' => $salesSummary,
            'room_status' => $roomStatus,

            'final_sales' => [
                'gross_sales' => $grossSales,
                'refund' => 0,
                'expenses' => $totalExpenses,
                'discounts' => 0,
                'net_sales' => $netSales,
            ],

            'cash_position' => [
                'opening_cash' => $openingCash,
                'forwarded_balance' => $forwardingBalance,
                'net_sales' => $netSales,
                'remittance' => $totalRemittance,
            ],

            'cash_reconciliation' => [
                'expected_cash' => $expectedCash,
                'actual_cash' => $actualCash,
                'difference' => $difference,
            ],
        ];
    }

    private function getSelectedSession(): ?array
    {
        return collect($this->availableShiftSessions)
            ->firstWhere('id', $this->selectedShiftLogId);
    }

    private function calculateOpeningCash($shiftLogs): float
    {
        $values = $shiftLogs->pluck('beginning_cash')->filter(fn($v) => $v !== null);

        if ($values->unique()->count() <= 1) {
            return (float) $values->first();
        }

        // Combined shift: if one is 1.00, add it to the other
        $main = $values->filter(fn($v) => (float) $v != 1.0)->first() ?? 0;
        $sub = $values->filter(fn($v) => (float) $v == 1.0)->sum();
        return (float) $main + (float) $sub;
    }

    private function calculateActualCash($shiftLogs): float
    {
        $values = $shiftLogs->pluck('end_cash')->filter(fn($v) => $v !== null);

        if ($values->unique()->count() <= 1) {
            return (float) $values->first();
        }

        $main = $values->filter(fn($v) => (float) $v != 1.0)->first() ?? 0;
        $sub = $values->filter(fn($v) => (float) $v == 1.0)->sum();
        return (float) $main + (float) $sub;
    }

    private function getIncomingFrontdesks(Carbon $currentTimeIn, Carbon $currentTimeOut, int $branchId): string
    {
        // Determine current shift session (type + date) to exclude it
        $currentShiftType = $this->getShiftType($currentTimeIn);
        $currentShiftDate = $currentTimeIn->format('Y-m-d');

        // Search from current shift's start time (not end time) to catch overlapping shifts,
        // then exclude logs from the same session
        $nextLogs = ShiftLog::query()
            ->whereHas('frontdesk', fn($q) => $q->where('branch_id', $branchId))
            ->where('time_in', '>=', $currentTimeIn)
            ->orderBy('time_in', 'asc')
            ->with('frontdesk:id,name')
            ->limit(10)
            ->get()
            ->reject(fn($l) => $this->getShiftType($l->time_in) === $currentShiftType
                             && $l->time_in->format('Y-m-d') === $currentShiftDate);

        if ($nextLogs->isEmpty()) {
            return '—';
        }

        // Get the first group (same shift type + date)
        $firstLog = $nextLogs->first();
        $shiftType = $this->getShiftType($firstLog->time_in);
        $shiftDate = $firstLog->time_in->format('Y-m-d');

        return $nextLogs
            ->filter(fn($l) => $this->getShiftType($l->time_in) === $shiftType && $l->time_in->format('Y-m-d') === $shiftDate)
            ->map(fn($l) => $l->frontdesk?->name)
            ->filter()
            ->unique()
            ->implode(', ') ?: '—';
    }

    private function getForwardedData(Carbon $shiftTimeIn, int $branchId): array
    {
        // Guests who checked in BEFORE this shift and are still occupying
        $forwardedGuests = CheckinDetail::query()
            ->with(['guest', 'room.type'])
            ->whereHas('room', fn($q) => $q->where('branch_id', $branchId))
            ->where('check_in_at', '<', $shiftTimeIn)
            ->where(function ($q) use ($shiftTimeIn) {
                $q->whereNull('check_out_at')
                  ->orWhere('check_out_at', '>=', $shiftTimeIn);
            })
            ->get();

        $forwardedIds = $forwardedGuests->pluck('id')->toArray();

        if (empty($forwardedIds)) {
            return [
                'room_count' => 0,
                'room_amount' => 0,
                'room_deposit' => 0,
                'guest_deposit' => 0,
                'guest_deposit_count' => 0,
            ];
        }

        $allTransactions = Transaction::whereIn('checkin_detail_id', $forwardedIds)->get();

        // Room charges from forwarded guests
        $roomCharges = $allTransactions->where('transaction_type_id', 1);
        $roomAmount = (float) $roomCharges->sum('payable_amount');

        // Room deposits (key/remote)
        $roomDeposit = (float) $allTransactions
            ->where('transaction_type_id', 2)
            ->filter(fn($t) => $t->remarks === 'Deposit From Check In (Room Key & TV Remote)')
            ->sum('payable_amount');

        // Guest deposits (non-room-key, before this shift)
        $guestDepositTotal = (float) $allTransactions
            ->where('transaction_type_id', 2)
            ->filter(fn($t) => $t->remarks !== 'Deposit From Check In (Room Key & TV Remote)')
            ->filter(fn($t) => $t->created_at < $shiftTimeIn)
            ->sum('payable_amount');

        // Cashouts before this shift
        $cashouts = (float) $allTransactions
            ->where('transaction_type_id', 5)
            ->filter(fn($t) => $t->created_at < $shiftTimeIn)
            ->sum('payable_amount');

        $guestDeposit = max(0, $guestDepositTotal - $cashouts);

        // Count guests with guest deposits
        $guestDepositCount = $allTransactions
            ->where('transaction_type_id', 2)
            ->filter(fn($t) => $t->remarks !== 'Deposit From Check In (Room Key & TV Remote)')
            ->filter(fn($t) => $t->created_at < $shiftTimeIn)
            ->pluck('checkin_detail_id')
            ->unique()
            ->count();

        return [
            'room_count' => $forwardedGuests->count(),
            'room_amount' => $roomAmount,
            'room_deposit' => $roomDeposit,
            'guest_deposit' => $guestDeposit,
            'guest_deposit_count' => $guestDepositCount,
        ];
    }

    private function getPreviousShiftNetSales(Carbon $currentTimeIn, int $branchId): float
    {
        // Find the previous shift session
        $prevLog = ShiftLog::query()
            ->whereHas('frontdesk', fn($q) => $q->where('branch_id', $branchId))
            ->where('time_out', '<=', $currentTimeIn)
            ->orderBy('time_out', 'desc')
            ->first();

        if (!$prevLog) {
            return 0;
        }

        // Get all logs in same session (same shift type + date)
        $shiftType = $this->getShiftType($prevLog->time_in);
        $shiftDate = $prevLog->time_in->format('Y-m-d');

        $prevLogs = ShiftLog::query()
            ->whereHas('frontdesk', fn($q) => $q->where('branch_id', $branchId))
            ->whereNotNull('time_out')
            ->get()
            ->filter(function ($l) use ($shiftType, $shiftDate) {
                return $this->getShiftType($l->time_in) === $shiftType
                    && $l->time_in->format('Y-m-d') === $shiftDate;
            });

        $prevTimeIn = $prevLogs->min('time_in');
        $prevTimeOut = $prevLogs->max('time_out');

        // Gross sales in previous shift
        $prevGross = (float) Transaction::where('branch_id', $branchId)
            ->whereBetween('created_at', [$prevTimeIn, $prevTimeOut])
            ->whereNotIn('transaction_type_id', [2, 5])
            ->sum('payable_amount');

        // Expenses in previous shift
        $prevExpenses = (float) Expense::whereBetween('created_at', [$prevTimeIn, $prevTimeOut])->sum('amount');

        return $prevGross - $prevExpenses;
    }

    private function getShiftType(Carbon $timeIn): string
    {
        $hour = $timeIn->hour;
        return ($hour >= 6 && $hour < 20) ? 'AM' : 'PM';
    }

    private function loadAvailableShiftSessions(): void
    {
        $shiftLogs = ShiftLog::query()
            ->where('branch_id', auth()->user()->branch_id)
            ->whereNotNull('time_out')
            ->with('frontdesk:id,name')
            ->orderBy('time_in', 'asc')
            ->get();

        $sessions = [];
        foreach ($shiftLogs as $log) {
            $shiftType = $this->getShiftType($log->time_in);
            $shiftDate = $log->time_in->format('Y-m-d');
            $key = $shiftType . '_' . $shiftDate;

            if (!isset($sessions[$key])) {
                $sessions[$key] = [
                    'id' => $log->id,
                    'logs' => [],
                    'log_ids' => [],
                    'time_in' => $log->time_in,
                    'time_out' => $log->time_out,
                    'shift_type' => $shiftType,
                    'shift_date' => $shiftDate,
                    'frontdesks' => [],
                ];
            }

            $sessions[$key]['logs'][] = $log;
            $sessions[$key]['log_ids'][] = $log->id;
            $sessions[$key]['frontdesks'][] = $log->frontdesk?->name ?? 'Unknown';

            if ($log->time_in < $sessions[$key]['time_in']) {
                $sessions[$key]['time_in'] = $log->time_in;
            }
            if ($log->time_out > $sessions[$key]['time_out']) {
                $sessions[$key]['time_out'] = $log->time_out;
            }
        }

        $this->availableShiftSessions = collect($sessions)
            ->sortBy('time_in')
            ->map(function ($s) {
                $frontdeskNames = implode(', ', array_unique($s['frontdesks']));

                return [
                    'id' => $s['log_ids'][0],
                    'log_ids' => $s['log_ids'],
                    'label' => $s['shift_type'] . ' ' . $s['time_in']->format('M j')
                             . ' - ' . $frontdeskNames
                             . ' (' . $s['time_in']->format('g:i A') . ' - ' . $s['time_out']->format('g:i A') . ')',
                    'frontdesks' => $frontdeskNames,
                    'time_in' => $s['time_in'],
                    'time_out' => $s['time_out'],
                    'time_in_formatted' => $s['time_in']->format('F d, Y g:i A'),
                    'time_out_formatted' => $s['time_out']->format('F d, Y g:i A'),
                ];
            })
            ->values()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.back-office.frontdesk-report-v2');
    }
}
