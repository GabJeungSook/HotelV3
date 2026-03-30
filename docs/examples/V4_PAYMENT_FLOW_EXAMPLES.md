# V4 Payment Flow Examples

Demonstrates all payment scenarios: individual, pay all, deposit, and checkout settlement.

---

## Setup

```
Guest: Juan Dela Cruz — Room 101
Check-in: 6hrs, ₱500
Deposit: ₱200 (key & remote)
```

---

## Scenario 1: Pay Individual Charge with Cash

Guest orders food, pays immediately.

```
Frontdesk adds fries (₱60), guest pays ₱100 cash.

transactions:
  #4  food        60.00   "Fries x1"                    linked_transaction_id=NULL
  #5  payment    100.00   "Cash payment"                 linked_transaction_id=4
  #6  deposit_in  40.00   "Excess from payment"          linked_transaction_id=NULL

Excess ₱40 saved as deposit.
Deposit balance: ₱200 + ₱40 = ₱240
```

---

## Scenario 2: Pay Individual Charge with Deposit

Guest orders extra towel, pays from deposit.

```
transactions:
  #7  amenity     50.00   "Extra towel x1"               linked_transaction_id=NULL
  #8  deposit_out 50.00   "Paid from deposit"            linked_transaction_id=7

Deposit balance: ₱240 - ₱50 = ₱190
```

---

## Scenario 3: Pay All Unpaid with Cash

Guest has multiple unpaid charges. Frontdesk clicks "Pay All".

```
UNPAID CHARGES:
  #9   food       200.00  "Burger x1, Coke x2"           (unpaid)
  #10  extension  250.00  "Extension 3hrs"                (unpaid)
  #11  amenity     50.00  "Extra pillow"                  (unpaid)
  ──────────────────────
  Total unpaid:   500.00

Guest hands over ₱500 cash.

SYSTEM CREATES (one DB transaction, same shift_id, same created_at):
  #12  payment    200.00  "Cash payment"                  linked_transaction_id=9
  #13  payment    250.00  "Cash payment"                  linked_transaction_id=10
  #14  payment     50.00  "Cash payment"                  linked_transaction_id=11

Each charge linked to its payment. All settled.
```

---

## Scenario 4: Pay All Unpaid with Deposit

Guest has unpaid charges. Deposit covers it all.

```
UNPAID CHARGES:
  #15  food        80.00  "Coke x2"                       (unpaid)
  #16  amenity     50.00  "Extra towel"                   (unpaid)
  ──────────────────────
  Total unpaid:   130.00

Deposit balance: ₱190 (must be >= ₱130)

SYSTEM CREATES:
  #17  deposit_out  80.00  "Paid from deposit"            linked_transaction_id=15
  #18  deposit_out  50.00  "Paid from deposit"            linked_transaction_id=16

Deposit balance: ₱190 - ₱130 = ₱60
Each charge linked to its deposit_out. All settled.
```

---

## Scenario 5: Pay All — Deposit Not Enough

Deposit can't cover the full amount. Guest must pay with cash.

```
UNPAID CHARGES:
  #19  food       200.00  "Burger x2"                     (unpaid)
  #20  extension  250.00  "Extension 3hrs"                (unpaid)
  ──────────────────────
  Total unpaid:   450.00
  Deposit:         60.00  (not enough for "Pay All with Deposit")

OPTION A: Pay all with cash, claim deposit later
  #21  payment    200.00  "Cash payment"                  linked_transaction_id=19
  #22  payment    250.00  "Cash payment"                  linked_transaction_id=20

  Then at checkout, deposit ₱60 refunded.

OPTION B: Pay some with deposit, rest with cash
  Pick charges that fit deposit (₱60):
  — Can't cover food (₱200) or extension (₱250) fully
  — Must pay all with cash

  #21  payment    200.00  "Cash payment"                  linked_transaction_id=19
  #22  payment    250.00  "Cash payment"                  linked_transaction_id=20
```

**Rule: No partial payments on a single charge.** Deposit must cover a charge fully, or use cash.

---

## Scenario 6: Guest Overpays on "Pay All"

```
UNPAID CHARGES:
  #23  food       150.00  (unpaid)
  #24  amenity     50.00  (unpaid)
  ──────────────────────
  Total: ₱200

Guest hands over ₱300.

SYSTEM CREATES:
  #25  payment    150.00  "Cash payment"                  linked_transaction_id=23
  #26  payment     50.00  "Cash payment"                  linked_transaction_id=24
  #27  deposit_in 100.00  "Excess from payment"           linked_transaction_id=NULL

Excess ₱100 saved as deposit.
```

---

## Scenario 7: Full Checkout Settlement

Guest checks out. Here's the complete flow.

```
BEFORE CHECKOUT — Guest's Full Bill:

  CHARGES:
  #1   room_charge  500.00   PAID (at check-in)
  #4   food          60.00   PAID (during stay, cash)
  #7   amenity       50.00   PAID (during stay, deposit)
  #9   food         200.00   UNPAID
  #10  extension    250.00   UNPAID

  DEPOSITS:
  #3   deposit_in   200.00   (check-in)
  #6   deposit_in    40.00   (excess from food payment)
  #8   deposit_out   50.00   (paid amenity)

  Deposit balance: 200 + 40 - 50 = ₱190
  Unpaid charges: 200 + 250 = ₱450

STEP 1: System checks — unpaid charges exist? YES → must settle first.

STEP 2: Frontdesk clicks "Pay All with Cash"
  #28  payment    200.00  linked_transaction_id=9
  #29  payment    250.00  linked_transaction_id=10

  Guest pays ₱450 cash.
  All charges now settled. ✓

STEP 3: Key & remote returned? YES

STEP 4: Claim remaining deposit (₱190 refund)
  #30  deposit_out  190.00  linked_transaction_id=NULL  "Deposit refund at checkout"

  Frontdesk hands ₱190 cash back to guest.
  Deposit balance: 0 ✓

STEP 5: Checkout finalized
  stays: status='checked_out', actual_checkout_at=NOW(), checked_out_by=Maria
  rooms: status='uncleaned'
  cleaning_tasks: created (pending)
```

---

## Scenario 8: Checkout — Key NOT Returned

```
STEP 3: Key & remote returned? NO

  System auto-creates damage charge:
  #28  damage      100.00  "Lost room key"               linked_transaction_id=NULL

  Deposit balance: ₱190
  Unpaid: ₱100 (damage)

  System auto-pays from deposit:
  #29  deposit_out  100.00  "Paid from deposit"           linked_transaction_id=28

  Deposit balance: ₱190 - ₱100 = ₱90

STEP 4: Claim remaining deposit (₱90)
  #30  deposit_out   90.00  linked_transaction_id=NULL  "Deposit refund at checkout"

  Frontdesk hands ₱90 back to guest.
```

---

## Scenario 9: Checkout — Guest Owes More Than Deposit

```
Unpaid charges: ₱500
Deposit balance: ₱190

OPTION: Pay all with cash
  #28  payment    500.00  (3 payment records, one per charge)
  Deposit ₱190 refunded to guest.

  OR guest says "use deposit + cash":
  — Frontdesk pays individual charges with deposit first
  — Then pays remaining with cash
  — Then no deposit to refund
```

---

## How to Query Payment Status

```sql
-- Is charge #9 paid?
SELECT EXISTS(
  SELECT 1 FROM transactions
  WHERE linked_transaction_id = 9
  AND type IN ('payment', 'deposit_out')
)

-- All unpaid charges for a stay
SELECT * FROM transactions t
WHERE t.stay_id = :stay_id
AND t.type IN ('room_charge','extension','food','amenity','damage','transfer_fee')
AND t.id NOT IN (
  SELECT linked_transaction_id FROM transactions
  WHERE linked_transaction_id IS NOT NULL
  AND type IN ('payment', 'deposit_out')
)

-- Deposit balance for a stay
SELECT
  SUM(CASE WHEN type = 'deposit_in' THEN amount ELSE 0 END) -
  SUM(CASE WHEN type = 'deposit_out' THEN amount ELSE 0 END) as balance
FROM transactions
WHERE stay_id = :stay_id

-- Total unpaid amount
SELECT SUM(t.amount) as total_unpaid
FROM transactions t
WHERE t.stay_id = :stay_id
AND t.type IN ('room_charge','extension','food','amenity','damage','transfer_fee')
AND t.id NOT IN (
  SELECT linked_transaction_id FROM transactions
  WHERE linked_transaction_id IS NOT NULL
  AND type IN ('payment', 'deposit_out')
)
```

---

## Rules Summary

| Rule | Description |
|------|-------------|
| **One charge = one payment** | Every charge is settled by exactly one `payment` or `deposit_out` |
| **No partial payments** | Deposit must cover a charge fully, or use cash |
| **Pay All = multiple records** | Creates one payment per unpaid charge (same timestamp) |
| **Excess = deposit_in** | Overpayment saved as new deposit |
| **Deposit refund = deposit_out with NULL link** | `linked_transaction_id = NULL` means refund |
| **Checkout requires ₱0 unpaid** | All charges settled before checkout allowed |
| **Immutable** | Never update transactions, only insert new ones |
