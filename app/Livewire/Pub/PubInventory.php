<?php

namespace App\Livewire\Pub;

use App\Models\MenuItem;
use Livewire\Component;
use App\Models\ItemInventory;
use WireUi\Traits\WireUiActions;
use App\Models\ItemCategory;

class PubInventory extends Component
{
    public $category;
    public $selectedItem;
    public $quantities = [];
    public $add_stock_modal = false;
    public $menu_item;
    public $menu_name;
    public $menu_price;
    public $menu_quantity;

    use WireUiActions;

    public function mount()
    {
        $this->category = ItemCategory::where('branch_id', auth()->user()->branch_id)->subcategories()->get();
    }

    public function addStock($id)
    {
        $this->add_stock_modal = true;

        $this->menu_item = MenuItem::where('id', $id)->first();
        $this->menu_name = $this->menu_item->name;
        $this->menu_price = '₱ '.number_format($this->menu_item->price, 2);
    }

    public function saveStock()
    {
        $this->validate([
            'menu_quantity' => 'required|numeric|min:1'
        ]);

        if($this->menu_item->inventory === null)
        {
            ItemInventory::create([
                'branch_id' =>  auth()->user()->branch_id,
                'menu_item_id' => $this->menu_item->id,
                'number_of_serving' => $this->menu_quantity
            ]);
        }else{
            $this->menu_item->inventory->update([
                'number_of_serving' => $this->menu_item->inventory->number_of_serving + $this->menu_quantity,
            ]);
        }



        $this->add_stock_modal = false;
        $this->menu_quantity = '';

        $this->dialog()->success(
            $title = 'Success',
            $description = 'Stock added successfully',
        );
    }



    public function render()
    {
        return view('livewire.pub.pub-inventory', [
            'menus' => $this->selectedItem ? MenuItem::where('category_id', $this->selectedItem)->where('department_id', \App\Models\Department::PUB)->get() : [],
        ]);
    }
}
