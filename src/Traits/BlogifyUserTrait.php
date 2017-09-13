<?php

namespace Donatix\Blogify\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;
use Donatix\Blogify\Models\Role;

Trait BlogifyUserTrait
{

    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | For more information pleas check out the official Laravel docs at
    | http://laravel.com/docs/5.0/eloquent#relationships
    |
    */

    public function role()
    {
        return $this->belongsTo('Donatix\Blogify\Models\role');
    }

    public function history()
    {
        return $this->hasMany('Donatix\Blogify\Models\history');
    }

    public function post()
    {
        return $this->hasMany('Donatix\Blogify\Models\post');
    }

    public function comment()
    {
        return $this->hasMany('Donatix\Blogify\Models\comment');
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

    public function scopeByHash($query, $hash)
    {
        return $query->whereHash($hash)->first();
    }

    public function scopeNewUsersSince($query, $date)
    {
        return $query->where('created_at', '>=', $date)->get();
    }

    public function scopeByRole($query, $role_id)
    {
        return $query->whereRoleId($role_id);
    }

    public function scopeReviewers($query)
    {
        $reviewerRoleId = Role::whereName('reviewer')->first()->id;
        $adminRoleId = Role::whereName('admin')->first()->id;

        return $query
            ->where('id', '<>', Auth::id())
            ->whereIn('role_id', [$reviewerRoleId, $adminRoleId]);
    }
}

