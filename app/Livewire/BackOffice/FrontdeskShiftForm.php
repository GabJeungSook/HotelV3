<?php

namespace App\Livewire\BackOffice;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\FrontdeskShift;
use WireUi\Traits\WireUiActions;
use Carbon\Carbon;

class FrontdeskShiftForm extends Component
{
    use WireUiActions;
    use WithFileUploads;
     public $raw_file;

    public $form = [];

    protected $rules = [
        'raw_file' => 'nullable|file|max:10240',
    ];

    public function save()
    {
        $this->validate();

        $this->dialog()->confirm([
            'title'       => 'Are you Sure?',
            'description' => 'Save the information?',
            'acceptLabel' => 'Yes, save it',
            'method'      => 'confirmSave',
            'params'      => 'Saved',
        ]);

       
    }

    public function confirmSave()
    {
         if ($this->raw_file) {
            $this->form['raw_file'] = $this->raw_file->store('raw_files', 'public');
        }

        if (!empty($this->form['shift_opened'])) {
            $this->form['shift_opened'] = Carbon::parse($this->form['shift_opened']);
        }

        if (!empty($this->form['shift_closed'])) {
            $this->form['shift_closed'] = Carbon::parse($this->form['shift_closed']);
        }



        FrontdeskShift::create($this->form);

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
        return view('livewire.back-office.frontdesk-shift-form');
    }
}
