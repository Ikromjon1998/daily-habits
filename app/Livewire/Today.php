<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Today extends Component
{
    public function render(): View
    {
        return view('livewire.today')
            ->layout('layouts.app');
    }
}
