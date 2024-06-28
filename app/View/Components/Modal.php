<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    public $id;
    public $title;
    public $footer;

    public function __construct($id, $title, $footer = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->footer = $footer;
    }

    public function render()
    {
        return view('components.modal');
    }
}
