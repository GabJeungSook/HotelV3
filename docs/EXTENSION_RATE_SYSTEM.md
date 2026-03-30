# Extension Rate System Documentation

This document explains the **Extension Rate System** - the most complex and problematic part of the hotel's business logic. Understanding this is critical for the schema rebuild.

---

## Simple Explanation (Start Here!)

### The Basic Idea

Your hotel charges guests **by the hour** (not by night like regular hotels).

**Example:**
- Guest checks in for **3 hours** → pays ₱500
- Guest checks in for **6 hours** → pays ₱800
- Guest checks in for **12 hours** → pays ₱1,200

These are the **original rates** (stored in `rates` table).

---

### The Problem: Guest Wants to Stay Longer

Guest booked 3 hours but wants to stay 2 more hours. What do you charge?

**Option A:** Charge them ₱800 (the 5-hour rate) - BUT they already paid ₱500!

**Option B:** Create **extension rates** - smaller hourly charges just for extending.

Your system uses **Option B**:
- Extend 1 hour → ₱100
- Extend 2 hours → ₱180
- Extend 3 hours → ₱250

So guest pays: ₱500 (original) + ₱180 (2hr extension) = **₱680 total**

---

### The Business Problem: Guests Gaming the System

Without limits, a clever guest could:
```
Book 3 hours     → ₱500
Extend 1 hour    → ₱100  (now 4 hours for ₱600)
Extend 1 hour    → ₱100  (now 5 hours for ₱700)
Extend 1 hour    → ₱100  (now 6 hours for ₱800)
...keep extending forever at ₱100/hour
```

This is **cheaper** than booking 12 hours upfront (₱1,200)!

---

### The Solution: Cycle Reset (`extension_time_reset`)

The hotel sets a **threshold** (e.g., 12 hours).

**Rule:** After guest accumulates 12 hours total, the system **resets** and charges **original rates** again (not cheap extension rates).

```
Hours 0-12:  Use extension rates (cheap)
Hour 12:     RESET!
Hours 12+:   Use original rates again (full price)
```

---

### How It Works in Practice

Let's say `extension_time_reset = 12`

```
CHECK-IN: 3 hours (₱500)
├── Cycle counter: 3 hours

EXTEND +2 hours (₱180 extension rate)
├── Cycle counter: 5 hours

EXTEND +3 hours (₱250 extension rate)
├── Cycle counter: 8 hours

EXTEND +6 hours ← THIS CROSSES 12!
├── Cycle counter would be: 14 hours
├── System says: "You crossed 12, time to pay full price!"
│
├── CHARGE SPLIT:
│   ├── 4 hours to reach 12 → Extension rate (₱400)
│   └── 2 hours after 12 → ORIGINAL rate for 2hr (₱300)
│   └── Total: ₱700
│
└── Cycle counter RESETS to: 2 hours (14 - 12 = 2)
```

---

### The Two Important Fields

| Field | What It Tracks |
|-------|----------------|
| `checkin_details.number_of_hours` | Where guest is in current cycle (0-12). Resets when crossing threshold. |
| `checkin_details.next_extension_is_original` | If `true`, next extension charges ORIGINAL rate (full price), not extension rate. |

---

### Simple Decision Tree

```
Guest wants to extend. What do we charge?

1. Is next_extension_is_original = TRUE and cycle_hours = 0?
   └── YES → Charge ORIGINAL RATE (full price)

2. Will this extension cross the 12-hour threshold?
   └── YES → Split charge:
             • Extension rate for hours BEFORE 12
             • Original rate for hours AFTER 12

3. Otherwise (normal case)
   └── Charge EXTENSION RATE (cheap rate)
```

---

### Real Example with Numbers

**Setup:**
- extension_time_reset = 12
- Original rates: 3hr=₱500, 6hr=₱800
- Extension rates: 1hr=₱100, 3hr=₱250, 6hr=₱400

**Guest Journey:**

| Action | Cycle Hours | Charge | Why |
|--------|-------------|--------|-----|
| Check-in 3hr | 3 | ₱500 | Original rate |
| Extend +3hr | 6 | ₱250 | Extension rate (under 12) |
| Extend +3hr | 9 | ₱250 | Extension rate (under 12) |
| Extend +6hr | 3 *(reset!)* | ₱250 + ₱500 = ₱750 | 3hr extension to reach 12, then 3hr original rate |
| Extend +3hr | 6 | ₱500 | Original rate (flag was true) |
| Extend +3hr | 9 | ₱250 | Extension rate (flag now false) |

---

### Why It's Confusing

1. **Two different rates** for the same thing (extending stay)
2. **Cycle resets** are invisible to staff - they just see different prices
3. **Field names are bad:**
   - `number_of_hours` sounds like "total hours" but it's actually "cycle position"
   - `hours_stayed` is the REAL total hours
4. **Split charges** when crossing boundary - hard to explain to guest

---

### Key Takeaways

| Term | Meaning |
|------|---------|
| **Original Rate** | Full price from `rates` table (check-in pricing) |
| **Extension Rate** | Cheap hourly rate from `extension_rates` table |
| **Cycle Reset** | After 12 hours, go back to charging full prices |
| **number_of_hours** | Position in current cycle (NOT total hours stayed) |
| **next_extension_is_original** | Flag: "charge full price next time" |

---

## Technical Details (For Developers)

Below are the detailed technical explanations for the schema rebuild.

---

## What is the Extension Rate System?

When a guest checks into a hotel room, they pay for a specific number of hours (e.g., 3 hours, 6 hours, 12 hours). If the guest wants to stay longer than their original booking, they need to **extend** their stay.

The extension rate system calculates how much to charge for additional time.

---

## The Two Pricing Systems

### 1. Original Rates (Initial Check-in)
These are the standard room rates based on:
- **Room Type** (Standard, Deluxe, Suite, etc.)
- **Staying Hours** (3hr, 6hr, 12hr, 24hr, etc.)

Example:
| Room Type | 3 Hours | 6 Hours | 12 Hours |
|-----------|---------|---------|----------|
| Standard  | ₱500    | ₱800    | ₱1,200   |
| Deluxe    | ₱700    | ₱1,000  | ₱1,500   |

**Tables:** `rates`, `staying_hours`, `types`

### 2. Extension Rates (Additional Hours)
These are flat-rate charges for extending a stay:

Example:
| Extension Hours | Amount |
|-----------------|--------|
| 1 hour          | ₱100   |
| 2 hours         | ₱180   |
| 3 hours         | ₱250   |
| 6 hours         | ₱400   |

**Tables:** `extension_rates`

---

## The Cycle Reset Concept (The Complex Part)

### What is `extension_time_reset`?

This is a **branch-level setting** stored in `branches.extension_time_reset` that defines the maximum hours before a guest's extension "resets" back to original pricing.

**Example:** If `extension_time_reset = 12`, then:
- After a guest accumulates 12 hours of extensions, the system resets
- The next extension charges the **original room rate** instead of extension rate
- This prevents guests from staying indefinitely on cheap extension rates

### Why Does This Exist?

Business Logic: The hotel doesn't want guests to:
1. Book 3 hours at ₱500
2. Extend 1 hour at ₱100 (total: 4 hours for ₱600)
3. Extend 1 hour at ₱100 (total: 5 hours for ₱700)
4. Keep extending at ₱100/hour forever

Instead, after reaching the cycle limit (e.g., 12 hours), they must pay a fresh original rate.

---

## The Three Extension Scenarios

### Scenario 1: Normal Extension (No Cycle Crossing)

**Condition:** `current_hours + extension_hours < extension_time_reset`

**Calculation:** Just charge the extension rate amount

```
Guest has: 3 hours
Extending: 2 hours
Reset threshold: 12 hours
Total after: 5 hours (less than 12)

Charge: Extension rate for 2 hours = ₱180
```

### Scenario 2: Extension Crosses Cycle Boundary

**Condition:** `current_hours + extension_hours >= extension_time_reset`

**Calculation:**
- Charge extension rate UP TO the reset point
- Charge original rate for hours AFTER reset
- Reset the hour counter

```
Guest has: 10 hours
Extending: 6 hours
Reset threshold: 12 hours
Total after: 16 hours (crosses 12)

Part 1: 2 hours to reach 12 (extension rate) = ₱180
Part 2: 4 hours after reset (original rate for 4hr) = ₱X (from rates table)

Total: ₱180 + ₱X
New hour counter: 4 hours
```

### Scenario 3: Post-Reset Extension (Fresh Cycle)

**Condition:** `current_hours == 0 AND next_extension_is_original == true`

**Calculation:** Charge original room rate (not extension rate)

```
Guest just crossed the reset boundary
Current hours: 0 (just reset)
next_extension_is_original: true

Extending: 3 hours
Charge: Original rate for 3 hours from rates table = ₱500
```

---

## Key Fields Explained

### `checkin_details` Table

| Field | Purpose |
|-------|---------|
| `hours_stayed` | Original hours booked at check-in |
| `number_of_hours` | **Current cycle hours** - tracks hours within current extension cycle (resets to 0 when crossing threshold) |
| `next_extension_is_original` | **Boolean flag** - if true, next extension charges original rate instead of extension rate |
| `check_out_at` | Expected checkout time (gets extended with each extension) |
| `static_amount` | Original rate amount at check-in |
| `static_room_amount` | Room amount snapshot |

### `stay_extensions` Table

| Field | Purpose |
|-------|---------|
| `guest_id` | Which guest extended |
| `extension_id` | FK to `extension_rates` |
| `hours` | How many hours extended |
| `amount` | **Actual amount charged** (may differ from extension_rate.amount due to cycle logic) |
| `frontdesk_ids` | Which frontdesk processed it |

### `branches` Table

| Field | Purpose |
|-------|---------|
| `extension_time_reset` | Hours threshold before cycle resets (e.g., 12) |

### `extension_rates` Table

| Field | Purpose |
|-------|---------|
| `hour` | Extension duration (1, 2, 3, 6, etc.) |
| `amount` | Base price for this extension |
| `branch_id` | Which branch this rate belongs to |

---

## Code Flow Analysis

From `ExtendGuest.php`:

```php
// Priority 1: Post-reset → charge original rate
if (($this->current_time_alloted == 0)
    && $this->guest->checkInDetail()->first()->next_extension_is_original == true) {
    // Find original rate for this room type + extension hours
    $rate = Rate::where('type_id', $this->rate->type_id)
        ->whereHas('stayingHour', function ($query) {
            $query->where('number', $this->extended_rate->hour);
        })->first();
    $this->initial_amount = $rate->amount;  // Original rate
    $this->extended_amount = 0;
}

// Priority 2: Extension crosses cycle boundary
elseif ($current >= $this->extension_time_reset) {
    // Calculate hours after reset
    $total_current_hours = $current - $this->extension_time_reset;

    // Find original rate for post-reset hours
    $rate = Rate::where('type_id', $this->rate->type_id)
        ->whereHas('stayingHour', fn($q) => $q->where('number', $total_current_hours))
        ->first();

    // Find extension rate for hours before reset
    $extend_hour = $this->extension_time_reset - $this->current_time_alloted;
    $extend = ExtensionRate::where('hour', $extend_hour)->first();

    $this->initial_amount = $rate->amount;   // Original rate (post-reset)
    $this->extended_amount = $extend->amount; // Extension rate (pre-reset)
}

// Priority 3: Normal extension (no cycle crossing)
else {
    $this->initial_amount = 0;
    $this->extended_amount = $this->extended_rate->amount;
}
```

After saving extension:
```php
// Calculate new cycle hours
$total_hours = $cycle_hours + $extension_hours;
$next_extension_is_original = false;

// Reset cycle if threshold crossed
while ($total_hours >= $this->extension_time_reset) {
    $total_hours = $total_hours - $this->extension_time_reset;
    $next_extension_is_original = true;  // Next extension uses original rate
}

$check_in_detail->update([
    'number_of_hours' => $total_hours,  // New cycle position
    'next_extension_is_original' => $next_extension_is_original,
    'check_out_at' => Carbon::parse($check_out_at)->addHours($rate->hour),
]);
```

---

## Visual Example: Guest Stay Timeline

```
CHECK-IN: Guest books 3 hours at ₱500 (Standard room)
├── number_of_hours: 3
├── next_extension_is_original: false
└── check_out_at: 11:00 AM

EXTENSION 1: +2 hours at ₱180 (extension rate)
├── Current: 3, Adding: 2, Total: 5 (< 12 reset)
├── number_of_hours: 5
├── next_extension_is_original: false
└── check_out_at: 1:00 PM

EXTENSION 2: +3 hours at ₱250 (extension rate)
├── Current: 5, Adding: 3, Total: 8 (< 12 reset)
├── number_of_hours: 8
├── next_extension_is_original: false
└── check_out_at: 4:00 PM

EXTENSION 3: +6 hours (CROSSES 12hr threshold!)
├── Current: 8, Adding: 6, Total: 14 (> 12 reset)
├── Charge: ₱400 (extension for 4hr to reach 12) + ₱X (original rate for 2hr)
├── number_of_hours: 2 (14 - 12 = 2, new cycle)
├── next_extension_is_original: true
└── check_out_at: 10:00 PM

EXTENSION 4: +3 hours (post-reset, charges ORIGINAL rate)
├── next_extension_is_original was true
├── Charge: ₱500 (original 3hr rate, NOT extension rate!)
├── number_of_hours: 5 (2 + 3)
├── next_extension_is_original: false
└── check_out_at: 1:00 AM
```

---

## Known Problems & Confusion Points

### 1. Extension Rate Filtering Logic
```php
if($this->current_time_alloted == 0 && $next_extension_is_original == true) {
    // Only show extension rates that match staying_hours in rates table
    $this->extension_rates = ExtensionRate::whereIn('hour', $stayingHours)->get();
} else {
    // Show all extension rates
    $this->extension_rates = ExtensionRate::all();
}
```
**Problem:** When post-reset, only certain extension options appear (must match original rate hours). This confuses staff.

### 2. `number_of_hours` vs `hours_stayed`
- `hours_stayed` = Original booking hours (never changes)
- `number_of_hours` = Current cycle position (resets at threshold)

**Problem:** Naming is confusing. Both sound like they track hours stayed.

### 3. Amount Stored in `stay_extensions`
The `amount` field stores the **actual charged amount**, not the extension rate's base amount.

**Problem:** Can't easily tell if original rate or extension rate was used.

### 4. Missing Extension Rate for Exact Hours
If `extension_time_reset = 12` and guest is at 10 hours wanting to extend 3 hours:
- Needs 2 hours to reach boundary
- But there may not be a 2-hour extension rate defined

**Problem:** System may fail or charge incorrectly.

### 5. While Loop for Multiple Resets
```php
while ($total_hours >= $this->extension_time_reset) {
    $total_hours = $total_hours - $this->extension_time_reset;
    $next_extension_is_original = true;
}
```
**Problem:** If extension is huge (e.g., 30 hours with 12hr reset), this loops multiple times but only charges once.

### 6. Data Type Issues
- `stay_extensions.hours` is `string` (should be `integer`)
- `stay_extensions.amount` is `string` (should be `decimal`)
- `extension_rates.amount` is `integer` (inconsistent with other amounts)

---

## Tables Involved

| Table | Role |
|-------|------|
| `extension_rates` | Defines hourly extension prices per branch |
| `stay_extensions` | Records each extension transaction |
| `checkin_details` | Tracks cycle state (`number_of_hours`, `next_extension_is_original`) |
| `transactions` | Financial record (transaction_type_id = 6 for extensions) |
| `extended_guest_reports` | Reporting on extensions |
| `branches` | Stores `extension_time_reset` threshold |
| `rates` | Original room rates (used when cycle resets) |
| `staying_hours` | Valid hour packages (3hr, 6hr, etc.) |

---

## Recommendations for Schema Rebuild

### 1. Rename Fields for Clarity
- `number_of_hours` → `cycle_hours_accumulated`
- `next_extension_is_original` → `charge_original_rate_next`
- `hours_stayed` → `initial_booking_hours`

### 2. Add Extension Type Tracking
Add field to `stay_extensions`:
```sql
extension_charge_type ENUM('extension_rate', 'original_rate', 'mixed')
```

### 3. Fix Data Types
- All monetary amounts: `decimal(10,2)`
- All hour fields: `integer`

### 4. Consider Simplification
Option A: Remove cycle reset entirely, always charge extension rates
Option B: Create a separate "cycle" table to track guest cycles explicitly

### 5. Add Validation
Ensure extension rates exist for all possible "gap" hours (1, 2, 3, etc.)

### 6. Log Original vs Extension
Store which rate type was used:
```sql
original_rate_amount DECIMAL(10,2) NULL
extension_rate_amount DECIMAL(10,2) NULL
```

---

## Quick Reference: Extension Calculation

```
Input:
  - current_hours (from checkin_details.number_of_hours)
  - extension_hours (selected by user)
  - extension_time_reset (from branches)
  - next_extension_is_original (from checkin_details)

Logic:
  IF next_extension_is_original == true AND current_hours == 0:
      charge = original_rate[extension_hours]
  ELSE IF (current_hours + extension_hours) >= extension_time_reset:
      hours_to_boundary = extension_time_reset - current_hours
      hours_after_reset = (current_hours + extension_hours) - extension_time_reset
      charge = extension_rate[hours_to_boundary] + original_rate[hours_after_reset]
  ELSE:
      charge = extension_rate[extension_hours]

Output:
  - new_cycle_hours = (current_hours + extension_hours) % extension_time_reset
  - next_extension_is_original = (current_hours + extension_hours) >= extension_time_reset
  - new_checkout_time = old_checkout_time + extension_hours
```
