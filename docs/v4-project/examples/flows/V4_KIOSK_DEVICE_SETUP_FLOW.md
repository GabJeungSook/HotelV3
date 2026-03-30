# V4 Kiosk Device Setup Flow — Complete Example

How to register, connect, manage, and deactivate kiosk devices.

---

## Flow 1: Admin Registers New Kiosk

```
Admin → Kiosk Management → "Register New Kiosk"

ENTERS:
  Name: "Lobby Entrance"

SYSTEM:
  1. Creates Sanctum API token
  2. Generates human-readable code from token

kiosk_terminals:
  id: 1
  branch_id: 1
  name: "Lobby Entrance"
  device_token: "hashed_token_abc123..."  ← Sanctum token (hashed)
  is_active: true
  last_heartbeat_at: NULL

SCREEN SHOWS:
  ┌──────────────────────────────────────┐
  │  Kiosk Registered!                    │
  │                                      │
  │  Name: Lobby Entrance                 │
  │  Code: KSK-A1B2C3                    │
  │                                      │
  │  Enter this code on the tablet.       │
  │  This code will only be shown once.  │
  │                                      │
  │  [ COPY CODE ]  [ DONE ]            │
  └──────────────────────────────────────┘

activity_log: log='admin', description='Kiosk terminal registered: Lobby Entrance'
```

---

## Flow 2: Tablet First-Time Setup

Admin goes to the physical tablet.

```
TABLET SCREEN (first boot / not connected):
  ┌──────────────────────────────────────┐
  │                                      │
  │  ALMA HOTEL                           │
  │  Kiosk Setup                          │
  │                                      │
  │  Enter Kiosk Code:                    │
  │  [ KSK-A1B2C3          ]            │
  │                                      │
  │  [ CONNECT ]                         │
  │                                      │
  └──────────────────────────────────────┘

Tablet sends: POST /api/kiosk/activate { code: "KSK-A1B2C3" }

SERVER:
  1. Finds kiosk_terminal by code
  2. Validates is_active = true
  3. Returns API token + branch info

TABLET:
  1. Stores token in secure local storage
  2. Stores branch_id, branch_name
  3. Shows: "Connected to Alma Hotel Branch A"
  4. Redirects to kiosk home screen

kiosk_terminals:
  last_heartbeat_at: NOW  ← first heartbeat

TABLET IS NOW READY. No login screen ever again.
```

---

## Flow 3: Daily Operation (Auto-Connect)

```
TABLET BOOTS UP:
  1. Checks local storage for API token → found
  2. Sends heartbeat: POST /api/kiosk/heartbeat { Authorization: Bearer token }
  3. Server updates: kiosk_terminals.last_heartbeat_at = NOW
  4. Server returns: { is_active: true, branch_name: "Branch A" }
  5. Tablet shows kiosk home screen → ready for guests

EVERY 2 MINUTES:
  Tablet sends heartbeat → server updates last_heartbeat_at
  If server returns is_active=false → tablet shows "Deactivated" screen

NO LOGIN. NO PASSWORD. Just token-based auto-connect.
```

---

## Flow 4: Admin Monitors Kiosks

```
Admin → Kiosk Management

┌──────────────────────────────────────────────────────────┐
│  KIOSK MANAGEMENT — Branch A                              │
├──────────────┬──────────┬────────────┬───────────────────┤
│  Name        │  Status  │  Last Seen │  Actions          │
├──────────────┼──────────┼────────────┼───────────────────┤
│  Lobby 1     │  Online  │  1 min ago │  [ Deactivate ]   │
│  Lobby 2     │  Online  │  2 min ago │  [ Deactivate ]   │
│  Lobby 3     │  Offline │  3 hrs ago │  [ Deactivate ]   │
└──────────────┴──────────┴────────────┴───────────────────┘

│  [ + Register New Kiosk ]                                │

Status logic:
  Online  = last_heartbeat_at >= NOW - 5 minutes
  Offline = last_heartbeat_at < NOW - 5 minutes OR NULL
```

---

## Flow 5: Admin Deactivates a Kiosk

Tablet is stolen or needs to be removed.

```
Admin clicks "Deactivate" on Lobby 3.

kiosk_terminals (id=3):
  is_active: true → false

NEXT HEARTBEAT from Lobby 3 tablet:
  Server returns: { is_active: false }

TABLET:
  ┌──────────────────────────────────────┐
  │                                      │
  │  ⚠ THIS KIOSK HAS BEEN             │
  │    DEACTIVATED                       │
  │                                      │
  │  Contact hotel administration.        │
  │                                      │
  └──────────────────────────────────────┘

  Tablet clears local token. Cannot be used until reactivated.

activity_log: log='admin', description='Kiosk deactivated: Lobby 3'
```

---

## Flow 6: Reactivate a Kiosk

```
Admin clicks "Activate" on Lobby 3.

kiosk_terminals (id=3):
  is_active: false → true

Admin must go to tablet and enter code again (token was cleared).
Or generate a new code if old one is lost.
```

---

## Flow 7: Replace a Broken Tablet

Old tablet broke. New tablet needs same kiosk identity.

```
OPTION A: Generate new code for same kiosk_terminal
  Admin → Lobby 1 → "Regenerate Code"
  New code: KSK-X9Y0Z1
  Enter on new tablet → connected as "Lobby 1"

OPTION B: Delete old kiosk, register new
  Admin deactivates "Lobby 1"
  Admin registers "Lobby 1 (New)" → new code
  Enter on new tablet → connected
```

---

## Multiple Kiosks at One Branch

```
Branch A has 3 kiosk tablets:

kiosk_terminals:
  id=1  "Lobby Entrance"   branch_id=1  is_active=true
  id=2  "Lobby Center"     branch_id=1  is_active=true
  id=3  "Near Elevator"    branch_id=1  is_active=true

All 3 see the same rooms, rates, and availability.
Each creates kiosk_requests with their own kiosk_terminal_id.

Frontdesk can see which tablet a request came from:
  "Check-in request from Lobby Entrance kiosk"
```

---

## API Endpoints

```
POST /api/kiosk/activate
  Body: { code: "KSK-A1B2C3" }
  Returns: { token: "...", branch: { id, name } }

POST /api/kiosk/heartbeat
  Header: Authorization: Bearer {token}
  Returns: { is_active: true/false, branch_name: "..." }

GET /api/kiosk/rooms
  Returns: available rooms for this branch

GET /api/kiosk/rates/:room_id
  Returns: rates for specific room

POST /api/kiosk/check-in
  Body: { guest_name, contact, room_id, rate_id, discount_id? }
  Returns: { kiosk_request_id, expires_at }

POST /api/kiosk/check-out
  Body: { stay_code: "ALM-X7Y8Z9" }
  Returns: { kiosk_request_id, bill_summary }
```

---

## Security

| Concern | Solution |
|---------|----------|
| Stolen tablet | Admin deactivates → token revoked instantly |
| Token leaked | Regenerate code → old token invalidated |
| Wrong branch | Token is branch-specific, can only see own branch data |
| Expired requests | Auto-cleanup job deletes after kiosk_time_limit |
| Multiple requests | Room filtered out of available list while kiosk_request exists |
