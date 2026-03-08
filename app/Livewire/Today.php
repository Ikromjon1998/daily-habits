<?php

namespace App\Livewire;

use Livewire\Component;

class Today extends Component
{
    public function render()
    {
        return view('livewire.today')
            ->layout('layouts.app');
    }
}
