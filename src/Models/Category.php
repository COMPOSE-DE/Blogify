<?php

namespace ComposeDe\Blogify\Models;

use BlogifyPostModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends BaseModel
{

    use SoftDeletes;

    /**
     * The database table used by the model
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = ['name', 'hash'];

    /**
     * Set or unset the timestamps for the model
     *
     * @var bool
     */
    public $timestamps = true;


    public function __construct(array $attributes = [])
    {
        $this->table = config('blogify.tables.categories');
        parent::__construct($attributes);
    }


    public function getLinkAttribute()
    {
        return config('blogify.blog_route_prefix') . '?category=' . $this->id;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | For more information pleas check out the official Laravel docs at
    | http://laravel.com/docs/5.0/eloquent#relationships
    |
    */

    public function post()
    {
        return $this->hasMany(BlogifyPostModel::class);
    }
}
