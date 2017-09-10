<?php

namespace Donatix\Blogify\database\Seeds;

use Illuminate\Database\Seeder;
use Donatix\Blogify\Models\Role;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        Role::create(["name" => "admin"]);

        Role::create(["name" => "author"]);

        Role::create(["name" => "reviewer"]);

        Role::create(["name" => "member"]);
    }
}
