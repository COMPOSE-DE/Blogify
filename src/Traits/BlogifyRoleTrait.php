<?php


namespace ComposeDe\Traits;

use BlogifyAuthModel;

trait BlogifyRoleTrait
{
    public function users()
    {
        return $this->belongsToMany(BlogifyAuthModel::class, config('blogify.tables.role_user'), 'role_id', 'user_id');
    }

    public function scopeByAdminRoles($query)
    {
        $query->whereIn('name', [
            $this->getAdminRoleName(),
            $this->getAuthorRoleName(),
            $this->getReviewerRoleName()
        ]);
    }

    public function getRoleOrder()
    {
        return [
            $this->getAdminRoleName(),
            $this->getAuthorRoleName(),
            $this->getReviewerRoleName(),
            $this->getMemberRoleName()
        ];
    }

    public function getAdminRoleId()
    {
        return $this->getCachedId($this->getAdminRoleName());
    }

    public function createUser($userData)
    {
        return $this->users()->create($userData);
    }

    public function getAdminRoleName()
    {
        return 'admin';
    }

    public function getAuthorRoleName()
    {
        return 'author';
    }

    public function getReviewerRoleName()
    {
        return 'reviewer';
    }

    public function getMemberRoleName()
    {
        return 'member';
    }
}