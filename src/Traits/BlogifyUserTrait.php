<?php

namespace ComposeDe\Blogify\Traits;

use BlogifyRole;
use BlogifyRoleModel;
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
        return $this->belongsToMany(BlogifyRoleModel::class, config('blogify.tables.role_user'), 'user_id', 'role_id');
    }

    public function post()
    {
        return $this->hasMany('ComposeDe\Blogify\Models\post');
    }

    public function comment()
    {
        return $this->hasMany('ComposeDe\Blogify\Models\comment');
    }

    public function hasRole($roleName)
    {
        return $this->roles->pluck('name')->search($roleName) !== false;
    }

    public function getHighestRole()
    {
        $rolesDescending = BlogifyRole::getFacadeRoot()->getRoleOrder();

        return $this->roles->sortBy(function($role) use($rolesDescending){
            return array_search($role->name, $rolesDescending);
        })->first();
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
            ->whereHas('roles', function($role) use($reviewerRoleId, $adminRoleId) {
                $role->whereIn('role_id', [$reviewerRoleId, $adminRoleId]);
            });
    }
}

