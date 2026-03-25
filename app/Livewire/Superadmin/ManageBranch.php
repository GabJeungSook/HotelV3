<?php

namespace App\Livewire\Superadmin;

use Filament\Tables;
use App\Models\Branch;
use App\Models\ActivityLog;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;

class ManageBranch extends Component implements Tables\Contracts\HasTable, \Filament\Forms\Contracts\HasForms
{
    use Tables\Concerns\InteractsWithTable;
    use \Filament\Forms\Concerns\InteractsWithForms;
    use WireUiActions;

    protected function getTableQuery(): Builder
    {
        return Branch::query();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('BRANCH NAME')
                ->searchable()
                ->sortable()
                ->formatStateUsing(fn(string $state): string => strtoupper($state)),
            Tables\Columns\TextColumn::make('address')
                ->label('ADDRESS')
                ->searchable()
                ->sortable()
                ->default('—'),
            Tables\Columns\TextColumn::make('initial_deposit')
                ->label('INITIAL DEPOSIT')
                ->money('PHP')
                ->sortable(),
            Tables\Columns\TextColumn::make('extension_time_reset')
                ->label('EXT. TIME RESET (HRS)')
                ->sortable()
                ->default('—'),
            Tables\Columns\TextColumn::make('kiosk_time_limit')
                ->label('KIOSK LIMIT (MIN)')
                ->sortable()
                ->default('—'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\EditAction::make()
                ->form($this->getBranchFormSchema())
                ->color('warning')
                ->button(),
            Tables\Actions\Action::make('credentials')
                ->label('Credentials')
                ->icon('heroicon-o-user')
                ->color('gray')
                ->button()
                ->url(fn (Branch $record) => route('superadmin.branch.credentials', $record->id)),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make('save')
                ->disableCreateAnother()
                ->modalHeading('Add New Branch')
                ->modalButton('Save')
                ->after(function ($record) {
                    ActivityLog::create([
                        'branch_id' => $record->id,
                        'user_id' => auth()->user()->id,
                        'activity' => 'Create Branch',
                        'description' => 'Created branch ' . $record->name,
                    ]);

                    $this->dialog()->success(
                        $title = 'Success',
                        $description = 'Branch created successfully'
                    );
                })
                ->label('Add New Branch')
                ->button()
                ->color('primary')
                ->icon('heroicon-o-plus')
                ->form($this->getBranchFormSchema())
                ->mutateFormDataUsing(function (array $data): array {
                    $data['autorization_code'] = '12345';
                    return $data;
                })
                ->requiresConfirmation(),
        ];
    }

    protected function getBranchFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Branch Name')
                ->required(),
            TextInput::make('address')
                ->label('Address'),
            TextInput::make('extension_time_reset')
                ->label('Extension Time Reset (Hours)')
                ->numeric(),
            TextInput::make('initial_deposit')
                ->label('Initial Deposit')
                ->numeric()
                ->prefix('₱'),
            TextInput::make('discount_amount')
                ->label('Discount Amount')
                ->numeric()
                ->prefix('₱'),
            TextInput::make('kiosk_time_limit')
                ->label('Kiosk Time Limit (Minutes)')
                ->numeric(),
        ];
    }

    public function render()
    {
        return view('livewire.superadmin.manage-branch');
    }
}
