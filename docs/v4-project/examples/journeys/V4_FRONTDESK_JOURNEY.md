# V4 Frontdesk Journey — Complete Day

A full day as a frontdesk staff member: login, shift start, check-ins, transactions, remittance, expense, shift close.

---

## Setup

```
Branch: Alma Hotel Branch A
Frontdesk: Maria Santos
Cash Drawer: Drawer 1
Date: Monday, March 30, 2026
```

---

## 7:55 AM — Maria Logs In

```
Maria opens browser → enters email + password → authenticated

System checks: Maria has role 'frontdesk' → redirect to shift setup
System checks: Maria has active shift? → NO → must start shift

Maria CANNOT access room monitoring, check-in, or any transaction screen yet.
```

---

## 8:00 AM — Maria Starts Shift

```
STEP 1: Select Cash Drawer
  Available drawers: Drawer 1 (inactive), Drawer 2 (inactive)
  Maria selects: Drawer 1

STEP 2: Select Partner (optional)
  Working alone today → skip

STEP 3: Enter Opening Cash
  Maria counts Drawer 1: ₱1,000 bills + ₱200 coins = ₱1,200
  Enters: ₱1,200.00

shifts:
  id: 10
  branch_id: 1
  user_id: Maria
  partner_user_id: NULL
  cash_drawer_id: 1
  shift_period: 'AM'
  started_at: 8:00 AM
  opening_cash: 1,200.00
  status: 'active'

cash_drawers: Drawer 1 → is_active: true

activity_log: log='shift', description='Shift started'

Maria now has access to Room Monitoring → ready to work.
```

---

## 8:15 AM — Walk-in Guest Check-In

Guest arrives at counter. No kiosk.

```
Maria clicks Available room 101 → check-in modal opens

ENTERS:
  Guest name: "Juan Dela Cruz"
  Contact: "09171234567"
  Rate: 6hrs (₱500)
  Deposit: ₱200 (from branch_settings.initial_deposit)
  Total to collect: ₱700

Guest pays ₱700 cash.

SYSTEM CREATES:
  stays: id=500, guest_name="Juan Dela Cruz", room_id=101, initial_amount=500.00

  transactions:
    #1  room_charge  500.00  shift_id=10  created_by=Maria
    #2  payment      500.00  shift_id=10  linked_transaction_id=1
    #3  deposit_in   200.00  shift_id=10

  rooms: Room 101 → 'occupied'

Maria's Drawer: 1,200 + 700 = ₱1,900

activity_log: log='frontdesk', description='Guest checked in'
```

---

## 8:30 AM — Kiosk Check-In Request Arrives

Pusher notification pops up on Maria's screen.

```
NOTIFICATION:
  "New kiosk check-in! Room 205, Ana Reyes, 12hrs (₱1,000)"

Maria opens kiosk request → verifies guest at counter.
Guest is a Senior Citizen → Maria checks ID → verifies discount.

Original: ₱1,000
Senior 20%: -₱200
Final: ₱800 + ₱200 deposit = ₱1,000 total

SYSTEM CREATES:
  stays: id=501, guest_name="Ana Reyes", room_id=205, initial_amount=800.00

  applied_discounts:
    discount_id=1, snapshot_discount_name="Senior Citizen"
    original_amount=1,000, discount_amount=200, final_amount=800
    verified_by=Maria, notes="Senior ID #SC-2025-001"

  transactions:
    #4  room_charge  800.00  shift_id=10
    #5  payment      800.00  linked_transaction_id=4
    #6  deposit_in   200.00

Maria's Drawer: 1,900 + 1,000 = ₱2,900

kiosk_requests: DELETED
rooms: Room 205 → 'occupied'
```

---

## 9:30 AM — Guest in 101 Orders Food

Kitchen staff adds food. Maria sees it on guest's bill.

```
transactions:
  #7  food  175.00  stay_id=500  shift_id=10  created_by=Kitchen Staff

transaction_items:
  Burger x1 (₱120), Rice x1 (₱25), Coke x1 (₱30)

Status: UNPAID (on guest's bill)
```

---

## 10:00 AM — Guest in 101 Pays Food with Cash

Guest calls front desk to pay.

```
Maria opens Guest Transaction for Room 101 → sees unpaid food ₱175.
Guest pays ₱200 cash.

transactions:
  #8  payment     175.00  shift_id=10  linked_transaction_id=7
  #9  deposit_in   25.00  shift_id=10  "Excess from payment"

Maria's Drawer: 2,900 + 200 = ₱3,100
Guest 101 deposit: 200 + 25 = ₱225
```

---

## 11:00 AM — Guest in 205 Requests Amenity

```
transactions:
  #10  amenity  50.00  stay_id=501  shift_id=10  "Extra towel"

transaction_items:
  Extra Towel x1 (₱50)

Status: UNPAID
```

---

## 12:00 PM — Remittance (Too Much Cash)

Maria's drawer has ₱3,100. Manager says hand over ₱2,000.

```
remittances:
  shift_id: 10
  user_id: Maria
  amount: 2,000.00
  description: "Midday cash handoff to supervisor Ate Joy"

shifts (id=10): total_remittances → 2,000.00
Maria's Drawer: 3,100 - 2,000 = ₱1,100

activity_log: log='shift', description='Remittance submitted'
```

---

## 1:00 PM — Expense

Maria buys paper for receipt printer.

```
expenses:
  shift_id: 10
  user_id: Maria
  expense_category_id: 2 (Office Supplies)
  name: "Receipt printer paper"
  amount: 150.00

shifts (id=10): total_expenses → 150.00
Maria's Drawer: 1,100 - 150 = ₱950
```

---

## 2:00 PM — Guest in 101 Extends +3hrs

```
transactions:
  #11  extension  250.00  stay_id=500  shift_id=10  "Extension 3hrs"

stay_extensions:
  hours=3, amount=250.00, charge_type='extension'
  cycle_hours_before=6, cycle_hours_after=9

stays (500): expected_checkout_at extended by 3hrs

Status: UNPAID
```

---

## 3:00 PM — Guest in 205 Checks Out (Kiosk Path)

Ana goes to kiosk, scans QR code.

```
KIOSK SHOWS:
  Room 205 — Ana Reyes
  Unpaid: amenity ₱50
  Deposit: ₱200
  → "Please proceed to counter"

kiosk_requests: request_type='check_out', stay_id=501

Maria sees notification → opens checkout.

SETTLE:
  Unpaid amenity ₱50 → pay with deposit
  #12  deposit_out  50.00  linked_transaction_id=10  "Paid from deposit"

  Key returned? YES
  Remaining deposit: 200 - 50 = ₱150 → refund
  #13  deposit_out  150.00  linked_transaction_id=NULL  "Deposit refund"

  Maria hands ₱150 cash back to Ana.

Maria's Drawer: 950 - 150 = ₱800

stays (501): status='checked_out', actual_checkout_at=3:00 PM
rooms: Room 205 → 'uncleaned'
cleaning_tasks: created for Room 205
kiosk_requests: DELETED
```

---

## 5:00 PM — Guest in 101 Checks Out

Juan comes to counter.

```
UNPAID:
  #11  extension  250.00

Deposit: ₱225

Maria: "Pay all with cash"
Guest pays ₱250.

  #14  payment  250.00  linked_transaction_id=11

Key returned? YES
Claim deposit ₱225:
  #15  deposit_out  225.00  linked_transaction_id=NULL  "Deposit refund"

Maria hands ₱225 back to Juan.

Maria's Drawer: 800 + 250 - 225 = ₱825

stays (500): status='checked_out'
rooms: Room 101 → 'uncleaned'
cleaning_tasks: created for Room 101
```

---

## 7:30 PM — One More Check-In Before Shift Ends

Walk-in guest, Room 301, 6hrs ₱500 + ₱200 deposit.

```
transactions:
  #16  room_charge  500.00  shift_id=10
  #17  payment      500.00  linked_transaction_id=16
  #18  deposit_in   200.00

Maria's Drawer: 825 + 700 = ₱1,525
(Guest is still staying when Maria's shift ends — forwarded to next shift)
```

---

## 8:00 PM — Maria Ends Shift

```
MARIA'S SHIFT RECONCILIATION:

  Opening cash:                 ₱1,200.00

  + Payments received:          ₱2,475.00
    #2=500, #5=800, #8=175, #14=250, #17=500 + excess deposit payments

  + Deposits collected:         ₱  625.00
    #3=200, #6=200, #9=25, #18=200

  - Deposit refunds:            ₱  375.00
    #13=150, #15=225

  - Remittances:                ₱2,000.00

  - Expenses:                   ₱  150.00
  ─────────────────────────────────────────
  Expected cash:                ₱1,775.00

  Maria counts drawer:          ₱1,775.00
  Difference:                   ₱    0.00 ✓ EXACT!

shifts (id=10):
  closing_cash: 1,775.00
  expected_cash: 1,775.00
  difference: 0.00
  total_payments: 2,475.00
  total_deposit_collected: 625.00
  total_deposit_refunded: 375.00
  total_remittances: 2,000.00
  total_expenses: 150.00
  ended_at: 8:00 PM
  status: 'closed'

cash_drawers: Drawer 1 → is_active: false
Maria is logged out.

activity_log: log='shift', description='Shift closed'
```

---

## 8:05 PM — Night Shift Pedro Takes Over

```
Pedro logs in → selects Drawer 1 → counts: ₱1,775 in drawer
Pedro enters opening_cash: ₱1,775.00

New shift #20 created. Fresh start.
Guest in Room 301 still staying → forwarded to Pedro's shift.
Any transactions for Room 301 now go to shift_id=20.
```

---

## Maria's Day Summary

| Metric | Count/Amount |
|--------|-------------|
| Guests checked in | 3 |
| Guests checked out | 2 |
| Room charges collected | ₱1,800 |
| Extensions | 1 (₱250) |
| Food orders | 1 (₱175) |
| Amenities | 1 (₱50) |
| Deposits collected | ₱625 |
| Deposits refunded | ₱375 |
| Remittances | ₱2,000 |
| Expenses | ₱150 |
| Discounts applied | 1 (Senior ₱200) |
| Cash shortage | ₱0 |
| Transactions created | 18 |
