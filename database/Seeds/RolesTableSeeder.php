<?php

namespace Donatix\Blogify\database\Seeds;

use Illuminate\Database\Seeder;
use Donatix\Blogify\Models\Role;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        Role::create(["name" => "Admin"]);

        Role::create(["name" => "Author"]);

        Role::create(["name" => "Reviewer"]);

        Role::create(["name" => "Member"]);
    }
}
