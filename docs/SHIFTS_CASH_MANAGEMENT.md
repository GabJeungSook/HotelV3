# Shifts & Cash Management Documentation

This document explains how frontdesk shifts and cash management work in V3.

---

## Overview: What is a Shift?

A **shift** is a work period for frontdesk staff. Before they can process check-ins or transactions, they must:

1. **Start their shift** (clock in)
2. **Select a cash drawer**
3. **Enter beginning cash** (how much money is in the drawer)

At the end of their shift, they must:

1. **Enter ending cash** (count the drawer)
2. **Reconcile** (expected vs actual)
3. **End shift** (clock out)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           SHIFT LIFECYCLE                                    │
└─────────────────────────────────────────────────────────────────────────────┘

  LOGIN          START SHIFT         DURING SHIFT              END SHIFT
  ─────          ───────────         ────────────              ─────────

┌─────────┐    ┌─────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ Login   │───►│Select Drawer│───►│ Process Guest   │───►│ Count Cash      │
│         │    │Enter Cash   │    │ Check-ins       │    │ Enter Ending    │
└─────────┘    └─────────────┘    │ Extensions      │    │ Reconcile       │
                                  │ Payments        │    │ Clock Out       │
                                  │ Remittances     │    └─────────────────┘
                                  └─────────────────┘
```

---

## Why Track Shifts & Cash?

### Business Reasons

| Reason | Explanation |
|--------|-------------|
| **Accountability** | Know WHO handled each transaction |
| **Cash Control** | Track money coming in and going out |
| **Shortage Detection** | Find discrepancies immediately |
| **Reporting** | Daily/shift-based sales reports |
| **Audit Trail** | Evidence if money goes missing |

### The Problem Without Shift Tracking

```
Without shifts:
- "Who took this payment?" → Unknown
- "Why is ₱500 missing?" → Can't trace
- "What were today's sales?" → Must count everything

With shifts:
- "Who took this payment?" → Juan, AM shift
- "Why is ₱500 missing?" → Juan's shift was ₱500 short
- "What were today's sales?" → AM: ₱15,000, PM: ₱12,000
```

---

## V3 Tables Involved

Based on your screenshot and the codebase:

### 1. `shifts` Table (Your Screenshot)

This appears to be the simplified shift record:

| Column | Type | Purpose |
|--------|------|---------|
| `id` | bigint | Primary key |
| `branch_id` | foreignId | Which branch |
| `cashier_id` | foreignId | Which frontdesk user |
| `opened_at` | datetime | When shift started |
| `closed_at` | datetime | When shift ended (NULL if open) |
| `opening_cash` | decimal | Cash at start of shift |
| `closing_cash` | decimal | Cash at end of shift |
| `expected_cash` | decimal | System-calculated expected amount |
| `difference` | decimal | closing_cash - expected_cash |
| `status` | string | 'open' or 'closed' |

**Example from your screenshot:**
```
Shift #1: Opened 10:33, Closed 15:29
├── Opening: ₱1,111.00
├── Closing: ₱36,110.00
├── Expected: ₱36,111.00
├── Difference: -₱1.00 (₱1 SHORT!)
└── Status: closed

Shift #2: Opened 15:30 (current shift)
├── Opening: ₱1,000.00
├── Status: open
└── (Still working...)
```

### 2. `shift_logs` Table

More detailed shift tracking:

| Column | Type | Purpose |
|--------|------|---------|
| `id` | bigint | Primary key |
| `branch_id` | foreignId | Which branch |
| `frontdesk_id` | foreignId | Which user |
| `cash_drawer_id` | foreignId | Which physical drawer |
| `time_in` | datetime | Shift start |
| `time_out` | datetime | Shift end (NULL if active) |
| `frontdesk_ids` | json | Partner info (if working with someone) |
| `shift` | string | 'AM' or 'PM' |
| `beginning_cash` | decimal | Starting cash |
| `end_cash` | decimal | Ending cash |
| `total_expenses` | decimal | Expenses during shift |
| `total_remittances` | decimal | Cash handed to management |
| `description` | text | Notes |

### 3. `cash_drawers` Table

Physical cash registers:

| Column | Type | Purpose |
|--------|------|---------|
| `id` | bigint | Primary key |
| `branch_id` | foreignId | Which branch |
| `name` | string | "Drawer 1", "Drawer 2" |
| `is_active` | boolean | Currently in use? |

### 4. `cash_on_drawers` Table

Every cash movement:

| Column | Type | Purpose |
|--------|------|---------|
| `id` | bigint | Primary key |
| `branch_id` | foreignId | Which branch |
| `cash_drawer_id` | foreignId | Which drawer |
| `transaction_type` | string | 'payment', 'deposit', 'refund' |
| `amount` | decimal | Amount added/removed |
| `deduction` | decimal | Deductions (deposit refunds) |
| `transaction_date` | date | When |

### 5. `remittances` Table

Cash handed to management:

| Column | Type | Purpose |
|--------|------|---------|
| `id` | bigint | Primary key |
| `branch_id` | foreignId | Which branch |
| `user_id` | foreignId | Who remitted |
| `shift_log_id` | foreignId | Which shift |
| `total_remittance` | decimal | Amount |
| `description` | string | Notes |

### 6. `frontdesk_shifts` Table

Detailed shift report (for back-office):

```sql
-- Cash Drawer Section
├── opening_cash_amount, opening_cash_sub_amount
├── key_amount (key deposits)
├── guest_deposit_amount
├── forwarding_balance_amount
└── total_cash_amount

-- Operations Section A (Income)
├── new_check_in_number, new_check_in_amount
├── extension_number, extension_amount
├── transfer_number, transfer_amount
├── miscellaneous_number, miscellaneous_amount
├── food_number, food_amount
├── drink_number, drink_amount
└── total_number, total_amount

-- Operations Section B (Outgoing)
├── forwarded_room_check_in_number/amount
├── key_remote_number/amount
├── forwarded_guest_deposit_number/amount
├── current_guest_deposit_number/amount
├── total_check_out_number/amount
└── expenses_number/amount

-- Final Sales
├── gross_sales
├── refund
├── expenses
├── discount
└── net_sales

-- Cash Reconciliation
├── expected_cash
├── actual_cash
└── difference
```

---

## The Complete Shift Flow

### Step 1: Start Shift

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           START SHIFT FLOW                                   │
└─────────────────────────────────────────────────────────────────────────────┘

FRONTDESK LOGS IN:
┌──────────────────────────────────────────────────────────────────────────┐
│ 1. User logs in with email/password                                      │
│ 2. System checks: Does user have role 'frontdesk'?                      │
│ 3. Redirect to: Assign Frontdesk page                                    │
└──────────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
SELECT PARTNER (Optional):
┌──────────────────────────────────────────────────────────────────────────┐
│  ASSIGN FRONTDESK                                                        │
│  ────────────────                                                        │
│                                                                          │
│  Working with a partner today?                                           │
│  ┌─────────────────────────────────────────────────┐                    │
│  │ [ ] Maria Santos (Frontdesk #2)                 │                    │
│  │ [ ] Pedro Cruz (Frontdesk #3)                   │                    │
│  └─────────────────────────────────────────────────┘                    │
│                                                                          │
│  Or enter partner name: [________________]                               │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
SELECT CASH DRAWER:
┌──────────────────────────────────────────────────────────────────────────┐
│  SELECT CASH DRAWER                                                      │
│  ─────────────────                                                       │
│                                                                          │
│  Available drawers:                                                      │
│  ┌─────────────────────────────────────────────────┐                    │
│  │ ( ) Drawer 1 - Counter Left                     │                    │
│  │ (●) Drawer 2 - Counter Right                    │                    │
│  │ [ ] Drawer 3 - Currently in use by Juan         │ ← Disabled         │
│  └─────────────────────────────────────────────────┘                    │
│                                                                          │
│  [ PROCEED ]                                                             │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
ENTER BEGINNING CASH:
┌──────────────────────────────────────────────────────────────────────────┐
│  BEGINNING CASH                                                          │
│  ──────────────                                                          │
│                                                                          │
│  Previous shift (Maria, ended 7:30 AM):                                  │
│  ├── Starting cash: ₱1,000.00                                           │
│  ├── Transactions: ₱15,000.00                                           │
│  ├── Deposits held: ₱3,000.00                                           │
│  └── Ending cash: ₱16,000.00                                            │
│                                                                          │
│  Count your drawer and enter amount:                                     │
│  Beginning Cash: [₱ 1,000.00    ]                                       │
│                                                                          │
│  [ START SHIFT ]                                                         │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘
```

**What happens in code:**

```php
// AssignedFrontdesk.php → saveFrontdesk()

// 1. Auto-close any stale shifts (forgotten clock-outs > 14 hours)
ShiftLog::whereNull('time_out')
    ->where('time_in', '<', now()->subHours(14))
    ->update(['time_out' => ...]);

// 2. Create new shift log
ShiftLog::create([
    'branch_id' => auth()->user()->branch_id,
    'frontdesk_id' => auth()->user()->id,
    'time_in' => now(),
    'cash_drawer_id' => $this->drawer,
    'shift' => 'AM' or 'PM',
]);

// 3. Mark drawer as active
CashDrawer::where('id', $this->drawer)->update(['is_active' => true]);

// 4. Update user
auth()->user()->update([
    'time_in' => now(),
    'cash_drawer_id' => $this->drawer,
]);
```

### Step 2: During Shift

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           DURING SHIFT                                       │
└─────────────────────────────────────────────────────────────────────────────┘

CASH COMES IN:                           CASH GOES OUT:
─────────────                            ──────────────

• Check-in payments                      • Deposit refunds
• Extension payments                     • Remittances (to management)
• Transfer fees                          • Expenses (supplies, etc.)
• Food/drink orders                      • Overage returns
• Deposit collections

         │                                       │
         ▼                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                         CASH ON DRAWER TRACKING                              │
│                                                                              │
│  Beginning Cash:    ₱1,000                                                  │
│  + Transactions:    ₱15,000  (check-ins, extensions, POS)                  │
│  + Deposits In:     ₱3,000   (collected from guests)                       │
│  - Deposits Out:    ₱2,500   (returned to guests)                          │
│  - Remittances:     ₱10,000  (handed to management)                        │
│  - Expenses:        ₱500     (bought supplies)                             │
│  ─────────────────────────────────────────────────                         │
│  Expected Cash:     ₱6,000                                                 │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

**Every transaction updates the drawer:**

```php
// When guest pays
CashOnDrawer::create([
    'branch_id' => $branch_id,
    'cash_drawer_id' => auth()->user()->cash_drawer_id,
    'transaction_type' => 'payment',
    'amount' => 500,
    'transaction_date' => now(),
]);
```

### Step 3: Remittance (During Shift)

When drawer has too much cash, frontdesk hands some to management:

```
┌──────────────────────────────────────────────────────────────────────────┐
│  REMITTANCE                                                              │
│  ──────────                                                              │
│                                                                          │
│  Current shift: AM                                                       │
│  Total remitted so far: ₱10,000                                         │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ Time       │ Amount    │ Description                              │ │
│  ├────────────┼───────────┼──────────────────────────────────────────┤ │
│  │ 11:30 AM   │ ₱5,000    │ First remittance to supervisor          │ │
│  │ 2:00 PM    │ ₱5,000    │ Second remittance                        │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  New Remittance:                                                         │
│  Amount: [₱ 3,000     ]                                                 │
│  Description: [Third remittance to supervisor]                          │
│                                                                          │
│  [ ADD REMITTANCE ]                                                      │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘
```

### Step 4: End Shift

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           END SHIFT FLOW                                     │
└─────────────────────────────────────────────────────────────────────────────┘

CASH ON HAND VIEW:
┌──────────────────────────────────────────────────────────────────────────┐
│  CASH ON HAND                                                            │
│  ────────────                                                            │
│                                                                          │
│  Beginning Cash:        ₱1,000.00                                       │
│  + Transactions:        ₱15,000.00                                      │
│  + Deposits In:         ₱3,000.00                                       │
│  - Deposits Out:        ₱2,500.00                                       │
│  - Remittances:         ₱10,000.00                                      │
│  - Expenses:            ₱500.00                                         │
│  ─────────────────────────────────────                                  │
│  EXPECTED IN DRAWER:    ₱6,000.00                                       │
│                                                                          │
│  Count your drawer:                                                      │
│  Ending Cash: [₱ 5,999.00   ]                                           │
│                                                                          │
│  Notes: [Short by ₱1 - coin dropped under counter]                      │
│                                                                          │
│  [ END SHIFT ]                                                           │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
ENTER PASSCODE:
┌──────────────────────────────────────────────────────────────────────────┐
│  AUTHORIZATION                                                           │
│  ─────────────                                                           │
│                                                                          │
│  Enter your passcode to confirm end of shift:                           │
│  Passcode: [****]                                                        │
│                                                                          │
│  [ CONFIRM ]                                                             │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
RECONCILIATION:
┌──────────────────────────────────────────────────────────────────────────┐
│  SHIFT SUMMARY                                                           │
│  ─────────────                                                           │
│                                                                          │
│  Shift: AM (10:33 AM - 3:29 PM)                                         │
│  Frontdesk: Juan Dela Cruz                                              │
│                                                                          │
│  Expected Cash:  ₱6,000.00                                              │
│  Actual Cash:    ₱5,999.00                                              │
│  ─────────────────────────────                                          │
│  Difference:     -₱1.00 (SHORT)                                         │
│                                                                          │
│  Status: ⚠️ Minor shortage detected                                     │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘
```

**What happens in code:**

```php
// CashOnHand.php → endShiftConfirm()

// 1. Verify passcode
if(auth()->user()->frontdesk->passcode !== $this->code) {
    // Error: incorrect passcode
}

// 2. Update shift log
$shift = ShiftLog::where('frontdesk_id', auth()->user()->id)
    ->whereNull('time_out')
    ->first();

$shift->end_cash = $this->remittance;  // Actually ending cash
$shift->total_expenses = $this->total_expenses;
$shift->total_remittances = $this->total_remittances;
$shift->time_out = now();  // Implicit
$shift->save();

// 3. Release cash drawer
$shift->cash_drawer->update(['is_active' => false]);

// 4. Logout
Auth::logout();
```

---

## Cash Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           CASH FLOW DURING SHIFT                             │
└─────────────────────────────────────────────────────────────────────────────┘

                              CASH DRAWER
                        ┌─────────────────────┐
                        │                     │
   BEGINNING CASH ─────►│  ₱1,000             │
                        │                     │
   CHECK-IN ───────────►│  +₱5,000            │
   EXTENSION ──────────►│  +₱2,000            │
   POS ────────────────►│  +₱3,000            │
   DEPOSIT IN ─────────►│  +₱2,000            │
                        │  ─────────          │
                        │  ₱13,000            │
                        │                     │
   DEPOSIT OUT ◄────────│  -₱1,500            │
   REMITTANCE ◄─────────│  -₱8,000            │
   EXPENSE ◄────────────│  -₱500              │
                        │  ─────────          │
                        │  ₱3,000             │◄──── EXPECTED ENDING
                        │                     │
                        └─────────────────────┘
                                  │
                                  ▼
                        ACTUAL COUNT: ₱2,999
                        DIFFERENCE: -₱1 (SHORT)
```

---

## Problems with V3 Shift System

### Problem 1: Multiple Overlapping Tables

```
shifts table ← Your screenshot
shift_logs table ← Main tracking
frontdesk_shifts table ← Detailed reports

Which is the source of truth?
```

### Problem 2: String Types for Money

```sql
frontdesk_shifts.opening_cash_amount VARCHAR   -- Should be DECIMAL
frontdesk_shifts.total_amount VARCHAR           -- Should be DECIMAL
```

### Problem 3: Manual Expected Calculation

```
Expected cash must be calculated by summing:
- Beginning cash
- All transactions
- All deposits in/out
- All remittances
- All expenses

If any are missed, reconciliation is wrong.
```

### Problem 4: Partner Tracking is JSON

```sql
shift_logs.frontdesk_ids JSON  -- ["FD001", "Partner Name"]

Hard to query: "Show all shifts where Maria was the partner"
```

---

## V4 Proposal: Simplified Shift System

### Single Shifts Table

```sql
CREATE TABLE shifts (
    id BIGINT PRIMARY KEY,
    branch_id BIGINT NOT NULL,

    -- Who
    user_id BIGINT NOT NULL,           -- Primary frontdesk
    partner_user_id BIGINT NULL,       -- Partner (if any)
    partner_name VARCHAR(100) NULL,    -- Or just a name

    -- When
    started_at DATETIME NOT NULL,
    ended_at DATETIME NULL,
    shift_type ENUM('AM', 'PM') NOT NULL,

    -- Cash Drawer
    cash_drawer_id BIGINT NOT NULL,
    opening_cash DECIMAL(10,2) NOT NULL,
    closing_cash DECIMAL(10,2) NULL,

    -- Calculated (updated in real-time)
    total_income DECIMAL(10,2) DEFAULT 0,      -- All money in
    total_deposits_in DECIMAL(10,2) DEFAULT 0,
    total_deposits_out DECIMAL(10,2) DEFAULT 0,
    total_remittances DECIMAL(10,2) DEFAULT 0,
    total_expenses DECIMAL(10,2) DEFAULT 0,

    -- Reconciliation
    expected_cash DECIMAL(10,2) GENERATED ALWAYS AS (
        opening_cash + total_income + total_deposits_in
        - total_deposits_out - total_remittances - total_expenses
    ) STORED,
    difference DECIMAL(10,2) NULL,

    -- Status
    status ENUM('active', 'closed') DEFAULT 'active',
    notes TEXT NULL,

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Shift Transactions Table

Link every transaction to a shift:

```sql
CREATE TABLE shift_transactions (
    id BIGINT PRIMARY KEY,
    shift_id BIGINT NOT NULL,
    transaction_id BIGINT NOT NULL,  -- FK to transactions table
    amount DECIMAL(10,2) NOT NULL,
    type ENUM('income', 'deposit_in', 'deposit_out', 'remittance', 'expense'),
    created_at TIMESTAMP
);

-- When any transaction happens, also insert here
-- Shift totals auto-update via triggers or application logic
```

### Benefits

| Aspect | V3 | V4 |
|--------|----|----|
| **Tables** | 3+ overlapping tables | 1 main shifts table |
| **Expected Cash** | Manual calculation | Auto-calculated column |
| **Partner** | JSON array | Proper foreign key |
| **Data Types** | Mix of string/decimal | All DECIMAL |
| **Real-time** | Calculated at report time | Updated as transactions happen |

---

## Summary

### What is a Shift?

A shift is a frontdesk work period that:
- Starts when staff clocks in and selects a cash drawer
- Tracks all cash in/out during the period
- Ends when staff counts cash and clocks out
- Compares expected vs actual cash (reconciliation)

### Key Concepts

| Term | Meaning |
|------|---------|
| **Beginning Cash** | Money in drawer at start |
| **Ending Cash** | Money in drawer at end (counted) |
| **Expected Cash** | What should be in drawer (calculated) |
| **Difference** | Ending - Expected (short or over) |
| **Remittance** | Cash handed to management during shift |
| **Cash Drawer** | Physical cash register/drawer |

### Tables in V3

| Table | Purpose |
|-------|---------|
| `shifts` | Simple shift record |
| `shift_logs` | Detailed shift tracking |
| `cash_drawers` | Physical drawers |
| `cash_on_drawers` | Cash movements |
| `remittances` | Cash given to management |
| `frontdesk_shifts` | Detailed shift reports |
