# Discount System Documentation

This document explains how discounts work in V3 and proposes improvements for V4.

---

## What is the Discount System?

The discount system allows hotels to offer **price reductions** to guests under certain conditions (senior citizens, PWDs, promos, etc.).

```
┌─────────────────────────────────────────────────────────────────┐
│                     DISCOUNT EXAMPLE                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Room Rate:          ₱500                                       │
│  Additional Charges: ₱100 (towels, pillows)                     │
│  ─────────────────────────────                                  │
│  Subtotal:           ₱600                                       │
│                                                                  │
│  Discount Applied:   -₱50  (Senior Citizen)                     │
│  ─────────────────────────────                                  │
│  TOTAL:              ₱550                                       │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## V3 Implementation: Two Discount Systems

### System 1: Named Discounts (Mostly Unused)

The `discounts` table stores named discount types:

```sql
discounts:
├── id
├── branch_id
├── name          -- e.g., "Senior Citizen", "PWD", "Employee"
├── description
├── amount        -- The discount value
├── is_percentage -- TRUE = percentage, FALSE = fixed peso
├── is_available  -- Toggle availability
└── created_at
```

**Example Data:**
| Name | Description | Amount | Is Percentage |
|------|-------------|--------|---------------|
| Senior Citizen | 20% off for ages 60+ | 20 | TRUE |
| PWD | Persons with disability | 15 | TRUE |
| Promo | Weekend special | 100 | FALSE (₱100 flat) |

**Problem:** This table EXISTS but is NOT actively used in check-in flow. Admin can create these discounts, but they're never applied.

---

### System 2: Branch Discount (Actually Used)

The branch has a simple ON/OFF discount setting:

```sql
branches:
├── discount_enabled  -- Boolean: Is discount feature on?
└── discount_amount   -- Flat peso amount (minimum ₱50)
```

**How It Works:**

```
1. Admin enables discount in Branch Settings
   └── discount_enabled = TRUE
   └── discount_amount = 50 (₱50)

2. Admin marks certain rates as discount-eligible
   └── rates.has_discount = TRUE

3. Guest at Kiosk:
   ├── Selects room and rate
   ├── IF rate.has_discount = TRUE AND branch.discount_enabled = TRUE:
   │   └── Guest sees option to apply discount
   └── IF guest applies discount:
       └── Total = (room_rate + charges) - ₱50
```

**Code Flow (Kiosk Check-In):**

```php
// In Kiosk\CheckIn.php (line 159-161)
if ($rate->has_discount && auth()->user()->branch->discount_enabled) {
    $this->discount_available = true;  // Show discount option
}

// When guest applies discount (line 333-341)
public function applyDiscount()
{
   if($this->discountEnabled) {
    $this->discount_amount = auth()->user()->branch->discount_amount;
   } else {
    $this->discount_amount = 0;
   }
}

// Saved to guest record (line 307-308)
'has_discount' => $this->discountEnabled,
'discount_amount' => $this->discountEnabled ? $this->discount_amount : 0,
```

---

## Where Discounts are Stored

### On the Guest Record

```sql
guests:
├── has_discount     -- Boolean: Did this guest get a discount?
└── discount_amount  -- String: The amount discounted (e.g., "50")
```

**Problem:** Amount is stored as STRING, not DECIMAL.

### On Rates

```sql
rates:
└── has_discount  -- Boolean: Is this rate eligible for discount?
```

**Use Case:** Some rates might be already promotional and shouldn't stack with additional discounts.

---

## The Complete Discount Flow

```
┌─────────────────────────────────────────────────────────────────────────┐
│                        DISCOUNT APPLICATION FLOW                         │
└─────────────────────────────────────────────────────────────────────────┘

SETUP (Admin):
┌──────────────────────────────────────────────────────────────────────┐
│ 1. Enable discount in Branch Settings                                │
│    └── discount_enabled = TRUE, discount_amount = ₱50               │
│                                                                      │
│ 2. Mark rates as discount-eligible                                   │
│    └── Rate "3hr Standard" → has_discount = TRUE                    │
│    └── Rate "Promo Package" → has_discount = FALSE (already promo)  │
└──────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
KIOSK CHECK-IN:
┌──────────────────────────────────────────────────────────────────────┐
│ 1. Guest selects room                                                │
│ 2. Guest selects rate (e.g., 3hr Standard - ₱500)                   │
│ 3. System checks: rate.has_discount && branch.discount_enabled      │
│    └── YES: Show discount toggle                                    │
│    └── NO: No discount option                                       │
│ 4. Guest enables discount toggle                                     │
│ 5. System calculates: Total = ₱500 - ₱50 = ₱450                    │
│ 6. Guest submits → saved to guest record                            │
└──────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
FRONTDESK CONFIRMATION:
┌──────────────────────────────────────────────────────────────────────┐
│ 1. Frontdesk sees kiosk request                                      │
│ 2. guest.has_discount = TRUE, guest.discount_amount = 50            │
│ 3. Total displayed with discount already applied                     │
│ 4. Frontdesk collects ₱450 (not ₱500)                               │
└──────────────────────────────────────────────────────────────────────┘
```

---

## Problems with V3 Discount System

### Problem 1: Named Discounts are Unused

```
discounts table has Senior Citizen, PWD, etc.
BUT the kiosk only uses branch.discount_amount

Result: All discounts are the same ₱50 flat amount
       No way to track WHY discount was given
```

### Problem 2: No Discount Reason Tracking

```
Guest Record:
├── has_discount = TRUE
├── discount_amount = "50"
└── discount_reason = ???  ← MISSING!

Reports can't tell:
- How many senior discounts were given?
- How many PWD discounts?
- Which promo codes were used?
```

### Problem 3: Discount Amount is STRING

```sql
guests.discount_amount VARCHAR  -- Should be DECIMAL!
```

### Problem 4: Only Flat Amount (No Percentage in Practice)

```
discounts.is_percentage EXISTS in schema
BUT branch.discount_amount is always flat peso

Senior Citizens legally get 20% off
But system only supports "₱50 off"
```

### Problem 5: Rate-Level Flag is Awkward

```
rates.has_discount = TRUE means "eligible for discount"

Why would a rate NOT be eligible?
- If it's already a promo rate?
- If it's a premium rate?

This creates confusion.
```

### Problem 6: No Validation of Discount Eligibility

```
Guest claims "Senior Citizen" discount
But no ID verification required
Anyone can toggle the discount on

Better: Frontdesk verifies ID before confirming
```

---

## V4 Proposal: Improved Discount System

### Option A: Simple Discount (Per Transaction)

Keep it simple but fix the issues:

```sql
discounts:
├── id
├── branch_id
├── name                -- "Senior Citizen", "PWD", "Promo"
├── description
├── discount_type       -- ENUM('percentage', 'fixed')
├── value              -- DECIMAL: 20 (for 20%) or 50 (for ₱50)
├── is_active
├── requires_approval   -- TRUE = frontdesk must verify
├── min_amount          -- Minimum purchase for discount
├── max_discount        -- Cap for percentage discounts
└── timestamps

guest_discounts (pivot):
├── id
├── guest_id
├── discount_id
├── applied_by          -- User ID who approved the discount
├── original_amount     -- Amount before discount
├── discount_amount     -- Amount discounted
└── created_at
```

**Benefits:**
- Track which discount type was used
- Support both percentage and fixed discounts
- Know who approved the discount
- Audit trail for reports

### Option B: Discount Codes

Allow promo codes guests can enter:

```sql
discount_codes:
├── id
├── branch_id
├── code               -- "SUMMER2024", "SENIOR", "PWD20"
├── discount_id        -- FK to discounts table
├── usage_limit        -- Max times code can be used
├── times_used         -- Current usage count
├── valid_from
├── valid_until
└── is_active
```

**Use Case:**
- Marketing promos: "Use code SUMMER2024 for 10% off"
- Pre-registered discounts: "Enter SENIOR for 20% discount"

### Option C: Stackable Discounts

Allow multiple discounts per transaction:

```sql
transaction_discounts:
├── id
├── transaction_id
├── discount_id
├── sequence          -- Order of application (1st, 2nd, etc.)
├── base_amount       -- Amount before THIS discount
├── discount_amount   -- Amount THIS discount reduced
└── resulting_amount  -- Amount after THIS discount
```

**Example:**
```
Base:                  ₱1,000
1. Senior (20%):       -₱200 → ₱800
2. Loyalty (₱50):      -₱50  → ₱750
Total Discount:        ₱250
```

**Warning:** Stackable discounts are complex. May not be necessary.

---

## V4 Recommended Schema

### Discounts Table (Enhanced)

```sql
CREATE TABLE discounts (
    id BIGINT PRIMARY KEY,
    branch_id BIGINT NOT NULL,

    -- Identity
    name VARCHAR(100) NOT NULL,         -- "Senior Citizen"
    code VARCHAR(50) NULL UNIQUE,       -- Optional promo code
    description TEXT,

    -- Discount Rules
    type ENUM('percentage', 'fixed') NOT NULL,
    value DECIMAL(10,2) NOT NULL,       -- 20.00 (%) or 50.00 (₱)
    max_discount DECIMAL(10,2) NULL,    -- Cap for percentage
    min_purchase DECIMAL(10,2) DEFAULT 0,

    -- Validation
    requires_verification BOOLEAN DEFAULT FALSE,  -- Needs ID check?

    -- Availability
    is_active BOOLEAN DEFAULT TRUE,
    valid_from DATETIME NULL,
    valid_until DATETIME NULL,
    usage_limit INT NULL,               -- NULL = unlimited
    times_used INT DEFAULT 0,

    -- Applicability
    applies_to ENUM('room', 'extension', 'pos', 'all') DEFAULT 'all',

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Applied Discounts Table

```sql
CREATE TABLE applied_discounts (
    id BIGINT PRIMARY KEY,

    -- What was discounted
    discountable_type VARCHAR(100),     -- 'App\Models\Transaction', 'App\Models\StayExtension'
    discountable_id BIGINT,

    -- Which discount
    discount_id BIGINT NOT NULL,

    -- Calculation
    original_amount DECIMAL(10,2) NOT NULL,
    discount_type ENUM('percentage', 'fixed'),
    discount_value DECIMAL(10,2),       -- The rate (20% or ₱50)
    discount_amount DECIMAL(10,2),      -- The actual amount deducted
    final_amount DECIMAL(10,2) NOT NULL,

    -- Audit
    applied_by BIGINT NULL,             -- User who applied (NULL if kiosk)
    verified_by BIGINT NULL,            -- User who verified ID
    notes TEXT NULL,                    -- "ID #123456 verified"

    created_at TIMESTAMP
);
```

---

## Discount Application Flow (V4)

```
┌─────────────────────────────────────────────────────────────────────────┐
│                        V4 DISCOUNT FLOW                                  │
└─────────────────────────────────────────────────────────────────────────┘

KIOSK:
┌──────────────────────────────────────────────────────────────────────┐
│ 1. Guest selects room and rate                                       │
│ 2. Guest sees available discounts:                                   │
│    ┌─────────────────────────────────────────────────────────┐      │
│    │  [ ] Senior Citizen (20% off) - Requires ID at counter  │      │
│    │  [ ] PWD (15% off) - Requires ID at counter             │      │
│    │  [ ] Enter Promo Code: [________]                       │      │
│    └─────────────────────────────────────────────────────────┘      │
│ 3. Guest selects "Senior Citizen"                                    │
│ 4. Kiosk shows: "Please present valid Senior ID at counter"         │
│ 5. Request submitted with discount_id attached                       │
└──────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
FRONTDESK:
┌──────────────────────────────────────────────────────────────────────┐
│ 1. Request shows: "Guest requested Senior Citizen discount"         │
│ 2. Frontdesk asks for Senior ID                                     │
│ 3. IF ID valid:                                                      │
│    └── Click "Verify Discount" → verified_by = staff_id             │
│    IF ID invalid or no ID:                                          │
│    └── Click "Remove Discount" → discount removed                   │
│ 4. Process payment with verified discount                            │
└──────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
RECORD:
┌──────────────────────────────────────────────────────────────────────┐
│ applied_discounts:                                                   │
│ ├── transaction_id: 12345                                           │
│ ├── discount_id: 1 (Senior Citizen)                                 │
│ ├── original_amount: 500.00                                         │
│ ├── discount_type: percentage                                       │
│ ├── discount_value: 20.00                                           │
│ ├── discount_amount: 100.00                                         │
│ ├── final_amount: 400.00                                            │
│ ├── applied_by: NULL (kiosk)                                        │
│ └── verified_by: 5 (staff who checked ID)                           │
└──────────────────────────────────────────────────────────────────────┘
```

---

## Reports Possible with V4

### Discount Usage Report

```
┌─────────────────────────────────────────────────────────────────┐
│  DISCOUNT USAGE REPORT - March 2024                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Discount Type      │ Count │ Total Discount │ Revenue Lost    │
│  ───────────────────┼───────┼────────────────┼─────────────────│
│  Senior Citizen     │   45  │    ₱9,000      │   ₱9,000        │
│  PWD                │   12  │    ₱1,800      │   ₱1,800        │
│  SUMMER2024 Promo   │   89  │    ₱8,900      │   ₱8,900        │
│  ───────────────────┼───────┼────────────────┼─────────────────│
│  TOTAL              │  146  │   ₱19,700      │  ₱19,700        │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### Verification Audit Report

```
┌─────────────────────────────────────────────────────────────────┐
│  DISCOUNT VERIFICATION AUDIT                                     │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Date       │ Discount      │ Verified By │ Amount │ Status    │
│  ───────────┼───────────────┼─────────────┼────────┼───────────│
│  Mar 1      │ Senior        │ Maria       │ ₱200   │ Verified  │
│  Mar 1      │ PWD           │ Juan        │ ₱150   │ Verified  │
│  Mar 2      │ Senior        │ Maria       │ ₱200   │ Removed*  │
│                                                                  │
│  * Guest could not present valid ID                              │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## Summary: V3 vs V4

| Aspect | V3 | V4 |
|--------|----|----|
| **Discount Types** | Only flat ₱ amount | Percentage AND fixed |
| **Named Discounts** | Exists but unused | Actively used |
| **Tracking** | Only has_discount flag | Full discount_id + audit |
| **Verification** | None | Optional ID verification |
| **Reports** | "How many got discount?" | "Who got which discount, verified by whom?" |
| **Promo Codes** | Not supported | Optional feature |
| **Data Type** | String amount | Decimal amount |
| **Applies To** | Room rate only | Room, extension, POS, or all |

---

## V4 Recommendation

**Use the Simple Discount model (Option A):**

1. Create `discounts` table with proper types (percentage/fixed)
2. Create `applied_discounts` table for tracking
3. Add verification workflow for discounts requiring ID
4. Generate proper reports

**Skip promo codes for now** — can add later if marketing needs it.

**Skip stackable discounts** — adds complexity without clear benefit.
