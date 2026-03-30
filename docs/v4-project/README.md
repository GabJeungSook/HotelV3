# HotelV4 — Project Documentation

## Overview

**HotelV4 (HOMI)** is a complete rebuild of the Alma Hotel Management System. It manages the full lifecycle of a multi-branch, hourly-rate hotel operation — from guest check-in to checkout, with integrated POS, shift-based cash management, kiosk self-service, housekeeping, and comprehensive reporting.

This is **not an upgrade** of V3. It is a **ground-up redesign** with a new database schema, new architecture, and new patterns — while preserving the exact same business processes Alma's staff uses daily.

---

## Why Rebuild?

**V3 worked, but it was breaking.** After 3 years of development, the system accumulated critical technical debt:

- **Reports were inaccurate** — wrong schema forced complex workarounds (forwarded deposit chain walks, hardcoded values, string-matching on remarks). The team spent a month debugging report inconsistencies.
- **Data types were wrong** — money stored as integers and strings, dates stored as strings, amounts stored as VARCHAR. SQL math was impossible.
- **Duplicate data everywhere** — same information stored in 2-3 tables (guests + checkin_details, 3 POS systems, 3 shift tables). No single source of truth.
- **No audit trail** — transactions were mutated after creation (paid_amount overwritten), transfers rewrote original charges, overrides had no tracking.
- **5 separate report tables** — new_guest_reports, check_out_guest_reports, extended_guest_reports, transferred_guest_reports, room_boy_reports — all had to stay in sync with actual data. They drifted.
- **28 documented issues** in total — see `v3-problems/V3_PROBLEMS_SOLVED.md` for the complete list.

---

## What V4 Delivers

| Goal | How |
|------|-----|
| **Accurate reports** | No separate report tables. All reports computed from core data. Every transaction has `shift_id` (required). No chain walks. |
| **Proper data types** | All money is `decimal(10,2)`. All dates are `datetime`. All hours are `integer`. |
| **Single source of truth** | One `stays` table (not guests + checkin_details). One `shifts` table (not 3). One POS system (not 3). |
| **Full audit trail** | Transactions are immutable — never update, only append. Voids create new records. Spatie Activitylog tracks every admin change with before/after values. |
| **No hardcoded values** | Every magic number (deposit amount, cleaning time, shift cutoff, kiosk timeout) is in `branch_settings` — configurable per branch. |
| **Clean architecture** | Traits for scoping, enums for types, services for business logic, observers for cache updates. Based on proven patterns from the general-pos project. |
| **Multi-branch ready** | Branch scoping via global scope trait. Staff can be deployed across branches. Superadmin sees everything. |
| **Scalable** | 35 well-designed tables replace 56+ V3 tables. 21 tables eliminated. No redundancy. |

---

## The Hotel Business

### What Kind of Hotel?

Alma operates **short-stay/transient hotels** — guests pay by the hour (6hrs, 12hrs, 24hrs), not by the night. Key characteristics:

- **Hourly rates** — each room has its own pricing per duration
- **24-hour operation** — guests check in/out anytime, staff work in AM/PM shifts
- **Walk-in focused** — most guests arrive without reservation
- **Kiosk self-service** — tablets in the lobby for guest check-in/out
- **Multi-branch** — Alma runs multiple hotel locations

### The Guest Journey

```
Guest arrives → Kiosk or frontdesk check-in → Pay room rate + deposit
    → Stay in room → Order food, request amenities
    → Extend stay if needed (cycle reset applies)
    → Transfer rooms if needed
    → Check out → Settle all charges → Deposit refunded
    → Room cleaned by roomboy → Available for next guest
```

### Business Rules That Drive the Design

1. **Extension Cycle Reset** — After every X hours (configurable per branch), guests must pay original rate instead of cheap extension rate. Prevents gaming the system.

2. **Deposit System** — Security deposit collected at check-in (key + remote). Can be used to pay charges during stay. Refunded at checkout if items returned.

3. **Shift-Based Cash Accountability** — Every frontdesk starts a shift with counted cash, every transaction tagged to their shift, cash reconciled at close. Shortages detected.

4. **Immutable Transactions** — Once created, never modified. Corrections use void + new charge. Full audit trail.

---

## System Roles

| Role | What They Do |
|------|-------------|
| **Superadmin** | Manage all branches, deploy staff, cross-branch reports, system configuration |
| **Admin** | Branch configuration (rooms, rates, staff, discounts, menus), view reports and activity logs |
| **Frontdesk** | Check-in/out guests, process payments, extensions, transfers, POS charges |
| **Back Office** | View financial reports, shift reconciliation, sales analysis |
| **Roomboy** | Clean rooms, view assigned floors, track cleaning time |
| **Kitchen Staff** | Manage kitchen menu, add food orders to guest bills |
| **Pub Staff** | Manage pub menu, add drink orders to guest bills |

---

## Database Schema

**35 custom tables + 5 Spatie Permission + 1 Spatie Activitylog = 41 total**

### Core Tables

| Table | Purpose |
|-------|---------|
| `branches` | Hotel locations |
| `branch_settings` | Per-branch configuration (12 settings) |
| `users` | Authentication only (email, password, is_active) |
| `user_profiles` | Personal information (name, DOB, contact, address) |
| `branch_user` | Multi-branch staff assignments (primary/deployed/oversight) |

### Rooms & Pricing

| Table | Purpose |
|-------|---------|
| `floors` | Building floors per branch |
| `room_types` | Room categories (Standard, Deluxe, Suite) — display only |
| `rooms` | Individual rooms with status tracking |
| `staying_hours` | Available check-in durations (6, 12, 18, 24 hrs) |
| `rates` | Per-room pricing for each duration |
| `extension_hours` | Available extension durations (1, 2, 3, 6 hrs) |
| `extension_rates` | Per-room extension pricing |

### Guest Lifecycle

| Table | Purpose |
|-------|---------|
| `stays` | One record per guest visit — the single source of truth |
| `stay_extensions` | Extension history with cycle tracking |
| `room_transfers` | Transfer history with snapshot amounts |
| `transfer_reasons` | Configurable transfer reasons |
| `discounts` | Discount definitions (percentage or fixed) |
| `applied_discounts` | Discount audit trail with verification |

### Financial

| Table | Purpose |
|-------|---------|
| `transactions` | Every money event — immutable, 10 types |
| `transaction_items` | Line items for food/amenity/damage orders |
| `shifts` | Frontdesk shift with cash tracking |
| `remittances` | Cash handed to management during shift |
| `expense_categories` | Expense category definitions |
| `expenses` | Individual expense records |

### Kiosk

| Table | Purpose |
|-------|---------|
| `kiosk_terminals` | Registered kiosk devices with API tokens |
| `kiosk_requests` | Temporary check-in/out requests (deleted after resolution) |

### Housekeeping

| Table | Purpose |
|-------|---------|
| `cleaning_tasks` | Room cleaning records with timing and override tracking |
| `floor_user` | Roomboy floor assignments (many-to-many) |

### POS & Menu

| Table | Purpose |
|-------|---------|
| `menu_categories` | Categories with service area (kitchen/frontdesk/pub/amenity/damage) |
| `menu_items` | Items with proper decimal prices |
| `menu_inventories` | Current stock levels |
| `menu_stock_logs` | Full audit trail of every stock movement |

### System

| Table | Purpose |
|-------|---------|
| `activity_log` | Spatie Activitylog — automatic model audit + manual business events |
| Spatie Permission (5 tables) | Roles and permissions |
| Laravel standard tables | Sessions, password resets, tokens, jobs, cache |

Full schema: `HOTEL_V4_COMPLETE_SCHEMA.md`

---

## Transaction System

10 transaction types, all immutable:

| Type | Purpose | Cash Effect |
|------|---------|-------------|
| `room_charge` | Check-in room rate | — |
| `extension` | Stay extension charge | — |
| `transfer_fee` | Room transfer price difference | — |
| `food` | Food & beverages | — |
| `amenity` | Hotel items | — |
| `damage` | Damage charges | — |
| `deposit_in` | Deposit collected | Cash IN |
| `deposit_out` | Deposit used or refunded | Cash OUT if refund |
| `payment` | Cash received for a charge | Cash IN |
| `void` | Cancel/correct a charge | Reverses original |

**Rule:** One charge = one payment. Never mutate. Always append.

---

## Reports (All Computed — No Report Tables)

| Report | Query Source |
|--------|-------------|
| Sales Report | `transactions` grouped by type, filtered by shift/date |
| Shift Reconciliation | `shifts` + `transactions` + `remittances` + `expenses` |
| Forwarded Guests | `stays` WHERE check_in_at < shift start |
| New Guest Report | `stays` WHERE check_in_at in range |
| Checkout Report | `stays` WHERE actual_checkout_at in range |
| Extension Report | `stay_extensions` in range |
| Transfer Report | `room_transfers` in range |
| Cleaning Report | `cleaning_tasks` with performance metrics |
| Expense Report | `expenses` by shift |
| Inventory Report | `menu_inventories` + `menu_stock_logs` |
| Unoccupied Rooms | `rooms` WHERE status = available |
| Cross-Branch Sales | Same queries + GROUP BY branch |
| Discount Usage | `applied_discounts` (NEW — impossible in V3) |
| Void Audit | `transactions` WHERE type = void (NEW — impossible in V3) |

---

## Technology Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12/13 |
| Admin Panel | Filament 5 |
| Frontend | Livewire 4 + Alpine.js |
| CSS | Tailwind CSS 4 |
| Auth | Sanctum (API tokens for kiosk) |
| Permissions | Spatie Permission |
| Audit | Spatie Activitylog |
| Media | Spatie Media Library |
| PDF | Laravel DomPDF |
| Real-time | Pusher + Laravel Echo |
| Testing | Pest 4 |

Full details: `V4_TECH_STACK.md`

---

## Architecture Patterns

| Pattern | Purpose |
|---------|---------|
| **ScopesToBranch trait** | Auto-filter all queries by active branch (multi-tenancy) |
| **PHP Enums** | Type-safe status/type values (RoomStatus, TransactionType, etc.) |
| **Service classes** | Business logic (CheckInService, PaymentService, ExtensionService) |
| **Model relation traits** | Keep models clean, relations in separate files |
| **Observers** | Auto-update cache columns on stays/shifts |
| **Spatie LogsActivity** | Automatic before/after logging on admin changes |
| **API Resources** | Structured JSON responses for kiosk/mobile API |

---

## Real-World Scenarios

Every flow is documented with realistic data in `examples/`:

### Journeys (Role-Based Full Day)

| Doc | Scenario |
|-----|----------|
| Guest Full Journey | Walk-in: check-in → food → extension → transfer → checkout (13 transactions) |
| Guest Kiosk Journey | Kiosk: senior discount → kitchen + pub POS → cycle reset → kiosk checkout (16 transactions) |
| Frontdesk Journey | Full shift: check-ins, payments, remittance, expense, reconciliation |
| Roomboy Journey | Assigned floors, clean rooms, 15-min minimum, override, delayed rooms |
| Kiosk Journey | Device auth, check-in flow, checkout flow, expired requests |
| Admin Journey | Rooms, rates, staff, discounts, settings, menu, kiosk registration |
| Superadmin Journey | Multi-branch: create branch, deploy staff, cross-branch reports |

### Flows (Feature-Specific)

| Doc | What It Proves |
|-----|---------------|
| Extension Cycle | 72hrs, 10 extensions, 3 complete cycles, split charges |
| Payment Flow | 9 scenarios: cash, deposit, pay all, overpay, checkout |
| Room Transfer | Upgrade, downgrade, same-price, override |
| Shift Lifecycle | Dual frontdesk, remittance, expense, reconciliation |
| Discount Flow | Senior verify, fixed promo, cap, branch disabled |
| Void/Override | Wrong price, duplicate, void paid charge, denied auth |
| Inventory Stock | Create item, restock, order deduction, spoilage, void restore |
| Kiosk Device Setup | Register, connect, heartbeat, deactivate, replace |
| Multi-Branch Staff | Deploy, branch select, work at deployed branch, return |

---

## Documentation Index

```
docs/v4-project/
├── README.md                              ← This file
├── HOTEL_V4_COMPLETE_SCHEMA.md            ← Full schema (35 tables)
├── V4_DESIGN_DECISIONS.md                 ← 14 business rules
├── V4_REPORT_VERIFICATION.md              ← 14 reports with SQL
├── V4_BRAND_AND_THEME.md                  ← Colors, fonts, assets
├── V4_TECH_STACK.md                       ← Packages, architecture
├── v3-problems/
│   └── V3_PROBLEMS_SOLVED.md              ← 28 issues fixed
├── assets/                                ← Logos, backgrounds (9 files)
├── reference/                             ← Architecture patterns
│   ├── bootstrap/app.php                  ← Laravel 12 bootstrap pattern
│   ├── api/                               ← API route + controller + resource patterns
│   ├── traits/                            ← Scopes, relations, API response
│   ├── concerns/                          ← Validation rules
│   ├── enums/                             ← PHP enum patterns
│   └── skills/                            ← Claude skills (Livewire, Flux, Pest, Tailwind)
└── examples/
    ├── journeys/ (7 docs)                 ← Role-based walkthroughs
    └── flows/ (9 docs)                    ← Feature-specific examples
```

---

## Getting Started (When Building)

1. Create fresh Laravel 12/13 project
2. Install packages (Filament 5, Spatie packages, Livewire 4)
3. Configure bootstrap/app.php (Laravel 12 style, no Kernel.php)
4. Set up Enums (RoomStatus, TransactionType, ShiftPeriod, etc.)
5. Set up Traits (ScopesToBranch, ModelRelations, ApiResponse)
6. Write migrations from `HOTEL_V4_COMPLETE_SCHEMA.md`
7. Create Models with relation traits and Spatie LogsActivity
8. Create Services (CheckInService, PaymentService, etc.)
9. Build Filament Admin panel (rooms, rates, users, discounts)
10. Build Livewire Frontdesk (room monitoring, guest transactions)
11. Build Kiosk API (Sanctum auth, check-in/out endpoints)
12. Build Roomboy dashboard (cleaning tasks, floor assignments)
13. Build Reports (all computed queries, no report tables)
14. Test everything against the example scenarios
