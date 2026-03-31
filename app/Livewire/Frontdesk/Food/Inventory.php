<?php

namespace App\Livewire\Frontdesk\Food;

use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\MenuItem;
use App\Models\ItemInventory;
use App\Models\ItemCategory;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Inventory extends Component
{
    use WireUiActions;

    public $category;
    public $selectedItem;
    public $quantities = [];
    public $add_stock_modal = false;
    public $menu_item;
    public $menu_name;
    public $menu_price;
    public $menu_quantity;
    public $record;

    public function mount($record)
    {
        $this->record = ItemCategory::find($record);
    }

    public function addStock($id)
    {
        $this->add_stock_modal = true;

        $this->menu_item = MenuItem::find($id);
        $this->menu_name = $this->menu_item->name;
        $this->menu_price = '₱ ' . number_format($this->menu_item->price, 2);
    }

    public function saveStock()
    {
        $this->validate([
            'menu_quantity' => 'required|numeric|min:1',
        ]);

        if ($this->menu_item->inventory === null) {
            ItemInventory::create([
                'branch_id' => auth()->user()->branch_id,
                'menu_item_id' => $this->menu_item->id,
                'number_of_serving' => $this->menu_quantity,
            ]);
        } else {
            $this->menu_item->inventory->update([
                'number_of_serving' => $this->menu_item->inventory->number_of_serving + $this->menu_quantity,
            ]);
        }

        ActivityLog::create([
            'branch_id' => auth()->user()->branch_id,
            'user_id' => auth()->user()->id,
            'activity' => 'Add Inventory',
            'description' => 'Added inventory for menu ' . $this->menu_item->name,
        ]);

        $this->add_stock_modal = false;
        $this->menu_quantity = '';

        $this->dialog()->success(
            $title = 'Success',
            $description = 'Stock added successfully',
        );
    }

    public function render()
    {
        return view('livewire.frontdesk.food.inventory', [
            'menus' => $this->record
                ? MenuItem::where('category_id', $this->record->id)
                    ->where('department_id', Department::FRONTDESK)
                    ->get()
                : [],
        ]);
    }
}
