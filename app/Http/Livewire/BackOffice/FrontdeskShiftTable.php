<?php

namespace App\Http\Livewire\BackOffice;

use App\Models\ActivityLog;
use Livewire\Component;
use App\Models\FrontdeskShift;
use WireUi\Traits\Actions;
use Livewire\WithPagination;
use Filament\Tables;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;

class FrontdeskShiftTable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return FrontdeskShift::query();
    }

        protected function getTableColumns(): array
    {
        return [

            Tables\Columns\TextColumn::make('label')
                ->label('Shift Schedule')
                ->formatStateUsing(
                    fn(string $state): string => strtoupper("{$state}")
                )
                ->sortable(),
            Tables\Columns\TextColumn::make('frontdesk_outgoing')
                ->label('OUTGOING')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('frontdesk_incoming')
                ->label('INCOMING')
                ->searchable()
                ->sortable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('edit')
                ->icon('heroicon-o-pencil-alt')
                ->color('success')
                ->action(function ($record, $data) {
                    
                    return redirect()->route('back-office.frontdesk-shift-edit', $record->id);
                   
                })
        ];
    }

    public function redirectToCreate()
    {
        return redirect()->route('back-office.frontdesk-shift-form');
    }

    public function render()
    {
        return view('livewire.back-office.frontdesk-shift-table');
    }
}
