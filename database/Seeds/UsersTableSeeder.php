<?php

namespace ComposeDe\Blogify\database\Seeds;

use Illuminate\Database\Seeder;
use ComposeDe\Blogify\Models\Role;
use \Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * @var \ComposeDe\Blogify\Models\Role
     */
    private $roles;
    private $users;

    public function __construct(Role $roles)
    {
        $this->roles = $roles;
        $this->users = app()->make(config('blogify.models.auth'));
    }


    public function run()
    {
        $admin = config('blogify.admin_user');

        if (! $this->users->where('email', $admin['email'])->exists()) {
            $this->users->create([
                'name' => $admin['name'],
                'email' => $admin['email'],
                'password' => Hash::make($admin['password']),
                'role_id' => $this->roles->getAdminRoleId(),
            ]);
        }
    }
}
