<?php

namespace App\Livewire\Superadmin;

use App\Models\ActivityLog;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms;
use Filament\Forms\Components\Grid;

class ActivityLogs extends Component implements Tables\Contracts\HasTable, \Filament\Forms\Contracts\HasForms
{
    use Tables\Concerns\InteractsWithTable;
    use \Filament\Forms\Concerns\InteractsWithForms;
    use WireUiActions;

    public function render()
    {
        return view('livewire.superadmin.activity-logs');
    }

    protected function getTableQuery(): Builder
    {
        if (auth()->user()->hasRole('superadmin')) {
            return ActivityLog::query()->latest();
        } else {
            return ActivityLog::query()->where(
                'branch_id',
                auth()->user()->branch_id
            )->latest();
        }
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('branch.name')
                ->label('BRANCH')
                ->formatStateUsing(fn(string $state): string => strtoupper($state))
                ->sortable()
                ->visible(fn () => auth()->user()->hasRole('superadmin')),
            Tables\Columns\TextColumn::make('user.name')
                ->label('USER')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('activity')
                ->label('ACTIVITY')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('description')
                ->label('DESCRIPTION')
                ->searchable()
                ->sortable()
                ->wrap(),
            Tables\Columns\TextColumn::make('created_at')
                ->label('DATE')
                ->dateTime('M d, Y h:i A')
                ->sortable(),
        ];
    }

    protected function getTableFilters(): array
    {
        $filters = [];

        if (auth()->user()->hasRole('superadmin')) {
            $filters[] = SelectFilter::make('branch')->relationship('branch', 'name');
        }

        $filters[] = Filter::make('created_at')
            ->form([
                Grid::make(2)->schema([
                    Forms\Components\DatePicker::make('created_from')->label('Date From'),
                    Forms\Components\DatePicker::make('created_until')->label('Date To'),
                ]),
            ])
            ->query(function (Builder $query, array $data): Builder {
                return $query
                    ->when(
                        $data['created_from'],
                        fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                    )
                    ->when(
                        $data['created_until'],
                        fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                    );
            });

        return $filters;
    }

    protected function getTableFiltersLayout(): ?string
    {
        return FiltersLayout::AboveContent->value;
    }
}
