<?php

namespace App\Livewire\Frontdesk\Food;

use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\ItemCategory;
use App\Models\MenuItem;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use WireUi\Traits\WireUiActions;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;

class Menu extends Component implements Tables\Contracts\HasTable, \Filament\Forms\Contracts\HasForms
{
    use Tables\Concerns\InteractsWithTable;
    use \Filament\Forms\Concerns\InteractsWithForms;
    use WireUiActions;

    public $add_modal = false;
    public $edit_modal = false;
    public $menu_id;
    public $name, $price, $category_id;

    protected function getTableQuery(): Builder
    {
        return MenuItem::query()
            ->where('branch_id', auth()->user()->branch_id)
            ->where('department_id', Department::FRONTDESK);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('NAME')
                ->formatStateUsing(fn ($record) => strtoupper($record->name))
                ->weight('bold')
                ->searchable()
                ->sortable(),
            TextColumn::make('price')
                ->label('PRICE')
                ->formatStateUsing(fn ($record) => '₱' . number_format($record->price, 2))
                ->searchable()
                ->sortable(),
            TextColumn::make('category.name')
                ->label('CATEGORY')
                ->searchable()
                ->sortable(),
        ];
    }

    public function saveMenu()
    {
        $this->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
        ], [
            'category_id.required' => 'Please select a category',
        ]);

        DB::beginTransaction();
        MenuItem::create([
            'branch_id' => auth()->user()->branch_id,
            'department_id' => Department::FRONTDESK,
            'name' => $this->name,
            'price' => $this->price,
            'category_id' => $this->category_id,
        ]);

        ActivityLog::create([
            'branch_id' => auth()->user()->branch_id,
            'user_id' => auth()->user()->id,
            'activity' => 'Create Menu',
            'description' => 'Created menu ' . $this->name,
        ]);
        DB::commit();

        $this->add_modal = false;
        $this->reset('name', 'price', 'category_id');

        $this->dialog()->success(
            $title = 'Success',
            $description = 'Menu has been added'
        );
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\EditAction::make('type.edit')
                ->icon('heroicon-o-pencil-square')
                ->color('success')
                ->action(function ($record, $data) {
                    $record->update($data);
                    $this->dialog()->success(
                        $title = 'Menu Updated',
                        $description = 'Menu was successfully updated'
                    );
                })
                ->form(function ($record) {
                    return [
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->default($record->name)
                                ->required(),
                            TextInput::make('price')
                                ->default($record->price)
                                ->numeric()
                                ->required(),
                        ]),
                    ];
                })
                ->modalHeading('Update Menu')
                ->modalWidth('xl'),
            Tables\Actions\DeleteAction::make('user.destroy'),
        ];
    }

    public function render()
    {
        return view('livewire.frontdesk.food.menu', [
            'categories' => ItemCategory::where('branch_id', auth()->user()->branch_id)
                ->subcategories()
                ->get(),
        ]);
    }
}
