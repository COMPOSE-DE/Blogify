<?php

namespace Donatix\Blogify\Models;

use Donatix\Blogify\Models\Post;

class Visibility extends BaseModel
{
    const PUBLIC = "Public";
    const PRIVATE = "Private";
    const PROTECTED = "Protected";
    const RECOMMENDED = "Recommended";

    protected $table = 'visibility';
    public $timestamps = false;

    public function post()
    {
        return $this->hasMany(Post::class);
    }

    public static function getPublicIds()
    {
        return static::whereIn('name', [static::RECOMMENDED, static::PUBLIC])->pluck('id');
    }

    public static function getRecommendedId()
    {
        return static::where('name', static::RECOMMENDED)->first()->id;
    }
}
