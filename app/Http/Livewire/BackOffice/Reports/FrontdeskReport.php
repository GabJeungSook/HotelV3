<?php

namespace App\Http\Livewire\BackOffice\Reports;

use App\Models\ShiftLog;
use Carbon\Carbon;
use Livewire\Component;

class FrontdeskReport extends Component
{
    public $shift = '';
    public $date = '';

    public function mount()
    {
        $this->date = now()->toDateString();
        $this->shift = "AM";
    }

    public function resetFilters()
    {
        $this->reset(['shift', 'date']);
        $this->date = now()->toDateString();
    }

    public function getShiftSummaryProperty()
    {
        $query = ShiftLog::query()
            ->with(['frontdesk']);

        if ($this->shift) {
            $query->where('shift', $this->shift);
        }

        if ($this->date) {
            $query->whereDate('time_out', $this->date);
        }

        $logs = $query->orderBy('time_in')->get();

        if ($logs->isEmpty()) {
            return [
                'frontdesk_outgoing' => '-',
                'shift_open' => '-',
                'shift_closed' => '-',
            ];
        }

        $closedLogs = $logs->filter(fn ($log) => !empty($log->time_out));

        $outgoingNames = $closedLogs
            ->pluck('frontdesk.name')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $firstLog = $logs->sortBy('time_in')->first();
        $latestClosedLog = $closedLogs->sortByDesc('time_out')->first();

        return [
            'frontdesk_outgoing' => $this->formatNames($outgoingNames),

            'shift_open' => $firstLog?->time_in
                ? Carbon::parse($firstLog->time_in)->format('M d, Y h:i A')
                : '-',

            'shift_closed' => $latestClosedLog?->time_out
                ? Carbon::parse($latestClosedLog->time_out)->format('M d, Y h:i A')
                : '-',
        ];
    }

    protected function formatNames(array $names): string
    {
        $names = array_values(array_filter($names));

        if (count($names) === 0) {
            return '-';
        }

        if (count($names) === 1) {
            return $names[0];
        }

        if (count($names) === 2) {
            return $names[0] . ' & ' . $names[1];
        }

        $last = array_pop($names);

        return implode(', ', $names) . ' & ' . $last;
    }

    public function getCashDrawerRowsProperty()
{
    /*
    |--------------------------------------------------------------------------
    | Current shift logs (based on filters)
    |--------------------------------------------------------------------------
    */
    $currentShiftLogs = ShiftLog::query()
        ->when($this->shift, fn ($q) => $q->where('shift', $this->shift))
        ->when($this->date, fn ($q) => $q->whereDate('time_out', $this->date))
        ->orderBy('time_in')
        ->get();

    if ($currentShiftLogs->isEmpty()) {
        return [];
    }

    $currentShiftLogIds = $currentShiftLogs->pluck('id')->all();
    $currentShiftStart = optional($currentShiftLogs->sortBy('time_in')->first())->time_in;

    /*
    |--------------------------------------------------------------------------
    | Current shift totals
    |--------------------------------------------------------------------------
    */
    $openingCash = (float) (optional($currentShiftLogs->first())->beginning_cash ?? 0);

    $expenses = (float) $currentShiftLogs->sum(function ($log) {
        return (float) ($log->total_expenses ?? 0);
    });

    $remittances = (float) $currentShiftLogs->sum(function ($log) {
        return (float) ($log->total_remittances ?? 0);
    });

    /*
    |--------------------------------------------------------------------------
    | Current shift deposit transactions
    |--------------------------------------------------------------------------
    */
    $currentDepositTransactions = \App\Models\Transaction::query()
        ->whereIn('shift_log_id', $currentShiftLogIds)
        ->where('transaction_type_id', 2);

    $keyRemoteDepositAmount = (float) (clone $currentDepositTransactions)
        ->where('remarks', 'Deposit From Check In (Room Key & TV Remote)')
        ->sum('payable_amount');

    $otherDepositsAmount = (float) (clone $currentDepositTransactions)
        ->where(function ($q) {
            $q->whereNull('remarks')
              ->orWhere('remarks', '!=', 'Deposit From Check In (Room Key & TV Remote)');
        })
        ->sum('payable_amount');

    /*
    |--------------------------------------------------------------------------
    | Strict previous shift batch
    |--------------------------------------------------------------------------
    | Find the latest time_out before the current shift started,
    | then get all shift logs with that exact time_out.
    |--------------------------------------------------------------------------
    */
    $previousShiftEnd = ShiftLog::query()
        ->whereNotNull('time_out')
        ->where('time_out', '<', $currentShiftStart)
        ->max('time_out');

    $previousShiftLogIds = [];

    if ($previousShiftEnd) {
        $previousShiftLogIds = ShiftLog::query()
            ->where('time_out', $previousShiftEnd)
            ->pluck('id')
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Previous shift forwarded deposit transactions
    |--------------------------------------------------------------------------
    | Only from the immediate previous shift
    | Only guests not yet checked out
    |--------------------------------------------------------------------------
    */
    $keyRemoteDepositForwarded = 0;
    $otherDepositsForwarded = 0;

    if (!empty($previousShiftLogIds)) {
        $previousForwardedTransactions = \App\Models\Transaction::query()
            ->whereIn('shift_log_id', $previousShiftLogIds)
            ->where('transaction_type_id', 2)
            ->whereHas('checkin_detail', function ($q) {
                $q->where('is_check_out', false);
            });

        $keyRemoteDepositForwarded = (float) (clone $previousForwardedTransactions)
            ->where('remarks', 'Deposit From Check In (Room Key & TV Remote)')
            ->sum('payable_amount');

        $otherDepositsForwarded = (float) (clone $previousForwardedTransactions)
            ->where(function ($q) {
                $q->whereNull('remarks')
                  ->orWhere('remarks', '!=', 'Deposit From Check In (Room Key & TV Remote)');
            })
            ->sum('payable_amount');
    }

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
            'amount' => $keyRemoteDepositAmount,
            'forwarded_amount' => $keyRemoteDepositForwarded,
            'total_amount' => $keyRemoteDepositAmount + $keyRemoteDepositForwarded,
            'remarks' => 'Deposit From Check In (Room Key & TV Remote)',
        ],
        [
            'description' => 'Other Deposits',
            'amount' => $otherDepositsAmount,
            'forwarded_amount' => $otherDepositsForwarded,
            'total_amount' => $otherDepositsAmount + $otherDepositsForwarded,
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

    public function render()
    {
        return view('livewire.back-office.reports.frontdesk-report', [
            'shiftSummary' => $this->shiftSummary,
            'cashDrawerRows' => $this->cashDrawerRows,
        ]);
    }
}