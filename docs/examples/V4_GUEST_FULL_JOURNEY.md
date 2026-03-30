# V4 Guest Full Journey — Complete Example

One guest from arrival to departure, touching every system: check-in, food, extension, transfer, deposit, damage, and checkout.

---

## Setup

```
Branch: Alma Hotel Branch A
  extension_cycle_threshold: 24
  initial_deposit: 200.00
  authorization_code: "ALMA2026"
  discounts_enabled: true

Room 101 (Standard, Floor 1):
  6hrs = ₱500, 12hrs = ₱800
  Extension: 3hrs = ₱250, 6hrs = ₱400

Room 205 (Deluxe, Floor 2):
  6hrs = ₱700, 12hrs = ₱1,000

Shift: Maria (Shift #10, Drawer 1, AM)
       Juan  (Shift #11, Drawer 2, AM)
```

---

## 10:00 AM — Guest Arrives via Kiosk

Guest uses kiosk tablet to self-check-in.

```
KIOSK TABLET:
  1. Guest selects room type: Standard
  2. Guest selects room: 101
  3. Guest selects hours: 6hrs (₱500)
  4. Guest enters: Name="Pedro Cruz", Contact="09171234567"
  5. Guest submits

kiosk_requests:
  branch_id: 1
  kiosk_terminal_id: 1
  request_type: 'check_in'
  guest_name: "Pedro Cruz"
  guest_contact: "09171234567"
  room_type_id: 1 (Standard)
  rate_id: 5 (Room 101, 6hrs)
  room_id: 101
  expires_at: 10:10 AM (10 min limit)

rooms:
  Room 101 status remains 'available' (held by kiosk request, not status change)

Pusher notification sent to frontdesk → "New kiosk check-in request!"
```

---

## 10:05 AM — Frontdesk Confirms Kiosk Request

Maria at frontdesk sees the notification, guest is at counter.

```
Maria confirms → system creates everything in one DB transaction:

stays:
  id: 500
  branch_id: 1
  code: "ALM-A1B2C3" (UUID-based, for QR)
  guest_name: "Pedro Cruz"
  guest_contact: "09171234567"
  room_id: 101
  rate_id: 5
  snapshot_room_number: 101
  snapshot_room_type_name: "Standard"
  snapshot_bed_type: "double"
  snapshot_floor_number: 1
  snapshot_rate_hours: 6
  snapshot_rate_amount: 500.00
  initial_hours: 6
  initial_amount: 500.00
  check_in_at: 10:05 AM
  expected_checkout_at: 4:05 PM
  cycle_hours: 6
  charge_original_rate_next: false
  total_deposit_in: 200.00
  total_charges: 500.00
  total_paid: 500.00
  status: 'active'
  checked_in_by: Maria

transactions:
  #1  room_charge  500.00  shift_id=10  "Room Charge 6hrs Room #101"
  #2  payment      500.00  shift_id=10  linked_transaction_id=1  "Cash payment"
  #3  deposit_in   200.00  shift_id=10  "Deposit (Key & Remote)"

rooms:
  Room 101 status → 'occupied'

kiosk_requests:
  DELETED (resolved)

activity_log:
  log='frontdesk', description='Guest checked in', subject=Stay #500, causer=Maria
```

Guest receives QR code (printed or displayed).

---

## 11:30 AM — Guest Orders Food (Kitchen)

Kitchen staff receives order, adds to guest bill.

```
Guest orders: Burger x2 (₱75 each), Coke x1 (₱40)
Total: ₱190

transactions:
  #4  food  190.00  shift_id=10  stay_id=500  "Food order"
      created_by: Kitchen Staff

transaction_items:
  transaction_id=4  name="Burger"  qty=2  unit_price=75.00  amount=150.00  menu_item_id=5
  transaction_id=4  name="Coke"    qty=1  unit_price=40.00  amount=40.00   menu_item_id=8

menu_stock_logs:
  menu_item_id=5  type='stock_out'  qty=2  stock_before=50  stock_after=48  reason='guest_order'
  menu_item_id=8  type='stock_out'  qty=1  stock_before=30  stock_after=29  reason='guest_order'

menu_inventories:
  Burger: stock 50 → 48
  Coke: stock 30 → 29

stays:
  total_charges: 500 + 190 = 690.00

Status: Food charge UNPAID (will settle later)
```

---

## 1:00 PM — Guest Orders Amenities

Guest requests extra towel and pillow.

```
transactions:
  #5  amenity  80.00  shift_id=10  stay_id=500  "Amenity order"

transaction_items:
  transaction_id=5  name="Extra Towel"   qty=1  unit_price=50.00  amount=50.00
  transaction_id=5  name="Extra Pillow"  qty=1  unit_price=30.00  amount=30.00

stays:
  total_charges: 690 + 80 = 770.00

Status: Amenity charge UNPAID
```

---

## 2:00 PM — Guest Pays Food with Deposit

Guest doesn't want to pay cash for food. Uses deposit.

```
Deposit balance: ₱200
Food charge #4: ₱190

transactions:
  #6  deposit_out  190.00  shift_id=10  linked_transaction_id=4  "Paid from deposit"

Deposit balance: 200 - 190 = ₱10
stays:
  total_deposit_in: 200.00 (unchanged)
  total_paid: 500 + 190 = 690.00

Food charge #4 is now SETTLED.
```

---

## 3:30 PM — Guest Extends +3 Hours

Guest wants to stay longer. Expected checkout was 4:05 PM.

```
cycle_hours: 6, threshold: 24 → 6+3=9, under 24 → normal extension rate

Extension rate for Room 101, 3hrs = ₱250

transactions:
  #7  extension  250.00  shift_id=10  stay_id=500  "Extension 3hrs"
      source_type='stay_extension'  source_id=1

stay_extensions:
  id: 1
  stay_id: 500
  room_id: 101
  hours: 3
  amount: 250.00
  charge_type: 'extension'
  rate_source_type: 'extension_rate'
  rate_source_id: (extension_rate record id)
  cycle_hours_before: 6
  cycle_hours_after: 9

stays:
  expected_checkout_at: 4:05 PM → 7:05 PM
  cycle_hours: 9
  total_charges: 770 + 250 = 1,020.00

Status: Extension charge UNPAID
```

---

## 5:00 PM — Guest Transfers to Room 205 (Upgrade)

Guest wants a better room. Moves from Standard 101 to Deluxe 205.

```
Old rate: Room 101, 6hrs = ₱500
New rate: Room 205, 6hrs = ₱700
Difference: ₱200 (guest pays more)

room_transfers:
  id: 1
  stay_id: 500
  from_room_id: 101
  to_room_id: 205
  from_rate_amount: 500.00
  to_rate_amount: 700.00
  price_difference: 200.00
  transferred_by: Maria

transactions:
  #8  transfer_fee  200.00  shift_id=10  stay_id=500  "Room transfer difference"
      source_type='room_transfer'  source_id=1

stays:
  room_id: 101 → 205 (live, updated)
  rate_id: updated to Room 205 rate
  total_charges: 1,020 + 200 = 1,220.00
  (snapshots unchanged — they're frozen from check-in)

rooms:
  Room 101: status → 'uncleaned'
  Room 205: status → 'occupied'

cleaning_tasks:
  room_id: 101, stay_id: 500, status: 'pending'
  checkout_at: 5:00 PM, deadline_at: 8:00 PM

Status: Transfer fee UNPAID
```

---

## 6:00 PM — Guest Pays Extension + Transfer with Cash

Guest settles the two unpaid charges.

```
UNPAID:
  #5  amenity       80.00
  #7  extension    250.00
  #8  transfer_fee 200.00
  ─────────────────────
  Total: ₱530

Guest pays ₱600 cash (₱70 excess).

transactions:
  #9   payment     80.00  linked_transaction_id=5   "Cash payment"
  #10  payment    250.00  linked_transaction_id=7   "Cash payment"
  #11  payment    200.00  linked_transaction_id=8   "Cash payment"
  #12  deposit_in  70.00  "Excess from payment"

Deposit balance: 10 + 70 = ₱80
stays:
  total_paid: 690 + 80 + 250 + 200 = 1,220.00
  total_deposit_in: 200 + 70 = 270.00
```

---

## 7:00 PM — Check-Out

Guest is ready to leave.

```
STEP 1: Any unpaid charges?
  → NO. All settled. ✓

STEP 2: Key & remote returned?
  → YES ✓

STEP 3: Claim remaining deposit (₱80)
  transactions:
    #13  deposit_out  80.00  linked_transaction_id=NULL  "Deposit refund at checkout"

  Frontdesk hands ₱80 cash back to Pedro.
  Deposit balance: ₱0 ✓

STEP 4: Finalize checkout
  stays:
    status: 'active' → 'checked_out'
    actual_checkout_at: 7:00 PM
    checked_out_by: Maria

  rooms:
    Room 205: status → 'uncleaned'

  cleaning_tasks:
    room_id: 205, stay_id: 500, status: 'pending'
    checkout_at: 7:00 PM, deadline_at: 10:00 PM

  activity_log:
    log='frontdesk', description='Guest checked out', subject=Stay #500
```

---

## Complete Transaction Log for Stay #500

```
 #  | Type          | Amount  | Linked To | Description                | Shift | Time
────┼───────────────┼─────────┼───────────┼────────────────────────────┼───────┼──────
  1 | room_charge   | 500.00  | —         | Room Charge 6hrs Room #101 | 10    | 10:05
  2 | payment       | 500.00  | #1        | Cash payment               | 10    | 10:05
  3 | deposit_in    | 200.00  | —         | Deposit (Key & Remote)     | 10    | 10:05
  4 | food          | 190.00  | —         | Food order                 | 10    | 11:30
  5 | amenity       |  80.00  | —         | Amenity order              | 10    | 1:00
  6 | deposit_out   | 190.00  | #4        | Paid from deposit          | 10    | 2:00
  7 | extension     | 250.00  | —         | Extension 3hrs             | 10    | 3:30
  8 | transfer_fee  | 200.00  | —         | Room transfer difference   | 10    | 5:00
  9 | payment       |  80.00  | #5        | Cash payment               | 10    | 6:00
 10 | payment       | 250.00  | #7        | Cash payment               | 10    | 6:00
 11 | payment       | 200.00  | #8        | Cash payment               | 10    | 6:00
 12 | deposit_in    |  70.00  | —         | Excess from payment        | 10    | 6:00
 13 | deposit_out   |  80.00  | —         | Deposit refund at checkout | 10    | 7:00
```

**13 transactions. Full audit trail. Every peso accounted for.**

---

## Final Numbers

```
CHARGES:
  Room charge:    ₱500
  Food:           ₱190
  Amenity:        ₱ 80
  Extension:      ₱250
  Transfer fee:   ₱200
  ─────────────────────
  Total charges:  ₱1,220

PAYMENTS:
  Cash at check-in:  ₱500 (room)
  Deposit at food:   ₱190
  Cash at settle:    ₱600 (amenity + extension + transfer + ₱70 excess)
  ─────────────────────────
  Total in:          ₱1,290

DEPOSITS:
  In:   ₱200 (check-in) + ₱70 (excess) = ₱270
  Out:  ₱190 (food) + ₱80 (refund) = ₱270
  Balance: ₱0 ✓

NET: Guest paid ₱1,220 for charges + ₱0 deposit remaining = ₱1,220 total revenue
     Guest received ₱80 refund + ₱0 change on final = paid exactly what was owed ✓
```

---

## Tables Touched During This Journey

| Table | Records Created |
|-------|----------------|
| `kiosk_requests` | 1 (then deleted) |
| `stays` | 1 |
| `transactions` | 13 |
| `transaction_items` | 4 (food: 2, amenity: 2) |
| `stay_extensions` | 1 |
| `room_transfers` | 1 |
| `cleaning_tasks` | 2 (Room 101 + Room 205) |
| `menu_stock_logs` | 2 (burger + coke) |
| `menu_inventories` | 2 (updated stock) |
| `activity_log` | Multiple (auto + manual) |
