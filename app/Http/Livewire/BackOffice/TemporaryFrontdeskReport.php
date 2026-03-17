<?php

namespace App\Http\Livewire\BackOffice;

use Livewire\Component;
use App\Models\FrontdeskShift;
use Carbon\Carbon;

class TemporaryFrontdeskReport extends Component
{
//     public $date_from;
//     public $date_to;
//     public $shift;
// public int $filter = 1;

public $filter;
public $shifts = [];
public $selectedShift = [];

public function mount(): void
{
    $records = FrontdeskShift::orderBy('shift_opened','desc')->get();

    foreach ($records as $shift) {
        $this->shifts[$shift->id] = [
            'label' => $shift->label
        ];
    }

    if ($records->count()) {
        $this->filter = $records->first()->id;
        $this->loadShift();
    }

    // $this->shifts = [
    //     1 => [
    //         'label' => 'PM SHIFT - March 8-9, 2026',
    //         'raw_file' => 'raw-files/PM SALES DATA- March 8-9, 2026.xlsx',

    //         'frontdesk_outgoing' => 'Hannah',
    //         'frontdesk_incoming' => 'Jeneath',
    //         'shift_opened' => 'March 08, 2026 08:03 PM',
    //         'shift_closed' => 'March 09, 2026 07:27 AM',

    //         'cash_drawer' => [
    //             ['description' => 'Opening Cash', 'sub' => '(Net Sales Receive from previous Shift)', 'amount' => '-', 'remark' => '-'],
    //             ['description' => 'Key/Remote Deposit', 'sub' => '(Room occupied receive)', 'amount' => '-', 'remark' => '-'],
    //             ['description' => 'Guest Deposit', 'sub' => '(Total guest other deposit received)', 'amount' => '-', 'remark' => '-'],
    //             ['description' => 'Forwarding Balance', 'sub' => '(From previous Shift)', 'amount' => '-', 'remark' => '-'],
    //         ],
    //         'cash_drawer_total' => [
    //             'amount' => '-',
    //             'remark' => '',
    //         ],

    //         'frontdesk_operation_a' => [
    //             ['description' => 'New Check-in', 'sub' => '(Total New Check-in)', 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Extension', 'sub' => null, 'number' => '', 'amount' => '-'],
    //             ['description' => 'Transfer', 'sub' => null, 'number' => '', 'amount' => '-'],
    //             ['description' => 'Miscellaneous charges', 'sub' => null, 'number' => '', 'amount' => '-'],
    //             ['description' => 'Food', 'sub' => null, 'number' => '', 'amount' => '-'],
    //             ['description' => 'Drink', 'sub' => null, 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Others', 'sub' => '(Foods and Drinks from POS)', 'number' => '-', 'amount' => '-'],
    //             ['description' => 'TOTAL', 'sub' => null, 'number' => '-', 'amount' => '-'],
    //         ],

    //         'frontdesk_operation_b' => [
    //             ['description' => 'Forwarded Room Check-In', 'sub' => '(Previous Shift)', 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Key/Remote Deposit', 'sub' => '(Current room occupied at the end shift)', 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Forwarded Room Guest Deposit', 'sub' => '(Previous Shift)', 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Guest Deposit', 'sub' => '(Current room occupied at the end shift)', 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Total Check-Out', 'sub' => null, 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Expenses', 'sub' => null, 'number' => '-', 'amount' => '-'],
    //         ],


    //         'final_sales' => [
    //             'gross_sales' => '-',
    //             'refund' => '-',
    //             'expenses' => '-',
    //             'discounts' => '-',
    //             'net_sales' => '-',
    //         ],

    //          'cash_position_summary' => [
    //             'opening_cash' => '-',
    //             'forwarded_balance' => '-',
    //             'net_sales' => '-',
    //             'remittance' => '-',
    //         ],

    //         'cash_reconciliation' => [
    //             'expected_cash' => '-',
    //             'actual_cash' => '-',
    //             'difference' => '-',
    //         ],

    //     ],

    //     2 => [
    //         'label' => 'AM SHIFT - March 9, 2026',
    //         'raw_file' => 'raw-files/AM SALES DATA- March 9, 2026.xlsx',

    //         'frontdesk_outgoing' => 'Jeneath Lecias',
    //         'frontdesk_incoming' => 'Kathleen Drew and Hannah',
    //         'shift_opened' => 'March 9, 2026 - 07:28 AM',
    //         'shift_closed' => 'March 9, 2026 - 08:57 PM',

    //         'cash_drawer' => [
    //             ['description' => 'Opening Cash', 'sub' => '(Net Sales Receive from previous Shift)', 'amount' => '₱ 34,496.00', 'remark' => '-'],
    //             ['description' => 'Key/Remote Deposit', 'sub' => '(Room occupied receive)', 'amount' => '₱ 11,600.00', 'remark' => '-'],
    //             ['description' => 'Guest Deposit', 'sub' => '(Total guest other deposit received)', 'amount' => '-', 'remark' => '-'],
    //             ['description' => 'Forwarding Balance', 'sub' => '(From previous Shift)', 'amount' => '₱ 1.00', 'remark' => '-'],
    //         ],
    //         'cash_drawer_total' => [
    //             'amount' => '₱ 45, 716 <span class="text-red-600">(381)</span>',
    //             'remark' => '-',
    //         ],

    //         'frontdesk_operation' => [
    //             ['description' => 'New Check-in', 'sub' => '(Total New Check-in)', 'number' => '120', 'amount' => '₱ 49, 112.00'],
    //             ['description' => 'Extension', 'sub' => null, 'number' => '', 'amount' => '₱ 336.00'],
    //             ['description' => 'Transfer', 'sub' => null, 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Miscellaneous charges', 'sub' => null, 'number' => '', 'amount' => '₱ 50.00'],
    //             ['description' => 'Food', 'sub' => null, 'number' => '', 'amount' => '₱ 55.00'],
    //             ['description' => 'Drink', 'sub' => null, 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Key/Remote Deposit', 'sub' => '(Current room occupied at the end shift)', 'number' => '89', 'amount' => '₱ 17,800.00'],
    //             ['description' => 'Total Check-out', 'sub' => null, 'number' => '31', 'amount' => '₱ 6,200.00'],
    //             ['description' => 'Guest Deposit', 'sub' => null, 'number' => '', 'amount' => '-'],
    //             ['description' => 'Expenses', 'sub' => null, 'number' => '', 'amount' => '₱ 9,407.00'],
    //             ['description' => 'Remittance', 'sub' => null, 'number' => '', 'amount' => '-'],
    //         ],

    //         'cash_reconciliation' => [
    //             'expected_cash' => '₱ 92,443.00',
    //             'actual_cash' => '₱ 86,594.00',
    //             'difference' => '₱ 5,849.00',
    //         ],

    //         'final_sales' => [
    //             'gross_sales' => '₱ 49,553.00',
    //             'expenses' => '₱ 9,407.00',
    //             'discounts' => '-',
    //             'remittance' => '-',
    //             'net_sales' => '₱ 40,146.00',
    //         ],
    //     ],

    //     3 => [
    //         'label' => 'PM SHIFT - March 9-10, 2026',
    //         'raw_file' => 'raw-files/PM SALES DATA- March 9-10, 2026.xlsx',

    //         'frontdesk_outgoing' => 'Kathleen Drew and Hannah',
    //         'frontdesk_incoming' => 'Jeneath Lecias and Ruby Gold',
    //         'shift_opened' => 'March 9, 2026 - 8:33 PM',
    //         'shift_closed' => 'March 10, 2026 - 8:37 AM',

    //         'cash_drawer' => [
    //             ['description' => 'Opening Cash', 'sub' => '(Net Sales Receive from previous Shift)', 'amount' => '₱ 40,146.00', 'remark' => '-'],
    //             ['description' => 'Key/Remote Deposit', 'sub' => '(Room occupied receive)', 'amount' => '₱ 17,800.00', 'remark' => '-'],
    //             ['description' => 'Guest Deposit', 'sub' => '(Total guest other deposit received)', 'amount' => '-', 'remark' => '-'],
    //             ['description' => 'Forwarding Balance', 'sub' => '(From previous Shift)', 'amount' => '₱ 34,497.00', 'remark' => '-'],
    //         ],
    //         'cash_drawer_total' => [
    //             'amount' => '₱ 86,595.00 <span class="text-red-600">(5,849)</span>',
    //             'remark' => '-',
    //         ],

    //         'frontdesk_operation' => [
    //             ['description' => 'New Check-in', 'sub' => '(Total New Check-in)', 'number' => '79', 'amount' => '₱ 30,296.00'],
    //             ['description' => 'Extension', 'sub' => null, 'number' => '', 'amount' => '₱ 112.00'],
    //             ['description' => 'Transfer', 'sub' => null, 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Miscellaneous charges', 'sub' => null, 'number' => '', 'amount' => '-'],
    //             ['description' => 'Food', 'sub' => null, 'number' => '', 'amount' => '₱ 12.00'],
    //             ['description' => 'Drink', 'sub' => null, 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Key/Remote Deposit', 'sub' => '(Current room occupied at the end shift)', 'number' => '32', 'amount' => '₱ 6,400.00'],
    //             ['description' => 'Total Check-out', 'sub' => null, 'number' => '47', 'amount' => '₱ 9,400.00'],
    //             ['description' => 'Guest Deposit', 'sub' => null, 'number' => '', 'amount' => '₱ 3,939.00'],
    //             ['description' => 'Expenses', 'sub' => null, 'number' => '', 'amount' => '-'],
    //             ['description' => 'Remittance', 'sub' => null, 'number' => '', 'amount' => '-'],
    //         ],

    //         'cash_reconciliation' => [
    //             'expected_cash' => '₱ 115,402.00',
    //             'actual_cash' => '₱ 105,995.00',
    //             'difference' => '₱ 9,407.00',
    //         ],

    //         'final_sales' => [
    //             'gross_sales' => '₱ 30,420.00',
    //             'expenses' => '-',
    //             'discounts' => '-',
    //             'remittance' => '-',
    //             'net_sales' => '₱ 30,420.00',
    //         ],
    //     ],

    //     4 => [
    //         'label' => 'AM SHIFT - March 10, 2026',
    //         'raw_file' => 'raw-files/PM SALES DATA- March 10, 2026.xlsx',

    //         'frontdesk_outgoing' => 'Jeneath Lecias and Ruby Gold',
    //         'frontdesk_incoming' => 'Hannah and Jinky Obag',
    //         'shift_opened' => 'March 10, 2026 - 8:37 AM',
    //         'shift_closed' => 'March 10, 2026 - 8:37 PM',

    //         'cash_drawer' => [
    //             ['description' => 'Opening Cash', 'sub' => '(Net Sales Receive from previous Shift)', 'amount' => '₱ 30,420.00', 'remark' => '-'],
    //             ['description' => 'Key/Remote Deposit', 'sub' => '(Room occupied receive)', 'amount' => '₱ 6,400.00', 'remark' => '-'],
    //             ['description' => 'Guest Deposit', 'sub' => '(Total guest other deposit received)', 'amount' => '₱ 3,939.00', 'remark' => '-'],
    //             ['description' => 'Forwarding Balance', 'sub' => '(From previous Shift)', 'amount' => '₱ 74,643.00', 'remark' => '-'],
    //         ],
    //         'cash_drawer_total' => [
    //             'amount' => '₱ 105,996.00 <span class="text-red-600">(9,406)</span>',
    //             'remark' => '-',
    //         ],

    //         'frontdesk_operation' => [
    //             ['description' => 'New Check-in', 'sub' => '(Total New Check-in)', 'number' => '153', 'amount' => '₱ 60,088.00'],
    //             ['description' => 'Extension', 'sub' => null, 'number' => '', 'amount' => '₱ 784.00'],
    //             ['description' => 'Transfer', 'sub' => null, 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Miscellaneous charges', 'sub' => null, 'number' => '', 'amount' => '-'],
    //             ['description' => 'Food', 'sub' => null, 'number' => '', 'amount' => '₱ 45.00'],
    //             ['description' => 'Drink', 'sub' => null, 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Key/Remote Deposit', 'sub' => '(Current room occupied at the end shift)', 'number' => '111', 'amount' => '₱ 22,200.00'],
    //             ['description' => 'Total Check-out', 'sub' => null, 'number' => '42', 'amount' => '₱ 8,400.00'],
    //             ['description' => 'Guest Deposit', 'sub' => null, 'number' => '', 'amount' => '₱ 11,606.00'],
    //             ['description' => 'Expenses', 'sub' => null, 'number' => '', 'amount' => '₱ 15,138.80'],
    //             ['description' => 'Remittance', 'sub' => null, 'number' => '', 'amount' => '-'],
    //         ],

    //         'cash_reconciliation' => [
    //             'expected_cash' => '₱ 184,647.20',
    //             'actual_cash' => '₱ 196,543.00',
    //             'difference' => '₱ 11,896.00',
    //         ],

    //         'final_sales' => [
    //             'gross_sales' => '₱ 60,917.00',
    //             'expenses' => '₱ 15,138.80',
    //             'discounts' => '-',
    //             'remittance' => '-',
    //             'net_sales' => '₱ 45,778.20',
    //         ],
    //     ],
    //     5 => [
    //         'label' => 'PM SHIFT - March 10-11, 2026',
    //         'raw_file' => 'raw-files/AM SALES DATA- March 10-11, 2026.xlsx',

    //         'frontdesk_outgoing' => 'Hannah and Jinky Obag',
    //         'frontdesk_incoming' => 'Jeneath Lecias and Seanne Karylle',
    //         'shift_opened' => 'March 10, 2026 - 8:38 PM',
    //         'shift_closed' => 'March 11, 2026 - 8:20 AM',

    //         'cash_drawer' => [
    //             ['description' => 'Opening Cash', 'sub' => '(Net Sales Receive from previous Shift)', 'amount' => '₱ 45,778.20', 'remark' => '-'],
    //             ['description' => 'Key/Remote Deposit', 'sub' => '(Room occupied receive)', 'amount' => '₱  22,200.00', 'remark' => '-'],
    //             ['description' => 'Guest Deposit', 'sub' => '(Total guest other deposit received)', 'amount' => '₱ 11,606.00', 'remark' => '-'],
    //             ['description' => 'Forwarding Balance', 'sub' => '(From previous Shift)', 'amount' => '₱ 105,063.00', 'remark' => '-'],
    //         ],
    //         'cash_drawer_total' => [
    //             'amount' => '₱ 196,543.00 <span class="text-red-600">(11,896.00) excess</span>',
    //             'remark' => '-',
    //         ],

    //         'frontdesk_operation' => [
    //             ['description' => 'New Check-in', 'sub' => '(Total New Check-in)', 'number' => '100', 'amount' => '₱ 35,560.00'],
    //             ['description' => 'Extension', 'sub' => null, 'number' => '', 'amount' => '₱ 112.00'],
    //             ['description' => 'Transfer', 'sub' => null, 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Miscellaneous charges', 'sub' => null, 'number' => '', 'amount' => '₱ 450.00'],
    //             ['description' => 'Food', 'sub' => null, 'number' => '', 'amount' => '-'],
    //             ['description' => 'Drink', 'sub' => null, 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Key/Remote Deposit', 'sub' => '(Current room occupied at the end shift)', 'number' => '33', 'amount' => '₱ 6,600.00'],
    //             ['description' => 'Total Check-out', 'sub' => null, 'number' => '67', 'amount' => '₱ 13,400.00'],
    //             ['description' => 'Guest Deposit', 'sub' => null, 'number' => '', 'amount' => '₱ 6,706.00'],
    //             ['description' => 'Expenses', 'sub' => null, 'number' => '', 'amount' => '-'],
    //             ['description' => 'Remittance', 'sub' => null, 'number' => '', 'amount' => '-'],
    //         ],

    //         'cash_reconciliation' => [
    //             'expected_cash' => '₱ 200,269.20',
    //             'actual_cash' => '₱ 228,889',
    //             'difference' => '₱ 28,619.80',
    //         ],

    //         'final_sales' => [
    //             'gross_sales' => '₱ 36,122.00',
    //             'expenses' => '-',
    //             'discounts' => '-',
    //             'remittance' => '-',
    //             'net_sales' => '₱ 36,112.00',
    //         ],
    //     ],
    //     6 => [
    //         'label' => 'AM SHIFT - March 11, 2026',
    //         'raw_file' => 'raw-files/AM SALES DATA- March 11, 2026.xlsx',

    //         'frontdesk_outgoing' => 'Jeneath Lecias and Seanne Karylle',
    //         'frontdesk_incoming' => 'Ruby Gold and Jinky Obag',
    //         'shift_opened' => 'March 11, 2026 8:21 AM',
    //         'shift_closed' => 'March 11, 2026 8:16 PM',

    //         'cash_drawer' => [
    //             ['description' => 'Opening Cash', 'sub' => '(Net Sales Receive from previous Shift)', 'amount' => '₱ 36,122.00', 'remark' => '-'],
    //             ['description' => 'Key/Remote Deposit', 'sub' => '(Room occupied receive)', 'amount' => '₱ 6,600.00', 'remark' => '-'],
    //             ['description' => 'Guest Deposit', 'sub' => '(Total guest other deposit received)', 'amount' => '₱ 6,706.00', 'remark' => '-'],
    //             ['description' => 'Forwarding Balance', 'sub' => '(From previous Shift)', 'amount' => '₱ 150,841.00', 'remark' => '-'],
    //         ],
    //         'cash_drawer_total' => [
    //             'amount' => '₱ 228,889.20 <span class="text-red-600">(+28,619.80)</span>',
    //             'remark' => '-',
    //         ],

    //         'frontdesk_operation' => [
    //             ['description' => 'New Check-in', 'sub' => '(Total New Check-in)', 'number' => '140', 'amount' => '₱ 63,728.00'],
    //             ['description' => 'Extension', 'sub' => null, 'number' => '', 'amount' => '₱ 224.00'],
    //             ['description' => 'Transfer', 'sub' => null, 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Miscellaneous charges', 'sub' => null, 'number' => '', 'amount' => '₱ 270.00'],
    //             ['description' => 'Food', 'sub' => null, 'number' => '', 'amount' => '-'],
    //             ['description' => 'Drink', 'sub' => null, 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Key/Remote Deposit', 'sub' => '(Current room occupied at the end shift)', 'number' => '108', 'amount' => '₱ 21,600.00'],
    //             ['description' => 'Total Check-out', 'sub' => null, 'number' => '32', 'amount' => '₱ 6,400.00'],
    //             ['description' => 'Guest Deposit', 'sub' => null, 'number' => '', 'amount' => '₱ 7,300.00'],
    //             ['description' => 'Expenses', 'sub' => null, 'number' => '', 'amount' => '-'],
    //             ['description' => 'Remittance', 'sub' => null, 'number' => '', 'amount' => '₱ 100,000.00'],
    //         ],

    //         'cash_reconciliation' => [
    //             'expected_cash' => '₱ 134,307.00',
    //             'actual_cash' => '₱ 160,094.00',
    //             'difference' => '₱ 25,787.00',
    //         ],

    //         'final_sales' => [
    //             'gross_sales' => '₱ 64,222.00',
    //             'expenses' => '-',
    //             'discounts' => '-',
    //             'remittance' => '₱ 100,000.00',
    //             'net_sales' => '₱ -35,778.00',
    //         ],
    //     ],
    //     7 => [
    //         'label' => 'PM SHIFT - March 11 - 12, 2026',
    //         'raw_file' => 'raw-files/PM SALES DATA- March 11-12, 2026.xlsx',

    //         'frontdesk_outgoing' => 'Jinky Obag',
    //         'frontdesk_incoming' => 'Jeneath Lecias',
    //         'shift_opened' => 'March 11, 2026 8:21 PM',
    //         'shift_closed' => 'March 12, 2026 8:16 AM',

    //         'cash_drawer' => [
    //             ['description' => 'Opening Cash', 'sub' => '(Net Sales Receive from previous Shift)', 'amount' => '₱ -35,778.00', 'remark' => '-'],
    //             ['description' => 'Key/Remote Deposit', 'sub' => '(Room occupied receive)', 'amount' => '₱ 21,600.00', 'remark' => '-'],
    //             ['description' => 'Guest Deposit', 'sub' => '(Total guest other deposit received)', 'amount' => '₱ 7,300.00', 'remark' => '-'],
    //             ['description' => 'Forwarding Balance', 'sub' => '(From previous Shift)', 'amount' => '₱ 186,963.00', 'remark' => '-'],
    //         ],
    //         'cash_drawer_total' => [
    //             'amount' => '₱ 160,094.00 <span class="text-red-600">(19,991.00)</span>',
    //             'remark' => '-',
    //         ],

    //         'frontdesk_operation' => [
    //             ['description' => 'New Check-in', 'sub' => '(Total New Check-in)', 'number' => '111', 'amount' => '₱ 51,352.00'],
    //             ['description' => 'Extension', 'sub' => null, 'number' => '', 'amount' => '₱ 784.00'],
    //             ['description' => 'Transfer', 'sub' => null, 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Miscellaneous charges', 'sub' => null, 'number' => '', 'amount' => '-'],
    //             ['description' => 'Food', 'sub' => null, 'number' => '', 'amount' => '-'],
    //             ['description' => 'Drink', 'sub' => null, 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Key/Remote Deposit', 'sub' => '(Current room occupied at the end shift)', 'number' => '50', 'amount' => '₱ 10,000.00'],
    //             ['description' => 'Total Check-out', 'sub' => null, 'number' => '61', 'amount' => '₱ 12,200.00'],
    //             ['description' => 'Guest Deposit', 'sub' => null, 'number' => '', 'amount' => '₱ 10,184.00'],
    //             ['description' => 'Expenses', 'sub' => null, 'number' => '', 'amount' => '₱ 6,697.00'],
    //             ['description' => 'Remittance', 'sub' => null, 'number' => '', 'amount' => '-'],
    //         ],

    //         'cash_reconciliation' => [
    //             'expected_cash' => '₱ 216,808.00',
    //             'actual_cash' => '₱ 205,760.00',
    //             'difference' => '₱ 11,048.00',
    //         ],

    //         'final_sales' => [
    //             'gross_sales' => '₱ 52,136.00',
    //             'expenses' => '₱ 6,697.00',
    //             'discounts' => '-',
    //             'remittance' => '-',
    //             'net_sales' => '₱ 45,439.00',
    //         ],
    //     ],
    //     8 => [
    //         'label' => 'AM SHIFT - March 12, 2026',
    //         'raw_file' => 'raw-files/AM SALES DATA- March 12, 2026.xlsx',

    //         'frontdesk_outgoing' => 'Jeneath Lecias',
    //         'frontdesk_incoming' => 'Jinky Obag and Ruby Gold',
    //         'shift_opened' => 'March 12, 2026 08:17 AM',
    //         'shift_closed' => 'March 12, 2026 08:20 AM',

    //         'cash_drawer' => [
    //             ['description' => 'Opening Cash', 'sub' => '(Net Sales Receive from previous Shift)', 'amount' => '₱ 45,439.00', 'remark' => '-'],
    //             ['description' => 'Key/Remote Deposit', 'sub' => '(Room occupied receive)', 'amount' => '₱ 16,000.00', 'remark' => '-'],
    //             ['description' => 'Guest Deposit', 'sub' => '(Total guest other deposit received)', 'amount' => '₱ 15,542.00', 'remark' => '-'],
    //             ['description' => 'Forwarding Balance', 'sub' => '(From previous Shift)', 'amount' => '₱ 151,185.00', 'remark' => '-'],
    //         ],
    //         'cash_drawer_total' => [
    //             'amount' => '₱ 160,094.00 <span class="text-red-600">(68,072.00)</span>',
    //             'remark' => '-',
    //         ],

    //         'frontdesk_operation' => [
    //             ['description' => 'New Check-in', 'sub' => '(Total New Check-in)', 'number' => '193', 'amount' => '₱ 111,552.00'],
    //             ['description' => 'Extension', 'sub' => null, 'number' => '', 'amount' => '₱ 448.00'],
    //             ['description' => 'Transfer', 'sub' => null, 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Miscellaneous charges', 'sub' => null, 'number' => '', 'amount' => '₱ 500.00'],
    //             ['description' => 'Food', 'sub' => null, 'number' => '', 'amount' => '-'],
    //             ['description' => 'Drink', 'sub' => null, 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Key/Remote Deposit', 'sub' => '(Current room occupied at the end shift)', 'number' => '80', 'amount' => '₱ 16,000.00'],
    //             ['description' => 'Total Check-out', 'sub' => null, 'number' => '113', 'amount' => '₱ 22,600.00'],
    //             ['description' => 'Guest Deposit', 'sub' => null, 'number' => '', 'amount' => '₱ 15,542.00'],
    //             ['description' => 'Expenses', 'sub' => null, 'number' => '', 'amount' => '-'],
    //             ['description' => 'Remittance', 'sub' => null, 'number' => '', 'amount' => '-'],
    //         ],

    //         'cash_reconciliation' => [
    //             'expected_cash' => '₱ 340,666.00',
    //             'actual_cash' => '₱ 16,000.94',
    //             'difference' => '₱ 324,665.06',
    //         ],

    //         'final_sales' => [
    //             'gross_sales' => '₱ 112,500.00',
    //             'expenses' => '₱ 23,500.00',
    //             'discounts' => '-',
    //             'remittance' => '₱ 100,000.00',
    //             'net_sales' => '₱ -11,000.00',
    //         ],
    //     ],
    // ];
}

public function updatedFilter()
{
    $this->loadShift();
}

private function peso($amount)
{
    if ($amount === null || $amount === '') {
        return '';
    }

    return '₱ ' . number_format((float)$amount, 2);
}

private function pesoWithSub($amount,$sub)
{
    if (!$amount) {
        return '';
    }

    $formatted = $this->peso($amount);

    if ($sub) {
        $formatted .= ' (' . number_format((float)$sub,0) . ')';
    }

    return $formatted;
}

private function dateTime($date)
{
    if (!$date) return '';

    return Carbon::parse($date)->format('F d, Y h:i A');
}

private function loadShift()
{
    $s = FrontdeskShift::find($this->filter);

    if (!$s) return;

    $this->selectedShift = [

        'raw_file' => $s->raw_file,

        'frontdesk_outgoing' => $s->frontdesk_outgoing,
        'frontdesk_incoming' => $s->frontdesk_incoming,

        'shift_opened' => $this->dateTime($s->shift_opened),
        'shift_closed' => $this->dateTime($s->shift_closed),

        // CASH DRAWER
        'cash_drawer' => [

            [
                'description' => 'Opening Cash',
                'sub' => 'Net Sales Receive from previous Shift',
                'amount' => $this->pesoWithSub(
                    $s->opening_cash_amount,
                    $s->opening_cash_sub_amount
                ),
                'remark' => $s->opening_cash_remark
            ],

            [
                'description' => 'Key Deposit',
                'sub' => 'Room occupied receive',
                'amount' => $this->pesoWithSub(
                    $s->key_amount,
                    $s->key_sub_amount
                ),
                'remark' => $s->key_remarks
            ],

            [
                'description' => 'Guest Deposit',
                'sub' => 'Total client other deposit received',
                'amount' => $this->pesoWithSub(
                    $s->guest_deposit_amount,
                    $s->guest_deposit_sub_amount
                ),
                'remark' => $s->guest_deposit_amount_remark
            ],

            [
                'description' => 'Forwarding Balance',
                'sub' => 'From previous Shift',
                'amount' => $this->pesoWithSub(
                    $s->forwarding_balance_amount,
                    $s->forwarding_balance_sub_amount
                ),
                'remark' => $s->forwarding_balance_remark
            ],
        ],

        'cash_drawer_total' => [
            'amount' => $this->pesoWithSub(
                $s->total_cash_amount,
                $s->total_cash_sub_amount
            ),
            'remark' => $s->total_cash_remark
        ],

        // FRONTDESK OPERATION A
        'frontdesk_operation_a' => [

            [
                'description' => 'New Check-in',
                'sub' => 'Total New Check-in',
                'number' => $s->new_check_in_number,
                'amount' => $this->peso($s->new_check_in_amount)
            ],

            [
                'description' => 'Extension',
                'sub' => '',
                'number' => $s->extension_number,
                'amount' => $this->peso($s->extension_amount)
            ],

            [
                'description' => 'Transfer',
                'sub' => '',
                'number' => $s->transfer_number,
                'amount' => $this->peso($s->transfer_amount)
            ],

            [
                'description' => 'Miscellaneous',
                'sub' => '',
                'number' => $s->miscellaneous_number,
                'amount' => $this->peso($s->miscellaneous_amount)
            ],

            [
                'description' => 'Food',
                'sub' => '',
                'number' => $s->food_number,
                'amount' => $this->peso($s->food_amount)
            ],

            [
                'description' => 'Drink',
                'sub' => '',
                'number' => $s->drink_number,
                'amount' => $this->peso($s->drink_amount)
            ],

            [
                'description' => 'Others',
                'sub' => 'Foods and Drinks from POS',
                'number' => $s->other_number,
                'amount' => $this->peso($s->other_amount)
            ],

            [
                'description' => 'Total',
                'sub' => '',
                'number' => $s->total_number,
                'amount' => $this->peso($s->total_amount),
                'is_bold' => true
            ],
        ],

        // FRONTDESK OPERATION B
        // ['description' => 'Forwarded Room Check-In', 'sub' => '(Previous Shift)', 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Key/Remote Deposit', 'sub' => '(Current room occupied at the end shift)', 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Forwarded Room Guest Deposit', 'sub' => '(Previous Shift)', 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Guest Deposit', 'sub' => '(Current room occupied at the end shift)', 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Total Check-Out', 'sub' => null, 'number' => '-', 'amount' => '-'],
    //             ['description' => 'Expenses', 'sub' => null, 'number' => '-', 'amount' => '-'],
        'frontdesk_operation_b' => [

            [
                'description' => 'Forwarded Room Check-In',
                'sub' => 'Previous Shift',
                'number' => $s->forwarded_room_check_in_number,
                'amount' => $this->peso($s->forwarded_room_check_in_amount)
            ],

            [
                'description' => 'Key/Remote Deposit',
                'sub' => 'Current room occupied at the end shift',
                'number' => $s->key_remote_number,
                'amount' => $this->peso($s->key_remote_amount)
            ],

            [
                'description' => 'Forwarded Room Guest Deposit',
                'sub' => 'Current room occupied at the end shift',
                'number' => $s->forwarded_guest_deposit_number,
                'amount' => $this->peso($s->forwarded_guest_deposit_amount)
            ],

            [
                'description' => 'Guest Deposit',
                'sub' => '',
                'number' => $s->current_guest_deposit_number,
                'amount' => $this->peso($s->current_guest_deposit_amount)
            ],

            [
                'description' => 'Total Check-Out',
                'sub' => '',
                'number' => $s->total_check_out_number,
                'amount' => $this->peso($s->total_check_out_amount)
            ],

            [
                'description' => 'Expenses',
                'sub' => '',
                'number' => $s->expenses_number,
                'amount' => $this->peso($s->expenses_amount)
            ],
        ],

        // FINAL SALES
        'final_sales' => [
            'gross_sales' => $this->peso($s->gross_sales),
            'refund' => $this->peso($s->refund),
            'expenses' => $this->peso($s->expenses),
            'discounts' => $this->peso($s->discount),
            'net_sales' => $this->peso($s->net_sales),
        ],

        // CASH POSITION
        'cash_position_summary' => [
            'opening_cash' => $this->peso($s->opening_cash),
            'forwarded_balance' => $this->peso($s->forwarded_balance),
            'net_sales' => $this->peso($s->cash_net_sales),
            'remittance' => $this->peso($s->remittance),
        ],

        // RECONCILIATION
        'cash_reconciliation' => [
            'expected_cash' => $this->peso($s->expected_cash),
            'actual_cash' => $this->peso($s->actual_cash),
            'difference' => $this->peso($s->difference),
        ],
    ];
}

public function getSelectedShiftProperty(): array
{
    return $this->shifts[$this->filter] ?? [];
}


    public function render()
    {
        return view('livewire.back-office.temporary-frontdesk-report');
    }
}
