<?php

namespace App\Http\Livewire\Admin\Manage;

use Filament\Tables;
use Livewire\Component;
use App\Models\CashDrawer;
use WireUi\Traits\Actions;
use App\Models\ActivityLog;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms;

class ManageCashDrawers extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    use Actions;

    public $add_modal = false;
    public $name;
    public $branch_id;

    protected function getTableQuery(): Builder
    {
        if(auth()->user()->hasRole('superadmin'))
        {
            return CashDrawer::query();
        }else{
            return CashDrawer::query()->where('branch_id', auth()->user()->branch_id);
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
            Tables\Columns\ToggleColumn::make('is_active')
                ->label('ACTIVE')
                ->onColor('success')
                ->offColor('danger')
                ->sortable(),
        ];
    }

     protected function getTableActions(): array
    {
        return [
            Tables\Actions\EditAction::make('cash_drawer.edit')
                ->icon('heroicon-o-pencil-alt')
                ->color('success')
                ->action(function ($record, $data) {
                    $record->update([
                        'branch_id' => auth()->user()->hasRole('superadmin') ? $data['branch_id'] : $record->branch_id,
                        'name' => $data['name'],
                    ]);

                     ActivityLog::create([
                            'branch_id' => auth()->user()->hasRole('superadmin') ? $this->branch_id : auth()->user()->branch_id,
                            'user_id' => auth()->user()->id,
                            'activity' => 'Update Cash Drawer',
                            'description' => 'Updated cash drawer name to' . $data['name'],
                        ]);


                    $this->dialog()->success(
                            $title = 'Cash Drawer Updated',
                            $description =
                                'The cash drawer has been updated successfully.'
                        );
                })
                ->form(function ($record) {
                    return [
                        Forms\Components\Select::make('branch_id')
                            ->label('Branch')
                            ->options(
                                function () {
                                    return \App\Models\Branch::all()->pluck('name', 'id')->toArray();
                                }
                            )
                            ->default($record->branch_id)
                            ->required()
                            ->visible(fn () => auth()->user()->hasRole('superadmin')),
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->default($record->name)
                            ->required(),
                    ];
                })
                ->modalHeading('Update Cash Drawer')
                ->modalWidth('lg'),

        ];
    }

    public function saveDrawer()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:cash_drawers,name',
        ]);

        CashDrawer::create([
            'branch_id' => auth()->user()->hasRole('superadmin') ? $this->branch_id : auth()->user()->branch_id,
            'name' => $this->name,
            'is_active' => false
        ]);

        ActivityLog::create([
            'branch_id' => auth()->user()->hasRole('superadmin') ? $this->branch_id : auth()->user()->branch_id,
            'user_id' => auth()->user()->id,
            'activity' => 'Create Cash Drawer',
            'description' => 'Created cash drawer for ' . $this->name,
        ]);

        $this->dialog()->success(
            $title = 'Cash Drawer Saved',
            $description = 'Item has been saved successfully'
        );
        $this->reset(['name']);
        $this->add_modal = false;
    }

    public function redirectBack()
    {
        return redirect()->route('admin.user');
    }

    public function render()
    {
        return view('livewire.admin.manage.manage-cash-drawers', [
             'branches' => \App\Models\Branch::all(),
        ]);
    }
}
