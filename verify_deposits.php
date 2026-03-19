<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ShiftLog;
use App\Models\CheckinDetail;
use App\Models\Transaction;

$allLogs = ShiftLog::whereNotNull('time_out')->orderBy('time_in')->get();
$ss = [];
foreach ($allLogs as $l) {
    $h = $l->time_in->hour;
    $t = ($h >= 6 && $h < 20) ? 'AM' : 'PM';
    $d = $l->time_in->format('Y-m-d');
    $k = $t . '_' . $d;
    if (!isset($ss[$k])) $ss[$k] = ['t' => $t, 'd' => $d, 'i' => $l->time_in, 'o' => $l->time_out];
    if ($l->time_in < $ss[$k]['i']) $ss[$k]['i'] = $l->time_in;
    if ($l->time_out > $ss[$k]['o']) $ss[$k]['o'] = $l->time_out;
}

$oo = collect($ss)->sortBy('i')->values();
$rR = 0;
$rG = 0;

echo "=== EXPECTED CARD VALUES PER SHIFT ===\n\n";

foreach ($oo as $i => $s) {
    $ti = $s['i'];
    $to = $s['o'];
    $oc = CheckinDetail::whereHas('room', fn($q) => $q->where('branch_id', 1))
        ->where('check_in_at', '<=', $to)
        ->where(function ($q) use ($ti) {
            $q->whereNull('check_out_at')->orWhere('check_out_at', '>=', $ti);
        })->pluck('id')->toArray();

    $tx = Transaction::whereIn('checkin_detail_id', $oc)->whereBetween('created_at', [$ti, $to])->get();

    $oR = (float) $tx->where('transaction_type_id', 2)
        ->filter(fn($t) => str_contains(strtolower($t->remarks ?? ''), 'room key') || str_contains(strtolower($t->remarks ?? ''), 'tv remote'))
        ->sum('payable_amount');

    $ci = CheckinDetail::whereHas('room', fn($q) => $q->where('branch_id', 1))
        ->whereBetween('check_out_at', [$ti, $to])->pluck('id')->toArray();

    $cd = empty($ci) ? 0 : (float) Transaction::whereIn('checkin_detail_id', $ci)
        ->where('transaction_type_id', 2)
        ->where('remarks', 'Deposit From Check In (Room Key & TV Remote)')
        ->sum('payable_amount');

    $oG = (float) $tx->where('transaction_type_id', 2)
        ->filter(fn($t) => !str_contains(strtolower($t->remarks ?? ''), 'room key') && !str_contains(strtolower($t->remarks ?? ''), 'tv remote'))
        ->sum('payable_amount');

    $oC = (float) $tx->where('transaction_type_id', 5)->sum('payable_amount');

    $n = $i + 1;
    $cR = $rR + $oR;
    $rmR = max(0, $cR - $cd);
    $cG = $rG + $oG;
    $rmG = max(0, $cG - $oC);

    echo "Shift $n ({$s['t']} " . substr($s['d'], 5) . ")\n";
    echo "  ROOM DEPOSIT:  FWD=" . number_format($rR) . " + Own=" . number_format($oR) . " = Card:" . number_format($cR) . " - Checkout:" . number_format($cd) . " = Remaining:" . number_format($rmR) . "\n";
    echo "  GUEST DEPOSIT: FWD=" . number_format($rG) . " + Own=" . number_format($oG) . " = Card:" . number_format($cG) . " - Cashouts:" . number_format($oC) . " = Remaining:" . number_format($rmG) . "\n\n";

    $rR = $rmR;
    $rG = $rmG;
}
