<?php

namespace ComposeDe\Blogify\Models;

use ComposeDe\Blogify\Models\Post;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends BaseModel
{
    use SoftDeletes;

    const PENDING = 1;
    const APPROVED = 2;
    const DISAPPROVED = 3;

    public $statuses = [
        'pending' => 1,
        'approved' => 2,
        'disapproved' => 3,
    ];

    public function __construct(array $attributes = [])
    {
        $this->table = config('blogify.tables.comments');
        parent::__construct($attributes);
    }

    public function user()
    {
        return $this->belongsTo(config('blogify.models.auth'));
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | For more information pleas check out the official Laravel docs at
    | http://laravel.com/docs/5.0/eloquent#query-scopes
    |
    */

    public function scopeByRevised($query, $revised)
    {
        return $query->whereRevised($this->statuses[$revised]);
    }

    public function scopePending($query)
    {
        return $query->where('revised', static::PENDING);
    }
    public function scopeApproved($query)
    {
        return $query->where('revised', static::APPROVED);
    }

    public function changeStatus($status)
    {
        $this->revised = $this->statuses[$status];
        $this->save();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    |
    | For more information pleas check out the official Laravel docs at
    | http://laravel.com/docs/5.0/eloquent#accessors-and-mutators
    |
    */

    public function getAuthorNameAttribute()
    {
        return $this->user->name;
    }

    public function getCreatedAtFormattedAttribute()
    {
        return date('d.m.Y', strtotime($this->created_at));
    }
}
