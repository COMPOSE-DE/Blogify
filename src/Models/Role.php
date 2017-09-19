<?php

namespace Donatix\Blogify\Models;

class Role extends BaseModel
{
    const ADMIN = 'admin';
    const AUTHOR = 'author';
    const REVIEWER = 'reviewer';
    const MEMBER = 'member';

    protected $hasHash = false;

    public function users()
    {
        return $this->hasMany(config('blogify.auth_model'));
    }

    public function scopeByAdminRoles($query)
    {
        $query->whereIn('name', [static::ADMIN, static::AUTHOR, static::REVIEWER]);
    }

    public function getAdminRoleId()
    {
        return static::where('name', static::ADMIN)->first()->id;
    }

    public function createUser($userData)
    {
        return $this->users()->create($userData);
    }
}
