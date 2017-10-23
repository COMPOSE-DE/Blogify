<?php

namespace ComposeDe\Blogify\Models;

use ComposeDe\Blogify\Models\Post;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends BaseModel
{

    use SoftDeletes;

    /**
     * The database table used by the model
     *
     * @var string
     */
    protected $table = 'media';

    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Set or unset the timestamps for the model
     *
     * @var bool
     */
    public $timestamps = true;

    public function __construct(array $attributes = [])
    {
        $this->table = config('blogify.tables.media');
        parent::__construct($attributes);
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
        return $this->belongsTo(config('blogify.models.post'));
    }
}
