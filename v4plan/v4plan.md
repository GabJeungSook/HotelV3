# HotelV3 Analysis & V4 Plan Comparison

## Current V3 Database Schema Overview

### Tables (56 total)

| Category | Tables | Count |
|----------|--------|-------|
| Core | users, branches, sessions, password_resets, personal_access_tokens, failed_jobs | 6 |
| Permissions (Spatie) | permissions, roles, model_has_permissions, model_has_roles, role_has_permissions | 5 |
| Room Management | rooms, floors, floor_user, types, staying_hours, rates, extension_rates, discount_configurations, discounts | 9 |
| Guest Management | guests, checkin_details, temporary_check_in_kiosks, temporary_reserveds | 4 |
| Financial | transactions, transaction_types, stay_extensions, shift_sessions, shift_members, shift_forwarded_guests, shift_snapshots, shift_logs, cash_drawers, cash_on_drawers, frontdesk_shifts | 11 |
| Frontdesk Ops | frontdesks, assigned_frontdesks, frontdesk_categories, frontdesk_menus, frontdesk_inventories | 5 |
| Kitchen/Pub POS | menu_categories, menus, inventories, pub_categories, pub_menus, pub_inventories | 6 |
| Expenses | expenses, expense_categories, remittances | 3 |
| Housekeeping | cleaning_histories | 1 |
| Reports | new_guest_reports, check_out_guest_reports, room_boy_reports, extended_guest_reports, transfered_guest_reports, unoccupied_room_reports | 6 |
| Misc | hotel_items, requestable_items, transfer_reasons, activity_logs | 4 |

### Entity Relationship Flow

```
Branch (hub)
  ├── User (branch_id)
  ├── Floor (branch_id)
  │     └── Room (floor_id, type_id)
  │           ├── Guest (room_id, rate_id, type_id)
  │           │     ├── CheckinDetail (guest_id, room_id, rate_id, type_id)
  │           │     ├── Transaction (guest_id, room_id)
  │           │     └── StayExtension (guest_id)
  │           └── CleaningHistory (room_id, user_id)
  ├── Type (branch_id)
  │     └── Rate (type_id, staying_hour_id)
  ├── StayingHour (branch_id)
  ├── CashDrawer (branch_id)
  │     └── ShiftSession (cash_drawer_id)
  │           ├── ShiftMember (user_id)
  │           ├── ShiftSnapshot (1:1)
  │           ├── ShiftForwardedGuest (checkin_detail_id)
  │           ├── Transaction (shift_session_id)
  │           ├── Expense (shift_session_id)
  │           └── Remittance (shift_session_id)
  ├── Frontdesk (branch_id, user_id)
  │     └── AssignedFrontdesk (user_id, frontdesk_id)
  ├── MenuCategory -> Menu -> Inventory (kitchen)
  ├── FrontdeskCategory -> FrontdeskMenu -> FrontdeskInventory
  └── PubCategory -> PubMenu -> PubInventory
```

---

## Problems Found in V3

### 1. CRITICAL: Data Integrity Issues

#### 1.1 Transactions Can Exist Without Shifts
- `transactions.shift_log_id` is **nullable** - transactions can be recorded outside of any shift
- This breaks financial accountability: who was responsible for this transaction?
- V4 fix: Middleware enforces active shift before any transaction is allowed

#### 1.2 Forwarded Deposit Calculation is a Cascading Chain
- Each shift's forwarded balance depends on the previous shift's calculation
- If one shift has an error, **every subsequent shift's balance is wrong**
- This is a ticking time bomb for financial reconciliation
- V4 fix: Self-contained shifts with snapshot-based forwarding, no chain dependency

#### 1.3 Immutability Violation - Transactions Are Mutable
- Transactions can be updated/modified after creation
- No audit trail for changes - original amounts lost forever
- V4 fix: Append-only transaction design, void/override creates new records with authorization codes

#### 1.4 Room Deposit Hardcoded
- `200` (peso deposit) is hardcoded in **13+ places** across the codebase
- Ignores `branches.initial_deposit` setting entirely
- Different branches cannot have different deposit amounts
- V4 fix: All amounts read from `branch_settings` table

### 2. CRITICAL: Wrong Data Types

#### 2.1 Money Stored as INTEGER
- All monetary columns use `integer` type - no decimal support
- Cannot represent ₱99.50 or any fractional amount
- V4 fix: `decimal(10,2)` for all money columns

#### 2.2 Hours/Amounts Stored as STRING
- `shift_logs.beggining_cash` is **string** type (also misspelled)
- Menu prices stored as **string** - cannot do SQL math (`SUM()`, `AVG()` fail)
- Cleaning duration stored as **string**
- V4 fix: Proper numeric types throughout

#### 2.3 Cleaning Times as STRING
- `cleaning_histories.start_time`, `end_time`, `expected_end_time` - stored as strings
- Cannot calculate duration with SQL, cannot sort chronologically
- V4 fix: Proper `datetime` columns

### 3. CRITICAL: Duplicate/Redundant Data

#### 3.1 Guests vs CheckinDetails - Which is the Source of Truth?
- `guests` table stores: room_id, rate_id, type_id, static_amount, has_discount, is_long_stay
- `checkin_details` table stores: room_id, rate_id, type_id, static_amount, is_long_stay, hours_stayed, deposit info
- **Same data duplicated** across two tables
- When they disagree, which one is correct? Nobody knows
- V4 fix: Single `stays` table replaces both

#### 3.2 Three Separate POS Systems
- **Kitchen**: menu_categories + menus + inventories
- **Frontdesk**: frontdesk_categories + frontdesk_menus + frontdesk_inventories
- **Pub**: pub_categories + pub_menus + pub_inventories
- 9 tables doing the same thing with identical schemas
- V4 fix: Single unified POS with `service_area` enum (kitchen/frontdesk/pub)

#### 3.3 Three Overlapping Shift Systems
- `shift_logs` - legacy shift tracking
- `shift_sessions` + `shift_members` - new shift system
- `frontdesk_shifts` - **60+ column** monolith table for shift reports
- All three coexist, transactions reference both old and new
- V4 fix: Single `shifts` + `shift_members` system

#### 3.4 Duplicate Cleaning Tracking
- `cleaning_histories` - tracks actual cleaning events
- `room_boy_reports` - tracks the same thing in report format
- Same data, two tables, can drift out of sync
- V4 fix: Single `cleaning_tasks` table, reports computed from it

#### 3.5 Redundant User Fields
- `users.branch_name` duplicates `branches.name`
- `users.assigned_frontdesks` (JSON) duplicates `assigned_frontdesks` table
- `users.roomboy_assigned_floor_id` duplicates `floor_user` pivot table
- V4 fix: Remove all redundant columns, use relationships

### 4. CRITICAL: Report Tables That Drift

#### 4.1 Six Separate Report Tables
- `new_guest_reports`, `check_out_guest_reports`, `extended_guest_reports`, `transfered_guest_reports`, `room_boy_reports`, `unoccupied_room_reports`
- These **duplicate data** that already exists in transactions/checkin_details
- They must be manually kept in sync - and they aren't
- When the report table disagrees with the source table, the report is wrong
- V4 fix: **Zero report tables** - all reports are computed queries from core data

#### 4.2 Inventory Report Calculation is WRONG
- Stock-in quantity is miscalculated in the current inventory reporting logic
- The formula doesn't properly account for all stock movement types
- V4 fix: Proper `stock_movements` table with audit trail

### 5. HIGH: Schema Design Problems

#### 5.1 Missing Foreign Key Constraints
- Most tables declare FK columns but don't use `.constrained()` or `.foreign()`
- No CASCADE/RESTRICT enforcement at the database level
- Deleting a guest doesn't cascade to transactions - orphaned records
- Deleting a room doesn't clean up guests - data corruption

#### 5.2 JSON Columns Used for Relationships
- `transactions.assigned_frontdesk_id` - JSON instead of proper FK
- `users.assigned_frontdesks` - JSON array instead of pivot table
- `stay_extensions.frontdesk_ids` - JSON instead of normalized
- `shift_logs.frontdesk_ids` - JSON instead of normalized
- Cannot be indexed, cannot be joined, cannot be constrained

#### 5.3 Inconsistent Naming
- `beggining_cash` (misspelled "beginning")
- `autorization_code` (misspelled "authorization")
- `transfered_guest_reports` (misspelled "transferred")
- `roomboy_id` vs `user_id` for the same concept
- `frontdesk_id` sometimes means User, sometimes means Frontdesk entity

#### 5.4 The 60+ Column `frontdesk_shifts` Table
- Single table with columns for: label, raw_file, frontdesk assignments, opening/closing data, every transaction type count and amount, forwarded guests, deposits, expenses, remittances, reconciliation
- This is a spreadsheet pretending to be a database table
- V4 fix: `shift_snapshots` with computed values, not 60+ denormalized columns

#### 5.5 Boolean Named as Foreign Key
- `cleaning_histories.current_assigned_floor_id` is a **boolean**, not a floor reference
- Extremely misleading column name

### 6. HIGH: Architectural Issues

#### 6.1 No Controllers - Everything in Livewire
- Zero HTTP controllers for web routes (only 8 API controllers)
- 103 Livewire components handle all business logic directly
- No service layer - check-in logic lives inside a Livewire component
- Cannot reuse business logic across API and web
- V4 fix: Service classes (CheckInService, PaymentService, etc.)

#### 6.2 Mixed Authorization Patterns
- Routes use Spatie middleware: `role:admin|superadmin`
- API controllers use manual `hasRole()` checks
- Some Livewire components check roles inline
- Inconsistent = security holes waiting to happen

#### 6.3 Deposit Type Detection by String Matching
- Transaction deposit type detected by matching strings like "Room Key Deposit"
- Fragile - rename the string and deposits break
- V4 fix: `deposit_type` enum column (room_key / guest)

#### 6.4 No Event/Job Architecture
- No event listeners or queued jobs
- Check-in doesn't trigger housekeeping assignment
- Checkout doesn't trigger room status update via events
- Everything is synchronous and tightly coupled

#### 6.5 Missing Tenant Isolation on Reports
- `check_out_guest_reports` has no `branch_id` column
- Could potentially leak data across branches
- Spatie permission tables have no branch awareness

### 7. MEDIUM: Missing Features (V4 Adds)

| Feature | V3 Status | V4 Plan |
|---------|-----------|---------|
| Discount audit trail | Missing | Full trail with authorization |
| Void/override tracking | Missing | Append-only with auth codes |
| Stock movement history | Missing | `stock_movements` table |
| Kiosk device management | Basic | Device auth, heartbeat, remote deactivation |
| Multi-branch staff deployment | Missing | Primary/deployed branch assignment |
| Cycle-based extension tracking | Missing | Extension cycle reset counter |
| Computed reports with SQL verification | Missing | 14 reports with proven SQL |
| Activity logging (automatic) | Basic manual | Spatie Activitylog (automatic) |

---

## V3 vs V4 Comparison Summary

### Database: 56 Tables -> 35 Tables

**Tables Eliminated (21):**
- `guests` (merged into `stays`)
- `checkin_details` (merged into `stays`)
- `new_guest_reports` (computed)
- `check_out_guest_reports` (computed)
- `extended_guest_reports` (computed)
- `transfered_guest_reports` (computed)
- `unoccupied_room_reports` (computed)
- `room_boy_reports` (computed from `cleaning_tasks`)
- `shift_logs` (replaced by `shifts`)
- `frontdesk_shifts` (replaced by `shift_snapshots`)
- `cash_on_drawers` (simplified)
- `frontdesks` (removed - users work shifts directly)
- `assigned_frontdesks` (simplified)
- `cleaning_histories` (replaced by `cleaning_tasks`)
- `frontdesk_categories` + `frontdesk_menus` + `frontdesk_inventories` (unified POS)
- `pub_categories` + `pub_menus` + `pub_inventories` (unified POS)
- `hotel_items` + `requestable_items` (merged into unified menu)

### Tech Stack Upgrade

| Component | V3 | V4 |
|-----------|----|----|
| PHP | 8.0.2+ | 8.3+ |
| Laravel | 9.19 | 12/13 |
| Livewire | 2.5 | 4 |
| Admin Panel | Custom Livewire | Filament 5 |
| CSS | Tailwind 3.2 | Tailwind 4 |
| Vite | 4.0 | 6 |
| Testing | PHPUnit 9.5 | Pest 4 |
| UI Components | WireUI 1.17 | Flux UI (free tier) |
| Activity Log | Manual | Spatie Activitylog |
| WebSockets | None | Laravel Reverb |

### Architecture Upgrade

| Aspect | V3 | V4 |
|--------|----|----|
| Business logic | Inside Livewire components | Service classes |
| Multi-tenancy | Manual `where('branch_id', ...)` | `ScopesToBranch` trait (automatic) |
| Enums | String matching | PHP 8.1+ enums (TransactionType, RoomStatus, etc.) |
| Shift enforcement | Optional (nullable FK) | Middleware-enforced |
| Transaction integrity | Mutable records | Append-only, immutable |
| Report generation | Stored in separate tables | Computed from core data |
| Deposit handling | Hardcoded ₱200 | Configurable per branch |
| POS | 3 duplicate systems | 1 unified system with service_area enum |
| Shift system | 3 overlapping tables | 1 clean system |
| Data types | int/string for money | decimal(10,2) |

---

## Priority Fix Order for V4 Migration

1. **Schema redesign** - Merge guests+checkin_details into stays, unify POS, eliminate report tables
2. **Data type fixes** - decimal for money, datetime for times, enums for statuses
3. **Foreign key constraints** - Add proper constraints with cascade rules
4. **Service layer** - Extract business logic from Livewire into services
5. **Shift enforcement** - Middleware requiring active shift for transactions
6. **Immutable transactions** - Append-only design with void/override flow
7. **Computed reports** - Replace 6 report tables with query-based reports
8. **Branch settings** - Move all hardcoded values to configurable settings
9. **Unified POS** - Single menu/category/inventory system with service_area
10. **Audit trail** - Spatie Activitylog for automatic change tracking
