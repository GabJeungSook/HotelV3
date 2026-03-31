<?php

namespace App\Livewire\Admin\Manage;

use App\Models\ActivityLog;
use Livewire\Component;
use App\Models\Floor as floorModel;
use WireUi\Traits\WireUiActions;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;

class Floor extends Component implements Tables\Contracts\HasTable, \Filament\Forms\Contracts\HasForms
{
    use Tables\Concerns\InteractsWithTable;
    use \Filament\Forms\Concerns\InteractsWithForms;
    use WireUiActions;

    public $branch_id;

    public function render()
    {
        return view('livewire.admin.manage.floor');
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
                Tables\Columns\TextColumn::make('number')
                    ->formatStateUsing(function (string $state) {
                        $ends = ['th','st','nd','rd','th','th','th','th','th','th'];

                        if ($state % 100 >= 11 && $state % 100 <= 13) {
                            return $state . 'th Floor';
                        } else {
                            return $state . $ends[$state % 10] . ' Floor';
                        }
                    })
                    ->label('NUMBER')
                    ->sortable()
                    ->searchable(query: function ($query, string $search): void {
                        $query->where(function ($q) use ($search) {
                            // Remove "floor" word from search if user typed it
                            $cleanSearch = strtolower(str_replace('floor', '', $search));
                            $cleanSearch = trim($cleanSearch);

                            // Map ordinals to numbers
                            $map = [
                                '1st' => 1,
                                '2nd' => 2,
                                '3rd' => 3,
                                '4th' => 4,
                                '5th' => 5,
                                '6th' => 6,
                                '7th' => 7,
                                '8th' => 8,
                                '9th' => 9,
                                '10th' => 10,
                            ];

                            // Check if user typed "1st", "2nd", etc.
                            if (array_key_exists($cleanSearch, $map)) {
                                $q->orWhere('number', $map[$cleanSearch]);
                            }

                            // Also allow direct numeric search (already default)
                            if (is_numeric($cleanSearch)) {
                                $q->orWhere('number', $cleanSearch);
                            }
                        });
                    }),
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
            CreateAction::make('add_floor')
                ->label('Add New Floor')
                ->icon('heroicon-o-plus')
                ->color('info')
                ->button()
                ->disableCreateAnother()
                ->modalHeading('Add New Floor')
                ->form([
                    Grid::make(1)->schema([
                        Select::make('branch_id')
                            ->label('Branch')
                            ->options(\App\Models\Branch::pluck('name', 'id'))
                            ->required()
                            ->visible(fn () => auth()->user()->hasRole('superadmin')),
                        TextInput::make('number')
                            ->label('Floor Number')
                            ->required()
                            ->numeric()
                            ->rules('required|integer|regex:/^\d+$/'),
                    ]),
                ])
                ->action(function (array $data) {
                    $branchId = auth()->user()->hasRole('superadmin')
                        ? $data['branch_id']
                        : auth()->user()->branch_id;

                    floorModel::create([
                        'branch_id' => $branchId,
                        'number' => $data['number'],
                    ]);

                    ActivityLog::create([
                        'branch_id' => $branchId,
                        'user_id' => auth()->user()->id,
                        'activity' => 'Create Floor',
                        'description' => 'Created floor ' . $data['number'],
                    ]);

                    $this->dialog()->success(
                        $title = 'Floor Saved',
                        $description = 'The floor has been saved successfully.'
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
                        'activity' => 'Update Floor',
                        'description' => 'Updated floor ' . $record->number,
                    ]);

                    $this->dialog()->success(
                        $title = 'Floor Updated',
                        $description = 'The floor has been updated successfully.'
                    );
                })
                ->form(function ($record) {
                    return [
                        Grid::make(1)->schema([
                            TextInput::make('number')
                                ->default($record->number)
                                ->numeric(),
                        ]),
                    ];
                })
                ->modalHeading('Update Floor'),
            Tables\Actions\DeleteAction::make()
                ->button()
                ->size('sm'),
        ];
    }

    protected function getTableQuery(): Builder
    {
         if (auth()->user()->hasRole('superadmin')) {
            return floorModel::query()->orderBy('branch_id', 'asc')->orderBy('number', 'asc');
         } else {
            return floorModel::query()
                ->where('branch_id', auth()->user()->branch_id)
                ->orderBy('number', 'asc');
         }
    }
}
