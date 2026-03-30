# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

HotelV3 is a multi-branch hotel management system with role-based access control. It handles room management, guest check-in/out, POS operations, kitchen/pub inventory, and reporting.

## Technology Stack

- **Backend:** PHP 8.2+, Laravel 11, Livewire 3, Filament Tables 3
- **Frontend:** Vite 4, Tailwind CSS 3.2, Alpine.js 3.11, WireUI 2
- **Auth/Permissions:** Laravel Sanctum, Spatie Permission 6
- **Real-time:** Pusher with Laravel Echo
- **Database:** MySQL

## Development Commands

```bash
# Development servers
php artisan serve              # Laravel (localhost:8000)
npm run dev                    # Vite with hot reload

# Database
php artisan migrate --seed     # Fresh setup
php artisan migrate:fresh      # Reset all

# Testing
php artisan test                           # All tests
php artisan test --filter=TestClassName    # Specific test

# Code style
./vendor/bin/pint              # Fix with Laravel Pint
./vendor/bin/pint --test       # Check only
```

## Architecture

### Multi-Tenant Branch System
Every query must be branch-scoped:
```php
->where('branch_id', auth()->user()->branch_id)
```
Superadmins can access all branches; other roles see only their assigned branch.

### Role-Based Route Organization
Routes are split by role in `routes/`:
- `superadmin.php` - Multi-branch management
- `admin.php` - Branch configuration (middleware: `role:admin|superadmin`)
- `frontdesk.php` - Check-in/out, POS, transactions
- `back-office.php` - Reports and analytics
- `kiosk.php` - Guest self-service
- `kitchen.php`, `pub-kitchen.php` - Inventory/menu management
- `roomboy.php` - Room cleaning workflow

### Livewire 3 + Filament Tables Pattern
Most CRUD components use Filament Tables within Livewire. Standard component structure:
```php
namespace App\Livewire\Admin\Manage;

class Room extends Component implements Tables\Contracts\HasTable, \Filament\Forms\Contracts\HasForms
{
    use Tables\Concerns\InteractsWithTable;
    use \Filament\Forms\Concerns\InteractsWithForms;
    use WireUiActions;  // For dialog notifications

    protected function getTableQuery(): Builder { }
    protected function getTableColumns(): array { }
    protected function getTableActions(): array { }
    protected function getTableFilters(): array { }
}
```

Components are in `app/Livewire/{Module}/`:
- `Admin/Manage/` - Room, Type, Floor, Rate, User configuration
- `Frontdesk/Monitoring/` - RoomMonitoring, GuestTransaction, CheckOut
- `BackOffice/Reports/` - Sales, Guest, Room reports
- `Kitchen/`, `Pub/` - Category, Menu, Inventory, Transaction

### API Layer
REST endpoints in `app/Http/Controllers/API/` for kiosk and mobile apps. Use `ApiResponse` helper:
```php
use App\Helpers\ApiResponse;

return ApiResponse::success($data, 'Message', 200);
return ApiResponse::error('Error message', 400);
return ApiResponse::paginated($paginator, 'Retrieved', ResourceClass::class);
```

### Real-time Events
Pusher broadcasting for check-in notifications. Channel: `newcheckin.{branch_id}`

### Key Models
Core entities: `User`, `Guest`, `Room`, `Branch`, `Rate`, `Type`, `Floor`, `Transaction`, `CheckinDetail`, `StayingHour`

Room statuses: `Available`, `Occupied`, `Reserved`, `Maintenance`, `Uncleaned`, `Cleaning`, `Cleaned`

## Conventions

### Notifications
Use WireUI dialog for user feedback:
```php
$this->dialog()->success($title = 'Success', $description = 'Action completed.');
```

### Activity Logging
Log significant actions:
```php
ActivityLog::create([
    'branch_id' => auth()->user()->branch_id,
    'user_id' => auth()->user()->id,
    'activity' => 'Action Name',
    'description' => 'Details...',
]);
```

### Query Patterns
- Always eager load: `Model::with(['relation'])->get()`
- Branch filtering: `->where('branch_id', auth()->user()->branch_id)`
- Superadmin check: `auth()->user()->hasRole('superadmin')`

## Debugging

- `php artisan tinker` - Interactive shell
- Debugbar enabled in development
- Logs: `storage/logs/laravel.log`

## Claude Code Skills & Superpowers

### Slash Commands
- `/commit` - Stage changes and create commit with AI-generated message
- `/review-pr` - Review pull request changes with detailed feedback
- `/init` - Regenerate this CLAUDE.md file
- `/find-skills` - Discover and install additional skills

### GitHub Integration
Use `gh` CLI for GitHub operations:
```bash
gh pr create              # Create pull request
gh pr view                # View PR details
gh pr checks              # Check CI status
gh issue list             # List issues
gh issue create           # Create issue
gh repo view --web        # Open repo in browser
```

### Code Review
Ask Claude to review code:
> "Review my changes for security issues and best practices"
> "Review this PR: [URL]"
> "Check this component for N+1 queries and performance issues"

### Context7 Documentation Lookup
Fetch up-to-date documentation:
> "Look up Laravel 11 Livewire integration docs"
> "Get Filament Tables 3 action documentation"
> "Find Spatie Permission middleware usage"

### Laravel Boost Commands
Quick Laravel scaffolding:
```bash
# Generate with artisan
php artisan make:model Guest -mfc      # Model + migration + factory + controller
php artisan make:livewire Admin/NewComponent
php artisan make:event CheckOutEvent
php artisan make:listener SendCheckOutNotification

# Database
php artisan db:seed --class=RoomSeeder
php artisan migrate:rollback --step=1
php artisan schema:dump
```

### Generation Prompts
Ask Claude to generate common patterns:

**Livewire + Filament Table:**
> "Create a Livewire component for managing [Entity] with Filament table, filters, and CRUD actions"

**API Endpoint:**
> "Create an API controller for [Resource] using ApiResponse helper"

**Full Feature:**
> "Create migration, model, Livewire component, and view for [Feature]"

### Useful Compound Tasks
- "Add a new room status and update all related components"
- "Create a new role with routes, middleware, and dashboard"
- "Add a new menu item to kitchen with inventory tracking"
- "Create a transaction type with POS integration"

### Browser Automation (Chrome DevTools MCP)
Claude can interact with the running app:
- Test check-in/checkout flows
- Verify room status changes
- Debug real-time Pusher events
- Screenshot UI for review
- Fill forms and click through workflows

### Plugins & MCP Servers
Install from https://claude.com/plugins

**Recommended for this project:**
- **Superpowers** - Brainstorming, subagent development, code review, debugging, TDD, skill authoring
- **Context7** - Live documentation lookup for Laravel 11, Livewire 3, Filament, Spatie packages
- **Code Review** - AI-powered code review with confidence-based filtering for PRs
- **GitHub** - Official GitHub MCP for issues, PRs, code review, repo management
- **Frontend Design** - Production-grade frontend code with polished design
- **Playwright** - Browser automation, e2e testing, form filling, screenshots
- **Feature Dev** - Structured workflow with exploration, design, and review phases
- **Code Simplifier** - Refine code for clarity while maintaining functionality

**Already connected:**
- **Figma MCP** - Import designs, generate UI from Figma files
- **Chrome DevTools** - Browser automation, DOM inspection, performance tracing
