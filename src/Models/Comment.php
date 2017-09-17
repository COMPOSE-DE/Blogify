<?php

namespace Donatix\Blogify\Models;

use Donatix\Blogify\Models\Post;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends BaseModel
{

    use SoftDeletes;

    public $statuses = [
	'pending' => 1,
	'approved' => 2,
	'disapproved' => 3,
    ];

    public function user()
    {
        return $this->belongsTo(config('blogify.auth_model'));
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

    public function getCreatedAtAttribute($value)
    {
        return date("d-m-Y H:i", strtotime($value));
    }
}
