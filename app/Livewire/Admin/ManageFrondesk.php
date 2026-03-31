<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\Frontdesk;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;

class ManageFrondesk extends Component implements Tables\Contracts\HasTable, \Filament\Forms\Contracts\HasForms
{
    use Tables\Concerns\InteractsWithTable;
    use \Filament\Forms\Concerns\InteractsWithForms;
    use WireUiActions;

    protected function getTableQuery(): Builder
    {
        if (auth()->user()->hasRole('superadmin')) {
            return Frontdesk::query();
        }

        return Frontdesk::query()->where('branch_id', auth()->user()->branch_id);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('branch.name')
                ->label('BRANCH')
                ->formatStateUsing(fn (string $state): string => strtoupper($state))
                ->sortable()
                ->visible(fn () => auth()->user()->hasRole('superadmin')),
            Tables\Columns\TextColumn::make('name')
                ->label('FRONTDESK NAME')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('number')
                ->label('NUMBER')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('passcode')
                ->label('PASSCODE')
                ->formatStateUsing(fn () => '*****')
                ->sortable(),
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
                        'branch_id' => $record->branch_id,
                        'user_id' => auth()->id(),
                        'activity' => 'Update Frontdesk',
                        'description' => 'Updated frontdesk ' . $record->name,
                    ]);

                    $this->dialog()->success('Frontdesk Updated', 'Updated successfully.');
                })
                ->form(function ($record) {
                    return [
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->default($record->name)
                                ->required(),
                            TextInput::make('number')
                                ->default($record->number)
                                ->required(),
                        ]),
                    ];
                })
                ->modalHeading('Update Frontdesk')
                ->modalWidth('lg'),
            Tables\Actions\DeleteAction::make()
                ->button()
                ->size('sm'),
        ];
    }

    protected function getTableFilters(): array
    {
        if (auth()->user()->hasRole('superadmin')) {
            return [
                SelectFilter::make('branch')->relationship('branch', 'name'),
            ];
        }

        return [];
    }

    protected function getTableFiltersLayout(): ?string
    {
        return FiltersLayout::AboveContent->value;
    }

    public function render()
    {
        return view('livewire.admin.manage-frondesk');
    }
}
