<?php

namespace ComposeDe\Blogify\Facades;

use Illuminate\Support\Facades\Facade;

class BlogifyAuth extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ComposeDe.blogifyAuth';
    }
}
