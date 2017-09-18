<?php

namespace Donatix\Blogify\Models;

use Validator;
use Donatix\Blogify\Models\Post;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Collection;

class Tag extends BaseModel
{
    use SoftDeletes;

    public function getRouteKeyName()
    {
        return 'hash';
    }

    public static function findOrCreateTags($tagNames)
    {
        $tags = new Collection;
        foreach ($tagNames as $name) {
            if ($tag = static::where('name', $name)->first()) {
                $tags->push($tag);
            } else {
                $tags->push(static::create(['name' => $name]));
            }
        }

        return $tags;
    }

    public static function createMissing($tags)
    {
        return $tags->reject(function($tag) {
            return static::where('name', $tag)->exists();
        })->map(function($tag) {
            return static::create(['name' => $tag]);
        });
    }

    public function post()
    {
        return $this->belongsToMany(Post::class, 'posts_have_tags', 'tag_id', 'post_id');
    }
}
