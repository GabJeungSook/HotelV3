# V4 Architecture Patterns Reference

Patterns to follow when building V4, based on the general-pos project architecture.

---

## 1. Bootstrap (Laravel 12 Style — No Kernel.php)

```php
// bootstrap/app.php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Kiosk API routes at /api/kiosk
            Route::middleware('api')
                ->prefix('api/kiosk')
                ->name('kiosk.')
                ->group(base_path('routes/kiosk-api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'shift.required' => \App\Http\Middleware\RequireActiveShift::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

**Key:** Routes, middleware, and exceptions all configured in one file. No Kernel.php.

---

## 2. Branch Scoping Trait (Multi-Tenancy)

```php
// app/Traits/ModelScopes/ScopesToBranch.php
namespace App\Traits\ModelScopes;

use Illuminate\Database\Eloquent\Builder;

trait ScopesToBranch
{
    public static function bootScopesToBranch(): void
    {
        static::addGlobalScope('branch', function (Builder $query) {
            if (auth()->check() && !auth()->user()->hasRole('superadmin')) {
                $query->where(
                    static::getTable() . '.branch_id',
                    session('active_branch_id')
                );
            }
        });

        // Auto-set branch_id on create
        static::creating(function ($model) {
            if (auth()->check() && !$model->branch_id) {
                $model->branch_id = session('active_branch_id');
            }
        });
    }
}

// Usage — add to any branch-scoped model:
class Room extends Model
{
    use ScopesToBranch;
    // All queries auto-filtered by branch. No manual where() needed.
}
```

---

## 3. Model Relation Traits

Keep models clean by extracting relations into traits:

```php
// app/Traits/ModelRelations/StayRelations.php
namespace App\Traits\ModelRelations;

use App\Models\Branch;
use App\Models\Room;
use App\Models\Rate;
use App\Models\StayExtension;
use App\Models\RoomTransfer;
use App\Models\Transaction;
use App\Models\User;

trait StayRelations
{
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function rate()
    {
        return $this->belongsTo(Rate::class);
    }

    public function extensions()
    {
        return $this->hasMany(StayExtension::class);
    }

    public function transfers()
    {
        return $this->hasMany(RoomTransfer::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function checkedOutBy()
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }
}

// Usage in model:
class Stay extends Model
{
    use ScopesToBranch, StayRelations;

    protected $casts = [
        'check_in_at' => 'datetime',
        'expected_checkout_at' => 'datetime',
        'actual_checkout_at' => 'datetime',
        'status' => StayStatus::class,
        'initial_amount' => 'decimal:2',
    ];
}
```

---

## 4. PHP Enums

```php
// app/Enums/TransactionType.php
namespace App\Enums;

enum TransactionType: string
{
    case RoomCharge = 'room_charge';
    case Extension = 'extension';
    case TransferFee = 'transfer_fee';
    case Food = 'food';
    case Amenity = 'amenity';
    case Damage = 'damage';
    case DepositIn = 'deposit_in';
    case DepositOut = 'deposit_out';
    case Payment = 'payment';
    case Void = 'void';

    public function isCharge(): bool
    {
        return in_array($this, [
            self::RoomCharge, self::Extension, self::TransferFee,
            self::Food, self::Amenity, self::Damage,
        ]);
    }

    public function isCashIn(): bool
    {
        return in_array($this, [self::Payment, self::DepositIn]);
    }

    public function isCashOut(): bool
    {
        return $this === self::DepositOut;
    }

    public function label(): string
    {
        return match($this) {
            self::RoomCharge => 'Room Charge',
            self::Extension => 'Extension',
            self::TransferFee => 'Transfer Fee',
            self::Food => 'Food & Beverages',
            self::Amenity => 'Amenity',
            self::Damage => 'Damage',
            self::DepositIn => 'Deposit',
            self::DepositOut => 'Deposit Out',
            self::Payment => 'Payment',
            self::Void => 'Void',
        };
    }
}

// app/Enums/RoomStatus.php
enum RoomStatus: string
{
    case Available = 'available';
    case Occupied = 'occupied';
    case Reserved = 'reserved';
    case Maintenance = 'maintenance';
    case Uncleaned = 'uncleaned';
    case Cleaning = 'cleaning';
    case Cleaned = 'cleaned';

    public function color(): string
    {
        return match($this) {
            self::Available => 'green',
            self::Occupied => 'red',
            self::Reserved => 'blue',
            self::Maintenance => 'gray',
            self::Uncleaned => 'orange',
            self::Cleaning => 'yellow',
            self::Cleaned => 'teal',
        };
    }
}

// app/Enums/StayStatus.php
enum StayStatus: string
{
    case Active = 'active';
    case CheckedOut = 'checked_out';
    case Cancelled = 'cancelled';
}

// app/Enums/ShiftPeriod.php
enum ShiftPeriod: string
{
    case AM = 'AM';
    case PM = 'PM';
}

// app/Enums/ServiceArea.php
enum ServiceArea: string
{
    case Kitchen = 'kitchen';
    case Frontdesk = 'frontdesk';
    case Pub = 'pub';
    case Amenity = 'amenity';
    case Damage = 'damage';
}

// app/Enums/CleaningStatus.php
enum CleaningStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
}
```

---

## 5. Service Classes

```php
// app/Services/PaymentService.php
namespace App\Services;

use App\Models\Stay;
use App\Models\Transaction;
use App\Enums\TransactionType;

class PaymentService
{
    public function payCharge(Transaction $charge, int $shiftId, int $userId): Transaction
    {
        return Transaction::create([
            'branch_id' => $charge->branch_id,
            'stay_id' => $charge->stay_id,
            'shift_id' => $shiftId,
            'type' => TransactionType::Payment,
            'amount' => $charge->amount,
            'description' => 'Cash payment',
            'linked_transaction_id' => $charge->id,
            'created_by' => $userId,
        ]);
    }

    public function payWithDeposit(Transaction $charge, int $shiftId, int $userId): Transaction
    {
        return Transaction::create([
            'branch_id' => $charge->branch_id,
            'stay_id' => $charge->stay_id,
            'shift_id' => $shiftId,
            'type' => TransactionType::DepositOut,
            'amount' => $charge->amount,
            'description' => 'Paid from deposit',
            'linked_transaction_id' => $charge->id,
            'created_by' => $userId,
        ]);
    }

    public function getDepositBalance(Stay $stay): float
    {
        return $stay->transactions()
            ->selectRaw("
                SUM(CASE WHEN type = ? THEN amount ELSE 0 END) -
                SUM(CASE WHEN type = ? THEN amount ELSE 0 END) as balance
            ", [TransactionType::DepositIn, TransactionType::DepositOut])
            ->value('balance') ?? 0;
    }

    public function getUnpaidCharges(Stay $stay)
    {
        $paidIds = $stay->transactions()
            ->whereIn('type', [TransactionType::Payment, TransactionType::DepositOut])
            ->whereNotNull('linked_transaction_id')
            ->pluck('linked_transaction_id');

        return $stay->transactions()
            ->whereIn('type', TransactionType::charges())
            ->whereNotIn('id', $paidIds)
            ->get();
    }
}
```

---

## 6. API Structure (for Kiosk)

```php
// routes/kiosk-api.php
use App\Http\Controllers\Api\Kiosk;

// Public — kiosk device activation
Route::post('/activate', [Kiosk\DeviceController::class, 'activate']);

// Authenticated — kiosk operations
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/heartbeat', [Kiosk\DeviceController::class, 'heartbeat']);

    // Check-in
    Route::get('/room-types', [Kiosk\CatalogController::class, 'roomTypes']);
    Route::get('/rooms/{roomType}', [Kiosk\CatalogController::class, 'availableRooms']);
    Route::get('/rates/{room}', [Kiosk\CatalogController::class, 'roomRates']);
    Route::get('/discounts', [Kiosk\CatalogController::class, 'activeDiscounts']);
    Route::post('/check-in', [Kiosk\RequestController::class, 'checkIn']);

    // Check-out
    Route::get('/stay/{code}', [Kiosk\RequestController::class, 'lookupStay']);
    Route::post('/check-out', [Kiosk\RequestController::class, 'checkOut']);
});
```

**API Resource pattern:**

```php
// app/Http/Resources/StayResource.php
class StayResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'guest_name' => $this->guest_name,
            'room_number' => $this->snapshot_room_number,
            'room_type' => $this->snapshot_room_type_name,
            'check_in_at' => $this->check_in_at->toIso8601String(),
            'expected_checkout_at' => $this->expected_checkout_at->toIso8601String(),
            'total_charges' => $this->total_charges,
            'total_paid' => $this->total_paid,
            'deposit_balance' => $this->total_deposit_in - $this->depositOutTotal(),
            'status' => $this->status->value,
        ];
    }
}
```

**ApiResponse trait:**

```php
// app/Traits/ApiResponse.php
trait ApiResponse
{
    protected function success($data = null, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error(string $message = 'Error', int $code = 400, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}
```

---

## 7. Shift Required Middleware

```php
// app/Http/Middleware/RequireActiveShift.php
namespace App\Http\Middleware;

use Closure;
use App\Models\Shift;

class RequireActiveShift
{
    public function handle($request, Closure $next)
    {
        $hasActiveShift = Shift::where('user_id', auth()->id())
            ->where('status', 'active')
            ->exists();

        if (!$hasActiveShift) {
            return redirect()->route('frontdesk.shift.start')
                ->with('error', 'You must start a shift before processing transactions.');
        }

        return $next($request);
    }
}

// Applied in routes:
Route::middleware(['auth', 'role:frontdesk', 'shift.required'])->group(function () {
    Route::get('/room-monitoring', RoomMonitoring::class);
    Route::get('/guest-transaction/{stay}', GuestTransaction::class);
    // ... all frontdesk transaction routes
});
```

---

## 8. Observer for Cache Columns

```php
// app/Observers/TransactionObserver.php
namespace App\Observers;

use App\Models\Transaction;
use App\Enums\TransactionType;

class TransactionObserver
{
    public function created(Transaction $transaction): void
    {
        $stay = $transaction->stay;

        // Update cache columns on stays
        if ($transaction->type->isCharge()) {
            $stay->increment('total_charges', $transaction->amount);
        }

        if ($transaction->type === TransactionType::Payment) {
            $stay->increment('total_paid', $transaction->amount);
        }

        if ($transaction->type === TransactionType::DepositIn) {
            $stay->increment('total_deposit_in', $transaction->amount);
        }

        // Update shift running totals
        $shift = $transaction->shift;

        if ($transaction->type === TransactionType::Payment) {
            $shift->increment('total_payments', $transaction->amount);
        }

        if ($transaction->type === TransactionType::DepositIn) {
            $shift->increment('total_deposit_collected', $transaction->amount);
        }

        if ($transaction->type === TransactionType::DepositOut
            && $transaction->linked_transaction_id === null) {
            $shift->increment('total_deposit_refunded', $transaction->amount);
        }
    }
}
```

---

## 9. Claude Skills (for Development)

These skills should be set up in `.claude/skills/` for the V4 project:

| Skill | Purpose |
|-------|---------|
| `livewire-development` | Livewire 4 component patterns, reactivity, forms |
| `fluxui-development` | Flux UI Free component usage (if using Flux) |
| `tailwindcss-development` | Tailwind v4 configuration and utilities |
| `pest-testing` | Pest 4 testing patterns for Laravel |

Reference source: `general-pos/.claude/skills/`
