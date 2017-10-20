<?php

namespace ComposeDe\Blogify\Facades;

use Illuminate\Support\Facades\Facade;

class BlogifyRole extends Facade
{
    protected static function getFacadeAccessor()
    {
        return config('blogify.models.role');
    }
}
