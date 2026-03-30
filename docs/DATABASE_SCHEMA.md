# Database Schema Documentation

This document describes the current database schema for HotelV3. Use this as a reference for the schema rebuild.

---

## Application Overview

**HotelV3** is a multi-branch hotel management system designed for short-stay/transient hotels (hourly rates). The system handles the complete guest lifecycle from check-in to checkout, with integrated POS for food/beverages, room cleaning workflow, and comprehensive shift-based financial reporting.

### Key Business Characteristics
- **Hourly-based stays** - Guests pay for specific hour packages (3hrs, 6hrs, 12hrs, etc.)
- **Multi-branch operation** - Single system manages multiple hotel locations
- **Shift-based operations** - Staff work in shifts with cash accountability
- **Kiosk self-service** - Guests can check-in/out via kiosk terminals
- **Real-time room status** - Tracks room availability, cleaning, and occupancy

---

## System Roles

| Role | Description | Key Responsibilities |
|------|-------------|---------------------|
| **superadmin** | System administrator | Manages all branches, global settings, user accounts, view all reports |
| **admin** | Branch manager | Branch configuration, room/rate setup, user management, reports |
| **frontdesk** | Front desk staff | Check-in/out guests, process payments, POS transactions, extensions |
| **back_office** | Back office/accounting | View reports, manage expenses, sales reconciliation |
| **roomboy** | Housekeeping staff | Room cleaning, status updates, floor assignments |
| **kitchen** | Kitchen staff | Manage kitchen menu, inventory, fulfill food orders |
| **pub_kitchen** | Pub/bar staff | Manage pub menu, inventory, fulfill drink orders |
| **kiosk** | Kiosk terminal | Self-service check-in/out interface for guests |

---

## Core Business Flows

### 1. Check-In Flow
```
Guest arrives → Select Room → Select Rate (hours) → Pay/Deposit → Generate QR Code → Room status: Occupied
```
**Tables involved:** `guests`, `checkin_details`, `transactions`, `rooms`, `rates`

### 2. Stay Extension Flow
```
Guest requests extension → Select extension hours → Pay extension rate → Update checkout time
```
**Tables involved:** `stay_extensions`, `extension_rates`, `checkin_details`, `transactions`

### 3. Room Transfer Flow
```
Guest requests transfer → Select new room → Calculate rate difference → Process payment → Update room assignments
```
**Tables involved:** `guests.previous_room_id`, `transactions`, `transfer_reasons`, `transfered_guest_reports`

### 4. Check-Out Flow
```
Guest checkout → Calculate final charges → Process payment/refund deposit → Room status: Uncleaned → Generate reports
```
**Tables involved:** `guests`, `checkin_details`, `transactions`, `rooms`, `check_out_guest_reports`

### 5. Room Cleaning Flow
```
Room checkout → Status: Uncleaned → Roomboy claims → Status: Cleaning → Complete → Status: Available
```
**Tables involved:** `rooms`, `cleaning_histories`, `room_boy_reports`

### 6. POS Transaction Flow
```
Guest orders food/items → Create transaction → Deduct inventory → Add to guest bill
```
**Tables involved:** `transactions`, `frontdesk_menus`/`menus`/`pub_menus`, `inventories`

### 7. Shift Management Flow
```
Staff time-in → Assign frontdesk → Beginning cash → Process transactions → Expenses → Remittance → Time-out → Shift report
```
**Tables involved:** `shift_logs`, `frontdesk_shifts`, `assigned_frontdesks`, `cash_on_drawers`, `expenses`, `remittances`

---

## Transaction Types

| ID | Name | Description |
|----|------|-------------|
| 1 | Check In | Initial room payment |
| 2 | Deposit | Security deposit collection |
| 3 | Kitchen Order | Food orders from kitchen |
| 4 | Damage Charges | Charges for damages |
| 5 | Cashout | Deposit refund/checkout |
| 6 | Extend | Stay extension payment |
| 7 | Transfer Room | Room transfer charges |
| 8 | Amenities | Amenity charges |
| 9 | Food and Beverages | F&B from frontdesk POS |

---

## Room Statuses

| Status | Description |
|--------|-------------|
| `Available` | Ready for check-in |
| `Occupied` | Currently occupied by guest |
| `Reserved` | Reserved/held for guest |
| `Maintenance` | Under maintenance, not available |
| `Uncleaned` | Guest checked out, needs cleaning |
| `Cleaning` | Roomboy currently cleaning |
| `Cleaned` | Cleaned, pending verification |

---

## Core Entity Relationships

```
Branch (1) ──────┬──── (*) Users
                 ├──── (*) Rooms ──── (1) Floor
                 │            └──── (1) Type
                 ├──── (*) Rates ──── (1) StayingHour
                 │            └──── (1) Type
                 │            └──── (?) Room (per-room rates)
                 ├──── (*) Guests ──── (1) Room
                 │            └──── (*) CheckinDetails
                 │            └──── (*) Transactions
                 │            └──── (*) StayExtensions
                 ├──── (*) Frontdesks ──── (*) AssignedFrontdesks
                 ├──── (*) ShiftLogs ──── (*) Expenses
                 │            └──── (*) Remittances
                 │            └──── (*) Transactions
                 └──── (*) Menus (Kitchen/Frontdesk/Pub)
                              └──── (*) Inventories
```

---

## Key Features & Extensions

### 1. Per-Room Pricing
- Originally rates were per room TYPE
- Extended to support per-ROOM rates (`rates.room_id`)
- Allows different pricing for same room type

### 2. Deposit System
- Configurable per branch (`branches.initial_deposit`)
- Can be enabled/disabled (`branches.discount_enabled`)
- Tracked in `checkin_details.total_deposit`

### 3. Discount System
- Branch-level discount configuration
- Rate-level discount flag (`rates.has_discount`)
- Guest-level discount tracking (`guests.has_discount`)

### 4. Multi-Frontdesk Support
- Multiple physical frontdesk stations per branch
- Staff assigned to specific frontdesks
- Transactions tracked per frontdesk

### 5. Kiosk Self-Service System

**What is the Kiosk?**
The kiosk is a self-service terminal (tablet/touchscreen) placed in the hotel lobby where guests can check-in and check-out without direct frontdesk assistance. It reduces wait times and allows frontdesk staff to handle other tasks.

**Kiosk Check-In Flow:**
```
Guest at Kiosk → Select Room Type → Select Room → Select Hours →
    → Creates temporary_check_in_kiosks record →
    → Frontdesk receives real-time notification (Pusher) →
    → Frontdesk confirms & processes payment →
    → Guest receives QR code → Check-in complete
```

**Kiosk Check-Out Flow:**
```
Guest at Kiosk → Scan QR Code → View bill summary →
    → Confirm checkout → Frontdesk receives notification →
    → Frontdesk processes refund/final payment →
    → Room status → Uncleaned
```

**How Kiosk Connects to Frontdesk:**
| Step | Kiosk Action | Frontdesk Action |
|------|--------------|------------------|
| 1 | Guest selects room & hours | - |
| 2 | Creates `temporary_check_in_kiosks` record | Receives Pusher notification |
| 3 | Waits for confirmation | Opens kiosk request, verifies guest |
| 4 | - | Processes payment at counter |
| 5 | - | Confirms check-in, assigns room |
| 6 | Displays QR code to guest | Transaction complete |

**Key Tables:**
- `temporary_check_in_kiosks` - Pending kiosk check-ins awaiting frontdesk confirmation
- `temporary_check_in_kiosks.terminated_at` - Auto-expires if not confirmed within time limit
- `temporary_check_in_kiosks.is_opened` - Tracks if frontdesk has opened/viewed the request
- `branches.kiosk_time_limit` - Minutes before kiosk request expires (default: 10 min)
- `guests.has_kiosk_check_out` - Flags if guest used kiosk for checkout

**Real-time Communication:**
- Uses **Pusher** for real-time notifications
- Channel: `newcheckin.{branch_id}`
- Frontdesk dashboard auto-updates when kiosk request comes in

### 6. Three POS Systems
- **Kitchen** - Full kitchen menu system
- **Frontdesk** - Quick items at front desk
- **Pub** - Bar/drinks menu
- Each with own categories, menus, and inventory

### 7. Shift-Based Accounting
- Beginning cash tracking
- Per-shift expenses
- Remittance tracking
- Comprehensive shift reports (`frontdesk_shifts`)

### 8. Room Transfer Tracking
- Transfer reasons catalog
- Previous room tracking
- Rate difference calculation
- Transfer reports

### 9. Floor-Based Roomboy Assignment
- Roomboys assigned to specific floors
- Cleaning time tracking
- Performance reports

---

## Core Tables

### `branches`
Hotel branch/location configuration.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| name | string | NO | | Branch name |
| address | string | YES | NULL | Physical address |
| autorization_code | string | YES | NULL | Current auth code |
| old_autorization | string | YES | NULL | Previous auth code |
| extension_time_reset | integer | YES | NULL | Extension time reset value |
| initial_deposit | decimal(10,2) | NO | 200.00 | Default deposit amount |
| discount_enabled | boolean | NO | true | Whether discounts are enabled |
| discount_amount | decimal(10,2) | NO | 50.00 | Default discount amount |
| kiosk_time_limit | integer | NO | 10 | Kiosk time limit in minutes |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `users`
System users (staff, admins, roomboys, etc.)

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| name | string | NO | | User's full name |
| email | string | NO | | Unique email |
| email_verified_at | timestamp | YES | NULL | |
| password | string | NO | | Hashed password |
| two_factor_secret | text | YES | NULL | 2FA secret |
| two_factor_recovery_codes | text | YES | NULL | 2FA recovery codes |
| two_factor_confirmed_at | timestamp | YES | NULL | 2FA confirmation |
| branch_name | string | NO | | Denormalized branch name |
| remember_token | string | YES | NULL | |
| current_team_id | foreignId | YES | NULL | Jetstream team |
| roomboy_assigned_floor_id | bigint | YES | NULL | Roomboy floor assignment |
| roomboy_cleaning_room_id | bigint | YES | NULL | Currently cleaning room |
| profile_photo_path | string(2048) | YES | NULL | |
| assigned_frontdesks | json | YES | NULL | Assigned frontdesk IDs |
| time_in | datetime | YES | NULL | Shift start time |
| shift | string | YES | NULL | Current shift |
| is_active | boolean | NO | true | Account active status |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `types`
Room types (e.g., Standard, Deluxe, Suite).

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| name | string | NO | | Type name |
| description | string | YES | NULL | Type description |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `floors`
Building floors.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| number | integer | NO | | Floor number |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `floor_user` (Pivot)
Many-to-many: users assigned to floors.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| user_id | foreignId | NO | | FK to users |
| floor_id | foreignId | NO | | FK to floors |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `rooms`
Individual hotel rooms.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| floor_id | foreignId | NO | | FK to floors |
| number | integer | NO | | Room number |
| area | string | YES | 'Main' | Area (Main/Extension) |
| status | string | NO | 'available' | Room status |
| type_id | foreignId | NO | | FK to types |
| is_priority | boolean | NO | false | Priority room flag |
| last_checkin_at | datetime | YES | NULL | Last check-in time |
| last_checkout_at | datetime | YES | NULL | Last check-out time |
| time_to_terminate_queue | string | YES | NULL | Queue termination time |
| check_out_time | datetime | YES | NULL | Expected checkout |
| time_to_clean | datetime | YES | NULL | Scheduled cleaning time |
| started_cleaning_at | datetime | YES | NULL | Cleaning start time |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

**Room Statuses:** `Available`, `Occupied`, `Reserved`, `Maintenance`, `Uncleaned`, `Cleaning`, `Cleaned`

---

### `staying_hours`
Available stay durations (e.g., 3 hours, 6 hours, 12 hours).

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| number | integer | NO | | Number of hours |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `rates`
Pricing for room types/rooms by staying hours.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| staying_hour_id | foreignId | NO | | FK to staying_hours |
| type_id | foreignId | NO | | FK to types |
| room_id | foreignId | YES | NULL | FK to rooms (for room-specific rates) |
| amount | integer | NO | | Rate amount |
| is_available | boolean | NO | true | Rate availability |
| has_discount | boolean | NO | false | Discount applicable |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `extension_rates`
Pricing for stay extensions.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| hour | integer | NO | | Extension hours |
| amount | integer | NO | | Extension rate |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `discounts`
Available discount types.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| name | string | NO | | Discount name |
| description | string | YES | NULL | Description |
| amount | integer | NO | | Discount value |
| is_percentage | boolean | NO | | True if percentage |
| is_available | boolean | NO | true | Availability |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

## Guest & Check-in Tables

### `guests`
Active/current guests.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| name | string | NO | | Guest name |
| contact | string | YES | NULL | Contact number |
| qr_code | string | NO | | Unique QR code |
| room_id | foreignId | NO | | FK to rooms |
| previous_room_id | foreignId | YES | NULL | Previous room (if transferred) |
| rate_id | foreignId | NO | | FK to rates |
| type_id | foreignId | NO | | FK to types |
| static_amount | integer | NO | | Original rate amount |
| is_long_stay | boolean | NO | false | Long stay flag |
| number_of_days | integer | YES | NULL | Days for long stay |
| has_discount | boolean | NO | false | Discount applied |
| has_kiosk_check_out | boolean | NO | false | Kiosk checkout flag |
| is_co | boolean | NO | false | Checked out flag |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `checkin_details`
Check-in session details.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| guest_id | foreignId | NO | | FK to guests |
| type_id | foreignId | NO | | FK to types |
| room_id | foreignId | NO | | FK to rooms |
| rate_id | foreignId | NO | | FK to rates |
| frontdesk_id | foreignId | YES | NULL | FK to frontdesks |
| static_amount | integer | NO | | Rate at check-in |
| static_room_amount | integer | YES | NULL | Room amount snapshot |
| hours_stayed | integer | NO | | Hours stayed |
| total_deposit | integer | YES | NULL | Deposit amount |
| total_deduction | integer | NO | 0 | Deductions |
| check_in_at | datetime | NO | | Check-in time |
| check_out_at | datetime | NO | | Expected checkout |
| is_check_out | boolean | NO | false | Checked out flag |
| is_long_stay | boolean | NO | | Long stay flag |
| number_of_hours | integer | NO | 0 | Total hours |
| next_extension_is_original | boolean | NO | false | Extension flag |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `stay_extensions`
Guest stay extensions.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| guest_id | foreignId | NO | | FK to guests |
| extension_id | foreignId | NO | | FK to extension_rates |
| hours | string | NO | | Extended hours |
| amount | string | NO | | Extension cost |
| frontdesk_ids | json | YES | NULL | Frontdesk IDs |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `temporary_check_in_kiosks`
Pending kiosk check-ins awaiting frontdesk confirmation.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| room_id | foreignId | NO | | FK to rooms |
| guest_id | foreignId | NO | | FK to guests |
| terminated_at | datetime | NO | | Expiration time |
| is_opened | boolean | NO | false | Opened flag |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `temporary_reserveds`
Temporary room reservations.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| room_id | foreignId | NO | | FK to rooms |
| guest_id | foreignId | NO | | FK to guests |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

## Transaction Tables

### `transaction_types`
Types of transactions.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| name | string | NO | | Type name |
| position | string | YES | NULL | Display position |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `transactions`
All financial transactions.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| room_id | foreignId | NO | | FK to rooms |
| guest_id | foreignId | NO | | FK to guests |
| floor_id | foreignId | NO | | FK to floors |
| transaction_type_id | foreignId | NO | | FK to transaction_types |
| checkin_detail_id | foreignId | YES | NULL | FK to checkin_details |
| shift_log_id | foreignId | YES | NULL | FK to shift_logs |
| assigned_frontdesk_id | json | NO | | Frontdesk IDs |
| description | string | NO | | Transaction description |
| payable_amount | integer | NO | 0 | Amount due |
| paid_amount | integer | NO | 0 | Amount paid |
| change_amount | integer | NO | 0 | Change given |
| deposit_amount | integer | NO | 0 | Deposit amount |
| paid_at | datetime | YES | NULL | Payment time |
| override_at | datetime | YES | NULL | Override time |
| remarks | text | NO | | Additional notes |
| transfer_reason_id | foreignId | YES | NULL | FK to transfer_reasons |
| shift | string | YES | NULL | Shift identifier |
| is_co | boolean | NO | false | Checkout transaction |
| is_override | boolean | NO | false | Override flag |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `transfer_reasons`
Reasons for room transfers.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| reason | text | NO | | Transfer reason |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

## Frontdesk & Shift Tables

### `frontdesks`
Frontdesk stations/counters.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| user_id | foreignId | YES | NULL | FK to users |
| name | string | NO | | Frontdesk name |
| number | string | NO | | Frontdesk number |
| passcode | string | YES | NULL | Access passcode |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `assigned_frontdesks`
User-frontdesk assignments.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| user_id | foreignId | NO | | FK to users |
| frontdesk_id | foreignId | NO | | FK to frontdesks |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `shift_logs`
Frontdesk shift records.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | YES | NULL | FK to branches |
| frontdesk_id | foreignId | YES | NULL | FK to frontdesks |
| time_in | datetime | NO | | Shift start |
| time_out | datetime | YES | NULL | Shift end |
| frontdesk_ids | json | NO | | Frontdesk IDs |
| shift | string | YES | NULL | Shift identifier |
| beginning_cash | decimal | YES | NULL | Opening cash |
| total_expenses | decimal | YES | NULL | Total expenses |
| total_remittance | decimal | YES | NULL | Total remittance |
| description | text | YES | NULL | Shift notes |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `frontdesk_shifts`
Detailed shift reports.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| label | string | NO | | Shift label |
| raw_file | string | YES | NULL | Raw file path |
| frontdesk_outgoing | string | YES | NULL | Outgoing frontdesk |
| frontdesk_incoming | string | YES | NULL | Incoming frontdesk |
| shift_opened | timestamp | YES | NULL | Shift open time |
| shift_closed | timestamp | YES | NULL | Shift close time |
| **Cash Drawer** | | | | |
| opening_cash_amount | string | YES | NULL | |
| opening_cash_sub_amount | string | YES | NULL | |
| opening_cash_remark | string | YES | NULL | |
| key_amount | string | YES | NULL | |
| key_sub_amount | string | YES | NULL | |
| key_remarks | string | YES | NULL | |
| guest_deposit_amount | string | YES | NULL | |
| guest_deposit_sub_amount | string | YES | NULL | |
| guest_deposit_amount_remark | string | YES | NULL | |
| forwarding_balance_amount | string | YES | NULL | |
| forwarding_balance_sub_amount | string | YES | NULL | |
| forwarding_balance_remark | string | YES | NULL | |
| total_cash_amount | string | YES | NULL | |
| total_cash_sub_amount | string | YES | NULL | |
| total_cash_remark | string | YES | NULL | |
| **Frontdesk Operations A** | | | | |
| new_check_in_number | string | YES | NULL | |
| new_check_in_amount | string | YES | NULL | |
| extension_number | string | YES | NULL | |
| extension_amount | string | YES | NULL | |
| transfer_number | string | YES | NULL | |
| transfer_amount | string | YES | NULL | |
| miscellaneous_number | string | YES | NULL | |
| miscellaneous_amount | string | YES | NULL | |
| food_number | string | YES | NULL | |
| food_amount | string | YES | NULL | |
| drink_number | string | YES | NULL | |
| drink_amount | string | YES | NULL | |
| other_number | string | YES | NULL | |
| other_amount | string | YES | NULL | |
| total_number | string | YES | NULL | |
| total_amount | string | YES | NULL | |
| **Frontdesk Operations B** | | | | |
| forwarded_room_check_in_number | string | YES | NULL | |
| forwarded_room_check_in_amount | string | YES | NULL | |
| key_remote_number | string | YES | NULL | |
| key_remote_amount | string | YES | NULL | |
| forwarded_guest_deposit_number | string | YES | NULL | |
| forwarded_guest_deposit_amount | string | YES | NULL | |
| current_guest_deposit_number | string | YES | NULL | |
| current_guest_deposit_amount | string | YES | NULL | |
| total_check_out_number | string | YES | NULL | |
| total_check_out_amount | string | YES | NULL | |
| expenses_number | string | YES | NULL | |
| expenses_amount | string | YES | NULL | |
| **Final Sales** | | | | |
| gross_sales | string | YES | NULL | |
| refund | string | YES | NULL | |
| expenses | string | YES | NULL | |
| discount | string | YES | NULL | |
| net_sales | string | YES | NULL | |
| **Cash Position** | | | | |
| opening_cash | string | YES | NULL | |
| forwarded_balance | string | YES | NULL | |
| cash_net_sales | string | YES | NULL | |
| remittance | string | YES | NULL | |
| **Cash Reconciliation** | | | | |
| expected_cash | string | YES | NULL | |
| actual_cash | string | YES | NULL | |
| difference | string | YES | NULL | |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `cash_drawers`
Physical cash drawer definitions.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| name | string | NO | | Drawer name |
| is_active | boolean | NO | true | Active status |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `cash_on_drawers`
Cash drawer transactions.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | bigint | NO | | FK to branches |
| frontdesk_id | bigint | NO | | FK to frontdesks |
| cash_drawer_id | bigint | NO | | FK to cash_drawers |
| amount | decimal(15,2) | NO | 0 | Transaction amount |
| deductions | decimal | YES | NULL | Deductions |
| transaction_date | date | NO | | Transaction date |
| transaction_type | string | NO | | Type: initial/top-up/withdrawal |
| shift | string | NO | | Shift identifier |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `remittances`
Cash remittance records.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| shift_log_id | foreignId | YES | NULL | FK to shift_logs |
| user_id | foreignId | YES | NULL | FK to users |
| branch_id | foreignId | YES | NULL | FK to branches |
| total_remittance | decimal(15,2) | NO | 0 | Remittance amount |
| description | text | YES | NULL | Notes |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

## Expense Tables

### `expense_categories`
Expense category definitions.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| name | string | NO | | Category name |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `expenses`
Individual expense records.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | YES | NULL | FK to branches |
| user_id | foreignId | YES | NULL | FK to users |
| shift_log_id | foreignId | YES | NULL | FK to shift_logs |
| expense_category_id | foreignId | NO | | FK to expense_categories |
| name | string | NO | | Expense name |
| description | string | YES | NULL | Description |
| amount | string | NO | | Expense amount |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

## Kitchen Menu Tables

### `menu_categories`
Kitchen menu categories.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| name | string | NO | | Category name |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `menus`
Kitchen menu items.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| menu_category_id | foreignId | NO | | FK to menu_categories |
| name | string | NO | | Item name |
| price | string | NO | | Item price |
| item_code | string | YES | NULL | Item code |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `inventories`
Kitchen inventory tracking.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| menu_id | foreignId | NO | | FK to menus |
| number_of_serving | double | NO | | Available servings |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

## Frontdesk Menu Tables

### `frontdesk_categories`
Frontdesk POS categories.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| name | string | NO | | Category name |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `frontdesk_menus`
Frontdesk POS menu items.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| frontdesk_category_id | foreignId | NO | | FK to frontdesk_categories |
| name | string | NO | | Item name |
| price | string | NO | | Item price |
| item_code | string | YES | NULL | Item code |
| image | string | YES | NULL | Image path |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `frontdesk_inventories`
Frontdesk inventory tracking.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| frontdesk_menu_id | foreignId | NO | | FK to frontdesk_menus |
| number_of_serving | double | NO | | Available servings |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

## Pub Menu Tables

### `pub_categories`
Pub/bar menu categories.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| name | string | NO | | Category name |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `pub_menus`
Pub/bar menu items.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| pub_category_id | foreignId | NO | | FK to pub_categories |
| name | string | NO | | Item name |
| price | string | NO | | Item price |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `pub_inventories`
Pub inventory tracking.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| pub_menu_id | foreignId | NO | | FK to pub_menus |
| number_of_serving | double | NO | | Available servings |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

## Item Tables

### `hotel_items`
Chargeable hotel items (damage charges, etc.)

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| name | string | NO | | Item name |
| price | integer | NO | | Item price |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `requestable_items`
Items guests can request.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| name | string | NO | | Item name |
| price | integer | NO | | Item price |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

## Housekeeping Tables

### `cleaning_histories`
Room cleaning records.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| user_id | foreignId | NO | | FK to users (roomboy) |
| room_id | foreignId | NO | | FK to rooms |
| floor_id | foreignId | NO | | FK to floors |
| branch_id | foreignId | NO | | FK to branches |
| start_time | string | NO | | Cleaning start |
| end_time | string | NO | | Cleaning end |
| current_assigned_floor_id | boolean | NO | | Was assigned floor |
| expected_end_time | string | NO | | Expected completion |
| cleaning_duration | string | NO | | Total duration |
| delayed_cleaning | boolean | NO | | Delayed flag |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

## Report Tables

### `new_guest_reports`
New check-in reports.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| room_id | foreignId | NO | | FK to rooms |
| checkin_details_id | foreignId | NO | | FK to checkin_details |
| shift_date | string | NO | | Report date |
| shift | string | NO | | Shift identifier |
| frontdesk_id | integer | NO | | Frontdesk ID |
| partner_name | string | NO | | Partner name |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `check_out_guest_reports`
Check-out reports.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| room_id | foreignId | NO | | FK to rooms |
| checkin_details_id | foreignId | NO | | FK to checkin_details |
| shift_date | string | NO | | Report date |
| shift | string | NO | | Shift identifier |
| frontdesk_id | integer | NO | | Frontdesk ID |
| partner_name | string | NO | | Partner name |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `extended_guest_reports`
Stay extension reports.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| room_id | foreignId | NO | | FK to rooms |
| checkin_details_id | foreignId | NO | | FK to checkin_details |
| number_of_extension | integer | NO | | Extension count |
| total_hours | integer | NO | | Total extended hours |
| shift | string | NO | | Shift identifier |
| frontdesk_id | integer | NO | | Frontdesk ID |
| partner_name | string | NO | | Partner name |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `transfered_guest_reports`
Room transfer reports.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| checkin_detail_id | foreignId | NO | | FK to checkin_details |
| previous_room_id | foreignId | NO | | Previous room FK |
| new_room_id | foreignId | NO | | New room FK |
| rate_id | foreignId | NO | | FK to rates |
| previous_amount | decimal(15,2) | NO | 0 | Previous rate |
| new_amount | decimal(15,2) | NO | 0 | New rate |
| original_check_in_time | datetime | NO | | Original check-in |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `room_boy_reports`
Roomboy cleaning reports.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | YES | NULL | FK to branches |
| room_id | foreignId | NO | | FK to rooms |
| checkin_details_id | foreignId | NO | | FK to checkin_details |
| roomboy_id | foreignId | NO | | FK to users |
| cleaning_start | datetime | NO | | Start time |
| cleaning_end | datetime | NO | | End time |
| total_hours_spent | integer | NO | | Hours spent |
| interval | integer | NO | | Interval |
| shift | string | NO | | Shift identifier |
| is_cleaned | boolean | NO | | Cleaned flag |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `unoccupied_room_reports`
Unoccupied room reports.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| shift | string | NO | | Shift identifier |
| rooms | longText | NO | | Room list (JSON/text) |
| frontdesk_id | integer | NO | | Frontdesk ID |
| partner_name | string | NO | | Partner name |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

## System Tables

### `activity_logs`
User activity audit trail.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| branch_id | foreignId | NO | | FK to branches |
| user_id | foreignId | NO | | FK to users |
| activity | string | NO | | Activity type |
| description | string | YES | NULL | Activity details |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `sessions`
User sessions (Laravel).

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | string | NO | | Primary key |
| user_id | foreignId | YES | NULL | FK to users |
| ip_address | string(45) | YES | NULL | Client IP |
| user_agent | text | YES | NULL | Browser info |
| payload | longText | NO | | Session data |
| last_activity | integer | NO | | Last activity timestamp |

---

### `personal_access_tokens`
API tokens (Sanctum).

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| tokenable_type | string | NO | | Polymorphic type |
| tokenable_id | bigint | NO | | Polymorphic ID |
| name | string | NO | | Token name |
| token | string(64) | NO | | Hashed token |
| abilities | text | YES | NULL | Token abilities |
| last_used_at | timestamp | YES | NULL | Last usage |
| expires_at | timestamp | YES | NULL | Expiration |
| created_at | timestamp | YES | | |
| updated_at | timestamp | YES | | |

---

### `password_resets` (Legacy) / `password_reset_tokens`
Password reset tokens.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| email | string | NO | | User email (primary) |
| token | string | NO | | Reset token |
| created_at | timestamp | YES | NULL | Creation time |

---

### `failed_jobs`
Failed queue jobs.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| uuid | string | NO | | Unique identifier |
| connection | text | NO | | Queue connection |
| queue | text | NO | | Queue name |
| payload | longText | NO | | Job payload |
| exception | longText | NO | | Exception details |
| failed_at | timestamp | NO | CURRENT | Failure time |

---

### `jobs`
Queued jobs.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | NO | auto | Primary key |
| queue | string | NO | | Queue name |
| payload | longText | NO | | Job payload |
| attempts | tinyint | NO | | Attempt count |
| reserved_at | integer | YES | NULL | Reserved timestamp |
| available_at | integer | NO | | Available timestamp |
| created_at | integer | NO | | Creation timestamp |

---

## Spatie Permission Tables

### `permissions`
| Column | Type |
|--------|------|
| id | bigint |
| name | string |
| guard_name | string |
| created_at | timestamp |
| updated_at | timestamp |

### `roles`
| Column | Type |
|--------|------|
| id | bigint |
| name | string |
| guard_name | string |
| created_at | timestamp |
| updated_at | timestamp |

### `model_has_permissions`
| Column | Type |
|--------|------|
| permission_id | bigint |
| model_type | string |
| model_id | bigint |

### `model_has_roles`
| Column | Type |
|--------|------|
| role_id | bigint |
| model_type | string |
| model_id | bigint |

### `role_has_permissions`
| Column | Type |
|--------|------|
| permission_id | bigint |
| role_id | bigint |

---

## Transaction System — Deep Analysis

### How Transactions Work (Money Flow)

Every financial event during a guest's stay creates a `transactions` record. The flow:

```
CHECK-IN
├── Type 1: Room Charge (₱500) ──── PAID immediately
├── Type 2: Deposit (₱200) ──────── PAID (held for key/remote)
└── Type 2: Excess Deposit ──────── PAID (if guest overpaid)

DURING STAY (all created UNPAID, paid separately)
├── Type 6: Extension ──── charged when guest extends
├── Type 9: Food & Bev ── frontdesk/kitchen adds food order
├── Type 8: Amenities ──── frontdesk adds item charges
├── Type 4: Damage ─────── damaged items charged
└── Type 7: Transfer ───── room transfer (reference record)

PAYMENT (two methods)
├── Cash: updates transaction.paid_amount, paid_at; excess → Type 2 deposit
└── Deposit: updates transaction.paid_amount; creates Type 5 cashout

CHECK-OUT
├── Settle all unpaid transactions
├── Refund remaining deposit
└── Room → Uncleaned
```

### Transaction Types (from TransactionTypeSeeder)

| ID | Name | Direction | Created When | Paid Immediately? |
|----|------|-----------|-------------|-------------------|
| 1 | Check In | Money IN | At check-in | Yes |
| 2 | Deposit | Money HELD | Check-in, excess payments, transfers | Yes |
| 3 | Kitchen Order | — | **NEVER USED (dead code)** | N/A |
| 4 | Damage Charges | Money IN | During stay or checkout | No |
| 5 | Cashout | Money OUT | When deposit pays a bill | Yes (auto) |
| 6 | Extension | Money IN | When guest extends | No |
| 7 | Transfer Room | Reference | When guest transfers | Marked paid (amount often 0) |
| 8 | Amenities | Money IN | Frontdesk adds charges | No |
| 9 | Food and Beverages | Money IN | Frontdesk/kitchen adds food | No |

### Cash Tracking (Dual System)

Cash is tracked in TWO places that must stay in sync manually:
- `transactions` — records what happened financially
- `cash_on_drawers` — records physical cash movement in the drawer

Every transaction creation also creates a `CashOnDrawer` record. If one is missed, shift reconciliation breaks.

### Payment Logic

```
payable_amount = what customer owes
paid_amount    = what customer actually paid (cash given)
change_amount  = cash returned to customer
deposit_amount = excess saved as guest deposit

Cash payment:  paid_amount = user input, change = excess, deposit = excess
Deposit payment: paid_amount = payable, change = 0, creates Type 5 cashout
```

### Deposit Tracking

```
Available Deposit = checkin_details.total_deposit - checkin_details.total_deduction
```
- `total_deposit` increases when: initial deposit, excess payments, transfer excess
- `total_deduction` increases when: deposit used to pay a bill (Type 5 cashout created)

### Where Transactions Are Created (Code Locations)

| Type | Component | Method |
|------|-----------|--------|
| 1 | CheckInFromKiosk, RoomMonitoring, CheckInCo | saveCheckIn(), storeGuest(), saveCheckInCO() |
| 2 | CheckInFromKiosk, GuestTransaction, TransferRoom, CheckOutGuest | Multiple (deposits from various sources) |
| 4 | GuestTransaction, CheckOutGuest, ManageGuestTransaction | addDamageCharges() |
| 5 | GuestTransaction | addPaymentWithDeposit(), deductDeposit() |
| 6 | ExtendGuest, GuestTransaction, ManageGuestTransaction | saveExtend() |
| 7 | TransferRoom, GuestTransaction, ManageGuestTransaction | saveTransfer() |
| 8 | GuestTransaction, ManageGuestTransaction | addAmenities() |
| 9 | GuestTransaction, ManageGuestTransaction, Kitchen/Transaction | addFood() |

---

## Known Issues & Notes for Rebuild

### Transaction System Problems

1. **All amounts are integers** — `payable_amount`, `paid_amount`, `change_amount`, `deposit_amount` are all `integer`. No decimal support (₱499.50 impossible).

2. **`assigned_frontdesk_id` is JSON but model uses belongsTo** — Column stores `json_encode([id, "Name"])` but model defines `belongsTo(User::class)`. Relationship is broken.

3. **`deposit_amount` is overloaded** — At check-in it means "deposit collected". During payment it means "excess change saved as deposit". Ambiguous.

4. **Type 3 "Kitchen Order" is dead** — Seeded but never created anywhere. Kitchen uses Type 9 instead.

5. **`is_co` column never used** — Added to transactions AND guests tables but never set to `true` or queried.

6. **`is_override` only set in one place** — Only TransferRoom sets it. Never queried in reports.

7. **Kitchen module skips shift_log_id and cash_drawer_id** — All other creation paths populate these. Kitchen doesn't.

8. **Payments mutate the original transaction** — `paid_amount` and `paid_at` are updated on the same record. No separate payment record. Can't tell if guest paid in multiple attempts or whether it was cash vs deposit.

9. **Transfer rewrites the original check-in transaction** — `TransferRoom.php` finds the Type 1 transaction and CHANGES its `payable_amount` and `paid_amount` to the new room rate. Destroys historical data.

10. **Missing columns without migrations** — `override_at` and `transfer_reason_id` are used in code but no migration creates them. Suggests manual DB changes or lost migrations.

11. **Checkout flow is incomplete** — `checkOutGuest()` and `payDamageCharge()` methods are called from Blade views but not implemented in the component.

12. **shift_log_id detection is fragile** — Uses "online in last 5 minutes" session check to find the active shift. Race conditions possible.

### Data Type Inconsistencies
- `transactions` amounts are `integer` — should be `decimal(10,2)`
- `menus.price`, `frontdesk_menus.price`, `pub_menus.price` are `string` — should be `decimal`
- `expenses.amount` is `string` — should be `decimal`
- `stay_extensions.hours`, `stay_extensions.amount` are `string` — should be `integer`/`decimal`
- Many `frontdesk_shifts` columns are `string` — should be numeric types

### Denormalized Data
- `users.branch_name` duplicates `branches.name`
- `guests.type_id` duplicates `rates.type_id`
- `checkin_details.type_id` duplicates room's type

### Duplicate Table Structures
- Three separate menu systems: `menus`/`inventories`, `frontdesk_menus`/`frontdesk_inventories`, `pub_menus`/`pub_inventories`
- Could be consolidated with a `menu_type` discriminator

### Missing Foreign Key Constraints
- Many `foreignId` columns lack actual constraints
- `frontdesk_id` in reports is `integer` not `foreignId`

### Suggested Improvements
1. Use consistent decimal types for all monetary values
2. Add proper foreign key constraints with cascade rules
3. Consider consolidating menu tables
4. Remove denormalized columns
5. Use enums for status fields
6. Add indexes for frequently queried columns
