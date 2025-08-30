<?php

namespace App\Http\Controllers;

use App\Jobs\SendNewPostEmail;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PostController extends Controller
{


    public function search($term)
    {
        //Basic Way of Searching, not so good
        return Post::where('title', 'LIKE', '%' . $term . '%')->orWhere('body', 'LIKE', '%' . $term . '%')->with('user:id,username,avatar')->get();
        // Better way with Laravel Scout
        $posts = Post::search($term)->get();
        $posts->load('user:id,username,avatar');
        return $posts;
    }

    public function actuallyUpdate(Post $post, Request $request)
    {
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);

        $post->update($incomingFields);

        return back()->with('success', 'Post successfully updated');
    }



    public function showEditForm(Post $post)
    {
        return view('edit-post', ['post' => $post]);
    }



    public function delete(Post $post)
    {
        // if (auth()->user()->cannot('delete', $post)) {
        //     return 'You do not have the permissions to delete this post';

        // }

        $post->delete();
        return redirect('/profile/' . auth()->user()->username)->with('success', 'Post successfully deleted');
    }



    public function deleteApi(Post $post)
    {
        $post->delete();
        return 'true';
    }


    public function showCreateForm()
    {
        if (!auth()->check()) {
            return redirect('/');
        }
        return view('create-post');
    }



    public function storeNewPost(Request $request)
    {
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);
        $incomingFields['user_id'] = auth()->id();

        $newPost = Post::create($incomingFields);

        dispatch(new SendNewPostEmail(['sendTo' => auth()->user()->email, 'name' => auth()->user()->username, 'title' => $newPost->title]));

        return redirect("/post/{$newPost->id}")->with('success', 'New Post successfully created');

    }


    public function storeNewPostApi(Request $request)
    {
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required',
            'post_tags' => 'required|string',
        ]);

        $post = new Post();
        $post->title = strip_tags($incomingFields['title']);
        $post->body = strip_tags($incomingFields['body']);
        $post->post_tags = strip_tags($incomingFields['post_tags']);
        $post->post_slug = strtolower(str_replace(' ', '_', $incomingFields['title']));
        $post->user_id = auth()->id();
        $post->photo = ''; // Set photo here

        $post->save();

        dispatch(new SendNewPostEmail(['sendTo' => auth()->user()->email, 'name' => auth()->user()->username, 'title' => $post->title]));

        return response()->json(['id' => $post->id, 'message' => 'Post successfully created'], 201);
    }




    // public function storeNewPostApi(Request $request)
    // {
    //     $incomingFields = $request->validate([
    //         'title' => 'required',
    //         'body' => 'required',
    //          'post_tags' => 'required|string',
    //     ]);

    //     $incomingFields['title'] = strip_tags($incomingFields['title']);
    //     $incomingFields['body'] = strip_tags($incomingFields['body']);
    //     $incomingFields['user_id'] = auth()->id();

    //     $newPost = Post::create($incomingFields);

    //     dispatch(new SendNewPostEmail(['sendTo'=> auth()->user()->email,'name' => auth()->user()->username, 'title' =>$newPost->title ]));

    //     return $newPost->id;

    // }



    public function viewSinglePost(Post $post)
    {
        // $post['body'] = strip_tags(Str::markdown($post->body),'<p><ul><ol><li><strong><h3><h1><i><br>');

        // $allowedTags = '<p><ul><ol><li><strong><h3><h1><i><br>';
// $post['body'] = strip_tags($post->body, $allowedTags);

        return view('single-post', ['post' => $post]);
    }
}
