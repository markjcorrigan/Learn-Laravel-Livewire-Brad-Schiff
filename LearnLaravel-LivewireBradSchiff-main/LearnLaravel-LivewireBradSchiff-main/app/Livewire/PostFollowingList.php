<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class PostFollowingList extends Component
{
    use WithPagination;

    public function render()
    {
        $posts = auth()->user()->feedPosts()->latest()->paginate(10);
        return view('livewire.post-following-list', ['posts' => $posts]);
    }
}


