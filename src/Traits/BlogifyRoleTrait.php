<?php


namespace ComposeDe\Traits;

use BlogifyUserModel;

trait BlogifyRoleTrait
{
    public function users()
    {
        return $this->belongsToMany(BlogifyUserModel::class, config('blogify.tables.role_user'), 'role_id', 'user_id');
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

    public function getCachedId($type)
    {
        return cache()->remember(
            "{$this->getTable()}.{$type}",
            config('blogify.config_items_cache_time'),
            function() use($type) {
                return $this->where('name', $type)->first(['id'])->id;
            }
        );
    }
}