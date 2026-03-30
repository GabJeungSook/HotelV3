# V4 Extension Cycle — Complete Example

Demonstrates how the extension cycle reset works across unlimited extensions over 72+ hours.

---

## Setup

```
Branch Settings:
  extension_cycle_threshold = 24 hours

Room 101 (Standard):
  Original rate:  6hrs = ₱500
  Extension rate: 6hrs = ₱250
```

## Schema Fields Involved

| Table | Column | Purpose |
|-------|--------|---------|
| `branch_settings` | `extension_cycle_threshold` | Hours before reset (24) |
| `stays` | `cycle_hours` | Position in current cycle (0-24) |
| `stays` | `charge_original_rate_next` | Flag: next extension uses original rate |
| `stay_extensions` | `charge_type` | 'extension' / 'original' / 'split' |
| `stay_extensions` | `cycle_hours_before` | Audit: position before |
| `stay_extensions` | `cycle_hours_after` | Audit: position after |
| `rates` | `room_id` + `staying_hour_id` | Original rate lookup |
| `extension_rates` | `room_id` + `extension_hour_id` | Extension rate lookup |

---

## Full Guest Journey: 72 Hours, 10 Extensions

### Check-In (10:00 AM)

```
Guest books 6 hours at ₱500
Expected checkout: 4:00 PM

stays:
  cycle_hours: 6
  charge_original_rate_next: false
  initial_amount: 500.00

transactions:
  #1  room_charge  500.00  "Room Charge 6hrs Room #101"
  #2  payment      500.00  linked_transaction_id=#1
  #3  deposit_in   200.00  "Deposit (Key & Remote)"
```

---

### CYCLE 1 (Hours 0-24)

**Extension #1 — 4:00 PM (+6hrs)**

```
cycle: 6 + 6 = 12 (under 24, normal)
Charge: ₱250 (extension rate)

stays: cycle_hours=12, flag=false

stay_extensions:
  hours=6, amount=250.00, charge_type='extension'
  cycle_hours_before=6, cycle_hours_after=12

transactions:
  #4  extension  250.00  "Extension 6hrs"
```

**Extension #2 — 10:00 PM (+6hrs)**

```
cycle: 12 + 6 = 18 (under 24, normal)
Charge: ₱250 (extension rate)

stays: cycle_hours=18, flag=false

stay_extensions:
  hours=6, amount=250.00, charge_type='extension'
  cycle_hours_before=12, cycle_hours_after=18
```

**Extension #3 — 4:00 AM (+6hrs) — HITS 24! RESET!**

```
cycle: 18 + 6 = 24 (equals threshold!)
Charge: ₱250 (extension rate — fills to boundary)

24 - 24 = 0 → cycle resets
stays: cycle_hours=0, flag=TRUE

stay_extensions:
  hours=6, amount=250.00, charge_type='extension'
  cycle_hours_before=18, cycle_hours_after=0

──── CYCLE 1 COMPLETE ────
Total cycle 1: ₱500 (check-in) + ₱250 + ₱250 + ₱250 = ₱1,250
Guest has stayed: 24 hours
```

---

### CYCLE 2 (Hours 24-48)

**Extension #4 — 10:00 AM (+6hrs) — FLAG IS TRUE**

```
flag=TRUE → charge ORIGINAL rate (not extension!)
Charge: ₱500 (original rate for 6hrs)

stays: cycle_hours=6, flag=FALSE (used up)

stay_extensions:
  hours=6, amount=500.00, charge_type='original'
  cycle_hours_before=0, cycle_hours_after=6
```

**Extension #5 — 4:00 PM (+6hrs)**

```
cycle: 6 + 6 = 12 (under 24, normal)
Charge: ₱250 (extension rate)

stays: cycle_hours=12, flag=false

stay_extensions:
  hours=6, amount=250.00, charge_type='extension'
  cycle_hours_before=6, cycle_hours_after=12
```

**Extension #6 — 10:00 PM (+6hrs)**

```
cycle: 12 + 6 = 18 (under 24, normal)
Charge: ₱250 (extension rate)

stays: cycle_hours=18, flag=false

stay_extensions:
  hours=6, amount=250.00, charge_type='extension'
  cycle_hours_before=12, cycle_hours_after=18
```

**Extension #7 — 4:00 AM (+6hrs) — HITS 24! RESET!**

```
cycle: 18 + 6 = 24 → resets to 0
Charge: ₱250 (extension rate)

stays: cycle_hours=0, flag=TRUE

stay_extensions:
  hours=6, amount=250.00, charge_type='extension'
  cycle_hours_before=18, cycle_hours_after=0

──── CYCLE 2 COMPLETE ────
Total cycle 2: ₱500 + ₱250 + ₱250 + ₱250 = ₱1,250
Guest has stayed: 48 hours
```

---

### CYCLE 3 (Hours 48-72)

**Extension #8 — 10:00 AM (+6hrs) — FLAG IS TRUE**

```
Charge: ₱500 (original rate)
stays: cycle_hours=6, flag=FALSE

stay_extensions:
  hours=6, amount=500.00, charge_type='original'
  cycle_hours_before=0, cycle_hours_after=6
```

**Extension #9 — 4:00 PM (+6hrs)**

```
cycle: 6 + 6 = 12, normal
Charge: ₱250 (extension rate)

stays: cycle_hours=12, flag=false

stay_extensions:
  hours=6, amount=250.00, charge_type='extension'
  cycle_hours_before=6, cycle_hours_after=12
```

**Extension #10 — 10:00 PM (+6hrs)**

```
cycle: 12 + 6 = 18, normal
Charge: ₱250 (extension rate)

stays: cycle_hours=18, flag=false

stay_extensions:
  hours=6, amount=250.00, charge_type='extension'
  cycle_hours_before=12, cycle_hours_after=18

Guest can keep extending... cycle continues forever.
```

---

## Summary Table

| # | Time | Action | Cycle | Flag | Charge | Type | Running Total |
|---|------|--------|-------|------|--------|------|---------------|
| - | 10AM Day 1 | Check-in 6hrs | 6 | false | ₱500 | original | ₱500 |
| 1 | 4PM Day 1 | Extend +6hrs | 12 | false | ₱250 | extension | ₱750 |
| 2 | 10PM Day 1 | Extend +6hrs | 18 | false | ₱250 | extension | ₱1,000 |
| 3 | 4AM Day 2 | Extend +6hrs | 0 | **TRUE** | ₱250 | extension | ₱1,250 |
| 4 | 10AM Day 2 | Extend +6hrs | 6 | false | **₱500** | **original** | ₱1,750 |
| 5 | 4PM Day 2 | Extend +6hrs | 12 | false | ₱250 | extension | ₱2,000 |
| 6 | 10PM Day 2 | Extend +6hrs | 18 | false | ₱250 | extension | ₱2,250 |
| 7 | 4AM Day 3 | Extend +6hrs | 0 | **TRUE** | ₱250 | extension | ₱2,500 |
| 8 | 10AM Day 3 | Extend +6hrs | 6 | false | **₱500** | **original** | ₱3,000 |
| 9 | 4PM Day 3 | Extend +6hrs | 12 | false | ₱250 | extension | ₱3,250 |
| 10 | 10PM Day 3 | Extend +6hrs | 18 | false | ₱250 | extension | ₱3,500 |

---

## The Pattern

```
Every 24-hour cycle costs: ₱1,250
  ├── ₱500 (original rate — once per cycle, after reset)
  ├── ₱250 (extension)
  ├── ₱250 (extension)
  └── ₱250 (extension — triggers reset)

Without cycle reset (extension rates forever):
  24 hours = ₱500 + (3 × ₱250) = ₱1,250... same?

Actually the protection kicks in because:
  Original rate for 6hrs = ₱500
  Extension rate for 6hrs = ₱250

  Without reset: 72hrs = ₱500 + (11 × ₱250) = ₱3,250
  With reset:    72hrs = ₱500 + (8 × ₱250) + (2 × ₱500) = ₱3,500

  Hotel earns ₱250 MORE per 72 hours from the reset system.
  Over a week: ₱583 extra. Over a month: ₱2,500 extra per guest.
```

---

## Split Charge Example (Crossing Boundary Mid-Extension)

Not all extensions land exactly on the boundary. Here's a split:

```
Setup: threshold=24, cycle_hours=20

Guest extends +6hrs (20+6=26, CROSSES 24!)

Split:
  4hrs to reach 24 → extension rate for 4hrs
  2hrs after 24   → original rate for 2hrs

stay_extensions:
  hours=6
  amount= ext_rate(4hrs) + orig_rate(2hrs)
  charge_type='split'
  cycle_hours_before=20
  cycle_hours_after=2

stays:
  cycle_hours=2
  charge_original_rate_next=true (crossed boundary)
```

**Important:** This requires that extension rates AND original rates exist for those exact hour values. Admin must ensure rates are configured for all possible gap hours.

---

## Database Queries

```sql
-- Get extension charge for Room 101, 6 hours (normal)
SELECT amount FROM extension_rates
WHERE room_id = 101
AND extension_hour_id = (SELECT id FROM extension_hours WHERE hours = 6 AND branch_id = 1)

-- Get original rate for Room 101, 6 hours (after reset)
SELECT amount FROM rates
WHERE room_id = 101
AND staying_hour_id = (SELECT id FROM staying_hours WHERE hours = 6 AND branch_id = 1)

-- Full extension history for a stay
SELECT
  se.hours, se.amount, se.charge_type,
  se.cycle_hours_before, se.cycle_hours_after,
  se.created_at
FROM stay_extensions se
WHERE se.stay_id = :stay_id
ORDER BY se.created_at
```

---

## Key Takeaway

The cycle reset is an infinite loop by design:

```
CHECK-IN → extensions (cheap) → RESET → original rate (full) → extensions (cheap) → RESET → ...
              ↑                                                                          |
              └──────────────────────── repeats forever ─────────────────────────────────┘
```

The schema handles this cleanly because:
1. `cycle_hours` resets to 0 when threshold crossed (modulo logic)
2. `charge_original_rate_next` flag fires once per cycle
3. Both rates and extension_rates are per-room (same lookup pattern)
4. Every extension is fully audited (before/after cycle position + charge type)
5. No limit on number of extensions — cycle keeps turning
