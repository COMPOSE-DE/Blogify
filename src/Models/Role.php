<?php

namespace ComposeDe\Blogify\Models;

class Role extends BaseModel
{
    const ADMIN = 'admin';
    const AUTHOR = 'author';
    const REVIEWER = 'reviewer';
    const MEMBER = 'member';

    protected $hasHash = false;

    public function __construct(array $attributes = [])
    {
        $this->table = config('blogify.tables.roles');
        parent::__construct($attributes);
    }

    public function users()
    {
        return $this->belongsToMany(config('blogify.auth_model'), config('blogify.tables.role_user'), 'role_id', 'user_id');
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
