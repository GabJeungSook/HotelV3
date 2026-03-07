<?php

namespace App\Http\Livewire\BackOffice;

use Livewire\Component;
use App\Models\Frontdesk;
use App\Models\CheckinDetail;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReport extends Component
{
    public string $type = 'Overall Sales';

    // toggles (still supported)
    public bool $showExtend = true;
    public bool $showAmenities = true;
    public bool $showFood = true;
    public bool $showDamages = true;
    public bool $showDeposits = true;
    public bool $showTransfer = true;

    public float $totalSales = 0;

    public $date_from;
    public $date_to;
    public $shift;
    public $frontdesk;

    public $transactions; // collection of grouped by room
    public array $groups = []; // NEW: for uniform grouped layout

    // grouped transaction buckets
    public array $salesRooms = [];     // visible sales rows for blade
    public array $salesRoomsRaw = [];  // master rows before toggle filtering

    public array $summary = [];

    public $expensesRows;
    public float $expensesTotal = 0;

    public array $roomSummary = []; // for the ROOM SUMMARY table

    public function mount()
    {
       $this->summary = [];
    $this->transactions = collect();
    $this->totalSales = 0;
    $this->groups = [];
    $this->salesRooms = [];
    $this->salesRoomsRaw = [];
    $this->expensesRows = collect();
    $this->expensesTotal = 0;
    $this->roomSummary = [];

    $this->generateReport();
    $this->summary = $this->buildSummary();
    $this->buildExpensesSummary();
    $this->buildRoomSummary();
    }

    /**
     * ✅ Fix totals when toggles change (NO DB hit)
     */
    // public function updatedShowExtend()   { $this->recomputeTotals(); }
    // public function updatedShowAmenities(){ $this->recomputeTotals(); }
    // public function updatedShowFood()     { $this->recomputeTotals(); }
    // public function updatedShowDamages()  { $this->recomputeTotals(); }
    // public function updatedShowTransfer() { $this->recomputeTotals(); }

    // private function recomputeTotals(): void
    // {
    //     $newTotal = 0;

    //     foreach ($this->groups as $gIndex => $group) {
    //         foreach ($group['rows'] as $rIndex => $row) {
    //             $total =
    //                 (float) $row['room_amount']
    //                 + ($this->showExtend ? (float) $row['extend_amount'] : 0)
    //                 + ($this->showAmenities ? (float) $row['amenities_amount'] : 0)
    //                 + ($this->showFood ? (float) $row['food_amount'] : 0)
    //                 + ($this->showDamages ? (float) $row['damages_amount'] : 0)
    //                 + ($this->showTransfer ? (float) $row['transfer_amount'] : 0);

    //             $this->groups[$gIndex]['rows'][$rIndex]['total'] = $total;
    //             $newTotal += $total;
    //         }
    //     }

    //     $this->totalSales = $newTotal;
    // }

public function updatedShowExtend()   { $this->recomputeTotals(); }
public function updatedShowAmenities(){ $this->recomputeTotals(); }
public function updatedShowFood()     { $this->recomputeTotals(); }
public function updatedShowDamages()  { $this->recomputeTotals(); }
public function updatedShowTransfer() { $this->recomputeTotals(); }

private function recomputeTotals(): void
{
    $this->applySalesVisibilityAndTotals();
}

private function applySalesVisibilityAndTotals(): void
{
    $rooms = [];
    $grandTotal = 0;

    foreach ($this->salesRoomsRaw as $room) {
        $visibleRows = collect($room['rows'])
            ->filter(function ($row) {
                return match ($row['row_type']) {
                    'room_amount' => true,
                    'extend'      => $this->showExtend,
                    'amenities'   => $this->showAmenities,
                    'food'        => $this->showFood,
                    'damages'     => $this->showDamages,
                    'transfer'    => $this->showTransfer,
                    default       => true,
                };
            })
            ->values()
            ->all();

        if (count($visibleRows) > 0) {
            foreach ($visibleRows as $idx => $row) {
                $lineTotal =
                    (float) $row['room_amount']
                    + (float) $row['extend_amount']
                    + (float) $row['amenities_amount']
                    + (float) $row['food_amount']
                    + (float) $row['damages_amount']
                    + (float) $row['transfer_amount'];

                $visibleRows[$idx]['total'] = $lineTotal;
                $grandTotal += $lineTotal;
            }

            $room['rows'] = $visibleRows;
            $room['rowspan'] = count($visibleRows);
            $rooms[] = $room;
        }
    }

    $this->salesRooms = $rooms;
    $this->totalSales = $grandTotal;
}

private function applySalesTypeFilter($query, string $column = 'check_out_at')
{
    switch ($this->type) {
        case 'Daily':
            $query->whereDate($column, now()->toDateString());
            break;

        case 'Weekly':
            $query->whereBetween($column, [
                now()->copy()->startOfWeek(),
                now()->copy()->endOfWeek(),
            ]);
            break;

        case 'Monthly':
            $query->whereMonth($column, now()->month)
                  ->whereYear($column, now()->year);
            break;

        default:
            break;
    }

    return $query;
}

    private function shiftWindow(): array
    {
        $fromDate = $this->date_from ? \Carbon\Carbon::parse($this->date_from) : now();
        $toDate   = $this->date_to ? \Carbon\Carbon::parse($this->date_to) : $fromDate;

        if (!$this->shift) {
            return [$fromDate->copy()->startOfDay(), $toDate->copy()->endOfDay()];
        }

        if ($this->shift === 'AM') {
            return [$fromDate->copy()->setTime(8, 0, 0), $toDate->copy()->setTime(20, 0, 0)];
        }

        return [$fromDate->copy()->setTime(20, 0, 0), $toDate->copy()->addDay()->setTime(8, 0, 0)];
    }

    /**
     * ✅ SUMMARY now uses checkin_detail_id (NOT room.latestCheckInDetail)
     * ✅ Guest per accommodation fixed to true guest counts per room type
     * ✅ Unoccupied rooms: correct shift logic + default 0 for all room types
     */
    private function buildSummary(): array
    {
        // Base tx scope for summary: uses checkinDetail filters and check_out_at
        $tx = Transaction::query()
            ->whereNotNull('checkin_detail_id')
            ->whereNotNull('paid_at')
            ->with(['checkin_details.room.type', 'checkin_details.guest'])
            ->whereHas('checkin_details', function ($q) {
                $q->when($this->date_from, fn($q, $d) => $q->whereDate('check_out_at', '>=', $d))
                  ->when($this->date_to, fn($q, $d) => $q->whereDate('check_out_at', '<=', $d))
                  ->when($this->frontdesk, fn($q, $f) => $q->where('frontdesk_id', $f))
                  ->whereNotNull('check_out_at');
            })
            ->when($this->shift, function ($q, $shift) {
                $q->whereHas('checkin_details', function ($q2) use ($shift) {

                    if ($shift === 'AM') {
                        $q2->whereTime(DB::raw('TIME(check_out_at)'), '>=', '08:00:00')
                           ->whereTime(DB::raw('TIME(check_out_at)'), '<',  '20:00:00');
                    }
                    if ($shift === 'PM') {
                        $q2->where(function ($sub) {
                            $sub->whereTime(DB::raw('TIME(check_out_at)'), '>=', '20:00:00')
                                ->orWhereTime(DB::raw('TIME(check_out_at)'), '<',  '08:00:00');
                        });
                    }
                });
            });

        // Stay IDs involved in this filtered set
        $checkinIds = (clone $tx)->distinct()->pluck('checkin_detail_id')->values();

        // Load stays once (room/type/guest)
        $details = CheckinDetail::query()
            ->whereIn('id', $checkinIds)
            ->with(['room.type', 'guest'])
            ->get();

        /**
         * 1) Guest per accommodation (Room Type -> unique guest count)
         *    This is now a TRUE guest metric, not room count.
         */
        $guestCounts = $details
            ->groupBy(fn($d) => $d->room?->type?->name ?? 'Unknown')
            ->map(function ($items) {
                return $items->pluck('guest_id')->filter()->unique()->count();
            });

            $allTypes = \App\Models\Type::where('branch_id', auth()->user()->branch_id)->get();

            $guestPerType = $allTypes->map(function ($type) use ($guestCounts) {
                return [
                    'label' => $type->name,
                    'value' => (int) ($guestCounts[$type->name] ?? 0),
                ];
            })->values()->all();

        /**
         * 2) Unoccupied rooms during shift (All types included, default 0)
         */
        [$start, $end] = $this->shiftWindow();

        $occupiedRoomIds = CheckinDetail::query()
            ->when($this->frontdesk, fn($q, $f) => $q->where('frontdesk_id', $f))
            ->whereHas('room', fn($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->where(function ($q) use ($start, $end) {
                $q->where('check_in_at', '<', $end)
                  ->where(function ($q2) use ($start) {
                      $q2->whereNull('check_out_at')
                         ->orWhere('check_out_at', '>', $start);
                  });
            })
            ->distinct()
            ->pluck('room_id');

        $allRooms = \App\Models\Room::query()
            ->where('branch_id', auth()->user()->branch_id)
            ->with('type')
            ->get();

        $unoccupiedRooms = $allRooms->whereNotIn('id', $occupiedRoomIds);

        $unoccupiedCounts = $unoccupiedRooms
            ->groupBy(fn($r) => $r->type?->name ?? 'Unknown')
            ->map(fn($items) => $items->count());

        $allTypes = \App\Models\Type::where('branch_id', auth()->user()->branch_id)->get();

        $unoccupiedPerType = $allTypes->map(function ($type) use ($unoccupiedCounts) {
            return [
                'label' => $type->name,
                'value' => (int) ($unoccupiedCounts[$type->name] ?? 0),
            ];
        })->values()->all();

        /**
         * 3) Under repair room (Room Type -> count)
         *    Uses damages tx within the SAME stay scope (checkin_detail_id)
         */
        $damagedRoomIds = Transaction::query()
            ->where('transaction_type_id', 4)
            ->whereNotNull('paid_at')
            ->whereIn('checkin_detail_id', $checkinIds)
            ->distinct()
            ->pluck('room_id');

        $damagedRooms = \App\Models\Room::query()
            ->where('branch_id', auth()->user()->branch_id)
            ->whereIn('id', $damagedRoomIds)
            ->with('type')
            ->get()
            ->groupBy(fn($r) => $r->type?->name ?? 'Unknown')
            ->map(fn($items) => $items->count());

        $underRepairCounts = $allTypes->map(function ($type) use ($damagedRooms) {
            return [
                'label' => $type->name,
                'value' => (int) ($damagedRooms[$type->name] ?? 0),
            ];
        })->values()->all();

        /**
         * 4) Group check-in time (fixed AM/PM buckets)
         * 5) Group check-out time (fixed AM/PM buckets, requires is_check_out=true)
         *    Now aligned with shift selection: if shift=AM, PM bucket forced 0 (and vice versa).
         */
        $checkinBuckets  = $this->twoShiftBucketsFor('check_in_at', false);
        $checkoutBuckets = $this->twoShiftBucketsFor('check_out_at', true);

        return [
            'guest_per_accommodation' => $guestPerType,
            'unoccupied_rooms'        => $unoccupiedPerType,
            'under_repair_rooms'      => $underRepairCounts,
            'group_checkin_time'      => $checkinBuckets,
            'group_checkout_time'     => $checkoutBuckets,
        ];
    }

    private function twoShiftBucketsFor(string $column, bool $requireIsCheckout = false): array
    {
        $q = CheckinDetail::query()
            ->whereHas('room', fn($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->when($requireIsCheckout, fn($q) => $q->where('is_check_out', true))
            ->when($this->date_from, fn($q, $d) => $q->whereDate($column, '>=', $d))
            ->when($this->date_to, fn($q, $d) => $q->whereDate($column, '<=', $d))
            ->when($this->frontdesk, fn($q, $f) => $q->where('frontdesk_id', $f))
            ->whereNotNull($column);

        // If a shift is selected, force the other bucket to 0
        if ($this->shift === 'AM') {
            $am = (clone $q)
                ->whereTime(DB::raw("TIME($column)"), '>=', '08:00:00')
                ->whereTime(DB::raw("TIME($column)"), '<',  '20:00:00')
                ->count();

            return [
                ['label' => '8:00 AM - 8:00 PM', 'value' => (int) $am],
                ['label' => '8:00 PM - 8:00 AM', 'value' => 0],
            ];
        }

        if ($this->shift === 'PM') {
            $pm = (clone $q)
                ->where(function ($sub) use ($column) {
                    $sub->whereTime(DB::raw("TIME($column)"), '>=', '20:00:00')
                        ->orWhereTime(DB::raw("TIME($column)"), '<',  '08:00:00');
                })
                ->count();

            return [
                ['label' => '8:00 AM - 8:00 PM', 'value' => 0],
                ['label' => '8:00 PM - 8:00 AM', 'value' => (int) $pm],
            ];
        }

        // No shift filter: compute both
        $am = (clone $q)
            ->whereTime(DB::raw("TIME($column)"), '>=', '08:00:00')
            ->whereTime(DB::raw("TIME($column)"), '<',  '20:00:00')
            ->count();

        $pm = (clone $q)
            ->where(function ($sub) use ($column) {
                $sub->whereTime(DB::raw("TIME($column)"), '>=', '20:00:00')
                    ->orWhereTime(DB::raw("TIME($column)"), '<',  '08:00:00');
            })
            ->count();

        return [
            ['label' => '8:00 AM - 8:00 PM', 'value' => (int) $am],
            ['label' => '8:00 PM - 8:00 AM', 'value' => (int) $pm],
        ];
    }

    public function render()
    {
        return view('livewire.back-office.sales-report', [
            'frontdesks' => Frontdesk::where('branch_id', auth()->user()->branch_id)->get(),
        ]);
    }

public function generateReport()
{
    $detailsQuery = CheckinDetail::query()
        ->with([
            'room.type',
            'guest',
            'rate',
            'frontdesk',
        ])
        ->whereHas('room', fn($q) => $q->where('branch_id', auth()->user()->branch_id))
        ->whereNotNull('check_out_at')
        ->when($this->date_from, fn($q, $d) => $q->whereDate('check_out_at', '>=', $d))
        ->when($this->date_to, fn($q, $d) => $q->whereDate('check_out_at', '<=', $d))
        ->when($this->frontdesk, fn($q, $f) => $q->where('frontdesk_id', $f))
        ->when($this->shift, function ($q, $shift) {
            if ($shift === 'AM') {
                $q->whereTime(DB::raw('TIME(check_out_at)'), '>=', '08:00:00')
                  ->whereTime(DB::raw('TIME(check_out_at)'), '<', '20:00:00');
            }

            if ($shift === 'PM') {
                $q->where(function ($sub) {
                    $sub->whereTime(DB::raw('TIME(check_out_at)'), '>=', '20:00:00')
                        ->orWhereTime(DB::raw('TIME(check_out_at)'), '<', '08:00:00');
                });
            }
        });

    $this->applySalesTypeFilter($detailsQuery, 'check_out_at');

    $details = $detailsQuery
        ->orderBy('room_id')
        ->orderBy('check_in_at')
        ->orderBy('check_out_at')
        ->get();

    $detailIds = $details->pluck('id')->values();

    $frontdeskMap = Frontdesk::query()
    ->where('branch_id', auth()->user()->branch_id)
    ->pluck('name', 'id');

$txRows = Transaction::query()
    ->whereIn('checkin_detail_id', $detailIds)
    ->whereNotNull('paid_at')
    ->whereIn('transaction_type_id', [4, 6, 7, 8, 9])
    ->select([
        'id',
        'checkin_detail_id',
        'transaction_type_id',
        'payable_amount',
        'assigned_frontdesk_id',
        'paid_at',
    ])
    ->orderBy('paid_at')
    ->get()
    ->map(function ($tx) use ($frontdeskMap) {
        $frontdeskId = $this->extractAssignedFrontdeskId($tx->assigned_frontdesk_id);

        $tx->resolved_frontdesk_id = $frontdeskId;
        $tx->resolved_frontdesk_name = $frontdeskId
            ? strtoupper($frontdeskMap[$frontdeskId] ?? '—')
            : '—';

        return $tx;
    })
    ->groupBy('checkin_detail_id');

    $this->salesRoomsRaw = $this->buildSalesRooms($details, $txRows);
    $this->applySalesVisibilityAndTotals();

    // leave the rest untouched
    $this->summary = $this->buildSummary();
    $this->buildExpensesSummary();
    $this->buildRoomSummary();
}

private function extractAssignedFrontdeskId($value): ?int
{
    if (blank($value)) {
        return null;
    }

    // already array-like
    if (is_array($value)) {
        return isset($value[0]) && is_numeric($value[0]) ? (int) $value[0] : null;
    }

    // JSON string like: [2,"N/A"]
    if (is_string($value)) {
        $decoded = json_decode($value, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return isset($decoded[0]) && is_numeric($decoded[0]) ? (int) $decoded[0] : null;
        }

        // fallback: grab first number from string
        if (preg_match('/\d+/', $value, $matches)) {
            return (int) $matches[0];
        }
    }

    return null;
}

private function buildSalesRooms($details, $txRowsByDetail): array
{
    $rooms = [];

    $groupedByRoom = $details
        ->groupBy('room_id')
        ->sortBy(function ($items) {
            return (int) ($items->first()?->room?->number ?? PHP_INT_MAX);
        });

    foreach ($groupedByRoom as $roomId => $roomDetails) {
        $room = $roomDetails->first()?->room;
        $roomRows = [];

        foreach ($roomDetails as $detail) {
            $guest = $detail->guest;
            $rate = $detail->rate;
            $frontdesk = $detail->frontdesk;

            $baseGuestName = strtoupper($guest?->name ?? '—');

            $roomAmount = $guest?->is_long_stay
                ? (float) ($rate?->amount ?? 0) * (int) ($guest?->number_of_days ?? 1)
                : (float) ($rate?->amount ?? 0);

            $meta = [
                'room_type'      => $room?->type?->name ?? '—',
                'guest_name'     => $baseGuestName,
                'check_in'       => $detail->check_in_at ? Carbon::parse($detail->check_in_at)->format('m-d-Y h:iA') : '—',
                'check_out'      => $detail->check_out_at ? Carbon::parse($detail->check_out_at)->format('m-d-Y h:iA') : '—',
                'initial_hrs'    => $detail->hours_stayed
                    ? ($guest?->is_long_stay
                        ? (($detail->hours_stayed * (int) ($guest?->number_of_days ?? 1)) . ' hrs')
                        : ($detail->hours_stayed . ' hrs'))
                    : '—',
                'frontdesk_name' => strtoupper($frontdesk?->name ?? '—'),
                'shift'          => $detail->check_out_at ? $this->resolveShift($detail->check_out_at) : '—',
            ];

            // Base room row
            $roomRows[] = [
                'detail_id'         => $detail->id,
                'row_type'          => 'room_amount',
                'number'            => $room?->number ?? '—',
                'room_type'         => $meta['room_type'],
                'guest_name'        => $meta['guest_name'],
                'check_in'          => $meta['check_in'],
                'check_out'         => $meta['check_out'],
                'initial_hrs'       => $meta['initial_hrs'],
                'room_amount'       => $roomAmount,
                'extend_amount'     => 0,
                'amenities_amount'  => 0,
                'food_amount'       => 0,
                'damages_amount'    => 0,
                'transfer_amount'   => 0,
                'frontdesk_name'    => $meta['frontdesk_name'],
                'shift'             => $meta['shift'],
                'total'             => $roomAmount,
            ];

            $detailTxs = collect($txRowsByDetail[$detail->id] ?? []);

            // EXTEND special rule
            $extendTxs = $detailTxs
                ->where('transaction_type_id', 6)
                ->values();

            if ($extendTxs->isNotEmpty()) {
                $extendByFrontdesk = $extendTxs
                    ->groupBy(fn($tx) => $tx->user_id ?: 'unknown')
                    ->map(function ($group) {
                        $first = $group->first();

                        return [
                            'user_id'        => $first->user_id,
                            'frontdesk_name' => strtoupper($first?->user?->name ?? '—'),
                            'amount'         => (float) $group->sum('payable_amount'),
                            'first_paid_at'  => optional($group->sortBy('paid_at')->first())->paid_at,
                        ];
                    })
                    ->sortBy('first_paid_at')
                    ->values();

                $isForwardedExtend = $extendByFrontdesk->count() > 1;

                foreach ($extendByFrontdesk as $idx => $extendGroup) {
    $roomRows[] = [
        'detail_id'         => $detail->id,
        'row_type'          => 'extend',
        'number'            => $room?->number ?? '—',
        'room_type'         => '-',
        'guest_name'        => $isForwardedExtend
            ? 'FWD ' . $baseGuestName
            : '-',
        'check_in'          => '-',
        'check_out'         => '-',
        'initial_hrs'       => '-',
        'room_amount'       => 0,
        'extend_amount'     => (float) $extendGroup['amount'],
        'amenities_amount'  => 0,
        'food_amount'       => 0,
        'damages_amount'    => 0,
        'transfer_amount'   => 0,
        'frontdesk_name'    => $extendGroup['frontdesk_name'],
        'shift'             => $meta['shift'],
        'total'             => (float) $extendGroup['amount'],
    ];
}
            }

            // Amenities summed per stay
            $amenitiesAmount = (float) $detailTxs
                ->where('transaction_type_id', 8)
                ->sum('payable_amount');

            if ($amenitiesAmount > 0) {
                $roomRows[] = [
                    'detail_id'         => $detail->id,
                    'row_type'          => 'amenities',
                    'number'            => $room?->number ?? '—',
                    'room_type'         => '-',
                    'guest_name'        => '-',
                    'check_in'          => '-',
                    'check_out'         => '-',
                    'initial_hrs'       => '-',
                    'room_amount'       => 0,
                    'extend_amount'     => 0,
                    'amenities_amount'  => $amenitiesAmount,
                    'food_amount'       => 0,
                    'damages_amount'    => 0,
                    'transfer_amount'   => 0,
                    'frontdesk_name'    => $meta['frontdesk_name'],
                    'shift'             => $meta['shift'],
                    'total'             => $amenitiesAmount,
                ];
            }

            // Food summed per stay
            $foodAmount = (float) $detailTxs
                ->where('transaction_type_id', 9)
                ->sum('payable_amount');

            if ($foodAmount > 0) {
                $roomRows[] = [
                    'detail_id'         => $detail->id,
                    'row_type'          => 'food',
                    'number'            => $room?->number ?? '—',
                    'room_type'         => '-',
                    'guest_name'        => '-',
                    'check_in'          => '-',
                    'check_out'         => '-',
                    'initial_hrs'       => '-',
                    'room_amount'       => 0,
                    'extend_amount'     => 0,
                    'amenities_amount'  => 0,
                    'food_amount'       => $foodAmount,
                    'damages_amount'    => 0,
                    'transfer_amount'   => 0,
                    'frontdesk_name'    => $meta['frontdesk_name'],
                    'shift'             => $meta['shift'],
                    'total'             => $foodAmount,
                ];
            }

            // Damages summed per stay
            $damagesAmount = (float) $detailTxs
                ->where('transaction_type_id', 4)
                ->sum('payable_amount');

            if ($damagesAmount > 0) {
                $roomRows[] = [
                    'detail_id'         => $detail->id,
                    'row_type'          => 'damages',
                    'number'            => $room?->number ?? '—',
                    'room_type'         => '-',
                    'guest_name'        => '-',
                    'check_in'          => '-',
                    'check_out'         => '-',
                    'initial_hrs'       => '-',
                    'room_amount'       => 0,
                    'extend_amount'     => 0,
                    'amenities_amount'  => 0,
                    'food_amount'       => 0,
                    'damages_amount'    => $damagesAmount,
                    'transfer_amount'   => 0,
                    'frontdesk_name'    => $meta['frontdesk_name'],
                    'shift'             => $meta['shift'],
                    'total'             => $damagesAmount,
                ];
            }

            // Transfer summed per stay
            $transferAmount = (float) $detailTxs
                ->where('transaction_type_id', 7)
                ->sum('payable_amount');

            if ($transferAmount > 0) {
                $roomRows[] = [
                    'detail_id'         => $detail->id,
                    'row_type'          => 'transfer',
                    'number'            => $room?->number ?? '—',
                    'room_type'         => '-',
                    'guest_name'        => '-',
                    'check_in'          => '-',
                    'check_out'         => '-',
                    'initial_hrs'       => '-',
                    'room_amount'       => 0,
                    'extend_amount'     => 0,
                    'amenities_amount'  => 0,
                    'food_amount'       => 0,
                    'damages_amount'    => 0,
                    'transfer_amount'   => $transferAmount,
                    'frontdesk_name'    => $meta['frontdesk_name'],
                    'shift'             => $meta['shift'],
                    'total'             => $transferAmount,
                ];
            }
        }

        if (count($roomRows) > 0) {
            $rooms[] = [
                'room_id'     => $roomId,
                'room_number' => $room?->number ?? '—',
                'rowspan'     => count($roomRows),
                'rows'        => $roomRows,
            ];
        }
    }

    return $rooms;
}

    private function buildGroups($transactions): array
    {
        // Group by checkout date (from latestCheckInDetail->check_out_at)
        $byDate = $transactions->groupBy(function ($t) {
            $dt = $t->room->latestCheckInDetail?->check_out_at;
            return $dt ? Carbon::parse($dt)->toDateString() : 'unknown';
        })->sortKeysDesc();

        $groups = [];

        foreach ($byDate as $dateKey => $items) {
            $dateLabel = $dateKey === 'unknown'
                ? 'Unknown Date'
                : Carbon::parse($dateKey)->format('F d, Y');

                $transferAssignedRoomByDetail = [];

            $items
                ->groupBy(fn($t) => $t->room->latestCheckInDetail?->id)
                ->each(function ($group, $detailId) use (&$transferAssignedRoomByDetail) {
                    if (!$detailId) return;

                    // pick ONE room in this date group to carry the transfer total
                    // here: smallest room number
                    $winner = $group->sortBy(fn($t) => (int) ($t->room?->number ?? PHP_INT_MAX))->first();
                    $transferAssignedRoomByDetail[$detailId] = $winner?->room_id;
                });

            $rows = $items->map(function ($t) {
                $detail = $t->room->latestCheckInDetail;
                $roomId = $t->room_id;
                $detailId = $detail?->id;

                // transfer summed by stay (checkin_detail_id)
                $transTotalForStay = (float) ($this->transferTransactions[$detailId]->paid_amount ?? 0);

                //if long stay
                if($detail?->guest?->is_long_stay) {
                    $roomRate = (float) ($detail?->rate?->amount ?? 0) * $detail?->guest?->number_of_days;
                } else {
                    $roomRate = (float) ($detail?->rate?->amount ?? 0);
                }
                $extend   = (float) ($this->extendedTransactions[$roomId]->paid_amount ?? 0);
                $amen     = (float) ($this->amenitiesTransactions[$roomId]->paid_amount ?? 0);
                $food     = (float) ($this->foodTransactions[$roomId]->paid_amount ?? 0);
                $dam      = (float) ($this->damagesTransactions[$roomId]->paid_amount ?? 0);
                // only show it on the "winner" room row
                $detailId = $detail?->id;
                $trans = (float) ($this->transferTransactions[$detailId]->paid_amount ?? 0);

                $total = $roomRate
                    + ($this->showExtend ? $extend : 0)
                    + ($this->showAmenities ? $amen : 0)
                    + ($this->showFood ? $food : 0)
                    + ($this->showDamages ? $dam : 0)
                    + ($this->showTransfer ? $trans : 0);

                return [
                    'number'     => $t->room?->number ?? '—',
                    'room_type'  => $t->room?->type?->name ?? '—',
                    'guest_name' => strtoupper($detail?->guest?->name ?? '—'),
                    'check_in'   => $detail?->check_in_at ? Carbon::parse($detail->check_in_at)->format('m-d-Y h:iA') : '—',
                    'check_out'  => $detail?->check_out_at ? Carbon::parse($detail->check_out_at)->format('m-d-Y h:iA') : '—',
                    'initial_hrs'=> $detail?->hours_stayed ? ($detail->guest?->is_long_stay ? (($detail->hours_stayed * $detail->guest?->number_of_days) . ' hrs') : ($detail->hours_stayed . ' hrs')) : '—',
                    'room_amount'=> $roomRate,

                    'extend_amount'    => $extend,
                    'amenities_amount' => $amen,
                    'food_amount'      => $food,
                    'damages_amount'   => $dam,
                    'transfer_amount'  => $trans,

                    'frontdesk_name'  => strtoupper($detail?->frontdesk?->name ?? '—'),
                    'forwarded_to'    => '—',
                    'shift'           => $detail?->check_out_at ? $this->resolveShift($detail->check_out_at) : '—',
                    'total'           => $total,
                ];
            })->values()->all();

            $groups[] = [
                'label' => 'SALES REPORT',
                'date_label' => $dateLabel,
                'rows' => $rows,
            ];
        }

        return $groups;
    }

    private function resolveShift(string $checkOutAt): string
    {
        $hour = Carbon::parse($checkOutAt)->hour;
        return ($hour >= 8 && $hour < 20) ? 'AM' : 'PM';
    }

 public function resetFilters()
{
    $this->reset(['frontdesk', 'type', 'date_from', 'date_to', 'shift']);
    $this->transactions = collect();
    $this->groups = [];
    $this->salesRooms = [];
    $this->salesRoomsRaw = [];
    $this->totalSales = 0;

    $this->type = 'Overall Sales';
    $this->generateReport();
}

    private function buildExpensesSummary(): void
    {
        [$start, $end] = $this->shiftWindow();

        $rows = \App\Models\Expense::query()
            ->with(['expenseCategory', 'user'])
            ->whereBetween('created_at', [$start, $end])
            ->when($this->shift, fn($q) => $q->where('shift', $this->shift))
            ->when($this->frontdesk, fn($q) => $q->where('user_id', $this->frontdesk)) // ✅ frontdesk = user_id
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($e) {
                return [
                    'expense_type' => $e->category?->name ?? '—',
                    'description'  => $e->description ?? '—',
                    'shift'        => $e->shift ?? '—',
                    'amount'       => (float) $e->amount,
                    'frontdesk'    => $e->user?->name ?? '—',
                ];
            });

        $this->expensesRows  = $rows;
        $this->expensesTotal = (float) $rows->sum('amount');
    }

private function buildRoomSummary(): void
{
    [$start, $end] = $this->shiftWindow();

    /*
    |--------------------------------------------------------------------------
    | 1. Get all floors (ordered by number)
    |--------------------------------------------------------------------------
    */
    $floors = \App\Models\Floor::query()
        ->where('branch_id', auth()->user()->branch_id)
        ->orderBy('number')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | 2. Get transactions within filtered scope
    |--------------------------------------------------------------------------
    | Exclude:
    | - Room Amount (base stay)
    | - transaction_type_id 1
    | - transaction_type_id 5
    |--------------------------------------------------------------------------
    */
    $transactions = Transaction::query()
        ->with(['room.floor', 'transaction_type'])
        ->whereNotNull('paid_at')
        ->whereNotIn('transaction_type_id', [1, 5]) // ✅ EXCLUDED
        ->when($this->date_from, fn($q, $d) => $q->whereDate('paid_at', '>=', $d))
        ->when($this->date_to, fn($q, $d) => $q->whereDate('paid_at', '<=', $d))
        ->when($this->frontdesk, fn($q, $f) => $q->where('user_id', $f))
        ->get();

    /*
    |--------------------------------------------------------------------------
    | 3. Group by Transaction Type (description row)
    |--------------------------------------------------------------------------
    */
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
                ->where('room.floor_id', $floor->id)
                ->sum('payable_amount');

            $rowPerFloor[$floor->id] = $amount;
            $rowTotal += $amount;

            $totalsPerFloor[$floor->id] += $amount;
        }

        $tableRows[] = [
            'description' => $typeName,
            'floors'      => $rowPerFloor,
            'row_total'   => $rowTotal, // ✅ this drives your "Total" column
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Store final structure
    |--------------------------------------------------------------------------
    */
    $this->roomSummary = [
        'floors' => $floors->map(fn($f) => [
            'id'     => $f->id,
            'number' => $f->number,
        ])->all(),
        'rows'   => $tableRows,
        'totals' => $totalsPerFloor,
    ];
}

public function printPdf()
{
    
}



}
