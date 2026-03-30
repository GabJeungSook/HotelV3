# V4 Void & Override Flow — Complete Example

How to correct wrong charges using the void system. Immutable — never update, only append.

---

## Scenario 1: Wrong Food Price

Frontdesk charged ₱300 for food but it should be ₱150.

```
ORIGINAL (wrong):
  #45  food  300.00  stay_id=500  "Burger x2"

Guest: "That's wrong, burger is ₱75 each not ₱150"

VOID FLOW:
  1. Frontdesk clicks "Void" on transaction #45
  2. System prompts: "Enter authorization code"
  3. Frontdesk enters: "ALMA2026"
  4. System validates against branch_settings.authorization_code → MATCH ✓
  5. System prompts: "Reason for void?"
  6. Frontdesk types: "Wrong unit price, should be ₱75 not ₱150"

SYSTEM CREATES:
  #46  void  300.00  linked_transaction_id=45
       description: "Voided: Wrong unit price, should be ₱75 not ₱150"
       created_by: Maria

  #47  food  150.00  stay_id=500  "Burger x2 (corrected)"
       transaction_items: Burger qty=2, unit_price=75.00, amount=150.00

RESULT:
  #45 is effectively cancelled (has a void pointing to it)
  #47 is the correct charge
  Full audit trail preserved

stays: total_charges adjusted (300 voided, 150 added = net -150)

activity_log:
  log='frontdesk', description='Transaction voided'
  properties: {original_id: 45, original_amount: 300, reason: "Wrong unit price"}
```

---

## Scenario 2: Duplicate Charge

Frontdesk accidentally charged food twice.

```
#50  food  150.00  stay_id=500  "Burger x2"  (correct)
#51  food  150.00  stay_id=500  "Burger x2"  (DUPLICATE!)

VOID:
  #52  void  150.00  linked_transaction_id=51
       description: "Voided: Duplicate charge"

Only #51 is voided. #50 remains valid.
No replacement charge needed — just remove the duplicate.
```

---

## Scenario 3: Wrong Room Charge (Override at Check-in)

Admin gave guest a special deal but frontdesk entered wrong amount.

```
#1  room_charge  500.00  stay_id=600  "Room Charge 6hrs Room #101"
#2  payment      500.00  linked_transaction_id=1

Guest was supposed to get ₱400 (special deal).

VOID THE CHARGE AND ITS PAYMENT:
  #60  void  500.00  linked_transaction_id=1
       description: "Voided: Wrong room charge, should be ₱400 (special deal)"

  #61  void  500.00  linked_transaction_id=2
       description: "Voided: Payment for voided room charge"

CREATE CORRECTED:
  #62  room_charge  400.00  stay_id=600  "Room Charge 6hrs Room #101 (special deal)"
  #63  payment      400.00  linked_transaction_id=62

REFUND DIFFERENCE:
  Guest paid ₱500 but should have paid ₱400.
  ₱100 excess → save as deposit or refund cash.

  #64  deposit_in  100.00  "Excess from room charge correction"

stays: total_charges = 400 (not 500)
```

---

## Scenario 4: Void a Charge That Was Already Paid with Deposit

```
#70  amenity      50.00  stay_id=500  "Extra towel"
#71  deposit_out  50.00  linked_transaction_id=70  "Paid from deposit"

Guest says: "I never requested a towel!"

VOID BOTH:
  #72  void  50.00  linked_transaction_id=70  "Voided: Guest did not request"
  #73  void  50.00  linked_transaction_id=71  "Voided: Payment for voided charge"

Deposit balance restored (deposit_out was voided).

NET EFFECT:
  Charge removed: ₱0
  Deposit restored: +₱50 back to guest's deposit balance
```

---

## Scenario 5: Void Denied — Wrong Authorization Code

```
Frontdesk enters wrong code: "WRONG123"
System: branch_settings.authorization_code = "ALMA2026" → NO MATCH

ERROR: "Invalid authorization code. Void denied."

No void created. Original transaction unchanged.
Frontdesk must get correct code from admin.
```

---

## How to Query Voided Transactions

```sql
-- All voided transactions for a stay
SELECT
  v.id as void_id,
  v.amount as voided_amount,
  v.description as void_reason,
  v.created_at as voided_at,
  up.first_name as voided_by,
  orig.id as original_id,
  orig.type as original_type,
  orig.amount as original_amount,
  orig.description as original_description
FROM transactions v
JOIN transactions orig ON orig.id = v.linked_transaction_id
JOIN user_profiles up ON up.user_id = v.created_by
WHERE v.type = 'void'
AND v.stay_id = :stay_id

-- Is a transaction voided?
SELECT EXISTS(
  SELECT 1 FROM transactions
  WHERE type = 'void' AND linked_transaction_id = :transaction_id
)

-- Active (non-voided) charges for a stay
SELECT * FROM transactions t
WHERE t.stay_id = :stay_id
AND t.type IN ('room_charge','extension','food','amenity','damage','transfer_fee')
AND t.id NOT IN (
  SELECT linked_transaction_id FROM transactions WHERE type = 'void'
)
AND t.id NOT IN (
  SELECT linked_transaction_id FROM transactions WHERE type IN ('payment','deposit_out')
)
```

---

## Void Audit Report

```sql
SELECT
  t.created_at as voided_at,
  b.name as branch,
  up.first_name as voided_by,
  orig.type as charge_type,
  orig.amount as charge_amount,
  t.description as reason
FROM transactions t
JOIN transactions orig ON orig.id = t.linked_transaction_id
JOIN branches b ON b.id = t.branch_id
JOIN user_profiles up ON up.user_id = t.created_by
WHERE t.type = 'void'
ORDER BY t.created_at DESC
```

---

## Rules

| Rule | Description |
|------|-------------|
| **Auth required** | Every void requires branch authorization code |
| **Reason required** | Must provide reason (stored in description) |
| **Immutable** | Original transaction never modified — void is a NEW record |
| **Linked** | Void always has linked_transaction_id pointing to voided charge |
| **Cascading** | If charge was paid, void both the charge AND the payment |
| **Audit trail** | Spatie + activity_log records who voided, when, why |
| **Deposit restore** | Voiding a deposit_out restores deposit balance |
