<?php

namespace App\Livewire;

use Livewire\Component;

class SinglePost extends Component
{

        public $successMessage;

    public function mount()
    {
        $this->successMessage = request()->query('success');
    }

    public function render()
    {
        return view('livewire.singlepost');
    }
}
