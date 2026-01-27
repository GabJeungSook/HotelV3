<?php

namespace App\Http\Livewire\Admin\Manage;

use Filament\Tables;
use Livewire\Component;
use App\Models\CashDrawer;
use WireUi\Traits\Actions;
use App\Models\ActivityLog;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

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
        ]);

        ActivityLog::create([
            'branch_id' => auth()->user()->hasRole('superadmin') ? $this->branch_id : auth()->user()->branch_id,
            'user_id' => auth()->user()->id,
            'activity' => 'Create Requestable Item',
            'description' => 'Created requestable item for ' . $this->name,
        ]);

        $this->dialog()->success(
            $title = 'Requestable Item Saved',
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
        return view('livewire.admin.manage.manage-cash-drawers');
    }
}
