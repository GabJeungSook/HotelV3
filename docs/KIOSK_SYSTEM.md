# Kiosk System Documentation

This document explains the **Kiosk System** - the self-service check-in/check-out feature for guests.

---

## What is a Kiosk?

A **kiosk** is a self-service tablet or touchscreen placed in the hotel lobby. Guests can use it to check-in or check-out without directly talking to frontdesk staff.

Think of it like:
- An **ATM** for banking
- A **self-checkout** at a grocery store
- An **airport check-in kiosk**

```
┌─────────────────────────────────┐
│                                 │
│      WELCOME TO HOTEL           │
│                                 │
│   ┌───────────┐ ┌───────────┐   │
│   │ CHECK-IN  │ │ CHECK-OUT │   │
│   └───────────┘ └───────────┘   │
│                                 │
│       [ Touch to Start ]        │
│                                 │
└─────────────────────────────────┘
            ↑
     Guest uses this tablet
     in the hotel lobby
```

---

## Why Does the Hotel Need a Kiosk?

### Problem Without Kiosk

```
Guest arrives
    ↓
Waits in line (5-10 mins if busy)
    ↓
Talks to frontdesk staff
    ↓
Staff asks questions, types info
    ↓
Staff shows available rooms
    ↓
Guest decides
    ↓
Staff processes payment
    ↓
Done (Total: 10-15 minutes)
```

### Solution With Kiosk

```
Guest arrives
    ↓
Uses kiosk (no waiting)
    ↓
Selects room & hours themselves
    ↓
Goes to counter for quick payment
    ↓
Done (Total: 3-5 minutes)
```

### Benefits

| Benefit | Description |
|---------|-------------|
| **Faster** | Guest doesn't wait in line |
| **Less Staff Work** | Frontdesk only confirms & takes payment |
| **Privacy** | Some guests prefer not talking to staff |
| **Handle Rush Hours** | Multiple guests can use kiosks + frontdesk at same time |
| **Fewer Errors** | Guest enters their own info |

---

## How Kiosk Check-In Works

### Step-by-Step Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                        KIOSK CHECK-IN FLOW                       │
└─────────────────────────────────────────────────────────────────┘

STEP 1: Guest at Kiosk
┌─────────────────────────────────┐
│  Select Room Type:              │
│  ┌─────────┐ ┌─────────┐       │
│  │Standard │ │ Deluxe  │       │
│  │  ₱500   │ │  ₱700   │       │
│  └─────────┘ └─────────┘       │
└─────────────────────────────────┘
        │
        ▼
STEP 2: Select Specific Room
┌─────────────────────────────────┐
│  Available Rooms:               │
│  [101] [102] [105] [108]       │
│                                 │
│  (Occupied rooms not shown)     │
└─────────────────────────────────┘
        │
        ▼
STEP 3: Select Hours
┌─────────────────────────────────┐
│  How long?                      │
│  ┌───────┐ ┌───────┐ ┌───────┐ │
│  │ 3 hrs │ │ 6 hrs │ │12 hrs │ │
│  │ ₱500  │ │ ₱800  │ │₱1,200 │ │
│  └───────┘ └───────┘ └───────┘ │
└─────────────────────────────────┘
        │
        ▼
STEP 4: Enter Guest Info
┌─────────────────────────────────┐
│  Name: [Juan Dela Cruz      ]  │
│  Contact: [0917-xxx-xxxx    ]  │
│                                 │
│  [ SUBMIT ]                     │
└─────────────────────────────────┘
        │
        ▼
STEP 5: Request Sent!
┌─────────────────────────────────┐
│                                 │
│   ✓ Request Submitted!          │
│                                 │
│   Please proceed to counter     │
│   for payment.                  │
│                                 │
│   Request expires in: 09:45     │
│                                 │
└─────────────────────────────────┘
```

---

## What Happens Behind the Scenes

### When Guest Submits on Kiosk:

```
1. System creates TEMPORARY records:
   ├── guests (temporary guest record)
   └── temporary_check_in_kiosks (the request)

2. Room is HELD temporarily
   └── Other guests can't select this room

3. Timer starts
   └── Request expires in X minutes (kiosk_time_limit)

4. Frontdesk gets NOTIFICATION
   └── Real-time via Pusher
```

### When Frontdesk Confirms:

```
1. Frontdesk opens the request
   └── is_opened = true

2. Verifies guest at counter

3. Collects payment

4. Confirms check-in:
   ├── Creates real checkin_details record
   ├── Creates transaction record
   ├── Updates room status to "Occupied"
   ├── Generates QR code for guest
   └── Deletes temporary_check_in_kiosks record
```

### If Request Expires (Not Confirmed):

```
1. Timer reaches 0

2. System auto-deletes:
   ├── temporary_check_in_kiosks record
   └── temporary guest record

3. Room becomes available again
```

---

## Kiosk to Frontdesk Connection

The kiosk does NOT complete check-in by itself. It only creates a **request** that frontdesk must confirm.

```
┌──────────────┐                      ┌──────────────┐
│              │    1. Request        │              │
│    KIOSK     │ ──────────────────►  │   FRONTDESK  │
│   (Guest)    │                      │    (Staff)   │
│              │                      │              │
└──────────────┘                      └──────────────┘
       │                                     │
       │                                     │
       ▼                                     ▼
┌──────────────────────────────────────────────────┐
│           temporary_check_in_kiosks              │
│                 (Waiting Queue)                   │
│                                                  │
│  ┌────────────────────────────────────────────┐  │
│  │ Request #1: Room 101, 3hrs, Juan           │  │
│  │ Expires in: 08:32                          │  │
│  │ Status: Waiting for frontdesk              │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
│  ┌────────────────────────────────────────────┐  │
│  │ Request #2: Room 205, 6hrs, Maria          │  │
│  │ Expires in: 05:18                          │  │
│  │ Status: Frontdesk opened                   │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
└──────────────────────────────────────────────────┘
```

### Why Not Let Kiosk Complete Check-In?

| Reason | Explanation |
|--------|-------------|
| **Payment** | Hotel needs to collect cash/card at counter |
| **Verification** | Staff needs to see guest in person |
| **ID Check** | Some hotels require ID verification |
| **Deposit** | Staff collects security deposit |
| **Room Key** | Physical key/card must be handed to guest |

---

## Kiosk Check-Out Flow

Guests can also use kiosk to check-out using their QR code.

```
STEP 1: Guest scans QR code at kiosk
┌─────────────────────────────────┐
│                                 │
│   [Scan Your QR Code]           │
│                                 │
│       ┌─────────┐               │
│       │ ▓▓▓▓▓▓▓ │               │
│       │ ▓▓▓▓▓▓▓ │  ← Camera     │
│       │ ▓▓▓▓▓▓▓ │               │
│       └─────────┘               │
│                                 │
└─────────────────────────────────┘
        │
        ▼
STEP 2: View Bill Summary
┌─────────────────────────────────┐
│  Room: 101                      │
│  Check-in: 10:00 AM             │
│  Check-out: 1:00 PM             │
│                                 │
│  Room Charge:     ₱500          │
│  Extension:       ₱180          │
│  Food Order:      ₱150          │
│  ─────────────────────          │
│  Total:           ₱830          │
│  Deposit Paid:    ₱200          │
│  ─────────────────────          │
│  Balance Due:     ₱630          │
│                                 │
│  [ CONFIRM CHECKOUT ]           │
└─────────────────────────────────┘
        │
        ▼
STEP 3: Frontdesk receives notification
        │
        ▼
STEP 4: Guest goes to counter
        ├── Pays remaining balance (or gets refund)
        ├── Returns room key
        └── Check-out complete
```

---

## Database Tables

### `temporary_check_in_kiosks`

The waiting queue for kiosk check-in requests.

| Column | Type | Purpose |
|--------|------|---------|
| `id` | bigint | Primary key |
| `branch_id` | foreignId | Which hotel branch |
| `room_id` | foreignId | Room guest selected |
| `guest_id` | foreignId | Temporary guest record |
| `terminated_at` | datetime | When request expires |
| `is_opened` | boolean | Has frontdesk seen this? |
| `created_at` | timestamp | When request was made |

### `branches` (Kiosk Settings)

| Column | Type | Purpose |
|--------|------|---------|
| `kiosk_time_limit` | integer | Minutes before request expires (default: 10) |

### `guests` (Kiosk Flag)

| Column | Type | Purpose |
|--------|------|---------|
| `has_kiosk_check_out` | boolean | Did guest use kiosk for checkout? |

---

## The Kiosk Role

In the system, `kiosk` is also a **user role**.

```php
// From RoleSeeder.php
$role = Role::create(['name' => 'kiosk']);
```

### Why is Kiosk a User Role?

The kiosk tablet needs to **log in** to the system:

```
┌─────────────────────────────────┐
│  KIOSK LOGIN                    │
│                                 │
│  Email: kiosk@hotel.com         │
│  Password: ********             │
│                                 │
│  [ LOGIN ]                      │
└─────────────────────────────────┘
```

### Kiosk Role Permissions

| Can Do | Cannot Do |
|--------|-----------|
| ✓ View available rooms | ✗ Access admin panel |
| ✓ Create check-in requests | ✗ View reports |
| ✓ Process check-out requests | ✗ Manage users |
| ✓ Display room types & rates | ✗ Modify settings |

### Kiosk Routes

From `routes/kiosk.php`:
```
/kiosk/check-in   → Check-in screen
/kiosk/check-out  → Check-out screen
```

---

## Real-Time Notifications (Pusher)

When a guest submits a kiosk request, frontdesk gets notified instantly.

```
┌──────────┐         ┌──────────┐         ┌──────────┐
│  KIOSK   │ ──────► │  PUSHER  │ ──────► │FRONTDESK │
│  Submit  │         │ (Cloud)  │         │  Alert!  │
└──────────┘         └──────────┘         └──────────┘
                           │
                           │ Channel: newcheckin.{branch_id}
                           │
                           ▼
                    Real-time push
                    (no page refresh needed)
```

### What Frontdesk Sees

```
┌─────────────────────────────────────────────────────┐
│  FRONTDESK DASHBOARD                                │
│                                                     │
│  ┌─────────────────────────────────────────────┐   │
│  │ 🔔 NEW KIOSK CHECK-IN REQUEST!              │   │
│  │                                              │   │
│  │ Room: 101 (Standard)                         │   │
│  │ Guest: Juan Dela Cruz                        │   │
│  │ Hours: 3 hours (₱500)                        │   │
│  │ Time: 2:35 PM                                │   │
│  │ Expires in: 09:22                            │   │
│  │                                              │   │
│  │ [ OPEN REQUEST ]                             │   │
│  └─────────────────────────────────────────────┘   │
│                                                     │
└─────────────────────────────────────────────────────┘
```

---

## Complete Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         KIOSK CHECK-IN FLOW                              │
└─────────────────────────────────────────────────────────────────────────┘

    GUEST                    SYSTEM                      FRONTDESK
      │                        │                            │
      │  1. Use kiosk          │                            │
      │  (select room/hours)   │                            │
      │──────────────────────► │                            │
      │                        │                            │
      │                        │  2. Create records:        │
      │                        │  - guests (temp)           │
      │                        │  - temporary_check_in_kiosks│
      │                        │  - Hold room               │
      │                        │                            │
      │                        │  3. Send Pusher event      │
      │                        │──────────────────────────► │
      │                        │                            │
      │                        │                            │  4. See notification
      │                        │                            │
      │  5. Go to counter      │                            │
      │─────────────────────────────────────────────────────►│
      │                        │                            │
      │                        │                            │  6. Open request
      │                        │                            │     (is_opened = true)
      │                        │                            │
      │                        │                            │  7. Verify guest
      │                        │                            │
      │                        │                            │  8. Collect payment
      │                        │                            │
      │                        │  9. Confirm check-in:      │
      │                        │◄──────────────────────────│
      │                        │  - Create checkin_details  │
      │                        │  - Create transaction      │
      │                        │  - Room → Occupied         │
      │                        │  - Delete temp records     │
      │                        │  - Generate QR code        │
      │                        │                            │
      │  10. Receive QR code   │                            │
      │◄───────────────────────│                            │
      │                        │                            │
      ▼                        ▼                            ▼
   [DONE]                   [DONE]                       [DONE]
```

---

## Summary

### What is Kiosk?
Self-service tablet in hotel lobby for guest check-in/check-out.

### Why Use Kiosk?
- Faster check-in (no waiting in line)
- Less work for frontdesk staff
- Handles rush hours better
- Guest privacy

### How Does It Work?

| Step | What Happens |
|------|--------------|
| 1 | Guest uses kiosk to select room & hours |
| 2 | System creates temporary request |
| 3 | Frontdesk gets real-time notification |
| 4 | Guest goes to counter |
| 5 | Frontdesk confirms & takes payment |
| 6 | Guest gets QR code |

### Key Points

- Kiosk does NOT complete check-in alone
- Frontdesk must CONFIRM every kiosk request
- Requests EXPIRE if not confirmed (kiosk_time_limit)
- Real-time notifications via Pusher
- Kiosk is also a user ROLE with limited permissions

### Tables Involved

| Table | Purpose |
|-------|---------|
| `temporary_check_in_kiosks` | Queue of pending kiosk requests |
| `branches.kiosk_time_limit` | Expiration time setting |
| `guests.has_kiosk_check_out` | Flag for kiosk checkout |
