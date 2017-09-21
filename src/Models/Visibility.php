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
        return [static::getRecommendedId(), static::getPublicId()];
    }

    public static function getRecommendedId()
    {
        return (new static)->getCachedId(static::RECOMMENDED);
    }

    public static function getPublicId()
    {
        return (new static)->getCachedId(static::PUBLIC);
    }

    public static function getPrivateId()
    {
        return (new static)->getCachedId(static::PRIVATE);
    }

    public static function getProtectedId()
    {
        return (new static)->getCachedId(static::PROTECTED);
    }

    public function getCachedId($type)
    {
        return cache()->remember(
            "visibility.{$type}",
            config('blogify.config_items_cache_time'),
            function() use($type) {
                return $this->where('name', $type)->first(['id'])->id;
            }
        );
    }
}
