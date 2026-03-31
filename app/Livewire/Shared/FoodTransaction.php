<?php

namespace App\Livewire\Shared;

use App\Models\MenuItem;
use App\Models\Guest;
use App\Models\Department;
use App\Models\ItemInventory;
use App\Models\ActivityLog;
use App\Models\Transaction as TransactionModel;
use App\Models\CheckinDetail;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Illuminate\Support\Facades\DB;

class FoodTransaction extends Component
{
    use WireUiActions;

    public string $department = 'kitchen';
    public $guest;
    public $food_id;
    public $food_price = 0;
    public $food_quantity;
    public $food_total_amount = 0;
    public $assigned_frontdesk;
    public bool $food_beverages_modal = false;

    public function mount(string $department = 'kitchen')
    {
        $this->department = $department;
        $this->assigned_frontdesk = auth()->user()->assigned_frontdesks;
    }

    private function getDepartmentId(): int
    {
        return Department::where('slug', $this->department)->value('id');
    }

    private function getDepartmentLabel(): string
    {
        return match ($this->department) {
            'kitchen' => 'Kitchen',
            'pub' => 'The Pub',
            default => ucfirst($this->department),
        };
    }

    public function addTransaction($id)
    {
        $this->guest = Guest::where('branch_id', auth()->user()->branch_id)->find($id);
        $this->reset(['food_id', 'food_price', 'food_quantity', 'food_total_amount']);
        $this->food_beverages_modal = true;
    }

    public function updatedFoodId()
    {
        if ($this->food_id && $this->food_id != 'Select Item') {
            $food = MenuItem::find($this->food_id);
            if ($food) {
                $qty = max($this->food_quantity ?? 1, 1);
                $this->food_price = $food->price;
                $this->food_total_amount = $food->price * $qty;
            }
        } else {
            $this->food_price = 0;
            $this->food_total_amount = 0;
        }
    }

    public function updatedFoodQuantity()
    {
        if ($this->food_id && $this->food_id != 'Select Item') {
            $food = MenuItem::find($this->food_id);
            if ($food) {
                $qty = max($this->food_quantity ?? 1, 1);
                $this->food_price = $food->price;
                $this->food_total_amount = $food->price * $qty;
            }
        } else {
            $this->food_price = 0;
            $this->food_total_amount = 0;
        }
    }

    public function closeModal()
    {
        $this->food_beverages_modal = false;
    }

    public function addFood()
    {
        $this->validate([
            'food_id' => 'required',
            'food_quantity' => 'required|gt:0',
        ], [
            'food_id.required' => 'Please select an item',
            'food_quantity.required' => 'Quantity is required',
            'food_quantity.gt' => 'Quantity must be greater than 0',
        ]);

        DB::beginTransaction();

        $check_in_detail = CheckinDetail::where('guest_id', $this->guest->id)->first();
        $food = MenuItem::find($this->food_id);
        $inventory = ItemInventory::where('menu_item_id', $this->food_id)->first();

        if ($inventory && $inventory->number_of_serving > 0) {
            TransactionModel::create([
                'branch_id' => $check_in_detail->guest->branch_id,
                'room_id' => $check_in_detail->room_id,
                'guest_id' => $check_in_detail->guest_id,
                'floor_id' => $check_in_detail->room->floor_id,
                'transaction_type_id' => 9,
                'assigned_frontdesk_id' => json_encode($this->assigned_frontdesk),
                'description' => 'Food and Beverages',
                'payable_amount' => $this->food_total_amount,
                'paid_amount' => 0,
                'change_amount' => 0,
                'deposit_amount' => 0,
                'paid_at' => null,
                'override_at' => null,
                'remarks' => 'Guest Added Food and Beverages: (' . $this->getDepartmentLabel() . ') (' . $this->food_quantity . ') ' . $food->name,
            ]);

            $inventory->update([
                'number_of_serving' => $inventory->number_of_serving - $this->food_quantity,
            ]);

            ActivityLog::create([
                'branch_id' => auth()->user()->branch_id,
                'user_id' => auth()->id(),
                'activity' => 'Food Transaction',
                'description' => 'Added ' . $this->food_quantity . 'x ' . $food->name . ' (' . $this->getDepartmentLabel() . ') to guest ' . $this->guest->name,
            ]);

            $this->food_beverages_modal = false;
            $this->dialog()->success('Success', 'Transaction added successfully');
        } else {
            $this->dialog()->error('Out of Stock', 'This item is out of stock');
        }

        DB::commit();
    }

    public function render()
    {
        return view('livewire.shared.food-transaction', [
            'departmentLabel' => $this->getDepartmentLabel(),
            'guests' => Guest::where('branch_id', auth()->user()->branch_id)
                ->whereHas('checkInDetail', fn ($q) => $q->where('is_check_out', false))
                ->get(),
            'foods' => MenuItem::where('branch_id', auth()->user()->branch_id)
                ->where('department_id', $this->getDepartmentId())
                ->get(),
        ]);
    }
}
