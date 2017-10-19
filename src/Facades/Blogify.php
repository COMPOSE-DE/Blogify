<?php

namespace ComposeDe\Blogify\Facades;

use Illuminate\Support\Facades\Facade;

class Blogify extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ComposeDe.blogify';
    }
}
