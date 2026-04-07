<?php

namespace App\Livewire\Admin;

use Filament\Tables;
use App\Models\Branch;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use App\Models\TransferReason;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;

class RoomTransferReasons extends Component implements Tables\Contracts\HasTable, \Filament\Forms\Contracts\HasForms
{
    use Tables\Concerns\InteractsWithTable;
    use \Filament\Forms\Concerns\InteractsWithForms;
    use WireUiActions;

    protected function getTableQuery(): Builder
    {
        if (auth()->user()->hasRole('superadmin')) {
            return TransferReason::query();
        }
        return TransferReason::query()->where('branch_id', auth()->user()->branch_id);
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
            Tables\Columns\TextColumn::make('reason')
                ->label('REASON')
                ->searchable()
                ->sortable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\EditAction::make()
            ->form([
                Select::make('branch_id')
                ->label('Branch')
                ->options(Branch::all()->pluck('name', 'id'))
                ->searchable()
                ->visible(fn () => auth()->user()->hasRole('superadmin'))
                ->default(auth()->user()->branch_id),
                Textarea::make('reason')->label("Reason")->required(),

            ])->color('success')
            ->button(),
        ];
    }

     protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make('save')
            ->disableCreateAnother()
            ->modalHeading('Add new')
            ->modalButton('Save')
            ->after(function () {
                $this->dialog()->success(
                    $title = 'Success',
                    $description = 'Saved Successfully'
                );
            })
            ->label('Add New')
            ->button()
            ->color('info')
            ->icon('heroicon-o-plus')
            ->form([
                Select::make('branch_id')
                ->label('Branch')
                ->options(Branch::all()->pluck('name', 'id'))
                ->searchable()
                ->visible(fn () => auth()->user()->hasRole('superadmin'))
                ->default(auth()->user()->branch_id),
                Textarea::make('reason')->label("Reason")->required(),

            ])
            ->action(function ($data) {
                if(auth()->user()->hasRole('superadmin'))
                {
                    TransferReason::create($data);
                }else{
                    $data['branch_id'] = auth()->user()->branch_id;
                    TransferReason::create($data);
                }
            })
            ->requiresConfirmation()
        ];
    }

    public function render()
    {
        return view('livewire.admin.room-transfer-reasons');
    }
}
