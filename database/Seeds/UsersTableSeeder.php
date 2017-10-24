<?php

namespace ComposeDe\Blogify\database\Seeds;

use Illuminate\Database\Seeder;
use BlogifyRoleModel;
use \Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * @var \ComposeDe\Blogify\Models\Role
     */
    private $roles;
    private $users;

    public function __construct(BlogifyRoleModel $roles)
    {
        $this->roles = $roles;
        $this->users = app()->make(config('blogify.models.auth'));
    }


    public function run()
    {
        $admin = config('blogify.admin_user');

        $user = $this->users->where('email', $admin['email'])->first();

        if (!$user) {
            $user = $this->users->create([
                'name' => $admin['name'],
                'email' => $admin['email'],
                'password' => Hash::make($admin['password']),
            ]);
        }

        if(!$user->roles()->where('name', $this->roles->getAdminRoleName())->exists()) {
            $user->roles()->attach($this->roles->getAdminRoleId());
        }
    }
}
