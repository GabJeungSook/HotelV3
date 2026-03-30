# V4 Room Transfer — Complete Examples

Three scenarios: upgrade (pay more), downgrade (get deposit), and same-price transfer.

---

## Setup

```
Room 101 (Standard, Floor 1): 6hrs = ₱500
Room 205 (Deluxe, Floor 2):   6hrs = ₱700
Room 301 (Standard, Floor 3): 6hrs = ₱500

Transfer Reasons:
  id=1 "Guest preference"
  id=2 "Room issue (uncleaned/damaged)"
  id=3 "Upgrade request"
```

---

## Scenario 1: Upgrade (Guest Pays More)

Guest in Room 101 (₱500) wants to move to Room 205 (₱700).

```
Price difference: ₱700 - ₱500 = ₱200 (guest owes more)

room_transfers:
  stay_id: 500
  from_room_id: 101
  to_room_id: 205
  from_rate_amount: 500.00    ← snapshot
  to_rate_amount: 700.00      ← snapshot
  price_difference: 200.00
  transfer_reason_id: 3 ("Upgrade request")
  transferred_by: Maria

transactions:
  #8  transfer_fee  200.00  stay_id=500  "Room transfer: #101 → #205 (upgrade ₱200)"
      source_type='room_transfer'  source_id=1

stays:
  room_id: 101 → 205 (updated)
  rate_id: updated to Room 205's rate
  total_charges: +200

rooms:
  Room 101: status → 'uncleaned'
  Room 205: status → 'occupied'

cleaning_tasks:
  room_id: 101, stay_id: 500, status: 'pending'
  (old room needs cleaning)

Guest pays ₱200 later (or at checkout).
```

---

## Scenario 2: Downgrade (Guest Gets Deposit)

Guest in Room 205 (₱700) wants to move to Room 101 (₱500).

```
Price difference: ₱500 - ₱700 = -₱200 (hotel owes guest)

room_transfers:
  stay_id: 501
  from_room_id: 205
  to_room_id: 101
  from_rate_amount: 700.00
  to_rate_amount: 500.00
  price_difference: -200.00
  transfer_reason_id: 1 ("Guest preference")
  transferred_by: Maria

transactions:
  #9  deposit_in  200.00  stay_id=501  "Excess from room transfer (downgrade)"

  No transfer_fee charge — guest is owed money, saved as deposit.

stays:
  room_id: 205 → 101
  total_deposit_in: +200

rooms:
  Room 205: status → 'uncleaned'
  Room 101: status → 'occupied'

cleaning_tasks:
  room_id: 205, stay_id: 501, status: 'pending'

Guest now has extra ₱200 deposit. Can use for food/amenities or claim at checkout.
```

---

## Scenario 3: Same Price Transfer

Guest in Room 101 (₱500) moves to Room 301 (₱500). Room has an issue.

```
Price difference: ₱500 - ₱500 = ₱0

room_transfers:
  stay_id: 502
  from_room_id: 101
  to_room_id: 301
  from_rate_amount: 500.00
  to_rate_amount: 500.00
  price_difference: 0.00
  transfer_reason_id: 2 ("Room issue (uncleaned/damaged)")
  transferred_by: Juan

  No transaction created — no money changes hands.

stays:
  room_id: 101 → 301

rooms:
  Room 101: status → 'uncleaned'
  Room 301: status → 'occupied'

cleaning_tasks:
  room_id: 101, stay_id: 502, status: 'pending'
```

---

## Scenario 4: Transfer with Override (Admin Authorization)

Guest in Room 101 wants Room 205 but admin wants to waive the ₱200 difference.

```
Admin enters authorization_code → override approved.

room_transfers:
  from_rate_amount: 500.00
  to_rate_amount: 700.00
  price_difference: 0.00   ← overridden to 0

  No transfer_fee transaction created (waived).

activity_log:
  log='frontdesk', description='Room transfer override — fee waived'
  properties: {from_room: 101, to_room: 205, waived_amount: 200, auth_by: Admin}
```

---

## What Happens to Extensions After Transfer?

Guest checked into Room 101 (6hrs, ₱500). Then transferred to Room 205 (₱700).

```
BEFORE TRANSFER:
  stays.cycle_hours = 6
  Extension rates → looked up by room_id = 101

AFTER TRANSFER:
  stays.room_id = 205
  stays.cycle_hours = 6 (unchanged — cycle continues)
  Extension rates → NOW looked up by room_id = 205

  Room 205 might have DIFFERENT extension rates!
  Room 101 extension 3hrs = ₱250
  Room 205 extension 3hrs = ₱350

  Guest now pays ₱350 for next extension (room 205's rate).
```

**Cycle position carries over. Extension rates change to new room's rates.** This is correct — the guest is now in a more expensive room, so extensions cost more.

---

## Transfer Report Query

```sql
SELECT
  rt.created_at,
  s.guest_name,
  r1.room_number as from_room,
  r2.room_number as to_room,
  rt.from_rate_amount,
  rt.to_rate_amount,
  rt.price_difference,
  tr.reason,
  up.first_name as transferred_by
FROM room_transfers rt
JOIN stays s ON s.id = rt.stay_id
JOIN rooms r1 ON r1.id = rt.from_room_id
JOIN rooms r2 ON r2.id = rt.to_room_id
LEFT JOIN transfer_reasons tr ON tr.id = rt.transfer_reason_id
JOIN user_profiles up ON up.user_id = rt.transferred_by
WHERE s.branch_id = :branch_id
AND rt.created_at BETWEEN :date_from AND :date_to
ORDER BY rt.created_at
```

---

## Summary

| Scenario | Price Diff | Transaction Created | Deposit Effect |
|----------|-----------|-------------------|----------------|
| **Upgrade** | +₱200 | `transfer_fee` ₱200 | None (guest pays) |
| **Downgrade** | -₱200 | `deposit_in` ₱200 | Deposit increases |
| **Same price** | ₱0 | None | None |
| **Override** | Waived | None (logged) | None |

**In all cases:**
- Old room → `uncleaned`, cleaning task created
- New room → `occupied`
- `stays.room_id` updated (live field)
- Snapshots unchanged (frozen from original check-in)
- Cycle position carries over
- Extension rates switch to new room's rates
