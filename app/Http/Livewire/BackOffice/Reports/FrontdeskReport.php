<?php

namespace App\Http\Livewire\BackOffice\Reports;

use App\Models\ShiftLog;
use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Component;

class FrontdeskReport extends Component
{
    public $shift = '';
    public $date = '';

    public function mount()
    {
        $this->date = now()->toDateString();
        $this->shift = 'PM';
    }

    public function resetFilters()
    {
        $this->reset(['shift', 'date']);
        $this->date = now()->toDateString();
    }

    /*
    |--------------------------------------------------------------------------
    | Shift Summary
    |--------------------------------------------------------------------------
    */
    public function getShiftSummaryProperty()
    {
        $logs = ShiftLog::query()
            ->where('branch_id', auth()->user()->branch_id)
            ->with('frontdesk')
            ->where('shift', $this->shift)
            ->whereDate('time_out', $this->date)
            ->orderBy('time_in')
            ->get();

        if ($logs->isEmpty()) {
            return [
                'frontdesk_outgoing' => '-',
                'shift_open' => '-',
                'shift_closed' => '-',
            ];
        }

        $outgoingNames = $logs
            ->pluck('frontdesk.name')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $firstLog = $logs->first();
        $lastLog = $logs->sortByDesc('time_out')->first();

        return [
            'frontdesk_outgoing' => $this->formatNames($outgoingNames),

            'shift_open' => $firstLog?->time_in
                ? Carbon::parse($firstLog->time_in)->format('M d, Y h:i A')
                : '-',

            'shift_closed' => $lastLog?->time_out
                ? Carbon::parse($lastLog->time_out)->format('M d, Y h:i A')
                : '-',
        ];
    }

    protected function formatNames(array $names): string
    {
        $names = array_values(array_filter($names));

        if (count($names) === 0) return '-';
        if (count($names) === 1) return $names[0];
        if (count($names) === 2) return $names[0].' & '.$names[1];

        $last = array_pop($names);

        return implode(', ', $names).' & '.$last;
    }

    /*
    |--------------------------------------------------------------------------
    | Cash Drawer Table
    |--------------------------------------------------------------------------
    */
    public function getCashDrawerRowsProperty()
    {
        /*
        |-------------------------------------------------------------
        | Current shift logs
        |-------------------------------------------------------------
        */
        $shiftLogs = ShiftLog::query()
            ->where('shift', $this->shift)
            ->whereDate('time_out', $this->date)
            ->get();

        if ($shiftLogs->isEmpty()) {
            return [];
        }

        $shiftLogIds = $shiftLogs->pluck('id');

        /*
        |-------------------------------------------------------------
        | Determine previous shift
        |-------------------------------------------------------------
        */
        $previousShift = $this->shift === 'AM' ? 'PM' : 'AM';

        /*
        |-------------------------------------------------------------
        | Opening Cash
        |-------------------------------------------------------------
        */
        $openingCash = (float) ($shiftLogs->first()->beginning_cash ?? 0);

        /*
        |-------------------------------------------------------------
        | Expenses & Remittances
        |-------------------------------------------------------------
        */
        $expenses = (float) $shiftLogs->sum('total_expenses');
        $remittances = (float) $shiftLogs->sum('total_remittances');

        /*
        |-------------------------------------------------------------
        | Current Shift Deposits
        |-------------------------------------------------------------
        */
        $currentTransactions = Transaction::query()
            ->whereIn('shift_log_id', $shiftLogIds)
            ->where('transaction_type_id', 2)
            ->where('shift', $this->shift);

        $keyRemoteDeposit = (clone $currentTransactions)
            ->where('remarks', 'Deposit From Check In (Room Key & TV Remote)')
            ->sum('payable_amount');

        $otherDeposits = (clone $currentTransactions)
            ->where(function ($q) {
                $q->whereNull('remarks')
                  ->orWhere('remarks', '!=', 'Deposit From Check In (Room Key & TV Remote)');
            })
            ->sum('payable_amount');

        /*
        |-------------------------------------------------------------
        | Forwarded Deposits (previous shift guests still checked in)
        |-------------------------------------------------------------
        */
        $forwardedTransactions = Transaction::query()
            ->where('transaction_type_id', 2)
            ->where('shift', $previousShift)
            ->whereHas('checkin_details', function ($q) {
                $q->where('is_check_out', false);
            });

        $keyRemoteForwarded = (clone $forwardedTransactions)
            ->where('remarks', 'Deposit From Check In (Room Key & TV Remote)')
            ->sum('payable_amount');

        $otherForwarded = (clone $forwardedTransactions)
            ->where(function ($q) {
                $q->whereNull('remarks')
                  ->orWhere('remarks', '!=', 'Deposit From Check In (Room Key & TV Remote)');
            })
            ->sum('payable_amount');

        return [
            [
                'description' => 'Opening Cash',
                'amount' => $openingCash,
                'forwarded_amount' => null,
                'total_amount' => $openingCash,
                'remarks' => '',
            ],
            [
                'description' => 'Key / Remote Deposit',
                'amount' => $keyRemoteDeposit,
                'forwarded_amount' => $keyRemoteForwarded,
                'total_amount' => $keyRemoteDeposit + $keyRemoteForwarded,
                'remarks' => 'Deposit From Check In (Room Key & TV Remote)',
            ],
            [
                'description' => 'Other Deposits',
                'amount' => $otherDeposits,
                'forwarded_amount' => $otherForwarded,
                'total_amount' => $otherDeposits + $otherForwarded,
                'remarks' => 'Other deposits',
            ],
            [
                'description' => 'Expenses',
                'amount' => $expenses,
                'forwarded_amount' => null,
                'total_amount' => $expenses,
                'remarks' => '',
            ],
            [
                'description' => 'Remittances',
                'amount' => $remittances,
                'forwarded_amount' => null,
                'total_amount' => $remittances,
                'remarks' => '',
            ],
        ];
    }

    public function getFrontdeskOperationRowsProperty()
{
    $shiftLogs = ShiftLog::query()
        ->where('branch_id', auth()->user()->branch_id)
        ->where('shift', $this->shift)
        ->whereDate('time_out', $this->date)
        ->get();

    if ($shiftLogs->isEmpty()) {
        return [];
    }

    $shiftLogIds = $shiftLogs->pluck('id');

    $baseTransactions = \App\Models\Transaction::query()
        ->whereIn('shift_log_id', $shiftLogIds)
        ->where('shift', $this->shift);

    $definitions = [
        [
            'description' => 'New Check-In',
            'transaction_type_id' => 1,
            'remarks' => '',
        ],
        [
            'description' => 'Extension',
            'transaction_type_id' => 6,
            'remarks' => '',
        ],
        [
            'description' => 'Damage Charges',
            'transaction_type_id' => 4,
            'remarks' => '',
        ],
        [
            'description' => 'Food & Beverages',
            'transaction_type_id' => 9,
            'remarks' => '',
        ],
        [
            'description' => 'Amenities',
            'transaction_type_id' => 8,
            'remarks' => '',
        ],
        [
            'description' => 'Transfer Room',
            'transaction_type_id' => 7,
            'remarks' => '',
        ],
    ];

    return collect($definitions)->map(function ($item) use ($baseTransactions) {
        $query = (clone $baseTransactions)
            ->where('transaction_type_id', $item['transaction_type_id']);

        return [
            'description' => $item['description'],
            'total_rooms' => (clone $query)->count(),
            'amount' => (float) (clone $query)->sum('payable_amount'),
            'remarks' => $item['remarks'],
        ];
    })->all();
}

public function getRoomActivityRowsProperty()
{
    $shiftLogs = ShiftLog::query()
        ->where('branch_id', auth()->user()->branch_id)
        ->where('shift', $this->shift)
        ->whereDate('time_out', $this->date)
        ->get();

    if ($shiftLogs->isEmpty()) {
        return [];
    }

    $shiftLogIds = $shiftLogs->pluck('id');
    $previousShift = $this->shift === 'AM' ? 'PM' : 'AM';
    $formattedShiftDate = Carbon::parse($this->date)->format('F j, Y');

    /*
    |--------------------------------------------------------------------------
    | Check-In (Forwarded)
    |--------------------------------------------------------------------------
    */
    $forwardedCheckIns = \App\Models\Transaction::query()
        ->where('transaction_type_id', 1)
        ->where('shift', $previousShift)
        ->whereHas('checkin_details', function ($q) {
            $q->where('is_check_out', false);
        });

    /*
    |--------------------------------------------------------------------------
    | Check-In (Current)
    |--------------------------------------------------------------------------
    */
    $currentCheckIns = \App\Models\Transaction::query()
        ->whereIn('shift_log_id', $shiftLogIds)
        ->where('shift', $this->shift)
        ->where('transaction_type_id', 1);

    /*
    |--------------------------------------------------------------------------
    | Check-Out (Current)
    |--------------------------------------------------------------------------
    */
  $currentCheckOuts = \App\Models\CheckinDetail::query()
        ->whereHas('checkOutGuestReports', function ($q) use ($formattedShiftDate) {
            $q->where('shift', $this->shift)
              ->where('shift_date', $formattedShiftDate);
        });

    return [
        [
            'description' => 'Check-In (Forwarded)',
            'number_of_rooms' => (clone $forwardedCheckIns)->count(),
            'amount' => (float) (clone $forwardedCheckIns)->sum('payable_amount'),
        ],
        [
            'description' => 'Check-In (Current)',
            'number_of_rooms' => (clone $currentCheckIns)->count(),
            'amount' => (float) (clone $currentCheckIns)->sum('payable_amount'),
        ],
        [
            'description' => 'Check-Out',
            'number_of_rooms' => (clone $currentCheckOuts)->count(),
            'amount' => 0,
        ],
    ];
}

public function getCashReconciliationProperty()
{
    $shiftLogs = ShiftLog::query()
        ->where('branch_id', auth()->user()->branch_id)
        ->where('shift', $this->shift)
        ->whereDate('time_out', $this->date)
        ->get();

    if ($shiftLogs->isEmpty()) {
        return [
            'expected_cash' => 0,
            'actual_cash' => 0,
            'difference' => 0,
        ];
    }

    $shiftLogIds = $shiftLogs->pluck('id');

    $expectedCash = (float) \App\Models\Transaction::query()
        ->whereIn('shift_log_id', $shiftLogIds)
        ->whereNotIn('transaction_type_id', [ 5])
        ->sum('payable_amount');

    $actualCash = (float) $shiftLogs->sum(function ($log) {
        return (float) ($log->end_cash ?? 0);
    });

    return [
        'expected_cash' => $expectedCash,
        'actual_cash' => $actualCash,
        'difference' => $expectedCash - $actualCash,
    ];
}

public function getFinalSalesProperty()
{
    $shiftLogs = ShiftLog::query()
        ->where('branch_id', auth()->user()->branch_id)
        ->where('shift', $this->shift)
        ->whereDate('time_out', $this->date)
        ->get();

    if ($shiftLogs->isEmpty()) {
        return [
            'gross_sales' => 0,
            'expenses' => 0,
            'discounts' => 0,
            'net_sales' => 0,
        ];
    }

    $shiftLogIds = $shiftLogs->pluck('id');

    $currentTransactions = \App\Models\Transaction::query()
        ->whereIn('shift_log_id', $shiftLogIds)
        ->where('shift', $this->shift);

    $grossSales = (float) (clone $currentTransactions)
        ->whereNotIn('transaction_type_id', [2, 5])
        ->sum('payable_amount');

    $expenses = (float) $shiftLogs->sum(function ($log) {
        return (float) ($log->total_expenses ?? 0);
    });

    $discountCheckinDetailIds = (clone $currentTransactions)
        ->whereNotNull('checkin_detail_id')
        ->distinct()
        ->pluck('checkin_detail_id');

   $discountCheckinDetails = \App\Models\CheckinDetail::query()
    ->with('guest')
    ->whereIn('id', $discountCheckinDetailIds)
    ->get();

$discounts = (float) $discountCheckinDetails->sum(function ($checkinDetail) {
    return (float) ($checkinDetail->guest->discount_amount ?? 0);
});

    return [
        'gross_sales' => $grossSales,
        'expenses' => $expenses,
        'discounts' => $discounts,
        'net_sales' => $grossSales - $expenses - $discounts,
    ];
}

    public function render()
    {
        return view('livewire.back-office.reports.frontdesk-report', [
            'shiftSummary' => $this->shiftSummary,
            'cashDrawerRows' => $this->cashDrawerRows,
            'frontdeskOperationRows' => $this->frontdeskOperationRows,
            'roomActivityRows' => $this->roomActivityRows,
            'cashReconciliation' => $this->cashReconciliation,
            'finalSales' => $this->finalSales,
        ]);
    }
}