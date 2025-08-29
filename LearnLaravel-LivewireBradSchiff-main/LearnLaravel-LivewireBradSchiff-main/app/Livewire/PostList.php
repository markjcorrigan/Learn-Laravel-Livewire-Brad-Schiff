<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;
use Livewire\WithPagination;

class PostList extends Component
{
    use WithPagination;

    public $username;

    public function mount($username)
    {
        $this->username = $username;
    }

    public function render()
    {
        $posts = Post::whereHas('user', function ($query) {
            $query->where('username', $this->username);
        })->paginate(10);

        return view('livewire.post-list', ['posts' => $posts]);
    }

    public function gotoPage($page)
    {
        $this->setPage($page);
    }
}



