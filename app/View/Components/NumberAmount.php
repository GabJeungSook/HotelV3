<?php

namespace App\View\Components;

use Illuminate\View\Component;

class NumberAmount extends Component
{
     public $label;
    public $number;
    public $amount;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->label = $label;
        $this->number = $number;
        $this->amount = $amount;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.number-amount');
    }
}
