# HotelV4 — Complete Schema (Reviewed & Corrected)

**Date:** March 30, 2026
**Modules Completed:** Branches, Roles, Users, User Profiles, Kiosk, Rooms, Floors, Pricing, Extensions, Discounts, Guest Lifecycle, Shifts & Cash, Transactions, Housekeeping, Activity Logs, POS & Menu
**Remaining:** Reports (deferred — computed from existing tables)

---

## Critical Rule: Snapshot Amounts

**Every financial record stores the actual amount at the time of the action.** Rate FKs are for tracing/reference only — never recalculate historical amounts from `rates` or `extension_rates` tables. If a rate changes from ₱500 to ₱600, past stays still show ₱500 because that's what was snapshotted.

This applies to: `stays`, `stay_extensions`, `room_transfers`, `applied_discounts`, and `transactions` (to be designed).

---

## All Tables (35 custom + 5 Spatie + 1 package = 41 total)

### 1. `branches`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| name | string | NO | | Branch name |
| code | string | NO | | Unique short code (e.g., "ALM-001") |
| address | string | YES | NULL | Full address |
| city | string | YES | NULL | City |
| province | string | YES | NULL | Province |
| region | string | YES | NULL | Region (for future grouping) |
| contact_number | string | YES | NULL | Branch phone |
| email | string | YES | NULL | Branch email |
| is_active | boolean | NO | true | Active status |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### 2. `branch_settings`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches (unique) |
| initial_deposit | decimal(10,2) | NO | 200.00 | Default deposit amount |
| kiosk_time_limit | integer | NO | 10 | Kiosk request expiry (minutes) |
| extension_cycle_threshold | integer | NO | 24 | Hours per cycle before reset |
| authorization_code | string | YES | NULL | Code for overrides (transaction, cleaning, transfer) |
| discounts_enabled | boolean | NO | true | Master switch — if false, no discounts at this branch |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### 3. `users`

Auth only. Personal info lives in `user_profiles`.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| email | string | NO | | Unique, for login |
| password | string | NO | | Hashed |
| email_verified_at | timestamp | YES | NULL | |
| is_active | boolean | NO | true | Can this user log in? |
| remember_token | string | YES | NULL | |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

> V3's `name`, `branch_name`, `time_in`, `shift`, `cash_drawer_id`, `assigned_frontdesks`, `roomboy_assigned_floor_id`, `roomboy_cleaning_room_id` all removed. Identity in `user_profiles`, branch in `branch_user`, shift state in `shifts`, roomboy state in `cleaning_tasks` / `floor_user`.

---

### 3b. `user_profiles`

Personal information, separated from auth. 1:1 with users.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| user_id | foreignId | NO | | FK to users (unique, 1:1) |
| first_name | string | NO | | |
| last_name | string | NO | | |
| middle_name | string | YES | NULL | |
| suffix | string | YES | NULL | Jr., Sr., III, etc. |
| date_of_birth | date | YES | NULL | |
| sex | string | YES | NULL | male / female |
| civil_status | string | YES | NULL | single / married / widowed / separated |
| contact_number | string | YES | NULL | Phone |
| emergency_contact_name | string | YES | NULL | |
| emergency_contact_number | string | YES | NULL | |
| address | string | YES | NULL | Full home address |
| profile_photo_path | string(2048) | YES | NULL | |
| passcode | string | YES | NULL | PIN for shift auth (frontdesk) |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

> **Unique constraint:** `(user_id)`

---

### 4. `branch_user` (Pivot)

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| user_id | foreignId | NO | | FK to users |
| branch_id | foreignId | NO | | FK to branches |
| assignment_type | string | NO | 'primary' | primary / deployed / oversight |
| assigned_at | datetime | NO | | When assigned |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

> **Unique constraint:** `(user_id, branch_id)`

---

### 5. `floors`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| floor_number | integer | NO | | Floor number |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### 6. `room_types`

Room categories — for display and filtering only. NOT for pricing.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| name | string | NO | | Type name (Standard, Deluxe, Suite) |
| description | string | YES | NULL | Type description |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### 7. `rooms`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| floor_id | foreignId | NO | | FK to floors |
| room_type_id | foreignId | NO | | FK to room_types (display/filtering only) |
| room_number | integer | NO | | Room number |
| bed_type | string | NO | | single / double / twin / queen / king |
| area | string | YES | NULL | Building wing (e.g., "Main", "Annex") |
| status | string | NO | 'available' | Room status |
| is_priority | boolean | NO | false | Priority room flag |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

**Room Statuses:** `available`, `occupied`, `reserved`, `maintenance`, `uncleaned`, `cleaning`, `cleaned`

---

### 8. `staying_hours`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| hours | integer | NO | | Number of hours (6, 12, 18, 24) |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### 9. `rates`

Check-in pricing. Per-room.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| room_id | foreignId | NO | | FK to rooms |
| staying_hour_id | foreignId | NO | | FK to staying_hours |
| amount | decimal(10,2) | NO | | Rate amount |
| is_available | boolean | NO | true | Whether this rate is offered |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

> **Unique constraint:** `(room_id, staying_hour_id)`

---

### 10. `extension_hours`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| hours | integer | NO | | Number of hours (1, 2, 3, 6) |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### 11. `extension_rates`

Extension pricing. Per-room.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| room_id | foreignId | NO | | FK to rooms |
| extension_hour_id | foreignId | NO | | FK to extension_hours |
| amount | decimal(10,2) | NO | | Extension rate amount |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

> **Unique constraint:** `(room_id, extension_hour_id)`

---

### 12. `stays`

**Replaces V3's `guests` + `checkin_details`.** One source of truth per guest visit. Includes snapshots of all data that might change later.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| code | string(20) | NO | | Unique stay code (UUID-based, for QR) |
| **Guest Info** | | | | |
| guest_name | string | NO | | Guest name |
| guest_contact | string | YES | NULL | Contact number |
| **Room & Rate (Live — updates on transfer)** | | | | |
| room_id | foreignId | NO | | FK to rooms (current room) |
| rate_id | foreignId | NO | | FK to rates (reference only) |
| **Snapshots (Frozen at check-in — never change)** | | | | |
| snapshot_room_number | integer | NO | | Room number at check-in |
| snapshot_room_type_name | string | NO | | Room type name at check-in |
| snapshot_bed_type | string | NO | | Bed type at check-in |
| snapshot_floor_number | integer | NO | | Floor number at check-in |
| snapshot_rate_hours | integer | NO | | Hours from the rate at check-in |
| snapshot_rate_amount | decimal(10,2) | NO | | Rate amount at check-in |
| **Booking** | | | | |
| initial_hours | integer | NO | | Hours booked at check-in |
| initial_amount | decimal(10,2) | NO | | Amount charged at check-in |
| is_long_stay | boolean | NO | false | Long stay flag |
| long_stay_days | integer | YES | NULL | Days for long stay |
| **Timing** | | | | |
| check_in_at | datetime | NO | | Actual check-in time |
| expected_checkout_at | datetime | NO | | Expected checkout (extends with each extension) |
| actual_checkout_at | datetime | YES | NULL | Actual checkout time (null while active) |
| **Cycle Tracking** | | | | |
| cycle_hours | integer | NO | 0 | Position in extension cycle (0 to threshold) |
| charge_original_rate_next | boolean | NO | false | Next extension charges original rate? |
| **Payment Summary (Running totals)** | | | | |
| total_deposit_in | decimal(10,2) | NO | 0 | Cache: total deposit collected |
| total_charges | decimal(10,2) | NO | 0 | Cache: running total of all charges |
| total_paid | decimal(10,2) | NO | 0 | Cache: running total of all payments |
| **Status** | | | | |
| status | string | NO | 'active' | active / checked_out / cancelled |
| **Audit** | | | | |
| checked_in_by | foreignId | YES | NULL | FK to users |
| checked_out_by | foreignId | YES | NULL | FK to users |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

> **Unique constraint:** `(code)`

---

### 13. `stay_extensions`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| stay_id | foreignId | NO | | FK to stays |
| room_id | foreignId | NO | | FK to rooms |
| hours | integer | NO | | Hours extended |
| amount | decimal(10,2) | NO | | **Snapshot:** actual amount charged |
| charge_type | string | NO | | 'extension' or 'original' |
| rate_source_id | bigint | NO | | FK to rates or extension_rates (reference) |
| rate_source_type | string | NO | | 'rate' or 'extension_rate' (polymorphic) |
| cycle_hours_before | integer | NO | | Cycle position before |
| cycle_hours_after | integer | NO | | Cycle position after |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### 14. `room_transfers`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| stay_id | foreignId | NO | | FK to stays |
| from_room_id | foreignId | NO | | FK to rooms |
| to_room_id | foreignId | NO | | FK to rooms |
| from_rate_amount | decimal(10,2) | NO | | **Snapshot:** old room rate |
| to_rate_amount | decimal(10,2) | NO | | **Snapshot:** new room rate |
| price_difference | decimal(10,2) | NO | 0 | Difference charged/refunded |
| transfer_reason_id | foreignId | YES | NULL | FK to transfer_reasons |
| transferred_by | foreignId | NO | | FK to users |
| created_at | timestamp | YES | | |

---

### 15. `transfer_reasons`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| reason | string | NO | | Transfer reason |
| is_active | boolean | NO | true | Active status |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### 16. `discounts`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| name | string | NO | | Discount name |
| description | string | YES | NULL | Description |
| discount_type | string | NO | | 'percentage' or 'fixed' |
| discount_value | decimal(10,2) | NO | | 20.00 for 20%, or 50.00 for ₱50 |
| max_discount | decimal(10,2) | YES | NULL | Cap for percentage discounts |
| requires_verification | boolean | NO | false | Needs ID check? |
| is_active | boolean | NO | true | Active status |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### 17. `applied_discounts`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| discountable_type | string | NO | | Polymorphic type |
| discountable_id | bigint | NO | | Polymorphic FK |
| discount_id | foreignId | NO | | FK to discounts (reference) |
| snapshot_discount_name | string | NO | | **Snapshot:** discount name |
| snapshot_discount_type | string | NO | | **Snapshot:** percentage or fixed |
| snapshot_discount_value | decimal(10,2) | NO | | **Snapshot:** the rate value |
| original_amount | decimal(10,2) | NO | | Amount before discount |
| discount_amount | decimal(10,2) | NO | | Actual deduction |
| final_amount | decimal(10,2) | NO | | Amount after discount |
| applied_by | foreignId | YES | NULL | FK to users (null if kiosk) |
| verified_by | foreignId | YES | NULL | FK to users (ID check) |
| notes | text | YES | NULL | Verification notes |
| created_at | timestamp | YES | | |

---

### 18. `kiosk_terminals`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| name | string | NO | | Display name |
| device_token | string(64) | NO | | Sanctum API token (hashed) |
| is_active | boolean | NO | true | Remotely deactivatable |
| last_heartbeat_at | datetime | YES | NULL | Last device ping |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### 19. `kiosk_requests`

Temporary. Deleted after resolution.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| kiosk_terminal_id | foreignId | NO | | FK to kiosk_terminals |
| request_type | string | NO | | check_in / check_out |
| stay_id | foreignId | YES | NULL | FK to stays (check-out only) |
| guest_name | string | YES | NULL | Check-in only |
| guest_contact | string | YES | NULL | Check-in only |
| room_type_id | foreignId | YES | NULL | Check-in only |
| rate_id | foreignId | YES | NULL | Check-in only |
| room_id | foreignId | YES | NULL | Room guest selected |
| expires_at | datetime | NO | | Auto-expiry time |
| created_at | timestamp | YES | | |

---

### 20. `cash_drawers`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| name | string | NO | | Drawer name |
| is_active | boolean | NO | false | Currently in use? |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### 21. `shifts`

**Replaces V3's `shifts` + `shift_logs` + `frontdesk_shifts`.**

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| user_id | foreignId | NO | | FK to users (primary frontdesk) |
| partner_user_id | foreignId | YES | NULL | FK to users (partner) |
| partner_name | string | YES | NULL | If partner not a system user |
| cash_drawer_id | foreignId | NO | | FK to cash_drawers |
| shift_period | string | NO | | 'AM' or 'PM' |
| started_at | datetime | NO | | Shift start |
| ended_at | datetime | YES | NULL | Shift end |
| opening_cash | decimal(10,2) | NO | | Cash at start |
| closing_cash | decimal(10,2) | YES | NULL | Cash at end |
| total_payments | decimal(10,2) | NO | 0 | Cache: all payments received |
| total_deposit_collected | decimal(10,2) | NO | 0 | Cache: deposits collected |
| total_deposit_refunded | decimal(10,2) | NO | 0 | Cache: deposits refunded |
| total_remittances | decimal(10,2) | NO | 0 | Cache: cash to management |
| total_expenses | decimal(10,2) | NO | 0 | Cache: expenses paid |
| expected_cash | decimal(10,2) | YES | NULL | Calculated at shift close |
| difference | decimal(10,2) | YES | NULL | closing - expected |
| status | string | NO | 'active' | active / closed |
| notes | text | YES | NULL | End-of-shift notes |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### 22. `remittances`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| shift_id | foreignId | NO | | FK to shifts |
| user_id | foreignId | NO | | FK to users |
| amount | decimal(10,2) | NO | | Amount |
| description | text | YES | NULL | Notes |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### 23. `expense_categories`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| name | string | NO | | Category name |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### 24. `expenses`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| shift_id | foreignId | NO | | FK to shifts |
| user_id | foreignId | NO | | FK to users |
| expense_category_id | foreignId | NO | | FK to expense_categories |
| name | string | NO | | Expense name |
| description | string | YES | NULL | Description |
| amount | decimal(10,2) | NO | | Amount |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### 25. `transactions`

**The financial backbone.** Every money event during a guest's stay. Immutable — never update, only append.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| stay_id | foreignId | NO | | FK to stays |
| shift_id | foreignId | NO | | FK to shifts (which shift's drawer) |
| type | string | NO | | See 9 types below |
| amount | decimal(10,2) | NO | | Always positive. Direction determined by type. |
| description | string | NO | | Human-readable (e.g., "Room Charge 6hrs Room #101") |
| remarks | text | YES | NULL | Extra notes |
| linked_transaction_id | foreignId | YES | NULL | FK to transactions (payment → charge, void → charge) |
| source_type | string | YES | NULL | Polymorphic: 'stay_extension', 'room_transfer' |
| source_id | bigint | YES | NULL | Polymorphic FK to originating record |
| created_by | foreignId | NO | | FK to users (who created this) |
| created_at | timestamp | YES | | Immutable — no updated_at |

> **No `updated_at`** — transactions are immutable. Never update, only insert.

**10 Transaction Types:**

| Type | What | linked_transaction_id | Cash Effect |
|------|------|---------------|-------------|
| room_charge | Check-in room rate | NULL | — |
| extension | Stay extension charge | NULL | — |
| transfer_fee | Room transfer price difference | NULL | — |
| food | Food & beverages | NULL | — |
| amenity | Hotel items (towel, etc.) | NULL | — |
| damage | Damage charges (lost key, broken remote) | NULL | — |
| deposit_in | Deposit collected (check-in, excess) | NULL | Cash IN |
| deposit_out | Deposit used to pay charge or refunded | → charge ID or NULL | Cash OUT if refund (NULL ref) |
| payment | Cash received for a charge | → charge ID | Cash IN |
| void | Cancel/correct a charge (requires auth code) | → voided charge ID | Reverses original |

**Void flow (for overrides/corrections):**
```
#45  food     300.00   "Burger x2" (original — wrong amount)
#46  void     300.00   linked_transaction_id=45  "Voided: price correction, auth by Admin Maria"
#47  food     150.00   "Burger x2" (corrected charge)
```
Immutability preserved. Full audit trail. Admin must provide authorization_code.

**Rules:**
1. Every charge gets paid by exactly ONE `payment` or `deposit_out` with `linked_transaction_id` pointing to the charge.
2. Deposit refund at checkout: `deposit_out` with `linked_transaction_id = NULL`.
3. "Pay All" creates one payment record per unpaid charge (same shift_id, same created_at).
4. No partial payments on a single charge.
5. `stays.status = 'checked_out'` = bill is fully settled.

**Key Queries:**
```sql
-- Unpaid charges
SELECT * FROM transactions t WHERE t.stay_id = ? AND t.type IN ('room_charge','extension','food','amenity','damage','transfer_fee')
AND t.id NOT IN (SELECT linked_transaction_id FROM transactions WHERE linked_transaction_id IS NOT NULL AND type IN ('payment','deposit_out'))

-- Deposit balance
SELECT SUM(CASE WHEN type='deposit_in' THEN amount ELSE 0 END) - SUM(CASE WHEN type='deposit_out' THEN amount ELSE 0 END) FROM transactions WHERE stay_id = ?

-- Cash in drawer for shift
SELECT SUM(CASE WHEN type IN ('payment','deposit_in') THEN amount ELSE 0 END) - SUM(CASE WHEN type='deposit_out' AND linked_transaction_id IS NULL THEN amount ELSE 0 END) FROM transactions WHERE shift_id = ?
```

**Eliminates V3's:** `cash_on_drawers` table, `transaction_types` seeder, mutating paid_amount/paid_at, JSON assigned_frontdesk_id, integer amounts, dead Type 3, unused is_co/is_override columns.

---

### 25b. `transaction_items`

Line items for food/amenity orders. One transaction can have multiple items (like a cart).

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| transaction_id | foreignId | NO | | FK to transactions |
| menu_item_id | foreignId | YES | NULL | FK to menu_items (null for non-menu charges like damage) |
| name | string | NO | | **Snapshot:** item name at time of order |
| quantity | integer | NO | 1 | |
| unit_price | decimal(10,2) | NO | | **Snapshot:** price per unit at time of order |
| amount | decimal(10,2) | NO | | quantity × unit_price |
| created_at | timestamp | YES | | |

> **Snapshot rule applies:** `name` and `unit_price` are frozen at order time. If menu price changes later, past orders still show the original price.

**When used:**
- `type = 'food'` → transaction_items has each food item (Burger x2, Coke x1, etc.)
- `type = 'amenity'` → transaction_items has each amenity (Extra towel x1, Pillow x2)
- `type = 'damage'` → transaction_items has each damaged item (Lost key x1, Broken remote x1)
- `type = 'room_charge'` → no transaction_items (amount is on the transaction itself)
- `type = 'extension'` → no transaction_items (amount is on the transaction itself)

**Example:**
```
transactions:
  #45  type=food  amount=290.00  description="Food order"  stay_id=500

transaction_items:
  transaction_id=45  name="Burger"  qty=2  unit_price=75.00  amount=150.00
  transaction_id=45  name="Coke"    qty=2  unit_price=40.00  amount=80.00
  transaction_id=45  name="Fries"   qty=1  unit_price=60.00  amount=60.00
```

Guest bill shows one line "Food order ₱290". Drill down shows the 3 items.

---

### 26. `cleaning_tasks`

**Replaces V3's `cleaning_histories` + `room_boy_reports`.** One table per cleaning event.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| room_id | foreignId | NO | | FK to rooms |
| stay_id | foreignId | NO | | FK to stays (which guest caused this) |
| assigned_to | foreignId | YES | NULL | FK to users (NULL until claimed) |
| checkout_at | datetime | NO | | When guest checked out |
| deadline_at | datetime | NO | | Cleaning deadline (checkout + 3hrs) |
| started_at | datetime | YES | NULL | When roomboy started |
| completed_at | datetime | YES | NULL | When roomboy finished |
| duration_minutes | integer | YES | NULL | completed_at - started_at |
| is_delayed | boolean | NO | false | completed_at > deadline_at |
| is_on_assigned_floor | boolean | NO | false | Room's floor is roomboy's assigned floor |
| status | string | NO | 'pending' | pending / in_progress / completed |
| overridden_by | foreignId | YES | NULL | FK to users (admin override of 15-min minimum) |
| override_reason | string | YES | NULL | Why override was needed |
| created_at | timestamp | YES | | |

> **No `updated_at`** — status changes tracked by started_at/completed_at timestamps.

**Eliminates V3's:** `cleaning_histories` (string datetimes, misleading names), `room_boy_reports` (fake placeholder data), `users.roomboy_cleaning_room_id` (query cleaning_tasks instead), string types for all time fields.

---

### 27. `floor_user` (Pivot)

Roomboy floor assignments. Many-to-many.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| user_id | foreignId | NO | | FK to users |
| floor_id | foreignId | NO | | FK to floors |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

> **Unique constraint:** `(user_id, floor_id)`
>
> Eliminates V3's `users.roomboy_assigned_floor_id` — primary floor is just `user->floors()->first()`.

---

### 28. Activity Logging (Spatie Activitylog Package)

**Uses `spatie/laravel-activitylog` package.** Creates its own `activity_log` table (standard migration from package).

**Two types of logging:**

**Type 1: Automatic Model Auditing** — Add `LogsActivity` trait to models:

| Model | Columns Tracked |
|-------|----------------|
| Rate | amount, is_available |
| ExtensionRate | amount |
| Room | status, room_type_id, floor_id, is_priority |
| Branch | name, is_active |
| BranchSettings | initial_deposit, kiosk_time_limit, extension_cycle_threshold |
| User | email, is_active |
| UserProfile | first_name, last_name, contact_number |
| Discount | name, type, value, is_active |
| Type | name |
| Floor | number |
| CashDrawer | name, is_active |
| TransferReason | reason, is_active |
| MenuItem | name, price, is_active |
| MenuCategory | name, service_area, is_active |

Automatically logs create/update/delete with before/after values in JSON `properties` column.

**Type 2: Manual Business Event Logging** — `activity()->log()` calls:

| Log Name | Events |
|----------|--------|
| frontdesk | stay.check_in, stay.check_out, stay.extend, stay.transfer, transaction.payment, transaction.deposit_in, transaction.deposit_out |
| shift | shift.start, shift.end, shift.remittance, shift.expense |
| housekeeping | cleaning.start, cleaning.complete, cleaning.override |
| auth | auth.login, auth.logout |

**Eliminates V3's:** Custom `activity_logs` table, 58 scattered `ActivityLog::create()` calls, inconsistent naming, missing before/after values, missing check-in/login/status-change logs.

---

### 29. `menu_categories`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| service_area | string | NO | | kitchen / frontdesk / pub / amenity / damage |
| name | string | NO | | Category name |
| sort_order | integer | NO | 0 | Display ordering |
| is_active | boolean | NO | true | |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### 30. `menu_items`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| menu_category_id | foreignId | NO | | FK to menu_categories |
| name | string | NO | | Item name |
| price | decimal(10,2) | NO | | Proper decimal |
| item_code | string | YES | NULL | SKU/code |
| image_path | string | YES | NULL | Item photo |
| is_active | boolean | NO | true | |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

> Service area inherited from category. `menu_items` → `menu_categories.service_area`.

---

### 31. `menu_inventories`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| menu_item_id | foreignId | NO | | FK to menu_items (unique) |
| stock | decimal(10,2) | NO | 0 | Current available servings |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

> **Unique constraint:** `(menu_item_id)`

---

### 32. `menu_stock_logs`

Audit trail for every stock movement. Who added/deducted, when, why, before/after.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| menu_item_id | foreignId | NO | | FK to menu_items |
| type | string | NO | | stock_in / stock_out |
| quantity | decimal(10,2) | NO | | Amount added or deducted |
| stock_before | decimal(10,2) | NO | | Stock level before change |
| stock_after | decimal(10,2) | NO | | Stock level after change |
| reason | string | NO | | manual_add / guest_order / spoilage / adjustment |
| reference_type | string | YES | NULL | Polymorphic (transaction, etc.) |
| reference_id | bigint | YES | NULL | Which record caused this |
| created_by | foreignId | NO | | FK to users |
| created_at | timestamp | YES | | |

> **No `updated_at`** — stock logs are immutable.

**How POS integrates with transactions:**
- Food/amenity orders create a `transactions` record (`type = 'food'` or `type = 'amenity'`)
- Stock deduction creates a `menu_stock_logs` record with `reference_type = 'transaction'`
- No separate POS transaction table — it's the same `transactions` table

**Eliminates V3's:** 9 duplicate POS tables (`menu_categories` + `frontdesk_categories` + `pub_categories` + `menus` + `frontdesk_menus` + `pub_menus` + `inventories` + `frontdesk_inventories` + `pub_inventories`), string prices, no stock audit trail.

---

### 33. Spatie Permission Tables (Standard)

- `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`
- **7 Roles:** superadmin, admin, frontdesk, back_office, roomboy, kitchen_staff, pub_staff

---

## Issues Found & Fixed During Reviews

### First Review (Schema Design)

| # | Issue | Fix |
|---|-------|-----|
| 1 | `stay_extensions.guest_id` → non-existent `guests` table | Changed to `stay_id` → FK to `stays` |
| 2 | `kiosk_requests.guest_id` → non-existent `guests` table | Changed to `stay_id` → FK to `stays` |
| 3 | Rate changes corrupt historical financial data | Added snapshot columns on `stays` and `applied_discounts` |
| 4 | `stay_events` duplicated data already in other tables | Dropped. Timeline built from existing tables via UNION query. |
| 5 | `frontdesks` / `assigned_frontdesks` not addressed | Not needed — `shifts` + `cash_drawers` covers who works where |
| 6 | `kiosk_requests` had unused fields (records are deleted) | Removed `status`, `confirmed_by`, `confirmed_at` |
| 7 | `applied_discounts` didn't snapshot discount details | Added `snapshot_discount_name`, `snapshot_discount_type`, `snapshot_discount_value` |
| 8 | Transactions must link to shifts for running totals | `transactions` has required `shift_id` + `processed_by` |

### Second Review (Transactions, Housekeeping, Users)

| # | Issue | Fix |
|---|-------|-----|
| 9 | V3 transactions mutate records (paid_amount updated) | V4 transactions are immutable — payments are new records |
| 10 | V3 `cash_on_drawers` duplicates transaction data | Eliminated — cash position derived from transactions |
| 11 | V3 `assigned_frontdesk_id` is JSON but model uses belongsTo | Replaced with proper `processed_by` FK to users |
| 12 | V3 transaction amounts are integers | All V4 amounts are `decimal(10,2)` |
| 13 | V3 Type 3 "Kitchen Order" is dead code | Removed — kitchen uses `food` type |
| 14 | V3 `is_co`, `is_override` columns never used | Removed |
| 15 | V3 `cleaning_histories` + `room_boy_reports` duplicate same event | Single `cleaning_tasks` table |
| 16 | V3 cleaning times stored as strings | Proper datetime columns |
| 17 | V3 `total_hours_spent` stores minutes | Renamed `duration_minutes` |
| 18 | V3 `current_assigned_floor_id` is boolean with ID name | Renamed `is_on_assigned_floor` |
| 19 | V3 users table mixes auth with operational state | Split: `users` (auth), `user_profiles` (personal), state in `shifts`/`cleaning_tasks` |
| 20 | V3 `frontdesks` table redundant with users | Eliminated — frontdesk is user with role + passcode on profile |
| 21 | V3 activity logs inconsistent, missing events | Spatie Activitylog package with auto + manual logging |
| 22 | V3 shift forwarding requires complex cumulative chain | V4: every transaction has `shift_id`, reports just query by shift |
| 23 | V3 `frontdesk_shifts` 50+ string columns | Eliminated — all computed from transactions at report time |
| 24 | V3 `autorization_code` typo on branches | Fixed: `authorization_code` on `branch_settings` |
| 25 | V3 transaction override mutates original record | V4 uses `void` type + new corrected charge (immutable) |
| 26 | V3 no branch-level discount master switch in settings | Added `discounts_enabled` to `branch_settings` |
| 27 | V3 `hotel_items` and `requestable_items` are separate tables | Merged into `menu_items` with `service_area = 'damage'` and `'amenity'` |
| 28 | V3 reports use 5 separate report tables | All eliminated — reports query `stays`, `transactions`, `cleaning_tasks` directly |

---

## V3 Tables Eliminated (20 tables → 0)

| Dropped | Replaced By |
|---------|-------------|
| `guests` | `stays` |
| `checkin_details` | `stays` |
| `temporary_check_in_kiosks` | `kiosk_requests` |
| `temporary_reserved` | Room status `reserved` |
| `new_guest_reports` | Query `stays` |
| `check_out_guest_reports` | Query `stays` |
| `extended_guest_reports` | Query `stay_extensions` |
| `transfered_guest_reports` | Query `room_transfers` |
| `shift_logs` | `shifts` |
| `frontdesk_shifts` (50+ cols) | Computed from `transactions` |
| `cash_on_drawers` | Derived from `transactions` |
| `frontdesks` | User with frontdesk role + `user_profiles.passcode` |
| `assigned_frontdesks` | `shifts.user_id` + `shifts.partner_user_id` |
| `stay_events` | UNION query on existing tables |
| `transaction_types` | String `type` column on `transactions` |
| `cleaning_histories` | `cleaning_tasks` |
| `room_boy_reports` | `cleaning_tasks` |
| `unoccupied_room_reports` | Query rooms + stays |
| `activity_logs` | Spatie Activitylog package (`activity_log` table) |
| `hotel_items` | `menu_items` with `service_area = 'damage'` |
| `requestable_items` | `menu_items` with `service_area = 'amenity'` |

---

## Room Table: Columns Removed in V4

| Removed Column | Now Lives In |
|----------------|-------------|
| `rooms.started_cleaning_at` | `cleaning_tasks.started_at` |
| `rooms.time_to_clean` | `cleaning_tasks.deadline_at` |
| `rooms.check_out_time` | `cleaning_tasks.checkout_at` / `stays.actual_checkout_at` |
| `rooms.last_checkin_at` | Query `stays` |
| `rooms.last_checkout_at` | Query `stays` |
| `rooms.time_to_terminate_queue` | Removed (kiosk expiry in `kiosk_requests.expires_at`) |

---

## Users Table: Columns Removed in V4

| Removed Column | Now Lives In |
|----------------|-------------|
| `users.name` | `user_profiles.first_name` + `last_name` |
| `users.branch_id` | `branch_user` pivot |
| `users.branch_name` | Removed — query `branch.name` |
| `users.time_in` | `shifts.started_at` |
| `users.shift` | `shifts.shift_type` |
| `users.cash_drawer_id` | `shifts.cash_drawer_id` |
| `users.assigned_frontdesks` | `shifts.user_id` + `shifts.partner_user_id` |
| `users.roomboy_assigned_floor_id` | `floor_user` pivot |
| `users.roomboy_cleaning_room_id` | Query `cleaning_tasks WHERE status = 'in_progress'` |
| `users.current_team_id` | Removed — unused Jetstream feature |
| `users.profile_photo_path` | `user_profiles.profile_photo_path` |

---

## Modules Still To Design

| # | Module | Key Notes |
|---|--------|-----------|
| 1 | **Reports** | Deferred. All computed from `stays` + `stay_extensions` + `room_transfers` + `transactions` + `cleaning_tasks`. No separate report tables needed. |

## Notes

### Service Areas for menu_items
- `kitchen` — food items managed by kitchen staff
- `frontdesk` — quick items at frontdesk counter
- `pub` — bar/drinks managed by pub staff
- `amenity` — requestable items (extra towel, pillow, etc.) — replaces V3's `requestable_items` table
- `damage` — damage charge catalog (lost key, broken remote, etc.) — replaces V3's `hotel_items` table

### Cache Columns (Source of Truth = Transactions)
These columns are **convenience caches** for fast reads. The `transactions` table is always the source of truth.
- `stays.total_deposit_in`, `stays.total_charges`, `stays.total_paid` — recomputable from transactions per stay
- `shifts.total_payments`, `shifts.total_deposit_collected`, `shifts.total_deposit_refunded`, `shifts.total_remittances`, `shifts.total_expenses` — recomputable from transactions/remittances/expenses per shift

If cache ever drifts, recalculate from transactions. Never trust cache over source.

### Standard Laravel Tables (not custom, no design needed)
- `sessions` — session driver (used for online user detection)
- `password_reset_tokens` — password resets
- `personal_access_tokens` — Sanctum API tokens
- `jobs` / `failed_jobs` — queue system
- `cache` / `cache_locks` — cache driver (if database)
