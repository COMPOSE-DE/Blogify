<?php

namespace Donatix\Blogify\database\Seeds;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Donatix\Blogify\Models\Role;
use \Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $admin = config('blogify.admin_user');
        $user = app()->make(config('blogify.auth_model'));

        if (! $user->where('email', $admin['email'])->exists()) {
            $user->create([
                'name' => $admin['name'],
                'email' => $admin['email'],
                'password' => Hash::make($admin['password']),
                'role_id' => Role::getAdminRoleId(),
            ]);
        }
    }
}
