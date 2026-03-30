# V4 Superadmin Journey — Multi-Branch Management

A day as superadmin: manage branches, deploy staff, monitor all locations, cross-branch reports.

---

## Setup

```
System: Alma Hotel Chain
Superadmin: Boss Alma
Branches:
  Branch A (Cebu) — 25 rooms, 10 staff
  Branch B (Manila) — 30 rooms, 12 staff
  Branch C (Davao) — 15 rooms, 6 staff (NEW)
```

---

## 8:00 AM — Boss Alma Logs In

```
Login → role = 'superadmin' → redirect to superadmin dashboard

SUPERADMIN DASHBOARD:
┌──────────────────────────────────────────────────────────────────┐
│  ALMA HOTEL CHAIN — Superadmin Dashboard                         │
│                                                                  │
│  BRANCHES OVERVIEW:                                              │
│  ┌──────────┬──────────┬────────┬──────────┬─────────────────┐  │
│  │ Branch   │ Rooms    │ Staff  │ Occupied │ Today's Sales   │  │
│  ├──────────┼──────────┼────────┼──────────┼─────────────────┤  │
│  │ Cebu (A) │ 25       │ 10     │ 12 (48%) │ ₱15,500        │  │
│  │ Manila(B)│ 30       │ 12     │ 22 (73%) │ ₱28,200        │  │
│  │ Davao (C)│ 15       │ 6      │ 0  (0%)  │ ₱0 (new!)      │  │
│  └──────────┴──────────┴────────┴──────────┴─────────────────┘  │
│                                                                  │
│  MENU:                                                          │
│  [Branches] [Users] [Reports] [Activity Logs] [Settings]        │
└──────────────────────────────────────────────────────────────────┘

Queries:
  -- Per-branch room status
  SELECT b.name,
    COUNT(*) as total_rooms,
    SUM(CASE WHEN r.status='occupied' THEN 1 ELSE 0 END) as occupied
  FROM branches b
  JOIN rooms r ON r.branch_id = b.id
  WHERE b.is_active = true
  GROUP BY b.id

  -- Per-branch today's sales
  SELECT b.name, COALESCE(SUM(t.amount), 0) as sales
  FROM branches b
  LEFT JOIN transactions t ON t.branch_id = b.id
    AND t.type IN ('room_charge','extension','food','amenity','damage','transfer_fee')
    AND DATE(t.created_at) = CURDATE()
  GROUP BY b.id
```

---

## 8:30 AM — Set Up New Branch (Davao)

Branch C was created but needs configuration.

### Configure Branch Settings

```
Superadmin → Branches → Davao → Settings

branch_settings (branch_id=3):
  initial_deposit: 150.00       ← Davao uses lower deposit
  kiosk_time_limit: 15          ← longer kiosk timeout
  extension_cycle_threshold: 12 ← 12hr cycle (not 24)
  cleaning_min_minutes: 15
  cleaning_deadline_hours: 3
  stale_shift_hours: 14
  shift_am_start: 6             ← Davao shifts start earlier
  shift_pm_start: 18
  authorization_code: "DAV2026"
  discounts_enabled: true

activity_log (Spatie auto):
  log='admin', subject=BranchSettings #3
```

### Add Floors

```
floors:
  branch_id=3, floor_number=1
  branch_id=3, floor_number=2
```

### Add Room Types

```
room_types:
  branch_id=3, name="Standard"
  branch_id=3, name="Deluxe"
```

### Add Rooms

```
rooms (15 rooms):
  branch_id=3, floor_id=5 (F1), room_type_id=5 (Standard), room_number=101...
  branch_id=3, floor_id=6 (F2), room_type_id=6 (Deluxe), room_number=201...
```

### Set Rates

```
rates:
  Room 101, 6hrs = ₱400   ← Davao has lower rates
  Room 101, 12hrs = ₱650
  Room 201, 6hrs = ₱600
  Room 201, 12hrs = ₱900
  ... (bulk copy for similar rooms)

extension_rates:
  Room 101, 1hr = ₱80
  Room 101, 3hrs = ₱200
  ... per room
```

### Add Staying Hours and Extension Hours

```
staying_hours: branch_id=3, hours=6, 12, 18, 24
extension_hours: branch_id=3, hours=1, 2, 3, 6
```

---

## 9:00 AM — Create Staff for Davao

### Create Admin for Branch C

```
users: email="admin.davao@alma.com", is_active=true
user_profiles: first_name="Ana", last_name="Davao"
branch_user: user_id=XX, branch_id=3, assignment_type='primary'
model_has_roles: role_id=2 (admin)
```

### Create Frontdesk Staff

```
users: email="fd1.davao@alma.com"
user_profiles: first_name="Maria", last_name="Davao", passcode="1234"
branch_user: user_id=XX, branch_id=3, assignment_type='primary'
model_has_roles: role_id=3 (frontdesk)
```

### Create Roomboy

```
users: email="rb1.davao@alma.com"
user_profiles: first_name="Pedro", last_name="Davao"
branch_user: user_id=XX, branch_id=3, assignment_type='primary'
model_has_roles: role_id=5 (roomboy)
floor_user: user_id=XX, floor_id=5 (F1), floor_id=6 (F2)
```

### Create Cash Drawers

```
cash_drawers:
  branch_id=3, name="Drawer 1"
  branch_id=3, name="Drawer 2"
```

### Register Kiosk

```
kiosk_terminals:
  branch_id=3, name="Lobby Kiosk", device_token=(generated)
  → Code: KSK-DAV001
```

**Branch C is now fully operational.**

---

## 10:00 AM — Deploy Staff Across Branches

Manila branch is busy. Deploy Cebu frontdesk to help.

```
Superadmin → Users → "Juan Cruz" (Cebu frontdesk) → Deploy to Manila

branch_user:
  Existing: user_id=Juan, branch_id=1 (Cebu), assignment_type='primary'
  New:      user_id=Juan, branch_id=2 (Manila), assignment_type='deployed'

Juan can now log in and work at BOTH branches.
When Juan logs in at Manila, system uses branch_id=2 for all operations.

activity_log: log='admin', description='User deployed to Manila branch'
```

---

## 11:00 AM — Cross-Branch Sales Report

```
Superadmin → Reports → Sales → All Branches → This Week

┌──────────────────────────────────────────────────────────────┐
│  CROSS-BRANCH SALES REPORT — March 24-30, 2026               │
├──────────┬────────────┬──────────┬──────────┬────────────────┤
│ Branch   │ Room Sales │ Food     │Extension │ Total Revenue  │
├──────────┼────────────┼──────────┼──────────┼────────────────┤
│ Cebu (A) │ ₱85,000    │ ₱12,500  │ ₱8,200   │ ₱105,700     │
│ Manila(B)│ ₱142,000   │ ₱28,000  │ ₱15,500  │ ₱185,500     │
│ Davao (C)│ ₱0         │ ₱0       │ ₱0       │ ₱0 (new)     │
├──────────┼────────────┼──────────┼──────────┼────────────────┤
│ TOTAL    │ ₱227,000   │ ₱40,500  │ ₱23,700  │ ₱291,200     │
└──────────┴────────────┴──────────┴──────────┴────────────────┘

Query:
  SELECT b.name,
    SUM(CASE WHEN t.type='room_charge' THEN t.amount ELSE 0 END) as room_sales,
    SUM(CASE WHEN t.type='food' THEN t.amount ELSE 0 END) as food_sales,
    SUM(CASE WHEN t.type='extension' THEN t.amount ELSE 0 END) as ext_sales,
    SUM(t.amount) as total
  FROM transactions t
  JOIN branches b ON b.id = t.branch_id
  WHERE t.type IN ('room_charge','extension','food','amenity','damage','transfer_fee')
  AND t.created_at BETWEEN '2026-03-24' AND '2026-03-30 23:59:59'
  GROUP BY b.id
```

---

## 12:00 PM — Cross-Branch Discount Report

```
Superadmin → Reports → Discount Usage → All Branches → This Month

┌─────────────────────────────────────────────────────────────────┐
│  DISCOUNT REPORT — March 2026                                    │
├──────────┬──────────────────┬───────┬────────────────────────────┤
│ Branch   │ Discount         │ Count │ Total Discounted           │
├──────────┼──────────────────┼───────┼────────────────────────────┤
│ Cebu     │ Senior Citizen   │ 45    │ ₱9,000                    │
│ Cebu     │ PWD              │ 12    │ ₱1,800                    │
│ Cebu     │ Summer Promo     │ 30    │ ₱3,000                    │
│ Manila   │ Senior Citizen   │ 89    │ ₱17,800                   │
│ Manila   │ PWD              │ 25    │ ₱3,750                    │
│ Manila   │ Summer Promo     │ 55    │ ₱5,500                    │
├──────────┼──────────────────┼───────┼────────────────────────────┤
│ TOTAL    │                  │ 256   │ ₱40,850                   │
└──────────┴──────────────────┴───────┴────────────────────────────┘

Query:
  SELECT b.name, ad.snapshot_discount_name,
    COUNT(*) as times_used,
    SUM(ad.discount_amount) as total_discounted
  FROM applied_discounts ad
  JOIN stays s ON s.id = ad.discountable_id AND ad.discountable_type = 'stay'
  JOIN branches b ON b.id = s.branch_id
  WHERE ad.created_at BETWEEN '2026-03-01' AND '2026-03-31 23:59:59'
  GROUP BY b.id, ad.snapshot_discount_name
```

---

## 1:00 PM — Cross-Branch Shift Shortage Report

```
Superadmin → Reports → Shift Shortages → All Branches → This Week

┌─────────────────────────────────────────────────────────────────┐
│  SHIFT SHORTAGE REPORT — This Week                               │
├──────────┬──────────────┬──────────┬─────────┬──────────────────┤
│ Branch   │ Staff        │ Date     │ Period  │ Shortage         │
├──────────┼──────────────┼──────────┼─────────┼──────────────────┤
│ Cebu     │ Juan Cruz    │ Mar 25   │ PM      │ -₱50.00          │
│ Manila   │ Ana Santos   │ Mar 27   │ AM      │ -₱150.00         │
│ Manila   │ Pedro Reyes  │ Mar 28   │ PM      │ -₱25.00          │
├──────────┼──────────────┼──────────┼─────────┼──────────────────┤
│ TOTAL    │              │          │         │ -₱225.00         │
└──────────┴──────────────┴──────────┴─────────┴──────────────────┘

Query:
  SELECT b.name, up.first_name, up.last_name,
    DATE(s.ended_at) as shift_date,
    s.shift_period, s.difference
  FROM shifts s
  JOIN branches b ON b.id = s.branch_id
  JOIN user_profiles up ON up.user_id = s.user_id
  WHERE s.difference < 0
  AND s.status = 'closed'
  AND s.ended_at BETWEEN :week_start AND :week_end
  ORDER BY s.difference ASC
```

---

## 2:00 PM — Cross-Branch Activity Logs

```
Superadmin → Activity Logs → All Branches → Filter: "Rate updated"

┌──────────────────────────────────────────────────────────────────┐
│  ACTIVITY LOGS — Rate Changes — All Branches                     │
├──────────┬──────────┬──────────────┬─────────────────────────────┤
│  Date    │  Branch  │  User        │  Change                     │
├──────────┼──────────┼──────────────┼─────────────────────────────┤
│  Mar 28  │  Cebu    │  Ate Joy     │  Room 101: ₱500 → ₱550     │
│  Mar 29  │  Manila  │  Admin Marco │  Room 301: ₱800 → ₱750     │
│  Mar 30  │  Cebu    │  Ate Joy     │  Room 110: Created (₱700)  │
└──────────┴──────────┴──────────────┴─────────────────────────────┘

Spatie Activity Log query with branch filter.
Full before/after values available for every change.
```

---

## 3:00 PM — Void Audit Report

```
Superadmin → Reports → Void Audit → All Branches

┌──────────────────────────────────────────────────────────────────┐
│  VOID AUDIT — This Month                                         │
├──────────┬──────────┬──────────────┬───────────┬────────────────┤
│  Date    │  Branch  │  Voided By   │  Amount   │  Reason        │
├──────────┼──────────┼──────────────┼───────────┼────────────────┤
│  Mar 15  │  Manila  │  Admin Marco │  ₱300     │  Wrong price   │
│  Mar 22  │  Cebu    │  Ate Joy     │  ₱150     │  Duplicate     │
├──────────┼──────────┼──────────────┼───────────┼────────────────┤
│  TOTAL   │          │              │  ₱450     │                │
└──────────┴──────────┴──────────────┴───────────┴────────────────┘

Query:
  SELECT t.created_at, b.name, up.first_name,
    t.amount, t.description
  FROM transactions t
  JOIN branches b ON b.id = t.branch_id
  JOIN user_profiles up ON up.user_id = t.created_by
  WHERE t.type = 'void'
  ORDER BY t.created_at DESC
```

---

## 4:00 PM — Cleaning Performance Across Branches

```
Superadmin → Reports → Cleaning Performance → All Branches

┌──────────────────────────────────────────────────────────────────┐
│  CLEANING PERFORMANCE — This Week                                │
├──────────┬──────────────┬────────┬──────────┬─────────┬─────────┤
│  Branch  │  Roomboy     │ Cleaned│ Avg Min  │ Delayed │Override │
├──────────┼──────────────┼────────┼──────────┼─────────┼─────────┤
│  Cebu    │  Pedro S.    │ 35     │ 18 min   │ 2       │ 1      │
│  Cebu    │  Ana M.      │ 28     │ 22 min   │ 5       │ 0      │
│  Manila  │  Juan R.     │ 42     │ 16 min   │ 0       │ 0      │
│  Manila  │  Maria T.    │ 38     │ 19 min   │ 3       │ 2      │
├──────────┼──────────────┼────────┼──────────┼─────────┼─────────┤
│  TOTAL   │              │ 143    │ 18.5 min │ 10      │ 3      │
└──────────┴──────────────┴────────┴──────────┴─────────┴─────────┘
```

---

## 5:00 PM — Deactivate a Branch

Branch C (Davao) temporarily closing for renovation.

```
Superadmin → Branches → Davao → Deactivate

branches (id=3): is_active → false

Staff at Davao can no longer log in (branch inactive).
All data preserved. Reactivate when renovation complete.

activity_log: log='admin', subject=Branch #3
  properties: {old: {is_active: true}, attributes: {is_active: false}}
```

---

## Superadmin's Unique Powers

| Action | Admin Can? | Superadmin Can? |
|--------|-----------|----------------|
| Create branches | No | Yes |
| Deactivate branches | No | Yes |
| Deploy staff across branches | No | Yes |
| View ALL branch data | No (own branch only) | Yes |
| Cross-branch reports | No | Yes |
| Cross-branch activity logs | No | Yes |
| Create branch admins | No | Yes |
| Manage branch settings | Own branch | Any branch |

---

## Tables Superadmin Touches

| Table | Action |
|-------|--------|
| `branches` | Create, activate, deactivate |
| `branch_settings` | Configure per branch |
| `users` + `user_profiles` | Create staff for any branch |
| `branch_user` | Assign/deploy staff to branches |
| `rooms`, `room_types`, `floors` | Setup for new branches |
| `rates`, `extension_rates` | Set pricing for any branch |
| `staying_hours`, `extension_hours` | Configure per branch |
| `discounts` | Create for any branch |
| `cash_drawers` | Setup for new branches |
| `kiosk_terminals` | Register for any branch |
| `floor_user` | Assign roomboys at any branch |
| Activity logs | View all branches |
| All report queries | Filter by any/all branches |
