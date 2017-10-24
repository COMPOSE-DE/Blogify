<?php

namespace ComposeDe\Blogify\Models;

use ComposeDe\Traits\BlogifyRoleTrait;

class Role extends BaseModel
{
    use BlogifyRoleTrait;

    protected $hasHash = false;
}
