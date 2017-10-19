<?php

namespace ComposeDe\Blogify\Models;

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
        return $this->getCachedId(static::ADMIN);
    }

    public function createUser($userData)
    {
        return $this->users()->create($userData);
    }

    public function getAdminRoleName()
    {
        return static::ADMIN;
    }

    public function getAuthorRoleName()
    {
        return static::AUTHOR;
    }

    public function getReviewerRoleName()
    {
        return static::REVIEWER;
    }

    public function getMemberRoleName()
    {
        return static::MEMBER;
    }
}
