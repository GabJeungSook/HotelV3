<div>


{{-- BASIC INFO --}}
<div class="bg-white shadow rounded-xl p-6 space-y-4">
<h2 class="text-lg font-semibold border-b pb-2">Shift Information</h2>
<div class="grid grid-cols-1 gap-4">
<input type="text" wire:model="form.label"
placeholder="Shift Schedule"
class="input"/>    
</div>
<div class="grid grid-cols-2 gap-4">

<input type="text" wire:model="form.frontdesk_outgoing"
placeholder="Frontdesk Outgoing"
class="input"/>

<input type="text" wire:model="form.frontdesk_incoming"
placeholder="Frontdesk Incoming"
class="input"/>

<input type="datetime-local"
wire:model="form.shift_opened"
class="input"/>

<input type="datetime-local"
wire:model="form.shift_closed"
class="input"/>

<input type="file" wire:model="raw_file" class="input col-span-2"/>

</div>
</div>

{{-- CASH DRAWER --}}
<div class="bg-white shadow rounded-xl p-6 space-y-4">
<h2 class="text-lg font-semibold border-b pb-2">Cash Drawer</h2>

<div class="grid grid-cols-3 gap-4">

<!-- Opening Cash -->
<div>
<label class="text-sm font-medium">Opening Cash</label>
<div class="flex gap-2">
<input type="text" wire:model="form.opening_cash_amount" placeholder="Amount" class="input w-full">
<input type="text" wire:model="form.opening_cash_sub_amount" placeholder="Sub Amount" class="input w-full">
</div>
<input type="text" wire:model="form.opening_cash_remark" placeholder="Remarks" class="input w-full mt-1">
</div>

<!-- Key Deposit -->
<div>
<label class="text-sm font-medium">Key Deposit</label>
<div class="flex gap-2">
<input type="text" wire:model="form.key_amount" placeholder="Amount" class="input w-full">
<input type="text" wire:model="form.key_sub_amount" placeholder="Sub Amount" class="input w-full">
</div>
<input type="text" wire:model="form.key_remarks" placeholder="Remarks" class="input w-full mt-1">
</div>

<!-- Guest Deposit -->
<div>
<label class="text-sm font-medium">Guest Deposit</label>
<div class="flex gap-2">
<input type="text" wire:model="form.guest_deposit_amount" placeholder="Amount" class="input w-full">
<input type="text" wire:model="form.guest_deposit_sub_amount" placeholder="Sub Amount" class="input w-full">
</div>
<input type="text" wire:model="form.guest_deposit_amount_remark" placeholder="Remarks" class="input w-full mt-1">
</div>

<!-- Forwarding Balance -->
<div>
<label class="text-sm font-medium">Forwarding Balance</label>
<div class="flex gap-2">
<input type="text" wire:model="form.forwarding_balance_amount" placeholder="Amount" class="input w-full">
<input type="text" wire:model="form.forwarding_balance_sub_amount" placeholder="Sub Amount" class="input w-full">
</div>
<input type="text" wire:model="form.forwarding_balance_remark" placeholder="Remarks" class="input w-full mt-1">
</div>

<!-- Total Cash -->
<div>
<label class="text-sm font-medium">Total Cash</label>
<div class="flex gap-2">
<input type="text" wire:model="form.total_cash_amount" placeholder="Amount" class="input w-full">
<input type="text" wire:model="form.total_cash_sub_amount" placeholder="Sub Amount" class="input w-full">
</div>
<input type="text" wire:model="form.total_cash_remark" placeholder="Remarks" class="input w-full mt-1">
</div>

</div>
</div>

{{-- FRONTDESK OPERATION A --}}
<div class="bg-white shadow rounded-xl p-6 space-y-4">

<h2 class="text-lg font-semibold border-b pb-2">
Frontdesk Operation — Sales Summary
</h2>

<div class="grid grid-cols-3 gap-4">

<div>
<label class="text-sm font-medium">New Check-in</label>
<div class="flex gap-2">
<input type="text" wire:model="form.new_check_in_number" placeholder="Number" class="input w-full">
<input type="text" wire:model="form.new_check_in_amount" placeholder="Amount" class="input w-full">
</div>
</div>

<div>
<label class="text-sm font-medium">Extension</label>
<div class="flex gap-2">
<input type="text" wire:model="form.extension_number" placeholder="Number" class="input w-full">
<input type="text" wire:model="form.extension_amount" placeholder="Amount" class="input w-full">
</div>
</div>

<div>
<label class="text-sm font-medium">Transfer</label>
<div class="flex gap-2">
<input type="text" wire:model="form.transfer_number" placeholder="Number" class="input w-full">
<input type="text" wire:model="form.transfer_amount" placeholder="Amount" class="input w-full">
</div>
</div>

<div>
<label class="text-sm font-medium">Miscellaneous</label>
<div class="flex gap-2">
<input type="text" wire:model="form.miscellaneous_number" placeholder="Number" class="input w-full">
<input type="text" wire:model="form.miscellaneous_amount" placeholder="Amount" class="input w-full">
</div>
</div>

<div>
<label class="text-sm font-medium">Food</label>
<div class="flex gap-2">
<input type="text" wire:model="form.food_number" placeholder="Number" class="input w-full">
<input type="text" wire:model="form.food_amount" placeholder="Amount" class="input w-full">
</div>
</div>

<div>
<label class="text-sm font-medium">Drink</label>
<div class="flex gap-2">
<input type="text" wire:model="form.drink_number" placeholder="Number" class="input w-full">
<input type="text" wire:model="form.drink_amount" placeholder="Amount" class="input w-full">
</div>
</div>

<div>
<label class="text-sm font-medium">Others</label>
<div class="flex gap-2">
<input type="text" wire:model="form.other_number" placeholder="Number" class="input w-full">
<input type="text" wire:model="form.other_amount" placeholder="Amount" class="input w-full">
</div>
</div>

<div>
<label class="text-sm font-medium">Total</label>
<div class="flex gap-2">
<input type="text" wire:model="form.total_number" placeholder="Number" class="input w-full">
<input type="text" wire:model="form.total_amount" placeholder="Amount" class="input w-full">
</div>
</div>

</div>
</div>

{{-- FRONTDESK OPERATION B --}}
<div class="bg-white shadow rounded-xl p-6 space-y-4">

<h2 class="text-lg font-semibold border-b pb-2">
Room Status & Deposit
</h2>

<div class="grid grid-cols-3 gap-4">

<div>
<label class="text-sm font-medium">Forwarded Room Check-in</label>
<div class="flex gap-2">
<input type="text" wire:model="form.forwarded_room_check_in_number" placeholder="Number" class="input w-full">
<input type="text" wire:model="form.forwarded_room_check_in_amount" placeholder="Amount" class="input w-full">
</div>
</div>

<div>
<label class="text-sm font-medium">Key/Remote</label>
<div class="flex gap-2">
<input type="text" wire:model="form.key_remote_number" placeholder="Number" class="input w-full">
<input type="text" wire:model="form.key_remote_amount" placeholder="Amount" class="input w-full">
</div>
</div>

<div>
<label class="text-sm font-medium">Forwarded Guest Deposit</label>
<div class="flex gap-2">
<input type="text" wire:model="form.forwarded_guest_deposit_number" placeholder="Number" class="input w-full">
<input type="text" wire:model="form.forwarded_guest_deposit_amount" placeholder="Amount" class="input w-full">
</div>
</div>

<div>
<label class="text-sm font-medium">Current Guest Deposit</label>
<div class="flex gap-2">
<input type="text" wire:model="form.current_guest_deposit_number" placeholder="Number" class="input w-full">
<input type="text" wire:model="form.current_guest_deposit_amount" placeholder="Amount" class="input w-full">
</div>
</div>

<div>
<label class="text-sm font-medium">Total Check-out</label>
<div class="flex gap-2">
<input type="text" wire:model="form.total_check_out_number" placeholder="Number" class="input w-full">
<input type="text" wire:model="form.total_check_out_amount" placeholder="Amount" class="input w-full">
</div>
</div>

<div>
<label class="text-sm font-medium">Expenses</label>
<div class="flex gap-2">
<input type="text" wire:model="form.expenses_number" placeholder="Number" class="input w-full">
<input type="text" wire:model="form.expenses_amount" placeholder="Amount" class="input w-full">
</div>
</div>

</div>
</div>

{{-- FINAL SALES --}}
<div class="bg-white shadow rounded-xl p-6">
<h2 class="text-lg font-semibold border-b pb-2">Final Sales</h2>

<div class="grid grid-cols-3 gap-4">
<input type="text" wire:model="form.gross_sales" placeholder="Gross Sales" class="input"/>
<input type="text" wire:model="form.refund" placeholder="Refund" class="input"/>
<input type="text" wire:model="form.expenses" placeholder="Expenses" class="input"/>
<input type="text" wire:model="form.discount" placeholder="Discount" class="input"/>
<input type="text" wire:model="form.net_sales" placeholder="Net Sales" class="input"/>
</div>

</div>

{{-- CASH POSITION --}}
<div class="bg-white shadow rounded-xl p-6">

<h2 class="text-lg font-semibold border-b pb-2">Cash Position Summary</h2>

<div class="grid grid-cols-4 gap-4">
<input type="text" wire:model="form.opening_cash" placeholder="Opening Cash" class="input"/>
<input type="text" wire:model="form.forwarded_balance" placeholder="Forwarded Balance" class="input"/>
<input type="text" wire:model="form.cash_net_sales" placeholder="Net Sales" class="input"/>
<input type="text" wire:model="form.remittance" placeholder="Remittance" class="input"/>
</div>

</div>

{{-- RECONCILIATION --}}
<div class="bg-white shadow rounded-xl p-6">

<h2 class="text-lg font-semibold border-b pb-2">Cash Reconciliation</h2>

<div class="grid grid-cols-3 gap-4">
<input type="text" wire:model="form.expected_cash" placeholder="Expected Cash" class="input"/>
<input type="text" wire:model="form.actual_cash" placeholder="Actual Cash" class="input"/>
<input type="text" wire:model="form.difference" placeholder="Difference" class="input"/>
</div>

</div>

</div>