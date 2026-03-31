<?php

namespace App\Livewire\Admin\Manage;

use App\Models\ActivityLog;
use Livewire\Component;
use App\Models\ExtensionRate as extensionRateModel;
use WireUi\Traits\WireUiActions;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;

class ExtensionRate extends Component implements Tables\Contracts\HasTable, \Filament\Forms\Contracts\HasForms
{
    use Tables\Concerns\InteractsWithTable;
    use \Filament\Forms\Concerns\InteractsWithForms;
    use WireUiActions;

    public $branch_id;

    public function render()
    {
        return view('livewire.admin.manage.extension-rate');
    }

    protected function getTableQuery(): Builder
    {
        if (auth()->user()->hasRole('superadmin')) {
            return extensionRateModel::query();
        } else {
            return extensionRateModel::query()->where(
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
            Tables\Columns\TextColumn::make('hour')
                ->label('HOUR')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('amount')
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
            CreateAction::make('add_extension_rate')
                ->label('Add New Extension Rate')
                ->icon('heroicon-o-plus')
                ->color('info')
                ->button()
                ->disableCreateAnother()
                ->modalHeading('Add New Extension Rate')
                ->form([
                    Grid::make(1)->schema([
                        Select::make('branch_id')
                            ->label('Branch')
                            ->options(\App\Models\Branch::pluck('name', 'id'))
                            ->required()
                            ->visible(fn () => auth()->user()->hasRole('superadmin')),
                        TextInput::make('hour')
                            ->required(),
                        TextInput::make('amount')
                            ->required(),
                    ]),
                ])
                ->action(function (array $data) {
                    $branchId = auth()->user()->hasRole('superadmin')
                        ? $data['branch_id']
                        : auth()->user()->branch_id;

                    extensionRateModel::create([
                        'hour' => $data['hour'],
                        'amount' => $data['amount'],
                        'branch_id' => $branchId,
                    ]);

                    ActivityLog::create([
                        'branch_id' => $branchId,
                        'user_id' => auth()->user()->id,
                        'activity' => 'Create Extension Rate',
                        'description' => 'Created extension rate for ' . $data['hour'] . ' hour(s)',
                    ]);

                    $this->dialog()->success(
                        $title = 'Extension Saved',
                        $description = 'Extension rate has been saved successfully'
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
                        'activity' => 'Update Extension Rate',
                        'description' => 'Updated extension rate for ' . $record->hour . ' hour(s)',
                    ]);

                    $this->dialog()->success(
                        $title = 'Update Successfully',
                        $description = 'Extension rate has been updated successfully'
                    );
                })
                ->form(function ($record) {
                    return [
                        Grid::make(1)->schema([
                            TextInput::make('hour')->default($record->hour),
                            TextInput::make('amount')->default($record->amount),
                        ]),
                    ];
                })
                ->modalHeading('Update Extension Rate'),
            Tables\Actions\DeleteAction::make()
                ->button()
                ->size('sm'),
        ];
    }
}
