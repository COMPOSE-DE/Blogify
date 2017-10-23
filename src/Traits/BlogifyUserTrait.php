<?php

namespace ComposeDe\Blogify\Traits;

use BlogifyRole;
use Illuminate\Database\Eloquent\SoftDeletes;
use BlogifyAuth;


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

    public function roles()
    {
        return $this->belongsToMany('ComposeDe\Blogify\Models\Role', config('blogify.tables.role_user'), 'user_id', 'role_id');
    }

    public function post()
    {
        return $this->hasMany('ComposeDe\Blogify\Models\post');
    }

    public function comment()
    {
        return $this->hasMany('ComposeDe\Blogify\Models\comment');
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
        $roles = BlogifyRole::getFacadeRoot();

        $reviewerRoleId = $roles->whereName($roles->getReviewerRoleName())->first()->id;
        $adminRoleId = $roles->whereName($roles->getAdminRoleName())->first()->id;

        return $query
            ->where('id', '<>', BlogifyAuth::id())
            ->whereIn('role_id', [$reviewerRoleId, $adminRoleId]);
    }
}

