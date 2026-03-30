# V4 Design Decisions & Rules

This document captures important design decisions, business rules, and constraints that are not visible in the schema alone. These must be enforced at the application level during implementation.

---

## 1. Shift Must Be Active Before Any Transaction

**Rule:** Frontdesk CANNOT process any transaction (check-in, payment, food order, etc.) without an active shift.

**Why:** V3 allowed transactions without shifts (`shift_log_id` was nullable). This caused orphaned transactions with no shift attribution, breaking reports and cash reconciliation.

**V4 Enforcement:**
- `transactions.shift_id` is NOT NULL at database level
- Application middleware must block access to transaction screens without active shift
- Flow: Login → Select Cash Drawer → Enter Opening Cash → Shift Created → Access Granted

```
ENFORCED FLOW:
  Login
    ↓
  Select Cash Drawer (from available inactive drawers)
    ↓
  Enter Opening Cash (physically count the drawer)
    ↓
  Shift Created (shift_period = AM/PM based on time)
    ↓
  Room Monitoring / Check-in / Transactions — NOW ACCESSIBLE
    ↓
  End Shift: Count cash → Enter closing amount → Reconcile → Logout
```

**If no active shift:** Redirect to shift start screen. No exceptions.

---

## 2. Each Shift Is Self-Contained (No Forwarded Balance Chain)

**Rule:** Each shift starts fresh with its own `opening_cash`. No cumulative chain from previous shifts.

**Why:** V3's forwarded balance required walking ALL previous shifts chronologically. One wrong shift = all subsequent reports wrong. This caused a month of debugging.

**V4 Approach:**
```
SHIFT A (Maria, 8AM-8PM):
  opening_cash: 1,000 (Maria counts drawer)
  + transactions during her shift
  - remittances, expenses, deposit refunds
  = expected_cash at close
  closing_cash: Maria counts drawer again
  difference: closing - expected

  Maria takes cash, leaves some for next shift.

SHIFT B (Juan, 8PM-8AM):
  opening_cash: 1,000 (Juan counts what's physically in drawer)
  → Fresh start. No dependency on Maria's numbers.
  → Juan's expected_cash = his opening + his transactions - his outflows
```

**Key insight:** `opening_cash` is always the REAL counted amount, not a calculated carryover. This breaks the chain.

---

## 3. Transactions Are Immutable (Append-Only)

**Rule:** Once a transaction is created, it is NEVER updated. Corrections use new records.

**Why:** V3 mutated `paid_amount`, `paid_at`, and even `payable_amount` (during transfers). This destroyed historical data and made auditing impossible.

**V4 Approach:**
- Payment → creates new `payment` transaction with `linked_transaction_id` → charge
- Void → creates new `void` transaction with `linked_transaction_id` → voided charge
- Override → void the original + create corrected charge

```
WRONG (V3): UPDATE transactions SET paid_amount = 500 WHERE id = 45
RIGHT (V4): INSERT transactions (type='payment', amount=500, linked_transaction_id=45)
```

---

## 4. Snapshot Rule

**Rule:** Every financial record stores the actual amount at the time of action. Rate FKs are for tracing only — never recalculate from current rates.

**Why:** If admin changes a rate from 500 to 600, past stays must still show 500.

**Where snapshots exist:**
- `stays.snapshot_rate_amount`, `snapshot_room_number`, `snapshot_room_type_name`, etc.
- `stay_extensions.amount` — actual charged amount
- `room_transfers.from_rate_amount`, `to_rate_amount`
- `applied_discounts.snapshot_discount_name`, `snapshot_discount_type`, `snapshot_discount_value`
- `transactions.amount` — the actual amount at time of event
- `transaction_items.name`, `unit_price` — frozen at order time

---

## 5. Cache Columns (Source of Truth = Transactions)

**Rule:** Running totals on `stays` and `shifts` are convenience caches. The `transactions` table is always the source of truth.

**Cache columns on stays:**
- `total_deposit_in` — recomputable: `SUM(amount) WHERE type='deposit_in' AND stay_id=?`
- `total_charges` — recomputable: `SUM(amount) WHERE type IN (charge types) AND stay_id=?`
- `total_paid` — recomputable: `SUM(amount) WHERE type='payment' AND stay_id=?`

**Cache columns on shifts:**
- `total_payments` — recomputable from transactions
- `total_deposit_collected` — recomputable from transactions
- `total_deposit_refunded` — recomputable from transactions
- `total_remittances` — recomputable from remittances table
- `total_expenses` — recomputable from expenses table

**If cache drifts:** Recalculate from source. Never trust cache over transactions.

---

## 6. Deposit Flow

**Rule:** Deposit is money held as security (key + remote) AND usable as a wallet for charges during stay.

**Flow:**
```
CHECK-IN:
  deposit_in → cash collected, held as security

DURING STAY:
  deposit_out + linked_transaction_id → deposit pays a specific charge
  (one charge = one deposit_out, no partial)

CHECKOUT:
  All charges must be paid first (cash or deposit)
  Remaining deposit → deposit_out with linked_transaction_id = NULL (refund)

  If key not returned → damage charge auto-created, paid from deposit
```

**Each branch sets its own deposit amount** in `branch_settings.initial_deposit`.

---

## 7. Extension Cycle Reset

**Rule:** After a guest accumulates X hours of extensions (set by `branch_settings.extension_cycle_threshold`), the next extension charges original rate instead of cheap extension rate.

**Tracked on stays:**
- `cycle_hours` — position in current cycle (0 to threshold)
- `charge_original_rate_next` — if true, next extension uses original rate

**Three scenarios:**
1. Normal: `cycle_hours + extension < threshold` → charge extension rate
2. Crosses boundary: split charge (extension rate before threshold + original rate after)
3. Post-reset: `charge_original_rate_next = true` → charge original rate, then reset flag

---

## 8. Checkout Enforcement

**Rule:** ALL charges must be paid before checkout is allowed. No unpaid balances at checkout.

**Flow:**
1. System checks: any unpaid charges? → Block checkout
2. Frontdesk settles all charges (cash or deposit)
3. Key/remote returned? → If no, auto-create damage charge
4. Remaining deposit refunded
5. Stay status → 'checked_out'
6. Room status → 'uncleaned'
7. Cleaning task auto-created

---

## 9. Kiosk Device Authentication

**Rule:** Kiosk tablets authenticate via device token, not user login.

**Flow:**
1. Admin registers kiosk in dashboard → system generates unique code
2. Tablet enters code once → receives API token (Sanctum)
3. Token stored locally → tablet always ready
4. Admin can deactivate remotely (`is_active = false`)
5. Heartbeat tracks if device is online

**No email/password on public tablets.** Device token is the authentication.

---

## 10. POS Integration (No Separate POS Transactions)

**Rule:** Food/amenity/damage charges create regular `transactions` records. No separate POS transaction table.

**Flow:**
```
Kitchen staff adds food to guest bill:
  → transactions record: type='food', amount=290
  → transaction_items: Burger x2 (150), Coke x2 (80), Fries x1 (60)
  → menu_stock_logs: stock_out for each item
  → menu_inventories: stock decremented
```

**Service areas** (kitchen, frontdesk, pub, amenity, damage) control which role sees which menu items. Same tables, different filters.

---

## 11. Reports Are Computed, Not Stored

**Rule:** No separate report tables. All reports are queries on existing data.

**V3 had:** `new_guest_reports`, `check_out_guest_reports`, `extended_guest_reports`, `transfered_guest_reports`, `room_boy_reports`, `unoccupied_room_reports`, `frontdesk_shifts` (50+ columns)

**V4:** All eliminated. Reports query `stays`, `transactions`, `stay_extensions`, `room_transfers`, `cleaning_tasks` directly. Always accurate, no sync issues.

---

## 12. Soft Deletes vs is_active

**Rule:** Never delete configuration data. Use `is_active = false` to disable.

**Applies to:** room_types, rooms, discounts, menu_categories, menu_items, cash_drawers, transfer_reasons, users, branches, kiosk_terminals

**Why:** Past records reference these entities via FK. Deleting would break constraints or orphan data. Disabling hides them from UI while preserving historical integrity.

---

## 13. Activity Logging (Two Types)

**Automatic (Spatie trait):** Admin config changes (rates, rooms, users, discounts, menu items) — logs before/after values automatically.

**Manual (activity() calls):** Business events (check-in, checkout, payment, shift start/end, cleaning) — logged explicitly in code with context.

**Log categories:** admin, frontdesk, shift, housekeeping, auth

---

## 14. Authorization Code for Overrides

**Rule:** Sensitive operations require branch authorization code.

**Operations requiring auth code:**
- Void a transaction (cancel/correct a charge)
- Override cleaning 15-minute minimum
- Room transfer override

**Stored in:** `branch_settings.authorization_code`

**Audit:** All overrides are logged — void transactions have full trail, cleaning overrides have `overridden_by` + `override_reason`.
