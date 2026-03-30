# V4 Guest Journey — Kiosk Check-in Path

Complete journey: kiosk check-in → kitchen food → pub drinks → extension (with cycle reset) → amenity → deposit payment → checkout via kiosk → frontdesk settlement.

---

## Setup

```
Branch: Alma Hotel Branch A
  extension_cycle_threshold: 12
  initial_deposit: 200.00
  discounts_enabled: true

Room 101 (Standard, Floor 1):
  6hrs = ₱500, 12hrs = ₱800
  Extension: 3hrs = ₱250, 6hrs = ₱400

Discount: "Senior Citizen" (20% off, requires_verification=true)

Shifts active:
  Maria (Shift #10, Drawer 1, AM)
  Kitchen Staff (role: kitchen_staff)
  Pub Staff (role: pub_staff)
```

---

## 9:00 AM — Senior Guest Arrives at Kiosk

Guest is a senior citizen wanting 6 hours.

```
KIOSK SCREEN:
  1. Select type: Standard
  2. Select room: 101
  3. Select hours: 6hrs (₱500)
  4. Enter name: "Lola Santos"
  5. Contact: "09171234567"
  6. Discount: selects "Senior Citizen (20% off)"
     → Screen shows: "Please present valid Senior ID at counter"
  7. Submit

kiosk_requests:
  kiosk_terminal_id: 1
  request_type: 'check_in'
  guest_name: "Lola Santos"
  room_type_id: 1
  rate_id: 5 (Room 101, 6hrs)
  room_id: 101
  expires_at: 9:10 AM

Pusher → frontdesk notified
```

---

## 9:03 AM — Frontdesk Confirms with Discount Verification

Maria opens the request. Guest presents Senior Citizen ID.

```
Discount calculation:
  Original: ₱500
  Senior 20%: -₱100
  Final: ₱400

stays:
  id: 600
  code: "ALM-X7Y8Z9"
  guest_name: "Lola Santos"
  room_id: 101
  snapshot_rate_amount: 500.00  ← original rate snapshotted
  initial_amount: 400.00       ← actual amount after discount
  initial_hours: 6
  check_in_at: 9:03 AM
  expected_checkout_at: 3:03 PM
  cycle_hours: 6
  charge_original_rate_next: false
  total_deposit_in: 200.00
  total_charges: 400.00
  total_paid: 400.00
  status: 'active'
  checked_in_by: Maria

applied_discounts:
  discountable_type: 'stay'
  discountable_id: 600
  discount_id: 1 (Senior Citizen)
  snapshot_discount_name: "Senior Citizen"
  snapshot_discount_type: "percentage"
  snapshot_discount_value: 20.00
  original_amount: 500.00
  discount_amount: 100.00
  final_amount: 400.00
  applied_by: NULL (kiosk)
  verified_by: Maria       ← Maria checked the ID
  notes: "Senior ID #SC-2024-12345 verified"

transactions:
  #1  room_charge  400.00  "Room Charge 6hrs Room #101 (Senior 20% off)"
  #2  payment      400.00  linked_transaction_id=1
  #3  deposit_in   200.00  "Deposit (Key & Remote)"

rooms: Room 101 → 'occupied'
kiosk_requests: DELETED
```

---

## 10:30 AM — Guest Orders from Kitchen

Lola calls room service. Kitchen staff processes.

```
transactions:
  #4  food  175.00  shift_id=10  stay_id=600  created_by=Kitchen Staff
      "Food order (Kitchen)"

transaction_items:
  transaction_id=4  name="Adobo"     qty=1  unit_price=120.00  amount=120.00
  transaction_id=4  name="Rice"      qty=1  unit_price=25.00   amount=25.00
  transaction_id=4  name="Iced Tea"  qty=1  unit_price=30.00   amount=30.00

menu_stock_logs:
  Adobo:    stock_out 1, 20→19, reason='guest_order'
  Rice:     stock_out 1, 50→49, reason='guest_order'
  Iced Tea: stock_out 1, 40→39, reason='guest_order'

stays: total_charges = 400 + 175 = 575.00
Status: Food UNPAID
```

---

## 12:00 PM — Guest Orders from Pub

Lola orders drinks. Pub staff processes.

```
transactions:
  #5  food  90.00  shift_id=10  stay_id=600  created_by=Pub Staff
      "Food order (Pub)"

transaction_items:
  transaction_id=5  name="San Mig Light"  qty=2  unit_price=45.00  amount=90.00

menu_stock_logs:
  San Mig Light: stock_out 2, 100→98, reason='guest_order'

stays: total_charges = 575 + 90 = 665.00
Status: Pub order UNPAID
```

---

## 1:00 PM — Guest Extends +3hrs (Normal)

```
cycle: 6+3=9, under 12 threshold → normal extension rate
Extension rate Room 101, 3hrs = ₱250

transactions:
  #6  extension  250.00  stay_id=600  "Extension 3hrs"

stay_extensions:
  hours=3, amount=250.00, charge_type='extension'
  cycle_hours_before=6, cycle_hours_after=9

stays:
  expected_checkout_at: 3:03 PM → 6:03 PM
  cycle_hours: 9
  total_charges: 665 + 250 = 915.00

Status: Extension UNPAID
```

---

## 3:00 PM — Guest Extends +6hrs (CROSSES 12! SPLIT!)

```
cycle: 9+6=15, CROSSES 12!

Split:
  3hrs to reach 12 → extension rate for 3hrs = ₱250
  3hrs after 12    → original rate for 3hrs = ???

  Wait — Room 101 has rates for 6hrs (₱500) and 12hrs (₱800).
  No 3hr original rate exists!

  System: firstOrFail() → ERROR. Admin must create 3hr rate for Room 101.

  Let's say admin has: 3hrs = ₱300 for Room 101.

  Split charge: ₱250 (extension) + ₱300 (original) = ₱550

transactions:
  #7  extension  550.00  stay_id=600  "Extension 6hrs (split: 3hrs ext + 3hrs orig)"

stay_extensions:
  hours=6, amount=550.00, charge_type='split'
  cycle_hours_before=9, cycle_hours_after=3

stays:
  expected_checkout_at: 6:03 PM → 12:03 AM
  cycle_hours: 3 (15-12)
  charge_original_rate_next: true
  total_charges: 915 + 550 = 1,465.00

  ──── CYCLE 1 COMPLETE ────
```

---

## 5:00 PM — Guest Requests Amenity

```
transactions:
  #8  amenity  50.00  stay_id=600  "Amenity order"

transaction_items:
  transaction_id=8  name="Extra Pillow"  qty=1  unit_price=50.00  amount=50.00

stays: total_charges = 1,465 + 50 = 1,515.00
Status: UNPAID
```

---

## 7:00 PM — Guest Pays Some Charges with Deposit

Lola pays kitchen food (₱175) from deposit.

```
Deposit balance: ₱200

transactions:
  #9  deposit_out  175.00  linked_transaction_id=4  "Paid from deposit"

Deposit balance: 200 - 175 = ₱25
stays: total_paid = 400 + 175 = 575.00
Food #4: SETTLED
```

---

## 9:00 PM — Guest Extends +3hrs (POST-RESET, ORIGINAL RATE!)

```
charge_original_rate_next = TRUE, cycle_hours = 3

Charge: ORIGINAL rate for Room 101, 3hrs = ₱300 (not extension ₱250!)

transactions:
  #10  extension  300.00  stay_id=600  "Extension 3hrs (original rate, post-reset)"

stay_extensions:
  hours=3, amount=300.00, charge_type='original'
  cycle_hours_before=3, cycle_hours_after=6

stays:
  expected_checkout_at: 12:03 AM → 3:03 AM
  cycle_hours: 6 (3+3)
  charge_original_rate_next: false  ← flag consumed
  total_charges: 1,515 + 300 = 1,815.00
```

---

## 11:00 PM — Shift Change (Maria → Night Shift Pedro)

```
Maria closes shift #10:
  → counts drawer, reconciles, logs out

Pedro opens shift #20:
  → selects Drawer 1, enters opening cash
  → fresh start, no chain

All subsequent transactions for Lola → shift_id=20
```

---

## 2:30 AM — Guest Uses Kiosk for Checkout

Lola goes to kiosk, scans QR code.

```
KIOSK SCREEN:
  Room: 101
  Guest: Lola Santos
  Check-in: 9:03 AM

  BILL SUMMARY:
  Room charge (6hrs, Senior):    ₱400    PAID
  Kitchen food:                  ₱175    PAID (deposit)
  Pub drinks:                    ₱ 90    UNPAID
  Extension 3hrs:                ₱250    UNPAID
  Extension 6hrs (split):        ₱550    UNPAID
  Amenity:                       ₱ 50    UNPAID
  Extension 3hrs (original):     ₱300    UNPAID
  ─────────────────────────────────
  Total charges:                ₱1,815
  Total paid:                   ₱  575
  Deposit remaining:            ₱   25
  ─────────────────────────────────
  Balance due:                  ₱1,240

  "Please proceed to counter for payment"

kiosk_requests:
  request_type: 'check_out'
  stay_id: 600
  expires_at: 2:40 AM

Pusher → frontdesk notified
```

---

## 2:35 AM — Frontdesk Pedro Settles the Bill

```
UNPAID CHARGES:
  #5   pub food      90.00
  #6   extension    250.00
  #7   extension    550.00
  #8   amenity       50.00
  #10  extension    300.00
  ───────────────────────
  Total unpaid:   1,240.00

Deposit: ₱25

Pedro: "Pay all with cash"
Guest pays ₱1,240

transactions (shift_id=20):
  #11  payment     90.00  linked_transaction_id=5
  #12  payment    250.00  linked_transaction_id=6
  #13  payment    550.00  linked_transaction_id=7
  #14  payment     50.00  linked_transaction_id=8
  #15  payment    300.00  linked_transaction_id=10

All charges SETTLED. ✓

STEP: Key & remote returned? YES

Claim deposit (₱25):
  #16  deposit_out  25.00  linked_transaction_id=NULL  "Deposit refund"

Pedro hands ₱25 back to Lola.

FINALIZE:
  stays: status='checked_out', actual_checkout_at=2:35 AM, checked_out_by=Pedro
  rooms: Room 101 → 'uncleaned'
  cleaning_tasks: created (pending)
  kiosk_requests: DELETED
```

---

## Complete Transaction Log for Stay #600

```
 #  | Type         | Amount   | Linked | Description                        | Shift | Who
────┼──────────────┼──────────┼────────┼────────────────────────────────────┼───────┼──────
  1 | room_charge  |   400.00 | —      | Room Charge 6hrs (Senior 20% off)  | 10    | Maria
  2 | payment      |   400.00 | #1     | Cash payment                       | 10    | Maria
  3 | deposit_in   |   200.00 | —      | Deposit (Key & Remote)             | 10    | Maria
  4 | food         |   175.00 | —      | Food order (Kitchen)               | 10    | Kitchen
  5 | food         |    90.00 | —      | Food order (Pub)                   | 10    | Pub
  6 | extension    |   250.00 | —      | Extension 3hrs                     | 10    | Maria
  7 | extension    |   550.00 | —      | Extension 6hrs (split)             | 10    | Maria
  8 | amenity      |    50.00 | —      | Amenity order                      | 10    | Maria
  9 | deposit_out  |   175.00 | #4     | Paid from deposit                  | 10    | Maria
 10 | extension    |   300.00 | —      | Extension 3hrs (original, reset)   | 10    | Maria
 11 | payment      |    90.00 | #5     | Cash payment                       | 20    | Pedro
 12 | payment      |   250.00 | #6     | Cash payment                       | 20    | Pedro
 13 | payment      |   550.00 | #7     | Cash payment                       | 20    | Pedro
 14 | payment      |    50.00 | #8     | Cash payment                       | 20    | Pedro
 15 | payment      |   300.00 | #10    | Cash payment                       | 20    | Pedro
 16 | deposit_out  |    25.00 | —      | Deposit refund                     | 20    | Pedro

16 transactions. 3 extensions (normal + split + original). 2 shifts. Full trail.
```

---

## Final Numbers

```
CHARGES:    ₱1,815 (room 400 + kitchen 175 + pub 90 + ext 250+550+300 + amenity 50)
PAID:       ₱1,815 (cash 400+90+250+550+50+300=1,640 + deposit 175)
DEPOSIT IN: ₱200
DEPOSIT OUT:₱200 (175 food + 25 refund)
DISCOUNT:   ₱100 (Senior Citizen 20% on ₱500 room)
NET REVENUE:₱1,815

Maria's shift #10: collected ₱400 room + ₱200 deposit, processed charges
Pedro's shift #20: collected ₱1,240 cash, refunded ₱25 deposit
```

---

## Systems Touched

| System | What Happened |
|--------|--------------|
| **Kiosk** | Check-in request + checkout request |
| **Frontdesk** | Confirmed check-in, processed extensions, settled bill |
| **Kitchen POS** | Added food order, stock deducted |
| **Pub POS** | Added drink order, stock deducted |
| **Discount** | Senior Citizen verified and applied |
| **Extension Cycle** | Normal → split (crossed 12) → original (post-reset) |
| **Deposit** | Collected → used for food → refunded remainder |
| **Shift** | Spanned 2 shifts (AM Maria → Night Pedro) |
| **Cleaning** | Task created at checkout |
| **Activity Log** | Check-in, discount verified, extensions, checkout all logged |
