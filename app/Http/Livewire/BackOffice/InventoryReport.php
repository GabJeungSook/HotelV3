<?php

namespace App\Http\Livewire\BackOffice;

use Livewire\Component;
use App\Models\FrontdeskInventory;

class InventoryReport extends Component
{
    public $category_id;
    public $item_id;

    public function render()
    {
        $branchId = auth()->user()->branch_id;

        $inventories = FrontdeskInventory::query()
            ->where('branch_id', $branchId)
            ->with(['frontdesk_menus.frontdeskCategory'])
            ->when($this->category_id, function ($q) {
                $q->whereHas('frontdesk_menus.frontdeskCategory', function ($q2) {
                    $q2->where('id', $this->category_id);
                });
            })
            ->when($this->item_id, function ($q) {
                $q->whereHas('frontdesk_menus', function ($q2) {
                    $q2->where('id', $this->item_id);
                });
            })
            ->get()
            ->map(function ($inventory) {
                $menu = $inventory->frontdesk_menus()->first(); // 1 menu per inventory record (assumption)
                $category = $menu?->frontdeskCategory;

                $openingStock = (int) ($inventory->number_of_serving ?? 0);
                $stockIn      = (int) ($inventory->number_of_serving ?? 0); // kept from your logic
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

        // filter dropdown sources
        $categories = FrontdeskInventory::query()
            ->where('branch_id', $branchId)
            ->with('frontdesk_menus.frontdeskCategory')
            ->get()
            ->pluck('frontdesk_menus.0.frontdeskCategory')
            ->filter()
            ->unique('id')
            ->values();

        $items = FrontdeskInventory::query()
            ->where('branch_id', $branchId)
            ->with('frontdesk_menus')
            ->get()
            ->pluck('frontdesk_menus.0')
            ->filter()
            ->unique('id')
            ->values();

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
