<?php

namespace App\Http\Livewire\Frontdesk;

use Livewire\Component;
use App\Models\Frontdesk;
use App\Models\AssignedFrontdesk as assignFrontdeskModel;
use App\Models\ShiftLog;
use App\Models\CashDrawer;
use WireUi\Traits\Actions;
use DB;
class AssignedFrontdesk extends Component
{
    use Actions;
    public $get_frontdesk = [];
    public $partner_modal = false;
    public $name;
    public $cash_drawers;
    public $drawer;
    public $shift;

    public function mount()
    {
        $currentHour = now()->hour;
        $this->shift = ($currentHour >= 8 && $currentHour < 20) ? 'AM' : 'PM';

        $assigned = Frontdesk::where('branch_id', auth()->user()->branch_id)
            ->where('user_id', auth()->user()->id)->get();
        foreach ($assigned as $item) {
            array_push($this->get_frontdesk, $item->id);
        }   

        $this->cash_drawers = CashDrawer::where('branch_id', $assigned->first()->branch_id)
            ->where('is_active', 1)
            ->get();
    }
    public function render()
    {
        return view('livewire.frontdesk.assigned-frontdesk', [
            'frontdesks' => Frontdesk::where(
                'branch_id',
                auth()->user()->branch_id
            )
                ->WhereNotIn('id', $this->get_frontdesk)
                ->get(),
        ]);
    }

    public function assignFrontdesk($frontdesk_id)
    {
        if (count($this->get_frontdesk) >= 1) {
            $this->dialog()->error(
                $title = 'Sorry',
                $description = 'you have already assigned a frontdesk'
            );
        } else {
            array_push($this->get_frontdesk, $frontdesk_id);
        }
    }

    public function removeFrontdesk($frontdesk_id)
    {
        $this->get_frontdesk = array_diff($this->get_frontdesk, [
            $frontdesk_id,
        ]);
    }

    public function saveFrontdesk()
    {
        if($this->drawer)
        {
         DB::beginTransaction();
             array_push($this->get_frontdesk, 'N/A');
            $frontdesk_ids = json_encode($this->get_frontdesk);
            ShiftLog::create([
                'time_in' => \Carbon\Carbon::now(),
                'frontdesk_ids' => $frontdesk_ids,
                'cash_drawer_id' => $this->drawer,
                'shift' => $this->shift,
            ]);

            auth()
                ->user()
                ->update([
                    'time_in' => \Carbon\Carbon::now(),
                    'assigned_frontdesks' => $frontdesk_ids,
                    'cash_drawer_id' => $this->drawer,  
                ]);

         DB::commit();
          $this->dialog()->success(
            $title = 'Success',
            $description = 'Cash drawer selected successfully'
        );

        return redirect()->route('frontdesk.dashboard');
        }else{
            $this->dialog()->error(
            $title = 'Oops!',
            $description = 'Please select a cash drawer to proceed.'
        );
        }
    }

    public function savePartner()
    {
        $this->validate([
            'name' => 'required',
        ]);
        array_push($this->get_frontdesk, $this->name);
        $haha = json_encode($this->get_frontdesk);
        DB::beginTransaction();
        ShiftLog::create([
            'time_in' => \Carbon\Carbon::now(),
            'frontdesk_ids' => json_encode($this->get_frontdesk),
        ]);

        auth()
            ->user()
            ->update([
                'time_in' => \Carbon\Carbon::now(),
                'assigned_frontdesks' => $haha,
            ]);

        DB::commit();
        $this->dialog()->success(
            $title = 'Success',
            $description = 'Frontdesk assigned successfully'
        );
        return redirect()->route('frontdesk.dashboard');
    }
}
