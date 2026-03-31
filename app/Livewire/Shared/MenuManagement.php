<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use Livewire\WithFileUploads;
use WireUi\Traits\WireUiActions;
use Filament\Tables;
use Filament\Forms;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Models\MenuItem;
use App\Models\ItemCategory;
use App\Models\ItemInventory;
use App\Models\Department;
use App\Models\ActivityLog;
use App\Models\Branch;

class MenuManagement extends Component implements Tables\Contracts\HasTable, \Filament\Forms\Contracts\HasForms
{
    use Tables\Concerns\InteractsWithTable;
    use \Filament\Forms\Concerns\InteractsWithForms;
    use WireUiActions;
    use WithFileUploads;

    public string $department = 'kitchen';
    public $branchId;
    public string $viewMode = 'table';

    // Category management modal
    public bool $categoryModal = false;
    public $categoryName = '';
    public $categoryParentId;
    public $editCategoryId;

    public function mount(string $department = 'kitchen')
    {
        $this->department = $department;
        $this->branchId = auth()->user()->branch_id;
    }

    public function toggleView()
    {
        $this->viewMode = $this->viewMode === 'table' ? 'grid' : 'table';
    }

    // ── Helpers ──────────────────────────────────────────────

    private function getBranchId(): int
    {
        return auth()->user()->hasRole('superadmin') ? $this->branchId : auth()->user()->branch_id;
    }

    private function getDepartmentId(): int
    {
        return Department::where('slug', $this->department)->value('id');
    }

    private function getDepartmentLabel(): string
    {
        return match ($this->department) {
            'kitchen' => 'Main Kitchen',
            'pub' => 'Pub Kitchen',
            'frontdesk' => 'Frontdesk',
            default => ucfirst($this->department),
        };
    }

    // ── Filament Table ──────────────────────────────────────

    protected function getTableQuery(): Builder
    {
        return MenuItem::query()
            ->where('branch_id', $this->getBranchId())
            ->where('department_id', $this->getDepartmentId())
            ->with(['category.parent', 'inventory']);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\ImageColumn::make('image')
                ->label('')
                ->circular()
                ->disk('public')
                ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=No+Image&background=e2e8f0&color=94a3b8&size=40')
                ->size(40),
            Tables\Columns\TextColumn::make('name')
                ->label('ITEM NAME')
                ->searchable()
                ->sortable()
                ->weight('bold'),
            Tables\Columns\TextColumn::make('item_code')
                ->label('CODE')
                ->searchable()
                ->placeholder('—')
                ->toggleable(),
            Tables\Columns\TextColumn::make('category.parent.name')
                ->label('TYPE')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Food' => 'success',
                    'Drinks' => 'info',
                    default => 'gray',
                }),
            Tables\Columns\TextColumn::make('category.name')
                ->label('CATEGORY')
                ->sortable(),
            Tables\Columns\TextColumn::make('price')
                ->label('PRICE')
                ->money('PHP')
                ->sortable(),
            Tables\Columns\TextColumn::make('inventory.number_of_serving')
                ->label('STOCK')
                ->default(0)
                ->badge()
                ->color(fn ($state): string => match (true) {
                    $state <= 0 => 'danger',
                    $state <= 10 => 'warning',
                    default => 'success',
                })
                ->formatStateUsing(fn ($state) => $state > 0 ? $state . ' servings' : 'Out of stock'),
            Tables\Columns\IconColumn::make('is_available')
                ->label('ACTIVE')
                ->boolean()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make('add_item')
                ->label('Add Item')
                ->icon('heroicon-o-plus')
                ->color('info')
                ->button()
                ->disableCreateAnother()
                ->modalHeading('Add New Menu Item')
                ->form($this->getMenuFormSchema())
                ->mutateFormDataUsing(function (array $data): array {
                    $data['branch_id'] = $this->getBranchId();
                    $data['department_id'] = $this->getDepartmentId();
                    return $data;
                })
                ->after(function ($record) {
                    ActivityLog::create([
                        'branch_id' => $this->getBranchId(),
                        'user_id' => auth()->id(),
                        'activity' => 'Create Menu Item',
                        'description' => 'Created menu item: ' . $record->name . ' (' . $this->getDepartmentLabel() . ')',
                    ]);
                    $this->dialog()->success('Success', 'Menu item created successfully');
                }),
            Action::make('manage_categories')
                ->label('Manage Categories')
                ->icon('heroicon-o-tag')
                ->color('gray')
                ->button()
                ->action(fn () => $this->categoryModal = true),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('add_stock')
                ->label('Stock')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->button()
                ->size('sm')
                ->modalHeading(fn ($record) => 'Add Stock — ' . $record->name)
                ->modalWidth('md')
                ->form([
                    TextInput::make('quantity')
                        ->label('Quantity to Add')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->suffix('servings'),
                ])
                ->action(function ($record, array $data) {
                    if ($record->inventory) {
                        $record->inventory->update([
                            'number_of_serving' => $record->inventory->number_of_serving + $data['quantity'],
                        ]);
                    } else {
                        ItemInventory::create([
                            'branch_id' => $record->branch_id,
                            'menu_item_id' => $record->id,
                            'number_of_serving' => $data['quantity'],
                        ]);
                    }

                    ActivityLog::create([
                        'branch_id' => $this->getBranchId(),
                        'user_id' => auth()->id(),
                        'activity' => 'Add Stock',
                        'description' => 'Added ' . $data['quantity'] . ' servings to ' . $record->name,
                    ]);

                    $this->dialog()->success('Stock Updated', $data['quantity'] . ' servings added');
                }),
            Tables\Actions\EditAction::make()
                ->form($this->getMenuFormSchema())
                ->color('success')
                ->button()
                ->size('sm')
                ->after(function ($record) {
                    ActivityLog::create([
                        'branch_id' => $this->getBranchId(),
                        'user_id' => auth()->id(),
                        'activity' => 'Update Menu Item',
                        'description' => 'Updated menu item: ' . $record->name,
                    ]);
                }),
            Tables\Actions\DeleteAction::make()
                ->button()
                ->size('sm')
                ->after(function ($record) {
                    ActivityLog::create([
                        'branch_id' => $this->getBranchId(),
                        'user_id' => auth()->id(),
                        'activity' => 'Delete Menu Item',
                        'description' => 'Deleted menu item: ' . $record->name,
                    ]);
                }),
        ];
    }

    protected function getTableFilters(): array
    {
        $branchId = $this->getBranchId();
        $filters = [];

        if (auth()->user()->hasRole('superadmin')) {
            $filters[] = SelectFilter::make('branch_id')
                ->label('Branch')
                ->options(Branch::pluck('name', 'id'));
        }

        $filters[] = SelectFilter::make('main_category')
            ->label('Type')
            ->options(
                ItemCategory::where('branch_id', $branchId)
                    ->mainCategories()
                    ->pluck('name', 'id')
            )
            ->query(function (Builder $query, array $data) {
                if ($data['value']) {
                    $query->whereHas('category', fn ($q) => $q->where('parent_id', $data['value']));
                }
            });

        $filters[] = SelectFilter::make('category_id')
            ->label('Category')
            ->options(
                ItemCategory::where('branch_id', $branchId)
                    ->subcategories()
                    ->pluck('name', 'id')
            );

        return $filters;
    }

    protected function getTableFiltersLayout(): ?string
    {
        return FiltersLayout::AboveContent->value;
    }

    // ── Menu Form Schema ────────────────────────────────────

    private function getMenuFormSchema(): array
    {
        $branchId = $this->getBranchId();

        return [
            Grid::make(2)->schema([
                TextInput::make('name')
                    ->label('Item Name')
                    ->required()
                    ->columnSpan(1),
                TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->required()
                    ->prefix('₱')
                    ->columnSpan(1),
            ]),
            Grid::make(2)->schema([
                Select::make('category_id')
                    ->label('Category')
                    ->options(
                        ItemCategory::where('branch_id', $branchId)
                            ->subcategories()
                            ->get()
                            ->mapWithKeys(fn ($cat) => [$cat->id => ($cat->parent?->name ?? '') . ' > ' . $cat->name])
                    )
                    ->searchable()
                    ->required(),
                TextInput::make('item_code')
                    ->label('Item Code')
                    ->nullable(),
            ]),
            FileUpload::make('image')
                ->label('Image')
                ->image()
                ->directory('menu_images')
                ->maxSize(5120)
                ->nullable(),
        ];
    }

    // ── Category Management ─────────────────────────────────

    public function saveCategory()
    {
        $this->validate([
            'categoryName' => 'required|string|max:255',
            'categoryParentId' => 'required|exists:item_categories,id',
        ]);

        if ($this->editCategoryId) {
            ItemCategory::where('id', $this->editCategoryId)->update([
                'name' => $this->categoryName,
                'parent_id' => $this->categoryParentId,
            ]);

            ActivityLog::create([
                'branch_id' => $this->getBranchId(),
                'user_id' => auth()->id(),
                'activity' => 'Update Category',
                'description' => 'Updated category: ' . $this->categoryName,
            ]);
        } else {
            ItemCategory::create([
                'name' => $this->categoryName,
                'parent_id' => $this->categoryParentId,
                'branch_id' => $this->getBranchId(),
            ]);

            ActivityLog::create([
                'branch_id' => $this->getBranchId(),
                'user_id' => auth()->id(),
                'activity' => 'Create Category',
                'description' => 'Created category: ' . $this->categoryName,
            ]);
        }

        $this->reset(['categoryName', 'categoryParentId', 'editCategoryId']);
        $this->dialog()->success('Success', 'Category saved');
    }

    public function editCategory($id)
    {
        $cat = ItemCategory::find($id);
        $this->editCategoryId = $cat->id;
        $this->categoryName = $cat->name;
        $this->categoryParentId = $cat->parent_id;
    }

    public function deleteCategory($id)
    {
        $cat = ItemCategory::find($id);
        $name = $cat->name;
        $cat->delete();

        ActivityLog::create([
            'branch_id' => $this->getBranchId(),
            'user_id' => auth()->id(),
            'activity' => 'Delete Category',
            'description' => 'Deleted category: ' . $name,
        ]);

        $this->dialog()->success('Success', 'Category deleted');
    }

    public function cancelEditCategory()
    {
        $this->reset(['categoryName', 'categoryParentId', 'editCategoryId']);
    }

    // ── Grid View Data ──────────────────────────────────────

    public function getGridItemsProperty()
    {
        return MenuItem::where('branch_id', $this->getBranchId())
            ->where('department_id', $this->getDepartmentId())
            ->with(['category.parent', 'inventory'])
            ->get();
    }

    // ── Render ──────────────────────────────────────────────

    public function render()
    {
        $branchId = $this->getBranchId();

        return view('livewire.shared.menu-management', [
            'departmentLabel' => $this->getDepartmentLabel(),
            'mainCategories' => ItemCategory::where('branch_id', $branchId)->mainCategories()->get(),
            'subcategories' => ItemCategory::where('branch_id', $branchId)->subcategories()->with('parent')->get(),
            'branches' => auth()->user()->hasRole('superadmin') ? Branch::all() : collect(),
            'gridItems' => $this->viewMode === 'grid' ? $this->gridItems : collect(),
        ]);
    }
}
