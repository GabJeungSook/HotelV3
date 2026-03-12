<?php

namespace App\Http\Livewire\BackOffice;

use Livewire\Component;

class TemporaryFrontdeskReport extends Component
{
    public $date_from;
    public $date_to;
    public $shift;
public int $filter = 1;

public array $shifts = [];

public function mount(): void
{
    $this->shifts = [
        1 => [
            'label' => 'PM SHIFT - March 8-9, 2026',
            'raw_file' => 'raw-files/PM SALES DATA- March 8-9, 2026.xlsx',

            'frontdesk_outgoing' => 'Hannah',
            'frontdesk_incoming' => 'Jeneath',
            'shift_opened' => 'March 08, 2026 08:03 PM',
            'shift_closed' => 'March 09, 2026 07:27 AM',

            'cash_drawer' => [
                ['description' => 'Opening Cash', 'sub' => '(Net Sales Receive from previous Shift)', 'amount' => '₱ 1.00', 'remark' => '-'],
                ['description' => 'Key/Remote Deposit', 'sub' => '(Room occupied receive)', 'amount' => '-', 'remark' => '-'],
                ['description' => 'Client Deposit', 'sub' => '(Total client deposit received)', 'amount' => '-', 'remark' => '-'],
                ['description' => 'Forwarding Balance', 'sub' => '(From previous Shift)', 'amount' => '-', 'remark' => '-'],
            ],
            'cash_drawer_total' => [
                'amount' => '₱ 1.00',
                'remark' => '',
            ],

            'frontdesk_operation' => [
                ['description' => 'New Check-in', 'sub' => '(Total New Check-in)', 'number' => '86', 'amount' => '₱ 32,424.00'],
                ['description' => 'Extension', 'sub' => null, 'number' => '', 'amount' => '₱ 1,456'],
                ['description' => 'Transfer', 'sub' => null, 'number' => '', 'amount' => '-'],
                ['description' => 'Miscellaneous charges', 'sub' => null, 'number' => '', 'amount' => '₱ 340'],
                ['description' => 'Food', 'sub' => null, 'number' => '', 'amount' => '₱ 276'],
                ['description' => 'Drink', 'sub' => null, 'number' => '-', 'amount' => '-'],
                ['description' => 'Key/Remote Deposit', 'sub' => '(Current room occupied at the end shift)', 'number' => '58', 'amount' => '₱ 11,600'],
                ['description' => 'Total Check-out', 'sub' => null, 'number' => '28', 'amount' => '₱ 5,600.00'],
                ['description' => 'Guest Deposit', 'sub' => null, 'number' => '', 'amount' => '-'],
                ['description' => 'Expenses', 'sub' => null, 'number' => '', 'amount' => '-'],
                ['description' => 'Remittance', 'sub' => null, 'number' => '', 'amount' => '-'],
            ],

            'cash_reconciliation' => [
                'expected_cash' => '₱ 46,097.00',
                'actual_cash' => '₱ 45,716.00',
                'difference' => '₱ 381.00',
            ],

            'final_sales' => [
                'gross_sales' => '₱ 34,496.00',
                'expenses' => '-',
                'discounts' => '-',
                'remittance' => '-',
                'net_sales' => '₱ 34,496.00',
            ],
        ],

        2 => [
            'label' => 'AM SHIFT - March 9, 2026',
            'raw_file' => 'raw-files/AM SALES DATA- March 9, 2026.xlsx',

            'frontdesk_outgoing' => 'Jeneath Lecias',
            'frontdesk_incoming' => 'Kathleen Drew and Hannah',
            'shift_opened' => 'March 9, 2026 - 07:28 AM',
            'shift_closed' => 'March 9, 2026 - 08:57 PM',

            'cash_drawer' => [
                ['description' => 'Opening Cash', 'sub' => '(Net Sales Receive from previous Shift)', 'amount' => '₱ 34,496.00', 'remark' => '-'],
                ['description' => 'Key/Remote Deposit', 'sub' => '(Room occupied receive)', 'amount' => '₱ 11,600.00', 'remark' => '-'],
                ['description' => 'Client Deposit', 'sub' => '(Total client deposit received)', 'amount' => '-', 'remark' => '-'],
                ['description' => 'Forwarding Balance', 'sub' => '(From previous Shift)', 'amount' => '₱ 1.00', 'remark' => '-'],
            ],
            'cash_drawer_total' => [
                'amount' => '₱ 45, 716 <span class="text-red-600">(381)</span>',
                'remark' => '-',
            ],

            'frontdesk_operation' => [
                ['description' => 'New Check-in', 'sub' => '(Total New Check-in)', 'number' => '120', 'amount' => '₱ 49, 112.00'],
                ['description' => 'Extension', 'sub' => null, 'number' => '', 'amount' => '₱ 336.00'],
                ['description' => 'Transfer', 'sub' => null, 'number' => '-', 'amount' => '-'],
                ['description' => 'Miscellaneous charges', 'sub' => null, 'number' => '', 'amount' => '₱ 50.00'],
                ['description' => 'Food', 'sub' => null, 'number' => '', 'amount' => '₱ 55.00'],
                ['description' => 'Drink', 'sub' => null, 'number' => '-', 'amount' => '-'],
                ['description' => 'Key/Remote Deposit', 'sub' => '(Current room occupied at the end shift)', 'number' => '89', 'amount' => '₱ 17,800.00'],
                ['description' => 'Total Check-out', 'sub' => null, 'number' => '31', 'amount' => '₱ 6,200.00'],
                ['description' => 'Guest Deposit', 'sub' => null, 'number' => '', 'amount' => '-'],
                ['description' => 'Expenses', 'sub' => null, 'number' => '', 'amount' => '₱ 9,407.00'],
                ['description' => 'Remittance', 'sub' => null, 'number' => '', 'amount' => '-'],
            ],

            'cash_reconciliation' => [
                'expected_cash' => '₱ 92,443.00',
                'actual_cash' => '₱ 86,594.00',
                'difference' => '₱ 5,849.00',
            ],

            'final_sales' => [
                'gross_sales' => '₱ 49,553.00',
                'expenses' => '₱ 9,407.00',
                'discounts' => '-',
                'remittance' => '-',
                'net_sales' => '₱ 40,146.00',
            ],
        ],

        3 => [
            'label' => 'PM SHIFT - March 9-10, 2026',
            'raw_file' => 'raw-files/PM SALES DATA- March 9-10, 2026.xlsx',

            'frontdesk_outgoing' => 'Kathleen Drew and Hannah',
            'frontdesk_incoming' => 'Jeneath Lecias and Ruby Gold',
            'shift_opened' => 'March 9, 2026 - 8:33 PM',
            'shift_closed' => 'March 10, 2026 - 8:37 AM',

            'cash_drawer' => [
                ['description' => 'Opening Cash', 'sub' => '(Net Sales Receive from previous Shift)', 'amount' => '₱ 40,146.00', 'remark' => '-'],
                ['description' => 'Key/Remote Deposit', 'sub' => '(Room occupied receive)', 'amount' => '₱ 17,800.00', 'remark' => '-'],
                ['description' => 'Client Deposit', 'sub' => '(Total client deposit received)', 'amount' => '-', 'remark' => '-'],
                ['description' => 'Forwarding Balance', 'sub' => '(From previous Shift)', 'amount' => '₱ 34,497.00', 'remark' => '-'],
            ],
            'cash_drawer_total' => [
                'amount' => '₱ 86,595.00 <span class="text-red-600">(5,849)</span>',
                'remark' => '-',
            ],

            'frontdesk_operation' => [
                ['description' => 'New Check-in', 'sub' => '(Total New Check-in)', 'number' => '79', 'amount' => '₱ 30,296.00'],
                ['description' => 'Extension', 'sub' => null, 'number' => '', 'amount' => '₱ 112.00'],
                ['description' => 'Transfer', 'sub' => null, 'number' => '-', 'amount' => '-'],
                ['description' => 'Miscellaneous charges', 'sub' => null, 'number' => '', 'amount' => '-'],
                ['description' => 'Food', 'sub' => null, 'number' => '', 'amount' => '₱ 12.00'],
                ['description' => 'Drink', 'sub' => null, 'number' => '-', 'amount' => '-'],
                ['description' => 'Key/Remote Deposit', 'sub' => '(Current room occupied at the end shift)', 'number' => '32', 'amount' => '₱ 6,400.00'],
                ['description' => 'Total Check-out', 'sub' => null, 'number' => '47', 'amount' => '₱ 9,400.00'],
                ['description' => 'Guest Deposit', 'sub' => null, 'number' => '', 'amount' => '₱ 3,939.00'],
                ['description' => 'Expenses', 'sub' => null, 'number' => '', 'amount' => '-'],
                ['description' => 'Remittance', 'sub' => null, 'number' => '', 'amount' => '-'],
            ],

            'cash_reconciliation' => [
                'expected_cash' => '₱ 115,402.00',
                'actual_cash' => '₱ 105,995.00',
                'difference' => '₱ 9,407.00',
            ],

            'final_sales' => [
                'gross_sales' => '₱ 30,420.00',
                'expenses' => '-',
                'discounts' => '-',
                'remittance' => '-',
                'net_sales' => '₱ 30,420.00',
            ],
        ],

        4 => [
            'label' => 'AM SHIFT - March 10, 2026',
            'raw_file' => 'raw-files/PM SALES DATA- March 10, 2026.xlsx',

            'frontdesk_outgoing' => 'Jeneath Lecias and Ruby Gold',
            'frontdesk_incoming' => 'Hannah and Jinky Obag',
            'shift_opened' => 'March 10, 2026 - 8:37 AM',
            'shift_closed' => 'March 10, 2026 - 8:37 PM',

            'cash_drawer' => [
                ['description' => 'Opening Cash', 'sub' => '(Net Sales Receive from previous Shift)', 'amount' => '₱ 30,420.00', 'remark' => '-'],
                ['description' => 'Key/Remote Deposit', 'sub' => '(Room occupied receive)', 'amount' => '₱ 6,400.00', 'remark' => '-'],
                ['description' => 'Client Deposit', 'sub' => '(Total client deposit received)', 'amount' => '₱ 3,939.00', 'remark' => '-'],
                ['description' => 'Forwarding Balance', 'sub' => '(From previous Shift)', 'amount' => '₱ 74,643.00', 'remark' => '-'],
            ],
            'cash_drawer_total' => [
                'amount' => '₱ 105,996.00 <span class="text-red-600">(9,406)</span>',
                'remark' => '-',
            ],

            'frontdesk_operation' => [
                ['description' => 'New Check-in', 'sub' => '(Total New Check-in)', 'number' => '153', 'amount' => '₱ 60,088.00'],
                ['description' => 'Extension', 'sub' => null, 'number' => '', 'amount' => '₱ 784.00'],
                ['description' => 'Transfer', 'sub' => null, 'number' => '-', 'amount' => '-'],
                ['description' => 'Miscellaneous charges', 'sub' => null, 'number' => '', 'amount' => '-'],
                ['description' => 'Food', 'sub' => null, 'number' => '', 'amount' => '₱ 45.00'],
                ['description' => 'Drink', 'sub' => null, 'number' => '-', 'amount' => '-'],
                ['description' => 'Key/Remote Deposit', 'sub' => '(Current room occupied at the end shift)', 'number' => '111', 'amount' => '₱ 22,200.00'],
                ['description' => 'Total Check-out', 'sub' => null, 'number' => '42', 'amount' => '₱ 8,400.00'],
                ['description' => 'Guest Deposit', 'sub' => null, 'number' => '', 'amount' => '₱ 11,606.00'],
                ['description' => 'Expenses', 'sub' => null, 'number' => '', 'amount' => '₱ 15,138.00'],
                ['description' => 'Remittance', 'sub' => null, 'number' => '', 'amount' => '-'],
            ],

            'cash_reconciliation' => [
                'expected_cash' => '₱ 184,647.00',
                'actual_cash' => '₱ 196,543.00',
                'difference' => '₱ 11,896.00',
            ],

            'final_sales' => [
                'gross_sales' => '₱ 60,917.00',
                'expenses' => '₱ 15,138.80',
                'discounts' => '-',
                'remittance' => '-',
                'net_sales' => '₱ 45,778.20',
            ],
        ],
        5 => [
            'label' => 'PM SHIFT - March 10-11, 2026',
            'raw_file' => 'raw-files/AM SALES DATA- March 10-11, 2026.xlsx',

            'frontdesk_outgoing' => 'Hannah and Jinky Obag',
            'frontdesk_incoming' => 'Jeneath Lecias and Seanne Karylle',
            'shift_opened' => 'March 10, 2026 - 8:38 PM',
            'shift_closed' => 'March 11, 2026 - 8:20 AM',

            'cash_drawer' => [
                ['description' => 'Opening Cash', 'sub' => '(Net Sales Receive from previous Shift)', 'amount' => '₱ 45,778.20', 'remark' => '-'],
                ['description' => 'Key/Remote Deposit', 'sub' => '(Room occupied receive)', 'amount' => '₱  22,200.00', 'remark' => '-'],
                ['description' => 'Client Deposit', 'sub' => '(Total client deposit received)', 'amount' => '₱ 11,606.00', 'remark' => '-'],
                ['description' => 'Forwarding Balance', 'sub' => '(From previous Shift)', 'amount' => '₱ 105,063.00', 'remark' => '-'],
            ],
            'cash_drawer_total' => [
                'amount' => '₱ 86,595.00 <span class="text-red-600">(98,052.2)</span>',
                'remark' => '-',
            ],

            'frontdesk_operation' => [
                ['description' => 'New Check-in', 'sub' => '(Total New Check-in)', 'number' => '100', 'amount' => '₱ 35,560.00'],
                ['description' => 'Extension', 'sub' => null, 'number' => '', 'amount' => '₱ 112.00'],
                ['description' => 'Transfer', 'sub' => null, 'number' => '-', 'amount' => '-'],
                ['description' => 'Miscellaneous charges', 'sub' => null, 'number' => '', 'amount' => '₱ 450.00'],
                ['description' => 'Food', 'sub' => null, 'number' => '', 'amount' => '-'],
                ['description' => 'Drink', 'sub' => null, 'number' => '-', 'amount' => '-'],
                ['description' => 'Key/Remote Deposit', 'sub' => '(Current room occupied at the end shift)', 'number' => '33', 'amount' => '₱ 6,600.00'],
                ['description' => 'Total Check-out', 'sub' => null, 'number' => '67', 'amount' => '₱ 13,400.00'],
                ['description' => 'Guest Deposit', 'sub' => null, 'number' => '', 'amount' => '₱ 6,706.00'],
                ['description' => 'Expenses', 'sub' => null, 'number' => '', 'amount' => '-'],
                ['description' => 'Remittance', 'sub' => null, 'number' => '', 'amount' => '-'],
            ],

            'cash_reconciliation' => [
                'expected_cash' => '₱ 200,269.20',
                'actual_cash' => '₱ 105,995.00',
                'difference' => '₱ 94,274.20',
            ],

            'final_sales' => [
                'gross_sales' => '₱ 36,122.00',
                'expenses' => '-',
                'discounts' => '-',
                'remittance' => '-',
                'net_sales' => '₱ 36,112.00',
            ],
        ],
        6 => [
            'label' => 'AM SHIFT - March 11, 2026',
            'raw_file' => 'raw-files/AM SALES DATA- March 11, 2026.xlsx',

            'frontdesk_outgoing' => 'Jeneath Lecias and Seanne Karylle',
            'frontdesk_incoming' => 'Ruby Gold and Jinky Obag',
            'shift_opened' => 'March 11, 2026 8:21 AM',
            'shift_closed' => 'March 11, 2026 8:16 PM',

            'cash_drawer' => [
                ['description' => 'Opening Cash', 'sub' => '(Net Sales Receive from previous Shift)', 'amount' => '₱ 36,122.00', 'remark' => '-'],
                ['description' => 'Key/Remote Deposit', 'sub' => '(Room occupied receive)', 'amount' => '₱ 6,600.00', 'remark' => '-'],
                ['description' => 'Client Deposit', 'sub' => '(Total client deposit received)', 'amount' => '₱ 6,706.00', 'remark' => '-'],
                ['description' => 'Forwarding Balance', 'sub' => '(From previous Shift)', 'amount' => '₱ 105,063.00', 'remark' => '-'],
            ],
            'cash_drawer_total' => [
                'amount' => '₱ 114,444.50 <span class="text-red-600">(40,046.50)</span>',
                'remark' => '-',
            ],

            'frontdesk_operation' => [
                ['description' => 'New Check-in', 'sub' => '(Total New Check-in)', 'number' => '140', 'amount' => '₱ 63,728.00'],
                ['description' => 'Extension', 'sub' => null, 'number' => '', 'amount' => '₱ 224.00'],
                ['description' => 'Transfer', 'sub' => null, 'number' => '-', 'amount' => '-'],
                ['description' => 'Miscellaneous charges', 'sub' => null, 'number' => '', 'amount' => '₱ 270.00'],
                ['description' => 'Food', 'sub' => null, 'number' => '', 'amount' => '-'],
                ['description' => 'Drink', 'sub' => null, 'number' => '-', 'amount' => '-'],
                ['description' => 'Key/Remote Deposit', 'sub' => '(Current room occupied at the end shift)', 'number' => '108', 'amount' => '₱ 21,600.00'],
                ['description' => 'Total Check-out', 'sub' => null, 'number' => '32', 'amount' => '₱ 6,400.00'],
                ['description' => 'Guest Deposit', 'sub' => null, 'number' => '', 'amount' => '₱ 7,300.00'],
                ['description' => 'Expenses', 'sub' => null, 'number' => '', 'amount' => '-'],
                ['description' => 'Remittance', 'sub' => null, 'number' => '', 'amount' => '₱ 100,000.00'],
            ],

            'cash_reconciliation' => [
                'expected_cash' => '₱ 134,307.00',
                'actual_cash' => '₱ 160,094.00',
                'difference' => '₱ 25,787.00',
            ],

            'final_sales' => [
                'gross_sales' => '₱ 64,222.00',
                'expenses' => '-',
                'discounts' => '-',
                'remittance' => '₱ 100,000.00',
                'net_sales' => '₱ -35,778.00',
            ],
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
