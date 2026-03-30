# V4 Admin Journey — Branch Management

A full day as a branch admin: configure rooms, rates, staff, discounts, monitor operations, view reports.

---

## Setup

```
Branch: Alma Hotel Branch A
Admin: Ate Joy (role: admin)
```

---

## 8:00 AM — Ate Joy Logs In

```
Login → role = 'admin' → redirect to admin dashboard

ADMIN DASHBOARD:
┌──────────────────────────────────────────────────────┐
│  ALMA HOTEL — Branch A                                │
│  Admin: Ate Joy                                       │
│                                                      │
│  TODAY'S STATUS:                                      │
│  Rooms: 15 available │ 8 occupied │ 2 cleaning        │
│  Active shifts: Maria (Drawer 1), Juan (Drawer 2)     │
│  Kiosks: 2 online, 1 offline                          │
│                                                      │
│  MENU:                                                │
│  [Rooms] [Rates] [Users] [Discounts] [Settings]      │
│  [Reports] [Activity Logs] [Kiosk Mgmt]              │
└──────────────────────────────────────────────────────┘
```

---

## 8:15 AM — Configure a New Room

Branch just finished renovating Room 110.

```
Admin → Rooms → Add Room

ENTERS:
  Room number: 110
  Floor: Floor 1
  Room type: Deluxe
  Bed type: queen
  Area: Main

rooms:
  id: 25
  branch_id: 1
  floor_id: 1
  room_type_id: 2 (Deluxe)
  room_number: 110
  bed_type: 'queen'
  area: 'Main'
  status: 'available'
  is_priority: false

activity_log (Spatie auto):
  log='admin', event='created', subject=Room #25
  properties: {attributes: {room_number: 110, bed_type: 'queen', ...}}
```

---

## 8:20 AM — Set Rates for New Room

```
Admin → Rates → Room 110

Sets rates:
  6hrs  = ₱700
  12hrs = ₱1,000
  18hrs = ₱1,300
  24hrs = ₱1,600

rates (4 records created):
  room_id=25, staying_hour_id=1 (6hrs),  amount=700.00
  room_id=25, staying_hour_id=2 (12hrs), amount=1,000.00
  room_id=25, staying_hour_id=3 (18hrs), amount=1,300.00
  room_id=25, staying_hour_id=4 (24hrs), amount=1,600.00

Also sets extension rates:
  room_id=25, extension_hour_id=1 (1hr),  amount=120.00
  room_id=25, extension_hour_id=2 (3hrs), amount=300.00
  room_id=25, extension_hour_id=3 (6hrs), amount=500.00

BULK COPY option: "Copy rates from Room 101 to Room 110"
  → copies all rate records, changing room_id

activity_log (Spatie auto):
  log='admin', event='created', subject=Rate #XX
  properties: {attributes: {room_id: 25, amount: 700.00, ...}}
```

---

## 9:00 AM — Update an Existing Rate

Standard rooms need a price increase. Room 101 goes from ₱500 to ₱550 for 6hrs.

```
Admin → Rates → Room 101 → Edit 6hrs

rates: amount 500.00 → 550.00

activity_log (Spatie auto):
  log='admin', event='updated', subject=Rate #5
  properties:
    old: {amount: 500.00}
    attributes: {amount: 550.00}

Note: Existing stays still show ₱500 (snapshot). Only NEW check-ins use ₱550.
```

---

## 9:30 AM — Create a New Staff Member

New roomboy hired.

```
Admin → Users → Add User

ENTERS:
  Email: pedro.new@alma.com
  Password: (temporary)
  Role: roomboy

users:
  id: 50
  email: pedro.new@alma.com
  is_active: true

user_profiles:
  user_id: 50
  first_name: "Pedro"
  last_name: "Bagong"
  contact_number: "09189876543"
  sex: "male"

branch_user:
  user_id: 50
  branch_id: 1
  assignment_type: 'primary'

model_has_roles:
  model_id: 50, role_id: 5 (roomboy)

activity_log (Spatie auto):
  log='admin', event='created', subject=User #50
```

---

## 9:35 AM — Assign Roomboy to Floors

```
Admin → Roomboy Designation → Pedro Bagong → Manage

Assigns: Floor 1, Floor 2

floor_user:
  user_id=50, floor_id=1
  user_id=50, floor_id=2

activity_log: log='admin', description='Updated roomboy floor assignment'
```

---

## 10:00 AM — Create a New Discount

Promo for summer season.

```
Admin → Discounts → Add Discount

ENTERS:
  Name: "Summer Promo 2026"
  Type: fixed
  Value: ₱100
  Requires verification: false
  Is active: true

discounts:
  branch_id: 1
  name: "Summer Promo 2026"
  discount_type: 'fixed'
  discount_value: 100.00
  max_discount: NULL
  requires_verification: false
  is_active: true

activity_log (Spatie auto):
  log='admin', event='created', subject=Discount #5
```

---

## 10:30 AM — Update Branch Settings

Change deposit amount and cleaning time.

```
Admin → Settings

Changes:
  initial_deposit: 200 → 250
  cleaning_min_minutes: 15 → 20
  shift_am_start: 8 → 7 (earlier shift start)

branch_settings:
  initial_deposit: 250.00
  cleaning_min_minutes: 20
  shift_am_start: 7

activity_log (Spatie auto):
  log='admin', event='updated', subject=BranchSettings #1
  properties:
    old: {initial_deposit: 200.00, cleaning_min_minutes: 15, shift_am_start: 8}
    attributes: {initial_deposit: 250.00, cleaning_min_minutes: 20, shift_am_start: 7}

All new check-ins now collect ₱250 deposit.
All new cleaning tasks now require 20 minutes.
AM shift determination now uses 7 AM cutoff.
```

---

## 11:00 AM — Manage Menu Items

Add new food item to kitchen.

```
Admin → Menu → Kitchen → Add Item

Category: "Main Course"
Name: "Sinigang"
Price: ₱180
Item code: "KC-015"

menu_items:
  branch_id: 1
  menu_category_id: 3 (Main Course, service_area='kitchen')
  name: "Sinigang"
  price: 180.00
  item_code: "KC-015"
  is_active: true

menu_inventories:
  menu_item_id: (new), stock: 0

Admin adds initial stock:
  menu_stock_logs:
    type: 'stock_in', quantity: 30
    stock_before: 0, stock_after: 30
    reason: 'manual_add', created_by: Ate Joy

  menu_inventories: stock → 30

activity_log (Spatie auto):
  log='admin', event='created', subject=MenuItem #XX
```

---

## 2:00 PM — Register New Kiosk Device

New tablet arrived for the lobby.

```
Admin → Kiosk Management → Register New Kiosk

ENTERS: Name: "Lobby Center"

kiosk_terminals:
  branch_id: 1
  name: "Lobby Center"
  device_token: (generated Sanctum token, hashed)
  is_active: true

System shows code: "KSK-G7H8I9"
Admin goes to tablet → enters code → tablet connected.

kiosk_terminals: last_heartbeat_at updated every 2 minutes
```

---

## 3:00 PM — Deactivate a Staff Member

Old frontdesk staff resigned.

```
Admin → Users → Ana Cruz → Deactivate

users (Ana): is_active → false

Ana can no longer log in. Existing records (transactions, stays) still reference her.
No data deleted. Just blocked from login.

activity_log (Spatie auto):
  log='admin', event='updated', subject=User #15
  properties: {old: {is_active: true}, attributes: {is_active: false}}
```

---

## 4:00 PM — View Activity Logs

Admin wants to see what happened today.

```
Admin → Activity Logs

┌──────────────────────────────────────────────────────────────────┐
│  ACTIVITY LOGS — Branch A — March 30, 2026                       │
├──────────┬──────────────┬────────────────────────────────────────┤
│  Time    │  User        │  Activity                              │
├──────────┼──────────────┼────────────────────────────────────────┤
│  8:00 AM │  Maria       │  Shift started (Drawer 1, ₱1,200)     │
│  8:05 AM │  Juan        │  Shift started (Drawer 2, ₱500)       │
│  8:15 AM │  Ate Joy     │  Room created: #110                    │
│  8:20 AM │  Ate Joy     │  Rate created: Room 110 6hrs ₱700     │
│  9:00 AM │  Ate Joy     │  Rate updated: Room 101 ₱500→₱550     │
│  8:15 AM │  Maria       │  Guest checked in: Juan, Room 101     │
│  8:30 AM │  Maria       │  Guest checked in: Ana (Senior), 205  │
│  9:30 AM │  Ate Joy     │  User created: Pedro Bagong (roomboy) │
│  10:00 AM│  Ate Joy     │  Discount created: Summer Promo 2026  │
│  10:30 AM│  Ate Joy     │  Settings updated: deposit ₱200→₱250  │
│  ...     │  ...         │  ...                                   │
└──────────┴──────────────┴────────────────────────────────────────┘

Filters: [Date range] [User] [Activity type]
Admin sees all branch activity. Full transparency.
```

---

## 5:00 PM — View Reports

```
Admin → Reports → Sales Report (today)

Shows: all transactions grouped by type, gross/net sales, expenses.
(See V4_REPORT_VERIFICATION.md for exact queries)

Admin → Reports → Shift Reconciliation (Maria's completed shift)

Shows: opening cash, payments, deposits, remittances, expenses, expected vs actual.
```

---

## Admin's Day Summary

| Action | What Changed |
|--------|-------------|
| Created Room 110 | rooms + rates + extension_rates |
| Updated Room 101 rate | rates (₱500→₱550), Spatie logged old/new |
| Created roomboy Pedro | users + user_profiles + branch_user + floor_user + role |
| Created Summer Promo discount | discounts |
| Updated branch settings | branch_settings (deposit, cleaning time, shift start) |
| Added Sinigang to kitchen menu | menu_items + menu_inventories + menu_stock_logs |
| Registered new kiosk | kiosk_terminals |
| Deactivated old staff | users.is_active = false |
| Viewed logs & reports | Read-only queries |

**Every change automatically logged by Spatie with before/after values.**
