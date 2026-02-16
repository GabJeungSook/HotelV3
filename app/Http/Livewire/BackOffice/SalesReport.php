<?php

namespace App\Http\Livewire\BackOffice;

use Livewire\Component;
use App\Models\Frontdesk;
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
    public $extendedTransactions;
    public $amenitiesTransactions;
    public $foodTransactions;
    public $damagesTransactions;
    public $transferTransactions;
    public $depositTransactions;
    public array $summary = [];

    public function mount()
    {
        $this->summary = [];
        $this->transactions = collect();
        $this->totalSales = 0;
        $this->groups = [];

        $this->generateReport();
        $this->summary = $this->buildSummary();
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

    private function buildSummary(): array
{
    // Reuse the same filter logic:
    // - frontdesk/date_from/date_to/shift applies via latestCheckInDetail->check_out_at
    // - you can tweak if you want check-in based summaries

    $tx = Transaction::query()
        ->with(['room.type', 'room.latestCheckInDetail'])
        ->whereHas('room.latestCheckInDetail', function ($q) {
            $q->when($this->date_from, fn($q, $d) => $q->whereDate('check_out_at', '>=', $d))
              ->when($this->date_to, fn($q, $d) => $q->whereDate('check_out_at', '<=', $d))
              ->when($this->frontdesk, fn($q, $f) => $q->where('frontdesk_id', $f));
        })
        ->when($this->shift, function ($q, $shift) {
            $q->whereHas('room.latestCheckInDetail', function ($q2) use ($shift) {
                if ($shift === 'AM') {
                    $q2->whereTime(DB::raw('TIME(check_out_at)'), '>=', '08:00:00')
                       ->whereTime(DB::raw('TIME(check_out_at)'), '<', '20:00:00');
                }
                if ($shift === 'PM') {
                    $q2->where(function ($sub) {
                        $sub->whereTime(DB::raw('TIME(check_out_at)'), '>=', '20:00:00')
                            ->orWhereTime(DB::raw('TIME(check_out_at)'), '<', '08:00:00');
                    });
                }
            });
        });


    // Rooms involved in this filtered set
    $roomIds = (clone $tx)->distinct()->pluck('room_id');

    // 1) Guest per accommodation (Room Type -> count)
    $guestPerType = Transaction::query()
        ->whereIn('room_id', $roomIds)
        ->with('room.type')
        ->get()
        ->groupBy(fn($t) => $t->room?->type?->name ?? 'Unknown')
        ->map(fn($items) => $items->pluck('room_id')->unique()->count()) // count unique rooms as proxy
        ->sortDesc()
        ->take(3)
        ->map(fn($count, $type) => ['label' => $type, 'value' => $count])
        ->values()
        ->all();

    // If you want true "guest count", replace the map logic with:
    // ->map(fn($items) => $items->pluck('room.latestCheckInDetail.guest_id')->unique()->count())

    // 2) Unoccupied rooms (Room Type -> count)
    // You likely have room status logic; here’s a placeholder pattern:

    // Rooms occupied during window (overlapping stays)
    [$start, $end] = $this->shiftWindow();
    $occupiedRoomIds = \App\Models\CheckInDetail::query()
        ->when($this->frontdesk, fn($q, $f) => $q->where('frontdesk_id', $f))
        ->where(function ($q) use ($start, $end) {
            $q->where('check_in_at', '<', $end)
            ->where(function ($q2) use ($start) {
                $q2->whereNull('check_out_at')
                    ->orWhere('check_out_at', '>', $start);
            });
        })
        ->distinct()
        ->pluck('room_id');
    $unoccupiedPerType = \App\Models\Room::query()
        ->whereIn('id', $occupiedRoomIds)
        ->where('status', 'UNOCCUPIED') // adjust to your actual column/value
        ->with('type')
        ->get()
        ->groupBy(fn($r) => $r->type?->name ?? 'Unknown')
        ->map(fn($items) => $items->count())
        ->sortDesc()
        ->take(3)
        ->map(fn($count, $type) => ['label' => $type, 'value' => $count])
        ->values()
        ->all();

    // 3) Under repair room (Room Type -> count)
    $damagedRoomIds = Transaction::query()
    ->where('transaction_type_id', 4)
    ->whereIn('room_id', $roomIds)   // roomIds from the filtered set
    ->distinct()
    ->pluck('room_id');
    $underRepairPerType = \App\Models\Room::query()
        ->whereIn('id', $damagedRoomIds)
        ->with('type')
        ->get()
        ->groupBy(fn($r) => $r->type?->name ?? 'Unknown')
        ->map(fn($items) => $items->count())
        ->sortDesc()
        ->take(3)
        ->map(fn($count, $type) => ['label' => $type, 'value' => $count])
        ->values()
        ->all();

    // 4) Group check-in time (hour bucket -> guest count)
    $checkinBuckets = $this->twoShiftBucketsFor('check_in_at');

    // $checkinBuckets = \App\Models\CheckInDetail::query()
    //     ->when($this->date_from, fn($q, $d) => $q->whereDate('check_in_at', '>=', $d))
    //     ->when($this->date_to, fn($q, $d) => $q->whereDate('check_in_at', '<=', $d))
    //     ->when($this->frontdesk, fn($q, $f) => $q->where('frontdesk_id', $f))
    //     ->selectRaw("DATE_FORMAT(check_in_at, '%h:00 %p') as t, COUNT(*) as c")
    //     ->groupBy('t')
    //     ->orderByRaw("MIN(check_in_at)")
    //     ->limit(3)
    //     ->get()
    //     ->map(fn($r) => ['label' => $r->t, 'value' => (int) $r->c])
    //     ->all();

    // 5) Group check-out time (hour bucket -> guest count)
    $checkoutBuckets = $this->twoShiftBucketsFor('check_out_at', true);
    // $checkoutBuckets = \App\Models\CheckInDetail::query()
    //     ->when($this->date_from, fn($q, $d) => $q->whereDate('check_out_at', '>=', $d))
    //     ->when($this->date_to, fn($q, $d) => $q->whereDate('check_out_at', '<=', $d))
    //     ->when($this->frontdesk, fn($q, $f) => $q->where('frontdesk_id', $f))
    //     ->selectRaw("DATE_FORMAT(check_out_at, '%h:00 %p') as t, COUNT(*) as c")
    //     ->groupBy('t')
    //     ->orderByRaw("MIN(check_out_at)")
    //     ->limit(3)
    //     ->get()
    //     ->map(fn($r) => ['label' => $r->t, 'value' => (int) $r->c])
    //     ->all();

    return [
        'guest_per_accommodation' => $guestPerType,
        'unoccupied_rooms'        => $unoccupiedPerType,
        'under_repair_rooms'      => $underRepairPerType,
        'group_checkin_time'      => $checkinBuckets,
        'group_checkout_time'     => $checkoutBuckets,
    ];
}

    private function twoShiftBucketsFor(string $column, bool $requireIsCheckout = false): array
    {
         $q = \App\Models\CheckInDetail::query()
        ->when($requireIsCheckout, fn($q) => $q->where('is_check_out', true))
        ->when($this->date_from, fn($q, $d) => $q->whereDate($column, '>=', $d))
        ->when($this->date_to, fn($q, $d) => $q->whereDate($column, '<=', $d))
        ->when($this->frontdesk, fn($q, $f) => $q->where('frontdesk_id', $f))
        ->whereNotNull($column);

        // AM: 08:00 - 19:59:59
        $am = (clone $q)
            ->whereTime(DB::raw("TIME($column)"), '>=', '08:00:00')
            ->whereTime(DB::raw("TIME($column)"), '<',  '20:00:00')
            ->count();

        // PM: 20:00 - 07:59:59
        $pm = (clone $q)
            ->where(function ($sub) use ($column) {
                $sub->whereTime(DB::raw("TIME($column)"), '>=', '20:00:00')
                    ->orWhereTime(DB::raw("TIME($column)"), '<',  '08:00:00');
            })
            ->count();

        return [
            ['label' => '8:00 AM - 8:00 PM', 'value' => $am],
            ['label' => '8:00 PM - 8:00 AM', 'value' => $pm],
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
        // Base query: transactions grouped per room_id
        $base = Transaction::query()
            // IMPORTANT: eager-load room + nested relations used in the blade
            ->with([
                'room.type',
                'room.latestCheckInDetail.guest',
                'room.latestCheckInDetail.rate',
                'room.latestCheckInDetail.frontdesk',
            ])
            ->whereHas('room.latestCheckInDetail', function ($q) {
                $q->when($this->date_from, fn($q, $d) => $q->whereDate('check_out_at', '>=', $d))
                  ->when($this->date_to, fn($q, $d) => $q->whereDate('check_out_at', '<=', $d))
                  ->when($this->frontdesk, fn($q, $f) => $q->where('frontdesk_id', $f));
            })
            ->when($this->shift, function ($q, $shift) {
                $q->whereHas('room.latestCheckInDetail', function ($q2) use ($shift) {
                    if ($shift === 'AM') {
                        $q2->whereTime(DB::raw('TIME(check_out_at)'), '>=', '08:00:00')
                           ->whereTime(DB::raw('TIME(check_out_at)'), '<', '20:00:00');
                    }

                    if ($shift === 'PM') {
                        $q2->where(function ($sub) {
                            $sub->whereTime(DB::raw('TIME(check_out_at)'), '>=', '20:00:00')
                                ->orWhereTime(DB::raw('TIME(check_out_at)'), '<', '08:00:00');
                        });
                    }
                });
            });

        // Optional type filter (kept as-is)
        switch ($this->type) {
            case 'Daily':
                $base->whereDate('paid_at', now()->toDateString());
                break;
            case 'Weekly':
                $base->whereBetween('paid_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'Monthly':
                $base->whereMonth('paid_at', now()->month)
                     ->whereYear('paid_at', now()->year);
                break;
            default:
                break;
        }

        // Group overall (excluding transaction types)
        $transactions = (clone $base)
            ->whereNotIn('transaction_type_id', [5, 2])
            ->selectRaw('room_id, SUM(payable_amount) as paid_amount')
            ->whereNotNull('paid_at')
            ->groupBy('room_id')
            ->get();

        $roomIds = $transactions->pluck('room_id');

        // Side buckets per room
        $this->extendedTransactions = Transaction::where('transaction_type_id', 6)
            ->whereIn('room_id', $roomIds)
            ->selectRaw('room_id, SUM(payable_amount) as paid_amount')
            ->whereNotNull('paid_at')
            ->groupBy('room_id')
            ->get()
            ->keyBy('room_id');

        $this->amenitiesTransactions = Transaction::where('transaction_type_id', 8)
            ->whereIn('room_id', $roomIds)
            ->selectRaw('room_id, SUM(payable_amount) as paid_amount')
            ->whereNotNull('paid_at')
            ->groupBy('room_id')
            ->get()
            ->keyBy('room_id');

        $this->foodTransactions = Transaction::where('transaction_type_id', 9)
            ->whereIn('room_id', $roomIds)
            ->selectRaw('room_id, SUM(payable_amount) as paid_amount')
            ->whereNotNull('paid_at')
            ->groupBy('room_id')
            ->get()
            ->keyBy('room_id');

        $this->damagesTransactions = Transaction::where('transaction_type_id', 4)
            ->whereIn('room_id', $roomIds)
            ->selectRaw('room_id, SUM(payable_amount) as paid_amount')
            ->whereNotNull('paid_at')
            ->groupBy('room_id')
            ->get()
            ->keyBy('room_id');

        $this->transferTransactions = Transaction::where('transaction_type_id', 7)
            ->whereIn('room_id', $roomIds)
            ->selectRaw('room_id, SUM(payable_amount) as paid_amount')
            ->whereNotNull('paid_at')
            ->groupBy('room_id')
            ->get()
            ->keyBy('room_id');

        // $this->depositTransactions = Transaction::where('transaction_type_id', 2)
        //     ->whereNotIn('remarks', ['Deposit From Check In (Room Key & TV Remote)'])
        //     ->whereIn('room_id', $roomIds)
        //     ->selectRaw('room_id, SUM(paid_amount) as paid_amount')
        //     ->groupBy('room_id')
        //     ->get()
        //     ->keyBy('room_id');

        // Compute room amount and total sales
        $roomAmount = 0;
        foreach ($transactions as $t) {
            $roomAmount += (float) ($t->room->latestCheckInDetail?->rate?->amount ?? 0);
        }

        $this->totalSales =
            ($this->showExtend ? (float) $this->extendedTransactions->sum('paid_amount') : 0) +
            ($this->showAmenities ? (float) $this->amenitiesTransactions->sum('paid_amount') : 0) +
            ($this->showFood ? (float) $this->foodTransactions->sum('paid_amount') : 0) +
            ($this->showDamages ? (float) $this->damagesTransactions->sum('paid_amount') : 0) +
            ($this->showTransfer ? (float) $this->transferTransactions->sum('paid_amount') : 0) +
            (float) $roomAmount;

        $this->transactions = $transactions;

        // Build grouped structure for uniform report view
        $this->groups = $this->buildGroups($transactions);
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

            $rows = $items->map(function ($t) {
                $detail = $t->room->latestCheckInDetail;
                $roomId = $t->room_id;

                $roomRate = (float) ($detail?->rate?->amount ?? 0);
                $extend   = (float) ($this->extendedTransactions[$roomId]->paid_amount ?? 0);
                $amen     = (float) ($this->amenitiesTransactions[$roomId]->paid_amount ?? 0);
                $food     = (float) ($this->foodTransactions[$roomId]->paid_amount ?? 0);
                $dam      = (float) ($this->damagesTransactions[$roomId]->paid_amount ?? 0);
                $trans      = (float) ($this->transferTransactions[$roomId]->paid_amount ?? 0);

                $total = $roomRate
                    + ($this->showExtend ? $extend : 0)
                    + ($this->showAmenities ? $amen : 0)
                    + ($this->showFood ? $food : 0)
                    + ($this->showDamages ? $dam : 0)
                    + ($this->showTransfer ? $trans : 0);

                // Using "entries" for variable columns is not ideal for sales,
                // but we’ll keep a consistent "row" structure per your standard.
                return [
                    'number'     => $t->room?->number ?? '—',
                    'room_type'  => $t->room?->type?->name ?? '—',
                    'guest_name' => strtoupper($detail?->guest?->name ?? '—'),
                    'check_in'   => $detail?->check_in_at ? Carbon::parse($detail->check_in_at)->format('m-d-Y h:iA') : '—',
                    'check_out'  => $detail?->check_out_at ? Carbon::parse($detail->check_out_at)->format('m-d-Y h:iA') : '—',
                    'initial_hrs'=> $detail?->hours_stayed ? ($detail->hours_stayed . ' hrs') : '—',
                    'room_amount'=> $roomRate,

                    'extend_amount'   => $extend,
                    'amenities_amount'=> $amen,
                    'food_amount'     => $food,
                    'damages_amount'  => $dam,
                    'transfer_amount'  => $trans,

                    'frontdesk_name'  => strtoupper($detail?->frontdesk?->name ?? '—'),
                    'forwarded_to'    => '—', // keep simple (you can add your forward logic if needed)
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
        $this->totalSales = 0;

        $this->type = 'Overall Sales';
        $this->generateReport();
    }
}
