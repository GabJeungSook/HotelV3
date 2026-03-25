<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AmountField extends Component
{
     public $label;
    public $amount;
    public $sub;
    public $remark;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
         $this->label = $label;
        $this->amount = $amount;
        $this->sub = $sub;
        $this->remark = $remark;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.amount-field');
    }
}
