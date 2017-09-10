<?php

namespace Donatix\Blogify\Models;

use Validator;
use Donatix\Blogify\Models\Post;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends BaseModel
{
    use SoftDeletes;

    public function getRouteKeyName()
    {
        return 'hash';
    }

    public static function fromArray($tags)
    {
        $tags->reject(function($tag) {
            return static::where('name', $tag)->exists();
        })->map(function($tag) {
            return static::create([
                'name' => $tag, 'hash' => str_random()
            ]);
        });
    }

    public function post()
    {
        return $this->belongsToMany(Post::class, 'posts_have_tags', 'tag_id', 'post_id');
    }
}
