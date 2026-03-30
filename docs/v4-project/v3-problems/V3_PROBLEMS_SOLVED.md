# V3 Problems Solved in V4

28 issues identified and resolved during the V4 redesign.

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
