<?php

namespace App\Livewire\BackOffice;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\FrontdeskShift;
use WireUi\Traits\WireUiActions;
use Carbon\Carbon;

class FrontdeskShiftEdit extends Component
{
      use WireUiActions;
     use WithFileUploads;

    public $shift;
    public $form = [];
    public $raw_file;

     public function mount($id)
    {
        $this->shift = FrontdeskShift::findOrFail($id);

        $this->form = $this->shift->toArray();

        if ($this->shift->shift_opened) {
            $this->form['shift_opened'] = Carbon::parse($this->shift->shift_opened)->format('Y-m-d\TH:i');
        }

        if ($this->shift->shift_closed) {
            $this->form['shift_closed'] = Carbon::parse($this->shift->shift_closed)->format('Y-m-d\TH:i');
        }
    }

    public function save()
    {

          $this->dialog()->confirm([
            'title'       => 'Are you Sure?',
            'description' => 'Save the information?',
            'acceptLabel' => 'Yes, save it',
            'method'      => 'confirmSave',
            'params'      => 'Saved',
        ]);


      
    }

    public function confirmSave(){
          if ($this->raw_file) {
            $this->form['raw_file'] = $this->raw_file->store('raw_files', 'public');
        }

        $this->shift->update($this->form);

          $this->dialog()->success(
            $title = 'Success',
            $description = 'Daily Shift Recorded Successfully.'
        );

        $this->reset();

        return redirect()->route('back-office.frontdesk-shift-table');
    }

    public function redirectToTable()
    {
        return redirect()->route('back-office.frontdesk-shift-table');
    }

    public function render()
    {
        return view('livewire.back-office.frontdesk-shift-edit');
    }
}
