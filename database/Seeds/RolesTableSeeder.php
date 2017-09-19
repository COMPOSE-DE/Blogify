<?php

namespace Donatix\Blogify\database\Seeds;

use Illuminate\Database\Seeder;
use Donatix\Blogify\Models\Role;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        Role::firstOrcreate(['name' => Role::ADMIN]);
        Role::firstOrcreate(['name' => Role::AUTHOR]);
        Role::firstOrcreate(['name' => Role::REVIEWER]);
        Role::firstOrcreate(['name' => Role::MEMBER]);
    }
}
