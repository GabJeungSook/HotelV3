# V4 Shift Lifecycle — Complete Example

One full shift from open to close with cash reconciliation. Shows multiple frontdesks, remittances, expenses, and final numbers.

---

## Setup

```
Branch: Alma Hotel Branch A
Shift: AM (8:00 AM - 8:00 PM)
Cash Drawers: Drawer 1, Drawer 2

Staff:
  Maria → primary frontdesk
  Juan  → second frontdesk
```

---

## 8:00 AM — Maria Starts Shift

```
Maria logs in → selects Drawer 1 → counts cash in drawer.

shifts:
  id: 10
  branch_id: 1
  user_id: Maria
  partner_user_id: NULL
  cash_drawer_id: 1 (Drawer 1)
  shift_period: 'AM'
  started_at: 8:00 AM
  ended_at: NULL
  opening_cash: 1,000.00
  status: 'active'

cash_drawers:
  Drawer 1: is_active → true

activity_log:
  log='shift', description='Shift started', properties={opening_cash: 1000, drawer: "Drawer 1"}
```

## 8:05 AM — Juan Starts Shift (Second Drawer)

```
shifts:
  id: 11
  user_id: Juan
  cash_drawer_id: 2 (Drawer 2)
  shift_period: 'AM'
  started_at: 8:05 AM
  opening_cash: 500.00
  status: 'active'
```

**Two shifts running in parallel, each with their own drawer.**

---

## 8:30 AM — Maria Checks In Guest (₱700)

```
Room 101, 6hrs, ₱500 room + ₱200 deposit

transactions (all shift_id=10, Maria's shift):
  #1  room_charge  500.00  created_by=Maria
  #2  payment      500.00  linked_transaction_id=1
  #3  deposit_in   200.00

Maria's Drawer 1:
  1,000 + 500 (room) + 200 (deposit) = ₱1,700
```

## 9:00 AM — Juan Checks In Guest (₱900)

```
Room 205, 6hrs, ₱700 room + ₱200 deposit

transactions (all shift_id=11, Juan's shift):
  #4  room_charge  700.00  created_by=Juan
  #5  payment      700.00  linked_transaction_id=4
  #6  deposit_in   200.00

Juan's Drawer 2:
  500 + 700 + 200 = ₱1,400
```

---

## 10:30 AM — Kitchen Adds Food to Guest in 101

```
Burger x2 = ₱150. Kitchen staff processes, goes to Maria's shift (she's handling this guest).

transactions (shift_id=10):
  #7  food  150.00  stay_id=500  created_by=Kitchen Staff
```

## 11:00 AM — Guest in 101 Pays Food with Cash

```
transactions (shift_id=10):
  #8  payment  150.00  linked_transaction_id=7  created_by=Maria

Maria's Drawer 1:
  1,700 + 150 = ₱1,850
```

## 11:30 AM — Guest in 205 Orders Amenity

```
Extra towel ₱50. Juan processes.

transactions (shift_id=11):
  #9  amenity  50.00  stay_id=501  created_by=Juan

Status: UNPAID (guest will pay later)
```

---

## 12:00 PM — Maria Does Remittance

Drawer has too much cash. Maria hands ₱1,000 to manager.

```
remittances:
  shift_id: 10
  user_id: Maria
  amount: 1,000.00
  description: "Midday cash handoff to supervisor"

shifts (id=10):
  total_remittances: 0 → 1,000.00

Maria's Drawer 1:
  1,850 - 1,000 = ₱850

activity_log:
  log='shift', description='Remittance submitted', properties={amount: 1000}
```

---

## 1:00 PM — Expense Recorded

Maria buys cleaning supplies ₱300.

```
expenses:
  branch_id: 1
  shift_id: 10
  user_id: Maria
  expense_category_id: 1 (Supplies)
  name: "Cleaning supplies"
  amount: 300.00

shifts (id=10):
  total_expenses: 0 → 300.00

Maria's Drawer 1:
  850 - 300 = ₱550
```

---

## 2:00 PM — Guest in 101 Extends +3hrs

```
transactions (shift_id=10):
  #10  extension  250.00  stay_id=500  created_by=Maria

Status: UNPAID
```

## 3:00 PM — Guest in 205 Pays Amenity + Extension

Juan's guest pays ₱50 amenity cash.

```
transactions (shift_id=11):
  #11  payment  50.00  linked_transaction_id=9  created_by=Juan

Juan's Drawer 2:
  1,400 + 50 = ₱1,450
```

---

## 5:00 PM — Guest in 101 Checks Out

```
Unpaid: extension ₱250
Deposit: ₱200

Pays ₱250 cash, claims ₱200 deposit refund.

transactions (shift_id=10):
  #12  payment      250.00  linked_transaction_id=10  "Cash payment"
  #13  deposit_out  200.00  linked_transaction_id=NULL  "Deposit refund"

Maria's Drawer 1:
  550 + 250 (payment) - 200 (refund) = ₱600
```

---

## 6:00 PM — Juan Does Remittance

```
remittances:
  shift_id: 11
  amount: 500.00

Juan's Drawer 2:
  1,450 - 500 = ₱950
```

---

## 7:00 PM — Guest in 205 Checks Out

```
No unpaid charges. Deposit ₱200 refunded.

transactions (shift_id=11):
  #14  deposit_out  200.00  linked_transaction_id=NULL  "Deposit refund"

Juan's Drawer 2:
  950 - 200 = ₱750
```

---

## 8:00 PM — Maria Ends Shift

Maria counts Drawer 1.

```
MARIA'S SHIFT RECONCILIATION (Shift #10):

  Opening cash:              ₱1,000.00
  + Payments received:       ₱  900.00  (#2=500, #8=150, #12=250)
  + Deposits collected:      ₱  200.00  (#3=200)
  - Deposit refunds:         ₱  200.00  (#13=200)
  - Remittances:             ₱1,000.00
  - Expenses:                ₱  300.00
  ─────────────────────────────────────
  Expected cash:             ₱  600.00

  Maria counts drawer:       ₱  600.00
  Difference:                ₱    0.00 ✓ EXACT

shifts (id=10):
  closing_cash: 600.00
  expected_cash: 600.00
  difference: 0.00
  total_payments: 900.00
  total_deposit_collected: 200.00
  total_deposit_refunded: 200.00
  total_remittances: 1,000.00
  total_expenses: 300.00
  ended_at: 8:00 PM
  status: 'closed'

cash_drawers:
  Drawer 1: is_active → false

activity_log:
  log='shift', description='Shift closed', properties={expected: 600, actual: 600, diff: 0}
```

## 8:05 PM — Juan Ends Shift

```
JUAN'S SHIFT RECONCILIATION (Shift #11):

  Opening cash:              ₱  500.00
  + Payments received:       ₱  750.00  (#5=700, #11=50)
  + Deposits collected:      ₱  200.00  (#6=200)
  - Deposit refunds:         ₱  200.00  (#14=200)
  - Remittances:             ₱  500.00
  - Expenses:                ₱    0.00
  ─────────────────────────────────────
  Expected cash:             ₱  750.00

  Juan counts drawer:        ₱  749.00  ← ₱1 SHORT!
  Difference:                ₱   -1.00

shifts (id=11):
  closing_cash: 749.00
  expected_cash: 750.00
  difference: -1.00
  status: 'closed'
```

---

## Combined Branch Sales for AM Shift

```sql
SELECT type, SUM(amount) as total
FROM transactions
WHERE shift_id IN (10, 11) -- both AM shifts
AND type IN ('room_charge','extension','food','amenity')
GROUP BY type
```

```
BRANCH AM SALES:
  Room charges:  ₱1,200  (500 + 700)
  Food:          ₱  150
  Amenities:     ₱   50
  Extensions:    ₱  250
  ─────────────────────
  Gross sales:   ₱1,650
  - Expenses:    ₱  300
  ─────────────────────
  Net sales:     ₱1,350

  Total remittances: ₱1,500 (Maria 1,000 + Juan 500)
  Total shortages:   ₱1.00 (Juan's drawer)
```

---

## Key Takeaways

| Aspect | How It Works |
|--------|-------------|
| **Multiple frontdesks** | Each has own shift + own drawer, independently tracked |
| **Cash goes to correct drawer** | `shift_id` on every transaction links to specific drawer |
| **Reconciliation** | Per-shift: opening + payments + deposits - refunds - remittances - expenses = expected |
| **No chain walk** | Each shift is self-contained. Opening cash = what's physically counted. |
| **Shortages detected** | difference = closing - expected. Juan was ₱1 short. |
| **Cross-shift reports** | Query by shift_id or date range. Same SQL, any scope. |
