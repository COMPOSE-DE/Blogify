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

    public static function getAdminRoleId()
    {
        return (new static)->getCachedId(static::ADMIN);
    }

    public function createUser($userData)
    {
        return $this->users()->create($userData);
    }
}
