<?php

namespace App\Livewire\Admin\Manage;

use App\Models\ActivityLog;
use Livewire\Component;
use App\Models\RequestableItem;
use WireUi\Traits\WireUiActions;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;

class Amenities extends Component implements Tables\Contracts\HasTable, \Filament\Forms\Contracts\HasForms
{
    use Tables\Concerns\InteractsWithTable;
    use \Filament\Forms\Concerns\InteractsWithForms;
    use WireUiActions;

    public $branch_id;

    public function render()
    {
        return view('livewire.admin.manage.amenities');
    }

    protected function getTableQuery(): Builder
    {
        if (auth()->user()->hasRole('superadmin')) {
            return RequestableItem::query();
        } else {
            return RequestableItem::query()->where(
                'branch_id',
                auth()->user()->branch_id
            );
        }
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('branch.name')
                ->label('BRANCH')
                ->formatStateUsing(
                    fn(string $state): string => strtoupper("{$state}")
                )
                ->sortable()
                ->visible(fn () => auth()->user()->hasRole('superadmin')),
            Tables\Columns\TextColumn::make('name')
                ->label('NAME')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('price')
                ->formatStateUsing(function (string $state) {
                    return '₱' . number_format($state, 2) . '';
                })
                ->label('AMOUNT')
                ->searchable()
                ->sortable(),
        ];
    }

    protected function getTableFilters(): array
    {
        if (auth()->user()->hasRole('superadmin')) {
            return [
                SelectFilter::make('branch')->relationship('branch', 'name')
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
            CreateAction::make('add_amenity')
                ->label('Add New Amenity')
                ->icon('heroicon-o-plus')
                ->color('info')
                ->button()
                ->disableCreateAnother()
                ->modalHeading('Add New Amenity')
                ->form([
                    Grid::make(1)->schema([
                        Select::make('branch_id')
                            ->label('Branch')
                            ->options(\App\Models\Branch::pluck('name', 'id'))
                            ->required()
                            ->visible(fn () => auth()->user()->hasRole('superadmin')),
                        TextInput::make('name')
                            ->required()
                            ->rules('required'),
                        TextInput::make('price')
                            ->label('Amount')
                            ->required()
                            ->numeric()
                            ->rules('required|numeric|regex:/^\d+$/'),
                    ]),
                ])
                ->action(function (array $data) {
                    $branchId = auth()->user()->hasRole('superadmin')
                        ? $data['branch_id']
                        : auth()->user()->branch_id;

                    RequestableItem::create([
                        'name' => $data['name'],
                        'price' => $data['price'],
                        'branch_id' => $branchId,
                    ]);

                    ActivityLog::create([
                        'branch_id' => $branchId,
                        'user_id' => auth()->user()->id,
                        'activity' => 'Create Requestable Item',
                        'description' => 'Created requestable item for ' . $data['name'],
                    ]);

                    $this->dialog()->success(
                        $title = 'Requestable Item Saved',
                        $description = 'Item has been saved successfully'
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

                    ActivityLog::create([
                        'branch_id' => auth()->user()->hasRole('superadmin') ? $record->branch_id : auth()->user()->branch_id,
                        'user_id' => auth()->user()->id,
                        'activity' => 'Update Requestable Item',
                        'description' => 'Updated requestable item ' . $record->name,
                    ]);

                    $this->dialog()->success(
                        $title = 'Update Successfully',
                        $description = 'Amenity has been updated successfully'
                    );
                })
                ->form(function ($record) {
                    return [
                        Grid::make(1)->schema([
                            TextInput::make('name')
                                ->default($record->name)
                                ->rules(
                                    'required|unique:requestable_items,name,' .
                                        $record->id
                                ),
                            TextInput::make('price')
                                ->default($record->price)
                                ->rules('required|numeric|regex:/^\d+$/'),
                        ]),
                    ];
                })
                ->modalHeading('Update Amenity'),
            Tables\Actions\DeleteAction::make()
                ->button()
                ->size('sm'),
        ];
    }
}
