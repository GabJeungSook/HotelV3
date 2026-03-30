# V4 Multi-Branch Staff Deployment Flow — Complete Example

How superadmin deploys staff across branches and how the system handles multi-branch access.

---

## Setup

```
Branches:
  Branch A (Cebu) — id=1
  Branch B (Manila) — id=2

Staff:
  Juan Cruz — frontdesk, assigned to Branch A (primary)
```

---

## Flow 1: Superadmin Deploys Juan to Branch B

Manila is short-staffed. Superadmin deploys Juan temporarily.

```
Superadmin → Users → Juan Cruz → "Deploy to Branch"

Selects: Branch B (Manila), assignment_type: 'deployed'

branch_user:
  EXISTING: user_id=Juan, branch_id=1 (Cebu),  assignment_type='primary'
  NEW:      user_id=Juan, branch_id=2 (Manila), assignment_type='deployed'

Juan now has access to BOTH branches.

activity_log: log='admin', description='User deployed: Juan Cruz → Manila'
```

---

## Flow 2: Juan Logs In at Manila

```
Juan logs in from Manila → system sees 2 branch assignments.

BRANCH SELECTION SCREEN:
  ┌──────────────────────────────────────┐
  │  SELECT BRANCH                        │
  │                                      │
  │  ┌────────────────────────────────┐  │
  │  │ Cebu (Primary)                 │  │
  │  └────────────────────────────────┘  │
  │  ┌────────────────────────────────┐  │
  │  │ Manila (Deployed)              │  │
  │  └────────────────────────────────┘  │
  │                                      │
  └──────────────────────────────────────┘

Juan selects Manila → session sets active_branch_id = 2

All queries now scoped to branch_id = 2 (Manila).
Juan sees Manila's rooms, rates, guests — NOT Cebu's.
```

---

## Flow 3: Juan Works a Shift at Manila

```
Juan selects Cash Drawer (Manila's Drawer 1) → enters opening cash → shift starts.

shifts:
  id: 50
  branch_id: 2 (Manila!)
  user_id: Juan
  cash_drawer_id: 5 (Manila Drawer 1)
  shift_period: 'AM'
  started_at: 8:00 AM

Juan checks in guests, processes payments — all tagged:
  transactions: branch_id=2, shift_id=50, created_by=Juan

Everything belongs to Manila's data. Clean separation.
```

---

## Flow 4: Juan's Shift Appears in Manila Reports

```
Manila admin views shift report:

  Shift #50: Juan Cruz (Deployed from Cebu)
  Opening: ₱1,000
  Payments: ₱5,000
  Expected: ₱6,000
  ...

Juan's shift is a Manila shift. Reports include it in Manila's numbers.
Cebu reports do NOT include this shift.
```

---

## Flow 5: Juan Returns to Cebu

Superadmin removes Manila deployment.

```
Superadmin → Users → Juan Cruz → Remove deployment

branch_user:
  DELETED: user_id=Juan, branch_id=2, assignment_type='deployed'
  REMAINS: user_id=Juan, branch_id=1, assignment_type='primary'

Juan can no longer access Manila.
Next login → goes directly to Cebu (only branch left).

Manila's historical data (shifts, transactions) still references Juan.
  Juan's user_id is on those records forever — audit trail preserved.
```

---

## Flow 6: Superadmin Oversight (View-Only Access)

Superadmin wants a branch admin to see another branch's data (read-only).

```
branch_user:
  user_id=AteJoy, branch_id=1, assignment_type='primary'   ← her branch
  user_id=AteJoy, branch_id=2, assignment_type='oversight'  ← can view Manila

When Ate Joy selects Manila:
  → Can view rooms, reports, activity logs
  → CANNOT create/edit (application enforces read-only for 'oversight')
```

---

## Assignment Types

| Type | Access | Use Case |
|------|--------|----------|
| `primary` | Full access (CRUD + transactions) | Staff's home branch |
| `deployed` | Full access (CRUD + transactions) | Temporarily working at another branch |
| `oversight` | Read-only (view reports, logs) | Admin monitoring another branch |

---

## How Branch Scoping Works

```php
// In middleware or base controller:
$activeBranch = session('active_branch_id');

// Every query:
Room::where('branch_id', $activeBranch)->get();
Transaction::where('branch_id', $activeBranch)->get();

// User's available branches:
$branches = auth()->user()->branches; // via branch_user pivot

// Can user access this branch?
$canAccess = auth()->user()->branches->contains($branchId);
```

---

## Edge Cases

### Juan Has Active Shift at Cebu, Tries to Start at Manila

```
System checks: any active shift for Juan?
  SELECT * FROM shifts WHERE user_id = Juan AND status = 'active'
  → Found: Shift #30 at Cebu

ERROR: "You have an active shift at Cebu. Close it before starting at Manila."

One active shift per user at a time. Cannot work two branches simultaneously.
```

### Juan is Deployed But Manila Admin Deactivates Him

```
Manila admin → Users → Juan → Deactivate

users: is_active = false

Juan can't log in ANYWHERE (not just Manila).
This is a system-wide deactivation.

To just remove Manila access: remove the branch_user deployment, don't deactivate.
```

### Superadmin Views Cross-Branch Data

```
Superadmin doesn't use branch_user — they see ALL branches.

Superadmin role bypasses branch scoping:
  if (auth()->user()->hasRole('superadmin')) {
      // No branch filter — see everything
  }
```

---

## Tables Involved

| Table | Role |
|-------|------|
| `branch_user` | Which branches a user can access, with what level |
| `shifts` | Always has branch_id — shift belongs to one branch |
| `transactions` | Always has branch_id — transaction belongs to one branch |
| `users` | No branch_id on user — branches via pivot |
| `user_profiles` | Personal info, not branch-specific |
