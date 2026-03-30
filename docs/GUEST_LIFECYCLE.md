# Guest Lifecycle Documentation

This document explains the complete journey of a guest from check-in to check-out, including all states, transitions, and data flows.

---

## Overview: The Guest Journey

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         GUEST LIFECYCLE OVERVIEW                             │
└─────────────────────────────────────────────────────────────────────────────┘

  BEFORE CHECK-IN          DURING STAY              AFTER CHECK-OUT
  ───────────────          ───────────              ────────────────

  ┌─────────────┐         ┌─────────────┐          ┌─────────────┐
  │   KIOSK     │         │  OCCUPIED   │          │  UNCLEANED  │
  │  REQUEST    │────────►│   (Guest    │─────────►│   (Room     │
  │  (Pending)  │         │   staying)  │          │   dirty)    │
  └─────────────┘         └─────────────┘          └─────────────┘
        │                       │                        │
        │                       │                        │
        ▼                       ▼                        ▼
  ┌─────────────┐         ┌─────────────┐          ┌─────────────┐
  │  FRONTDESK  │         │  EXTEND     │          │  ROOMBOY    │
  │  CONFIRMS   │         │  TRANSFER   │          │   CLEANS    │
  │             │         │  ADD ITEMS  │          │             │
  └─────────────┘         └─────────────┘          └─────────────┘
                                                         │
                                                         ▼
                                                   ┌─────────────┐
                                                   │  AVAILABLE  │
                                                   │  (Ready for │
                                                   │  next guest)│
                                                   └─────────────┘
```

---

## Room Status Flow

Rooms have 5 possible statuses:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           ROOM STATUS FLOW                                   │
└─────────────────────────────────────────────────────────────────────────────┘

                    ┌───────────────────────────────────────┐
                    │                                       │
                    ▼                                       │
              ┌──────────┐                                  │
              │AVAILABLE │◄─────────────────────────────────┤
              └──────────┘                                  │
                    │                                       │
                    │ Guest checks in                       │
                    ▼                                       │
              ┌──────────┐                                  │
              │ OCCUPIED │                                  │
              └──────────┘                                  │
                    │                                       │
                    │ Guest checks out                      │
                    ▼                                       │
              ┌──────────┐                                  │
              │UNCLEANED │                                  │
              └──────────┘                                  │
                    │                                       │
                    │ Roomboy cleans                        │
                    ▼                                       │
              ┌──────────┐                                  │
              │ CLEANED  │──────────────────────────────────┘
              └──────────┘       (Auto or manual → Available)


              ┌──────────┐
              │MAINTENANCE│  ← Admin can set any room to this
              └──────────┘    (Out of service for repairs)
```

### Status Definitions

| Status | Color | Meaning | Who Changes It |
|--------|-------|---------|----------------|
| `Available` | Green | Ready for check-in | System/Admin |
| `Occupied` | Red | Guest currently staying | Frontdesk (check-in) |
| `Uncleaned` | Orange | Guest checked out, needs cleaning | System (auto after check-out) |
| `Cleaned` | Blue | Roomboy finished cleaning | Roomboy |
| `Maintenance` | Gray | Out of service | Admin |

---

## Phase 1: Check-In

### Option A: Kiosk Check-In (Guest Self-Service)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         KIOSK CHECK-IN FLOW                                  │
└─────────────────────────────────────────────────────────────────────────────┘

GUEST AT KIOSK:
┌──────────────────────────────────────────────────────────────────────────┐
│ 1. Select Room Type (Standard, Deluxe, etc.)                             │
│ 2. Select Available Room (101, 102, etc.)                                │
│ 3. Select Staying Hours (3hr, 6hr, 12hr, 24hr)                          │
│ 4. Enter Name & Contact                                                  │
│ 5. Optionally apply discount                                             │
│ 6. Submit → Creates PENDING request                                      │
└──────────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
SYSTEM CREATES:
┌──────────────────────────────────────────────────────────────────────────┐
│ • guests record (temporary)                                              │
│ • temporary_check_in_kiosks record (the pending request)                │
│ • Room is HELD (not available for others)                               │
│ • Timer starts (expires in X minutes)                                    │
│ • Pusher notification sent to frontdesk                                  │
└──────────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
FRONTDESK CONFIRMS:
┌──────────────────────────────────────────────────────────────────────────┐
│ 1. Opens pending request                                                 │
│ 2. Verifies guest at counter                                            │
│ 3. Collects payment                                                      │
│ 4. Confirms check-in:                                                    │
│    • Creates checkin_details record                                      │
│    • Creates transaction (room charge)                                   │
│    • Room status → Occupied                                              │
│    • Deletes temporary records                                           │
│    • Generates QR code                                                   │
└──────────────────────────────────────────────────────────────────────────┘
```

### Option B: Direct Frontdesk Check-In

```
FRONTDESK:
┌──────────────────────────────────────────────────────────────────────────┐
│ 1. Clicks on Available room in Room Monitoring                          │
│ 2. Fills guest info: Name, Contact                                       │
│ 3. Selects staying hours                                                 │
│ 4. Collects deposit (if required)                                        │
│ 5. Collects payment                                                      │
│ 6. Confirms check-in → All records created                              │
└──────────────────────────────────────────────────────────────────────────┘
```

### Data Created at Check-In

```sql
-- 1. Guest Record
guests:
├── id: 1234
├── branch_id: 1
├── name: "Juan Dela Cruz"
├── contact: "09171234567"
├── qr_code: "1240001"           -- Unique code for this stay
├── room_id: 101
├── rate_id: 5                   -- Which rate was used
├── type_id: 1                   -- Room type
├── static_amount: 500           -- Original room charge
├── is_long_stay: false
├── has_discount: false
└── created_at: "2024-03-15 10:00:00"

-- 2. Check-In Details (The Stay Record)
checkin_details:
├── id: 5678
├── guest_id: 1234
├── room_id: 101
├── rate_id: 5
├── type_id: 1
├── static_amount: 500
├── hours_stayed: 3              -- Original hours booked
├── total_deposit: 200           -- Deposit collected
├── total_deduction: 0           -- Deductions (damages, etc.)
├── check_in_at: "2024-03-15 10:00:00"
├── check_out_at: "2024-03-15 13:00:00"  -- Expected checkout
├── is_check_out: false          -- Still staying
├── number_of_hours: 3           -- Cycle position (for extensions)
├── next_extension_is_original: false
└── is_long_stay: false

-- 3. Transaction Record (Room Charge)
transactions:
├── id: 9999
├── branch_id: 1
├── guest_id: 1234
├── checkin_detail_id: 5678
├── room_id: 101
├── transaction_type_id: 1       -- Check-in charge
├── payable_amount: 500
├── remarks: "Room Charge 3hrs"
└── created_at: "2024-03-15 10:00:00"
```

---

## Phase 2: During Stay (Occupied)

While the guest is staying, several actions can occur:

### 2.1 Extension

Guest wants to stay longer:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           EXTENSION FLOW                                     │
└─────────────────────────────────────────────────────────────────────────────┘

FRONTDESK:
┌──────────────────────────────────────────────────────────────────────────┐
│ 1. Clicks on Occupied room                                               │
│ 2. Clicks "Extend"                                                       │
│ 3. Selects extension hours (1hr, 2hr, 3hr, 6hr, etc.)                   │
│ 4. System calculates price (extension rate or original rate)            │
│ 5. Collects payment                                                      │
│ 6. Confirms extension                                                    │
└──────────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
SYSTEM UPDATES:
┌──────────────────────────────────────────────────────────────────────────┐
│ • checkin_details.check_out_at += extension hours                        │
│ • checkin_details.number_of_hours updated (cycle position)              │
│ • Creates stay_extensions record                                         │
│ • Creates transaction (extension charge)                                 │
│ • Creates extended_guest_report                                          │
└──────────────────────────────────────────────────────────────────────────┘
```

**Data Created:**

```sql
-- Stay Extension Record
stay_extensions:
├── id: 111
├── guest_id: 1234
├── extension_id: 3              -- Which extension rate
├── hours: "6"                   -- Hours extended (stored as string!)
├── amount: "100"                -- Amount charged (stored as string!)
└── frontdesk_ids: ["FD001"]

-- Transaction
transactions:
├── transaction_type_id: 6       -- Extension charge
├── payable_amount: 100
└── remarks: "Extension 6hrs"
```

### 2.2 Room Transfer

Guest wants to move to a different room:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         ROOM TRANSFER FLOW                                   │
└─────────────────────────────────────────────────────────────────────────────┘

FRONTDESK:
┌──────────────────────────────────────────────────────────────────────────┐
│ 1. Clicks on Occupied room                                               │
│ 2. Clicks "Transfer"                                                     │
│ 3. Selects new room (must be Available)                                  │
│ 4. Calculates price difference (if new room is more expensive)          │
│ 5. Collects additional payment (if any)                                  │
│ 6. Confirms transfer                                                     │
└──────────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
SYSTEM UPDATES:
┌──────────────────────────────────────────────────────────────────────────┐
│ • Old room status → Uncleaned                                            │
│ • New room status → Occupied                                             │
│ • guest.room_id → new room                                               │
│ • guest.previous_room_id → old room                                      │
│ • checkin_details.room_id → new room                                     │
│ • Creates transfer transaction (if price diff)                           │
│ • Creates transfered_guest_report                                        │
└──────────────────────────────────────────────────────────────────────────┘
```

### 2.3 Add Charges (POS)

Guest orders food, uses amenities, or damages something:

```
CHARGE TYPES:
├── transaction_type_id: 2  → Deposit
├── transaction_type_id: 4  → Damages
├── transaction_type_id: 6  → Extension
├── transaction_type_id: 7  → Transfer
├── transaction_type_id: 8  → Amenities
└── transaction_type_id: 9  → Food/Drinks
```

All charges are added to `transactions` table linked to the guest.

---

## Phase 3: Check-Out

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           CHECK-OUT FLOW                                     │
└─────────────────────────────────────────────────────────────────────────────┘

GUEST TRANSACTION VIEW:
┌──────────────────────────────────────────────────────────────────────────┐
│                                                                          │
│  ROOM #101 - Juan Dela Cruz                                              │
│  Check-in: 10:00 AM | Expected out: 4:00 PM                             │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ TRANSACTIONS                                                       │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │ Room Charge (3hrs)              ₱500.00      [PAID]               │ │
│  │ Extension (3hrs)                ₱100.00      [PAID]               │ │
│  │ Food - Burger                   ₱150.00      [UNPAID]             │ │
│  │ Amenities - Extra Towel         ₱50.00       [UNPAID]             │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │ TOTAL:                          ₱800.00                           │ │
│  │ PAID:                           ₱600.00                           │ │
│  │ DEPOSIT:                        ₱200.00                           │ │
│  │ ─────────────────────────────────────────                         │ │
│  │ TO COLLECT:                     ₱0.00                             │ │
│  │ TO REFUND:                      ₱0.00                             │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  [ PAY ALL UNPAID ]    [ PROCESS CHECK-OUT ]                            │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘
```

### Check-Out Process

```
FRONTDESK:
┌──────────────────────────────────────────────────────────────────────────┐
│ 1. Reviews all transactions                                              │
│ 2. Collects remaining balance OR refunds excess deposit                 │
│ 3. Clicks "Process Check-Out"                                           │
│ 4. System prompts for reminders (room key returned? damages?)           │
│ 5. Confirms check-out                                                    │
└──────────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
SYSTEM UPDATES:
┌──────────────────────────────────────────────────────────────────────────┐
│ • Room status → Uncleaned                                                │
│ • room.last_checkin_at = check_in_at                                    │
│ • room.last_checkout_at = NOW                                           │
│ • room.time_to_clean = NOW + 3 hours (deadline)                         │
│ • checkin_details.is_check_out = TRUE                                   │
│ • checkin_details.check_out_at = NOW (actual checkout time)             │
│ • Creates check_out_guest_report                                         │
│ • Creates activity_log entry                                             │
└──────────────────────────────────────────────────────────────────────────┘
```

---

## Phase 4: Cleaning (Uncleaned → Available)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           CLEANING FLOW                                      │
└─────────────────────────────────────────────────────────────────────────────┘

ROOMBOY VIEW:
┌──────────────────────────────────────────────────────────────────────────┐
│  ROOMS TO CLEAN                                                          │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ Room 101 │ Checkout: 4:00 PM │ Deadline: 7:00 PM │ [ CLEAN ]      │ │
│  │ Room 205 │ Checkout: 3:30 PM │ Deadline: 6:30 PM │ [ CLEAN ]      │ │
│  │ Room 302 │ Checkout: 2:00 PM │ Deadline: 5:00 PM │ [ OVERDUE! ]   │ │
│  └────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────┘
                              │
                              │ Roomboy clicks "Clean"
                              ▼
┌──────────────────────────────────────────────────────────────────────────┐
│ 1. Roomboy assigned to room                                              │
│ 2. Roomboy finishes cleaning                                             │
│ 3. Clicks "Mark as Cleaned"                                              │
│ 4. Room status → Available                                               │
│ 5. Creates cleaning_history record                                       │
│ 6. Creates roomboy_report record                                         │
└──────────────────────────────────────────────────────────────────────────┘
```

---

## Database Tables Involved

### Core Tables

| Table | Purpose |
|-------|---------|
| `guests` | Guest identity and booking info |
| `checkin_details` | The actual stay record (times, amounts) |
| `rooms` | Room status and info |
| `transactions` | All charges and payments |
| `stay_extensions` | Extension history |

### Report Tables

| Table | Purpose |
|-------|---------|
| `new_guest_reports` | New check-in records for reporting |
| `check_out_guest_reports` | Check-out records for reporting |
| `extended_guest_reports` | Extension records for reporting |
| `transfered_guest_reports` | Transfer records for reporting |
| `roomboy_reports` | Cleaning records for reporting |
| `cleaning_histories` | Cleaning audit trail |

### Temporary Tables

| Table | Purpose |
|-------|---------|
| `temporary_check_in_kiosks` | Pending kiosk requests |
| `temporary_reserved` | Rooms temporarily held |

---

## V3 Problems with Guest Lifecycle

### Problem 1: Duplicate Data

```
Same data stored in multiple places:
├── guests.room_id AND checkin_details.room_id
├── guests.rate_id AND checkin_details.rate_id
├── guests.type_id AND checkin_details.type_id
├── guests.static_amount AND checkin_details.static_amount
└── guests.is_long_stay AND checkin_details.is_long_stay

Which is the source of truth?
```

### Problem 2: String Types for Numbers

```sql
stay_extensions.hours VARCHAR    -- Should be INT
stay_extensions.amount VARCHAR   -- Should be DECIMAL
guests.discount_amount VARCHAR   -- Should be DECIMAL
```

### Problem 3: No Soft Deletes

```
When guest checks out:
├── checkin_details.is_check_out = TRUE (good)
├── But guest record remains (no status field)
└── Room still references guest_id

Hard to tell: Is this guest currently staying or historical?
```

### Problem 4: Scattered Reports

```
Reports are created separately:
├── new_guest_reports (check-in)
├── check_out_guest_reports (check-out)
├── extended_guest_reports (extension)
├── transfered_guest_reports (transfer)

These should be events/logs, not separate tables.
```

### Problem 5: QR Code Not Unique

```php
$transaction_code = $branch_id . $year . str_pad($count, 4, '0', STR_PAD_LEFT);
// Example: 12400001

// Problem: Resets each year
// 2024: 12400001, 12400002...
// 2025: 12500001 ← Starts over

// If branch had 10,000+ guests, code overflows (4 digits max)
```

---

## V4 Proposal: Cleaner Guest Lifecycle

### Single Source of Truth

```sql
-- V4: stays table (replaces guests + checkin_details)
stays:
├── id
├── branch_id
├── room_id
├── rate_id

-- Guest info (denormalized for convenience)
├── guest_name
├── guest_contact

-- Booking
├── initial_hours
├── initial_amount DECIMAL
├── is_long_stay BOOLEAN
├── long_stay_days INT

-- Timing
├── check_in_at DATETIME
├── expected_checkout_at DATETIME
├── actual_checkout_at DATETIME NULL

-- Cycle tracking (for extensions)
├── cycle_hours INT
├── use_original_rate_next BOOLEAN

-- Status
├── status ENUM('active', 'checked_out', 'cancelled')

-- Payment tracking
├── total_charges DECIMAL
├── total_paid DECIMAL
├── deposit_amount DECIMAL

-- Audit
├── checked_in_by BIGINT
├── checked_out_by BIGINT
├── created_at
├── updated_at

-- Unique code
├── code VARCHAR(20) UNIQUE  -- UUID or structured code
```

### Event-Based History

```sql
-- V4: stay_events table (replaces all report tables)
stay_events:
├── id
├── stay_id
├── event_type ENUM('check_in', 'extension', 'transfer', 'charge', 'payment', 'check_out')
├── event_data JSON      -- Details of what happened
├── performed_by BIGINT  -- User who did it
├── created_at

-- Example events:
-- Check-in:   { "room": 101, "hours": 3, "amount": 500 }
-- Extension:  { "hours": 6, "amount": 100, "new_checkout": "2024-03-15 19:00" }
-- Transfer:   { "from_room": 101, "to_room": 205, "price_diff": 100 }
-- Charge:     { "type": "food", "item": "Burger", "amount": 150 }
-- Payment:    { "amount": 500, "method": "cash" }
-- Check-out:  { "final_amount": 800, "refund": 0 }
```

### Benefits

| Aspect | V3 | V4 |
|--------|----|----|
| **Source of Truth** | Scattered across 2+ tables | Single `stays` table |
| **History** | 4+ report tables | Single `stay_events` table |
| **Data Types** | Mix of string/int | Proper types (DECIMAL, INT) |
| **Status** | is_check_out boolean | Explicit status enum |
| **Audit** | Limited | Full event history |
| **Queries** | Complex joins | Simple and fast |

---

## Complete Lifecycle Diagram (V4)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        V4 GUEST LIFECYCLE                                    │
└─────────────────────────────────────────────────────────────────────────────┘

                              KIOSK REQUEST
                                   │
                                   ▼
                    ┌──────────────────────────────┐
                    │      kiosk_requests          │
                    │      (pending)               │
                    └──────────────────────────────┘
                                   │
                                   │ Frontdesk confirms
                                   ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                              CREATE STAY                                     │
│                                                                              │
│  stays:                          │  stay_events:                            │
│  ├── status: 'active'            │  └── type: 'check_in'                   │
│  ├── room_id: 101                │      data: { room: 101, amount: 500 }   │
│  └── check_in_at: NOW            │                                          │
│                                  │                                          │
│  rooms:                          │  transactions:                           │
│  └── status: 'occupied'          │  └── type: 'room_charge', amount: 500   │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
                                   │
           ┌───────────────────────┼───────────────────────┐
           │                       │                       │
           ▼                       ▼                       ▼
    ┌─────────────┐         ┌─────────────┐         ┌─────────────┐
    │  EXTENSION  │         │  TRANSFER   │         │   CHARGE    │
    │             │         │             │         │   (POS)     │
    │ stay_events │         │ stay_events │         │ stay_events │
    │ type:extend │         │ type:transfer│        │ type:charge │
    └─────────────┘         └─────────────┘         └─────────────┘
           │                       │                       │
           └───────────────────────┼───────────────────────┘
                                   │
                                   ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                              CHECK-OUT                                       │
│                                                                              │
│  stays:                          │  stay_events:                            │
│  ├── status: 'checked_out'       │  └── type: 'check_out'                  │
│  └── actual_checkout_at: NOW     │      data: { final: 800, refund: 0 }    │
│                                  │                                          │
│  rooms:                          │                                          │
│  └── status: 'uncleaned'         │                                          │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
                                   │
                                   ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                              CLEANING                                        │
│                                                                              │
│  cleaning_tasks:                 │  rooms:                                  │
│  └── status: 'completed'         │  └── status: 'available'                │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## Summary

### Guest Lifecycle Phases

| Phase | Room Status | Key Actions |
|-------|-------------|-------------|
| 1. Check-In | Available → Occupied | Create guest, checkin_details, transaction |
| 2. During Stay | Occupied | Extensions, transfers, charges |
| 3. Check-Out | Occupied → Uncleaned | Settle balance, update records |
| 4. Cleaning | Uncleaned → Available | Roomboy cleans, room ready |

### Key Tables (V3)

| Table | Records |
|-------|---------|
| `guests` | Guest identity |
| `checkin_details` | Stay details |
| `stay_extensions` | Extension history |
| `transactions` | All charges |
| `*_reports` | Various reports |

### V4 Improvements

| Change | Benefit |
|--------|---------|
| Single `stays` table | One source of truth |
| Event-based history | Full audit trail |
| Proper data types | No string amounts |
| Status enum | Clear state management |
