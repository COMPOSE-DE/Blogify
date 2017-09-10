<?php

namespace Donatix\Blogify\Models;

class Role extends BaseModel
{
    public function users()
    {
        return $this->hasMany(config('blogify.auth_model'));
    }

    public function scopeByAdminRoles($query)
    {
        $query->whereIn('name', ['admin', 'author', 'reviewer']);
    }

    public function createUser($userData)
    {
        return $this->users()->create($userData);
    }
}
