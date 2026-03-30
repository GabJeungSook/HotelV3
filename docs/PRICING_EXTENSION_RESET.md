# Pricing, Extension & Reset System

This is the **most complex part** of the hotel system. This document explains how all three concepts work together.

---

## The Big Picture

```
┌─────────────────────────────────────────────────────────────────────┐
│                     GUEST STAY LIFECYCLE                             │
└─────────────────────────────────────────────────────────────────────┘

CHECK-IN                    EXTENSIONS                      CHECK-OUT
   │                            │                               │
   ▼                            ▼                               ▼
┌─────────┐    ┌─────────┐    ┌─────────┐    ┌─────────┐    ┌─────────┐
│ Guest   │───►│ Pay     │───►│ Stay    │───►│ Extend? │───►│ Pay &   │
│ Arrives │    │ ORIGINAL│    │ in Room │    │         │    │ Leave   │
│         │    │ RATE    │    │         │    │         │    │         │
└─────────┘    └─────────┘    └─────────┘    └─────────┘    └─────────┘
                   │                              │
                   │                              ▼
                   │                         ┌─────────┐
                   │                         │ Pay     │
                   │                         │EXTENSION│◄─── Cheaper rate
                   │                         │ RATE    │     for extra hours
                   │                         └─────────┘
                   │                              │
                   │                              ▼
                   │                         ┌─────────┐
                   │                         │ RESET?  │◄─── After X hours,
                   │                         │         │     back to original
                   └────────────────────────►│         │     rate
                                             └─────────┘
```

---

## Part 1: Original Rates (Check-In Pricing)

When a guest checks in, they pay the **original rate** based on:
- Room type (or floor, depending on branch)
- Number of hours

### Example Original Rates:

| Room Type | 3 Hours | 6 Hours | 12 Hours |
|-----------|---------|---------|----------|
| Standard  | ₱500    | ₱800    | ₱1,200   |
| Deluxe    | ₱700    | ₱1,000  | ₱1,500   |

### Check-In Example:

```
Guest: "I want a Standard room for 3 hours"

System looks up: Standard + 3 hours = ₱500

Guest pays ₱500
└── Check-in time: 10:00 AM
└── Check-out time: 1:00 PM (10:00 + 3 hours)
```

**Simple so far.**

---

## Part 2: Extension Rates (Extra Hours)

Guest is in the room but wants to stay longer. What do you charge?

### The Problem:

```
Guest checked in for 3 hours (₱500)
Guest wants to stay 2 more hours

Option A: Look up 5-hour rate?
├── But there's no 5-hour rate!
└── Only 3hr, 6hr, 12hr exist

Option B: Charge the 6-hour rate (₱800)?
├── But guest already paid ₱500
└── Charge ₱800 - ₱500 = ₱300 for 2 extra hours?
└── This is complicated and confusing
```

### The Solution: Extension Rates

Create a separate, simpler pricing table just for extensions:

| Extension | Price |
|-----------|-------|
| +1 hour   | ₱100  |
| +2 hours  | ₱180  |
| +3 hours  | ₱250  |
| +6 hours  | ₱400  |

### Extension Example:

```
Guest: "I want to stay 2 more hours"

System looks up: Extension 2 hours = ₱180

Guest pays ₱180
└── New check-out time: 3:00 PM (1:00 PM + 2 hours)

Total paid: ₱500 + ₱180 = ₱680
Total hours: 3 + 2 = 5 hours
```

**Still simple. But here's where it gets tricky...**

---

## Part 3: The Problem — Guests Gaming the System

Without limits, a clever guest could exploit extension rates:

```
GAMING THE SYSTEM:

Check-in 3 hours:     ₱500     (Total: 3 hrs for ₱500)
Extend +1 hour:       ₱100     (Total: 4 hrs for ₱600)
Extend +1 hour:       ₱100     (Total: 5 hrs for ₱700)
Extend +1 hour:       ₱100     (Total: 6 hrs for ₱800)
Extend +1 hour:       ₱100     (Total: 7 hrs for ₱900)
Extend +1 hour:       ₱100     (Total: 8 hrs for ₱1,000)
Extend +1 hour:       ₱100     (Total: 9 hrs for ₱1,100)
Extend +1 hour:       ₱100     (Total: 10 hrs for ₱1,200)
Extend +1 hour:       ₱100     (Total: 11 hrs for ₱1,300)
Extend +1 hour:       ₱100     (Total: 12 hrs for ₱1,400)
...keep extending forever at ₱100/hour...

COMPARE TO BOOKING UPFRONT:

Book 12 hours directly: ₱1,200

RESULT: Guest gets 12 hours for ₱1,400 by extending
        vs ₱1,200 by booking upfront

        BUT after 12 hours, they keep paying only ₱100/hour
        They could stay 24 hours for ₱1,400 + ₱1,200 = ₱2,600
        When 24-hour rate might be ₱2,000 or more!
```

**The hotel loses money on long-staying guests who extend repeatedly.**

---

## Part 4: The Solution — Cycle Reset

### The Concept:

Set a **threshold** (e.g., 12 hours). After a guest accumulates this many hours, the system **resets** and charges **original rates** again.

```
┌─────────────────────────────────────────────────────────────────────┐
│                        CYCLE RESET CONCEPT                           │
└─────────────────────────────────────────────────────────────────────┘

        ◄─────────── CYCLE 1 (12 hours) ───────────►

Hours:  0    1    2    3    4    5    6    7    8    9   10   11   12
        │    │    │    │    │    │    │    │    │    │    │    │    │
Rate:   └────────────── EXTENSION RATES (cheap) ──────────────────────┘
                                                                      │
                                                                   RESET!
                                                                      │
        ◄─────────── CYCLE 2 (next 12 hours) ──────────►              ▼

Hours: 12   13   14   15   16   17   18   19   20   21   22   23   24
        │    │    │    │    │    │    │    │    │    │    │    │    │
Rate:   │    └────────────── EXTENSION RATES (cheap) ─────────────────┘
        │
        └── ORIGINAL RATE (full price) for first extension after reset
```

### The Rule:

```
Hours 0-12:   Use EXTENSION rates (cheap)
Hour 12:      RESET! ⚠️
Hour 12+:     First extension uses ORIGINAL rate (full price)
              Then back to extension rates until next reset
```

---

## Part 5: How Reset Works — Step by Step

### Setting: `extension_time_reset = 12`

### Scenario: Guest keeps extending

```
┌─────────────────────────────────────────────────────────────────────┐
│  CHECK-IN: 3 hours for ₱500 (Standard room)                        │
├─────────────────────────────────────────────────────────────────────┤
│  Cycle counter: 3                                                   │
│  next_extension_is_original: FALSE                                  │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│  EXTEND +3 hours for ₱250 (extension rate)                         │
├─────────────────────────────────────────────────────────────────────┤
│  3 + 3 = 6 (still under 12, no reset)                              │
│  Cycle counter: 6                                                   │
│  next_extension_is_original: FALSE                                  │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│  EXTEND +3 hours for ₱250 (extension rate)                         │
├─────────────────────────────────────────────────────────────────────┤
│  6 + 3 = 9 (still under 12, no reset)                              │
│  Cycle counter: 9                                                   │
│  next_extension_is_original: FALSE                                  │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│  EXTEND +6 hours — THIS CROSSES 12! ⚠️                              │
├─────────────────────────────────────────────────────────────────────┤
│  9 + 6 = 15 (crosses 12!)                                          │
│                                                                      │
│  SPLIT CHARGE:                                                       │
│  ├── 3 hours to reach 12 → Extension rate: ₱250                    │
│  └── 3 hours after 12 → ORIGINAL rate for 3hr: ₱500                │
│                                                                      │
│  Guest pays: ₱250 + ₱500 = ₱750                                    │
│                                                                      │
│  Cycle counter: 3 (15 - 12 = 3, new cycle)                         │
│  next_extension_is_original: TRUE                                   │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│  EXTEND +3 hours — FLAG IS TRUE! ⚠️                                 │
├─────────────────────────────────────────────────────────────────────┤
│  next_extension_is_original = TRUE                                  │
│                                                                      │
│  Guest pays: ORIGINAL rate for 3hr = ₱500 (not ₱250 extension!)   │
│                                                                      │
│  Cycle counter: 6 (3 + 3)                                          │
│  next_extension_is_original: FALSE (flag resets after use)         │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│  EXTEND +3 hours — BACK TO NORMAL                                   │
├─────────────────────────────────────────────────────────────────────┤
│  next_extension_is_original = FALSE                                 │
│  6 + 3 = 9 (under 12, no reset)                                    │
│                                                                      │
│  Guest pays: Extension rate for 3hr = ₱250                         │
│                                                                      │
│  Cycle counter: 9                                                   │
│  next_extension_is_original: FALSE                                  │
└─────────────────────────────────────────────────────────────────────┘
```

---

## Part 6: The Three Scenarios

### Scenario 1: Normal Extension (No Reset)

**Condition:** `current_cycle_hours + extension_hours < 12`

```
Current: 5 hours in cycle
Extend: +3 hours
Total: 8 hours (under 12)

Charge: Extension rate for 3 hours = ₱250
New cycle counter: 8
```

### Scenario 2: Extension Crosses Reset Boundary

**Condition:** `current_cycle_hours + extension_hours >= 12`

```
Current: 9 hours in cycle
Extend: +6 hours
Total: 15 hours (crosses 12!)

SPLIT:
├── 3 hours to reach 12 → Extension rate: ₱250
└── 3 hours after 12 → Original rate: ₱500

Charge: ₱250 + ₱500 = ₱750
New cycle counter: 3 (15 - 12)
Flag: next_extension_is_original = TRUE
```

### Scenario 3: First Extension After Reset

**Condition:** `next_extension_is_original = TRUE`

```
Flag is TRUE (just crossed boundary in previous extension)

Extend: +3 hours

Charge: ORIGINAL rate for 3 hours = ₱500 (not extension rate!)
New cycle counter: increases by 3
Flag: next_extension_is_original = FALSE (used up)
```

---

## Part 7: Why It's Confusing

### Problem 1: Two Fields Sound Similar

| Field | What It ACTUALLY Means |
|-------|------------------------|
| `hours_stayed` | Original booking hours (NEVER changes) |
| `number_of_hours` | Position in current CYCLE (resets at 12) |

**Confusion:** Both sound like "how long guest stayed"

**Fix:** Rename to `initial_booking_hours` and `cycle_position`

### Problem 2: Flag Name is Unclear

| Field | What It Means |
|-------|---------------|
| `next_extension_is_original` | "The NEXT extension will charge ORIGINAL rate, not extension rate" |

**Fix:** Rename to `charge_full_price_next_extension`

### Problem 3: Split Charges

When extension crosses boundary, guest pays TWO amounts:
- Extension rate for hours BEFORE reset
- Original rate for hours AFTER reset

**Staff confusion:** "Why is this extension ₱750 when it should be ₱400?"

### Problem 4: Extension Options Change

After reset, only certain extension options appear (must match staying_hours):
- Before reset: Can extend 1hr, 2hr, 3hr, 6hr (any extension rate)
- After reset: Can only extend 3hr, 6hr, 12hr (must match original rates)

**Staff confusion:** "Where did the 1-hour and 2-hour options go?"

---

## Part 8: Complete Example Timeline

**Setup:**
- `extension_time_reset = 12`
- Original rates: 3hr=₱500, 6hr=₱800, 12hr=₱1,200
- Extension rates: 1hr=₱100, 2hr=₱180, 3hr=₱250, 6hr=₱400

**Guest Journey:**

| Time | Action | Cycle | Charge | Total Paid | Total Hours |
|------|--------|-------|--------|------------|-------------|
| 10:00 AM | Check-in 3hr | 3 | ₱500 (original) | ₱500 | 3 |
| 1:00 PM | Extend +3hr | 6 | ₱250 (extension) | ₱750 | 6 |
| 4:00 PM | Extend +3hr | 9 | ₱250 (extension) | ₱1,000 | 9 |
| 7:00 PM | Extend +6hr | 3 ⚠️ | ₱250+₱500=₱750 (split!) | ₱1,750 | 15 |
| 1:00 AM | Extend +3hr | 6 | ₱500 (original, flag was TRUE) | ₱2,250 | 18 |
| 4:00 AM | Extend +3hr | 9 | ₱250 (extension) | ₱2,500 | 21 |
| 7:00 AM | Extend +6hr | 3 ⚠️ | ₱250+₱500=₱750 (split!) | ₱3,250 | 27 |
| 1:00 PM | Check-out | - | - | ₱3,250 | 27 |

**Without reset system (if extension rates applied forever):**
- 27 hours at extension rates ≈ ₱500 + (24 × ₱100) = ₱2,900

**With reset system:**
- Guest paid ₱3,250 (₱350 more)

**Hotel protected from gaming!**

---

## Part 9: The Decision Flowchart

```
┌─────────────────────────────────────────────────────────────────────┐
│                 EXTENSION PRICING DECISION TREE                      │
└─────────────────────────────────────────────────────────────────────┘

Guest wants to extend. What do we charge?
                │
                ▼
        ┌───────────────┐
        │ Is flag TRUE? │──── next_extension_is_original
        │               │
        └───────┬───────┘
                │
        ┌───────┴───────┐
        │               │
       YES              NO
        │               │
        ▼               ▼
┌───────────────┐  ┌──────────────────────┐
│ Charge        │  │ Will this cross      │
│ ORIGINAL RATE │  │ the 12-hour reset?   │
│ (full price)  │  │                      │
│               │  │ current + extension  │
│ Reset flag    │  │ >= extension_reset?  │
│ to FALSE      │  └──────────┬───────────┘
└───────────────┘             │
                      ┌───────┴───────┐
                      │               │
                     YES              NO
                      │               │
                      ▼               ▼
              ┌───────────────┐  ┌───────────────┐
              │ SPLIT CHARGE: │  │ Charge        │
              │               │  │ EXTENSION     │
              │ Extension rate│  │ RATE only     │
              │ for hours     │  │               │
              │ BEFORE 12     │  │ (cheap rate)  │
              │               │  │               │
              │ Original rate │  └───────────────┘
              │ for hours     │
              │ AFTER 12      │
              │               │
              │ Set flag TRUE │
              │ Reset cycle   │
              └───────────────┘
```

---

## Part 10: Database Fields

### `checkin_details` Table

| Field | Type | Purpose |
|-------|------|---------|
| `hours_stayed` | integer | Original booking hours (never changes) |
| `number_of_hours` | integer | Current position in cycle (0-12, resets) |
| `next_extension_is_original` | boolean | If TRUE, next extension charges original rate |
| `check_out_at` | datetime | Current expected checkout (extends with each extension) |

### `branches` Table

| Field | Type | Purpose |
|-------|------|---------|
| `extension_time_reset` | integer | Hours before cycle resets (e.g., 12) |

### `stay_extensions` Table

| Field | Type | Purpose |
|-------|------|---------|
| `hours` | integer | How many hours extended |
| `amount` | decimal | Actual amount charged (may include split) |

---

## Part 11: V4 Recommendations

### 1. Rename Fields for Clarity

```
number_of_hours → cycle_position
hours_stayed → initial_hours
next_extension_is_original → charge_original_next
```

### 2. Track Rate Type Used

Add to `stay_extensions`:
```sql
charge_type ENUM('extension', 'original', 'split')
extension_portion DECIMAL(10,2) NULL  -- Amount from extension rate
original_portion DECIMAL(10,2) NULL   -- Amount from original rate
```

### 3. Consider Simplifying

**Option A: Remove reset entirely**
- Always charge extension rates
- Simpler but hotel loses money on long stays

**Option B: Simple multiplier after threshold**
- After 12 hours, extension rate × 1.5
- No complex split calculations

**Option C: Keep current logic but improve UI**
- Show staff exactly why price is what it is
- "₱750 = ₱250 (3hr extension) + ₱500 (3hr original after reset)"

### 4. Validate Extension Rates Exist

Ensure all possible "gap" hours have rates:
- If guest is at 10 hours and reset is 12, they need 2 hours to reach boundary
- There MUST be a 2-hour extension rate defined

---

## Summary

| Concept | What It Is | Why It Exists |
|---------|------------|---------------|
| **Original Rate** | Full price at check-in | Revenue from new guests |
| **Extension Rate** | Cheaper rate for extra hours | Convenience for guests who stay a bit longer |
| **Cycle Reset** | After X hours, back to original pricing | Prevents gaming the system with endless cheap extensions |

### The Flow:

```
CHECK-IN (Original Rate)
    ↓
EXTEND (Extension Rate) ←──────┐
    ↓                          │
EXTEND (Extension Rate) ───────┤ Repeat until
    ↓                          │ reaching reset
EXTEND (Extension Rate) ───────┘
    ↓
CROSS 12 HOURS → RESET! (Split charge)
    ↓
EXTEND (Original Rate) ← Flag forces full price once
    ↓
EXTEND (Extension Rate) ← Back to cheap rates
    ↓
... cycle continues ...
```

### Key Insight:

The reset system ensures that **long-staying guests can't pay less than short-staying guests** by gaming extension rates. Every 12 hours, they're forced to pay original pricing at least once.
