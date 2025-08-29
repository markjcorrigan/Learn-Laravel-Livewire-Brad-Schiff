<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Post extends Model
{
    use Searchable;
    use HasFactory;

    protected $fillable = [
    'title',
    'post_tags',
    'post_slug',
    'user_id',
    'photo',
    'body', // Make sure 'body' is included in the $fillable array
];


    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'post_slug' => $this->post_slug,
            'post_tags' => $this->post_tags,
            'approved' => $this->approved
        ];
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
