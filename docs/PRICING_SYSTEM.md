# Pricing System Documentation

This document explains how room pricing works across Alma's hotel branches and proposes a flexible V4 pricing model.

---

## The Problem: One Size Doesn't Fit All

In V3, pricing is based on **room type** only:

```
Standard Room + 3 hours = ₱500
Deluxe Room + 3 hours = ₱700
```

**But in reality**, different branches price rooms differently:

| Branch | Pricing Model |
|--------|---------------|
| Branch A | By **Room Type** (Standard, Deluxe, Suite) |
| Branch B | By **Floor** (1st floor cheaper, higher floors more expensive) |
| Branch C | By **Both** (Deluxe on 3rd floor costs more than Deluxe on 1st floor) |

---

## Pricing Model 1: By Room Type

**How it works:** All rooms of the same type have the same price, regardless of floor.

```
┌─────────────────────────────────────────────────────┐
│                    BRANCH A                          │
│              (Prices by Room Type)                   │
├─────────────────────────────────────────────────────┤
│                                                      │
│  3rd Floor:  [301 Standard ₱500] [302 Deluxe ₱700]  │
│  2nd Floor:  [201 Standard ₱500] [202 Deluxe ₱700]  │
│  1st Floor:  [101 Standard ₱500] [102 Deluxe ₱700]  │
│                                                      │
│  Same type = Same price (floor doesn't matter)       │
└─────────────────────────────────────────────────────┘
```

**Rate Table:**
| Room Type | 3 Hours | 6 Hours | 12 Hours |
|-----------|---------|---------|----------|
| Standard  | ₱500    | ₱800    | ₱1,200   |
| Deluxe    | ₱700    | ₱1,000  | ₱1,500   |
| Suite     | ₱1,000  | ₱1,500  | ₱2,200   |

**Use case:** Hotels where room quality/size is the main differentiator.

---

## Pricing Model 2: By Floor

**How it works:** All rooms on the same floor have the same price, regardless of type.

```
┌─────────────────────────────────────────────────────┐
│                    BRANCH B                          │
│                (Prices by Floor)                     │
├─────────────────────────────────────────────────────┤
│                                                      │
│  3rd Floor:  [301 ₱700] [302 ₱700] [303 ₱700]       │  ← Premium floor
│  2nd Floor:  [201 ₱600] [202 ₱600] [203 ₱600]       │  ← Mid floor
│  1st Floor:  [101 ₱500] [102 ₱500] [103 ₱500]       │  ← Budget floor
│                                                      │
│  Same floor = Same price (type doesn't matter)       │
└─────────────────────────────────────────────────────┘
```

**Rate Table:**
| Floor | 3 Hours | 6 Hours | 12 Hours |
|-------|---------|---------|----------|
| 1st   | ₱500    | ₱800    | ₱1,200   |
| 2nd   | ₱600    | ₱900    | ₱1,400   |
| 3rd   | ₱700    | ₱1,000  | ₱1,600   |

**Use case:** Hotels where higher floors have better views, less noise, or are considered more premium.

---

## Pricing Model 3: By Room Type + Floor (Combined)

**How it works:** Price varies by BOTH room type AND floor.

```
┌─────────────────────────────────────────────────────┐
│                    BRANCH C                          │
│            (Prices by Type + Floor)                  │
├─────────────────────────────────────────────────────┤
│                                                      │
│  3rd Floor:  [301 Standard ₱600] [302 Deluxe ₱900]  │
│  2nd Floor:  [201 Standard ₱550] [202 Deluxe ₱800]  │
│  1st Floor:  [101 Standard ₱500] [102 Deluxe ₱700]  │
│                                                      │
│  Deluxe 3rd floor > Deluxe 1st floor                 │
│  Standard 3rd floor > Standard 1st floor             │
└─────────────────────────────────────────────────────┘
```

**Rate Table:**
| Room Type | Floor | 3 Hours | 6 Hours | 12 Hours |
|-----------|-------|---------|---------|----------|
| Standard  | 1st   | ₱500    | ₱800    | ₱1,200   |
| Standard  | 2nd   | ₱550    | ₱850    | ₱1,300   |
| Standard  | 3rd   | ₱600    | ₱900    | ₱1,400   |
| Deluxe    | 1st   | ₱700    | ₱1,000  | ₱1,500   |
| Deluxe    | 2nd   | ₱800    | ₱1,100  | ₱1,700   |
| Deluxe    | 3rd   | ₱900    | ₱1,200  | ₱1,900   |

**Use case:** Hotels where both room quality AND floor level affect perceived value.

---

## Pricing Model 4: Per Individual Room

**How it works:** Each room has its own unique price (most flexible, most complex).

```
┌─────────────────────────────────────────────────────┐
│                    BRANCH D                          │
│              (Prices per Room)                       │
├─────────────────────────────────────────────────────┤
│                                                      │
│  [301 ₱650]  [302 ₱900]  [303 ₱1,200 - Corner Suite]│
│  [201 ₱550]  [202 ₱800]  [203 ₱600 - Near elevator] │
│  [101 ₱500]  [102 ₱700]  [103 ₱480 - No window]     │
│                                                      │
│  Each room priced individually                       │
└─────────────────────────────────────────────────────┘
```

**Use case:**
- Rooms with special features (corner room, balcony, jacuzzi)
- Rooms with drawbacks (no window, near elevator noise)
- Very customized pricing needs

**Note:** V3 partially supports this with `rates.room_id` but it's awkward.

---

## V3 Schema: How It Works Now

```
rates table:
├── branch_id
├── type_id (FK to types)        ← Room type determines price
├── staying_hour_id (FK)         ← Hours determine price
├── room_id (nullable)           ← Optional per-room override
└── amount
```

**Problem:** The schema assumes pricing is primarily by type, with room-level as an exception. This doesn't cleanly support floor-based pricing.

**Workaround people use:** Create "fake" room types like:
- "Standard 1st Floor"
- "Standard 2nd Floor"
- "Standard 3rd Floor"

This is messy and confusing.

---

## V4 Proposal: Flexible Pricing Model

### Option A: Branch-Level Pricing Mode

Add a setting to `branches` table:

```sql
branches.pricing_mode ENUM('by_type', 'by_floor', 'by_type_and_floor', 'by_room')
```

Then the `rates` table adapts:

| pricing_mode | How rates are looked up |
|--------------|------------------------|
| `by_type` | Match `type_id` + `staying_hour_id` |
| `by_floor` | Match `floor_id` + `staying_hour_id` |
| `by_type_and_floor` | Match `type_id` + `floor_id` + `staying_hour_id` |
| `by_room` | Match `room_id` + `staying_hour_id` |

**New rates table:**
```sql
rates:
├── id
├── branch_id
├── staying_hour_id (required)
├── type_id (nullable)           ← Used when pricing_mode includes 'type'
├── floor_id (nullable)          ← Used when pricing_mode includes 'floor'
├── room_id (nullable)           ← Used when pricing_mode = 'by_room'
├── amount (decimal)
└── is_available (boolean)
```

---

### Option B: Rate Groups (More Flexible)

Create a "rate group" concept that can group rooms however the branch wants:

```sql
rate_groups:
├── id
├── branch_id
├── name (e.g., "Budget Rooms", "Premium Floor", "Standard Type")
└── description

rate_group_rooms (pivot):
├── rate_group_id
├── room_id

rates:
├── id
├── branch_id
├── rate_group_id (FK)           ← Price applies to this group
├── staying_hour_id
└── amount
```

**Benefits:**
- Maximum flexibility
- Branch can group rooms however they want
- Same room could be in multiple groups (careful!)

**Drawbacks:**
- More complex to set up
- Admin UI needs to be intuitive

---

### Option C: Room-Level Pricing Only (Simplest)

Every room has its own rates. Period.

```sql
rates:
├── id
├── room_id (required, FK)       ← Every rate is per-room
├── staying_hour_id
└── amount
```

**Setup process:**
1. Admin creates rooms
2. Admin sets rate for each room + hour combination
3. System can "copy" rates from template (e.g., "Apply Standard pricing to rooms 101-110")

**Benefits:**
- Simplest to understand
- Most flexible
- No ambiguity about which rate applies

**Drawbacks:**
- More rows in rates table
- Need bulk operations for setup

---

## Kiosk Impact: How Does Pricing Mode Affect Guest Selection?

### If pricing is by TYPE:

```
┌─────────────────────────────────┐
│  Select Room Type:              │
│                                 │
│  ┌─────────┐ ┌─────────┐       │
│  │Standard │ │ Deluxe  │       │
│  │  ₱500   │ │  ₱700   │       │
│  └─────────┘ └─────────┘       │
│                                 │
│  (Guest picks type, staff       │
│   assigns actual room)          │
└─────────────────────────────────┘
```

### If pricing is by FLOOR:

```
┌─────────────────────────────────┐
│  Select Floor:                  │
│                                 │
│  ┌─────────┐ ┌─────────┐       │
│  │1st Floor│ │2nd Floor│       │
│  │  ₱500   │ │  ₱600   │       │
│  └─────────┘ └─────────┘       │
│  ┌─────────┐                   │
│  │3rd Floor│                   │
│  │  ₱700   │                   │
│  └─────────┘                   │
│                                 │
│  (Guest picks floor, staff      │
│   assigns actual room)          │
└─────────────────────────────────┘
```

### If pricing is by TYPE + FLOOR:

```
┌─────────────────────────────────┐
│  Select Room:                   │
│                                 │
│  Standard:                      │
│  [1F ₱500] [2F ₱550] [3F ₱600] │
│                                 │
│  Deluxe:                        │
│  [1F ₱700] [2F ₱800] [3F ₱900] │
│                                 │
│  (Guest picks type+floor,       │
│   staff assigns actual room)    │
└─────────────────────────────────┘
```

---

## Recommendation for V4

**Use Option A (Branch-Level Pricing Mode)** because:

1. **Covers all real-world cases** across Alma's branches
2. **Branch-configurable** — each branch picks their pricing model
3. **Kiosk adapts automatically** — shows type OR floor OR both based on branch setting
4. **Simpler than rate groups** — no complex grouping logic
5. **Cleaner than per-room only** — avoids duplicate data when rooms share prices

### Proposed Schema:

```sql
-- Branch setting
ALTER TABLE branches ADD COLUMN pricing_mode
    ENUM('by_type', 'by_floor', 'by_type_and_floor', 'by_room')
    DEFAULT 'by_type';

-- Updated rates table
CREATE TABLE rates (
    id BIGINT PRIMARY KEY,
    branch_id BIGINT NOT NULL,
    staying_hour_id BIGINT NOT NULL,

    -- Nullable based on pricing_mode
    type_id BIGINT NULL,
    floor_id BIGINT NULL,
    room_id BIGINT NULL,

    amount DECIMAL(10,2) NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    -- Constraints ensure correct columns are filled based on mode
    -- (enforced in application layer)
);
```

### Rate Lookup Logic:

```php
public function getRateForRoom(Room $room, StayingHour $hour): ?Rate
{
    $branch = $room->branch;

    return match($branch->pricing_mode) {
        'by_type' => Rate::where('type_id', $room->type_id)
                         ->where('staying_hour_id', $hour->id)
                         ->first(),

        'by_floor' => Rate::where('floor_id', $room->floor_id)
                          ->where('staying_hour_id', $hour->id)
                          ->first(),

        'by_type_and_floor' => Rate::where('type_id', $room->type_id)
                                   ->where('floor_id', $room->floor_id)
                                   ->where('staying_hour_id', $hour->id)
                                   ->first(),

        'by_room' => Rate::where('room_id', $room->id)
                         ->where('staying_hour_id', $hour->id)
                         ->first(),
    };
}
```

---

## Summary

| Model | Price Based On | Best For |
|-------|----------------|----------|
| By Type | Room type (Standard/Deluxe) | Hotels where room size/quality matters most |
| By Floor | Floor level | Hotels where higher floors = premium |
| By Type + Floor | Both | Hotels where both matter |
| By Room | Individual room | Maximum flexibility, special rooms |

**V4 Solution:** Add `branches.pricing_mode` setting so each branch can choose their model. The rates table supports all modes with nullable foreign keys.
