# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

HotelV2 is a hotel management system built with **Laravel 9** and **Livewire 2**, featuring Tailwind CSS, Alpine.js, and Vite. The system manages multiple hotel branches with role-based access control.

## Technology Stack

- **Backend:** PHP 8.0.2+, Laravel 9.19, Livewire 2.5, Spatie Permission 5.7
- **Frontend:** Vite 4.0, Tailwind CSS 3.2, Alpine.js 3.11, WireUI 1.17
- **Database:** MySQL with Laravel migrations
- **Testing:** PHPUnit 9.5, Laravel Pint (linting)

## Development Commands

```bash
# Setup
composer install && npm install
php artisan key:generate
php artisan migrate --seed

# Development
php artisan serve              # Laravel server (localhost:8000)
npm run dev                    # Vite dev server with hot reload

# Database
php artisan migrate            # Run migrations
php artisan migrate:fresh      # Reset and re-run all migrations
php artisan db:seed            # Run seeders

# Testing
php artisan test                           # Run all tests
php artisan test --filter=TestClassName    # Run specific test class
./vendor/bin/phpunit tests/Unit            # Unit tests only
./vendor/bin/phpunit tests/Feature         # Feature tests only

# Code Quality
./vendor/bin/pint              # Fix code style (Laravel Pint)
./vendor/bin/pint --test       # Check style without fixing
```

## Architecture

### Role-Based Multi-Tenant System
- 8 roles: superadmin, admin, back_office, frontdesk, kiosk, kitchen, pub_kitchen, roomboy
- Routes organized by role in separate files (`routes/admin.php`, `routes/frontdesk.php`, etc.)
- Branch-aware queries throughout (use `auth()->user()->branch_id`)

### Livewire Component Structure
Components are organized by role under `app/Http/Livewire/`:
- `Admin/` - Branch management, user management, room configuration
- `Frontdesk/` - Check-in/out, POS, transactions, room monitoring
- `BackOffice/` - Reports and analytics
- `Kiosk/` - Guest self-service
- `Kitchen/` & `Pub/` - Inventory and menu management
- `Roomboy/` - Room cleaning and maintenance

### API Layer
REST endpoints in `app/Http/Controllers/API/` using Laravel Sanctum authentication. Use `ApiResponse` helper for consistent responses:
```php
ApiResponse::success($data, 'Message', 200);
ApiResponse::error('Error message', 400);
```

### Key Models
Core entities in `app/Models/`: User, Guest, Room, Branch, Rate, Transaction, Type, Floor, CheckInDetail

## Conventions

### Livewire Components
```php
class ComponentName extends Component
{
    use Actions;  // WireUI notifications

    #[Validate('required|string')]
    public $property;

    public function mount() { }
    public function submit() { }
    public function render() { return view('livewire.path'); }
}
```

### Route Registration
Each role has its own route file with middleware protection. Use named routes:
```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('admin.dashboard');
});
```

### Query Patterns
- Always eager load relationships: `Model::with('relation')->get()`
- Filter by branch: `->where('branch_id', auth()->user()->branch_id)`
- Use Eloquent over raw queries

## File Locations

| Purpose | Location |
|---------|----------|
| Livewire component | `app/Http/Livewire/{Module}/Name.php` |
| API controller | `app/Http/Controllers/API/NameController.php` |
| Model | `app/Models/Name.php` |
| Migration | `database/migrations/` |
| View | `resources/views/{section}/name.blade.php` |
| Route file | `routes/{role}.php` |
| Test | `tests/Feature/` or `tests/Unit/` |

## Debugging

- `php artisan tinker` - Interactive shell
- Laravel Debugbar enabled in development
- Logs at `storage/logs/laravel.log`
