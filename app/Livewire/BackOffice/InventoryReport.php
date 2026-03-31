<?php

namespace App\Livewire\BackOffice;

use Livewire\Component;
use App\Models\ItemInventory;
use App\Models\ItemCategory;
use App\Models\MenuItem;

class InventoryReport extends Component
{
    public $category_id;
    public $item_id;

    public function render()
    {
        $branchId = auth()->user()->branch_id;

        $inventories = ItemInventory::query()
            ->where('branch_id', $branchId)
            ->with(['menuItem.category'])
            ->when($this->category_id, function ($q) {
                $q->whereHas('menuItem', function ($q2) {
                    $q2->where('category_id', $this->category_id);
                });
            })
            ->when($this->item_id, function ($q) {
                $q->where('menu_item_id', $this->item_id);
            })
            ->get()
            ->map(function ($inventory) {
                $menu = $inventory->menuItem;
                $category = $menu?->category;

                $openingStock = (int) ($inventory->number_of_serving ?? 0);
                $stockIn      = (int) ($inventory->number_of_serving ?? 0);
                $stockOut     = (int) ($inventory->stock_out ?? 0);
                $wastage      = (int) ($inventory->wastage ?? 0);

                $closingStock = $openingStock - ($stockOut + $wastage);

                $unitCost   = (float) ($menu?->price ?? 0);
                $totalValue = $closingStock * $unitCost;

                return [
                    'item_code'      => $menu?->item_code ?? '—',
                    'item_name'      => $menu?->name ?? '—',
                    'category'       => $category?->name ?? '—',
                    'unit'           => 'Serving',
                    'opening_stock'  => $openingStock,
                    'stock_in'       => $stockIn,
                    'stock_out'      => $stockOut,
                    'wastage'        => $wastage,
                    'closing_stock'  => $closingStock,
                    'unit_cost'      => $unitCost,
                    'total_value'    => $totalValue,
                ];
            });

        // Filter dropdown sources
        $categories = ItemCategory::where('branch_id', $branchId)
            ->subcategories()
            ->whereHas('menuItems.inventory')
            ->get();

        $items = MenuItem::where('branch_id', $branchId)
            ->whereHas('inventory')
            ->get();

        return view('livewire.back-office.inventory-report', [
            'inventories' => $inventories,
            'categories' => $categories,
            'items' => $items,
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['category_id', 'item_id']);
    }
}
