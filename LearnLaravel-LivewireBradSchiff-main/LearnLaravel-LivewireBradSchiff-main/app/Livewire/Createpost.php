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



    // public function updateBody($content)
    // {
    //     $this->body = $content;

    //     Log::info('Body updated:', [$this->body]);
    // }





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



    // public function uploadFile(Request $request)
    // {
    //     Log::info('Upload file request received');

    //     try {
    //         $image = file_get_contents('php://input');
    //         $tmpFile = tempnam(sys_get_temp_dir(), 'rayeditor');
    //         file_put_contents($tmpFile, $image);
    //         $manager = new ImageManager(new Driver());
    //         $img = $manager->read($tmpFile);
    //         unlink($tmpFile);
    //         $imageName = 'post_' . hexdec(uniqid()) . '.jpg';
    //         $img = $img->resize(409, 368);
    //         $directory = storage_path('app/public/blog');
    //         if (!File::exists($directory)) {
    //             File::makeDirectory($directory, 0777, true);
    //         }
    //         $img = $img->toJpeg(80)->save($directory . '/' . $imageName);
    //         $url = asset('storage/blog/' . $imageName);
    //         return response()->json(['url' => $url]);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }



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
    $post->body = $this->body;
    $post->post_tags = $this->post_tags;
    $post->post_slug = strtolower(str_replace(' ', '_', $this->title)); // Use $this->title instead of $post->title
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

    $post->save();
    session()->flash('success', 'Post successfully created.');
    return redirect()->route('create-post', $post->id);
}


/*

public function create(Request $request)
{
    $validator = Validator::make($request->all(), [
        'title' => 'required',
        'body' => 'required',
        'post_tags' => 'required|string',
        'post_photo' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048'
    ]);

    // if ($validator->fails()) {
    //     session()->flash('errors', $validator->errors());
    //     session()->flash('oldInput', request()->all());
    //     return redirect()->to('/create-post');
    // }

    // try {
        $validatedData = $validator->validated();

        // Handle file upload
         $validatedData['title'] = strip_tags($validatedData['title']);
        $validatedData['body'] = strip_tags($validatedData['body']);
        $imagePath = $request->file('post_photo')->store('uploads', 'public');
            $validatedData['post_tags'] = strip_tags($validatedData['post_tags']);
        // $validatedData['post_photo'] = $imagePath;
        $validatedData['user_id'] = auth()->id();
       
    

        // dd($validatedData); // dump the validated data

        $newPost = Post::create($validatedData);

        dd($newPost); // dump the new post

        // dispatch(new SendNewPostEmail([
        //     'sendTo' => auth()->user()->email,
        //     'name' => auth()->user()->username,
        //     'title' => $newPost->title
        // ]));

        return redirect("/post/{$newPost->id}")->with('success', 'New Post successfully created');
     

    // } catch (\Exception $e) {
    //     dd($e->getMessage()); // dump the error message
    //     return redirect()->back()->with('error', 'An error occurred while creating the post');
    // }
}


*/

        // try {
        //         // dump($this->body);
        //         //   dd($this->body);

        // if (!auth()->check()) {
        //     abort(403, 'Unauthorized');
        // }

        // $this->validate([
        //     'title' => 'required',
        //     'body' => 'required',
        //     'post_tags' => 'required|string',
        //     'post_photo' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048'
        // ]);

        // $directory = public_path('uploads/blog');
        // if (!File::exists($directory)) {
        //     File::makeDirectory($directory, 0777, true);
        // }

        // $originalExtension = $this->post_photo->getClientOriginalExtension();
        // $imageName = 'post_' . hexdec(uniqid()) . '.' . $originalExtension;
        // $manager = new ImageManager(new Driver());
        // $img = $manager->read($this->post_photo->getRealPath());
        // $img = $img->resize(409, 368);
        // $img->save($directory . '/' . $imageName);

        // $newPost = Post::create([
        //     'title' => $this->title,
        //     'body' => $this->body,
        //     'post_tags' => $this->post_tags,
        //     'post_slug' => Str::slug($this->title),
        //     'user_id' => auth()->id(),
        //     'photo' => 'uploads/' . $imageName,
        // ]);

        // session()->flash('success', 'New post successfully created.');
        // return $this->redirect("/post/{$newPost->id}", navigate: true);
        //  } catch (\Exception $e) {
        //     session()->flash('error', 'An error occurred while creating the post: ' . $e->getMessage());
        //     return back();
        // }
    // }

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
        $post->body = $this->body;
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
