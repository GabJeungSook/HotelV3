# V3 Problems Solved in V4

28 issues identified and resolved during the V4 redesign.

---

## The Core Problem: V3 Reports Will Never Be Fully Accurate

**This is the single biggest reason for the V4 rebuild.** The V3 report system is fundamentally broken — not because of bad code, but because the database schema makes accurate reporting impossible. Here's the justification:

### 1. Transactions Can Exist Without a Shift

`shift_log_id` on the transactions table is **nullable** — it was added late (March 2026 migration) as a retrofit. Many transactions, especially from the kitchen module, have `shift_log_id = NULL`. These transactions are **invisible to shift reports**. Revenue is lost in the numbers. There is no way to retroactively fix these records because the system doesn't know which shift they belonged to.

**Impact:** Shift sales report under-counts revenue. Cash reconciliation doesn't match actual cash in drawer. Management can't trust the numbers.

### 2. Forwarded Deposit Calculation Is a Cascading Chain

When a guest spans multiple shifts, V3 calculates the "forwarded deposit" by walking through **every single previous shift chronologically**, building a running balance:

```
Shift 1 balance = deposits - cashouts
Shift 2 balance = Shift 1 balance + deposits - cashouts
Shift 3 balance = Shift 2 balance + deposits - cashouts
...
```

If **any single shift** in this chain has wrong data (missing transaction, wrong timing, overlapping shift boundary), **every subsequent shift's report is wrong**. And the error compounds — it never self-corrects. The team spent over a month debugging report inaccuracies caused by this cascading chain.

**Impact:** Forwarded deposit amounts don't match reality. Shift reports show wrong numbers for every shift after the first error.

### 3. Room Deposit Hardcoded to ₱200

The deposit amount is hardcoded as `200` in **13+ places** across the codebase — in check-in, reports, reconciliation, and forwarding calculations. The database already has `branches.initial_deposit` as a configurable setting, but **the code ignores it and uses the hardcoded value**. If any branch changes their deposit to ₱300, every report calculation using `count × 200` is immediately wrong.

```php
// V3 SalesReportV2.php line 506 — hardcoded, ignores branch setting
$this->forwardedRoomDeposit = $this->forwardedCount * 200;
```

**Impact:** Any branch with a non-200 deposit gets wrong deposit totals on every report.

### 4. Deposit Type Detection Uses String Matching

V3 distinguishes "room key deposit" from "guest deposit" by checking the **remarks string**:

```php
str_contains(strtolower($t->remarks), 'room key')
str_contains(strtolower($t->remarks), 'tv remote')
```

If anyone types the remark slightly differently — "Room Key and TV Remote" instead of "Room Key & TV Remote", or "room key deposit" — the filter fails silently. The deposit gets miscategorized, and the report shows wrong breakdowns.

**Impact:** Deposit categorization is fragile. One typo in remarks = wrong report numbers. No way to detect or prevent this.

### 5. Transactions Are Mutated After Creation

When a guest pays, V3 **updates the original transaction record**:

```php
$transaction->update(['paid_amount' => $amount, 'paid_at' => now()]);
```

When a guest transfers rooms, V3 **rewrites the original check-in transaction**:

```php
Transaction::where('description', 'Guest Check In')->update([
    'payable_amount' => $new_room_rate,
    'paid_amount' => $new_room_rate,
]);
```

The original values are **gone forever**. You can't tell what was originally charged, when it was paid, or how many times it was modified. Historical data is destroyed.

**Impact:** No audit trail. Can't reconstruct what actually happened. Reports show current values, not historical truth.

### 6. Report Tables Must Stay in Sync (They Don't)

V3 creates separate records in `new_guest_reports`, `check_out_guest_reports`, `extended_guest_reports`, and `transfered_guest_reports` alongside the actual data. These report tables must be created at the exact same time as the operational records. If the code misses creating one (due to a bug, timeout, or edge case), the report is wrong — and there's no automated way to detect or fix the drift.

**Impact:** Reports can silently show fewer check-ins, checkouts, or extensions than actually occurred. No reconciliation mechanism exists.

### 7. Inventory Report Calculation Is Wrong

The stock-in value is set equal to the opening stock, which means the closing stock formula effectively ignores stock additions during the period:

```php
$stockIn = $inventory->number_of_serving; // WRONG — this is current stock, not stock added
$closingStock = $opening - ($stockOut + $wastage); // Ignores stock-in entirely
```

**Impact:** Inventory reports show wrong closing stock. Stock valuation is incorrect.

### Why V4 Fixes This Permanently

| V3 Root Cause | V4 Design |
|---------------|-----------|
| Optional shift link | `shift_id` is NOT NULL — every transaction must belong to a shift |
| Cascading deposit chain | `SUM(deposit_in - deposit_out)` per stay — one query, no chain |
| Hardcoded ₱200 | All values read from `branch_settings` — no magic numbers |
| String matching on remarks | Transaction `type` column — no string parsing needed |
| Mutable transactions | Immutable — payments are new records, voids create new records |
| Separate report tables | No report tables — all reports query core data directly |
| Wrong inventory math | `menu_stock_logs` with before/after audit trail |

**V4 reports are accurate by design** because they query the actual data, not copies or chains or hardcoded assumptions.

---

---

## Schema & Data Type Issues

| # | Problem | Impact | V4 Solution |
|---|---------|--------|-------------|
| 1 | All monetary amounts stored as INTEGER | No decimal support (₱499.50 impossible) | All amounts `decimal(10,2)` |
| 2 | `stay_extensions.hours` and `.amount` stored as STRING | Can't do math in SQL, requires PHP casting | Proper `integer` and `decimal` types |
| 3 | Menu prices stored as STRING (`menus.price`, `frontdesk_menus.price`, `pub_menus.price`) | Same problem | `decimal(10,2)` on `menu_items.price` |
| 4 | `expenses.amount` stored as STRING | Same problem | `decimal(10,2)` |
| 5 | `frontdesk_shifts` table has 50+ STRING columns for financial data | Can't aggregate in SQL | Eliminated — all computed from `transactions` |
| 6 | Cleaning times stored as STRING (`cleaning_histories.start_time`, `.end_time`) | Can't do date range queries | Proper `datetime` columns on `cleaning_tasks` |

## Duplicate & Redundant Data

| # | Problem | Impact | V4 Solution |
|---|---------|--------|-------------|
| 7 | `guests` + `checkin_details` store same data (room_id, rate_id, type_id, static_amount) | Which is source of truth? | Single `stays` table |
| 8 | `users.branch_name` duplicates `branches.name` | Can drift, must update both | Removed — query `branch.name` |
| 9 | 3 duplicate POS systems (9 tables: kitchen/frontdesk/pub each with categories+menus+inventories) | Code duplication, data isolation | Unified 4 tables with `service_area` column |
| 10 | 3 overlapping shift tables (`shifts`, `shift_logs`, `frontdesk_shifts`) | Confusing, which is source of truth? | Single `shifts` table |
| 11 | `cleaning_histories` + `room_boy_reports` track same cleaning event | Duplicate data | Single `cleaning_tasks` table |
| 12 | User table mixes auth + personal info + operational state (18 columns) | Poor separation of concerns | Split: `users` (auth) + `user_profiles` (personal) |

## Broken Relationships & Missing Links

| # | Problem | Impact | V4 Solution |
|---|---------|--------|-------------|
| 13 | `assigned_frontdesk_id` is JSON but model uses `belongsTo` | Relationship literally broken | Proper `created_by` FK to users |
| 14 | `shift_log_id` on transactions is nullable, added late | Transactions without shift attribution | `shift_id` required (NOT NULL) |
| 15 | Kitchen module doesn't set `shift_log_id` or `cash_drawer_id` | Inconsistent data | All transactions require `shift_id` |
| 16 | `frontdesks` table separate from `users` | Confusing 1:1 relationship | Eliminated — frontdesk is user with role |

## Report & Calculation Issues

| # | Problem | Impact | V4 Solution |
|---|---------|--------|-------------|
| 17 | Forwarded guest deposit requires chain walk across ALL previous shifts | One wrong shift = all subsequent reports wrong | `SUM(deposit_in - deposit_out)` per stay — no chain |
| 18 | Room deposit hardcoded to ₱200 in 13+ places | Wrong if branch uses different amount | Read from `branch_settings.initial_deposit` |
| 19 | Deposit type detection uses string matching on remarks | "Room Key & TV Remote" — fragile | Transaction `type` column (no string matching) |
| 20 | 5 separate report tables that must stay in sync | Sync drift = wrong reports | All eliminated — reports query core tables directly |
| 21 | Inventory report stock-in calculation is WRONG | Closing stock ignores stock-in | `menu_stock_logs` with proper before/after audit |

## Immutability & Audit Issues

| # | Problem | Impact | V4 Solution |
|---|---------|--------|-------------|
| 22 | Transactions mutated after creation (paid_amount, paid_at updated) | Can't tell when/how payment happened | Immutable — payments are new records |
| 23 | Room transfer rewrites original check-in transaction amount | Destroys historical data | Snapshot rule — never modify, only append |
| 24 | Transaction override mutates original record | No audit trail of what changed | `void` type + new corrected charge |
| 25 | `cash_on_drawers` must stay in sync with transactions manually | If one missed, reconciliation breaks | Eliminated — cash derived from transactions |

## Dead Code & Unused Features

| # | Problem | Impact | V4 Solution |
|---|---------|--------|-------------|
| 26 | Transaction Type 3 "Kitchen Order" seeded but never created | Dead code | Removed — kitchen uses `food` type |
| 27 | `is_co` column on transactions AND guests — never used | Wasted columns | Removed |
| 28 | `is_override` column — only set in one place, never queried | Wasted column | Removed — void system handles overrides |

## Hardcoded Values (Bonus)

| Value | Hardcoded In | V4 Solution |
|-------|-------------|-------------|
| ₱200 deposit | 13+ places | `branch_settings.initial_deposit` |
| 15 min cleaning | 10+ places | `branch_settings.cleaning_min_minutes` |
| 3 hour cleaning deadline | 4 places | `branch_settings.cleaning_deadline_hours` |
| 14 hour stale shift | 2 places | `branch_settings.stale_shift_hours` |
| 8AM/8PM shift cutoff | 50+ places | `branch_settings.shift_am_start/pm_start` |
| 20 min kiosk timeout | 2 places (DB says 10!) | `branch_settings.kiosk_time_limit` (consistent) |

---

## Tables Eliminated (21 → 0)

| Dropped V3 Table | Replaced By |
|-------------------|-------------|
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
| `frontdesks` | User with frontdesk role |
| `assigned_frontdesks` | `shifts.user_id` + `shifts.partner_user_id` |
| `transaction_types` | String `type` column |
| `cleaning_histories` | `cleaning_tasks` |
| `room_boy_reports` | `cleaning_tasks` |
| `unoccupied_room_reports` | Query rooms |
| `activity_logs` | Spatie Activitylog package |
| `hotel_items` | `menu_items` (service_area='damage') |
| `requestable_items` | `menu_items` (service_area='amenity') |
| `stay_events` | UNION query on existing tables |
