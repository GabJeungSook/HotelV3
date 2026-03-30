# V4 Roomboy Journey — Complete Day

A full day as a roomboy: login, see assigned floors, claim rooms, clean, finish, handle override, view history.

---

## Setup

```
Branch: Alma Hotel Branch A
Roomboy: Pedro Santos
Assigned Floors: Floor 1 (primary), Floor 2
Settings:
  cleaning_min_minutes: 15
  cleaning_deadline_hours: 3
```

---

## 7:00 AM — Pedro Logs In

```
Pedro enters email + password → authenticated
System checks: role = 'roomboy' → redirect to roomboy dashboard

Pedro sees his dashboard:
┌─────────────────────────────────────────────────┐
│  ROOMBOY: Pedro Santos                           │
│  Status: Not Cleaning                            │
│                                                  │
│  [Floor 1] [Floor 2]   ← tabs (from floor_user) │
│                                                  │
│  FLOOR 1 — UNCLEANED ROOMS:                      │
│  ┌──────────────────────────────────────────┐   │
│  │ Room 101 │ Checkout: 6:30 AM             │   │
│  │          │ Deadline: 9:30 AM (2hrs left)  │   │
│  │          │ [ START CLEANING ]             │   │
│  ├──────────────────────────────────────────┤   │
│  │ Room 105 │ Checkout: 7:00 AM             │   │
│  │          │ Deadline: 10:00 AM (3hrs left) │   │
│  │          │ (waiting)                      │   │
│  └──────────────────────────────────────────┘   │
│                                                  │
│  FLOOR 2 — UNCLEANED ROOMS:                      │
│  ┌──────────────────────────────────────────┐   │
│  │ Room 201 │ Checkout: 5:00 AM             │   │
│  │          │ Deadline: 8:00 AM (1hr left!)  │   │
│  │          │ [ START CLEANING ]             │   │
│  └──────────────────────────────────────────┘   │
└─────────────────────────────────────────────────┘

Data source:
  SELECT * FROM cleaning_tasks
  WHERE status = 'pending'
  AND room_id IN (SELECT id FROM rooms WHERE floor_id IN (Pedro's floors))
  ORDER BY deadline_at ASC
```

---

## 7:05 AM — Pedro Starts Cleaning Room 201 (Most Urgent)

Room 201 deadline is 8:00 AM — most urgent.

```
Pedro clicks "Start Cleaning" on Room 201.

System checks: Pedro already cleaning another room?
  → SELECT * FROM cleaning_tasks WHERE assigned_to = Pedro AND status = 'in_progress'
  → No → proceed

cleaning_tasks (id=50):
  room_id: 201
  stay_id: 498 (guest who checked out)
  assigned_to: Pedro          ← was NULL, now Pedro
  started_at: 7:05 AM         ← was NULL, now set
  status: 'pending' → 'in_progress'
  is_on_assigned_floor: false  ← Floor 2, Pedro's primary is Floor 1

rooms:
  Room 201: status → 'cleaning'

Pedro's dashboard now shows:
┌─────────────────────────────────────────────────┐
│  Status: CLEANING Room 201 (Floor 2)             │
│  Started: 7:05 AM                                │
│  Minimum time: 15 minutes (until 7:20 AM)        │
│                                                  │
│  [ FINISH CLEANING ]  (disabled until 7:20 AM)   │
└─────────────────────────────────────────────────┘

Pedro cannot start another room until he finishes this one.
```

---

## 7:10 AM — Pedro Tries to Finish Early

```
Pedro clicks "Finish Cleaning" at 7:10 AM.

System checks: duration >= cleaning_min_minutes (15)?
  7:10 - 7:05 = 5 minutes → NO, only 5 minutes!

ERROR: "You need to clean for at least 15 minutes"

Pedro must wait.
```

---

## 7:25 AM — Pedro Finishes Room 201

```
Pedro clicks "Finish Cleaning" at 7:25 AM.

System checks: 7:25 - 7:05 = 20 minutes ≥ 15 → OK ✓

cleaning_tasks (id=50):
  completed_at: 7:25 AM
  duration_minutes: 20
  is_delayed: false          ← 7:25 AM < 8:00 AM deadline ✓
  status: 'in_progress' → 'completed'

rooms:
  Room 201: status → 'available'
  Room 201: is_priority → true  ← freshly cleaned, ready for next guest

Pedro's dashboard: Status → "Not Cleaning"

activity_log: log='housekeeping', description='Cleaning completed Room 201'
```

---

## 7:30 AM — Pedro Starts Room 101

```
cleaning_tasks (id=48):
  assigned_to: Pedro
  started_at: 7:30 AM
  status: 'in_progress'
  is_on_assigned_floor: true  ← Floor 1 = Pedro's primary floor ✓

rooms: Room 101 → 'cleaning'
```

---

## 7:50 AM — Pedro Finishes Room 101

```
7:50 - 7:30 = 20 minutes ≥ 15 → OK ✓

cleaning_tasks (id=48):
  completed_at: 7:50 AM
  duration_minutes: 20
  is_delayed: false   ← 7:50 < 9:30 AM deadline ✓
  status: 'completed'

rooms: Room 101 → 'available', is_priority → true
```

---

## 7:55 AM — Pedro Starts Room 105

```
cleaning_tasks (id=49):
  assigned_to: Pedro
  started_at: 7:55 AM
  status: 'in_progress'
  is_on_assigned_floor: true

rooms: Room 105 → 'cleaning'
```

---

## 8:05 AM — Emergency: Guest Waiting for Room 105

Admin calls Pedro: "Guest is waiting, just do a quick clean."
Room only needs light cleaning but Pedro started 10 minutes ago (< 15 min).

```
Pedro clicks "Finish Cleaning" → blocked (only 10 minutes).

OVERRIDE FLOW:
  Pedro clicks "Request Override"
  Enters authorization code: "ALMA2026"
  Enters reason: "Guest waiting, light clean only"

  System validates code against branch_settings.authorization_code → MATCH ✓

cleaning_tasks (id=49):
  completed_at: 8:05 AM
  duration_minutes: 10         ← under 15, allowed by override
  is_delayed: false
  overridden_by: Admin         ← who authorized
  override_reason: "Guest waiting, light clean only"
  status: 'completed'

rooms: Room 105 → 'available', is_priority → true

activity_log: log='housekeeping', description='Cleaning override Room 105'
  properties: {reason: "Guest waiting, light clean only", auth_by: Admin, duration: 10}
```

---

## 10:00 AM — More Rooms Come In

Guests check out → cleaning tasks auto-created.

```
cleaning_tasks:
  Room 301 (Floor 3) — NOT Pedro's floor, he won't see it
  Room 102 (Floor 1) — Pedro's floor, shows in his dashboard
  Room 203 (Floor 2) — Pedro's floor, shows in his dashboard
```

Pedro continues cleaning throughout the day.

---

## 2:00 PM — A Room Goes Past Deadline

Room 203 checkout was 11:00 AM, deadline 2:00 PM. Pedro was busy.

```
Pedro starts at 2:15 PM, finishes at 2:35 PM.

cleaning_tasks:
  deadline_at: 2:00 PM
  completed_at: 2:35 PM
  is_delayed: TRUE  ← completed AFTER deadline!
  duration_minutes: 20

Dashboard shows Room 203 with red "OVERDUE" indicator before Pedro starts.
```

---

## 5:00 PM — Pedro Views Cleaning History

```
Pedro navigates to Cleaning History page.

┌──────────────────────────────────────────────────────────────────┐
│  CLEANING HISTORY — Pedro Santos — March 30, 2026                │
├──────┬──────┬──────────┬──────────┬─────────┬─────────┬─────────┤
│ Room │ Floor│ Started  │ Finished │ Minutes │ Delayed │ Override│
├──────┼──────┼──────────┼──────────┼─────────┼─────────┼─────────┤
│ 201  │  2   │ 7:05 AM  │ 7:25 AM  │   20    │   No    │   No   │
│ 101  │  1   │ 7:30 AM  │ 7:50 AM  │   20    │   No    │   No   │
│ 105  │  1   │ 7:55 AM  │ 8:05 AM  │   10    │   No    │  Yes*  │
│ 102  │  1   │ 10:30 AM │ 10:50 AM │   20    │   No    │   No   │
│ 203  │  2   │ 2:15 PM  │ 2:35 PM  │   20    │  Yes!   │   No   │
├──────┴──────┴──────────┴──────────┴─────────┴─────────┴─────────┤
│ Total: 5 rooms │ Avg: 18 min │ Delayed: 1 │ Overrides: 1       │
└──────────────────────────────────────────────────────────────────┘

* Override by Admin: "Guest waiting, light clean only"

Query:
  SELECT * FROM cleaning_tasks
  WHERE assigned_to = Pedro AND status = 'completed'
  AND DATE(completed_at) = '2026-03-30'
  ORDER BY started_at
```

---

## Pedro's Day Summary

| Metric | Value |
|--------|-------|
| Rooms cleaned | 5 |
| Average time | 18 minutes |
| Fastest | 10 min (Room 105, override) |
| Slowest | 20 min |
| Delayed | 1 (Room 203) |
| Overrides | 1 (Room 105) |
| Floors covered | 2 (Floor 1 primary, Floor 2 secondary) |
| On assigned floor | 3 of 5 (Floor 1) |

---

## Tables Touched

| Table | Purpose |
|-------|---------|
| `cleaning_tasks` | All 5 cleaning records |
| `rooms` | Status changes (uncleaned → cleaning → available) |
| `floor_user` | Pedro's floor assignments |
| `activity_log` | Cleaning events logged |
| `branch_settings` | cleaning_min_minutes, cleaning_deadline_hours |
