# V4 Discount Flow — Complete Example

How discounts are created, applied at kiosk, verified by frontdesk, and tracked in reports.

---

## Setup

```
Branch A:
  branch_settings.discounts_enabled: true

Discounts configured:
  id=1  "Senior Citizen"  percentage  20%   requires_verification=true
  id=2  "PWD"             percentage  15%   requires_verification=true
  id=3  "Summer Promo"    fixed       ₱100  requires_verification=false
```

---

## Flow 1: Senior Citizen Discount (Requires ID Verification)

### Step 1: Admin Creates Discount

```
discounts:
  id: 1
  branch_id: 1
  name: "Senior Citizen"
  discount_type: 'percentage'
  discount_value: 20.00
  max_discount: NULL (no cap)
  requires_verification: true
  is_active: true

activity_log (Spatie auto): Discount created
```

### Step 2: Guest Selects Discount at Kiosk

```
KIOSK SCREEN:
  Room 101, 6hrs, ₱500

  Available Discounts:
  [x] Senior Citizen (20% off) — Present valid ID at counter
  [ ] PWD (15% off) — Present valid ID at counter
  [ ] Summer Promo (₱100 off)

  Guest selects Senior Citizen → submits

kiosk_requests:
  guest_name: "Lola Santos"
  rate_id: 5 (Room 101, 6hrs, ₱500)
  room_id: 101
  (discount selection passed to frontdesk via request data)
```

### Step 3: Frontdesk Verifies ID

```
FRONTDESK SCREEN:
  ┌──────────────────────────────────────────────┐
  │  KIOSK REQUEST                                │
  │  Guest: Lola Santos                           │
  │  Room: 101, 6hrs, ₱500                        │
  │                                               │
  │  DISCOUNT REQUESTED: Senior Citizen (20%)     │
  │  ⚠ REQUIRES ID VERIFICATION                   │
  │                                               │
  │  Original: ₱500                               │
  │  Discount: -₱100 (20%)                        │
  │  Final:    ₱400                               │
  │                                               │
  │  [ VERIFY & CONFIRM ]  [ REMOVE DISCOUNT ]   │
  └──────────────────────────────────────────────┘

Maria checks Senior Citizen ID → clicks "Verify & Confirm"
```

### Step 4: Records Created

```
stays:
  id: 600
  snapshot_rate_amount: 500.00  ← original rate
  initial_amount: 400.00        ← after discount

applied_discounts:
  discountable_type: 'stay'
  discountable_id: 600
  discount_id: 1
  snapshot_discount_name: "Senior Citizen"
  snapshot_discount_type: 'percentage'
  snapshot_discount_value: 20.00
  original_amount: 500.00
  discount_amount: 100.00
  final_amount: 400.00
  applied_by: NULL (kiosk initiated)
  verified_by: Maria
  notes: "Senior ID #SC-2024-12345 verified"

transactions:
  #1  room_charge  400.00  "Room Charge 6hrs Room #101 (Senior 20% off)"
  #2  payment      400.00  linked_transaction_id=1
  #3  deposit_in   200.00  "Deposit (Key & Remote)"
```

### Step 5: Guest Has No ID — Discount Removed

```
Different scenario: guest claims Senior but can't show ID.

Maria clicks "Remove Discount"

stays:
  initial_amount: 500.00 (full price, no discount)

No applied_discounts record created.

transactions:
  #1  room_charge  500.00  "Room Charge 6hrs Room #101"
  #2  payment      500.00
  #3  deposit_in   200.00
```

---

## Flow 2: Fixed Discount (No Verification Needed)

### Summer Promo — Anyone Can Use It

```
Guest selects "Summer Promo (₱100 off)" at kiosk.
No ID required (requires_verification = false).

Frontdesk confirms without verification step.

applied_discounts:
  discount_id: 3
  snapshot_discount_name: "Summer Promo"
  snapshot_discount_type: 'fixed'
  snapshot_discount_value: 100.00
  original_amount: 500.00
  discount_amount: 100.00
  final_amount: 400.00
  applied_by: NULL (kiosk)
  verified_by: NULL (no verification needed)
  notes: NULL
```

---

## Flow 3: Percentage Discount with Cap

```
Discount: "Loyalty Member" percentage 25%, max_discount=₱300

Room rate: ₱1,400 (24hrs)
25% of 1,400 = ₱350 → but cap is ₱300!

applied_discounts:
  original_amount: 1,400.00
  discount_amount: 300.00     ← capped at max_discount
  final_amount: 1,100.00

Calculation:
  raw_discount = 1,400 × 0.25 = 350
  actual_discount = MIN(350, 300) = 300
```

---

## Flow 4: Discount Disabled at Branch Level

```
Admin sets: branch_settings.discounts_enabled = false

KIOSK SCREEN:
  Room 101, 6hrs, ₱500

  (No discount options shown — discounts_enabled is false)

  Guest sees only: Name, Contact, Submit.
  No discount possible at this branch.
```

---

## Flow 5: Discount Deactivated

```
Admin deactivates Summer Promo: discounts (id=3) → is_active = false

KIOSK SCREEN:
  Available Discounts:
  [x] Senior Citizen (20% off)
  [x] PWD (15% off)
  (Summer Promo NOT shown — is_active = false)

Past stays that used Summer Promo still show the discount
(snapshot_discount_name preserved in applied_discounts).
```

---

## Discount Usage Report

```sql
SELECT
  ad.snapshot_discount_name,
  ad.snapshot_discount_type,
  COUNT(*) as times_used,
  SUM(ad.discount_amount) as total_discounted,
  SUM(ad.original_amount) as total_before_discount
FROM applied_discounts ad
WHERE ad.created_at BETWEEN :from AND :to
GROUP BY ad.snapshot_discount_name, ad.snapshot_discount_type

RESULT:
┌──────────────────┬──────┬────────────┬─────────────┐
│ Discount         │ Used │ Discounted │ Would Have   │
├──────────────────┼──────┼────────────┼─────────────┤
│ Senior Citizen   │ 45   │ ₱9,000     │ ₱45,000     │
│ PWD              │ 12   │ ₱1,800     │ ₱12,000     │
│ Summer Promo     │ 89   │ ₱8,900     │ ₱89,000     │
└──────────────────┴──────┴────────────┴─────────────┘
```

## Verification Audit Report

```sql
SELECT
  ad.created_at,
  ad.snapshot_discount_name,
  s.guest_name,
  up_v.first_name as verified_by,
  ad.discount_amount,
  ad.notes
FROM applied_discounts ad
JOIN stays s ON s.id = ad.discountable_id
LEFT JOIN user_profiles up_v ON up_v.user_id = ad.verified_by
WHERE ad.snapshot_discount_type = 'percentage'
AND ad.verified_by IS NOT NULL

RESULT:
┌──────────┬──────────┬──────────────┬─────────┬──────────────────────┐
│ Date     │ Discount │ Verified By  │ Amount  │ Notes                │
├──────────┼──────────┼──────────────┼─────────┼──────────────────────┤
│ Mar 28   │ Senior   │ Maria        │ ₱100    │ SC ID #SC-2024-12345 │
│ Mar 28   │ PWD      │ Juan         │ ₱75     │ PWD ID #PW-2024-001  │
│ Mar 29   │ Senior   │ Maria        │ ₱200    │ SC ID #SC-2024-99999 │
└──────────┴──────────┴──────────────┴─────────┴──────────────────────┘
```
