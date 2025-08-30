<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\File;
use Mews\Purifier\Facades\Purifier;

class Editpost extends Component
{
    use WithFileUploads;

    public $post;
    public $title;
    public $body;
    public $post_tags;
    public $post_photo;

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->title = $post->title;
        $this->body = $post->body;
        $this->post_tags = $post->post_tags;
    }


    public function save()
    {
        $this->validate([
            'title' => 'required',
            'body' => 'required',
            'post_tags' => 'required|string',
            'post_photo' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048'
        ]);

        $post = Post::find($this->post->id);
        $post->title = $this->title;
        $post->body = Purifier::clean($this->body);

        $post->post_tags = $this->post_tags;

        if ($this->post_photo) {
            $directory = public_path('uploads/');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0777, true);
            }

            $originalExtension = $this->post_photo->getClientOriginalExtension();
            $imageName = 'post_' . hexdec(uniqid()) . '.' . $originalExtension;
            $manager = new ImageManager(new Driver());
            $img = $manager->read($this->post_photo->getRealPath());
            $img = $img->resize(409, 368);

            if ($originalExtension == 'png' || $originalExtension == 'gif' || $originalExtension == 'svg') {
                $img = $img->toJpeg(80);
                $imageName = 'post_' . hexdec(uniqid()) . '.jpg';
            }

            $img->save($directory . '/' . $imageName);
            $post->photo = 'uploads/' . $imageName;
        }

        $post->save();

        session()->flash('success', 'Post successfully updated');
        return $this->redirect("/profile/" . auth()->user()->username, navigate: true);

    }




    // public function save()
    // {
    //     $incomingFields = $this->validate([
    //         'title' => 'required',
    //         'body' => 'required',
    //         'post_tags' => 'required|string',
    //         'post_photo' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048'
    //     ]);

    //     $incomingFields['title'] = strip_tags($incomingFields['title']);
    //     $incomingFields['body'] = strip_tags($incomingFields['body']);

    //     $this->authorize('update', $this->post);

    //     $this->post->title = $incomingFields['title'];
    //     $this->post->body = $incomingFields['body'];
    //     $this->post->post_tags = $incomingFields['post_tags'];
    //     $this->post->post_slug = strtolower(str_replace(' ', '_', $this->post->title));

    //     if ($this->post_photo) {
    //         $directory = public_path('uploads/');
    //         if (!File::exists($directory)) {
    //             File::makeDirectory($directory, 0777, true);
    //         }
    //         $originalExtension = $this->post_photo->getClientOriginalExtension();
    //         $imageName = 'post_' . hexdec(uniqid()) . '.' . $originalExtension;
    //         $manager = new ImageManager(new Driver());
    //         $img = $manager->read($this->post_photo->getRealPath());
    //         $img = $img->resize(409, 368);
    //         if ($originalExtension == 'png' || $originalExtension == 'gif' || $originalExtension == 'svg') {
    //             $img = $img->toJpeg(80);
    //             $imageName = 'post_' . hexdec(uniqid()) . '.jpg';
    //         }
    //         $img->save($directory . '/' . $imageName);
    //         $this->post->photo = 'uploads/' . $imageName;
    //     }

    //     $this->post->save();
    //     session()->flash('success', 'Post successfully updated.');
    //     $id = $this->post->id;
    //     return $this->redirect("/post/$id/edit", navigate: true);
    // }

    public function render()
    {
        return view('livewire.editpost');
    }
}

