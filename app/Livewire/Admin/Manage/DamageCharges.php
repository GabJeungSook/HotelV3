<?php

namespace App\Livewire\Admin\Manage;

use App\Models\ActivityLog;
use Livewire\Component;
use App\Models\HotelItems;
use WireUi\Traits\WireUiActions;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;

class DamageCharges extends Component implements Tables\Contracts\HasTable, \Filament\Forms\Contracts\HasForms
{
    use Tables\Concerns\InteractsWithTable;
    use \Filament\Forms\Concerns\InteractsWithForms;
    use WireUiActions;

    public $branch_id;

    public function render()
    {
        return view('livewire.admin.manage.damage-charges');
    }

    protected function getTableQuery(): Builder
    {
        if (auth()->user()->hasRole('superadmin')) {
            return HotelItems::query();
        } else {
            return HotelItems::query()->where(
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
            CreateAction::make('add_damage_charge')
                ->label('Add New Damage Charge')
                ->icon('heroicon-o-plus')
                ->color('info')
                ->button()
                ->disableCreateAnother()
                ->modalHeading('Add New Damage Charge')
                ->form([
                    Grid::make(1)->schema([
                        Select::make('branch_id')
                            ->label('Branch')
                            ->options(\App\Models\Branch::pluck('name', 'id'))
                            ->required()
                            ->visible(fn () => auth()->user()->hasRole('superadmin')),
                        TextInput::make('name')
                            ->required(),
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

                    HotelItems::create([
                        'name' => $data['name'],
                        'price' => $data['price'],
                        'branch_id' => $branchId,
                    ]);

                    ActivityLog::create([
                        'branch_id' => $branchId,
                        'user_id' => auth()->user()->id,
                        'activity' => 'Create Damage Charges',
                        'description' => 'Created damage charges for ' . $data['name'],
                    ]);

                    $this->dialog()->success(
                        $title = 'Item Saved',
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
                        'activity' => 'Update Damage Charges',
                        'description' => 'Updated damage charges for ' . $record->name,
                    ]);

                    $this->dialog()->success(
                        $title = 'Update Successfully',
                        $description = 'Damage charge has been updated successfully'
                    );
                })
                ->form(function ($record) {
                    return [
                        Grid::make(1)->schema([
                            TextInput::make('name')
                                ->default($record->name)
                                ->rules(
                                    'required|unique:hotel_items,name,' .
                                        $record->id
                                ),
                            TextInput::make('price')->required()->default($record->price),
                        ]),
                    ];
                })
                ->modalHeading('Update Damage Charges'),
            Tables\Actions\DeleteAction::make()
                ->button()
                ->size('sm'),
        ];
    }
}
