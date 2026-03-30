# Reset Logic — The Critical Part

This document focuses ONLY on the **cycle reset logic** — the most confusing and problematic part of the extension system.

---

## What is the Reset?

The hotel has a **cycle threshold** (e.g., 24 hours). After a guest accumulates this many hours, the system **resets** and forces them to pay **original rates** instead of cheap extension rates.

```
┌─────────────────────────────────────────────────────────────────────┐
│                          CYCLE = 24 HOURS                            │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  Hours 0 ──────────────────────────────────────────────────► 24     │
│         │                                                    │      │
│         │◄─────── EXTENSION RATES (cheap) ──────────────────►│      │
│         │                                                    │      │
│         │                                                  RESET!   │
│         │                                                    │      │
│         │                                                    ▼      │
│         │                                             PAY ORIGINAL  │
│         │                                             RATE ONCE     │
│         │                                                    │      │
│         │◄────────────── THEN BACK TO ───────────────────────┘      │
│         │                EXTENSION RATES                            │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

---

## The Two Key Fields

| Field | Location | Purpose |
|-------|----------|---------|
| `number_of_hours` | `checkin_details` | Current position in cycle (0 to 24) |
| `next_extension_is_original` | `checkin_details` | Flag: TRUE = charge original rate next time |

**Important:** `number_of_hours` is NOT total hours stayed. It's the **cycle position** that resets to 0 when crossing the threshold.

---

## The Three Scenarios in Code

From `ExtendGuest.php` → `updatedExtensionRateId()`:

### Scenario 1: Normal Extension (No Reset)

**Code (lines 147-151):**
```php
else {
    $this->initial_amount = 0;
    $this->extended_amount = $this->extended_rate->amount;
    $this->total_amount = $this->initial_amount + $this->extended_amount;
}
```

**Condition:** `current + extension < 24` (doesn't cross threshold)

**Example:**
```
Current cycle position: 6 hours
Extension requested: +6 hours
New position: 12 hours (still under 24)

Charge: Extension rate only (e.g., ₱100)
```

---

### Scenario 2: Extension Crosses Threshold (SPLIT Charge)

**Code (lines 125-144):**
```php
elseif ($current >= $this->extension_time_reset) {
    $total_current_hours = $this->current_time_alloted + $this->extended_rate->hour;
    $total_current_hours = $total_current_hours - $this->extension_time_reset;

    // Find ORIGINAL rate for hours AFTER reset
    $rate = Rate::where('type_id', $this->rate->type_id)
        ->whereHas('stayingHour', function ($query) use ($total_current_hours) {
            $query->where('number', $total_current_hours);
        })->first();

    // Find EXTENSION rate for hours BEFORE reset
    $extend_hour = $this->extension_time_reset - $this->current_time_alloted;
    $extend = ExtensionRate::where('hour', $extend_hour)->first();

    $this->initial_amount = $rate?->amount ?? 0;    // Original rate (post-reset)
    $this->extended_amount = $extend?->amount ?? 0;  // Extension rate (pre-reset)
    $this->total_amount = $this->initial_amount + $this->extended_amount;
}
```

**Condition:** `current + extension >= 24` (crosses threshold)

**Example:**
```
Current cycle position: 18 hours
Extension requested: +12 hours
Total would be: 30 hours (crosses 24!)

SPLIT:
├── Hours to reach 24: 24 - 18 = 6 hours → Extension rate (e.g., ₱100)
├── Hours after 24: 30 - 24 = 6 hours → Original rate (e.g., ₱200)
└── Total charge: ₱100 + ₱200 = ₱300

New cycle position: 6 hours (30 - 24)
Flag: next_extension_is_original = TRUE
```

---

### Scenario 3: Post-Reset (Flag is TRUE)

**Code (lines 113-123):**
```php
if (($this->current_time_alloted == 0)
    && $this->guest->checkInDetail()->first()->next_extension_is_original == true) {

    // Find ORIGINAL rate matching extension hours
    $rate = Rate::where('type_id', $this->rate->type_id)
        ->whereHas('stayingHour', function ($query) {
            $query->where('number', $this->extended_rate->hour);
        })->first();

    $this->initial_amount = $rate?->amount ?? 0;
    $this->extended_amount = 0;
    $this->total_amount = $this->initial_amount + $this->extended_amount;
}
```

**Condition:** `current == 0` AND `next_extension_is_original == TRUE`

**Example:**
```
Current cycle position: 0 (just reset)
Flag: next_extension_is_original = TRUE
Extension requested: +6 hours

Charge: ORIGINAL rate for 6 hours (e.g., ₱200, NOT extension rate ₱100)

Flag resets to: FALSE (after this extension)
```

---

## The Cycle Update Logic

From `ExtendGuest.php` → `saveExtend()` (lines 219-233):

```php
$cycle_hours = $check_in_detail->number_of_hours;
$extension_hours = $rate->hour;
$total_hours = $cycle_hours + $extension_hours;

$next_extension_is_original = false;

// WHILE loop handles crossing threshold
while ($total_hours >= $this->extension_time_reset) {
    $total_hours = $total_hours - $this->extension_time_reset;
    $next_extension_is_original = true;
}

$check_in_detail->update([
    'number_of_hours' => $total_hours,
    'next_extension_is_original' => $next_extension_is_original,
    'check_out_at' => Carbon::parse($check_out_at)->addHours($rate->hour),
]);
```

**Logic:**
1. Add extension hours to current cycle position
2. If total >= 24, subtract 24 and set flag to TRUE
3. Keep subtracting while still >= 24 (handles huge extensions)
4. Save new cycle position and flag

---

## Extension Rate Dropdown Filtering

From `ExtendGuest.php` → `mount()` (lines 82-93):

```php
if ($this->current_time_alloted == 0
    && $this->guest->checkInDetail()->first()->next_extension_is_original == true) {

    // ONLY show extension rates that match original staying hours (6, 12, 24)
    $this->extension_rates = ExtensionRate::whereIn('hour', $stayingHours)->get();

} else {
    // Show ALL extension rates (1, 2, 3, 6, 12, etc.)
    $this->extension_rates = ExtensionRate::all();
}
```

**Why?** After reset, the system charges ORIGINAL rates. Original rates only exist for certain hours (6, 12, 24). So dropdown only shows those options.

**Problem:** Staff gets confused when 1hr, 2hr, 3hr options suddenly disappear.

---

## Complete Example: 24-Hour Reset Cycle

**Setup:**
- `extension_time_reset = 24`
- Original rates: 6hr=₱200, 12hr=₱350, 24hr=₱600
- Extension rates: 6hr=₱100, 12hr=₱180

**Guest Journey:**

| Step | Action | Cycle Pos | Flag | Charge | Why |
|------|--------|-----------|------|--------|-----|
| 1 | Check-in 12hr | 12 | FALSE | ₱350 | Original rate |
| 2 | Extend +6hr | 18 | FALSE | ₱100 | Extension (under 24) |
| 3 | Extend +6hr | 0 | TRUE | ₱100 | Extension (reached exactly 24, reset!) |
| 4 | Extend +6hr | 6 | FALSE | ₱200 | Original (flag was TRUE) |
| 5 | Extend +6hr | 12 | FALSE | ₱100 | Extension (under 24) |
| 6 | Extend +6hr | 18 | FALSE | ₱100 | Extension (under 24) |
| 7 | Extend +12hr | 6 | TRUE | ₱100 + ₱200 = ₱300 | SPLIT! 6hr ext + 6hr orig |

**Step 7 Breakdown:**
```
Position: 18
Extension: +12
Total: 30 (crosses 24!)

Split:
├── To reach 24: 24 - 18 = 6 hours → Extension rate ₱100
├── After 24: 30 - 24 = 6 hours → Original rate ₱200
└── Total: ₱300

New position: 6
Flag: TRUE (crossed threshold)
```

---

## Special Case: Check-in with Full Cycle

**If guest checks in for 24 hours (equal to reset threshold):**

```
Check-in: 24 hours
Cycle position: 24

But wait... 24 >= 24!

The while loop runs:
├── 24 - 24 = 0
├── next_extension_is_original = TRUE
└── Cycle position: 0

Result: Guest's FIRST extension will be original rate!
```

This matches the screenshot: *"if the guest checked in for 24hours then the next extension will be the original rate"*

---

## Visual: The Reset Cycle

```
                    ┌──────── CYCLE 1 (24 hrs) ────────┐
                    │                                   │
CHECK-IN ──────────►│ Extension rates apply             │──── RESET! ────┐
    12hr            │ (cheap: ₱100/6hr)                 │                │
    ₱350            │                                   │                │
                    │ Position: 12 → 18 → 24            │                │
                    └───────────────────────────────────┘                │
                                                                         │
                    ┌──────── CYCLE 2 (next 24 hrs) ────┐                │
                    │                                   │                │
                    │ First extension: ORIGINAL rate ◄──┼────────────────┘
                    │ (full price: ₱200/6hr)            │
                    │                                   │
                    │ Then back to extension rates      │
                    │ until next reset...               │
                    │                                   │
                    └───────────────────────────────────┘
```

---

## The Problems in Current Code

### Problem 1: Confusing Field Names

```
number_of_hours     ← Sounds like "total hours stayed"
                       Actually means "cycle position"

hours_stayed        ← This is the REAL original hours
                       But name sounds similar!
```

### Problem 2: Missing Extension Rate

```
Position: 20
Extension: +6
Hours to threshold: 24 - 20 = 4

System looks for: ExtensionRate where hour = 4
But if no 4-hour extension rate exists... ₱0 charge!
```

**Line 140:** `$extend = ExtensionRate::where('hour', $extend_hour)->first();`

If this returns `null`, the `extended_amount` becomes 0.

### Problem 3: Original Rate Not Found

```
Guest extends +6 hours after reset
System looks for: Rate where staying_hour.number = 6

But if branch only has 3hr, 12hr, 24hr rates (no 6hr)...
Rate not found! ₱0 charge!
```

**Line 119:** `$rate = Rate::where(...)->first();`

If this returns `null`, the `initial_amount` becomes 0.

### Problem 4: While Loop Charges Once

```php
while ($total_hours >= $this->extension_time_reset) {
    $total_hours = $total_hours - $this->extension_time_reset;
    $next_extension_is_original = true;
}
```

If guest extends +48 hours (crosses threshold twice):
- Loop runs twice
- But charge is calculated only once (before save)
- Guest pays for one crossing, not two

---

## Summary: The Reset Rules

| Rule | Description |
|------|-------------|
| **Threshold** | Set by branch (e.g., 24 hours) |
| **Cycle Position** | Tracks hours in current cycle (0 to threshold) |
| **Reset Trigger** | When cycle position >= threshold |
| **Reset Effect** | Position resets to remainder, flag set TRUE |
| **Flag Effect** | Next extension charges ORIGINAL rate (full price) |
| **Split Charge** | When extension crosses threshold: part extension + part original |
| **Dropdown Filter** | After reset, only show hours that have original rates |

---

## V4 Recommendation

**Option A: Simplify**
- Remove cycle reset entirely
- Always charge extension rates
- Simpler but hotel may lose money

**Option B: Per-Room Extension Rates**
- Extension rates proportional to room price
- ₱700 room has higher extension rates than ₱400 room
- Still keeps reset logic

**Option C: Fixed Multiplier After Threshold**
- After 24 hours: extension rate × 1.5 or × 2
- No complex split calculations
- Simpler than current logic

**Option D: Keep Current Logic, Fix Bugs**
- Add validation: ensure all gap hours have extension rates
- Add fallback: if rate not found, use nearest rate
- Improve naming: `cycle_position` instead of `number_of_hours`
- Better UI: explain to staff why price is what it is
