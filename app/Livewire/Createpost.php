<?php

namespace App\Livewire;



use Illuminate\Support\Facades\Log;

use App\Models\Post;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Jobs\SendNewPostEmail;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Illuminate\Http\Request;

// use Illuminate\Support\Facades\Request;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Validator;
use Mews\Purifier\Facades\Purifier;




class Createpost extends Component
{
    use WithFileUploads;

    public $title;
    public $body = '';

    public $post;

    public $post_tags;
    public $post_photo;


    public $image;



    public function mount(Post $post)
    {
        $this->post = $post;
        $this->title = $post->title;
        $this->body = $post->body ?? ''; // Set a default value if $post->body is null
        // $this->body = $post->body;
        $this->post_tags = $post->post_tags;
    }



    public function uploadFile(Request $request)
    {
        Log::info('Upload file request received');
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $file = $request->file('file');
        $filePath = $file->store('uploads', 'public');
        /*  $filePath = $file->store('uploads', 'public'); Saves the file to the uploads directory within the public disk.
        The public disk is defined in the config/filesystems.php file, and it's typically set to store files in the storage/app/public directory.
        So, the file will be saved to: storage/app/public/uploads */


        $url = asset('storage/' . $filePath);

        return response()->json(['url' => $url]);
    }





    public function create()
    {
        $this->validate([
            'title' => 'required',
            'body' => 'required',
            'post_tags' => 'required|string',
            'post_photo' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048'
        ]);

        $post = new Post();
        $post->title = $this->title;
        $post->body = Purifier::clean($this->body);
        $post->post_tags = $this->post_tags;
        $post->post_slug = strtolower(str_replace(' ', '_', $this->title));
        $post->user_id = auth()->id();

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

        // $post->save();
        // session()->flash('success', 'Post successfully created.');
        // return redirect()->route('create-post', $post->id);
        // dispatch(new SendNewPostEmail(['sendTo' => auth()->user()->email, 'name' => auth()->user()->username, 'title' => $post->title]));

        // return redirect("/post/{$post->id}")->with('success', 'New Post successfully created');

        $post->save();
        dispatch(new SendNewPostEmail(['sendTo' => auth()->user()->email, 'name' => auth()->user()->username, 'title' => $post->title]));
        // return $this->redirect("/post/{$post->id}?success=Post successfully created", navigate: true);
        session()->flash('success', 'Post successfully created');
        return $this->redirect("/post/{$post->id}", navigate: true);


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
        session()->flash('success', 'Post successfully updated.');
        return redirect()->route('post.show', $post->id);
    }



    public function render()
    {
        return view('livewire.createpost');
    }
}
