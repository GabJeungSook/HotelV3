<?php

namespace App\Livewire\Admin\Manage;

use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use App\Models\DiscountConfiguration;
use App\Models\Type;
use App\Models\StayingHour;
use App\Models\Branch;

class Discount extends Component implements Tables\Contracts\HasTable, \Filament\Forms\Contracts\HasForms
{
    use Tables\Concerns\InteractsWithTable;
    use \Filament\Forms\Concerns\InteractsWithForms;
    use WireUiActions;

    public $branchId;
    public $discountAmount;
    public $discountEnabled;

    public function mount()
    {
        $this->branchId = auth()->user()->branch_id;
        $this->loadBranchSettings();
        $this->ensureAllCombinationsExist();
    }

    public function updatedBranchId()
    {
        $this->loadBranchSettings();
        $this->ensureAllCombinationsExist();
    }

    private function loadBranchSettings()
    {
        $branch = Branch::find($this->branchId);
        $this->discountAmount = $branch?->discount_amount ?? 0;
        $this->discountEnabled = $branch?->discount_enabled ?? false;
    }

    private function ensureAllCombinationsExist()
    {
        $types = Type::where('branch_id', $this->branchId)->get();
        $stayingHours = StayingHour::where('branch_id', $this->branchId)->get();

        foreach ($types as $type) {
            foreach ($stayingHours as $sh) {
                DiscountConfiguration::firstOrCreate([
                    'branch_id' => $this->branchId,
                    'type_id' => $type->id,
                    'staying_hour_id' => $sh->id,
                ], [
                    'is_enabled' => false,
                ]);
            }
        }
    }

    public function saveGlobalSettings()
    {
        $this->validate([
            'discountAmount' => 'required|numeric|min:0',
        ]);

        Branch::where('id', $this->branchId)->update([
            'discount_amount' => $this->discountAmount,
            'discount_enabled' => $this->discountEnabled,
        ]);

        $this->dialog()->success('Saved', 'Discount settings updated.');
    }

    protected function getTableQuery(): Builder
    {
        return DiscountConfiguration::query()
            ->where('branch_id', $this->branchId)
            ->with(['type', 'stayingHour']);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('type.name')
                ->label('ROOM TYPE')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('stayingHour.number')
                ->label('HOURS')
                ->formatStateUsing(fn ($state) => $state . ' Hours')
                ->sortable(),
            Tables\Columns\ToggleColumn::make('is_enabled')
                ->label('DISCOUNT ENABLED')
                ->onColor('success')
                ->offColor('danger'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('type_id')
                ->label('Room Type')
                ->options(Type::where('branch_id', $this->branchId)->pluck('name', 'id')),
        ];
    }

    protected function getTableFiltersLayout(): ?string
    {
        return FiltersLayout::AboveContent->value;
    }

    public function render()
    {
        return view('livewire.admin.manage.discount', [
            'branches' => auth()->user()->hasRole('superadmin') ? Branch::all() : collect(),
        ]);
    }
}
