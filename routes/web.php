<?php

use App\Models\Post;
use App\Events\ChatMessage;
use App\Livewire\Createpost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\FileUploadController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::post('/upload-file', [FileUploadController::class, 'uploadFile']);

Route::get('/admins-only', function () {
    return 'Only admins should be able to see this page';
})->middleware('can:visitAdminPages');



// User Related routes
Route::get('/', [UserController::class,"showCorrectHomepage"])->name('login');
Route::post('/register', [UserController::class,'register'])->middleware('guest');
Route::post('/login', [UserController::class,'login'])->middleware('guest');
Route::post('/logout', [UserController::class,'logout'])->middleware('auth');
Route::get('/manage-avatar', [UserController::class, 'showAvatarForm'])->middleware('mustBeLoggedIn');
Route::post('/manage-avatar', [UserController::class, 'storeAvatar'])->middleware('mustBeLoggedIn');

// Follow Related routes
Route::post('/create-follow/{user:username}', [FollowController::class, 'createFollow'])->middleware('mustBeLoggedIn');
Route::post('/remove-follow/{user:username}', [FollowController::class, 'removeFollow'])->middleware('mustBeLoggedIn');

// Blog related routes
Route::get('/create-post',[PostController::class,'showCreateForm'])->name('create-post')->middleware('auth');
Route::post('/create-post',[PostController::class,'storeNewPost'])->middleware('auth');
Route::get('/post/{post}',[PostController::class,'viewSinglePost']);
Route::delete('/post/{post}',[PostController::class,'delete'])->middleware('can:delete,post');
Route::get('/post/{post}/edit',[PostController::class, 'showEditForm'])->middleware('can:update,post');
Route::put('/post/{post}',[PostController::class, 'actuallyUpdate'])->middleware('can:update,post');
Route::get('/search/{term}',[PostController::class,'search']);

//Livewire Blog (Brad Schiff)
// Route::get('/postlist', function () {
//     return \Livewire\Livewire::mount(\App\Livewire\PostList::class);
// });





// Profile related routes

Route::get('/profile/{user:username}', [UserController::class,'profile']);
Route::get('/profile/{user:username}/followers', [UserController::class,'profileFollowers']);
Route::get('/profile/{user:username}/following', [UserController::class,'profileFollowing']);

Route::middleware('cache.headers:public;max_age=20;etag')->group(function () {
    Route::get('/profile/{user:username}/raw', [UserController::class,'profileRaw']);
    Route::get('/profile/{user:username}/followers/raw', [UserController::class,'profileFollowersRaw']);
    Route::get('/profile/{user:username}/following/raw', [UserController::class,'profileFollowingRaw']);
});



// Chat Route
Route::post('/send-chat-message',function (Request $request){
    $formFields = $request->validate([
        'textvalue' => 'required'
    ]);
    if (!trim(strip_tags($formFields['textvalue']))) {
        return response()->noContent();
    }

    broadcast(new ChatMessage(['username' =>auth()->user()->username, 'textvalue'=> strip_tags($request->textvalue), 'avatar' => auth()->user()->avatar]))->toOthers();
    return response()->noContent();
})->middleware('mustBeLoggedIn');







