# V4 Technology Stack

Packages, tools, and architecture decisions for the V4 rebuild.

---

## Target Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| **PHP** | PHP | ^8.3 |
| **Framework** | Laravel | 12 (or 13 if available) |
| **Admin Panel** | Filament | 5 |
| **Livewire** | Livewire | 4 |
| **Frontend** | Alpine.js | 3.x |
| **CSS** | Tailwind CSS | 4.x |
| **Build** | Vite | 6.x |
| **Database** | MySQL | 8.x |
| **Auth** | Laravel Sanctum | Latest |
| **Permissions** | Spatie Permission | ^6.0 |
| **Activity Log** | Spatie Activitylog | ^4.0 |
| **Media** | Spatie Media Library | ^11.0 |
| **PDF** | Laravel DomPDF or Spatie Browsershot | Latest |
| **Real-time** | Pusher + Laravel Echo | Latest |
| **UI Components** | WireUI | ^2.0 (or Flux UI) |

---

## V3 Packages (Reference)

### Composer Packages (V3)

| Package | V3 Version | Keep for V4? | Notes |
|---------|-----------|-------------|-------|
| laravel/framework | ^11.0 | Upgrade to ^12 or ^13 | Core framework |
| livewire/livewire | ^3.0 | Upgrade to ^4.0 | Livewire 4 with Filament 5 |
| filament/tables | ^3.0 | Upgrade to filament/filament ^5.0 | Full Filament 5 (not just tables) |
| laravel/jetstream | ^5.0 | **Review** | May not need if using Filament auth |
| laravel/sanctum | ^4.0 | Keep (latest) | API auth for kiosk |
| spatie/laravel-permission | ^6.0 | Keep | RBAC |
| wireui/wireui | ^2.0 | Keep or switch to Flux UI | Dialog, notifications |
| pusher/pusher-php-server | ^7.2 | Keep | Real-time notifications |
| blade-ui-kit/blade-ui-kit | ^0.5 | **Review** | May not need with Filament 5 |
| spatie/laravel-db-snapshots | * | **Remove** | Dev tool, not for production |
| guzzlehttp/guzzle | ^7.2 | Keep | HTTP client |
| laravel/tinker | ^2.9 | Keep | Dev tool |

### Composer Dev Packages (V3)

| Package | V3 Version | Keep? | Notes |
|---------|-----------|-------|-------|
| barryvdh/laravel-debugbar | ^3.13 | Keep | Dev debugging |
| fakerphp/faker | ^1.9.1 | Keep | Test data |
| laravel/pint | ^1.0 | Keep | Code style |
| laravel/sail | ^1.0.1 | Keep | Docker dev |
| mockery/mockery | ^1.6 | Keep | Testing |
| nunomaduro/collision | ^8.0 | Keep | Error reporting |
| phpunit/phpunit | ^11.0 | Keep | Testing |
| spatie/laravel-ignition | ^2.0 | Keep | Error pages |

### NPM Packages (V3)

| Package | V3 Version | Keep? | Notes |
|---------|-----------|-------|-------|
| alpinejs | ^3.11.1 | Keep (latest) | Frontend interactivity |
| @alpinejs/focus | ^3.11.1 | Keep | Focus management |
| @alpinejs/collapse | ^3.10.5 | Keep | Collapse animations |
| tailwindcss | ^3.2.6 | **Upgrade to ^4.0** | Latest Tailwind |
| @tailwindcss/forms | ^0.5.3 | Keep (latest) | Form styling |
| @tailwindcss/typography | ^0.5.9 | Keep (latest) | Prose styling |
| laravel-vite-plugin | ^0.7.2 | Keep (latest) | Vite integration |
| vite | ^4.0.0 | **Upgrade to ^6.0** | Build tool |
| axios | ^1.1.2 | Keep | HTTP client |
| laravel-echo | ^1.15.0 | Keep (latest) | Pusher client |
| pusher-js | ^8.0.1 | Keep (latest) | Pusher client |
| postcss | ^8.4.14 | Keep (latest) | CSS processing |
| autoprefixer | ^10.4.13 | Keep (latest) | CSS vendor prefixes |
| lodash | ^4.17.19 | **Review** | May not need full lodash |
| @formkit/addons | ^1.0.0-beta.12 | **Remove** | Beta, not used meaningfully |
| @formkit/auto-animate | ^1.0.0-beta.5 | **Remove** | Beta, replaced by Alpine transitions |

---

## New Packages for V4

| Package | Purpose |
|---------|---------|
| **spatie/laravel-activitylog** | Automatic model audit logging (before/after) |
| **spatie/laravel-medialibrary** | File uploads (profile photos, menu images) |
| **barryvdh/laravel-dompdf** or **spatie/browsershot** | PDF generation for reports |
| **filament/filament** | Full admin panel (replaces filament/tables only) |
| **maatwebsite/excel** | Excel export for reports (optional) |

---

## Architecture Patterns (from general-pos)

### Traits / Concerns

Adopt the trait-based architecture from the general-pos project:

```
app/
├── Traits/
│   ├── ApiResponse.php              ← Standard JSON responses for API
│   ├── ResolvesActiveShift.php      ← Find current user's active shift
│   ├── ModelRelations/              ← Separate relation definitions per model
│   │   ├── StayRelations.php
│   │   ├── TransactionRelations.php
│   │   └── ...
│   └── ModelScopes/                 ← Query scoping for multi-tenancy
│       ├── ScopesToBranch.php       ← Auto-filter by active branch
│       └── ScopesToShift.php        ← Auto-filter by active shift
├── Concerns/
│   ├── PasswordValidationRules.php
│   └── ProfileValidationRules.php
├── Enums/
│   ├── RoomStatus.php               ← available, occupied, uncleaned, etc.
│   ├── TransactionType.php          ← room_charge, payment, deposit_in, etc.
│   ├── ShiftPeriod.php              ← AM, PM
│   ├── StayStatus.php               ← active, checked_out, cancelled
│   ├── ServiceArea.php              ← kitchen, frontdesk, pub, amenity, damage
│   └── CleaningStatus.php           ← pending, in_progress, completed
├── Services/
│   ├── CheckInService.php
│   ├── CheckOutService.php
│   ├── ExtensionService.php
│   ├── TransferService.php
│   ├── PaymentService.php
│   └── ShiftService.php
└── ...
```

### Branch Scoping (Multi-Tenancy)

```php
// app/Traits/ModelScopes/ScopesToBranch.php
trait ScopesToBranch
{
    public static function bootScopesToBranch(): void
    {
        static::addGlobalScope('branch', function ($query) {
            if (auth()->check() && !auth()->user()->hasRole('superadmin')) {
                $query->where('branch_id', session('active_branch_id'));
            }
        });
    }
}

// Usage on any model:
class Room extends Model
{
    use ScopesToBranch;
    // All queries automatically filtered by branch
}
```

### Enum Usage

```php
// app/Enums/TransactionType.php
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
}
```

---

## Folder Structure (V4 Target)

```
app/
├── Actions/               ← Single-purpose action classes
├── Concerns/              ← Validation rules, shared behaviors
├── Console/               ← Artisan commands (kiosk cleanup, etc.)
├── Enums/                 ← PHP 8.1 enums for statuses, types
├── Exceptions/            ← Custom exceptions
├── Filament/              ← Filament admin panel resources
│   ├── Admin/             ← Branch admin resources
│   ├── Superadmin/        ← System-wide resources
│   └── Shared/            ← Shared widgets, pages
├── Http/
│   ├── Controllers/
│   │   └── Api/           ← API controllers (kiosk, mobile)
│   ├── Middleware/         ← Branch scope, shift required, etc.
│   └── Requests/          ← Form requests
├── Livewire/              ← Livewire components
│   ├── Frontdesk/         ← Room monitoring, guest transactions
│   ├── Roomboy/           ← Cleaning dashboard
│   ├── BackOffice/        ← Reports
│   └── Kiosk/             ← Kiosk web interface (if not API-only)
├── Models/                ← Eloquent models
├── Observers/             ← Model observers (auto-update cache columns)
├── Policies/              ← Authorization policies
├── Providers/             ← Service providers
├── Services/              ← Business logic services
│   ├── CheckInService.php
│   ├── CheckOutService.php
│   ├── ExtensionService.php
│   ├── TransferService.php
│   ├── PaymentService.php
│   ├── ShiftService.php
│   └── StockService.php
└── Traits/                ← Reusable traits
    ├── ApiResponse.php
    ├── ResolvesActiveShift.php
    ├── ModelRelations/
    └── ModelScopes/
        ├── ScopesToBranch.php
        └── ScopesToShift.php
```

---

## Key Architecture Decisions

| Decision | Choice | Why |
|----------|--------|-----|
| Admin panel | Filament 5 | Built-in CRUD, tables, forms, widgets |
| Frontend interactivity | Livewire 4 + Alpine.js | Real-time without SPA complexity |
| API for kiosk/mobile | Sanctum token auth | Stateless, per-device tokens |
| Multi-tenancy | Global scope trait | Auto-filter by branch, clean code |
| Business logic | Service classes | Keep Livewire components thin |
| Status/type values | PHP Enums | Type-safe, IDE autocomplete |
| File uploads | Spatie Media Library | Handles conversions, disk management |
| PDF reports | DomPDF or Browsershot | Print-ready shift reports |
| Real-time | Pusher + Echo | Kiosk notifications, room status |
