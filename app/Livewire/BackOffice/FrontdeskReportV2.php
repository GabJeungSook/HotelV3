<?php

namespace App\Livewire\BackOffice;

use Livewire\Component;
use App\Models\ShiftSession;
use App\Models\ShiftSnapshot;
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

        $session = ShiftSession::with(['snapshot', 'members.user'])->find($this->selectedShiftLogId);
        if (!$session || !$session->snapshot) {
            $this->reportData = [];
            return;
        }

        $snapshot = $session->snapshot;
        $branchId = auth()->user()->branch_id;

        // Outgoing frontdesk names from this session's members
        $outgoingNames = $session->members->pluck('user.name')->filter()->unique()->implode(', ');

        // Incoming frontdesk: find the next closed session after this one
        $incomingNames = $this->getIncomingFrontdesks($session);

        // Previous shift snapshot for cash drawer section
        $prevSnapshot = $this->getPreviousSnapshot($session);

        // Cash Drawer data from previous snapshot
        $prevNetSales = $prevSnapshot ? (float) $prevSnapshot->net_sales : 0;
        $prevKeyDeposit = $prevSnapshot ? (float) $prevSnapshot->remaining_room_deposit_amount : 0;
        $prevGuestDeposit = $prevSnapshot ? (float) $prevSnapshot->remaining_guest_deposit_amount : 0;
        $prevForwardedBalance = $prevSnapshot ? (float) $prevSnapshot->forwarded_balance : 0;
        $hasPrevious = $prevSnapshot !== null;

        $openingCash = (float) $snapshot->opening_cash;
        $expectedReceived = $prevNetSales + $prevKeyDeposit + $prevGuestDeposit + $prevForwardedBalance;
        $cashDifference = $expectedReceived - $openingCash;

        // Miscellaneous breakdown
        $amenitiesCount = (int) $snapshot->amenity_count;
        $amenitiesAmount = (float) $snapshot->amenity_amount;
        $damagesCount = (int) $snapshot->damage_count;
        $damagesAmount = (float) $snapshot->damage_amount;
        $unclaimedCount = (int) $snapshot->unclaimed_count;
        $unclaimedAmount = (float) $snapshot->unclaimed_amount;
        $miscCount = $amenitiesCount + $damagesCount + $unclaimedCount;
        $miscAmount = $amenitiesAmount + $damagesAmount + $unclaimedAmount;

        // Sales Summary
        $salesSummary = [
            'new_checkin' => ['count' => (int) $snapshot->checkin_count, 'amount' => (float) $snapshot->checkin_amount],
            'extension' => ['count' => (int) $snapshot->extension_count, 'amount' => (float) $snapshot->extension_amount],
            'transfer' => ['count' => (int) $snapshot->transfer_count, 'amount' => (float) $snapshot->transfer_amount],
            'miscellaneous' => [
                'count' => $miscCount,
                'amount' => $miscAmount,
                'breakdown' => [
                    'amenities' => ['count' => $amenitiesCount, 'amount' => $amenitiesAmount],
                    'damages' => ['count' => $damagesCount, 'amount' => $damagesAmount],
                    'unclaimed' => ['count' => $unclaimedCount, 'amount' => $unclaimedAmount],
                ],
            ],
            'food' => ['count' => (int) $snapshot->food_count, 'amount' => (float) $snapshot->food_amount],
            'drink' => ['count' => 0, 'amount' => 0],
            'others' => ['count' => 0, 'amount' => 0],
        ];
        $salesSummary['total'] = [
            'count' => collect($salesSummary)->sum('count'),
            'amount' => collect($salesSummary)->sum('amount'),
        ];

        // Room Summary
        $roomSummary = [
            'forwarded_prev' => [
                'count' => (int) $snapshot->forwarded_room_count,
                'amount' => (float) $snapshot->forwarded_room_amount,
            ],
            'current_shift' => [
                'count' => (int) $snapshot->current_room_count,
                'amount' => (float) $snapshot->current_room_amount,
            ],
        ];

        // Guest Deposit Summary
        $fwdGuestDepCount = (int) $snapshot->fwd_guest_deposit_count;
        $fwdGuestDepAmount = (float) $snapshot->fwd_guest_deposit_amount;
        $currentGuestDepCount = (int) $snapshot->guest_deposit_collected > 0 ? 1 : 0;
        // For current shift guest deposit count, derive from snapshot fields
        // guest_deposit_collected is the total collected this shift
        $currentGuestDepAmount = (float) $snapshot->guest_deposit_collected;

        // Net Sales
        $grossSales = (float) $snapshot->gross_sales;
        $expensesAmount = (float) $snapshot->expenses_amount;
        $netSales = (float) $snapshot->net_sales;

        // Forwarded balance
        $forwardedBalance = (float) $snapshot->forwarded_balance;

        // Remittance
        $remittanceAmount = (float) $snapshot->remittance_amount;

        // Cash Reconciliation
        $expectedCash = (float) $snapshot->expected_cash;
        $actualCash = (float) $snapshot->actual_cash;
        $difference = (float) $snapshot->cash_difference;

        $this->reportData = [
            'frontdesk_outgoing' => $outgoingNames ?: '—',
            'frontdesk_incoming' => $incomingNames ?: '—',
            'shift_opened' => $snapshot->shift_opened_at
                ? $snapshot->shift_opened_at->format('F d, Y g:i A')
                : ($session->opened_at ? $session->opened_at->format('F d, Y g:i A') : '—'),
            'shift_closed' => $snapshot->shift_closed_at
                ? $snapshot->shift_closed_at->format('F d, Y g:i A')
                : ($session->closed_at ? $session->closed_at->format('F d, Y g:i A') : '—'),

            'cash_drawer' => [
                'net_sales_prev' => $prevNetSales,
                'key_deposit_prev' => $prevKeyDeposit,
                'guest_deposit_prev' => $prevGuestDeposit,
                'forwarded_balance' => $forwardedBalance,
                'cash_received' => $openingCash,
                'expected_received' => $expectedReceived,
                'cash_difference' => $cashDifference,
                'has_previous' => $hasPrevious,
            ],

            'sales_summary' => $salesSummary,

            'room_summary' => $roomSummary,
            'room_summary_subtotal' => [
                'count' => $roomSummary['forwarded_prev']['count'] + $roomSummary['current_shift']['count'],
                'amount' => $roomSummary['forwarded_prev']['amount'] + $roomSummary['current_shift']['amount'],
            ],

            'guest_deposit_summary' => [
                'forwarded_prev' => [
                    'count' => $fwdGuestDepCount,
                    'amount' => $fwdGuestDepAmount,
                ],
                'current_shift' => [
                    'count' => $currentGuestDepCount,
                    'amount' => $currentGuestDepAmount,
                ],
            ],
            'guest_deposit_subtotal' => [
                'count' => $fwdGuestDepCount + $currentGuestDepCount,
                'amount' => $fwdGuestDepAmount + $currentGuestDepAmount,
            ],

            'checkout_summary' => [
                'count' => (int) $snapshot->checkout_count,
                'amount' => (float) $snapshot->checkout_room_deposit,
            ],

            'forwarded_deposit_summary' => [
                'room_deposit' => [
                    'count' => (int) $snapshot->remaining_room_deposit_count,
                    'amount' => (float) $snapshot->remaining_room_deposit_amount,
                ],
                'guest_deposit' => [
                    'count' => (int) $snapshot->remaining_guest_deposit_count,
                    'amount' => (float) $snapshot->remaining_guest_deposit_amount,
                ],
            ],

            'final_sales' => [
                'gross_sales' => $grossSales,
                'refund' => 0,
                'expenses' => $expensesAmount,
                'discounts' => 0,
                'net_sales' => $netSales,
            ],

            'cash_position' => [
                'opening_cash' => $openingCash,
                'forwarded_balance' => $forwardedBalance,
                'net_sales' => $netSales,
                'remittance' => $remittanceAmount,
            ],

            'cash_reconciliation' => [
                'expected_cash' => $expectedCash,
                'actual_cash' => $actualCash,
                'difference' => $difference,
            ],
        ];
    }

    private function getIncomingFrontdesks(ShiftSession $currentSession): string
    {
        $nextSession = ShiftSession::where('branch_id', $currentSession->branch_id)
            ->where('status', 'closed')
            ->where('opened_at', '>', $currentSession->opened_at)
            ->with('members.user')
            ->orderBy('opened_at', 'asc')
            ->first();

        if (!$nextSession) {
            return '—';
        }

        return $nextSession->members
            ->pluck('user.name')
            ->filter()
            ->unique()
            ->implode(', ') ?: '—';
    }

    private function getPreviousSnapshot(ShiftSession $currentSession): ?ShiftSnapshot
    {
        $prevSession = ShiftSession::where('branch_id', $currentSession->branch_id)
            ->where('cash_drawer_id', $currentSession->cash_drawer_id)
            ->where('status', 'closed')
            ->where('opened_at', '<', $currentSession->opened_at)
            ->with('snapshot')
            ->orderBy('opened_at', 'desc')
            ->first();

        return $prevSession?->snapshot;
    }

    private function loadAvailableShiftSessions(): void
    {
        $sessions = ShiftSession::where('branch_id', auth()->user()->branch_id)
            ->where('status', 'closed')
            ->with('members.user')
            ->orderBy('opened_at')
            ->get();

        $this->availableShiftSessions = $sessions->map(function ($session) {
            $memberNames = $session->members->pluck('user.name')->filter()->unique()->implode(', ');
            $shiftType = $session->shift_type ?? $this->getShiftType($session->opened_at);

            return [
                'id' => $session->id,
                'label' => strtoupper($shiftType) . ' ' . $session->opened_at->format('M j')
                         . ' - ' . ($memberNames ?: 'Unknown')
                         . ' (' . $session->opened_at->format('g:i A') . ' - ' . ($session->closed_at ? $session->closed_at->format('g:i A') : '?') . ')',
            ];
        })->values()->toArray();
    }

    private function getShiftType(Carbon $timeIn): string
    {
        $hour = $timeIn->hour;
        return ($hour >= 6 && $hour < 20) ? 'AM' : 'PM';
    }

    public function render()
    {
        return view('livewire.back-office.frontdesk-report-v2');
    }
}
