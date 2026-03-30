# V4 Kiosk Journey — Guest Self-Service

Complete kiosk experience: check-in, view bill during stay, and checkout.

---

## Setup

```
Branch: Alma Hotel Branch A
Kiosk: "Lobby Entrance" (kiosk_terminal id=1)
Settings:
  kiosk_time_limit: 10 minutes
  initial_deposit: 200.00
  discounts_enabled: true
```

---

## The Kiosk Device

```
KIOSK TABLET (always on, always ready):
  - Authenticated via device_token (Sanctum)
  - Sends heartbeat every 2 minutes → last_heartbeat_at updated
  - Shows branch name and kiosk name at top
  - No login screen — device is always connected

┌─────────────────────────────────────┐
│  ALMA HOTEL — Branch A              │
│  Lobby Entrance Kiosk               │
│                                     │
│   ┌─────────────┐ ┌─────────────┐  │
│   │  CHECK-IN   │ │  CHECK-OUT  │  │
│   └─────────────┘ └─────────────┘  │
│                                     │
│       Touch to get started          │
└─────────────────────────────────────┘
```

---

## KIOSK CHECK-IN FLOW

### Step 1: Guest Selects Room Type

```
┌─────────────────────────────────────┐
│  Select Room Type:                   │
│                                     │
│  ┌───────────┐  ┌───────────┐      │
│  │ Standard  │  │  Deluxe   │      │
│  │  from     │  │  from     │      │
│  │  ₱500     │  │  ₱700     │      │
│  └───────────┘  └───────────┘      │
│                                     │
│  ┌───────────┐                     │
│  │   Suite   │                     │
│  │  from     │                     │
│  │  ₱1,000   │                     │
│  └───────────┘                     │
│                                     │
│  [ BACK ]                           │
└─────────────────────────────────────┘

Query:
  SELECT DISTINCT rt.name, MIN(r.amount) as from_price
  FROM room_types rt
  JOIN rooms rm ON rm.room_type_id = rt.id
  JOIN rates r ON r.room_id = rm.id
  WHERE rt.branch_id = :branch_id
  AND rm.status = 'available'
  GROUP BY rt.id
```

### Step 2: Guest Selects Available Room

```
Guest chose "Standard"

┌─────────────────────────────────────┐
│  Available Standard Rooms:           │
│                                     │
│  [101] [103] [105] [108]           │
│                                     │
│  Room 102 — occupied                │
│  Room 104 — cleaning                │
│  Room 106 — maintenance             │
│  Room 107 — reserved (kiosk hold)   │
│                                     │
│  [ BACK ]                           │
└─────────────────────────────────────┘

Query:
  SELECT * FROM rooms
  WHERE branch_id = :branch_id
  AND room_type_id = :type_id
  AND status IN ('available', 'cleaned')
  AND id NOT IN (SELECT room_id FROM kiosk_requests WHERE room_id IS NOT NULL)
  ORDER BY is_priority DESC, room_number ASC
```

### Step 3: Guest Selects Hours

```
Guest chose Room 101

┌─────────────────────────────────────┐
│  Room 101 (Standard)                 │
│                                     │
│  Select Duration:                    │
│                                     │
│  ┌─────────┐ ┌─────────┐          │
│  │  6 hrs  │ │ 12 hrs  │          │
│  │  ₱500   │ │  ₱800   │          │
│  └─────────┘ └─────────┘          │
│  ┌─────────┐ ┌─────────┐          │
│  │ 18 hrs  │ │ 24 hrs  │          │
│  │ ₱1,100  │ │ ₱1,400  │          │
│  └─────────┘ └─────────┘          │
│                                     │
│  [ BACK ]                           │
└─────────────────────────────────────┘

Query:
  SELECT sh.hours, r.amount
  FROM rates r
  JOIN staying_hours sh ON sh.id = r.staying_hour_id
  WHERE r.room_id = 101 AND r.is_available = true
  ORDER BY sh.hours ASC
```

### Step 4: Guest Enters Info + Optional Discount

```
Guest chose 6hrs (₱500)

┌─────────────────────────────────────┐
│  Room 101 — Standard — 6hrs (₱500)  │
│                                     │
│  Name:    [Juan Dela Cruz        ]  │
│  Contact: [09171234567           ]  │
│                                     │
│  Available Discounts:                │
│  [ ] Senior Citizen (20% off)       │
│      → Present valid ID at counter  │
│  [ ] PWD (15% off)                  │
│      → Present valid ID at counter  │
│                                     │
│  [ SUBMIT ]                         │
└─────────────────────────────────────┘

Discounts shown only if branch_settings.discounts_enabled = true
Each discount from: SELECT * FROM discounts WHERE branch_id = :id AND is_active = true
```

### Step 5: Request Submitted

```
Guest submits (no discount selected).

kiosk_requests:
  branch_id: 1
  kiosk_terminal_id: 1
  request_type: 'check_in'
  guest_name: "Juan Dela Cruz"
  guest_contact: "09171234567"
  room_type_id: 1
  rate_id: 5
  room_id: 101
  expires_at: NOW + 10 minutes (from branch_settings.kiosk_time_limit)

┌─────────────────────────────────────┐
│                                     │
│   ✓ Request Submitted!              │
│                                     │
│   Room 101 — 6hrs — ₱500            │
│   + Deposit: ₱200                   │
│   Total: ₱700                       │
│                                     │
│   Please proceed to counter          │
│   for payment.                       │
│                                     │
│   Request expires in: 09:45          │
│                                     │
│   [ NEW CHECK-IN ]                  │
└─────────────────────────────────────┘

Pusher event → frontdesk receives real-time notification
Room 101 not shown to other kiosk guests (filtered by kiosk_requests)
```

### Step 6: Frontdesk Confirms

```
Frontdesk Maria opens request → verifies guest → collects ₱700 → confirms.

stays: created
transactions: room_charge + payment + deposit_in
rooms: Room 101 → 'occupied'
kiosk_requests: DELETED

Guest receives QR code (stays.code printed or displayed).
```

### What If Request Expires?

```
Guest submitted but never went to counter. 10 minutes pass.

Scheduled job (runs every minute):
  SELECT * FROM kiosk_requests
  WHERE expires_at < NOW()

  Found expired request → DELETE

Room 101 becomes available again for other guests.
No stays record created. No transactions. Clean.
```

---

## KIOSK CHECK-OUT FLOW

### Step 1: Guest Scans QR Code

```
Guest returns to kiosk, taps "CHECK-OUT"

┌─────────────────────────────────────┐
│                                     │
│   Scan Your QR Code                  │
│                                     │
│       ┌─────────┐                   │
│       │ ▓▓▓▓▓▓▓ │                   │
│       │ ▓▓▓▓▓▓▓ │  ← Camera        │
│       │ ▓▓▓▓▓▓▓ │                   │
│       └─────────┘                   │
│                                     │
│   Or enter code: [ALM-X7Y8Z9    ]  │
│                                     │
└─────────────────────────────────────┘

System looks up: SELECT * FROM stays WHERE code = 'ALM-X7Y8Z9' AND status = 'active'
```

### Step 2: Guest Sees Bill Summary

```
┌─────────────────────────────────────┐
│  Room: 101 — Juan Dela Cruz          │
│  Check-in: 8:15 AM                   │
│  Expected checkout: 2:15 PM          │
│                                     │
│  CHARGES:                            │
│  Room Charge (6hrs)      ₱500  PAID  │
│  Food (Burger, Coke)     ₱175  UNPAID│
│  Extension (3hrs)        ₱250  UNPAID│
│  ─────────────────────────────       │
│  Total charges:        ₱925          │
│  Total paid:           ₱500          │
│  Deposit held:         ₱200          │
│  ─────────────────────────────       │
│  Balance due:          ₱225          │
│                                     │
│  Please proceed to counter           │
│  for final settlement.               │
│                                     │
│  [ CONFIRM CHECKOUT ]               │
└─────────────────────────────────────┘

Queries:
  -- Charges
  SELECT type, SUM(amount) FROM transactions
  WHERE stay_id = :id AND type IN (charge types) GROUP BY type

  -- Paid
  SELECT SUM(amount) FROM transactions
  WHERE stay_id = :id AND type IN ('payment','deposit_out')
  AND linked_transaction_id IS NOT NULL

  -- Deposit balance
  SELECT SUM(CASE WHEN type='deposit_in' THEN amount ELSE 0 END)
       - SUM(CASE WHEN type='deposit_out' THEN amount ELSE 0 END)
  FROM transactions WHERE stay_id = :id
```

### Step 3: Guest Confirms

```
Guest taps "Confirm Checkout"

kiosk_requests:
  request_type: 'check_out'
  stay_id: 500
  kiosk_terminal_id: 1
  expires_at: NOW + 10 minutes

┌─────────────────────────────────────┐
│                                     │
│   ✓ Checkout request submitted!     │
│                                     │
│   Please proceed to counter          │
│   to settle your bill.              │
│                                     │
│   Balance due: ₱225                  │
│   Deposit to claim: ₱200            │
│                                     │
└─────────────────────────────────────┘

Pusher → frontdesk notified
Kiosk does NOT finalize checkout — frontdesk does.
```

### Step 4: Frontdesk Settles

```
Frontdesk sees checkout notification → settles bill → refunds deposit → finalizes.
(See V4_FRONTDESK_JOURNEY.md for full checkout flow)

kiosk_requests: DELETED after resolution
```

---

## ADMIN VIEW: Kiosk Management

```
┌──────────────────────────────────────────────────────────┐
│  KIOSK MANAGEMENT — Branch A                              │
├──────────┬──────────┬────────────┬──────────┬────────────┤
│  Name    │  Status  │  Last Seen │  Requests│  Actions   │
├──────────┼──────────┼────────────┼──────────┼────────────┤
│  Lobby 1 │  Online  │  1 min ago │    2     │ [Deactivate│
│  Lobby 2 │  Online  │  2 min ago │    0     │ [Deactivate│
│  Lobby 3 │  Offline │  3 hrs ago │    0     │ [Activate] │
└──────────┴──────────┴────────────┴──────────┴────────────┘

│  [ + Register New Kiosk ]                                │

  Admin clicks "Register New Kiosk"
  → enters name: "Lobby 4"
  → system generates code: "KSK-D4E5F6"
  → admin goes to tablet, enters code
  → tablet connected and ready
```

---

## Tables Touched

| Table | During Check-in | During Check-out |
|-------|----------------|-----------------|
| `kiosk_requests` | Created → deleted after confirm | Created → deleted after settle |
| `kiosk_terminals` | heartbeat updated | heartbeat updated |
| `stays` | Created by frontdesk | Updated (checked_out) |
| `transactions` | Created by frontdesk | Created (payments, deposit refund) |
| `rooms` | Status → occupied | Status → uncleaned |
| `cleaning_tasks` | — | Created (pending) |
| `discounts` | Shown if active | — |
| `applied_discounts` | Created if discount selected | — |
