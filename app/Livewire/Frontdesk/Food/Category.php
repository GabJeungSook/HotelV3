<?php

namespace App\Livewire\Frontdesk\Food;

use App\Models\ActivityLog;
use App\Models\ItemCategory;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;

class Category extends Component implements Tables\Contracts\HasTable, \Filament\Forms\Contracts\HasForms
{
    use Tables\Concerns\InteractsWithTable;
    use \Filament\Forms\Concerns\InteractsWithForms;
    use WireUiActions;

    public $branch_id;

    protected function getTableQuery(): Builder
    {
        if (auth()->user()->hasRole('superadmin')) {
            return ItemCategory::query()->subcategories();
        } else {
            return ItemCategory::query()
                ->where('branch_id', auth()->user()->branch_id)
                ->subcategories();
        }
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('branch.name')
                ->label('BRANCH')
                ->formatStateUsing(fn(string $state): string => strtoupper($state))
                ->sortable()
                ->visible(fn () => auth()->user()->hasRole('superadmin')),
            TextColumn::make('parent.name')
                ->label('MAIN CATEGORY')
                ->sortable(),
            TextColumn::make('name')
                ->label('SUBCATEGORY NAME')
                ->searchable()
                ->sortable(),
        ];
    }

    protected function getTableFilters(): array
    {
        if (auth()->user()->hasRole('superadmin')) {
            return [
                SelectFilter::make('branch')->relationship('branch', 'name'),
            ];
        } else {
            return [];
        }
    }

    protected function getTableFiltersLayout(): ?string
    {
        return FiltersLayout::AboveContent->value;
    }

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make('add_category')
                ->label('Add New Category')
                ->icon('heroicon-o-plus')
                ->color('info')
                ->button()
                ->disableCreateAnother()
                ->modalHeading('Add New Category')
                ->form([
                    Grid::make(1)->schema([
                        Select::make('branch_id')
                            ->label('Branch')
                            ->options(\App\Models\Branch::pluck('name', 'id'))
                            ->required()
                            ->reactive()
                            ->visible(fn () => auth()->user()->hasRole('superadmin')),
                        Select::make('parent_id')
                            ->label('Main Category')
                            ->options(function (callable $get) {
                                $branchId = auth()->user()->hasRole('superadmin')
                                    ? $get('branch_id')
                                    : auth()->user()->branch_id;
                                return $branchId
                                    ? ItemCategory::where('branch_id', $branchId)->mainCategories()->pluck('name', 'id')
                                    : [];
                            })
                            ->required(),
                        TextInput::make('name')
                            ->required(),
                    ]),
                ])
                ->action(function (array $data) {
                    $branchId = auth()->user()->hasRole('superadmin')
                        ? $data['branch_id']
                        : auth()->user()->branch_id;

                    ItemCategory::create([
                        'name' => $data['name'],
                        'parent_id' => $data['parent_id'],
                        'branch_id' => $branchId,
                    ]);

                    ActivityLog::create([
                        'branch_id' => $branchId,
                        'user_id' => auth()->user()->id,
                        'activity' => 'Create Category',
                        'description' => 'Created category ' . $data['name'],
                    ]);

                    $this->dialog()->success(
                        $title = 'Success',
                        $description = 'Category Added Successfully'
                    );
                }),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\EditAction::make()
                ->color('success')
                ->button()
                ->size('sm')
                ->action(function ($record, $data) {
                    $record->update($data);
                    $this->dialog()->success(
                        $title = 'Category Updated',
                        $description = 'Category was successfully updated'
                    );
                })
                ->form(function ($record) {
                    $branchId = $record->branch_id;
                    return [
                        Grid::make(1)->schema([
                            Select::make('parent_id')
                                ->label('Main Category')
                                ->options(ItemCategory::where('branch_id', $branchId)->mainCategories()->pluck('name', 'id'))
                                ->default($record->parent_id)
                                ->required(),
                            TextInput::make('name')
                                ->default($record->name)
                                ->required(),
                        ]),
                    ];
                })
                ->modalHeading('Update Category'),
            Tables\Actions\DeleteAction::make()
                ->button()
                ->size('sm'),
        ];
    }

    public function render()
    {
        return view('livewire.frontdesk.food.category');
    }
}
