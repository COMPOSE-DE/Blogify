<?php

namespace ComposeDe\Blogify\Models;

use BlogifyPostModel;

class Visibility extends BaseModel
{
    const PUBLIC = "Public";
    const PRIVATE = "Private";
    const PROTECTED = "Protected";
    const RECOMMENDED = "Recommended";

    protected $table = 'visibility';
    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        $this->table = config('blogify.tables.visibility');
        parent::__construct($attributes);
    }

    public function post()
    {
        return $this->hasMany(BlogifyPostModel::class);
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

    public function getPublicVisibilityName()
    {
        return static::PUBLIC;
    }
    
    public function getPrivateVisibilityName()
    {
        return static::PRIVATE;
    }

    public function getProtectedVisibilityName()
    {
        return static::PROTECTED;
    }

    public function getRecommendedVisibilityName()
    {
        return static::RECOMMENDED;
    }
}
